<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGroupIdToCorrespondences extends Migration
{
    public function up()
    {
        // Add group_id column after organization_id
        $this->forge->addColumn('correspondences', [
            'group_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'organization_id',
                'comment'    => 'Link to groups table',
            ],
        ]);

        // Add foreign key
        $this->db->query('ALTER TABLE correspondences ADD CONSTRAINT fk_correspondences_group_id FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL ON UPDATE CASCADE');

        // Add index
        $this->db->query('ALTER TABLE correspondences ADD INDEX idx_correspondences_group_id (group_id)');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE correspondences DROP FOREIGN KEY fk_correspondences_group_id');

        // Drop the column
        $this->forge->dropColumn('correspondences', 'group_id');
    }
}
