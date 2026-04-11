<?php

namespace Marvel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Marvel\Database\Models\User;
use Marvel\Http\Resources\PermissionResource;
use Marvel\Http\Resources\RoleResource;
use Marvel\Http\Resources\UserResource;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionController extends CoreController
{
    public function __construct()
    {
        $this->middleware('permission:create roles')->only('addRole');
        $this->middleware('permission:update roles')->only('updateRole');
        $this->middleware('permission:delete roles')->only('destroyRole');

        $this->middleware('permission:create permissions')->only('addPermission');

        $this->middleware('permission:assign role')->only('assignRole');
        $this->middleware('permission:remove role')->only('removeRoleFromUser');

        $this->middleware('permission:give permission')->only('givePermission');
        $this->middleware('permission:sync permissions')->only('syncPermissions');
        $this->middleware('permission:remove permission')->only('removePermission');
    }

    // ================= ROLES =================

    public function getAllRoles()
    {
        $limit = request('limit', 10);
        $search = request('search', null);
        $roles = Role::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => RoleResource::collection($roles),
        ]);
    }

    public function addRole(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')->where(fn($q) => $q->where('guard_name', 'api')),
            ],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api',
        ]);

        return response()->json([
            'success' => true,
            'data' => RoleResource::make($role),
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findById($id, 'api');

        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')->where(fn($q) => $q->where('id', '!=', $role->id)->where('guard_name', 'api')),
            ],
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'data' => RoleResource::make($role),
        ]);
    }

    public function destroyRole($id)
    {
        $role = Role::findById($id, 'api');

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted',
        ]);
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

        return response()->json([
            'success' => true,
            'data' => UserResource::make($user),
        ]);
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

        return response()->json([
            'success' => true,
            'data' => UserResource::make($user),
        ]);
    }

    // ================= PERMISSIONS =================

    public function getAllPermissions()
    {
        $limit = request('limit', 100);
        $search = request('search', null);
        $permissions = Permission::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($permissions),
        ]);
    }

    public function addPermission(Request $request)
    {
        $permissions = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => ['distinct','string','max:50', Rule::unique('permissions', 'name')->where(fn($q) => $q->where('guard_name', 'api'))],
        ]);


        $data =[];
        foreach ($permissions['permissions'] as $permission) {
            $permission = Permission::create([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
            $data[] = $permission;
        }

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($data),
        ]);
    }

    public function assignPermissionToRole(Request $request, $roleId)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => ['distinct','string','max:50', Rule::unique('permissions', 'name')->where(fn($q) => $q->where('guard_name', 'api'))],
        ]);

        $role = Role::findById($roleId, 'api');

        $permissions = Permission::whereIn('id', $request->permissions)->get();

        $role->syncPermissions($permissions)->load('permissions');

        return response()->json([
            'success' => true,
            'data' => RoleResource::make($role),
        ]);
    }

    public function givePermission(Request $request, $userId)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => ['distinct','string','max:50', Rule::unique('permissions', 'name')->where(fn($q) => $q->where('guard_name', 'api'))],
        ]);

        $user = User::findOrFail($userId);

        $permissions = Permission::whereIn('id', $request->permissions)->get();

        $user->givePermissionTo($permissions)->load('permissions');

        return response()->json([
            'success' => true,
            'data' => UserResource::make($user),
        ]);
    }

    public function syncPermissions(Request $request, $userId)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => ['distinct','string','max:50', Rule::unique('permissions', 'name')->where(fn($q) => $q->where('guard_name', 'api'))],
        ]);

        $user = User::findOrFail($userId);

        $permissions = Permission::whereIn('id', $request->permissions)->get();

        $user->syncPermissions($permissions)->load('permissions');

        return response()->json([
            'success' => true,
            'data' => UserResource::make($user),
        ]);
    }

    public function removePermission(Request $request, $userId)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $user = User::findOrFail($userId);

        $permission = Permission::findById($request->permission_id, 'api');

        $user->revokePermissionTo($permission);

        return response()->json([
            'success' => true,
            'message' => 'Permission removed',
        ]);
    }
}