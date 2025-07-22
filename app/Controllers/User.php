<?php namespace App\Controllers;

use App\Models\UserModel; // Import User model
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse; // Import RedirectResponse
use CodeIgniter\Exceptions\PageNotFoundException; // Import Exception
use CodeIgniter\Validation\Validation; // Import Validation Service

// Filter 'auth' diterapkan pada rute yang memanggil controller ini jika rute tersebut ada di dalam grup auth

class User extends BaseController
{
    protected UserModel $userModel; // Deklarasikan properti model

    public function __construct()
    {
        // Inisialisasi model user
        $this->userModel = new UserModel();
    }

    /**
     * Menampilkan halaman profil pengguna yang sedang login.
     * Rute ini seharusnya dilindungi oleh filter 'auth'.
     *
     * @return string|RedirectResponse
     * @throws PageNotFoundException Jika user tidak ditemukan (kasus yang sangat jarang).
     */
    public function profile(): string|RedirectResponse
    {
        // Filter 'auth' sudah menangani pengecekan apakah user login
        // Jika user tidak login, filter akan me-redirect ke halaman login.

        $userId = session()->get('user_id'); // Ambil ID user dari sesi

        // Cari data user berdasarkan ID dari sesi
        $user = $this->userModel->find($userId);

        // Jika user tidak ditemukan di database (kasus jarang, mungkin data sesi rusak atau user dihapus)
        if (!$user) {
            // Hancurkan sesi dan redirect ke login dengan pesan error
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Pengguna tidak ditemukan. Silakan login kembali.');
        }

        // Siapkan data untuk view
        $data = [
            'title' => 'Profil Saya',
            'user' => $user, // Kirim data user ke view
            // Ambil validation errors jika ada redirect dari form edit profil
             'errors' => session()->getFlashdata('errors')
        ];

        // Muat view profil
        return view('profile', $data); // Pastikan nama view 'profile' sudah benar (di folder app/Views/)
    }

    /**
     * Menampilkan form untuk mengedit profil pengguna yang sedang login.
     * Rute ini seharusnya dilindungi oleh filter 'auth'.
     *
     * @return string|RedirectResponse
     * @throws PageNotFoundException Jika user tidak ditemukan (kasus yang sangat jarang).
     */
    public function editProfile(): string|RedirectResponse
    {
        // Filter 'auth' sudah menangani pengecekan apakah user login

        $userId = session()->get('user_id'); // Ambil ID user dari sesi

        // Cari data user berdasarkan ID dari sesi
        $user = $this->userModel->find($userId);

        // Jika user tidak ditemukan (kasus jarang)
        if (!$user) {
             session()->destroy(); // Hancurkan sesi
             return redirect()->to('/login')->with('error', 'Pengguna tidak ditemukan.'); // Redirect ke login
        }

        // Siapkan data untuk view (menggunakan input lama jika ada redirect karena validasi error)
        $data = [
            'title' => 'Edit Profil',
            'user' => $user, // Kirim data user
             'errors' => session()->getFlashdata('errors'), // Kirim validation errors
             'oldInput' => session()->getFlashdata('old') // Kirim input lama jika ada
        ];

        // Muat view edit profil
        return view('edit_profile', $data); // Perlu membuat view edit_profile.php
    }

    /**
     * Memproses update data profil pengguna dari form edit.
     * Dipanggil via POST ke /profile/update.
     * Rute ini seharusnya dilindungi oleh filter 'auth'.
     *
     * @return RedirectResponse
     */
    public function updateProfile(): RedirectResponse // Hapus komentar '/*' dan '*/' pada method ini
    {
        // Filter 'auth' sudah menangani pengecekan apakah user login

        $userId = session()->get('user_id'); // Ambil ID user dari sesi
        $user = $this->userModel->find($userId); // Cari user di database

        // Jika user tidak ditemukan (kasus jarang)
        if (!$user) {
             session()->destroy(); // Hancurkan sesi
             return redirect()->to('/login')->with('error', 'Pengguna tidak ditemukan.'); // Redirect ke login
        }

        // Aturan validasi untuk update
        // Perhatikan jika mengizinkan ganti email/username, perlu validasi is_unique
        // yang mengecualikan user ID yang sedang di-update ({id} dalam aturan).
        $rules = [
            'full_name' => 'required',
            'phone' => 'permit_empty|string|max_length[20]',
            'address' => 'permit_empty|string',
            // Contoh aturan validasi email/username jika diperbolehkan diubah:
            // 'email' => "required|valid_email|is_unique[users.email,id,{$userId}]", // Mengecualikan ID user saat ini
            // 'username' => "required|min_length[3]|is_unique[users.username,id,{$userId}]", // Mengecualikan ID user saat ini
        ];
         // Pesan error validasi kustom
         $errors = [
             'full_name' => ['required' => 'Nama lengkap harus diisi.'],
             'phone' => [
                 'string' => 'Format nomor telepon tidak valid.',
                 'max_length' => 'Nomor telepon terlalu panjang.'
             ],
              'address' => ['string' => 'Format alamat tidak valid.'],
             // Tambahkan pesan error untuk email/username jika relevan
             // 'email' => [ /* ... */ ],
             // 'username' => [ /* ... */ ],
         ];


        // Jalankan validasi menggunakan data POST
        if (!$this->validate($rules, $this->request->getPost())) {
             // Jika validasi gagal, redirect kembali ke form edit dengan errors dan input lama
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Siapkan data yang akan diupdate (hanya field yang diizinkan)
        $updateData = [
            'full_name' => $this->request->getPost('full_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            // Sertakan field lain jika diperbolehkan diubah (email, username, dll.)
             // 'email' => $this->request->getPost('email'),
             // 'username' => $this->request->getPost('username'),
        ];

        // Lakukan update pada record user di database
        // Method update() dari Model CodeIgniter 4 menerima ID sebagai parameter pertama
        if ($this->userModel->update($userId, $updateData)) {
             // Jika update berhasil, perbarui data di session jika nama lengkap berubah
             if (session()->get('full_name') !== $updateData['full_name']) {
                 session()->set('full_name', $updateData['full_name']);
             }
            // Redirect kembali ke halaman profil setelah update berhasil dengan pesan sukses
            return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
        } else {
             // Jika update database gagal (misal ada error lain), log error
             log_message('error', 'Failed to update user profile ID ' . $userId . ': ' . json_encode($this->userModel->errors()));
            // Redirect kembali ke form edit dengan pesan error
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui profil.');
        }
    }
}