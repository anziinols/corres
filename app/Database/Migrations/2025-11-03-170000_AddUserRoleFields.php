<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserRoleFields extends Migration
{
    public function up()
    {
        $fields = [
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'User job position/title',
            ],
            'signature_filepath' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Path to user signature image',
            ],
            'stamp_filepath' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Path to user stamp image',
            ],
            'is_supervisor' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Is user a supervisor? (0=No, 1=Yes)',
            ],
            'is_front_desk' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Is user front desk staff? (0=No, 1=Yes)',
            ],
            'is_admin' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Is user an admin? (0=No, 1=Yes)',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', [
            'position',
            'signature_filepath',
            'stamp_filepath',
            'is_supervisor',
            'is_front_desk',
            'is_admin',
        ]);
    }
}

