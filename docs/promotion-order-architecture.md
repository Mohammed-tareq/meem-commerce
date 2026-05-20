# Promotions, Coupons, Cart & Order — Architecture and Lifecycle

This document explains, in detail, the full lifecycle and architecture for promotions, coupons, the cart, and order flows in the project. It is written for backend engineers and architects to understand exactly how the system behaves, why it behaves that way, and where to improve it.

> Reference files read while preparing this document:
>
> - `app/Services/General/PromotionService.php`
> - `app/Services/General/PromotionEngine/PromotionEligibilityResolver.php`
> - `app/Services/General/PromotionEngine/PromotionResult.php`
> - `app/Services/General/PromotionEngine/Contracts/PromotionStrategy.php`
> - `app/Services/General/PromotionEngine/Strategies/AbstractPromotionStrategy.php`
> - `app/Services/General/PromotionEngine/Strategies/PercentagePromotionStrategy.php`
> - `app/Services/General/PromotionEngine/Strategies/FixedPromotionStrategy.php`
> - `app/Services/General/PromotionEngine/Strategies/GiftPromotionStrategy.php`
> - `app/Services/General/CartInventoryService.php`
> - `packages/marvel/src/Database/Models/Promotion.php`
> - `packages/marvel/src/Database/Models/Coupon.php`
> - `packages/marvel/src/Database/Models/Cart.php`
> - `app/Services/General/OrderService.php`

---

## 1. High-Level System Architecture

### Overview

The system separates concerns into a set of services and models:

- Cart & Inventory: `CartInventoryService` — reserves, releases and finalizes stock; touches `carts`, `cart_items`, `products`, and `product_variants`.
- Promotions: `PromotionService` — orchestrates eligibility queries and applying promotions; uses `PromotionEligibilityResolver`.
- Promotion Engine: `PromotionEligibilityResolver` + `PromotionStrategy` implementations — decide eligibility and compute discounts/gifts.
- Orders: `OrderService` — converts cart into an `Order`, persists promotion and coupon details, creates `order_items` and `Transaction`.
- Models: `Promotion`, `Coupon`, `Cart`, `CartItem`, `Product`, `ProductVariant`, `Order`, `Transaction`.

### Main modules and responsibilities

- `CartInventoryService`
    - Methods: `reserveItem`, `reserveGiftItem`, `releaseItem`, `releaseCart`, `finalizeCart`, `ensureCartReservation`.
    - Responsible for all stock updates and `reserved_quantity` management.

- `PromotionService`
    - Methods: `eligiblePromotions`, `eligiblePromotionsPayload`, `applySelectedPromotion`, `incrementUsage`.
    - Bridges controllers and `PromotionEngine` and applies gift items.

- `PromotionEligibilityResolver`
    - Maps promotion type to a strategy and coordinates eligibility checks.

- `PromotionStrategy` (contract)
    - Methods: `eligible(Promotion, Cart, subtotal, matchedQty): bool`, `apply(...): PromotionResult`.

- `OrderService`
    - Methods handling checkout flows: `calcInvoicePrice`, `addItemsInOrder`, `calculateCheckoutTotals`, `saveOrderInDatabase`.

### Request lifecycle (simplified)

User → API Controller → `CartInventoryService` (reserve item) → `PromotionService` → `PromotionEligibilityResolver` → `PromotionStrategy` → `PromotionResult` → `PromotionService` (apply gifts, update cart) → `OrderService` (persist order) → `CartInventoryService` (finalize stock)

Sequence (Mermaid):

```mermaid
sequenceDiagram
  participant User
  participant API
  participant CartInventoryService
  participant PromotionService
  participant PromotionEligibilityResolver
  participant Strategy
  participant DB

  User->>API: Add item / Checkout / Request eligible promotions
  API->>CartInventoryService: reserveItem / getActiveCart
  CartInventoryService->>DB: lock Cart & stock (lockForUpdate)
  API->>PromotionService: eligiblePromotions or applySelectedPromotion
  PromotionService->>PromotionEligibilityResolver: eligible/resolve
  PromotionEligibilityResolver->>Strategy: eligible() / apply()
  Strategy->>DB: (reads) promotion, products, gift_products
  Strategy->>PromotionService: PromotionResult
  PromotionService->>CartInventoryService: reserveGiftItem (if gift)
  CartInventoryService->>DB: reserve stock rows
  PromotionService->>DB: update Cart.total_price
```

