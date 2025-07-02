<?php namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\CategoryModel; // Pastikan model ini diimpor
use App\Models\OrderItemModel;
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException; // Import exception
use CodeIgniter\HTTP\RedirectResponse; // Import RedirectResponse
use CodeIgniter\HTTP\ResponseInterface; // Import ResponseInterface
use CodeIgniter\Validation\Validation; // Pastikan ini diimpor


class Admin extends BaseController
{
    // Deklarasi properti model
    protected OrderModel $orderModel;
    protected ProductModel $productModel;
    protected UserModel $userModel;
    protected CategoryModel $categoryModel; // Deklarasikan CategoryModel
    protected OrderItemModel $orderItemModel;

    public function __construct()
    {
        // Filter 'admin' applied in Routes.php, handles auth check before this controller runs.
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel(); // Inisiasi CategoryModel
        $this->orderItemModel = new OrderItemModel();
    }

    /**
     * Displays the admin dashboard overview.
     */
    public function index(): string
    {
        $data = [
            'title' => 'Dashboard Admin',
            'totalOrders' => $this->orderModel->countAll(),
            'totalProducts' => $this->productModel->countAll(),
            'totalUsers' => $this->userModel->where('role', 'customer')->countAllResults(),
            // Count orders that are pending or waiting for review
            'pendingOrders' => $this->orderModel->whereIn('status', ['pending', 'pending_review'])->countAllResults(),
            // Get a limited number of recent orders for the dashboard display
            'recentOrders' => $this->orderModel->getOrdersWithUser(5) // Get last 5 orders
        ];

        return view('admin/dashboard', $data);
    }

    // --- Products Management ---

    /**
     * Displays the list of all products for admin management.
     */
    public function products(): string
    {
         // Use the model method that gets all products regardless of active/stock status
         $products = $this->productModel->getAllProductsWithCategory();

        $data = [
            'title' => 'Kelola Produk',
            'products' => $products
        ];

        return view('admin/products', $data);
    }

    /**
     * Displays the form to create a new product.
     */
    public function createProduct(): string
    {
        // Fetch all categories to populate dropdown in the form
        $categories = $this->categoryModel->findAll();

        $data = [
            'title' => 'Tambah Produk Baru',
            'categories' => $categories, // Pass categories to view
             'errors' => session()->getFlashdata('errors') // Pass validation errors if redirected back
        ];

        // Needs view: app/Views/admin/create_product.php
        return view('admin/create_product', $data);
    }

