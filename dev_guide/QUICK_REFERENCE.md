# Correspondence Management System - Quick Reference Guide

## 📚 Documentation Index

1. **SYSTEM_DESIGN_OVERALL.md** - Complete system design with gap analysis integration
2. **GAP_ANALYSIS_SUMMARY.md** - Excel vs Database field mapping and gap resolution
3. **DATABASE_SCHEMA_ENHANCED.md** - Visual database schema with enhancements
4. **QUICK_REFERENCE.md** - This file (quick reference)

---

## 🎯 Project Status at a Glance

### ✅ Phase 1: COMPLETE
- Multi-organization support
- Hierarchical group management
- User management with roles
- Dual authentication system (Admin + Dakoii)
- File upload (signatures, stamps, logos)
- Audit trail (created_by, updated_by, deleted_by)
- RESTful architecture

### ⏳ Phase 2: PLANNED (Enhanced with Gap Analysis)
- Correspondence registration (Inward/Outward/Internal)
- Department-based numbering (HRM-001, FIN-002, etc.)
- Referral system with action types
- Response management with action tracking
- Follow-up management system
- Filing & archive tracking
- Digital stamps/signatures
- Notifications & reminders

---

## 📊 Excel Integration Status

### All 16 Excel Fields Mapped ✓

| Excel Field | Database Field | Status |
|-------------|----------------|--------|
| REF NO | correspondence_number | ✓ Enhanced |
| CORRESPONDENCE TYPE | correspondence_direction | ✓ NEW |
| DATE RECEIVED | date_received | ✓ |
| ORIGINAL DATE | original_date | ✓ NEW |
| SUBJECT | subject | ✓ |
| TYPE | correspondence_type | ✓ Enhanced |
| SENDER NAME | sender_name | ✓ |
| SENDER ADDRESS | sender_organization/address | ✓ |
| REFER TO | referred_to | ✓ |
| REFER DATE | referral_date | ✓ |
| RESPONSIBLE OFFICER | referred_to | ✓ |
| STATUS | status | ✓ |
| ACTION REQUIRED | action_required | ✓ NEW |
| DATE ACTIONED | completed_date | ✓ |
| FOLLOW-UP DATE | follow_up_date | ✓ NEW |
| FILED/ARCHIVED | filing_reference | ✓ NEW |

---

## 🗄️ Database Tables

### Current (Phase 1) - Implemented ✓
1. **organizations** - Organization management
2. **groups** - Hierarchical groups/departments
3. **users** - User accounts with roles
4. **dakoii_users** - System administrators

### Planned (Phase 2) - Enhanced with Gap Analysis
5. **correspondences** - Main correspondence registry (ENHANCED)
6. **files** - Document storage
7. **referrals** - Referral tracking (ENHANCED)
8. **responses** - Response management (ENHANCED)
9. **follow_ups** - Follow-up tracking (NEW)
10. **numbering_sequences** - Number generation (ENHANCED)
11. **notifications** - Notification system (ENHANCED)
12. **audit_trail** - Comprehensive audit log

---

## 🔑 Key Enhancements from Gap Analysis

### 1. Bidirectional Correspondence ✓
- **Field**: `correspondence_direction`
- **Values**: INWARD, OUTWARD, INTERNAL
- **Impact**: Support for both incoming and outgoing mail

### 2. Original Date Tracking ✓
- **Field**: `original_date`
- **Purpose**: Track date on document (separate from received date)
- **Impact**: Accurate document dating

### 3. Action Type Management ✓
- **Field**: `action_required` (in referrals)
- **Values**: APPROVAL, REVIEW, REPLY, RECORD, INFORMATION, INVESTIGATION, FILING, OTHER
- **Impact**: Clear action categorization

### 4. Follow-up System ✓
- **Table**: `follow_ups` (NEW)
- **Fields**: follow_up_date, status, assigned_to, completed_date
- **Impact**: Systematic follow-up tracking

