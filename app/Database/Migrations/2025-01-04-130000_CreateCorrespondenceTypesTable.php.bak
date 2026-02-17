<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCorrespondenceTypesTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'type_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
                'comment'    => 'Manually entered type number/code',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
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
        $this->forge->addUniqueKey('type_number');
        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');
        $this->forge->addKey('deleted_by');
        $this->forge->addKey('deleted_at');
        
        $this->forge->createTable('correspondence_types', true);

        // Add foreign keys
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'SET NULL', 'fk_correspondence_types_created_by');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'SET NULL', 'fk_correspondence_types_updated_by');
        $this->forge->addForeignKey('deleted_by', 'users', 'id', 'SET NULL', 'SET NULL', 'fk_correspondence_types_deleted_by');
        $this->forge->processIndexes('correspondence_types');
    }

    public function down()
    {
        $this->forge->dropTable('correspondence_types', true);
    }
}

