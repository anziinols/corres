<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-pencil me-2"></i>Edit Organization
                    </h2>
                    <p class="text-muted mb-0">Update organization information</p>
                </div>
                <div>
                    <a href="<?= base_url('dakoii/organizations') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
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
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Organization Details</h5>
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

                    <form action="<?= base_url('dakoii/organizations/' . $organization['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <!-- Organization Code -->
                        <div class="mb-3">
                            <label for="org_code" class="form-label">
                                Organization Code <span class="text-danger">*</span>
                                <small class="text-muted">(4 digits)</small>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="org_code" 
                                   name="org_code" 
                                   value="<?= old('org_code', $organization['org_code']) ?>"
                                   maxlength="4"
                                   pattern="[0-9]{4}"
                                   required>
                            <div class="form-text">
                                Current code: <strong><?= esc($organization['org_code']) ?></strong>
                            </div>
                        </div>

                        <!-- Organization Name -->
                        <div class="mb-3">
                            <label for="org_name" class="form-label">
                                Organization Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="org_name" 
                                   name="org_name" 
                                   value="<?= old('org_name', $organization['org_name']) ?>"
                                   maxlength="255"
                                   required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="4"><?= old('description', $organization['description']) ?></textarea>
                        </div>

                        <!-- Current Logo -->
                        <?php if ($organization['org_logo']): ?>
                            <div class="mb-3">
                                <label class="form-label">Current Logo</label>
                                <div>
                                    <img src="<?= base_url('uploads/organizations/' . esc($organization['org_logo'])) ?>" 
                                         alt="Current Logo" 
                                         class="img-thumbnail"
                                         style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Organization Logo -->
                        <div class="mb-3">
                            <label for="org_logo" class="form-label">
                                <?= $organization['org_logo'] ? 'Change Logo' : 'Upload Logo' ?>
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="org_logo" 
                                   name="org_logo" 
                                   accept="image/jpeg,image/png,image/gif">
                            <div class="form-text">
                                Accepted formats: JPG, PNG, GIF. Max size: 2MB
                                <?= $organization['org_logo'] ? '(Leave empty to keep current logo)' : '' ?>
                            </div>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <label class="form-label">New Logo Preview</label>
                                <div>
                                    <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= old('status', $organization['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status', $organization['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('dakoii/organizations') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Update Organization
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Image preview functionality
document.getElementById('org_logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
});

// Validate organization code format
document.getElementById('org_code').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '').substring(0, 4);
});
</script>
<?= $this->endSection() ?>

