<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Contact Us</h1>
                <p class="lead mb-4">
                    Get in touch with us for admissions, inquiries, or any questions about our school.
                </p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-phone fa-2x text-white me-3"></i>
                            <div>
                                <strong>Call Us</strong><br>
                                <small>+91-XXXXXXXXXX</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope fa-2x text-white me-3"></i>
                            <div>
                                <strong>Email Us</strong><br>
                                <small>info@school.com</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-white text-dark shadow">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">Quick Contact</h4>
                        <form id="quickContactForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" placeholder="Your Name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" class="form-control" placeholder="Your Email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Subject" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="3" placeholder="Your Message" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Contact Details -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            <i class="fas fa-map-marker-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Visit Our Campus</h5>
                        <p class="card-text">
                            A.s.higher secondary school<br>
                            School Address, City, State - PIN<br>
                            India
                        </p>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-directions me-1"></i>Get Directions
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            <i class="fas fa-clock fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title">Office Hours</h5>
                        <div class="text-start">
                            <p class="mb-2"><strong>Monday - Friday:</strong><br>8:00 AM - 4:00 PM</p>
                            <p class="mb-2"><strong>Saturday:</strong><br>8:00 AM - 12:00 PM</p>
                            <p class="mb-0"><strong>Sunday:</strong><br>Closed</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            <i class="fas fa-phone fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title">Contact Numbers</h5>
                        <p class="card-text">
                            <strong>Principal:</strong><br>
                            +91-XXXXXXXXXX<br><br>
                            <strong>Admission Office:</strong><br>
                            +91-XXXXXXXXXX<br><br>
                            <strong>General Inquiry:</strong><br>
                            +91-XXXXXXXXXX
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Contact Form -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0">Send us a Message</h3>
                        <p class="mb-0">We'd love to hear from you</p>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        $flash_message = $session->getFlash('message');
                        $flash_type = $session->getFlash('message_type') ?: 'info';
                        $errors = $session->getFlash('errors') ?? [];
                        $old_input = $session->getFlash('old_input') ?? [];
                        ?>

                        <?php if ($flash_message): ?>
                            <div class="alert alert-<?php echo $flash_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo $flash_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/contact">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                           id="name" name="name" value="<?php echo htmlspecialchars($old_input['name'] ?? ''); ?>" required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['name'][0]; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                                           id="email" name="email" value="<?php echo htmlspecialchars($old_input['email'] ?? ''); ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['email'][0]; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?php echo htmlspecialchars($old_input['phone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <select class="form-select <?php echo isset($errors['subject']) ? 'is-invalid' : ''; ?>"
                                            id="subject" name="subject" required>
                                        <option value="">Select Subject</option>
                                        <option value="Admission Inquiry" <?php echo ($old_input['subject'] ?? '') === 'Admission Inquiry' ? 'selected' : ''; ?>>Admission Inquiry</option>
                                        <option value="General Information" <?php echo ($old_input['subject'] ?? '') === 'General Information' ? 'selected' : ''; ?>>General Information</option>
                                        <option value="Fee Structure" <?php echo ($old_input['subject'] ?? '') === 'Fee Structure' ? 'selected' : ''; ?>>Fee Structure</option>
                                        <option value="Academic Programs" <?php echo ($old_input['subject'] ?? '') === 'Academic Programs' ? 'selected' : ''; ?>>Academic Programs</option>
                                        <option value="Transportation" <?php echo ($old_input['subject'] ?? '') === 'Transportation' ? 'selected' : ''; ?>>Transportation</option>
                                        <option value="Other" <?php echo ($old_input['subject'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <?php if (isset($errors['subject'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['subject'][0]; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control <?php echo isset($errors['message']) ? 'is-invalid' : ''; ?>"
                                          id="message" name="message" rows="5" required><?php echo htmlspecialchars($old_input['message'] ?? ''); ?></textarea>
                                <?php if (isset($errors['message'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['message'][0]; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                    <label class="form-check-label" for="newsletter">
                                        Subscribe to our newsletter for updates and announcements
                                    </label>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">Find Us on Map</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="ratio ratio-16x9">
