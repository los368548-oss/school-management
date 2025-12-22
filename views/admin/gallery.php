<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 0.75rem 1rem;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: #0d6efd;
        }
        .content-wrapper {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            .content-wrapper {
                margin-left: 250px;
            }
        }
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .gallery-item:hover {
            transform: translateY(-2px);
        }
        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .upload-area.dragover {
            border-color: #0d6efd;
            background-color: #e3f2fd;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar d-md-block collapse" id="sidebar">
        <div class="sidebar-sticky">
            <div class="p-3">
                <h5 class="text-white mb-4">
                    <i class="fas fa-school"></i> School Admin
                </h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/students">
                            <i class="fas fa-users"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/classes">
                            <i class="fas fa-chalkboard"></i> Classes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/attendance">
                            <i class="fas fa-calendar-check"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/exams">
                            <i class="fas fa-file-alt"></i> Exams
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fees">
                            <i class="fas fa-money-bill"></i> Fees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/events">
                            <i class="fas fa-calendar"></i> Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/gallery">
                            <i class="fas fa-images"></i> Gallery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/reports">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/settings">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
            <div class="p-3 border-top border-secondary">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle fa-2x text-white me-2"></i>
                    <div>
                        <small class="text-white">Admin</small><br>
                        <a href="/logout" class="text-white-50 small">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand mb-0 h1">Gallery Management</span>
                <div class="d-flex">
                    <span class="badge bg-info me-2">
                        Academic Year: <?php echo htmlspecialchars($academic_year['year_name'] ?? 'Not Set'); ?>
                    </span>
                    <a href="/admin/select-academic-year" class="btn btn-sm btn-outline-primary">Change Year</a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Photo Gallery</h2>
                    <p class="text-muted">Manage school photos, event galleries, and media content</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fas fa-upload"></i> Upload Photos
                    </button>
                </div>
            </div>

            <!-- Gallery Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-images fa-2x mb-2"></i>
                            <h4 class="card-title"><?php echo $gallery_stats['total_images'] ?? 0; ?></h4>
                            <p class="card-text">Total Images</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-star fa-2x mb-2"></i>
                            <h4 class="card-title"><?php echo $gallery_stats['featured_images'] ?? 0; ?></h4>
                            <p class="card-text">Featured Images</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar fa-2x mb-2"></i>
                            <h4 class="card-title"><?php echo $gallery_stats['event_galleries'] ?? 0; ?></h4>
                            <p class="card-text">Event Galleries</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-folder fa-2x mb-2"></i>
                            <h4 class="card-title"><?php echo $gallery_stats['categories'] ?? 0; ?></h4>
                            <p class="card-text">Categories</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                <option value="general">General</option>
                                <option value="academic">Academic</option>
                                <option value="sports">Sports</option>
                                <option value="cultural">Cultural</option>
                                <option value="events">Events</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Event</label>
                            <select class="form-select" id="eventFilter">
                                <option value="">All Events</option>
                                <!-- Events will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search by title...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">View</label>
                            <div class="btn-group w-100">
                                <button class="btn btn-outline-secondary active" id="gridView">
                                    <i class="fas fa-th"></i> Grid
                                </button>
                                <button class="btn btn-outline-secondary" id="listView">
                                    <i class="fas fa-list"></i> List
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div id="galleryGrid">
                <div class="row g-4" id="galleryContainer">
                    <?php if (!empty($gallery)): ?>
                        <?php foreach ($gallery as $image): ?>
                            <div class="col-md-3 col-sm-6 gallery-item-container" data-category="<?php echo htmlspecialchars($image['category']); ?>">
                                <div class="gallery-item">
                                    <img src="/uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>"
                                         alt="<?php echo htmlspecialchars($image['title']); ?>"
                                         onerror="this.src='https://via.placeholder.com/300x200/6c757d/ffffff?text=No+Image'">
                                    <div class="gallery-overlay">
                                        <div class="text-center">
                                            <button class="btn btn-light btn-sm me-2" onclick="viewImage(<?php echo $image['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-light btn-sm me-2" onclick="editImage(<?php echo $image['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteImage(<?php echo $image['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php if ($image['is_featured']): ?>
                                        <div class="position-absolute top-0 end-0 bg-warning text-dark px-2 py-1 rounded">
                                            <i class="fas fa-star"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-2">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($image['title']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($image['category']); ?>
                                        <?php if ($image['event_title']): ?>
                                            • <?php echo htmlspecialchars($image['event_title']); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-images fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No images in gallery</h4>
                                <p class="text-muted">Upload some photos to get started</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-upload"></i> Upload First Photo
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Gallery List View (Hidden by default) -->
            <div id="galleryList" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Preview</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Event</th>
                                <th>Featured</th>
                                <th>Uploaded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($gallery)): ?>
                                <?php foreach ($gallery as $image): ?>
                                    <tr>
                                        <td>
                                            <img src="/uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>"
                                                 class="rounded" width="60" height="40" style="object-fit: cover;"
                                                 onerror="this.src='https://via.placeholder.com/60x40/6c757d/ffffff?text=No+Img'">
                                        </td>
                                        <td><?php echo htmlspecialchars($image['title']); ?></td>
                                        <td><?php echo htmlspecialchars($image['category']); ?></td>
                                        <td><?php echo htmlspecialchars($image['event_title'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($image['is_featured']): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($image['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewImage(<?php echo $image['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" onclick="editImage(<?php echo $image['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteImage(<?php echo $image['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Photos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h5>Drag & Drop Photos Here</h5>
                                    <p class="text-muted">or click to browse files</p>
                                    <input type="file" id="fileInput" name="images[]" multiple accept="image/*" style="display: none;">
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                                        Choose Files
                                    </button>
                                </div>
                                <div id="fileList" class="mt-3"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="general">General</option>
                                    <option value="academic">Academic</option>
                                    <option value="sports">Sports</option>
                                    <option value="cultural">Cultural</option>
                                    <option value="events">Events</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Event (Optional)</label>
                                <select class="form-select" name="event_id">
                                    <option value="">No Event</option>
                                    <!-- Events will be loaded dynamically -->
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured">
                                    <label class="form-check-label" for="isFeatured">
                                        Mark as featured image
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                            <i class="fas fa-upload"></i> Upload Photos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image View Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalTitle">Image Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid rounded" alt="Gallery Image">
                    <div class="mt-3">
                        <h6 id="imageTitle"></h6>
                        <p class="text-muted" id="imageDescription"></p>
                        <div class="row text-start">
                            <div class="col-md-6">
                                <strong>Category:</strong> <span id="imageCategory"></span><br>
                                <strong>Event:</strong> <span id="imageEvent"></span><br>
                                <strong>Uploaded:</strong> <span id="imageDate"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Featured:</strong> <span id="imageFeatured"></span><br>
                                <strong>Uploaded by:</strong> <span id="imageUploader"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let selectedFiles = [];

            // View switching
            $('#gridView').click(function() {
                $('#galleryGrid').show();
                $('#galleryList').hide();
                $('#gridView').addClass('active');
                $('#listView').removeClass('active');
            });

            $('#listView').click(function() {
                $('#galleryGrid').hide();
                $('#galleryList').show();
                $('#listView').addClass('active');
                $('#gridView').removeClass('active');
            });

            // File upload handling
            $('#uploadArea').on('click', function() {
                $('#fileInput').click();
            });

            $('#fileInput').on('change', function() {
                handleFileSelection(this.files);
            });

            // Drag and drop
            $('#uploadArea').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            $('#uploadArea').on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            $('#uploadArea').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                handleFileSelection(e.originalEvent.dataTransfer.files);
            });

            function handleFileSelection(files) {
                selectedFiles = Array.from(files);
                displayFileList();
                $('#uploadBtn').prop('disabled', selectedFiles.length === 0);
            }

            function displayFileList() {
                const fileList = $('#fileList');
                fileList.empty();

                selectedFiles.forEach((file, index) => {
                    const item = $(`
                        <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                            <div class="d-flex align-items-center">
                                <img src="${URL.createObjectURL(file)}" class="rounded me-2" width="40" height="30" style="object-fit: cover;">
                                <div>
                                    <small class="fw-bold">${file.name}</small><br>
                                    <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);
                    fileList.append(item);
                });
            }

            window.removeFile = function(index) {
                selectedFiles.splice(index, 1);
                displayFileList();
                $('#uploadBtn').prop('disabled', selectedFiles.length === 0);
            };

            // Upload form submission
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                if (selectedFiles.length === 0) {
                    alert('Please select at least one image');
                    return;
                }

                const formData = new FormData();
                selectedFiles.forEach(file => {
                    formData.append('images[]', file);
                });

                // Add form fields
                formData.append('category', $('[name=category]').val());
                formData.append('event_id', $('[name=event_id]').val());
                formData.append('is_featured', $('#isFeatured').is(':checked') ? 1 : 0);

                $('#uploadBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');

                $.ajax({
                    url: '/api/v1/gallery/upload',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-API-Token': '<?php echo $_SESSION["api_token"] ?? ""; ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#uploadModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(xhr) {
                        alert('Error uploading images: ' + xhr.responseJSON?.error || 'Unknown error');
                    },
                    complete: function() {
                        $('#uploadBtn').prop('disabled', false).html('<i class="fas fa-upload"></i> Upload Photos');
                    }
                });
            });

            // Load events for dropdown
            loadEvents();
        });

        function loadEvents() {
            $.ajax({
                url: '/api/v1/events',
                method: 'GET',
                headers: {
                    'X-API-Token': '<?php echo $_SESSION["api_token"] ?? ""; ?>'
                },
                success: function(response) {
                    if (response.success) {
                        const events = response.data;
                        const eventSelects = $('select[name=event_id]');

                        eventSelects.each(function() {
                            const select = $(this);
                            select.empty();
                            select.append('<option value="">No Event</option>');

                            events.forEach(event => {
                                select.append(`<option value="${event.id}">${event.title}</option>`);
                            });
                        });
                    }
                }
            });
        }

        function viewImage(imageId) {
            $.ajax({
                url: '/api/v1/gallery/' + imageId,
                method: 'GET',
                headers: {
                    'X-API-Token': '<?php echo $_SESSION["api_token"] ?? ""; ?>'
                },
                success: function(response) {
                    if (response.success) {
                        const image = response.data;
                        $('#modalImage').attr('src', '/uploads/gallery/' + image.image_path);
                        $('#imageModalTitle').text(image.title);
                        $('#imageTitle').text(image.title);
                        $('#imageDescription').text(image.description || 'No description');
                        $('#imageCategory').text(image.category);
                        $('#imageEvent').text(image.event_title || 'No event');
                        $('#imageDate').text(new Date(image.created_at).toLocaleDateString());
                        $('#imageFeatured').html(image.is_featured ? '<i class="fas fa-star text-warning"></i> Yes' : '<i class="far fa-star"></i> No');
                        $('#imageUploader').text(image.uploaded_by_name);

                        $('#imageModal').modal('show');
                    }
                },
                error: function(xhr) {
                    alert('Error loading image details: ' + xhr.responseJSON?.error || 'Unknown error');
                }
            });
        }

        function editImage(imageId) {
            // Load image data and show edit modal
            // Implementation similar to viewImage but for editing
            alert('Edit functionality will be implemented');
        }

        function deleteImage(imageId) {
            if (confirm('Are you sure you want to delete this image?')) {
                $.ajax({
                    url: '/api/v1/gallery/' + imageId,
                    method: 'DELETE',
                    headers: {
                        'X-API-Token': '<?php echo $_SESSION["api_token"] ?? ""; ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(xhr) {
                        alert('Error deleting image: ' + xhr.responseJSON?.error || 'Unknown error');
                    }
                });
            }
        }

        // Filter functions
        $('#categoryFilter').on('change', filterGallery);
        $('#eventFilter').on('change', filterGallery);
        $('#searchFilter').on('input', filterGallery);

        function filterGallery() {
            const category = $('#categoryFilter').val().toLowerCase();
            const event = $('#eventFilter').val();
            const search = $('#searchFilter').val().toLowerCase();

            $('.gallery-item-container').each(function() {
                const item = $(this);
                const itemCategory = item.data('category').toLowerCase();
                const itemTitle = item.find('h6').text().toLowerCase();

                const categoryMatch = !category || itemCategory === category;
                const searchMatch = !search || itemTitle.includes(search);

                if (categoryMatch && searchMatch) {
                    item.show();
                } else {
                    item.hide();
                }
            });
        }
    </script>
</body>
</html>