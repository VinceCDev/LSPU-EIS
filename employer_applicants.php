<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard - Applicants</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --header-height: 70px;
            --sidebar-width: 280px;
            --logo-size: 40px;
            --profile-img-size: 40px;
            --primary-color: #2557a7;
            --secondary-color: #f8f9fa;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
            --sidebar-hover: #34495e;
            --sidebar-active: #3498db;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            padding-top: var(--header-height);
            min-height: 100vh;
        }

        /* Header Styles */
        header {
            height: var(--header-height);
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            width: 100%;
            padding: 0 20px;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 1100;
            display: flex;
            flex-direction: column;
            color: var(--sidebar-text);
        }

        .sidebar-brand {
            padding: 20px;
            display: flex;
            align-items: center;
            height: var(--header-height);
            background-color: rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            width: var(--logo-size);
            height: var(--logo-size);
            margin-right: 10px;
        }

        .sidebar-brand-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 20px 0;
        }

        .sidebar-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.2s;
            margin: 0 10px;
            border-radius: 5px;
        }

        .sidebar-item:hover {
            background-color: var(--sidebar-hover);
            color: white;
        }

        .sidebar-item.active {
            background-color: var(--sidebar-active);
            color: white;
            font-weight: 500;
        }

        .sidebar-item i {
            width: 24px;
            margin-right: 12px;
            text-align: center;
            font-size: 1rem;
        }

        .sidebar-item span {
            font-size: 0.95rem;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 25px;
            transition: all 0.3s ease;
            min-height: calc(100vh - var(--header-height));
        }

        /* Dashboard Cards */
        .dashboard-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 25px;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            position: relative;
            overflow: hidden;
            border: none;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 15px;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .card-change {
            font-size: 0.85rem;
            color: #28a745;
        }

        .card-change.negative {
            color: #dc3545;
        }

        .card-icon {
            font-size: 3.5rem;
            color: var(--primary-color);
            opacity: 0.1;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
        }

        .data-table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 1px solid #eee;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover td {
            background-color: #f9f9f9;
        }

        /* Applicant Avatar */
        .applicant-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-new {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .status-reviewed {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .status-interview {
            background-color: #fff3e0;
            color: #ff6d00;
        }

        .status-rejected {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .status-hired {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        /* Buttons */
        .btn-action {
            padding: 5px 12px;
            font-size: 0.85rem;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
        }

        .btn-action i {
            margin-right: 5px;
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block !important;
            }

            .header-content {
                justify-content: space-between;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .dashboard-card {
                padding: 20px;
            }

            .data-table th,
            .data-table td {
                padding: 12px 10px;
            }
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #555;
            padding: 5px;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .profile-img {
            width: var(--profile-img-size);
            height: var(--profile-img-size);
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 2px solid #eee;
        }

        .profile-name {
            font-weight: 500;
            margin-right: 5px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Applicants Table */
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            padding-left: 35px;
            border-radius: 20px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 10px;
            color: #aaa;
        }

        .action-dropdown .dropdown-toggle::after {
            display: none;
        }

        .action-dropdown .btn {
            padding: 5px 10px;
            background: none;
            border: none;
            color: #666;
        }

        .action-dropdown .btn:hover {
            background-color: #f1f1f1;
        }

        .pagination-info {
            font-size: 0.9rem;
            color: #666;
        }

        /* Applicant Profile Modal */
        .applicant-profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            border: 5px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-section {
            margin-bottom: 25px;
        }

        .profile-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        .resume-preview {
            height: 500px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 15px;
        }

        .resume-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .skills-tag {
            display: inline-block;
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 3px 10px;
            border-radius: 20px;
            margin-right: 5px;
            margin-bottom: 5px;
            font-size: 0.8rem;
        }

        .status-select {
            width: 150px;
            display: inline-block;
            margin-left: 10px;
        }

        /* Notes Section */
        .notes-container {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .note-item {
            background-color: #f8f9fa;
            border-left: 3px solid var(--primary-color);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 0 5px 5px 0;
        }

        .note-date {
            font-size: 0.7rem;
            color: #666;
        }
    </style>
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
                <a href="#" class="sidebar-item">
                    <i class="fas fa-briefcase"></i>
                    <span>Job Postings</span>
                </a>
                <a href="#" class="sidebar-item active">
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
                        <h2 class="mb-0">Applicants</h2>
                        <div class="d-flex align-items-center">
                            <div class="search-box me-3">
                                <i class="fas fa-search"></i>
                                <input type="text" class="form-control" placeholder="Search applicants..." v-model="searchQuery" @input="filterApplicants">
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                    <li>
                                        <h6 class="dropdown-header">Status</h6>
                                    </li>
                                    <li><a class="dropdown-item" href="#" @click="setStatusFilter('All')">All Applicants</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#" @click="setStatusFilter('New')"><span class="status-badge status-new me-2">New</span> New</a></li>
                                    <li><a class="dropdown-item" href="#" @click="setStatusFilter('Reviewed')"><span class="status-badge status-reviewed me-2">Reviewed</span> Reviewed</a></li>
                                    <li><a class="dropdown-item" href="#" @click="setStatusFilter('Interview')"><span class="status-badge status-interview me-2">Interview</span> Interview</a></li>
                                    <li><a class="dropdown-item" href="#" @click="setStatusFilter('Rejected')"><span class="status-badge status-rejected me-2">Rejected</span> Rejected</a></li>
                                    <li><a class="dropdown-item" href="#" @click="setStatusFilter('Hired')"><span class="status-badge status-hired me-2">Hired</span> Hired</a></li>
                                </ul>
                            </div>
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
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#" @click="viewApplicant(applicant)"><i class="fas fa-eye me-2"></i> View Profile</a></li>
                                                <li><a class="dropdown-item" href="#" @click="editApplicant(applicant)"><i class="fas fa-edit me-2"></i> Edit Status</a></li>
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
        </main>

        <!-- Applicant Profile Modal -->
        <div class="modal fade" id="applicantModal" tabindex="-1" aria-labelledby="applicantModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="applicantModalLabel">Applicant Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedApplicant" class="row">
                            <div class="col-md-4 text-center">
                                <img :src="selectedApplicant.profileImage || 'https://via.placeholder.com/150'" alt="Profile" class="applicant-profile-img">
                                <h4 class="mt-2">{{ selectedApplicant.name }}</h4>
                                <p class="text-muted">{{ selectedApplicant.email }}</p>
                                <p><i class="fas fa-phone me-2"></i> {{ selectedApplicant.phone }}</p>
                                <p><i class="fas fa-map-marker-alt me-2"></i> {{ selectedApplicant.location }}</p>

                                <div class="profile-section">
                                    <h6 class="profile-section-title">Application Status</h6>
                                    <select class="form-select status-select" v-model="selectedApplicant.status">
                                        <option value="New">New</option>
                                        <option value="Reviewed">Reviewed</option>
                                        <option value="Interview">Interview</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Hired">Hired</option>
                                    </select>
                                </div>

                                <div class="profile-section">
                                    <h6 class="profile-section-title">Skills</h6>
                                    <div>
                                        <span class="skills-tag" v-for="skill in selectedApplicant.skills" :key="skill">{{ skill }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="profile-section">
                                    <h6 class="profile-section-title">About</h6>
                                    <p>{{ selectedApplicant.about }}</p>
                                </div>

                                <div class="profile-section">
                                    <h6 class="profile-section-title">Education</h6>
                                    <div class="mb-3" v-for="edu in selectedApplicant.education" :key="edu.degree">
                                        <h6>{{ edu.degree }}</h6>
                                        <p class="mb-1">{{ edu.institution }}</p>
                                        <small class="text-muted">{{ edu.year }}</small>
                                    </div>
                                </div>

                                <div class="profile-section">
                                    <h6 class="profile-section-title">Experience</h6>
                                    <div class="mb-3" v-for="exp in selectedApplicant.experience" :key="exp.position">
                                        <h6>{{ exp.position }}</h6>
                                        <p class="mb-1">{{ exp.company }}</p>
                                        <p class="mb-1">{{ exp.duration }}</p>
                                        <p>{{ exp.description }}</p>
                                    </div>
                                </div>

                                <div class="profile-section">
                                    <h6 class="profile-section-title">Notes</h6>
                                    <div class="notes-container">
                                        <div class="note-item" v-for="note in selectedApplicant.notes" :key="note.date">
                                            <p>{{ note.content }}</p>
                                            <small class="note-date">{{ formatDateTime(note.date) }}</small>
                                        </div>
                                        <div v-if="selectedApplicant.notes.length === 0" class="text-muted">
                                            No notes added yet
                                        </div>
                                    </div>
                                    <div class="input-group mt-2">
                                        <input type="text" class="form-control" placeholder="Add a note..." v-model="newNote">
                                        <button class="btn btn-primary" type="button" @click="addNote">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="downloadResume" v-if="selectedApplicant && selectedApplicant.resume">
                            <i class="fas fa-download me-2"></i> Download Resume
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resume View Modal -->
        <div class="modal fade" id="resumeModal" tabindex="-1" aria-labelledby="resumeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resumeModalLabel">{{ selectedApplicant ? selectedApplicant.name + "'s Resume" : '' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="resume-preview">
                            <iframe :src="selectedApplicant ? selectedApplicant.resume : ''" class="resume-iframe" frameborder="0"></iframe>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="downloadResume" v-if="selectedApplicant && selectedApplicant.resume">
                            <i class="fas fa-download me-2"></i> Download
                        </button>
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
                        Are you sure you want to delete the application from {{ selectedApplicant ? selectedApplicant.name : '' }}?
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
    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    sidebarActive: false,
                    employer: {
                        name: "Tech Solutions Inc.",
                        email: "contact@techsolutions.com",
                        logo: "https://via.placeholder.com/150"
                    },
                    applicants: [{
                            id: 1,
                            name: "Juan Dela Cruz",
                            email: "juan.delacruz@example.com",
                            phone: "+63 912 345 6789",
                            location: "Manila, Philippines",
                            profileImage: "https://randomuser.me/api/portraits/men/32.jpg",
                            appliedFor: "Frontend Developer",
                            appliedDate: "2023-06-15",
                            experience: 3,
                            status: "New",
                            about: "Frontend developer with 3 years of experience in React and Vue.js. Passionate about creating responsive and user-friendly web applications.",
                            skills: ["HTML", "CSS", "JavaScript", "React", "Vue.js", "Bootstrap"],
                            education: [{
                                degree: "BS in Computer Science",
                                institution: "University of the Philippines",
                                year: "2015-2019"
                            }],
                            experience: [{
                                    position: "Frontend Developer",
                                    company: "WebTech Solutions",
                                    duration: "2020-Present",
                                    description: "Developed and maintained multiple web applications using React and Vue.js."
                                },
                                {
                                    position: "Web Developer Intern",
                                    company: "Digital Creations",
                                    duration: "Summer 2019",
                                    description: "Assisted in developing company websites and web applications."
                                }
                            ],
                            resume: "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                            notes: [{
                                content: "Strong portfolio with impressive React projects.",
                                date: "2023-06-16T09:30:00"
                            }]
                        },
                        {
                            id: 2,
                            name: "Maria Santos",
                            email: "maria.santos@example.com",
                            phone: "+63 917 890 1234",
                            location: "Cebu, Philippines",
                            profileImage: "https://randomuser.me/api/portraits/women/44.jpg",
                            appliedFor: "Marketing Specialist",
                            appliedDate: "2023-06-10",
                            experience: 5,
                            status: "Reviewed",
                            about: "Marketing professional with expertise in digital marketing and social media strategies. Experienced in running successful campaigns for various industries.",
                            skills: ["Digital Marketing", "Social Media", "SEO", "Content Creation", "Google Analytics"],
                            education: [{
                                degree: "BS in Marketing",
                                institution: "Ateneo de Manila University",
                                year: "2014-2018"
                            }],
                            experience: [{
                                position: "Digital Marketing Specialist",
                                company: "BrandGrowth Inc.",
                                duration: "2019-Present",
                                description: "Managed digital marketing campaigns and increased online engagement by 40%."
                            }],
                            resume: "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                            notes: [{
                                content: "Excellent communication skills. Good fit for customer-facing roles.",
                                date: "2023-06-11T14:15:00"
                            }]
                        },
                        {
                            id: 3,
                            name: "Robert Lim",
                            email: "robert.lim@example.com",
                            phone: "+63 918 765 4321",
                            location: "Davao, Philippines",
                            profileImage: "https://randomuser.me/api/portraits/men/67.jpg",
                            appliedFor: "DevOps Engineer",
                            appliedDate: "2023-06-05",
                            experience: 4,
                            status: "Interview",
                            about: "DevOps engineer with experience in cloud infrastructure and CI/CD pipelines. Certified AWS Solutions Architect with a passion for automation.",
                            skills: ["AWS", "Docker", "Kubernetes", "CI/CD", "Terraform", "Linux"],
                            education: [{
                                degree: "BS in Information Technology",
                                institution: "De La Salle University",
                                year: "2013-2017"
                            }],
                            experience: [{
                                position: "DevOps Engineer",
                                company: "CloudScale Technologies",
                                duration: "2018-Present",
                                description: "Implemented CI/CD pipelines and managed cloud infrastructure on AWS."
                            }],
                            resume: "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                            notes: [{
                                    content: "Technical interview scheduled for June 20.",
                                    date: "2023-06-07T11:20:00"
                                },
                                {
                                    content: "Strong AWS knowledge. Good cultural fit.",
                                    date: "2023-06-06T16:45:00"
                                }
                            ]
                        },
                        {
                            id: 4,
                            name: "Anna Reyes",
                            email: "anna.reyes@example.com",
                            phone: "+63 920 123 4567",
                            location: "Quezon City, Philippines",
                            profileImage: "https://randomuser.me/api/portraits/women/28.jpg",
                            appliedFor: "HR Manager",
                            appliedDate: "2023-05-28",
                            experience: 6,
                            status: "Rejected",
                            about: "HR professional with experience in talent acquisition and employee relations. Strong background in developing HR policies and procedures.",
                            skills: ["Recruitment", "Employee Relations", "HR Policies", "Training", "Performance Management"],
                            education: [{
                                    degree: "BS in Psychology",
                                    institution: "University of Santo Tomas",
                                    year: "2011-2015"
                                },
                                {
                                    degree: "MA in Human Resource Management",
                                    institution: "Miriam College",
                                    year: "2016-2018"
                                }
                            ],
                            experience: [{
                                position: "HR Manager",
                                company: "PeopleFirst Corp.",
                                duration: "2019-2023",
                                description: "Managed all HR functions for a 200-employee company."
                            }],
                            resume: "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                            notes: [{
                                content: "Not enough experience in tech industry.",
                                date: "2023-06-01T10:10:00"
                            }]
                        },
                        {
                            id: 5,
                            name: "Michael Tan",
                            email: "michael.tan@example.com",
                            phone: "+63 921 987 6543",
                            location: "Makati, Philippines",
                            profileImage: "https://randomuser.me/api/portraits/men/52.jpg",
                            appliedFor: "Financial Analyst",
                            appliedDate: "2023-06-12",
                            experience: 2,
                            status: "Hired",
                            about: "Financial analyst with strong analytical skills and attention to detail. Experienced in financial modeling and data analysis.",
                            skills: ["Financial Analysis", "Excel", "Financial Modeling", "Data Analysis", "SQL"],
                            education: [{
                                degree: "BS in Accountancy",
                                institution: "University of the Philippines",
                                year: "2016-2020"
                            }],
                            experience: [{
                                position: "Financial Analyst",
                                company: "Wealth Management Inc.",
                                duration: "2021-Present",
                                description: "Prepared financial reports and conducted market analysis."
                            }],
                            resume: "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                            notes: [{
                                    content: "Offer accepted. Starting date July 1.",
                                    date: "2023-06-14T15:30:00"
                                },
                                {
                                    content: "Excellent technical skills. Good potential.",
                                    date: "2023-06-13T09:15:00"
                                }
                            ]
                        },
                        {
                            id: 6,
                            name: "Sarah Gomez",
                            email: "sarah.gomez@example.com",
                            phone: "+63 923 456 7890",
                            location: "Iloilo, Philippines",
                            profileImage: "https://randomuser.me/api/portraits/women/63.jpg",
                            appliedFor: "UI/UX Designer",
                            appliedDate: "2023-06-18",
                            experience: 4,
                            status: "New",
                            about: "Creative UI/UX designer with a passion for creating intuitive user experiences. Skilled in user research and prototyping.",
                            skills: ["UI Design", "UX Research", "Figma", "Adobe XD", "Prototyping", "User Testing"],
                            education: [{
                                degree: "BS in Fine Arts",
                                institution: "University of the Philippines",
                                year: "2015-2019"
                            }],
                            experience: [{
                                position: "UI/UX Designer",
                                company: "DesignHub Studio",
                                duration: "2019-Present",
                                description: "Designed user interfaces for web and mobile applications."
                            }],
                            resume: "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                            notes: []
                        },
                        {
                            id: 7,
                            name: "David Ong",
                            email: "david.ong@example.com",
                            phone: "+63 925 678 9012",
                            location: "Baguio, Philippines",
                            profileImage: "https://randomuser.me/api/portraits/men/29.jpg",
                            appliedFor: "Backend Developer",
                            appliedDate: "2023-06-14",
                            experience: 5,
                            status: "Interview",
                            about: "Backend developer specializing in Node.js and Python. Experienced in building scalable APIs and microservices.",
                            skills: ["Node.js", "Python", "SQL", "MongoDB", "REST APIs", "Microservices"],
                            education: [{
                                degree: "BS in Computer Engineering",
                                institution: "Mapua University",
                                year: "2014-2018"
                            }],
                            experience: [{
                                position: "Backend Developer",
                                company: "TechSolutions Inc.",
                                duration: "2018-Present",
                                description: "Developed and maintained backend services for various applications."
                            }],
                            resume: "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                            notes: [{
                                content: "Technical interview scheduled for June 21.",
                                date: "2023-06-15T13:45:00"
                            }]
                        },
                        {
                            id: 8,
                            name: "Carla Ramirez",
                            email: "carla.ramirez@example.com",
                            phone: "+63 927 890 1234",
                            location: "Cavite, Philippines",
                            profileImage: "https://randomuser.me/api/portraits/women/35.jpg",
                            appliedFor: "Content Writer",
                            appliedDate: "2023-06-08",
                            experience: 3,
                            status: "Reviewed",
                            about: "Creative content writer with experience in blogging and copywriting. Skilled in SEO and content strategy.",
                            skills: ["Content Writing", "Copywriting", "SEO", "Blogging", "Content Strategy"],
                            education: [{
                                degree: "AB in English",
                                institution: "Ateneo de Manila University",
                                year: "2016-2020"
                            }],
                            experience: [{
                                position: "Content Writer",
                                company: "WordCraft Media",
                                duration: "2020-Present",
                                description: "Created engaging content for various clients and managed blog content."
                            }],
                            resume: "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                            notes: [{
                                content: "Writing samples are excellent. May be a good fit.",
                                date: "2023-06-09T10:20:00"
                            }]
                        }
                    ],
                    selectedApplicant: null,
                    searchQuery: "",
                    statusFilter: "All",
                    filteredApplicants: [],
                    currentPage: 1,
                    itemsPerPage: 5,
                    newNote: ""
                }
            },
            created() {
                this.filteredApplicants = [...this.applicants];
            },
            computed: {
                totalPages() {
                    return Math.ceil(this.filteredApplicants.length / this.itemsPerPage);
                },
                paginatedApplicants() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.filteredApplicants.slice(start, end);
                }
            },
            methods: {
                toggleSidebar() {
                    this.sidebarActive = !this.sidebarActive;
                },
                filterApplicants() {
                    this.filteredApplicants = this.applicants.filter(applicant => {
                        const matchesSearch =
                            applicant.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            applicant.email.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            applicant.appliedFor.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            applicant.location.toLowerCase().includes(this.searchQuery.toLowerCase());

                        const matchesStatus = this.statusFilter === "All" || applicant.status === this.statusFilter;

                        return matchesSearch && matchesStatus;
                    });
                    this.currentPage = 1;
                },
                setStatusFilter(status) {
                    this.statusFilter = status;
                    this.filterApplicants();
                },
                formatDate(dateString) {
                    const options = {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    };
                    return new Date(dateString).toLocaleDateString('en-US', options);
                },
                formatDateTime(dateString) {
                    const options = {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    };
                    return new Date(dateString).toLocaleDateString('en-US', options);
                },
                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                    }
                },
                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                    }
                },
                goToPage(page) {
                    this.currentPage = page;
                },
                viewApplicant(applicant) {
                    this.selectedApplicant = {
                        ...applicant
                    };
                    const modal = new bootstrap.Modal(document.getElementById('applicantModal'));
                    modal.show();
                },
                viewResume() {
                    const modal = new bootstrap.Modal(document.getElementById('resumeModal'));
                    modal.show();
                },
                editApplicant(applicant) {
                    this.selectedApplicant = {
                        ...applicant
                    };
                    const modal = new bootstrap.Modal(document.getElementById('applicantModal'));
                    modal.show();
                },
                confirmDelete(applicant) {
                    this.selectedApplicant = applicant;
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    modal.show();
                },
                deleteApplicant() {
                    this.applicants = this.applicants.filter(applicant => applicant.id !== this.selectedApplicant.id);
                    this.filterApplicants();
                    this.selectedApplicant = null;
                },
                downloadResume() {
                    if (this.selectedApplicant && this.selectedApplicant.resume) {
                        // In a real app, this would trigger the download
                        alert(`Downloading resume for ${this.selectedApplicant.name}`);
                    }
                },
                addNote() {
                    if (this.newNote.trim() && this.selectedApplicant) {
                        this.selectedApplicant.notes.unshift({
                            content: this.newNote,
                            date: new Date().toISOString()
                        });
                        this.newNote = "";

                        // Update the original applicant data
                        const index = this.applicants.findIndex(a => a.id === this.selectedApplicant.id);
                        if (index !== -1) {
                            this.applicants[index] = {
                                ...this.selectedApplicant
                            };
                        }
                    }
                }
            }
        }).mount('#app');
    </script>
</body>

</html>