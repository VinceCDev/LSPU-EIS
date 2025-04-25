<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard - Pending Companies</title>
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

            /* Enhanced Table Variables */
            --table-border-radius: 12px;
            --table-header-bg: #f8fafc;
            --table-row-hover: #f1f5f9;
            --table-border: #e2e8f0;
            --table-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            --table-transition: all 0.2s ease;
            --table-cell-padding: 1rem 1.25rem;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
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
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
            padding: 25px;
            margin-bottom: 25px;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            position: relative;
            overflow: hidden;
            border: none;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
        }

        /* Enhanced Table Styles */
        .enhanced-table {
            width: 100%;
        }

        .enhanced-table .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: var(--table-border-radius);
            overflow: hidden;
            box-shadow: var(--table-shadow);
        }

        .enhanced-table .table thead th {
            background-color: var(--table-header-bg);
            padding: var(--table-cell-padding);
            font-weight: 600;
            color: #334155;
            border-bottom: 1px solid var(--table-border);
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .enhanced-table .table tbody tr {
            transition: var(--table-transition);
        }

        .enhanced-table .table tbody tr:hover {
            background-color: var(--table-row-hover);
        }

        .enhanced-table .table td {
            padding: var(--table-cell-padding);
            border-bottom: 1px solid var(--table-border);
            vertical-align: middle;
            font-size: 0.925rem;
        }

        .enhanced-table .table tr:last-child td {
            border-bottom: none;
        }

        /* Company Logo */
        .company-logo {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            object-fit: cover;
            margin-right: 12px;
            border: 1px solid #e2e8f0;
            background-color: white;
            padding: 2px;
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        .status-badge i {
            margin-right: 4px;
            font-size: 0.65rem;
        }

        .status-pending {
            background-color: #fffbeb;
            color: #d97706;
        }

        .status-pending i {
            color: #f59e0b;
        }

        .status-approved {
            background-color: #ecfdf5;
            color: #059669;
        }

        .status-approved i {
            color: #10b981;
        }

        .status-rejected {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .status-rejected i {
            color: #ef4444;
        }

        /* Action Dropdown */
        .action-dropdown .btn {
            padding: 5px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            color: #64748b;
            transition: all 0.2s;
        }

        .action-dropdown .btn:hover {
            background-color: #f1f5f9;
            color: #334155;
        }

        .action-dropdown .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 8px;
        }

        .action-dropdown .dropdown-item {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .action-dropdown .dropdown-item i {
            width: 20px;
            margin-right: 8px;
            font-size: 0.8rem;
        }

        .action-dropdown .dropdown-item.view {
            color: #2563eb;
        }

        .action-dropdown .dropdown-item.approve {
            color: #059669;
        }

        .action-dropdown .dropdown-item.reject {
            color: #dc2626;
        }

        .action-dropdown .dropdown-item:hover {
            background-color: #f8fafc;
        }

        /* Search Box */
        .search-box {
            position: relative;
            width: 300px;
            transition: all 0.3s;
        }

        .search-box:focus-within {
            width: 350px;
        }

        .search-box input {
            padding-left: 40px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            height: 40px;
            font-size: 0.925rem;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        .pagination-info {
            font-size: 0.875rem;
            color: #64748b;
        }

        .pagination .page-item {
            margin: 0 4px;
        }

        .pagination .page-link {
            border: none;
            border-radius: 8px;
            min-width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .pagination .page-link:hover {
            background-color: #f1f5f9;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #cbd5e1;
            background-color: transparent;
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

            .search-box {
                width: 100%;
            }

            .search-box:focus-within {
                width: 100%;
            }

            .enhanced-table .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        /* Mobile Menu Button */
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

        /* View Company Modal */
        .company-modal .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1rem;
        }

        .company-modal .modal-body {
            padding-top: 1.5rem;
        }

        .company-modal .company-logo-lg {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background-color: white;
            padding: 4px;
        }

        .company-modal .detail-item {
            margin-bottom: 1rem;
        }

        .company-modal .detail-label {
            font-weight: 600;
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .company-modal .detail-value {
            font-size: 0.95rem;
            color: #334155;
        }

        /* Loading State */
        .loading-state {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .loading-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(37, 87, 167, 0.2);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Empty State */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 0;
            text-align: center;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #94a3b8;
            font-size: 0.925rem;
            max-width: 400px;
            margin: 0 auto;
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
                <a href="#" class="sidebar-item">
                    <i class="fas fa-users"></i>
                    <span>Applicants</span>
                </a>
                <a href="#" class="sidebar-item active">
                    <i class="fas fa-building"></i>
                    <span>Companies</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-user-graduate"></i>
                    <span>Alumni</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
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
                        <h2 class="mb-0">Pending Companies</h2>
                        <div class="d-flex align-items-center">
                            <div class="search-box me-3">
                                <i class="fas fa-search"></i>
                                <input type="text" class="form-control" placeholder="Search companies..." v-model="searchQuery" @input="filterCompanies">
                            </div>
                        </div>
                    </div>

                    <div class="enhanced-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Industry</th>
                                    <th>Location</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Applied Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="7" class="text-center py-4">
                                        <div class="loading-state">
                                            <div class="loading-spinner"></div>
                                            <span>Loading companies...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredCompanies.length === 0">
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="fas fa-building"></i>
                                            <h5>No Pending Companies</h5>
                                            <p>There are currently no companies waiting for approval.</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="company in paginatedCompanies" :key="company.id">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img :src="company.logo || 'https://via.placeholder.com/150'" alt="Logo" class="company-logo">
                                            <div>
                                                <strong>{{ company.name }}</strong>
                                                <div class="small text-muted">{{ company.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ company.industry }}</td>
                                    <td>{{ company.location }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ company.contactPerson }}</span>
                                            <small class="text-muted">{{ company.contactPhone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span :class="'status-badge status-' + company.status.toLowerCase()">
                                            <i class="fas" :class="{
                                                'fa-clock': company.status === 'Pending',
                                                'fa-check-circle': company.status === 'Approved',
                                                'fa-times-circle': company.status === 'Rejected'
                                            }"></i>
                                            {{ company.status }}
                                        </span>
                                    </td>
                                    <td>{{ formatDate(company.appliedDate) }}</td>
                                    <td>
                                        <div class="action-dropdown dropdown">
                                            <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item view" href="#" @click="viewCompany(company)">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a></li>
                                                <li><a class="dropdown-item approve" href="#" @click="approveCompany(company)">
                                                        <i class="fas fa-check"></i> Approve
                                                    </a></li>
                                                <li><a class="dropdown-item reject" href="#" @click="rejectCompany(company)">
                                                        <i class="fas fa-times"></i> Reject
                                                    </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-container" v-if="filteredCompanies.length > 0">
                        <div class="pagination-info">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredCompanies.length) }} of {{ filteredCompanies.length }} entries
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

        <!-- View Company Modal -->
        <div class="modal fade company-modal" id="viewCompanyModal" tabindex="-1" aria-labelledby="viewCompanyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewCompanyModalLabel">Company Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedCompany" class="row">
                            <div class="col-md-3 text-center mb-4 mb-md-0">
                                <img :src="selectedCompany.logo || 'https://via.placeholder.com/150'" alt="Logo" class="company-logo-lg mb-3">
                                <div class="mb-3">
                                    <span :class="'status-badge status-' + selectedCompany.status.toLowerCase()">
                                        <i class="fas" :class="{
                                            'fa-clock': selectedCompany.status === 'Pending',
                                            'fa-check-circle': selectedCompany.status === 'Approved',
                                            'fa-times-circle': selectedCompany.status === 'Rejected'
                                        }"></i>
                                        {{ selectedCompany.status }}
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Applied Date</div>
                                    <div class="detail-value">{{ formatDate(selectedCompany.appliedDate) }}</div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Company Name</div>
                                            <div class="detail-value">{{ selectedCompany.name }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Industry</div>
                                            <div class="detail-value">{{ selectedCompany.industry }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Location</div>
                                            <div class="detail-value">{{ selectedCompany.location }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Website</div>
                                            <div class="detail-value">
                                                <a :href="selectedCompany.website" target="_blank">{{ selectedCompany.website }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Contact Person</div>
                                            <div class="detail-value">{{ selectedCompany.contactPerson }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Contact Email</div>
                                            <div class="detail-value">{{ selectedCompany.contactEmail }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Contact Phone</div>
                                            <div class="detail-value">{{ selectedCompany.contactPhone }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12" v-if="selectedCompany.description">
                                        <div class="detail-item">
                                            <div class="detail-label">Description</div>
                                            <div class="detail-value">{{ selectedCompany.description }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" v-if="selectedCompany && selectedCompany.status === 'Pending'" @click="approveCompany(selectedCompany)" data-bs-dismiss="modal">
                            <i class="fas fa-check me-1"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger" v-if="selectedCompany && selectedCompany.status === 'Pending'" @click="rejectCompany(selectedCompany)" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Reject
                        </button>
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
                            name: "LSPU Admin",
                            email: "admin@lspu.edu.ph",
                            logo: "https://via.placeholder.com/150"
                        },
                        companies: [{
                                id: 1,
                                name: "Tech Innovations Inc.",
                                email: "contact@techinnovations.com",
                                logo: "https://logo.clearbit.com/techinnovations.com",
                                industry: "Information Technology",
                                location: "Manila, Philippines",
                                website: "https://techinnovations.com",
                                contactPerson: "John Smith",
                                contactEmail: "john@techinnovations.com",
                                contactPhone: "+63 912 345 6789",
                                description: "A leading technology company specializing in software development and AI solutions.",
                                status: "Pending",
                                appliedDate: "2023-06-15"
                            },
                            {
                                id: 2,
                                name: "Green Energy Solutions",
                                email: "info@greenenergy.com",
                                logo: "https://logo.clearbit.com/greenenergy.com",
                                industry: "Renewable Energy",
                                location: "Cebu, Philippines",
                                website: "https://greenenergy.com",
                                contactPerson: "Maria Garcia",
                                contactEmail: "maria@greenenergy.com",
                                contactPhone: "+63 917 890 1234",
                                description: "Providing sustainable energy solutions for a greener future.",
                                status: "Pending",
                                appliedDate: "2023-06-14"
                            },
                            {
                                id: 3,
                                name: "Global Logistics PH",
                                email: "support@globallogistics.ph",
                                logo: "https://logo.clearbit.com/globallogistics.ph",
                                industry: "Logistics",
                                location: "Davao, Philippines",
                                website: "https://globallogistics.ph",
                                contactPerson: "Robert Lim",
                                contactEmail: "robert@globallogistics.ph",
                                contactPhone: "+63 918 765 4321",
                                description: "International logistics and supply chain management company.",
                                status: "Pending",
                                appliedDate: "2023-06-12"
                            },
                            {
                                id: 4,
                                name: "FinServ Corporation",
                                email: "hello@finserv.ph",
                                logo: "https://logo.clearbit.com/finserv.ph",
                                industry: "Financial Services",
                                location: "Makati, Philippines",
                                website: "https://finserv.ph",
                                contactPerson: "Anna Reyes",
                                contactEmail: "anna@finserv.ph",
                                contactPhone: "+63 920 123 4567",
                                description: "Financial technology company offering innovative banking solutions.",
                                status: "Pending",
                                appliedDate: "2023-06-10"
                            },
                            {
                                id: 5,
                                name: "HealthPlus Medical",
                                email: "contact@healthplusmedical.com",
                                logo: "https://logo.clearbit.com/healthplusmedical.com",
                                industry: "Healthcare",
                                location: "Quezon City, Philippines",
                                website: "https://healthplusmedical.com",
                                contactPerson: "Michael Tan",
                                contactEmail: "michael@healthplusmedical.com",
                                contactPhone: "+63 921 987 6543",
                                description: "Healthcare provider with state-of-the-art medical facilities.",
                                status: "Pending",
                                appliedDate: "2023-06-08"
                            }
                        ],
                        selectedCompany: null,
                        searchQuery: "",
                        filteredCompanies: [],
                        currentPage: 1,
                        itemsPerPage: 5,
                        loading: false
                    }
                },
                created() {
                    // Simulate loading data
                    this.loading = true;
                    setTimeout(() => {
                        this.filterCompanies();
                        this.loading = false;
                    }, 800);
                },
                computed: {
                    totalPages() {
                        return Math.ceil(this.filteredCompanies.length / this.itemsPerPage);
                    },
                    paginatedCompanies() {
                        const start = (this.currentPage - 1) * this.itemsPerPage;
                        const end = start + this.itemsPerPage;
                        return this.filteredCompanies.slice(start, end);
                    }
                },
                methods: {
                    toggleSidebar() {
                        this.sidebarActive = !this.sidebarActive;
                    },
                    filterCompanies() {
                        if (!this.searchQuery) {
                            this.filteredCompanies = this.companies.filter(company => company.status === "Pending");
                            this.currentPage = 1;
                            return;
                        }

                        const query = this.searchQuery.toLowerCase();
                        this.filteredCompanies = this.companies.filter(company =>
                            (company.status === "Pending") && (
                                company.name.toLowerCase().includes(query) ||
                                company.industry.toLowerCase().includes(query) ||
                                company.location.toLowerCase().includes(query) ||
                                company.contactPerson.toLowerCase().includes(query)
                            )
                        );
                        this.currentPage = 1;
                    },
                    formatDate(dateString) {
                        const options = {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
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
                    viewCompany(company) {
                        this.selectedCompany = company;
                        const modal = new bootstrap.Modal(document.getElementById('viewCompanyModal'));
                        modal.show();
                    },
                    approveCompany(company) {
                        if (confirm(`Are you sure you want to approve ${company.name}?`)) {
                            company.status = "Approved";
                            this.filterCompanies();
                            // In a real app, you would make an API call here
                            alert(`${company.name} has been approved successfully!`);
                        }
                    },
                    rejectCompany(company) {
                        if (confirm(`Are you sure you want to reject ${company.name}?`)) {
                            company.status = "Rejected";
                            this.filterCompanies();
                            // In a real app, you would make an API call here
                            alert(`${company.name} has been rejected.`);
                        }
                    }
                }
            }).mount('#app');
        </script>
</body>

</html>