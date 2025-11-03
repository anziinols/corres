<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/organizations') ?>">Organizations</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('dakoii/organizations/' . $organization['id']) ?>"><?= esc($organization['org_name']) ?></a></li>
            <li class="breadcrumb-item active">Groups</li>
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
                                <i class="bi bi-diagram-3 me-2"></i>Groups Management
                            </h2>
                            <p class="text-muted mb-0">
                                <i class="bi bi-building me-1"></i>Organization: <strong><?= esc($organization['org_name']) ?></strong> 
                                <span class="badge bg-primary ms-2"><?= esc($organization['org_code']) ?></span>
                            </p>
                        </div>
                        <div>
                            <a href="<?= base_url('dakoii/organizations/' . $organization['id']) ?>" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left me-2"></i>Back to Organization
                            </a>
                            <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/new') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add New Group
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Groups List</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($groups)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-diagram-3" style="font-size: 4rem; color: var(--dakoii-text-secondary);"></i>
                            <h4 class="mt-3 text-muted">No Groups Found</h4>
                            <p class="text-muted">Get started by creating your first group for this organization.</p>
                            <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/new') ?>" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Create Group
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Code</th>
                                        <th>Group Name</th>
                                        <th style="width: 200px;">Parent Group</th>
                                        <th style="width: 120px;">Status</th>
                                        <th style="width: 150px;">Created By</th>
                                        <th style="width: 150px;">Created At</th>
                                        <th style="width: 200px;" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($groups as $group): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info"><?= esc($group['group_code']) ?></span>
                                            </td>
                                            <td>
                                                <strong><?= esc($group['group_name']) ?></strong>
                                                <?php if ($group['description']): ?>
                                                    <br>
                                                    <small class="text-muted"><?= esc(substr($group['description'], 0, 60)) ?><?= strlen($group['description']) > 60 ? '...' : '' ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($group['parent_name']): ?>
                                                    <small class="text-muted">
                                                        <i class="bi bi-arrow-return-right me-1"></i><?= esc($group['parent_name']) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <small class="text-muted">—</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($group['status'] === 'active'): ?>
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
                                                    <?= esc($group['created_by_name'] ?? 'System') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M d, Y', strtotime($group['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/' . $group['id']) ?>" 
                                                       class="btn btn-sm btn-outline-info"
                                                       title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups/' . $group['id'] . '/edit') ?>" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDelete(<?= $group['id'] ?>, '<?= esc($group['group_name']) ?>')"
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
        form.action = '<?= base_url('dakoii/organizations/' . $organization['id'] . '/groups') ?>/' + id;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>

