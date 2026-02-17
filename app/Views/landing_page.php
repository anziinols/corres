<?= $this->extend('templates/public_template') ?>

<?= $this->section('title') ?>Home<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #1A3A5F 0%, #0F2C44 100%);">
    <div class="container hero-content">
        <div class="row align-items-center text-white">
            <div class="col-lg-6 animate-fade-in-up">
                <h1 class="display-4 fw-bold mb-4">Corporate Correspondence Management System</h1>
                <p class="lead mb-4">Professional document tracking and management solution for enterprise organizations. Streamline workflows, ensure accountability, and boost productivity.</p>
                
                <!-- Trust Badges -->
                <div class="mb-4">
                    <span class="trust-badge"><i class="bi bi-shield-check"></i> Bank-level Security</span>
                    <span class="trust-badge"><i class="bi bi-lightning-charge"></i> Real-time Tracking</span>
                    <span class="trust-badge"><i class="bi bi-people"></i> 500+ Organizations</span>
                </div>
                
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= base_url('login') ?>" class="btn btn-light btn-lg px-4">
                        <i class="bi bi-rocket-takeoff me-2"></i>Get Started
                    </a>
                    <a href="<?= base_url('about') ?>" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-4 mt-lg-0 animate-fade-in animate-delay-2">
                <div class="hero-icon-wrapper">
                    <i class="bi bi-file-earmark-text hero-icon"></i>
                </div>
                <div class="mt-4">
                    <img src="<?= base_url('public/assets/images/corres_logo.png') ?>" alt="CORRES Logo" style="max-width: 200px; opacity: 0.9;" class="animate-float">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section" id="features">
    <div class="container">
        <div class="text-center mb-5 animate-fade-in-up">
            <h6 class="text-uppercase text-primary fw-bold mb-2">Features</h6>
            <h2 class="fw-bold display-5 mb-3">Everything You Need</h2>
            <p class="text-muted lead">Comprehensive tools to manage your correspondence efficiently</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 animate-fade-in-up animate-delay-1">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-file-earmark-check card-icon"></i>
                        </div>
                        <h5 class="card-title mb-3">Document Tracking</h5>
                        <p class="card-text text-muted">Track all incoming and outgoing correspondence in real-time with comprehensive logging and audit trails.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up animate-delay-2">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-clock-history card-icon"></i>
                        </div>
                        <h5 class="card-title mb-3">Workflow Management</h5>
                        <p class="card-text text-muted">Automate routing and approval processes to ensure timely responses and eliminate bottlenecks.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up animate-delay-3">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-shield-check card-icon"></i>
                        </div>
                        <h5 class="card-title mb-3">Secure Storage</h5>
                        <p class="card-text text-muted">Keep your documents safe with encrypted storage, access controls, and compliance features.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up animate-delay-1">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-search card-icon"></i>
                        </div>
                        <h5 class="card-title mb-3">Advanced Search</h5>
                        <p class="card-text text-muted">Quickly find any document with powerful search, filtering, and full-text capabilities.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up animate-delay-2">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-graph-up card-icon"></i>
                        </div>
                        <h5 class="card-title mb-3">Reports & Analytics</h5>
                        <p class="card-text text-muted">Generate detailed reports and gain insights into your correspondence patterns and performance.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up animate-delay-3">
                <div class="card feature-card h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-people card-icon"></i>
                        </div>
                        <h5 class="card-title mb-3">Team Collaboration</h5>
                        <p class="card-text text-muted">Work together seamlessly with team members on document processing and approvals.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5" style="background-color: #F8F9FA;">
    <div class="container">
        <div class="text-center mb-5 animate-fade-in-up">
            <h6 class="text-uppercase text-primary fw-bold mb-2">How It Works</h6>
            <h2 class="fw-bold display-5 mb-3">Simple 3-Step Process</h2>
            <p class="text-muted lead">Get started with CORRES in minutes</p>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0 animate-fade-in-up animate-delay-1">
                <div class="how-it-works-step text-center">
                    <div class="step-number">1</div>
                    <h5 class="fw-bold mb-3">Register & Setup</h5>
                    <p class="text-muted">Create your organization account and configure departments, users, and permissions in just a few clicks.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 mb-md-0 animate-fade-in-up animate-delay-2">
                <div class="how-it-works-step text-center">
                    <div class="step-number">2</div>
                    <h5 class="fw-bold mb-3">Upload & Track</h5>
                    <p class="text-muted">Start uploading correspondence documents. Our system automatically assigns tracking numbers and routes them.</p>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up animate-delay-3">
                <div class="how-it-works-step text-center">
                    <div class="step-number">3</div>
                    <h5 class="fw-bold mb-3">Manage & Report</h5>
                    <p class="text-muted">Monitor progress, generate reports, and ensure compliance with comprehensive analytics tools.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4 mb-md-0 animate-fade-in-up animate-delay-1">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Organizations Served</div>
                </div>
            </div>
            <div class="col-md-3 mb-4 mb-md-0 animate-fade-in-up animate-delay-2">
                <div class="stat-item">
                    <div class="stat-number">1M+</div>
                    <div class="stat-label">Documents Processed</div>
                </div>
            </div>
            <div class="col-md-3 mb-4 mb-md-0 animate-fade-in-up animate-delay-3">
                <div class="stat-item">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">System Uptime</div>
                </div>
            </div>
            <div class="col-md-3 animate-fade-in-up animate-delay-4">
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support Available</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5" style="background-color: #F8F9FA;">
    <div class="container">
        <div class="text-center mb-5 animate-fade-in-up">
            <h6 class="text-uppercase text-primary fw-bold mb-2">Testimonials</h6>
            <h2 class="fw-bold display-5 mb-3">What Our Clients Say</h2>
            <p class="text-muted lead">Trusted by organizations worldwide</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 animate-fade-in-up animate-delay-1">
                <div class="testimonial-card h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="testimonial-avatar me-3">JD</div>
                        <div>
                            <h6 class="fw-bold mb-0">John Davis</h6>
                            <small class="text-muted">Operations Manager</small>
                        </div>
                    </div>
                    <p class="text-muted mb-0">"CORRES has transformed how we handle correspondence. What used to take days now takes hours. The tracking feature alone has saved us countless hours."</p>
                    <div class="mt-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up animate-delay-2">
                <div class="testimonial-card h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="testimonial-avatar me-3">SM</div>
                        <div>
                            <h6 class="fw-bold mb-0">Sarah Mitchell</h6>
                            <small class="text-muted">Director of Administration</small>
                        </div>
                    </div>
                    <p class="text-muted mb-0">"The security features give us peace of mind. We deal with sensitive documents daily, and CORRES ensures everything is protected and compliant."</p>
                    <div class="mt-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 animate-fade-in-up animate-delay-3">
                <div class="testimonial-card h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="testimonial-avatar me-3">RK</div>
                        <div>
                            <h6 class="fw-bold mb-0">Robert Kim</h6>
                            <small class="text-muted">IT Director</small>
                        </div>
                    </div>
                    <p class="text-muted mb-0">"Implementation was seamless and the support team is exceptional. Our staff was up and running within a day. Highly recommend!"</p>
                    <div class="mt-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5 animate-fade-in-up">
                    <h6 class="text-uppercase text-primary fw-bold mb-2">FAQ</h6>
                    <h2 class="fw-bold display-5 mb-3">Frequently Asked Questions</h2>
                    <p class="text-muted lead">Got questions? We've got answers</p>
                </div>
                
                <div class="accordion animate-fade-in-up animate-delay-1" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                How do I get started with CORRES?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Getting started is easy! Contact your system administrator for login credentials. Once you have access, you can immediately start tracking and managing correspondence through the intuitive dashboard.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                Is my data secure with CORRES?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely! CORRES uses bank-level encryption, secure access controls, and comprehensive audit trails. We comply with industry security standards and regular security updates to ensure your data is always protected.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                Can I access CORRES from mobile devices?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! CORRES is fully responsive and works seamlessly on desktop, tablet, and mobile devices. Access your correspondence anytime, anywhere with an internet connection.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                What kind of support do you offer?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We provide 24/7 online support through email and our support portal. Enterprise clients have access to priority support with dedicated account managers and phone support.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                <i class="bi bi-question-circle text-primary me-2"></i>
                                Can I integrate CORRES with other systems?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, CORRES supports API integrations and can be connected with your existing enterprise systems such as ERP, CRM, and document management platforms. Contact us for custom integration options.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5">
    <div class="container cta-content">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8 animate-fade-in-up">
                <h2 class="display-5 fw-bold mb-4">Ready to Transform Your Correspondence Management?</h2>
                <p class="lead mb-4 opacity-75">Join hundreds of organizations already using CORRES to streamline their document workflows and boost productivity.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="<?= base_url('login') ?>" class="btn cta-btn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Access System
                    </a>
                    <a href="<?= base_url('contact') ?>" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-envelope me-2"></i>Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>