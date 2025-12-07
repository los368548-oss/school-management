<?php
// Extract data
$categories = $categories ?? [];
$gallery_images = $gallery_images ?? [];
$selected_category = $_GET['category'] ?? null;
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">School Gallery</h1>
        <p class="lead mb-4">Explore our memorable moments and school activities through photos</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">
    <!-- Category Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filter by Category</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="/gallery" class="btn <?php echo !$selected_category ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <i class="fas fa-th-large me-1"></i>All
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <?php if ($category['category']): ?>
                                <a href="/gallery?category=<?php echo urlencode($category['category']); ?>"
                                   class="btn <?php echo $selected_category === $category['category'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars(ucfirst($category['category'])); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="row">
        <?php if (!empty($gallery_images)): ?>
            <?php foreach ($gallery_images as $image): ?>
                <?php
                // Skip if category filter is active and doesn't match
                if ($selected_category && $image['category'] !== $selected_category) {
                    continue;
                }
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm card-hover">
                        <div class="position-relative overflow-hidden">
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>"
                                 class="card-img-top gallery-image"
                                 alt="<?php echo htmlspecialchars($image['title']); ?>"
                                 style="height: 250px; object-fit: cover; transition: transform 0.3s ease;"
                                 data-bs-toggle="modal"
                                 data-bs-target="#imageModal"
                                 data-image="<?php echo htmlspecialchars($image['image_path']); ?>"
                                 data-title="<?php echo htmlspecialchars($image['title']); ?>"
                                 data-description="<?php echo htmlspecialchars($image['description'] ?? ''); ?>"
                                 data-category="<?php echo htmlspecialchars($image['category'] ?? ''); ?>"
                                 data-date="<?php echo date('F j, Y', strtotime($image['upload_date'])); ?>">

                            <!-- Overlay with zoom icon -->
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 transition-opacity">
                                <i class="fas fa-search-plus fa-2x text-white"></i>
                            </div>
                        </div>

                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($image['title']); ?></h6>
                            <?php if ($image['category']): ?>
                                <span class="badge bg-primary mb-2"><?php echo htmlspecialchars(ucfirst($image['category'])); ?></span>
                            <?php endif; ?>
                            <?php if ($image['description']): ?>
                                <p class="card-text small text-muted">
                                    <?php echo htmlspecialchars(substr($image['description'], 0, 100)); ?>
                                    <?php if (strlen($image['description']) > 100): ?>...<?php endif; ?>
                                </p>
                            <?php endif; ?>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i><?php echo date('M j, Y', strtotime($image['upload_date'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Images Found</h4>
                    <p class="text-muted">
                        <?php if ($selected_category): ?>
                            No images found in the "<?php echo htmlspecialchars(ucfirst($selected_category)); ?>" category.
                            <a href="/gallery">View all images</a>
                        <?php else: ?>
                            Our gallery is being updated. Check back soon for new photos!
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid rounded">
                <div class="mt-3">
                    <p id="modalDescription" class="mb-2"></p>
                    <div id="modalMeta" class="small text-muted"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<section class="bg-light py-5">
    <div class="container text-center">
        <h3 class="mb-3">Share Your Moments</h3>
        <p class="lead mb-4">Have photos from school events? We'd love to feature them in our gallery.</p>
        <a href="/contact" class="btn btn-primary btn-lg">
            <i class="fas fa-envelope me-2"></i>Contact Us
        </a>
    </div>
</section>

<script>
// Gallery hover effects
document.querySelectorAll('.gallery-image').forEach(img => {
    const overlay = img.nextElementSibling;

    img.addEventListener('mouseenter', () => {
        overlay.style.opacity = '1';
        img.style.transform = 'scale(1.05)';
    });

    img.addEventListener('mouseleave', () => {
        overlay.style.opacity = '0';
        img.style.transform = 'scale(1)';
    });
});

// Modal functionality
document.getElementById('imageModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const image = button.getAttribute('data-image');
    const title = button.getAttribute('data-title');
    const description = button.getAttribute('data-description');
    const category = button.getAttribute('data-category');
    const date = button.getAttribute('data-date');

    document.getElementById('modalImage').src = image;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDescription').textContent = description;

    let metaText = date;
    if (category) {
        metaText += ' • ' + category.charAt(0).toUpperCase() + category.slice(1);
    }
    document.getElementById('modalMeta').textContent = metaText;
});
</script>

<style>
.transition-opacity {
    transition: opacity 0.3s ease;
}

.gallery-image {
    cursor: pointer;
}
</style>