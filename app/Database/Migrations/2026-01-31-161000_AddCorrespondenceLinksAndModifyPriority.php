<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCorrespondenceLinksAndModifyPriority extends Migration
{
    public function up()
    {
        // 1. Create correspondence_links table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'correspondence_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'linked_correspondence_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('correspondence_id', 'correspondences', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('linked_correspondence_id', 'correspondences', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('correspondence_links');

        // 2. Modify Priority Column
        // Note: CI4 forge modifyColumn has limitations with ENUMs on some drivers, 
        // strictly speaking we should use raw SQL to be safe for ENUM modifications if there's data,
        // but here we will try forge or raw sql.
        $this->db->query("ALTER TABLE correspondences MODIFY COLUMN priority ENUM('NORMAL', 'URGENT', 'CONFIDENTIAL') DEFAULT 'NORMAL'");
    }

    public function down()
    {
        $this->forge->dropTable('correspondence_links');
        // Revert priority
        $this->db->query("ALTER TABLE correspondences MODIFY COLUMN priority ENUM('LOW', 'NORMAL', 'HIGH', 'URGENT') DEFAULT 'NORMAL'");
    }
}
