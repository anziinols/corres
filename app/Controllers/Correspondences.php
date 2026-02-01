<?php

namespace App\Controllers;

use App\Models\CorrespondenceModel;
use App\Models\OrganizationModel;
use App\Models\GroupModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Correspondences extends ResourceController
{
    protected $modelName = 'App\Models\CorrespondenceModel';
    protected $format    = 'json';
    protected $session;

    public function __construct()
    {
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Display a list of all correspondences
     * GET /admin/correspondences
     * 
     * @return mixed
     */
    public function index()
    {
        $corrModel = new CorrespondenceModel();
        
        $data = [
            'title' => 'Correspondence Management',
            'correspondences' => $corrModel->getAllWithDetails(),
        ];

        return view('admin/correspondences/correspondences_list', $data);
    }

    /**
     * Display the form to create a new correspondence
     * GET /admin/correspondences/new
     * 
     * @return mixed
     */
    public function new()
    {
        $orgModel = new OrganizationModel();
        $groupModel = new GroupModel();
        $userModel = new UserModel();
        $corrModel = new CorrespondenceModel();
        $typeModel = new \App\Models\CorrespondenceTypeModel();

        $data = [
            'title' => 'Register New Correspondence',
            'organizations' => $orgModel->where('status', 'active')->findAll(),
            'groups' => $groupModel->where('status', 'active')->findAll(),
            'users' => $userModel->where('status', 'active')->findAll(),
            'correspondence_types' => $typeModel->orderBy('type_number', 'ASC')->findAll(),
            'suggested_number' => $corrModel->generateCorrespondenceNumber(),
            'existing_correspondences' => $corrModel->select('id, correspondence_number, subject')->orderBy('created_at', 'DESC')->findAll(),
            'ai_api_key' => getenv('AI_API_KEY'),
            'ai_model' => getenv('AI_MODEL'),
        ];

        return view('admin/correspondences/correspondences_create', $data);
    }

    /**
     * Process the creation of a new correspondence
     * POST /admin/correspondences
     * 
     * @return mixed
     */
    public function create()
    {
        $corrModel = new CorrespondenceModel();
        
        $postData = $this->request->getPost();
        
        // Auto-generate correspondence number if not provided
        if (empty($postData['correspondence_number'])) {
            $department = $postData['department'] ?? null;
            $postData['correspondence_number'] = $corrModel->generateCorrespondenceNumber($department);
        }

        // Handle File Upload
        $file = $this->request->getFile('correspondence_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            // Move to public/uploads/correspondences using ROOTPATH
            $file->move(ROOTPATH . 'public/uploads/correspondences', $newName);
            // Store path with public/ prefix
            $postData['file_path'] = 'public/uploads/correspondences/' . $newName;
            $postData['file_type'] = $file->getClientMimeType();
        }
        
        // Set registered_by from session
        $postData['registered_by'] = $this->session->get('dakoii_user_id');
        
        if ($corrModel->save($postData)) {
            $newId = $corrModel->getInsertID();

            // Handle Linked Correspondences
            $linkedIds = $this->request->getPost('linked_correspondence_ids');
            if (!empty($linkedIds) && is_array($linkedIds)) {
                $linkModel = new \App\Models\CorrespondenceLinkModel();
                foreach ($linkedIds as $linkId) {
                    $linkModel->insert([
                        'correspondence_id' => $newId,
                        'linked_correspondence_id' => $linkId
                    ]);
                }
            }

            return $this->respondCreated([
                'success' => true,
                'message' => 'Correspondence registered successfully',
                'id' => $newId,
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ]);
        } else {
            return $this->fail([
                'success' => false,
                'message' => 'Failed to register correspondence',
                'errors' => $corrModel->errors(),
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ], 400);
        }
    }

    /**
     * Display a single correspondence
     * GET /admin/correspondences/{id}
     * 
     * @param int $id
     * @return mixed
     */
    public function show($id = null)
    {
        $corrModel = new CorrespondenceModel();
        $correspondence = $corrModel->getWithDetails($id);

        if (!$correspondence) {
            return redirect()->to('/admin/correspondences')
                ->with('error', 'Correspondence not found.');
        }

        // Get linked correspondences
        $linkModel = new \App\Models\CorrespondenceLinkModel();
        $linked = $linkModel->select('correspondences.id, correspondences.correspondence_number, correspondences.subject')
            ->join('correspondences', 'correspondences.id = correspondence_links.linked_correspondence_id')
            ->where('correspondence_links.correspondence_id', $id)
            ->findAll();

        $data = [
            'title' => 'View Correspondence',
            'correspondence' => $correspondence,
            'linked_correspondences' => $linked
        ];

        return view('admin/correspondences/correspondences_view', $data);
    }

    /**
     * Display the form to edit a correspondence
     * GET /admin/correspondences/{id}/edit
     * 
     * @param int $id
     * @return mixed
     */
    public function edit($id = null)
    {
        $corrModel = new CorrespondenceModel();
        $correspondence = $corrModel->find($id);

        if (!$correspondence) {
            return redirect()->to('/admin/correspondences')
                ->with('error', 'Correspondence not found.');
        }

        $orgModel = new OrganizationModel();
        $groupModel = new GroupModel();
        $userModel = new UserModel();
        $typeModel = new \App\Models\CorrespondenceTypeModel();
        $linkModel = new \App\Models\CorrespondenceLinkModel();

        // Get currently linked IDs
        $currentLinks = $linkModel->where('correspondence_id', $id)->findAll();
        $linkedIds = array_column($currentLinks, 'linked_correspondence_id');

        // Get all existing correspondences for the dropdown (exclude current)
        $existing = $corrModel->where('id !=', $id)
                             ->orderBy('created_at', 'DESC')
                             ->findAll();

        $data = [
            'title' => 'Edit Correspondence',
            'correspondence' => $correspondence,
            'organizations' => $orgModel->where('status', 'active')->findAll(),
            'groups' => $groupModel->where('status', 'active')->findAll(),
            'users' => $userModel->where('status', 'active')->findAll(),
            'correspondence_types' => $typeModel->orderBy('type_number', 'ASC')->findAll(),
            'existing_correspondences' => $existing,
            'linked_correspondence_ids' => $linkedIds
        ];

        return view('admin/correspondences/correspondences_edit', $data);
    }

    /**
     * Process the update of a correspondence
     * PUT/PATCH /admin/correspondences/{id}
     * 
     * @param int $id
     * @return mixed
     */
    public function update($id = null)
    {
        $corrModel = new CorrespondenceModel();
        $correspondence = $corrModel->find($id);

        if (!$correspondence) {
            return $this->fail([
                'success' => false,
                'message' => 'Correspondence not found',
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ], 404);
        }

        $postData = $this->request->getPost();
        $postData['id'] = $id; // Inject ID for validation rules that use {id} placeholder

        // Handle File Upload (Optional replacement)
        $file = $this->request->getFile('correspondence_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            // Move to public/uploads/correspondences using ROOTPATH
            $file->move(ROOTPATH . 'public/uploads/correspondences', $newName);
            // Store path with public/ prefix
            $postData['file_path'] = 'public/uploads/correspondences/' . $newName;
            $postData['file_type'] = $file->getClientMimeType();
        }
        
        // Use save() instead of update() as it handles IDs and validation cleaner
        if ($corrModel->save($postData)) {
            // Handle Linked Correspondences Sync
            $linkModel = new \App\Models\CorrespondenceLinkModel();
            
            // Delete existing
            $linkModel->where('correspondence_id', $id)->delete();

            // Insert new
            $linkedIds = $this->request->getPost('linked_correspondence_ids');
            if (!empty($linkedIds) && is_array($linkedIds)) {
                foreach ($linkedIds as $linkId) {
                    $linkModel->insert([
                        'correspondence_id' => $id,
                        'linked_correspondence_id' => $linkId
                    ]);
                }
            }

            return $this->respond([
                'success' => true,
                'message' => 'Correspondence updated successfully',
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ]);
        } else {
            return $this->fail([
                'success' => false,
                'message' => 'Failed to update correspondence',
                'errors' => $corrModel->errors(),
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ], 400);
        }
    }

    /**
     * Delete a correspondence (soft delete)
     * DELETE /admin/correspondences/{id}
     * 
     * @param int $id
     * @return mixed
     */
    public function delete($id = null)
    {
        $corrModel = new CorrespondenceModel();
        $correspondence = $corrModel->find($id);

        if (!$correspondence) {
            return $this->fail([
                'success' => false,
                'message' => 'Correspondence not found',
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ], 404);
        }

        if ($corrModel->delete($id)) {
            return $this->respond([
                'success' => true,
                'message' => 'Correspondence deleted successfully',
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ]);
        } else {
            return $this->fail([
                'success' => false,
                'message' => 'Failed to delete correspondence',
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ], 400);
        }
    }

    /**
     * Generate correspondence number via AJAX
     * GET /admin/correspondences/generate-number
     * 
     * @return mixed
     */
    public function generateNumber()
    {
        $corrModel = new CorrespondenceModel();
        $department = $this->request->getGet('department');
        
        $number = $corrModel->generateCorrespondenceNumber($department);
        
        return $this->respond([
            'success' => true,
            'number' => $number,
        ]);
    }
}

