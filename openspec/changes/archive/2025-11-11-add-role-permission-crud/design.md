# Design: Role and Permission CRUD Management

## Context

Beautyversity uses Spatie Laravel Permission for RBAC with 5 roles (student, content-manager, instructor, admin, super-admin) and 40+ permissions. Currently, all role-permission assignments are seeded via `RolePermissionSeeder.php`. This change introduces a conservative administrative UI for runtime role management while maintaining security.

### Constraints
- **Security First**: Authorization system is critical infrastructure
- **Minimal Complexity**: Avoid over-engineering with role hierarchies or dynamic permissions
- **Audit Compliance**: All changes must be traceable
- **Backward Compatibility**: Existing seeder-based permissions must continue to work

### Stakeholders
- **Super-Admins**: Need flexibility to adjust permissions without deployments
- **Developers**: Need clear permission structure and audit trails
- **Security**: Require protection against privilege escalation and self-lockout

## Goals / Non-Goals

### Goals
1. Enable runtime role-permission assignment via admin UI
2. Provide complete audit trail for all authorization changes
3. Prevent self-lockout and privilege escalation
4. Maintain code-defined permission registry (no ad-hoc permissions)
5. Support role soft deletion with validation

### Non-Goals
1. ❌ Dynamic permission creation from UI
2. ❌ Role hierarchy/inheritance
3. ❌ Direct user permission assignment (bypass roles)
4. ❌ Permission expiry or scheduling
5. ❌ Multi-tenancy or team-based permissions

## Decisions

### Decision 1: Permissions Stay Code-Defined
**Choice**: Permissions defined in seeder/migration only, not creatable via UI

**Rationale**:
- Permissions are tightly coupled to controller authorization checks
- Creating permissions without corresponding code is meaningless
- Prevents "permission sprawl" from ad-hoc creation
- Keeps permission list authoritative and reviewable in version control

**Alternatives Considered**:
- ❌ **Dynamic UI creation**: Too risky, creates orphaned permissions
- ❌ **Sync from codebase**: Complex, requires AST parsing of controllers

### Decision 2: Soft Delete for Roles
**Choice**: Use soft deletes with validation (no active users)

**Rationale**:
- Prevents accidental data loss
- Maintains referential integrity in activity logs
- Allows role restoration if deleted by mistake
- Simple to implement with Eloquent trait

**Migration Strategy**:
- Add `deleted_at` column to `roles` table
- No data migration needed (existing roles stay active)
- Cleanup: Manual periodic purging of old soft-deleted roles (optional)

### Decision 3: Matrix UI for Permission Assignment
**Choice**: Grouped checkbox matrix (rows=roles, columns=permissions grouped by category)

**Rationale**:
- Visual overview of permission distribution
- Bulk assignment/revocation in single operation
- Familiar pattern from other admin systems
- Responsive design works on tablet/desktop

**Permission Grouping**:
```
Course Management: [view, create, edit, delete, publish, unpublish] courses
Lesson Management: [view, create, edit, delete] lessons
Article Management: [view, create, edit, delete, publish, unpublish] articles
User Management: [view, create, edit, delete, assign roles] users
Enrollment Management: [view, manage, approve, reject] enrollments
Category Management: [view, create, edit, delete] article/course categories
Tag Management: [view, create, edit, delete] tags
Admin Panel: [access admin panel, view dashboard, manage site settings, view reports]
System Management: [manage roles, manage permissions]
Student Features: [enroll courses, view enrolled courses, access course content, complete lessons]
```

**Implementation**: Alpine.js for checkbox interaction, Livewire avoided for simplicity

### Decision 4: Spatie Activity Log for Audit Trail
**Choice**: Use `spatie/laravel-activitylog` package

**Rationale**:
- Industry standard package from same vendor (Spatie)
- Minimal configuration required
- Supports causers, subjects, properties out of the box
- Searchable/filterable activity log UI

**What Gets Logged**:
- Role created: `{causer} created role "{role_name}"`
- Role updated: `{causer} updated role "{role_name}" (name/description changes)`
- Permission assigned: `{causer} assigned permission "{permission}" to role "{role}"`
- Permission revoked: `{causer} revoked permission "{permission}" from role "{role}"`
- Role soft deleted: `{causer} deleted role "{role_name}"`
- Role restored: `{causer} restored role "{role_name}"`

**Log Properties**:
```php
[
    'role_id' => 3,
    'role_name' => 'content-manager',
    'permission_name' => 'edit articles', // for permission changes
    'changes' => ['old' => ..., 'new' => ...] // for updates
]
```

