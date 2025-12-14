<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/correspondences') ?>">Correspondences</a></li>
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
                                <i class="bi bi-eye me-2"></i>Correspondence Details
                            </h2>
                            <p class="text-muted mb-0">
                                <strong><?= esc($correspondence['correspondence_number']) ?></strong>
                            </p>
                        </div>
                        <div>
                            <a href="<?= base_url('admin/correspondences/' . $correspondence['id'] . '/edit') ?>" class="btn btn-warning me-2">
                                <i class="bi bi-pencil me-2"></i>Edit
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

    <!-- Correspondence Details -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Correspondence Number</label>
                            <p class="fw-bold"><?= esc($correspondence['correspondence_number']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">External Reference</label>
                            <p><?= esc($correspondence['reference_number'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted small">Subject/Particulars</label>
                            <p><?= esc($correspondence['subject']) ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted small">Direction</label>
                            <p>
                                <?php
                                $directionBadge = [
                                    'INWARD' => 'bg-info',
                                    'OUTWARD' => 'bg-warning',
                                    'INTERNAL' => 'bg-secondary'
                                ];
                                ?>
                                <span class="badge <?= $directionBadge[$correspondence['correspondence_direction']] ?? 'bg-secondary' ?>">
                                    <?= esc($correspondence['correspondence_direction']) ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Type</label>
                            <p><?= esc($correspondence['correspondence_type']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Department</label>
                            <p><?= esc($correspondence['department'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar me-2"></i>Dates</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="text-muted small">Date Received</label>
                            <p><?= date('d M Y', strtotime($correspondence['date_received'])) ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Original Date</label>
                            <p><?= $correspondence['original_date'] ? date('d M Y', strtotime($correspondence['original_date'])) : '-' ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Date Sent</label>
                            <p><?= $correspondence['date_sent'] ? date('d M Y', strtotime($correspondence['date_sent'])) : '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sender/Recipient Information -->
            <?php if ($correspondence['correspondence_direction'] === 'OUTWARD'): ?>
                <!-- Recipient Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person-check-fill me-2"></i>Recipient Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Recipient Name</label>
                                <p><?= esc($correspondence['recipient_name'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Recipient Organization</label>
                                <p><?= esc($correspondence['recipient_organization'] ?? '-') ?></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="text-muted small">Recipient Address</label>
                                <p><?= esc($correspondence['recipient_address'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Dispatch Method</label>
                                <p><?= esc($correspondence['dispatch_method'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Sender Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person-fill me-2"></i>Sender Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Sender Name</label>
                                <p><?= esc($correspondence['sender_name'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Sender Organization</label>
                                <p><?= esc($correspondence['sender_organization'] ?? '-') ?></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="text-muted small">Sender Address</label>
                                <p><?= esc($correspondence['sender_address'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Sender Contact</label>
                                <p><?= esc($correspondence['sender_contact'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filing & Archive -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-folder-fill me-2"></i>Filing & Archive</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="text-muted small">Filing Reference</label>
                            <p><?= esc($correspondence['filing_reference'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Archive Location</label>
                            <p><?= esc($correspondence['archive_location'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Archive Date</label>
                            <p><?= $correspondence['archive_date'] ? date('d M Y', strtotime($correspondence['archive_date'])) : '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            <?php if (!empty($correspondence['remarks'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Remarks</h5>
                    </div>
                    <div class="card-body">
                        <p><?= nl2br(esc($correspondence['remarks'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Status & Priority -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-flag-fill me-2"></i>Status & Priority</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <p>
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
                            <span class="badge <?= $statusBadge[$correspondence['status']] ?? 'bg-secondary' ?> fs-6">
                                <?= esc($correspondence['status']) ?>
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-muted small">Priority</label>
                        <p>
                            <?php
                            $priorityBadge = [
                                'LOW' => 'bg-secondary',
                                'NORMAL' => 'bg-primary',
                                'HIGH' => 'bg-warning',
                                'URGENT' => 'bg-danger'
                            ];
                            ?>
                            <span class="badge <?= $priorityBadge[$correspondence['priority']] ?? 'bg-primary' ?> fs-6">
                                <?= esc($correspondence['priority']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Organization -->
            <?php if (!empty($correspondence['org_name'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-building me-2"></i>Organization</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong><?= esc($correspondence['org_name']) ?></strong></p>
                        <p class="text-muted small mb-0">Code: <?= esc($correspondence['org_code']) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Registration Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Registration Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Registered By</label>
                        <p><?= esc($correspondence['registered_by_name'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-muted small">Registration Date</label>
                        <p><?= $correspondence['registration_date'] ? date('d M Y H:i', strtotime($correspondence['registration_date'])) : '-' ?></p>
                    </div>
                </div>
            </div>

            <!-- Audit Trail -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Audit Trail</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="text-muted small">Created</label>
                        <p class="small"><?= date('d M Y H:i', strtotime($correspondence['created_at'])) ?></p>
                    </div>
                    <div>
                        <label class="text-muted small">Last Updated</label>
                        <p class="small"><?= date('d M Y H:i', strtotime($correspondence['updated_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

