# Implementation Tasks: Role and Permission CRUD Management

## 1. Dependencies and Configuration

- [ ] 1.1 Install Spatie Laravel Activity Log package
  - Run: `composer require spatie/laravel-activitylog`
  - Verify installation in `composer.json`

- [ ] 1.2 Publish Activity Log configuration and migration
  - Run: `php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"`
  - Run: `php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"`
  - Review `config/activitylog.php` for default settings

- [ ] 1.3 Create authorization configuration file
  - Create: `config/authorization.php`
  - Define protected roles: `['super-admin', 'student']`
  - Define critical permissions map for each protected role
  - Add phpDoc comments explaining protection rules

## 2. Database Schema Changes

- [ ] 2.1 Create migration for roles table modifications
  - Create: `database/migrations/YYYY_MM_DD_HHMMSS_add_soft_deletes_and_description_to_roles_table.php`
  - Add `deleted_at` timestamp column (nullable)
  - Add `description` text column (nullable)
  - Add index on `deleted_at` for query performance

- [ ] 2.2 Run activity log migrations
  - Run: `php artisan migrate`
  - Verify `activity_log` table created with correct schema

- [ ] 2.3 Update RolePermissionSeeder
  - Add new permission: `'manage roles and permissions'`
  - Assign to Super-Admin role only in seeder
  - Test seeder runs without errors: `php artisan db:seed --class=RolePermissionSeeder`

## 3. Model and Service Layer

- [ ] 3.1 Extend Role model with soft deletes
  - Note: Cannot directly modify Spatie's Role model
  - Alternative: Create `app/Models/Role.php` extending `Spatie\Permission\Models\Role`
  - Add `use SoftDeletes` trait
  - Add `protected $fillable = ['name', 'guard_name', 'description']`
  - Register custom model in `config/permission.php` → `models.role`

- [ ] 3.2 Create RoleService helper (optional)
  - Create: `app/Services/RoleService.php`
  - Method: `getPermissionGroups()` - returns permissions grouped by category
  - Method: `isProtectedRole(Role $role)` - checks if role is protected
  - Method: `getCriticalPermissions(Role $role)` - returns critical permissions for role
  - Method: `validatePermissionUpdate(Role $role, array $newPermissions)` - validates protected permissions

## 4. Controllers

- [ ] 4.1 Create RoleController
  - Create: `app/Http/Controllers/Admin/RoleController.php`
  - Implement `index()` - role list with user counts, soft delete filter
  - Implement `create()` - role creation form
  - Implement `store()` - create role + activity log
  - Implement `edit()` - role edit form with permission matrix
  - Implement `update()` - update role + permissions + activity log with validation
  - Implement `destroy()` - soft delete with validation + activity log
  - Add authorization checks: `$this->authorize('manage roles and permissions')`

- [ ] 4.2 Create ActivityLogController
  - Create: `app/Http/Controllers/Admin/ActivityLogController.php`
  - Implement `index()` - paginated activity log (20 per page)
  - Add filters: causer_id, date range, log_name
  - Query only logs related to roles/permissions: `->where('log_name', 'authorization')`

## 5. Routes

- [ ] 5.1 Add role management routes
  - Location: `routes/web.php` (inside admin middleware group)
  - Add middleware: `can:manage roles and permissions`
  - Add resource routes: `Route::resource('roles', RoleController::class)->except(['show'])`
  - Add custom routes if needed (e.g., restore soft-deleted role)

- [ ] 5.2 Add activity log route
  - Add: `Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index')`
  - Apply same middleware: `can:manage roles and permissions`

## 6. Views - Role Management

- [ ] 6.1 Create roles index view
  - Create: `resources/views/admin/roles/index.blade.php`
  - Table columns: Name, Description, Users Count, Last Modified, Actions
  - Actions: Edit, Delete (with confirmation modal)
  - Add "Show Deleted" toggle filter
  - Add "Create Role" button
  - Use Tailwind CSS consistent with existing admin styles

