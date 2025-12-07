<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'School Management System'; ?> - A.s.higher secondary school</title>

    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-grid.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-utilities.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo $csrf_token ?? ''; ?>">
</head>
<body>
    <!-- Header -->
    <?php if (isset($show_header) && $show_header): ?>
        <?php include __DIR__ . '/../partials/header.php'; ?>
    <?php endif; ?>

    <!-- Sidebar -->
    <?php if (isset($show_sidebar) && $show_sidebar): ?>
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="<?php echo $main_class ?? 'container-fluid'; ?>" style="<?php echo isset($show_sidebar) && $show_sidebar ? 'margin-left: 250px; padding-top: 20px;' : ''; ?>">
        <div class="container-fluid">
            <?php if (isset($page_title)): ?>
                <div class="page-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><?php echo $page_title; ?></h1>
                            <?php if (isset($breadcrumb)): ?>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <?php foreach ($breadcrumb as $item): ?>
                                            <li class="breadcrumb-item <?php echo $item['active'] ?? false ? 'active' : ''; ?>">
                                                <?php if (isset($item['url']) && !$item['active']): ?>
                                                    <a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
                                                <?php else: ?>
                                                    <?php echo $item['title']; ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php endif; ?>
                        </div>
                        <?php if (isset($page_actions)): ?>
                            <div class="btn-group">
                                <?php foreach ($page_actions as $action): ?>
                                    <a href="<?php echo $action['url']; ?>" class="btn btn-<?php echo $action['type'] ?? 'primary'; ?>">
                                        <i class="fas fa-<?php echo $action['icon'] ?? 'plus'; ?> me-1"></i>
                                        <?php echo $action['title']; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Flash Messages -->
            <?php if (isset($flash_message)): ?>
                <div class="alert alert-<?php echo $flash_type ?? 'info'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $flash_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Content -->
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php if (isset($show_footer) && $show_footer): ?>
        <?php include __DIR__ . '/../partials/footer.php'; ?>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/custom.js"></script>

    <!-- Page-specific JS -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline scripts -->
    <?php if (isset($inline_scripts)): ?>
        <script>
            <?php echo $inline_scripts; ?>
        </script>
    <?php endif; ?>
</body>
</html>