<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Correspondence Management System</title>
    <meta name="description" content="Correspondence Management System - Efficient document tracking and management">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('public/assets/images/corres_favicon.ico') ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2C5F8D;
            --secondary-color: #1E4A6D;
            --accent-color: #4A90C8;
            --light-bg: #F8F9FA;
            --dark-text: #212529;
            --border-color: #DEE2E6;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand img {
            height: 50px;
            width: auto;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .footer {
            background-color: var(--secondary-color);
            color: white;
            padding: 2rem 0;
            margin-top: auto;
        }
        
        .main-content {
            min-height: calc(100vh - 200px);
        }
        
        .card {
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background-color: var(--light-bg);
            border-bottom: 2px solid var(--primary-color);
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100">
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <img src="<?= base_url('public/assets/images/corres_logo.png') ?>" alt="Correspondence Management System">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('contact') ?>">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-primary ms-2 px-3" href="<?= base_url('login') ?>">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5>Correspondence Management System</h5>
                    <p class="mb-0 small">Efficient document tracking and management solution</p>
                </div>
                <div class="col-md-4 text-center">
                    <p class="mb-0">&copy; <?= date('Y') ?> All rights reserved.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end justify-content-start mt-md-0 mt-3">
                        <span class="me-2">Developed by</span>
                        <a href="https://www.dakoiims.com/" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                            <img src="<?= base_url('public/assets/images/dakoii_systems_logo.png') ?>" alt="Dakoii Systems" style="height: 35px; width: auto;" class="ms-1">
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

