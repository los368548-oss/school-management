<?php
// Extract data
$carousel_images = $carousel_images ?? [];
$about_content = $about_content ?? [];
$about = !empty($about_content) ? $about_content[0] : null;
$courses_content = $courses_content ?? [];
$upcoming_events = $upcoming_events ?? [];
$gallery_images = $gallery_images ?? [];
$testimonials = $testimonials ?? [];
?>

<!-- Hero Carousel -->
<?php if (!empty($carousel_images)): ?>
<section class="hero-carousel">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php foreach ($carousel_images as $index => $image): ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo $index; ?>"
                        class="<?php echo $index === 0 ? 'active' : ''; ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($carousel_images as $index => $image): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($image['image_path'] ?? ''); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($image['title'] ?? ''); ?>">
                    <div class="carousel-caption d-none d-md-block">
                        <h1><?php echo htmlspecialchars($image['title'] ?? ''); ?></h1>
                        <p><?php echo htmlspecialchars($image['content'] ?? ''); ?></p>
                        <?php if (!empty($image['link_url'])): ?>
                            <a href="<?php echo htmlspecialchars($image['link_url']); ?>" class="btn btn-primary btn-lg">Learn More</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>
<?php endif; ?>

<!-- About Section -->
<?php if ($about): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <?php if (!empty($about['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($about['image_path']); ?>" alt="About Us" class="img-fluid rounded shadow">
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <h2 class="mb-4"><?php echo htmlspecialchars($about['title'] ?? 'About A.s.higher secondary school'); ?></h2>
                <p class="lead"><?php echo nl2br(htmlspecialchars($about['content'] ?? '')); ?></p>
                <a href="/about" class="btn btn-primary">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Courses Section -->
<?php if (!empty($courses_content)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4">Our Courses</h2>
            <p class="lead">Comprehensive education programs designed for excellence</p>
        </div>
        <div class="row">
            <?php foreach ($courses_content as $course): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($course['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars($course['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($course['content'] ?? '', 0, 150)) . (strlen($course['content'] ?? '') > 150 ? '...' : ''); ?></p>
                        </div>
                        <div class="card-footer">
                            <a href="/courses" class="btn btn-primary btn-sm">Learn More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Events Section -->
<?php if (!empty($upcoming_events)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4">Upcoming Events</h2>
            <p class="lead">Stay connected with our school activities</p>
        </div>
        <div class="row">
            <?php foreach ($upcoming_events as $event): ?>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($event['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars($event['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="card-text">
                                <i class="fas fa-calendar me-2"></i><?php echo date('d M Y', strtotime($event['event_date'])); ?>
                                <?php if (!empty($event['location'])): ?>
                                    <br><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($event['location']); ?>
                                <?php endif; ?>
                            </p>
                            <p class="card-text"><?php echo htmlspecialchars(substr($event['description'] ?? '', 0, 100)) . (strlen($event['description'] ?? '') > 100 ? '...' : ''); ?></p>
                        </div>
                        <div class="card-footer">
                            <a href="/events" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery Section -->
<?php if (!empty($gallery_images)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4">Photo Gallery</h2>
            <p class="lead">Capturing memorable moments at our school</p>
        </div>
        <div class="row">
            <?php foreach (array_slice($gallery_images, 0, 6) as $image): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="gallery-item">
                        <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>" class="img-fluid rounded shadow">
                        <div class="gallery-overlay">
                            <div class="gallery-info">
                                <h6><?php echo htmlspecialchars($image['title']); ?></h6>
                                <?php if (!empty($image['category'])): ?>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($image['category']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center">
            <a href="/gallery" class="btn btn-primary btn-lg">View Full Gallery</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials Section -->
<?php if (!empty($testimonials)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4">What Parents Say</h2>
            <p class="lead">Hear from our community</p>
        </div>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="text-center">
                            <blockquote class="blockquote">
                                <p class="mb-4 fs-5">"<?php echo htmlspecialchars($testimonial['content'] ?? ''); ?>"</p>
                                <footer class="blockquote-footer">
                                    <strong><?php echo htmlspecialchars($testimonial['title'] ?? ''); ?></strong>
                                </footer>
                            </blockquote>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="display-4 mb-4">Ready to Join Our Community?</h2>
        <p class="lead mb-4">Discover the difference quality education makes</p>
        <div class="row justify-content-center">
            <div class="col-md-4 mb-3">
                <a href="/contact" class="btn btn-light btn-lg w-100">
                    <i class="fas fa-envelope me-2"></i>Contact Us
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="/courses" class="btn btn-outline-light btn-lg w-100">
                    <i class="fas fa-graduation-cap me-2"></i>Our Programs
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.hero-carousel .carousel-item {
    height: 70vh;
    min-height: 400px;
}

.hero-carousel .carousel-item img {
    height: 100%;
    object-fit: cover;
}

.hero-carousel .carousel-caption {
    bottom: 3rem;
}

.hero-carousel .carousel-caption h1 {
    font-size: 3rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.hero-carousel .carousel-caption p {
    font-size: 1.25rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 0.375rem;
    cursor: pointer;
}

.gallery-item img {
    transition: transform 0.3s ease;
}

.gallery-item:hover img {
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-info {
    color: white;
    text-align: center;
}

.gallery-info h6 {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .hero-carousel .carousel-item {
        height: 50vh;
        min-height: 300px;
    }

    .hero-carousel .carousel-caption h1 {
        font-size: 2rem;
    }
}
</style>