### Decision 5: Protected Role System
**Choice**: Hardcoded list of protected roles (`super-admin`, `student`)

**Rationale**:
- Prevents catastrophic system breakage
- Student role is foundational for enrollment system
- Super-Admin role must always exist for bootstrap access

**Protection Rules**:
- ✅ Can update name/description
- ✅ Can assign/revoke non-critical permissions
- ❌ Cannot delete (soft or hard)
- ❌ Cannot revoke `manage roles and permissions` from Super-Admin
- ❌ Cannot revoke `enroll courses` from student (business logic dependency)

**Implementation**:
```php
// config/authorization.php
return [
    'protected_roles' => ['super-admin', 'student'],
    'critical_permissions' => [
        'super-admin' => ['manage roles', 'manage permissions'],
        'student' => ['enroll courses', 'view enrolled courses'],
    ],
];
```

### Decision 6: Single Permission for Role Management
**Choice**: One permission `manage roles and permissions` (not separate create/edit/delete)

**Rationale**:
- Role management is all-or-nothing operation
- Partial access creates security gaps (e.g., can assign but not revoke)
- Simpler permission model
- Reserved for Super-Admin only

## Architecture

### Data Model

#### Existing (Spatie Package)
```
roles
- id
- name (unique)
- guard_name
- created_at
- updated_at
```

#### Migration: Add Soft Deletes
```php
Schema::table('roles', function (Blueprint $table) {
    $table->softDeletes();
    $table->text('description')->nullable();
});
```

#### New Activity Log Table (Spatie Package)
```
activity_log
- id
- log_name
- description
- subject_type, subject_id (polymorphic)
- causer_type, causer_id (polymorphic)
- properties (JSON)
- created_at
```

### Controller Structure

**RoleController** (`app/Http/Controllers/Admin/RoleController.php`)
- `index()`: List all roles with user count, last modified
- `create()`: Form for new role
- `store()`: Create role + log activity
- `edit($role)`: Form with permission matrix
- `update($role)`: Update role + permissions + log activity
- `destroy($role)`: Soft delete with validation + log activity

**ActivityLogController** (`app/Http/Controllers/Admin/ActivityLogController.php`)
- `index()`: Paginated activity log with filters (causer, date, log_name)

### Route Organization

```php
// routes/web.php (inside admin middleware group)
Route::middleware(['can:manage roles and permissions'])->group(function () {
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
});
```

### View Structure

```
resources/views/admin/roles/
├── index.blade.php        # Role list table
├── create.blade.php       # Role creation form
├── edit.blade.php         # Role edit + permission matrix
└── _permission-matrix.blade.php  # Reusable matrix component

resources/views/admin/activity-log/
└── index.blade.php        # Activity timeline
```

### Permission Matrix Component (Alpine.js)

```blade
<div x-data="{
    selectedPermissions: @js($rolePermissions),
    selectAll(category) { /* logic */ },
    deselectAll(category) { /* logic */ }
}">
    @foreach($permissionGroups as $category => $permissions)
    <div class="mb-6">
        <h3>{{ $category }}</h3>
        <div class="flex items-center gap-2 mb-2">
            <button @click="selectAll('{{ $category }}')" type="button">Select All</button>
            <button @click="deselectAll('{{ $category }}')" type="button">Deselect All</button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($permissions as $permission)
            <label class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="permissions[]"
                    value="{{ $permission->name }}"
                    x-model="selectedPermissions"
                    {{ $isProtectedPermission ? 'disabled' : '' }}
                >
                <span>{{ $permission->name }}</span>
            </label>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
```

### Validation Logic

**RoleController@destroy**:
```php
public function destroy(Role $role)
{
    // Check protected roles
    if (in_array($role->name, config('authorization.protected_roles'))) {
        return back()->with('error', 'Cannot delete protected role.');
    }

    // Check active users
    if ($role->users()->exists()) {
        return back()->with('error', 'Cannot delete role with active users.');
    }

    // Log activity
    activity()
        ->causedBy(auth()->user())
        ->performedOn($role)
        ->withProperties(['role_name' => $role->name])
        ->log('deleted role');

    $role->delete(); // soft delete

    return redirect()->route('roles.index')->with('success', 'Role deleted.');
}
```

