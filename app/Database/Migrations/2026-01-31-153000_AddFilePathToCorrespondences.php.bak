<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFilePathToCorrespondences extends Migration
{
    public function up()
    {
        $fields = [
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'subject',
                'comment'    => 'Path to the uploaded document',
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'file_path',
                'comment'    => 'MIME type of the file',
            ],
        ];

        $this->forge->addColumn('correspondences', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('correspondences', ['file_path', 'file_type']);
    }
}
