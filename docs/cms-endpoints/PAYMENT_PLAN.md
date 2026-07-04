# Payment System — Implementation Plan

> This document is a **future implementation plan only**.
> No database changes or code implementation should be done based on this document alone.

---

## Table of Contents

1. [Current State](#current-state)
2. [Proposed Payment Methods](#proposed-payment-methods)
3. [Database Changes](#database-changes)
4. [Architecture](#architecture)
5. [Flow Diagrams](#flow-diagrams)
6. [Key Concepts](#key-concepts)

---

## Current State

The current system handles payments through:

- **External gateway (MyFatoorah):** The client-facing checkout flow creates an invoice on MyFatoorah, redirects the user to pay, and handles the callback to update order status.
- **Cash on Delivery (COD):** Handled in `OrderRepository::storeOrder()` by setting `payment_gateway = CASH_ON_DELIVERY` and `order_status = PROCESSING`.
- **Cash:** Similar to COD but tracked separately via `PaymentStatus::CASH`.
- **Wallet:** Full wallet payment where the order is marked completed immediately.

The `orders` table has `payment_gateway` and `payment_status` columns, but these are stored as strings without a dedicated database-level enum or separate payment table.

---

## Proposed Payment Methods

### 1. Cash On Delivery (COD)

COD is already partially implemented. The plan would formalize it:

- Customer selects COD at checkout.
- Order is created with `payment_gateway = CASH_ON_DELIVERY`, `payment_status = PAYMENT_PENDING`.
- Payment is collected by the delivery driver upon delivery.
- Admin/staff manually marks payment as received, or it transitions automatically when order status changes to `delivered`.

### 2. Cashier Payment (Laravel Cashier)

[Laravel Cashier](https://laravel.com/docs/billing) provides an elegant interface for Stripe/Paddle billing. The plan would:

- Integrate Cashier for credit card and digital wallet payments.
- Use Cashier's built-in Stripe Checkout or direct payment intents.
- Leverage Cashier's webhook handling for payment status updates.
- Support subscription billing if needed later.

---

## Database Changes

### Option A: Add Columns to `orders` Table

```php
// In a new migration:
Schema::table('orders', function (Blueprint $table) {
    $table->string('payment_method')->nullable()->after('payment_gateway');
    $table->string('payment_status')->default('pending')->after('payment_method');
});
```

| Column | Type | Purpose |
|---|---|---|
| `payment_method` | string, nullable | The method used: `cod`, `stripe`, `credit_card`, `wallet` |
| `payment_status` | string, default 'pending' | Current status: `pending`, `processing`, `paid`, `failed`, `refunded` |

**Pros:** Simple, no new table, keeps all order data in one place.
**Cons:** Mixes payment metadata with order data. Cannot track multiple payment attempts.

### Option B: New `payment_transactions` Table

```php
Schema::create('payment_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->string('payment_method');        // cod, stripe, credit_card, wallet
    $table->string('payment_status');         // pending, processing, paid, failed, refunded
    $table->string('transaction_id')->nullable();  // External gateway transaction ID
    $table->string('gateway_response')->nullable(); // Raw gateway response (JSON)
    $table->decimal('amount', 10, 2);
    $table->json('metadata')->nullable();     // Additional gateway-specific data
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();
});
```

**Pros:** Tracks multiple payment attempts. Clean separation of concerns. Stores gateway response for debugging.
**Cons:** More complex queries (requires join).

**Recommendation:** Option B is enterprise-grade. Option A is acceptable for MVP.

---

## Architecture

### Models Needed

```php
// Model: Payment
class Payment extends Model
{
    protected $fillable = [
        'order_id', 'payment_method', 'payment_status',
        'transaction_id', 'gateway_response', 'amount',
        'metadata', 'paid_at',
    ];

    protected $casts = [
        'metadata' => 'json',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', 'paid');
    }
}

// Order model addition:
public function payments(): HasMany
{
    return $this->hasMany(Payment::class);
}
```

### Services Needed

| Service | Responsibility |
|---|---|
| `PaymentService` | Orchestrates payment flow, delegates to specific gateway |
| `CashOnDeliveryService` | Handles COD-specific logic (status transitions) |
| `CashierService` | Wraps Laravel Cashier for Stripe/Paddle integration |
| `WalletPaymentService` | Handles wallet deduction/refund |

### Events Needed

| Event | Fired When | Listeners |
|---|---|---|
| `PaymentInitiated` | Payment process starts | Log payment, send notification |
| `PaymentSucceeded` | Payment confirmed by gateway | Update order status, release cart, send receipt |
| `PaymentFailed` | Gateway rejects payment | Notify customer, release cart reservation |
| `PaymentRefunded` | Admin initiates refund | Update order status, return stock |

### Transaction Handling

All payment operations must be wrapped in database transactions:

```php
DB::transaction(function () use ($order, $request) {
    $payment = Payment::create([...]);

    if ($paymentMethod === 'stripe') {
        $charge = $this->cashierService->charge($order, $request);
        $payment->update([
            'transaction_id' => $charge->id,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    $order->update(['payment_status' => 'paid']);
    event(new PaymentSucceeded($order, $payment));
});
```

---

## Flow Diagrams

### Cash On Delivery Flow

```
Customer selects COD
     │
     ▼
Order created (status: processing, payment_status: cash_on_delivery)
     │
     ▼
Order delivered
     │
     ▼
Admin marks payment as received
     │
     ▼
Payment status updated to 'paid'
     │
     ▼
Event: PaymentSucceeded fired → receipt emailed
```

### Cashier (Stripe) Flow

```
Customer selects Stripe
     │
     ▼
Order created (status: pending, payment_status: pending)
     │
     ▼
Cashier::checkout() → Redirect to Stripe Checkout
     │
     ▼
Customer completes payment on Stripe
     │
     ▼
Stripe webhook → PaymentSucceeded
     │
     ▼
Order status → processing
Payment status → paid
Transaction recorded
     │
     ▼
Cart finalized, receipt emailed
```

---

## Key Concepts

### Payment Method vs Payment Status

| Concept | Definition | Examples |
|---|---|---|
| **Payment Method** | The instrument or service used to transfer funds | `cod`, `credit_card`, `stripe`, `wallet`, `bank_transfer` |
| **Payment Status** | The current state of the payment lifecycle | `pending`, `processing`, `paid`, `failed`, `refunded`, `partially_refunded` |

A payment method does not change for an order. A payment status transitions through a lifecycle:
```
pending → processing → paid (success)
pending → processing → failed → pending (retry)
paid → refunded (admin action)
```

### Payment Transaction vs Order Transaction

| Concept | Table | Purpose |
|---|---|---|
| **Payment Transaction** | New `payments` table | Full payment lifecycle per attempt: method, status, gateway response, timestamps |
| **Order Transaction** | Existing `transactions` table | Simple invoice tracking: `invoice_id`, `payment_method` — no lifecycle management |

The existing `transactions` table is too simple for a robust payment system. A new `payments` table (or adding columns to `transactions`) would provide proper status tracking.
