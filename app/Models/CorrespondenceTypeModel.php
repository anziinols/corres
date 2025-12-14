<?php

namespace App\Models;

use CodeIgniter\Model;

class CorrespondenceTypeModel extends Model
{
    protected $table            = 'correspondence_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
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

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
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

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedBy'];
    protected $beforeUpdate   = ['setUpdatedBy'];
    protected $beforeDelete   = ['setDeletedBy'];

    /**
     * Set created_by field before insert
     * 
     * @param array $data
     * @return array
     */
    protected function setCreatedBy(array $data): array
    {
        if (session()->has('dakoii_user_id')) {
            $data['data']['created_by'] = session()->get('dakoii_user_id');
        }
        return $data;
    }

    /**
     * Set updated_by field before update
     * 
     * @param array $data
     * @return array
     */
    protected function setUpdatedBy(array $data): array
    {
        if (session()->has('dakoii_user_id')) {
            $data['data']['updated_by'] = session()->get('dakoii_user_id');
        }
        return $data;
    }

    /**
     * Set deleted_by field before soft delete
     * 
     * @param array $data
     * @return array
     */
    protected function setDeletedBy(array $data): array
    {
        if (session()->has('dakoii_user_id')) {
            $data['data']['deleted_by'] = session()->get('dakoii_user_id');
        }
        return $data;
    }

    /**
     * Get all correspondence types with audit information
     * 
     * @return array
     */
    public function getTypesWithAudit(): array
    {
        return $this->select('correspondence_types.*, 
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('users as creator', 'creator.id = correspondence_types.created_by', 'left')
            ->join('users as updater', 'updater.id = correspondence_types.updated_by', 'left')
            ->orderBy('correspondence_types.type_number', 'ASC')
            ->findAll();
    }

    /**
     * Get a single correspondence type with audit information
     * 
     * @param int $id
     * @return array|null
     */
    public function getTypeWithAudit(int $id): ?array
    {
        return $this->select('correspondence_types.*, 
                             creator.name as created_by_name,
                             updater.name as updated_by_name,
                             deleter.name as deleted_by_name')
            ->join('users as creator', 'creator.id = correspondence_types.created_by', 'left')
            ->join('users as updater', 'updater.id = correspondence_types.updated_by', 'left')
            ->join('users as deleter', 'deleter.id = correspondence_types.deleted_by', 'left')
            ->find($id);
    }

    /**
     * Check if type number exists (excluding current record)
     * 
     * @param string $typeNumber
     * @param int|null $excludeId
     * @return bool
     */
    public function typeNumberExists(string $typeNumber, ?int $excludeId = null): bool
    {
        $builder = $this->where('type_number', $typeNumber);
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
}