---

## 2. Promotion Lifecycle (VERY IMPORTANT)

This section explains how promotions are created, validated, selected, and applied.

### Creation & storage

- Promo creation occurs through admin surfaces (not shown in provided code). When a `Promotion` model is created:
    - `creating` hook ensures `code` exists.
    - `saving` syncs `value`/`discount` fields if one is changed.
- Storage layout:
    - `promotions` table holds promotion data fields (`type_amount`, `discount`/`value`, `max_discount_amount`, `apply_to`, `required_quantity_type`, `start_at`, `end_at`, `limiter`, `usage`, etc.).
    - `promotion_product` pivot maps promotions to the specific products they apply to.
    - `promotion_gift_products` pivot maps promotions to gift products and pivot `quantity`.

### Activation & Validation

- Querying active promotions uses `Promotion::valid()` which checks:
    - `status` true
    - `limiter` null or `usage < limiter`
    - `start_at` null or `start_at <= today()`
    - `end_at` null or `end_at >= today()`

- Model-level `isValid()` performs the same business checks and is used by strategies.

### How promotions reach the cart

- `PromotionService::eligiblePromotions(Cart)`:
    - Loads cart items and product relations.
    - Computes `subtotal()` (excludes `is_gift` items).
    - Loads `Promotion::valid()->with(['products', 'giftProducts'])->get()`.
    - Calls `PromotionEligibilityResolver::eligible` passing the cart, promotions, and subtotal.

### Strategy selection

- `PromotionEligibilityResolver` maps promotion mount types (enum `type_amount`) to concrete strategy classes:
    - `PERCENTAGE` → `PercentagePromotionStrategy`
    - `FIXED_RATE` → `FixedPromotionStrategy`
    - `GIFT` → `GiftPromotionStrategy`
- Strategies are resolved using `app(StrategyClass::class)` so they can be injected and tested via the Laravel IoC container.

### How strategies differ

- `AbstractPromotionStrategy::eligible()` performs common checks:
    - `promotion->isValid()`
    - `subtotal >= promotion->minimum_order_amount` (if set)
    - `promotion->isRequiredQuantityTrue($matchedQuantity)`

- `PercentagePromotionStrategy` and `FixedPromotionStrategy`:
    - Use `Promotion::discountAmount($price, $qty)` to compute discount amount.
    - Return `PromotionResult($promotion, discount)`.

- `GiftPromotionStrategy`:
    - Extends eligibility to require `giftProducts->isNotEmpty()`.
    - Returns `PromotionResult($promotion, 0.0, $giftItems)` where `giftItems` is derived from `giftProducts` pivot including `quantity`.

### Why strategy pattern & IoC

- Strategy isolates promotion-specific logic into small classes, following Single Responsibility and enabling Open/Closed.
- Using the container (`app()`) allows strategies to receive their own dependencies and makes testing/mocking easier.

### Sequence diagram (apply promotion)

```mermaid
sequenceDiagram
  participant Admin
  participant DB
  participant User
  participant API
  participant PromotionService
  participant Resolver
  participant Strategy

  Admin->>DB: create promotion + pivot rows
  User->>API: view cart / apply promotion
  API->>PromotionService: eligiblePromotions
  PromotionService->>Resolver: eligible(promotions, subtotal, cart)
  Resolver->>Strategy: eligible/apply
  Strategy->>PromotionService: PromotionResult
  PromotionService->>CartInventoryService: reserveGiftItem (if gift)
  PromotionService->>DB: update cart.total_price
```

---

## 3. Cart Lifecycle

This section dives deeply into adding, updating, removing items and recalculation flows.

### Add to cart flow (detailed)

