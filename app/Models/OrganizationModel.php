<?php

namespace App\Models;

class OrganizationModel extends UuidModel
{
    protected $table            = 'organizations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
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

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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

    protected function getUuidFields(): array
    {
        return [
            'id',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    public function generateOrgCode(): string
    {
        $lastOrg = $this->select('org_code')
            ->orderBy('org_code', 'DESC')
            ->withDeleted()
            ->first();

        if ($lastOrg) {
            $lastCode = (int) $lastOrg['org_code'];
            $nextCode = $lastCode + 1;
        } else {
            $nextCode = 1100;
        }

        return str_pad($nextCode, 4, '0', STR_PAD_LEFT);
    }

    public function getNextOrgCode(): string
    {
        return $this->generateOrgCode();
    }

    public function isOrgCodeUnique(string $orgCode, ?string $excludeId = null): bool
    {
        $builder = $this->where('org_code', $orgCode);
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->withDeleted()->countAllResults() === 0;
    }

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

    public function getOrganizationWithAudit(string $id): ?array
    {
        return $this->select('organizations.*, 
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('dakoii_users as creator', 'creator.id = organizations.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = organizations.updated_by', 'left')
            ->find($id);
    }
}
