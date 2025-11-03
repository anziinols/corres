<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationModel extends Model
{
    protected $table            = 'organizations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'org_code',
        'org_name',
        'org_logo',
        'description',
        'status',
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
        'org_code' => 'required|exact_length[4]|is_unique[organizations.org_code,id,{id}]|numeric',
        'org_name' => 'required|max_length[255]',
        'org_logo' => 'permit_empty|max_length[255]',
        'description' => 'permit_empty',
        'status' => 'permit_empty|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'org_code' => [
            'required' => 'Organization code is required',
            'exact_length' => 'Organization code must be exactly 4 digits',
            'is_unique' => 'This organization code already exists',
            'numeric' => 'Organization code must contain only numbers',
        ],
        'org_name' => [
            'required' => 'Organization name is required',
            'max_length' => 'Organization name cannot exceed 255 characters',
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
     * Generate next available organization code
     * 
     * @return string 4-digit code starting with 11, 12, 13, etc.
     */
    public function generateOrgCode(): string
    {
        // Get the highest org_code from the database
        $lastOrg = $this->select('org_code')
            ->orderBy('org_code', 'DESC')
            ->withDeleted()
            ->first();

        if ($lastOrg) {
            $lastCode = (int) $lastOrg['org_code'];
            $nextCode = $lastCode + 1;
        } else {
            // Start with 1100 if no organizations exist
            $nextCode = 1100;
        }

        // Ensure the code is 4 digits and starts with 11, 12, 13, etc.
        return str_pad($nextCode, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get next available organization code for display
     * 
     * @return string
     */
    public function getNextOrgCode(): string
    {
        return $this->generateOrgCode();
    }

    /**
     * Check if organization code is unique (excluding current record during update)
     * 
     * @param string $orgCode
     * @param int|null $excludeId
     * @return bool
     */
    public function isOrgCodeUnique(string $orgCode, ?int $excludeId = null): bool
    {
        $builder = $this->where('org_code', $orgCode);
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->withDeleted()->countAllResults() === 0;
    }

    /**
     * Set created_by field before insert
     * 
     * @param array $data
     * @return array
     */
    protected function setCreatedBy(array $data): array
    {
        if (!isset($data['data']['created_by'])) {
            $session = session();
            if ($session->has('dakoii_user_id')) {
                $data['data']['created_by'] = $session->get('dakoii_user_id');
            }
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
        if (!isset($data['data']['updated_by'])) {
            $session = session();
            if ($session->has('dakoii_user_id')) {
                $data['data']['updated_by'] = $session->get('dakoii_user_id');
            }
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
        $session = session();
        if ($session->has('dakoii_user_id')) {
            $this->update($data['id'], ['deleted_by' => $session->get('dakoii_user_id')]);
        }
        return $data;
    }

    /**
     * Get all organizations with creator/updater information
     * 
     * @return array
     */
    public function getOrganizationsWithAudit(): array
    {
        return $this->select('organizations.*, 
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('dakoii_users as creator', 'creator.id = organizations.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = organizations.updated_by', 'left')
            ->orderBy('organizations.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get single organization with audit information
     * 
     * @param int $id
     * @return array|null
     */
    public function getOrganizationWithAudit(int $id): ?array
    {
        return $this->select('organizations.*, 
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('dakoii_users as creator', 'creator.id = organizations.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = organizations.updated_by', 'left')
            ->find($id);
    }
}

