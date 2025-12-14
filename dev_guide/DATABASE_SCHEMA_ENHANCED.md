# Enhanced Database Schema - Correspondence Management System

## Schema Overview (with Gap Analysis Enhancements)

This document provides a visual representation of the enhanced database schema incorporating all fields from the Excel tracking system gap analysis.

---

## Core Tables Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         ORGANIZATIONS                                │
│  - id (PK)                                                           │
│  - org_code (UNIQUE)                                                 │
│  - org_name                                                          │
│  - org_logo                                                          │
│  - status                                                            │
│  - created_by, updated_by, deleted_by                                │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             │ 1:N
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                            GROUPS                                    │
│  - id (PK)                                                           │
│  - group_code (UNIQUE)                                               │
│  - group_name                                                        │
│  - organization_id (FK)                                              │
│  - parent_id (FK - self-referencing)                                 │
│  - status                                                            │
│  - created_by, updated_by, deleted_by                                │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             │ 1:N
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                            USERS                                     │
│  - id (PK)                                                           │
│  - name, email (UNIQUE), password                                    │
│  - organization_id (FK)                                              │
│  - group_id (FK)                                                     │
│  - position                                                          │
│  - signature_filepath, stamp_filepath                                │
│  - is_admin, is_supervisor, is_front_desk                            │
│  - status                                                            │
│  - created_by, updated_by, deleted_by                                │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             │ Registers/Refers
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      CORRESPONDENCES (Enhanced)                      │
│  ═══════════════════════════════════════════════════════════════    │
│  CORE FIELDS:                                                        │
│  - id (PK)                                                           │
│  - correspondence_number (UNIQUE)                                    │
│  - reference_number                                                  │
│  - subject                                                           │
│                                                                      │
│  DIRECTION & TYPE (NEW/Enhanced):                                    │
│  - correspondence_type (LETTER, EMAIL, FAX, MEMO, CIRCULAR,         │
│                         PHONE, WHATSAPP, SMS, etc.)                  │
│  - correspondence_direction (INWARD, OUTWARD, INTERNAL) ← NEW       │
│                                                                      │
│  DATE FIELDS (Enhanced):                                             │
│  - date_received                                                     │
│  - original_date ← NEW (date on document)                           │
│  - date_sent ← NEW (for outward)                                    │
│                                                                      │
│  SENDER INFO (Inward):                                               │
│  - sender_name, sender_organization                                  │
│  - sender_address, sender_contact                                    │
│                                                                      │
│  RECIPIENT INFO (Outward) ← NEW:                                     │
│  - recipient_name, recipient_organization                            │
│  - recipient_address                                                 │
│  - dispatch_method                                                   │
│                                                                      │
│  ORGANIZATION CONTEXT ← NEW:                                         │
│  - department (for dept-based numbering)                             │
│  - organization_id (FK)                                              │
│                                                                      │
│  FOLLOW-UP ← NEW:                                                    │
│  - follow_up_date                                                    │
│  - follow_up_completed                                               │
│                                                                      │
│  FILING & ARCHIVE ← NEW:                                             │
│  - filing_reference (physical location)                              │
│  - archive_location                                                  │
│  - archive_date                                                      │
│                                                                      │
│  STATUS & METADATA:                                                  │
│  - priority (LOW, NORMAL, HIGH, URGENT)                              │
│  - status (REGISTERED, REFERRED, IN_PROCESS, etc.)                   │
│  - registered_by (FK → users)                                        │
│  - registration_date                                                 │
│                                                                      │
│  LINKING:                                                            │
│  - parent_correspondence_id (FK - self)                              │
│  - is_linked, linked_type                                            │
│  - remarks                                                           │
└────────────┬────────────────────────────────────┬────────────────────┘
             │                                    │
             │ 1:N                                │ 1:N
             ▼                                    ▼
