# UUID v7 Migration Guide for CodeIgniter 4

## Table of Contents
1. [Executive Summary](#executive-summary)
2. [UUID v7 Overview](#uuid-v7-overview)
3. [Database Migration Strategy](#database-migration-strategy)
4. [UUID Generation Implementation](#uuid-generation-implementation)
5. [CodeIgniter 4 Application Updates](#codeigniter-4-application-updates)
6. [Migration Execution Plan](#migration-execution-plan)
7. [Testing & Verification](#testing--verification)
8. [Performance Considerations](#performance-considerations)
9. [Troubleshooting](#troubleshooting)

---

## Executive Summary

This document provides a complete implementation plan for migrating the CORRES Correspondence Management System from auto-increment integer primary keys to UUID v7 identifiers. This is a **breaking change** with no backward compatibility.

### Scope
- **7 database tables** to migrate
- **All primary keys** (id columns)
- **All foreign keys** (organization_id, group_id, user_id, etc.)
- **All audit trail fields** (created_by, updated_by, deleted_by, registered_by)
- **Application code** (Models, Controllers, Views, Routes)

### Timeline Estimate
- **Database preparation**: 2-4 hours
- **Code updates**: 6-8 hours
- **Testing**: 4-6 hours
- **Total**: 12-18 hours

---

## UUID v7 Overview

### What is UUID v7?

UUID v7 (RFC 9562, 2024) is the latest UUID standard that combines:
- **Time-ordered**: First 48 bits are Unix timestamp in milliseconds
- **Monotonic**: Incrementing sequence for same timestamp
- **Random**: Remaining bits are random for uniqueness
- **Sortable**: Natural chronological ordering like auto-increment
- **Globally unique**: No coordination needed across systems

### Format
```
01234567-89ab-7def-8901-234567890abc
├──────┬──┬─┬──┬──┬───────────────┘
│      │  │ │  │  └─ Random bits (48 bits)
│      │  │ │  └──── Random bits (12 bits)
│      │  │ └─────── Version 7 (4 bits)
│      │  └────────── Timestamp ms (12 bits)
│      └───────────── Timestamp ms (16 bits)
└──────────────────── Timestamp ms (32 bits)
```

### Why UUID v7?

✅ **Better than UUID v4**: Time-ordered, better for database indexes
✅ **Better than UUID v1**: No MAC address exposure, better privacy
✅ **Better than ULID**: Standard RFC format, wider library support
✅ **Better than auto-increment**: Distributed systems ready, no collision risk

### Storage Decision: BINARY(16) vs CHAR(36)

| Aspect | BINARY(16) | CHAR(36) |
|--------|------------|----------|
| **Storage** | 16 bytes | 36 bytes (2.25x larger) |
| **Index Size** | Smaller, faster | Larger, slower |
| **Readability** | Requires conversion | Human-readable |
| **Performance** | ✅ Better | ❌ Slower |
| **Debugging** | Harder | ✅ Easier |
| **Recommendation** | ✅ **Use this** | Use for debugging only |

**Decision**: Use **BINARY(16)** for production with helper functions for display.

---

## Database Migration Strategy

### Phase 1: Pre-Migration Preparation

#### Step 1.1: Backup Database
```bash
# Full database backup
mysqldump -u root -p corres_db > corres_db_backup_$(date +%Y%m%d_%H%M%S).sql

# Verify backup
mysql -u root -p corres_db < corres_db_backup_YYYYMMDD_HHMMSS.sql
```

#### Step 1.2: Analyze Current Schema

**Tables to Migrate** (in dependency order):
1. `dakoii_users` (no dependencies)
2. `organizations` (depends on dakoii_users for audit)
3. `groups` (depends on organizations, self-referential)
4. `users` (depends on organizations, groups)
5. `correspondence_types` (minimal dependencies)
6. `correspondences` (depends on users, organizations, self-referential)
7. `correspondence_links` (depends on correspondences)

**Foreign Key Relationships**:
```
dakoii_users (root)
  └─► organizations.created_by/updated_by/deleted_by
       ├─► groups.organization_id
       │    ├─► groups.created_by/updated_by/deleted_by
       │    ├─► groups.parent_id (self-referential)
       │    └─► users.group_id
       │         ├─► users.created_by/updated_by/deleted_by
       │         └─► correspondences.registered_by
       │              ├─► correspondences.organization_id
       │              ├─► correspondences.created_by/updated_by/deleted_by
       │              ├─► correspondences.parent_correspondence_id (self)
       │              └─► correspondence_links
       └─► correspondences.organization_id
```

### Phase 2: Migration Execution Strategy

**Option A: Clean Database (Recommended for Development)**
- Drop all tables
- Recreate with UUID v7 schema
- Re-seed with new data

**Option B: Data Preservation (Production with Existing Data)**
- Create temporary UUID mapping tables
- Add new UUID columns alongside integer IDs
- Populate UUID columns
- Update all foreign keys
- Drop old integer columns
- Rename UUID columns to replace old IDs

**This guide uses Option A** (clean migration). For Option B, see [Data Preservation Strategy](#data-preservation-strategy).

### Phase 3: New Schema Definition

#### 3.1: Helper Functions for MySQL

```sql
-- Create UUID v7 generation function (MySQL 8.0+)
-- Note: This is a fallback. Primary generation should be in PHP.

DELIMITER $$

CREATE FUNCTION uuid_v7() RETURNS BINARY(16)
DETERMINISTIC
NO SQL
BEGIN
    DECLARE unix_ts_ms BIGINT;
    DECLARE rand_a BINARY(2);
    DECLARE rand_b BINARY(8);

    SET unix_ts_ms = UNIX_TIMESTAMP() * 1000;
    SET rand_a = UNHEX(LPAD(HEX(FLOOR(RAND() * 65535)), 4, '0'));
    SET rand_b = UNHEX(LPAD(HEX(FLOOR(RAND() * 18446744073709551615)), 16, '0'));

    RETURN CONCAT(
        UNHEX(LPAD(HEX(unix_ts_ms >> 16), 8, '0')),
        UNHEX(LPAD(HEX(unix_ts_ms & 0xFFFF), 4, '0')),
        UNHEX('7'), -- Version 7
        rand_a,
        rand_b
    );
END$$

DELIMITER ;

-- Conversion helper functions
DELIMITER $$

CREATE FUNCTION uuid_to_bin(uuid CHAR(36)) RETURNS BINARY(16)
DETERMINISTIC
NO SQL
BEGIN
    RETURN UNHEX(REPLACE(uuid, '-', ''));
END$$

CREATE FUNCTION bin_to_uuid(bin BINARY(16)) RETURNS CHAR(36)
DETERMINISTIC
NO SQL
BEGIN
    RETURN LOWER(CONCAT(
        HEX(SUBSTRING(bin, 1, 4)), '-',
        HEX(SUBSTRING(bin, 5, 2)), '-',
        HEX(SUBSTRING(bin, 7, 2)), '-',
        HEX(SUBSTRING(bin, 9, 2)), '-',
        HEX(SUBSTRING(bin, 11, 6))
    ));
END$$

DELIMITER ;
```

#### 3.2: Table Migration Scripts

##### Migration 1: dakoii_users

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertDakoiiUsersToUUID extends Migration
{
    public function up()
    {
        // Drop existing table
        $this->forge->dropTable('dakoii_users', true);

        // Recreate with UUID
        $this->forge->addField([
            'id' => [
                'type'       => 'BINARY',
                'constraint' => 16,
                'null'       => false,
                'comment'    => 'UUID v7 primary key',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('dakoii_users');
    }

    public function down()
    {
        $this->forge->dropTable('dakoii_users', true);
    }
}
```

##### Migration 2: organizations

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertOrganizationsToUUID extends Migration
{
    public function up()
    {
        $this->forge->dropTable('organizations', true);

        $this->forge->addField([
            'id' => [
                'type'       => 'BINARY',
                'constraint' => 16,
                'null'       => false,
                'comment'    => 'UUID v7 primary key',
            ],
            'org_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '4',
                'null'       => false,
                'comment'    => 'Unique 4-digit organization code',
            ],
            'org_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'org_logo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'null'       => false,
            ],
            'created_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
                'comment' => 'UUID of dakoii_user who created',
            ],
            'updated_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
                'comment' => 'UUID of dakoii_user who updated',
            ],
            'deleted_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
                'comment' => 'UUID of dakoii_user who deleted',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('org_code');
        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');
        $this->forge->addKey('deleted_by');

        // Foreign keys to dakoii_users
        $this->forge->addForeignKey('created_by', 'dakoii_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'dakoii_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('deleted_by', 'dakoii_users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('organizations');
    }

    public function down()
    {
        $this->forge->dropTable('organizations', true);
    }
}
```

##### Migration 3: groups

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertGroupsToUUID extends Migration
{
    public function up()
    {
        $this->forge->dropTable('groups', true);

        $this->forge->addField([
            'id' => [
                'type'       => 'BINARY',
                'constraint' => 16,
                'null'       => false,
                'comment'    => 'UUID v7 primary key',
            ],
            'group_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => false,
            ],
            'group_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'organization_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => false,
                'comment' => 'UUID of organization',
            ],
            'parent_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
                'comment' => 'UUID of parent group (self-referential)',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'null'       => false,
            ],
            'created_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'updated_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'deleted_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('group_code');
        $this->forge->addKey('organization_id');
        $this->forge->addKey('parent_id');

        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('parent_id', 'groups', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'dakoii_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'dakoii_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('deleted_by', 'dakoii_users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('groups');
    }

    public function down()
    {
        $this->forge->dropTable('groups', true);
    }
}
```

##### Migration 4: users

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertUsersToUUID extends Migration
{
    public function up()
    {
        $this->forge->dropTable('users', true);

        $this->forge->addField([
            'id' => [
                'type'       => 'BINARY',
                'constraint' => 16,
                'null'       => false,
                'comment'    => 'UUID v7 primary key',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'organization_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => false,
            ],
            'group_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'supervisor', 'front_desk'],
                'default'    => 'front_desk',
                'null'       => false,
            ],
            'signature_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'stamp_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'null'       => false,
            ],
            'created_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'updated_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'deleted_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('organization_id');
        $this->forge->addKey('group_id');

        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('group_id', 'groups', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'dakoii_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'dakoii_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('deleted_by', 'dakoii_users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
```

##### Migration 5: correspondences

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertCorrespondencesToUUID extends Migration
{
    public function up()
    {
        $this->forge->dropTable('correspondences', true);

        $this->forge->addField([
            'id' => [
                'type'       => 'BINARY',
                'constraint' => 16,
                'null'       => false,
                'comment'    => 'UUID v7 primary key',
            ],
            'correspondence_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'reference_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
                'null'       => false,
            ],
            'correspondence_type' => [
                'type'       => 'ENUM',
                'constraint' => ['LETTER', 'EMAIL', 'FAX', 'MEMO', 'CIRCULAR', 'PHONE', 'MEETING_MINUTES', 'REPORT', 'WHATSAPP', 'SMS', 'OTHER'],
                'default'    => 'LETTER',
                'null'       => false,
            ],
            'correspondence_direction' => [
                'type'       => 'ENUM',
                'constraint' => ['INWARD', 'OUTWARD', 'INTERNAL'],
                'default'    => 'INWARD',
                'null'       => false,
            ],
            'date_received' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'original_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'date_sent' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'sender_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'sender_organization' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'sender_address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sender_contact' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'recipient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'recipient_organization' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'recipient_address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'dispatch_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['LOW', 'NORMAL', 'HIGH', 'URGENT'],
                'default'    => 'NORMAL',
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['REGISTERED', 'REFERRED', 'IN_PROCESS', 'ACTIONED', 'COMPLETED', 'ARCHIVED'],
                'default'    => 'REGISTERED',
                'null'       => false,
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'organization_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'group_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'filing_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'archive_location' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'archive_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'registered_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'registration_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'parent_correspondence_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'is_linked' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'linked_type' => [
                'type'       => 'ENUM',
                'constraint' => ['RESPONSE', 'FOLLOW_UP', 'RELATED'],
                'null'       => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'updated_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'deleted_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('correspondence_number');
        $this->forge->addKey('organization_id');
        $this->forge->addKey('group_id');
        $this->forge->addKey('registered_by');
        $this->forge->addKey('parent_correspondence_id');
        $this->forge->addKey('status');
        $this->forge->addKey('date_received');
        $this->forge->addKey('correspondence_direction');
        $this->forge->addKey('department');

        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('group_id', 'groups', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('registered_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('parent_correspondence_id', 'correspondences', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('deleted_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('correspondences');
    }

    public function down()
    {
        $this->forge->dropTable('correspondences', true);
    }
}
```

---

## UUID Generation Implementation

### PHP Library Recommendation

**Recommended**: `ramsey/uuid` (Most mature, RFC 9562 compliant)

```bash
composer require ramsey/uuid
```

**Alternative**: `symfony/uid` (Good if already using Symfony components)

```bash
composer require symfony/uid
```

### UUID Helper Class

Create `app/Helpers/UuidHelper.php`:

```php
<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidHelper
{
    /**
     * Generate a new UUID v7
     *
     * @return string UUID v7 in string format
     */
    public static function generate(): string
    {
        return Uuid::uuid7()->toString();
    }

    /**
     * Generate UUID v7 as binary for database storage
     *
     * @return string Binary representation (16 bytes)
     */
    public static function generateBinary(): string
    {
        return Uuid::uuid7()->getBytes();
    }

    /**
     * Convert UUID string to binary
     *
     * @param string $uuid UUID in string format
     * @return string Binary representation
     */
    public static function toBinary(string $uuid): string
    {
        return Uuid::fromString($uuid)->getBytes();
    }

    /**
     * Convert binary UUID to string
     *
     * @param string $binary Binary UUID
     * @return string UUID in string format
     */
    public static function toString(string $binary): string
    {
        return Uuid::fromBytes($binary)->toString();
    }

    /**
     * Validate UUID format
     *
     * @param string $uuid UUID to validate
     * @return bool
     */
    public static function isValid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }

    /**
     * Check if UUID is version 7
     *
     * @param string $uuid UUID to check
     * @return bool
     */
    public static function isUuidV7(string $uuid): bool
    {
        try {
            $uuidObj = Uuid::fromString($uuid);
            return $uuidObj->getVersion() === 7;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extract timestamp from UUID v7
     *
     * @param string $uuid UUID v7
     * @return int Unix timestamp in milliseconds
     */
    public static function getTimestamp(string $uuid): int
    {
        $uuidObj = Uuid::fromString($uuid);
        if ($uuidObj->getVersion() !== 7) {
            throw new \InvalidArgumentException('UUID is not version 7');
        }

        // Extract first 48 bits as timestamp
        $hex = str_replace('-', '', $uuid);
        $timestampHex = substr($hex, 0, 12);
        return hexdec($timestampHex);
    }
}
```

### Load Helper Globally

Add to `app/Config/Autoload.php`:

```php
public $helpers = ['uuid'];
```

Create `app/Helpers/uuid_helper.php`:

```php
<?php

use App\Helpers\UuidHelper;

if (!function_exists('uuid_generate')) {
    function uuid_generate(): string
    {
        return UuidHelper::generate();
    }
}

if (!function_exists('uuid_binary')) {
    function uuid_binary(): string
    {
        return UuidHelper::generateBinary();
    }
}

if (!function_exists('uuid_to_bin')) {
    function uuid_to_bin(string $uuid): string
    {
        return UuidHelper::toBinary($uuid);
    }
}

if (!function_exists('bin_to_uuid')) {
    function bin_to_uuid(string $binary): string
    {
        return UuidHelper::toString($binary);
    }
}
```

---

## CodeIgniter 4 Application Updates

### Model Updates

#### Base Model Pattern

Create `app/Models/UuidModel.php`:

```php
<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Helpers\UuidHelper;

class UuidModel extends Model
{
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false; // Critical!
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateUuid', 'setCreatedBy'];
    protected $beforeUpdate = ['setUpdatedBy'];
    protected $beforeDelete = ['setDeletedBy'];
    protected $afterFind = ['convertUuidToString'];

    /**
     * Generate UUID v7 before insert
     */
    protected function generateUuid(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = UuidHelper::generateBinary();
        } elseif (is_string($data['data']['id']) && strlen($data['data']['id']) === 36) {
            // Convert string UUID to binary if provided
            $data['data']['id'] = UuidHelper::toBinary($data['data']['id']);
        }

        return $data;
    }

    /**
     * Convert binary UUIDs to strings after retrieval
     */
    protected function convertUuidToString(array $data): array
    {
        if (isset($data['data'])) {
            // Single record
            $data['data'] = $this->convertRecordUuids($data['data']);
        } elseif (isset($data['data'][0]) && is_array($data['data'][0])) {
            // Multiple records
            foreach ($data['data'] as &$record) {
                $record = $this->convertRecordUuids($record);
            }
        }

        return $data;
    }

    /**
     * Convert all UUID fields in a record from binary to string
     */
    protected function convertRecordUuids(array $record): array
    {
        $uuidFields = $this->getUuidFields();

        foreach ($uuidFields as $field) {
            if (isset($record[$field]) && $record[$field] !== null) {
                try {
                    $record[$field] = UuidHelper::toString($record[$field]);
                } catch (\Exception $e) {
                    // Already a string or invalid, leave as is
                }
            }
        }

        return $record;
    }

    /**
     * Get list of UUID fields for this model
     * Override in child models if needed
     */
    protected function getUuidFields(): array
    {
        return [
            'id',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    /**
     * Override find to handle UUID parameter
     */
    public function find($id = null)
    {
        if ($id !== null && is_string($id) && strlen($id) === 36) {
            $id = UuidHelper::toBinary($id);
        }

        return parent::find($id);
    }

    /**
     * Override delete to handle UUID parameter
     */
    public function delete($id = null, bool $purge = false)
    {
        if ($id !== null && is_string($id) && strlen($id) === 36) {
            $id = UuidHelper::toBinary($id);
        }

        return parent::delete($id, $purge);
    }

    /**
     * Set created_by with UUID
     */
    protected function setCreatedBy(array $data): array
    {
        if (!isset($data['data']['created_by'])) {
            $session = session();
            $userIdKey = $this->getUserIdSessionKey();

            if ($session->has($userIdKey)) {
                $userId = $session->get($userIdKey);
                // Convert to binary if string UUID
                if (is_string($userId) && strlen($userId) === 36) {
                    $userId = UuidHelper::toBinary($userId);
                }
                $data['data']['created_by'] = $userId;
            }
        }
        return $data;
    }

    /**
     * Set updated_by with UUID
     */
    protected function setUpdatedBy(array $data): array
    {
        if (!isset($data['data']['updated_by'])) {
            $session = session();
            $userIdKey = $this->getUserIdSessionKey();

            if ($session->has($userIdKey)) {
                $userId = $session->get($userIdKey);
                if (is_string($userId) && strlen($userId) === 36) {
                    $userId = UuidHelper::toBinary($userId);
                }
                $data['data']['updated_by'] = $userId;
            }
        }
        return $data;
    }

    /**
     * Set deleted_by with UUID
     */
    protected function setDeletedBy(array $data): array
    {
        $session = session();
        $userIdKey = $this->getUserIdSessionKey();

        if ($session->has($userIdKey)) {
            $userId = $session->get($userIdKey);
            if (is_string($userId) && strlen($userId) === 36) {
                $userId = UuidHelper::toBinary($userId);
            }
            $this->update($data['id'], ['deleted_by' => $userId]);
        }
        return $data;
    }

    /**
     * Get session key for user ID
     * Override in child models if needed
     */
    protected function getUserIdSessionKey(): string
    {
        return 'dakoii_user_id';
    }
}
```

#### Updated OrganizationModel

```php
<?php

namespace App\Models;

class OrganizationModel extends UuidModel
{
    protected $table = 'organizations';
    protected $allowedFields = [
        'org_code',
        'org_name',
        'org_logo',
        'description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $validationRules = [
        'org_code' => 'required|exact_length[4]|is_unique[organizations.org_code,id,{id}]|numeric',
        'org_name' => 'required|max_length[255]',
        'org_logo' => 'permit_empty|max_length[255]',
        'description' => 'permit_empty',
        'status' => 'permit_empty|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'org_code' => [
            'required' => 'Organization code is required',
            'exact_length' => 'Organization code must be exactly 4 digits',
            'is_unique' => 'This organization code already exists',
            'numeric' => 'Organization code must contain only numbers',
        ],
        'org_name' => [
            'required' => 'Organization name is required',
            'max_length' => 'Organization name cannot exceed 255 characters',
        ],
    ];

    /**
     * Get UUID fields for this model
     */
    protected function getUuidFields(): array
    {
        return [
            'id',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    /**
     * Generate next organization code (unchanged logic)
     */
    public function generateOrgCode(): string
    {
        $lastOrg = $this->select('org_code')
            ->orderBy('org_code', 'DESC')
            ->withDeleted()
            ->first();

        if ($lastOrg) {
            $lastCode = (int) $lastOrg['org_code'];
            $nextCode = $lastCode + 1;
        } else {
            $nextCode = 1100;
        }

        return str_pad($nextCode, 4, '0', STR_PAD_LEFT);
    }
}
```

#### Updated GroupModel

```php
<?php

namespace App\Models;

class GroupModel extends UuidModel
{
    protected $table = 'groups';
    protected $allowedFields = [
        'group_code',
        'group_name',
        'organization_id',
        'parent_id',
        'description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get UUID fields including foreign keys
     */
    protected function getUuidFields(): array
    {
        return [
            'id',
            'organization_id',
            'parent_id',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    /**
     * Before insert - convert foreign key UUIDs to binary
     */
    protected function beforeInsert(array $data): array
    {
        $data = parent::beforeInsert($data);
        $data = $this->convertForeignKeysToBinary($data);
        return $data;
    }

    /**
     * Before update - convert foreign key UUIDs to binary
     */
    protected function beforeUpdate(array $data): array
    {
        $data = parent::beforeUpdate($data);
        $data = $this->convertForeignKeysToBinary($data);
        return $data;
    }

    /**
     * Convert foreign key string UUIDs to binary
     */
    private function convertForeignKeysToBinary(array $data): array
    {
        $foreignKeys = ['organization_id', 'parent_id'];

        foreach ($foreignKeys as $key) {
            if (isset($data['data'][$key]) && is_string($data['data'][$key]) && strlen($data['data'][$key]) === 36) {
                $data['data'][$key] = \App\Helpers\UuidHelper::toBinary($data['data'][$key]);
            }
        }

        return $data;
    }
}
```

### Controller Updates

#### Organizations Controller Example

```php
<?php

namespace App\Controllers;

use App\Models\OrganizationModel;
use App\Helpers\UuidHelper;

class Organizations extends BaseController
{
    protected $organizationModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
    }

    /**
     * Display single organization
     * Route: GET /dakoii/organizations/{uuid}
     */
    public function show($uuid)
    {
        // Validate UUID format
        if (!UuidHelper::isValid($uuid)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid UUID format'
            ])->setStatusCode(400);
        }

        // Find uses string UUID, model handles conversion
        $organization = $this->organizationModel->find($uuid);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found');
        }

        return view('dakoii/dakoii_organizations_view', [
            'organization' => $organization
        ]);
    }

    /**
     * Create new organization
     * Route: POST /dakoii/organizations
     */
    public function create()
    {
        $data = [
            'org_code' => $this->request->getPost('org_code'),
            'org_name' => $this->request->getPost('org_name'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status') ?? 'active',
        ];

        // Handle logo upload
        $logo = $this->request->getFile('org_logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/organizations/', $newName);
            $data['org_logo'] = 'public/uploads/organizations/' . $newName;
        }

        // Model auto-generates UUID v7
        $result = $this->organizationModel->insert($data);

        if ($result) {
            // $result is the UUID (binary), convert to string for session/display
            $uuidString = UuidHelper::toString($result);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Organization created successfully',
                'data' => [
                    'id' => $uuidString
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create organization',
                'errors' => $this->organizationModel->errors()
            ])->setStatusCode(400);
        }
    }

    /**
     * Update organization
     * Route: PUT /dakoii/organizations/{uuid}
     */
    public function update($uuid)
    {
        if (!UuidHelper::isValid($uuid)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid UUID format'
            ])->setStatusCode(400);
        }

        $organization = $this->organizationModel->find($uuid);
        if (!$organization) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Organization not found'
            ])->setStatusCode(404);
        }

        $data = [
            'org_name' => $this->request->getPost('org_name'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
        ];

        // Model handles UUID conversion
        $result = $this->organizationModel->update($uuid, $data);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Organization updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update organization',
                'errors' => $this->organizationModel->errors()
            ])->setStatusCode(400);
        }
    }

    /**
     * Delete organization
     * Route: DELETE /dakoii/organizations/{uuid}
     */
    public function delete($uuid)
    {
        if (!UuidHelper::isValid($uuid)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid UUID format'
            ])->setStatusCode(400);
        }

        // Soft delete
        $result = $this->organizationModel->delete($uuid);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Organization deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete organization'
            ])->setStatusCode(400);
        }
    }
}
```

### Route Updates

Update `app/Config/Routes.php`:

```php
// UUID pattern: 8-4-4-4-12 hexadecimal format
$uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

$routes->group('dakoii', ['filter' => 'dakoiiauth'], static function ($routes) use ($uuidPattern) {
    $routes->get('dashboard', 'Dakoii::dashboard');
    $routes->post('logout', 'Dakoii::logout');

    // Organizations with UUID routes
    $routes->get('organizations', 'Organizations::index');
    $routes->get('organizations/new', 'Organizations::new');
    $routes->post('organizations', 'Organizations::create');
    $routes->get('organizations/generate-code', 'Organizations::generateCode');
    $routes->get('organizations/(' . $uuidPattern . ')', 'Organizations::show/$1');
    $routes->get('organizations/(' . $uuidPattern . ')/edit', 'Organizations::edit/$1');
    $routes->put('organizations/(' . $uuidPattern . ')', 'Organizations::update/$1');
    $routes->patch('organizations/(' . $uuidPattern . ')', 'Organizations::update/$1');
    $routes->delete('organizations/(' . $uuidPattern . ')', 'Organizations::delete/$1');

    // Groups with UUID routes (nested under organizations)
    $routes->get('organizations/(' . $uuidPattern . ')/groups', 'Groups::index/$1');
    $routes->get('organizations/(' . $uuidPattern . ')/groups/new', 'Groups::new/$1');
    $routes->post('organizations/(' . $uuidPattern . ')/groups', 'Groups::create/$1');
    $routes->get('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')', 'Groups::show/$1/$2');
    $routes->get('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')/edit', 'Groups::edit/$1/$2');
    $routes->put('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')', 'Groups::update/$1/$2');
    $routes->patch('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')', 'Groups::update/$1/$2');
    $routes->delete('organizations/(' . $uuidPattern . ')/groups/(' . $uuidPattern . ')', 'Groups::delete/$1/$2');

    // Users with UUID routes (nested under organizations)
    $routes->get('organizations/(' . $uuidPattern . ')/users', 'Users::index/$1');
    $routes->get('organizations/(' . $uuidPattern . ')/users/new', 'Users::new/$1');
    $routes->post('organizations/(' . $uuidPattern . ')/users', 'Users::create/$1');
    $routes->get('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')', 'Users::show/$1/$2');
    $routes->get('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')/edit', 'Users::edit/$1/$2');
    $routes->put('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')', 'Users::update/$1/$2');
    $routes->patch('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')', 'Users::update/$1/$2');
    $routes->delete('organizations/(' . $uuidPattern . ')/users/(' . $uuidPattern . ')', 'Users::delete/$1/$2');
});
```

### View Updates

Views automatically receive string UUIDs due to `afterFind` callback. Example:

```php
<!-- app/Views/dakoii/dakoii_organizations_view.php -->

<div class="card">
    <div class="card-header">
        <h3><?= esc($organization['org_name']) ?></h3>
        <p class="text-muted">Code: <?= esc($organization['org_code']) ?></p>
        <p class="text-muted small">ID: <?= esc($organization['id']) ?></p>
    </div>
    <div class="card-body">
        <!-- Edit link with UUID -->
        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/edit') ?>"
           class="btn btn-primary">Edit</a>

        <!-- Delete button with UUID -->
        <button onclick="deleteOrganization('<?= esc($organization['id']) ?>')"
                class="btn btn-danger">Delete</button>
    </div>
</div>

<script>
function deleteOrganization(uuid) {
    if (confirm('Are you sure?')) {
        fetch(`/dakoii/organizations/${uuid}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/dakoii/organizations';
            }
        });
    }
}
</script>
```

### Seeder Updates

Update `app/Database/Seeds/DakoiiUserSeeder.php`:

```php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Helpers\UuidHelper;

class DakoiiUserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id'         => UuidHelper::generateBinary(),
            'name'       => 'Free Kenny',
            'username'   => 'fkenny',
            'password'   => password_hash('dakoii', PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('dakoii_users')->insert($data);
    }
}
```

---

## Migration Execution Plan

### Pre-Migration Checklist

- [ ] Backup entire database
- [ ] Backup application code
- [ ] Install `ramsey/uuid` via Composer
- [ ] Create all UUID helper functions
- [ ] Create base UuidModel
- [ ] Review all table relationships
- [ ] Identify all foreign key dependencies
- [ ] Plan downtime window (if production)

### Execution Steps

#### Step 1: Install Dependencies

```bash
cd C:\xampp\htdocs\corres
composer require ramsey/uuid
```

#### Step 2: Create Helper Files

1. Create `app/Helpers/UuidHelper.php` (see above)
2. Create `app/Helpers/uuid_helper.php` (see above)
3. Update `app/Config/Autoload.php`

#### Step 3: Create Base Model

1. Create `app/Models/UuidModel.php` (see above)

#### Step 4: Drop and Recreate Tables

```bash
# WARNING: This deletes all data!

# Drop all tables in reverse dependency order
php spark db:query "SET FOREIGN_KEY_CHECKS = 0"
php spark db:query "DROP TABLE IF EXISTS correspondence_links"
php spark db:query "DROP TABLE IF EXISTS correspondences"
php spark db:query "DROP TABLE IF EXISTS correspondence_types"
php spark db:query "DROP TABLE IF EXISTS users"
php spark db:query "DROP TABLE IF EXISTS groups"
php spark db:query "DROP TABLE IF EXISTS organizations"
php spark db:query "DROP TABLE IF EXISTS dakoii_users"
php spark db:query "DROP TABLE IF EXISTS migrations"
php spark db:query "SET FOREIGN_KEY_CHECKS = 1"
```

#### Step 5: Run UUID Migrations

Create all migration files as shown in [Phase 3](#phase-3-new-schema-definition), then:

```bash
# Run migrations
php spark migrate

# Verify tables
php spark db:query "SHOW TABLES"
```

#### Step 6: Update Models

Update all models to extend `UuidModel`:

```php
// OrganizationModel
class OrganizationModel extends UuidModel { ... }

// GroupModel
class GroupModel extends UuidModel { ... }

// UserModel
class UserModel extends UuidModel { ... }

// CorrespondenceModel
class CorrespondenceModel extends UuidModel { ... }
```

Override `getUuidFields()` in each model to include foreign keys.

#### Step 7: Update Controllers

1. Add UUID validation to show/edit/update/delete methods
2. Remove integer type hints for ID parameters
3. Use `UuidHelper::isValid()` for validation

#### Step 8: Update Routes

Add UUID pattern to all routes (see [Route Updates](#route-updates))

#### Step 9: Update Views

1. Replace integer ID references with UUID strings
2. Update all links/forms to use UUID format
3. Test UI navigation

#### Step 10: Update Authentication Filters

Update session storage to use UUID strings:

```php
// app/Filters/DakoiiAuthFilter.php
public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
{
    if ($authenticated) {
        session()->set([
            'dakoii_logged_in' => true,
            'dakoii_user_id'   => bin_to_uuid($user['id']), // Store as string UUID
            'dakoii_username'  => $user['username'],
            'dakoii_name'      => $user['name'],
        ]);
    }
}
```

#### Step 11: Reseed Database

```bash
php spark db:seed DakoiiUserSeeder
php spark db:seed OrganizationSeeder  # If exists
```

#### Step 12: Test Application

Run through all CRUD operations for each module.

---

## Testing & Verification

### Database Integrity Checks

```sql
-- Check all tables use BINARY(16) for id
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    COLUMN_TYPE
FROM
    INFORMATION_SCHEMA.COLUMNS
WHERE
    TABLE_SCHEMA = 'corres_db'
    AND COLUMN_NAME = 'id';

-- Verify foreign key constraints
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    TABLE_SCHEMA = 'corres_db'
    AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Check for NULL UUIDs (should be none for primary keys)
SELECT 'dakoii_users' as tbl, COUNT(*) as null_count FROM dakoii_users WHERE id IS NULL
UNION ALL
SELECT 'organizations', COUNT(*) FROM organizations WHERE id IS NULL
UNION ALL
SELECT 'groups', COUNT(*) FROM groups WHERE id IS NULL
UNION ALL
SELECT 'users', COUNT(*) FROM users WHERE id IS NULL;

-- Verify UUID v7 format (check version byte = 0x70)
SELECT
    bin_to_uuid(id) as uuid,
    HEX(SUBSTRING(id, 7, 1)) as version_byte
FROM
    organizations
LIMIT 10;
-- version_byte should be '70' (version 7)
```

### Application Testing Checklist

#### Organizations Module
- [ ] List organizations (index)
- [ ] View organization details (show)
- [ ] Create new organization (create)
- [ ] Edit organization (update)
- [ ] Delete organization (soft delete)
- [ ] Generate organization code
- [ ] Upload organization logo
- [ ] Verify audit trail (created_by, updated_by)

#### Groups Module
- [ ] List groups for organization
- [ ] View group details
- [ ] Create new group
- [ ] Edit group
- [ ] Delete group
- [ ] Test parent-child relationship (self-referential FK)
- [ ] Test organization FK constraint
- [ ] Verify audit trail

#### Users Module
- [ ] List users for organization
- [ ] View user details
- [ ] Create new user
- [ ] Edit user
- [ ] Delete user
- [ ] Test organization FK
- [ ] Test group FK
- [ ] Upload signature/stamp files
- [ ] Verify audit trail

#### Correspondences Module
- [ ] List correspondences
- [ ] View correspondence
- [ ] Create correspondence
- [ ] Edit correspondence
- [ ] Delete correspondence
- [ ] Test parent correspondence FK (self-referential)
- [ ] Test organization FK
- [ ] Test registered_by FK
- [ ] Test correspondence linking
- [ ] Verify audit trail

#### Authentication
- [ ] Login with Dakoii credentials
- [ ] Session stores UUID correctly
- [ ] Logout
- [ ] Login with Admin credentials (if different portal)
- [ ] Verify created_by uses correct UUID

#### URL Routing
- [ ] All detail pages load with UUID in URL
- [ ] Edit pages load with UUID
- [ ] Form submissions with UUID work
- [ ] Delete actions with UUID work
- [ ] Invalid UUID format returns 400 error
- [ ] Non-existent UUID returns 404 error

### Performance Testing

```php
// Test script: test_uuid_performance.php

use App\Helpers\UuidHelper;

$iterations = 10000;

// Test UUID generation speed
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    UuidHelper::generateBinary();
}
$end = microtime(true);
echo "Generated {$iterations} UUIDs in " . round($end - $start, 4) . " seconds\n";
echo "Rate: " . round($iterations / ($end - $start)) . " UUIDs/second\n";

// Test conversion speed
$uuid = UuidHelper::generate();
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $binary = UuidHelper::toBinary($uuid);
    $string = UuidHelper::toString($binary);
}
$end = microtime(true);
echo "Converted {$iterations} UUIDs in " . round($end - $start, 4) . " seconds\n";
```

Expected results:
- **Generation**: 50,000+ UUIDs/second
- **Conversion**: 100,000+ conversions/second

### Query Performance Test

```sql
-- Create test data
INSERT INTO organizations (id, org_code, org_name, status, created_at)
SELECT
    uuid_v7(),
    LPAD(1100 + n, 4, '0'),
    CONCAT('Test Org ', n),
    'active',
    NOW()
FROM
    (SELECT @row := @row + 1 as n FROM
     (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) t1,
     (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) t2,
     (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) t3,
     (SELECT @row := 0) t4
     LIMIT 1000) numbers;

-- Test query performance
EXPLAIN SELECT * FROM organizations WHERE id = uuid_to_bin('some-uuid-here');

-- Should show:
-- type: const (best)
-- possible_keys: PRIMARY
-- key: PRIMARY
-- rows: 1
```

---

## Performance Considerations

### Index Strategy

#### Primary Key Index (Automatic)
```sql
-- BINARY(16) primary keys are automatically indexed
-- B-tree index works efficiently for UUID v7 due to time-ordering
```

#### Foreign Key Indexes
```sql
-- Already created in migrations
-- Example:
KEY `organization_id` (`organization_id`)
KEY `group_id` (`group_id`)
KEY `created_by` (`created_by`)
```

#### Composite Indexes (if needed)
```sql
-- For common queries
CREATE INDEX idx_org_status ON organizations(status, created_at);
CREATE INDEX idx_correspondence_date ON correspondences(date_received, status);
```

### Storage Impact

#### Size Comparison

| Database | INT (4 bytes) | UUID BINARY(16) | Increase |
|----------|---------------|-----------------|----------|
| 1,000 records | 4 KB | 16 KB | 12 KB |
| 10,000 records | 40 KB | 160 KB | 120 KB |
| 100,000 records | 400 KB | 1.6 MB | 1.2 MB |
| 1,000,000 records | 4 MB | 16 MB | 12 MB |

**Impact**: Minimal for most applications. Only significant at very large scale.

#### Index Size Impact

- B-tree index size increases proportionally (4x larger)
- For 100K records: ~4 MB vs ~16 MB
- Modern servers handle this easily

### Query Performance

#### UUID v7 Advantages
✅ **Time-ordered**: Sequential inserts (like auto-increment)
✅ **Index locality**: Similar timestamps cluster together
✅ **Less fragmentation**: Better than UUID v4
✅ **Range queries**: Efficient time-based queries

#### Performance Comparison

| Operation | INT AUTO_INCREMENT | UUID v7 BINARY(16) |
|-----------|-------------------|-------------------|
| INSERT | ⚡ Fast | ⚡ Fast |
| SELECT by PK | ⚡ Very Fast | ⚡ Very Fast |
| SELECT range | ⚡ Fast | ⚡ Fast (time-ordered) |
| JOIN | ⚡ Fast | 🐢 Slightly slower (larger keys) |
| Index scan | ⚡ Very Fast | ⚡ Fast |

**Verdict**: Negligible performance difference for most applications.

### Mitigation Strategies

#### 1. Use BINARY(16) Not CHAR(36)
```php
// ✅ GOOD: 16 bytes
'id' => ['type' => 'BINARY', 'constraint' => 16]

// ❌ BAD: 36 bytes (2.25x larger)
'id' => ['type' => 'CHAR', 'constraint' => 36]
```

#### 2. Convert Only When Necessary
```php
// Store and query as binary
$binaryId = UuidHelper::generateBinary();
$this->db->table('organizations')->insert(['id' => $binaryId, ...]);

// Convert to string only for display
$stringId = UuidHelper::toString($binaryId);
echo "Organization ID: {$stringId}";
```

#### 3. Use Prepared Statements
```php
// Query builder handles this automatically
$org = $this->organizationModel->find($uuid); // Uses prepared statement
```

#### 4. Index Foreign Keys
```sql
-- Already done in migrations
KEY `organization_id` (`organization_id`)
KEY `created_by` (`created_by`)
```

#### 5. Optimize JOIN Queries
```php
// Select only needed columns
$this->db->select('organizations.id, organizations.org_name')
    ->join('groups', 'groups.organization_id = organizations.id')
    ->get();
```

#### 6. Use Query Caching (if applicable)
```php
// Cache expensive queries
$organizations = cache()->remember('organizations_list', 300, function() {
    return $this->organizationModel->findAll();
});
```

#### 7. Monitor Query Performance
```sql
-- Enable slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;

-- Check execution plans
EXPLAIN SELECT * FROM organizations WHERE id = uuid_to_bin(?);
```

---

## Troubleshooting

### Common Issues

#### Issue 1: "Foreign key constraint fails"

**Cause**: Trying to insert record with non-existent foreign key UUID

**Solution**:
```php
// Verify foreign key exists before insert
$orgExists = $this->organizationModel->find($organizationId);
if (!$orgExists) {
    throw new \Exception('Organization not found');
}
```

#### Issue 2: "Invalid UUID format"

**Cause**: Passing integer or malformed string as UUID

**Solution**:
```php
// Validate UUID before use
if (!UuidHelper::isValid($uuid)) {
    throw new \InvalidArgumentException('Invalid UUID format');
}
```

#### Issue 3: "Data too long for column 'id'"

**Cause**: Column is not BINARY(16) or trying to insert string UUID

**Solution**:
```sql
-- Fix column type
ALTER TABLE table_name MODIFY id BINARY(16) NOT NULL;
```

```php
// Convert string to binary before insert
$data['id'] = UuidHelper::toBinary($stringUuid);
```

#### Issue 4: "Cannot convert binary to string"

**Cause**: Trying to display binary UUID directly

**Solution**:
```php
// Use conversion helper
$displayId = UuidHelper::toString($binaryId);

// Or use model's afterFind callback (automatic)
$org = $this->organizationModel->find($id); // Returns string UUID
```

#### Issue 5: "Session UUID not matching database"

**Cause**: Session stores binary, database expects string (or vice versa)

**Solution**:
```php
// Store string UUID in session
session()->set('user_id', UuidHelper::toString($binaryId));

// Convert when querying
$userId = session()->get('user_id');
$user = $this->userModel->find($userId); // Model handles conversion
```

#### Issue 6: Routes not matching UUID pattern

**Cause**: Route regex doesn't match UUID format

**Solution**:
```php
// Use correct UUID pattern in routes
$uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
$routes->get('organizations/(' . $uuidPattern . ')', 'Organizations::show/$1');
```

#### Issue 7: Seeder fails with UUID

**Cause**: Not generating UUID before insert

**Solution**:
```php
$data = [
    'id' => UuidHelper::generateBinary(), // Add this!
    'name' => 'Test User',
    // ...
];
```

### Debug Helper

Create `app/Helpers/uuid_debug.php`:

```php
<?php

if (!function_exists('dd_uuid')) {
    /**
     * Debug UUID - shows both binary and string format
     */
    function dd_uuid($uuid)
    {
        echo "<pre>";
        echo "Type: " . gettype($uuid) . "\n";
        echo "Length: " . strlen($uuid) . "\n";

        if (strlen($uuid) === 16) {
            echo "Format: Binary\n";
            echo "Hex: " . bin2hex($uuid) . "\n";
            echo "String: " . \App\Helpers\UuidHelper::toString($uuid) . "\n";
        } elseif (strlen($uuid) === 36) {
            echo "Format: String\n";
            echo "Binary hex: " . bin2hex(\App\Helpers\UuidHelper::toBinary($uuid)) . "\n";
        } else {
            echo "Format: Unknown\n";
            echo "Value: " . $uuid . "\n";
        }
        echo "</pre>";
        die();
    }
}
```

---

## Data Preservation Strategy

**Note**: This section is for migrating existing production data. Skip if doing clean migration.

### Strategy Overview

1. Add new UUID columns alongside integer IDs
2. Generate UUIDs for all existing records
3. Create mapping table (int_id → uuid)
4. Update all foreign keys to reference UUIDs
5. Drop integer columns
6. Rename UUID columns to 'id'

### Step-by-Step Process

#### Step 1: Add UUID Columns

```sql
-- Add UUID columns to all tables
ALTER TABLE dakoii_users ADD COLUMN uuid_id BINARY(16) NULL AFTER id;
ALTER TABLE organizations ADD COLUMN uuid_id BINARY(16) NULL AFTER id;
ALTER TABLE organizations ADD COLUMN uuid_created_by BINARY(16) NULL;
ALTER TABLE organizations ADD COLUMN uuid_updated_by BINARY(16) NULL;
ALTER TABLE organizations ADD COLUMN uuid_deleted_by BINARY(16) NULL;
-- Repeat for all tables and all foreign key columns
```

#### Step 2: Generate UUIDs for Existing Records

```php
// Create migration: GenerateUuidsForExistingData.php

public function up()
{
    $db = \Config\Database::connect();

    // Generate UUIDs for dakoii_users
    $users = $db->table('dakoii_users')->get()->getResultArray();
    foreach ($users as $user) {
        $uuid = \App\Helpers\UuidHelper::generateBinary();
        $db->table('dakoii_users')
            ->where('id', $user['id'])
            ->update(['uuid_id' => $uuid]);
    }

    // Repeat for all tables
    // ...
}
```

#### Step 3: Create ID Mapping Table

```php
public function up()
{
    $this->forge->addField([
        'table_name' => ['type' => 'VARCHAR', 'constraint' => '50'],
        'int_id' => ['type' => 'INT', 'unsigned' => true],
        'uuid_id' => ['type' => 'BINARY', 'constraint' => 16],
    ]);
    $this->forge->addKey(['table_name', 'int_id']);
    $this->forge->createTable('uuid_migration_map');

    // Populate mapping
    $db = \Config\Database::connect();

    $tables = ['dakoii_users', 'organizations', 'groups', 'users', 'correspondences'];
    foreach ($tables as $table) {
        $records = $db->table($table)->select('id, uuid_id')->get()->getResultArray();
        foreach ($records as $record) {
            $db->table('uuid_migration_map')->insert([
                'table_name' => $table,
                'int_id' => $record['id'],
                'uuid_id' => $record['uuid_id'],
            ]);
        }
    }
}
```

#### Step 4: Update Foreign Keys

```php
public function up()
{
    $db = \Config\Database::connect();

    // Update organizations.created_by (INT) → organizations.uuid_created_by (UUID)
    $query = "
        UPDATE organizations o
        JOIN dakoii_users u ON o.created_by = u.id
        SET o.uuid_created_by = u.uuid_id
        WHERE o.created_by IS NOT NULL
    ";
    $db->query($query);

    // Repeat for all foreign key relationships
    // ...
}
```

#### Step 5: Drop Integer Columns

```sql
-- Drop foreign key constraints first
ALTER TABLE organizations DROP FOREIGN KEY fk_organizations_created_by;
ALTER TABLE organizations DROP FOREIGN KEY fk_organizations_updated_by;
-- Drop all FK constraints

-- Drop integer columns
ALTER TABLE dakoii_users DROP COLUMN id;
ALTER TABLE organizations DROP COLUMN id;
ALTER TABLE organizations DROP COLUMN created_by;
ALTER TABLE organizations DROP COLUMN updated_by;
-- Drop all integer ID columns
```

#### Step 6: Rename UUID Columns

```sql
-- Rename uuid_id to id
ALTER TABLE dakoii_users CHANGE COLUMN uuid_id id BINARY(16) NOT NULL;
ALTER TABLE organizations CHANGE COLUMN uuid_id id BINARY(16) NOT NULL;

-- Rename FK columns
ALTER TABLE organizations CHANGE COLUMN uuid_created_by created_by BINARY(16) NULL;
ALTER TABLE organizations CHANGE COLUMN uuid_updated_by updated_by BINARY(16) NULL;
-- Rename all FK columns
```

#### Step 7: Recreate Constraints

```sql
-- Add primary keys
ALTER TABLE dakoii_users ADD PRIMARY KEY (id);
ALTER TABLE organizations ADD PRIMARY KEY (id);

-- Add foreign keys
ALTER TABLE organizations
    ADD CONSTRAINT fk_organizations_created_by
    FOREIGN KEY (created_by)
    REFERENCES dakoii_users(id)
    ON DELETE SET NULL ON UPDATE CASCADE;
-- Add all FK constraints
```

#### Step 8: Drop Mapping Table

```sql
DROP TABLE uuid_migration_map;
```

---

## Conclusion

This migration guide provides a complete roadmap for converting the CORRES application from integer auto-increment primary keys to UUID v7 identifiers. The migration includes:

✅ Database schema updates with BINARY(16) columns
✅ UUID v7 generation using `ramsey/uuid`
✅ Base model pattern for automatic UUID handling
✅ Controller updates for validation and routing
✅ Route pattern matching for UUID format
✅ View updates for displaying UUIDs
✅ Comprehensive testing checklist
✅ Performance optimization strategies
✅ Troubleshooting guide
✅ Data preservation strategy (optional)

### Key Benefits After Migration

1. **Distributed-ready**: Generate unique IDs without coordination
2. **Security**: Non-sequential IDs prevent enumeration attacks
3. **Time-ordered**: UUID v7 maintains insert performance
4. **Scalability**: No single point of failure for ID generation
5. **Future-proof**: Standard RFC format with wide support

### Estimated Performance Impact

- **Storage**: +300% for ID columns (16 bytes vs 4 bytes)
- **Query speed**: ~5-10% slower for JOINs (larger index keys)
- **Insert speed**: Same as auto-increment (time-ordered)
- **Overall**: Negligible for most applications

### Next Steps

1. Review this document thoroughly
2. Test migration in development environment
3. Measure performance baselines
4. Execute migration steps sequentially
5. Verify all tests pass
6. Monitor production performance
7. Update documentation

---

**Document Version**: 1.0
**Last Updated**: 2026-02-12
**Status**: Ready for Implementation
**Estimated Implementation Time**: 12-18 hours
