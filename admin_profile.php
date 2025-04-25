<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator - Alumni Pending</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_alumni.css">
</head>

<body>
    <div id="app">
        <!-- Sidebar -->
        <div class="sidebar" :class="{ 'active': sidebarActive }">
            <div class="sidebar-brand">
                <img src="images/alumni.png" alt="Logo" class="sidebar-logo">
                <span class="sidebar-brand-name">LSPU Administrator</span>
            </div>
            <div class="sidebar-menu">
                <a href="admin_dashboard.php" class="sidebar-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <a class="sidebar-item" data-bs-toggle="collapse" href="admin_job.php" role="button" aria-expanded="false">
                    <i class="fas fa-briefcase"></i>
                    <span>Jobs</span>
                </a>

                <a href="admin_applicant.php" class="sidebar-item">
                    <i class="fas fa-users"></i>
                    <span>Applicants</span>
                </a>

                <a class="sidebar-item sidebar-dropdown-toggle" data-bs-toggle="collapse" href="#companiesDropdown" role="button" aria-expanded="false">
                    <i class="fas fa-building"></i>
                    <span>Companies</span>
                </a>
                <div class="sidebar-dropdown collapse" id="companiesDropdown">
                    <a href="admin_company.php" class="sidebar-dropdown-item">Manage Companies</a>
                    <a href="admin_company_pending.php" class="sidebar-dropdown-item">Pending Companies</a>
                </div>

                <a class="sidebar-item sidebar-dropdown-toggle active" data-bs-toggle="collapse" href="#alumniDropdown" role="button" aria-expanded="false">
                    <i class="fas fa-user-graduate"></i>
                    <span>Alumni</span>
                </a>
                <div class="sidebar-dropdown collapse" id="alumniDropdown">
                    <a href="admin_alumni.php" class="sidebar-dropdown-item">Manage Alumni</a>
                    <a href="admin_alumni_pending.php" class="sidebar-dropdown-item">Pending Alumni</a>
                </div>

                <a href="admin_message.php" class="sidebar-item">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
            </div>
        </div>

        <!-- Header -->
        <header>
            <div class="container-fluid">
                <div class="header-content">
                    <button class="mobile-menu-btn" @click="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="profile-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img :src="employer.logo || 'https://via.placeholder.com/150'" alt="Profile" class="profile-img">
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="admin_profile.php"><i class="fas fa-user me-2"></i> View Profile</a></li>
                        <li><a class="dropdown-item" href="forgot_password.php"><i class="fas fa-cog me-2"></i> Forgot Password</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" @click.prevent="confirmLogout">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid">
                <div class="dashboard-card">
                    <div class="table-header">
                        <h2 class="mb-0">Alumni Pending</h2>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <!-- Search (Left Side) -->
                        <div class="search-box me-3" style="max-width: 350px;">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" placeholder="Search jobs..." v-model="searchQuery" @input="filterJobs">
                        </div>

                        <!-- Filter Options (Right Side) -->
                        <div class="d-flex flex-nowrap gap-2">
                            <select class="form-select flex-grow-1" style="min-width: 140px;" v-model="filters.department" @change="filterJobs">
                                <option value="">All Campus</option>
                                <option v-for="dept in uniqueDepartments" :key="dept">{{ dept }}</option>
                            </select>

                            <select class="form-select flex-grow-1" style="min-width: 140px;" v-model="filters.type" @change="filterJobs">
                                <option value="">All Year</option>
                                <option v-for="type in uniqueTypes" :key="type">{{ type }}</option>
                            </select>

                            <select class="form-select flex-grow-1" style="min-width: 140px;" v-model="filters.status" @change="filterJobs">
                                <option value="">All Statuses</option>
                                <option value="Active">Active</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name of Alumni</th>
                                    <th>Gender</th>
                                    <th>Email</th>
                                    <th>Year Graduated</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="job in paginatedJobs" :key="job.id">
                                    <td>
                                        <strong>{{ alumni.name }}</strong>
                                    </td>
                                    <td>{{ alumni.gender }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ alumni.year_graduated }}</td>
                                    <td>{{ alumni.course }}</td>
                                    <td>
                                        <span :class="'status-badge status-' + job.status.toLowerCase()">
                                            {{ alumni.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-dropdown dropdown position-relative">
                                            <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#" @click="viewJob(job)"><i class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item" href="#" @click="openEditModal(job)"><i class="fas fa-edit me-2"></i> Edit</a></li>
                                                <li><a class="dropdown-item" href="#" @click="confirmDelete(job)"><i class="fas fa-trash me-2"></i> Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredJobs.length === 0">
                                    <td colspan="8" class="text-center py-4 text-muted">No job postings found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="pagination-info">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredJobs.length) }} of {{ filteredJobs.length }} entries
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                                    <a class="page-link" href="#" @click.prevent="prevPage">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                <li class="page-item" v-for="page in totalPages" :key="page" :class="{ 'active': page === currentPage }">
                                    <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
                                </li>
                                <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                                    <a class="page-link" href="#" @click.prevent="nextPage">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <footer class="footer bg-light py-3 border-top position-sticky bottom-0 w-100">
                <div class="container text-center">
                    <small text-muted d-block text-start>
                        &copy; 2025 Laguna State Polytechnic University - Employment and Information System. All rights reserved.
                    </small>
                </div>
            </footer>
        </main>

        <!-- Add/Edit Job Modal -->
        <div class="modal fade" id="addJobModal" tabindex="-1" aria-labelledby="addJobModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addJobModalLabel">{{ selectedJob ? 'Edit Job Posting' : 'Post New Job' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <div class="row">
                            <!-- Left: Account Information -->
                            <div class="col-md-4 info">
                                <h5 class="fw-bold">Account Information</h5>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="input-icon">
                                        <i class="bi bi-envelope"></i>
                                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-icon">
                                        <i class="bi bi-lock"></i>
                                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <div class="input-icon">
                                        <i class="bi bi-shield-lock"></i>
                                        <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Personal Information -->
                            <div class="col-md-8">
                                <h5 class="fw-bold">Personal Information</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <div class="input-icon">
                                            <i class="bi bi-person"></i>
                                            <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name</label>
                                        <div class="input-icon">
                                            <i class="bi bi-person"></i>
                                            <input type="text" name="middle_name" class="form-control" placeholder="Middle Name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <div class="input-icon">
                                            <i class="bi bi-person"></i>
                                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Birth Date</label>
                                        <div class="input-icon">
                                            <i class="bi bi-calendar"></i>
                                            <input type="date" name="birthdate" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Contact Number</label>
                                        <div class="input-icon">
                                            <i class="bi bi-telephone"></i>
                                            <input type="text" name="contact" class="form-control" placeholder="Contact Number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Gender</label>
                                        <div class="input-icon">
                                            <i class="bi bi-telephone"></i>
                                            <select class="form-select form-control" name="gender" aria-placeholder="Gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Civil Status</label>
                                        <div class="input-icon">
                                            <i class="bi bi-telephone"></i>
                                            <select class="form-select form-control" name="civil_status" aria-placeholder="Civil Status" required>
                                                <option value="">Select Status</option>
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                                <option value="Divorced">Divorced</option>
                                                <option value="Widowed">Widowed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Contact Number</label>
                                        <div class="input-icon">
                                            <i class="bi bi-telephone"></i>
                                            <input type="text" name="contact" class="form-control" placeholder="Contact Number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">City</label>
                                        <div class="input-icon">
                                            <i class="bi bi-geo-alt"></i>
                                            <input type="text" name="city" class="form-control" placeholder="City" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Province</label>
                                        <div class="input-icon">
                                            <i class="bi bi-geo"></i>
                                            <input type="text" name="province" class="form-control" placeholder="Province" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Year Graduated</label>
                                        <div class="input-icon">
                                            <i class="bi bi-calendar"></i>
                                            <input type="number" name="year_graduated" class="form-control" placeholder="Year Graduated" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Campus Graduated</label>
                                        <div class="input-icon">
                                            <i class="bi bi-building"></i>
                                            <select class="form-select form-control" name="campus" aria-placeholder="Campus" required>
                                                <option value="">Select Campus</option>
                                                <option value="LSPU - San Pablo">LSPU - San Pablo</option>
                                                <option value="LSPU - Los Baños">LSPU - Los Baños</option>
                                                <option value="LSPU - Siniloan">LSPU - Siniloan</option>
                                                <option value="LSPU - Sta. Cruz">LSPU - Sta. Cruz</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Course</label>
                                        <div class="input-icon">
                                            <i class="bi bi-building"></i>
                                            <select class="form-select form-control" name="course" aria-placeholder="Course" required>
                                                <option value="">Select Campus</option>
                                                <option value="LSPU - San Pablo">LSPU - San Pablo</option>
                                                <option value="LSPU - Los Baños">LSPU - Los Baños</option>
                                                <option value="LSPU - Siniloan">LSPU - Siniloan</option>
                                                <option value="LSPU - Sta. Cruz">LSPU - Sta. Cruz</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8 photo">
                                        <label class="form-label">Photo</label>
                                        <input type="file" name="photo" class="form-control" accept="image/jpeg, image/png, image/gif">
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="submitJob">{{ selectedJob ? 'Update Job' : 'Post Job' }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Job Modal -->
        <div class="modal fade" id="editJobModal" tabindex="-1" aria-labelledby="editJobModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editJobModalLabel">Edit Job Posting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="updateJob">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Job Title</label>
                                    <input type="text" class="form-control" v-model="newJob.title" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Company</label>
                                    <select class="form-select" v-model="newJob.department" required>
                                        <option value="">Select Department</option>
                                        <option v-for="department in departments" :key="department" :value="department">
                                            {{ department }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Job Type</label>
                                    <select class="form-select" v-model="newJob.type" required>
                                        <option value="">Select Type</option>
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Internship">Internship</option>
                                        <option value="Remote">Remote</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Location</label>
                                    <input type="text" class="form-control" v-model="newJob.location" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Job Description</label>
                                <textarea class="form-control" rows="3" v-model="newJob.description" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Requirements</label>
                                <textarea class="form-control" rows="3" v-model="newJob.requirements" required></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Salary Range</label>
                                    <input type="text" class="form-control" v-model="newJob.salary">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" v-model="newJob.status" required>
                                        <option value="Active">Active</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">What is your company’s unique value proposition?</label>
                                <textarea class="form-control" rows="3" v-model="newJob.employerQuestion" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Job Qualifications</label>
                                <textarea class="form-control" rows="3" v-model="newJob.qualifications" required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="updateJob">Update Job</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Job Modal -->
        <div class="modal fade" id="jobDetailsModal" tabindex="-1" aria-labelledby="jobDetailsModalLabel" aria-hidden="true" v-if="selectedJob">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="jobDetailsModalLabel">{{ selectedJob.title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="hideJobDetails"></button>
                    </div>
                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                        <!-- Company Information -->
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex align-items-center">
                                <img :src="selectedJob.companyLogo" :alt="selectedJob.company" class="company-logo me-3">
                                <div>
                                    <h3 class="mb-0">{{ selectedJob.department }}</h3>
                                    <p class="mb-0 text-muted">{{ selectedJob.location }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Job Badges (Location, Type, Salary) -->
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

                        <!-- Job Description -->
                        <div class="mb-4">
                            <h5 class="mb-3">Job Description</h5>
                            <p>{{ selectedJob.description }}</p>
                        </div>

                        <!-- Qualifications -->
                        <div class="mb-4">
                            <h5 class="mb-3">Qualifications</h5>
                            <p>{{ selectedJob.qualifications }}</p>
                        </div>

                        <!-- Employer Questions (Accordion) -->
                        <div class="mb-4">
                            <h5 class="mb-3">Employer Questions</h5>
                            <p>{{ selectedJob.employer_question }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete the job posting "{{ selectedJob ? selectedJob.title : '' }}"?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" @click="deleteJob" data-bs-dismiss="modal">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</body>

</html>