<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDispatchAndDepartment extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('correspondences', [
            'dispatch_method',
            'department',
        ]);
    }

    public function down()
    {
        $this->forge->addColumn('correspondences', [
            'dispatch_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'sender_name',
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'status',
            ],
        ]);
    }
}