1. Controller receives an add-item request.
2. Controller obtains the active cart for a user and calls `CartInventoryService::reserveItem($cart, $product, $variant, $quantity, $mode, $attributes)`.
3. `reserveItem` runs a `DB::transaction(...)` and uses `lockForUpdate()` to lock the `carts` row.
4. `findCartItemForLock` locks the relevant `cart_items` row for update (if it exists).
5. `lockInventoryRow($product,$variant)` locks the `products` or `product_variants` row.
6. The method computes `delta` and calls `reserveStock` or `releaseStock` to update the `reserved_quantity` and `in_stock` flags on inventory rows.
7. It creates or updates `cart_items` with `quantity`, `reserved_quantity`, `price`, `total_price`.
8. `touchCartReservation` updates `carts` metadata (`status='active'`, `reserved_at`, `expires_at`).
9. Transaction is committed.

### Update quantity flow

- Reuses `reserveItem` with `mode='set'` or simple add mode; same locking/reserve logic. Ensures `desiredQuantity >= 1` else throws.

### Remove item flow

- `releaseItem($cartItem, $deleteItem)` encapsulates: lock `cart_items`, lock inventory row via `lockInventoryRowByItem`, call `releaseStock`, then delete or update item.

### Recalculate totals

- `PromotionService::subtotal(Cart)` sums `cart->items->reject(is_gift)->sum('total_price')` and rounds to 2 decimals.
- `eligiblePromotions` and `applySelectedPromotion` call this subtotal and then run the resolver.
- `applySelectedPromotion` removes existing gift items (calls `removeGiftItems` → `CartInventoryService->releaseItem`), re-evaluates, applies new gift items (reserving stock) and updates `cart->total_price`.

### Promotion recalculation flow

- `eligiblePromotions` computes available promotions and returns an array of `PromotionResult`.
- `applySelectedPromotion` loads and locks the promotion row if a promotion id is provided: `Promotion::valid()->whereKey($id)->lockForUpdate()->first()` to avoid limiter races.
- If `$result` has gift items, `applyGiftItems` will call `CartInventoryService::reserveGiftItem` for each gift product.
- Discount calculation: `discount = min($subtotal, (float) ($result?->discount ?? 0));` then `finalTotal = round(max(0, subtotal - discount), 2)` and `cart->total_price` is updated.

### Coupon validation & flow (at checkout)

- After promotions applied, `OrderService::calculateCheckoutTotals` calls `calculatePriceByCoupon` which calls `CalcPriceByCoupon`.
- `CalcPriceByCoupon` loads the coupon via `Coupon::valid()->where('code', $couponId)` and uses `ProductPricingService::calculateCouponPrice` for the final reduction.
- Coupons are applied after promotions in the current flow.

### Shipping/tax calculations

- Not present in provided code. Hook points exist in `OrderService::saveOrderInDatabase` which writes `shipping_price`. Add shipping/tax calculation before final persistence.

### Reserved quantity updates & locking rationale

- Inventory and cart rows are updated using `lockForUpdate()` inside DB transactions to ensure that concurrent operations serialize access to these rows and prevent double reservation of the same stock.
- This is the safest approach for correctness but may increase contention under heavy concurrency.

### Tables touched (typical)

- `carts`, `cart_items`, `products`, `product_variants`, `promotions`, `promotion_product`, `promotion_gift_products`, `orders`, `order_items`, `transactions`, `coupons`, `coupon_usages`.

---

## 4. Order Lifecycle

### How a cart becomes an order

1. `OrderController::checkout` validates order request and ensures cart exists and is reserved with `CartInventoryService::ensureCartReservation`.
2. `OrderService::calcInvoicePrice` starts a DB transaction, re-calculates totals via `calculateCheckoutTotals` (this calls `PromotionService::applySelectedPromotion`) and updates `cart->total_price`.
3. Payment provider (Myfatora) flow creates invoice; after successful payment, callback calls `OrderService::addItemsInOrder`.
4. `addItemsInOrder` re-evaluates `calculateCheckoutTotals` (defensive re-check), calls `saveOrderInDatabase`, `createOrderItems`, increments promotion usage via `PromotionService::incrementUsage`, commits transaction.
5. Upon successful payment in `checkoutCallback`, `cartInventoryService->finalizeCart($cart)` is called to decrement `stock_quantity` and remove the reserved items.

### Transaction boundaries

