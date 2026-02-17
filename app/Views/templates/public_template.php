<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Correspondence Management System</title>
    <meta name="description" content="CORRES - Professional Correspondence Management System for enterprise organizations. Streamline document tracking, workflow management, and boost productivity.">
    <meta name="keywords" content="correspondence management, document tracking, workflow management, enterprise software, document management system">
    <meta name="author" content="Dakoii Systems">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="CORRES - Corporate Correspondence Management System">
    <meta property="og:description" content="Professional document tracking and management solution for enterprise organizations.">
    <meta property="og:image" content="<?= base_url('public/assets/images/corres_logo.png') ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:site_name" content="CORRES">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="CORRES - Corporate Correspondence Management System">
    <meta name="twitter:description" content="Professional document tracking and management solution for enterprise organizations.">
    <meta name="twitter:image" content="<?= base_url('public/assets/images/corres_logo.png') ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('public/assets/images/corres_favicon.ico') ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Landing Page CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/css/landing.css') ?>">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1A3A5F;
            --secondary-color: #0F2C44;
            --accent-color: #E5B31E;
            --light-bg: #F8F9FA;
            --dark-text: #212529;
            --border-color: #DEE2E6;
            --corporate-gray: #4A5568;
            --light-gray: #E2E8F0;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--dark-text);
            line-height: 1.6;
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
        
        .btn-accent {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }
        
        .btn-accent:hover {
            background-color: #D8A31A;
            border-color: #D8A31A;
            color: white;
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
        
        .hero-section {
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,96C1248,75,1344,53,1392,42.7L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }
        
        .features-section {
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
        }
        
        .cta-section {
            background: linear-gradient(135deg, #1A3A5F 0%, #0F2C44 100%);
            color: white;
        }
        
        .text-corporate {
            color: var(--primary-color);
        }
        
        .border-corporate {
            border-color: var(--primary-color);
        }
        
        .bg-corporate {
            background-color: var(--primary-color);
        }
        
        .text-muted-corporate {
            color: var(--corporate-gray);
        }
        
        /* Accessibility improvements */
        .visually-hidden-focusable {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        
        .visually-hidden-focusable:focus {
            position: static !important;
            width: auto !important;
            height: auto !important;
            padding: inherit !important;
            margin: inherit !important;
            overflow: visible !important;
            clip: auto !important;
            white-space: normal !important;
        }
        
        .hover-opacity {
            transition: opacity 0.3s ease;
        }
        
        .hover-opacity:hover {
            opacity: 0.8 !important;
        }
        
        /* Footer link hover effects */
        footer a:hover {
            opacity: 0.8;
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100">
    
    <!-- Skip to main content link for accessibility -->
    <a href="#main-content" class="visually-hidden-focusable btn btn-primary position-absolute m-2" style="z-index: 9999;">Skip to main content</a>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" role="navigation" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>" aria-label="CORRES Home">
                <img src="<?= base_url('public/assets/images/corres_logo.png') ?>" alt="CORRES Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>" aria-current="<?= uri_string() === '' ? 'page' : 'false' ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('about') ?>" aria-current="<?= uri_string() === 'about' ? 'page' : 'false' ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('contact') ?>" aria-current="<?= uri_string() === 'contact' ? 'page' : 'false' ?>">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-primary ms-2 px-3" href="<?= base_url('login') ?>">
                            <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" class="main-content" role="main">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto" role="contentinfo">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="mb-3">CORRES</h5>
                    <p class="mb-3 small">Professional correspondence management system for enterprise organizations. Streamline your document workflows with confidence.</p>
                    <div class="d-flex gap-3">
                        <a href="https://www.dakoiims.com/" target="_blank" rel="noopener noreferrer" class="text-white" aria-label="Visit Dakoii Systems Website">
                            <i class="bi bi-globe fs-5"></i>
                        </a>
                        <a href="<?= base_url('contact') ?>" class="text-white" aria-label="Contact Us">
                            <i class="bi bi-envelope fs-5"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="<?= base_url() ?>" class="text-white text-decoration-none hover-opacity"><i class="bi bi-chevron-right small me-1"></i>Home</a></li>
                        <li class="mb-2"><a href="<?= base_url('about') ?>" class="text-white text-decoration-none hover-opacity"><i class="bi bi-chevron-right small me-1"></i>About Us</a></li>
                        <li class="mb-2"><a href="<?= base_url('contact') ?>" class="text-white text-decoration-none hover-opacity"><i class="bi bi-chevron-right small me-1"></i>Contact</a></li>
                        <li class="mb-2"><a href="<?= base_url('login') ?>" class="text-white text-decoration-none hover-opacity"><i class="bi bi-chevron-right small me-1"></i>Login</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h6 class="mb-3">Contact Information</h6>
                    <ul class="list-unstyled mb-3">
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>support@dakoiims.com</li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i>24/7 Support Available</li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <span class="me-2 small">Developed by</span>
                        <a href="https://www.dakoiims.com/" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                            <img src="<?= base_url('public/assets/images/dakoii_systems_logo.png') ?>" alt="Dakoii Systems" style="height: 30px; width: auto;" class="ms-1">
                        </a>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 opacity-25">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 small">&copy; <?= date('Y') ?> CORRES. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 small">Built with <i class="bi bi-heart-fill text-danger"></i> by Dakoii Systems</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
