<?php

namespace App\Models;

class UserModel extends UuidModel
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'password',
        'organization_id',
        'group_id',
        'status',
        'position',
        'signature_filepath',
        'stamp_filepath',
        'is_supervisor',
        'is_front_desk',
        'is_admin',
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
        'name' => 'required|max_length[255]',
        'email' => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[4]',
        'organization_id' => 'required',
        'group_id' => 'permit_empty',
        'status' => 'permit_empty|in_list[active,inactive]',
        'position' => 'permit_empty|max_length[255]',
        'signature_filepath' => 'permit_empty|max_length[255]',
        'stamp_filepath' => 'permit_empty|max_length[255]',
        'is_supervisor' => 'permit_empty|in_list[0,1]',
        'is_front_desk' => 'permit_empty|in_list[0,1]',
        'is_admin' => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Name is required',
            'max_length' => 'Name cannot exceed 255 characters',
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please provide a valid email address',
            'max_length' => 'Email cannot exceed 255 characters',
            'is_unique' => 'This email address is already in use',
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 4 characters',
        ],
        'organization_id' => [
            'required' => 'Organization is required',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function getUuidFields(): array
    {
        return [
            'id',
            'organization_id',
            'group_id',
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
        $foreignKeys = ['organization_id', 'group_id'];

        foreach ($foreignKeys as $key) {
            if (isset($data['data'][$key]) && is_string($data['data'][$key]) && strlen($data['data'][$key]) === 36) {
                $data['data'][$key] = \App\Helpers\UuidHelper::toBinary($data['data'][$key]);
            }
        }

        return $data;
    }

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } elseif (isset($data['data']['password']) && empty($data['data']['password'])) {
            unset($data['data']['password']);
        }
        
        return $data;
    }

    public function verifyCredentials(string $email, string $password): ?array
    {
        $user = $this->where('email', $email)
            ->where('status', 'active')
            ->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    public function getUsersByOrganization(string $organizationId): array
    {
        return $this->select('users.*, 
                             groups.group_name,
                             groups.group_code,
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('groups', 'groups.id = users.group_id', 'left')
            ->join('dakoii_users as creator', 'creator.id = users.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = users.updated_by', 'left')
            ->where('users.organization_id', $organizationId)
            ->orderBy('users.created_at', 'DESC')
            ->findAll();
    }

    public function getUsersWithAudit(): array
    {
        return $this->select('users.*, 
                             organizations.org_name,
                             organizations.org_code,
                             groups.group_name,
                             groups.group_code,
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('organizations', 'organizations.id = users.organization_id', 'left')
            ->join('groups', 'groups.id = users.group_id', 'left')
            ->join('dakoii_users as creator', 'creator.id = users.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = users.updated_by', 'left')
            ->orderBy('users.created_at', 'DESC')
            ->findAll();
    }

    public function getUserWithAudit(string $id): ?array
    {
        return $this->select('users.*, 
                             organizations.org_name,
                             organizations.org_code,
                             groups.group_name,
                             groups.group_code,
                             creator.name as created_by_name,
                             updater.name as updated_by_name')
            ->join('organizations', 'organizations.id = users.organization_id', 'left')
            ->join('groups', 'groups.id = users.group_id', 'left')
            ->join('dakoii_users as creator', 'creator.id = users.created_by', 'left')
            ->join('dakoii_users as updater', 'updater.id = users.updated_by', 'left')
            ->find($id);
    }
}
