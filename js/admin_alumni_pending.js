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
            // Ensure we're always working with an array
            if (!Array.isArray(this.alumni)) {
                console.warn('Alumni data is not an array:', this.alumni);
                return [];
            }
            
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
            // Add a safety check
            if (!Array.isArray(this.filteredAlumni)) {
                console.warn('Filtered alumni is not an array:', this.filteredAlumni);
                return [];
            }
            
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
            this.fetchAlumniWithDetails();
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
            // Add this method to fetch alumni with detailed information
            async fetchAlumniWithDetails() {
                this.isLoading = true;
                try {
                    const response = await fetch('functions/get_alumni_pending.php');
                    const data = await response.json();

                    if (!data || !data.alumni || !Array.isArray(data.alumni)) {
                        console.error('Invalid data structure received:', data);
                        this.showNotification('Invalid data received from server', 'error');
                        this.alumni = []; // Reset to empty array
                        return;
                    }
                    
                    const alumniWithDetails = await Promise.all(
                        data.alumni.map(async (alumni) => {
                            try {
                                // Try to fetch skills for this alumni
                                let skillsData = {success: false, skills: []};
                                try {
                                    const skillsResponse = await fetch(`functions/get_skill.php?alumni_id=${alumni.alumni_id}`);
                                    skillsData = await skillsResponse.json();
                                } catch (skillError) {
                                    console.error(`Error fetching skills for alumni ${alumni.alumni_id}:`, skillError);
                                }
                                
                                // Try to fetch education for this alumni
                                let educationData = {success: false, education: []};
                                try {
                                    const educationResponse = await fetch(`functions/get_admin_education.php?alumni_id=${alumni.alumni_id}`);
                                    educationData = await educationResponse.json();
                                } catch (educationError) {
                                    console.error(`Error fetching education for alumni ${alumni.alumni_id}:`, educationError);
                                }
                                
                                // Try to fetch experience for this alumni
                                let experienceData = {success: false, experience: []};
                                try {
                                    const experienceResponse = await fetch(`functions/get_experience.php?alumni_id=${alumni.alumni_id}`);
                                    experienceData = await experienceResponse.json();
                                } catch (experienceError) {
                                    console.error(`Error fetching experience for alumni ${alumni.alumni_id}:`, experienceError);
                                }
                                
                                // Try to fetch resume for this alumni
                                let resumeData = {success: false, resume: null};
                                try {
                                    const resumeResponse = await fetch(`functions/get_admin_resume.php?alumni_id=${alumni.alumni_id}`);
                                    resumeData = await resumeResponse.json();
                                } catch (resumeError) {
                                    console.error(`Error fetching resume for alumni ${alumni.alumni_id}:`, resumeError);
                                }
                                
                                // Process resume data to include full URL
                                let processedResume = null;
                                if (resumeData.success && resumeData.resume) {
                                    if (typeof resumeData.resume === 'string') {
                                        // If resume is just a filename, create full object
                                        processedResume = {
                                            file_name: resumeData.resume,
                                            url: 'uploads/resume/' + resumeData.resume
                                        };
                                    } else if (resumeData.resume.file_name) {
                                        // If resume is an object with file_name, ensure it has URL
                                        processedResume = {
                                            ...resumeData.resume,
                                            url: resumeData.resume.url || 'uploads/resume/' + resumeData.resume.file_name
                                        };
                                    }
                                }
                                
                                return {
                                    id: alumni.alumni_id,
                                    first_name: alumni.first_name,
                                    middle_name: alumni.middle_name,
                                    last_name: alumni.last_name,
                                    email: alumni.email,
                                    secondary_email: alumni.secondary_email,
                                    gender: alumni.gender,
                                    year_graduated: alumni.year_graduated,
                                    course: alumni.course,
                                    college: alumni.college,
                                    province: alumni.province,
                                    city: alumni.city,
                                    status: alumni.status,
                                    birthdate: alumni.birthdate,
                                    contact: alumni.contact,
                                    civil_status: alumni.civil_status,
                                    verification_document: alumni.verification_document,
                                    profile_picture: alumni.profile_picture,
                                    skills: skillsData.success ? skillsData.skills : [],
                                    education: educationData.success ? educationData.education : [],
                                    experiences: experienceData.success ? experienceData.experience : [],
                                    resume: processedResume,
                                    employment: experienceData.success && experienceData.experience.length > 0 ? {
                                        company_name: experienceData.experience[0].company,
                                        position: experienceData.experience[0].title,
                                        status: experienceData.experience[0].employment_status,
                                        years: this.calculateYears(experienceData.experience[0].start_date, experienceData.experience[0].end_date)
                                    } : null
                                };
                            } catch (error) {
                                console.error(`Error processing alumni ${alumni.alumni_id}:`, error);
                                // Return basic data if detailed fetch fails
                                return {
                                    id: alumni.alumni_id,
                                    first_name: alumni.first_name,
                                    middle_name: alumni.middle_name,
                                    last_name: alumni.last_name,
                                    email: alumni.email,
                                    secondary_email: alumni.secondary_email,
                                    gender: alumni.gender,
                                    year_graduated: alumni.year_graduated,
                                    course: alumni.course,
                                    college: alumni.college,
                                    province: alumni.province,
                                    city: alumni.city,
                                    status: alumni.status,
                                    birthdate: alumni.birthdate,
                                    contact: alumni.contact,
                                    civil_status: alumni.civil_status,
                                    verification_document: alumni.verification_document,
                                    profile_picture: alumni.profile_picture,
                                    skills: [],
                                    education: [],
                                    experiences: [],
                                    resume: null,
                                    employment: null
                                };
                            }
                        })
                    );
                    
                    this.alumni = alumniWithDetails;
                } catch (error) {
                    console.error('Error fetching alumni:', error);
                    this.showNotification('Error loading alumni data', 'error');
                    this.alumni = [];
                } finally {
                    this.isLoading = false;
                }
            },

            // Add this method to view alumni details
            viewAlumniDetails(alumni) {
                this.viewAlumniData = {
                    ...alumni,
                    skills: alumni.skills || [],
                    experiences: alumni.experiences || [],
                    documents: alumni.documents || [],
                    employment: alumni.employment,
                    profile_picture: alumni.profile_picture // Correct path: /lspu_eis/uploads/profile_picture/
                };
                this.showViewModal = true;
                this.$nextTick(() => this.focusFirstInput('view-modal'));
            },

            // Add this method to close the view modal
            closeViewModal() {
                this.showViewModal = false;
                this.viewAlumniData = null;
            },

            // Add this helper method for file icons
            getFileIcon(filename) {
                if (!filename) return 'fas fa-file text-gray-400';
                
                const extension = filename.split('.').pop().toLowerCase();
                switch (extension) {
                    case 'pdf':
                        return 'fas fa-file-pdf text-red-500';
                    case 'doc':
                    case 'docx':
                        return 'fas fa-file-word text-blue-500';
                    case 'xls':
                    case 'xlsx':
                        return 'fas fa-file-excel text-green-500';
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
                        return 'fas fa-file-image text-purple-500';
                    default:
                        return 'fas fa-file text-gray-400';
                }
            },

            // Add this method to calculate years of experience
            calculateYears(startDate, endDate) {
                if (!startDate) return 0;
                
                const start = new Date(startDate);
                const end = endDate ? new Date(endDate) : new Date();
                
                const years = end.getFullYear() - start.getFullYear();
                const months = end.getMonth() - start.getMonth();
                
                return years + (months >= 0 ? 0 : -1);
            },

            // Add this method to format dates
            formatDate(dateString) {
                if (!dateString) return 'N/A';
                
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },

            async contactAlumni(alumni) {
                try {
                    // Redirect to messages page with alumni email as parameter
                    const messagesUrl = `admin_message.php?compose=true&to=${encodeURIComponent(alumni.email)}`;
                    window.location.href = messagesUrl;
                } catch (error) {
                    console.error('Error redirecting to messages:', error);
                    this.showNotification('Error opening message composer', 'error');
                }
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