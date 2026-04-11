# Image Handling Guide - Chawkbazar API

Complete guide to handle images in the Chawkbazar Laravel project using **Spatie Media Library**.

---

## 📦 Package Information

**Package Name:** `spatie/laravel-medialibrary`  
**Version:** `10.14.0`  
**Framework:** Laravel 10.30.1  
**Location:** Integrated in `packages/marvel/` and main application

### What is Spatie Media Library?

Spatie Media Library is a powerful Laravel package that handles:

- ✅ File uploads and storage
- ✅ Image conversions (thumbnails, different sizes)
- ✅ Multiple images per model
- ✅ Cloud storage integration (S3, etc.)
- ✅ Queue-based processing
- ✅ Automatic image optimization
- ✅ Collection-based organization

---

## 🚀 Quick Start

### 1. Make Your Model Support Images

Add the following to any model that needs images:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    // Your model code...

    /**
     * Register media conversions (image sizes)
     */
    public function registerMediaConversions(Media $media = null): void
    {
        // Thumbnail: 150x150
        $this->addMediaConversion('thumbnail')
            ->width(150)
            ->height(150)
            ->nonQueued();

        // Medium: 300x300
        $this->addMediaConversion('medium')
            ->width(300)
            ->height(300)
            ->nonQueued();

        // Large: 600x600
        $this->addMediaConversion('large')
            ->width(600)
            ->height(600)
            ->nonQueued();
    }
}
```

### 2. Current Implementation

Your project already has this implemented in:

- **User Model:** `app/Models/User.php` ✅ (Updated with media support)
- **Attachment Model:** `packages/marvel/src/Database/Models/Attachment.php` ✅

---

## 📷 Uploading Images

### Single Image Upload

```php
// From HTTP Request
$product = Product::find(1);
$product->addMediaFromRequest('image')
    ->toMediaCollection('products');

// From File Path
$product->addMedia('/path/to/image.jpg')
    ->toMediaCollection('products');

// From URL
$product->addMediaFromUrl('https://example.com/image.jpg')
    ->toMediaCollection('products');

// With Custom Name
$product->addMedia('/path/to/image.jpg')
    ->usingName('My Custom Name')
    ->toMediaCollection('products');
```

### Multiple Images Upload (Array of Images)

```php
// Upload multiple images at once
$product = Product::find(1);

$imageFiles = $request->file('images'); // Returns array

foreach ($imageFiles as $imageFile) {
    $product->addMedia($imageFile)
        ->toMediaCollection('gallery');
}

// Shorter way using addMultipleMediaFromRequest
$images = $request->file('images');
foreach ($images as $image) {
    $product
        ->addMedia($image)
        ->toMediaCollection('gallery');
}

// Chain method (cleaner)
foreach ($request->file('images') as $image) {
    $product
        ->addMedia($image)
        ->preservingOriginal()
        ->toMediaCollection('gallery');
}
```

### Upload with Validation

```php
// In Controller
public function uploadProductImages(Request $request, Product $product)
{
    // Validate request
    $validated = $request->validate([
        'images' => 'required|array|min:1|max:5',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
    ]);

    // Clear existing images
    $product->clearMediaCollection('gallery');

    // Upload new images
    foreach ($validated['images'] as $imageFile) {
        $product->addMedia($imageFile)
            ->toMediaCollection('gallery');
    }

    return response()->json([
        'message' => 'Images uploaded successfully',
        'images' => $this->getProductImages($product),
    ]);
}

// Helper method to get all images
private function getProductImages(Product $product)
{
    return $product->getMedia('gallery')->map(function ($media) {
        return [
            'id' => $media->id,
            'original' => $media->getUrl(),
            'thumbnail' => $media->getUrl('thumbnail'),
            'medium' => $media->getUrl('medium'),
            'large' => $media->getUrl('large'),
        ];
    });
}
```

---

## 🖼️ Retrieving Images

### Single Image

```php
$product = Product::find(1);

// Get original URL
$imageUrl = $product->getFirstMediaUrl('gallery');

// Get specific conversion
$thumbnailUrl = $product->getFirstMediaUrl('gallery', 'thumbnail');
$mediumUrl = $product->getFirstMediaUrl('gallery', 'medium');
$largeUrl = $product->getFirstMediaUrl('gallery', 'large');

// Get Media object (more details)
$media = $product->getFirstMedia('gallery');
if ($media) {
    echo $media->getUrl();
    echo $media->getPath();
    echo $media->file_name;
    echo $media->size;
    echo $media->mime_type;
}
```

### Multiple Images (Array)

```php
$product = Product::find(1);

