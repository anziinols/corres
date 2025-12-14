<?php

namespace App\Controllers;

use App\Models\CorrespondenceTypeModel;
use CodeIgniter\RESTful\ResourceController;

class CorrespondenceTypes extends ResourceController
{
    protected $modelName = 'App\Models\CorrespondenceTypeModel';
    protected $format    = 'json';
    protected $session;

    public function __construct()
    {
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Display a list of all correspondence types
     * GET /dakoii/correspondence-types
     * 
     * @return mixed
     */
    public function index()
    {
        $model = new CorrespondenceTypeModel();
        $data = [
            'title' => 'Correspondence Types Management',
            'types' => $model->getTypesWithAudit(),
        ];

        return view('dakoii/correspondence_types/dakoii_correspondence_types_list', $data);
    }

    /**
     * Display the form to create a new correspondence type
     * GET /dakoii/correspondence-types/new
     * 
     * @return mixed
     */
    public function new()
    {
        $data = [
            'title' => 'Create Correspondence Type',
        ];

        return view('dakoii/correspondence_types/dakoii_correspondence_types_create', $data);
    }

    /**
     * Handle the creation of a new correspondence type
     * POST /dakoii/correspondence-types
     * 
     * @return mixed
     */
    public function create()
    {
        $model = new CorrespondenceTypeModel();

        $data = [
            'name'        => $this->request->getPost('name'),
            'type_number' => $this->request->getPost('type_number'),
            'description' => $this->request->getPost('description'),
        ];

        if ($model->insert($data)) {
            if ($this->request->isAJAX()) {
                return $this->respond([
                    'success' => true,
                    'message' => 'Correspondence type created successfully',
                    'csrf_token_name' => csrf_token(),
                    'csrf_token_value' => csrf_hash(),
                ]);
            }

            return redirect()->to(base_url('dakoii/correspondence-types'))
                ->with('success', 'Correspondence type created successfully');
        }

        $errors = $model->errors();
        
        if ($this->request->isAJAX()) {
            return $this->fail([
                'success' => false,
                'message' => 'Failed to create correspondence type',
                'errors' => $errors,
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ], 400);
        }

        return redirect()->back()
            ->withInput()
            ->with('errors', $errors)
            ->with('error', 'Failed to create correspondence type');
    }

    /**
     * Display a single correspondence type
     * GET /dakoii/correspondence-types/{id}
     * 
     * @param int $id
     * @return mixed
     */
    public function show($id = null)
    {
        $model = new CorrespondenceTypeModel();
        $type = $model->getTypeWithAudit($id);

        if (!$type) {
            return redirect()->to(base_url('dakoii/correspondence-types'))
                ->with('error', 'Correspondence type not found');
        }

        $data = [
            'title' => 'View Correspondence Type',
            'type' => $type,
        ];

        return view('dakoii/correspondence_types/dakoii_correspondence_types_view', $data);
    }

    /**
     * Display the form to edit a correspondence type
     * GET /dakoii/correspondence-types/{id}/edit
     * 
     * @param int $id
     * @return mixed
     */
    public function edit($id = null)
    {
        $model = new CorrespondenceTypeModel();
        $type = $model->find($id);

        if (!$type) {
            return redirect()->to(base_url('dakoii/correspondence-types'))
                ->with('error', 'Correspondence type not found');
        }

        $data = [
            'title' => 'Edit Correspondence Type',
            'type' => $type,
        ];

        return view('dakoii/correspondence_types/dakoii_correspondence_types_edit', $data);
    }

    /**
     * Handle the update of a correspondence type
     * PUT/PATCH /dakoii/correspondence-types/{id}
     * 
     * @param int $id
     * @return mixed
     */
    public function update($id = null)
    {
        $model = new CorrespondenceTypeModel();
        $type = $model->find($id);

        if (!$type) {
            if ($this->request->isAJAX()) {
                return $this->fail([
                    'success' => false,
                    'message' => 'Correspondence type not found',
                    'csrf_token_name' => csrf_token(),
                    'csrf_token_value' => csrf_hash(),
                ], 404);
            }

            return redirect()->to(base_url('dakoii/correspondence-types'))
                ->with('error', 'Correspondence type not found');
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'type_number' => $this->request->getPost('type_number'),
            'description' => $this->request->getPost('description'),
        ];

        // Set validation rules with current ID for unique check
        $model->setValidationRule('type_number', "required|max_length[50]|is_unique[correspondence_types.type_number,id,{$id}]");

        if ($model->update($id, $data)) {
            if ($this->request->isAJAX()) {
                return $this->respond([
                    'success' => true,
                    'message' => 'Correspondence type updated successfully',
                    'csrf_token_name' => csrf_token(),
                    'csrf_token_value' => csrf_hash(),
                ]);
            }

            return redirect()->to(base_url('dakoii/correspondence-types/' . $id))
                ->with('success', 'Correspondence type updated successfully');
        }

        $errors = $model->errors();

        if ($this->request->isAJAX()) {
            return $this->fail([
                'success' => false,
                'message' => 'Failed to update correspondence type',
                'errors' => $errors,
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ], 400);
        }

        return redirect()->back()
            ->withInput()
            ->with('errors', $errors)
            ->with('error', 'Failed to update correspondence type');
    }

    /**
     * Handle the deletion of a correspondence type (soft delete)
     * DELETE /dakoii/correspondence-types/{id}
     * 
     * @param int $id
     * @return mixed
     */
    public function delete($id = null)
    {
        $model = new CorrespondenceTypeModel();
        $type = $model->find($id);

        if (!$type) {
            if ($this->request->isAJAX()) {
                return $this->fail([
                    'success' => false,
                    'message' => 'Correspondence type not found',
                    'csrf_token_name' => csrf_token(),
                    'csrf_token_value' => csrf_hash(),
                ], 404);
            }

            return redirect()->to(base_url('dakoii/correspondence-types'))
                ->with('error', 'Correspondence type not found');
        }

        if ($model->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->respond([
                    'success' => true,
                    'message' => 'Correspondence type deleted successfully',
                    'csrf_token_name' => csrf_token(),
                    'csrf_token_value' => csrf_hash(),
                ]);
            }

            return redirect()->to(base_url('dakoii/correspondence-types'))
                ->with('success', 'Correspondence type deleted successfully');
        }

        if ($this->request->isAJAX()) {
            return $this->fail([
                'success' => false,
                'message' => 'Failed to delete correspondence type',
                'csrf_token_name' => csrf_token(),
                'csrf_token_value' => csrf_hash(),
            ], 400);
        }

        return redirect()->to(base_url('dakoii/correspondence-types'))
            ->with('error', 'Failed to delete correspondence type');
    }
}

