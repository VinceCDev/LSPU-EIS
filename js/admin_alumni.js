const { createApp } = Vue;
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
            showAlumniModal: false,
            selectedAlumni: null,
            alumniForm: {
                first_name: '',
                middle_name: '',
                last_name: '',
                email: '',
                secondary_email: '',
                gender: '',
                year_graduated: '',
                college: '',
                course: '',
                province: '',
                city: '',
                status: 'Active',
            },
            showDeleteModal: false,
            alumniToDelete: null,
            showViewModal: false,
            viewAlumniData: {
                skills: [],
                experiences: [],
                documents: [],
                employment: null
            },
            isLoading: false, // Added for loading state
            colleges: [
                {
                    name: "College of Arts and Sciences",
                    courses: [
                        "BS Biology",
                        "BS Psychology"
                    ]
                },
                {
                    name: "College of Business Administration and Accountancy",
                    courses: [
                        "BS Office Administration",
                        "BS Business Administration Major in Financial Management",
                        "BS Business Administration Major in Marketing Management",
                        "BS Accountancy"
                    ]
                },
                {
                    name: "College of Computer Studies",
                    courses: [
                        "BS Information Technology",
                        "BS Computer Science"
                    ]
                },
                {
                    name: "College of Criminal Justice Education",
                    courses: [
                        "BS Criminology"
                    ]
                },
                {
                    name: "College of Engineering",
                    courses: [
                        "BS Electronics Engineering",
                        "BS Electrical Engineering",
                        "BS Computer Engineering"
                    ]
                },
                {
                    name: "College of Hospitality Management and Tourism",
                    courses: [
                        "BS Hospitality Management",
                        "BS Tourism Management"
                    ]
                },
                {
                    name: "College of Industrial Technology",
                    courses: [
                        "BS Industrial Technology Major in Automotive Technology",
                        "BS Industrial Technology Major in Architectural Drafting",
                        "BS Industrial Technology Major in Electrical Technology",
                        "BS Industrial Technology Major in Electronics Technology",
                        "BS Industrial Technology Major in Food & Beverage Preparation and Service Management Technology",
                        "BS Industrial Technology Major in Heating, Ventilating, Air-Conditioning & Refrigeration Technology"
                    ]
                },
                {
                    name: "College of Teacher Education",
                    courses: [
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
                    ]
                }
            ],
            courseOptions: [],
            provinces: [],
            cities: [],
            filters: {
                college: '',
                course: '',
                status: ''
            },
            filterCourseOptions: [],
            profile: {
                profile_pic: '',
                name: ''
            }
        }
    },
    mounted() {
        this.applyDarkMode();
        window.addEventListener('resize', this.handleResize);
        this.fetchProvinces();
        this.updateFilterCourseOptions();
        this.fetchAlumni();
        this.fetchProfile();
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize);
    },
    watch: {
        darkMode(val) {
            this.applyDarkMode();
        },
        'filters.college'(val) {
            this.filters.course = '';
            this.updateFilterCourseOptions();
        }
    },
    computed: {
        filteredAlumni() {
            let filtered = this.alumni;
            if (this.filters.college) {
                filtered = filtered.filter(a => a.college === this.filters.college);
            }
            if (this.filters.course) {
                filtered = filtered.filter(a => a.course === this.filters.course);
            }
            if (this.filters.status) {
                filtered = filtered.filter(a => a.status === this.filters.status);
            }
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                filtered = filtered.filter(a =>
                    (`${a.first_name} ${a.middle_name} ${a.last_name}`.toLowerCase().includes(q) ||
                    a.email.toLowerCase().includes(q) ||
                    a.gender.toLowerCase().includes(q) ||
                    a.year_graduated.toLowerCase().includes(q) ||
                    a.course.toLowerCase().includes(q) ||
                    a.college.toLowerCase().includes(q) ||
                    a.province.toLowerCase().includes(q) ||
                    a.city.toLowerCase().includes(q) ||
                    a.status.toLowerCase().includes(q))
                );
            }
            return filtered;
        },
        paginatedAlumni() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredAlumni.slice(start, start + this.itemsPerPage);
        },
        totalPages() {
            return Math.ceil(this.filteredAlumni.length / this.itemsPerPage) || 1;
        }
    },
    methods: {
        // Sidebar and dropdowns
        toggleSidebar() {
            this.sidebarActive = !this.sidebarActive;
            this.alumniDropdownOpen = true;
            this.companiesDropdownOpen = false;
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
        handleResize() {
            this.isMobile = window.innerWidth < 768;
            this.sidebarActive = window.innerWidth >= 768;
        },
        confirmLogout() {
            this.showLogoutModal = true;
        },
        logout() {
            window.location.href = 'logout.php';
        },
        // Dark mode
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
        // Profile dropdown
        toggleProfileDropdown() {
            this.profileDropdownOpen = !this.profileDropdownOpen;
        },
        // Table and pagination
        filterAlumni() {
            this.currentPage = 1;
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
        toggleActionDropdown(id) {
            this.actionDropdown = this.actionDropdown === id ? null : id;
        },
        // Modals (Add/Edit/View/Delete)
        openAddModal() {
            this.showAlumniModal = true;
            this.selectedAlumni = null;
            this.alumniForm = {
                first_name: '',
                middle_name: '',
                last_name: '',
                email: '',
                secondary_email: '',
                gender: '',
                year_graduated: '',
                college: '',
                course: '',
                province: '',
                city: '',
                status: 'Active'
            };
            this.courseOptions = [];
            this.cities = [];
            this.$nextTick(() => this.focusFirstInput('alumni-modal'));
        },
        editAlumni(alumni) {
            this.selectedAlumni = alumni;
            this.alumniForm = { ...alumni };
            this.updateCourseOptions();
            this.fetchCities();
            this.showAlumniModal = true;
            this.$nextTick(() => this.focusFirstInput('alumni-modal'));
        },
        closeAlumniModal() {
            this.showAlumniModal = false;
            this.selectedAlumni = null;
        },
        closeViewModal() {
            this.showViewModal = false;
            this.viewAlumniData = { skills: [], experiences: [], documents: [], employment: null };
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
        deleteAlumni(alumni) {
            fetch('functions/admin_alumni_add_edit.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ alumni_id: alumni.id }),
                credentials: 'include' // Ensure session cookie is sent
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification('Alumni deleted!', 'success');
                        this.fetchAlumni();
                    } else {
                        this.showNotification(data.message || 'Failed to delete alumni', 'error');
                    }
                })
                .catch(() => this.showNotification('Error deleting alumni', 'error'));
        },
        // Form logic
        updateCourseOptions() {
            const college = this.colleges.find(c => c.name === this.alumniForm.college);
            this.courseOptions = college ? college.courses : [];
            if (!this.courseOptions.includes(this.alumniForm.course)) {
                this.alumniForm.course = '';
            }
        },
        updateFilterCourseOptions() {
            if (this.filters.college) {
                const college = this.colleges.find(c => c.name === this.filters.college);
                this.filterCourseOptions = college ? college.courses : [];
            } else {
                const allCourses = this.colleges.flatMap(c => c.courses);
                this.filterCourseOptions = [...new Set(allCourses)];
            }
        },
        // Province/City API logic
        async fetchProvinces() {
            try {
                const res = await fetch('https://psgc.gitlab.io/api/provinces/');
                const data = await res.json();
                this.provinces = data.map(p => ({ code: p.code, name: p.name }));
            } catch (e) {
                this.provinces = [{ code: '0434', name: 'Laguna' }];
            }
        },
        async fetchCities() {
            this.cities = [];
            const province = this.provinces.find(p => p.name === this.alumniForm.province);
            if (!province) return;
            try {
                const res = await fetch(`https://psgc.gitlab.io/api/provinces/${province.code}/cities-municipalities/`);
                const data = await res.json();
                this.cities = data.map(c => ({ code: c.code, name: c.name }));
            } catch (e) {
                this.cities = [];
            }
        },
        // Notifications
        showNotification(message, type = 'success') {
            const id = this.notificationId++;
            this.notifications.push({ id, type, message });
            setTimeout(() => this.removeNotification(id), 3000);
        },
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        },
        async fetchAlumni() {
            this.isLoading = true;
            try {
                const response = await fetch('functions/get_alumni_active.php', { credentials: 'include' });
                const data = await response.json();
                
                if (!data.success) {
                    this.showNotification(data.message || 'Failed to load alumni data', 'error');
                    return;
                }
                
                // Process each alumni to fetch their detailed information
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
            } finally {
                this.isLoading = false;
            }
        },
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

        
        calculateYears(startDate, endDate) {
            const start = new Date(startDate);
            const end = endDate ? new Date(endDate) : new Date();
            const diffTime = Math.abs(end - start);
            return Math.floor(diffTime / (1000 * 60 * 60 * 24 * 365.25));
        },
        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        },
        getDocumentUrl(filename) {
            if (!filename) return null;
            const allowedExtensions = ['.pdf', '.jpg', '.png', '.doc', '.docx'];
            const ext = filename.slice(filename.lastIndexOf('.')).toLowerCase();
            if (!allowedExtensions.includes(ext)) {
                console.warn('Invalid document extension:', filename);
                return null;
            }
            return `/lspu_eis/uploads/documents/${encodeURIComponent(filename)}`;
        },
        getFileIcon(url) {
            if (!url) return 'fas fa-file text-gray-400';
            const ext = url.split('.').pop().toLowerCase();
            const icons = {
                pdf: 'fas fa-file-pdf text-red-500',
                jpg: 'fas fa-file-image text-green-500',
                jpeg: 'fas fa-file-image text-green-500',
                png: 'fas fa-file-image text-green-500',
                doc: 'fas fa-file-word text-blue-500',
                docx: 'fas fa-file-word text-blue-500',
                default: 'fas fa-file text-gray-400'
            };
            return icons[ext] || icons.default;
        },
        // Add/Update Alumni
        addAlumni() {
            fetch('functions/admin_alumni_add_edit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.alumniForm),
                credentials: 'include'
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification('Alumni added!', 'success');
                        this.showAlumniModal = false;
                        this.fetchAlumni();
                    } else {
                        this.showNotification(data.message || 'Failed to add alumni', 'error');
                    }
                })
                .catch(() => this.showNotification('Error adding alumni', 'error'));
        },
        updateAlumni() {
            fetch('functions/admin_alumni_add_edit.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ alumni_id: this.selectedAlumni.id, ...this.alumniForm }),
                credentials: 'include'
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification('Alumni updated!', 'success');
                        this.showAlumniModal = false;
                        this.fetchAlumni();
                    } else {
                        this.showNotification(data.message || 'Failed to update alumni', 'error');
                    }
                })
                .catch(() => this.showNotification('Error updating alumni', 'error'));
        },
        // Export
        exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const columns = [
                'Name', 'Email', 'Gender', 'Year Graduated', 'Course', 'College', 'Province', 'City/Municipality', 'Status'
            ];
            const rows = this.filteredAlumni.map(a => [
                `${a.first_name} ${a.middle_name} ${a.last_name}`,
                a.email, a.gender, a.year_graduated, a.course, a.college, a.province, a.city, a.status
            ]);
            doc.autoTable({ head: [columns], body: rows });
            doc.save('alumni.pdf');
            this.showNotification('PDF generated successfully!', 'success');
        },
        exportToExcel() {
            const wb = XLSX.utils.book_new();
            const wsData = [
                ['Name', 'Email', 'Gender', 'Year Graduated', 'Course', 'College', 'Province', 'City/Municipality', 'Status'],
                ...this.filteredAlumni.map(a => [
                    `${a.first_name} ${a.middle_name} ${a.last_name}`,
                    a.email, a.gender, a.year_graduated, a.course, a.college, a.province, a.city, a.status
                ])
            ];
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            XLSX.utils.book_append_sheet(wb, ws, 'Alumni');
            XLSX.writeFile(wb, 'alumni.xlsx');
            this.showNotification('Excel file generated successfully!', 'success');
        },
        // Accessibility helpers
        focusFirstInput(modalId) {
            this.$nextTick(() => {
                const modal = document.querySelector(`[aria-modal="true"][data-modal="${modalId}"]`) || document.querySelector('.fixed[role="dialog"]');
                if (modal) {
                    const input = modal.querySelector('input, select, textarea, button');
                    if (input) input.focus();
                }
            });
        },
        // Fetch profile data
        async fetchProfile() {
            try {
                const response = await fetch('functions/fetch_admin_details.php', { credentials: 'include' });
                const data = await response.json();
                if (data.success) {
                    this.profile = data.profile;
                } else {
                    console.error('Error fetching profile:', data.message);
                    this.showNotification(data.message || 'Failed to fetch admin details', 'error');
                    this.profile = { profile_pic: null, name: 'Admin' };
                }
            } catch (error) {
                console.error('Error fetching profile:', error);
                this.showNotification('Error fetching admin details', 'error');
                this.profile = { profile_pic: null, name: 'Admin' };
            }
        },
        
    }
}).mount('#app');