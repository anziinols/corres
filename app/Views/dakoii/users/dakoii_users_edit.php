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
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </h2>
                    <p class="text-muted mb-0">Update user information for <?= esc($user['name']) ?></p>
                </div>
                <div>
                    <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>User Details</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users/' . $user['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?= old('name', $user['name']) ?>"
                                   maxlength="255"
                                   required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= old('email', $user['email']) ?>"
                                   maxlength="255"
                                   required>
                            <div class="form-text">
                                Current email: <strong><?= esc($user['email']) ?></strong>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                New Password <small class="text-muted">(Leave blank to keep current password)</small>
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   minlength="4">
                            <div class="form-text">
                                Minimum 4 characters. Only fill if you want to change the password.
                            </div>
                        </div>

                        <!-- Position -->
                        <div class="mb-3">
                            <label for="position" class="form-label">
                                Position / Job Title <small class="text-muted">(Optional)</small>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="position" 
                                   name="position" 
                                   value="<?= old('position', $user['position']) ?>"
                                   maxlength="255"
                                   placeholder="e.g., Manager, Clerk, Director">
                        </div>

                        <!-- Group -->
                        <div class="mb-3">
                            <label for="group_id" class="form-label">
                                Group <small class="text-muted">(Optional)</small>
                            </label>
                            <select class="form-select" id="group_id" name="group_id">
                                <option value="">— No Group —</option>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?= $group['id'] ?>" <?= old('group_id', $user['group_id']) == $group['id'] ? 'selected' : '' ?>>
                                        <?= esc($group['group_code']) ?> - <?= esc($group['group_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <hr class="my-4">

                        <!-- File Uploads Section -->
                        <h6 class="mb-3 text-muted"><i class="bi bi-file-earmark-image me-2"></i>Document Files</h6>

                        <!-- Signature Upload -->
                        <div class="mb-3">
                            <label for="signature_file" class="form-label">
                                Signature File <small class="text-muted">(Optional)</small>
                            </label>
                            <?php if ($user['signature_filepath']): ?>
                                <div class="mb-2">
                                    <small class="text-muted">Current file:</small>
                                    <div class="p-2 border rounded bg-light">
                                        <img src="<?= base_url($user['signature_filepath']) ?>" alt="Signature" style="max-height: 80px;">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input type="file" 
                                   class="form-control" 
                                   id="signature_file" 
                                   name="signature_file" 
                                   accept="image/*">
                            <div class="form-text">
                                Upload a new file to replace the existing signature (PNG, JPG, GIF)
                            </div>
                        </div>

                        <!-- Stamp Upload -->
                        <div class="mb-3">
                            <label for="stamp_file" class="form-label">
                                Stamp File <small class="text-muted">(Optional)</small>
                            </label>
                            <?php if ($user['stamp_filepath']): ?>
                                <div class="mb-2">
                                    <small class="text-muted">Current file:</small>
                                    <div class="p-2 border rounded bg-light">
                                        <img src="<?= base_url($user['stamp_filepath']) ?>" alt="Stamp" style="max-height: 80px;">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input type="file" 
                                   class="form-control" 
                                   id="stamp_file" 
                                   name="stamp_file" 
                                   accept="image/*">
                            <div class="form-text">
                                Upload a new file to replace the existing stamp (PNG, JPG, GIF)
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Roles Section -->
                        <h6 class="mb-3 text-muted"><i class="bi bi-shield-check me-2"></i>User Roles & Permissions</h6>

                        <div class="row">
                            <div class="col-md-4">
                                <!-- Is Admin -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_admin" 
                                               name="is_admin" 
                                               value="1"
                                               <?= old('is_admin', $user['is_admin']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_admin">
                                            <strong>Administrator</strong>
                                            <div class="form-text">Full system access</div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Is Supervisor -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_supervisor" 
                                               name="is_supervisor" 
                                               value="1"
                                               <?= old('is_supervisor', $user['is_supervisor']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_supervisor">
                                            <strong>Supervisor</strong>
                                            <div class="form-text">Can approve documents</div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Is Front Desk -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_front_desk" 
                                               name="is_front_desk" 
                                               value="1"
                                               <?= old('is_front_desk', $user['is_front_desk']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_front_desk">
                                            <strong>Front Desk</strong>
                                            <div class="form-text">Handles incoming docs</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Account Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= old('status', $user['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status', $user['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('dakoii/organizations/' . $organization['id'] . '/users') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
