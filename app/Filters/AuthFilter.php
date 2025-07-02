<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\RedirectResponse;

class AuthFilter implements FilterInterface
{
    /**
     * Checks if the user is logged in. If not, redirects to login page.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RedirectResponse|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // If user is not logged in
        if (!session()->get('logged_in')) {
            // Store the current URL to redirect back after login (optional but recommended)
            session()->setFlashdata('redirect_url', current_url());
            return redirect()->to('/login')->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

         // Optional: Check if user is active (e.g., if admin deactivated account)
         // $userModel = new \App\Models\UserModel();
         // $user = $userModel->find(session()->get('user_id'));
         // if (!$user || !$user['is_active']) {
         //      session()->destroy();
         //      return redirect()->to('/login')->with('error', 'Akun Anda tidak aktif.');
         // }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}