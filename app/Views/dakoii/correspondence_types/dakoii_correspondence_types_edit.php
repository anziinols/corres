<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/correspondence-types') ?>">Correspondence Types</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="bi bi-pencil-square me-2"></i>Edit Correspondence Type
                            </h2>
                            <p class="text-muted mb-0">Update correspondence type information</p>
                        </div>
                        <div>
                            <a href="<?= base_url('dakoii/correspondence-types/' . $type['id']) ?>" class="btn btn-outline-info me-2">
                                <i class="bi bi-eye me-2"></i>View
                            </a>
                            <a href="<?= base_url('dakoii/correspondence-types') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Type Information</h5>
                </div>
                <div class="card-body">
                    <form id="editTypeForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="mb-3">
                            <label for="type_number" class="form-label">
                                Type Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="type_number" 
                                   name="type_number" 
                                   value="<?= esc($type['type_number']) ?>"
                                   placeholder="e.g., CT-001, TYPE-01"
                                   required>
                            <small class="text-muted">Enter a unique identifier/code for this type</small>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Type Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?= esc($type['name']) ?>"
                                   placeholder="e.g., Official Letter, Memo, Circular"
                                   required>
                            <small class="text-muted">Enter a descriptive name for this correspondence type</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Enter a detailed description of this correspondence type (optional)"><?= esc($type['description'] ?? '') ?></textarea>
                            <small class="text-muted">Provide additional details about when to use this type</small>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Type
                            </button>
                            <a href="<?= base_url('dakoii/correspondence-types/' . $type['id']) ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#editTypeForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('dakoii/correspondence-types/' . $type['id']) ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                // Update CSRF token
                $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                
                if (response.success) {
                    alert(response.message);
                    window.location.href = '<?= base_url('dakoii/correspondence-types/' . $type['id']) ?>';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.csrf_token_value) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                }
                
                let errorMsg = 'Failed to update correspondence type';
                if (response && response.errors) {
                    errorMsg += ':\n';
                    for (let field in response.errors) {
                        errorMsg += '- ' + response.errors[field] + '\n';
                    }
                }
                alert(errorMsg);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

