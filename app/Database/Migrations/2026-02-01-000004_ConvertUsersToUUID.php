<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertUsersToUUID extends Migration
{
    public function up()
    {
        $this->forge->dropTable('users', true);

        $this->forge->addField([
            'id' => [
                'type'       => 'BINARY',
                'constraint' => 16,
                'null'       => false,
                'comment'    => 'UUID v7 primary key',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'organization_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => false,
            ],
            'group_id' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'supervisor', 'front_desk'],
                'default'    => 'front_desk',
                'null'       => false,
            ],
            'signature_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'stamp_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('organization_id');
        $this->forge->addKey('group_id');

        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('group_id', 'groups', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
