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

                <a class="sidebar-item sidebar-dropdown-toggle active"
                    data-bs-toggle="collapse"
                    href="#alumniDropdown"
                    role="button"
                    :aria-expanded="isAlumniOpen ? 'true' : 'false'"
                    @click="isAlumniOpen = !isAlumniOpen">
                    <i class="fas fa-user-graduate"></i>
                    <span>Alumni</span>
                </a>
                <div :class="['sidebar-dropdown collapse', isAlumniOpen ? 'show' : '']" id="alumniDropdown">
                    <a href="admin_alumni.php" class="sidebar-dropdown-item" :class="{ active: currentPage === 'admin_alumni.php' }">Manage Alumni</a>
                    <a href="admin_alumni_pending.php" class="sidebar-dropdown-item" :class="{ active: currentPage === 'admin_alumni_pending.php' }">Pending Alumni</a>
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
                        <img :src="selectedAlumni?.logo || 'https://dummyimage.com/150'" alt="Profile" class="profile-img">
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
                            <input type="text" class="form-control" placeholder="Search alumni..." v-model="searchQuery" @input="filteredAlumni">
                        </div>

                        <!-- Filter Options (Right Side) -->
                        <div class="d-flex flex-nowrap gap-2">
                            <select class="form-select flex-grow-1" style="min-width: 140px;" v-model="filters.campus" @change="filteredAlumni">
                                <option value="">All Campus</option>
                                <option v-for="campus in uniqueDepartments" :key="campus">{{ campus }}</option>
                                <!-- LSPU Campus -->
                            </select>

                            <select class="form-select flex-grow-1" style="min-width: 140px;" v-model="filters.year" @change="filteredAlumni">
                                <option value="">All Year</option>
                                <!-- All Years -->
                                <option v-for="year in uniqueYear" :key="year">{{ year }}</option>
                            </select>


                            <select class="form-select flex-grow-1" style="min-width: 140px;" v-model="filters.course" @change="filteredAlumni">
                                <option value="">Courses</option>
                                <option v-for="course in uniqueTypes" :key="course">{{ course }}</option>
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
                                <tr v-for="alumni in paginatedAlumni" :key="alumni.id">
                                    <td><strong>{{ alumni.first_name }} {{ alumni.last_name }}</strong></td>
                                    <td>{{ alumni.gender }}</td>
                                    <td>{{ alumni.email }}</td>
                                    <td>{{ alumni.year_graduated }}</td>
                                    <td>{{ alumni.course }}</td>
                                    <td>
                                        <span :class="'status-badge status-' + alumni.status.toLowerCase()">
                                            {{ alumni.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-dropdown dropdown position-relative">
                                            <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#" @click="viewAlumni(alumni)"><i class="fas fa-eye me-2"></i> View</a></li>
                                                <li><a class="dropdown-item" href="#" @click="approveAlumni(alumni)"><i class="fas fa-check-circle me-2"></i> Approve</a></li>
                                                <li><a class="dropdown-item" href="#" @click="confirmDelete(alumni)"><i class="fas fa-trash me-2"></i> Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredAlumni.length === 0">
                                    <td colspan="8" class="text-center py-4 text-muted">No alumni register found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="pagination-info">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredAlumni.length) }} of {{ filteredAlumni.length }} entries
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

        <div class="modal fade" id="alumniDetailsModal" tabindex="-1" aria-labelledby="alumniDetailsModalLabel" v-if="selectedAlumni">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-4 fw-bold" id="alumniDetailsModalLabel">Alumni Identification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" @click="selectedAlumni = null" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <!-- Full-width photo box -->
                        <div class="alumni-photo-box bg-light p-4 text-center">
                            <img :src="`/uploads/${selectedAlumni.alumni_id_photo}`" alt="Alumni Photo"
                                class="img-fluid rounded shadow" style="max-height: 300px; width: auto; object-fit: cover;">
                        </div>

                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="fw-bold text-primary mb-0">{{ selectedAlumni.first_name }} {{ selectedAlumni.middle_name }} {{ selectedAlumni.last_name }}</h3>
                                <span :class="'badge fs-6 py-2 bg-' + (selectedAlumni.status === 'Active' ? 'success' : 'secondary')">
                                    {{ selectedAlumni.status }}
                                </span>
                            </div>

                            <div class="row">
                                <!-- Personal Info -->
                                <div class="col-md-6 mb-4">
                                    <h5 class="fw-bold border-bottom pb-2 mb-3">Personal Information</h5>
                                    <div class="mb-2">
                                        <span class="d-block text-muted small">Gender</span>
                                        <span class="fw-medium">{{ selectedAlumni.gender }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="d-block text-muted small">Birthdate</span>
                                        <span class="fw-medium">{{ selectedAlumni.birthdate }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="d-block text-muted small">Civil Status</span>
                                        <span class="fw-medium">{{ selectedAlumni.civil_status }}</span>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="col-md-6 mb-4">
                                    <h5 class="fw-bold border-bottom pb-2 mb-3">Contact Information</h5>
                                    <div class="mb-2">
                                        <span class="d-block text-muted small">Email</span>
                                        <span class="fw-medium">{{ selectedAlumni.email }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="d-block text-muted small">Contact Number</span>
                                        <span class="fw-medium">{{ selectedAlumni.contact_number }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="d-block text-muted small">Location</span>
                                        <span class="fw-medium">{{ selectedAlumni.city }}, {{ selectedAlumni.province }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Education Info -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="fw-bold border-bottom pb-2 mb-3">Education Information</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <span class="d-block text-muted small">Year Graduated</span>
                                            <span class="fw-medium">{{ selectedAlumni.year_graduated }}</span>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <span class="d-block text-muted small">Campus</span>
                                            <span class="fw-medium">{{ selectedAlumni.campus }}</span>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <span class="d-block text-muted small">Course</span>
                                            <span class="fw-medium">{{ selectedAlumni.course }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.0/dist/vue.global.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="js/alumni_pending.js"></script>
</body>

</html>