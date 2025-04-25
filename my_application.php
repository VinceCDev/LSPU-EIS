<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPU EIS - My Applications</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 for confirmation dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/my_application.css">
</head>

<body>
    <div id="app">
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
                                <a class="nav-link" href="home.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active custom-active" href="my_application.php">My Applications</a>
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
                                            <img src="https://via.placeholder.com/150" alt="Profile" class="profile-img me-2">
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

        <!-- Main Content -->
        <main class="main-container">
            <div class="application-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <h1 class="application-title m-0">
                    My Applications
                </h1>
                <div class="application-actions btn-group" role="tablist">
                    <button class="btn btn-outline-secondary btn-sm active w-auto w-md-auto" id="applied-tab" data-bs-toggle="tab" data-bs-target="#applied" type="button" role="tab" aria-controls="applied" aria-selected="true">
                        Jobs Applied
                    </button>
                    <button class="btn btn-outline-secondary btn-sm w-auto w-md-auto" id="saved-tab" data-bs-toggle="tab" data-bs-target="#saved" type="button" role="tab" aria-controls="saved" aria-selected="false">
                        Saved Jobs
                    </button>
                </div>
            </div>


            <!-- ✅ Only one tab-content wrapper -->
            <div class="tab-content mt-3" id="myTabContent">
                <!-- Applied Jobs Tab -->
                <div class="tab-pane fade show active" id="applied" role="tabpanel" aria-labelledby="applied-tab">
                    <div v-if="loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading your applications...</p>
                    </div>

                    <div v-else-if="appliedJobs.length === 0" class="empty-state">
                        <i class="far fa-folder-open"></i>
                        <h4>No applications yet</h4>
                        <p>When you apply for jobs, they'll appear here.</p>
                        <a href="home.php" class="btn btn-primary mt-3">Browse Jobs</a>
                    </div>

                    <div v-else>
                        <div v-for="job in appliedJobs" :key="job.id" class="job-card">
                            <div class="job-card-header">
                                <h3 class="job-title">{{ job.title }}</h3>
                                <p class="job-company">{{ job.company }}</p>
                                <p class="job-location">
                                    <i class="fas fa-map-marker-alt"></i> {{ job.location }}
                                </p>
                                <span class="job-status status-applied">Applied {{ formatDate(job.appliedDate) }}</span>
                            </div>
                            <div class="job-card-body">
                                <p class="job-description">{{ job.description }}</p>
                            </div>
                            <div class="job-card-footer">
                                <div class="job-date">Posted {{ formatDate(job.postedDate) }}</div>
                                <div class="job-actions">
                                    <button @click="viewJob(job)" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button @click="withdrawApplication(job.id)" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saved Jobs Tab -->
                <div class="tab-pane fade" id="saved" role="tabpanel" aria-labelledby="saved-tab">
                    <div v-if="loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading your saved jobs...</p>
                    </div>

                    <div v-else-if="savedJobs.length === 0" class="empty-state">
                        <i class="far fa-bookmark"></i>
                        <h4>No saved jobs yet</h4>
                        <p>When you save jobs, they'll appear here.</p>
                        <a href="#" class="btn btn-primary mt-3">Browse Jobs</a>
                    </div>

                    <div v-else>
                        <div v-for="job in savedJobs" :key="job.id" class="job-card">
                            <div class="job-card-header">
                                <h3 class="job-title">{{ job.title }}</h3>
                                <p class="job-company">{{ job.company }}</p>
                                <p class="job-location">
                                    <i class="fas fa-map-marker-alt"></i> {{ job.location }}
                                </p>
                                <span class="job-status status-saved">Saved {{ formatDate(job.savedDate) }}</span>
                            </div>
                            <div class="job-card-body">
                                <p class="job-description">{{ job.description }}</p>
                            </div>
                            <div class="job-card-footer">
                                <div class="job-date">Posted {{ formatDate(job.postedDate) }}</div>
                                <div class="job-actions">
                                    <button @click="viewJob(job)" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button @click="applyJob(job.id)" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </button>
                                    <button @click="unsaveJob(job.id)" class="btn btn-outline-secondary btn-sm">
                                        <i class="far fa-trash-alt"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Job Detail Modal -->
        <div class="modal fade" id="jobModal" tabindex="-1" aria-labelledby="jobModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="jobModalLabel">Job Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedJob" class="job-modal-content">
                            <div class="job-modal-header">
                                <img :src="selectedJob.logo || 'https://via.placeholder.com/80'" alt="Company Logo" class="job-modal-img">
                                <div>
                                    <h3 class="job-modal-title">{{ selectedJob.title }}</h3>
                                    <p class="job-modal-company">{{ selectedJob.company }}</p>
                                    <p><i class="fas fa-map-marker-alt"></i> {{ selectedJob.location }}</p>
                                    <span class="job-status" :class="activeTab === 'applied' ? 'status-applied' : 'status-saved'">
                                        {{ activeTab === 'applied' ? 'Applied ' + formatDate(selectedJob.appliedDate) : 'Saved ' + formatDate(selectedJob.savedDate) }}
                                    </span>
                                </div>
                            </div>

                            <div class="job-modal-section">
                                <h5 class="job-modal-section-title">Job Description</h5>
                                <p>{{ selectedJob.fullDescription || selectedJob.description }}</p>
                            </div>

                            <div class="job-modal-section">
                                <h5 class="job-modal-section-title">Requirements</h5>
                                <ul>
                                    <li v-for="(req, index) in selectedJob.requirements" :key="index">{{ req }}</li>
                                </ul>
                            </div>

                            <div class="job-modal-section">
                                <h5 class="job-modal-section-title">About the Company</h5>
                                <p>{{ selectedJob.aboutCompany || 'No company information available' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button v-if="activeTab === 'saved'" @click="applyJob(selectedJob.id)" type="button" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Apply Now
                        </button>
                        <button v-else type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <footer class="fixed-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p class="mb-0">© 2023 LSPU EIS Job Portal. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vue.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.2.47/vue.global.min.js"></script>

    <script src="js/my_application.js"></script>
</body>

</html>