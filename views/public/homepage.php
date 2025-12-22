<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System - Excellence in Education</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            opacity: 0.1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .stats-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .testimonial-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 15px;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .section-title {
            font-weight: 700;
            margin-bottom: 50px;
            position: relative;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="/">
                <i class="fas fa-school me-2"></i>School MS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#courses">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#events">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#gallery">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/login" class="btn btn-outline-primary me-2">Login</a>
                    <a href="/admission" class="btn btn-primary">Admission</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <?php if (!empty($hero_content)): ?>
                        <?php foreach ($hero_content as $content): ?>
                            <h1 class="display-4 fw-bold mb-4">
                                <?php echo htmlspecialchars($content['title']); ?>
                            </h1>
                            <?php if ($content['content']): ?>
                                <p class="lead mb-4">
                                    <?php echo nl2br(htmlspecialchars($content['content'])); ?>
                                </p>
                            <?php endif; ?>
                            <div class="d-flex flex-wrap gap-3">
                                <?php if ($content['link_url'] && $content['link_text']): ?>
                                    <a href="<?php echo htmlspecialchars($content['link_url']); ?>" class="btn btn-light btn-custom btn-lg">
                                        <i class="fas fa-graduation-cap me-2"></i><?php echo htmlspecialchars($content['link_text']); ?>
                                    </a>
                                <?php else: ?>
                                    <a href="/admission" class="btn btn-light btn-custom btn-lg">
                                        <i class="fas fa-graduation-cap me-2"></i>Apply Now
                                    </a>
                                <?php endif; ?>
                                <a href="#about" class="btn btn-outline-light btn-custom btn-lg">
                                    <i class="fas fa-info-circle me-2"></i>Learn More
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <h1 class="display-4 fw-bold mb-4">
                            Excellence in <span class="text-warning">Education</span>
                        </h1>
                        <p class="lead mb-4">
                            Empowering students with modern education, innovative teaching methods,
                            and comprehensive academic management for a brighter future.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="/admission" class="btn btn-light btn-custom btn-lg">
                                <i class="fas fa-graduation-cap me-2"></i>Apply Now
                            </a>
                            <a href="#about" class="btn btn-outline-light btn-custom btn-lg">
                                <i class="fas fa-info-circle me-2"></i>Learn More
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="position-relative">
                        <?php
                        $heroImage = '';
                        if (!empty($hero_content)) {
                            foreach ($hero_content as $content) {
                                if ($content['image_path']) {
                                    $heroImage = $content['image_path'];
                                    break;
                                }
                            }
                        }
                        ?>
                        <img src="<?php echo $heroImage ?: 'https://via.placeholder.com/500x400/667eea/ffffff?text=School+Building'; ?>"
                             class="img-fluid rounded shadow-lg" alt="School Building">
                        <div class="position-absolute top-0 end-0 bg-white rounded-circle p-3 shadow">
                            <i class="fas fa-award text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Why Choose Our School?</h2>
                <p class="text-muted">Discover what makes us the preferred choice for quality education</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-chalkboard-teacher fa-2x"></i>
                            </div>
                            <h5 class="card-title">Expert Faculty</h5>
                            <p class="card-text">Learn from experienced educators who are passionate about student success and innovative teaching methods.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-flask fa-2x"></i>
                            </div>
                            <h5 class="card-title">Modern Facilities</h5>
                            <p class="card-text">State-of-the-art laboratories, smart classrooms, sports facilities, and digital learning resources.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                            <h5 class="card-title">Holistic Development</h5>
                            <p class="card-text">Comprehensive curriculum focusing on academic excellence, sports, arts, and character development.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <h2 class="display-4 fw-bold mb-2">500+</h2>
                    <p class="mb-0">Students Enrolled</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h2 class="display-4 fw-bold mb-2">50+</h2>
                    <p class="mb-0">Expert Teachers</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h2 class="display-4 fw-bold mb-2">25+</h2>
                    <p class="mb-0">Years Experience</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h2 class="display-4 fw-bold mb-2">95%</h2>
                    <p class="mb-0">Success Rate</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <?php if (!empty($about_content)): ?>
                        <?php foreach ($about_content as $content): ?>
                            <h2 class="section-title text-start"><?php echo htmlspecialchars($content['title']); ?></h2>
                            <?php if ($content['content']): ?>
                                <p class="lead text-muted mb-4">
                                    <?php echo nl2br(htmlspecialchars($content['content'])); ?>
                                </p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <h2 class="section-title text-start">About Our School</h2>
                        <p class="lead text-muted mb-4">
                            Founded with a vision to provide quality education that nurtures young minds
                            and prepares them for the challenges of tomorrow.
                        </p>
                    <?php endif; ?>
                    <p class="mb-4">
                        Our school combines traditional values with modern teaching methodologies,
                        creating an environment where students can thrive academically, socially, and personally.
                        We believe in holistic development and provide opportunities for students to explore
                        their interests and develop their talents.
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Qualified Teachers</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Modern Facilities</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Sports & Arts</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Digital Learning</span>
                            </div>
                        </div>
                    </div>
                    <a href="#contact" class="btn btn-primary btn-custom">Contact Us</a>
                </div>
                <div class="col-lg-6">
                    <?php
                    $aboutImage = '';
                    if (!empty($about_content)) {
                        foreach ($about_content as $content) {
                            if ($content['image_path']) {
                                $aboutImage = $content['image_path'];
                                break;
                            }
                        }
                    }
                    ?>
                    <img src="<?php echo $aboutImage ?: 'https://via.placeholder.com/600x400/764ba2/ffffff?text=School+Campus'; ?>"
                         class="img-fluid rounded shadow-lg" alt="School Campus">
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="py-5 bg-light" id="courses">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Our Courses</h2>
                <p class="text-muted">Comprehensive curriculum designed for academic excellence</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-book fa-lg"></i>
                            </div>
                            <h6 class="card-title">Primary Education</h6>
                            <p class="card-text small">Classes 1-5 with focus on foundational learning and creativity.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-atom fa-lg"></i>
                            </div>
                            <h6 class="card-title">Middle School</h6>
                            <p class="card-text small">Classes 6-8 with comprehensive subjects and skill development.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-graduation-cap fa-lg"></i>
                            </div>
                            <h6 class="card-title">Secondary School</h6>
                            <p class="card-text small">Classes 9-10 with board preparation and career guidance.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-university fa-lg"></i>
                            </div>
                            <h6 class="card-title">Senior Secondary</h6>
                            <p class="card-text small">Classes 11-12 with specialized streams and entrance preparation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">What Parents Say</h2>
                <p class="text-muted">Hear from our satisfied parents and students</p>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://via.placeholder.com/60x60/667eea/ffffff?text=P"
                                 class="rounded-circle me-3" alt="Parent">
                            <div>
                                <h6 class="mb-0">Mrs. Sharma</h6>
                                <small class="text-muted">Parent</small>
                            </div>
                        </div>
                        <p class="mb-0">"The school's focus on holistic development has helped my child grow not just academically but also personally. The teachers are excellent and the facilities are world-class."</p>
                        <div class="mt-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://via.placeholder.com/60x60/764ba2/ffffff?text=S"
                                 class="rounded-circle me-3" alt="Student">
                            <div>
                                <h6 class="mb-0">Rahul Kumar</h6>
                                <small class="text-muted">Class 10 Student</small>
                            </div>
                        </div>
                        <p class="mb-0">"I've learned so much here. The teachers make learning fun and interesting. The extracurricular activities have helped me discover my passion for sports."</p>
                        <div class="mt-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://via.placeholder.com/60x60/f093fb/ffffff?text=T"
                                 class="rounded-circle me-3" alt="Teacher">
                            <div>
                                <h6 class="mb-0">Mr. Patel</h6>
                                <small class="text-muted">Mathematics Teacher</small>
                            </div>
                        </div>
                        <p class="mb-0">"Teaching here has been incredibly rewarding. The school supports professional development and provides all the resources needed for effective teaching."</p>
                        <div class="mt-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5 bg-light" id="contact">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Get In Touch</h2>
                <p class="text-muted">Ready to join our educational community? Contact us today!</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">Contact Information</h5>
                            <div class="d-flex mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Address</h6>
                                    <p class="text-muted mb-0">123 Education Street, Knowledge City, State - 123456</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Phone</h6>
                                    <p class="text-muted mb-0">+91 12345 67890</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Email</h6>
                                    <p class="text-muted mb-0">info@schoolmanagement.com</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Office Hours</h6>
                                    <p class="text-muted mb-0">Mon - Sat: 8:00 AM - 6:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">Send us a Message</h5>
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Your Name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" placeholder="Your Email" required>
                                    </div>
                                    <div class="col-12">
                                        <input type="text" class="form-control" placeholder="Subject" required>
                                    </div>
                                    <div class="col-12">
                                        <textarea class="form-control" rows="4" placeholder="Your Message" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-custom w-100">
                                            <i class="fas fa-paper-plane me-2"></i>Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-3">
                        <i class="fas fa-school me-2"></i>School Management System
                    </h5>
                    <p class="text-muted mb-3">
                        Providing quality education and nurturing young minds for over 25 years.
                        Join our community of learners and achievers.
                    </p>
                    <div class="d-flex">
                        <a href="#" class="text-white me-3 fs-5"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3 fs-5"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3 fs-5"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3 fs-5"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#about" class="text-muted text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="#courses" class="text-muted text-decoration-none">Courses</a></li>
                        <li class="mb-2"><a href="#events" class="text-muted text-decoration-none">Events</a></li>
                        <li class="mb-2"><a href="#gallery" class="text-muted text-decoration-none">Gallery</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="mb-3">Academic Programs</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Primary Education</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Middle School</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Secondary School</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Senior Secondary</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="mb-3">Contact Info</h6>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>123 Education Street</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i>+91 12345 67890</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@schoolmanagement.com</li>
                        <li class="mb-2"><i class="fas fa-clock me-2"></i>Mon-Sat: 8AM-6PM</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; 2024 School Management System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/login" class="text-muted text-decoration-none me-3">Admin Login</a>
                    <a href="/privacy" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="/terms" class="text-muted text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-white', 'shadow');
                navbar.classList.remove('bg-transparent');
            } else {
                navbar.classList.remove('bg-white', 'shadow');
                navbar.classList.add('bg-transparent');
            }
        });
    </script>
</body>
</html>