- `OrderService::calcInvoicePrice` uses `DB::beginTransaction()` and commits after updating cart price.
- `OrderService::addItemsInOrder` uses transactions around saving order and order items and incrementing usage.
- `CartInventoryService` uses its own transactions for each reserve/release/finalize operation.

### Concurrency protection

- `lockForUpdate()` used on `cart` and inventory rows and promotions when needed.
- `Promotion::incrementUsage()` uses `lockForUpdate()` with limiter checks to avoid oversubscribing a limited promotion.

### Rollback behavior

- Exceptions in transactional sections trigger `DB::rollBack()`.
- Reservation operations throw on insufficient stock which rolls back the transaction and conserves invariants.

### Inventory updates and finalization

- `reserveStock` modifies `reserved_quantity` and `in_stock`.
- `finalizeStock` decrements `stock_quantity`, decrements `reserved_quantity`, increments `sold_quantity` and updates `in_stock`.
- `finalizeCart` runs through all cart items and finalizes stock then deletes cart items and sets `carts.status='checked_out'`.

### Promotion persistence into orders

- `saveOrderInDatabase` persists `promotion_id`, `promotion_code`, `promotion_type`, and `promotion_discount` into the `orders` table so historical order reflects discounts as applied.

---

## 5. Apply To All vs Specific Logic (VERY IMPORTANT)

This is a critical, subtle area where business intent and code diverge in the current implementation — it's explained and evaluated below.

### How `apply_to` is represented

- `Promotion` model has `apply_to` column. `appliesToAllProducts()` returns true when `apply_to === 'all_products'`.
- If `appliesToAllProducts()` is false, the promotion must have entries in `promotion_product` pivot explictly listing product ids.

### How the system determines matching

- `PromotionEligibilityResolver::matchedQuantity` builds a list of `requiredProductIds` from `$promotion->products`.
- It filters `cart->items` skipping `is_gift` items. For each item:
    - If `promotion->appliesToAllProducts()` → item is matched.
    - Else checks `in_array($item->product_id, $requiredProductIds)` → if true, it's matched.
- The matched quantity is the sum of matching items' `quantity`.

### Eligibility checking with matchedQuantity

- `AbstractPromotionStrategy::eligible()` checks `isValid()`, `subtotal >= minimum_order_amount`, and `isRequiredQuantityTrue($matchedQuantity)`.
- `isRequiredQuantityTrue` checks `required_quantity_type` (minimum units that must be present to qualify).

### Important behavioral nuance (current code)

- The resolver computes `matchedQuantity` and passes it to `eligible()` and `apply()`.
- However, strategies currently call `$promotion->discountAmount($subtotal, $matchedQuantity)` passing the _entire_ cart `subtotal` as price, not the sum of matched products' totals.
- This implies that, as implemented today, if a promotion is set to apply only to specific products, the discount calculation still uses full cart subtotal — meaning the discount is effectively applied to whole cart, provided matchedQuantity passes thresholds.

### Consequences & whether this is a bug

- If business expects discounts to apply only to matching items (common expectation for `apply_to` specific promotions), current behavior is incorrect and should be fixed.
- If business intended `apply_to` to only control eligibility (which items count towards minimum quantity) but discount can be computed over entire subtotal, then current behavior is intentional. This must be clarified.

### Recommended correction (if needed)

- Compute `matchedProductsTotal` inside the resolver or strategy and pass that instead of `$subtotal` when `apply_to !== 'all_products'`. Example:
    - `matchedProductsTotal = cart->items.filter(product_id in requiredProductIds).sum('total_price')`
    - then call `discount = $promotion->discountAmount(matchedProductsTotal, $matchedQuantity)`

### Real example (explicit walk-through)

Cart:

- iPhone (Product A id=1) qty=1 price=1000
- AirPods (Product B id=2) qty=2 price=200 each → 400
- T-Shirt (Product C id=3) qty=3 price=50 each → 150
- Subtotal = 1550

Promotion:

- 20% discount only for Electronics
- `apply_to` = specific products, pivot products = [1, 2]

Current internal processing:

