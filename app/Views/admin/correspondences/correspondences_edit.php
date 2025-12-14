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
                            <a href="<?= base_url('admin/correspondences/' . $correspondence['id']) ?>" class="btn btn-outline-info me-2">
                                <i class="bi bi-eye me-2"></i>View
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
                    <form id="editCorrespondenceForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <!-- Basic Information -->
                        <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="correspondence_number" class="form-label">Correspondence Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="correspondence_number" name="correspondence_number" 
                                       value="<?= esc($correspondence['correspondence_number']) ?>" required readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="reference_number" class="form-label">External Reference Number</label>
                                <input type="text" class="form-control" id="reference_number" name="reference_number"
                                       value="<?= esc($correspondence['reference_number'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department" name="department" 
                                       value="<?= esc($correspondence['department'] ?? '') ?>"
                                       placeholder="e.g., HRM, FIN, IT">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="subject" class="form-label">Subject/Particulars <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="subject" name="subject" rows="2" required><?= esc($correspondence['subject']) ?></textarea>
                            </div>
                        </div>

                        <!-- Direction & Type -->
                        <h5 class="mb-3 mt-4"><i class="bi bi-arrow-left-right me-2"></i>Direction & Type</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="correspondence_direction" class="form-label">Direction <span class="text-danger">*</span></label>
                                <select class="form-select" id="correspondence_direction" name="correspondence_direction" required>
                                    <option value="INWARD" <?= $correspondence['correspondence_direction'] === 'INWARD' ? 'selected' : '' ?>>Inward</option>
                                    <option value="OUTWARD" <?= $correspondence['correspondence_direction'] === 'OUTWARD' ? 'selected' : '' ?>>Outward</option>
                                    <option value="INTERNAL" <?= $correspondence['correspondence_direction'] === 'INTERNAL' ? 'selected' : '' ?>>Internal</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="correspondence_type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="correspondence_type" name="correspondence_type" required>
                                    <option value="">-- Select Type --</option>
                                    <?php if (!empty($correspondence_types)): ?>
                                        <?php foreach ($correspondence_types as $type): ?>
                                            <option value="<?= esc($type['type_number']) ?>"
                                                    <?= $correspondence['correspondence_type'] === $type['type_number'] ? 'selected' : '' ?>>
                                                <?= esc($type['type_number']) ?> - <?= esc($type['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No types available</option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">
                                    <a href="<?= base_url('dakoii/correspondence-types') ?>" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-plus-circle"></i> Manage Types
                                    </a>
                                </small>
                            </div>
                            <div class="col-md-4">
                                <label for="organization_id" class="form-label">Organization</label>
                                <select class="form-select" id="organization_id" name="organization_id">
                                    <option value="">Select Organization</option>
                                    <?php foreach ($organizations as $org): ?>
                                        <option value="<?= $org['id'] ?>" <?= $correspondence['organization_id'] == $org['id'] ? 'selected' : '' ?>>
                                            <?= esc($org['org_name']) ?> (<?= esc($org['org_code']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Dates -->
                        <h5 class="mb-3 mt-4"><i class="bi bi-calendar me-2"></i>Dates</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="date_received" class="form-label">Date Received <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_received" name="date_received" 
                                       value="<?= esc($correspondence['date_received']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="original_date" class="form-label">Original Date (on document)</label>
                                <input type="date" class="form-control" id="original_date" name="original_date"
                                       value="<?= esc($correspondence['original_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="date_sent" class="form-label">Date Sent (for outward)</label>
                                <input type="date" class="form-control" id="date_sent" name="date_sent"
                                       value="<?= esc($correspondence['date_sent'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Sender Information -->
                        <div id="senderSection" style="display: <?= $correspondence['correspondence_direction'] === 'OUTWARD' ? 'none' : 'block' ?>;">
                            <h5 class="mb-3 mt-4"><i class="bi bi-person-fill me-2"></i>Sender Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sender_name" class="form-label">Sender Name</label>
                                    <input type="text" class="form-control" id="sender_name" name="sender_name"
                                           value="<?= esc($correspondence['sender_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="sender_organization" class="form-label">Sender Organization</label>
                                    <input type="text" class="form-control" id="sender_organization" name="sender_organization"
                                           value="<?= esc($correspondence['sender_organization'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sender_address" class="form-label">Sender Address</label>
                                    <textarea class="form-control" id="sender_address" name="sender_address" rows="2"><?= esc($correspondence['sender_address'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="sender_contact" class="form-label">Sender Contact</label>
                                    <input type="text" class="form-control" id="sender_contact" name="sender_contact" 
                                           value="<?= esc($correspondence['sender_contact'] ?? '') ?>"
                                           placeholder="Email or Phone">
                                </div>
                            </div>
                        </div>

                        <!-- Recipient Information -->
                        <div id="recipientSection" style="display: <?= $correspondence['correspondence_direction'] === 'OUTWARD' ? 'block' : 'none' ?>;">
                            <h5 class="mb-3 mt-4"><i class="bi bi-person-check-fill me-2"></i>Recipient Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="recipient_name" class="form-label">Recipient Name</label>
                                    <input type="text" class="form-control" id="recipient_name" name="recipient_name"
                                           value="<?= esc($correspondence['recipient_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="recipient_organization" class="form-label">Recipient Organization</label>
                                    <input type="text" class="form-control" id="recipient_organization" name="recipient_organization"
                                           value="<?= esc($correspondence['recipient_organization'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="recipient_address" class="form-label">Recipient Address</label>
                                    <textarea class="form-control" id="recipient_address" name="recipient_address" rows="2"><?= esc($correspondence['recipient_address'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="dispatch_method" class="form-label">Dispatch Method</label>
                                    <select class="form-select" id="dispatch_method" name="dispatch_method">
                                        <option value="">Select Method</option>
                                        <option value="Email" <?= ($correspondence['dispatch_method'] ?? '') === 'Email' ? 'selected' : '' ?>>Email</option>
                                        <option value="Post" <?= ($correspondence['dispatch_method'] ?? '') === 'Post' ? 'selected' : '' ?>>Post</option>
                                        <option value="Courier" <?= ($correspondence['dispatch_method'] ?? '') === 'Courier' ? 'selected' : '' ?>>Courier</option>
                                        <option value="Hand Delivery" <?= ($correspondence['dispatch_method'] ?? '') === 'Hand Delivery' ? 'selected' : '' ?>>Hand Delivery</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Priority & Status -->
                        <h5 class="mb-3 mt-4"><i class="bi bi-flag-fill me-2"></i>Priority & Status</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="LOW" <?= $correspondence['priority'] === 'LOW' ? 'selected' : '' ?>>Low</option>
                                    <option value="NORMAL" <?= $correspondence['priority'] === 'NORMAL' ? 'selected' : '' ?>>Normal</option>
                                    <option value="HIGH" <?= $correspondence['priority'] === 'HIGH' ? 'selected' : '' ?>>High</option>
                                    <option value="URGENT" <?= $correspondence['priority'] === 'URGENT' ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
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

                        <!-- Filing & Archive -->
                        <h5 class="mb-3 mt-4"><i class="bi bi-folder-fill me-2"></i>Filing & Archive</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="filing_reference" class="form-label">Filing Reference</label>
                                <input type="text" class="form-control" id="filing_reference" name="filing_reference" 
                                       value="<?= esc($correspondence['filing_reference'] ?? '') ?>"
                                       placeholder="e.g., P/F-FKupiaw/F-HRM">
                            </div>
                            <div class="col-md-4">
                                <label for="archive_location" class="form-label">Archive Location</label>
                                <input type="text" class="form-control" id="archive_location" name="archive_location"
                                       value="<?= esc($correspondence['archive_location'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="archive_date" class="form-label">Archive Date</label>
                                <input type="date" class="form-control" id="archive_date" name="archive_date"
                                       value="<?= esc($correspondence['archive_date'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3"><?= esc($correspondence['remarks'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Correspondence
                                </button>
                                <a href="<?= base_url('admin/correspondences/' . $correspondence['id']) ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>
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
    // Toggle sender/recipient sections based on direction
    $('#correspondence_direction').change(function() {
        if ($(this).val() === 'OUTWARD') {
            $('#senderSection').hide();
            $('#recipientSection').show();
        } else {
            $('#senderSection').show();
            $('#recipientSection').hide();
        }
    });

    // Form submission
    $('#editCorrespondenceForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('admin/correspondences/' . $correspondence['id']) ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                // Update CSRF token
                $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                
                if (response.success) {
                    alert(response.message);
                    window.location.href = '<?= base_url('admin/correspondences/' . $correspondence['id']) ?>';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.csrf_token_value) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                }
                
                var errorMsg = 'Failed to update correspondence';
                if (response && response.errors) {
                    errorMsg += ':\n';
                    for (var field in response.errors) {
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

