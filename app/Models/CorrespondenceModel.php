<?php

namespace App\Models;

use CodeIgniter\Model;

class CorrespondenceModel extends Model
{
    protected $table            = 'correspondences';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'correspondence_number',
        'reference_number',
        'subject',
        'file_path',
        'file_type',
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
        'filing_reference',
        'archive_location',
        'archive_date',
        'registered_by',
        'registration_date',
        'parent_correspondence_id',
        'is_linked',
        'linked_type',
        'remarks',
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
        'correspondence_number' => 'required|max_length[50]|is_unique[correspondences.correspondence_number,id,{id}]',
        'subject' => 'required|max_length[500]',
        'correspondence_type' => 'required|in_list[LETTER,EMAIL,FAX,MEMO,CIRCULAR,PHONE,MEETING_MINUTES,REPORT,WHATSAPP,SMS,OTHER]',
        'correspondence_direction' => 'required|in_list[INWARD,OUTWARD,INTERNAL]',
        'date_received' => 'required|valid_date',
        'original_date' => 'permit_empty|valid_date',
        'date_sent' => 'permit_empty|valid_date',
        'sender_name' => 'permit_empty|max_length[255]',
        'sender_organization' => 'permit_empty|max_length[255]',
        'sender_contact' => 'permit_empty|max_length[100]',
        'recipient_name' => 'permit_empty|max_length[255]',
        'recipient_organization' => 'permit_empty|max_length[255]',
        'dispatch_method' => 'permit_empty|max_length[50]',
        'priority' => 'permit_empty|in_list[LOW,NORMAL,HIGH,URGENT]',
        'status' => 'permit_empty|in_list[REGISTERED,REFERRED,IN_PROCESS,ACTIONED,COMPLETED,ARCHIVED]',
        'department' => 'permit_empty|max_length[50]',
        'organization_id' => 'permit_empty|numeric',
        'filing_reference' => 'permit_empty|max_length[100]',
        'archive_location' => 'permit_empty|max_length[100]',
        'archive_date' => 'permit_empty|valid_date',
        'registered_by' => 'permit_empty|numeric',
        'parent_correspondence_id' => 'permit_empty|numeric',
        'is_linked' => 'permit_empty|in_list[0,1]',
        'linked_type' => 'permit_empty|in_list[RESPONSE,FOLLOW_UP,RELATED]',
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
            'in_list' => 'Invalid correspondence type',
        ],
        'correspondence_direction' => [
            'required' => 'Correspondence direction is required',
            'in_list' => 'Invalid correspondence direction',
        ],
        'date_received' => [
            'required' => 'Date received is required',
            'valid_date' => 'Please provide a valid date',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedBy', 'setRegistrationDate'];
    protected $beforeUpdate   = ['setUpdatedBy'];
    protected $beforeDelete   = ['setDeletedBy'];

    /**
     * Set created_by before insert
     * 
     * @param array $data
     * @return array
     */
    protected function setCreatedBy(array $data): array
    {
        $session = session();
        if ($session->has('dakoii_user_id')) {
            $data['data']['created_by'] = $session->get('dakoii_user_id');
        }
        return $data;
    }

    /**
     * Set updated_by before update
     * 
     * @param array $data
     * @return array
     */
    protected function setUpdatedBy(array $data): array
    {
        $session = session();
        if ($session->has('dakoii_user_id')) {
            $data['data']['updated_by'] = $session->get('dakoii_user_id');
        }
        return $data;
    }

    /**
     * Set deleted_by before delete
     * 
     * @param array $data
     * @return array
     */
    protected function setDeletedBy(array $data): array
    {
        $session = session();
        if ($session->has('dakoii_user_id')) {
            $data['data']['deleted_by'] = $session->get('dakoii_user_id');
        }
        return $data;
    }

    /**
     * Set registration_date before insert
     * 
     * @param array $data
     * @return array
     */
    protected function setRegistrationDate(array $data): array
    {
        if (!isset($data['data']['registration_date'])) {
            $data['data']['registration_date'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get all correspondences with organization and user details
     * 
     * @return array
     */
    public function getAllWithDetails()
    {
        return $this->select('correspondences.*, organizations.org_name, organizations.org_code, users.name as registered_by_name')
            ->join('organizations', 'organizations.id = correspondences.organization_id', 'left')
            ->join('users', 'users.id = correspondences.registered_by', 'left')
            ->orderBy('correspondences.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get correspondence by ID with details
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithDetails($id)
    {
        return $this->select('correspondences.*, organizations.org_name, organizations.org_code, users.name as registered_by_name, users.email as registered_by_email')
            ->join('organizations', 'organizations.id = correspondences.organization_id', 'left')
            ->join('users', 'users.id = correspondences.registered_by', 'left')
            ->where('correspondences.id', $id)
            ->first();
    }

    /**
     * Get correspondences by organization
     * 
     * @param int $orgId
     * @return array
     */
    public function getByOrganization($orgId)
    {
        return $this->select('correspondences.*, users.name as registered_by_name')
            ->join('users', 'users.id = correspondences.registered_by', 'left')
            ->where('correspondences.organization_id', $orgId)
            ->orderBy('correspondences.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Generate next correspondence number
     * 
     * @param string|null $department
     * @param int|null $year
     * @return string
     */
    public function generateCorrespondenceNumber($department = null, $year = null)
    {
        $year = $year ?? date('Y');
        
        if ($department) {
            // Department-based numbering: HRM-2025/001
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
            // Generic numbering: CORR/2025/00001
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

