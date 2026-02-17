<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertOrganizationsToUUID extends Migration
{
    public function up()
    {
        $this->forge->dropTable('organizations', true);

        $this->forge->addField([
            'id' => [
                'type'       => 'BINARY',
                'constraint' => 16,
                'null'       => false,
                'comment'    => 'UUID v7 primary key',
            ],
            'org_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '4',
                'null'       => false,
                'comment'    => 'Unique 4-digit organization code',
            ],
            'org_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'org_logo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
                'comment' => 'UUID of dakoii_user who created',
            ],
            'updated_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
                'comment' => 'UUID of dakoii_user who updated',
            ],
            'deleted_by' => [
                'type'    => 'BINARY',
                'constraint' => 16,
                'null'    => true,
                'comment' => 'UUID of dakoii_user who deleted',
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
        $this->forge->addUniqueKey('org_code');

        $this->forge->createTable('organizations');
    }

    public function down()
    {
        $this->forge->dropTable('organizations', true);
    }
}
