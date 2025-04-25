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
    <title>LSPU EIS - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/home.css">
</head>

<body>
    <div id="app" class="footer-wrapper">
        <!-- Header -->
        <header class="bg-white shadow-sm fixed-top">
            <div class="container h-100">
                <nav class="navbar navbar-expand-lg navbar-light h-100 py-0">
                    <div class="d-flex align-items-center">
                        <img src="images/alumni.png" alt="LSPU Logo" class="me-3" style="height: 60px; width: auto;">
                        <span class="navbar-brand fs-3 fw-bold">LSPU EIS</span>
                    </div>


                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto align-items-lg-center gap-3">
                            <li class="nav-item">
                                <a class="nav-link" href="home.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="my_application.php">My Applications</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="notif.php">Notifications</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Profile
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                    <li class="px-3 py-2">
                                        <div class="d-flex align-items-center">
                                            <img src="images/alumni.png" alt="Profile" class="profile-img me-2">
                                            <span>John Doe</span>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="my_profile.php"><i class="fas fa-user me-2"></i> View Profile</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-key me-2"></i> Forgot Password</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-key me-2"></i>Employer Site</a></li>
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
        <main class="container mt-5 pt-5">
            <div class="form-section bg-light p-4 rounded shadow-sm">
                <div v-if="step === 1">
                    <h4>1. Personal Details</h4>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input v-model="form.name" type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input v-model="form.email" type="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input v-model="form.phone" type="text" class="form-control">
                    </div>
                </div>

                <div v-if="step === 2">
                    <h4>2. Educational Background</h4>
                    <div class="mb-3">
                        <label class="form-label">School</label>
                        <input v-model="form.school" type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Degree</label>
                        <input v-model="form.degree" type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Graduation Year</label>
                        <input v-model="form.gradYear" type="number" class="form-control">
                    </div>
                </div>

                <div v-if="step === 3">
                    <h4>3. Work Experience</h4>
                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input v-model="form.company" type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input v-model="form.position" type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Years Worked</label>
                        <input v-model="form.years" type="number" class="form-control">
                    </div>
                </div>

                <div v-if="step === 4">
                    <h4>4. Skills & Resume</h4>
                    <div class="mb-3">
                        <label class="form-label">Skills</label>
                        <textarea v-model="form.skills" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Resume</label>
                        <input type="file" class="form-control" @change="handleResumeUpload">
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-secondary" :disabled="step === 1" @click="step--">Back</button>
                    <button class="btn btn-primary" @click="nextStep">{{ step === 4 ? 'Submit' : 'Next' }}</button>
                </div>
            </div>
        </main>

        <footer class="bg-dark text-light pt-5 pb-4">
            <div class="container">
                <div class="row">

                    <!-- About Us -->
                    <div class="col-md-3 col-sm-6 mb-4">
                        <h5 class="text-uppercase mb-3">About Us</h5>
                        <p>We're dedicated to providing quality services and products to our customers with exceptional support.</p>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-md-3 col-sm-6 mb-4">
                        <h5 class="text-uppercase mb-3">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="/" class="text-light text-decoration-none">Home</a></li>
                            <li><a href="/about" class="text-light text-decoration-none">About</a></li>
                            <li><a href="/services" class="text-light text-decoration-none">Services</a></li>
                            <li><a href="/blog" class="text-light text-decoration-none">Blog</a></li>
                            <li><a href="/contact" class="text-light text-decoration-none">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div class="col-md-3 col-sm-6 mb-4">
                        <h5 class="text-uppercase mb-3">Contact Info</h5>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-geo-alt-fill me-2"></i>123 Main Street, City, Country</li>
                            <li><i class="bi bi-telephone-fill me-2"></i>+1 (555) 123-4567</li>
                            <li><i class="bi bi-envelope-fill me-2"></i>info@yourdomain.com</li>
                        </ul>
                    </div>

                    <!-- Optional Fourth Column (e.g., Social or Newsletter) -->
                    <div class="col-md-3 col-sm-6 mb-4">
                        <h5 class="text-uppercase mb-3">Follow Us</h5>
                        <div>
                            <a href="#" class="text-light me-3"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="text-light me-3"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="text-light me-3"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="text-light"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>

                </div>

                <!-- Footer Bottom -->
                <div class="text-center mt-4 border-top pt-3">
                    <p class="mb-0">&copy; 2023 Your Company Name. All Rights Reserved.</p>
                    <small>
                        <a href="/privacy" class="text-decoration-none text-light">Privacy Policy</a> |
                        <a href="/terms" class="text-decoration-none text-light">Terms of Service</a>
                    </small>
                </div>
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