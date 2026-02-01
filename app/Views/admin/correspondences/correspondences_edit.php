<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/correspondences') ?>">Correspondences</a></li>
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
                                <i class="bi bi-pencil-square me-2"></i>Edit Correspondence
                            </h2>
                            <p class="text-muted mb-0">Update correspondence details</p>
                        </div>
                        <div>
                            <a href="<?= base_url($correspondence['file_path']) ?>" class="btn btn-outline-info me-2" target="_blank">
                                <i class="bi bi-eye me-2"></i>View File
                            </a>
                            <a href="<?= base_url('admin/correspondences') ?>" class="btn btn-outline-secondary">
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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="editCorrespondenceForm" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <!-- 1. Header Details -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="correspondence_number" class="form-label text-uppercase text-secondary fw-bold small">Correspondence Number</label>
                                <input type="text" class="form-control bg-light" id="correspondence_number" name="correspondence_number" 
                                       value="<?= esc($correspondence['correspondence_number']) ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="date_received" class="form-label text-uppercase text-secondary fw-bold small">Date Received</label>
                                <input type="date" class="form-control" id="date_received" name="date_received" 
                                       value="<?= esc($correspondence['date_received']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="correspondence_direction" class="form-label text-uppercase text-secondary fw-bold small">Direction</label>
                                <select class="form-select" id="correspondence_direction" name="correspondence_direction" required>
                                    <option value="INWARD" <?= $correspondence['correspondence_direction'] === 'INWARD' ? 'selected' : '' ?>>Inward</option>
                                    <option value="OUTWARD" <?= $correspondence['correspondence_direction'] === 'OUTWARD' ? 'selected' : '' ?>>Outward</option>
                                    <option value="INTERNAL" <?= $correspondence['correspondence_direction'] === 'INTERNAL' ? 'selected' : '' ?>>Internal</option>
                                </select>
                            </div>
                        </div>

                        <!-- 2. Main Content -->
                        <h5 class="text-primary border-bottom pb-2 mb-3">Main Content</h5>
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">Subject</label>
                            <input type="text" class="form-control form-control-lg" id="subject" name="subject" 
                                   value="<?= esc($correspondence['subject']) ?>" placeholder="Headline / Title of the file" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="remarks" class="form-label fw-bold">Brief Description</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Short summary of contents..."><?= esc($correspondence['remarks'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="correspondence_type" class="form-label fw-bold">Document Type</label>
                                <select class="form-select" id="correspondence_type" name="correspondence_type" required>
                                    <option value="">Select Type</option>
                                    <?php foreach ($correspondence_types as $type): ?>
                                        <option value="<?= esc($type['type_number']) ?>" <?= $correspondence['correspondence_type'] === $type['type_number'] ? 'selected' : '' ?>>
                                            <?= esc($type['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- 3. Organization & Group -->
                        <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Organization & Group</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="organization_id" class="form-label fw-bold">Organization</label>
                                <select class="form-select" id="organization_id" name="organization_id">
                                    <option value="">Select Organization</option>
                                    <?php foreach ($organizations as $org): ?>
                                        <option value="<?= $org['id'] ?>" <?= ($correspondence['organization_id'] ?? '') == $org['id'] ? 'selected' : '' ?>><?= esc($org['org_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="group_id" class="form-label fw-bold">Group / Department</label>
                                <select class="form-select" id="group_id" name="group_id">
                                    <option value="">Select Group</option>
                                    <?php foreach ($groups as $group): ?>
                                        <option value="<?= $group['id'] ?>" data-org="<?= $group['organization_id'] ?>" <?= ($correspondence['group_id'] ?? '') == $group['id'] ? 'selected' : '' ?>><?= esc($group['group_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- 4. Sender & Status -->
                        <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Sender & Status</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="sender_name" class="form-label fw-bold">Sender / Organization</label>
                                <input type="text" class="form-control" id="sender_name" name="sender_name"
                                       value="<?= esc($correspondence['sender_name'] ?? '') ?>" placeholder="e.g. John Smith - Dakoii Systems">
                            </div>
                            <div class="col-md-3">
                                <label for="priority" class="form-label fw-bold">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="NORMAL" <?= $correspondence['priority'] === 'NORMAL' ? 'selected' : '' ?>>Normal</option>
                                    <option value="URGENT" <?= $correspondence['priority'] === 'URGENT' ? 'selected' : '' ?>>Urgent</option>
                                    <option value="CONFIDENTIAL" <?= $correspondence['priority'] === 'CONFIDENTIAL' ? 'selected' : '' ?>>Confidential</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label fw-bold">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="REGISTERED" <?= $correspondence['status'] === 'REGISTERED' ? 'selected' : '' ?>>Registered</option>
                                    <option value="REFERRED" <?= $correspondence['status'] === 'REFERRED' ? 'selected' : '' ?>>Referred</option>
                                    <option value="IN_PROCESS" <?= $correspondence['status'] === 'IN_PROCESS' ? 'selected' : '' ?>>In Process</option>
                                    <option value="ACTIONED" <?= $correspondence['status'] === 'ACTIONED' ? 'selected' : '' ?>>Actioned</option>
                                    <option value="COMPLETED" <?= $correspondence['status'] === 'COMPLETED' ? 'selected' : '' ?>>Completed</option>
                                    <option value="ARCHIVED" <?= $correspondence['status'] === 'ARCHIVED' ? 'selected' : '' ?>>Archived</option>
                                </select>
                            </div>
                        </div>

                        <!-- 5. Context & Relationships -->
                        <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Context & Relationships</h5>
                        <div class="mb-3">
                            <label for="linked_correspondence_ids" class="form-label fw-bold">Linked Correspondence</label>
                            <select class="form-select" id="linked_correspondence_ids" name="linked_correspondence_ids[]" multiple="multiple">
                                <?php foreach ($existing_correspondences as $existing): ?>
                                    <option value="<?= $existing['id'] ?>" <?= in_array($existing['id'], $linked_correspondence_ids) ? 'selected' : '' ?>>
                                        <?= esc($existing['correspondence_number']) ?> - <?= esc($existing['subject']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Search for existing files to tag relationships.</small>
                        </div>

                        <!-- Hidden Archive Fields -->
                        <input type="hidden" id="filing_reference" name="filing_reference" value="<?= esc($correspondence['filing_reference'] ?? '') ?>">
                        <input type="hidden" id="archive_location" name="archive_location" value="<?= esc($correspondence['archive_location'] ?? '') ?>">
                        <input type="hidden" id="archive_date" name="archive_date" value="<?= esc($correspondence['archive_date'] ?? '') ?>">

                        <!-- 6. Attachment -->
                        <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Attachment</h5>
                        <div class="mb-4 p-4 border rounded bg-light">
                            <?php if (!empty($correspondence['file_path'])): ?>
                                <div class="mb-3">
                                    <p class="mb-2 fw-bold text-success"><i class="bi bi-file-earmark-check me-2"></i>Current File Attached</p>
                                    <a href="<?= base_url($correspondence['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>View Current File
                                    </a>
                                </div>
                                <hr>
                            <?php endif; ?>
                            
                            <label class="form-label fw-bold">Replace File</label>
                            <div id="dropZone" class="p-5 text-center border rounded bg-white" style="border: 2px dashed #0d6efd; cursor: pointer; transition: all 0.2s;">
                                <div id="dropZoneContent">
                                    <i class="bi bi-cloud-arrow-up display-4 text-primary mb-3"></i>
                                    <h5 class="fw-bold">Drag & Drop to Replace</h5>
                                    <p class="text-muted mb-0">or click to browse</p>
                                    <small class="text-muted">(Supports .pdf, .jpg, .png)</small>
                                    <div id="fileNameDisplay" class="mt-2 fw-bold text-success d-none"></div>
                                </div>
                            </div>
                            <input type="file" id="correspondence_file" name="correspondence_file" class="d-none" accept=".pdf,.jpg,.jpeg,.png">
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <a href="<?= base_url('admin/correspondences/' . $correspondence['id']) ?>" class="btn btn-link text-secondary text-decoration-none me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-check-circle me-2"></i>Update Correspondence
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="archiveModalLabel">
                    <i class="bi bi-archive me-2"></i>Filing & Archive Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Please provide archive details before setting status to Archived.</p>
                <div class="mb-3">
                    <label for="modal_filing_reference" class="form-label fw-bold">Filing Reference</label>
                    <input type="text" class="form-control" id="modal_filing_reference" placeholder="e.g. P/F-HRM/2026"
                           value="<?= esc($correspondence['filing_reference'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="modal_archive_location" class="form-label fw-bold">Archive Location</label>
                    <input type="text" class="form-control" id="modal_archive_location" placeholder="Physical archive location"
                           value="<?= esc($correspondence['archive_location'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="modal_archive_date" class="form-label fw-bold">Archive Date</label>
                    <input type="date" class="form-control" id="modal_archive_date"
                           value="<?= esc($correspondence['archive_date'] ?? date('Y-m-d')) ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="cancelArchive" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmArchive">
                    <i class="bi bi-check-circle me-2"></i>Confirm Archive
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Initialize Select2
    $('#linked_correspondence_ids').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search for files...',
        allowClear: true
    });

    // Archive Modal Logic
    var previousStatus = $('#status').val();
    var archiveModal = new bootstrap.Modal(document.getElementById('archiveModal'));

    $('#status').change(function() {
        if ($(this).val() === 'ARCHIVED') {
            // Set default archive date to today if empty
            if (!$('#modal_archive_date').val()) {
                $('#modal_archive_date').val('<?= date('Y-m-d') ?>');
            }
            archiveModal.show();
        }
    });

    // Cancel archive - revert status
    $('#cancelArchive').click(function() {
        $('#status').val(previousStatus);
    });

    // Also handle modal hidden event (clicking X or outside)
    document.getElementById('archiveModal').addEventListener('hidden.bs.modal', function () {
        if ($('#status').val() === 'ARCHIVED' && !$('#filing_reference').val() && !$('#archive_location').val()) {
            $('#status').val(previousStatus);
        }
    });

    // Confirm archive - copy values to hidden fields
    $('#confirmArchive').click(function() {
        $('#filing_reference').val($('#modal_filing_reference').val());
        $('#archive_location').val($('#modal_archive_location').val());
        $('#archive_date').val($('#modal_archive_date').val());
        previousStatus = 'ARCHIVED';
        archiveModal.hide();
        toastr.info('Archive details saved. Don\'t forget to update the correspondence.');
    });

    // Drag & Drop Logic
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('correspondence_file');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    
    if (dropZone) {
        // Open file selector on click
        dropZone.addEventListener('click', () => fileInput.click());

        // Handle file selection from browser dialog
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                showFileName(e.target.files[0].name);
            }
        });

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone when dragging over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.style.borderColor = '#0b5ed7';
                dropZone.classList.add('bg-light');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.style.borderColor = '#0d6efd';
                dropZone.classList.remove('bg-light');
            }, false);
        });

        // Handle drop event
        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files; // Assign dropped files to input
                showFileName(files[0].name);
            }
        });

        function showFileName(name) {
            fileNameDisplay.innerText = "Selected to Replace: " + name;
            fileNameDisplay.classList.remove('d-none');
        }
    }

    // Form submission
    $('#editCorrespondenceForm').submit(function(e) {
        e.preventDefault();
        
        // Use FormData to handle file uploads
        var formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/correspondences/' . $correspondence['id']) ?>',
            type: 'POST',
            data: formData,
            processData: false, // Required for FormData
            contentType: false, // Required for FormData
            dataType: 'json',
            success: function(response) {
                // Update CSRF token
                if (response.csrf_token_value) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                }
                
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.href = '<?= base_url('admin/correspondences/' . $correspondence['id']) ?>';
                    }, 1000);
                } else {
                    toastr.error('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.csrf_token_value) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                }
                
                var errorMsg = 'Failed to update correspondence';
                if (response && response.errors) {
                    errorMsg += ':<br>';
                    for (var field in response.errors) {
                        errorMsg += '- ' + response.errors[field] + '<br>';
                    }
                }
                toastr.error(errorMsg);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

