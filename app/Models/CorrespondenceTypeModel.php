<?php

namespace App\Models;

class CorrespondenceTypeModel extends UuidModel
{
    protected $table            = 'correspondence_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'type_number',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'        => 'required|max_length[255]',
        'type_number' => 'required|max_length[50]|is_unique[correspondence_types.type_number,id,{id}]',
        'description' => 'permit_empty',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Correspondence type name is required',
            'max_length' => 'Name cannot exceed 255 characters',
        ],
        'type_number' => [
            'required'   => 'Type number is required',
            'max_length' => 'Type number cannot exceed 50 characters',
            'is_unique'  => 'This type number already exists',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected function getUuidFields(): array
    {
        return [
            'id',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    public function getTypesWithAudit(): array
    {
        return $this->select('correspondence_types.*, 
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('dakoii_users as creator', 'creator.id = correspondence_types.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = correspondence_types.updated_by', 'left')
            ->orderBy('correspondence_types.type_number', 'ASC')
            ->findAll();
    }

    public function getTypeWithAudit(string $id): ?array
    {
        return $this->select('correspondence_types.*, 
                             creator.name as created_by_name,
                             updater.name as updated_by_name,
                             deleter.name as deleted_by_name')
            ->join('dakoii_users as creator', 'creator.id = correspondence_types.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = correspondence_types.updated_by', 'left')
            ->join('dakoii_users as deleter', 'deleter.id = correspondence_types.deleted_by', 'left')
            ->find($id);
    }

    public function typeNumberExists(string $typeNumber, ?string $excludeId = null): bool
    {
        $builder = $this->where('type_number', $typeNumber);
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
}
