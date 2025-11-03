<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
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

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[255]',
        'email' => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[4]',
        'organization_id' => 'required|numeric',
        'group_id' => 'permit_empty|numeric',
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
            'numeric' => 'Invalid organization',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword', 'setCreatedBy'];
    protected $beforeUpdate   = ['hashPassword', 'setUpdatedBy'];
    protected $beforeDelete   = ['setDeletedBy'];

    /**
     * Hash password before insert/update
     * 
     * @param array $data
     * @return array
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } elseif (isset($data['data']['password']) && empty($data['data']['password'])) {
            // Remove password field if empty during update
            unset($data['data']['password']);
        }
        
        return $data;
    }

    /**
     * Verify user password
     * 
     * @param string $email
     * @param string $password
     * @return array|null
     */
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

    /**
     * Get all users for a specific organization with audit information
     * 
     * @param int $organizationId
     * @return array
     */
    public function getUsersByOrganization(int $organizationId): array
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

    /**
     * Get all users with organization and audit information
     * 
     * @return array
     */
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

    /**
     * Get single user with audit information
     * 
     * @param int $id
     * @return array|null
     */
    public function getUserWithAudit(int $id): ?array
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