    /**
     * Handles the submission of the new product form.
     * Validates input, handles image upload, and saves to the database.
     */
    public function storeProduct(): RedirectResponse
    {
        // Validation rules for product creation
        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]', // Added max_length
            'description' => 'permit_empty|string', // Added string rule
            'price'       => 'required|numeric|greater_than_equal_to[0]', // Price can be 0 for free products? Or > 0?
            'stock'       => 'required|integer|greater_than_equal_to[0]',
            'weight'      => 'required|numeric|greater_than_equal_to[0]',
            'category_id' => 'permit_empty|integer', // Allow empty, validate exists if not empty
             'image' => [ // Validation for file upload (optional)
                 'rules' => 'max_size[image,1024]|ext_in[image,png,jpg,jpeg,webp]', // Max 1MB, specific extensions
                 'errors' => [
                      'max_size' => 'Ukuran file gambar terlalu besar (maksimal 1MB).',
                      'ext_in' => 'File gambar harus berformat PNG, JPG, JPEG, atau WEBP.'
                 ]
             ],
            'is_active'   => 'permit_empty|integer|in_list[0,1]', // Added permit_empty and in_list
        ];

         // Optional: Validate category_id exists if not empty
         if (!empty($this->request->getPost('category_id'))) {
              $rules['category_id'] .= '|is_not_unique[categories.id]'; // Check if category_id EXISTS in categories table
              $errors['category_id']['is_not_unique'] = 'Kategori tidak valid.';
         }


         // Perform validation
        if (!$this->validate($rules, $this->request->getPost())) { // Pass post data including file
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

         // Handle image upload
         $file = $this->request->getFile('image');
         $fileName = null;
         if ($file && $file->isValid() && !$file->hasMoved()) {
              $uploadPath = ROOTPATH . 'public/assets/images/products'; // Upload to products folder
              if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);
               if (is_writable($uploadPath)) {
                   $fileName = $file->getRandomName();
                   if (!$file->move($uploadPath, $fileName)) {
                        log_message('error', 'Gagal memindahkan file upload produk: ' . $file->getErrorString());
                         return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file gambar produk.');
                   }
               } else {
                    log_message('error', 'Direktori upload gambar produk tidak writable: ' . $uploadPath);
                    return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file gambar produk. Periksa izin folder server.');
               }
         }


         // Prepare data for insertion
        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price'       => $this->request->getPost('price'),
            'stock'       => $this->request->getPost('stock'),
            'weight'      => $this->request->getPost('weight'),
            'category_id' => $this->request->getPost('category_id') ?: null, // Save as null if empty
            'image'       => $fileName, // Save filename or null
            'is_active'   => $this->request->getPost('is_active') ?? 0, // Checkbox value (1 if checked, 0 if not present) - assuming 0 as default if not set
             'created_at'  => date('Y-m-d H:i:s'), // <-- Tambahkan manual created_at
             'updated_at'  => date('Y-m-d H:i:s'), // <-- Tambahkan manual updated_at
        ];

        // *** PERBAIKAN: HAPUS PANGGILAN useTimestamps() DARI SINI ***
        // $this->categoryModel->useTimestamps(false); // Ini salah model dan salah method

        $success = $this->productModel->insert($data); // Menggunakan insert() pada ProductModel

        // *** PERBAIKAN: HAPUS PANGGILAN useTimestamps() DARI SINI ***
        // $this->categoryModel->useTimestamps(true); // Ini salah model dan salah method


        if ($success) {
            return redirect()->to('/admin/products')->with('success', 'Produk berhasil ditambahkan.');
        } else {
             log_message('error', 'Failed to insert product: ' . json_encode($this->productModel->errors()));
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan produk.');
        }
    }

    /**
     * Displays the form to edit an existing product.
     *
     * @param int $id The ID of the product to edit.
     * @throws PageNotFoundException If the product is not found.
     */
    public function editProduct(int $id): string
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            throw new PageNotFoundException('Produk tidak ditemukan.');
        }

        // Fetch all categories to populate dropdown
        $categories = $this->categoryModel->findAll();

        $data = [
            'title' => 'Edit Produk',
            'product' => $product,
            'categories' => $categories,
             'errors' => session()->getFlashdata('errors') // Pass validation errors
        ];

        // Needs view: app/Views/admin/edit_product.php
        return view('admin/edit_product', $data);
    }

    /**
     * Handles the submission of the product edit form.
     * Validates input, handles potential image upload, and updates the database record.
     *
     * @param int $id The ID of the product to update.
     */
    public function updateProduct(int $id): RedirectResponse
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        // Validation rules for product update
        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]', // Added max_length
            'description' => 'permit_empty|string', // Added string rule
            'price'       => 'required|numeric|greater_than_equal_to[0]',
            'stock'       => 'required|integer|greater_than_equal_to[0]',
            'weight'      => 'required|numeric|greater_than_equal_to[0]',
            'category_id' => 'permit_empty|integer', // Allow empty
             'image' => [ // Validation for file upload (optional on update)
                 'rules' => 'max_size[image,1024]|ext_in[image,png,jpg,jpeg,webp]',
                 'errors' => [
                      'max_size' => 'Ukuran file gambar terlalu besar (maksimal 1MB).',
                      'ext_in' => 'File gambar harus berformat PNG, JPG, JPEG, atau WEBP.'
                 ]
             ],
            'is_active'   => 'permit_empty|integer|in_list[0,1]',
        ];

         // Optional: Validate category_id exists if not empty
         if (!empty($this->request->getPost('category_id'))) {
              $rules['category_id'] .= '|is_not_unique[categories.id]'; // Check if category_id EXISTS in categories table
              $errors['category_id']['is_not_unique'] = 'Kategori tidak valid.';
         }


         // Perform validation
        if (!$this->validate($rules, $this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

         // Handle image upload (only if a new file is selected)
         $file = $this->request->getFile('image');
         $fileName = $product['image']; // Keep existing image by default

         if ($file && $file->isValid() && !$file->hasMoved()) {
             $uploadPath = ROOTPATH . 'public/assets/images/products';
             if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);

             if (is_writable($uploadPath)) {
                 $fileName = $file->getRandomName();
                 if ($file->move($uploadPath, $fileName)) {
                     // Optional: Delete old image if the move was successful
                     if ($product['image'] && file_exists($uploadPath . '/' . $product['image'])) {
                          // Hanya hapus jika file lama ada dan bukan file default.jpg (jika Anda punya default)
                           if ($product['image'] !== 'default.jpg') { unlink($uploadPath . '/' . $product['image']); } // Corrected variable name here
                     }
                 } else {
                     log_message('error', 'Gagal memindahkan file upload produk saat update: ' . $file->getErrorString());
                     return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file gambar produk baru.');
                 }
             } else {
                  log_message('error', 'Direktori upload gambar produk tidak writable: ' . $uploadPath);
                 return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file gambar produk. Periksa izin folder server.');
             }
         }


         // Prepare data for update
        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price'       => $this->request->getPost('price'),
            'stock'       => $this->request->getPost('stock'),
            'weight'      => $this->request->getPost('weight'),
            'category_id' => $this->request->getPost('category_id') ?: null, // Save as null if empty
            'image'       => $fileName, // Save new filename or the old one
            'is_active'   => $this->request->getPost('is_active') ?? 0, // Checkbox value
             'updated_at'  => date('Y-m-d H:i:s'), // <-- Tambahkan manual updated_at
        ];


        // Update data using the model
        // Model ProductModel punya useTimestamps = true, jadi updated_at akan diisi otomatis
        // Sebaiknya hapus 'updated_at' manual di $data jika useTimestamps true di Model
        // Jika Model ProductModel punya useTimestamps = false, maka 'updated_at' manual di $data ini diperlukan

        // --- PERBAIKAN KRUSIAL: Cek konfigurasi ProductModel ---
        // DI ProductModel.php, seharusnya: protected $useTimestamps = true;
        // Dan createdField/updatedField diset.
        // Jika begitu, HAPUS 'updated_at' manual dari array $data di updateProduct dan storeProduct

        // Jika ProductModel.php diset protected $useTimestamps = false;,
        // maka biarkan 'updated_at' manual di sini dan pastikan 'created_at' juga manual di storeProduct.
        // Asumsi kode awal kita: ProductModel punya useTimestamps = true.

        // Berdasarkan error SQL sebelumnya (Unknown column 'updated_at' di CategoryModel),
        // sepertinya masalahnya bukan di ProductModel, tapi memang Model default tidak selalu mulus dengan timestamp manual.
        // Mari kita tetap gunakan pendekatan manual timestamp di controller,
        // tapi pastikan ProductModel TIDAK menggunakan timestamp otomatis.


        // --- PERBAIKAN: Pastikan ProductModel useTimestamps = false ---
        // Di ProductModel.php, set: protected $useTimestamps = false;
        // DAN protected $allowedFields = ['...', 'created_at', 'updated_at'];


        if ($this->productModel->update($id, $data)) { // Menggunakan update() dengan ID
            return redirect()->to('/admin/products')->with('success', 'Produk berhasil diperbarui.');
        } else {
             log_message('error', 'Failed to update product ID ' . $id . ': ' . json_encode($this->productModel->errors()));
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui produk.');
        }
    }

    /**
     * Handles the deletion of a product.
     * Supports both standard form submission and AJAX requests.
     *
     * @param int $id The ID of the product to delete.
     * @return RedirectResponse|ResponseInterface Redirects or returns JSON.
     */
    public function deleteProduct(int $id): RedirectResponse|ResponseInterface
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            $errorMessage = 'Produk tidak ditemukan.';
             // Return JSON for AJAX or redirect for standard request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }
            return redirect()->to('/admin/products')->with('error', $errorMessage);
        }

        try {
             // Optional: Delete the product image file before deleting the record
             if ($product['image']) {
                  $imagePath = ROOTPATH . 'public/assets/images/products/' . $product['image'];
                   // Pastikan file exist dan bukan file default.jpg (jika ada default)
                   // if (file_exists($imagePath) && $product['image'] !== 'default.jpg') { unlink($imagePath); }
                   if (file_exists($imagePath)) { // Hanya periksa apakah file exist
                        unlink($imagePath); // Hapus file gambar dari server
                   }
             }

            // Delete the product record from the database
            $this->productModel->delete($id);
            $successMessage = 'Produk berhasil dihapus.';

             // Return JSON for AJAX or redirect for standard request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }
            return redirect()->to('/admin/products')->with('success', $successMessage);

        } catch (\Exception $e) {
            // Handle database deletion errors
            log_message('error', 'Gagal menghapus produk ID ' . $id . ': ' . $e->getMessage());
            $errorMessage = 'Terjadi kesalahan saat menghapus produk.';

             // Return JSON for AJAX or redirect for standard request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }
            return redirect()->to('/admin/products')->with('error', $errorMessage);
        }
    }


    // --- Orders Management ---

    /**
     * Displays the list of all orders for admin management.
     */
    public function orders(): string
    {
        // Get all orders with user details
        $orders = $this->orderModel->getOrdersWithUser();
        $data = [
            'title' => 'Kelola Pesanan',
            'orders' => $orders
        ];
        return view('admin/orders', $data);
    }

     /**
      * Displays the details of a specific order for admin.
      *
      * @param int $orderId The ID of the order.
      * @throws PageNotFoundException If the order is not found.
      */
     public function viewOrder(int $orderId): string
     {
         // Get single order with associated user details
         $order = $this->orderModel->getOrdersWithUserById($orderId);

         if (!$order) {
             throw new PageNotFoundException('Pesanan tidak ditemukan.');
         }

         // Fetch order items for this order with product details
         $orderItems = $this->orderItemModel->getItemsWithProduct($orderId);

         $data = [
             'title' => 'Detail Pesanan #' . $order['order_number'],
             'order' => $order,
             'orderItems' => $orderItems
         ];

         return view('admin/view_order', $data);
     }


    /**
     * Confirms the payment for a specific order.
     *
     * @param int $orderId The ID of the order.
     * @return RedirectResponse Redirects back to the previous page (orders list or detail).
     */
    public function confirmPayment(int $orderId): RedirectResponse
    {
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
        }

         // Allow confirmation only if status is 'pending' or 'pending_review'
         if (!in_array($order['status'], ['pending', 'pending_review'])) {
             return redirect()->back()->with('warning', 'Pembayaran hanya bisa dikonfirmasi untuk status pending atau pending review.');
         }

        // Use transaction for safety if stock manipulation were tied to confirmation
        // For simple status update, transaction might be overkill but good practice
        $this->orderModel->db->transStart();
        try {
            $this->orderModel->update($orderId, ['status' => 'confirmed']);
            $this->orderModel->db->transCommit();
            return redirect()->back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
        } catch (\Exception $e) {
             // If transaction failed
             if ($this->orderModel->db->transStatus() === false) {
                 $this->orderModel->db->transRollback();
             }
             log_message('error', 'Gagal konfirmasi pembayaran Order ID ' . $orderId . ': ' . $e->getMessage());
             return redirect()->back()->with('error', 'Gagal mengkonfirmasi pembayaran.');
        }
    }

    /**
     * Updates the status of a specific order (AJAX endpoint).
     *
     * @param int $orderId The ID of the order.
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function updateOrderStatus(int $orderId): ResponseInterface
    {
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
        }

        $status = $this->request->getPost('status');

         // Basic validation for status
         $validStatuses = ['pending', 'pending_review', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
         if (!in_array($status, $validStatuses)) {
             return $this->response->setJSON(['success' => false, 'message' => 'Status tidak valid.']);
         }

         // Prevent status updates that don't make sense (optional business logic)
         // E.g., cannot go back from delivered to pending
         // if ($order['status'] === 'delivered' && $status !== 'delivered') {
         //     return $this->response->setJSON(['success' => false, 'message' => 'Tidak dapat mengubah status dari "Selesai".']);
         // }
         // if ($order['status'] === 'cancelled') {
         //      return this->response->setJSON(['success' => false, 'message' => 'Pesanan telah dibatalkan dan tidak dapat diubah statusnya.']);
         // }
         // if ($status === 'confirmed' && $order['payment_proof'] === null) {
         //      return $this->response->setJSON(['success' => false, 'message' => 'Tidak dapat mengkonfirmasi pembayaran tanpa bukti pembayaran.']);
         // }


        // Use transaction for status update
        $this->orderModel->db->transStart();
         try {
            $this->orderModel->update($orderId, ['status' => $status]);

             // Additional logic based on status change (e.g., trigger email, update inventory)
             // if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
             //      // Logic to return stock to inventory
             // }


            $this->orderModel->db->transCommit();
             return $this->response->setJSON(['success' => true, 'message' => 'Status pesanan berhasil diupdate.']);
         } catch (\Exception $e) {
             // If transaction failed
             if ($this->orderModel->db->transStatus() === false) {
                 $this->orderModel->db->transRollback();
             }
             log_message('error', 'Update Order Status Failed for Order ID ' . $orderId . ' to ' . $status . ': ' . $e->getMessage());
             return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupdate status pesanan.']);
         }
    }

    // --- User Management ---

    /**
     * Displays the list of all users (customers) for admin management.
     * (Method already exists in your provided code)
     */
    public function users(): string
    {
        // Fetch all users from the database
        $users = $this->userModel->findAll();

        $data = [
            'title' => 'Kelola Pengguna', // Judul halaman
            'users' => $users // Data pengguna untuk ditampilkan
        ];

        // Needs view: admin/users.php
        return view('admin/users', $data);
    }

    // Optional: Add CRUD methods for Users (editUser, updateUser, activateUser, deactivateUser, deleteUser)


    // --- Category Management ---

    /**
     * Displays the list of all categories for admin management.
     * (Method already exists in your provided code)
     */
    public function categories(): string
    {
        // Fetch all categories from the database (including inactive ones for admin)
        $categories = $this->categoryModel->findAll();

        $data = [
            'title' => 'Kelola Kategori', // Judul halaman
            'categories' => $categories // Data kategori untuk ditampilkan
        ];

        // Needs view: admin/categories.php
        return view('admin/categories', $data);
    }

    /**
     * Displays the form to create a new category.
     */
    public function createCategory(): string
    {
        $data = [
            'title' => 'Tambah Kategori Baru', // Judul halaman form
             'errors' => session()->getFlashdata('errors') // Pass validation errors if redirected back
        ];

        // Needs view: app/Views/admin/categories/create.php
        return view('admin/categories/create', $data);
    }

    /**
     * Handles the submission of the new category form.
     * Validates input, handles image upload, and saves to the database.
     */
    public function storeCategory(): RedirectResponse
    {
         // Aturan validasi untuk data form kategori
         $rules = [
             'name' => [
                 'rules' => 'required|max_length[100]|is_unique[categories.name]', // Nama required, max 100, unik
                 'errors' => [
                     'required' => 'Nama kategori harus diisi.',
                     'max_length' => 'Nama kategori terlalu panjang (maksimal 100 karakter).',
                     'is_unique' => 'Nama kategori "{value}" sudah ada.'
                 ]
             ],
             'description' => [ // Deskripsi opsional
                 'rules' => 'permit_empty|string',
                 'errors' => ['string' => 'Format deskripsi tidak valid.']
             ],
             'image' => [ // Validasi untuk file gambar (opsional)
                 'rules' => 'max_size[image,1024]|ext_in[image,png,jpg,jpeg,webp]', // Max 1MB, hanya ekstensi gambar tertentu
                 'errors' => [
                      'max_size' => 'Ukuran file gambar terlalu besar (maksimal 1MB).',
                      'ext_in' => 'File gambar harus berformat PNG, JPG, JPEG, atau WEBP.'
                 ]
             ],
              'is_active' => [ // Validasi status aktif (opsional)
                 'rules' => 'permit_empty|integer|in_list[0,1]',
                 'errors' => [
                     'integer' => 'Nilai status aktif tidak valid.',
                     'in_list' => 'Nilai status aktif tidak valid.'
                 ]
             ],
         ];

         // Lakukan validasi
         // Menggunakan helper validate() yang otomatis menangani $this->request->getPost() + file
         if (!$this->validate($rules)) {
             // Jika validasi gagal, arahkan kembali ke form dengan input sebelumnya dan error
             return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
         }

         // Data valid, proses upload gambar
         $file = $this->request->getFile('image');
         $fileName = null; // Default nama file gambar
         if ($file && $file->isValid() && !$file->hasMoved()) {
              // Definisikan path upload kategori
              $uploadPath = ROOTPATH . 'public/assets/images/categories'; // Upload to categories folder

              // Pastikan direktori upload ada dan writable
              if (!is_dir($uploadPath)) {
                  mkdir($uploadPath, 0775, true); // Buat direktori rekursif jika belum ada
              }
               // Cek izin tulis
               if (is_writable($uploadPath)) {
                   // Generate nama file unik
                   $fileName = $file->getRandomName();
                   // Pindahkan file yang diupload
                   if (!$file->move($uploadPath, $fileName)) {
                        // Jika gagal memindahkan file
                        log_message('error', 'Gagal memindahkan file upload kategori: ' . $file->getErrorString());
                         // Redirect dengan pesan error
                        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file gambar kategori.');
                   }
               } else {
                    // Jika direktori tidak writable
                    log_message('error', 'Direktori upload gambar kategori tidak writable: ' . $uploadPath);
                    return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file gambar kategori. Periksa izin folder server.');
               }
         }
          // Catatan: Jika tidak ada file diupload atau validasi file gambar gagal, $fileName tetap null.

         // Siapkan data untuk disimpan ke database
         $data = [
             'name' => $this->request->getPost('name'),
             'description' => $this->request->getPost('description'),
             'image' => $fileName, // Simpan nama file gambar (atau null)
              // Status aktif (ambil dari form atau default ke 1 jika tidak ada inputnya)
             'is_active' => $this->request->getPost('is_active') ?? 0, // Default 0 (tidak aktif) jika checkbox tidak dicentang/nilai tidak ada
              'created_at' => date('Y-m-d H:i:s'), // <-- Tambahkan manual created_at
              'updated_at' => date('Y-m-d H:i:s'), // <-- Tambahkan manual updated_at
         ];

         // Coba simpan data ke database menggunakan CategoryModel
         // Model CategoryModel sudah dikonfigurasi dengan $useTimestamps = false.
         // Kita sudah menyediakan 'created_at' dan 'updated_at' secara manual di $data.
         // useTimestamps() tidak ada di Model.


         $success = $this->categoryModel->insert($data); // Menggunakan insert() untuk menambah baru kategori


         if ($success) {
             // Jika berhasil disimpan
             return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil ditambahkan.');
         } else {
             // Jika gagal disimpan ke database (meskipun validasi form lewat, misal error DB lain)
              log_message('error', 'Gagal insert kategori ke DB: ' . json_encode($this->categoryModel->errors())); // Catat error model
             return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan kategori.');
         }
    }

    /**
     * Displays the form to edit an existing category.
     *
     * @param int $id The ID of the category to edit.
     * @throws PageNotFoundException If the category is not found.
     */
    public function editCategory(int $id): string
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            throw new PageNotFoundException('Kategori tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Kategori', // Judul halaman form edit
            'category' => $category, // Data kategori yang akan diedit
             'errors' => session()->getFlashdata('errors') // Pass validation errors
        ];

        // Needs view: app/Views/admin/categories/edit.php
        return view('admin/categories/edit', $data);
    }

    /**
     * Handles the submission of the category edit form.
     * Validates input, handles potential image upload, and updates the database record.
     *
     * @param int $id The ID of the category to update.
     */
    public function updateCategory(int $id): RedirectResponse
    {
        // Cari kategori yang akan diedit
        $category = $this->categoryModel->find($id);
        if (!$category) {
            // Jika kategori tidak ditemukan, redirect dengan pesan error
            return redirect()->back()->with('error', 'Kategori tidak ditemukan.');
        }

        // Aturan validasi untuk update kategori
         // is_unique harus mengecualikan record yang sedang diedit
         $rules = [
             'name' => [
                 'rules' => "required|max_length[100]|is_unique[categories.name,id,{$id}]", // Exclude current ID
                 'errors' => [
                     'required' => 'Nama kategori harus diisi.',
                     'max_length' => 'Nama kategori terlalu panjang (maksimal 100 karakter).',
                     'is_unique' => 'Nama kategori "{value}" sudah ada.'
                 ]
             ],
             'description' => [ // Deskripsi opsional
                 'rules' => 'permit_empty|string',
                 'errors' => ['string' => 'Format deskripsi tidak valid.']
             ],
             'image' => [ // Validasi file gambar (opsional di update)
                 'rules' => 'max_size[image,1024]|ext_in[image,png,jpg,jpeg,webp]',
                 'errors' => [
                      'max_size' => 'Ukuran file gambar terlalu besar (maksimal 1MB).',
                      'ext_in' => 'File gambar harus berformat PNG, JPG, JPEG, atau WEBP.'
                 ]
             ],
              'is_active' => [ // Validasi status aktif (opsional)
                 'rules' => 'permit_empty|integer|in_list[0,1]',
                 'errors' => [
                     'integer' => 'Nilai status aktif tidak valid.',
                     'in_list' => 'Nilai status aktif tidak valid.'
                 ]
             ],
         ];


         // Lakukan validasi data yang masuk
        if (!$this->validate($rules, $this->request->getPost())) { // Pass post data including file
            // Jika validasi gagal, arahkan kembali ke form edit dengan input sebelumnya dan error
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

         // Handle image upload (only if a new file is selected)
         $file = $this->request->getFile('image');
         $fileName = $category['image']; // Default: keep existing image filename

         if ($file && $file->isValid() && !$file->hasMoved()) {
             // Definisikan path upload kategori
             $uploadPath = ROOTPATH . 'public/assets/images/categories'; // Upload to categories folder

             // Pastikan direktori upload ada dan writable (sudah dicek saat create, tapi cek lagi bisa)
             if (!is_dir($uploadPath)) {
                  mkdir($uploadPath, 0775, true); // Buat rekursif jika belum ada
             }
             if (is_writable($uploadPath)) {
                 // Generate nama file unik
                 $fileName = $file->getRandomName();
                 // Pindahkan file yang diupload
                 if ($file->move($uploadPath, $fileName)) {
                     // Optional: Delete old image if the new one was moved successfully
                     if ($category['image'] && file_exists($uploadPath . '/' . $category['image'])) {
                          // Hanya hapus jika file lama ada dan bukan file default.jpg (jika Anda punya default)
                           if ($category['image'] !== 'default.jpg') { unlink($uploadPath . '/' . $category['image']); } // Corrected variable name here
                     }
                 } else {
                     // Jika gagal memindahkan file baru, catat log dan redirect dengan error
                     log_message('error', 'Gagal memindahkan file upload kategori saat update ID ' . $id . ': ' . $file->getErrorString());
                      // Keep the user on the form with an error related to file move
                     return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file gambar kategori baru.');
                 }
             } else {
                  // Jika direktori tidak writable
                  log_message('error', 'Direktori upload gambar kategori tidak writable: ' . $uploadPath);
                   return redirect()->back()->withInput()->with('error', 'Gagal menyimpan file gambar kategori. Periksa izin folder server.');
             }
         }
          // Jika checkbox 'is_active' tidak dicentang, nilai yang diterima dari POST adalah null atau tidak ada.
          // Kita perlu menanganinya agar tersimpan sebagai 0.
         $isActive = $this->request->getPost('is_active') ?? 0; // Default ke 0 jika tidak ada di POST


         // Siapkan data untuk update
        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'image' => $fileName, // Simpan nama file baru atau nama file lama
            'is_active' => $isActive, // Simpan status aktif
            // updated_at akan otomatis terisi jika useTimestamps true di model
             'updated_at'  => date('Y-m-d H:i:s'), // <-- Tambahkan manual updated_at
        ];


        // Update data menggunakan CategoryModel
        // Model CategoryModel sudah dikonfigurasi dengan $useTimestamps = false.
        // Kita sudah menyediakan 'updated_at' secara manual di $data.
        // useTimestamps() tidak ada di Model.

        // useTimestamps(false) dan useTimestamps(true) seharusnya dihapus karena bukan metode.
        // Model CategoryModel sudah dikonfigurasi dengan $useTimestamps = false.


        if ($this->categoryModel->update($id, $data)) { // Menggunakan update() dengan ID
            return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil diperbarui.');
        } else {
             log_message('error', 'Gagal update kategori ID ' . $id . ': ' . json_encode($this->categoryModel->errors())); // Catat error model
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui kategori.');
        }
    }


     /**
      * Handles the deletion of a category.
      * Supports both standard form submission (simulating DELETE method) and potentially AJAX.
      *
      * @param int $id The ID of the category to delete.
      * @return RedirectResponse|ResponseInterface Redirects or returns JSON response.
      */
     public function deleteCategory(int $id): RedirectResponse|ResponseInterface
     {
         // Cari kategori berdasarkan ID
         $category = $this->categoryModel->find($id);

         // Jika kategori tidak ditemukan
         if (!$category) {
             $errorMessage = 'Kategori tidak ditemukan.';
             // Tangani respon berdasarkan jenis permintaan (AJAX atau standar)
             if ($this->request->isAJAX()) {
                 return $this->response->setJSON([
                     'success' => false,
                     'message' => $errorMessage
                 ]);
             }
             return redirect()->to('/admin/categories')->with('error', $errorMessage);
         }

         try {
              // Opsional: Hapus file gambar kategori terkait sebelum menghapus record dari DB
              // Pastikan file ada dan bukan file default (jika ada default.jpg) sebelum dihapus
              if ($category['image']) {
                   $imagePath = ROOTPATH . 'public/assets/images/categories/' . $category['image'];
                    // Periksa apakah file exist dan bukan default.jpg (jika default.jpg tidak ingin dihapus)
                    // if (file_exists($imagePath) && $category['image'] !== 'default.jpg') { unlink($imagePath); }
                    if (file_exists($imagePath)) { // Hanya periksa apakah file exist
                         unlink($imagePath); // Hapus file gambar dari server
                    }
              }

             // Hapus record kategori dari database
             // Note: ON DELETE SET NULL constraint pada products.category_id akan menangani produk-produk yang tadinya terkait dengan kategori ini.
             $this->categoryModel->delete($id);
             $successMessage = 'Kategori berhasil dihapus.';

             // Tangani respon berdasarkan jenis permintaan (AJAX atau standar)
             if ($this->request->isAJAX()) {
                 return $this->response->setJSON([
                     'success' => true,
                     'message' => $successMessage
                 ]);
             }
             return redirect()->to('/admin/categories')->with('success', $successMessage);

         } catch (\Exception $e) {
             // Tangani error database saat penghapusan (misal, integrity constraint jika tidak menggunakan ON DELETE dengan benar)
             log_message('error', 'Gagal menghapus kategori ID ' . $id . ': ' . $e->getMessage());
             $errorMessage = 'Terjadi kesalahan saat menghapus kategori.';

             // Tangani respon berdasarkan jenis permintaan (AJAX atau standar)
             if ($this->request->isAJAX()) {
                 return $this->response->setJSON([
                     'success' => false,
                     'message' => $errorMessage
                 ]);
             }
             return redirect()->to('/admin/categories')->with('error', $errorMessage);
         }
     }


    // ... (metode orders, viewOrder, confirmPayment, updateOrderStatus, users lainnya) ...

}