### 5. Filing References ✓
- **Fields**: `filing_reference`, `archive_location`, `archive_date`
- **Purpose**: Track physical file locations
- **Impact**: Bridge digital and physical filing

### 6. Department Numbering ✓
- **Field**: `department` (in correspondences)
- **Enhancement**: `numbering_sequences` supports dept prefixes
- **Format**: HRM-2025/001, FIN-2025/002
- **Impact**: Department-based reference numbers

### 7. Expanded Communication Types ✓
- **Enhanced**: `correspondence_type`
- **New Types**: PHONE, CIRCULAR, WHATSAPP, SMS, MEETING_MINUTES
- **Impact**: Cover all communication channels

### 8. Outward Correspondence ✓
- **Fields**: recipient_name, recipient_organization, recipient_address, date_sent, dispatch_method
- **Impact**: Full support for outgoing mail

### 9. Enhanced Action Tracking ✓
- **Field**: `action_category` (in responses)
- **Values**: REPLIED, APPROVED, REJECTED, REVIEWED, RECORDED, FORWARDED, FILED, NO_ACTION_REQUIRED, DEFERRED
- **Impact**: Detailed action documentation

---

## 🏗️ Technology Stack

- **Framework**: CodeIgniter 4
- **Database**: MySQL with InnoDB engine
- **Frontend**: Bootstrap 5, JavaScript, AJAX
- **Authentication**: Session-based with custom filters
- **File Storage**: Local filesystem (public/uploads/)
- **Server**: XAMPP (Apache + MySQL + PHP)
- **Version Control**: Git

---

## 📁 Directory Structure

```
corres/
├── app/
│   ├── Config/
│   │   ├── Routes.php
│   │   ├── Filters.php
│   │   └── Database.php
│   ├── Controllers/
│   │   ├── Admin.php
│   │   ├── Dakoii.php
│   │   ├── Organizations.php
│   │   ├── Groups.php
│   │   └── Users.php
│   ├── Models/
│   │   ├── OrganizationModel.php
│   │   ├── GroupModel.php
│   │   ├── UserModel.php
│   │   └── DakoiiUserModel.php
│   ├── Filters/
│   │   ├── AdminAuthFilter.php
│   │   └── DakoiiAuthFilter.php
│   ├── Database/
│   │   ├── Migrations/
│   │   └── Seeds/
│   └── Views/
│       ├── admin/
│       ├── dakoii/
│       └── templates/
├── public/
│   └── uploads/
│       ├── signatures/
│       ├── stamps/
│       └── logos/
└── dev_guide/
    ├── SYSTEM_DESIGN_OVERALL.md
    ├── GAP_ANALYSIS_SUMMARY.md
    ├── DATABASE_SCHEMA_ENHANCED.md
    └── QUICK_REFERENCE.md
```

---

## 🔐 Authentication

### Admin Portal
- **URL**: `/login`, `/admin/*`
- **Filter**: AdminAuthFilter
- **Session**: admin_logged_in, admin_user_id, admin_username
- **Table**: dakoii_users

### Dakoii Portal
- **URL**: `/dakoii/`, `/dakoii/*`
- **Filter**: DakoiiAuthFilter
- **Session**: dakoii_logged_in, dakoii_user_id, dakoii_username, dakoii_name
- **Table**: dakoii_users

### User Portal (Planned)
- **URL**: `/user/*`
- **Filter**: UserAuthFilter (to be created)
- **Session**: user_logged_in, user_id, user_email
- **Table**: users

---

## 🎨 Design Patterns Used

1. **MVC Pattern** - Model-View-Controller separation
2. **Repository Pattern** - Models handle data access
3. **RESTful Design** - Standard HTTP methods
4. **Soft Delete Pattern** - Data preservation
5. **Audit Trail Pattern** - Track changes
6. **Template Pattern** - Consistent views
7. **Filter Pattern** - Authentication & CSRF
8. **Nested Resources** - Hierarchical URLs

---

## 🚀 RESTful Routes

