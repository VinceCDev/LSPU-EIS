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
                        <img src="images/alumni.png" alt="LSPU Logo" class="me-0" style="height: 60px; width: auto;">
                        <span class=" navbar-brand fs-3 fw-bold me-0">LSPU</span><span class="navbar-brand fs-3 fw-light ms-0">EIS</span>
                    </div>


                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto align-items-lg-center gap-3">
                            <li class="nav-item">
                                <a class="nav-link active" href="home.php">Home</a>
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
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-briefcase me-2"></i>Employer Site</a></li>
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
        <section class="bg-light py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" placeholder="Search for jobs..." v-model="searchQuery">
                            <button class="btn btn-primary btn-lg" type="button">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-secondary btn-lg w-100" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                            <i class="fas fa-sliders-h me-1"></i> Filters
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="collapse mt-3" id="advancedFilters">
                    <div class="card card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Location</label>
                                <select class="form-select" v-model="selectedLocation">
                                    <option value="">All Locations</option>
                                    <option v-for="location in locations" :value="location">{{ location }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Job Type</label>
                                <select class="form-select" v-model="selectedJobType">
                                    <option value="">All Types</option>
                                    <option v-for="type in jobTypes" :value="type">{{ type }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Salary Range</label>
                                <select class="form-select" v-model="selectedSalary">
                                    <option value="">Any Salary</option>
                                    <option v-for="salary in salaryRanges" :value="salary.value">{{ salary.label }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Job Listings -->
        <section class="py-5">
            <div class="container">
                <h2 class="mb-4">Available Jobs</h2>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <div class="col" v-for="(job, index) in filteredJobs" :key="job.id">
                        <div class="card job-card h-100" @click="showJobDetails(job)">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <img :src="job.logo" :alt="job.company" class="company-logo me-3">
                                        <div>
                                            <h5 class="mb-0">{{ job.title }}</h5>
                                            <p class="mb-0 text-muted small">{{ job.department }}</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-bookmark save-icon fs-5"
                                        :class="{ 'saved': job.saved }"
                                        @click.stop="toggleSave(job)"></i>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ job.location }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-briefcase me-1"></i> {{ job.type }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-money-bill-wave me-1"></i> {{ job.salary }}
                                    </span>
                                </div>
                                <p class="card-text text-truncate">{{ job.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="filteredJobs.length === 0" class="empty-state text-center mt-4">
                    <i class="fas fa-briefcase fa-3x mb-3 text-secondary"></i>
                    <h4>No jobs found</h4>
                    <p>Try adjusting your filters to find more opportunities.</p>
                </div>

            </div>
        </section>

        <!-- Job Details Sidebar -->
        <div class="overlay" :class="{ 'show': showDetails }" @click="hideJobDetails"></div>

        <div class="job-details-sidebar" :class="{ 'show': showDetails }" v-if="selectedJob">
            <div class="p-4">
                <button class="btn btn-close float-end" @click="hideJobDetails"></button>

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="d-flex align-items-center">
                        <img :src="selectedJob.logo" :alt="selectedJob.company" class="company-logo me-3">
                        <div>
                            <h3 class="mb-0">{{ selectedJob.title }}</h3>
                            <p class="mb-0 text-muted">{{ selectedJob.department }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-map-marker-alt me-1"></i> {{ selectedJob.location }}
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-briefcase me-1"></i> {{ selectedJob.type }}
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-money-bill-wave me-1"></i> {{ selectedJob.salary }}
                    </span>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <button class="btn btn-primary flex-grow-1">
                        <i class="fas fa-paper-plane me-2"></i> Apply Now
                    </button>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">Job Description</h5>
                    <p>{{ selectedJob.description }}</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl eget ultricies tincidunt, nisl nisl aliquam nisl, eget ultricies nisl nisl eget nisl.</p>
                    <ul>
                        <li>Develop and maintain web applications using modern technologies</li>
                        <li>Collaborate with cross-functional teams to define, design, and ship new features</li>
                        <li>Optimize applications for maximum speed and scalability</li>
                        <li>Implement security and data protection measures</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">Qualifications</h5>
                    <ul>
                        <li v-for="(qualification, qIndex) in selectedJob.qualifications" :key="qIndex">
                            {{ qualification }}
                        </li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">Employer Questions</h5>
                    <div class="accordion" id="employerQuestions">
                        <div class="accordion-item" v-for="(question, eqIndex) in selectedJob.questions" :key="eqIndex">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#question' + eqIndex">
                                    {{ question.text }}
                                </button>
                            </h2>
                            <div :id="'question' + eqIndex" class="accordion-collapse collapse" data-bs-parent="#employerQuestions">
                                <div class="accordion-body">
                                    <div v-if="question.type === 'text'">
                                        <input type="text" class="form-control" :placeholder="question.placeholder || 'Enter your answer'">
                                    </div>
                                    <div v-else-if="question.type === 'textarea'">
                                        <textarea class="form-control" rows="3" :placeholder="question.placeholder || 'Enter your answer'"></textarea>
                                    </div>
                                    <div v-else-if="question.type === 'select'">
                                        <select class="form-select">
                                            <option v-for="(option, oIndex) in question.options" :key="oIndex" :value="option">{{ option }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vue.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.min.js"></script>

    <script src="js/home.js"></script>
</body>

</html>