<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Management - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .content-card {
            transition: transform 0.2s;
        }
        .content-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="fas fa-home"></i> Homepage Management</h2>
                        <p class="text-muted">Manage your school's homepage content</p>
                    </div>
                    <a href="/admin/homepage/add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Content
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Sections -->
        <?php foreach ($sections as $sectionKey => $sectionName): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><?php echo htmlspecialchars($sectionName); ?></h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $sectionContent = array_filter($content, function($item) use ($sectionKey) {
                                return $item['section'] === $sectionKey;
                            });
                            ?>

                            <?php if (!empty($sectionContent)): ?>
                                <div class="row">
                                    <?php foreach ($sectionContent as $item): ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card content-card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                                                    <?php if ($item['content']): ?>
                                                        <p class="card-text small"><?php echo htmlspecialchars(substr($item['content'], 0, 100)); ?>...</p>
                                                    <?php endif; ?>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            Order: <?php echo $item['display_order']; ?>
                                                            <?php if (!$item['is_active']): ?>
                                                                <span class="badge bg-secondary">Inactive</span>
                                                            <?php endif; ?>
                                                        </small>
                                                        <div>
                                                            <a href="/admin/homepage/edit/<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="/admin/homepage/delete/<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger"
                                                               onclick="return confirm('Are you sure you want to delete this content?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No content added for this section yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>