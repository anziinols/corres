<?= $this->extend('templates/dakoii_template') ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill" style="font-size: 3rem; color: var(--dakoii-accent);"></i>
                        <h3 class="mt-3 mb-1">Dakoii Admin Portal</h3>
                        <p class="text-muted">Sign in to continue</p>
                    </div>

                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('dakoii/authenticate') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="bi bi-person-fill me-1"></i>Username
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg" 
                                id="username" 
                                name="username" 
                                value="<?= old('username') ?>"
                                placeholder="Enter your username"
                                required
                                autofocus
                            >
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock-fill me-1"></i>Password
                            </label>
                            <input 
                                type="password" 
                                class="form-control form-control-lg" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required
                            >
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="bi bi-shield-check me-1"></i>Secure authentication required
                </small>
            </div>

            <div class="text-center mt-3">
                <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .card {
        border-radius: 12px;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(88, 166, 255, 0.25);
    }
    
    .btn-primary {
        font-weight: 500;
        letter-spacing: 0.5px;
    }
</style>
<?= $this->endSection() ?>

