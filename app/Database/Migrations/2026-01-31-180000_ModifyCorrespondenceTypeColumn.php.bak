<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyCorrespondenceTypeColumn extends Migration
{
    public function up()
    {
        // Change correspondence_type from ENUM to VARCHAR to accept dynamic type_number values
        $this->db->query("ALTER TABLE correspondences MODIFY COLUMN correspondence_type VARCHAR(50) NOT NULL DEFAULT 'LETTER'");
    }

    public function down()
    {
        // Revert to original ENUM
        $this->db->query("ALTER TABLE correspondences MODIFY COLUMN correspondence_type ENUM('LETTER', 'EMAIL', 'FAX', 'MEMO', 'CIRCULAR', 'PHONE', 'MEETING_MINUTES', 'REPORT', 'WHATSAPP', 'SMS', 'OTHER') DEFAULT 'LETTER' NOT NULL");
    }
}
