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

        $this->forge->createTable('correspondences');
    }

    public function down()
    {
        $this->forge->dropTable('correspondences', true);
    }
}
