<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DakoiiUserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'       => 'Free Kenny',
            'username'   => 'fkenny',
            'password'   => password_hash('dakoii', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Using Query Builder
        $this->db->table('dakoii_users')->insert($data);
    }
}

