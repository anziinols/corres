<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertCorrespondenceTypesToUUID extends Migration
{
    public function up()
    {
        $this->forge->dropTable('correspondence_types', true);

        $this->forge->addField([
            'id' => [
                'type'       => 'BINARY',
                'constraint' => 16,
                'null'       => false,
                'comment'    => 'UUID v7 primary key',
            ],
            'type_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey('type_name');
        $this->forge->createTable('correspondence_types');
    }

    public function down()
    {
        $this->forge->dropTable('correspondence_types', true);
    }
}
