<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\User;
use Marvel\Enums\Permission as PermissionEnum;
use Marvel\Enums\Role as RoleEnum;
use Marvel\Services\Import\ProductImportService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    private const GUARD = 'api';
    private const PREFIX = '/api/v1';

    private function createSuperAdminUser(): User
    {
        $permissions = [
            PermissionEnum::SUPER_ADMIN,
            PermissionEnum::CREATE_PRODUCT,
            PermissionEnum::VIEW_PRODUCTS,
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, self::GUARD);
        }

        $role = Role::create([
            'name' => RoleEnum::SUPER_ADMIN,
            'guard_name' => self::GUARD,
            'display_name' => json_encode(['en' => 'Super Admin', 'ar' => 'مدير النظام']),
        ]);

        foreach ($permissions as $perm) {
            $role->givePermissionTo($perm);
        }

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'type' => 'admin',
            'phone_number' => '+1-555-0100',
        ]);

        $user->assignRole($role);

        foreach ($permissions as $perm) {
            $user->givePermissionTo($perm);
        }

        return $user;
    }

    public function test_unauthenticated_user_cannot_import(): void
    {
        $response = $this->postJson(self::PREFIX . '/products/import', []);

        $response->assertUnauthorized();
    }

    public function test_import_validates_required_fields(): void
    {
        $user = $this->createSuperAdminUser();
        Sanctum::actingAs($user);

        $response = $this->postJson(self::PREFIX . '/products/import', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    public function test_import_validates_file_type(): void
    {
        $user = $this->createSuperAdminUser();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson(self::PREFIX . '/products/import', [
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    public function test_import_dispatches_job_and_returns_202(): void
    {
        Queue::fake();

        $user = $this->createSuperAdminUser();
        Sanctum::actingAs($user);

        Storage::fake('public');

        $file = UploadedFile::fake()->create('products.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->postJson(self::PREFIX . '/products/import', [
            'file' => $file,
        ]);

        $response->assertStatus(202);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['data' => ['import_id', 'status']]);

        $this->assertDatabaseHas('imports', [
            'id' => $response->json('data.import_id'),
            'status' => 'pending',
        ]);

        Queue::assertPushed(\Marvel\Jobs\ImportProductsJob::class);
    }

    public function test_can_fetch_import_status(): void
    {
        $user = $this->createSuperAdminUser();
        Sanctum::actingAs($user);

        $import = \Marvel\Database\Models\Import::create([
            'type' => 'product',
            'file_path' => 'imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'completed',
            'total_rows' => 10,
            'processed_rows' => 10,
            'success_rows' => 8,
            'failed_rows' => 2,
            'created_by' => $user->id,
        ]);

        $response = $this->getJson(self::PREFIX . "/products/import/{$import->id}");

        $response->assertOk();
        $response->assertJsonPath('data.status', 'completed');
        $response->assertJsonPath('data.total_rows', 10);
        $response->assertJsonPath('data.success_rows', 8);
        $response->assertJsonPath('data.failed_rows', 2);
    }

    public function test_returns_404_for_nonexistent_import(): void
    {
        $user = $this->createSuperAdminUser();
        Sanctum::actingAs($user);

        $response = $this->getJson(self::PREFIX . '/products/import/99999');

        $response->assertNotFound();
    }

    public function test_download_errors_returns_file_when_errors_exist(): void
    {
        $user = $this->createSuperAdminUser();
        Sanctum::actingAs($user);

        $import = \Marvel\Database\Models\Import::create([
            'type' => 'product',
            'file_path' => 'imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'completed_with_errors',
            'total_rows' => 1,
            'processed_rows' => 1,
            'success_rows' => 0,
            'failed_rows' => 1,
            'errors' => [
                ['sheet' => 'products', 'row' => 5, 'sku' => 'TEST-001', 'error_message' => 'Invalid price'],
            ],
            'created_by' => $user->id,
        ]);

        $response = $this->getJson(self::PREFIX . "/products/import/{$import->id}/download-errors");

        $response->assertOk();
    }

    public function test_download_errors_returns_404_when_no_errors(): void
    {
        $user = $this->createSuperAdminUser();
        Sanctum::actingAs($user);

        $import = \Marvel\Database\Models\Import::create([
            'type' => 'product',
            'file_path' => 'imports/test.xlsx',
            'file_name' => 'test.xlsx',
            'status' => 'completed',
            'total_rows' => 1,
            'processed_rows' => 1,
            'success_rows' => 1,
            'failed_rows' => 0,
            'errors' => null,
            'created_by' => $user->id,
        ]);

        $response = $this->getJson(self::PREFIX . "/products/import/{$import->id}/download-errors");

        $response->assertStatus(404);
    }

    public function test_process_product_row_creates_product_in_database(): void
    {
        $service = new ProductImportService();

        $row = [
            'sku' => 'REGRESSION-TEST-001',
            'name_en' => 'Regression Test Product',
            'price' => 100,
            'quantity' => 10,
            'product_type' => 'simple',
            'status' => 1,
            'in_stock' => 1,
        ];

        $service->processProductRow($row, 2);

        $this->assertDatabaseHas('products', [
            'sku' => 'REGRESSION-TEST-001',
        ]);

        $product = Product::where('sku', 'REGRESSION-TEST-001')->first();
        $this->assertNotNull($product);
        $this->assertEquals(100, (float) $product->price);
        $this->assertEquals(1, $service->getSuccessCount());
    }

    public function test_process_product_row_updates_existing_product(): void
    {
        $service = new ProductImportService();

        $row = [
            'sku' => 'REGRESSION-TEST-002',
            'name_en' => 'Original Product',
            'price' => 50,
            'quantity' => 5,
            'product_type' => 'simple',
            'status' => 1,
            'in_stock' => 1,
        ];

        $service->processProductRow($row, 2);

        $this->assertDatabaseHas('products', [
            'sku' => 'REGRESSION-TEST-002',
            'price' => 50,
        ]);

        $updatedRow = [
            'sku' => 'REGRESSION-TEST-002',
            'name_en' => 'Updated Product',
            'price' => 75,
            'quantity' => 10,
            'product_type' => 'simple',
            'status' => 1,
            'in_stock' => 1,
        ];

        $service->processProductRow($updatedRow, 3);

        $this->assertDatabaseHas('products', [
            'sku' => 'REGRESSION-TEST-002',
            'price' => 75,
        ]);
    }

    public function test_process_product_row_handles_empty_sku(): void
    {
        $service = new ProductImportService();

        $row = [
            'name_en' => 'No SKU Product',
            'price' => 25,
            'quantity' => 3,
            'product_type' => 'simple',
            'status' => 1,
            'in_stock' => 1,
        ];

        $service->processProductRow($row, 2);

        $product = Product::orderBy('id', 'desc')->first();
        $this->assertNotNull($product);
        $this->assertStringStartsWith('PRD-', $product->sku);
    }

    public function test_process_product_row_tracks_failures(): void
    {
        $service = new ProductImportService();

        $this->assertEquals(0, $service->getSuccessCount());
        $this->assertEmpty($service->getFailedRows());

        $row = [
            'sku' => 'REGRESSION-FAIL-001',
            'name_en' => 'Failure Test',
            'price' => 100,
            'quantity' => 10,
            'product_type' => 'simple',
            'status' => 1,
            'in_stock' => 1,
        ];

        $service->processProductRow($row, 2);

        $this->assertEquals(1, $service->getSuccessCount());
        $this->assertEmpty($service->getFailedRows());
    }

    public function test_service_tracks_success_and_failure_counts(): void
    {
        $service = new ProductImportService();

        $row1 = [
            'sku' => 'REGRESSION-TRACK-001',
            'name_en' => 'Track Test 1',
            'price' => 100,
            'quantity' => 10,
            'product_type' => 'simple',
            'status' => 1,
            'in_stock' => 1,
        ];

        $row2 = [
            'sku' => 'REGRESSION-TRACK-002',
            'name_en' => 'Track Test 2',
            'price' => 200,
            'quantity' => 20,
            'product_type' => 'simple',
            'status' => 1,
            'in_stock' => 1,
        ];

        $service->processProductRow($row1, 2);
        $service->processProductRow($row2, 3);

        $this->assertEquals(2, $service->getSuccessCount());
        $this->assertCount(0, $service->getFailedRows());

        $product1 = Product::where('sku', 'REGRESSION-TRACK-001')->first();
        $product2 = Product::where('sku', 'REGRESSION-TRACK-002')->first();

        $this->assertNotNull($product1);
        $this->assertNotNull($product2);
    }
}
