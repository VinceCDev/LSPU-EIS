<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>

<body>
    <div id="app">
        <div class="sidebar" :class="{ 'active': sidebarActive }">
            <div class="sidebar-brand">
                <img src="images/alumni.png" alt="Logo" class="sidebar-logo">
                <span class="sidebar-brand-name">LSPU Administrator</span>
            </div>
            <div class="sidebar-menu">
                <a href="admin_dashboard.php" class="sidebar-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <a class="sidebar-item" href="admin_job.php">
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
                    <a href="admin_profile.php" class="sidebar-dropdown-item">Manage Alumni</a>
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
                <!-- Dashboard Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="dashboard-card">
                            <div class="card-title">Active Jobs</div>
                            <div class="card-value">12</div>
                            <div class="card-change">
                                <i class="fas fa-arrow-up me-1"></i> 2 from last week
                            </div>
                            <i class="fas fa-briefcase card-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="dashboard-card">
                            <div class="card-title">New Applicants</div>
                            <div class="card-value">24</div>
                            <div class="card-change">
                                <i class="fas fa-arrow-up me-1"></i> 5 from yesterday
                            </div>
                            <i class="fas fa-users card-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="dashboard-card">
                            <div class="card-title">Interviews</div>
                            <div class="card-value">8</div>
                            <div class="card-change">
                                <i class="fas fa-arrow-down me-1 negative"></i> 1 from yesterday
                            </div>
                            <i class="fas fa-calendar-check card-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="dashboard-card">
                            <div class="card-title">Profile Views</div>
                            <div class="card-value">156</div>
                            <div class="card-change">
                                <i class="fas fa-arrow-up me-1"></i> 12% from last week
                            </div>
                            <i class="fas fa-eye card-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Analytics Charts -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="dashboard-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Applicants Overview</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="timeRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Last 30 Days
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="timeRangeDropdown">
                                        <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                                        <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                                        <li><a class="dropdown-item" href="#">Last 90 Days</a></li>
                                        <li><a class="dropdown-item" href="#">This Year</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="applicantsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="dashboard-card">
                            <h5 class="mb-3">Application Status</h5>
                            <div class="chart-container">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h5 class="mb-3">Recent Applicants</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="applicant in recentApplicants" :key="applicant.id">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img :src="applicant.profileImage" alt="Profile" class="applicant-avatar me-2">
                                                    <span>{{ applicant.name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ applicant.position }}</td>
                                            <td>{{ formatDate(applicant.date) }}</td>
                                            <td>
                                                <span :class="'badge bg-' + applicant.statusColor">{{ applicant.status }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="dashboard-card">
                            <h5 class="mb-3">Upcoming Interviews</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Candidate</th>
                                            <th>Position</th>
                                            <th>Date & Time</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="interview in upcomingInterviews" :key="interview.id">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img :src="interview.profileImage" alt="Profile" class="applicant-avatar me-2">
                                                    <span>{{ interview.name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ interview.position }}</td>
                                            <td>{{ formatDateTime(interview.date) }}</td>
                                            <td>{{ interview.type }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="footer bg-light py-3 border-top position-sticky bottom-0 w-100">
            <div class="container text-center">
                <small text-muted d-block text-start>
                    &copy; 2025 Laguna State Polytechnic University - Employment and Information System. All rights reserved.
                </small>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vue.js -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.min.js"></script>
    <script src="js/admin_dashboard.js"></script>
</body>

</html>