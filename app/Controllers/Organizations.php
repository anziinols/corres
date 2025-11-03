<?php

namespace App\Controllers;

use App\Models\OrganizationModel;
use CodeIgniter\RESTful\ResourceController;

class Organizations extends ResourceController
{
    protected $modelName = 'App\Models\OrganizationModel';
    protected $format    = 'json';
    protected $session;

    public function __construct()
    {
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Display a list of all organizations
     * GET /organizations
     * 
     * @return mixed
     */
    public function index()
    {
        $model = new OrganizationModel();
        $data = [
            'title' => 'Organizations Management',
            'organizations' => $model->getOrganizationsWithAudit(),
        ];

        return view('dakoii/organizations/index', $data);
    }

    /**
     * Display the form to create a new organization
     * GET /organizations/new
     * 
     * @return mixed
     */
    public function new()
    {
        $model = new OrganizationModel();
        $data = [
            'title' => 'Create Organization',
            'nextOrgCode' => $model->getNextOrgCode(),
        ];

        return view('dakoii/organizations/create', $data);
    }

    /**
     * Process the creation of a new organization
     * POST /organizations
     * 
     * @return mixed
     */
    public function create()
    {
        $model = new OrganizationModel();
        
        // Handle file upload
        $logoFile = $this->request->getFile('org_logo');
        $logoFileName = null;

        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            // Validate file type
            if (!in_array($logoFile->getExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid logo file type. Only JPG, PNG, and GIF are allowed.');
            }

            // Validate file size (max 2MB)
            if ($logoFile->getSize() > 2048000) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Logo file size must not exceed 2MB.');
            }

            // Generate unique filename
            $logoFileName = $logoFile->getRandomName();
            
            // Move file to upload directory
            $logoFile->move(FCPATH . 'uploads/organizations', $logoFileName);
        }

        // Prepare data
        $data = [
            'org_code' => $this->request->getPost('org_code'),
            'org_name' => $this->request->getPost('org_name'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status') ?? 'active',
            'org_logo' => $logoFileName,
        ];

        // Insert data
        if ($model->insert($data)) {
            return redirect()->to('/dakoii/organizations')
                ->with('success', 'Organization created successfully!');
        } else {
            // Delete uploaded file if insertion failed
            if ($logoFileName && file_exists(FCPATH . 'uploads/organizations/' . $logoFileName)) {
                unlink(FCPATH . 'uploads/organizations/' . $logoFileName);
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $model->errors());
        }
    }

    /**
     * Display a single organization
     * GET /organizations/{id}
     * 
     * @param int|null $id
     * @return mixed
     */
    public function show($id = null)
    {
        $model = new OrganizationModel();
        $organization = $model->getOrganizationWithAudit($id);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        $data = [
            'title' => 'View Organization',
            'organization' => $organization,
        ];

        return view('dakoii/organizations/show', $data);
    }

    /**
     * Display the form to edit an organization
     * GET /organizations/{id}/edit
     * 
     * @param int|null $id
     * @return mixed
     */
    public function edit($id = null)
    {
        $model = new OrganizationModel();
        $organization = $model->find($id);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        $data = [
            'title' => 'Edit Organization',
            'organization' => $organization,
        ];

        return view('dakoii/organizations/edit', $data);
    }

    /**
     * Process the update of an organization
     * PUT/PATCH /organizations/{id}
     * 
     * @param int|null $id
     * @return mixed
     */
    public function update($id = null)
    {
        $model = new OrganizationModel();
        $organization = $model->find($id);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        // Handle file upload
        $logoFile = $this->request->getFile('org_logo');
        $logoFileName = $organization['org_logo']; // Keep existing logo by default

        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            // Validate file type
            if (!in_array($logoFile->getExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid logo file type. Only JPG, PNG, and GIF are allowed.');
            }

            // Validate file size (max 2MB)
            if ($logoFile->getSize() > 2048000) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Logo file size must not exceed 2MB.');
            }

            // Delete old logo if exists
            if ($organization['org_logo'] && file_exists(FCPATH . 'uploads/organizations/' . $organization['org_logo'])) {
                unlink(FCPATH . 'uploads/organizations/' . $organization['org_logo']);
            }

            // Generate unique filename
            $logoFileName = $logoFile->getRandomName();
            
            // Move file to upload directory
            $logoFile->move(FCPATH . 'uploads/organizations', $logoFileName);
        }

        // Prepare data
        $data = [
            'org_code' => $this->request->getPost('org_code'),
            'org_name' => $this->request->getPost('org_name'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
            'org_logo' => $logoFileName,
        ];

        // Update data with custom validation to exclude current record
        $model->setValidationRule('org_code', "required|exact_length[4]|is_unique[organizations.org_code,id,{$id}]|numeric");

        if ($model->update($id, $data)) {
            return redirect()->to('/dakoii/organizations')
                ->with('success', 'Organization updated successfully!');
        } else {
            // Delete uploaded file if update failed and it's a new file
            if ($logoFileName !== $organization['org_logo'] && $logoFileName && file_exists(FCPATH . 'uploads/organizations/' . $logoFileName)) {
                unlink(FCPATH . 'uploads/organizations/' . $logoFileName);
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $model->errors());
        }
    }

    /**
     * Delete an organization (soft delete)
     * DELETE /organizations/{id}
     * 
     * @param int|null $id
     * @return mixed
     */
    public function delete($id = null)
    {
        $model = new OrganizationModel();
        $organization = $model->find($id);

        if (!$organization) {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Organization not found.');
        }

        if ($model->delete($id)) {
            return redirect()->to('/dakoii/organizations')
                ->with('success', 'Organization deleted successfully!');
        } else {
            return redirect()->to('/dakoii/organizations')
                ->with('error', 'Failed to delete organization.');
        }
    }

    /**
     * Generate next organization code (AJAX)
     * GET /organizations/generate-code
     * 
     * @return mixed
     */
    public function generateCode()
    {
        $model = new OrganizationModel();
        return $this->respond(['code' => $model->getNextOrgCode()]);
    }
}

