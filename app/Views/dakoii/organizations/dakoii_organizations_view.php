<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-eye me-2"></i>View Organization
                    </h2>
                    <p class="text-muted mb-0">Organization details and information</p>
                </div>
                <div>
                    <a href="<?= base_url('dakoii/organizations') ?>" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/edit') ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Organization Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Organization Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Organization Code</label>
                            <h5><span class="badge bg-primary"><?= esc($organization['org_code']) ?></span></h5>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Status</label>
                            <h5>
                                <?php if ($organization['status'] === 'active'): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Inactive
                                    </span>
                                <?php endif; ?>
                            </h5>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Organization Name</label>
                        <h4><?= esc($organization['org_name']) ?></h4>
                    </div>

                    <?php if ($organization['description']): ?>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Description</label>
                            <p><?= nl2br(esc($organization['description'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($organization['org_logo']): ?>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Organization Logo</label>
                            <div>
                                <img src="<?= base_url('uploads/organizations/' . esc($organization['org_logo'])) ?>" 
                                     alt="<?= esc($organization['org_name']) ?>" 
                                     class="img-thumbnail"
                                     style="max-width: 300px; max-height: 300px;">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Audit Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Audit Trail</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Created By</label>
                            <p class="mb-0">
                                <i class="bi bi-person me-1"></i>
                                <?= esc($organization['created_by_name'] ?? 'System') ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Created At</label>
                            <p class="mb-0">
                                <i class="bi bi-calendar me-1"></i>
                                <?= date('F d, Y h:i A', strtotime($organization['created_at'])) ?>
                            </p>
                        </div>
                        <?php if ($organization['updated_at']): ?>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Last Updated By</label>
                                <p class="mb-0">
                                    <i class="bi bi-person me-1"></i>
                                    <?= esc($organization['updated_by_name'] ?? 'System') ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Last Updated At</label>
                                <p class="mb-0">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= date('F d, Y h:i A', strtotime($organization['updated_at'])) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups') ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-diagram-3 me-2"></i>Manage Groups
                        </a>
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users') ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-people me-2"></i>Manage Users
                        </a>
                        <hr>
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/edit') ?>" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Organization
                        </a>
                        <button type="button" 
                                class="btn btn-outline-danger"
                                onclick="confirmDelete()">
                            <i class="bi bi-trash me-2"></i>Delete Organization
                        </button>
                        <hr>
                        <a href="<?= base_url('dakoii/organizations') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-2"></i>View All Organizations
                        </a>
                        <a href="<?= base_url('dakoii/organizations/new') ?>" class="btn btn-outline-success">
                            <i class="bi bi-plus-circle me-2"></i>Add New Organization
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form (hidden) -->
<form id="deleteForm" action="<?= base_url('dakoii/organizations/' . $organization['id']) ?>" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete "<?= esc($organization['org_name']) ?>"?\nThis action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?= $this->endSection() ?>