**RoleController@update** (Permission Assignment):
```php
public function update(Request $request, Role $role)
{
    $validated = $request->validate([
        'name' => 'required|unique:roles,name,' . $role->id,
        'description' => 'nullable|string',
        'permissions' => 'array',
    ]);

    // Check protected permissions
    $protectedPerms = config("authorization.critical_permissions.{$role->name}", []);
    $newPermissions = $validated['permissions'] ?? [];
    $missingProtected = array_diff($protectedPerms, $newPermissions);

    if (!empty($missingProtected)) {
        return back()->with('error', 'Cannot revoke critical permissions: ' . implode(', ', $missingProtected));
    }

    // Update role
    $role->update(['name' => $validated['name'], 'description' => $validated['description']]);

    // Sync permissions and log changes
    $oldPermissions = $role->permissions->pluck('name')->toArray();
    $role->syncPermissions($newPermissions);
    $newPermissionsActual = $role->fresh()->permissions->pluck('name')->toArray();

    $added = array_diff($newPermissionsActual, $oldPermissions);
    $removed = array_diff($oldPermissions, $newPermissionsActual);

    // Log activity
    activity()
        ->causedBy(auth()->user())
        ->performedOn($role)
        ->withProperties([
            'role_name' => $role->name,
            'permissions_added' => $added,
            'permissions_removed' => $removed,
        ])
        ->log('updated role permissions');

    return redirect()->route('roles.index')->with('success', 'Role updated.');
}
```

## Risks / Trade-offs

### Risk 1: Self-Lockout
**Risk**: Admin removes their own Super-Admin role or critical permissions

**Mitigation**:
- Validate that at least one Super-Admin with full permissions exists
- Prevent users from modifying their own role assignments
- Confirmation modal: "You are about to modify Super-Admin permissions. Confirm?"

### Risk 2: Permission Drift
**Risk**: UI-assigned permissions diverge from seeder definitions

**Mitigation**:
- Seeder runs `syncPermissions()` (idempotent)
- Activity log provides source of truth for manual changes
- Deployment docs: "Seeder will reset permissions to defaults on fresh installs"

### Risk 3: UI Performance
**Risk**: 40+ permissions × 5 roles = large checkbox matrix

**Mitigation**:
- Group permissions by category (8-10 groups)
- Lazy load permission matrix on edit page only (not index)
- No pagination needed (40 permissions fit on one page)

### Risk 4: Audit Log Bloat
**Risk**: Activity log grows unbounded

**Mitigation**:
- Add cleanup command: `php artisan activity-log:clean --days=365`
- Index on `created_at` for efficient queries
- Optional: Archive old logs to separate table

## Migration Plan

### Phase 1: Install Dependencies
1. Install `spatie/laravel-activitylog`: `composer require spatie/laravel-activitylog`
2. Publish config: `php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"`
3. Run migrations: `php artisan migrate`

### Phase 2: Database Changes
1. Create migration: Add `deleted_at`, `description` to `roles` table
2. Create config: `config/authorization.php` with protected roles/permissions
3. Update seeder: Add `manage roles and permissions` permission

### Phase 3: Implement Core CRUD
1. Create `RoleController` with basic CRUD
2. Create views: index, create, edit
3. Add routes with permission middleware

### Phase 4: Permission Matrix
1. Implement permission grouping logic
2. Build Alpine.js matrix component
3. Add bulk select/deselect

### Phase 5: Activity Logging
1. Integrate activity logging in controller actions
2. Create `ActivityLogController` and view
3. Add log filters and pagination

### Phase 6: Protected Role Logic
1. Implement protected role validation
2. Add self-protection checks
3. Confirmation modals for critical actions

### Rollback Plan
If critical issues arise:
1. Remove routes: Comment out role management routes in `web.php`
2. Revert permissions: Run seeder to reset to code-defined state
3. Data preservation: Activity log and role descriptions persist (no data loss)

## Open Questions

1. **Q**: Should we add role cloning (duplicate role with same permissions)?
   **A**: Not in MVP. Can add later if requested.

2. **Q**: Should activity log be real-time (websockets) or paginated?
   **A**: Paginated. Real-time is overkill for audit logs.

3. **Q**: Should we allow role name changes or make them immutable?
   **A**: Allow changes but log in activity. Slug-based role identification avoided.

4. **Q**: Should we add role assignment UI (assign roles to users)?
   **A**: No, that's user management. Keep scoped to role-permission management only.

5. **Q**: Pagination for roles list?
   **A**: Not needed. Max 10-15 roles expected. If it grows beyond 50, add pagination.
