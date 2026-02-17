<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertDakoiiUsersToUUID extends Migration
{
    public function up()
    {
        $this->forge->dropTable('dakoii_users', true);

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
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
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
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('dakoii_users');
    }

    public function down()
    {
        $this->forge->dropTable('dakoii_users', true);
    }
}