- [ ] 6.2 Create role creation form
  - Create: `resources/views/admin/roles/create.blade.php`
  - Fields: Name (required), Description (optional)
  - Form validation messages
  - Cancel and Save buttons

- [ ] 6.3 Create role edit form with permission matrix
  - Create: `resources/views/admin/roles/edit.blade.php`
  - Tabs: "Details" and "Permissions"
  - Details tab: Name, Description fields
  - Permissions tab: Include permission matrix component
  - Display warning if editing protected role

- [ ] 6.4 Create permission matrix component
  - Create: `resources/views/admin/roles/_permission-matrix.blade.php`
  - Alpine.js data: `selectedPermissions` array
  - Group permissions by category using RoleService
  - Each category: Category header + "Select All" / "Deselect All" buttons
  - Checkboxes: `name="permissions[]"` with permission name as value
  - Disable checkboxes for critical permissions of protected roles
  - Responsive grid: 2 columns mobile, 3 columns tablet+

## 7. Views - Activity Log

- [ ] 7.1 Create activity log index view
  - Create: `resources/views/admin/activity-log/index.blade.php`
  - Timeline layout with cards per activity
  - Display: Causer name/avatar, description, timestamp (human-readable)
  - Expandable details: JSON properties in formatted table
  - Pagination: 20 per page
  - Filters: Causer dropdown, date range picker

## 8. Frontend Interactivity (Alpine.js)

- [ ] 8.1 Implement permission matrix interactivity
  - Alpine component: Select/Deselect All by category
  - Track selected permissions in `selectedPermissions` array
  - Sync checkboxes with x-model
  - Disable logic for protected permissions

- [ ] 8.2 Implement confirmation modals
  - Alpine component for delete confirmation
  - Show affected user count if applicable
  - "Cancel" and "Confirm Delete" buttons
  - Use existing modal patterns from admin panel

## 9. Validation and Business Logic

- [ ] 9.1 Implement protected role validation
  - RoleController: Check `config('authorization.protected_roles')` before delete
  - Return error flash message if protected

- [ ] 9.2 Implement active users validation
  - RoleController@destroy: Query `$role->users()->exists()`
  - If true, return error with user count
  - Provide link to user management filtered by role

- [ ] 9.3 Implement critical permissions validation
  - RoleController@update: Load critical permissions from config
  - Compare with incoming `$request->permissions`
  - If missing critical permissions, return error listing them
  - Do not save any changes if validation fails

- [ ] 9.4 Form request validation classes
  - Create: `app/Http/Requests/StoreRoleRequest.php`
  - Rules: name (required, unique, max:255), description (nullable, string)
  - Create: `app/Http/Requests/UpdateRoleRequest.php`
  - Rules: name (required, unique except self), description (nullable), permissions (array)

## 10. Activity Logging Integration

- [ ] 10.1 Log role creation
  - RoleController@store: After role creation
  - Code: `activity()->causedBy(auth()->user())->performedOn($role)->withProperties(['role_name' => $role->name])->log('created role')`
  - Set log_name: 'authorization'

- [ ] 10.2 Log role updates
  - RoleController@update: After role name/description update
  - Properties: old and new values for changed fields
  - Log name: 'authorization'

- [ ] 10.3 Log permission changes
  - RoleController@update: After syncPermissions
  - Calculate added = array_diff($new, $old)
  - Calculate removed = array_diff($old, $new)
  - Properties: permissions_added[], permissions_removed[]
  - Log description: 'updated role permissions'

- [ ] 10.4 Log role deletion
  - RoleController@destroy: Before soft delete
  - Properties: role_name
  - Log description: 'deleted role'

## 11. Testing

