<?php

namespace App\Models;

class CorrespondenceModel extends UuidModel
{
    protected $table            = 'correspondences';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'correspondence_number',
        'reference_number',
        'subject',
        'correspondence_type',
        'correspondence_direction',
        'date_received',
        'original_date',
        'date_sent',
        'sender_name',
        'sender_organization',
        'sender_address',
        'sender_contact',
        'recipient_name',
        'recipient_organization',
        'recipient_address',
        'dispatch_method',
        'priority',
        'status',
        'department',
        'organization_id',
        'group_id',
        'filing_reference',
        'archive_location',
        'archive_date',
        'registered_by',
        'registration_date',
        'parent_correspondence_id',
        'is_linked',
        'linked_type',
        'remarks',
        'file_path',
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
        'id' => 'permit_empty',
        'correspondence_number' => 'required|max_length[50]|is_unique[correspondences.correspondence_number,id,{id}]',
        'subject' => 'required|max_length[500]',
        'correspondence_type' => 'required',
        'correspondence_direction' => 'required|in_list[INWARD,OUTWARD,INTERNAL]',
        'date_received' => 'required|valid_date',
        'sender_name' => 'permit_empty|max_length[255]',
        'priority' => 'permit_empty|in_list[LOW,NORMAL,HIGH,URGENT]',
        'status' => 'permit_empty|in_list[REGISTERED,REFERRED,IN_PROCESS,ACTIONED,COMPLETED,ARCHIVED]',
        'organization_id' => 'permit_empty',
        'group_id' => 'permit_empty',
        'filing_reference' => 'permit_empty|max_length[100]',
        'archive_location' => 'permit_empty|max_length[100]',
        'archive_date' => 'permit_empty|valid_date',
        'registered_by' => 'permit_empty',
    ];

    protected $validationMessages = [
        'correspondence_number' => [
            'required' => 'Correspondence number is required',
            'max_length' => 'Correspondence number cannot exceed 50 characters',
            'is_unique' => 'This correspondence number already exists',
        ],
        'subject' => [
            'required' => 'Subject is required',
            'max_length' => 'Subject cannot exceed 500 characters',
        ],
        'correspondence_type' => [
            'required' => 'Correspondence type is required',
        ],
        'correspondence_direction' => [
            'required' => 'Correspondence direction is required',
        ],
        'date_received' => [
            'required' => 'Date received is required',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $beforeInsert   = ['setCreatedBy', 'setRegistrationDate'];
    protected $beforeUpdate   = ['setUpdatedBy'];

    protected function getUuidFields(): array
    {
        return [
            'id',
            'organization_id',
            'group_id',
            'registered_by',
            'parent_correspondence_id',
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
        $foreignKeys = ['organization_id', 'group_id', 'registered_by', 'parent_correspondence_id'];

        foreach ($foreignKeys as $key) {
            if (isset($data['data'][$key]) && is_string($data['data'][$key]) && strlen($data['data'][$key]) === 36) {
                $data['data'][$key] = \App\Helpers\UuidHelper::toBinary($data['data'][$key]);
            }
        }

        return $data;
    }

    protected function setRegistrationDate(array $data): array
    {
        if (!isset($data['data']['registration_date'])) {
            $data['data']['registration_date'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    public function getAllWithDetails()
    {
        return $this->select('correspondences.*, organizations.org_name, organizations.org_code, users.name as registered_by_name, groups.group_name')
            ->join('organizations', 'organizations.id = correspondences.organization_id', 'left')
            ->join('groups', 'groups.id = correspondences.group_id', 'left')
            ->join('users', 'users.id = correspondences.registered_by', 'left')
            ->orderBy('correspondences.created_at', 'DESC')
            ->findAll();
    }

    public function getWithDetails(string $id)
    {
        return $this->select('correspondences.*, organizations.org_name, organizations.org_code, users.name as registered_by_name, users.email as registered_by_email, groups.group_name')
            ->join('organizations', 'organizations.id = correspondences.organization_id', 'left')
            ->join('groups', 'groups.id = correspondences.group_id', 'left')
            ->join('users', 'users.id = correspondences.registered_by', 'left')
            ->where('correspondences.id', $id)
            ->first();
    }

    public function getByOrganization(string $orgId)
    {
        return $this->select('correspondences.*, users.name as registered_by_name')
            ->join('users', 'users.id = correspondences.registered_by', 'left')
            ->where('correspondences.organization_id', $orgId)
            ->orderBy('correspondences.created_at', 'DESC')
            ->findAll();
    }

    public function generateCorrespondenceNumber($department = null, $year = null)
    {
        $year = $year ?? date('Y');
        
        if ($department) {
            $prefix = strtoupper($department) . '-' . $year . '/';
            $lastCorr = $this->like('correspondence_number', $prefix, 'after')
                ->orderBy('id', 'DESC')
                ->first();
            
            if ($lastCorr) {
                $lastNumber = (int) substr($lastCorr['correspondence_number'], strlen($prefix));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $prefix = 'CORR/' . $year . '/';
            $lastCorr = $this->like('correspondence_number', $prefix, 'after')
                ->orderBy('id', 'DESC')
                ->first();
            
            if ($lastCorr) {
                $lastNumber = (int) substr($lastCorr['correspondence_number'], strlen($prefix));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }
    }
}
