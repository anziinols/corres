<?php

namespace App\Models;

use CodeIgniter\Model;

class GroupModel extends Model
{
    protected $table            = 'groups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'group_code',
        'group_name',
        'organization_id',
        'parent_id',
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
        'group_code' => 'required|max_length[10]|is_unique[groups.group_code,id,{id}]',
        'group_name' => 'required|max_length[255]',
        'organization_id' => 'required|numeric',
        'parent_id' => 'permit_empty|numeric',
        'description' => 'permit_empty',
        'status' => 'permit_empty|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'group_code' => [
            'required' => 'Group code is required',
            'max_length' => 'Group code cannot exceed 10 characters',
            'is_unique' => 'This group code already exists',
        ],
        'group_name' => [
            'required' => 'Group name is required',
            'max_length' => 'Group name cannot exceed 255 characters',
        ],
        'organization_id' => [
            'required' => 'Organization is required',
            'numeric' => 'Invalid organization',
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
     * Generate next available group code with g2 prefix
     * 
     * @return string Code starting with g2 (g201, g202, g203, etc.)
     */
    public function generateGroupCode(): string
    {
        // Get all groups with codes starting with g2
        $groups = $this->select('group_code')
            ->like('group_code', 'g2', 'after')
            ->withDeleted()
            ->findAll();

        if (!empty($groups)) {
            // Extract numeric parts and find the maximum
            $maxNumber = 0;
            foreach ($groups as $group) {
                $numericPart = (int) substr($group['group_code'], 2);
                if ($numericPart > $maxNumber) {
                    $maxNumber = $numericPart;
                }
            }
            $nextNumber = $maxNumber + 1;
        } else {
            // Start with 01 if no groups exist (g201)
            $nextNumber = 1;
        }

        return 'g2' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get next available group code for display
     * 
     * @return string
     */
    public function getNextGroupCode(): string
    {
        return $this->generateGroupCode();
    }

    /**
     * Check if group code is unique (excluding current record during update)
     * 
     * @param string $groupCode
     * @param int|null $excludeId
     * @return bool
     */
    public function isGroupCodeUnique(string $groupCode, ?int $excludeId = null): bool
    {
        $builder = $this->where('group_code', $groupCode);
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->withDeleted()->countAllResults() === 0;
    }

    /**
     * Get all groups for a specific organization with audit information
     * 
     * @param int $organizationId
     * @return array
     */
    public function getGroupsByOrganization(int $organizationId): array
    {
        return $this->select('groups.*, 
                             parent.group_name as parent_name,
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('groups as parent', 'parent.id = groups.parent_id', 'left')
            ->join('dakoii_users as creator', 'creator.id = groups.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = groups.updated_by', 'left')
            ->where('groups.organization_id', $organizationId)
            ->orderBy('groups.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all groups with organization and audit information
     * 
     * @return array
     */
    public function getGroupsWithAudit(): array
    {
        return $this->select('groups.*, 
                             organizations.org_name,
                             parent.group_name as parent_name,
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('organizations', 'organizations.id = groups.organization_id', 'left')
            ->join('groups as parent', 'parent.id = groups.parent_id', 'left')
            ->join('dakoii_users as creator', 'creator.id = groups.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = groups.updated_by', 'left')
            ->orderBy('groups.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get single group with audit information
     * 
     * @param int $id
     * @return array|null
     */
    public function getGroupWithAudit(int $id): ?array
    {
        return $this->select('groups.*, 
                             organizations.org_name,
                             organizations.org_code,
                             parent.group_name as parent_name,
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('organizations', 'organizations.id = groups.organization_id', 'left')
            ->join('groups as parent', 'parent.id = groups.parent_id', 'left')
            ->join('dakoii_users as creator', 'creator.id = groups.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = groups.updated_by', 'left')
            ->find($id);
    }

    /**
     * Get available parent groups for a specific organization
     * Excludes the current group to prevent circular references
     * 
     * @param int $organizationId
     * @param int|null $excludeId
     * @return array
     */
    public function getAvailableParentGroups(int $organizationId, ?int $excludeId = null): array
    {
        $builder = $this->select('id, group_code, group_name')
            ->where('organization_id', $organizationId)
            ->where('status', 'active');
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->orderBy('group_name', 'ASC')->findAll();
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
}

