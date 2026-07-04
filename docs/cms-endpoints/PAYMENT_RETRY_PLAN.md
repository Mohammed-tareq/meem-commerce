# Payment Retry Feature — Technical Plan

> **Status:** Planning Only
>
> **Do NOT implement from this document.** This is an architectural plan for
> future implementation. No migrations, models, services, or code should be
> written based on this document until explicitly approved.

---

## Problem

Currently, when a customer places an order, the flow is:

```
Customer submits checkout
         │
         ▼
Create invoice in MyFatoorah
         │
         ▼
Create order + transaction in database
         │
         ▼
Redirect customer to payment gateway
         │
         ├── Success → Order completed
         │
         └── Failure → Order cancelled, cart released
```

If the payment fails or the customer closes the browser during redirect, the
order exists in the database with a `cancelled` status. The customer cannot
retry payment for the same order. They must start the checkout process again
from scratch.

**Issues with the current approach:**

1. **Lost orders** — The order is created but never paid. The data is wasted.
2. **Poor customer experience** — Customer fills in all details again.
3. **Cart reservation lost** — Items may go out of stock during retry.
4. **No payment history** — Only the final transaction is recorded. Failed
   attempts have no trace.

---

## Proposed Solution: Payment Attempts System

Introduce a `payment_attempts` table to track every payment attempt for an
order. Instead of cancelling the order on payment failure, the order remains
in `payment_pending` status and the customer can retry.

### Why a separate `payment_attempts` table?

| Approach | Pros | Cons |
|----------|------|------|
| Update transaction row | Simple | Loses history of previous attempts. Cannot track why each attempt failed. |
| Add columns to orders table | No new table | Pollutes orders schema. Fixed number of retry columns. Not scalable. |
| **New `payment_attempts` table** | Full history. Each attempt is independent. Can analyze failure patterns. Clean schema. | More tables. Slightly more queries. |

The dedicated `payment_attempts` table is recommended because:

- Each payment attempt is a distinct event with its own status, error message,
  and gateway reference.
- Historical data enables debugging, analytics, and fraud detection.
- Schema remains clean — orders stay focused on order data, not payment
  logistics.

### Proposed `payment_attempts` table design

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint, PK | Auto-increment |
| `order_id` | bigint, FK → orders.id | The order being paid |
| `payment_method` | string | Gateway used (e.g. `myfatoorah`, `stripe`) |
| `transaction_id` | string, nullable | Gateway transaction ID (e.g. MyFatoorah InvoiceId) |
| `status` | enum | `pending`, `processing`, `paid`, `failed`, `expired` |
| `amount` | decimal(10,2) | Amount attempted |
| `currency` | string | Currency code (e.g. `EGP`, `USD`) |
| `error_code` | string, nullable | Gateway error code |
| `error_message` | text, nullable | Human-readable error description |
| `gateway_response` | json, nullable | Full gateway response for debugging |
| `attempted_at` | timestamp | When the attempt started |
| `completed_at` | timestamp, nullable | When the attempt completed (success or failure) |
| `created_at` | timestamp | |

**Indexes:**
- `order_id` — find all attempts for an order
- `status` — filter by attempt status
- `transaction_id` — match with gateway records

---

## Order Status vs Payment Status vs Payment Transaction

These three concepts serve different purposes and should not be mixed.

### Order Status

Tracks the **fulfillment lifecycle** of the order:

```
pending → processing → completed
                     → cancelled
                     → refunded
```

- Represents where the order is in the operational workflow.
- Changes as the seller processes, ships, and delivers.
- Example: `pending`, `processing`, `completed`, `cancelled`, `refunded`.

### Payment Status

Tracks the **financial settlement** of the order:

```
pending → paid → refunded
       → failed
```

- Represents whether money has been successfully transferred.
- A single order can have only one effective payment status at a time.
- Example: `pending`, `paid`, `failed`, `refunded`, `partially_refunded`.

### Payment Transaction / Attempt

Represents a **single attempt** to transfer money:

```
initiated → processing → success
                       → failed
```

- There can be many attempts for one order.
- Each attempt is independent and carries its own result.
- Example table: `payment_attempts` with fields above.

### Why they should not be mixed

| Scenario | Order Status | Payment Status | Payment Attempt |
|----------|-------------|----------------|-----------------|
| Customer places order | `pending` | `pending` | — |
| Payment succeeds | `processing` | `paid` | `paid` (attempt 1) |
| Payment fails | `payment_pending` | `pending` | `failed` (attempt 1) |
| Customer retries | `payment_pending` | `pending` | `failed` (attempt 1), `paid` (attempt 2) |
| Order is fulfilled | `completed` | `paid` | `paid` (attempt 2) |
| Refund issued | `completed` | `refunded` | `paid` (attempt 2), `refunded` (refund record) |

Mixing them would mean:
- An order status of `cancelled` tells you nothing about whether payment was
  attempted, succeeded, or failed.
- A payment status of `paid` tells you nothing about how many attempts it took
  or what errors occurred.
- You cannot debug failed payments without individual attempt records.

---

## Payment Retry Flow