### Organizations
```
GET    /dakoii/organizations              - List
GET    /dakoii/organizations/new          - Create form
POST   /dakoii/organizations              - Create
GET    /dakoii/organizations/{id}         - View
GET    /dakoii/organizations/{id}/edit    - Edit form
PUT    /dakoii/organizations/{id}         - Update
DELETE /dakoii/organizations/{id}         - Delete
```

### Groups (Nested)
```
GET    /dakoii/organizations/{org_id}/groups              - List
POST   /dakoii/organizations/{org_id}/groups              - Create
GET    /dakoii/organizations/{org_id}/groups/{id}         - View
PUT    /dakoii/organizations/{org_id}/groups/{id}         - Update
DELETE /dakoii/organizations/{org_id}/groups/{id}         - Delete
```

### Users (Nested)
```
GET    /dakoii/organizations/{org_id}/users              - List
POST   /dakoii/organizations/{org_id}/users              - Create
GET    /dakoii/organizations/{org_id}/users/{id}         - View
PUT    /dakoii/organizations/{org_id}/users/{id}         - Update
DELETE /dakoii/organizations/{org_id}/users/{id}         - Delete
```

---

## 📝 Naming Conventions

### View Files
- **Pattern**: `{folder_name}_{action}.php`
- **Examples**:
  - `dakoii_organizations_list.php`
  - `dakoii_organizations_create.php`
  - `dakoii_groups_edit.php`
  - `dakoii_users_view.php`

### Database Tables
- **Lowercase with underscores**: organizations, groups, users
- **Plural names**: correspondences, referrals, responses

### Model Classes
- **Singular PascalCase**: OrganizationModel, GroupModel, UserModel

### Controller Classes
- **Plural PascalCase**: Organizations, Groups, Users

---

## 🔧 Development Guidelines

### File Uploads
- Always use `getRandomName()` for uploaded files
- Store file paths with `public/` prefix in database
- Validate file types and sizes
- Store in appropriate subdirectories

### AJAX Forms
- Always refresh CSRF token after each request
- Handle both success and error responses
- Update token in both success and error callbacks

### Database Operations
- Use Query Builder for security
- Always use soft deletes (deleted_at)
- Track audit fields (created_by, updated_by, deleted_by)
- Use transactions for multi-table operations

### Security
- Validate all inputs
- Escape all outputs with `esc()`
- Use CSRF protection on all forms
- Hash passwords with `password_hash()`
- Check authentication in filters

---

## 📈 Implementation Roadmap

### Phase 2A - Critical (Weeks 1-4)
- [ ] Correspondence registration (Inward/Outward)
- [ ] Department-based numbering
- [ ] Basic referral system
- [ ] Action type categorization
- [ ] Filing reference tracking

### Phase 2B - Important (Weeks 5-8)
- [ ] Follow-up management
- [ ] Enhanced response tracking
- [ ] Notifications system
- [ ] Basic reports

### Phase 2C - Advanced (Weeks 9-12)
- [ ] Digital stamps/signatures
- [ ] Advanced reports
- [ ] Archive automation
- [ ] Mobile integration

---

## 🆘 Quick Commands

### Database Migration
```bash
php spark migrate
php spark migrate:rollback
php spark migrate:refresh
```

### Seeding
```bash
php spark db:seed DakoiiUserSeeder
```

### Clear Cache
```bash
php spark cache:clear
```

### Routes List
```bash
php spark routes
```

---

## 📞 Support & Documentation

- **System Design**: See SYSTEM_DESIGN_OVERALL.md
- **Gap Analysis**: See GAP_ANALYSIS_SUMMARY.md
- **Database Schema**: See DATABASE_SCHEMA_ENHANCED.md
- **CodeIgniter 4 Docs**: https://codeigniter.com/user_guide/

---

**Last Updated**: 2025-01-04  
**Version**: 2.0 (Enhanced with Gap Analysis)  
**Status**: Phase 1 Complete, Phase 2 Planned

