<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixFilePathsPrefix extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('correspondences');
        
        // Find records where file_path starts with 'uploads/' (missing 'public/')
        $records = $builder->like('file_path', 'uploads/correspondences/', 'after')
                           ->get()
                           ->getResultArray();
        
        foreach ($records as $record) {
            if (strpos($record['file_path'], 'public/') !== 0) {
                $newPath = 'public/' . $record['file_path'];
                $builder->where('id', $record['id'])
                        ->update(['file_path' => $newPath]);
            }
        }
    }

    public function down()
    {
        // Reverting would involve removing 'public/' from the start
        $db = \Config\Database::connect();
        $builder = $db->table('correspondences');
        
        $records = $builder->like('file_path', 'public/uploads/correspondences/', 'after')
                           ->get()
                           ->getResultArray();
        
        foreach ($records as $record) {
            $newPath = substr($record['file_path'], 7); // Remove 'public/'
            $builder->where('id', $record['id'])
                    ->update(['file_path' => $newPath]);
        }
    }
}
