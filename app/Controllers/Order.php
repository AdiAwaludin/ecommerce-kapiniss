<?php namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\CartModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Validation\Validation; // Pastikan baris ini ada

// Filter 'auth' diterapkan di Routes.php untuk grup rute ini.

class Order extends BaseController
{
    protected OrderModel $orderModel;
    protected CartModel $cartModel;
    protected OrderItemModel $orderItemModel;
    protected ProductModel $productModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->cartModel = new CartModel();
        $this->orderItemModel = new OrderItemModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();

        // Pastikan helper dimuat, meskipun sudah di BaseController
        // helper(['form', 'url', 'text']); // Biasanya sudah di BaseController
    }

    public function checkout(): string|RedirectResponse
    {
        // Filter auth memastikan pengguna login
        $userId = session()->get('user_id');
        $cartItems = $this->cartModel->getCartItems($userId);

        if (empty($cartItems)) {
            return redirect()->to('/cart')->with('info', 'Keranjang belanja kosong. Tidak dapat checkout.');
        }

        // --- Pre-checkout Stock Check and Auto-correction ---
        $hasStockIssue = false;
        // Clone cartItems to iterate over a copy while potentially modifying the original $cartItems array indirectly via $this->cartModel
        $cartItemsCopy = $cartItems;
        foreach ($cartItemsCopy as $item) {
            $product = $this->productModel->find($item['product_id']);

            // Check if product is invalid or stock is insufficient BEFORE transaction
            if (!$product || !$product['is_active'] || $product['stock'] < $item['quantity']) {
                $hasStockIssue = true;
                log_message('warning', 'Stock issue for product ID ' . $item['product_id'] . ' (in cart item ' . $item['id'] . ') during checkout pre-check. Requested: ' . $item['quantity'] . ', Available: ' . ($product['stock'] ?? 'N/A') . '. Product Active: ' . ($product['is_active'] ?? 'N/A'));

                 if ($product && $product['is_active'] && $product['stock'] > 0 && $product['stock'] < $item['quantity']) {
                      // If product exists and is active but stock is less than cart quantity,
                      // update cart quantity to the available stock.
                      $this->cartModel->update($item['id'], ['quantity' => $product['stock']]);
                      log_message('info', 'Auto-corrected cart quantity for item ID ' . $item['id'] . ' to available stock: ' . $product['stock']);
                 } else {
                      // If product is inactive, does not exist, or stock is 0, remove the item completely.
                      $this->cartModel->delete($item['id']);
                       log_message('info', 'Auto-removed cart item ID ' . $item['id'] . ' due to unavailability (inactive/zero stock).');
                 }
            }
        }

        // If any stock issues were found, reload cart items and redirect back to cart
        // to show the user the corrected cart state.
        if ($hasStockIssue) {
             // Re-fetch cart items after potential auto-correction
             $cartItems = $this->cartModel->getCartItems($userId); // Reload items after changes
             if (empty($cartItems)) {
                 // If cart is now empty after correction
                 return redirect()->to('/cart')->with('warning', 'Beberapa produk di keranjang stoknya tidak mencukupi atau tidak tersedia lagi. Keranjang Anda telah diperbarui dan sekarang kosong.');
             }
             // If cart still has items after correction
             session()->setFlashdata('warning', 'Ada produk di keranjang yang stoknya tidak mencukupi atau tidak tersedia lagi. Kuantitas di keranjang Anda telah diperbarui. Silakan periksa kembali sebelum checkout.');
             return redirect()->to('/cart'); // Redirect back to cart page
        }

        // --- Proceed to Checkout View if no stock issues ---
        $total = $this->cartModel->getCartTotal($userId);

        // Fetch user details for default address/phone values in the form
        $user = $this->userModel->find($userId);

        $data = [
            'title' => 'Checkout',
            'cartItems' => $cartItems,
            'total' => $total,
            'user' => $user, // Pass user data
            // Pass validation errors if redirected back from process()
            'errors' => session()->getFlashdata('errors') ?? [], // Ensure it's an array
            'old_input' => session()->getFlashdata('old_input') ?? [] // Pass old input
        ];

        return view('order/checkout', $data);
    }

    /**
     * Processes the order creation from the cart.
     * Includes a final stock check within a database transaction.
     * Decreases product stock and clears the cart upon success.
     *
     * @return RedirectResponse
     */
    public function process(): RedirectResponse
    {
        // Filter auth memastikan pengguna login
        $userId = session()->get('user_id');
        $cartItems = $this->cartModel->getCartItems($userId);

        if (empty($cartItems)) {
            return redirect()->to('/cart')->with('warning', 'Keranjang belanja kosong. Tidak dapat memproses pesanan.');
        }

        // Validation rules for checkout form inputs
        $rules = [
            'shipping_address' => [
                 'rules' => 'required|string|min_length[10]|max_length[500]',
                 'errors' => [
                     'required' => 'Alamat pengiriman harus diisi.',
                     'string' => 'Format alamat pengiriman tidak valid.',
                     'min_length' => 'Alamat pengiriman minimal 10 karakter.',
                     'max_length' => 'Alamat pengiriman maksimal 500 karakter.'
                 ]
            ],
            'phone' => [
                 'rules' => 'required|string|max_length[20]|regex_match[/^[0-9+\-\s]*$/]', // Diperbaiki: escape tanda hubung '-'
                 'errors' => [
                      'required' => 'Nomor telepon harus diisi.',
                      'string' => 'Format nomor telepon tidak valid.',
                      'max_length' => 'Nomor telepon maksimal 20 karakter.',
                      'regex_match' => 'Format nomor telepon tidak valid. Hanya angka, +, -, dan spasi yang diperbolehkan.' // Perbaiki pesan error
                 ]
            ],
            'payment_method' => [
                 'rules' => 'required|string', // Anda bisa menambahkan in_list jika perlu: 'required|string|in_list[bank_transfer,cod]'
                 'errors' => ['required' => 'Metode pembayaran harus dipilih.']
            ],
            'notes' => [
                 'rules' => 'permit_empty|string|max_length[300]',
                 'errors' => [
                      'string' => 'Format catatan tidak valid.',
                      'max_length' => 'Catatan maksimal 300 karakter.'
                 ]
            ],
        ];

        // Lakukan validasi data POST
        if (!$this->validate($rules, $this->request->getPost())) { // Lewatkan data POST ke validate()
            // Jika validasi gagal, kembalikan ke halaman sebelumnya dengan input lama dan error validasi
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Start Database Transaction
        // Ini memastikan bahwa jika ada langkah (insert order, insert item, update stok) gagal,
        // semua langkah sebelumnya dalam transaksi akan dibatalkan (rollback).
        $this->orderModel->db->transStart();

        try {
            // --- Crucial: Final Stock Check Before Creating Order within transaction ---
            // Buat daftar item yang akan diproses setelah pemeriksaan stok final
            $itemsToProcess = [];
            foreach ($cartItems as $item) {
                 // Ambil produk lagi dari database (penting di dalam transaksi untuk menghindari race condition)
                 $product = $this->productModel->find($item['product_id']);

                 // Periksa kembali keberadaan produk, status aktif, dan kecukupan stok
                 if (!$product || !$product['is_active'] || $product['stock'] < $item['quantity']) {
                     // Jika stok tidak cukup atau produk tidak tersedia, batalkan transaksi
                     log_message('error', 'Transaction Rollback: Stock insufficient or product unavailable for product ID ' . $item['product_id'] . ' ("' . ($product['name'] ?? 'Unknown Product') . '"). Requested: ' . $item['quantity'] . ', Available: ' . ($product['stock'] ?? 'N/A') . '. Product Active: ' . ($product['is_active'] ?? 'N/A'));
                     $this->orderModel->db->transRollback();

                      // Opsional: Lakukan auto-koreksi keranjang (update kuantitas atau hapus item)
                      // Ini dilakukan SETELAH rollback, jadi perubahan keranjang tidak menjadi bagian dari transaksi order.
                      if ($product && $product['is_active'] && $product['stock'] > 0 && $product['stock'] < $item['quantity']) {
                           // Jika produk ada, aktif, dan stok > 0 tapi kurang dari kuantitas di keranjang,
                           // update kuantitas di keranjang menjadi stok yang tersedia.
                           $this->cartModel->update($item['id'], ['quantity' => $product['stock']]);
                           log_message('info', 'Auto-corrected cart quantity for item ID ' . $item['id'] . ' to available stock: ' . $product['stock']);
                      } else {
                           // Jika produk tidak aktif, tidak ada, atau stoknya 0, hapus item sepenuhnya dari keranjang.
                           $this->cartModel->delete($item['id']);
                           log_message('info', 'Auto-removed cart item ID ' . $item['id'] . ' due to unavailability (inactive/zero stock).');
                      }
                     // Redirect kembali ke halaman keranjang dengan pesan peringatan
                     return redirect()->to('/cart')->with('warning', 'Mohon maaf, stok untuk produk "' . esc($item['name'] ?? 'produk yang dihapus') . '" tidak mencukupi atau produk tidak tersedia saat penyelesaian pesanan. Keranjang Anda telah diperbarui. Silakan periksa kembali.');
                 }
                 // Jika semua pemeriksaan stok berhasil, tambahkan item ke daftar yang akan diproses
                 $itemsToProcess[] = [
                     'product_id' => $item['product_id'],
                     'quantity' => $item['quantity'],
                     'price' => $item['price'] // Simpan harga saat pesanan dibuat (penting!)
                 ];
            }

             // Pastikan masih ada item yang akan diproses setelah pemeriksaan stok ketat
            if (empty($itemsToProcess)) {
                 log_message('warning', 'Transaction Rollback: Cart became empty after strict stock check for user ID ' . $userId);
                 $this->orderModel->db->transRollback();
                 // Redirect ke keranjang dengan pesan warning
                 return redirect()->to('/cart')->with('warning', 'Tidak ada produk yang valid di keranjang Anda untuk dipesan.');
            }

            // Hitung kembali total jumlah pesanan berdasarkan item yang akan diproses (untuk keamanan)
            $totalAmount = 0;
            foreach($itemsToProcess as $item) {
                $totalAmount += (float)$item['price'] * (int)$item['quantity'];
            }

            // Persiapkan data untuk record pesanan utama
            $orderData = [
                'user_id' => $userId,
                'order_number' => $this->orderModel->generateOrderNumber(), // Generate nomor pesanan unik
                'total_amount' => $totalAmount, // Gunakan total yang dihitung ulang
                'shipping_address' => $this->request->getPost('shipping_address'),
                'phone' => $this->request->getPost('phone'),
                'payment_method' => $this->request->getPost('payment_method'),
                'notes' => $this->request->getPost('notes'),
                'status' => 'pending', // Status awal pesanan
            ];

            // Masukkan record pesanan utama ke database
            $orderId = $this->orderModel->insert($orderData);

            // Periksa apakah penyisipan pesanan berhasil
            if (!$orderId) {
                 log_message('error', 'Order insertion failed for user ID ' . $userId . '. Model Errors: ' . json_encode($this->orderModel->errors()));
                 // Jika insert gagal, batalkan transaksi dan lemparkan exception
                throw new \Exception('Gagal membuat record pesanan di database.');
            }

            // Masukkan item-item pesanan dan perbarui stok produk untuk setiap item yang diproses
            foreach ($itemsToProcess as $item) {
                // Masukkan record order item
                $this->orderItemModel->insert([
                    'order_id' => $orderId, // ID pesanan yang baru dibuat
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] // Simpan harga item saat itu
                ]);

                // Kurangi stok produk
                // Ambil produk lagi *di dalam transaksi* untuk mendapatkan nilai stok terbaru sebelum mengurangi
                $product = $this->productModel->find($item['product_id']);
                 if ($product) {
                      $newStock = max(0, $product['stock'] - $item['quantity']); // Pastikan stok tidak menjadi negatif
                      $this->productModel->update($item['product_id'], ['stock' => $newStock]);
                      log_message('info', 'Decreased stock for product ID ' . $item['product_id'] . ' by ' . $item['quantity'] . '. New stock: ' . $newStock);
                 } else {
                     // Kasus ini seharusnya tertangkap oleh pemeriksaan stok sebelumnya, tapi sebagai jaga-jaga
                     log_message('error', 'Transaction Rollback: Product ID ' . $item['product_id'] . ' missing during stock update in order process for order ID ' . $orderId);
                     throw new \Exception('Produk tidak ditemukan saat update stok.'); // Batalkan transaksi
                 }
            }

            // Bersihkan keranjang pengguna hanya setelah semua langkah di dalam transaksi berhasil
            $this->cartModel->where('user_id', $userId)->delete();
             log_message('info', 'Cart cleared for user ID ' . $userId . ' after successful order ID ' . $orderId);

            // Commit transaksi (simpan semua perubahan ke database)
            $this->orderModel->db->transCommit();
             log_message('info', 'Transaction committed for order ID ' . $orderId);

            // Alihkan pengguna ke halaman daftar pesanan mereka dengan pesan sukses
            return redirect()->to('/orders')->with('success', 'Pesanan berhasil dibuat! Mohon segera lakukan pembayaran dan upload bukti pembayaran melalui halaman detail pesanan.');

        } catch (\Exception $e) {
            // Jika ada exception (kesalahan) terjadi selama transaksi
            // Pastikan transaksi dibatalkan (rollback)
            if ($this->orderModel->db->transStatus() === false) {
                 log_message('error', 'Ensuring rollback after exception for user ID ' . $userId);
                $this->orderModel->db->transRollback();
            }
            // Log detail error untuk debugging
            log_message('error', 'Order Processing Failed Exception for user ID ' . $userId . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            // Alihkan kembali ke halaman checkout dengan pesan error
            // Bisa sertakan pesan exception e.getMessage() untuk debugging, tapi sebaiknya jangan di produksi
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memproses pesanan Anda. Mohon coba lagi.');
        }
    }

    /**
     * Menampilkan daftar pesanan untuk pengguna yang login.
     * Autentikasi ditangani oleh filter 'auth' di Routes.php.
     * Metode ini yang dipanggil oleh rute /orders.
     *
     * @return string Mengembalikan hasil render view my_orders.
     */
    public function myOrders(): string
    {
        // Filter auth memastikan pengguna login

        $userId = session()->get('user_id'); // Ambil ID pengguna dari sesi
        $orders = $this->orderModel->getUserOrders($userId); // Ambil pesanan untuk pengguna ini

        // Siapkan data untuk view
        $data = [
            'title' => 'Pesanan Saya', // Judul halaman
            'orders' => $orders // Daftar pesanan pengguna
        ];

        // Render view my_orders, menggunakan layout template
        return view('order/my_orders', $data);
    }

    /**
     * Menampilkan detail pesanan tertentu untuk pengguna yang login.
     * Memverifikasi bahwa pesanan tersebut milik pengguna yang login.
     * Mengambil item-item pesanan untuk ditampilkan.
     *
     * @param int $orderId ID pesanan yang akan ditampilkan. Diharapkan dari segmen URI.
     * @throws PageNotFoundException Jika pesanan tidak ditemukan atau bukan milik pengguna yang login.
     * @return string Mengembalikan hasil render halaman view pesanan.
     */
    public function view(int $orderId): string
    {
         // Filter auth memastikan pengguna login

         $userId = session()->get('user_id');
         // Cari pesanan berdasarkan ID dan verifikasi bahwa itu milik pengguna yang login
         $order = $this->orderModel->where('id', $orderId)->where('user_id', $userId)->first();

         // Jika pesanan tidak ditemukan atau bukan milik pengguna
         if (!$order) {
             // Tampilkan error 404 Page Not Found (lebih aman)
             throw new PageNotFoundException('Pesanan tidak ditemukan.');
         }

         // Ambil item-item yang terkait dengan pesanan ini, termasuk detail produk (nama, gambar)
         $orderItems = $this->orderItemModel->getItemsWithProduct($orderId);

         // Siapkan data untuk view
         $data = [
             'title' => 'Detail Pesanan #' . $order['order_number'], // Judul dinamis
             'order' => $order, // Detail pesanan
             'orderItems' => $orderItems, // Daftar item dalam pesanan
              // Lewatkan error dan flag jika redirect kembali dari uploadPayment untuk re-open modal
             'errors' => session()->getFlashdata('errors') ?? [], // Pastikan selalu array
             'open_upload_modal' => session()->getFlashdata('open_upload_modal') ?? false, // Pastikan default false
         ];

         // Render halaman view pesanan, menggunakan layout template
         return view('order/view', $data);
    }


    /**
     * Menangani upload bukti pembayaran untuk pesanan tertentu via permintaan POST.
     * Memvalidasi file yang diupload dan menyimpannya.
     * Memperbarui record pesanan dengan nama file bukti pembayaran dan status ('pending_review').
     *
     * @param int $orderId ID pesanan yang akan diperbarui. Diharapkan dari segmen URI.
     * @return RedirectResponse Mengalihkan kembali ke halaman view pesanan, biasanya dengan pesan flash atau error.
     */
    public function uploadPayment(int $orderId): RedirectResponse
    {
        // Auth filter memastikan pengguna login

        $userId = session()->get('user_id');
        // Cari pesanan berdasarkan ID dan verifikasi milik pengguna yang login
        $order = $this->orderModel->where('id', $orderId)->where('user_id', $userId)->first();

        // Jika pesanan tidak ditemukan atau bukan milik pengguna
        if (!$order) {
             // Redirect kembali ke daftar pesanan pengguna dengan error
            return redirect()->to('/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        // Hanya izinkan upload jika status pesanan adalah 'pending'
         if ($order['status'] !== 'pending') {
              // Redirect kembali ke halaman view pesanan dengan peringatan
              return redirect()->to('/order/view/' . $orderId)->with('warning', 'Bukti pembayaran hanya bisa diupload untuk pesanan berstatus pending.');
         }

        // Ambil file dari permintaan (nama 'payment_proof' dari form)
        $file = $this->request->getFile('payment_proof');

        // Aturan validasi untuk upload file
         $validationRule = [
             'payment_proof' => [
                 'label' => 'Bukti Pembayaran', // Nama yang user-friendly untuk field di pesan error
                 'rules' => 'uploaded[payment_proof]|max_size[payment_proof,2048]|ext_in[payment_proof,jpg,jpeg,png,pdf]', // Max 2MB, izinkan jpg, jpeg, png, pdf
                 'errors' => [
                     'uploaded' => 'Silakan upload bukti pembayaran.',
                     'max_size' => 'Ukuran file terlalu besar (maks 2MB).',
                     'ext_in' => 'Hanya file JPG, JPEG, PNG, dan PDF yang diperbolehkan untuk bukti pembayaran.'
                 ],
             ],
         ];

         // Lakukan validasi pada upload file
         // Penting: Lewatkan data POST ($this->request->getPost()) ke $this->validate
         if (! $this->validate($validationRule, $this->request->getPost())) {
             // Jika validasi gagal, redirect kembali ke halaman view pesanan.
             // Lewatkan error validasi menggunakan flashdata 'errors'.
             // Lewatkan flag kustom 'open_upload_modal' untuk memberi sinyal JS di view agar membuka kembali modal.
              return redirect()->to('/order/view/' . $orderId)->withInput()->with('errors', $this->validator->getErrors())->with('open_upload_modal', true);
         }

        // Proses upload file jika validasi berhasil dan file valid serta belum dipindahkan
        if ($file->isValid() && !$file->hasMoved()) {
            // Tentukan path upload target di dalam direktori public
            $uploadPath = ROOTPATH . 'public/uploads/payments';

            // Pastikan direktori upload ada dan memiliki izin tulis
            if (!is_dir($uploadPath)) {
                 // Buat direktori secara rekursif jika tidak ada (dengan izin 0775)
                 mkdir($uploadPath, 0775, true);
            }
             // Periksa kembali apakah direktori dapat ditulis setelah percobaan pembuatan
             if (!is_writable($uploadPath)) {
                  log_message('error', 'Upload directory is not writable: ' . $uploadPath);
                  // Alihkan kembali dengan pesan error izin file
                   return redirect()->to('/order/view/' . $orderId)->with('error', 'Gagal upload bukti pembayaran: Folder upload tidak dapat ditulisi oleh server.');
             }

            // Buat nama file unik untuk mencegah konflik nama (direkomendasikan)
            $fileName = $file->getRandomName();

            // Coba pindahkan file yang diupload ke direktori target
            if ($file->move($uploadPath, $fileName)) {

                // Jika pemindahan file berhasil, update record pesanan di database.
                // Simpan nama file dan ubah status pesanan menjadi 'pending_review' untuk menunjukkan bukti sudah diupload dan menunggu tinjauan admin.
                $this->orderModel->update($orderId, [
                    'payment_proof' => $fileName, // Simpan nama file yang dibuat
                    'status' => 'pending_review' // Update status
                ]);

                // Redirect kembali ke halaman view pesanan dengan pesan sukses
                return redirect()->to('/order/view/' . $orderId)->with('success', 'Bukti pembayaran berhasil diupload. Pesanan Anda sekarang menunggu konfirmasi admin.');

            } else {
                // Tangani error spesifik terkait pemindahan file
                 log_message('error', 'Failed to move uploaded file for order ID ' . $orderId . '. Error: ' . $file->getErrorString());
                return redirect()->to('/order/view/' . $orderId)->with('error', 'Gagal menyimpan file bukti pembayaran.');
            }
        }

        // Kasus fallback: Bagian ini seharusnya tidak tercapai jika validasi 'uploaded' berhasil,
        // tapi ini sebagai jaga-jaga.
         log_message('error', 'Upload payment failed unexpectedly for order ID ' . $orderId . ' after validation.');
        return redirect()->to('/order/view/' . $orderId)->with('error', 'Gagal upload bukti pembayaran. File tidak valid atau terjadi kesalahan tak terduga.');
    }
}