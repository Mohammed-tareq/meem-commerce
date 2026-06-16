# Production Edit Proposals — Authentication & User Management API

> **Status:** Awaiting Approval
> **Date:** 2026-06-15
> **Scope:** UserController, ContactController, Routes, User Model, API Responses

---

## Change 1: Fix Password Reset Token Storage & Verification

**Severity:** CRITICAL

**File:** `packages/marvel/src/Http/Controllers/UserController.php`
**Methods:** `forgetPassword` (line 732), `verifyForgetPasswordToken` (line 762)

**Problem:**
The `forgetPassword` method stores token `123456` as plaintext in the `password_resets` table:
```php
DB::table('password_resets')->insert([
    'token' => 123456,  // plaintext integer
    ...
]);
```

But `verifyForgetPasswordToken` uses `Hash::check()`:
```php
if (!Hash::check($request->otp, $tokenData->token)) {
    return false;
}
```

`Hash::check()` expects a bcrypt-hashed value as the second argument. Since the stored token is plaintext `123456` (not `Hash::make(123456)`), this comparison **always fails**. Password reset is effectively broken.

Additionally, the hardcoded token `123456` is a security concern for production.

**Solution:**
Two options:
- **Option A (Quick fix):** Use `Hash::make($token)` when storing and generate a random token instead of hardcoded value.
- **Option B (Proper):** Generate a random string token, hash it before storing, and send the original unhashed token via email.

**Affected Code:**
```php
// Current (broken):
'token' => 123456,

// Proposed:
'token' => Hash::make($plaintextToken = Str::random(60)),
```
And in the email, send `$plaintextToken` instead.

**Risk:** Low — this fixes broken functionality.

---

## Change 2: Assign CUSTOMER Permission on Registration

**Severity:** HIGH

**File:** `packages/marvel/src/Http/Controllers/UserController.php`
**Method:** `register` (line 609)

**Problem:**
The `register()` method creates users via:
```php
$this->repository->create([...]);
```

This bypasses `UserRepository::storeUser()` which assigns the `CUSTOMER` permission:
```php
$user->givePermissionTo(UserPermission::CUSTOMER);
```

Users registered through the API endpoint end up with **no permissions and no roles**, making them effectively unable to use the system.

**Solution:**
After creating the user, explicitly assign the CUSTOMER permission:
```php
$user->givePermissionTo(UserPermission::CUSTOMER);
```

**Affected Code Location:** After line 622 (after `DB::commit()`).

**Risk:** Low — aligns with existing behavior in `storeUser()`.

---

## Change 3: Fix Login HTTP Status Code

**Severity:** LOW

**File:** `packages/marvel/src/Http/Controllers/UserController.php`
**Methods:** `token` (line 474), `loginWithOutEmailVerification` (line 499)

**Problem:**
Invalid credentials return HTTP 404:
```php
return $this->apiResponse(INVALID_CREDENTIALS, 404, false);
```

HTTP 404 means "Not Found" which is semantically incorrect. Authentication failures should return **401 Unauthorized**.

**Solution:**
```php
return $this->apiResponse(INVALID_CREDENTIALS, 401, false);
```

**Risk:** Low — clients may need updates if they check for 404 specifically.

---

## Change 4: Fix `sendVerificationEmail` Method Signature

**Severity:** MEDIUM

**File:** `packages/marvel/src/Http/Controllers/UserController.php`
**Method:** `sendVerificationEmail` (line 109)

**Problem:**
```php
public function sendVerificationEmail(User $user): JsonResponse
```

The route (`/email/verification-notification`) does **not** include a user ID parameter:
```php
Route::post('/email/verification-notification', [UserController::class, 'sendVerificationEmail'])
    ->middleware(['auth:sanctum', 'throttle:6,1']);
```

Laravel's route model binding cannot resolve `User` from the request. This will fail with a ModelNotFoundException.

**Solution:**
```php
public function sendVerificationEmail(Request $request): JsonResponse
{
    $request->user()->sendEmailVerificationNotification();
    ...
}
```

**Risk:** Low — method was effectively unreachable before.

---

## Change 5: Remove Duplicate Code in `verifyLoginOtp`

**Severity:** MEDIUM

**File:** `packages/marvel/src/Http/Controllers/UserController.php`
**Method:** `verifyLoginOtp` (line 535)

**Problem:**
The user fetch and OTP verification logic is duplicated. Lines 541-549 and 548-561 contain overlapping code:
```php
// Lines 541-549 (first block)
$user = User::where('email', $request->email)->where('is_active', true)->first();
if ($request->code === '123456' || $user->verifyOneTimePassword($request->code)) {
    $data = ["token" => $user->createToken('auth_token')->plainTextToken];
    ...
}

// Lines 548-561 (second block — overlaps)
if (!$user) { ... }
if ($request->code === '123456' || $user->verifyOneTimePassword($request->code)) {
    $data = ["token" => $user->createToken('auth_token')->plainTextToken];
    ...
}
```

The second block will never execute correctly because the first block already returned.

**Solution:**
Remove the duplicate block entirely, keeping only the corrected first block.

**Risk:** Low — dead code removal.

---

## Change 6: Add Token Existence Check in Logout

**Severity:** MEDIUM

**File:** `packages/marvel/src/Http/Controllers/UserController.php`
**Method:** `logout` (line 565)

**Problem:**
```php
$user->currentAccessToken()->delete();
```

If `currentAccessToken()` returns `null` (e.g., token already expired, or user accessed via cookie session), this throws a `Call to member function delete() on null` error.

**Solution:**
```php
$token = $user->currentAccessToken();
if ($token) {
    $token->delete();
}
```

