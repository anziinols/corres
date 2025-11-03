<?php

namespace App\Controllers;

use App\Models\DakoiiUserModel;

class Dakoii extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new DakoiiUserModel();
        $this->session = session();
    }

    /**
     * Display login form (GET)
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->has('dakoii_logged_in') && $this->session->get('dakoii_logged_in')) {
            return redirect()->to('/dakoii/dashboard');
        }

        $data = [
            'title' => 'Login',
        ];

        return view('dakoii/login', $data);
    }

    /**
     * Process login credentials (POST)
     */
    public function authenticate()
    {
        // Validate input
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Verify credentials
        $user = $this->userModel->verifyCredentials($username, $password);

        if ($user) {
            // Set session data
            $sessionData = [
                'dakoii_user_id'   => $user['id'],
                'dakoii_username'  => $user['username'],
                'dakoii_name'      => $user['name'],
                'dakoii_logged_in' => true,
            ];

            $this->session->set($sessionData);

            // Check if there's a redirect URL
            $redirectUrl = $this->session->get('dakoii_redirect_url');
            if ($redirectUrl) {
                $this->session->remove('dakoii_redirect_url');
                return redirect()->to($redirectUrl);
            }

            return redirect()->to('/dakoii/dashboard')->with('success', 'Welcome back, ' . $user['name'] . '!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
        }
    }

    /**
     * Display dashboard (GET) - Requires authentication
     */
    public function dashboard()
    {
        $data = [
            'title' => 'Dashboard',
            'user'  => [
                'id'       => $this->session->get('dakoii_user_id'),
                'username' => $this->session->get('dakoii_username'),
                'name'     => $this->session->get('dakoii_name'),
            ],
        ];

        return view('dakoii/dashboard', $data);
    }

    /**
     * Logout user (POST)
     */
    public function logout()
    {
        // Remove all Dakoii session data
        $this->session->remove([
            'dakoii_user_id',
            'dakoii_username',
            'dakoii_name',
            'dakoii_logged_in',
        ]);

        return redirect()->to('/dakoii')->with('success', 'You have been logged out successfully.');
    }
}

