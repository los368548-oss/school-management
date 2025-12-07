<?php
// Extract data
$images = $images ?? [];
$categories = $categories ?? [];
$stats = $stats ?? ['total_images' => 0, 'total_categories' => 0, 'recent_uploads' => 0, 'last_upload' => null];
?>

<div class="row">
    <!-- Gallery Statistics -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Images
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['total_images']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-images fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Categories
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['total_categories']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tags fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Recent Uploads
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo number_format($stats['recent_uploads']); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Last Upload
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $stats['last_upload'] ? date('d M Y', strtotime($stats['last_upload'])) : 'Never'; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Images -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gallery Images</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleView('grid')">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleView('list')">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($images)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No images found</h5>
                        <p class="text-muted">Start by uploading some images to your gallery.</p>
                        <a href="/admin/gallery/upload" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Upload First Images
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Category Filter -->
                    <div class="mb-3">
                        <select class="form-select form-select-sm" id="categoryFilter" onchange="filterByCategory(this.value)">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['category']); ?>">
                                    <?php echo htmlspecialchars($category['category']); ?> (<?php echo $category['image_count']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Images Grid -->
                    <div id="imagesContainer" class="row">
                        <?php foreach ($images as $image): ?>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4 image-item"
                                 data-category="<?php echo htmlspecialchars($image['category'] ?? ''); ?>">
                                <div class="card h-100">
                                    <div class="position-relative">
                                        <img src="<?php echo htmlspecialchars($image['image_path']); ?>"
                                             class="card-img-top" alt="<?php echo htmlspecialchars($image['title']); ?>"
                                             style="height: 150px; object-fit: cover;">
                                        <div class="position-absolute top-0 end-0 p-1">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="deleteImage(<?php echo $image['id']; ?>, '<?php echo htmlspecialchars($image['title']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1" title="<?php echo htmlspecialchars($image['title']); ?>">
                                            <?php echo htmlspecialchars(strlen($image['title']) > 20 ? substr($image['title'], 0, 20) . '...' : $image['title']); ?>
                                        </h6>
                                        <?php if ($image['category']): ?>
                                            <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($image['category']); ?></span>
                                        <?php endif; ?>
                                        <div class="small text-muted">
                                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($image['uploaded_by_name'] ?: 'Unknown'); ?>
                                            <br>
                                            <i class="fas fa-calendar me-1"></i><?php echo date('d M Y', strtotime($image['upload_date'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function deleteImage(imageId, imageTitle) {
    if (confirm(`Are you sure you want to delete "${imageTitle}"? This action cannot be undone.`)) {
        fetch(`/admin/gallery/${imageId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the image.');
        });
    }
}

function filterByCategory(category) {
    const items = document.querySelectorAll('.image-item');

    items.forEach(item => {
        if (!category || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function toggleView(viewType) {
    const container = document.getElementById('imagesContainer');

    if (viewType === 'list') {
        container.className = 'list-group';
        // Would need to restructure for list view - simplified for now
    } else {
        container.className = 'row';
    }
}
</script>