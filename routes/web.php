<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/readme', function () {
    $html = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation — ChawkBazar</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f7fa; color: #1a1a2e; line-height: 1.7; }
        .container { max-width: 1000px; margin: 0 auto; padding: 40px 20px; }
        h1 { font-size: 2.2em; margin-bottom: 8px; color: #0f3460; }
        h2 { font-size: 1.5em; margin: 40px 0 16px; padding-bottom: 8px; border-bottom: 3px solid #e94560; color: #0f3460; }
        h3 { font-size: 1.15em; margin: 28px 0 10px; color: #16213e; }
        p, li { color: #333; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.78em; font-weight: 600; margin-right: 6px; }
        .badge-get { background: #61affe; color: #fff; }
        .badge-post { background: #49cc90; color: #fff; }
        .badge-put { background: #fca130; color: #fff; }
        .badge-delete { background: #f93e3e; color: #fff; }
        .endpoint { background: #fff; border-radius: 8px; padding: 16px 20px; margin: 12px 0; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .endpoint .method { font-weight: 700; font-size: 0.9em; }
        .endpoint .path { font-family: 'SF Mono', 'Fira Code', monospace; color: #555; margin-left: 10px; }
        .endpoint .desc { margin-top: 6px; color: #666; font-size: 0.92em; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #eef1f5; font-size: 0.92em; }
        th { background: #0f3460; color: #fff; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        code { background: #eef1f5; padding: 2px 7px; border-radius: 4px; font-family: 'SF Mono', 'Fira Code', monospace; font-size: 0.88em; color: #e94560; }
        pre { background: #1a1a2e; color: #e4e4e4; padding: 16px 20px; border-radius: 8px; overflow-x: auto; font-size: 0.85em; line-height: 1.5; margin: 12px 0; }
        pre .string { color: #a5d6ff; }
        pre .key { color: #c5e1a5; }
        pre .bool { color: #ffcc80; }
        pre .num { color: #ffab91; }
        .note { background: #fff8e1; border-left: 4px solid #ffa000; padding: 12px 16px; border-radius: 4px; margin: 12px 0; font-size: 0.9em; }
        .warning { background: #ffebee; border-left: 4px solid #e53935; padding: 12px 16px; border-radius: 4px; margin: 12px 0; font-size: 0.9em; }
        ul { padding-left: 24px; margin: 8px 0; }
        .meta { color: #888; font-size: 0.88em; margin-bottom: 24px; }
        hr { border: none; border-top: 1px solid #e0e0e0; margin: 32px 0; }
    </style>
</head>
<body>
<div class="container">

<h1>ChawkBazar API Documentation</h1>
<p class="meta">Base URL: <code>/api/v1</code> &middot; Authentication: <code>Bearer Token (Sanctum)</code> &middot; Content-Type: <code>application/json</code></p>

<h2>Rate Limiting</h2>
<table>
    <tr><th>Limiter</th><th>Limit</th><th>Scoped To</th><th>Endpoints</th></tr>
    <tr><td><code>api</code></td><td>60/min</td><td>User ID or IP</td><td>All endpoints</td></tr>
    <tr><td><code>auth</code></td><td>10/min</td><td>IP</td><td>register, token, social-login-token</td></tr>
    <tr><td><code>sensitive</code></td><td>5/min</td><td>IP</td><td>forget-password, verify-token, reset-password, contact-us</td></tr>
    <tr><td><code>otp</code></td><td>3/min</td><td>IP</td><td>send-otp-code, otp-login</td></tr>
    <tr><td><code>orders</code></td><td>10/min</td><td>User ID or IP</td><td>order store</td></tr>
    <tr><td><code>content</code></td><td>5/min</td><td>User ID or IP</td><td>reviews, questions, feedbacks, messages</td></tr>
    <tr><td><code>refunds</code></td><td>5/min</td><td>User ID or IP</td><td>refund store</td></tr>
    <tr><td><code>uploads</code></td><td>10/min</td><td>User ID or IP</td><td>attachments, imports</td></tr>
</table>

<h2>Authentication Endpoints</h2>

<h3>POST /api/v1/token — Login</h3>
<table>
    <tr><th colspan="4">Request Body</th></tr>
    <tr><th>Field</th><th>Type</th><th>Rules</th><th>Description</th></tr>
    <tr><td><code>email</code></td><td>string</td><td>required_without:phone_number, email</td><td>User email address</td></tr>
    <tr><td><code>phone_number</code></td><td>string</td><td>required_without:email, min:8, max:15</td><td>User phone number</td></tr>
    <tr><td><code>password</code></td><td>string</td><td>required, min:6</td><td>User password</td></tr>
</table>

<h4>Success Response (200)</h4>
<pre>{
    <span class="key">"status"</span>: <span class="num">200</span>,
    <span class="key">"message"</span>: <span class="string">"Logged in successfully!"</span>,
    <span class="key">"success"</span>: <span class="bool">true</span>,
    <span class="key">"data"</span>: {
        <span class="key">"token"</span>: <span class="string">"1|abc123def456..."</span>,
        <span class="key">"permissions"</span>: [<span class="string">"customer"</span>],
        <span class="key">"email_verified"</span>: <span class="bool">true</span>,
        <span class="key">"role"</span>: [<span class="string">"customer"</span>]
    }
}</pre>

<table>
    <tr><th colspan="4">Response Fields</th></tr>
    <tr><th>Field</th><th>Type</th><th>Nullable</th><th>Description</th></tr>
    <tr><td><code>token</code></td><td>string</td><td>No</td><td>Sanctum Bearer token for authenticated requests</td></tr>
    <tr><td><code>permissions</code></td><td>array</td><td>No</td><td>List of permission names assigned to the user</td></tr>
    <tr><td><code>email_verified</code></td><td>boolean</td><td>No</td><td>Whether the user has verified their email</td></tr>
    <tr><td><code>role</code></td><td>array</td><td>No</td><td>List of role names assigned to the user</td></tr>
</table>

<h4>Error Response (401)</h4>
<pre>{
    <span class="key">"status"</span>: <span class="num">401</span>,
    <span class="key">"message"</span>: <span class="string">"Invalid credentials!"</span>,
    <span class="key">"success"</span>: <span class="bool">false</span>
}</pre>

<h4>Error Response (422 — Validation)</h4>
<pre>{
    <span class="key">"email"</span>: [<span class="string">"The email field is required when phone number is not present."</span>],
    <span class="key">"password"</span>: [<span class="string">"The password field is required."</span>]
}</pre>

<h4>cURL Example</h4>
<pre>curl -X POST https://api.example.com/api/v1/token \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePass123!"
}'</pre>

<hr>

<h3>POST /api/v1/register</h3>
<table>
    <tr><th colspan="4">Request Body</th></tr>
    <tr><th>Field</th><th>Type</th><th>Rules</th><th>Description</th></tr>
    <tr><td><code>first_name</code></td><td>string</td><td>required, min:2, max:50</td><td>User's first name</td></tr>
    <tr><td><code>last_name</code></td><td>string</td><td>required, min:2, max:50</td><td>User's last name</td></tr>
    <tr><td><code>email</code></td><td>string</td><td>required, email:rfc,dns, unique:users</td><td>User email address</td></tr>
    <tr><td><code>phone_number</code></td><td>string</td><td>required, min:10, max:20, unique:users</td><td>User phone number</td></tr>
    <tr><td><code>password</code></td><td>string</td><td>required, min:8, max:50, confirmed</td><td>User password</td></tr>
    <tr><td><code>password_confirmation</code></td><td>string</td><td>required, min:8, max:50</td><td>Password confirmation</td></tr>
    <tr><td><code>policy</code></td><td>boolean/integer</td><td>required, in:1,true</td><td>Accept terms &amp; conditions</td></tr>
</table>

<div class="endpoint">
    <span class="badge badge-post">POST</span>
    <span class="path">/api/v1/social-login-token</span>
    <div class="desc">Authenticate via Google or Facebook OAuth token. Auto-creates account if new.</div>
</div>

<div class="endpoint">
    <span class="badge badge-get">GET</span>
    <span class="path">/api/v1/me</span>
    <div class="desc">Get the currently authenticated user's profile (auth required).</div>
</div>

<div class="endpoint">
    <span class="badge badge-post">POST</span>
    <span class="path">/api/v1/logout</span>
    <div class="desc">Invalidate current Sanctum token (auth required).</div>
</div>

<h2>Password Management</h2>

<div class="endpoint">
    <span class="badge badge-post">POST</span>
    <span class="path">/api/v1/forget-password</span>
    <div class="desc">Request password reset. Sends OTP to email (5-min expiry).</div>
</div>

<div class="endpoint">
    <span class="badge badge-post">POST</span>
    <span class="path">/api/v1/verify-forget-password-token</span>
    <div class="desc">Verify password reset OTP validity.</div>
</div>

<div class="endpoint">
    <span class="badge badge-post">POST</span>
    <span class="path">/api/v1/reset-password</span>
    <div class="desc">Reset password using verified OTP.</div>
</div>

<div class="endpoint">
    <span class="badge badge-post">POST</span>
    <span class="path">/api/v1/change-password</span>
    <div class="desc">Change password for authenticated user (requires old password).</div>
</div>

<h2>Contact</h2>

<div class="endpoint">
    <span class="badge badge-post">POST</span>
    <span class="path">/api/v1/contact-us</span>
    <div class="desc">Submit a contact form message (rate-limited).</div>
</div>

<h3>User Resource Response — GET /api/v1/me</h3>
<pre>{
    <span class="key">"status"</span>: <span class="num">200</span>,
    <span class="key">"message"</span>: <span class="string">"User profile retrieved successfully!"</span>,
    <span class="key">"success"</span>: <span class="bool">true</span>,
    <span class="key">"data"</span>: {
        <span class="key">"id"</span>: <span class="num">1</span>,
        <span class="key">"name"</span>: <span class="string">"John Doe"</span>,
        <span class="key">"email"</span>: <span class="string">"john@example.com"</span>,
        <span class="key">"email_verified_at"</span>: <span class="string">"2026-06-15T10:00:00Z"</span>,
        <span class="key">"is_active"</span>: <span class="bool">true</span>,
        <span class="key">"shop_id"</span>: <span class="bool">null</span>,
        <span class="key">"image"</span>: <span class="string">"https://..."</span>,
        <span class="key">"roles"</span>: [...],
        <span class="key">"permissions"</span>: [...]
    }
}</pre>

<h3>Login Response Example</h3>
<pre>{
    <span class="key">"status"</span>: <span class="num">200</span>,
    <span class="key">"message"</span>: <span class="string">"Logged in successfully!"</span>,
    <span class="key">"success"</span>: <span class="bool">true</span>,
    <span class="key">"data"</span>: {
        <span class="key">"token"</span>: <span class="string">"1|abc123def456..."</span>,
        <span class="key">"permissions"</span>: [<span class="string">"customer"</span>],
        <span class="key">"email_verified"</span>: <span class="bool">true</span>,
        <span class="key">"role"</span>: [<span class="string">"customer"</span>]
    }
}</pre>

<h3>User Resource Response</h3>
<pre>{
    <span class="key">"status"</span>: <span class="num">200</span>,
    <span class="key">"message"</span>: <span class="string">"User profile retrieved successfully!"</span>,
    <span class="key">"success"</span>: <span class="bool">true</span>,
    <span class="key">"data"</span>: {
        <span class="key">"id"</span>: <span class="num">1</span>,
        <span class="key">"name"</span>: <span class="string">"John Doe"</span>,
        <span class="key">"email"</span>: <span class="string">"john@example.com"</span>,
        <span class="key">"email_verified_at"</span>: <span class="string">"2026-06-15T10:00:00Z"</span>,
        <span class="key">"is_active"</span>: <span class="bool">true</span>,
        <span class="key">"shop_id"</span>: <span class="kw">null</span>,
        <span class="key">"image"</span>: <span class="string">"https://..."</span>,
        <span class="key">"roles"</span>: [...],
        <span class="key">"permissions"</span>: [...]
    }
}</pre>

<h2>HTTP Status Codes</h2>
<table>
    <tr><th>Code</th><th>Meaning</th><th>Scenarios</th></tr>
    <tr><td>200</td><td>Success</td><td>Operation completed</td></tr>
    <tr><td>201</td><td>Created</td><td>Resource created (contact, partial registration)</td></tr>
    <tr><td>400</td><td>Bad Request</td><td>Invalid token, wrong password, business logic error</td></tr>
    <tr><td>401</td><td>Unauthorized</td><td>Missing or invalid token</td></tr>
    <tr><td>404</td><td>Not Found</td><td>User or resource not found</td></tr>
    <tr><td>422</td><td>Unprocessable Entity</td><td>Validation failure</td></tr>
    <tr><td>429</td><td>Too Many Requests</td><td>Rate limit exceeded</td></tr>
    <tr><td>500</td><td>Internal Server Error</td><td>Unexpected server error</td></tr>
</table>

<div class="warning">
    <strong>Known Issue:</strong> Invalid credentials currently return HTTP 404 instead of 401.
</div>

<h2>Known Limitations</h2>
<ul>
    <li>Email verification checks are disabled (commented out in controller and middleware)</li>
    <li>OTP verification route is disabled</li>
    <li>Password reset token is hardcoded to <code>123456</code>; <code>Hash::check</code> verification is broken</li>
    <li>Registration does not assign roles/permissions to new users</li>
    <li>Two User models exist causing potential auth resolution issues</li>
    <li>Global scope on User model adds unnecessary <code>ORDER BY</code> to all queries</li>
    <li><code>verifyForgetPasswordToken</code> returns raw boolean instead of JSON</li>
</ul>



<hr>
<p style="text-align:center;color:#888;font-size:0.85em;">
    ChawkBazar API &middot; Generated from codebase analysis &middot; 2026-06-15
</p>

</div>
</body>
</html>
HTML;
    return response($html)->header('Content-Type', 'text/html');
});
