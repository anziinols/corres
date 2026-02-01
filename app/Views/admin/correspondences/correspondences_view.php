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
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary"><i class="bi bi-info-circle me-2"></i>Correspondence Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Correspondence Number</label>
                            <p class="fw-bold fs-5 text-dark"><?= esc($correspondence['correspondence_number']) ?></p>
                        </div>
                        <div class="col-md-6">
                             <label class="text-muted small text-uppercase fw-bold">Date Received</label>
                             <p class="fs-5"><?= date('d M Y', strtotime($correspondence['date_received'])) ?></p>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold">Subject (Headline)</label>
                        <p class="fs-5 fw-semibold"><?= esc($correspondence['subject']) ?></p>
                    </div>

                    <?php if (!empty($correspondence['remarks'])): ?>
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold">Brief Description</label>
                        <div class="p-3 bg-light rounded">
                            <?= nl2br(esc($correspondence['remarks'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row mb-3">
                         <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Type</label>
                            <p class="badge bg-secondary"><?= esc($correspondence['correspondence_type_name'] ?? $correspondence['correspondence_type']) ?></p>
                         </div>
                         <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Direction</label>
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
                    </div>
                </div>
            </div>

            <!-- Organization & Group -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary"><i class="bi bi-building me-2"></i>Organization & Group</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Organization</label>
                            <p class="fs-5"><?= esc($correspondence['org_name'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Group / Department</label>
                            <p class="fs-5"><?= esc($correspondence['group_name'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sender & Context -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Sender & Context</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="text-muted small text-uppercase fw-bold">Sender / Organization</label>
                            <p class="fs-5"><?= esc($correspondence['sender_name'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold">Priority</label>
                            <p>
                                <?php
                                $priorityBadge = [
                                    'NORMAL' => 'bg-primary',
                                    'URGENT' => 'bg-danger',
                                    'CONFIDENTIAL' => 'bg-dark'
                                ];
                                ?>
                                <span class="badge <?= $priorityBadge[$correspondence['priority']] ?? 'bg-primary' ?> fs-6">
                                    <?= esc($correspondence['priority']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($correspondence['status'] === 'ARCHIVED'): ?>
            <!-- Filing & Archive (only shown when archived) -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-archive me-2"></i>Archive Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Filing Reference</label>
                            <p class="fs-6"><?= esc($correspondence['filing_reference'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Archive Location</label>
                            <p class="fs-6"><?= esc($correspondence['archive_location'] ?? '-') ?></p>
                        </div>
                    </div>
                    <?php if (!empty($correspondence['archive_date'])): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Archive Date</label>
                            <p class="fs-6"><?= date('d M Y', strtotime($correspondence['archive_date'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Attachment -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary"><i class="bi bi-paperclip me-2"></i>Attachment</h5>
                </div>
                <div class="card-body text-center py-4">
                    <?php if (!empty($correspondence['file_path'])): ?>
                        <i class="bi bi-file-earmark-pdf text-danger display-1 mb-3"></i>
                        <h6 class="fw-bold mb-3">Document Attached</h6>
                        <a href="<?= base_url($correspondence['file_path']) ?>" target="_blank" class="btn btn-primary w-100">
                            <i class="bi bi-eye me-2"></i>View Full Document
                        </a>
                    <?php else: ?>
                        <i class="bi bi-file-earmark-x text-muted display-1 mb-3"></i>
                        <p class="text-muted mb-0">No document attached.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Linked Correspondence -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary"><i class="bi bi-link-45deg me-2"></i>Linked Documents</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($linked_correspondences)): ?>
                        <div class="p-3 text-muted small text-center">No linked documents.</div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($linked_correspondences as $link): ?>
                                <li class="list-group-item">
                                    <a href="<?= base_url('admin/correspondences/' . $link['id']) ?>" class="text-decoration-none">
                                        <div class="fw-bold small"><?= esc($link['correspondence_number']) ?></div>
                                        <div class="text-muted small truncate-1"><?= esc($link['subject']) ?></div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status & Priority -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary"><i class="bi bi-flag-fill me-2"></i>Tracking</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Current Status</label>
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
                            <span class="badge <?= $statusBadge[$correspondence['status']] ?? 'bg-secondary' ?> fs-6 w-100 py-2">
                                <?= esc($correspondence['status']) ?>
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-muted small text-uppercase fw-bold">Priority Level</label>
                        <p>
                            <?php
                            $priorityBadge = [
                                'NORMAL' => 'bg-primary',
                                'URGENT' => 'bg-danger',
                                'CONFIDENTIAL' => 'bg-dark'
                            ];
                            ?>
                            <span class="badge <?= $priorityBadge[$correspondence['priority']] ?? 'bg-primary' ?> fs-6 w-100 py-2">
                                <?= esc($correspondence['priority']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Registration Info -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Registered By</label>
                        <p class="mb-0 fw-semibold"><?= esc($correspondence['registered_by_name'] ?? 'System') ?></p>
                        <small class="text-muted"><?= $correspondence['registration_date'] ? date('d M Y H:i', strtotime($correspondence['registration_date'])) : '-' ?></small>
                    </div>
                    <hr>
                    <div class="row small">
                        <div class="col-6 text-muted">Created:</div>
                        <div class="col-6 text-end"><?= date('d M Y', strtotime($correspondence['created_at'])) ?></div>
                        <div class="col-6 text-muted">Updated:</div>
                        <div class="col-6 text-end"><?= date('d M Y', strtotime($correspondence['updated_at'])) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