**Risk:** Low — prevents 500 errors during logout.

---

## Change 7: Refactor `resetPassword` Validation Logic

**Severity:** MEDIUM

**File:** `packages/marvel/src/Http/Controllers/UserController.php`
**Method:** `resetPassword` (line 788)

**Problem:**
```php
$request->validate([...]);  // Line 791 — already validates
...
if (!$this->verifyForgetPasswordToken($request) || !$request->validate()) {  // Line 797 — duplicate validate call
```

The `$request->validate()` call on line 791 runs first. Then the `if` condition on line 797 calls `$request->validate()` again. Due to short-circuit evaluation:
- If `verifyForgetPasswordToken` returns false: validation is skipped (OK)
- If `verifyForgetPasswordToken` returns true: validation runs twice (unnecessary)

Additionally, the `||` logic is wrong — if `verifyForgetPasswordToken` returns `false`, we should NOT proceed, but the current code has the condition inverted for a failure path.

**Solution:**
Restructure to:
```php
$request->validate([...]);

if (!$this->verifyForgetPasswordToken($request)) {
    return $this->apiResponse(INVALID_TOKEN, 400, false);
}

// Proceed with password update
```

**Risk:** Low — fixes incorrect logic.

---

## Change 8: Standardize OTP Login Response

**Severity:** LOW

**File:** `packages/marvel/src/Http/Controllers/UserController.php`
**Method:** `otpLogin` (line 1073)

**Problem:**
OTP login returns the bare token string instead of a wrapped object:
```php
return $this->apiResponse(USER_LOGGED_IN_SUCCESSFULLY, 200, true, $token);
// $token is a string — other endpoints return ['token' => $token]
```

All other auth endpoints wrap the token: `"data": {"token": "..."}`. This inconsistency breaks client expectations.

**Solution:**
```php
return $this->apiResponse(USER_LOGGED_IN_SUCCESSFULLY, 200, true, ['token' => $token]);
```

**Risk:** Low — clients using this disabled route won't be affected until it's enabled.

---

## Change 9: Add JSON Response to `verifyForgetPasswordToken`

**Severity:** MEDIUM

**File:** `packages/marvel/src/Http/Controllers/UserController.php`
**Method:** `verifyForgetPasswordToken` (line 762)

**Problem:**
Returns raw `true` / `false`:
```php
return false;  // line 769, 773, 781
return true;   // line 784
```

A direct HTTP call to this endpoint receives `true`/`false` with no JSON structure, no status code, and potentially a wrong Content-Type header.

**Solution:**
```php
return response()->json(['status' => 200, 'success' => true, 'valid' => true]);
// or for false:
return response()->json(['status' => 400, 'success' => false, 'valid' => false], 400);
```

**Risk:** Medium — `resetPassword` calls this method and relies on boolean return. Both callers must be updated together.

---

## Change 10: Remove Global Scope on User Model

**Severity:** MEDIUM

**File:** `packages/marvel/src/Database/Models/User.php`
**Method:** `boot` (line 80-87)

**Problem:**
```php
protected static function boot()
{
    parent::boot();
    static::addGlobalScope('order', function (Builder $builder) {
        $builder->orderBy('updated_at', 'desc');
    });
}
```

This global scope adds `ORDER BY updated_at DESC` to **every** User query, including:
- Count queries (adds unnecessary sorting overhead)
- Subqueries and joins (can cause conflicts)
- Admin list pages (may not want this ordering)
- Auth lookups (wasteful)

**Solution:**
Remove the global scope. Apply ordering explicitly where needed (e.g., in the `index()` method of the controller).

**Risk:** Low — may change default list ordering in admin panels; update those queries to explicitly order.

---

## Change 11: Add Unique Constraint on password_resets.email

**Severity:** LOW

**File:** Related migration file

**Problem:**
The `password_resets` table likely lacks a compound unique constraint on `email`. Concurrent password reset requests can create multiple rows for the same email, causing ambiguity in `forgetPassword` (which only ever reads the first record).

**Solution:**
Add a migration to create a unique index on `email` in `password_resets` table, and use `updateOrInsert` instead of `insert`.

**Risk:** Low — improves data integrity.

---

## Change 12: Document Query Inefficiency in Contact Index

**Severity:** LOW

**File:** `packages/marvel/src/Http/Controllers/ContactController.php`
**Method:** `index` (line 29)

**Problem:**
No eager loading of relationships. While Contact has none currently, the pagination query lacks explicit column selection (`SELECT *`).

**Solution:**
Consider using `->paginate($limit, ['id', 'email', 'subject', 'message', 'is_read', 'is_replay', 'created_at'])` for performance.

**Risk:** Minimal — optimization only.

---

## Summary

| # | Change | Severity | Effort | Priority |
|---|--------|----------|--------|----------|
| 1 | Fix password reset token | CRITICAL | Small | P0 |
| 2 | Assign permission on register | HIGH | Small | P0 |
| 3 | Fix login HTTP status | LOW | Tiny | P2 |
| 4 | Fix sendVerificationEmail | MEDIUM | Tiny | P1 |
| 5 | Remove duplicate verifyLoginOtp | MEDIUM | Tiny | P1 |
| 6 | Add null check in logout | MEDIUM | Tiny | P1 |
| 7 | Fix resetPassword logic | MEDIUM | Small | P1 |
| 8 | Standardize OTP response | LOW | Tiny | P2 |
| 9 | Add JSON to verifyForgetPasswordToken | MEDIUM | Small | P1 |
| 10 | Remove global scope | MEDIUM | Small | P2 |
| 11 | Add unique constraint | LOW | Tiny | P2 |
| 12 | Optimize contact query | LOW | Tiny | P3 |
