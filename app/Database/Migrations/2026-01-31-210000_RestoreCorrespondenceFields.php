<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RestoreCorrespondenceFields extends Migration
{
    public function up()
    {
        // Re-add the fields that should be kept
        $this->forge->addColumn('correspondences', [
            'dispatch_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'sender_name',
                'comment'    => 'Method of dispatch (Email, Post, Courier, Hand Delivery)',
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'status',
                'comment'    => 'Department code for numbering (HRM, FIN, etc.)',
            ],
            'filing_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'group_id',
                'comment'    => 'Physical file location (e.g., P/F-FKupiaw/F-HRM)',
            ],
            'archive_location' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'filing_reference',
                'comment'    => 'Archive location',
            ],
            'archive_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'after'   => 'archive_location',
                'comment' => 'Date archived',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('correspondences', [
            'dispatch_method',
            'department',
            'filing_reference',
            'archive_location',
            'archive_date',
        ]);
    }
}
