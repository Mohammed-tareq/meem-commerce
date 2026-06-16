# Authentication & User Management API Documentation

## Overview

This API provides complete user authentication and management for the ChawkBazar e-commerce platform. It handles user registration, login (email/phone/social), password management, OTP verification, contact form submissions, and user administration.

**Base URL:** `/api/v1`

**Authentication:** Laravel Sanctum Token (Bearer token)

**Content-Type:** `application/json`

---

## Rate Limiting

| Limiter | Rate Limit | Scoped To | Endpoints | Purpose |
|---------|-----------|-----------|-----------|---------|
| `api` | 60 req/min | User ID or IP | All endpoints | General DoS protection |
| `auth` | 10 req/min | IP | register, token, social-login-token | Brute force & credential stuffing prevention |
| `sensitive` | 5 req/min | IP | forget-password, verify-forget-password-token, reset-password, contact-us | Email bombing & account takeover prevention |
| `otp` | 3 req/min | IP | send-otp-code, otp-login | SMS/email cost protection |
| `orders` | 10 req/min | User ID or IP | order store | Order spam & inventory locking prevention |
| `content` | 5 req/min | User ID or IP | reviews, questions, feedbacks, messages | Review bombing & spam prevention |
| `refunds` | 5 req/min | User ID or IP | refund store | Refund fraud prevention |
| `uploads` | 10 req/min | User ID or IP | attachments, imports | Storage abuse prevention |

---

## Business Logic Rules

### 1. Only Active Users Can Authenticate
- **Rule:** `is_active` must be `true` for login
- **Why:** Banned/deactivated users must not access the system
- **Input:** email/phone + password credentials
- **Output:** `INVALID_CREDENTIALS` if user is inactive or not found

### 2. Registration Requires Policy Acceptance
- **Rule:** `policy` field must be `1` or `true`
- **Why:** Legal/compliance requirement
- **Input:** `policy: 1` or `policy: true`
- **Failure:** Validation error with status 422

### 3. Password Reset Token Expires in 5 Minutes
- **Rule:** Token valid only for 5 minutes from creation
- **Why:** Security best practice to limit attack window
- **Input:** email for forgot-password request
- **Output:** password_resets record created with 5-min expiry

### 4. Social Login Auto-Creates Accounts
- **Rule:** If no user exists with the social provider's email, a new user is created
- **Why:** Seamless onboarding
- **Input:** provider + access_token
- **Output:** User created if new; provider record created/updated

### 5. Contact Form Rate-Limited
- **Rule:** Maximum 5 contact submissions per minute per IP
- **Why:** Prevent spam and email bombing
- **Input:** email, subject, message
- **Output:** Contact record created or 429 rate limit response

---

## Request Parameters & Validation

### POST /api/v1/register

| Parameter | Type | Required | Rules |
|-----------|------|----------|-------|
| `first_name` | string | Yes | min:2, max:50 |
| `last_name` | string | Yes | min:2, max:50 |
| `email` | string | Yes | email, rfc,dns, unique:users |
| `phone_number` | string | Yes | min:10, max:20, unique:users |
| `password` | string | Yes | min:8, max:50, confirmed |
| `password_confirmation` | string | Yes | min:8, max:50 |
| `policy` | boolean/integer | Yes | in:1,true |
| `avatar` | file | No | image (not validated in request but handled in controller) |

### POST /api/v1/token

| Parameter | Type | Required | Rules |
|-----------|------|----------|-------|
| `email` | string | If phone absent | email |
| `phone_number` | string | If email absent | min:8, max:15 |
| `password` | string | Yes | min:6 |

### POST /api/v1/contact-us

| Parameter | Type | Required | Rules |
|-----------|------|----------|-------|
| `email` | string | Yes | email, max:255 |
| `subject` | string | Yes | max:255 |
| `message` | string | Yes | min:3, max:5000 |

---

## Execution Flow Diagrams

### Registration Flow
```
Client -> POST /api/v1/register
  -> throttle:auth (10/min/IP)
  -> UserCreateRequest (validation)
  -> DB::beginTransaction()
  -> UserRepository::create()
  -> (optional) Upload avatar via Spatie Media
  -> DB::commit()
  -> sendOneTimePassword() via Spatie OTP
  <- 200: {status, message, success, data: {otp_status: bool}}
  OR
  <- 201: {status, message, success, data: {requires_resend, otp_status: false}}
  OR (on exception)
  <- 500: {status, message, success}
```

