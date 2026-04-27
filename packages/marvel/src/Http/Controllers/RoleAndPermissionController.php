<?php

namespace Marvel\Http\Controllers;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Marvel\Database\Models\User;
use Marvel\Http\Resources\PermissionResource;
use Marvel\Http\Resources\RoleResource;
use Marvel\Http\Resources\UserResource;
use Marvel\Traits\ApiResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Marvel\Enums\Permission as PermissionEnum;

class RoleAndPermissionController extends CoreController
{
    use ApiResponse;
    public function __construct()
    {
        $this->middleware('permission:' . PermissionEnum::CREATE_ROLES)->only('addRole');
        $this->middleware('permission:' . PermissionEnum::UPDATE_ROLES)->only('updateRole');
        $this->middleware('permission:' . PermissionEnum::DELETE_ROLES)->only('destroyRole');

        $this->middleware('permission:' . PermissionEnum::ASSIGN_ROLE)->only('assignRole');
        $this->middleware('permission:' . PermissionEnum::REMOVE_ROLE)->only('removeRoleFromUser');
    }

    // ================= ROLES =================

    public function getAllRoles()
    {
        $limit = request('limit', 10);
        $search = request('search', null);
        $roles = Role::paginate($limit);

        return $this->apiResponse('Roles fetched successfully', 200, true, RoleResource::collection($roles));
    }

    public function addRole(Request $request)
    {
        $request->validate([
            'display_name' => 'required|array',
            'display_name.*' => [
                'required',
                'string',
                UniqueTranslationRule::for('roles', 'display_name'),
            ],
        ]);

        $name = strtolower(str_replace(' ', '_', $request->display_name['en']));

        $role = Role::create([
            'name' => $name,
            'display_name' => $request->display_name,
            'guard_name' => 'api',
        ]);

        return $this->apiResponse('Role added successfully', 200, true, RoleResource::make($role));
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findById($id, 'api');

        $request->validate([
            'display_name' => 'required|array',
            'display_name.*' => [
                'required',
                'string',
                UniqueTranslationRule::for('roles', 'display_name')->ignore($id),
            ],
        ]);
        $name = strtolower(str_replace(' ', '_', $request->display_name['en']));
        $role->update([
            'name' => $name,
            'display_name' => $request->display_name,
        ]);

        return $this->apiResponse('Role updated successfully', 200, true, RoleResource::make($role));
    }

    public function destroyRole($id)
    {
        $role = Role::findById($id, 'api');

        $role->delete();

        return $this->apiResponse('Role deleted successfully', 200, true, null);
    }

    public function assignRole(Request $request, $userId)
    {
        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => Rule::exists('roles', 'id')->where(fn($q) => $q->where('guard_name', 'api')),
        ]);

        $user = User::findOrFail($userId);
        $roles = Role::whereIn('id', $request->role_ids)->where('guard_name', 'api')->get();
        $user->syncRoles($roles)->load('roles', 'permissions');

        return $this->apiResponse('Role assigned successfully', 200, true, UserResource::make($user));
    }

    public function removeRoleFromUser(Request $request, $userId)
    {
        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => Rule::exists('roles', 'id')->where(fn($q) => $q->where('guard_name', 'api')),
        ]);
        $user = User::findOrFail($userId);
        $roles = Role::whereIn('id', $request->role_ids)->where('guard_name', 'api')->get();
        foreach ($roles as $role) {
            $user->removeRole($role);
        }
        $user->load('roles', 'permissions');

        return $this->apiResponse('Role removed successfully', 200, true, UserResource::make($user));
    }

    // ================= PERMISSIONS =================

    public function getAllPermissions()
    {
        $limit = request('limit', 100);
        $search = request('search', null);
        $permissions = Permission::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->paginate($limit);

        return $this->apiResponse('Permissions fetched successfully', 200, true, PermissionResource::collection($permissions));
    }



    public function assignPermissionToRole(Request $request, $roleId)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => ['distinct', 'string', 'max:50', Rule::unique('permissions', 'name')->where(fn($q) => $q->where('guard_name', 'api'))],
        ]);

        $role = Role::findById($roleId, 'api');

        $permissions = Permission::whereIn('id', $request->permissions)->get();

        $role->syncPermissions($permissions)->load('permissions');

        return $this->apiResponse('Permission assigned successfully', 200, true, RoleResource::make($role));
    }



   

    
}