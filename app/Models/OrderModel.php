<?php namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'order_number', 'total_amount', 'shipping_address', 'phone', 'status', 'payment_method', 'payment_proof', 'notes'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';


    // Get orders with user details (for admin list or dashboard)
    // Includes an optional limit parameter
    public function getOrdersWithUser(int $limit = null): array
    {
        $builder = $this->select('orders.*, users.full_name, users.email')
                   // Join with users table, use left join in case user is deleted
                   ->join('users', 'users.id = orders.user_id', 'left')
                   // Order by creation date, descending
                   ->orderBy('orders.created_at', 'DESC');

        // Apply limit if specified
        if ($limit !== null) {
             $builder->limit($limit);
        }

        // Execute query and return results
        return $builder->findAll();
    }

    // Get a single order with user details by order ID (for admin detail view)
    public function getOrdersWithUserById(int $orderId): ?array
    {
        return $this->select('orders.*, users.full_name, users.email, users.phone as user_phone, users.address as user_address')
                    // Join with users table, left join
                    ->join('users', 'users.id = orders.user_id', 'left')
                    // Filter by the specific order ID
                    ->where('orders.id', $orderId)
                    // Execute query and return a single row or null
                    ->first();
    }


    // Get all orders for a specific user (for customer order list)
    public function getUserOrders(int $userId): array
    {
        // Filter by user ID and order by creation date descending
        return $this->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
    }

    // Generate a simple, unique order number
    public function generateOrderNumber(): string
    {
        // Combines date and a random 4-digit number
        // For high-traffic sites, you might need a more sophisticated approach
        // to guarantee uniqueness and avoid collisions.
        return 'ORD-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));
    }
}