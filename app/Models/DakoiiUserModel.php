<?php

namespace App\Models;

class DakoiiUserModel extends UuidModel
{
    protected $table            = 'dakoii_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'username', 'password'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[255]',
        'username' => 'required|min_length[3]|max_length[100]|is_unique[dakoii_users.username,id,{id}]',
        'password' => 'required|min_length[6]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Name is required',
            'min_length' => 'Name must be at least 3 characters long',
            'max_length' => 'Name cannot exceed 255 characters',
        ],
        'username' => [
            'required'   => 'Username is required',
            'min_length' => 'Username must be at least 3 characters long',
            'max_length' => 'Username cannot exceed 100 characters',
            'is_unique'  => 'This username is already taken',
        ],
        'password' => [
            'required'   => 'Password is required',
            'min_length' => 'Password must be at least 6 characters long',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    public function verifyCredentials(string $username, string $password)
    {
        $user = $this->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
}
