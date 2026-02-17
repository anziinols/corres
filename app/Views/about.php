<?= $this->extend('templates/public_template') ?>

<?= $this->section('title') ?>About Us<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #1A3A5F 0%, #0F2C44 100%);">
    <div class="container">
        <div class="row align-items-center text-white">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">About CORRES</h1>
                <p class="lead mb-4">Transforming how organizations manage their correspondence through innovation and reliability.</p>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-4">Our Mission</h2>
                <p class="text-muted mb-4">At CORRES, we believe that efficient correspondence management is the backbone of successful organizations. Our mission is to provide enterprises with a comprehensive, secure, and user-friendly platform that streamlines document tracking, enhances collaboration, and ensures accountability.</p>
                <p class="text-muted">Founded by Dakoii Systems, we've helped countless organizations digitize their correspondence workflows, reduce processing times, and maintain compliance with industry standards.</p>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4">
                    <div class="card-body text-center">
                        <i class="bi bi-bullseye text-primary" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 mb-3">Core Values</h4>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Reliability:</strong> Your documents are always secure and accessible</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Innovation:</strong> Continuous improvement and modern features</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Simplicity:</strong> Intuitive design for all users</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Transparency:</strong> Clear tracking and audit trails</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5" style="background-color: #F8F9FA;">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="display-4 fw-bold text-primary">500+</div>
                <p class="text-muted">Organizations Served</p>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="display-4 fw-bold text-primary">1M+</div>
                <p class="text-muted">Documents Processed</p>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="display-4 fw-bold text-primary">99.9%</div>
                <p class="text-muted">Uptime</p>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-primary">24/7</div>
                <p class="text-muted">Support Available</p>
            </div>
        </div>
    </div>
</section>

<!-- Team/Development Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Developed by Dakoii Systems</h2>
            <p class="text-muted">Expert software development for enterprise solutions</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <img src="<?= base_url('public/assets/images/dakoii_systems_logo.png') ?>" alt="Dakoii Systems" style="height: 80px; width: auto;" class="mb-4">
                        <p class="text-muted mb-4">Dakoii Systems is a leading software development company specializing in enterprise solutions. With years of experience in building robust, scalable applications, we bring expertise and innovation to every project.</p>
                        <a href="https://www.dakoiims.com/" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                            <i class="bi bi-globe me-2"></i>Visit Our Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>