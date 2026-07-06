<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Marvel\Database\Models\CmsPage;
use Marvel\Database\Models\User;
use Marvel\Enums\Permission as PermissionEnum;
use Marvel\Enums\Role as RoleEnum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsPageTest extends TestCase
{
    use RefreshDatabase;

    private function seedEditorPermission(): void
    {
        $guard = 'api';
        Permission::findOrCreate(PermissionEnum::SUPER_ADMIN, $guard);
        Permission::findOrCreate(PermissionEnum::EDITOR, $guard);
        $role = Role::findOrCreate(RoleEnum::EDITOR, $guard, ['display_name' => 'Editor']);
        $role->givePermissionTo(PermissionEnum::EDITOR);
    }

    private function makeEditorUser(): User
    {
        $this->seedEditorPermission();

        /** @var User $user */
        $user = User::create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'type' => 'user',
            'phone_number' => '01000000001',
        ]);

        $user->givePermissionTo(PermissionEnum::EDITOR);
        $user->assignRole(RoleEnum::EDITOR);

        return $user;
    }

    public function test_public_can_fetch_page_by_slug_sorted_content(): void
    {
        /** @var CmsPage $page */
        $page = CmsPage::create([
            'path' => '/home',
            'slug' => 'home',
            'title' => 'Home',
            'content' => [
                ['type' => 'B', 'order' => 2],
                ['type' => 'A', 'order' => 1],
            ],
        ]);

        $response = $this->getJson('/api/v1/cms-pages/home');

        $response->assertOk();
        $response->assertJsonPath('slug', 'home');
        $response->assertJsonPath('content.0.type', 'A');
        $response->assertJsonPath('content.1.type', 'B');
    }

    public function test_editor_can_create_update_and_delete_page(): void
    {
        $user = $this->makeEditorUser();
        Sanctum::actingAs($user);

        // Create
        $createPayload = [
            'path' => '/landing',
            'slug' => 'landing',
            'title' => 'Landing',
            'content' => [
                ['type' => 'Hero', 'order' => 2],
                ['type' => 'Heading', 'order' => 1],
            ],
        ];

        $create = $this->postJson('/api/v1/cms-pages', $createPayload);
        $create->assertCreated();
        $create->assertJsonPath('slug', 'landing');
        $create->assertJsonPath('content.0.type', 'Heading');

        $pageId = $create['id'];

        // Update
        $updatePayload = [
            'slug' => 'landing',
            'path' => 'landing',
            'title' => 'Updated Landing',
            'content' => [
                ['type' => 'Heading', 'order' => 1],
            ],
        ];

        $update = $this->putJson("/api/v1/cms-pages/{$pageId}", $updatePayload);
        $update->assertOk();
        $update->assertJsonPath('title', 'Updated Landing');

        // Delete
        $delete = $this->deleteJson("/api/v1/cms-pages/{$pageId}");
        $delete->assertOk();
    }

    public function test_non_editor_cannot_mutate_pages(): void
    {
        $user = User::create([
            'name' => 'Viewer',
            'email' => 'viewer@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'type' => 'user',
            'phone_number' => '01000000002',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/cms-pages', [
            'slug' => 'blocked',
            'path' => 'blocked',
            'title' => 'Blocked',
        ]);

        $response->assertStatus(403);
    }
}

