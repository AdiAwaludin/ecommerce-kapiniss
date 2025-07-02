<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\RedirectResponse; // Import RedirectResponse
use CodeIgniter\Exceptions\PageNotFoundException; // Import PageNotFoundException for throwing 404/403

class AdminFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. Anything written to the Response
     * object will be served back to the client, and any
     * thrown exception will be caught and handled by the
     * ErrorHandler if one is defined.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|RedirectResponse|PageNotFoundException|object|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Langkah 1: Cek apakah pengguna sudah login
        // Ini mirip dengan AuthFilter. Sebaiknya pengguna memang sudah login
        // sebelum dicek perannya. Jika belum login, arahkan ke halaman login.
        if (!session()->get('logged_in')) {
            // Simpan URL saat ini untuk redirect kembali setelah login
            session()->setFlashdata('redirect_url', current_url());
            return redirect()->to('/login')->with('error', 'Anda harus login untuk mengakses halaman admin.');
        }

        // Langkah 2: Jika sudah login, cek perannya (role)
        // Cek apakah peran pengguna BUKAN 'admin'
        if (session()->get('role') !== 'admin') {
            // Jika bukan admin, berikan respons "Access Denied".
            // Opsi yang lebih aman adalah melempar PageNotFoundException (status 404 Not Found)
            // agar penyerang tidak tahu halaman admin itu ada.
            throw new PageNotFoundException('Access denied');

            // Opsi lain (kurang aman, lebih user-friendly) adalah redirect ke halaman utama
            // return redirect()->to('/')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman admin.');
        }

         // Optional: Cek apakah pengguna admin yang login masih aktif
         // $userModel = new \App\Models\UserModel();
         // $user = $userModel->find(session()->get('user_id'));
         // if (!$user || !$user['is_active']) {
         //      session()->destroy(); // Hapus sesi admin yang tidak aktif
         //      return redirect()->to('/login')->with('error', 'Akun admin Anda tidak aktif. Silakan login kembali.');
         // }

        // Jika pengguna login dan perannya admin, biarkan request berlanjut ke Controller
        // Tidak perlu return apa pun.
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi yang perlu dilakukan setelah Controller selesai untuk filter ini
    }
}