- [ ] 11.1 Write feature tests for RoleController
  - Test: `test/Feature/Admin/RoleControllerTest.php`
  - Test unauthorized access (non-Super-Admin)
  - Test role creation
  - Test role update with permission sync
  - Test protected role deletion blocked
  - Test role with users deletion blocked
  - Test activity log entries created

- [ ] 11.2 Write unit tests for RoleService
  - Test: `tests/Unit/Services/RoleServiceTest.php`
  - Test permission grouping logic
  - Test protected role detection
  - Test critical permissions validation

- [ ] 11.3 Write browser tests (optional)
  - Test permission matrix interactions
  - Test confirmation modals
  - Test filters on activity log

## 12. Documentation

- [ ] 12.1 Update CLAUDE.md
  - Add section: "Role and Permission Management"
  - Document protected roles and critical permissions
  - Document activity log location and purpose

- [ ] 12.2 Add inline code comments
  - Document protection logic in controllers
  - Explain permission grouping algorithm
  - Add phpDoc blocks for all public methods

- [ ] 12.3 Create user guide (optional)
  - Markdown document explaining how to use role management
  - Screenshots or text-based UI mockups
  - Best practices for permission assignment

## 13. Deployment and Verification

- [ ] 13.1 Run migrations on staging
  - `php artisan migrate`
  - Verify roles table has deleted_at and description columns
  - Verify activity_log table exists

- [ ] 13.2 Seed permissions on staging
  - `php artisan db:seed --class=RolePermissionSeeder`
  - Verify "manage roles and permissions" exists
  - Verify Super-Admin role has the permission

- [ ] 13.3 Manual testing checklist
  - Login as Super-Admin
  - Create new role → verify activity logged
  - Assign permissions → verify activity logged
  - Attempt to delete super-admin role → verify blocked
  - Delete role with no users → verify soft deleted
  - View activity log → verify all actions visible
  - Test permission matrix: select/deselect all

- [ ] 13.4 Performance testing
  - Load roles index with 20+ roles → verify fast load
  - Load permission matrix with 40+ permissions → verify responsive
  - Query activity log with 1000+ entries → verify pagination works

## 14. Post-Launch Monitoring

- [ ] 14.1 Monitor activity log growth
  - Check database size of activity_log table after 1 week
  - Plan cleanup strategy if growing rapidly (>10k entries/month)

- [ ] 14.2 Collect user feedback
  - Super-Admins: Is permission matrix intuitive?
  - Any requests for bulk operations or shortcuts?

- [ ] 14.3 Performance optimization (if needed)
  - Add indexes to activity_log if queries slow
  - Cache permission groups if regenerating frequently
  - Add Redis caching for role-permission lookups

---

## Dependencies Between Tasks

- **Blocking**: 1.1 → 1.2 (package must be installed before publishing)
- **Blocking**: 2.1, 2.2 → 2.3 (migrations must run before seeder)
- **Blocking**: 3.1 → 4.1 (model must exist before controller)
- **Blocking**: 4.1 → 5.1 (controller must exist before routes)
- **Blocking**: 5.1 → 6.1-6.4 (routes needed for view testing)
- **Parallel**: 6.1-6.4, 7.1 (views can be built concurrently)
- **Parallel**: 8.1, 8.2 (Alpine components independent)
- **Blocking**: 4.1 → 10.1-10.4 (logging code in controller)
- **Blocking**: All implementation tasks → 11.1-11.3 (tests written after code)
- **Blocking**: 11.1-11.3 → 13.1 (tests pass before deployment)

## Estimated Effort

- **Phase 1-2**: Dependencies & Database (1-2 hours)
- **Phase 3-5**: Models, Controllers, Routes (3-4 hours)
- **Phase 6-8**: Views & Frontend (4-5 hours)
- **Phase 9-10**: Validation & Logging (2-3 hours)
- **Phase 11**: Testing (3-4 hours)
- **Phase 12-14**: Docs & Deployment (2 hours)

**Total**: ~15-20 hours for complete implementation
