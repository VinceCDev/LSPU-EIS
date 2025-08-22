const { createApp } = Vue;
    const collegeCourses = {
        "College of Computer Studies": [
                "BS Information Technology",
                "BS Computer Science"
            ],
            "College of Engineering": [
                "BS Electronics Engineering",
                "BS Electrical Engineering",
                "BS Computer Engineering"
            ],
            "College of Business Administration": [
                "BS Office Administration",
                "BS Business Administration Major in Financial Management",
                "BS Business Administration Major in Marketing Management",
                "BS Accountancy"
            ],
            "College of Education": [
                "BS Elementary Education",
                "BS Physical Education",
                "BS Secondary Education Major in English",
                "BS Secondary Education Major in Filipino",
                "BS Secondary Education Major in Mathematics",
                "BS Secondary Education Major in Science",
                "BS Secondary Education Major in Social Studies",
                "BS Technology and Livelihood Education Major in Home Economics",
                "BS Technical-Vocational Teacher Education Major in Electrical Technology",
                "BS Technical-Vocational Teacher Education Major in Electronics Technology",
                "BS Technical-Vocational Teacher Education Major in Food & Service Management",
                "BS Technical-Vocational Teacher Education Major in Garments, Fashion & Design"
            ],
            "College of Arts and Sciences": [
                "BS Psychology",
                "BS Biology"
            ],
            "College of Industrial Technology": [
                "BS Industrial Technology Major in Automotive Technology",
                "BS Industrial Technology Major in Architectural Drafting",
                "BS Industrial Technology Major in Electrical Technology",
                "BS Industrial Technology Major in Electronics Technology",
                "BS Industrial Technology Major in Food & Beverage Preparation and Service Management Technology",
                "BS Industrial Technology Major in Heating, Ventilating, Air-Conditioning & Refrigeration Technology"
            ],
            "College of Criminal Justice Education": [
                "BS Criminology"
            ],
            "College of Hospitality Management and Tourism": [
                "BS Hospitality Management",
                "BS Tourism Management"
            ]
    };
