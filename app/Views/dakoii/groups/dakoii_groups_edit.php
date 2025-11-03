<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/organizations') ?>">Organizations</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/organizations/' . $organization['id']) ?>"><?= esc($organization['org_name']) ?></a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups') ?>">Groups</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-pencil me-2"></i>Edit Group
                    </h2>
                    <p class="text-muted mb-0">Update group information</p>
                </div>
                <div>
                    <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Groups
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Group Details</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/' . $group['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <!-- Group Code -->
                        <div class="mb-3">
                            <label for="group_code" class="form-label">
                                Group Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="group_code" 
                                   name="group_code" 
                                   value="<?= old('group_code', $group['group_code']) ?>"
                                   maxlength="10"
                                   required>
                            <div class="form-text">
                                Current code: <strong><?= esc($group['group_code']) ?></strong>
                            </div>
                        </div>

                        <!-- Group Name -->
                        <div class="mb-3">
                            <label for="group_name" class="form-label">
                                Group Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="group_name" 
                                   name="group_name" 
                                   value="<?= old('group_name', $group['group_name']) ?>"
                                   maxlength="255"
                                   required>
                        </div>

                        <!-- Parent Group -->
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">
                                Parent Group <small class="text-muted">(Optional - for hierarchical structure)</small>
                            </label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">— No Parent (Top Level) —</option>
                                <?php foreach ($parentGroups as $parent): ?>
                                    <option value="<?= $parent['id'] ?>" <?= old('parent_id', $group['parent_id']) == $parent['id'] ? 'selected' : '' ?>>
                                        <?= esc($parent['group_code']) ?> - <?= esc($parent['group_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Note: Current group is excluded from parent options to prevent circular references
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="4"><?= old('description', $group['description']) ?></textarea>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= old('status', $group['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status', $group['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Update Group
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