1. `matchedQuantity` = sum of qty for items with id 1 or 2 = 1 + 2 = 3
2. Strategy eligibility passes because `matchedQuantity` satisfies required thresholds.
3. `PercentagePromotionStrategy->apply()` calls `discountAmount($subtotal=1550, $matchedQuantity)` → discount = 20% of 1550 = 310.
4. Final total = 1240.

Expected behavior (if discount should only be applied to matched products):

1. `matchedProductsTotal` = 1000 + 400 = 1400
2. Discount = 20% of 1400 = 280
3. Final total = 1270

This demonstrates the difference and shows the code will currently give a larger discount than intended if `discountAmount` is applied to the full subtotal.

### Filtering logic, edge cases and priority rules

- If `applies_to=false` and `promotion->products` is empty: `resolve()` returns `null` and promotion is ignored.
- Gifts: the resolver will add gift items, and `PromotionService` will remove existing gift items before applying new ones to avoid duplication.
- Promotions with `limiter` must be guarded: resolver `resolve()` doesn't itself increment `usage`; `OrderService::addItemsInOrder` calls `promotionService->incrementUsage()` after successfully creating the order.

---

## 6. Coupon Flow

### Validation & expiry

- `Coupon::scopeValid()` implements checks similar to promotions: `status`, `start_date`, `end_date`, `limiter` vs `used`.
- `CalcPriceByCoupon` uses this scope to fetch a valid coupon and then uses `ProductPricingService::calculateCouponPrice` to compute the price after coupon.

### Usage limits & user limits

- `coupon_usages` pivot tracks `order_id`, `used_at` per user; `OrderService::recordCouponUsage` updates this pivot on order completion.
- `checkCouponUsage` prevents re-use for a given user by checking `CouponUsage` with `used_at` present.

### Combining coupons and promotions

- Promotions are applied first (via `applySelectedPromotion`) and coupons are applied afterward in `calculateCheckoutTotals`.
- `coupon_discount` is computed as the difference between price after promotion and final price after coupon. This makes coupons stack on top of promotions.

### Conflict handling and edge cases

- There is no `cannot_combine` property; business rules must be added if some promotions/coupons should not be combinable.
- Coupon invalidation after order creation: the `used` counter and pivot entries are updated on a successful order completion flow.

---

## 7. Database Deep Analysis

### Important tables & pivots (summary)

- `promotions` (fields: id, name, type, type_amount, value, discount, max_discount_amount, code, required_quantity_type, minimum_order_amount, apply_to, limiter, usage, start_at, end_at, status)
- `promotion_product` (promotion_id, product_id)
- `promotion_gift_products` (promotion_id, product_id, quantity)
- `carts` (user_id, coupon, total_price, status, reserved_at, expires_at)
- `cart_items` (cart_id, product_id, product_variant_id, quantity, reserved_quantity, price, total_price, is_gift, promotion_id)
- `products`, `product_variants` (stock_quantity, reserved_quantity, sold_quantity, in_stock)
- `orders`, `order_items`
- `transactions` (invoice mappings)
- `coupons`, `coupon_usages`

### Relationships

- `Promotion` belongsToMany `Product` via `promotion_product`.
- `Promotion` belongsToMany `giftProducts` via `promotion_gift_products` with pivot `quantity`.
- `Cart` hasMany `CartItem`.
- `CartItem` belongsTo `Product` and optionally `ProductVariant`.

### Index & performance suggestions

- Add composite indexes to support `Promotion::valid()`:
    - `(status, start_at)`, `(status, end_at)`, `(status, usage, limiter)` or similarly tailored on your DB.
- Index pivot tables: `promotion_product(promotion_id, product_id)` and `promotion_gift_products(promotion_id, product_id)`.
- Index `cart_items(cart_id, product_id, product_variant_id)` to accelerate finding items for lock operations.
- Index `transactions(invoice_id)` for payment callbacks.

---

## 8. Laravel Engineering Analysis

### Patterns and principles

- Strategy Pattern: used for promotions to separate different discount/gift computations.
- Service Layer: `CartInventoryService`, `PromotionService`, `OrderService` provide domain operations.
- Dependency Injection: services injected via constructors; strategies resolved via `app()`.
- Transactions: used extensively in `CartInventoryService` and `OrderService` for correctness when modifying multiple rows.