// Get all media as array of URLs
$allImages = $product->getMedia('gallery')->map(function ($media) {
    return $media->getUrl();
})->toArray();

// Get all media with conversions
$imagesWithConversions = $product->getMedia('gallery')->map(function ($media) {
    return [
        'id' => $media->id,
        'original' => $media->getUrl(),
        'thumbnail' => $media->getUrl('thumbnail'),
        'medium' => $media->getUrl('medium'),
        'large' => $media->getUrl('large'),
        'name' => $media->file_name,
        'size' => $media->size,
    ];
})->toArray();

// Get specific number of images
$firstThreeImages = $product->getMedia('gallery')
    ->take(3)
    ->map(fn($media) => $media->getUrl('thumbnail'))
    ->toArray();
```

### Check if Model Has Images

```php
$product = Product::find(1);

// Check if has images
if ($product->hasMedia('gallery')) {
    echo "Product has images";
}

// Count images
$count = $product->getMedia('gallery')->count();

// Get specific image by ID
$media = $product->getMedia('gallery')->find($mediaId);
```

---

## 🔄 Media Collections

Collections are like folders for organizing different types of images per model.

```php
// Define default collection
$product->addMedia($file)->toMediaCollection('gallery');

// Multiple collections on same model
$product->addMedia($mainImage)->toMediaCollection('featured');
$product->addMedia($thumbnailImage)->toMediaCollection('thumbnail');
$product->addMedia($banner).toMediaCollection('banner');

// Retrieve from specific collection
$gallery = $product->getMedia('gallery');
$featured = $product->getMedia('featured');
$banner = $product->getMedia('banner');

// Clear specific collection
$product->clearMediaCollection('gallery');

// Clear all collections
$product->clearMediaCollection();
```

---

## 🛢️ Storage Configuration

### Configuration File Location

`packages/marvel/config/media-library.php`

### Key Settings

```php
// Default disk
'disk_name' => env('MEDIA_DISK', config('shop.media_disk')),

// Max file size (currently 10MB)
'max_file_size' => 1024 * 1024 * 10, // bytes

// Queue processing
'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),

// File naming strategy
'file_namer' => Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,

// Path generation
'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,
```

### .env Configuration

```env
# Media Library Configuration
MEDIA_DISK=public
QUEUE_CONVERSIONS_BY_DEFAULT=true

# Cloud Storage (if using S3)
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket
```

### Filesystem Configuration

Location: `config/filesystems.php`

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL') . '/storage',
        'visibility' => 'public',
    ],

    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
    ],
]
```

---

## 🎨 Image Conversions (Different Sizes)

### Define Conversions in Model

```php
public function registerMediaConversions(Media $media = null): void
{
    // Queued conversion (default)
    $this->addMediaConversion('thumbnail')
        ->width(150)
        ->height(150);

    // Non-queued (immediate, faster for small images)
    $this->addMediaConversion('medium')
        ->width(300)
        ->height(300)
        ->nonQueued();

    // With format specification
    $this->addMediaConversion('webp-thumbnail')
        ->width(150)
        ->height(150)
        ->format('webp')
        ->nonQueued();

    // Preserve aspect ratio (don't crop)
    $this->addMediaConversion('responsive')
        ->width(600)
        ->height(600)
        ->withoutEnlargement(); // Don't enlarge if smaller than dimensions

    // Conditional conversion
    if ($this->shouldGenerateWebp()) {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->nonQueued();
    }
}
```

---

## 🔗 API Response Examples

### Single Image Endpoint

```php
// Resource class
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => [
                'original' => $this->getFirstMediaUrl('gallery') ?? null,
                'thumbnail' => $this->getFirstMediaUrl('gallery', 'thumbnail') ?? null,
                'medium' => $this->getFirstMediaUrl('gallery', 'medium') ?? null,
                'large' => $this->getFirstMediaUrl('gallery', 'large') ?? null,
            ],
            'created_at' => $this->created_at,
        ];
    }
}

// Response
{
    "data": {
        "id": 1,
        "name": "Product Name",
        "description": "Product description",
        "image": {
            "original": "https://app.com/storage/products/image.jpg",
            "thumbnail": "https://app.com/storage/products/conversions/image-thumbnail.jpg",
            "medium": "https://app.com/storage/products/conversions/image-medium.jpg",
            "large": "https://app.com/storage/products/conversions/image-large.jpg"
        },
        "created_at": "2024-01-10T10:30:00.000000Z"
    }
}
```

### Multiple Images Endpoint

