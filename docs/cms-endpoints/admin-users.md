# Admin Users API

## Overview

Endpoints for managing admin users (SUPER_ADMIN role only). All endpoints require authentication via Sanctum and SUPER_ADMIN permission.

---

## 1. List Admin Users

**Endpoint**

`GET /admin/list`

**Purpose**

Retrieve a paginated list of all admin-type users.

**Authentication**

| Field | Value |
|---|---|
| Required | Yes |
| Guard | Sanctum |
| Permission | `view-users` |

**Query Parameters**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `limit` | integer | No | Items per page (default: 15) |

**Success Response (200)**

```json
{
    "data": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "type": "admin",
            "is_active": true,
            "permissions": [...],
            "created_at": "2024-01-01T00:00:00Z",
            "updated_at": "2024-01-01T00:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 1
    }
}
```

**Error Responses**

| Code | Description |
|---|---|
| 401 | Unauthenticated |
| 403 | Forbidden - requires `view-users` permission |
| 500 | Something went wrong |

---

## 2. Add Admin User

**Endpoint**

`POST /admin-users/add`

**Purpose**

Create a new user and assign roles.

**Authentication**

| Field | Value |
|---|---|
| Required | Yes |
| Guard | Sanctum |
| Permission | `create-user` |

**Request Body**

| Field | Type | Required | Description |
|---|---|---|---|
| `name` | string | Yes | User's full name |
| `email` | string | Yes | User's email address |
| `password` | string | Yes | User's password |
| `roles` | array | No | Array of role IDs to assign |

**Validation Rules**

- `name`: required, string
- `email`: required, email, unique:users
- `password`: required, string, min:8
- `roles`: array, each element must exist in roles table

**Success Response (200)**

```json
{
    "success": true,
    "message": "User added successfully",
    "data": {
        "id": 2,
        "name": "New Admin",
        "email": "newadmin@example.com",
        "roles": [...]
    }
}
```

**Error Responses**

| Code | Description |
|---|---|
| 401 | Unauthenticated |
| 403 | Forbidden - requires `create-user` permission |
| 422 | Validation error |
| 500 | Something went wrong |

---

## 3. Update User Activation Status

**Endpoint**

`PUT /admin-users/update-activation`

**Purpose**

Toggle a user's `is_active` status. Cannot deactivate a super_admin unless deactivating yourself.

**Authentication**

| Field | Value |
|---|---|
| Required | Yes |
| Guard | Sanctum |
| Permission | `update-user-activation` |

**Request Body**

| Field | Type | Required | Description |
|---|---|---|---|
| `user_id` | integer | Yes | ID of the user to toggle activation |

**Validation Rules**

- `user_id`: required, integer, exists:users,id

**Business Rules**

- If the target user has `super_admin` permission, the operation is blocked entirely (super_admin users cannot have their activation toggled).
- Toggles the `is_active` boolean field.

**Success Response (200)**

```json
{
    "success": true,
    "message": "User updated successfully"
}
```

**Error Responses**

| Code | Description |
|---|---|
| 400 | User cannot be updated |
| 401 | Unauthenticated |
| 403 | Forbidden - requires `edit-user` permission |
| 404 | User not found |
| 422 | Validation error |

---

## 4. Soft Delete User

**Endpoint**

`DELETE /admin-users/delete/{id}`

**Purpose**

Soft delete a user (sets `deleted_at` timestamp). Cannot delete super_admin users or yourself.

**Authentication**

| Field | Value |
|---|---|
| Required | Yes |
| Guard | Sanctum |
| Permission | `delete-user` |

**Path Parameters**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `id` | integer | Yes | User ID to soft delete |

**Business Rules**

- Cannot delete a user with `super_admin` role.
- Cannot delete yourself.
- Uses Eloquent's SoftDeletes trait.

**Success Response (200)**

```json
{
    "success": true,
    "message": "User deleted successfully"
}
```

**Error Responses**

| Code | Description |
|---|---|
| 400 | User cannot be deleted |
| 401 | Unauthenticated |
| 403 | Forbidden - requires `delete-user` permission |
| 404 | User not found |

---

## 5. Force Delete User (Permanent)

**Endpoint**

`DELETE /admin-users/delete-forever/{id}`

**Purpose**

Permanently delete a user from the database (bypasses soft delete). Cannot delete super_admin users or yourself.

**Authentication**

| Field | Value |
|---|---|
| Required | Yes |
| Guard | Sanctum |
| Permission | `delete-user` |

**Path Parameters**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `id` | integer | Yes | User ID to permanently delete |

**Business Rules**

- Can find and permanently delete both active and already soft-deleted users (uses `withTrashed()`).
- Cannot delete a user with `super_admin` role.
- Cannot delete yourself.
- Uses `forceDelete()` to bypass soft delete.

**Success Response (200)**

```json
{
    "success": true,
    "message": "User deleted successfully"
}
```

**Error Responses**

| Code | Description |
|---|---|
| 400 | User cannot be deleted |
| 401 | Unauthenticated |
| 403 | Forbidden - requires `delete-user` permission |
| 404 | User not found |

---

## 6. Restore Soft-Deleted User

**Endpoint**

`PUT /admin-users/restore/{id}`

**Purpose**