### Clean architecture evaluation

- Strengths:
    - Clear separation of responsibilities.
    - Small, testable strategy classes.
    - Use of Eloquent model methods for domain logic (`Promotion::discountAmount`, `isValid`).
- Weaknesses:
    - Nudges toward anemic controllers: controllers orchestrate service calls but services already contain complex logic — this is fine but requires good tests.
    - Potential implicit transaction assumptions (some methods rely on callers to provide transactional context).

---

## 9. Performance & Scalability

### N+1 & eager loading

- The code uses `with(['items.product', 'items.productVariant'])` and `Promotion::with(['products', 'giftProducts'])` in key places to avoid N+1 queries.

### Caching opportunities

- Cache static promotion metadata for short TTLs but avoid caching limiter/usage without careful invalidation.
- Cache product details that don’t change often; keep inventory dynamic.

### Queue opportunities

- Offload notification and analytics work to queues.
- Consider queueing heavy finalization or reconciliation tasks if you need to reduce immediate DB locking time.

### Locking and scaling

- Pessimistic locking ensures correctness by serializing access to critical rows. Under heavy concurrency it can become a bottleneck.
- Consider increasing horizontal scaling and using techniques like optimistic locking, stock reservation using Redis, or queuing checkout requests for high-throughput scenarios.

---

## 10. Security & Edge Cases

- Duplicate coupon prevention: ensured via `coupon_usages` pivot per user and `recordCouponUsage`.
- Concurrent checkout: `lockForUpdate()` and transactions used to prevent double reservations and limiter race conditions.
- Expired/invalid promotions: re-evaluation happens in `addItemsInOrder` ensuring final saved order matches current rules; errors are surfaced.
- Negative totals: `final_total` computed as `round(max(0, subtotal - discount), 2)` to prevent negative values.

---

## 11. Visuals (Mermaid)

### Overall request flow (repeated)

```mermaid
sequenceDiagram
  participant U as User
  participant C as Controller (API)
  participant CIS as CartInventoryService
  participant PS as PromotionService
  participant PER as PromotionEligibilityResolver
  participant STR as PromotionStrategy
  participant DB as Database

  U->>C: add item / view cart / checkout
  C->>CIS: reserveItem / getActiveCart
  CIS->>DB: lock cart & inventory (lockForUpdate)
  C->>PS: eligiblePromotions / applySelectedPromotion
  PS->>PER: eligible/resole
  PER->>STR: eligible() / apply()
  STR->>PS: PromotionResult
  PS->>CIS: reserveGiftItem (if gift)
  PS->>DB: update cart.total_price
  C->>OrderService: calcInvoicePrice / addItemsInOrder
```

### Class relationships

```mermaid
classDiagram
  class PromotionEligibilityResolver {
    - strategies: array
    + eligible(cart, promotions, subtotal)
    + resolve(cart, promotion, subtotal)
  }
  class PromotionStrategy {
    <<interface>>
    + eligible(promotion,cart,subtotal,matchedQuantity)
    + apply(promotion,cart,subtotal,matchedQuantity)
  }
  class AbstractPromotionStrategy {
    + eligible(...)
  }
  class PercentagePromotionStrategy
  class FixedPromotionStrategy
  class GiftPromotionStrategy

  PromotionEligibilityResolver ..> PromotionStrategy : uses
  PromotionStrategy <|-- AbstractPromotionStrategy
  AbstractPromotionStrategy <|-- PercentagePromotionStrategy
  AbstractPromotionStrategy <|-- FixedPromotionStrategy
  AbstractPromotionStrategy <|-- GiftPromotionStrategy
```

### Apply-To-All vs Specific example (Mermaid)

```mermaid
graph LR
  subgraph Cart
    A[iPhone id=1 qty=1 price=1000]
    B[AirPods id=2 qty=2 price=200]
    C[T-Shirt id=3 qty=3 price=50]
  end
  Promo[Promotion: 20% apply_to=specific products {1,2}]
  Cart --> Promo
  Promo -->|matchedQuantity=3| Strategy
  Strategy -->|discount=20% * 1550| Result
```

---

## 12. Final Summary

### Full request lifecycle summary

