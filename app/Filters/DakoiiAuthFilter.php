<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class DakoiiAuthFilter implements FilterInterface
{
    /**
     * Check if user is authenticated before allowing access
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Check if user is logged in
        if (!$session->has('dakoii_logged_in') || !$session->get('dakoii_logged_in')) {
            // Store the intended URL to redirect after login
            $session->set('dakoii_redirect_url', current_url());
            
            // Redirect to login page with error message
            return redirect()->to('/dakoii')->with('error', 'Please login to access this page.');
        }
    }

    /**
     * Allows After filters to inspect and modify the response object as needed.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