### Login Flow
```
Client -> POST /api/v1/token
  -> throttle:auth (10/min/IP)
  -> UserAuthEmailAndPasswordRequest (validation)
  -> User::where(email or phone_number)->where(is_active, true)->first()
  -> Hash::check(password, user->password)
  -> createToken('auth_token')->plainTextToken
  <- 200: {status, message, success, data: {token, permissions, email_verified, role}}
  OR
  <- 404: {status: 404, message: INVALID_CREDENTIALS, success: false}
```

### Password Reset Flow
```
Client -> POST /api/v1/forget-password {email}
  -> throttle:sensitive (5/min/IP)
  -> Find user by email
  -> Create/update password_resets record (token, expires_at)
  -> Send email with token via ForgetPassword mailable
  <- 200: {status, message}

Client -> POST /api/v1/verify-forget-password-token {email, otp}
  -> throttle:sensitive (5/min/IP)
  -> Hash::check(otp, stored_token)
  -> Check created_at + 5 min not expired
  <- true OR false (raw boolean - NOT wrapped in JSON)

Client -> POST /api/v1/reset-password {email, otp, password, password_confirmation}
  -> throttle:sensitive (5/min/IP)
  -> Validate input
  -> verifyForgetPasswordToken (boolean check)
  -> Update user password
  -> Delete password_resets record
  <- 200: {status, message, success}
```

---

## Response Structure

### Success Response Format
```json
{
  "status": 200,
  "message": "Human readable message",
  "success": true,
  "data": {
    "token": "1|abc123def456..."
  }
}
```

| Field | Type | Nullable | Source | Description |
|-------|------|----------|--------|-------------|
| `status` | integer | No | Controller | HTTP status code |
| `message` | string | No | ApiResponse trait | i18n-translated message (key from constants.php, resolved via message.php lang files) |
| `success` | boolean | No | Controller | Operation success indicator |
| `data` | mixed | Yes (omitted if empty) | Controller | Response payload (varies by endpoint) |

### Error Response Format
```json
{
  "status": 422,
  "message": "Human readable error",
  "success": false
}
```

### Validation Error Format
```json
{
  "field_name": ["The field_name field is required."]
}
```

---

## Full Request/Response Examples

### Register a New User
**Request:**
```http
POST /api/v1/register
Content-Type: application/json

{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@example.com",
  "phone_number": "+12345678901",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!",
  "policy": 1
}
```

**Response (200 - OTP sent):**
```json
{
  "status": 200,
  "message": "Account created successfully! Please verify your email with the OTP sent.",
  "success": true,
  "data": {
    "otp_status": true
  }
}
```

**Response (201 - OTP failed but account created):**
```json
{
  "status": 201,
  "message": "Account created but we couldn't send OTP email.",
  "success": true,
  "data": {
    "requires_resend": true,
    "email": "john.doe@example.com",
    "phone_number": "+12345678901",
    "otp_status": false
  }
}
```

### Login with Email
**Request:**
```http
POST /api/v1/token
Content-Type: application/json

{
  "email": "john.doe@example.com",
  "password": "SecurePass123!"
}
```

**Response:**
```json
{
  "status": 200,
  "message": "Logged in successfully!",
  "success": true,
  "data": {
    "token": "2|sanctum_token_string_here",
    "permissions": ["customer"],
    "email_verified": true,
    "role": ["customer"]
  }
}
```

### Login with Phone Number
**Request:**
```http
POST /api/v1/token
Content-Type: application/json

{
  "phone_number": "+12345678901",
  "password": "SecurePass123!"
}
```

**Response:** Same as email login.

### Get Current User
**Request:**
```http
GET /api/v1/me
Authorization: Bearer 2|sanctum_token_string_here
```

**Response:**
```json
{
  "status": 200,
  "message": "User profile retrieved successfully!",
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "email_verified_at": "2026-06-15T10:30:00Z",
    "is_active": true,
    "shop_id": null,
    "image": "https://storage.example.com/users/avatar.jpg",
    "roles": [
      {
        "id": 1,
        "name": "customer",
        "guard_name": "api"
      }
    ],
    "permissions": [
      {
        "id": 1,
        "name": "customer",
        "guard_name": "api"
      }
    ]
  }
}
```

### Logout
**Request:**
```http
POST /api/v1/logout
Authorization: Bearer 2|sanctum_token_string_here
```

**Response:**
```json
{
  "status": 200,
  "message": "Logged out successfully!",
  "success": true
}
```

### Submit Contact Form
**Request:**
```http
POST /api/v1/contact-us
Content-Type: application/json

{
  "email": "buyer@example.com",
  "subject": "Order Inquiry",
  "message": "I have a question about my recent order #12345."
}
```

