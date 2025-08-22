const { createApp } = Vue;
        createApp({
            data() {
                return {
                    loading: true,
                    notificationIcons: {
                        application: 'fas fa-briefcase',
                        interview: 'fas fa-calendar-check',
                        message: 'fas fa-envelope',
                        profile: 'fas fa-user-tie',
                        system: 'fas fa-cog',
                        job_match: 'fas fa-bolt'
                    },
                    darkMode: false,
                    mobileMenuOpen: false,
                    profileDropdownOpen: false,
                    profilePicData: { file_name: '' },
                    profile: { name: '' },
                    showLogoutModal: false,
                    unreadNotifications: 0,
                    notifications: [],
                    mobileProfileDropdownOpen: false
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
                        // Fetch notifications for the logged-in user
                        const res = await fetch('functions/fetch_notifications.php');
                        const data = await res.json();
                        if (data.success) {
                            this.notifications = data.notifications;
                        } else {
                            this.notifications = [];
                        }
                    } catch (error) {
                        this.notifications = [];
                    } finally {
                        this.loading = false;
                    }
                },
                formatTime(date) {
                    const now = new Date();
                    const d = new Date(date);
                    const diffInSeconds = Math.floor((now - d) / 1000);
                    if (diffInSeconds < 60) return 'Just now';
                    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
                    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
                    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
                    return d.toLocaleDateString('en-US', {
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
                async markNotificationAsRead(notification) {
                    if (notification.read) return;
                    // Optimistically mark as read
                    notification.read = true;
                    try {
                        await fetch('functions/update_notif.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `action=mark_one_read&id=${encodeURIComponent(notification.id)}`
                        });
                    } catch (e) {
                        // Optionally handle error, revert if needed
                    }
                },
                async fetchUnreadNotifications() {
                    try {
                      const response = await fetch('functions/get_unread_notifications.php');
                      const data = await response.json();
                      if (data.success) {
                        this.unreadNotifications = data.unread_count;
                      }
                    } catch (error) {
                      console.error('Error fetching unread notifications:', error);
                    }
                  },
                markAllAsRead() {
                    this.notifications.forEach(n => n.read = true);
                    // Update on backend using session (no user_id sent)
                    fetch('functions/update_notif.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=mark_all_read`
                    });
                },
                clearAllNotifications() {
                    this.notifications = [];
                },
                applyDarkMode() {
                    const html = document.documentElement;
                    if (this.darkMode) {
                        html.classList.add('dark');
                    } else {
                        html.classList.remove('dark');
                    }
                },
                logout() {
                    window.location.href = 'logout.php';
                }
            },
            watch: {
                darkMode(val) {
                    localStorage.setItem('darkMode', val.toString());
                    this.applyDarkMode();
                }
            },
            mounted() {
                // Set dark mode on initial load
                const storedMode = localStorage.getItem('darkMode');
                this.fetchUnreadNotifications();
  
                // Optional: Poll for new notifications every 30 seconds
                this.notificationInterval = setInterval(this.fetchUnreadNotifications, 30000);
                if (storedMode !== null) {
                    this.darkMode = storedMode === 'true';
                } else {
                    this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                }
                this.applyDarkMode();
                this.fetchNotifications();
                fetch('functions/fetch_profile_pic.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.file_name) {
                            this.profilePicData.file_name = data.file_name;
                        } else {
                            this.profilePicData.file_name = '';
                        }
                    });
                fetch('functions/fetch_alumni_details.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.profile) {
                            this.profile.name = `${data.profile.first_name} ${data.profile.last_name}`;
                        }
                    });
            },
            beforeUnmount() {
                // Clean up the interval
                if (this.notificationInterval) {
                  clearInterval(this.notificationInterval);
                }
              }
        }).mount('#app');