<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="mb-3 mb-md-0">
                            <h2 class="mb-2 fw-bold" style="color: var(--admin-primary);">
                                <i class="bi bi-speedometer2 me-2"></i>Welcome back, <?= esc($user['name']) ?>!
                            </h2>
                            <?php if ($user['position']): ?>
                                <p class="mb-1">
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-briefcase me-1"></i><?= esc($user['position']) ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                            <p class="text-muted mb-1">
                                <i class="bi bi-envelope me-1"></i>Logged in as: <strong><?= esc($user['email']) ?></strong>
                            </p>
                            <p class="mb-1">
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge bg-danger me-1">
                                        <i class="bi bi-shield-fill-check me-1"></i>Administrator
                                    </span>
                                <?php endif; ?>
                                <?php if ($user['is_supervisor']): ?>
                                    <span class="badge bg-success me-1">
                                        <i class="bi bi-person-check me-1"></i>Supervisor
                                    </span>
                                <?php endif; ?>
                                <?php if ($user['is_front_desk']): ?>
                                    <span class="badge bg-info me-1">
                                        <i class="bi bi-door-open me-1"></i>Front Desk
                                    </span>
                                <?php endif; ?>
                            </p>
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-calendar-event me-1"></i><?= date('l, F d, Y h:i A') ?>
                            </p>
                        </div>
                        <div>
                            <form action="<?= base_url('admin/logout') ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-file-earmark-text text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small text-uppercase">Total Documents</h6>
                            <h2 class="mb-0 fw-bold" style="color: var(--admin-primary);">0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card h-100 stat-card" style="border-left-color: var(--admin-warning);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #FFC107, #FF9800); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-clock-history text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small text-uppercase">Pending</h6>
                            <h2 class="mb-0 fw-bold" style="color: var(--admin-warning);">0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card h-100 stat-card" style="border-left-color: var(--admin-success);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #28A745, #20C997); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check-circle text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small text-uppercase">Completed</h6>
                            <h2 class="mb-0 fw-bold" style="color: var(--admin-success);">0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card h-100 stat-card" style="border-left-color: var(--admin-info);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #17A2B8, #138496); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-people text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small text-uppercase">Active Users</h6>
                            <h2 class="mb-0 fw-bold" style="color: var(--admin-info);">1</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and System Info -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="#" class="btn btn-outline-primary btn-lg text-start d-flex align-items-center">
                            <i class="bi bi-plus-circle me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <div class="fw-semibold">Add New Document</div>
                                <small class="text-muted">Create and register a new correspondence</small>
                            </div>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-lg text-start d-flex align-items-center">
                            <i class="bi bi-search me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <div class="fw-semibold">Search Documents</div>
                                <small class="text-muted">Find and manage existing records</small>
                            </div>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-lg text-start d-flex align-items-center">
                            <i class="bi bi-graph-up me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <div class="fw-semibold">View Reports</div>
                                <small class="text-muted">Generate and analyze reports</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>System Information</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--admin-primary), var(--admin-accent)); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-calendar-check text-white" style="font-size: 1.25rem;"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="fw-semibold">Last Login</div>
                                <small class="text-muted"><?= date('F d, Y h:i A') ?></small>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #28A745, #20C997); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-shield-check text-white" style="font-size: 1.25rem;"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="fw-semibold">Account Status</div>
                                <small class="text-success fw-semibold">Active & Verified</small>
                            </div>
                        </div>
                        <?php if ($user['signature_path'] || $user['stamp_path']): ?>
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #17A2B8, #138496); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-file-earmark-check text-white" style="font-size: 1.25rem;"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="fw-semibold">Documents</div>
                                <small class="text-muted">
                                    <?php if ($user['signature_path']): ?>
                                        <i class="bi bi-check-circle text-success me-1"></i>Signature
                                    <?php endif; ?>
                                    <?php if ($user['stamp_path']): ?>
                                        <i class="bi bi-check-circle text-success me-1"></i>Stamp
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="list-group-item d-flex align-items-center border-0 px-0">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6C757D, #495057); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-gear text-white" style="font-size: 1.25rem;"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="fw-semibold">System Version</div>
                                <small class="text-muted">v1.0.0</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: var(--admin-border);"></i>
                        <p class="text-muted mt-3 mb-0">No recent activity to display</p>
                        <small class="text-muted">Activity will appear here once you start using the system</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    
    .list-group-item {
        padding: 1rem 0;
        transition: background-color 0.3s;
    }
    
    .list-group-item:hover {
        background-color: var(--admin-light-bg);
    }
</style>
<?= $this->endSection() ?>

