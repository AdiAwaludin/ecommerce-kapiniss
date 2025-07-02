<?php namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['order_id', 'product_id', 'quantity', 'price'];
    // --- PASTIKAN BARIS INI ADA DAN NILAINYA false ---
    // Tabel order_items TIDAK memiliki kolom timestamp di skema database yang diberikan.
    protected $useTimestamps = false; // Non-aktifkan manajemen timestamp otomatis oleh Model

    // --- PASTIKAN BARIS-BARIS INI TIDAK ADA ATAU DIKOMENTARI ---
    // protected $createdField = 'created_at'; // Tidak relevan jika useTimestamps false
    // protected $updatedField = null; // Tidak relevan jika useTimestamps false


    // Method to get order items with product details for a specific order
    public function getItemsWithProduct(int $orderId): array
    {
        return $this->select('order_items.*, products.name, products.image')
                    ->join('products', 'products.id = order_items.product_id', 'left') // Left join in case product is deleted
                    ->where('order_items.order_id', $orderId)
                    ->findAll();
    }
}