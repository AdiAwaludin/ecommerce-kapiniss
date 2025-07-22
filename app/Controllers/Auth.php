<?php namespace App\Controllers;

use App\Models\UserModel; // Pastikan Model user di-import
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse; // Pastikan ini di-import
use CodeIgniter\Validation\Validation; // Pastikan ini di-import

class Auth extends BaseController
{
    // Tidak perlu memuat model di constructor jika hanya digunakan di method spesifik

    /**
     * Menampilkan halaman login.
     */
    public function login(): string|RedirectResponse
    {
        // Redirect jika user sudah login
        if (session()->get('logged_in')) {
            // Cek role untuk menentukan ke mana redirect
            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin'); // Redirect ke dashboard admin jika admin
            } else {
                return redirect()->to('/'); // Redirect ke beranda jika customer
            }
        }
        $data['title'] = 'Login'; // Judul halaman
        // Ambil validation errors jika ada redirect sebelumnya dari loginProcess
        $data['errors'] = session()->getFlashdata('errors');
        // Memuat view auth/login.php
        return view('auth/login', $data); // <-- Pastikan nama view 'auth/login' sudah benar di folder app/Views/auth/
    }

    /**
     * Memproses data login dari form.
     * Dipanggil via POST ke /login (sesuai rute).
     */
    public function loginProcess(): RedirectResponse
    {
        $userModel = new UserModel(); // Inisialisasi Model User

        // Aturan validasi input form login
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];
        // Pesan error validasi kustom
        $errors = [
            'email' => [
                'required'    => 'Email harus diisi.',
                'valid_email' => 'Email tidak valid.',
            ],
            'password' => [
                'required' => 'Password harus diisi.',
            ],
        ];

        // Jalankan validasi terhadap data POST
        if (!$this->validate($rules, $errors)) {
            // Jika validasi gagal, redirect kembali ke halaman login dengan input lama dan error
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email'); // Ambil email dari input form
        $password = $this->request->getPost('password'); // Ambil password dari input form (masih dalam teks biasa)

        // Cari user di database berdasarkan email yang dimasukkan
        $user = $userModel->getUserByEmail($email); // Gunakan method dari UserModel

        // --- BARIS LOGGING DIAGNOSIS (Opsional, Hapus jika tidak lagi diperlukan) ---
        log_message('debug', 'Attempting login process for email: ' . $email);
        log_message('debug', 'User fetched: ' . json_encode($user));
        if ($user) {
             log_message('debug', 'Password hash from DB: ' . ($user['password'] ?? 'NULL'));
             // Lakukan verifikasi password dan log hasilnya
             $password_match = password_verify($password, $user['password'] ?? '');
             log_message('debug', 'Password verification result: ' . ($password_match ? 'TRUE' : 'FALSE'));
             log_message('debug', 'Is user active: ' . ($user['is_active'] ?? 'NULL')); // Log status aktif
        } else {
             log_message('debug', 'User with email ' . $email . ' not found.');
        }
        // --- AKHIR BARIS LOGGING DIAGNOSIS ---


        // Verifikasi password dan status aktif user
        // Cek apakah user ditemukan ($user) DAN password cocok (password_verify) DAN user aktif (is_active)
        if ($user && password_verify($password, $user['password']) && $user['is_active'] == 1) { // Periksa is_active == 1
            // Jika login berhasil (user ditemukan, password cocok, dan aktif)

            // Set data session untuk menandai user sudah login
            $sessionData = [
                'user_id'   => $user['id'],
                'username'  => $user['username'],
                'full_name' => $user['full_name'],
                'role'      => $user['role'],
                'logged_in' => true // Bendera penanda login
            ];
            session()->set($sessionData); // Simpan data ke session

            // Redirect ke halaman tujuan (jika ada redirect_url di flashdata) atau dashboard/home
            $redirectUrl = session()->getFlashdata('redirect_url') ?? ($user['role'] == 'admin' ? '/admin' : '/');
          
            log_message('debug', 'Login successful for user ID: ' . $user['id'] . ', redirecting to ' . $redirectUrl); // Logging sukses
            return redirect()->to($redirectUrl)->with('success', 'Login berhasil! Selamat datang, ' . esc($user['full_name']) . '.'); // Redirect dengan pesan sukses

        } else {
            // Jika login gagal (user tidak ditemukan, atau password salah, atau user tidak aktif)
            log_message('debug', 'Login failed for email: ' . $email . ' (User not found, password mismatch, or inactive)'); // Logging gagal
            return redirect()->back()->withInput()->with('error', 'Email atau password salah!'); // Redirect kembali ke halaman login dengan pesan error
        }
    }

    /**
     * Menampilkan halaman registrasi.
     */
    public function register(): string|RedirectResponse
    {
        // Redirect jika user sudah login
         if (session()->get('logged_in')) {
             if (session()->get('role') === 'admin') {
                return redirect()->to('/admin');
            } else {
                return redirect()->to('/');
            }
        }
        $data['title'] = 'Daftar Akun'; // Judul halaman
        // Ambil validation errors jika ada redirect sebelumnya dari registerProcess
        $data['errors'] = session()->getFlashdata('errors');
        // Memuat view auth/register.php
        return view('auth/register', $data); // <-- Pastikan nama view 'auth/register' sudah benar di folder app/Views/auth/
    }

    /**
     * Memproses data registrasi dari form.
     * Dipanggil via POST ke /register (sesuai rute).
     */
    public function registerProcess(): RedirectResponse
    {
        $userModel = new UserModel(); // Inisialisasi Model User

        // Aturan validasi input form registrasi
        $rules = [
            'full_name' => [
                'rules' => 'required',
                'errors' => ['required' => 'Nama lengkap harus diisi.']
            ],
            'username' => [
                'rules' => 'required|min_length[3]|is_unique[users.username]',
                'errors' => [
                    'required' => 'Nama pengguna harus diisi.',
                    'min_length' => 'Nama pengguna minimal 3 karakter.',
                    'is_unique' => 'Nama pengguna sudah terdaftar.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email harus diisi.',
                    'valid_email' => 'Email tidak valid.',
                    'is_unique' => 'Email sudah terdaftar.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password harus diisi.',
                    'min_length' => 'Password minimal 6 karakter.'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password harus diisi.',
                    'matches' => 'Konfirmasi password tidak cocok.'
                ]
            ],
            'phone' => [
                'rules' => 'permit_empty|string|max_length[20]',
                'errors' => [
                    'string' => 'Format nomor telepon tidak valid.',
                    'max_length' => 'Nomor telepon terlalu panjang.'
                    ]
            ],
            'address' => [
               'rules' => 'permit_empty|string',
               'errors' => ['string' => 'Format alamat tidak valid.']
            ],
        ];

        // Jalankan validasi terhadap data POST
        if (!$this->validate($rules, $this->request->getPost())) { // Kirim data POST (termasuk file jika ada) untuk validasi
            // Redirect kembali dengan input lama dan error validasi
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Siapkan data untuk disimpan ke database
        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'), // Password akan di-hash oleh Model::beforeInsert
            'full_name' => $this->request->getPost('full_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'role' => 'customer', // Default role adalah customer
            'is_active' => 1 // Default status aktif
        ];

        // Coba simpan data user baru ke database
        if ($userModel->insert($data)) {
            // Jika sukses, redirect ke halaman login dengan pesan sukses
            return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
        } else {
            // Jika gagal menyimpan ke database (misal error lain yang tidak tertangkap validasi unik)
             log_message('error', 'Failed to insert user data: ' . json_encode($userModel->errors())); // Log error model jika ada
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat registrasi. Mohon coba lagi.'); // Redirect dengan pesan error
        }
    }

    /**
     * Proses logout pengguna.
     * Dipanggil via GET ke /logout (sesuai rute).
     */
    public function logout(): RedirectResponse
    {
        // Hancurkan seluruh sesi user
        session()->destroy();
        // Redirect ke halaman login dengan pesan sukses
        return redirect()->to('/login')->with('success', 'Logout berhasil!');
    }
}