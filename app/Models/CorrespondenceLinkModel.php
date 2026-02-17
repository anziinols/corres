<?php

namespace App\Models;

class CorrespondenceLinkModel extends UuidModel
{
    protected $table            = 'correspondence_links';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['correspondence_id', 'linked_correspondence_id', 'link_type', 'created_at'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    protected function getUuidFields(): array
    {
        return [
            'id',
            'correspondence_id',
            'linked_correspondence_id',
        ];
    }

    protected function beforeInsert(array $data): array
    {
        $data = parent::beforeInsert($data);
        $data = $this->convertForeignKeysToBinary($data);
        return $data;
    }

    private function convertForeignKeysToBinary(array $data): array
    {
        $foreignKeys = ['correspondence_id', 'linked_correspondence_id'];

        foreach ($foreignKeys as $key) {
            if (isset($data['data'][$key]) && is_string($data['data'][$key]) && strlen($data['data'][$key]) === 36) {
                $data['data'][$key] = \App\Helpers\UuidHelper::toBinary($data['data'][$key]);
            }
        }

        return $data;
    }
}
