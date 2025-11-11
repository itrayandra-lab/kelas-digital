# Specification: Authorization Management

## ADDED Requirements

### Requirement: Role CRUD Operations
The system SHALL provide an administrative interface for managing roles with create, read, update, and soft delete operations.

#### Scenario: Super-Admin lists all roles
- **WHEN** a Super-Admin navigates to `/admin/roles`
- **THEN** the system displays a table with all roles (including soft-deleted if filter applied)
- **AND** each row shows role name, description, active user count, last modified timestamp, and action buttons

#### Scenario: Super-Admin creates new role
- **WHEN** a Super-Admin submits the role creation form with valid name and description
- **THEN** the system creates a new role with no permissions assigned
- **AND** logs the activity with causer and role details
- **AND** redirects to edit page for permission assignment

#### Scenario: Super-Admin updates role details
- **WHEN** a Super-Admin updates role name or description
- **THEN** the system saves changes and logs the activity
- **AND** validates uniqueness of role name (case-insensitive)

#### Scenario: Super-Admin soft deletes role without users
- **WHEN** a Super-Admin attempts to delete a role with no active users
- **AND** the role is not in the protected roles list
- **THEN** the system soft deletes the role and logs the activity

#### Scenario: Super-Admin cannot delete role with active users
- **WHEN** a Super-Admin attempts to delete a role with active users assigned
- **THEN** the system rejects the request with error message "Cannot delete role with active users"
- **AND** displays count of affected users

#### Scenario: Super-Admin cannot delete protected role
- **WHEN** a Super-Admin attempts to delete a protected role (super-admin, student)
- **THEN** the system rejects the request with error message "Cannot delete protected system role"

### Requirement: Permission Assignment Matrix
The system SHALL provide a grouped checkbox matrix for bulk permission assignment to roles.

#### Scenario: Super-Admin views permission matrix
- **WHEN** a Super-Admin opens role edit page
- **THEN** the system displays permissions grouped by category (Course Management, Article Management, etc.)
- **AND** pre-checks all permissions currently assigned to the role
- **AND** disables checkboxes for critical permissions of protected roles

#### Scenario: Super-Admin assigns permissions to role
- **WHEN** a Super-Admin selects multiple permissions and saves
- **THEN** the system syncs permissions to the role (adds new, removes unchecked)
- **AND** logs activity with detailed changes (permissions added/removed)

#### Scenario: Super-Admin uses bulk select in category
- **WHEN** a Super-Admin clicks "Select All" in a permission category
- **THEN** the system checks all permission checkboxes in that category
- **AND** "Deselect All" unchecks all in that category

#### Scenario: Critical permissions cannot be revoked from protected roles
- **WHEN** a Super-Admin attempts to revoke "manage roles and permissions" from super-admin role
- **THEN** the system rejects the update with error message listing critical permissions
- **AND** does not save any permission changes

### Requirement: Activity Audit Trail
The system SHALL log all role and permission changes with full context using Spatie Laravel Activity Log.

#### Scenario: Role creation logged
- **WHEN** a new role is created
- **THEN** the system creates activity log entry with description "created role"
- **AND** includes properties: role_id, role_name
- **AND** records causer (authenticated user) and timestamp

#### Scenario: Permission change logged
- **WHEN** role permissions are updated
- **THEN** the system creates activity log entry with description "updated role permissions"
- **AND** includes properties: role_id, role_name, permissions_added[], permissions_removed[]
- **AND** records causer and timestamp

#### Scenario: Role deletion logged
- **WHEN** a role is soft deleted
- **THEN** the system creates activity log entry with description "deleted role"
- **AND** includes properties: role_id, role_name
- **AND** records causer and timestamp

#### Scenario: Super-Admin views activity log
- **WHEN** a Super-Admin navigates to `/admin/activity-log`
- **THEN** the system displays paginated timeline of all role/permission activities
- **AND** shows causer name, action description, timestamp, and expandable properties

#### Scenario: Activity log filtered by causer
- **WHEN** a Super-Admin filters activity log by specific user
- **THEN** the system displays only activities caused by that user
- **AND** maintains pagination and sort order (newest first)

### Requirement: Protected Role System
The system SHALL enforce protection rules for critical system roles to prevent security incidents.

#### Scenario: Protected roles list defined in config
- **WHEN** the system initializes
- **THEN** protected roles are loaded from `config/authorization.php`
- **AND** default protected roles are: super-admin, student

#### Scenario: Critical permissions enforced for super-admin
- **WHEN** updating super-admin role permissions
- **THEN** the system validates that "manage roles" and "manage permissions" remain assigned
- **AND** rejects updates that remove these permissions

#### Scenario: Critical permissions enforced for student
- **WHEN** updating student role permissions
- **THEN** the system validates that "enroll courses" remains assigned
- **AND** rejects updates that remove this permission

#### Scenario: Protected role metadata editable
- **WHEN** a Super-Admin updates name or description of protected role
- **THEN** the system allows the change
- **AND** logs the activity normally

### Requirement: Role Soft Delete Management
The system SHALL support soft deletion with restoration capability and validation.

#### Scenario: Soft deleted role hidden from default list
- **WHEN** a role is soft deleted
- **THEN** it does not appear in the default roles index
- **AND** role users are not affected (relationships preserved)

#### Scenario: Soft deleted role viewable with filter
- **WHEN** a Super-Admin enables "Show Deleted" filter
- **THEN** the system includes soft-deleted roles in the table
- **AND** marks them visually distinct (e.g., strike-through, badge)

#### Scenario: Soft deleted role restored
- **WHEN** a Super-Admin clicks "Restore" on soft-deleted role
- **THEN** the system restores the role (clears deleted_at)
- **AND** logs activity "restored role"
- **AND** role becomes available for assignment again

#### Scenario: Validation prevents deletion with active users
- **WHEN** attempting to delete role with users()->exists() === true
- **THEN** the system blocks deletion with error "Cannot delete role with {count} active users"
- **AND** provides link to view users with this role

### Requirement: Permission Registry Immutability
The system SHALL maintain permissions as code-defined entities that cannot be created or deleted via UI.

#### Scenario: Permissions loaded from database
- **WHEN** the permission matrix loads
- **THEN** the system retrieves all permissions from the permissions table
- **AND** groups them by category based on naming convention

#### Scenario: No permission creation UI
- **WHEN** a Super-Admin accesses role management
- **THEN** no interface exists for creating new permissions
- **AND** documentation directs to seeder modification for new permissions

#### Scenario: Permission categories inferred from names
- **WHEN** displaying permission matrix
- **THEN** the system groups permissions by parsing the suffix:
  - `* courses` → Course Management
  - `* articles` → Article Management
  - `* users` → User Management
  - `* enrollments` → Enrollment Management
  - `* tags` → Tag Management
  - `* categories` → Category Management
  - `manage *` → System Management
  - `enroll courses`, `access *` → Student Features
- **AND** displays ungrouped permissions in "Other" category
