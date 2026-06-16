# Error Fix Status Report

Each bug from the report is checked against the current codebase. Status: ✅ **Fixed** / ❌ **Not Fixed** / ⚠️ **Partial**

---

## 1. Contact Us — POST send reply

**Status:** ✅ **Fixed**

**Bug:** `POST /contacts/{id}/replay` returned raw `ModelNotFoundException` (`"No query results for model..."`) as a 500.

**Fix applied:** `packages/marvel/src/Http/Controllers/ContactController.php:81`
```php
// Before:
catch (MarvelException $e) { throw new MarvelException(NOT_FOUND); }

// After:
catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { throw new MarvelException(NOT_FOUND); }
```

`ModelNotFoundException` does NOT extend `MarvelException`, so the old catch block never caught it. Now it does.

**Also fixed in same file:** `show()`, `destroy()`, `deleteAll()`, `deleteAllReadContacts()` — same pattern.

**Result:** Returns proper 404 with `"Resource Not Found"` instead of raw SQL/Model error.

---

## 2. Admin Users — POST add admin (duplicate email)

**Status:** ❌ **Not Fixed**

**Bug:** No `unique:users,email` validation on `AdminCreateUserRequest`, so duplicate email throws raw SQL `QueryException` (23000 integrity constraint violation) as a 500.

**Current code:** `packages/marvel/src/Http/Requests/AdminCreateUserRequest.php:33`
```php
"email" => "required|email",  // missing: |unique:users,email
```

**Plan to fix:**

1. Add `unique` rule to the email validation:
   ```php
   // AdminCreateUserRequest.php
   'email' => 'required|email|unique:users,email',
   ```

2. If email is provided on update (for admin update method), add `->ignore($id)`:
   ```php
   'email' => 'required|email|unique:users,email,' . $this->route('id'),
   ```

3. As a safety net, the global exception handler already catches `QueryException` (added in `Handler.php`) and returns HTTP 409, so raw SQL won't leak even without the validation fix.

---

## 3. Roles — PUT update role

**Status:** ⚠️ **Partially Fixed**

**Bug:** `Role::findById($id, 'api')` throws `RoleDoesNotExist` → caught by generic `catch (\Exception $e)` → returns misleading `"something went wrong"` (500).

**Fix applied:** `packages/marvel/src/Http/Controllers/RoleAndPermissionController.php`
```php
catch (RoleDoesNotExist|ModelNotFoundException $e) {
    throw new MarvelException(NOT_FOUND);
} catch (\Exception $e) {
    return $this->apiResponse(SOMETHING_WENT_WRONG, 500, false);
}
```

**What's covered:** Role-not-found now returns 404. ✅

**What's NOT covered:** If the update fails for another reason (e.g., validation, DB error), it still falls through to the generic `catch (\Exception)` returning 500 "something went wrong". This is the existing behavior and is acceptable as a safety net.

---

## 4. Roles — DELETE delete role

