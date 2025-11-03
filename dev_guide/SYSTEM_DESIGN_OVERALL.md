# Correspondence Management System - System Design Document

## Table of Contents
1. [System Overview](#system-overview)
2. [Architecture Design](#architecture-design)
3. [Database Schema](#database-schema)
4. [Workflow Design](#workflow-design)
5. [API Design](#api-design)
6. [Implementation Guidelines](#implementation-guidelines)
7. [Security Considerations](#security-considerations)

---

## 1. System Overview

### Purpose
The Correspondence Management System (CMS) is designed to digitize and streamline the handling of official correspondence within an organization, providing tracking, accountability, and efficient document management.

### Key Features
- **Document Registration**: Unique numbering and initial registration by front desk
- **Multi-level Referral System**: Chain of referrals with tracking
- **Response Management**: Comments/remarks and file attachments
- **Linked Documents**: Response documents linked to original correspondence
- **Audit Trail**: Complete history of all actions
- **Digital Signatures/Stamps**: Position-tracked stamps and signatures

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
                    │  (Local/Cloud)       │
                    └──────────────────────┘
```

---

## 2. Architecture Design

### Component Architecture

```
┌────────────────────────────────────────────────────────┐
│                    Presentation Layer                   │
│                 (Bootstrap + JavaScript)                │
├────────────────────────────────────────────────────────┤
│                   Application Layer                     │
│                   (CodeIgniter 4 MVC)                  │
│  ┌─────────────┬──────────────┬───────────────────┐   │
│  │ Controllers │    Models     │     Services      │   │
│  │             │               │                   │   │
│  │ • Register  │ • Correspond  │ • FileService    │   │
│  │ • Referral  │ • Referral    │ • NumberingServ  │   │
│  │ • Response  │ • Response    │ • NotifyService  │   │
│  │ • Search    │ • User        │ • StampService   │   │
│  └─────────────┴──────────────┴───────────────────┘   │
├────────────────────────────────────────────────────────┤
│                      Data Layer                         │
│                    (MySQL Database)                     │
└────────────────────────────────────────────────────────┘
```

### Workflow State Machine

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

## 3. Database Schema

### Core Tables Design

```sql
-- 1. Correspondences Table (Main Registry)
CREATE TABLE correspondences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    correspondence_number VARCHAR(50) UNIQUE NOT NULL,
    reference_number VARCHAR(100),
    subject VARCHAR(500) NOT NULL,
    sender_name VARCHAR(255),
    sender_organization VARCHAR(255),
    sender_address TEXT,
    date_received DATE NOT NULL,
    correspondence_type ENUM('LETTER', 'EMAIL', 'FAX', 'MEMO', 'OTHER'),
    priority ENUM('LOW', 'NORMAL', 'HIGH', 'URGENT'),
    status ENUM('REGISTERED', 'REFERRED', 'IN_PROCESS', 'ACTIONED', 'COMPLETED', 'ARCHIVED'),
    registered_by INT,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    parent_correspondence_id INT NULL, -- For linked correspondences
    is_linked BOOLEAN DEFAULT FALSE,
    linked_type ENUM('RESPONSE', 'FOLLOW_UP', 'RELATED') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (registered_by) REFERENCES users(id),
    FOREIGN KEY (parent_correspondence_id) REFERENCES correspondences(id),
    INDEX idx_corr_number (correspondence_number),
    INDEX idx_status (status),
    INDEX idx_date (date_received)
) ENGINE=InnoDB;

-- 2. Files Table (Document Storage)
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

-- 3. Referrals Table (Tracking Document Movement)
CREATE TABLE referrals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    correspondence_id INT NOT NULL,
    referral_number VARCHAR(50) UNIQUE NOT NULL,
    referred_from INT NOT NULL,
    referred_to INT NOT NULL,
    referral_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    due_date DATE,
    priority ENUM('LOW', 'NORMAL', 'HIGH', 'URGENT'),
    referral_remarks TEXT,
    status ENUM('PENDING', 'ACKNOWLEDGED', 'IN_PROGRESS', 'COMPLETED', 'RETURNED'),
    acknowledged_date DATETIME NULL,
    completed_date DATETIME NULL,
    stamp_signature_data JSON, -- Position and details of stamps/signatures
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (referred_from) REFERENCES users(id),
    FOREIGN KEY (referred_to) REFERENCES users(id),
    INDEX idx_correspondence_ref (correspondence_id),
    INDEX idx_referred_to (referred_to),
    INDEX idx_status_ref (status)
) ENGINE=InnoDB;

-- 4. Responses Table (Actions and Remarks)
CREATE TABLE responses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    correspondence_id INT NOT NULL,
    referral_id INT,
    response_number VARCHAR(50) UNIQUE NOT NULL,
    responded_by INT NOT NULL,
    response_type ENUM('COMMENT', 'FILE', 'BOTH'),
    remarks TEXT,
    action_taken VARCHAR(500),
    response_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    file_id INT NULL,
    is_final_response BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (referral_id) REFERENCES referrals(id),
    FOREIGN KEY (responded_by) REFERENCES users(id),
    FOREIGN KEY (file_id) REFERENCES files(id),
    INDEX idx_correspondence_resp (correspondence_id),
    INDEX idx_referral (referral_id)
) ENGINE=InnoDB;

-- 5. Users Table (System Users)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id VARCHAR(50) UNIQUE,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    department VARCHAR(100),
    designation VARCHAR(100),
    role ENUM('FRONT_DESK', 'OFFICER', 'SUPERVISOR', 'ADMIN'),
    signature_path VARCHAR(500), -- Digital signature file
    stamp_path VARCHAR(500), -- Digital stamp file
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- 6. Audit Trail Table
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

-- 7. Numbering Sequences Table (For Unique Number Generation)
CREATE TABLE numbering_sequences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sequence_type VARCHAR(50) NOT NULL,
    prefix VARCHAR(20),
    current_number INT DEFAULT 0,
    year INT,
    format_pattern VARCHAR(100), -- e.g., 'CORR/{YEAR}/{NUMBER:05d}'
    last_reset_date DATE,
    UNIQUE KEY unique_sequence (sequence_type, year)
) ENGINE=InnoDB;

-- 8. Notifications Table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    correspondence_id INT,
    referral_id INT,
    type ENUM('NEW_REFERRAL', 'RESPONSE_RECEIVED', 'DUE_DATE_REMINDER', 'STATUS_CHANGE'),
    title VARCHAR(255),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (correspondence_id) REFERENCES correspondences(id),
    FOREIGN KEY (referral_id) REFERENCES referrals(id),
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

### A. Registration Workflow

```
1. Front Desk receives physical/digital correspondence
2. System generates unique correspondence number
3. Front desk enters metadata (subject, sender, etc.)
4. Original document is scanned/uploaded
5. File gets unique file number
6. Initial referral created to responsible officer
7. Notification sent to assigned officer
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

### RESTful API Endpoints

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

### A. CodeIgniter 4 Structure

```
app/
├── Controllers/
│   ├── CorrespondenceController.php
│   ├── ReferralController.php
│   ├── ResponseController.php
│   ├── FileController.php
│   └── ReportController.php
├── Models/
│   ├── CorrespondenceModel.php
│   ├── ReferralModel.php
│   ├── ResponseModel.php
│   ├── FileModel.php
│   ├── UserModel.php
│   └── AuditModel.php
├── Services/
│   ├── NumberingService.php
│   ├── FileUploadService.php
│   ├── NotificationService.php
│   ├── StampService.php
│   └── WorkflowService.php
├── Libraries/
│   ├── PdfProcessor.php
│   └── DocumentValidator.php
└── Views/
    ├── correspondence/
    ├── referral/
    ├── response/
    └── reports/
```

### B. Numbering Service Implementation

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

### C. Referral Chain Implementation

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

### D. Frontend Components (Bootstrap)

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

### A. Access Control
- Role-based permissions (RBAC)
- Department-level restrictions
- Document classification levels

### B. Data Security
- Encrypted file storage
- Audit trail for all actions
- Session management
- CSRF protection (CI4 built-in)

### C. File Integrity
- Checksum verification
- Digital signature validation
- Watermarking for sensitive documents

### D. Compliance
- Data retention policies
- GDPR compliance features
- Regular security audits

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

## Conclusion

This system design provides a robust, scalable solution for correspondence management with:
- **Complete tracking** of document lifecycle
- **Flexible referral chains** with unlimited depth
- **Linked document system** for responses
- **Comprehensive audit trail**
- **Security and compliance** features

The modular architecture allows for easy enhancement and scaling as requirements grow.