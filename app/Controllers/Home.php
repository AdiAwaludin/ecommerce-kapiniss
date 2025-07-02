<?php namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ContactModel; // <-- Import ContactModel
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException; // Import exception
use CodeIgniter\HTTP\RedirectResponse; // Import RedirectResponse
// use CodeIgniter\Validation\Validation; // Import Validation (optional if using $this->validate())


class Home extends BaseController
{
    // Deklarasikan property model
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;
    // protected ContactModel $contactModel; // Opsional: deklarasi properti jika ingin dipakai di banyak method Home

    public function __construct()
    {
        // Inisiasi model dalam konstruktor
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        // $this->contactModel = new ContactModel(); // Opsional: inisiasi di konstruktor
    }

    /**
     * Menampilkan halaman beranda dengan produk unggulan dan kategori.
     * Mengambil hanya produk aktif yang memiliki stok.
     *
     * @return string Mengembalikan hasil render view home/index.
     */
    public function index(): string
    {
        // Ambil hanya produk aktif yang tersedia (stok > 0)
        $products = $this->productModel->getActiveProducts();

        // Ambil hanya kategori aktif untuk navigasi atau tampilan
        $categories = $this->categoryModel->getActiveCategories();

        // Siapkan data yang akan dikirim ke view
        $data = [
            'title' => 'Beranda - Keripik Kapinis', // Judul halaman
            'products' => $products, // Data produk yang akan ditampilkan
            'categories' => $categories // Data kategori untuk filter atau tampilan lain
        ];

        // Render view home/index menggunakan layout template
        return view('home/index', $data);
    }

    /**
     * Menampilkan halaman detail untuk satu produk.
     * Produk diambil berdasarkan ID dari segmen URI.
     *
     * @param int $id ID produk dari URI segment.
     * @return string Mengembalikan hasil render view home/product_detail.
     * @throws PageNotFoundException Jika produk tidak ditemukan, tidak aktif, atau stoknya nol/negatif.
     */
    public function product(int $id): string
    {
        // Cari produk berdasarkan ID
        $product = $this->productModel->find($id);

        // Periksa apakah produk ada, aktif, dan memiliki stok yang tersedia (publik hanya bisa lihat ini)
        if (!$product || !$product['is_active'] || $product['stock'] <= 0) {
            // Jika tidak memenuhi kriteria, tampilkan error 404 Page Not Found (lebih aman)
            throw new PageNotFoundException('Produk tidak ditemukan atau tidak tersedia.');
        }

        // Siapkan data untuk view
        $data = [
            'title' => $product['name'], // Gunakan nama produk sebagai judul halaman
            'product' => $product // Data produk yang detail
        ];

        // Render view product_detail menggunakan layout template
        return view('home/product_detail', $data);
    }

    /**
     * Menampilkan halaman toko (Shop) dengan semua produk aktif dan stok tersedia.
     * Mendukung filtering berdasarkan kategori dan pencarian berdasarkan nama/deskripsi
     * melalui parameter GET di URL.
     *
     * @return string Mengembalikan hasil render view home/shop.
     */
    public function shop(): string
    {
        // Ambil parameter filter kategori dan pencarian dari permintaan GET
        $categoryId = $this->request->getGet('category');
        $search = $this->request->getGet('search');

        // Mulai membangun query untuk mengambil produk yang aktif dan stoknya lebih dari nol
        $builder = $this->productModel->where('is_active', 1)->where('stock >', 0);

        // Jika parameter category_id ada dan tidak kosong, terapkan filter kategori
        if (!empty($categoryId)) {
            // Opsional: Bisa ditambahkan validasi tambahan untuk memastikan category_id yang diminta memang ada dan aktif.
             $category = $this->categoryModel->find($categoryId); // Cek di tabel kategori
             if ($category && $category['is_active']) { // Jika kategori valid dan aktif
                 $builder->where('category_id', $categoryId); // Tambahkan kondisi filter
             } else {
                 // Jika category_id di URL tidak valid atau kategori tidak aktif, abaikan filter ini
                 $categoryId = null; // Set selectedCategory menjadi null agar link filter tidak terlihat aktif yang salah
                 session()->setFlashdata('info', 'Kategori yang dipilih tidak ditemukan atau tidak aktif.'); // Beri notifikasi info
             }
        }

        // Jika parameter search ada dan tidak kosong, terapkan filter pencarian
        if (!empty($search)) {
             // Terapkan pencarian pada nama produk ATAU deskripsi produk
             // Gunakan groupStart/groupEnd untuk mengelompokkan kondisi OR
             $builder->groupStart()
                     ->like('products.name', $search) // Cari di kolom nama produk
                     ->orLike('products.description', $search) // Cari di kolom deskripsi
                     ->groupEnd();
        }

        // Jalankan query yang sudah dibangun dan ambil hasilnya (produk-produk yang terfilter)
         $products = $builder->findAll();

         // Ambil daftar semua kategori aktif untuk ditampilkan di sidebar filter di view
        $categories = $this->categoryModel->getActiveCategories();


        // Siapkan data yang akan dikirim ke view
        $data = [
            'title' => 'Toko Online - Keripik Kapinis', // Judul halaman Shop
            'products' => $products, // Produk hasil filter/search
            'categories' => $categories, // Daftar kategori untuk sidebar
            'selectedCategory' => $categoryId, // ID kategori yang sedang aktif difilter
            'search' => $search // Term pencarian yang sedang aktif
        ];

        // Render view home/shop menggunakan layout template
        return view('home/shop', $data);
    }

