<?php namespace App\Models;

use CodeIgniter\Model;

class ContactMessageModel extends Model
{
    protected $table      = 'contact_messages'; // Sesuaikan dengan nama tabel Anda
    protected $primaryKey = 'id'; // Sesuaikan primary key Anda

    protected $useAutoIncrement = true; // Sesuaikan jika tidak auto increment

    protected $returnType     = 'array'; // atau 'object'
    protected $useSoftDeletes = false; // Set true jika menggunakan soft delete

    protected $allowedFields = ['name', 'email', 'subject', 'message', 'created_at', 'updated_at']; // Sesuaikan kolom tabel Anda

    // Default CodeIgniter Model menggunakan timestamps secara otomatis
    // jika ada kolom 'created_at' dan 'updated_at' di allowedFields dan tabel
    protected $useTimestamps = true; // Biarkan true agar otomatis terisi
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Hanya jika useSoftDeletes true

    // Validation
    protected $validationRules    = []; // Tambahkan aturan validasi jika perlu
    protected $validationMessages = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}