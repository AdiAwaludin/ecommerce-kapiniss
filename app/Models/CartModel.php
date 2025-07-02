<?php namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'product_id', 'quantity'];
    // --- UBAH BARIS INI MENJADI false ---
    protected $useTimestamps = false; // Non-aktifkan manajemen timestamp otomatis oleh Model
    // --- BARIS INI BISA DIHAPUS ATAU DIKOMENTARI JIKA useTimestamps false ---
    // protected $createdField = 'created_at';
    // protected $updatedField = null;


    public function getCartItems(int $userId): array
    {
        // ... (kode lainnya tetap sama) ...
        return $this->select('cart.*, products.name, products.price, products.image, products.stock')
                   ->join('products', 'products.id = cart.product_id', 'left')
                   ->where('cart.user_id', $userId)
                   ->findAll();
    }

    public function getCartTotal(int $userId): float
    {
        // ... (kode lainnya tetap sama) ...
         $items = $this->getCartItems($userId);
        $total = 0;
        foreach ($items as $item) {
            $total += (float)$item['price'] * (int)$item['quantity'];
        }
        return $total;
    }

    public function countCartItems(int $userId): int
    {
        // ... (kode lainnya tetap sama) ...
        return $this->where('user_id', $userId)->countAllResults();
    }
}