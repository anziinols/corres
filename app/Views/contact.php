<?= $this->extend('templates/public_template') ?>

<?= $this->section('title') ?>Contact Us<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #1A3A5F 0%, #0F2C44 100%);">
    <div class="container">
        <div class="row align-items-center text-white">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Get in Touch</h1>
                <p class="lead mb-4">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Contact Info -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Contact Information</h4>
                        
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <i class="bi bi-envelope-fill text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1">Email</h6>
                                <p class="text-muted mb-0">support@dakoiims.com</p>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <i class="bi bi-globe text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1">Website</h6>
                                <p class="text-muted mb-0">
                                    <a href="https://www.dakoiims.com/" target="_blank" rel="noopener noreferrer" class="text-decoration-none">www.dakoiims.com</a>
                                </p>
                            </div>
                        </div>
                        
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-clock-fill text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1">Support Hours</h6>
                                <p class="text-muted mb-0">24/7 Online Support</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Send us a Message</h4>
                        
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('contact/submit') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" required value="<?= old('name') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required value="<?= old('email') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject *</label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Select a subject...</option>
                                    <option value="General Inquiry" <?= old('subject') == 'General Inquiry' ? 'selected' : '' ?>>General Inquiry</option>
                                    <option value="Technical Support" <?= old('subject') == 'Technical Support' ? 'selected' : '' ?>>Technical Support</option>
                                    <option value="Demo Request" <?= old('subject') == 'Demo Request' ? 'selected' : '' ?>>Request a Demo</option>
                                    <option value="Partnership" <?= old('subject') == 'Partnership' ? 'selected' : '' ?>>Partnership Opportunity</option>
                                    <option value="Other" <?= old('subject') == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?= old('message') ?></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Preview Section -->
<section class="py-5" style="background-color: #F8F9FA;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Frequently Asked Questions</h2>
            <p class="text-muted">Quick answers to common questions</p>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2"><i class="bi bi-question-circle-fill text-primary me-2"></i>How do I get started?</h6>
                        <p class="text-muted mb-0">Simply click the "Get Started" button and log in with your credentials. If you need an account, contact your administrator.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2"><i class="bi bi-question-circle-fill text-primary me-2"></i>Is my data secure?</h6>
                        <p class="text-muted mb-0">Yes! We use industry-standard encryption and security measures to protect your correspondence and documents.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2"><i class="bi bi-question-circle-fill text-primary me-2"></i>Can I request a demo?</h6>
                        <p class="text-muted mb-0">Absolutely! Use the contact form above and select "Request a Demo" as the subject, and our team will reach out.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2"><i class="bi bi-question-circle-fill text-primary me-2"></i>What support options are available?</h6>
                        <p class="text-muted mb-0">We offer 24/7 online support through email and our support portal. Premium support plans are available for enterprise clients.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>