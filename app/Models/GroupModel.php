<?php

namespace App\Models;

class GroupModel extends UuidModel
{
    protected $table            = 'groups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
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

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'group_code' => 'required|max_length[10]|is_unique[groups.group_code,id,{id}]',
        'group_name' => 'required|max_length[255]',
        'organization_id' => 'required',
        'parent_id' => 'permit_empty',
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
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected function getUuidFields(): array
    {
        return [
            'id',
            'organization_id',
            'parent_id',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    protected function beforeInsert(array $data): array
    {
        $data = parent::beforeInsert($data);
        $data = $this->convertForeignKeysToBinary($data);
        return $data;
    }

    protected function beforeUpdate(array $data): array
    {
        $data = parent::beforeUpdate($data);
        $data = $this->convertForeignKeysToBinary($data);
        return $data;
    }

    private function convertForeignKeysToBinary(array $data): array
    {
        $foreignKeys = ['organization_id', 'parent_id'];

        foreach ($foreignKeys as $key) {
            if (isset($data['data'][$key]) && is_string($data['data'][$key]) && strlen($data['data'][$key]) === 36) {
                $data['data'][$key] = \App\Helpers\UuidHelper::toBinary($data['data'][$key]);
            }
        }

        return $data;
    }

    public function generateGroupCode(): string
    {
        $groups = $this->select('group_code')
            ->like('group_code', 'g2', 'after')
            ->withDeleted()
            ->findAll();

        if (!empty($groups)) {
            $maxNumber = 0;
            foreach ($groups as $group) {
                $numericPart = (int) substr($group['group_code'], 2);
                if ($numericPart > $maxNumber) {
                    $maxNumber = $numericPart;
                }
            }
            $nextNumber = $maxNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'g2' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }

    public function getNextGroupCode(): string
    {
        return $this->generateGroupCode();
    }

    public function isGroupCodeUnique(string $groupCode, ?string $excludeId = null): bool
    {
        $builder = $this->where('group_code', $groupCode);
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->withDeleted()->countAllResults() === 0;
    }

    public function getGroupsByOrganization(string $organizationId): array
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

    public function getGroupWithAudit(string $id): ?array
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

    public function getAvailableParentGroups(string $organizationId, ?string $excludeId = null): array
    {
        $builder = $this->select('id, group_code, group_name')
            ->where('organization_id', $organizationId)
            ->where('status', 'active');
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->orderBy('group_name', 'ASC')->findAll();
    }
}
