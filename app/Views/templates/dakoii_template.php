<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Dakoii Admin Portal</title>
    <meta name="description" content="Dakoii Admin Portal - Secure Administration Dashboard">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('public/assets/images/corres_favicon.ico') ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Dark Theme CSS -->
    <style>
        :root {
            --dakoii-bg-primary: #0d1117;
            --dakoii-bg-secondary: #161b22;
            --dakoii-bg-tertiary: #21262d;
            --dakoii-border: #30363d;
            --dakoii-text-primary: #c9d1d9;
            --dakoii-text-secondary: #8b949e;
            --dakoii-accent: #58a6ff;
            --dakoii-accent-hover: #1f6feb;
            --dakoii-success: #3fb950;
            --dakoii-danger: #f85149;
            --dakoii-warning: #d29922;
        }
        
        body {
            background-color: var(--dakoii-bg-primary);
            color: var(--dakoii-text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans', Helvetica, Arial, sans-serif;
        }
        
        .navbar-dark {
            background-color: var(--dakoii-bg-secondary) !important;
            border-bottom: 1px solid var(--dakoii-border);
        }
        
        .navbar-brand {
            color: var(--dakoii-text-primary) !important;
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .nav-link {
            color: var(--dakoii-text-secondary) !important;
        }
        
        .nav-link:hover {
            color: var(--dakoii-accent) !important;
        }
        
        .card {
            background-color: var(--dakoii-bg-secondary);
            border: 1px solid var(--dakoii-border);
            color: var(--dakoii-text-primary);
        }
        
        .card-header {
            background-color: var(--dakoii-bg-tertiary);
            border-bottom: 1px solid var(--dakoii-border);
        }
        
        .btn-primary {
            background-color: var(--dakoii-accent);
            border-color: var(--dakoii-accent);
            color: #ffffff;
        }
        
        .btn-primary:hover {
            background-color: var(--dakoii-accent-hover);
            border-color: var(--dakoii-accent-hover);
        }
        
        .btn-danger {
            background-color: var(--dakoii-danger);
            border-color: var(--dakoii-danger);
        }
        
        .form-control, .form-select {
            background-color: var(--dakoii-bg-tertiary);
            border-color: var(--dakoii-border);
            color: var(--dakoii-text-primary);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--dakoii-bg-tertiary);
            border-color: var(--dakoii-accent);
            color: var(--dakoii-text-primary);
            box-shadow: 0 0 0 0.25rem rgba(88, 166, 255, 0.25);
        }
        
        .alert {
            border: 1px solid var(--dakoii-border);
        }
        
        .footer {
            background-color: var(--dakoii-bg-secondary);
            border-top: 1px solid var(--dakoii-border);
            color: var(--dakoii-text-secondary);
            padding: 1.5rem 0;
            margin-top: auto;
        }
        
        .main-content {
            min-height: calc(100vh - 150px);
            padding: 2rem 0;
        }
        
        .sidebar {
            background-color: var(--dakoii-bg-secondary);
            border-right: 1px solid var(--dakoii-border);
            min-height: calc(100vh - 56px);
        }
        
        .sidebar .nav-link {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 0.25rem;
        }
        
        .sidebar .nav-link.active {
            background-color: var(--dakoii-bg-tertiary);
            color: var(--dakoii-accent) !important;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--dakoii-bg-tertiary);
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('dakoii/dashboard') ?>">
                <i class="bi bi-shield-lock-fill me-2"></i>Dakoii Admin Portal
            </a>

            <?php if (session()->has('dakoii_logged_in') && session()->get('dakoii_logged_in')): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dakoii/dashboard') ?>">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= esc(session()->get('dakoii_name')) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="<?= base_url('dakoii/logout') ?>" method="post" class="d-inline">
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

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <p class="mb-0"><i class="bi bi-shield-lock-fill me-1"></i>Dakoii Admin Portal</p>
                </div>
                <div class="col-md-4 text-center">
                    <p class="mb-0">&copy; <?= date('Y') ?> All rights reserved.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end justify-content-start">
                        <span class="me-2" style="font-size: 0.875rem;">Developed by</span>
                        <a href="https://www.dakoiims.com/" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                            <img src="<?= base_url('public/assets/images/dakoii_systems_logo.png') ?>" alt="Dakoii Systems" style="height: 30px; width: auto; filter: brightness(0.9);" class="ms-1">
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
