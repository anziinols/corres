# Correspondence Management System - System Design Document

## Table of Contents
1. [System Overview](#system-overview)
2. [Architecture Design](#architecture-design)
3. [Database Schema](#database-schema)
4. [Workflow Design](#workflow-design)
5. [API Design](#api-design)
6. [Implementation Guidelines](#implementation-guidelines)
7. [Security Considerations](#security-considerations)
8. [Current Implementation Status](#current-implementation-status)

---

## 1. System Overview

### Purpose
The Correspondence Management System (CMS) is designed to digitize and streamline the handling of official correspondence within an organization, providing tracking, accountability, and efficient document management.

### Key Features (Planned)
- **Document Registration**: Unique numbering and initial registration by front desk
- **Multi-level Referral System**: Chain of referrals with tracking
- **Response Management**: Comments/remarks and file attachments
- **Linked Documents**: Response documents linked to original correspondence
- **Audit Trail**: Complete history of all actions
- **Digital Signatures/Stamps**: Position-tracked stamps and signatures

### Current Implementation (Phase 1)
- **Multi-Organization Support**: System supports multiple organizations with unique codes
- **Hierarchical Group Management**: Groups/departments within organizations with parent-child relationships
- **User Management**: Users with roles (Admin, Supervisor, Front Desk) and digital signatures/stamps
- **Authentication System**: Dual authentication (Admin portal and Dakoii portal)
- **Audit Trail**: Created by, updated by, deleted by tracking with soft deletes
- **File Upload Support**: Signature and stamp file uploads for users

### System Architecture
```
┌─────────────────┐     ┌──────────────────┐     ┌───────────────┐
│   Front Desk    │────▶│  Web Application │────▶│   Database    │
│   Registration  │     │  (CodeIgniter 4) │     │    (MySQL)    │
└─────────────────┘     └──────────────────┘     └───────────────┘
                               │
                               ▼
                    ┌──────────────────────┐
                    │   File Storage       │
                    │  (public/uploads/)   │
                    └──────────────────────┘
```

---

## 2. Architecture Design

### Component Architecture (Current Implementation)

```
┌────────────────────────────────────────────────────────┐
│                    Presentation Layer                   │
│                 (Bootstrap + JavaScript)                │
│                      (AJAX Forms)                       │
├────────────────────────────────────────────────────────┤
│                   Application Layer                     │
│                   (CodeIgniter 4 MVC)                  │
│  ┌─────────────┬──────────────┬───────────────────┐   │
│  │ Controllers │    Models     │     Filters       │   │
│  │             │               │                   │   │
│  │ • Admin     │ • User        │ • AdminAuth      │   │
│  │ • Dakoii    │ • DakoiiUser  │ • DakoiiAuth     │   │
│  │ • Organiz.  │ • Organiz.    │ • CSRF           │   │
│  │ • Groups    │ • Group       │                   │   │
│  │ • Users     │               │                   │   │
│  └─────────────┴──────────────┴───────────────────┘   │
├────────────────────────────────────────────────────────┤
│                      Data Layer                         │
│                    (MySQL Database)                     │
│         • organizations  • groups  • users              │
│         • dakoii_users   • migrations                   │
└────────────────────────────────────────────────────────┘
```

### Authentication Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Two-Portal System                     │
├──────────────────────────┬──────────────────────────────┤
│      Admin Portal        │      Dakoii Portal           │
│   /login, /admin/*       │   /dakoii/*, /dakoii/login   │
├──────────────────────────┼──────────────────────────────┤
│   AdminAuthFilter        │   DakoiiAuthFilter           │
│   Session: admin_*       │   Session: dakoii_*          │
└──────────────────────────┴──────────────────────────────┘
```

### Workflow State Machine (Planned for Correspondence)

```
         ┌──────────────┐
         │  REGISTERED  │
         └──────┬───────┘
                │
         ┌──────▼───────┐
         │   REFERRED   │◄────────┐
         └──────┬───────┘         │
                │                 │
         ┌──────▼───────┐         │
         │  IN_PROCESS  │         │
         └──────┬───────┘         │
                │                 │
         ┌──────▼───────┐         │
         │   ACTIONED   │─────────┘
         └──────┬───────┘  (Can Refer)
                │
         ┌──────▼───────┐
         │   COMPLETED  │
         └──────────────┘
```

---

## 2A. Gap Analysis & Excel Integration

### Excel Tracking System Analysis
The system design has been enhanced based on analysis of the existing Excel-based correspondence tracking system (`CORRESPONDENCE_2025.xlsx`). The following critical fields from the Excel system have been integrated into the database design:

#### Excel Fields Captured:
1. **REF NO** → `correspondence_number` (with department prefix support)
2. **CORRESPONDENCE TYPE** → `correspondence_direction` (INWARD/OUTWARD/INTERNAL)
3. **DATE RECEIVED** → `date_received`
4. **ORIGINAL DATE** → `original_date` (NEW - date on original document)
5. **SUBJECT/PARTICULARS** → `subject`
6. **TYPE** → `correspondence_type` (expanded to include PHONE, CIRCULAR, WHATSAPP, SMS)
7. **SENDER NAME** → `sender_name`
8. **SENDER ADDRESS** → `sender_organization`, `sender_address`
9. **REFER TO** → `referred_to` (in referrals table)
10. **REFER DATE** → `referral_date`
11. **RESPONSIBLE OFFICER** → `referred_to` (assigned officer)
12. **STATUS** → `status`
13. **ACTION REQUIRED** → `action_required` (NEW - in referrals table)
14. **DATE ACTIONED** → `completed_date` (in referrals/responses)
15. **FOLLOW-UP DATE** → `follow_up_date` (NEW - in correspondences and follow_ups table)
16. **FILED/ARCHIVED** → `filing_reference`, `archive_location` (NEW)

### Key Enhancements Based on Gap Analysis:

#### 1. Bidirectional Correspondence Support
- **Added**: `correspondence_direction` field (INWARD/OUTWARD/INTERNAL)
- **Added**: Recipient fields for outward correspondence
- **Added**: `dispatch_method` for tracking how outward mail was sent

#### 2. Enhanced Date Tracking
- **Added**: `original_date` - Date on the original document (separate from received date)
- **Added**: `date_sent` - For outward correspondence

#### 3. Action Management
- **Added**: `action_required` enum in referrals (APPROVAL, REVIEW, REPLY, RECORD, etc.)
- **Added**: `action_category` in responses (REPLIED, APPROVED, REJECTED, etc.)
- **Added**: `action_details` for comprehensive action documentation

#### 4. Follow-up System
- **Added**: `follow_ups` table for systematic follow-up tracking
- **Added**: `follow_up_date` and `follow_up_completed` in correspondences
- **Added**: Follow-up fields in responses table

#### 5. Filing & Archive Management
- **Added**: `filing_reference` - Physical file location (e.g., "P/F-FKupiaw/F-HRM")
- **Added**: `archive_location` - Archive storage location
- **Added**: `archive_date` - When archived

#### 6. Department-based Numbering
- **Added**: `department` field in correspondences
- **Enhanced**: `numbering_sequences` table to support department prefixes (HRM-001, FIN-002, etc.)

#### 7. Expanded Communication Types
- **Enhanced**: `correspondence_type` to include:
  - PHONE (phone calls)
  - CIRCULAR (circulars)
  - WHATSAPP (WhatsApp messages)
  - SMS (text messages)
  - MEETING_MINUTES
  - REPORT

### Implementation Priority (Based on Gap Analysis):

#### Phase 2A - Critical Enhancements (High Priority):
1. ✓ Correspondence direction (Inward/Outward)
2. ✓ Original date field
3. ✓ Action required categorization
4. ✓ Filing reference field
5. ✓ Follow-up management system

#### Phase 2B - Important Enhancements (Medium Priority):
1. ✓ Department-based numbering
2. ✓ Enhanced action tracking
3. ✓ Recipient fields for outward correspondence
4. ✓ Expanded communication types

#### Phase 2C - Advanced Features (Lower Priority):
1. Archive management automation
2. Dispatch tracking for outward mail
3. Follow-up reminder notifications
4. Overdue action alerts

---

## 3. Database Schema

### Current Implementation (Phase 1)

#### 1. Organizations Table (Implemented)
```sql
CREATE TABLE organizations (
    id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    org_code VARCHAR(4) UNIQUE NOT NULL COMMENT 'Unique 4-digit organization code (11XX, 12XX, etc.)',
    org_name VARCHAR(255) NOT NULL,
    org_logo VARCHAR(255) NULL COMMENT 'Organization logo filename',
    description TEXT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active' NOT NULL,
    created_by INT(11) UNSIGNED NULL,
    updated_by INT(11) UNSIGNED NULL,
    deleted_by INT(11) UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,
    INDEX idx_org_code (org_code),
    INDEX idx_status (status)
) ENGINE=InnoDB;
```

#### 2. Groups Table (Implemented)
```sql
CREATE TABLE groups (
    id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    group_code VARCHAR(10) UNIQUE NOT NULL,
    group_name VARCHAR(255) NOT NULL,
    organization_id INT(11) UNSIGNED NOT NULL,
    parent_id INT(11) UNSIGNED NULL COMMENT 'Self-referencing for hierarchical structure',
    description TEXT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active' NOT NULL,
    created_by INT(11) UNSIGNED NULL,
    updated_by INT(11) UNSIGNED NULL,
    deleted_by INT(11) UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES groups(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_organization (organization_id),
    INDEX idx_parent (parent_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;
```

#### 3. Users Table (Implemented)
```sql
CREATE TABLE users (
    id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL COMMENT 'Unique email address',
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password (minimum 4 characters)',
    position VARCHAR(255) NULL COMMENT 'User job position/title',
    organization_id INT(11) UNSIGNED NOT NULL,
    group_id INT(11) UNSIGNED NULL,
    signature_filepath VARCHAR(255) NULL COMMENT 'Path to user signature image',
    stamp_filepath VARCHAR(255) NULL COMMENT 'Path to user stamp image',
    is_supervisor TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Is user a supervisor? (0=No, 1=Yes)',
    is_front_desk TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Is user front desk staff? (0=No, 1=Yes)',
    is_admin TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Is user an admin? (0=No, 1=Yes)',
    status ENUM('active', 'inactive') DEFAULT 'active' NOT NULL,
    created_by INT(11) UNSIGNED NULL,
    updated_by INT(11) UNSIGNED NULL,
    deleted_by INT(11) UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_email (email),
    INDEX idx_organization (organization_id),
    INDEX idx_group (group_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;
```

#### 4. Dakoii Users Table (Implemented - System Administrators)
```sql
CREATE TABLE dakoii_users (
    id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    INDEX idx_username (username)
) ENGINE=InnoDB;
```

### Planned Tables (Phase 2 - Correspondence Management)

#### 5. Correspondences Table (Planned - Enhanced with Gap Analysis)
**Note**: Enhanced based on gap analysis with Excel tracking system to capture all vital fields

```sql
CREATE TABLE correspondences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    correspondence_number VARCHAR(50) UNIQUE NOT NULL,
    reference_number VARCHAR(100),
    subject VARCHAR(500) NOT NULL,

    -- Communication Type & Direction (Enhanced)
    correspondence_type ENUM('LETTER', 'EMAIL', 'FAX', 'MEMO', 'CIRCULAR', 'PHONE',
                             'MEETING_MINUTES', 'REPORT', 'WHATSAPP', 'SMS', 'OTHER'),
    correspondence_direction ENUM('INWARD', 'OUTWARD', 'INTERNAL') DEFAULT 'INWARD',

    -- Date Fields (Enhanced with original_date)
    date_received DATE NOT NULL,
    original_date DATE COMMENT 'Date on the original document',
    date_sent DATE COMMENT 'For outward correspondence',

    -- Sender Information (Inward)
    sender_name VARCHAR(255),
    sender_organization VARCHAR(255),
    sender_address TEXT,
    sender_contact VARCHAR(100),

    -- Recipient Information (Outward - NEW)
    recipient_name VARCHAR(255),
    recipient_organization VARCHAR(255),
    recipient_address TEXT,
    dispatch_method VARCHAR(50) COMMENT 'Email, Post, Courier, Hand Delivery',

    -- Priority & Status
    priority ENUM('LOW', 'NORMAL', 'HIGH', 'URGENT') DEFAULT 'NORMAL',
    status ENUM('REGISTERED', 'REFERRED', 'IN_PROCESS', 'ACTIONED', 'COMPLETED', 'ARCHIVED'),

    -- Department & Organization Context (NEW)
    department VARCHAR(50) COMMENT 'Department code for numbering (HRM, FIN, etc.)',
    organization_id INT COMMENT 'Link to organizations table',

    -- Follow-up Management (NEW)
    follow_up_date DATE,
    follow_up_completed BOOLEAN DEFAULT FALSE,

    -- Filing & Archive (NEW)
    filing_reference VARCHAR(100) COMMENT 'Physical file location (e.g., P/F-FKupiaw/F-HRM)',
    archive_location VARCHAR(100),
    archive_date DATE,

    -- Registration & Linking
    registered_by INT,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    parent_correspondence_id INT NULL, -- For linked correspondences
    is_linked BOOLEAN DEFAULT FALSE,
    linked_type ENUM('RESPONSE', 'FOLLOW_UP', 'RELATED') NULL,

    -- Additional Information
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (registered_by) REFERENCES users(id),
    FOREIGN KEY (parent_correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (organization_id) REFERENCES organizations(id),

    INDEX idx_corr_number (correspondence_number),
    INDEX idx_status (status),
    INDEX idx_date (date_received),
    INDEX idx_direction (correspondence_direction),
    INDEX idx_department (department),
    INDEX idx_follow_up (follow_up_date, follow_up_completed),
    INDEX idx_filing (filing_reference)
) ENGINE=InnoDB;

#### 6. Files Table (Planned - Document Storage)
```sql
CREATE TABLE files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_number VARCHAR(50) UNIQUE NOT NULL,
    correspondence_id INT NOT NULL,
    file_type ENUM('ORIGINAL', 'RESPONSE', 'ATTACHMENT', 'LINKED'),
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    mime_type VARCHAR(100),
    uploaded_by INT,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_stamped BOOLEAN DEFAULT FALSE,
    is_signed BOOLEAN DEFAULT FALSE,
    stamp_data JSON, -- Stores stamp/signature positions and metadata
    checksum VARCHAR(64), -- For file integrity
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    INDEX idx_correspondence (correspondence_id),
    INDEX idx_file_number (file_number)
) ENGINE=InnoDB;
```

#### 7. Referrals Table (Planned - Enhanced with Action Types)
**Note**: Enhanced to capture action required types from Excel tracking

```sql
CREATE TABLE referrals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    correspondence_id INT NOT NULL,
    referral_number VARCHAR(50) UNIQUE NOT NULL,
    referred_from INT NOT NULL,
    referred_to INT NOT NULL,
    referral_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    due_date DATE,

    -- Priority & Action Required (Enhanced)
    priority ENUM('LOW', 'NORMAL', 'HIGH', 'URGENT'),
    action_required ENUM('APPROVAL', 'REVIEW', 'REPLY', 'RECORD', 'INFORMATION',
                         'INVESTIGATION', 'FILING', 'OTHER') COMMENT 'Type of action needed',
    action_deadline DATETIME COMMENT 'Specific deadline for action',

    -- Referral Details
    referral_remarks TEXT,
    status ENUM('PENDING', 'ACKNOWLEDGED', 'IN_PROGRESS', 'COMPLETED', 'RETURNED'),
    acknowledged_date DATETIME NULL,
    completed_date DATETIME NULL,

    -- Digital Stamps/Signatures
    stamp_signature_data JSON COMMENT 'Position and details of stamps/signatures',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (referred_from) REFERENCES users(id),
    FOREIGN KEY (referred_to) REFERENCES users(id),

    INDEX idx_correspondence_ref (correspondence_id),
    INDEX idx_referred_to (referred_to),
    INDEX idx_status_ref (status),
    INDEX idx_action_required (action_required),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB;
```

#### 8. Responses Table (Planned - Enhanced Action Tracking)
**Note**: Enhanced to capture detailed action categories and follow-up requirements

```sql
CREATE TABLE responses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    correspondence_id INT NOT NULL,
    referral_id INT,
    response_number VARCHAR(50) UNIQUE NOT NULL,
    responded_by INT NOT NULL,
    response_type ENUM('COMMENT', 'FILE', 'BOTH'),

    -- Action Details (Enhanced)
    action_category ENUM('REPLIED', 'APPROVED', 'REJECTED', 'REVIEWED', 'RECORDED',
                         'FORWARDED', 'FILED', 'NO_ACTION_REQUIRED', 'DEFERRED'),
    action_taken VARCHAR(500) COMMENT 'Brief description of action',
    action_details TEXT COMMENT 'Detailed description of action taken',
    remarks TEXT,

    -- Response Metadata
    response_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    file_id INT NULL,
    is_final_response BOOLEAN DEFAULT FALSE,

    -- Follow-up Management (NEW)
    follow_up_required BOOLEAN DEFAULT FALSE,
    follow_up_date DATE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (referral_id) REFERENCES referrals(id),
    FOREIGN KEY (responded_by) REFERENCES users(id),
    FOREIGN KEY (file_id) REFERENCES files(id),

    INDEX idx_correspondence_resp (correspondence_id),
    INDEX idx_referral (referral_id),
    INDEX idx_action_category (action_category),
    INDEX idx_follow_up (follow_up_required, follow_up_date)
) ENGINE=InnoDB;
```

#### 9. Follow-ups Table (NEW - Systematic Follow-up Management)
**Note**: New table to track follow-up actions and deadlines from Excel tracking

```sql
CREATE TABLE follow_ups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    correspondence_id INT NOT NULL,
    follow_up_date DATE NOT NULL,
    follow_up_reason VARCHAR(500),
    assigned_to INT,
    status ENUM('PENDING', 'COMPLETED', 'CANCELLED', 'OVERDUE') DEFAULT 'PENDING',
    completed_date DATETIME,
    completed_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (completed_by) REFERENCES users(id),

    INDEX idx_follow_date (follow_up_date),
    INDEX idx_status_follow (status),
    INDEX idx_assigned (assigned_to)
) ENGINE=InnoDB;
```

#### 10. Audit Trail Table (Planned - Enhanced Audit Logging)
```sql
CREATE TABLE audit_trail (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    correspondence_id INT NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    action_details JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_correspondence_audit (correspondence_id),
    INDEX idx_user_audit (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;
```

#### 11. Numbering Sequences Table (Planned - Enhanced with Department Support)
**Note**: Enhanced to support department-based numbering (HRM-001, FIN-001, etc.)

```sql
CREATE TABLE numbering_sequences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sequence_type VARCHAR(50) NOT NULL,
    prefix VARCHAR(20),
    department VARCHAR(50) COMMENT 'Department code for department-based numbering',
    current_number INT DEFAULT 0,
    year INT,
    format_pattern VARCHAR(100) COMMENT 'e.g., {DEPT}-{YEAR}/{NUMBER:03d} or CORR/{YEAR}/{NUMBER:05d}',
    last_reset_date DATE,
    UNIQUE KEY unique_sequence (sequence_type, year),
    UNIQUE KEY unique_dept_sequence (sequence_type, department, year)
) ENGINE=InnoDB;
```

#### 12. Notifications Table (Planned - Enhanced with Follow-up Reminders)
```sql
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    correspondence_id INT,
    referral_id INT,
    follow_up_id INT,
    type ENUM('NEW_REFERRAL', 'RESPONSE_RECEIVED', 'DUE_DATE_REMINDER',
              'STATUS_CHANGE', 'FOLLOW_UP_REMINDER', 'OVERDUE_ACTION'),
    title VARCHAR(255),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (referral_id) REFERENCES referrals(id),
    FOREIGN KEY (follow_up_id) REFERENCES follow_ups(id),

    INDEX idx_user_notif (user_id, is_read)
) ENGINE=InnoDB;
```

### Stamp/Signature JSON Structure

```json
{
  "stamps": [
    {
      "type": "stamp",
      "user_id": 123,
      "timestamp": "2024-01-15 10:30:00",
      "position": {
        "page": 1,
        "x": 150,
        "y": 200,
        "width": 100,
        "height": 50
      },
      "stamp_id": "STAMP_001",
      "verification_hash": "abc123..."
    }
  ],
  "signatures": [
    {
      "type": "signature",
      "user_id": 124,
      "timestamp": "2024-01-15 11:00:00",
      "position": {
        "page": 1,
        "x": 300,
        "y": 400,
        "width": 150,
        "height": 75
      },
      "signature_id": "SIG_001",
      "verification_hash": "def456..."
    }
  ]
}
```

---

## 4. Workflow Design

### A. Registration Workflow (Enhanced with Gap Analysis Fields)

```
1. Front Desk receives/sends physical/digital correspondence
   - Select correspondence direction: INWARD, OUTWARD, or INTERNAL
   - Select communication type: LETTER, EMAIL, PHONE, CIRCULAR, etc.

2. System generates unique department-based correspondence number
   - Format: {DEPT}-{YEAR}/{NUMBER} (e.g., HRM-2025/001)
   - Or generic: CORR/{YEAR}/{MONTH}/{NUMBER}

3. Front desk enters comprehensive metadata:
   - Subject/Particulars
   - Original date (date on document)
   - Date received (for inward) or Date sent (for outward)
   - Sender information (for inward)
   - Recipient information (for outward)
   - Priority level
   - Action required type (APPROVAL, REVIEW, REPLY, etc.)
   - Follow-up date (if applicable)
   - Filing reference (physical location)

4. Original document is scanned/uploaded
   - File gets unique file number
   - Stored in public/uploads/correspondences/

5. Initial referral created to responsible officer
   - Assign to specific officer
   - Set action deadline
   - Specify action required

6. Notification sent to assigned officer
   - Email notification
   - In-app notification

7. System tracks follow-up dates and sends reminders
```

### B. Referral Chain Workflow

```
┌────────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│Front Desk  │────▶│Officer A │────▶│Officer B │────▶│Officer C │
│(Register)  │     │(Review)  │     │(Process) │     │(Action)  │
└────────────┘     └──────────┘     └──────────┘     └──────────┘
                        │                 │                │
                        ▼                 ▼                ▼
                   [Response]        [Response]       [Response]
                   [Remarks]         [File Upload]    [Final Action]
```

### C. Response Document Linking Strategy

**Approach: Linked Correspondence System**

When an officer creates a response document:
1. New correspondence record created with `parent_correspondence_id`
2. Marked as `is_linked = true` with `linked_type = 'RESPONSE'`
3. Gets its own unique correspondence number
4. Appears in registry but shows relationship to parent
5. Maintains complete audit trail

**Benefits:**
- Full tracking of all documents
- Maintains document hierarchy
- Searchable in registry
- Clear parent-child relationships
- Supports multiple response levels

---

## 5. API Design

### Current RESTful Routes (Implemented)

```php
// Authentication Routes
GET    /login                                    // Admin login page
POST   /login/authenticate                       // Admin authentication
GET    /admin/dashboard                          // Admin dashboard
POST   /admin/logout                             // Admin logout

GET    /dakoii/                                  // Dakoii login page
POST   /dakoii/authenticate                      // Dakoii authentication
GET    /dakoii/dashboard                         // Dakoii dashboard
POST   /dakoii/logout                            // Dakoii logout

// Organizations Management (RESTful)
GET    /dakoii/organizations                     // List all organizations
GET    /dakoii/organizations/new                 // Show create form
POST   /dakoii/organizations                     // Create organization
GET    /dakoii/organizations/{id}                // Show organization
GET    /dakoii/organizations/{id}/edit           // Show edit form
PUT    /dakoii/organizations/{id}                // Update organization
PATCH  /dakoii/organizations/{id}                // Update organization
DELETE /dakoii/organizations/{id}                // Delete organization (soft)
GET    /dakoii/organizations/generate-code       // Generate unique org code

// Groups Management (RESTful - Nested under Organizations)
GET    /dakoii/organizations/{org_id}/groups                     // List groups
GET    /dakoii/organizations/{org_id}/groups/new                 // Show create form
POST   /dakoii/organizations/{org_id}/groups                     // Create group
GET    /dakoii/organizations/{org_id}/groups/{id}                // Show group
GET    /dakoii/organizations/{org_id}/groups/{id}/edit           // Show edit form
PUT    /dakoii/organizations/{org_id}/groups/{id}                // Update group
PATCH  /dakoii/organizations/{org_id}/groups/{id}                // Update group
DELETE /dakoii/organizations/{org_id}/groups/{id}                // Delete group (soft)

// Users Management (RESTful - Nested under Organizations)
GET    /dakoii/organizations/{org_id}/users                      // List users
GET    /dakoii/organizations/{org_id}/users/new                  // Show create form
POST   /dakoii/organizations/{org_id}/users                      // Create user
GET    /dakoii/organizations/{org_id}/users/{id}                 // Show user
GET    /dakoii/organizations/{org_id}/users/{id}/edit            // Show edit form
PUT    /dakoii/organizations/{org_id}/users/{id}                 // Update user
PATCH  /dakoii/organizations/{org_id}/users/{id}                 // Update user
DELETE /dakoii/organizations/{org_id}/users/{id}                 // Delete user (soft)
```

### Planned RESTful Routes (Phase 2 - Correspondence Management)

```php
// Correspondence Endpoints
POST   /api/correspondence/register      // Register new correspondence
GET    /api/correspondence/{id}          // Get correspondence details
PUT    /api/correspondence/{id}          // Update correspondence
GET    /api/correspondence/list          // List with pagination/filters

// Referral Endpoints
POST   /api/referral/create             // Create new referral
GET    /api/referral/{id}               // Get referral details
PUT    /api/referral/{id}/acknowledge   // Acknowledge receipt
GET    /api/referral/pending            // Get pending referrals for user

// Response Endpoints
POST   /api/response/create             // Create response
GET    /api/response/{id}               // Get response details
POST   /api/response/{id}/upload        // Upload response file

// File Management
POST   /api/file/upload                 // Upload file
GET    /api/file/{id}/download          // Download file
POST   /api/file/{id}/stamp             // Add stamp/signature
GET    /api/file/{id}/metadata          // Get file metadata

// Search and Reports
GET    /api/search                      // Search correspondences
GET    /api/reports/tracking/{id}       // Get tracking report
GET    /api/reports/statistics          // Get system statistics
```

---

## 6. Implementation Guidelines

### A. CodeIgniter 4 Structure (Current Implementation)

```
app/
├── Config/
│   ├── Routes.php                    # RESTful routing configuration
│   ├── Filters.php                   # Filter configuration
│   └── Database.php                  # Database configuration
├── Controllers/
│   ├── BaseController.php            # Base controller
│   ├── Admin.php                     # Admin portal authentication
│   ├── Dakoii.php                    # Dakoii portal authentication
│   ├── Organizations.php             # Organization CRUD (RESTful)
│   ├── Groups.php                    # Groups CRUD (RESTful, nested)
│   ├── Users.php                     # Users CRUD (RESTful, nested)
│   └── Home.php                      # Landing page
├── Models/
│   ├── OrganizationModel.php         # Organization model with audit methods
│   ├── GroupModel.php                # Group model with hierarchy support
│   ├── UserModel.php                 # User model with authentication
│   └── DakoiiUserModel.php           # System admin model
├── Filters/
│   ├── AdminAuthFilter.php           # Admin authentication filter
│   └── DakoiiAuthFilter.php          # Dakoii authentication filter
├── Database/
│   ├── Migrations/                   # Database migrations
│   │   ├── CreateOrganizationsTable.php
│   │   ├── CreateGroupsTable.php
│   │   ├── CreateUsersTable.php
│   │   ├── CreateDakoiiUsersTable.php
│   │   ├── AddUserRoleFields.php
│   │   └── ReorganizeUsersTableStructure.php
│   └── Seeds/
│       └── DakoiiUserSeeder.php      # Default admin seeder
└── Views/
    ├── templates/
    │   ├── admin_template.php        # Admin portal template
    │   ├── dakoii_template.php       # Dakoii portal template
    │   └── public_template.php       # Public template
    ├── admin/
    │   ├── admin_login.php
    │   └── admin_dashboard.php
    ├── dakoii/
    │   ├── login.php
    │   ├── dashboard.php
    │   ├── organizations/            # Organization views
    │   │   ├── dakoii_organizations_list.php
    │   │   ├── dakoii_organizations_create.php
    │   │   ├── dakoii_organizations_edit.php
    │   │   └── dakoii_organizations_view.php
    │   ├── groups/                   # Group views
    │   │   ├── dakoii_groups_list.php
    │   │   ├── dakoii_groups_create.php
    │   │   ├── dakoii_groups_edit.php
    │   │   └── dakoii_groups_view.php
    │   └── users/                    # User views
    │       ├── dakoii_users_list.php
    │       ├── dakoii_users_create.php
    │       ├── dakoii_users_edit.php
    │       └── dakoii_users_view.php
    └── landing_page.php

public/
└── uploads/                          # File upload directory
    ├── signatures/                   # User signatures
    ├── stamps/                       # User stamps
    └── logos/                        # Organization logos
```

### Planned Structure (Phase 2 - Correspondence Management)

```
app/
├── Controllers/
│   ├── CorrespondenceController.php  # Correspondence CRUD
│   ├── ReferralController.php        # Referral management
│   ├── ResponseController.php        # Response management
│   ├── FileController.php            # File management
│   └── ReportController.php          # Reports and analytics
├── Models/
│   ├── CorrespondenceModel.php       # Correspondence model
│   ├── ReferralModel.php             # Referral model
│   ├── ResponseModel.php             # Response model
│   ├── FileModel.php                 # File model
│   └── AuditModel.php                # Audit trail model
├── Services/
│   ├── NumberingService.php          # Unique number generation
│   ├── FileUploadService.php         # File upload handling
│   ├── NotificationService.php       # Notification system
│   ├── StampService.php              # Digital stamp/signature
│   └── WorkflowService.php           # Workflow management
├── Libraries/
│   ├── PdfProcessor.php              # PDF processing
│   └── DocumentValidator.php         # Document validation
└── Views/
    ├── correspondence/               # Correspondence views
    ├── referral/                     # Referral views
    ├── response/                     # Response views
    └── reports/                      # Report views
```

### B. Current Model Implementations

#### OrganizationModel.php
```php
<?php
namespace App\Models;

class OrganizationModel extends Model
{
    protected $table = 'organizations';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'org_code', 'org_name', 'org_logo', 'description', 'status',
        'created_by', 'updated_by', 'deleted_by'
    ];

    // Key Features:
    // - Auto-generate unique 4-digit org codes (11XX, 12XX, etc.)
    // - Validation rules for unique org_code
    // - Audit trail callbacks (setCreatedBy, setUpdatedBy, setDeletedBy)
    // - getOrganizationsWithAudit() - joins with dakoii_users for audit info
    // - getOrganizationWithAudit($id) - single record with audit info
}
```

#### GroupModel.php
```php
<?php
namespace App\Models;

class GroupModel extends Model
{
    protected $table = 'groups';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'group_code', 'group_name', 'organization_id', 'parent_id',
        'description', 'status', 'created_by', 'updated_by', 'deleted_by'
    ];

    // Key Features:
    // - Auto-generate group codes with g2 prefix (g201, g202, etc.)
    // - Self-referencing parent_id for hierarchical structure
    // - getGroupsByOrganization($orgId) - filtered by organization
    // - getAvailableParentGroups($orgId, $excludeId) - for dropdown
    // - Audit trail callbacks
}
```

#### UserModel.php
```php
<?php
namespace App\Models;

class UserModel extends Model
{
    protected $table = 'users';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'name', 'email', 'password', 'organization_id', 'group_id',
        'status', 'position', 'signature_filepath', 'stamp_filepath',
        'is_supervisor', 'is_front_desk', 'is_admin',
        'created_by', 'updated_by', 'deleted_by'
    ];

    // Key Features:
    // - Password hashing in beforeInsert/beforeUpdate callbacks
    // - verifyCredentials($email, $password) - authentication
    // - getUsersByOrganization($orgId) - filtered by organization
    // - getUsersWithAudit() - joins with organizations, groups, dakoii_users
    // - Email uniqueness validation
    // - Audit trail callbacks
}
```

#### DakoiiUserModel.php
```php
<?php
namespace App\Models;

class DakoiiUserModel extends Model
{
    protected $table = 'dakoii_users';
    protected $allowedFields = ['name', 'username', 'password'];

    // Key Features:
    // - System administrator authentication
    // - Password hashing
    // - verifyCredentials($username, $password)
    // - Separate from regular users table
}
```

### C. Planned Numbering Service Implementation (Phase 2)

```php
<?php
namespace App\Services;

class NumberingService
{
    protected $db;

    public function generateCorrespondenceNumber($type = 'CORR')
    {
        $year = date('Y');
        $month = date('m');

        // Get current sequence
        $sequence = $this->db->table('numbering_sequences')
            ->where('sequence_type', $type)
            ->where('year', $year)
            ->first();

        if (!$sequence) {
            // Create new sequence for the year
            $this->db->table('numbering_sequences')->insert([
                'sequence_type' => $type,
                'prefix' => $type,
                'current_number' => 1,
                'year' => $year,
                'format_pattern' => '{PREFIX}/{YEAR}/{MONTH}/{NUMBER:05d}'
            ]);
            $nextNumber = 1;
        } else {
            // Increment and get next number
            $nextNumber = $sequence->current_number + 1;
            $this->db->table('numbering_sequences')
                ->where('id', $sequence->id)
                ->update(['current_number' => $nextNumber]);
        }

        // Format: CORR/2024/01/00001
        return sprintf("%s/%04d/%02d/%05d", $type, $year, $month, $nextNumber);
    }

    public function generateFileNumber($correspondenceId)
    {
        $corrNumber = $this->getCorrespondenceNumber($correspondenceId);
        $fileCount = $this->getFileCount($correspondenceId) + 1;

        // Format: CORR/2024/01/00001/F001
        return sprintf("%s/F%03d", $corrNumber, $fileCount);
    }
}
```

### D. Current Controller Pattern (RESTful Implementation)

#### Example: Organizations Controller
```php
<?php
namespace App\Controllers;

use App\Models\OrganizationModel;
use CodeIgniter\RESTful\ResourceController;

class Organizations extends ResourceController
{
    protected $modelName = 'App\Models\OrganizationModel';
    protected $format = 'json';

    // GET /dakoii/organizations
    public function index()
    {
        $model = new OrganizationModel();
        $data = [
            'title' => 'Organizations Management',
            'organizations' => $model->getOrganizationsWithAudit(),
        ];
        return view('dakoii/organizations/dakoii_organizations_list', $data);
    }

    // GET /dakoii/organizations/new
    public function new()
    {
        // Show create form
    }

    // POST /dakoii/organizations
    public function create()
    {
        // Handle file upload (org_logo)
        // Validate and insert
        // Redirect with success/error message
    }

    // GET /dakoii/organizations/{id}
    public function show($id = null)
    {
        // Display single organization
    }

    // GET /dakoii/organizations/{id}/edit
    public function edit($id = null)
    {
        // Show edit form
    }

    // PUT/PATCH /dakoii/organizations/{id}
    public function update($id = null)
    {
        // Handle file upload
        // Validate and update
        // Redirect with success/error message
    }

    // DELETE /dakoii/organizations/{id}
    public function delete($id = null)
    {
        // Soft delete
        // Redirect with success/error message
    }
}
```

### E. Planned Referral Chain Implementation (Phase 2)

```php
<?php
namespace App\Services;

class WorkflowService
{
    public function createReferral($data)
    {
        DB::beginTransaction();
        try {
            // Create referral
            $referralNumber = $this->numberingService->generateReferralNumber();
            $referral = ReferralModel::create([
                'correspondence_id' => $data['correspondence_id'],
                'referral_number' => $referralNumber,
                'referred_from' => auth()->id(),
                'referred_to' => $data['referred_to'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'referral_remarks' => $data['remarks'],
                'status' => 'PENDING',
                'stamp_signature_data' => $data['stamp_data'] ?? null
            ]);

            // Update correspondence status
            CorrespondenceModel::where('id', $data['correspondence_id'])
                ->update(['status' => 'REFERRED']);

            // Create notification
            $this->notificationService->notify(
                $data['referred_to'],
                'NEW_REFERRAL',
                $data['correspondence_id'],
                $referral->id
            );

            // Log audit trail
            $this->auditService->log(
                'REFERRAL_CREATED',
                $data['correspondence_id'],
                ['referral_id' => $referral->id]
            );

            DB::commit();
            return $referral;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function createLinkedCorrespondence($parentId, $responseData)
    {
        // Get parent correspondence
        $parent = CorrespondenceModel::find($parentId);

        // Create linked correspondence
        $linkedNumber = $this->numberingService->generateCorrespondenceNumber('RESP');

        $linked = CorrespondenceModel::create([
            'correspondence_number' => $linkedNumber,
            'subject' => "Response to: " . $parent->subject,
            'parent_correspondence_id' => $parentId,
            'is_linked' => true,
            'linked_type' => 'RESPONSE',
            'status' => 'COMPLETED',
            // ... other fields
        ]);

        return $linked;
    }
}
```

### F. Current Frontend Implementation

#### View Naming Convention
All view files follow the pattern: `{folder_name}_{action}.php`
- Folder: `dakoii/organizations/` → Files: `dakoii_organizations_list.php`, `dakoii_organizations_create.php`, etc.
- Folder: `dakoii/groups/` → Files: `dakoii_groups_list.php`, `dakoii_groups_create.php`, etc.
- Folder: `dakoii/users/` → Files: `dakoii_users_list.php`, `dakoii_users_create.php`, etc.

#### Template Structure
```php
<!-- dakoii_template.php -->
<!DOCTYPE html>
<html>
<head>
    <title><?= esc($title) ?> - Correspondence Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <!-- Navigation items -->
    </nav>

    <!-- Content -->
    <div class="container-fluid mt-4">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
```

#### AJAX Form Pattern (with CSRF Token Refresh)
```javascript
$('#createForm').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response) {
            // Refresh CSRF token
            $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token);

            // Show success message
            alert('Success!');
            location.reload();
        },
        error: function(xhr) {
            // Refresh CSRF token
            if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
                $('input[name="<?= csrf_token() ?>"]').val(xhr.responseJSON.csrf_token);
            }

            // Show error message
            alert('Error: ' + xhr.responseJSON.message);
        }
    });
});
```

### G. Planned Frontend Components (Phase 2 - Correspondence)

```html
<!-- Correspondence Registration Form -->
<div class="card">
    <div class="card-header">
        <h4>Register New Correspondence</h4>
    </div>
    <div class="card-body">
        <form id="correspondenceForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Reference Number</label>
                        <input type="text" class="form-control" name="reference_number">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Date Received</label>
                        <input type="date" class="form-control" name="date_received" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Subject</label>
                <input type="text" class="form-control" name="subject" required>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Sender Name</label>
                        <input type="text" class="form-control" name="sender_name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Organization</label>
                        <input type="text" class="form-control" name="sender_organization">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Priority</label>
                        <select class="form-control" name="priority">
                            <option value="NORMAL">Normal</option>
                            <option value="HIGH">High</option>
                            <option value="URGENT">Urgent</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Upload Document</label>
                <input type="file" class="form-control-file" name="document" required>
            </div>

            <div class="form-group">
                <label>Refer To</label>
                <select class="form-control" name="referred_to" required>
                    <option value="">Select Officer</option>
                    <!-- Populated from database -->
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Register & Refer</button>
        </form>
    </div>
</div>

<!-- Referral Chain View -->
<div class="card mt-3">
    <div class="card-header">
        <h5>Referral History</h5>
    </div>
    <div class="card-body">
        <div class="timeline">
            <!-- Timeline items dynamically generated -->
        </div>
    </div>
</div>
```

---

## 7. Security Considerations

### Current Implementation (Phase 1)

#### A. Authentication & Authorization
- **Dual Portal System**: Separate authentication for Admin and Dakoii portals
- **Session-based Authentication**: Secure session management with CodeIgniter 4
- **Custom Auth Filters**:
  - `AdminAuthFilter` - Protects admin routes
  - `DakoiiAuthFilter` - Protects dakoii routes
- **Password Security**:
  - Bcrypt hashing with `password_hash()` and `PASSWORD_DEFAULT`
  - Minimum 4 characters (configurable in validation rules)
  - Password verification with `password_verify()`
- **Role-based Flags**:
  - `is_admin` - System administrator
  - `is_supervisor` - Supervisor role
  - `is_front_desk` - Front desk staff

#### B. Data Security
- **CSRF Protection**: Built-in CodeIgniter 4 CSRF filter on all forms
- **CSRF Token Refresh**: AJAX forms refresh token after each request
- **SQL Injection Prevention**: Query builder and prepared statements
- **XSS Protection**: Output escaping with `esc()` helper
- **Soft Deletes**: Data preservation with `deleted_at` timestamp
- **Audit Trail**: Track who created, updated, and deleted records
  - `created_by` - User who created the record
  - `updated_by` - User who last updated the record
  - `deleted_by` - User who deleted the record

#### C. File Upload Security
- **File Validation**: Type and size validation
- **Random File Names**: `getRandomName()` prevents file overwrites
- **Secure Storage**: Files stored in `public/uploads/` with subdirectories
- **Path Prefix**: Database stores `public/` prefix for proper access control
- **File Type Restrictions**: Only allowed file types (images for signatures/stamps)

#### D. Database Security
- **Foreign Key Constraints**: Referential integrity enforcement
- **Cascade Deletes**: Proper cleanup of related records
- **Unique Constraints**: Prevent duplicate emails, org codes, group codes
- **Indexed Fields**: Performance optimization on frequently queried fields

#### E. Session Security
- **Session Variables**:
  - Admin: `admin_logged_in`, `admin_user_id`, `admin_username`
  - Dakoii: `dakoii_logged_in`, `dakoii_user_id`, `dakoii_username`, `dakoii_name`
- **Session Validation**: Filters check session before allowing access
- **Logout Functionality**: Proper session destruction

### Planned Security Enhancements (Phase 2)

#### A. Enhanced Access Control
- **Role-based Permissions (RBAC)**: Granular permission system
- **Department-level Restrictions**: Users can only access their department's data
- **Document Classification Levels**: Confidential, Secret, Top Secret
- **Permission Matrix**: Define who can view/edit/delete specific resources

#### B. Advanced Data Security
- **Encrypted File Storage**: Encrypt sensitive documents at rest
- **Enhanced Audit Trail**: Log all correspondence actions with IP and user agent
- **Two-Factor Authentication (2FA)**: Optional 2FA for admin users
- **Password Policies**: Enforce strong passwords, expiration, history

#### C. File Integrity & Verification
- **Checksum Verification**: SHA-256 hash for file integrity
- **Digital Signature Validation**: PKI-based signature verification
- **Watermarking**: Add watermarks to sensitive documents
- **Version Control**: Track document versions and changes

#### D. Compliance & Monitoring
- **Data Retention Policies**: Automatic archival and deletion
- **GDPR Compliance**: Data export, right to be forgotten
- **Activity Logging**: Comprehensive logging of all system activities
- **Security Audits**: Regular automated security scans
- **Backup & Recovery**: Automated backups with encryption

---

## Additional Features to Consider

### Phase 2 Enhancements
1. **Email Integration**: Auto-import from email
2. **OCR Processing**: Text extraction from scanned documents
3. **Advanced Search**: Full-text search capabilities
4. **Mobile App**: iOS/Android applications
5. **Dashboard Analytics**: Real-time statistics
6. **Workflow Automation**: Rule-based auto-routing
7. **Template Management**: Response templates
8. **Bulk Operations**: Mass referral/actions
9. **API Integration**: Third-party system integration
10. **Digital Signature Integration**: PKI-based signatures

### Performance Optimizations
1. **Database Indexing**: Strategic index placement
2. **Caching Strategy**: Redis/Memcached integration
3. **Lazy Loading**: Pagination and infinite scroll
4. **File CDN**: Content delivery for documents
5. **Queue Processing**: Background job processing

---

## 8. Current Implementation Status

### Phase 1: Foundation (COMPLETED ✓)

#### Implemented Features:
1. **Multi-Organization Support**
   - Organization CRUD with unique 4-digit codes (11XX, 12XX, etc.)
   - Organization logo upload support
   - Soft delete with audit trail (created_by, updated_by, deleted_by)

2. **Hierarchical Group Management**
   - Group CRUD with parent-child relationships
   - Auto-generated group codes (g201, g202, etc.)
   - Nested under organizations
   - Soft delete with audit trail

3. **User Management**
   - User CRUD with email-based authentication
   - Role-based flags (is_admin, is_supervisor, is_front_desk)
   - Position/title support
   - Digital signature and stamp file uploads
   - Password hashing with bcrypt
   - Nested under organizations
   - Group assignment
   - Soft delete with audit trail

4. **Authentication System**
   - Dual portal system (Admin and Dakoii)
   - Session-based authentication
   - CSRF protection
   - Custom authentication filters (AdminAuthFilter, DakoiiAuthFilter)
   - Secure password handling

5. **RESTful Architecture**
   - Standard RESTful routes for all resources
   - Nested routes for hierarchical resources
   - Proper HTTP methods (GET, POST, PUT, PATCH, DELETE)
   - ResourceController pattern

6. **File Upload System**
   - Signature file uploads (public/uploads/signatures/)
   - Stamp file uploads (public/uploads/stamps/)
   - Organization logo uploads (public/uploads/logos/)
   - File path storage with public/ prefix for proper access

7. **Database Design**
   - CodeIgniter 4 migrations
   - Foreign key constraints
   - Soft deletes
   - Timestamps (created_at, updated_at, deleted_at)
   - Audit fields (created_by, updated_by, deleted_by)

8. **View Templates**
   - Consistent template system (admin_template, dakoii_template, public_template)
   - Bootstrap-based UI
   - AJAX form submissions with CSRF token refresh
   - Responsive design
   - Consistent naming convention (prefix with folder name)

### Phase 2: Correspondence Management (PLANNED - Enhanced with Gap Analysis)

#### To Be Implemented:

##### Phase 2A - Core Correspondence Features (High Priority):
1. **Enhanced Correspondence Registration**
   - Front desk registration interface
   - Bidirectional support (Inward/Outward/Internal)
   - Department-based unique numbering (HRM-001, FIN-002, etc.)
   - Comprehensive metadata capture:
     * Original date vs received date
     * Sender/Recipient information
     * Communication type (including PHONE, CIRCULAR, WHATSAPP, SMS)
     * Action required categorization
     * Follow-up date setting
     * Filing reference entry
   - File upload and scanning

2. **Referral System with Action Types**
   - Multi-level referral chains
   - Action required specification (APPROVAL, REVIEW, REPLY, RECORD, etc.)
   - Referral tracking with action deadlines
   - Due date management
   - Priority handling
   - Responsible officer assignment

3. **Enhanced Response Management**
   - Response creation and tracking
   - Action category tracking (REPLIED, APPROVED, REJECTED, etc.)
   - Detailed action documentation
   - File attachments
   - Linked correspondence system
   - Follow-up requirement flagging

4. **Follow-up Management System**
   - Systematic follow-up tracking
   - Follow-up date scheduling
   - Follow-up completion tracking
   - Overdue follow-up alerts
   - Follow-up assignment to officers

5. **Filing & Archive Management**
   - Physical filing reference tracking
   - Archive location management
   - Archive date recording
   - Filing status tracking

##### Phase 2B - Advanced Features (Medium Priority):
1. **Digital Stamps/Signatures**
   - PDF stamp/signature placement
   - Position tracking
   - Verification system

2. **Notifications & Reminders**
   - Email notifications
   - In-app notifications
   - Due date reminders
   - Follow-up reminders
   - Overdue action alerts

3. **Reports and Analytics**
   - Tracking reports
   - Statistics dashboard
   - Search functionality
   - Department-wise reports
   - Action status reports
   - Follow-up reports

4. **Workflow Automation**
   - Auto-routing based on rules
   - Status transitions
   - Approval workflows
   - Automatic follow-up creation

##### Phase 2C - Integration & Enhancement (Lower Priority):
1. **Outward Correspondence Features**
   - Outward mail registration
   - Recipient management
   - Dispatch method tracking
   - Delivery confirmation

2. **Advanced Archive Management**
   - Automated archival based on rules
   - Archive retrieval system
   - Archive search functionality

3. **Mobile App Integration**
   - Mobile notifications
   - Mobile document scanning
   - Mobile approval workflows

### Key Design Patterns Used:

1. **MVC Pattern**: Clear separation of concerns
2. **Repository Pattern**: Models handle data access
3. **RESTful Design**: Standard HTTP methods and resource-based URLs
4. **Soft Delete Pattern**: Data preservation with deleted_at
5. **Audit Trail Pattern**: Track who created/updated/deleted records
6. **Template Pattern**: Consistent view templates
7. **Filter Pattern**: Authentication and CSRF protection
8. **Nested Resources**: Hierarchical URL structure

### Technology Stack:

- **Framework**: CodeIgniter 4
- **Database**: MySQL with InnoDB engine
- **Frontend**: Bootstrap 5, JavaScript, AJAX
- **Authentication**: Session-based with custom filters
- **File Storage**: Local filesystem (public/uploads/)
- **Server**: XAMPP (Apache + MySQL + PHP)

---

## Conclusion

This system design provides a robust, scalable solution for correspondence management with:
- **Multi-organization support** with hierarchical groups
- **Complete audit trail** for all operations
- **RESTful architecture** for easy maintenance and extension
- **Secure authentication** with dual portal system
- **Flexible user roles** (Admin, Supervisor, Front Desk)
- **File upload support** for signatures, stamps, and logos
- **Soft delete pattern** for data preservation
- **Foundation ready** for correspondence management features

### Current Status:
✓ **Phase 1 Complete**: Foundation infrastructure with organizations, groups, and users
⏳ **Phase 2 Pending**: Correspondence management features (registration, referrals, responses)
✓ **Gap Analysis Complete**: Excel tracking system analyzed and integrated into design

### Gap Analysis Integration Summary:

The system design has been comprehensively enhanced based on analysis of the existing Excel-based correspondence tracking system. All critical fields from the Excel system have been incorporated:

#### ✓ **Critical Enhancements Integrated**:
1. **Bidirectional Correspondence** - Support for Inward, Outward, and Internal correspondence
2. **Original Date Tracking** - Separate field for document date vs received date
3. **Action Type Management** - Categorized action requirements (APPROVAL, REVIEW, REPLY, etc.)
4. **Follow-up System** - Comprehensive follow-up tracking with dedicated table
5. **Filing References** - Physical file location and archive management
6. **Department Numbering** - Department-based reference numbers (HRM-001, FIN-002, etc.)
7. **Expanded Communication Types** - PHONE, CIRCULAR, WHATSAPP, SMS, etc.
8. **Enhanced Action Tracking** - Detailed action categories and completion tracking
9. **Recipient Management** - Full support for outward correspondence
10. **Dispatch Tracking** - Method of dispatch for outward mail

#### 📊 **Excel Field Mapping**:
- All 16 fields from Excel system mapped to database schema
- Additional fields added for digital workflow enhancement
- Backward compatibility maintained with existing Excel data

#### 🎯 **Implementation Readiness**:
The enhanced design ensures:
- **Zero data loss** from Excel to database migration
- **Enhanced functionality** beyond Excel capabilities
- **Scalable architecture** for future requirements
- **Complete audit trail** for compliance
- **Automated workflows** to reduce manual effort

The modular architecture allows for easy enhancement and scaling as requirements grow. The foundation is solid and ready for the correspondence management features to be built on top, with full integration of existing Excel tracking practices.