<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator - Job Posting</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_job.css">
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

                <a class="sidebar-item sidebar-dropdown-toggle active" data-bs-toggle="collapse" href="admin_job.php" role="button" aria-expanded="false">
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

                <a class="sidebar-item sidebar-dropdown-toggle" data-bs-toggle="collapse" href="#alumniDropdown" role="button" aria-expanded="false">
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
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid">
                <div class="dashboard-card">
                    <div class="table-header">
                        <h2 class="mb-0">Job Postings</h2>
                        <div class="d-flex align-items-center gap-3">
                            <button class=" add-job-btn" data-bs-toggle="modal" data-bs-target="#addJobModal">
                                <i class="fas fa-plus"></i> Post New Job
                            </button>
                            <button class="btn btn-success" @click="exportToExcel"><i class="fas fa-file-excel me-1 p-lg-0"></i> Export Excel</button>
                            <button class="btn btn-danger" @click="exportToPDF"><i class="fas fa-file-pdf me-1"></i> Export PDF</button>
                        </div>
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
                                <option value="">All Company</option>
                                <option v-for="dept in uniqueDepartments" :key="dept">{{ dept }}</option>
                            </select>

                            <select class="form-select flex-grow-1" style="min-width: 140px;" v-model="filters.type" @change="filterJobs">
                                <option value="">All Types</option>
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
                                    <th>Job Title</th>
                                    <th>Company</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Posted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="job in paginatedJobs" :key="job.id">
                                    <td>
                                        <strong>{{ job.title }}</strong>
                                    </td>
                                    <td>{{ job.department }}</td>
                                    <td>{{ job.type }}</td>
                                    <td>{{ job.location }}</td>
                                    <td>
                                        <span :class="'status-badge status-' + job.status.toLowerCase()">
                                            {{ job.status }}
                                        </span>
                                    </td>
                                    <td>{{ formatDate(job.created_at) }}</td>
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
                        <form @submit.prevent="submitJob">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="jobTitle" class="form-label">Job Title</label>
                                    <input type="text" class="form-control" id="jobTitle" v-model="newJob.title" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="jobDepartment" class="form-label">Company</label>
                                    <select class="form-select" id="jobDepartment" v-model="newJob.department" required>
                                        <option value="">Select Department</option>
                                        <option v-for="department in departments" :key="department" :value="department">
                                            {{ department }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="jobType" class="form-label">Job Type</label>
                                    <select class="form-select" id="jobType" v-model="newJob.type" required>
                                        <option value="">Select Type</option>
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Internship">Internship</option>
                                        <option value="Remote">Remote</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="jobLocation" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="jobLocation" v-model="newJob.location" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="jobDescription" class="form-label">Job Description</label>
                                <textarea class="form-control" id="jobDescription" rows="3" v-model="newJob.description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="jobRequirements" class="form-label">Requirements</label>
                                <textarea class="form-control" id="jobRequirements" rows="3" v-model="newJob.requirements" required></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="jobSalary" class="form-label">Salary Range</label>
                                    <input type="text" class="form-control" id="jobSalary" v-model="newJob.salary">
                                </div>
                                <div class="col-md-6">
                                    <label for="jobStatus" class="form-label">Status</label>
                                    <select class="form-select" id="jobStatus" v-model="newJob.status" required>
                                        <option value="Active">Active</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Employer Question Section -->
                            <div class="mb-3">
                                <label for="employerQuestion" class="form-label">What is your company’s unique value proposition?</label>
                                <textarea class="form-control" id="employerQuestion" rows="3" v-model="newJob.employerQuestion" required></textarea>
                            </div>

                            <!-- Job Qualifications Section -->
                            <div class="mb-3">
                                <label for="jobQualifications" class="form-label">Job Qualifications</label>
                                <textarea class="form-control" id="jobQualifications" rows="3" v-model="newJob.qualifications" required></textarea>
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
    <script src="js/admin_job.js"></script>
</body>

</html>