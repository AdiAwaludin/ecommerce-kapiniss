<?php namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Set default controller and method with full namespace
// Ketika hanya mengakses base URL (misal: http://localhost:8080/)
$routes->setDefaultController('App\Controllers\Home'); // Controller yang akan menangani halaman utama
$routes->setDefaultMethod('index'); // Method di Home Controller yang akan dijalankan


// --------------------------------------------------------------------
// Public Routes
// Rute yang bisa diakses oleh siapa saja (pengguna atau tamu) tanpa perlu login
// --------------------------------------------------------------------
$routes->get('/', 'Home::index'); // Halaman Beranda - ditangani oleh Home::index()
$routes->get('/shop', 'Home::shop'); // Halaman Toko - ditangani oleh Home::shop()
$routes->get('/product/(:num)', 'Home::product/$1'); // Halaman Detail Produk - ditangani oleh Home::product() dengan parameter ID angka
$routes->get('/about', 'Home::about'); // Halaman Tentang Kami - ditangani oleh Home::about() (butuh method & view)
$routes->get('/contact', 'Home::contact'); // Halaman Kontak Kami - ditangani oleh Home::contact() (butuh method & view)
$routes->get('/contact', 'Home::contact'); // Rute untuk menampilkan form (GET)
$routes->post('/contact/send', 'Home::sendContact'); // <-- Tambahkan rute ini untuk menangani POST dari form



// --------------------------------------------------------------------
// Authentication Routes
// Rute untuk proses login, register, logout
// --------------------------------------------------------------------
$routes->get('/login', 'Auth::login'); // Menampilkan form login - ditangani oleh Auth::login()
$routes->post('/login', 'Auth::loginProcess'); // Memproses data login (POST) - ditangani oleh Auth::loginProcess()
$routes->get('/register', 'Auth::register'); // Menampilkan form register - ditangani oleh Auth::register()
$routes->post('/register', 'Auth::registerProcess'); // Memproses data register (POST) - ditangani oleh Auth::registerProcess()
$routes->get('/logout', 'Auth::logout'); // Proses logout - ditangani oleh Auth::logout()


// --------------------------------------------------------------------
// Customer Routes (Require Authentication)
// Rute yang hanya bisa diakses oleh pengguna yang sudah login (customer atau admin)
// Filter 'auth' yang sudah kita buat diterapkan pada grup rute ini.
// CodeIgniter akan menjalankan AuthFilter::before() sebelum memanggil Controller method di grup ini.
// --------------------------------------------------------------------
$routes->group('/', ['filter' => 'auth'], function($routes) {
    // Cart Routes
    $routes->get('cart', 'Cart::index'); // Menampilkan keranjang belanja - ditangani oleh Cart::index()
    $routes->post('cart/add', 'Cart::add'); // Menambahkan produk ke keranjang (POST) - ditangani oleh Cart::add()
    $routes->post('cart/update/(:num)', 'Cart::update/$1'); // Mengupdate kuantitas item keranjang (POST) - ditangani oleh Cart::update() dengan ID
    $routes->get('cart/remove/(:num)', 'Cart::remove/$1'); // Menghapus item keranjang (GET) - ditangani oleh Cart::remove() dengan ID
    $routes->get('cart/count', 'Cart::count'); // Mendapatkan jumlah item di keranjang (untuk AJAX navbar) - ditangani oleh Cart::count()

    // Order Routes
    $routes->get('order/checkout', 'Order::checkout'); // Menampilkan halaman checkout - ditangani oleh Order::checkout()
    $routes->post('order/process', 'Order::process'); // Memproses pesanan (POST) - ditangani oleh Order::process()
    $routes->get('orders', 'Order::myOrders'); // Menampilkan daftar pesanan pengguna (alias URL yang lebih ramah) - ditangani oleh Order::myOrders()
    $routes->get('order/view/(:num)', 'Order::view/$1'); // Menampilkan detail pesanan pengguna - ditangani oleh Order::view() dengan ID
    $routes->post('order/upload-payment/(:num)', 'Order::uploadPayment/$1'); // Mengupload bukti pembayaran (POST) - ditangani oleh Order::uploadPayment() dengan ID

    // Profile Routes (Placeholder - butuh Controller User dan view profile.php)
    $routes->get('profile', 'User::profile'); // Halaman profil pengguna
    $routes->get('profile/edit', 'User::editProfile'); // Form edit profil
    $routes->post('profile/update', 'User::updateProfile'); // Proses update profil
});


