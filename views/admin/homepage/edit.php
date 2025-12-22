<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Homepage Content - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/dashboard">
                <i class="fas fa-school"></i> School Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/students">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/classes">Classes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/attendance">Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/exams">Exams</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fees">Fees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/events">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/gallery">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/homepage">Homepage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/reports">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/settings">Settings</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/admin/profile">Profile</a></li>
                            <li><a class="dropdown-item" href="/admin/change-password">Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/homepage">Homepage</a></li>
                        <li class="breadcrumb-item active">Edit Content</li>
                    </ol>
                </nav>
                <h2><i class="fas fa-edit"></i> Edit Homepage Content</h2>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="/admin/homepage/edit/<?php echo $content['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                            <div class="mb-3">
                                <label for="section" class="form-label">Section *</label>
                                <select class="form-select <?php echo isset($errors['section']) ? 'is-invalid' : ''; ?>" id="section" name="section" required>
                                    <option value="">Choose a section</option>
                                    <?php foreach ($sections as $key => $name): ?>
                                        <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $content['section'] === $key ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['section'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['section']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($content['title']); ?>" required>
                                <?php if (isset($errors['title'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['title']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control" id="content" name="content" rows="5"><?php echo htmlspecialchars($content['content']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="image_path" class="form-label">Image Path</label>
                                <input type="text" class="form-control" id="image_path" name="image_path" value="<?php echo htmlspecialchars($content['image_path']); ?>" placeholder="e.g., /uploads/images/hero.jpg">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="link_url" class="form-label">Link URL</label>
                                        <input type="url" class="form-control" id="link_url" name="link_url" value="<?php echo htmlspecialchars($content['link_url']); ?>" placeholder="https://example.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="link_text" class="form-label">Link Text</label>
                                        <input type="text" class="form-control" id="link_text" name="link_text" value="<?php echo htmlspecialchars($content['link_text']); ?>" placeholder="Learn More">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="display_order" class="form-label">Display Order</label>
                                        <input type="number" class="form-control" id="display_order" name="display_order" value="<?php echo htmlspecialchars($content['display_order']); ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo $content['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Content
                                </button>
                                <a href="/admin/homepage" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Help</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">
                            <strong>Section:</strong> Choose where this content will appear on the homepage.<br><br>
                            <strong>Title:</strong> The heading for this content block.<br><br>
                            <strong>Content:</strong> The main text content (optional).<br><br>
                            <strong>Image Path:</strong> Path to an image file (optional).<br><br>
                            <strong>Link:</strong> Optional link with custom text.<br><br>
                            <strong>Display Order:</strong> Controls the order within the section (lower numbers appear first).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>