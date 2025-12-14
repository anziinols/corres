<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Correspondence Types</li>
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
                                <i class="bi bi-tags me-2"></i>Correspondence Types Management
                            </h2>
                            <p class="text-muted mb-0">Manage correspondence type classifications</p>
                        </div>
                        <div>
                            <a href="<?= base_url('dakoii/correspondence-types/new') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add New Type
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Correspondence Types Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>All Correspondence Types</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($types)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-3 mb-0">No correspondence types found</p>
                            <small class="text-muted">Click "Add New Type" to create your first correspondence type</small>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="typesTable">
                                <thead>
                                    <tr>
                                        <th>Type Number</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($types as $type): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary"><?= esc($type['type_number']) ?></span>
                                            </td>
                                            <td><strong><?= esc($type['name']) ?></strong></td>
                                            <td>
                                                <?php if (!empty($type['description'])): ?>
                                                    <?= esc(substr($type['description'], 0, 100)) ?>
                                                    <?= strlen($type['description']) > 100 ? '...' : '' ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($type['created_by_name'] ?? '-') ?></td>
                                            <td><?= date('M d, Y', strtotime($type['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?= base_url('dakoii/correspondence-types/' . $type['id']) ?>" 
                                                       class="btn btn-outline-info" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('dakoii/correspondence-types/' . $type['id'] . '/edit') ?>" 
                                                       class="btn btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger delete-btn" 
                                                            data-id="<?= $type['id'] ?>"
                                                            data-name="<?= esc($type['name']) ?>"
                                                            title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
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

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    // Initialize DataTable
    <?php if (!empty($types)): ?>
    $('#typesTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        language: {
            search: "Search types:",
            lengthMenu: "Show _MENU_ types per page",
            info: "Showing _START_ to _END_ of _TOTAL_ types",
            infoEmpty: "No types available",
            infoFiltered: "(filtered from _MAX_ total types)"
        }
    });
    <?php endif; ?>

    // Delete functionality
    $('.delete-btn').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        if (confirm(`Are you sure you want to delete the correspondence type "${name}"?\n\nThis action can be undone by restoring from the database.`)) {
            $.ajax({
                url: '<?= base_url('dakoii/correspondence-types') ?>/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    <?= csrf_token() ?>: $('meta[name="<?= csrf_token() ?>"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    // Update CSRF token
                    $('meta[name="<?= csrf_token() ?>"]').attr('content', response.csrf_token_value);
                    
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.csrf_token_value) {
                        $('meta[name="<?= csrf_token() ?>"]').attr('content', response.csrf_token_value);
                    }
                    alert('Failed to delete correspondence type');
                }
            });
        }
    });
});
</script>

<?= $this->endSection() ?>

