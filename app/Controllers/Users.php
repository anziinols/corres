<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OrganizationModel;
use App\Models\GroupModel;
use CodeIgniter\RESTful\ResourceController;

class Users extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';
    protected $session;

    public function __construct()
    {
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Display a list of all users for a specific organization
     * GET /organizations/{org_id}/users
     * 
     * @param int $orgId
     * @return mixed
     */
    public function index($orgId = null)
    {
        $orgModel = new OrganizationModel();
        $organization = $orgModel->find($orgId);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        $userModel = new UserModel();
        $data = [
            'title' => 'Users Management',
            'organization' => $organization,
            'users' => $userModel->getUsersByOrganization($orgId),
        ];

        return view('dakoii/users/dakoii_users_list', $data);
    }

    /**
     * Display the form to create a new user
     * GET /organizations/{org_id}/users/new
     * 
     * @param int $orgId
     * @return mixed
     */
    public function new($orgId = null)
    {
        $orgModel = new OrganizationModel();
        $organization = $orgModel->find($orgId);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        $groupModel = new GroupModel();
        $data = [
            'title' => 'Create User',
            'organization' => $organization,
            'groups' => $groupModel->getAvailableParentGroups($orgId), // Reusing this method as it gets active groups
        ];

        return view('dakoii/users/dakoii_users_create', $data);
    }

    /**
     * Process the creation of a new user
     * POST /organizations/{org_id}/users
     * 
     * @param int $orgId
     * @return mixed
     */
    public function create($orgId = null)
    {
        $orgModel = new OrganizationModel();
        $organization = $orgModel->find($orgId);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        $userModel = new UserModel();

        // Handle file uploads
        $signatureFilepath = null;
        $stampFilepath = null;

        $signatureFile = $this->request->getFile('signature_file');
        if ($signatureFile && $signatureFile->isValid() && !$signatureFile->hasMoved()) {
            $signatureName = $signatureFile->getRandomName();
            $signatureFile->move(FCPATH . 'uploads/signatures', $signatureName);
            $signatureFilepath = 'public/uploads/signatures/' . $signatureName;
        }

        $stampFile = $this->request->getFile('stamp_file');
        if ($stampFile && $stampFile->isValid() && !$stampFile->hasMoved()) {
            $stampName = $stampFile->getRandomName();
            $stampFile->move(FCPATH . 'uploads/stamps', $stampName);
            $stampFilepath = 'public/uploads/stamps/' . $stampName;
        }

        // Prepare data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'position' => $this->request->getPost('position') ?: null,
            'organization_id' => $orgId,
            'group_id' => $this->request->getPost('group_id') ?: null,
            'signature_filepath' => $signatureFilepath,
            'stamp_filepath' => $stampFilepath,
            'is_admin' => $this->request->getPost('is_admin') ? 1 : 0,
            'is_supervisor' => $this->request->getPost('is_supervisor') ? 1 : 0,
            'is_front_desk' => $this->request->getPost('is_front_desk') ? 1 : 0,
            'status' => $this->request->getPost('status') ?? 'active',
        ];

        // Insert data
        if ($userModel->insert($data)) {
            return redirect()->to("/dakoii/organizations/{$orgId}/users")
                ->with('success', 'User created successfully!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errors', $userModel->errors());
        }
    }

    /**
     * Display a single user
     * GET /organizations/{org_id}/users/{id}
     * 
     * @param int $orgId
     * @param int|null $id
     * @return mixed
     */
    public function show($orgId = null, $id = null)
    {
        $orgModel = new OrganizationModel();
        $organization = $orgModel->find($orgId);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        $userModel = new UserModel();
        $user = $userModel->getUserWithAudit($id);

        if (!$user || $user['organization_id'] != $orgId) {
            return redirect()->to("/dakoii/organizations/{$orgId}/users")
                ->with('error', 'User not found.');
        }

        $data = [
            'title' => 'View User',
            'organization' => $organization,
            'user' => $user,
        ];

        return view('dakoii/users/dakoii_users_view', $data);
    }

    /**
     * Display the form to edit a user
     * GET /organizations/{org_id}/users/{id}/edit
     * 
     * @param int $orgId
     * @param int|null $id
     * @return mixed
     */
    public function edit($orgId = null, $id = null)
    {
        $orgModel = new OrganizationModel();
        $organization = $orgModel->find($orgId);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user || $user['organization_id'] != $orgId) {
            return redirect()->to("/dakoii/organizations/{$orgId}/users")
                ->with('error', 'User not found.');
        }

        $groupModel = new GroupModel();
        $data = [
            'title' => 'Edit User',
            'organization' => $organization,
            'user' => $user,
            'groups' => $groupModel->getAvailableParentGroups($orgId),
        ];

        return view('dakoii/users/dakoii_users_edit', $data);
    }

    /**
     * Process the update of a user
     * PUT/PATCH /organizations/{org_id}/users/{id}
     * 
     * @param int $orgId
     * @param int|null $id
     * @return mixed
     */
    public function update($orgId = null, $id = null)
    {
        $orgModel = new OrganizationModel();
        $organization = $orgModel->find($orgId);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user || $user['organization_id'] != $orgId) {
            return redirect()->to("/dakoii/organizations/{$orgId}/users")
                ->with('error', 'User not found.');
        }

        // Handle file uploads
        $signatureFilepath = $user['signature_filepath'];
        $stampFilepath = $user['stamp_filepath'];

        $signatureFile = $this->request->getFile('signature_file');
        if ($signatureFile && $signatureFile->isValid() && !$signatureFile->hasMoved()) {
            // Delete old file if exists
            if ($user['signature_filepath'] && file_exists($user['signature_filepath'])) {
                @unlink($user['signature_filepath']);
            }
            $signatureName = $signatureFile->getRandomName();
            $signatureFile->move(FCPATH . 'uploads/signatures', $signatureName);
            $signatureFilepath = 'public/uploads/signatures/' . $signatureName;
        }

        $stampFile = $this->request->getFile('stamp_file');
        if ($stampFile && $stampFile->isValid() && !$stampFile->hasMoved()) {
            // Delete old file if exists
            if ($user['stamp_filepath'] && file_exists($user['stamp_filepath'])) {
                @unlink($user['stamp_filepath']);
            }
            $stampName = $stampFile->getRandomName();
            $stampFile->move(FCPATH . 'uploads/stamps', $stampName);
            $stampFilepath = 'public/uploads/stamps/' . $stampName;
        }

        // Prepare data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'position' => $this->request->getPost('position') ?: null,
            'group_id' => $this->request->getPost('group_id') ?: null,
            'signature_filepath' => $signatureFilepath,
            'stamp_filepath' => $stampFilepath,
            'is_admin' => $this->request->getPost('is_admin') ? 1 : 0,
            'is_supervisor' => $this->request->getPost('is_supervisor') ? 1 : 0,
            'is_front_desk' => $this->request->getPost('is_front_desk') ? 1 : 0,
            'status' => $this->request->getPost('status'),
        ];

        // Only update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        // Update data with custom validation to exclude current record
        $userModel->setValidationRule('email', "required|valid_email|max_length[255]|is_unique[users.email,id,{$id}]");
        
        // Make password optional for update
        if (empty($password)) {
            $userModel->setValidationRule('password', 'permit_empty|min_length[4]');
        }

        if ($userModel->update($id, $data)) {
            return redirect()->to("/dakoii/organizations/{$orgId}/users")
                ->with('success', 'User updated successfully!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errors', $userModel->errors());
        }
    }

    /**
     * Delete a user (soft delete)
     * DELETE /organizations/{org_id}/users/{id}
     * 
     * @param int $orgId
     * @param int|null $id
     * @return mixed
     */
    public function delete($orgId = null, $id = null)
    {
        $orgModel = new OrganizationModel();
        $organization = $orgModel->find($orgId);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user || $user['organization_id'] != $orgId) {
            return redirect()->to("/dakoii/organizations/{$orgId}/users")
                ->with('error', 'User not found.');
        }

        if ($userModel->delete($id)) {
            return redirect()->to("/dakoii/organizations/{$orgId}/users")
                ->with('success', 'User deleted successfully!');
        } else {
            return redirect()->to("/dakoii/organizations/{$orgId}/users")
                ->with('error', 'Failed to delete user.');
        }
    }
}

