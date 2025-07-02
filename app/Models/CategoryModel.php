<?php namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'image', 'is_active', 'created_at', 'updated_at']; // <-- Tambahkan kolom timestamp
    protected $useTimestamps = false; // <-- SET FALSE DI SINI
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get only active categories
    public function getActiveCategories(): array
    {
        return $this->where('is_active', 1)->findAll();
    }
}