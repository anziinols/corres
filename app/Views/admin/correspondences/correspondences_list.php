<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Correspondences</li>
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
                                <i class="bi bi-envelope-paper me-2"></i>Correspondence Management
                            </h2>
                            <p class="text-muted mb-0">
                                Register and manage all correspondence (Inward, Outward, Internal)
                            </p>
                        </div>
                        <div>
                            <a href="<?= base_url('admin/correspondences/new') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Register New Correspondence
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Correspondences Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Correspondences List</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($correspondences)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-envelope-paper" style="font-size: 4rem; color: #6c757d;"></i>
                            <h4 class="mt-3 text-muted">No Correspondences Found</h4>
                            <p class="text-muted">Get started by registering your first correspondence.</p>
                            <a href="<?= base_url('admin/correspondences/new') ?>" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Register Correspondence
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="correspondencesTable">
                                <thead>
                                    <tr>
                                        <th>Corr. Number</th>
                                        <th>Subject</th>
                                        <th>Direction</th>
                                        <th>Type</th>
                                        <th>Date Received</th>
                                        <th>Organization</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($correspondences as $corr): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($corr['correspondence_number']) ?></strong>
                                                <?php if (!empty($corr['reference_number'])): ?>
                                                    <br><small class="text-muted">Ref: <?= esc($corr['reference_number']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?= esc($corr['subject']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $directionBadge = [
                                                    'INWARD' => 'bg-info',
                                                    'OUTWARD' => 'bg-warning',
                                                    'INTERNAL' => 'bg-secondary'
                                                ];
                                                ?>
                                                <span class="badge <?= $directionBadge[$corr['correspondence_direction']] ?? 'bg-secondary' ?>">
                                                    <?= esc($corr['correspondence_direction']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= esc($corr['correspondence_type']) ?></small>
                                            </td>
                                            <td>
                                                <small><?= date('d M Y', strtotime($corr['date_received'])) ?></small>
                                            </td>
                                            <td>
                                                <?php if (!empty($corr['org_name'])): ?>
                                                    <small><?= esc($corr['org_name']) ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">-</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusBadge = [
                                                    'REGISTERED' => 'bg-primary',
                                                    'REFERRED' => 'bg-info',
                                                    'IN_PROCESS' => 'bg-warning',
                                                    'ACTIONED' => 'bg-success',
                                                    'COMPLETED' => 'bg-success',
                                                    'ARCHIVED' => 'bg-secondary'
                                                ];
                                                ?>
                                                <span class="badge <?= $statusBadge[$corr['status']] ?? 'bg-secondary' ?>">
                                                    <?= esc($corr['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $priorityBadge = [
                                                    'LOW' => 'bg-secondary',
                                                    'NORMAL' => 'bg-primary',
                                                    'HIGH' => 'bg-warning',
                                                    'URGENT' => 'bg-danger'
                                                ];
                                                ?>
                                                <span class="badge <?= $priorityBadge[$corr['priority']] ?? 'bg-primary' ?>">
                                                    <?= esc($corr['priority']) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= base_url('admin/correspondences/' . $corr['id']) ?>" 
                                                   class="btn btn-sm btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/correspondences/' . $corr['id'] . '/edit') ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="deleteCorrespondence(<?= $corr['id'] ?>)" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#correspondencesTable').DataTable({
        order: [[4, 'desc']], // Sort by date received
        pageLength: 25,
        language: {
            search: "Search correspondences:",
            lengthMenu: "Show _MENU_ correspondences per page",
            info: "Showing _START_ to _END_ of _TOTAL_ correspondences",
            infoEmpty: "No correspondences available",
            infoFiltered: "(filtered from _MAX_ total correspondences)"
        }
    });
});

function deleteCorrespondence(id) {
    if (confirm('Are you sure you want to delete this correspondence? This action cannot be undone.')) {
        $.ajax({
            url: '<?= base_url('admin/correspondences') ?>/' + id,
            type: 'DELETE',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                // Update CSRF token
                $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.csrf_token_value) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token_value);
                }
                alert('Error: ' + (response ? response.message : 'Failed to delete correspondence'));
            }
        });
    }
}
</script>

<?= $this->endSection() ?>

