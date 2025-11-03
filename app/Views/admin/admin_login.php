<?= $this->extend('templates/admin_template') ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="bi bi-shield-lock-fill" style="font-size: 4rem; color: var(--admin-primary);"></i>
                        </div>
                        <h3 class="mb-1 fw-bold" style="color: var(--admin-primary);">Admin Portal</h3>
                        <p class="text-muted">Sign in to access your account</p>
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

                    <form action="<?= base_url('login/authenticate') ?>" method="post" id="loginForm">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="bi bi-envelope-fill me-1"></i>Email Address
                            </label>
                            <input 
                                type="email" 
                                class="form-control form-control-lg" 
                                id="email" 
                                name="email" 
                                value="<?= old('email') ?>"
                                placeholder="Enter your email"
                                required
                                autofocus
                            >
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
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

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
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
        border: none;
    }
    
    .form-control:focus {
        border-color: var(--admin-accent);
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 200, 0.25);
    }
    
    .btn-primary {
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 0.75rem;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(44, 95, 141, 0.3);
    }
</style>
<?= $this->endSection() ?>

