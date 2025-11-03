<?php

namespace App\Controllers;

use App\Models\GroupModel;
use App\Models\OrganizationModel;
use CodeIgniter\RESTful\ResourceController;

class Groups extends ResourceController
{
    protected $modelName = 'App\Models\GroupModel';
    protected $format    = 'json';
    protected $session;

    public function __construct()
    {
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Display a list of all groups for a specific organization
     * GET /organizations/{org_id}/groups
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

        $groupModel = new GroupModel();
        $data = [
            'title' => 'Groups Management',
            'organization' => $organization,
            'groups' => $groupModel->getGroupsByOrganization($orgId),
        ];

        return view('dakoii/groups/dakoii_groups_list', $data);
    }

    /**
     * Display the form to create a new group
     * GET /organizations/{org_id}/groups/new
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
            'title' => 'Create Group',
            'organization' => $organization,
            'nextGroupCode' => $groupModel->getNextGroupCode(),
            'parentGroups' => $groupModel->getAvailableParentGroups($orgId),
        ];

        return view('dakoii/groups/dakoii_groups_create', $data);
    }

    /**
     * Process the creation of a new group
     * POST /organizations/{org_id}/groups
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

        $groupModel = new GroupModel();

        // Prepare data
        $data = [
            'group_code' => $this->request->getPost('group_code'),
            'group_name' => $this->request->getPost('group_name'),
            'organization_id' => $orgId,
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status') ?? 'active',
        ];

        // Insert data
        if ($groupModel->insert($data)) {
            return redirect()->to("/dakoii/organizations/{$orgId}/groups")
                ->with('success', 'Group created successfully!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errors', $groupModel->errors());
        }
    }

    /**
     * Display a single group
     * GET /organizations/{org_id}/groups/{id}
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

        $groupModel = new GroupModel();
        $group = $groupModel->getGroupWithAudit($id);

        if (!$group || $group['organization_id'] != $orgId) {
            return redirect()->to("/dakoii/organizations/{$orgId}/groups")
                ->with('error', 'Group not found.');
        }

        $data = [
            'title' => 'View Group',
            'organization' => $organization,
            'group' => $group,
        ];

        return view('dakoii/groups/dakoii_groups_view', $data);
    }

    /**
     * Display the form to edit a group
     * GET /organizations/{org_id}/groups/{id}/edit
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

        $groupModel = new GroupModel();
        $group = $groupModel->find($id);

        if (!$group || $group['organization_id'] != $orgId) {
            return redirect()->to("/dakoii/organizations/{$orgId}/groups")
                ->with('error', 'Group not found.');
        }

        $data = [
            'title' => 'Edit Group',
            'organization' => $organization,
            'group' => $group,
            'parentGroups' => $groupModel->getAvailableParentGroups($orgId, $id),
        ];

        return view('dakoii/groups/dakoii_groups_edit', $data);
    }

    /**
     * Process the update of a group
     * PUT/PATCH /organizations/{org_id}/groups/{id}
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

        $groupModel = new GroupModel();
        $group = $groupModel->find($id);

        if (!$group || $group['organization_id'] != $orgId) {
            return redirect()->to("/dakoii/organizations/{$orgId}/groups")
                ->with('error', 'Group not found.');
        }

        // Prepare data
        $data = [
            'group_code' => $this->request->getPost('group_code'),
            'group_name' => $this->request->getPost('group_name'),
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status'),
        ];

        // Update data with custom validation to exclude current record
        $groupModel->setValidationRule('group_code', "required|max_length[10]|is_unique[groups.group_code,id,{$id}]");

        if ($groupModel->update($id, $data)) {
            return redirect()->to("/dakoii/organizations/{$orgId}/groups")
                ->with('success', 'Group updated successfully!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errors', $groupModel->errors());
        }
    }

    /**
     * Delete a group (soft delete)
     * DELETE /organizations/{org_id}/groups/{id}
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

        $groupModel = new GroupModel();
        $group = $groupModel->find($id);

        if (!$group || $group['organization_id'] != $orgId) {
            return redirect()->to("/dakoii/organizations/{$orgId}/groups")
                ->with('error', 'Group not found.');
        }

        if ($groupModel->delete($id)) {
            return redirect()->to("/dakoii/organizations/{$orgId}/groups")
                ->with('success', 'Group deleted successfully!');
        } else {
            return redirect()->to("/dakoii/organizations/{$orgId}/groups")
                ->with('error', 'Failed to delete group.');
        }
    }
}