┌──────────────────────────┐      ┌──────────────────────────────────┐
│    FILES                 │      │    REFERRALS (Enhanced)          │
│  - id (PK)               │      │  - id (PK)                       │
│  - file_number (UNIQUE)  │      │  - referral_number (UNIQUE)      │
│  - correspondence_id (FK)│      │  - correspondence_id (FK)        │
│  - file_type             │      │  - referred_from (FK → users)    │
│  - file_name             │      │  - referred_to (FK → users)      │
│  - file_path             │      │  - referral_date                 │
│  - file_size             │      │  - due_date                      │
│  - mime_type             │      │                                  │
│  - uploaded_by (FK)      │      │  ACTION FIELDS ← NEW:            │
│  - is_stamped            │      │  - action_required (APPROVAL,    │
│  - is_signed             │      │    REVIEW, REPLY, RECORD, etc.)  │
│  - stamp_data (JSON)     │      │  - action_deadline               │
│  - checksum              │      │                                  │
└──────────────────────────┘      │  - priority                      │
                                  │  - referral_remarks              │
                                  │  - status (PENDING, etc.)        │
                                  │  - acknowledged_date             │
                                  │  - completed_date                │
                                  │  - stamp_signature_data (JSON)   │
                                  └────────────┬─────────────────────┘
                                               │
                                               │ 1:N
                                               ▼
                                  ┌──────────────────────────────────┐
                                  │    RESPONSES (Enhanced)          │
                                  │  - id (PK)                       │
                                  │  - response_number (UNIQUE)      │
                                  │  - correspondence_id (FK)        │
                                  │  - referral_id (FK)              │
                                  │  - responded_by (FK → users)     │
                                  │  - response_type                 │
                                  │                                  │
                                  │  ACTION TRACKING ← NEW:          │
                                  │  - action_category (REPLIED,     │
                                  │    APPROVED, REJECTED, etc.)     │
                                  │  - action_taken                  │
                                  │  - action_details                │
                                  │  - remarks                       │
                                  │                                  │
                                  │  FOLLOW-UP ← NEW:                │
                                  │  - follow_up_required            │
                                  │  - follow_up_date                │
                                  │                                  │
                                  │  - response_date                 │
                                  │  - file_id (FK → files)          │
                                  │  - is_final_response             │
                                  └──────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                    FOLLOW_UPS (NEW TABLE)                            │
│  - id (PK)                                                           │
│  - correspondence_id (FK)                                            │
│  - follow_up_date                                                    │
│  - follow_up_reason                                                  │
│  - assigned_to (FK → users)                                          │
│  - status (PENDING, COMPLETED, CANCELLED, OVERDUE)                   │
│  - completed_date                                                    │
│  - completed_by (FK → users)                                         │
│  - notes                                                             │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│              NUMBERING_SEQUENCES (Enhanced)                          │
│  - id (PK)                                                           │
│  - sequence_type                                                     │
│  - prefix                                                            │
│  - department ← NEW (for dept-based numbering)                      │
│  - current_number                                                    │
│  - year                                                              │
│  - format_pattern (e.g., {DEPT}-{YEAR}/{NUMBER:03d})                │
│  - last_reset_date                                                   │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                    NOTIFICATIONS (Enhanced)                          │
│  - id (PK)                                                           │
│  - user_id (FK)                                                      │
│  - correspondence_id (FK)                                            │
│  - referral_id (FK)                                                  │
│  - follow_up_id (FK) ← NEW                                          │
│  - type (NEW_REFERRAL, RESPONSE_RECEIVED, DUE_DATE_REMINDER,       │
│          FOLLOW_UP_REMINDER, OVERDUE_ACTION) ← Enhanced             │
│  - title, message                                                    │
│  - is_read, read_at                                                  │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                        AUDIT_TRAIL                                   │
│  - id (PK)                                                           │
│  - correspondence_id (FK)                                            │
│  - user_id (FK)                                                      │
│  - action                                                            │
│  - action_details (JSON)                                             │
│  - ip_address                                                        │
│  - user_agent                                                        │
│  - created_at                                                        │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Key Enhancements Summary

### 🆕 New Tables
1. **follow_ups** - Dedicated follow-up management

### 📊 Enhanced Tables

#### correspondences
- ✓ correspondence_direction (INWARD/OUTWARD/INTERNAL)
- ✓ original_date
- ✓ date_sent
- ✓ recipient fields (name, organization, address)
- ✓ dispatch_method
- ✓ department
- ✓ organization_id
- ✓ follow_up_date, follow_up_completed
- ✓ filing_reference, archive_location, archive_date
- ✓ Expanded correspondence_type enum

#### referrals
- ✓ action_required (APPROVAL, REVIEW, REPLY, etc.)
- ✓ action_deadline

#### responses
- ✓ action_category (REPLIED, APPROVED, REJECTED, etc.)
- ✓ action_details
- ✓ follow_up_required, follow_up_date

