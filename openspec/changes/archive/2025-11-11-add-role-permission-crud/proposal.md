# Change: Add Role and Permission CRUD Management

## Why

Currently, roles and permissions are defined exclusively through the `RolePermissionSeeder` and require database migrations to add, modify, or remove. While this provides strong security guarantees, it creates operational friction:

1. **Deployment Required**: Every permission change requires code deployment
2. **Limited Flexibility**: Super-Admins cannot adjust role permissions in response to evolving organizational needs
3. **No Audit Trail**: Permission changes lack visibility into who made changes and when
4. **Difficult Testing**: Testing permission scenarios requires seeder modifications

This change introduces a conservative administrative interface for managing roles and permissions while maintaining strict security controls.

## What Changes

### New Capabilities
- **Role Management UI**: CRUD interface for roles (create, read, update, soft delete)
- **Permission Assignment**: Matrix-based permission assignment to roles
- **Activity Logging**: Complete audit trail using Spatie Laravel Activity Log
- **Protected Roles**: System roles (`super-admin`, `student`) cannot be deleted or have critical permissions removed
- **Permission Registry**: Permissions remain code-defined but can be assigned/revoked via UI

### Security Guardrails
- **Super-Admin Only**: Access restricted to `manage roles and permissions` permission
- **Soft Deletes**: Roles can be soft-deleted with validation (no active users)
- **Self-Protection**: Users cannot revoke their own Super-Admin permissions
- **Confirmation Gates**: Critical actions require explicit confirmation
- **No Dynamic Permissions**: Permission creation stays in code (seeder/migration only)

### Technical Additions
- New permission: `manage roles and permissions` (Super-Admin only)
- Role soft deletes with `deleted_at` column
- Activity log integration for all role/permission changes
- Protected role validation middleware
- Permission matrix UI component (Alpine.js + Tailwind)

## Impact

### Affected Specs
- **NEW**: `authorization-management` - Role and permission CRUD interface

### Affected Code
- **Database**:
  - Migration: Add `deleted_at` to `roles` table
  - Seeder: Add `manage roles and permissions` permission
- **Models**:
  - Add soft delete trait to Role model (Spatie package model)
- **Routes** (`routes/web.php`):
  - New admin routes: `/admin/roles`, `/admin/permissions`
  - Activity log viewer: `/admin/activity-log`
- **Controllers**:
  - New: `app/Http/Controllers/Admin/RoleController.php`
  - New: `app/Http/Controllers/Admin/ActivityLogController.php`
- **Views**:
  - New: `resources/views/admin/roles/` (index, create, edit)
  - New: `resources/views/admin/activity-log/index.blade.php`
  - Component: Permission matrix with grouped checkboxes
- **Middleware**:
  - Enhanced protected role validation logic
- **Packages**:
  - Install: `spatie/laravel-activitylog` (not yet in project)

### Breaking Changes
None. This is purely additive functionality.

### Dependencies
- Requires Spatie Laravel Activity Log installation
- Builds on existing Spatie Laravel Permission package
