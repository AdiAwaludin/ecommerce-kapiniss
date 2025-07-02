<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'password', 'full_name', 'phone', 'address', 'role', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Define callback functions to run before inserting or updating data
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // Method to hash the password before it's stored in the database
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            // Use PHP's built-in password_hash for secure password storage
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data; // Return the modified data array
    }

    // Custom method to find a user by their email address
    public function getUserByEmail(string $email): ?array // Returns array or null
    {
        return $this->where('email', $email)->first();
    }

    // Custom method to find a user by their username
    public function getUserByUsername(string $username): ?array // Returns array or null
    {
        return $this->where('username', $username)->first();
    }
}