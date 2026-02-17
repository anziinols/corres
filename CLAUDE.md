# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**CORRES** is a Correspondence Management System built with CodeIgniter 4. It's a multi-organization application designed to digitize and streamline the handling of official correspondence with tracking, accountability, and document management.

The system implements a dual-portal architecture:
- **Admin Portal** (`/login`, `/admin/*`) - For correspondence management
- **Dakoii Portal** (`/dakoii/*`) - For system administration (organizations, groups, users)

## Development Commands

### Database Operations
```bash
# Run migrations
php spark migrate

# Rollback last migration batch
php spark migrate:rollback

# Refresh all migrations
php spark migrate:refresh

# Run seeders
php spark db:seed DakoiiUserSeeder

# Alternative: Browser-based migration (for XAMPP environment)
# Navigate to: http://localhost/corres/run_migration.php
```

### Testing
```bash
# Run all tests
composer test
# or
vendor/bin/phpunit
```

### Development Server
The project is configured for XAMPP (Apache + MySQL). The entry point is `public/index.php`.
- Local URL: `http://localhost/corres/`
- Ensure Apache and MySQL are running in XAMPP

### Other Commands
```bash
# List all routes
php spark routes

# Clear cache
php spark cache:clear
```

## Architecture

### Database Configuration
- **Database**: `corres_db` (MySQL)
- **Default credentials**: root user with no password
- **Config**: `app/Config/Database.php`
- Uses MySQLi driver with utf8mb4 charset

### Multi-Tenant Structure
The system supports multiple organizations with hierarchical structure:
```
Organizations (organization_code: unique identifier)
  └── Groups (department/sections with parent-child relationships)
      └── Users (with roles: Admin, Supervisor, Front Desk)
```

### Authentication System
Two separate authentication systems with different filters:

1. **AdminAuthFilter** - For `/admin/*` routes
   - Session keys: `admin_logged_in`, `admin_user_id`, `admin_username`
   - Uses `dakoii_users` table

2. **DakoiiAuthFilter** - For `/dakoii/*` routes
   - Session keys: `dakoii_logged_in`, `dakoii_user_id`, `dakoii_username`, `dakoii_name`
   - Uses `dakoii_users` table

### RESTful Route Patterns
Controllers follow RESTful conventions with nested resources:

**Standard resource routes:**
- `GET /resource` - index (list)
- `GET /resource/new` - new (create form)
- `POST /resource` - create
- `GET /resource/{id}` - show (view)
- `GET /resource/{id}/edit` - edit (edit form)
- `PUT|PATCH /resource/{id}` - update
- `DELETE /resource/{id}` - delete

**Nested resources** (Groups and Users under Organizations):
- `GET /dakoii/organizations/{org_id}/groups` - List groups for organization
- `POST /dakoii/organizations/{org_id}/groups` - Create group under organization
- Similar pattern for users

### File Upload Conventions
- **Storage path**: `public/uploads/{subdirectory}/`
- **Subdirectories**: `signatures/`, `stamps/`, `logos/`, `organizations/`
- **Naming**: Always use `getRandomName()` for uploaded files
- **Database storage**: Store paths with `public/` prefix (e.g., `public/uploads/signatures/abc123.png`)
- **Validation**: Always validate file types and sizes before upload

### Audit Trail Pattern
All major tables include audit fields:
- `created_by` - User ID who created the record
- `updated_by` - User ID who last updated
- `deleted_by` - User ID who deleted (soft delete)
- `deleted_at` - Timestamp for soft delete (NULL = active)

When modifying records, always populate these fields based on session user ID.

### View File Naming Convention
Pattern: `{portal}_{module}_{action}.php`

Examples:
- `dakoii_organizations_list.php`
- `dakoii_organizations_create.php`
- `dakoii_groups_edit.php`
- `admin_correspondences_view.php`

Views are organized in subdirectories: `app/Views/admin/`, `app/Views/dakoii/`, with shared templates in `app/Views/templates/`.

## Key Database Tables

### Phase 1 (Implemented)
- **dakoii_users** - System administrators
- **organizations** - Multi-tenant organizations with unique codes
- **groups** - Hierarchical departments/sections within organizations
- **users** - End users with roles and digital signatures/stamps

### Phase 2 (Planned/Partial)
- **correspondences** - Main correspondence registry with inward/outward/internal types
- **correspondence_types** - Types of correspondence (Letter, Email, Memo, etc.)
- **correspondence_links** - Links between related correspondences
- **referrals** - Referral tracking with action types
- **responses** - Response management
- **follow_ups** - Follow-up tracking
- **numbering_sequences** - Department-based numbering (HRM-001, FIN-002, etc.)

## Important Implementation Details

### CSRF Protection
All forms use CSRF protection. When implementing AJAX forms:
```javascript
// Always refresh CSRF token after each request
fetch(url, {
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
}).then(response => {
    // Update token in both success and error cases
    document.querySelector('meta[name="csrf-token"]').content = newToken;
});
```

### Database Queries
- Use Query Builder for security (avoid raw SQL)
- Always use soft deletes: set `deleted_at` instead of physical deletion
- Use transactions for multi-table operations
- Escape all outputs with `esc()` helper

### Correspondence Number Generation
Correspondences use department-based numbering:
- Format: `{DEPT}-{YEAR}/{NUMBER}` (e.g., `HRM-2025/001`, `FIN-2025/002`)
- Sequence resets yearly per department
- Generated via `Correspondences::generateNumber()` endpoint

### File Path Handling
Recent migration fixed file path prefixes. When dealing with file paths:
- Files uploaded through forms are stored with `public/` prefix
- When displaying files, construct full URL: `base_url($file_path)`
- Migration `2026-01-31-165900_FixFilePathsPrefix.php` normalized all paths to include `public/` prefix

## Development Guidelines

### When adding new features:
1. Create migration for database changes in `app/Database/Migrations/`
2. Follow timestamp naming: `YYYY-MM-DD-HHMMSS_DescriptiveName.php`
3. Create/update Model in `app/Models/` (singular name, e.g., `CorrespondenceModel.php`)
4. Create/update Controller in `app/Controllers/` (plural name, e.g., `Correspondences.php`)
5. Add routes to `app/Config/Routes.php` following RESTful conventions
6. Create views in appropriate subdirectory following naming convention
7. Update filters in `app/Config/Filters.php` if authentication is needed

### Security checklist:
- [ ] Validate all inputs in controller
- [ ] Use prepared statements (Query Builder)
- [ ] Escape outputs with `esc()` in views
- [ ] Apply appropriate authentication filter to routes
- [ ] Enable CSRF protection on forms
- [ ] Hash passwords with `password_hash()`
- [ ] Validate file uploads (type, size, extension)
- [ ] Use `getRandomName()` for uploaded files

## Documentation References

Comprehensive documentation is available in `dev_guide/`:
- **QUICK_REFERENCE.md** - Quick reference for common tasks
- **SYSTEM_DESIGN_OVERALL.md** - Complete system architecture
- **DATABASE_SCHEMA_ENHANCED.md** - Visual database schema
- **GAP_ANALYSIS_SUMMARY.md** - Excel field mapping to database

## Default Credentials

After running migrations and seeds:
- **Username**: `fkenny`
- **Password**: `dakoii`

Password can be regenerated using `generate_password.php` in the project root.
