<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: employer_login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();

    header("Location: employer_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/employer_dashboard.css">
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
                <a href="#" class="sidebar-item active">
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
        <div class="main-content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Dashboard Overview</h4>
                    <div class="d-flex">
                        <button class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-plus me-1"></i> Post Job
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
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

                <!-- Recent Applicants -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="dashboard-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Recent Applicants</h5>
                                <a href="#" class="btn btn-link btn-sm">View All</a>
                            </div>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Candidate</th>
                                            <th>Position</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(applicant, index) in recentApplicants" :key="index">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img :src="applicant.avatar" class="applicant-avatar">
                                                    <div>
                                                        <div>{{ applicant.name }}</div>
                                                        <div class="text-muted small">{{ formatDate(applicant.appliedDate) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ applicant.position }}</td>
                                            <td>
                                                <span class="status-badge" :class="'status-' + applicant.status.toLowerCase()">
                                                    {{ applicant.status }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn-action btn-outline-primary me-1">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <button class="btn-action btn-outline-secondary">
                                                    <i class="fas fa-envelope"></i> Message
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="dashboard-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Upcoming Interviews</h5>
                                <a href="#" class="btn btn-link btn-sm">View All</a>
                            </div>
                            <div class="upcoming-interviews">
                                <div v-for="(interview, index) in upcomingInterviews" :key="index" class="interview-item mb-3 p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ interview.candidate }}</strong>
                                            <div class="small">{{ interview.position }}</div>
                                        </div>
                                        <span class="badge bg-light text-dark">{{ interview.time }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="small text-muted">
                                            <i class="fas fa-video me-1"></i> {{ interview.type }}
                                        </span>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="upcomingInterviews.length === 0" class="text-center py-4 text-muted">
                                    <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                    <p>No upcoming interviews</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Postings -->
                <div class="dashboard-card mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Your Job Postings</h5>
                        <div>
                            <button class="btn btn-outline-secondary btn-sm me-2">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Post New Job
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Applications</th>
                                    <th>Date Posted</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(job, index) in jobPostings" :key="index">
                                    <td>
                                        <div>{{ job.title }}</div>
                                        <div class="small text-muted">{{ job.type }} • {{ job.location }}</div>
                                    </td>
                                    <td>{{ job.applications }}</td>
                                    <td>{{ formatDate(job.postedDate) }}</td>
                                    <td>
                                        <span class="status-badge" :class="'status-' + job.status.toLowerCase()">
                                            {{ job.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-action btn-outline-primary me-1">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action btn-outline-secondary me-1">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vue.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.2.47/vue.global.min.js"></script>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    sidebarActive: true,
                    employer: {
                        name: 'Tech Solutions Inc.',
                        logo: 'https://logo.clearbit.com/techsolutions.com',
                        email: 'hr@techsolutions.com'
                    },
                    recentApplicants: [{
                            name: 'John Smith',
                            avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
                            position: 'Frontend Developer',
                            appliedDate: '2023-06-15',
                            status: 'New'
                        },
                        {
                            name: 'Sarah Johnson',
                            avatar: 'https://randomuser.me/api/portraits/women/44.jpg',
                            position: 'UX Designer',
                            appliedDate: '2023-06-14',
                            status: 'Reviewed'
                        },
                        {
                            name: 'Michael Chen',
                            avatar: 'https://randomuser.me/api/portraits/men/75.jpg',
                            position: 'Backend Developer',
                            appliedDate: '2023-06-13',
                            status: 'Interview'
                        },
                        {
                            name: 'Emily Wilson',
                            avatar: 'https://randomuser.me/api/portraits/women/68.jpg',
                            position: 'Product Manager',
                            appliedDate: '2023-06-12',
                            status: 'Rejected'
                        },
                        {
                            name: 'David Kim',
                            avatar: 'https://randomuser.me/api/portraits/men/22.jpg',
                            position: 'Data Scientist',
                            appliedDate: '2023-06-10',
                            status: 'Reviewed'
                        }
                    ],
                    upcomingInterviews: [{
                            candidate: 'Sarah Johnson',
                            position: 'UX Designer',
                            time: 'Today, 2:00 PM',
                            type: 'Zoom Meeting'
                        },
                        {
                            candidate: 'Michael Chen',
                            position: 'Backend Developer',
                            time: 'Tomorrow, 10:30 AM',
                            type: 'On-site'
                        }
                    ],
                    jobPostings: [{
                            title: 'Senior Frontend Developer (React)',
                            type: 'Full-time',
                            location: 'Remote',
                            applications: 24,
                            postedDate: '2023-06-01',
                            status: 'Active'
                        },
                        {
                            title: 'UX/UI Designer',
                            type: 'Full-time',
                            location: 'San Francisco',
                            applications: 18,
                            postedDate: '2023-05-28',
                            status: 'Active'
                        },
                        {
                            title: 'Backend Engineer (Node.js)',
                            type: 'Contract',
                            location: 'Remote',
                            applications: 15,
                            postedDate: '2023-05-20',
                            status: 'Active'
                        },
                        {
                            title: 'Marketing Intern',
                            type: 'Internship',
                            location: 'New York',
                            applications: 32,
                            postedDate: '2023-05-15',
                            status: 'Closed'
                        }
                    ]
                }
            },
            methods: {
                toggleSidebar() {
                    this.sidebarActive = !this.sidebarActive;
                },
                formatDate(dateString) {
                    const date = new Date(dateString);
                    const now = new Date();

                    // If same day, show "Today"
                    if (date.toDateString() === now.toDateString()) {
                        return 'Today';
                    }

                    // If yesterday, show "Yesterday"
                    const yesterday = new Date(now);
                    yesterday.setDate(yesterday.getDate() - 1);
                    if (date.toDateString() === yesterday.toDateString()) {
                        return 'Yesterday';
                    }

                    // Otherwise show formatted date
                    return date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric'
                    });
                }
            },
            mounted() {
                // Check screen size and adjust sidebar
                const checkScreenSize = () => {
                    this.sidebarActive = window.innerWidth >= 992;
                };

                checkScreenSize();
                window.addEventListener('resize', checkScreenSize);
            }
        }).mount('#app');
    </script>
</body>

</html>