```
Customer opens failed order
         │
         ▼
┌──────────────────────────────┐
│  Check: order.payment_status │
│  is 'pending' or 'failed'   │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  Create new PaymentAttempt   │
│  status = 'pending'          │
│  amount = order.total_price  │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  Create invoice in gateway   │
│  (MyFatoorah / Stripe)       │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  Update PaymentAttempt       │
│  transaction_id = invoice_id │
│  status = 'processing'       │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  Redirect customer to        │
│  gateway payment page        │
└──────────────┬───────────────┘
               │
    ┌──────────┴──────────┐
    │                     │
    ▼                     ▼
Success              Failure
    │                     │
    ▼                     ▼
┌──────────────┐   ┌──────────────────┐
│ Mark attempt │   │ Mark attempt     │
│ as 'paid'    │   │ as 'failed'      │
│              │   │ Store error      │
│ Update order │   │ ───────────────  │
│ payment      │   │ Customer can     │
│ status=paid  │   │ retry again      │
│              │   │                  │
│ Release cart │   │ Cart stays       │
│ Finalize     │   │ reserved         │
└──────────────┘   └──────────────────┘
```

---

## Required Components

### Models

```php
app/Models/PaymentAttempt.php
```

Fields as described in the table design above.

Relations:
- `belongsTo(Order::class)`
- `order()` — access the order for this attempt

### Services

```php
app/Services/Payment/PaymentRetryService.php
```

Methods:

| Method | Purpose |
|--------|---------|
| `canRetry(Order $order): bool` | Check if order is eligible for retry |
| `createAttempt(Order $order): PaymentAttempt` | Create new attempt record |
| `initiatePayment(PaymentAttempt $attempt): array` | Create gateway invoice, get redirect URL |
| `handleCallback(PaymentAttempt $attempt, array $gatewayResponse): void` | Process gateway callback |
| `markFailed(PaymentAttempt $attempt, string $error): void` | Mark attempt as failed |
| `markPaid(PaymentAttempt $attempt): void` | Mark attempt as paid, update order |

### Events

| Event | When Fired | Listeners |
|-------|-----------|-----------|
| `PaymentAttemptCreated` | New attempt created | Log attempt |
| `PaymentFailed` | Attempt marked failed | Notify customer, log error |
| `PaymentCompleted` | Attempt marked paid | Notify customer, update order, release cart |

### Notifications

| Notification | Trigger | Channel |
|-------------|---------|---------|
| `PaymentFailedNotification` | `PaymentFailed` event | Email, SMS, in-app |
| `PaymentSuccessNotification` | `PaymentCompleted` event | Email, in-app |

### Endpoints

| Method | URL | Purpose |
|--------|-----|---------|
| `GET` | `/api/v1/orders/{id}/payment/retry` | Check if retry is available |
| `POST` | `/api/v1/orders/{id}/payment/retry` | Initiate new payment attempt |

---

## Existing Code Impact

### Current `App\Http\Controllers\Api\General\OrderController::checkout`

The `checkout` method currently creates the order BEFORE payment is confirmed:

```php
// Current flow:
// 1. Create invoice in MyFatoorah
// 2. Create order in database
// 3. Create transaction
// 4. Redirect to gateway
```

With the retry system, the flow would change to:

```php
// Proposed flow:
// 1. Create order with status 'payment_pending'
// 2. Create PaymentAttempt with status 'pending'
// 3. Create invoice in MyFatoorah
// 4. Update PaymentAttempt with invoice ID
// 5. Redirect to gateway
```

### Current `App\Http\Controllers\Api\General\OrderController::checkoutCallback`

The callback currently cancels the order on failure:

```php
// Current: $this->orderService->changeOrderStatus($invoiceId, 'cancelled');
// Proposed: $paymentRetryService->markFailed($attempt, $error);
```

### Current `App\Http\Controllers\Api\General\OrderController::checkoutErrorCallback`

Same as callback — currently cancels order. Should instead mark the attempt as failed and keep the order in `payment_pending`.

---

## Open Questions

1. **Retry limit** — How many times should a customer be allowed to retry?
   Suggested: 3 attempts per order.

2. **Cart reservation timeout** — How long should cart inventory be held during
   retry? Suggested: match the gateway invoice expiry (typically 1–24 hours).

3. **Payment method switching** — Should the customer be allowed to switch
   payment methods on retry (e.g., from MyFatoorah to Cash on Delivery)?
   Suggested: no for simplicity. Same method as original order.

4. **Admin override** — Should an admin be able to mark a payment attempt as
   paid manually (offline payment)? Suggested: yes, for edge cases.

5. **Expired reservation** — If cart reservation expires during retry, should
   the system automatically re-reserve or ask the customer to re-add items?
   Suggested: notify the customer that items are no longer reserved and prices
   may have changed.

---

## Summary of Changes Needed

| Type | Change |
|------|--------|
| New table | `payment_attempts` |
| New model | `PaymentAttempt` |
| New service | `PaymentRetryService` |
| New events | `PaymentAttemptCreated`, `PaymentFailed`, `PaymentCompleted` |
| New notifications | `PaymentFailedNotification`, `PaymentSuccessNotification` |
| New endpoints | `GET/POST /orders/{id}/payment/retry` |
| Modified | `OrderController@checkout` — create order before payment |
| Modified | `OrderController@checkoutCallback` — mark attempt, not cancel |
| Modified | `OrderController@checkoutErrorCallback` — mark attempt, not cancel |
| Modified | Order status enum — add `payment_pending` |
| New translation keys | `payment.retry_available`, `payment.retry_limit_reached`, `payment.retry_initiated` |
