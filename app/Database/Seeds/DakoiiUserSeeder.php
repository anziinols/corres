<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Helpers\UuidHelper;

class DakoiiUserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id'         => UuidHelper::generateBinary(),
            'name'       => 'Free Kenny',
            'username'   => 'fkenny',
            'password'   => password_hash('dakoii', PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('dakoii_users')->insert($data);
    }
}
