<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Helpers\UuidHelper;

class UuidModel extends Model
{
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateUuid', 'setCreatedBy'];
    protected $beforeUpdate = ['setUpdatedBy'];
    protected $beforeDelete = ['setDeletedBy'];
    protected $afterFind = ['convertUuidToString'];

    protected function generateUuid(array $data): array
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = UuidHelper::generateBinary();
        } elseif (is_string($data['data']['id']) && strlen($data['data']['id']) === 36) {
            $data['data']['id'] = UuidHelper::toBinary($data['data']['id']);
        }

        return $data;
    }

    protected function convertUuidToString(array $data): array
    {
        if (isset($data['data'])) {
            $data['data'] = $this->convertRecordUuids($data['data']);
        } elseif (isset($data['data'][0]) && is_array($data['data'][0])) {
            foreach ($data['data'] as &$record) {
                $record = $this->convertRecordUuids($record);
            }
        }

        return $data;
    }

    protected function convertRecordUuids(array $record): array
    {
        $uuidFields = $this->getUuidFields();

        foreach ($uuidFields as $field) {
            if (isset($record[$field]) && $record[$field] !== null) {
                try {
                    $record[$field] = UuidHelper::toString($record[$field]);
                } catch (\Exception $e) {
                }
            }
        }

        return $record;
    }

    protected function getUuidFields(): array
    {
        return [
            'id',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    public function find($id = null)
    {
        if ($id !== null && is_string($id) && strlen($id) === 36) {
            $id = UuidHelper::toBinary($id);
        }

        return parent::find($id);
    }

    public function delete($id = null, bool $purge = false)
    {
        if ($id !== null && is_string($id) && strlen($id) === 36) {
            $id = UuidHelper::toBinary($id);
        }

        return parent::delete($id, $purge);
    }

    protected function setCreatedBy(array $data): array
    {
        if (!isset($data['data']['created_by'])) {
            $session = session();
            $userIdKey = $this->getUserIdSessionKey();

            if ($session->has($userIdKey)) {
                $userId = $session->get($userIdKey);
                if (is_string($userId) && strlen($userId) === 36) {
                    $userId = UuidHelper::toBinary($userId);
                }
                $data['data']['created_by'] = $userId;
            }
        }
        return $data;
    }

    protected function setUpdatedBy(array $data): array
    {
        if (!isset($data['data']['updated_by'])) {
            $session = session();
            $userIdKey = $this->getUserIdSessionKey();

            if ($session->has($userIdKey)) {
                $userId = $session->get($userIdKey);
                if (is_string($userId) && strlen($userId) === 36) {
                    $userId = UuidHelper::toBinary($userId);
                }
                $data['data']['updated_by'] = $userId;
            }
        }
        return $data;
    }

    protected function setDeletedBy(array $data): array
    {
        $session = session();
        $userIdKey = $this->getUserIdSessionKey();

        if ($session->has($userIdKey)) {
            $userId = $session->get($userIdKey);
            if (is_string($userId) && strlen($userId) === 36) {
                $userId = UuidHelper::toBinary($userId);
            }
            $this->update($data['id'], ['deleted_by' => $userId]);
        }
        return $data;
    }

    protected function getUserIdSessionKey(): string
    {
        return 'dakoii_user_id';
    }
}
