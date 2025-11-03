<?= $this->extend('templates/public_template') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #2C5F8D 0%, #1E4A6D 100%);">
    <div class="container">
        <div class="row align-items-center text-white">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Welcome to Correspondence Management System</h1>
                <p class="lead mb-4">Streamline your document workflow with our comprehensive correspondence tracking and management solution.</p>
                <div class="d-flex gap-3">
                    <a href="<?= base_url('login') ?>" class="btn btn-light btn-lg px-4">Get Started</a>
                    <a href="<?= base_url('about') ?>" class="btn btn-outline-light btn-lg px-4">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-4 mt-lg-0">
                <i class="bi bi-file-earmark-text" style="font-size: 15rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Key Features</h2>
            <p class="text-muted">Everything you need to manage your correspondence efficiently</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-check text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Document Tracking</h5>
                        <p class="card-text text-muted">Track all incoming and outgoing correspondence in real-time with comprehensive logging.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="bi bi-clock-history text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Workflow Management</h5>
                        <p class="card-text text-muted">Automate routing and approval processes to ensure timely responses.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Secure Storage</h5>
                        <p class="card-text text-muted">Keep your documents safe with encrypted storage and access controls.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="bi bi-search text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Advanced Search</h5>
                        <p class="card-text text-muted">Quickly find any document with powerful search and filtering capabilities.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="bi bi-graph-up text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Reports & Analytics</h5>
                        <p class="card-text text-muted">Generate detailed reports and gain insights into your correspondence patterns.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div class="card-body">
                        <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Collaboration</h5>
                        <p class="card-text text-muted">Work together seamlessly with team members on document processing.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section py-5" style="background-color: #F8F9FA;">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Ready to Get Started?</h2>
        <p class="lead text-muted mb-4">Join us today and transform the way you manage correspondence.</p>
        <a href="<?= base_url('login') ?>" class="btn btn-primary btn-lg px-5">Access System</a>
    </div>
</section>

<?= $this->endSection() ?>

