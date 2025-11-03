<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/organizations') ?>">Organizations</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/organizations/' . $organization['id']) ?>"><?= esc($organization['org_name']) ?></a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users') ?>">Users</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-eye me-2"></i>View User
                    </h2>
                    <p class="text-muted mb-0">User details and information</p>
                </div>
                <div>
                    <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users') ?>" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-2"></i>Back to Users
                    </a>
                    <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users/' . $user['id'] . '/edit') ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- User Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Full Name</label>
                            <h4><?= esc($user['name']) ?></h4>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Status</label>
                            <h5>
                                <?php if ($user['status'] === 'active'): ?>
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
                        <label class="text-muted mb-1">Email Address</label>
                        <p>
                            <i class="bi bi-envelope me-1"></i><?= esc($user['email']) ?>
                        </p>
                    </div>

                    <?php if ($user['position']): ?>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Position / Job Title</label>
                            <p>
                                <i class="bi bi-briefcase me-1"></i><?= esc($user['position']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Organization</label>
                        <p>
                            <a href="<?= base_url('dakoii/organizations/' . $user['organization_id']) ?>" class="text-decoration-none">
                                <i class="bi bi-building me-1"></i><?= esc($user['org_name']) ?>
                                <span class="badge bg-primary ms-2"><?= esc($user['org_code']) ?></span>
                            </a>
                        </p>
                    </div>

                    <?php if ($user['group_name']): ?>
                        <div class="mb-3">
                            <label class="text-muted mb-1">Group</label>
                            <p>
                                <span class="badge bg-info">
                                    <?= esc($user['group_code']) ?>
                                </span>
                                <span class="ms-2"><?= esc($user['group_name']) ?></span>
                            </p>
                        </div>
                    <?php endif; ?>

                    <hr class="my-3">

                    <!-- User Roles -->
                    <div class="mb-3">
                        <label class="text-muted mb-2">User Roles & Permissions</label>
                        <div>
                            <?php if ($user['is_admin']): ?>
                                <span class="badge bg-danger me-2 mb-2">
                                    <i class="bi bi-shield-fill-check me-1"></i>Administrator
                                </span>
                            <?php endif; ?>
                            <?php if ($user['is_supervisor']): ?>
                                <span class="badge bg-success me-2 mb-2">
                                    <i class="bi bi-person-check me-1"></i>Supervisor
                                </span>
                            <?php endif; ?>
                            <?php if ($user['is_front_desk']): ?>
                                <span class="badge bg-info me-2 mb-2">
                                    <i class="bi bi-door-open me-1"></i>Front Desk
                                </span>
                            <?php endif; ?>
                            <?php if (!$user['is_admin'] && !$user['is_supervisor'] && !$user['is_front_desk']): ?>
                                <span class="text-muted"><i>No special roles assigned</i></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Files -->
            <?php if ($user['signature_filepath'] || $user['stamp_filepath']): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-image me-2"></i>Document Files</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if ($user['signature_filepath']): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-2">Signature</label>
                                    <div class="border rounded p-3 text-center bg-light">
                                        <img src="<?= base_url($user['signature_filepath']) ?>" 
                                             alt="Signature" 
                                             class="img-fluid" 
                                             style="max-height: 120px;">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($user['stamp_filepath']): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-2">Official Stamp</label>
                                    <div class="border rounded p-3 text-center bg-light">
                                        <img src="<?= base_url($user['stamp_filepath']) ?>" 
                                             alt="Stamp" 
                                             class="img-fluid" 
                                             style="max-height: 120px;">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

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
                                <?= esc($user['created_by_name'] ?? 'System') ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Created At</label>
                            <p class="mb-0">
                                <i class="bi bi-calendar me-1"></i>
                                <?= date('F d, Y h:i A', strtotime($user['created_at'])) ?>
                            </p>
                        </div>
                        <?php if ($user['updated_at']): ?>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Last Updated By</label>
                                <p class="mb-0">
                                    <i class="bi bi-person me-1"></i>
                                    <?= esc($user['updated_by_name'] ?? 'System') ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Last Updated At</label>
                                <p class="mb-0">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= date('F d, Y h:i A', strtotime($user['updated_at'])) ?>
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
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users/' . $user['id'] . '/edit') ?>" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit User
                        </a>
                        <button type="button" 
                                class="btn btn-outline-danger"
                                onclick="confirmDelete()">
                            <i class="bi bi-trash me-2"></i>Delete User
                        </button>
                        <hr>
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-2"></i>View All Users
                        </a>
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users/new') ?>" class="btn btn-outline-success">
                            <i class="bi bi-plus-circle me-2"></i>Add New User
                        </a>
                        <hr>
                        <a href="<?= base_url('dakoii/organizations/' . $organization['id']) ?>" class="btn btn-outline-info">
                            <i class="bi bi-building me-2"></i>View Organization
                        </a>
                        <?php if ($user['group_id']): ?>
                            <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/' . $user['group_id']) ?>" class="btn btn-outline-info">
                                <i class="bi bi-diagram-3 me-2"></i>View Group
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form (hidden) -->
<form id="deleteForm" action="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users/' . $user['id']) ?>" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete "<?= esc($user['name']) ?>"?\nThis action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?= $this->endSection() ?>

