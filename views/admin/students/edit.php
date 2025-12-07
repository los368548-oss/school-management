<?php
// Extract data
$student = $student ?? [];
$classes = $classes ?? [];
$csrf_token = $csrf_token ?? '';
$errors = $session->getFlash('errors') ?? [];
$old_input = $session->getFlash('old_input') ?? [];
?>

<form method="POST" action="/admin/students/<?php echo $student['id']; ?>" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

    <div class="row">
        <!-- Student Photo -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Student Photo</h5>
                </div>
                <div class="card-body text-center">
                    <div id="photoPreview" class="mb-3">
                        <?php if ($student['photo_path']): ?>
                            <img src="<?php echo htmlspecialchars($student['photo_path']); ?>"
                                 alt="Current Photo" class="img-fluid rounded" style="max-width: 200px;">
                        <?php else: ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto"
                                 style="width: 200px; height: 200px;">
                                <i class="fas fa-user fa-4x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
                    <small class="text-muted">Upload a new photo to replace the current one (Max: 2MB, JPG/PNG)</small>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="scholar_number" class="form-label">Scholar Number *</label>
                            <input type="text" class="form-control <?php echo isset($errors['scholar_number']) ? 'is-invalid' : ''; ?>"
                                   id="scholar_number" name="scholar_number"
                                   value="<?php echo htmlspecialchars($old_input['scholar_number'] ?? $student['scholar_number']); ?>" required>
                            <?php if (isset($errors['scholar_number'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['scholar_number'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="admission_number" class="form-label">Admission Number *</label>
                            <input type="text" class="form-control <?php echo isset($errors['admission_number']) ? 'is-invalid' : ''; ?>"
                                   id="admission_number" name="admission_number"
                                   value="<?php echo htmlspecialchars($old_input['admission_number'] ?? $student['admission_number']); ?>" required>
                            <?php if (isset($errors['admission_number'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['admission_number'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="admission_date" class="form-label">Admission Date *</label>
                            <input type="date" class="form-control <?php echo isset($errors['admission_date']) ? 'is-invalid' : ''; ?>"
                                   id="admission_date" name="admission_date"
                                   value="<?php echo htmlspecialchars($old_input['admission_date'] ?? $student['admission_date']); ?>" required>
                            <?php if (isset($errors['admission_date'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['admission_date'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="class_id" class="form-label">Class *</label>
                            <select class="form-select <?php echo isset($errors['class_id']) ? 'is-invalid' : ''; ?>"
                                    id="class_id" name="class_id" required>
                                <option value="">Select Class</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['id']; ?>"
                                            <?php echo ($old_input['class_id'] ?? $student['class_id']) == $class['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class['name'] . ' ' . $class['section']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['class_id'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['class_id'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>"
                                   id="first_name" name="first_name"
                                   value="<?php echo htmlspecialchars($old_input['first_name'] ?? $student['first_name']); ?>" required>
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['first_name'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control"
                                   id="middle_name" name="middle_name"
                                   value="<?php echo htmlspecialchars($old_input['middle_name'] ?? $student['middle_name']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>"
                                   id="last_name" name="last_name"
                                   value="<?php echo htmlspecialchars($old_input['last_name'] ?? $student['last_name']); ?>" required>
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['last_name'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth *</label>
                            <input type="date" class="form-control <?php echo isset($errors['date_of_birth']) ? 'is-invalid' : ''; ?>"
                                   id="date_of_birth" name="date_of_birth"
                                   value="<?php echo htmlspecialchars($old_input['date_of_birth'] ?? $student['date_of_birth']); ?>" required>
                            <?php if (isset($errors['date_of_birth'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['date_of_birth'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender *</label>
                            <select class="form-select <?php echo isset($errors['gender']) ? 'is-invalid' : ''; ?>"
                                    id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo ($old_input['gender'] ?? $student['gender']) === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($old_input['gender'] ?? $student['gender']) === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($old_input['gender'] ?? $student['gender']) === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                            <?php if (isset($errors['gender'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['gender'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="caste_category" class="form-label">Caste/Category</label>
                            <input type="text" class="form-control" id="caste_category" name="caste_category"
                                   value="<?php echo htmlspecialchars($old_input['caste_category'] ?? $student['caste_category']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" class="form-control" id="religion" name="religion"
                                   value="<?php echo htmlspecialchars($old_input['religion'] ?? $student['religion']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="blood_group" class="form-label">Blood Group</label>
                            <select class="form-select" id="blood_group" name="blood_group">
                                <option value="">Select Blood Group</option>
                                <option value="A+" <?php echo ($old_input['blood_group'] ?? $student['blood_group']) === 'A+' ? 'selected' : ''; ?>>A+</option>
                                <option value="A-" <?php echo ($old_input['blood_group'] ?? $student['blood_group']) === 'A-' ? 'selected' : ''; ?>>A-</option>
                                <option value="B+" <?php echo ($old_input['blood_group'] ?? $student['blood_group']) === 'B+' ? 'selected' : ''; ?>>B+</option>
                                <option value="B-" <?php echo ($old_input['blood_group'] ?? $student['blood_group']) === 'B-' ? 'selected' : ''; ?>>B-</option>
                                <option value="AB+" <?php echo ($old_input['blood_group'] ?? $student['blood_group']) === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                <option value="AB-" <?php echo ($old_input['blood_group'] ?? $student['blood_group']) === 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                <option value="O+" <?php echo ($old_input['blood_group'] ?? $student['blood_group']) === 'O+' ? 'selected' : ''; ?>>O+</option>
                                <option value="O-" <?php echo ($old_input['blood_group'] ?? $student['blood_group']) === 'O-' ? 'selected' : ''; ?>>O-</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" class="form-control" id="nationality" name="nationality"
                                   value="<?php echo htmlspecialchars($old_input['nationality'] ?? $student['nationality']); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mobile_number" class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control <?php echo isset($errors['mobile_number']) ? 'is-invalid' : ''; ?>"
                                   id="mobile_number" name="mobile_number"
                                   value="<?php echo htmlspecialchars($old_input['mobile_number'] ?? $student['mobile_number']); ?>">
                            <?php if (isset($errors['mobile_number'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['mobile_number'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                                   id="email" name="email"
                                   value="<?php echo htmlspecialchars($old_input['email'] ?? $student['email']); ?>">
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="village_address" class="form-label">Village/Town Address</label>
                            <textarea class="form-control" id="village_address" name="village_address" rows="3"><?php echo htmlspecialchars($old_input['village_address'] ?? $student['village_address']); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="permanent_address" class="form-label">Permanent Address</label>
                            <textarea class="form-control" id="permanent_address" name="permanent_address" rows="3"><?php echo htmlspecialchars($old_input['permanent_address'] ?? $student['permanent_address']); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Family Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Family Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name"
                                   value="<?php echo htmlspecialchars($old_input['father_name'] ?? $student['father_name']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mother_name" class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="mother_name"
                                   value="<?php echo htmlspecialchars($old_input['mother_name'] ?? $student['mother_name']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="guardian_name" class="form-label">Guardian's Name</label>
                            <input type="text" class="form-control" id="guardian_name" name="guardian_name"
                                   value="<?php echo htmlspecialchars($old_input['guardian_name'] ?? $student['guardian_name']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="guardian_contact" class="form-label">Guardian's Contact</label>
                            <input type="tel" class="form-control" id="guardian_contact" name="guardian_contact"
                                   value="<?php echo htmlspecialchars($old_input['guardian_contact'] ?? $student['guardian_contact']); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Academic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="previous_school" class="form-label">Previous School</label>
                            <input type="text" class="form-control" id="previous_school" name="previous_school"
                                   value="<?php echo htmlspecialchars($old_input['previous_school'] ?? $student['previous_school']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="medical_conditions" class="form-label">Medical Conditions</label>
                            <textarea class="form-control" id="medical_conditions" name="medical_conditions" rows="2"><?php echo htmlspecialchars($old_input['medical_conditions'] ?? $student['medical_conditions']); ?></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="aadhar_number" class="form-label">Aadhar Number</label>
                            <input type="text" class="form-control" id="aadhar_number" name="aadhar_number"
                                   value="<?php echo htmlspecialchars($old_input['aadhar_number'] ?? $student['aadhar_number']); ?>" maxlength="12">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="samagra_number" class="form-label">Samagra Number</label>
                            <input type="text" class="form-control" id="samagra_number" name="samagra_number"
                                   value="<?php echo htmlspecialchars($old_input['samagra_number'] ?? $student['samagra_number']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="apaar_id" class="form-label">Aapaar ID</label>
                            <input type="text" class="form-control" id="apaar_id" name="apaar_id"
                                   value="<?php echo htmlspecialchars($old_input['apaar_id'] ?? $student['apaar_id']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pan_number" class="form-label">PAN Number</label>
                            <input type="text" class="form-control" id="pan_number" name="pan_number"
                                   value="<?php echo htmlspecialchars($old_input['pan_number'] ?? $student['pan_number']); ?>" maxlength="10">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="/admin/students/<?php echo $student['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Student Details
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Student
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="img-fluid rounded" style="max-width: 200px;">
            `;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>