<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();

    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPU EIS - Application Page</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/applications.css">
</head>

<body>
    <div id="app" class="footer-wrapper">
        <!-- Header -->
        <header class="bg-white shadow-sm fixed-top">
            <div class="container h-100">
                <nav class="navbar navbar-expand-lg navbar-light h-100 py-0">
                    <div class="d-flex align-items-center">
                        <img src="images/alumni.png" alt="LSPU Logo" class="me-0" style="height: 60px; width: auto;">
                        <span class=" navbar-brand fs-3 fw-bold me-0">LSPU</span><span class="navbar-brand fs-3 fw-light ms-0">EIS</span>
                    </div>


                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto align-items-lg-center gap-3">
                            <li class="nav-item">
                                <a class="nav-link" href="home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="my_application">My Applications</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="notification">Notifications</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Profile
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                    <li class="px-3 py-2">
                                        <div class="d-flex align-items-center">
                                            <img v-if="profilePhoto" :src="profilePhoto" alt="Profile Photo" class="profile-img me-2" />
                                            <span>{{ fullName }}</span>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="my_profile"><i class="fas fa-user me-2"></i> View Profile</a></li>
                                    <li><a class="dropdown-item" href="forgot_password"><i class="fas fa-key me-2"></i> Forgot Password</a></li>
                                    <li><a class="dropdown-item" href="employer_signup"><i class="fas fa-briefcase me-2"></i>Employer Site</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                                </ul>
                            </li>

                        </ul>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Search Section -->
        <div class="container py-4 mt-2">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-container">
                        <h2 class="text-center mb-4">Job Application Form</h2>

                        <!-- Numbered Step Indicator -->
                        <div class="step-indicator position-relative d-flex justify-content-between align-items-center px-4" style="max-width: 600px; margin: 0 auto;">
                            <!-- Connecting lines (modified to stop before last step) -->
                            <div class="position-absolute" style="width: calc(100% - 2.5rem); height: 2px; background-color: #e9ecef; top: 50%; left: 0; transform: translateY(-50%); z-index: 0;"></div>
                            <div class="position-absolute bg-primary"
                                :style="{width: `${((currentStep - 1) / 4) * (100 - (100/4))}%`, height: '2px', top: '50%', left: '0', transform: 'translateY(-50%)', zIndex: '0', transition: 'width 0.3s ease'}"></div>

                            <!-- Your original code (completely unchanged) -->
                            <div class="step-circle position-relative z-1"
                                :class="{active: currentStep === 1, completed: currentStep > 1}"
                                @click="goToStep(1)">1</div>
                            <div class="step-circle position-relative z-1"
                                :class="{active: currentStep === 2, completed: currentStep > 2}"
                                @click="goToStep(2)">2</div>
                            <div class="step-circle position-relative z-1"
                                :class="{active: currentStep === 3, completed: currentStep > 3}"
                                @click="goToStep(3)">3</div>
                            <div class="step-circle position-relative z-1"
                                :class="{active: currentStep === 4, completed: currentStep > 4}"
                                @click="goToStep(4)">4</div>
                            <div class="step-circle position-relative z-1"
                                :class="{active: currentStep === 5, completed: currentStep > 5}"
                                @click="goToStep(5)">5</div>
                        </div>
                        <!-- Step Title -->
                        <h5 class="step-title text-start mt-4 mb-4 fs-4">{{ steps[currentStep-1].title }}</h5>

                        <!-- Personal Information -->
                        <div class="form-section" :class="{active: currentStep === 1}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" v-model="application.firstName" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" v-model="application.lastName" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" v-model="application.email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" v-model="application.phone" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select class="form-control" id="editGender" v-model="application.gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Civil</label>
                                    <select class="form-control" id="editCivilStatus" v-model="application.civil" required>
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" v-model="application.city" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Province</label>
                                    <input type="text" class="form-control" v-model="application.province" required>
                                </div>
                            </div>
                        </div>

                        <!-- Education -->
                        <div class="form-section" :class="{active: currentStep === 2}">
                            <div v-for="(edu, index) in application.education" :key="index" class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Institution</label>
                                        <input type="text" class="form-control" v-model="edu.school" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Degree</label>
                                        <input type="text" class="form-control" v-model="edu.degree" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Field of Study</label>
                                        <input type="text" class="form-control" v-model="edu.field" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" v-model="edu.start_date" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" v-model="edu.end_date" required>
                                    </div>
                                    <div class="col-12 text-end" v-if="application.education.length > 1">
                                        <button class="btn btn-sm btn-outline-danger" @click="removeEducation(index)">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                <hr v-if="index < application.education.length - 1">
                            </div>
                            <button class="btn btn-outline-secondary" @click="addEducation">
                                + Add Education
                            </button>
                        </div>

                        <!-- Skills -->
                        <div class="form-section" :class="{active: currentStep === 3}">
                            <div class="mb-3">
                                <label class="form-label">Add Skills</label>
                                <input type="text" class="form-control"
                                    v-model="newSkill" @keyup.enter="addSkill"
                                    placeholder="Type skill and press Enter">
                            </div>

                            <div class="d-flex flex-wrap mb-4">
                                <span class="badge bg-primary p-2 skill-tag" v-for="(skill, index) in application.skills" :key="index">
                                    {{ skill }}
                                    <button type="button" class="btn-close btn-close-white ms-2"
                                        @click="removeSkill(index)"></button>
                                </span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Additional Skills</label>
                                <textarea class="form-control" v-model="application.additionalSkills" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Work Experience -->
                        <div class="form-section" :class="{active: currentStep === 4}">
                            <div v-for="(exp, index) in application.experience" :key="index" class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Company</label>
                                        <input type="text" class="form-control" v-model="exp.company" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Position</label>
                                        <input type="text" class="form-control" v-model="exp.title" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" v-model="exp.start_date" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" v-model="exp.end_date" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Responsibilities</label>
                                        <textarea class="form-control" v-model="exp.description" rows="3"></textarea>
                                    </div>
                                    <div class="col-12 text-end" v-if="application.experience.length > 1">
                                        <button class="btn btn-sm btn-outline-danger" @click="removeExperience(index)">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                <hr v-if="index < application.experience.length - 1">
                            </div>
                            <button class="btn btn-outline-secondary" @click="addExperience">
                                + Add Experience
                            </button>
                        </div>

                        <!-- Resume -->
                        <div class="form-section" :class="{active: currentStep === 5}">
                            <!-- Resume File Input Section -->
                            <div class="mb-3">
                                <label class="form-label">Upload Resume</label>
                                <input type="file" class="form-control" accept=".pdf,.doc,.docx" @change="handleFileUpload">
                            </div>

                            <!-- Terms & Conditions Checkbox -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" v-model="application.agreeTerms" required>
                                <label class="form-check-label">
                                    I certify that all information is accurate
                                </label>
                            </div>

                            <!-- Display Resume if Available -->
                            <div v-if="application.resume">
                                <p><strong>Current Resume:</strong> <a :href="'/uploads/resumes/' + application.resume" target="_blank">View Resume</a></p>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-secondary" @click="prevStep" :disabled="currentStep === 1">
                                Back
                            </button>
                            <button class="btn btn-primary" @click="nextStep" v-if="currentStep < 5">
                                Continue
                            </button>
                            <button class="btn btn-success" @click="submitApplication" v-if="currentStep === 5">
                                Submit Application
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Bottom -->
        <footer class="fixed-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p class="mb-0">© 2025 LSPU - EIS Job Portal. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    </footer>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vue.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.min.js"></script>

    <script src="js/application_form.js"></script>
</body>

</html>