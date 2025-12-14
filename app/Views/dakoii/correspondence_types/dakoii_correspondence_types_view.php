<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/correspondence-types') ?>">Correspondence Types</a></li>
            <li class="breadcrumb-item active">View</li>
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
                                <i class="bi bi-eye me-2"></i>Correspondence Type Details
                            </h2>
                            <p class="text-muted mb-0">
                                <span class="badge bg-primary"><?= esc($type['type_number']) ?></span>
                            </p>
                        </div>
                        <div>
                            <a href="<?= base_url('dakoii/correspondence-types/' . $type['id'] . '/edit') ?>" class="btn btn-warning me-2">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                            <a href="<?= base_url('dakoii/correspondence-types') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Type Details -->
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted small">Type Number</label>
                            <p class="fw-bold">
                                <span class="badge bg-primary fs-6"><?= esc($type['type_number']) ?></span>
                            </p>
                        </div>
                        <div class="col-md-8">
                            <label class="text-muted small">Type Name</label>
                            <p class="fw-bold"><?= esc($type['name']) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($type['description'])): ?>
                    <div class="row">
                        <div class="col-12">
                            <label class="text-muted small">Description</label>
                            <p><?= nl2br(esc($type['description'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Audit Trail -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Audit Trail</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Created By</label>
                            <p><?= esc($type['created_by_name'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Created At</label>
                            <p><?= date('F d, Y h:i A', strtotime($type['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($type['updated_at'])): ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Last Updated By</label>
                            <p><?= esc($type['updated_by_name'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Last Updated At</label>
                            <p><?= date('F d, Y h:i A', strtotime($type['updated_at'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($type['deleted_at'])): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">Deleted By</label>
                            <p><?= esc($type['deleted_by_name'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Deleted At</label>
                            <p><?= date('F d, Y h:i A', strtotime($type['deleted_at'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

