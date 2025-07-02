<?php namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'subject', 'message', 'is_read']; // Kolom yang diizinkan diisi
    protected $useTimestamps = true; // Menggunakan kolom created_at dan updated_at otomatis
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $useSoftDeletes = false; // Tidak menggunakan soft deletes untuk tabel ini

    // Opsional: Metode tambahan jika diperlukan, misal get unread messages
    // public function getUnreadMessages()
    // {
    //     return $this->where('is_read', 0)->orderBy('created_at', 'DESC')->findAll();
    // }
}