createApp({
    data() {
        return {
            sidebarActive: window.innerWidth >= 768,
            alumniDropdownOpen: true,
            companiesDropdownOpen: false,
            profileDropdownOpen: false,
            darkMode: localStorage.getItem('darkMode') === 'true' || 
                     (localStorage.getItem('darkMode') === null && 
                      window.matchMedia('(prefers-color-scheme: dark)').matches),
            showLogoutModal: false,
            isMobile: window.innerWidth < 768,
            notifications: [],
            notificationId: 0,
            actionDropdown: null,
                alumni: [],
            searchQuery: '',
            itemsPerPage: 5,
            currentPage: 1,
            showViewModal: false,
            viewAlumniData: {},
            showDeleteModal: false,
            alumniToDelete: null,
            filters: {
                college: '',
                course: '',
                    status: ''
            },
            profile: {
                profile_pic: '',
                name: '',
            }
            };
    },
    computed: {
        filteredAlumni() {
            let filtered = this.alumni;
                
                // Search
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                filtered = filtered.filter(a =>
                    (`${a.first_name} ${a.middle_name} ${a.last_name}`.toLowerCase().includes(q) ||
                    a.email.toLowerCase().includes(q) ||
                    a.gender.toLowerCase().includes(q) ||
                        a.year_graduated.toString().includes(q) ||
                    a.course.toLowerCase().includes(q) ||
                    a.college.toLowerCase().includes(q) ||
                    a.province.toLowerCase().includes(q) ||
                    a.city.toLowerCase().includes(q) ||
                    a.status.toLowerCase().includes(q))
                );
            }
                
                // Filters
                if (this.filters.college) {
                    filtered = filtered.filter(a => a.college === this.filters.college);
                }
                if (this.filters.course) {
                    filtered = filtered.filter(a => a.course === this.filters.course);
                }
                if (this.filters.status) {
                    filtered = filtered.filter(a => a.status === this.filters.status);
                }
                
            return filtered;
        },
        paginatedAlumni() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredAlumni.slice(start, start + this.itemsPerPage);
        },
        totalPages() {
            return Math.ceil(this.filteredAlumni.length / this.itemsPerPage) || 1;
            },
            visiblePages() {
                const pages = [];
                const start = Math.max(1, this.currentPage - 2);
                const end = Math.min(this.totalPages, this.currentPage + 2);
                
                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
                return pages;
            },
            uniqueColleges() {
                return Object.keys(collegeCourses);
            },
            filterCourseOptions() {
                return Object.values(collegeCourses).flat();
            }
        },
        mounted() {
            this.applyDarkMode();
            window.addEventListener('resize', this.handleResize);
            this.fetchAlumni();
            fetch('functions/fetch_admin_details.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.profile) {
                        this.profile = data.profile;
                    }
                });
        },
        beforeUnmount() {
            window.removeEventListener('resize', this.handleResize);
        },
        watch: {
        
            'filters.college'(val) {
                this.filters.course = '';
        },
        darkMode(val) {
            this.applyDarkMode();
        }
    },
    methods: {
            fetchAlumni() {
                fetch('functions/get_alumni_pending.php')
                    .then(res => res.json())
                    .then(data => {
                        this.alumni = data;
                    });
            },
            confirmLogout() {
                this.showLogoutModal = true;
            },
            approveAlumni(alumni) {
                fetch('functions/approve_alumni.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ alumni_id: alumni.alumni_id })
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        this.fetchAlumni();
                        this.showNotification('Alumni approved!', 'success');
                    } else {
                        this.showNotification('Failed to approve alumni.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error approving alumni:', error);
                    this.showNotification('Error approving alumni.', 'error');
                });
            },
            deleteAlumni(alumni) {
                fetch('functions/delete_alumni.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ alumni_id: alumni.alumni_id })
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        this.fetchAlumni();
                        this.showNotification('Alumni deleted!', 'success');
                    } else {
                        this.showNotification('Failed to delete alumni.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting alumni:', error);
                    this.showNotification('Error deleting alumni.', 'error');
                });
            },
        toggleSidebar() {
            this.sidebarActive = !this.sidebarActive;
        },
        handleResize() {
            this.isMobile = window.innerWidth < 768;
            this.sidebarActive = window.innerWidth >= 768;
        },
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode.toString());
            this.applyDarkMode();
            // Force a small delay to ensure the DOM updates
            this.$nextTick(() => {
                this.applyDarkMode();
            });
        },
        applyDarkMode() {
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark');
            }
        },
        toggleActionDropdown(id) {
            this.actionDropdown = this.actionDropdown === id ? null : id;
        },
        viewAlumniDetails(alumni) {
            this.viewAlumniData = alumni;
            this.showViewModal = true;
        },
        closeViewModal() {
            this.showViewModal = false;
            this.viewAlumniData = {};
        },
        confirmDelete(alumni) {
            this.showDeleteModal = true;
            this.alumniToDelete = alumni;
        },
        confirmDeleteAlumni() {
            if (this.alumniToDelete) {
                this.deleteAlumni(this.alumniToDelete);
                this.alumniToDelete = null;
            }
            this.showDeleteModal = false;
        },
            updateFilterCourseOptions() {
                // This method is now redundant as filterCourseOptions is computed
            },
            prevPage() {
                if (this.currentPage > 1) this.currentPage--;
            },
            nextPage() {
                if (this.currentPage < this.totalPages) this.currentPage++;
            },
            goToPage(page) {
                this.currentPage = page;
        },
        showNotification(message, type = 'success') {
            const id = this.notificationId++;
            this.notifications.push({ id, type, message });
            setTimeout(() => this.removeNotification(id), 3000);
        },
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        },
        
    }
}).mount('#app');