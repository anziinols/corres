<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUnusedCorrespondenceFields extends Migration
{
    public function up()
    {
        // Drop foreign key for parent_correspondence_id first
        $this->db->query('ALTER TABLE correspondences DROP FOREIGN KEY correspondences_parent_correspondence_id_foreign');

        // Remove unused columns
        $this->forge->dropColumn('correspondences', [
            'reference_number',
            'original_date',
            'date_sent',
            'sender_organization',
            'sender_address',
            'sender_contact',
            'recipient_name',
            'recipient_organization',
            'recipient_address',
            'dispatch_method',
            'department',
            'filing_reference',
            'archive_location',
            'archive_date',
            'parent_correspondence_id',
            'is_linked',
            'linked_type',
        ]);
    }

    public function down()
    {
        // Re-add columns if needed (rollback)
        $this->forge->addColumn('correspondences', [
            'reference_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'correspondence_number',
            ],
            'original_date' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'date_received',
            ],
            'date_sent' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'original_date',
            ],
            'sender_organization' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'sender_name',
            ],
            'sender_address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'sender_organization',
            ],
            'sender_contact' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'sender_address',
            ],
            'recipient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'sender_contact',
            ],
            'recipient_organization' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'recipient_name',
            ],
            'recipient_address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'recipient_organization',
            ],
            'dispatch_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'recipient_address',
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'status',
            ],
            'filing_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'group_id',
            ],
            'archive_location' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'filing_reference',
            ],
            'archive_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'archive_location',
            ],
            'parent_correspondence_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'registration_date',
            ],
            'is_linked' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null'    => false,
                'after'   => 'parent_correspondence_id',
            ],
            'linked_type' => [
                'type'       => 'ENUM',
                'constraint' => ['RESPONSE', 'FOLLOW_UP', 'RELATED'],
                'null'       => true,
                'after'      => 'is_linked',
            ],
        ]);

        // Re-add foreign key
        $this->db->query('ALTER TABLE correspondences ADD CONSTRAINT correspondences_parent_correspondence_id_foreign FOREIGN KEY (parent_correspondence_id) REFERENCES correspondences(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }
}
