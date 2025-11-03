<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
    }

    /**
     * Display login form (GET)
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->has('admin_logged_in') && $this->session->get('admin_logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        $data = [
            'title' => 'Admin Login',
        ];

        return view('admin/admin_login', $data);
    }

    /**
     * Process login credentials (POST)
     */
    public function authenticate()
    {
        // Validate input
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[4]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Verify credentials
        $user = $this->userModel->verifyCredentials($email, $password);

        if ($user) {
            // Set session data with frequently used fields
            $sessionData = [
                'admin_user_id'          => $user['id'],
                'admin_email'            => $user['email'],
                'admin_name'             => $user['name'],
                'admin_position'         => $user['position'] ?? null,
                'admin_org_id'           => $user['organization_id'],
                'admin_group_id'         => $user['group_id'],
                'admin_signature_path'   => $user['signature_filepath'] ?? null,
                'admin_stamp_path'       => $user['stamp_filepath'] ?? null,
                'admin_is_admin'         => (bool)$user['is_admin'],
                'admin_is_supervisor'    => (bool)$user['is_supervisor'],
                'admin_is_front_desk'    => (bool)$user['is_front_desk'],
                'admin_logged_in'        => true,
            ];

            $this->session->set($sessionData);

            // Check if there's a redirect URL
            $redirectUrl = $this->session->get('admin_redirect_url');
            if ($redirectUrl) {
                $this->session->remove('admin_redirect_url');
                return redirect()->to($redirectUrl);
            }

            return redirect()->to('/admin/dashboard')->with('success', 'Welcome back, ' . $user['name'] . '!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password. Please try again.');
        }
    }

    /**
     * Display dashboard (GET) - Requires authentication
     */
    public function dashboard()
    {
        $data = [
            'title' => 'Admin Dashboard',
            'user'  => [
                'id'              => $this->session->get('admin_user_id'),
                'email'           => $this->session->get('admin_email'),
                'name'            => $this->session->get('admin_name'),
                'position'        => $this->session->get('admin_position'),
                'organization_id' => $this->session->get('admin_org_id'),
                'group_id'        => $this->session->get('admin_group_id'),
                'signature_path'  => $this->session->get('admin_signature_path'),
                'stamp_path'      => $this->session->get('admin_stamp_path'),
                'is_admin'        => $this->session->get('admin_is_admin'),
                'is_supervisor'   => $this->session->get('admin_is_supervisor'),
                'is_front_desk'   => $this->session->get('admin_is_front_desk'),
            ],
        ];

        return view('admin/admin_dashboard', $data);
    }

    /**
     * Logout user (POST)
     */
    public function logout()
    {
        // Remove all admin session data
        $this->session->remove([
            'admin_user_id',
            'admin_email',
            'admin_name',
            'admin_position',
            'admin_org_id',
            'admin_group_id',
            'admin_signature_path',
            'admin_stamp_path',
            'admin_is_admin',
            'admin_is_supervisor',
            'admin_is_front_desk',
            'admin_logged_in',
        ]);

        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }
}

