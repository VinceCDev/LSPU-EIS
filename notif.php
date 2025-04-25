<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPU EIS - Notifications</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --header-height: 70px;
            --footer-height: 60px;
            --sidebar-width: 600px;
            --logo-size: 40px;
            --profile-img-size: 35px;
            --primary-color: #2557a7;
            --secondary-color: #f8f9fa;
        }

        body {
            padding-top: var(--header-height);
            padding-bottom: var(--footer-height);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }

        /* Header Styles */
        header {
            height: var(--header-height);
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .nav-link {
            font-size: 1.05rem;
            padding: 0.5rem 1rem;
        }

        /* Notification Main Content */
        .notification-container {
            padding: 2rem 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0 15px;
        }

        .notification-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .notification-actions .btn {
            margin-left: 0.5rem;
        }

        .notification-list {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .notification-item {
            padding: 1.25rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: flex-start;
            transition: all 0.2s ease;
        }

        .notification-item.unread {
            background-color: rgba(37, 87, 167, 0.03);
            border-left: 4px solid var(--primary-color);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover {
            background-color: #f9f9f9;
        }

        .notification-icon {
            font-size: 1.25rem;
            color: var(--primary-color);
            margin-right: 1rem;
            min-width: 24px;
        }

        .notification-content {
            flex: 1;
        }

        .notification-message {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .notification-details {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .notification-time {
            color: #888;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
        }

        .notification-time i {
            margin-right: 0.5rem;
            font-size: 0.7rem;
        }

        .notification-actions {
            display: flex;
            gap: 0.5rem;
            margin-left: 1rem;
        }

        .btn-mark-read {
            background-color: transparent;
            border: 1px solid #ddd;
            color: #555;
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .btn-mark-read:hover {
            background-color: #f0f0f0;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 0;
            color: #777;
        }

        .empty-state i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 1rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .notification-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .notification-actions {
                margin-top: 1rem;
                width: 100%;
                justify-content: flex-end;
            }

            .notification-item {
                flex-direction: column;
                padding: 1rem;
            }

            .notification-actions {
                margin-left: 0;
                margin-top: 0.5rem;
                justify-content: flex-end;
                width: 100%;
            }
        }

        /* Footer */
        footer {
            background-color: #f8f9fa;
            padding: 1rem 0;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            border-top: 1px solid #eee;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                position: fixed;
                top: var(--header-height);
                left: 0;
                right: 0;
                background-color: white;
                padding: 15px;
                margin-top: 0;
                border-radius: 0;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                width: 100vw;
            }

            .nav-item {
                margin: 5px 0;
                width: 100%;
            }

            .dropdown-menu {
                position: static;
                float: none;
                width: 100%;
                margin-left: 0;
                border: none;
                box-shadow: none;
                background-color: white;
            }

            .navbar-nav {
                width: 100%;
            }

            .nav-link {
                padding: 0.75rem 0;
            }

            .dropdown-toggle::after {
                float: right;
                margin-top: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <!-- Header -->
        <header class="bg-white shadow-sm fixed-top">
            <div class="container h-100">
                <nav class="navbar navbar-expand-lg navbar-light h-100 py-0">
                    <div class="d-flex align-items-center">
                        <img src="images/alumni.png" alt="LSPU Logo" class="me-2" style="height: var(--logo-size);">
                        <span class="navbar-brand">LSPU EIS Job Portal</span>
                    </div>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto align-items-lg-center">
                            <li class="nav-item">
                                <a class="nav-link" href="home.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">My Applications</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active custom-active" href="#">Notifications</a>
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
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> View Profile</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-key me-2"></i> Change Password</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-question-circle me-2"></i> Help Center</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                                </ul>
                            </li>
                            <li class="nav-item ms-lg-2 my-2 my-lg-0">
                                <a class="btn btn-outline-primary btn-sm" href="#">Employer Site</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Notification Main Content -->
        <main class="notification-container">
            <div class="notification-header">
                <h1 class="notification-title">
                    <i class="fas fa-bell"></i> Notifications
                    <span v-if="loading" class="spinner-border spinner-border-sm ms-2" role="status"></span>
                </h1>
                <div class="notification-actions">
                    <button @click="markAllAsRead" class="btn btn-outline-secondary btn-sm" :disabled="notifications.length === 0 || allRead">
                        <i class="fas fa-check"></i> Mark all as read
                    </button>
                    <button @click="clearAllNotifications" class="btn btn-outline-secondary btn-sm" :disabled="notifications.length === 0">
                        <i class="fas fa-trash"></i> Clear all
                    </button>
                </div>
            </div>

            <!-- Loading state -->
            <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading notifications...</p>
            </div>

            <!-- Empty state -->
            <div v-else-if="notifications.length === 0" class="empty-state">
                <i class="far fa-bell-slash"></i>
                <h4>No notifications yet</h4>
                <p>When you have new notifications, they'll appear here.</p>
            </div>

            <!-- Notifications list -->
            <div v-else class="notification-list">
                <div v-for="notification in notifications" :key="notification.id"
                    class="notification-item" :class="{ 'unread': !notification.read }">
                    <div class="notification-icon">
                        <i :class="notificationIcons[notification.type]"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-message">
                            {{ notification.message }}
                        </div>
                        <div class="notification-details">
                            {{ notification.details }}
                        </div>
                        <div class="notification-time">
                            <i class="far fa-clock"></i> {{ formatTime(notification.time) }}
                        </div>
                    </div>
                    <div class="notification-actions">
                        <button @click="markAsRead(notification.id)" class="btn-mark-read" :disabled="notification.read">
                            {{ notification.read ? 'Read' : 'Mark as read' }}
                        </button>
                    </div>
                </div>
            </div>
        </main>

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
    <!-- Date formatting library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/date-fns/2.29.3/date_fns.min.js"></script>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    loading: true,
                    notifications: [],
                    notificationIcons: {
                        application: 'fas fa-briefcase',
                        interview: 'fas fa-calendar-check',
                        message: 'fas fa-envelope',
                        profile: 'fas fa-user-tie',
                        system: 'fas fa-cog'
                    }
                }
            },
            computed: {
                allRead() {
                    return this.notifications.every(n => n.read);
                }
            },
            methods: {
                async fetchNotifications() {
                    try {
                        this.loading = true;
                        // Simulate API call with timeout
                        await new Promise(resolve => setTimeout(resolve, 800));

                        // Mock data - in a real app, this would come from an API
                        this.notifications = [{
                                id: 1,
                                type: 'application',
                                message: 'Your application for Frontend Developer at Tech Solutions has been reviewed',
                                details: 'The hiring manager has viewed your application and it\'s currently under consideration.',
                                time: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
                                read: false
                            },
                            {
                                id: 2,
                                type: 'interview',
                                message: 'Interview scheduled for Backend Engineer position',
                                details: 'Your interview is scheduled for Friday, June 10 at 2:00 PM via Zoom.',
                                time: new Date(Date.now() - 24 * 60 * 60 * 1000), // 1 day ago
                                read: false
                            },
                            {
                                id: 3,
                                type: 'message',
                                message: 'New message from HR at Data Systems',
                                details: '"Thank you for submitting your documents. We\'ll get back to you soon."',
                                time: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000), // 3 days ago
                                read: true
                            },
                            {
                                id: 4,
                                type: 'profile',
                                message: 'Profile completeness reminder',
                                details: 'Complete your profile to increase your chances of getting hired by 40%.',
                                time: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000), // 1 week ago
                                read: true
                            }
                        ];
                    } catch (error) {
                        console.error('Error fetching notifications:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                formatTime(date) {
                    const now = new Date();
                    const diffInSeconds = Math.floor((now - date) / 1000);

                    if (diffInSeconds < 60) return 'Just now';
                    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
                    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
                    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;

                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                },
                markAsRead(id) {
                    const notification = this.notifications.find(n => n.id === id);
                    if (notification) {
                        notification.read = true;
                    }
                },
                markAllAsRead() {
                    this.notifications.forEach(n => n.read = true);
                },
                clearAllNotifications() {
                    this.notifications = [];
                }
            },
            mounted() {
                this.fetchNotifications();

                // Simulate receiving new notifications (for demo purposes)
                setTimeout(() => {
                    if (this.notifications.length > 0) {
                        this.notifications.unshift({
                            id: Date.now(),
                            type: 'system',
                            message: 'New job posting matches your profile',
                            details: 'A new position for Senior UI Designer has been posted that matches your skills.',
                            time: new Date(),
                            read: false
                        });
                    }
                }, 5000);
            }
        }).mount('#app');
    </script>
</body>

</html>