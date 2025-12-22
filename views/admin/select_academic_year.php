<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Academic Year - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Select Academic Year</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Please select the academic year to continue. All data operations will be scoped to the selected academic year.</p>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                            <div class="mb-3">
                                <label for="academic_year_id" class="form-label">Academic Year</label>
                                <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                    <option value="">Choose an academic year...</option>
                                    <?php foreach ($academic_years as $year): ?>
                                        <option value="<?php echo $year['id']; ?>" <?php echo $year['is_active'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($year['year_name']); ?>
                                            <?php if ($year['is_active']): ?>
                                                (Active)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Continue to Dashboard</button>
                            </div>
                        </form>

                        <?php if (empty($academic_years)): ?>
                            <div class="alert alert-warning mt-3">
                                No academic years found. Please contact administrator to set up academic years.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>