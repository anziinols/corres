<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="bi bi-speedometer2 me-2"></i>Welcome back, <?= esc($user['name']) ?>!
                            </h2>
                            <p class="text-muted mb-0">
                                <i class="bi bi-person-badge me-1"></i>Logged in as: <strong><?= esc($user['username']) ?></strong>
                            </p>
                        </div>
                        <div>
                            <form action="<?= base_url('dakoii/logout') ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger">
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
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-file-earmark-text" style="font-size: 2.5rem; color: var(--dakoii-accent);"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Documents</h6>
                            <h3 class="mb-0">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock-history" style="font-size: 2.5rem; color: var(--dakoii-warning);"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h3 class="mb-0">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle" style="font-size: 2.5rem; color: var(--dakoii-success);"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Completed</h6>
                            <h3 class="mb-0">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-people" style="font-size: 2.5rem; color: var(--dakoii-accent);"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Users</h6>
                            <h3 class="mb-0">1</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start" disabled>
                            <i class="bi bi-plus-circle me-2"></i>Add New Document
                        </button>
                        <button class="btn btn-outline-primary text-start" disabled>
                            <i class="bi bi-search me-2"></i>Search Documents
                        </button>
                        <button class="btn btn-outline-primary text-start" disabled>
                            <i class="bi bi-graph-up me-2"></i>View Reports
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>System Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-calendar-check me-2 text-muted"></i>
                            <strong>Last Login:</strong> <?= date('F d, Y h:i A') ?>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-shield-check me-2 text-success"></i>
                            <strong>Status:</strong> <span class="text-success">Active</span>
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-gear me-2 text-muted"></i>
                            <strong>Version:</strong> 1.0.0
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

