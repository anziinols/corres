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
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-eye me-2"></i>View Group
                    </h2>
                    <p class="text-muted mb-0">Group details and information</p>
                </div>
                <div>
                    <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups') ?>" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-2"></i>Back to Groups
                    </a>
                    <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/' . $group['id'] . '/edit') ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Group Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Group Code</label>
                            <h5><span class="badge bg-info"><?= esc($group['group_code']) ?></span></h5>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Status</label>
                            <h5>
                                <?php if ($group['status'] === 'active'): ?>
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
                        <label class="text-muted mb-1">Group Name</label>
                        <h4><?= esc($group['group_name']) ?></h4>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Organization</label>
                        <p>
                            <a href="<?= base_url('dakoii/organizations/' . $group['organization_id']) ?>" class="text-decoration-none">
                                <i class="bi bi-building me-1"></i><?= esc($group['org_name']) ?>
                                <span class="badge bg-primary ms-2"><?= esc($group['org_code']) ?></span>
                            </a>
                        </p>
                    </div>

                    <?php if ($group['parent_name']): ?>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Parent Group</label>
                            <p>
                                <i class="bi bi-arrow-return-right me-1"></i><?= esc($group['parent_name']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if ($group['description']): ?>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Description</label>
                            <p><?= nl2br(esc($group['description'])) ?></p>
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
                                <?= esc($group['created_by_name'] ?? 'System') ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Created At</label>
                            <p class="mb-0">
                                <i class="bi bi-calendar me-1"></i>
                                <?= date('F d, Y h:i A', strtotime($group['created_at'])) ?>
                            </p>
                        </div>
                        <?php if ($group['updated_at']): ?>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Last Updated By</label>
                                <p class="mb-0">
                                    <i class="bi bi-person me-1"></i>
                                    <?= esc($group['updated_by_name'] ?? 'System') ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Last Updated At</label>
                                <p class="mb-0">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= date('F d, Y h:i A', strtotime($group['updated_at'])) ?>
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
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/' . $group['id'] . '/edit') ?>" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Group
                        </a>
                        <button type="button" 
                                class="btn btn-outline-danger"
                                onclick="confirmDelete()">
                            <i class="bi bi-trash me-2"></i>Delete Group
                        </button>
                        <hr>
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-2"></i>View All Groups
                        </a>
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/new') ?>" class="btn btn-outline-success">
                            <i class="bi bi-plus-circle me-2"></i>Add New Group
                        </a>
                        <hr>
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id']) ?>" class="btn btn-outline-info">
                            <i class="bi bi-building me-2"></i>View Organization
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form (hidden) -->
<form id="deleteForm" action="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/' . $group['id']) ?>" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete "<?= esc($group['group_name']) ?>"?\nThis action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?= $this->endSection() ?>