    /**
     * Menampilkan halaman Tentang Kami (dummy/placeholder).
     * Membutuhkan file view di app/Views/home/about.php.
     *
     * @return string Mengembalikan hasil render view about.
     */
    public function about(): string
    {
         $data['title'] = 'Tentang Kami';
         // Render view dummy about menggunakan layout
         return view('home/about', $data);
    }

    /**
     * Menampilkan halaman Kontak Kami (dummy/placeholder).
     * Membutuhkan file view di app/Views/home/contact.php.
     *
     * @return string Mengembalikan hasil render view contact.
     */
    public function contact(): string
    {
         $data['title'] = 'Kontak Kami';
         // Render view dummy contact menggunakan layout
         return view('home/contact', $data);
    }

    /**
     * Menangani pengiriman form kontak.
     * Melakukan validasi dan mengarahkan kembali dengan pesan.
     * Menyimpan pesan ke database.
     *
     * @return RedirectResponse Mengarahkan pengguna kembali setelah proses.
     */
    public function sendContact(): RedirectResponse
    {
         // Aturan validasi untuk form kontak
         $rules = [
             'name' => 'required|string|max_length[100]',
             'email' => 'required|valid_email|max_length[254]', // Max length standar untuk email
             'subject' => 'permit_empty|string|max_length[255]', // Permit_empty mengizinkan kosong
             'message' => 'required|string',
              // Tambahkan validasi honeypot jika Anda mengaktifkan global filter honeypot
              // 'honeypot' => 'required', // Contoh jika menggunakan honeypot
         ];

         // Pesan error kustom untuk validasi
         $errors = [
             'name' => [
                 'required' => 'Nama lengkap harus diisi.',
                 'max_length' => 'Nama lengkap terlalu panjang (maksimal 100 karakter).',
             ],
             'email' => [
                 'required' => 'Email harus diisi.',
                 'valid_email' => 'Format email tidak valid.',
                 'max_length' => 'Email terlalu panjang (maksimal 254 karakter).'
             ],
             'subject' => [
                  'max_length' => 'Subjek terlalu panjang (maksimal 255 karakter).'
             ],
             'message' => [
                 'required' => 'Pesan harus diisi.',
             ],
         ];

         // Lakukan validasi data yang masuk dari form POST
         // Menggunakan helper $this->validate() yang sudah disediakan BaseController
         if (!$this->validate($rules, $errors)) {
             // Jika validasi gagal, arahkan kembali ke halaman sebelumnya (form kontak)
             // dengan input yang sudah diisi (old input) dan daftar error validasi
             return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
         }

         // Data valid, siapkan data untuk disimpan ke database
         $dataKontak = [
             'name' => $this->request->getPost('name'),
             'email' => $this->request->getPost('email'),
             'subject' => $this->request->getPost('subject'),
             'message' => $this->request->getPost('message'),
             'is_read' => 0, // Default status belum dibaca
             // created_at dan updated_at akan otomatis diisi oleh Model karena $useTimestamps = true
         ];

         // Inisiasi ContactModel dan simpan data ke database
         $contactModel = new ContactModel(); // Inisiasi di dalam method jika tidak di konstruktor

         if ($contactModel->insert($dataKontak)) {
              // Jika berhasil disimpan
              // Opsional: Kirim notifikasi email ke admin di sini
              // $emailService = \Config\Services::email(); // Dapatkan instance Email Service
              // $emailService->setTo('email_admin@keripikpisang.com'); // Email penerima (misal, admin)
              // $emailService->setFrom($dataKontak['email'], $dataKontak['name']); // Pengirim (dari input user)
              // $emailService->setSubject($dataKontak['subject'] ?: 'Pesan dari Form Kontak Website'); // Subjek
              // $emailService->setMessage("Dari: {$dataKontak['name']} <{$dataKontak['email']}>\n\nPesan:\n{$dataKontak['message']}"); // Isi pesan
              //
              // if ($emailService->send()) {
              //     log_message('info', 'Email kontak berhasil dikirim ke admin.');
              // } else {
              //     log_message('error', 'Gagal mengirim email kontak ke admin: ' . $emailService->printDebugger(['headers', 'subject', 'body']));
              // }


             log_message('info', 'Form kontak berhasil disubmit dan disimpan ke DB. Data: ' . json_encode($dataKontak));
             return redirect()->to('/contact')->with('success', 'Pesan Anda berhasil dikirim! Kami akan segera merespons.');
         } else {
              // Jika gagal menyimpan ke database (meskipun validasi form lewat, misal error DB lain)
              log_message('error', 'Gagal menyimpan data kontak ke DB: ' . json_encode($contactModel->errors()));
             return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat merekam pesan Anda. Mohon coba lagi.');
         }
    }

    // ... (metode-metode lain seperti index, product, shop, about, contact sebelumnya) ...

}