# Sections API

---

### GET /sections — List Sections

**Purpose:** Fetch all sections ordered by their `order` column.

**Method:** `GET`

**URL:** `/sections`

**Authentication:** Required

**Permissions:** N/A

**Business Logic:**
1. Calls `Section::ordered()->get()` (uses `spatie/eloquent-sortable`)

**Success Response (200):**
```json
{
    "status": 200,
    "message": "Data fetched successfully",
    "success": true,
    "data": [
        {
            "id": 1,
            "type": "hero",
            "title": "Hero Banner",
            "endpoint": "general/hero?slug=summer-sale",
            "order": 1,
            "setting": {
                "front": { "autoplay": true, "slider_speed": 5000 },
                "back": { "slug": "summer-sale" }
            }
        }
    ]
}
```

---

### POST /sections — Create Section

**Purpose:** Create a new section.

**Method:** `POST`

**URL:** `/sections`

**Authentication:** Required

**Permissions:** N/A

**Request Body:**
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `type` | string | **Yes** | `required`, `string`, `max:100`, exists in `section_types` table |
| `title` | object | **Yes** | `required`, translatable array |
| `title.*` | string | **Yes** | `string`, `max:50`, unique translation |
| `with_product` | bool/int | **Yes** | `required`, `in:0,1` |
| `is_active` | bool/int | No | `nullable`, `in:0,1` |
| `title_visible` | bool/int | No | `nullable`, `in:0,1` |
| `order` | int | No | `nullable`, `integer` |
| `setting` | object | No | `nullable`, `array` |
| `setting.front` | object | No | `nullable`, `array` |
| `setting.back` | object | No | `nullable`, `array` |

**Business Logic:**
1. If `with_product=1`, validates `setting.back` only contains `slug` key
2. Order auto-assigned by `spatie/eloquent-sortable` if not provided

**Success Response (200):**
```json
{
    "status": 200,
    "message": "Section created successfully",
    "success": true,
    "data": {
        "id": 1,
        "type": "hero",
        "title": "Hero Banner",
        "endpoint": "general/hero?slug=summer-sale",
        "order": 1,
        "setting": {
            "front": { "autoplay": true, "slider_speed": 5000 },
            "back": { "slug": "summer-sale" }
        }
    }
}
```

---

### GET /sections/{section} — Show Section

**Purpose:** Fetch a single section by ID.

**Method:** `GET`

**URL:** `/sections/{section}`

**Authentication:** Required

**Permissions:** N/A

**Success Response (200):**
```json
{
    "status": 200,
    "message": "Data fetched successfully",
    "success": true,
    "data": {
        "id": 1,
        "type": "hero",
        "title": "Hero Banner",
        "endpoint": "general/hero?slug=summer-sale",
        "order": 1,
        "setting": {
            "front": { "autoplay": true, "slider_speed": 5000 },
            "back": { "slug": "summer-sale" }
        }
    }
}
```

---

### PUT /sections/{section} — Update Section

**Purpose:** Update a section's fields.

**Method:** `PUT`

**URL:** `/sections/{section}`

**Authentication:** Required

**Permissions:** N/A

**Request Body:**
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `title` | object | No | `sometimes`, translatable array |
| `title.*` | string | No | `string`, `max:50` |
| `order` | int | No | `sometimes`, `integer` |
| `is_active` | bool/int | No | `sometimes`, `in:0,1` |
| `title_visible` | bool/int | No | `sometimes`, `in:0,1` |
| `with_product` | bool | No | `sometimes`, `boolean` |
| `setting` | object | No | `nullable`, `array` |
| `setting.front` | object | No | `nullable`, `array` |
| `setting.back` | object | No | `nullable`, `array` |

**Business Logic:**
1. Reads existing `with_product` from the section if not in request
2. If `with_product` is true, validates `setting.back` only contains `slug`

**Success Response (200):**
```json
{
    "status": 200,
    "message": "Section updated successfully",
    "success": true,
    "data": {
        "id": 1,
        "type": "hero",
        "title": "Hero Banner (Updated)",
        "endpoint": "general/hero?slug=summer-sale",
        "order": 1,
        "setting": {
            "front": { "autoplay": true, "slider_speed": 5000 },
            "back": { "slug": "summer-sale" }
        }
    }
}
```

---

### DELETE /sections/{section} — Delete Section

**Purpose:** Permanently delete a section.

**Method:** `DELETE`

**URL:** `/sections/{section}`

**Authentication:** Required

**Permissions:** N/A

**Success Response (200):**
```json
{
    "status": 200,
    "message": "Section deleted successfully",
    "success": true
}
```

**Error Responses:**
| Status | Condition |
|--------|-----------|
| 401 | Unauthenticated |
| 404 | Section not found |
| 422 | Validation failure |
