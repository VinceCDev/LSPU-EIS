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
            recentApplicants: [{
                    id: 1,
                    name: "Juan Dela Cruz",
                    profileImage: "https://randomuser.me/api/portraits/men/32.jpg",
                    position: "Frontend Developer",
                    date: "2023-06-15",
                    status: "New",
                    statusColor: "info"
                },
                {
                    id: 2,
                    name: "Maria Santos",
                    profileImage: "https://randomuser.me/api/portraits/women/44.jpg",
                    position: "Marketing Specialist",
                    date: "2023-06-14",
                    status: "Reviewed",
                    statusColor: "primary"
                },
                {
                    id: 3,
                    name: "Robert Lim",
                    profileImage: "https://randomuser.me/api/portraits/men/67.jpg",
                    position: "DevOps Engineer",
                    date: "2023-06-12",
                    status: "Interview",
                    statusColor: "warning"
                },
                {
                    id: 4,
                    name: "Anna Reyes",
                    profileImage: "https://randomuser.me/api/portraits/women/28.jpg",
                    position: "HR Manager",
                    date: "2023-06-10",
                    status: "Rejected",
                    statusColor: "danger"
                },
                {
                    id: 5,
                    name: "Michael Tan",
                    profileImage: "https://randomuser.me/api/portraits/men/52.jpg",
                    position: "Financial Analyst",
                    date: "2023-06-08",
                    status: "Hired",
                    statusColor: "success"
                }
            ],
            upcomingInterviews: [{
                    id: 1,
                    name: "Robert Lim",
                    profileImage: "https://randomuser.me/api/portraits/men/67.jpg",
                    position: "DevOps Engineer",
                    date: "2023-06-20T14:30:00",
                    type: "Technical"
                },
                {
                    id: 2,
                    name: "Sarah Gomez",
                    profileImage: "https://randomuser.me/api/portraits/women/63.jpg",
                    position: "UI/UX Designer",
                    date: "2023-06-21T10:00:00",
                    type: "Portfolio Review"
                },
                {
                    id: 3,
                    name: "David Ong",
                    profileImage: "https://randomuser.me/api/portraits/men/29.jpg",
                    position: "Backend Developer",
                    date: "2023-06-21T13:45:00",
                    type: "Technical"
                },
                {
                    id: 4,
                    name: "Carla Ramirez",
                    profileImage: "https://randomuser.me/api/portraits/women/35.jpg",
                    position: "Content Writer",
                    date: "2023-06-22T11:15:00",
                    type: "Culture Fit"
                }
            ]
        }
    },
    mounted() {
        this.renderCharts();
    },
    methods: {
        toggleSidebar() {
            this.sidebarActive = !this.sidebarActive;
        
            if (this.sidebarActive) {
                document.addEventListener('click', this.handleClickOutside);
            } else {
                document.removeEventListener('click', this.handleClickOutside);
            }
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
        confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        },
        renderCharts() {
            // Applicants Overview Chart (Line Chart)
            const applicantsCtx = document.getElementById('applicantsChart').getContext('2d');
            new Chart(applicantsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                            label: 'Applicants',
                            data: [45, 60, 75, 52, 80, 95, 70],
                            borderColor: '#2557a7',
                            backgroundColor: 'rgba(37, 87, 167, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Interviews',
                            data: [15, 25, 30, 22, 35, 40, 30],
                            borderColor: '#ff6d00',
                            backgroundColor: 'rgba(255, 109, 0, 0.1)',
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Application Status Chart (Doughnut Chart)
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['New', 'Reviewed', 'Interview', 'Rejected', 'Hired'],
                    datasets: [{
                        data: [24, 18, 8, 5, 3],
                        backgroundColor: [
                            '#1976d2',
                            '#2557a7',
                            '#ff6d00',
                            '#d32f2f',
                            '#388e3c'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    }
}).mount('#app');