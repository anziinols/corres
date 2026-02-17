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

        $this->forge->createTable('groups');
    }

    public function down()
    {
        $this->forge->dropTable('groups', true);
    }
}