Restore a soft-deleted user. Cannot restore non-trashed users, super_admin users, or yourself.

**Authentication**

| Field | Value |
|---|---|
| Required | Yes |
| Guard | Sanctum |
| Permission | `restore-user` |

**Path Parameters**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `id` | integer | Yes | User ID to restore |

**Business Rules**

- Uses `withTrashed()` to find the soft-deleted user.
- User must be in a trashed state (`deleted_at` is not null).
- Cannot restore a user with `super_admin` role.
- Cannot restore yourself.
- Calls `restore()` to clear the `deleted_at` timestamp.

**Success Response (200)**

```json
{
    "success": true,
    "message": "User restored successfully"
}
```

**Error Responses**

| Code | Description |
|---|---|
| 400 | User cannot be restored (not trashed, is super_admin, or self) |
| 401 | Unauthenticated |
| 403 | Forbidden - requires `restore-user` permission |
| 404 | User not found |

---

## 7. List Soft-Deleted (Trashed) Users

**Endpoint**

`GET /admin-users/trashed`

**Purpose**

Retrieve a paginated list of all soft-deleted users.

**Authentication**

| Field | Value |
|---|---|
| Required | Yes |
| Guard | Sanctum |
| Permission | `view-users` |

**Query Parameters**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `limit` | integer | No | Items per page (default: 15) |

**Business Rules**

- Uses `onlyTrashed()` scope to query only soft-deleted records.
- Eager loads `permissions` relationship.

**Success Response (200)**

```json
{
    "data": [
        {
            "id": 3,
            "name": "Deleted User",
            "email": "deleted@example.com",
            "type": "admin",
            "is_active": true,
            "permissions": [...],
            "deleted_at": "2024-06-01T10:00:00Z",
            "created_at": "2024-01-01T00:00:00Z",
            "updated_at": "2024-05-01T00:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 1
    }
}
```

**Error Responses**

| Code | Description |
|---|---|
| 401 | Unauthenticated |
| 403 | Forbidden - requires `view-users` permission |
| 500 | Something went wrong |

---

## 8. List All Users

**Endpoint**

`GET /users`

**Purpose**

Retrieve a paginated, filterable list of all users. Supports filtering by type, active status, search, sorting, and pagination.

**Authentication**

| Field | Value |
|---|---|
| Required | Yes |
| Guard | Sanctum |
| Permission | `view-users` |

**Query Parameters**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `limit` | integer | No | Items per page (default: 15) |
| `page` | integer | No | Page number |
| `users` | string | No | `true` to filter by type `user` |
| `admins` | string | No | `true` to filter by type `admin` |
| `type` | string | No | Exact match on the `type` column |
| `trash` | string | No | `true` to only show soft-deleted users (where `deleted_at` is not null) |
| `is_active` | boolean | No | Filter by active status (`true`/`false`) |
| `search` | string | No | LIKE search on `name` and `email` fields |
| `order_by` | string | No | Column to sort by (default: `created_at`) |
| `sort` | string | No | Sort direction `asc` or `desc` (default: `desc`) |

**Example URLs**

```
GET /users?limit=10&page=1
GET /users?users=true&is_active=true
GET /users?admins=true&is_active=false
GET /users?search=john&order_by=name&sort=asc
GET /users?type=admin&is_active=true&search=admin&limit=20&page=2
GET /users?trash=true
GET /users?trash=true&search=jane&limit=10&page=1
```

**Business Rules**

- Uses `withQueryString()` so pagination links preserve all active filters.
- Data is formatted through `UserResource`.
- Pagination metadata includes explicit fields (page, from, to, last_page, etc.).

**Success Response (200)**

```json
{
    "success": true,
    "message": "Users listed successfully",
    "data": {
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "type": "user",
                "is_active": true,
                "permissions": [...],
                "created_at": "2024-01-01T00:00:00Z",
                "updated_at": "2024-01-01T00:00:00Z"
            }
        ],
        "page": 1,
        "current_page": 1,
        "from": 1,
        "to": 15,
        "last_page": 5,
        "path": "http://example.com/users",
        "per_page": 15,
        "total": 72,
        "next_page_url": "http://example.com/users?page=2",
        "prev_page_url": "",
        "last_page_url": "http://example.com/users?page=5",
        "first_page_url": "http://example.com/users?page=1"
    }
}
```

**Error Responses**

| Code | Description |
|---|---|
| 401 | Unauthenticated |
| 403 | Forbidden - requires `view-users` permission |
| 500 | Something went wrong |

---

## Database Impact

| Table | Relation | Type |
|---|---|---|
| `users` | Main table | Soft deletes via `deleted_at` column |

## Dependencies

| Component | File |
|---|---|
| Controller | `packages/marvel/src/Http/Controllers/UserController.php` |
| Repository | `packages/marvel/src/Database/Repositories/UserRepository.php` |
| Model | `packages/marvel/src/Database/Models/User.php` |
| Permission Enum | `packages/marvel/src/Enums/Permission.php` |
| Role Enum | `packages/marvel/src/Enums/Role.php` |
| Routes | `packages/marvel/src/Rest/Routes.php` |
| Translations (EN) | `resources/lang/en/message.php` |
| Translations (AR) | `resources/lang/ar/message.php` |
| Constants | `packages/marvel/config/constants.php` |
