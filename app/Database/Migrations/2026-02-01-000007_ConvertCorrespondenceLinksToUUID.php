<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertCorrespondenceLinksToUUID extends Migration
{
    public function up()
    {
        $this->forge->dropTable('correspondence_links', true);

        $this->forge->addField([
            'id' => [
                'type'       => 'BINARY',
                'constraint' => 16,
                'null'       => false,
                'comment'    => 'UUID v7 primary key',
            ],
            'correspondence_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => false,
                'comment' => 'UUID of correspondence',
            ],
            'linked_correspondence_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => false,
                'comment' => 'UUID of linked correspondence',
            ],
            'link_type' => [
                'type'       => 'ENUM',
                'constraint' => ['RESPONSE', 'FOLLOW_UP', 'RELATED'],
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('correspondence_id');
        $this->forge->addKey('linked_correspondence_id');
        $this->forge->addUniqueKey(['correspondence_id', 'linked_correspondence_id'], 'unique_correspondence_link');

        $this->forge->addForeignKey('correspondence_id', 'correspondences', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('linked_correspondence_id', 'correspondences', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('correspondence_links');
    }

    public function down()
    {
        $this->forge->dropTable('correspondence_links', true);
    }
}
