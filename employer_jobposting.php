<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard - Job Postings</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/employer_job.css">
</head>

<body>
    <div id="app">
        <!-- Sidebar -->
        <div class="sidebar" :class="{ 'active': sidebarActive }">
            <div class="sidebar-brand">
                <img src="images/alumni.png" alt="Logo" class="sidebar-logo">
                <span class="sidebar-brand-name">LSPU Employer</span>
            </div>
            <div class="sidebar-menu">
                <a href="#" class="sidebar-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="sidebar-item active">
                    <i class="fas fa-briefcase"></i>
                    <span>Job Postings</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-users"></i>
                    <span>Applicants</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Interviews</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Analytics</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-building"></i>
                    <span>Company Profile</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <div class="mt-auto px-3 py-4">
                    <div class="d-flex align-items-center">
                        <img :src="employer.logo || 'https://via.placeholder.com/150'" alt="Profile" class="profile-img">
                        <div class="ms-2">
                            <div class="text-white small">{{ employer.name }}</div>
                            <div class="text-muted small">{{ employer.email }}</div>
                        </div>
                    </div>
                </div>
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
                        <span class="profile-name d-none d-md-inline">{{ employer.name }}</span>
                        <img :src="employer.logo || 'https://via.placeholder.com/150'" alt="Profile" class="profile-img">
                        <i class="fas fa-chevron-down small ms-1 d-none d-md-inline"></i>
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
                        <div class="d-flex align-items-center">
                            <div class="search-box me-3">
                                <i class="fas fa-search"></i>
                                <input type="text" class="form-control" placeholder="Search jobs..." v-model="searchQuery" @input="filterJobs">
                            </div>
                            <button class="add-job-btn" data-bs-toggle="modal" data-bs-target="#addJobModal">
                                <i class="fas fa-plus"></i> Post New Job
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Department</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Applications</th>
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
                                    <td>{{ job.applications }}</td>
                                    <td>
                                        <span :class="'status-badge status-' + job.status.toLowerCase()">
                                            {{ job.status }}
                                        </span>
                                    </td>
                                    <td>{{ formatDate(job.postedDate) }}</td>
                                    <td>
                                        <div class="action-dropdown dropdown">
                                            <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#" @click="viewJob(job)"><i class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item" href="#" @click="editJob(job)"><i class="fas fa-edit me-2"></i> Edit</a></li>
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
        </main>

        <!-- Add Job Modal -->
        <div class="modal fade" id="addJobModal" tabindex="-1" aria-labelledby="addJobModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addJobModalLabel">Post New Job</h5>
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
                                    <label for="jobDepartment" class="form-label">Department</label>
                                    <select class="form-select" id="jobDepartment" v-model="newJob.department" required>
                                        <option value="">Select Department</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Human Resources">Human Resources</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Operations">Operations</option>
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
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="submitJob">Post Job</button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.min.js"></script>
    <script src="js/employer_job.js"></script>
</body>

</html>