#### numbering_sequences
- ✓ department (for dept-based numbering)

#### notifications
- ✓ follow_up_id
- ✓ Enhanced notification types

---

## Excel Field Coverage

| Excel Field | Database Location | Status |
|-------------|-------------------|--------|
| REF NO | correspondences.correspondence_number | ✓ |
| CORRESPONDENCE TYPE | correspondences.correspondence_direction | ✓ NEW |
| DATE RECEIVED | correspondences.date_received | ✓ |
| ORIGINAL DATE | correspondences.original_date | ✓ NEW |
| SUBJECT | correspondences.subject | ✓ |
| TYPE | correspondences.correspondence_type | ✓ Enhanced |
| SENDER NAME | correspondences.sender_name | ✓ |
| SENDER ADDRESS | correspondences.sender_organization/address | ✓ |
| REFER TO | referrals.referred_to | ✓ |
| REFER DATE | referrals.referral_date | ✓ |
| RESPONSIBLE OFFICER | referrals.referred_to | ✓ |
| STATUS | correspondences.status | ✓ |
| ACTION REQUIRED | referrals.action_required | ✓ NEW |
| DATE ACTIONED | referrals.completed_date | ✓ |
| FOLLOW-UP DATE | correspondences.follow_up_date, follow_ups | ✓ NEW |
| FILED/ARCHIVED | correspondences.filing_reference | ✓ NEW |

**Coverage: 16/16 (100%)**

---

## Indexes for Performance

### correspondences
- idx_corr_number (correspondence_number)
- idx_status (status)
- idx_date (date_received)
- idx_direction (correspondence_direction)
- idx_department (department)
- idx_follow_up (follow_up_date, follow_up_completed)
- idx_filing (filing_reference)

### referrals
- idx_correspondence_ref (correspondence_id)
- idx_referred_to (referred_to)
- idx_status_ref (status)
- idx_action_required (action_required)
- idx_due_date (due_date)

### responses
- idx_correspondence_resp (correspondence_id)
- idx_referral (referral_id)
- idx_action_category (action_category)
- idx_follow_up (follow_up_required, follow_up_date)

### follow_ups
- idx_follow_date (follow_up_date)
- idx_status_follow (status)
- idx_assigned (assigned_to)

---

## Foreign Key Relationships

```
organizations (1) ──→ (N) groups
organizations (1) ──→ (N) users
organizations (1) ──→ (N) correspondences

groups (1) ──→ (N) users
groups (1) ──→ (N) groups (self-referencing)

users (1) ──→ (N) correspondences (registered_by)
users (1) ──→ (N) referrals (referred_from, referred_to)
users (1) ──→ (N) responses (responded_by)
users (1) ──→ (N) follow_ups (assigned_to, completed_by)
users (1) ──→ (N) files (uploaded_by)

correspondences (1) ──→ (N) correspondences (parent_correspondence_id)
correspondences (1) ──→ (N) files
correspondences (1) ──→ (N) referrals
correspondences (1) ──→ (N) responses
correspondences (1) ──→ (N) follow_ups
correspondences (1) ──→ (N) audit_trail

referrals (1) ──→ (N) responses

files (1) ──→ (N) responses (file_id)

follow_ups (1) ──→ (N) notifications
```

---

## Data Flow Example

### Inward Correspondence Flow:
```
1. Front Desk receives letter
   ↓
2. Create correspondence record:
   - correspondence_direction = 'INWARD'
   - correspondence_type = 'LETTER'
   - date_received = today
   - original_date = date on letter
   - sender_name, sender_organization
   - department = 'HRM'
   - filing_reference = 'P/F-FKupiaw/F-HRM'
   ↓
3. System generates: HRM-2025/001
   ↓
4. Create referral:
   - referred_to = Officer A
   - action_required = 'REVIEW'
   - action_deadline = +7 days
   ↓
5. Create notification for Officer A
   ↓
6. Officer A responds:
   - action_category = 'REVIEWED'
   - action_details = "Approved for processing"
   - follow_up_required = true
   - follow_up_date = +14 days
   ↓
7. Create follow_up record
   ↓
8. System sends follow-up reminder on due date
```

This enhanced schema provides complete coverage of all Excel tracking fields while adding significant improvements for digital workflow management.

