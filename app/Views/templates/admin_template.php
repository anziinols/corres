<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Admin Portal</title>
    <meta name="description" content="Admin Portal - Correspondence Management System">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('public/assets/images/corres_favicon.ico') ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Admin Theme CSS -->
    <style>
        :root {
            --admin-primary: #2C5F8D;
            --admin-secondary: #1E4A6D;
            --admin-accent: #4A90C8;
            --admin-light-bg: #F8F9FA;
            --admin-white: #FFFFFF;
            --admin-dark-text: #212529;
            --admin-border: #DEE2E6;
            --admin-success: #28A745;
            --admin-danger: #DC3545;
            --admin-warning: #FFC107;
            --admin-info: #17A2B8;
        }
        
        body {
            background-color: var(--admin-light-bg);
            color: var(--admin-dark-text);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans', Helvetica, Arial, sans-serif;
        }
        
        .navbar-admin {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            border-bottom: 3px solid var(--admin-accent);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            color: var(--admin-white) !important;
            font-weight: 600;
            font-size: 1.35rem;
            letter-spacing: 0.5px;
        }
        
        .navbar-brand:hover {
            color: var(--admin-accent) !important;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: var(--admin-white) !important;
            transform: translateY(-1px);
        }
        
        .card {
            background-color: var(--admin-white);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: box-shadow 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: var(--admin-white);
            border-bottom: 2px solid var(--admin-accent);
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        
        .btn-primary {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--admin-secondary);
            border-color: var(--admin-secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn-danger {
            background-color: var(--admin-danger);
            border-color: var(--admin-danger);
            font-weight: 500;
        }
        
        .btn-outline-primary {
            color: var(--admin-primary);
            border-color: var(--admin-primary);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
            color: var(--admin-white);
        }
        
        .form-control, .form-select {
            border: 2px solid var(--admin-border);
            padding: 0.6rem 0.9rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 200, 0.25);
        }
        
        .alert {
            border-radius: 6px;
            border-left: 4px solid;
        }
        
        .alert-success {
            border-left-color: var(--admin-success);
        }
        
        .alert-danger {
            border-left-color: var(--admin-danger);
        }
        
        .alert-warning {
            border-left-color: var(--admin-warning);
        }
        
        .alert-info {
            border-left-color: var(--admin-info);
        }
        
        .footer {
            background: linear-gradient(135deg, var(--admin-secondary) 0%, var(--admin-primary) 100%);
            color: var(--admin-white);
            padding: 1.5rem 0;
            margin-top: auto;
            border-top: 3px solid var(--admin-accent);
        }
        
        .footer a {
            color: var(--admin-white);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer a:hover {
            color: var(--admin-accent);
        }
        
        .main-content {
            min-height: calc(100vh - 150px);
            padding: 2rem 0;
        }

        @media (max-width: 991.98px) {
            .main-content {
                padding: 1.5rem 0;
            }
        }


        .stat-card {
            background: linear-gradient(135deg, var(--admin-white) 0%, var(--admin-light-bg) 100%);
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid var(--admin-accent);
        }
        
        .dropdown-menu {
            border: 1px solid var(--admin-border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-admin">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('admin/dashboard') ?>">
                <i class="bi bi-shield-lock-fill me-2"></i>Admin Portal
            </a>

            <?php if (session()->has('admin_logged_in') && session()->get('admin_logged_in')): ?>
            <div class="ms-auto">
                <ul class="navbar-nav align-items-lg-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= esc(session()->get('admin_name')) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header"><?= esc(session()->get('admin_name')) ?></h6></li>
                            <?php if (session()->get('admin_position')): ?>
                                <li><span class="dropdown-item-text small text-muted">
                                    <i class="bi bi-briefcase me-1"></i><?= esc(session()->get('admin_position')) ?>
                                </span></li>
                            <?php endif; ?>
                            <li><span class="dropdown-item-text small text-muted">
                                <i class="bi bi-envelope me-1"></i><?= esc(session()->get('admin_email')) ?>
                            </span></li>
                            <?php if (session()->get('admin_is_admin') || session()->get('admin_is_supervisor') || session()->get('admin_is_front_desk')): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><span class="dropdown-item-text small">
                                    <?php if (session()->get('admin_is_admin')): ?>
                                        <span class="badge bg-danger me-1"><i class="bi bi-shield-fill-check"></i> Admin</span>
                                    <?php endif; ?>
                                    <?php if (session()->get('admin_is_supervisor')): ?>
                                        <span class="badge bg-success me-1"><i class="bi bi-person-check"></i> Supervisor</span>
                                    <?php endif; ?>
                                    <?php if (session()->get('admin_is_front_desk')): ?>
                                        <span class="badge bg-info me-1"><i class="bi bi-door-open"></i> Front Desk</span>
                                    <?php endif; ?>
                                </span></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="<?= base_url('admin/logout') ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Content -->
    <?php if (session()->has('admin_logged_in') && session()->get('admin_logged_in')): ?>
        <main class="main-content flex-grow-1">
            <div class="container-fluid">
                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i><?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
        </main>
    <?php else: ?>
    <!-- No Sidebar for Non-Authenticated Pages -->
    <main class="main-content flex-grow-1">
        <div class="container-fluid">
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </main>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <p class="mb-0"><i class="bi bi-shield-lock-fill me-1"></i>Admin Portal</p>
                </div>
                <div class="col-md-4 text-center">
                    <p class="mb-0">&copy; <?= date('Y') ?> Correspondence Management System. All rights reserved.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end justify-content-start">
                        <span class="me-2" style="font-size: 0.875rem;">Developed by</span>
                        <a href="https://www.dakoiims.com/" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                            <img src="<?= base_url('public/assets/images/dakoii_systems_logo.png') ?>" alt="Dakoii Systems" style="height: 30px; width: auto; filter: brightness(1.2);" class="ms-1">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>