// --------------------------------------------------------------------
// Admin Routes (Require Admin Authentication)
// Rute yang hanya bisa diakses oleh pengguna dengan peran 'admin'.
// Filter 'admin' yang sudah kita buat diterapkan pada grup rute ini.
// CodeIgniter akan menjalankan AdminFilter::before() sebelum memanggil Controller method di grup ini.
// --------------------------------------------------------------------
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'Admin::index'); // Dashboard Admin - ditangani oleh Admin::index()
    $routes->get('products', 'Admin::products'); // Halaman Kelola Produk (daftar) - ditangani oleh Admin::products()

    //Admin Product CRUD Routes (Placeholder - butuh method di Admin Controller)
    $routes->get('products/create', 'Admin::createProduct'); // Form tambah produk
    $routes->post('products/store', 'Admin::storeProduct'); // Simpan produk baru
    $routes->get('products/edit/(:num)', 'Admin::editProduct/$1'); // Form edit produk
    $routes->post('products/update/(:num)', 'Admin::updateProduct/$1'); // Update produk
    $routes->delete('products/delete/(:num)', 'Admin::deleteProduct/$1'); // Hapus produk (metode DELETE)

     // Contact Messages Management Routes <-- Tambahkan bagian ini
    $routes->get('contacts', 'Admin::contactMessages'); // Rute GET untuk daftar pesan
    $routes->get('contacts/view/(:num)', 'Admin::viewContactMessage/$1'); // Rute GET untuk detail pesan (Opsional)
    $routes->post('contacts/mark-read/(:num)', 'Admin::markContactMessageRead/$1'); // Rute POST untuk menandai sudah dibaca (Opsional)
    $routes->post('contacts/delete/(:num)', 'Admin::deleteContactMessage/$1'); // Rute POST untuk menghapus pesan (Opsional)

    $routes->get('orders', 'Admin::orders'); // Halaman Kelola Pesanan (daftar) - ditangani oleh Admin::orders()
    $routes->get('order/view/(:num)', 'Admin::viewOrder/$1'); // Menampilkan detail pesanan (untuk admin) - ditangani oleh Admin::viewOrder() dengan ID
    $routes->post('order/confirm-payment/(:num)', 'Admin::confirmPayment/$1'); // Konfirmasi pembayaran pesanan (POST) - ditangani oleh Admin::confirmPayment() dengan ID
    $routes->post('order/update-status/(:num)', 'Admin::updateOrderStatus/$1'); // Update status pesanan (POST) - ditangani oleh Admin::updateOrderStatus() dengan ID

    // Admin User Management Routes (Placeholder - butuh method di Admin Controller)
    $routes->get('users', 'Admin::users'); // Daftar pengguna
    // $routes->get('users/edit/(:num)', 'Admin::editUser/$1'); // Form edit pengguna
    // $routes->post('users/update/(:num)', 'Admin::updateUser/$1'); // Update pengguna
    // $routes->post('users/activate/(:num)', 'Admin::activateUser/$1'); // Aktivasi pengguna
    // $routes->post('users/deactivate/(:num)', 'Admin::deactivateUser/$1'); // Deaktivasi pengguna

     // Admin Category Management Routes (Placeholder - butuh method di Admin Controller)
    $routes->get('categories', 'Admin::categories'); // Daftar kategori
    $routes->get('categories/create', 'Admin::createCategory'); // Form tambah kategori
    $routes->post('categories/store', 'Admin::storeCategory'); // Simpan kategori baru
    $routes->get('categories/edit/(:num)', 'Admin::editCategory/$1'); // Form edit kategori
    $routes->post('categories/update/(:num)', 'Admin::updateCategory/$1'); // Update kategori
    $routes->delete('categories/delete/(:num)', 'Admin::deleteCategory/$1'); // Hapus kategori (metode DELETE)

});

// Redirects
// Mengarahkan URL lama atau tidak valid ke URL baru
// Contoh: Mengarahkan /dashboard ke / (Home)
// Jika user yang login adalah admin, Auth Controller akan mengarahkannya ke /admin secara otomatis setelah login, meskipun tujuan awalnya adalah /.
$routes->addRedirect('/dashboard', '/');


// --------------------------------------------------------------------
// Error Handling (Default - bisa dikustomisasi)
// --------------------------------------------------------------------
//$routes->set404Override('App\Controllers\Errors::show404'); // Contoh custom 404 handler jika Anda membuatnya