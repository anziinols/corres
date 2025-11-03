<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-building me-2"></i>Organizations Management
                    </h2>
                    <p class="text-muted mb-0">Manage all organizations in the system</p>
                </div>
                <div>
                    <a href="<?= base_url('dakoii/organizations/new') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add New Organization
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Organizations Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Organizations List</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($organizations)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-building" style="font-size: 4rem; color: var(--dakoii-text-secondary);"></i>
                            <h4 class="mt-3 text-muted">No Organizations Found</h4>
                            <p class="text-muted">Get started by creating your first organization.</p>
                            <a href="<?= base_url('dakoii/organizations/new') ?>" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Create Organization
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Logo</th>
                                        <th style="width: 100px;">Code</th>
                                        <th>Organization Name</th>
                                        <th style="width: 150px;">Status</th>
                                        <th style="width: 180px;">Created By</th>
                                        <th style="width: 150px;">Created At</th>
                                        <th style="width: 200px;" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($organizations as $org): ?>
                                        <tr>
                                            <td>
                                                <?php if ($org['org_logo']): ?>
                                                    <img src="<?= base_url('uploads/organizations/' . esc($org['org_logo'])) ?>" 
                                                         alt="<?= esc($org['org_name']) ?>" 
                                                         class="rounded"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="bi bi-building text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?= esc($org['org_code']) ?></span>
                                            </td>
                                            <td>
                                                <strong><?= esc($org['org_name']) ?></strong>
                                                <?php if ($org['description']): ?>
                                                    <br>
                                                    <small class="text-muted"><?= esc(substr($org['description'], 0, 60)) ?><?= strlen($org['description']) > 60 ? '...' : '' ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($org['status'] === 'active'): ?>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Active
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-x-circle me-1"></i>Inactive
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= esc($org['created_by_name'] ?? 'System') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M d, Y', strtotime($org['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('dakoii/organizations/' . $org['id'] . '/groups') ?>" 
                                                       class="btn btn-sm btn-outline-success"
                                                       title="Manage Groups">
                                                        <i class="bi bi-diagram-3"></i>
                                                    </a>
                                                    <a href="<?= base_url('dakoii/organizations/' . $org['id'] . '/users') ?>" 
                                                       class="btn btn-sm btn-outline-success"
                                                       title="Manage Users">
                                                        <i class="bi bi-people"></i>
                                                    </a>
                                                    <a href="<?= base_url('dakoii/organizations/' . $org['id']) ?>" 
                                                       class="btn btn-sm btn-outline-info"
                                                       title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('dakoii/organizations/' . $org['id'] . '/edit') ?>" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDelete(<?= $org['id'] ?>, '<?= esc($org['org_name']) ?>')"
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

<!-- Delete Confirmation Form (hidden) -->
<form id="deleteForm" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id, name) {
    if (confirm('Are you sure you want to delete "' + name + '"?\nThis action cannot be undone.')) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= base_url('dakoii/organizations') ?>/' + id;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>

