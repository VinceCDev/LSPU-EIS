<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator - Applicants</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_applicant.css">
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

                <a href="admin_applicant.php" class="sidebar-item active">
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
                        <h2 class="mb-0">Applicants</h2>
                        <div class="d-flex align-items-center gap-1">
                            <button class="add-applicant-btn" data-bs-toggle="modal" data-bs-target="#addApplicantModal">
                                <i class="fas fa-plus"></i> Add Applicant
                            </button>
                            <button class="btn btn-success" @click="exportToExcel"><i class="fas fa-file-excel me-1 p-lg-0"></i> Export Excel</button>
                            <button class="btn btn-danger" @click="exportToPDF"><i class="fas fa-file-pdf me-1"></i> Export PDF</button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <!-- Search (Left Side) -->
                        <div class="search-box me-3" style="max-width: 350px;">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" placeholder="Search applicants..." v-model="searchQuery" @input="filterApplicants">
                        </div>

                        <!-- Filter Options (Right Side) -->
                        <div class="d-flex flex-nowrap gap-2">
                            <select class="form-select flex-grow-1" style="min-width: 140px;">
                                <option value="">All Company</option>
                                <option v-for="dept in uniqueDepartments" :key="dept">{{ dept }}</option>
                            </select>

                            <select class="form-select flex-grow-1" style="min-width: 140px;">
                                <option value="">All Types</option>
                                <option v-for="type in uniqueTypes" :key="type">{{ type }}</option>
                            </select>

                            <select class="form-select flex-grow-1" style="min-width: 140px;">
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
                                    <th>Applicant</th>
                                    <th>Applied For</th>
                                    <th>Applied Date</th>
                                    <th>Experience</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="applicant in paginatedApplicants" :key="applicant.id">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img :src="applicant.profileImage || 'https://via.placeholder.com/150'" alt="Profile" class="applicant-avatar">
                                            <div>
                                                <strong>{{ applicant.name }}</strong>
                                                <div class="small text-muted">{{ applicant.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ applicant.appliedFor }}</td>
                                    <td>{{ formatDate(applicant.appliedDate) }}</td>
                                    <td>{{ applicant.experience }} years</td>
                                    <td>
                                        <span :class="'status-badge status-' + applicant.status.toLowerCase()">
                                            {{ applicant.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-dropdown dropdown">
                                            <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#" @click="viewApplicant(applicant)"><i class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item" href="#" @click="editApplicant(applicant)"><i class="fas fa-edit me-2"></i> Edit</a></li>
                                                <li><a class="dropdown-item" href="#" @click="confirmDelete(applicant)"><i class="fas fa-trash me-2"></i> Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredApplicants.length === 0">
                                    <td colspan="6" class="text-center py-4 text-muted">No applicants found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="pagination-info">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredApplicants.length) }} of {{ filteredApplicants.length }} entries
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

        <!-- Add/Edit Applicant Modal -->
        <div class="modal fade" id="addApplicantModal" tabindex="-1" aria-labelledby="addApplicantModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addApplicantModalLabel">{{ selectedApplicant ? 'Edit Applicant' : 'Add New Applicant' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitApplicant">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="applicantName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="applicantName" v-model="newApplicant.name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="applicantEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="applicantEmail" v-model="newApplicant.email" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="applicantPhone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="applicantPhone" v-model="newApplicant.phone">
                                </div>
                                <div class="col-md-6">
                                    <label for="applicantLocation" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="applicantLocation" v-model="newApplicant.location">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="applicantPosition" class="form-label">Applied For</label>
                                    <input type="text" class="form-control" id="applicantPosition" v-model="newApplicant.appliedFor" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="applicantExperience" class="form-label">Experience (years)</label>
                                    <input type="number" class="form-control" id="applicantExperience" v-model="newApplicant.experience" min="0">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="applicantStatus" class="form-label">Status</label>
                                    <select class="form-select" id="applicantStatus" v-model="newApplicant.status" required>
                                        <option value="New">New</option>
                                        <option value="Reviewed">Reviewed</option>
                                        <option value="Interview">Interview</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Hired">Hired</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="applicantDate" class="form-label">Applied Date</label>
                                    <input type="date" class="form-control" id="applicantDate" v-model="newApplicant.appliedDate" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="applicantSkills" class="form-label">Skills</label>
                                <input type="text" class="form-control" id="applicantSkills" v-model="newApplicant.skills" placeholder="Separate skills with commas">
                            </div>
                            <div class="mb-3">
                                <label for="applicantNotes" class="form-label">Notes</label>
                                <textarea class="form-control" id="applicantNotes" rows="3" v-model="newApplicant.notes"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="submitApplicant">{{ selectedApplicant ? 'Update' : 'Add' }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Applicant Modal -->
        <div class="modal fade" id="viewApplicantModal" tabindex="-1" aria-labelledby="viewApplicantModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewApplicantModalLabel">{{ selectedApplicant ? selectedApplicant.name : '' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedApplicant" class="row">
                            <div class="col-md-4 text-center">
                                <img :src="selectedApplicant.profileImage || 'https://via.placeholder.com/150'" alt="Profile" class="applicant-profile-img">
                                <h6 class="mt-2">{{ selectedApplicant.email }}</h6>
                                <p><i class="fas fa-phone me-2"></i> {{ selectedApplicant.phone || 'Not provided' }}</p>
                                <p><i class="fas fa-map-marker-alt me-2"></i> {{ selectedApplicant.location || 'Not provided' }}</p>

                                <div class="profile-section">
                                    <h6 class="profile-section-title">Application Status</h6>
                                    <span :class="'status-badge status-' + selectedApplicant.status.toLowerCase()">
                                        {{ selectedApplicant.status }}
                                    </span>
                                </div>

                                <div class="profile-section" v-if="selectedApplicant.skills">
                                    <h6 class="profile-section-title">Skills</h6>
                                    <div>
                                        <span class="skills-tag" v-for="skill in selectedApplicant.skills.split(',')" :key="skill">{{ skill.trim() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="profile-section">
                                    <h6 class="profile-section-title">Application Details</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Applied For:</strong> {{ selectedApplicant.appliedFor }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Applied Date:</strong> {{ formatDate(selectedApplicant.appliedDate) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Experience:</strong> {{ selectedApplicant.experience }} years</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="profile-section" v-if="selectedApplicant.notes">
                                    <h6 class="profile-section-title">Notes</h6>
                                    <p>{{ selectedApplicant.notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                        Are you sure you want to delete the applicant "{{ selectedApplicant ? selectedApplicant.name : '' }}"?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" @click="deleteApplicant" data-bs-dismiss="modal">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vue.js -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.min.js"></script>
    <script src="js/admin_applicant.js"></script>
</body>

</html>