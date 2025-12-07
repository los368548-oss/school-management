<?php
// Extract data
$upcoming_events = $upcoming_events ?? [];
$past_events = $past_events ?? [];
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">School Events</h1>
        <p class="lead mb-4">Stay connected with our school community through exciting events and activities</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">
    <!-- Upcoming Events -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4 text-primary">
                <i class="fas fa-calendar-alt me-2"></i>Upcoming Events
            </h2>

            <?php if (!empty($upcoming_events)): ?>
                <div class="row">
                    <?php foreach ($upcoming_events as $event): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card h-100 shadow-sm card-hover">
                                <?php if ($event['image_path']): ?>
                                    <img src="<?php echo htmlspecialchars($event['image_path']); ?>"
                                         class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-primary d-flex align-items-center justify-content-center"
                                         style="height: 200px;">
                                        <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                        <?php if ($event['location']): ?>
                                            <br><i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($event['location']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="card-text flex-grow-1">
                                        <?php echo htmlspecialchars(substr($event['description'] ?? '', 0, 120)); ?>
                                        <?php if (strlen($event['description'] ?? '') > 120): ?>...<?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Upcoming Events</h4>
                    <p class="text-muted">Check back later for upcoming school events and activities.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Past Events -->
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 text-secondary">
                <i class="fas fa-history me-2"></i>Past Events
            </h2>

            <?php if (!empty($past_events)): ?>
                <div class="row">
                    <?php foreach ($past_events as $event): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <?php if ($event['image_path']): ?>
                                    <img src="<?php echo htmlspecialchars($event['image_path']); ?>"
                                         class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>"
                                         style="height: 200px; object-fit: cover; filter: grayscale(50%);">
                                <?php else: ?>
                                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center"
                                         style="height: 200px;">
                                        <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                        <?php if ($event['location']): ?>
                                            <br><i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($event['location']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="card-text flex-grow-1">
                                        <?php echo htmlspecialchars(substr($event['description'] ?? '', 0, 120)); ?>
                                        <?php if (strlen($event['description'] ?? '') > 120): ?>...<?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-history fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Past Events</h4>
                    <p class="text-muted">Past events will be displayed here once they occur.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Call to Action -->
<section class="bg-light py-5">
    <div class="container text-center">
        <h3 class="mb-3">Stay Updated</h3>
        <p class="lead mb-4">Never miss an important school event. Subscribe to our newsletter for regular updates.</p>
        <a href="/contact" class="btn btn-primary btn-lg">
            <i class="fas fa-envelope me-2"></i>Contact Us
        </a>
    </div>
</section>