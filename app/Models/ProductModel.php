<?php namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['category_id', 'name', 'description', 'price', 'stock', 'weight', 'image', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Method to get active products with their category name (for public shop/home)
    // Filters for is_active = 1 and stock > 0
    public function getActiveProductsWithCategory(): array
    {
        return $this->select('products.*, categories.name as category_name')
                   // Join with categories table, use left join in case a product has no category
                   ->join('categories', 'categories.id = products.category_id', 'left')
                   // Filter by active products
                   ->where('products.is_active', 1)
                   // Filter by products that have stock greater than 0
                   ->where('products.stock >', 0)
                   // Execute query and return all matching results
                   ->findAll();
    }

    // Method to get only active products that are in stock (simpler version)
    public function getActiveProducts(): array
    {
        // Filter by active products and stock > 0
        return $this->where('is_active', 1)->where('stock >', 0)->findAll();
    }

     // Method to get ALL products with their category name (for admin panel)
     // Does NOT filter by active status or stock
     public function getAllProductsWithCategory(): array
     {
          return $this->select('products.*, categories.name as category_name')
                     // Join with categories table, use left join
                     ->join('categories', 'categories.id = products.category_id', 'left')
                     // Execute query and return all results
                     ->findAll();
     }
}