```php
// Resource class
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'images' => $this->getProductImages(),
            'created_at' => $this->created_at,
        ];
    }

    private function getProductImages()
    {
        return $this->getMedia('gallery')->map(function ($media) {
            return [
                'id' => $media->id,
                'original' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumbnail'),
                'medium' => $media->getUrl('medium'),
                'large' => $media->getUrl('large'),
                'name' => $media->file_name,
                'size' => $media->size,
                'mime_type' => $media->mime_type,
            ];
        })->toArray();
    }
}

// Response
{
    "data": {
        "id": 1,
        "name": "Product Name",
        "description": "Product description",
        "images": [
            {
                "id": 1,
                "original": "https://app.com/storage/gallery/image1.jpg",
                "thumbnail": "https://app.com/storage/gallery/conversions/image1-thumbnail.jpg",
                "medium": "https://app.com/storage/gallery/conversions/image1-medium.jpg",
                "large": "https://app.com/storage/gallery/conversions/image1-large.jpg",
                "name": "image1.jpg",
                "size": 2048000,
                "mime_type": "image/jpeg"
            },
            {
                "id": 2,
                "original": "https://app.com/storage/gallery/image2.jpg",
                "thumbnail": "https://app.com/storage/gallery/conversions/image2-thumbnail.jpg",
                "medium": "https://app.com/storage/gallery/conversions/image2-medium.jpg",
                "large": "https://app.com/storage/gallery/conversions/image2-large.jpg",
                "name": "image2.jpg",
                "size": 2048000,
                "mime_type": "image/jpeg"
            }
        ],
        "created_at": "2024-01-10T10:30:00.000000Z"
    }
}
```

---

## 📝 Complete Controller Example

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

class ProductImageController extends Controller
{
    /**
     * Upload single image
     */
    public function uploadImage(Request $request, Product $product)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Delete old featured image
        $product->clearMediaCollection('featured');

        // Upload new image
        $product->addMedia($validated['image'])
            ->toMediaCollection('featured');

