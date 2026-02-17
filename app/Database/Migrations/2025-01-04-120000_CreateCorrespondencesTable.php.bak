<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCorrespondencesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'correspondence_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
                'comment'    => 'Unique correspondence reference number',
            ],
            'reference_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'External reference number if any',
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
                'null'       => false,
                'comment'    => 'Subject/particulars of correspondence',
            ],
            
            // Communication Type & Direction
            'correspondence_type' => [
                'type'       => 'ENUM',
                'constraint' => ['LETTER', 'EMAIL', 'FAX', 'MEMO', 'CIRCULAR', 'PHONE', 'MEETING_MINUTES', 'REPORT', 'WHATSAPP', 'SMS', 'OTHER'],
                'default'    => 'LETTER',
                'null'       => false,
                'comment'    => 'Type of communication',
            ],
            'correspondence_direction' => [
                'type'       => 'ENUM',
                'constraint' => ['INWARD', 'OUTWARD', 'INTERNAL'],
                'default'    => 'INWARD',
                'null'       => false,
                'comment'    => 'Direction of correspondence',
            ],
            
            // Date Fields
            'date_received' => [
                'type'    => 'DATE',
                'null'    => false,
                'comment' => 'Date when correspondence was received',
            ],
            'original_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Date on the original document',
            ],
            'date_sent' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Date sent (for outward correspondence)',
            ],
            
            // Sender Information (Inward)
            'sender_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Name of sender',
            ],
            'sender_organization' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Sender organization',
            ],
            'sender_address' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Sender address',
            ],
            'sender_contact' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Sender contact (email/phone)',
            ],
            
            // Recipient Information (Outward)
            'recipient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Name of recipient',
            ],
            'recipient_organization' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Recipient organization',
            ],
            'recipient_address' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Recipient address',
            ],
            'dispatch_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'comment'    => 'Method of dispatch (Email, Post, Courier, Hand Delivery)',
            ],
            
            // Priority & Status
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
            
            // Department & Organization Context
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'comment'    => 'Department code for numbering (HRM, FIN, etc.)',
            ],
            'organization_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Link to organizations table',
            ],
            
            // Filing & Archive
            'filing_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Physical file location (e.g., P/F-FKupiaw/F-HRM)',
            ],
            'archive_location' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Archive location',
            ],
            'archive_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Date archived',
            ],
            
            // Registration & Linking
            'registered_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'User who registered the correspondence',
            ],
            'registration_date' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Date and time of registration',
            ],
            'parent_correspondence_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'For linked correspondences',
            ],
            'is_linked' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
                'comment' => 'Whether this is linked to another correspondence',
            ],
            'linked_type' => [
                'type'       => 'ENUM',
                'constraint' => ['RESPONSE', 'FOLLOW_UP', 'RELATED'],
                'null'       => true,
                'comment'    => 'Type of link',
            ],
            
            // Additional Information
            'remarks' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Additional remarks or notes',
            ],
            
            // Audit Trail Fields
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey('registered_by');
        $this->forge->addKey('parent_correspondence_id');
        $this->forge->addKey('status');
        $this->forge->addKey('date_received');
        $this->forge->addKey('correspondence_direction');
        $this->forge->addKey('department');
        
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('registered_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('parent_correspondence_id', 'correspondences', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('correspondences');
    }

    public function down()
    {
        $this->forge->dropTable('correspondences');
    }
}