**Response:**
```json
{
  "status": 201,
  "message": "Contact created successfully",
  "success": true,
  "data": {
    "id": 15,
    "email": "buyer@example.com",
    "subject": "Order Inquiry",
    "message": "I have a question about my recent order #12345.",
    "is_read": false,
    "is_replay": false,
    "created_at": "2026-06-15T11:00:00Z"
  }
}
```

### Social Login
**Request:**
```http
POST /api/v1/social-login-token
Content-Type: application/json

{
  "provider": "google",
  "access_token": "ya29.a0AfH6SMC..."
}
```

**Response:**
```json
{
  "status": 200,
  "message": "Logged in successfully!",
  "success": true,
  "data": {
    "token": "3|sanctum_token_string_here"
  }
}
```

### Request Password Reset
**Request:**
```http
POST /api/v1/forget-password
Content-Type: application/json

{
  "email": "john.doe@example.com"
}
```

**Response:**
```json
{
  "status": 200,
  "message": "Check your inbox for password reset email!",
  "success": true
}
```

### Reset Password
**Request:**
```http
POST /api/v1/reset-password
Content-Type: application/json

{
  "email": "john.doe@example.com",
  "otp": "123456",
  "password": "NewSecurePass456!",
  "password_confirmation": "NewSecurePass456!"
}
```

**Response:**
```json
{
  "status": 200,
  "message": "Password reset successful!",
  "success": true
}
```

---

## Error Responses

| HTTP Status | Meaning | Example Scenarios |
|-------------|---------|-------------------|
| 200 | Success | Operation completed successfully |
| 201 | Created | Resource created (contact, partial registration) |
| 400 | Bad Request | Invalid token, wrong old password, business logic error |
| 401 | Unauthorized | Missing/invalid token (should be used for invalid credentials but currently returns 404) |
| 404 | Not Found | User not found, invalid credentials (currently), resource missing |
| 409 | Conflict | Email not verified (middleware disabled) |
| 417 | Expectation Failed | Invalid license key (middleware disabled) |
| 422 | Validation Error | Missing/invalid fields |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Server Error | SMTP failure, unexpected exceptions |

---

## Known Limitations

1. **Email verification is disabled.** The `token()` method has email verification check commented out. The `EnsureEmailIsVerified` middleware does nothing (all logic commented out).
2. **OTP verification route is disabled.** The route for `/verify-otp-code` is commented out in Routes.php.
3. **Password reset token is hardcoded to `123456`.** This is insecure for production and the `Hash::check` verification is broken since the stored token is not hashed.
4. **Registration does not assign permissions.** Users registered via `/register` have no roles/permissions because the method bypasses `storeUser()`.
5. **Two User models exist.** `App\Models\User` is used as the auth provider but `Marvel\Database\Models\User` is used for all business logic. This can cause authentication resolution issues.
6. **Global scope on User model.** Forces `ORDER BY updated_at DESC` on every query, adding unnecessary overhead.
7. **`verifyForgetPasswordToken` returns raw boolean.** Not wrapped in JSON response — breaks API contract when called directly.
8. **`otpLogin` returns inconsistent response.** Returns bare token string instead of wrapped `{"token": "..."}` object.

---

## Future Improvements

1. Implement proper email verification flow (re-enable middleware and checks)
2. Generate cryptographically secure random password reset tokens
3. Move hardcoded test OTP to environment-based configuration
4. Unify the two User models into one
5. Add proper password reset throttle (per email, not just per IP)
6. Implement refresh tokens for longer sessions
7. Add 2FA support
8. Add login history/audit logging
9. Implement account lockout after N failed attempts (beyond rate limiting)
10. Add webhook notifications for user registration

---

## Developer Notes

### Adding a New Auth Endpoint
1. Add validation rules in a new Form Request class in `packages/marvel/src/Http/Requests/`
2. Add the controller method in `UserController`
3. Register the route in `packages/marvel/src/Rest/Routes.php`
4. Apply appropriate rate limiter middleware
5. Use `$this->apiResponse()` for consistent response format
6. Add message keys in `packages/marvel/config/constants.php`
7. Add translations in `resources/lang/{lang}/message.php`

### Testing
```bash
# Run API tests
php artisan test --filter=UserTest

# Test registration
curl -X POST http://localhost/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"first_name":"John","last_name":"Doe","email":"test@example.com","phone_number":"+1234567890","password":"password123","password_confirmation":"password123","policy":1}'

# Test login
curl -X POST http://localhost/api/v1/token \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# Test authenticated endpoint
curl http://localhost/api/v1/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```
