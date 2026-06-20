<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        if ($request->has('show_deleted') && $request->show_deleted) {
            $query->onlyTrashed();
        }

        $roles = $query->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties(['role_name' => $role->name])
            ->log('created role');

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Show the form for editing the role.
     */
    public function edit(Role $role)
    {
        $permissionGroups = $this->roleService->getPermissionGroups();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $isProtected = $this->roleService->isProtectedRole($role);
        $criticalPermissions = $this->roleService->getCriticalPermissions($role);

        return view('admin.roles.edit', compact(
            'role',
            'permissionGroups',
            'rolePermissions',
            'isProtected',
            'criticalPermissions'
        ));
    }

    /**
     * Update the role.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();

        // Validate protected permissions
        $newPermissions = $validated['permissions'] ?? [];
        $validation = $this->roleService->validatePermissionUpdate($role, $newPermissions);

        if (! $validation['valid']) {
            return back()->with('error', 'Cannot revoke critical permissions: '.implode(', ', $validation['missing']));
        }

        // Update role details
        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Sync permissions
        $oldPermissions = $role->permissions->pluck('name')->toArray();
        $role->syncPermissions($newPermissions);
        $newPermissionsActual = $role->fresh()->permissions->pluck('name')->toArray();

        $added = array_diff($newPermissionsActual, $oldPermissions);
        $removed = array_diff($oldPermissions, $newPermissionsActual);

        // Log activity
        if (! empty($added) || ! empty($removed)) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($role)
                ->withProperties([
                    'role_name' => $role->name,
                    'permissions_added' => array_values($added),
                    'permissions_removed' => array_values($removed),
                ])
                ->log('updated role permissions');
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Soft delete the role.
     */
    public function destroy(Role $role)
    {
        // Check if role is protected
        if ($this->roleService->isProtectedRole($role)) {
            return back()->with('error', 'Cannot delete protected system role.');
        }

        // Check if role has active users
        if ($role->users()->exists()) {
            $count = $role->users()->count();

            return back()->with('error', "Cannot delete role with {$count} active user(s). Please reassign users first.");
        }

        // Log activity before deletion
        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties(['role_name' => $role->name])
            ->log('deleted role');

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' deleted successfully.");
    }
}
