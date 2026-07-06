# Activity Logs API

## Overview

The Activity Logs module tracks all CRUD operations and status changes across the system using `Spatie/laravel-activitylog`. Every create, update, delete, restore, force-delete, and status change on Products, Categories, Brands, Coupons, Flash Sales, Promotions, Roles, and Users is automatically recorded via Laravel Observers.

Activity logging is implemented through **queue jobs** (`LogActivityJob`) dispatched from observers and listeners. All jobs are dispatched to the `medium` priority queue. In testing (sync queue), jobs execute synchronously.

---

## Authentication

| Guard | Required |
|-------|----------|
| `sanctum` | ✓ |

Authorization is enforced via `$this->middleware('permission:' . Permission::VIEW_ACTIVITY_LOG)` — the authenticated user must hold the `view-activity-log` permission.

---

## Endpoints

### GET `/api/v1/logs/activity`

Fetch paginated activity logs with optional filtering.

#### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `log_name` | string | No | — | Filter by log name (e.g. `products`, `users`, `roles`) |
| `event` | string | No | — | Filter by event type (e.g. `created`, `updated`, `deleted`, `statusChanged`, `roleUpdated`) |
| `subject_type` | string | No | — | Filter by subject model class (e.g. `Marvel\Database\Models\Product`). Must be URL-encoded if it contains backslashes. |
| `causer_id` | integer | No | — | Filter by the user ID who performed the action |
| `search` | string | No | — | Full-text search across `description` and `log_name` (uses SQL `LIKE` with wildcards) |
| `per_page` | integer | No | 15 | Number of results per page |

#### URL Examples

**Filter by log name + event:**
```
GET /api/v1/logs/activity?log_name=products&event=created
```

**Filter by subject type (URL-encode backslashes):**
```
GET /api/v1/logs/activity?subject_type=Marvel%5CDatabase%5CModels%5CUser
```

**Filter by causer:**
```
GET /api/v1/logs/activity?causer_id=5
```

**Search across description and log_name:**
```
GET /api/v1/logs/activity?search=admin
```

**Combined with pagination:**
```
GET /api/v1/logs/activity?log_name=users&event=roleUpdated&per_page=25
```

**Full example — all params:**
```
GET /api/v1/logs/activity?log_name=products&event=updated&causer_id=3&search=price&per_page=50
```

#### Business Rules

- Logs are returned in **descending** order by `created_at` (most recent first)
- Only users with `view-activity-log` permission can access this endpoint
- The `log_name` values use the **plural English** entity name: `products`, `categories`, `brands`, `coupons`, `flash_sales`, `promotions`, `roles`, `users`
- Event values: `created`, `updated`, `deleted`, `restored`, `forceDeleted`, `statusChanged`, `roleUpdated`
- Activity logging is **queued** via `LogActivityJob` (queue: `medium`). In production with a queue worker, there may be a slight delay before logs appear.
- Each model that is the subject of the activity is **re-retrieved from the database** inside the job at execution time, ensuring the log reflects the actual state when the job runs.

#### Success Response

```json
{
    "success": true,
    "message": "Activity logs fetched successfully.",
    "data": [
        {
            "id": 1,
            "log_name": "products",
            "description": "Product created",
            "event": "created",
            "subject_type": "Marvel\\Database\\Models\\Product",
            "subject_id": 42,
            "causer_type": "Marvel\\Database\\Models\\User",
            "causer_id": 1,
            "properties": {
                "attributes": {
                    "name": "{\"en\":\"Organic Apples\",\"ar\":\"\\u062a\\u0641\\u0627\\u062d \\u0639\\u0636\\u0648\\u064a\"}",
                    "price": 45.00
                }
            },
            "created_at": "2026-07-05T10:30:00+00:00",
            "updated_at": "2026-07-05T10:30:00+00:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 1,
        "last_page": 1,
        "from": 1,
        "to": 1
    }
}
```

#### Error Responses

| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated (no token) |
| 403 | User lacks `view-activity-log` permission |

```json
{
    "message": "NOT_AUTHORIZED"
}
```

---

## Resource Structure — `ActivityLogResource`

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique identifier |
| `log_name` | string | Category name (`products`, `users`, `roles`, etc.) |
| `description` | string | Human-readable description of the action |
| `event` | string | Event type (`created`, `updated`, `deleted`, etc.) |
| `subject_type` | string | Fully qualified model class name |
| `subject_id` | integer | ID of the affected model |
| `causer_type` | string | Fully qualified user model class name |
| `causer_id` | integer | ID of the user who performed the action (nullable — null for system/self-registration) |
| `properties` | object\|null | JSON object containing the changed attributes and metadata (shape varies by event type — see Events Reference below) |
| `created_at` | string | ISO 8601 timestamp (nullable) |
| `updated_at` | string | ISO 8601 timestamp (nullable) |