- Modifying cart reserves inventory with `lockForUpdate()` and stores cart items.
- Promotion eligibility uses `Promotion::valid()` and a Strategy-based engine.
- Applying promotions can add gift items and update cart price.
- Checkout re-evaluates promotions and coupons inside DB transactions; order objects are persisted including promotion/coupon metadata and `promotion->usage` is incremented with locking.
- After payment success, `CartInventoryService::finalizeCart` finalizes stock counts.

### Important classes/files to study first

- `app/Services/General/PromotionService.php`
- `app/Services/General/PromotionEngine/PromotionEligibilityResolver.php`
- `app/Services/General/PromotionEngine/Strategies/*`
- `app/Services/General/CartInventoryService.php`
- `app/Services/General/OrderService.php`

---

## Developer Changes Implemented (code patch summary)

I implemented a production-grade fix for the `apply_to` bug so promotions compute discounts from the correct scope (matched items only). Changes made in the codebase during this task:

- Added DTO: `app/Services/General/PromotionEngine/PromotionEligibility.php` (holds `matchedItems`, `matchedSubtotal`, `matchedQuantity`).
- Refactored resolver: `app/Services/General/PromotionEngine/PromotionEligibilityResolver.php` now computes `matchedItems`, `matchedSubtotal`, and `matchedQuantity` and returns/uses these when evaluating strategies.
- Updated strategy contract: `app/Services/General/PromotionEngine/Contracts/PromotionStrategy.php` to accept `PromotionEligibility`.
- Updated strategies to use matched subtotal/quantity:
    - `app/Services/General/PromotionEngine/Strategies/PercentagePromotionStrategy.php`
    - `app/Services/General/PromotionEngine/Strategies/FixedPromotionStrategy.php`
    - `app/Services/General/PromotionEngine/Strategies/GiftPromotionStrategy.php` (eligibility uses matched scope; gift items reserved unchanged)
- Updated abstract strategy checks: `app/Services/General/PromotionEngine/Strategies/AbstractPromotionStrategy.php` to validate against `matchedSubtotal` and `matchedQuantity`.
- Added unit tests: `tests/Unit/PromotionEligibilityResolverTest.php` covering apply-to-all, specific-products, and gift promotions.

Test run summary:

- Ran focused unit tests: `php vendor/bin/phpunit tests/Unit/PromotionEligibilityResolverTest.php` — 3 tests passed.
- Ran full test suite: `php vendor/bin/phpunit` — executed without failures in this environment. (Note: PHPUnit XML config warns about deprecated schema.)

Why this change matters:

- The resolver now explicitly models the promotion scope (`matchedItems` and `matchedSubtotal`) so discount calculations are accurate and unambiguous.
- Strategies remain simple and SOLID: they receive a domain DTO describing eligible items, so adding new scope types (categories, variants, brands) requires only updating the resolver matching logic, not each strategy.

If you want, I can now:

- Run the full test suite and save the JUnit XML output.
- Add integration tests covering `PromotionService::applySelectedPromotion` and `OrderService` checkout flow.
- Implement category/variant matching and migrations for pivot tables if you want to enable those scopes now.

- `packages/marvel/src/Database/Models/Promotion.php`

### Architectural strengths

- Clear separation of concerns, strategy pattern, and transaction/lock usage for correctness.

### Architectural weaknesses

- Discount base ambiguity for `apply_to` specific promotions (fix recommended).
- Potential contention from heavy use of `lockForUpdate()` under high concurrency.
- Some methods rely implicitly on caller transactions which is brittle.

### Suggested improvements (short list)

1. Fix discount calculation for specific-product promotions: compute `matchedProductsTotal` and use it for discount calculation.
2. Enforce/clarify transactional boundaries in service methods or make important methods internally transactional.
3. Add indexes where recommended and profile lock wait times.
4. Add explicit `cannot_combine` flags if business requires exclusivity rules between promotions and coupons.
5. Add thorough unit/integration tests for promotion application, gift reservation, and concurrent limiter edge cases.

---

If you want, I can now implement the recommended code fix that computes the matched-products total and use it when applying promotions (patch and unit tests). Let me know if you'd like me to do that next and I will create the patch and tests.