**Status:** ⚠️ **Partially Fixed** (same as #3)

**Bug:** `Role::findById($id, 'api')` throws `RoleDoesNotExist` → caught by generic `catch (\Exception $e)` → misleading 500.

**Fix applied:** Same as #3 — added `RoleDoesNotExist` catch.

**What's NOT covered:** If role has users assigned with foreign key constraint, delete will throw `QueryException`. Plan to add:
```php
catch (QueryException $e) {
    return $this->apiResponse('Cannot delete role with assigned users', 409, false);
}
```

---

## 5. Permissions — GET permissions (labels not localized)

**Status:** ❌ **Not Fixed**

**Bug:** `GET /permissions` returns `"label": "permissions.view-brand"` (the translation key) instead of the localized string like `"View Brand"`.

**Current code:** `packages/marvel/src/Http/Resources/PermissionResource.php:18-23`
```php
$translation = __('permissions.' . $this->name);
return [
    "id" => $this->id,
    "name" => $this->name,
    "label" => $translation ?? null,  // returns key if not in lang file
];
```

**Root cause:** `__('permissions.' . $this->name)` returns the key itself when no translation exists for it. The `?? null` fallback doesn't trigger because `__()` returns a string, not null.

**Plan to fix:**

Option A — Check if translation exists:
```php
$key = 'permissions.' . $this->name;
$label = __($key);
return [
    "id" => $this->id,
    "name" => $this->name,
    "label" => $label !== $key ? $label : $this->name,
];
```

Option B — Add missing translation entries to `resources/lang/en/permissions.php` and `resources/lang/ar/permissions.php` for all permission names. This is the proper fix.

---

## 6. Roles — POST remove role from user

**Status:** ❌ **Not Fixed**

**Bug:** `POST /users/{userId}/remove-role` returns `"something went wrong"` (500).

**Current code:** `packages/marvel/src/Http/Controllers/RoleAndPermissionController.php:146`
```php
catch (\Exception $e) {
    return $this->apiResponse(SOMETHING_WENT_WRONG, 500, false);
}
```

**Root cause:** Unknown — could be pivot table issue, `removeRole()` failing, or `User::findOrFail()` throwing `ModelNotFoundException` (which also gets caught by `\Exception`).

**Plan to fix:**

1. Add `ModelNotFoundException` specific catch before generic:
   ```php
   catch (ModelNotFoundException $e) {
       throw new MarvelException(NOT_FOUND);
   } catch (\Exception $e) {
       \Log::error('removeRoleFromUser failed: ' . $e->getMessage(), ['userId' => $userId]);
       return $this->apiResponse(SOMETHING_WENT_WRONG, 500, false);
   }
   ```

2. Add logging to capture the actual error for debugging.

---

## 7. Categories — DELETE delete category

**Status:** ✅ **Fixed**

**Bug:** Deleting a category with children/products threw raw SQL `QueryException` (FK constraint 1451) as a 500.

**Fix applied:** `packages/marvel/src/Http/Controllers/CategoryController.php:381-384`
```php
catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    throw new MarvelException(NOT_FOUND);
} catch (\Illuminate\Database\QueryException $e) {
    throw new MarvelException('Cannot delete category with existing associated resources');
}
```

**Result:** Returns clean error messages:
- Category not found → `404 "Resource Not Found"`
- Category has children/products → `500 "Cannot delete category with existing associated resources"`

---

## 8. Products — POST create product (validation keys)

**Status:** ✅ **Already Working — Not a Bug**

**Bug claim:** Validation requires flat keys like `name`, `description` instead of localized `name.en`, `name.ar`.

**Current code:** `packages/marvel/src/Http/Requests/ProductCreateRequest.php:48-51`
```php
'name'           => ['required', 'array'],
'name.*'         => ['required', 'string', 'max:255'],
'description'    => ['required', 'array'],
'description.*'  => ['required', 'string', 'max:10000'],
```

**Reality:** The validation uses `'name' => ['required', 'array']` with `'name.*'` wildcard — this correctly accepts `{"en": "Product", "ar": "منتج"}` format. The `HasTranslations` trait on the model stores it as JSON.

The error `"The name field is required."` in the bug report occurs when the client sends `name.en` and `name.ar` as **flat keys** instead of a nested object:
```json
// WRONG — will fail validation
{ "name.en": "Product", "name.ar": "منتج" }

// CORRECT — passes validation
{ "name": { "en": "Product", "ar": "منتج" } }
```

**No code change needed.** This is working as designed. Documentation should show the correct nested format.

---

## 9. Products — PUT update product (null pointer)

**Status:** ✅ **Fixed**

**Bug:** `Product::find($id)` returns `null` for non-existent product, then `$product->id` throws `TypeError` (PHP 8) which is NOT caught by `catch (Exception $e)`.

**Fix applied:** `packages/marvel/src/Database/Repositories/ProductRepository.php:118`
```php
// Before:
$product = Product::find($id);

// After:
$product = Product::findOrFail($id);
```

**Result:** Non-existent product ID now properly throws `ModelNotFoundException` (which is already caught and returns 500 "Something went wrong").

---

## 10. Products — PUT update product (docs mismatch / status validation)

**Status:** ❌ **Not Fixed**

**Bug:** `status` field validation rejects certain values, and `discount_status` conditionally required.

**Current code:** `ProductUpdateRequest.php`:
```php
'status'          => ['sometimes', 'in:true,1,0,false'],
'discount_status' => ['required_if:has_discount,1', 'in:true,false,1,0'],
```

**Analysis:** The validation is correct — `status` accepts `true/false/1/0`. The error `"The selected status is invalid."` means the client sent an unsupported value (e.g., `"publish"`, `"draft"`).

**Plan to fix:**

1. If `status` should accept values like `"publish"`, `"draft"`, `"under_review"`, update the `ProductStatus` enum and the validation:
   ```php
   'status' => ['sometimes', Rule::in(ProductStatus::getValues())],
   ```

2. Update ApiDogs/Swagger docs to document the allowed `status` values.

---

## 11. Products — GET products (undocumented query params)

**Status:** ❌ **Not Fixed** (documentation)

**Plan to fix:** Add ApiDogs/Swagger annotations to the `ProductController@index` method documenting:
- `limit`, `page`, `search`, `orderBy`, `sortedBy`
- `category`, `shop`, `author`, `manufacturer`, `brand`
- `range[min]`, `range[max]`, `status`, `language`
- `with` (comma-separated relations)
- `include`

---

## 12. Product Reviews — GET reviews (405 Method Not Allowed)

**Status:** ❌ **Not Fixed**

**Bug claim:** `GET /products/{id}/reviews` returns 405.

**Current routes:** `packages/marvel/src/Rest/Routes.php`
```php
Route::apiResource('reviews', ReviewController::class, [
    'only' => ['store', 'update']          // in auth:sanctum group
]);
// and:
Route::apiResource('reviews', ReviewController::class, [
    'only' => ['destroy']                   // in super_admin group
]);
```

**Root cause:** There is NO `index` or `show` route registered for reviews! The index route is commented out at line 220-222:
```php
// Route::apiResource('reviews', ReviewController::class, [
//     'only' => ['index', 'show'],
// ]);
```

**Plan to fix:**

1. Uncomment the index/show routes for reviews:
   ```php
   Route::apiResource('reviews', ReviewController::class, [
       'only' => ['index', 'show'],
   ]);
   ```
   Place this in the public routes section (no auth).

2. Or add explicitly:
   ```php
   Route::get('reviews', [ReviewController::class, 'index']);
   Route::get('reviews/{id}', [ReviewController::class, 'show']);
   ```

---

## 13. Flash Sales — POST create flash sale (validation keys)

**Status:** ✅ **Already Working — Not a Bug**

**Bug claim:** Validation requires flat keys instead of localized.

**Current code:** `CreateFlashSaleRequest.php`
```php
'title'        => ['required', 'array'],
'title.*'      => ['required', 'string', 'min:3', 'max:70'],
'description'  => ['required', 'array'],
'description.*'=> ['required', 'string', 'max:1000'],
```

Same pattern as products — uses `'field' => ['required', 'array']` with `'field.*'` wildcard. Correctly accepts `{"en": "...", "ar": "..."}` format. **No code change needed.**

---

## 14. Flash Sales — PUT update flash sale (500 Type Error)

**Status:** ❌ **Not Fixed**

**Bug:** `PUT /flash-sales/{id}` returns:
```json
{
    "message": "Exception::__construct(): Argument #2 ($code) must be of type int, string given"
}
```

**Root cause:** Somewhere in the update flow, a `throw new \Exception('message', 'STRING_CODE')` is being called with a string as the second parameter (`$code` must be int). This could be in `FlashSaleRepository`, `FlashSaleController`, or the `UpdateFlashSaleRequest` validation.

**Plan to fix:**

1. Search for `throw new \Exception` in:
   - `packages/marvel/src/Database/Repositories/FlashSaleRepository.php`
   - `packages/marvel/src/Http/Controllers/FlashSaleController.php`

2. Find the line that passes a string as `$code`:
   ```php
   // BAD — string code:
   throw new \Exception($message, 'SOME_STRING');
   
   // GOOD — int code:
   throw new \Exception($message, 500);
   ```

3. Fix the string code to int, or change to `HttpException`/`MarvelException`.

---

## 15. Promotions — POST add promotion (validation keys)

**Status:** ✅ **Already Working — Not a Bug**

**Bug claim:** Validation requires flat keys instead of localized.

**Current code:** `PromotionRequest.php`
```php
"name"     => "required|array",
'name.*'   => ['required_with:name', ...],
```

Correctly accepts `{"en": "...", "ar": "..."}` format. **No code change needed.**

---

## 16. Promotions — PUT update promotion (400 Generic Error)

**Status:** ⚠️ **Partially Fixed**

**Bug:** Returns raw error key `"CHAWKBAZAR_ERROR.COULD_NOT_UPDATE_THE_RESOURCE"` instead of localized message.

**Fix applied:** `packages/marvel/src/Database/Repositories/PromotionRepository.php`
```php
catch (\Exception $e) {
    Log::error('Promotion update failed: ' . $e->getMessage());
    throw new HttpException(400, COULD_NOT_UPDATE_THE_RESOURCE);
}
```

**What's covered:** The actual error is now logged. ✅

**What's NOT covered:** The error key `CHAWKBAZAR_ERROR.COULD_NOT_UPDATE_THE_RESOURCE` is still returned as-is because the `apiResponse` translateNotice method only translates keys that exist in the `message.*` lang files. If the translation fails, the key is returned raw.

**Plan to fix:**

1. Verify translation exists in `resources/lang/en/message.php`:
   ```php
   return [
       'COULD_NOT_UPDATE_THE_RESOURCE' => 'Could not update the resource',
       // ...
   ];
   ```

2. Or in the controller, wrap with a `__()` call:
   ```php
   return $this->apiResponse(__('COULD_NOT_UPDATE_THE_RESOURCE'), 400, false);
   ```

---

## 17. Coupons — GET add coupon to cart (empty endpoint)

**Status:** ❌ **Not Fixed**

**Bug:** Endpoint is empty/not implemented or undocumented.

**Current routes:**
```php
Route::post('coupons/verify', [CouponController::class, 'verify']);
Route::post('coupons/add-to-cart', [CouponController::class, 'addCouponToCart']);
```

The `/api/v1/cart/coupon` endpoint mentioned in the bug report doesn't exist in the routes. The actual endpoints are `POST /coupons/verify` and `POST /coupons/add-to-cart`.

**Plan to fix:** Verify the intended endpoint path and either:
1. Update the route to match the documentation, OR
2. Update the documentation to match the actual route

---

## 18. Coupons — POST add coupon (400 Generic Error)

**Status:** ⚠️ **Partially Fixed**

**Bug:** Returns raw error key `"CHAWKBAZAR_ERROR.COULD_NOT_CREATE_THE_RESOURCE"`.

**Fix applied:** `PromotionRepository.php` (same pattern) — error is now logged.

**Plan to fix:** Same as #16 — verify translation keys exist in lang files.

Additionally, the `CouponRequest` validation should be checked:
```php
'name.*' => ['required_with:name', UniqueTranslationRule::for('coupons', 'name')],
```
Note `UniqueTranslationRule` is used but the syntax may be missing the column name parameter. Verify it works correctly.

---

## 19. FAQs — GET FAQs (bad relationship)

**Status:** ✅ **Fixed**

**Bug:** `Call to undefined relationship [shop] on model [Faqs]` — the controller uses `->with('shop')` but the model had no `shop()` relationship.

**Fix applied:** `packages/marvel/src/Database/Models/Faqs.php:32-40`
```php
// Was commented out, now active:
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function shop(): BelongsTo
{
    return $this->belongsTo(Shop::class);
}
```

Also added `'shop_id'` and `'user_id'` to `$fillable`.

---

## 20. Sliders — POST create slider (500 Generic Error)

**Status:** ✅ **Fixed**

**Bug:** `uploadSingleImage($request, 'image', ...)` passed the wrong parameter name `'image'` instead of `'image_desktop'`/`'image_mobile'`, so `$request->hasFile('image')` always returned false.

**Fix applied:** `packages/marvel/src/Database/Repositories/SliderRepository.php:37,42`
```php
// Before:
$this->uploadSingleImage($request, 'image', $slider, 'slider-image-desktop', 'sliders');
$this->uploadSingleImage($request, 'image', $slider, 'slider-image-mobile', 'sliders');

// After:
$this->uploadSingleImage($request, 'image_desktop', $slider, 'slider-image-desktop', 'sliders');
$this->uploadSingleImage($request, 'image_mobile', $slider, 'slider-image-mobile', 'sliders');
```

---

## 21. Sliders — POST reorder slider (404 Route Not Found)

**Status:** ✅ **Already Working**

**Bug claim:** `POST /api/v1/slider/reorder` returns 404.

**Current route (line 455):**
```php
Route::post('sliders/reorder', [SliderController::class, 'reorder']);
```

**Analysis:** The route exists at `/api/v1/sliders/reorder` (plural `sliders`). The bug report uses `/api/v1/slider/reorder` (singular `slider`). This is a client-side typo — using the wrong URL.

**No code change needed.** The route is correctly registered.

---

## 22. Countries — POST create country (422 validation keys)

**Status:** ✅ **Fixed**

**Bug:** Validation required BOTH `name` (flat) AND `name.en`/`name.ar` — duplicate requirement that caused confusion.

**Fix applied:** `packages/marvel/src/Http/Requests/CountryStoreRequest.php`
```php
// Before:
'name'    => ['required', 'array'],
'name.en' => ['required', 'string', ...],
'name.ar' => ['required', 'string', ...],

// After:
'name.en' => ['required', 'string', ...],
'name.ar' => ['required', 'string', ...],
```

Removed the redundant `'name' => ['required', 'array']` rule. Only `name.en` and `name.ar` are validated now.

Also fixed `CountryUpdateRequest.php` — removed `'name' => ['sometimes', 'array']`.

---

## 23. Shipping Prices — GET shipping prices (404 Route Not Found)

**Status:** ✅ **Fixed**

**Bug:** `GET /api/v1/shipping-prices` and `GET /api/v1/shipping-prices/{id}` returned 404 — route not registered.

**Fix applied:** `packages/marvel/src/Rest/Routes.php:655`
```php
Route::apiResource('shipping-prices', ShippingPriceController::class);
```

Added as a full resource route inside the super_admin middleware group, supporting all CRUD operations.

---

## Summary Table

| # | Endpoint | Bug | Status | Priority for Remaining |
|---|----------|-----|--------|----------------------|
| 1 | POST `/contacts/{id}/replay` | ModelNotFoundException | ✅ Fixed | — |
| 2 | POST `/admin-users` | Duplicate email SQL leak | ❌ Not Fixed | **P0** |
| 3 | PUT `/roles/{id}` | Generic 500 on not-found | ⚠️ Partial | P2 |
| 4 | DELETE `/roles/{id}` | Generic 500 on not-found | ⚠️ Partial | P2 |
| 5 | GET `/permissions` | Label not localized | ❌ Not Fixed | P2 |
| 6 | POST `/users/{id}/remove-role` | Generic 500 | ❌ Not Fixed | **P1** |
| 7 | DELETE `/categories/{id}` | FK constraint SQL leak | ✅ Fixed | — |
| 8 | POST `/products` | "Old validation keys" | ✅ Already working | — |
| 9 | PUT `/products/{id}` | Null pointer on `$product->id` | ✅ Fixed | — |
| 10 | PUT `/products/{id}` | Status/discount_status validation | ❌ Not Fixed | P2 |
| 11 | GET `/products` | Undocumented query params | ❌ Not Fixed | P3 |
| 12 | GET `/reviews` | 405 Method Not Allowed | ❌ Not Fixed | **P1** |
| 13 | POST `/flash-sales` | "Old validation keys" | ✅ Already working | — |
| 14 | PUT `/flash-sales/{id}` | `$code` must be int (Exception) | ❌ Not Fixed | **P0** |
| 15 | POST `/promotions` | "Old validation keys" | ✅ Already working | — |
| 16 | PUT `/promotions/{id}` | Raw error key returned | ⚠️ Partial | P2 |
| 17 | GET `/cart/coupon` | Empty/undocumented endpoint | ❌ Not Fixed | P3 |
| 18 | POST `/coupons` | Raw error key returned | ⚠️ Partial | P2 |
| 19 | GET `/faqs` | Missing shop relationship | ✅ Fixed | — |
| 20 | POST `/sliders` | Wrong param name in upload | ✅ Fixed | — |
| 21 | POST `/sliders/reorder` | 404 (client typo `slider` vs `sliders`) | ✅ Already working | — |
| 22 | POST `/countries` | Duplicate name validation rule | ✅ Fixed | — |
| 23 | GET `/shipping-prices` | Route not registered | ✅ Fixed | — |

## Remaining Work — Priority Order

### P0 — Must Fix
- **#2** AdminCreateUserRequest — add `unique:users,email` to email validation
- **#14** FlashSaleController/Repository — find and fix `Exception` string code parameter

### P1 — Should Fix
- **#6** RoleAndPermissionController — add ModelNotFoundException catch + logging to `removeRoleFromUser()`
- **#12** Routes.php — uncomment or add `reviews.index` and `reviews.show` routes

### P2 — Nice to Fix
- **#3/#4** RoleAndPermissionController — add QueryException catch for role delete with FK
- **#5** PermissionResource — fix label localization fallback
- **#10** ProductUpdateRequest — verify status allowed values match ProductStatus enum
- **#16/#18** Verify lang file entries for error keys

### P3 — Documentation
- **#11** Add ApiDogs/Swagger annotations for product query params
- **#17** Document or implement cart/coupon endpoint