---

## Events Reference — Properties Shape

### Standard CRUD Events (`created`, `updated`, `deleted`, `restored`, `forceDeleted`)

**created / deleted / restored / forceDeleted:**
No `properties` are passed (null). Only the event type and description distinguish them.

**updated (model observer):**
```json
{
    "old": { "field_name": "old_value", ... },
    "new": { "field_name": "new_value", ... }
}
```

### `statusChanged`
```json
{
    "old": { "is_active": "0" },
    "new": { "is_active": "1" }
}
```

### `roleUpdated` (from `LogUserRolesUpdated` listener)
```json
{
    "old": { "roles": ["customer"] },
    "new": { "roles": ["super_admin", "editor"] },
    "previous_roles": ["customer"],
    "new_roles": ["super_admin", "editor"],
    "roles_added": ["super_admin", "editor"],
    "roles_removed": ["customer"]
}
```

### Promotion-specific events (`products_synced`, `categories_updated`, `brands_updated`)
```json
{
    "old": { "product_ids": [1, 2] },
    "new": { "product_ids": [1, 2, 3, 4] }
}
```

---

## Database Schema

### `activity_log` Table

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | bigint | PK, AUTO_INCREMENT | Unique identifier |
| `log_name` | varchar(255) | NULLABLE | Category name (indexed) |
| `description` | text | — | Human-readable description |
| `subject_type` | varchar(255) | NULLABLE | Morphs: model class |
| `subject_id` | bigint | NULLABLE | Morphs: model ID |
| `causer_type` | varchar(255) | NULLABLE | Morphs: causer class |
| `causer_id` | bigint | NULLABLE | Morphs: causer ID |
| `event` | varchar(255) | NULLABLE | Event type |
| `properties` | json | NULLABLE | Changed attributes + metadata |
| `batch_uuid` | uuid | NULLABLE | Batch identifier |
| `created_at` | timestamp | NULLABLE | Creation time |
| `updated_at` | timestamp | NULLABLE | Last update |

Indexes: `log_name`, `subject` (subject_type + subject_id), `causer` (causer_type + causer_id)

---

## Architecture — Queue Flow

```
Model Event (created/updated/deleted/...)
       │
       ▼
  Observer (e.g. ProductObserver)
       │
       ▼
  LogActivityJob::dispatch(...)    ← Queue: 'medium'
       │
       ▼
  [Queue Worker] (sync in testing)
       │
       ▼
  LogActivityJob::handle()
       │
       ├── Re-retrieves subject model from DB
       ├── Sets causer explicitly via ActivityLogger::causerBy()
       └── Calls activity()->log()
```

For role changes specifically:

```
$user->assignRole($role)
       │
       ▼
  UserRolesUpdated event
       │
       ▼
  LogUserRolesUpdated listener (ShouldQueue)
       │
       ▼
  LogActivityJob::dispatch(...)    ← Queue: 'medium'
```

---

## Dependencies

| Component | File |
|-----------|------|
| Controller | `packages/marvel/src/Http/Controllers/ActivityLogController.php` |
| Resource | `packages/marvel/src/Http/Resources/ActivityLogResource.php` |
| Route | `packages/marvel/src/Rest/Routes.php` (line 724) |
| Job | `app/Jobs/LogActivityJob.php` |
| Observers | `app/Observers/{Product,Category,Brand,Coupon,FlashSale,Promotion,Role,User}Observer.php` |
| Event | `app/Events/UserRolesUpdated.php` (uses `SerializesModels`) |
| Listener | `app/Listeners/LogUserRolesUpdated.php` (implements `ShouldQueue`) |
| Translations | `resources/lang/{en,ar}/activity.php` |
| Package | `spatie/laravel-activitylog` |

Note: `ActivityLogService` (`app/Services/ActivityLog/ActivityLogService.php`) is **no longer** used by observers or listeners. All activity logging is now performed through `LogActivityJob` dispatches.

---

## Log Name Reference

| Log Name | Observer | Events Emitted |
|----------|----------|----------------|
| `products` | `ProductObserver` | created, updated, deleted, restored, forceDeleted, statusChanged |
| `categories` | `CategoryObserver` | created, updated, deleted, statusChanged |
| `brands` | `BrandObserver` | created, updated, deleted, statusChanged |
| `coupons` | `CouponObserver` | created, updated, deleted, enabled, disabled |
| `flash_sales` | `FlashSaleObserver` | created, updated, deleted, restored, forceDeleted, statusChanged |
| `promotions` | `PromotionObserver` | created, updated, deleted, statusChanged, products_synced, categories_updated, brands_updated |
| `roles` | `RoleObserver` | created, updated, deleted |
| `users` | `UserObserver` + `LogUserRolesUpdated` | created, updated, deleted, restored, forceDeleted, statusChanged, roleUpdated |
