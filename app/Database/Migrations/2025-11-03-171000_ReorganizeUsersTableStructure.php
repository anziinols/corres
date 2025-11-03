<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReorganizeUsersTableStructure extends Migration
{
    public function up()
    {
        // Using raw SQL to reorder columns (preserves data)
        // MySQL MODIFY COLUMN allows repositioning without data loss
        
        // Reorder position after password
        $this->db->query("ALTER TABLE users MODIFY COLUMN position VARCHAR(255) NULL COMMENT 'User job position/title' AFTER password");
        
        // Reorder signature_filepath after group_id
        $this->db->query("ALTER TABLE users MODIFY COLUMN signature_filepath VARCHAR(255) NULL COMMENT 'Path to user signature image' AFTER group_id");
        
        // Reorder stamp_filepath after signature_filepath
        $this->db->query("ALTER TABLE users MODIFY COLUMN stamp_filepath VARCHAR(255) NULL COMMENT 'Path to user stamp image' AFTER signature_filepath");
        
        // Reorder is_admin after stamp_filepath
        $this->db->query("ALTER TABLE users MODIFY COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is user an admin? (0=No, 1=Yes)' AFTER stamp_filepath");
        
        // Reorder is_supervisor after is_admin
        $this->db->query("ALTER TABLE users MODIFY COLUMN is_supervisor TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is user a supervisor? (0=No, 1=Yes)' AFTER is_admin");
        
        // Reorder is_front_desk after is_supervisor
        $this->db->query("ALTER TABLE users MODIFY COLUMN is_front_desk TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is user front desk staff? (0=No, 1=Yes)' AFTER is_supervisor");
        
        // Reorder status after is_front_desk
        $this->db->query("ALTER TABLE users MODIFY COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER is_front_desk");
        
        // Reorder created_at after created_by
        $this->db->query("ALTER TABLE users MODIFY COLUMN created_at DATETIME NULL AFTER created_by");
        
        // Reorder updated_at after updated_by
        $this->db->query("ALTER TABLE users MODIFY COLUMN updated_at DATETIME NULL AFTER updated_by");
        
        // Reorder deleted_at after deleted_by (already in correct position, but for consistency)
        $this->db->query("ALTER TABLE users MODIFY COLUMN deleted_at DATETIME NULL AFTER deleted_by");
    }

    public function down()
    {
        // Reverse the order back to original
        // This is optional and can be skipped if not needed
        
        $this->db->query("ALTER TABLE users MODIFY COLUMN position VARCHAR(255) NULL COMMENT 'User job position/title' AFTER deleted_at");
        $this->db->query("ALTER TABLE users MODIFY COLUMN signature_filepath VARCHAR(255) NULL COMMENT 'Path to user signature image' AFTER position");
        $this->db->query("ALTER TABLE users MODIFY COLUMN stamp_filepath VARCHAR(255) NULL COMMENT 'Path to user stamp image' AFTER signature_filepath");
        $this->db->query("ALTER TABLE users MODIFY COLUMN is_supervisor TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is user a supervisor? (0=No, 1=Yes)' AFTER stamp_filepath");
        $this->db->query("ALTER TABLE users MODIFY COLUMN is_front_desk TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is user front desk staff? (0=No, 1=Yes)' AFTER is_supervisor");
        $this->db->query("ALTER TABLE users MODIFY COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is user an admin? (0=No, 1=Yes)' AFTER is_front_desk");
    }
}

