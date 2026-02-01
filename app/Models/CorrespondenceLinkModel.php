<?php

namespace App\Models;

use CodeIgniter\Model;

class CorrespondenceLinkModel extends Model
{
    protected $table            = 'correspondence_links';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['correspondence_id', 'linked_correspondence_id', 'created_at'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // No updated_at
}
