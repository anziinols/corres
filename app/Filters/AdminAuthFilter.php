<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    /**
     * Check if user is authenticated for admin portal
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Check if user is logged in to admin portal
        if (!$session->has('admin_logged_in') || !$session->get('admin_logged_in')) {
            // Store the intended URL
            $session->set('admin_redirect_url', current_url());

            // Redirect to login page with message
            return redirect()->to('/login')->with('error', 'Please log in to access this page.');
        }
    }

    /**
     * After the controller method has run
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

