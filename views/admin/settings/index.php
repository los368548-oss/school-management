<?php
// Extract data
$csrf_token = $csrf_token ?? '';
$flash_message = $session->getFlash('message');
$flash_type = $session->getFlash('message_type') ?: 'info';
?>

<div class="row">
    <!-- Settings Navigation -->
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Settings</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                        <i class="fas fa-cogs me-2"></i>General Settings
                    </a>
                    <a href="#school" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-school me-2"></i>School Information
                    </a>
                    <a href="#users" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-users me-2"></i>User Management
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-shield-alt me-2"></i>Security Settings
                    </a>
                    <a href="#backup" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-database me-2"></i>Backup & Restore
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <!-- Flash Messages -->
                <?php if ($flash_message): ?>
                    <div class="alert alert-<?php echo $flash_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo $flash_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="tab-content">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general">
                        <h4>General Settings</h4>
                        <form method="POST" action="/admin/settings/general">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="app_name" class="form-label">Application Name</label>
                                    <input type="text" class="form-control" id="app_name" name="app_name"
                                           value="A.s.higher secondary school Management System" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <option value="Asia/Kolkata" selected>Asia/Kolkata (IST)</option>
                                        <option value="UTC">UTC</option>
                                        <option value="America/New_York">America/New_York (EST)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="language" class="form-label">Default Language</label>
                                    <select class="form-select" id="language" name="language">
                                        <option value="en" selected>English</option>
                                        <option value="hi">Hindi</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="per_page" class="form-label">Items Per Page</label>
                                    <select class="form-select" id="per_page" name="per_page">
                                        <option value="10">10</option>
                                        <option value="25" selected>25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save General Settings
                            </button>
                        </form>
                    </div>

                    <!-- School Information -->
                    <div class="tab-pane fade" id="school">
                        <h4>School Information</h4>
                        <form method="POST" action="/admin/settings/school">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="school_name" class="form-label">School Name *</label>
                                    <input type="text" class="form-control" id="school_name" name="school_name"
                                           value="A.s.higher secondary school" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="school_address" class="form-label">School Address</label>
                                    <textarea class="form-control" id="school_address" name="school_address" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="school_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="school_phone" name="school_phone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="school_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="school_email" name="school_email">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="school_website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="school_website" name="school_website">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="academic_year" class="form-label">Current Academic Year</label>
                                    <input type="text" class="form-control" id="academic_year" name="academic_year"
                                           value="2024-2025" placeholder="YYYY-YYYY">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="school_description" class="form-label">School Description</label>
                                <textarea class="form-control" id="school_description" name="school_description" rows="4"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save School Information
                            </button>
                        </form>
                    </div>

                    <!-- User Management -->
                    <div class="tab-pane fade" id="users">
                        <h4>User Management</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            User management features will be available in future updates.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                        <h5>Manage Users</h5>
                                        <p class="text-muted">Add, edit, and manage system users</p>
                                        <button class="btn btn-primary" disabled>Coming Soon</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-shield fa-3x text-success mb-3"></i>
                                        <h5>Role Permissions</h5>
                                        <p class="text-muted">Configure user roles and permissions</p>
                                        <button class="btn btn-success" disabled>Coming Soon</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security">
                        <h4>Security Settings</h4>
                        <form method="POST" action="/admin/settings/security">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                                    <input type="number" class="form-control" id="session_timeout" name="session_timeout"
                                           value="60" min="15" max="480">
                                    <small class="text-muted">Users will be automatically logged out after this period of inactivity</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_min_length" class="form-label">Minimum Password Length</label>
                                    <input type="number" class="form-control" id="password_min_length" name="password_min_length"
                                           value="8" min="6" max="32">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                    <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts"
                                           value="5" min="3" max="10">
                                    <small class="text-muted">Account will be locked after this many failed attempts</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lockout_duration" class="form-label">Lockout Duration (minutes)</label>
                                    <input type="number" class="form-control" id="lockout_duration" name="lockout_duration"
                                           value="30" min="5" max="1440">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enable_2fa" name="enable_2fa">
                                    <label class="form-check-label" for="enable_2fa">
                                        Enable Two-Factor Authentication
                                    </label>
                                </div>
                                <small class="text-muted">Require additional verification for user login</small>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Security Settings
                            </button>
                        </form>
                    </div>

                    <!-- Backup & Restore -->
                    <div class="tab-pane fade" id="backup">
                        <h4>Backup & Restore</h4>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Regular backups are crucial for data safety. Always backup before making major changes.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Create Backup</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">Create a complete backup of your database and uploaded files.</p>
                                        <button class="btn btn-primary w-100" onclick="createBackup()">
                                            <i class="fas fa-download me-1"></i>Create Backup
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Restore from Backup</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">Restore your system from a previously created backup file.</p>
                                        <button class="btn btn-warning w-100" disabled>
                                            <i class="fas fa-upload me-1"></i>Restore Backup
                                        </button>
                                        <small class="text-muted">Coming in future update</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Backup History</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Size</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No backups found</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createBackup() {
    if (confirm('Are you sure you want to create a backup? This may take a few minutes.')) {
        const btn = event.target;
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating Backup...';

        fetch('/admin/settings/backup/create', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?php echo $csrf_token; ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Backup created successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to create backup'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the backup');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
}

// Handle tab switching
document.querySelectorAll('.list-group-item[data-bs-toggle="tab"]').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.list-group-item').forEach(el => el.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>