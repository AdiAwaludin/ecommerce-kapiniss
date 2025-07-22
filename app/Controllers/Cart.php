<?php namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\RedirectResponse;

// Filter 'auth' diterapkan di Routes.php untuk grup ini (misal '/cart', '/cart/add', dll.)
// Artinya, semua method di controller ini memerlukan user untuk login.
// Jika user belum login, AuthFilter akan me-redirect ke halaman login.

class Cart extends BaseController
{
    // Deklarasi properti untuk Model
    protected CartModel $cartModel;
    protected ProductModel $productModel;

    public function __construct()
    {
        // Inisialisasi model
        // Pastikan Model-model ini memiliki konfigurasi timestamp yang benar
        // CartModel.php -> protected $useTimestamps = false;
        // ProductModel.php -> protected $useTimestamps = true; (sesuai skema DB)
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Menampilkan keranjang belanja user.
     * Method ini dipanggil saat user mengakses URL '/cart'.
     * AuthFilter di Routes.php sudah menangani pengecekan login.
     *
     * @return string Rendered view halaman keranjang.
     */
    public function index(): string
    {
        // Filter auth sudah menangani pengecekan login di awal request

        $userId = session()->get('user_id'); // Ambil ID user dari sesi

        // Ambil semua item di keranjang user dari database
        $cartItems = $this->cartModel->getCartItems($userId);

        // Hitung total belanja dari item-item di keranjang
        $total = $this->cartModel->getCartTotal($userId);

        // Siapkan data untuk dikirim ke view
        $data = [
            'title' => 'Keranjang Belanja',
            'cartItems' => $cartItems,
            'total' => $total
        ];

        // Render view halaman keranjang dengan data yang disiapkan
        return view('cart/index', $data);
    }

    /**
     * Menambahkan produk ke keranjang (endpoint AJAX).
     * Method ini dipanggil via AJAX POST dari tombol 'Tambah ke Keranjang' di halaman produk/toko.
     *
     * @return ResponseInterface Respon JSON yang mengindikasikan sukses atau gagal,
     *                           beserta pesan dan jumlah item keranjang terbaru.
     */
    public function add(): ResponseInterface
    {
        // Filter auth sudah menangani pengecekan login sebelum method ini dijalankan

        // Ambil data product_id dan quantity dari body request POST
        $productId = $this->request->getPost('product_id');
        $quantity = (int)$this->request->getPost('quantity') ?: 1; // Pastikan kuantitas adalah integer, default 1 jika kosong/0

        $userId = session()->get('user_id'); // Ambil ID user dari sesi

        // Validasi dasar input
        if (empty($productId) || $quantity <= 0) {
             // Jika data tidak valid, kirim respon JSON error
             return $this->response->setJSON(['success' => false, 'message' => 'Data produk atau kuantitas tidak valid.']);
        }

        // Cari produk di database berdasarkan product_id
        $product = $this->productModel->find($productId);

        // Periksa apakah produk ditemukan dan statusnya aktif
        if (!$product || !$product['is_active']) {
             // Jika produk tidak ditemukan atau tidak aktif, kirim respon JSON error
             return $this->response->setJSON(['success' => false, 'message' => 'Produk tidak tersedia.']);
        }

        // Periksa apakah stok produk tersedia
        if ($product['stock'] <= 0) {
             // Jika stok habis, kirim respon JSON error
             return $this->response->setJSON(['success' => false, 'message' => 'Stok produk "' . esc($product['name']) . '" habis.']);
        }

        // Periksa jika kuantitas yang ingin ditambahkan melebihi stok yang tersedia
        if ($quantity > $product['stock']) {
             // Jika kuantitas diminta melebihi stok, kirim respon JSON error
             return $this->response->setJSON(['success' => false, 'message' => 'Kuantitas yang diminta (' . $quantity . ') melebihi stok tersedia (' . $product['stock'] . ').']);
        }


        // Periksa apakah item produk ini sudah ada di keranjang user yang sedang login
       $existingItem = $this->cartModel->where('user_id', $userId)->where('product_id', $productId)->first();

if ($existingItem) {
    // ... (kode update item yang sudah ada, tidak menyentuh created_at/updated_at) ...
} else {
    // Item belum ada, masukkan baru
     // ... (cek stok) ...
    $this->cartModel->insert([
        'user_id' => $userId,
        'product_id' => $productId,
        'quantity' => $quantity,
                // Tidak perlu menyediakan 'created_at' di sini jika DB sudah diset DEFAULT CURRENT_TIMESTAMP
            ]);
        }

        // Ambil jumlah item terbaru di keranjang user
        $newCartCount = $this->cartModel->countCartItems($userId);

        // Kirim respon JSON sukses dengan pesan dan jumlah item keranjang terbaru
        return $this->response->setJSON([
             'success' => true,
             'message' => 'Produk berhasil ditambahkan ke keranjang',
             'cart_count' => $newCartCount // Sertakan jumlah item untuk diupdate di frontend
         ]);
    }

    /**
     * Mengupdate kuantitas item di keranjang.
     * Method ini dipanggil dari form update di halaman keranjang (biasanya via POST).
     *
     * @param int $id ID item di tabel cart.
     * @return RedirectResponse Redirect kembali ke halaman keranjang dengan pesan.
     */
    public function update(int $id): RedirectResponse
    {
        // Filter auth sudah menangani pengecekan login di awal request

        // Ambil kuantitas baru dari input POST
        $quantity = (int)$this->request->getPost('quantity');
        $userId = session()->get('user_id'); // Ambil ID user dari sesi

        // Cari item keranjang berdasarkan ID dan pastikan milik user yang sedang login
        $cartItem = $this->cartModel->where('id', $id)->where('user_id', $userId)->first();

        // Jika item tidak ditemukan atau bukan milik user ini
        if (!$cartItem) {
            return redirect()->back()->with('error', 'Item keranjang tidak ditemukan.');
        }

         // Jika kuantitas baru 0 atau kurang, anggap sebagai permintaan hapus item
        if ($quantity < 1) {
            return $this->remove($id); // Panggil method remove untuk menghapus item
        }

        // Cari produk terkait dengan item keranjang
        $product = $this->productModel->find($cartItem['product_id']);

        // Periksa ketersediaan produk dan status aktifnya
        if (!$product || !$product['is_active']) {
             // Jika produk tidak lagi aktif/tersedia, hapus item ini dari keranjang dan beri peringatan
             $this->cartModel->delete($id);
             return redirect()->back()->with('warning', 'Produk "' . esc($cartItem['name']) . '" sudah tidak tersedia dan dihapus dari keranjang.');
        }

        // Penting: Periksa stok yang tersedia terhadap kuantitas *baru* yang diminta
        if ($quantity > $product['stock']) {
            // Jika kuantitas baru melebihi stok, beri pesan error
            return redirect()->back()->with('error', 'Stok "' . esc($product['name']) . '" tidak mencukupi. Maksimal: ' . $product['stock']);
        }

        // Lakukan update kuantitas item keranjang di database
        // Karena CartModel diset useTimestamps = false, Model tidak akan mencoba update updated_at
        $this->cartModel->update($id, ['quantity' => $quantity]);

        // Redirect kembali ke halaman keranjang dengan pesan sukses
        return redirect()->back()->with('success', 'Kuantitas keranjang berhasil diupdate.');
    }

    /**
     * Menghapus item dari keranjang.
     * Method ini dipanggil dari link hapus di halaman keranjang (biasanya via GET, meskipun POST lebih aman).
     *
     * @param int $id ID item di tabel cart.
     * @return RedirectResponse Redirect kembali ke halaman keranjang dengan pesan.
     */
    public function remove(int $id): RedirectResponse
    {
        // Filter auth sudah menangani pengecekan login di awal request

        $userId = session()->get('user_id'); // Ambil ID user dari sesi

        // Cari item keranjang berdasarkan ID dan pastikan milik user yang sedang login
        $cartItem = $this->cartModel->where('id', $id)->where('user_id', $userId)->first();

        // Jika item tidak ditemukan atau bukan milik user ini
        if (!$cartItem) {
            return redirect()->back()->with('error', 'Item keranjang tidak ditemukan.');
        }

        // Hapus item dari tabel cart
        $this->cartModel->delete($id);

        // Redirect kembali ke halaman keranjang dengan pesan sukses
        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Mengembalikan jumlah item di keranjang user (endpoint AJAX).
     * Digunakan oleh JavaScript di layout template untuk menampilkan jumlah di navbar.
     * Method ini bisa dipanggil bahkan jika user belum login, jadi perlu penanganan.
     *
     * @return ResponseInterface Respon JSON berisi jumlah item (integer).
     */
    public function count(): ResponseInterface
    {
         // Periksa apakah user sudah login
         if (!session()->get('logged_in')) {
             // Jika tidak login, kembalikan 0 item
             return $this->response->setJSON(0);
         }
         $userId = session()->get('user_id'); // Ambil ID user dari sesi

         // Hitung jumlah item di keranjang user
         $count = $this->cartModel->countCartItems($userId);

         // Kembalikan jumlah item dalam format JSON
         return $this->response->setJSON($count);
    }
}