        return response()->json([
            'message' => 'Image uploaded successfully',
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Upload multiple images
     */
    public function uploadImages(Request $request, Product $product)
    {
        $validated = $request->validate([
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Option 1: Append to existing images
        foreach ($validated['images'] as $imageFile) {
            $product->addMedia($imageFile)
                ->toMediaCollection('gallery');
        }

        // Option 2: Replace all images
        // $product->clearMediaCollection('gallery');
        // foreach ($validated['images'] as $imageFile) {
        //     $product->addMedia($imageFile)
        //         ->toMediaCollection('gallery');
        // }

        return response()->json([
            'message' => count($validated['images']) . ' images uploaded successfully',
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Delete specific image
     */
    public function deleteImage(Product $product, $mediaId)
    {
        $media = $product->getMedia('gallery')->find($mediaId);

        if (!$media) {
            return response()->json([
                'message' => 'Image not found',
            ], 404);
        }

        $media->delete();

        return response()->json([
            'message' => 'Image deleted successfully',
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Get all images
     */
    public function getImages(Product $product)
    {
        $images = $product->getMedia('gallery')->map(function ($media) {
            return [
                'id' => $media->id,
                'original' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumbnail'),
                'medium' => $media->getUrl('medium'),
                'large' => $media->getUrl('large'),
                'name' => $media->file_name,
            ];
        });

        return response()->json([
            'data' => $images,
            'count' => $images->count(),
        ]);
    }

    /**
     * Reorder images (change order in gallery)
     */
    public function reorderImages(Request $request, Product $product)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        foreach ($validated['order'] as $position => $mediaId) {
            $media = $product->getMedia('gallery')->find($mediaId);
            if ($media) {
                $media->update(['order_column' => $position]);
            }
        }

        return response()->json([
            'message' => 'Images reordered successfully',
            'product' => new ProductResource($product),
        ]);
    }
}
```

---

## 📡 API Routes Example

```php
// routes/api.php
Route::post('/products/{product}/image',
    [ProductImageController::class, 'uploadImage']);

Route::post('/products/{product}/images',
    [ProductImageController::class, 'uploadImages']);

Route::delete('/products/{product}/images/{mediaId}',
    [ProductImageController::class, 'deleteImage']);

Route::get('/products/{product}/images',
    [ProductImageController::class, 'getImages']);

Route::put('/products/{product}/images/reorder',
    [ProductImageController::class, 'reorderImages']);
```

---

## 🗂️ Database Tables

Media Library automatically creates these tables:

### `media` table

Stores all media information:

```
id
model_type (class name)
model_id (model ID)
collection_name (e.g., 'gallery', 'featured')
name
file_name
mime_type
disk
size
manipulations
custom_properties
responsive_images
order_column
created_at
updated_at
```

### `media_conversions_mapping` table

Maps generated image conversions

---

## 🚨 Handling Array Requests in Detail

### When Images Come as Array

```php
// HTML Form (multiple files)
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="images[]" multiple>
    <button type="submit">Upload</button>
</form>

// Request Structure
POST /api/products/1/images
{
    "images": [
        "file1.jpg (binary)",
        "file2.jpg (binary)",
        "file3.jpg (binary)"
    ]
}

// Access in Controller
public function uploadImages(Request $request)
{
    // Get all files
    $files = $request->file('images'); // Array of UploadedFile objects

    // Validate entire array
    $request->validate([
        'images' => 'required|array|min:1|max:5',
        'images.*' => 'image|max:5120',
    ]);

    // Process each file
    foreach ($files as $index => $file) {
        // $file is an instance of UploadedFile
        $product->addMedia($file)
            ->setName("image-{$index}")
            ->toMediaCollection('gallery');
    }
}
```

### Array Response

```php
// When returning multiple images
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'images' => [
            [
                'id' => 1,
                'url' => 'https://...',
                'thumbnail' => 'https://...',
            ],
            [
                'id' => 2,
                'url' => 'https://...',
                'thumbnail' => 'https://...',
            ],
            // ... more images
        ],
    ];
}
```

---

## ⚙️ Advanced Features

### Responsive Images

```php
// Generate multiple responsive sizes
$this->addMediaResponsiveImages()
    ->useFilesystem('public')
    ->generateResponsiveImageSet();

// For each media item
$media->generated_conversions; // Array of all conversions
```

### Using AWS S3

```php
// .env
MEDIA_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket

// Code still same, files go to S3 automatically
$product->addMedia('/path/image.jpg')
    ->toMediaCollection('gallery');
```

### Generate Conversions on Demand

```php
// Manually trigger conversion generation
$media->refresh();

// Force regenerate all conversions
$media->manipulations = [];
$media->generated_conversions = [];
$media->save();
```

### Delete Images

```php
// Delete specific media
$media = $product->getFirstMedia('gallery');
$media->delete();

// Delete all from collection
$product->clearMediaCollection('gallery');

// Delete all media (all collections)
$product->clearMediaCollection();
```

---

## 🐛 Common Issues & Solutions

| Issue                       | Solution                                   |
| --------------------------- | ------------------------------------------ |
| Images not displaying       | Check `storage/app/public` permissions     |
| Large files fail            | Increase `max_file_size` in config         |
| Conversions taking too long | Set `nonQueued()` for immediate generation |
| Can't find images           | Run `php artisan storage:link`             |
| S3 images not public        | Check S3 bucket public access settings     |

---

## 📚 Useful Commands

```bash
# Link storage to public
php artisan storage:link

# Clean up failed conversions
php artisan media-library:clean

# Generate missing conversions
php artisan media-library:regenerate

# Clear media cache
php artisan cache:clear
```

---

## 🔍 Current Package Status

✅ **Installed:** `spatie/laravel-media-library` v10.14.0  
✅ **Models Updated:** User, Attachment  
✅ **Configuration:** `packages/marvel/config/media-library.php`  
✅ **Storage:** Local (public disk) - ready for S3 upgrade  
✅ **Conversions:** Thumbnail (150x150), Medium (300x300), Large (600x600)

---

## 📖 Additional Resources

- [Spatie Media Library Docs](https://spatie.be/docs/laravel-medialibrary/v10/introduction)
- [Laravel Storage Docs](https://laravel.com/docs/10.x/filesystem)
- [S3 Integration Guide](https://laravel.com/docs/10.x/filesystem#s3-driver-configuration)

---

## Quick Reference Table

| Task                | Code                                                 |
| ------------------- | ---------------------------------------------------- |
| Upload image        | `$model->addMedia($file)->toMediaCollection('name')` |
| Get image URL       | `$model->getFirstMediaUrl('name')`                   |
| Get all images      | `$model->getMedia('name')`                           |
| Delete image        | `$media->delete()`                                   |
| Clear collection    | `$model->clearMediaCollection('name')`               |
| Check if has images | `$model->hasMedia('name')`                           |
| Get conversion      | `$media->getUrl('thumbnail')`                        |

---

Generated: April 11, 2026  
Framework: Laravel 10.30.1  
Package: Spatie Media Library 10.14.0
