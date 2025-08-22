const { createApp } = Vue;
    createApp({
        data() {
            return {
                sidebarActive: window.innerWidth >= 768,
                profileDropdownOpen: false,
                darkMode: localStorage.getItem('darkMode') === 'true' || 
                     (localStorage.getItem('darkMode') === null && 
                      window.matchMedia('(prefers-color-scheme: dark)').matches),
                showLogoutModal: false,
                isMobile: window.innerWidth < 768,
                jobs: [
                    { id: 1, title: 'Software Engineer', type: 'Full-time', location: 'Remote', status: 'Active', created_at: '2023-01-15', description: 'Looking for a skilled software engineer with 3+ years of experience.', requirements: 'Bachelor\'s degree in Computer Science, 3+ years of experience in software development.', salary: '₱40,000 - ₱60,000' },
                    { id: 2, title: 'Data Analyst', type: 'Part-time', location: 'San Pablo City', status: 'Active', created_at: '2023-02-01', description: 'Looking for a data analyst to help with data processing and reporting.', requirements: 'Bachelor\'s degree in Statistics or related field, proficiency in SQL and data visualization tools.', salary: '₱25,000 - ₱35,000' },
                    { id: 3, title: 'UI/UX Designer', type: 'Contract', location: 'Santa Cruz', status: 'Closed', created_at: '2023-03-10', description: 'Looking for a UI/UX designer to create engaging user experiences.', requirements: 'Bachelor\'s degree in Design or related field, proficiency in Figma, Adobe XD, or similar tools.', salary: '₱30,000 - ₱50,000' }
                ],
                searchQuery: '',
                itemsPerPage: 5,
                currentPage: 1,
                showJobModal: false,
                modalMode: 'add', // 'add', 'edit', 'view'
                selectedJob: null,
                showDeleteModal: false,
                actionDropdown: null,
                jobForm: {
                    title: '',
                    type: '',
                    location: '',
                    description: '',
                    requirements: '',
                    qualifications: '',
                    employerQuestion: '',
                    salary: '',
                    status: 'Active',
                    created_at: ''
                },
                notifications: [],
                notificationId: 0,
                filters: {
                    type: '',
                    status: '',
                    location: ''
                },
                uniqueTypes: [],
                uniqueStatuses: [],
                uniqueLocations: [],
                employerProfile: {
                    company_name: '',
                    company_logo: ''
                },
                activePage: 'jobs',
            }
        },
        mounted() {
            this.applyDarkMode();
            this.fetchJobs(); // Fetch jobs on mount
            fetch('functions/fetch_employer_details.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.profile) {
                        this.employerProfile = data.profile;
                    }
                });
            window.addEventListener('resize', this.handleResize);
            const path = document.location.pathname;
            if (path.endsWith('employer_dashboard.php')) this.activePage = 'dashboard';
            else if (path.endsWith('employer_jobposting.php')) this.activePage = 'jobs';
            else if (path.endsWith('employer_applicants.php')) this.activePage = 'applicants';
            else if (path.endsWith('employer_messages.php')) this.activePage = 'messages';
            else if (path.endsWith('employer_profile.php')) this.activePage = 'profile';
        },
        watch: {
            darkMode(val) {
                this.applyDarkMode();
            }
        },
        computed: {
            filteredJobs() {
                let filtered = this.jobs;
                if (this.searchQuery) {
                    filtered = filtered.filter(job =>
                        job.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        job.type.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        job.location.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        job.status.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                }
                if (this.filters.type) {
                    filtered = filtered.filter(job => job.type === this.filters.type);
                }
                if (this.filters.status) {
                    filtered = filtered.filter(job => job.status === this.filters.status);
                }
                if (this.filters.location) {
                    filtered = filtered.filter(job => job.location === this.filters.location);
                }
                return filtered;
            },
            paginatedJobs() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredJobs.slice(start, end);
            },
            totalPages() {
                return Math.ceil(this.filteredJobs.length / this.itemsPerPage);
            }
        },
        methods: {
            toggleSidebar() {
                this.sidebarActive = !this.sidebarActive;
            },
            toggleProfileDropdown() {
                this.profileDropdownOpen = !this.profileDropdownOpen;
            },
            confirmLogout() {
                this.showLogoutModal = true;
            },
            logout() {
                window.location.href = 'functions/employer_logout.php';
            },
            toggleDarkMode() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('darkMode', this.darkMode.toString());
                this.applyDarkMode();
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
            handleResize() {
                this.isMobile = window.innerWidth < 768;
                if (window.innerWidth >= 768) {
                    this.sidebarActive = true;
                } else {
                    this.sidebarActive = false;
                }
            },
            initializeUniqueLists() {
                this.uniqueTypes = [...new Set(this.jobs.map(job => job.type))];
                this.uniqueStatuses = [...new Set(this.jobs.map(job => job.status))];
                this.uniqueLocations = [...new Set(this.jobs.map(job => job.location))];
            },
            filterJobs() {
                this.currentPage = 1; // Reset to first page when filters change
            },
            openAddModal() {
                this.modalMode = 'add';
                this.selectedJob = null;
                this.jobForm = {
                    title: '',
                    type: '',
                    location: '',
                    description: '',
                    requirements: '',
                    qualifications: '',
                    employerQuestion: '',
                    salary: '',
                    status: 'Active',
                    created_at: ''
                };
                this.showJobModal = true;
            },
            openEditModal(job) {
                this.modalMode = 'edit';
                this.selectedJob = job;
                this.jobForm = { ...job };
                this.showJobModal = true;
            },
            viewJob(job) {
                this.modalMode = 'view';
                this.selectedJob = job;
                this.showJobModal = true;
            },
            async fetchJobs() {
                try {
                    const res = await fetch('functions/get_employer_jobs.php');
                    const data = await res.json();
                    // No need to map company name, just use the job data
                    this.jobs = data.map(job => ({
                        ...job,
                        id: job.job_id,
                        employerQuestion: job.employer_question
                    }));
                    this.initializeUniqueLists();
                } catch (e) {
                    this.showNotification('Failed to fetch jobs.', 'error');
                }
            },
            async addJob() {
                try {
                    const formData = new FormData();
                    for (const key in this.jobForm) {
                        if (this.jobForm[key] !== null && this.jobForm[key] !== undefined && this.jobForm[key] !== '') {
                            if (key === 'employerQuestion') {
                                formData.append('employer_question', this.jobForm[key]);
                            } else {
                                formData.append(key, this.jobForm[key]);
                            }
                        }
                    }
                    const res = await fetch('functions/employer_job_add_edit.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    this.showNotification(data.message, data.success ? 'success' : 'error');
                    if (data.success) {
                        this.closeJobModal();
                        this.fetchJobs();
                    }
                } catch (error) {
                    this.showNotification('Failed to post job.', 'error');
                }
            },
            async updateJob() {
                try {
                    const formData = new FormData();
                    for (const key in this.jobForm) {
                        if (this.jobForm[key] !== null && this.jobForm[key] !== undefined && this.jobForm[key] !== '') {
                            if (key === 'employerQuestion') {
                                formData.append('employer_question', this.jobForm[key]);
                            } else {
                                formData.append(key, this.jobForm[key]);
                            }
                        }
                    }
                    formData.append('job_id', this.selectedJob.id || this.selectedJob.job_id);
                    const res = await fetch('functions/employer_job_add_edit.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    this.showNotification(data.message, data.success ? 'success' : 'error');
                    if (data.success) {
                        this.closeJobModal();
                        this.fetchJobs();
                    }
                } catch (error) {
                    this.showNotification('Failed to update job.', 'error');
                }
            },
            confirmDelete(job) {
                this.selectedJob = job;
                this.showDeleteModal = true;
            },
            async deleteJob() {
                try {
                    const res = await fetch('functions/employer_job_add_edit.php', {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ job_id: this.selectedJob.id || this.selectedJob.job_id })
                    });
                    const data = await res.json();
                    this.showNotification(data.message, data.success ? 'success' : 'error');
                    if (data.success) {
                        this.closeJobModal();
                        this.fetchJobs();
                    }
                    this.showDeleteModal = false;
                } catch (error) {
                    this.showNotification('Failed to delete job.', 'error');
                }
            },
            closeJobModal() {
                this.showJobModal = false;
                this.actionDropdown = null;
            },
            toggleActionDropdown(jobId) {
                this.actionDropdown = this.actionDropdown === jobId ? null : jobId;
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
            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString();
            },
            async exportToExcel() {
                try {
                    const workbook = XLSX.utils.book_new();
                    const worksheet = XLSX.utils.json_to_sheet(this.jobs);
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Jobs");
                    XLSX.writeFile(workbook, "job_postings.xlsx");
                    this.showNotification('Job postings exported to Excel successfully!', 'success', 'Export Success');
                } catch (error) {
                    console.error('Error exporting to Excel:', error);
                    this.showNotification('Failed to export job postings to Excel.', 'error', 'Export Error');
                }
            },
            async exportToPDF() {
                try {
                    const doc = new window.jspdf.jsPDF();
                    const tableColumn = ["ID", "Title", "Company", "Type", "Location", "Status", "Posted Date", "Description", "Requirements", "Qualifications", "Employer Question", "Salary"];
                    const tableRows = [];

                    this.jobs.forEach(job => {
                        const jobData = [
                            job.id,
                            job.title,
                            job.company,
                            job.type,
                            job.location,
                            job.status,
                            job.created_at,
                            job.description,
                            job.requirements,
                            job.qualifications,
                            job.employerQuestion,
                            job.salary
                        ];
                        tableRows.push(jobData);
                    });

                    doc.autoTable({
                        head: [tableColumn],
                        body: tableRows
                    });
                    doc.save("job_postings.pdf");
                    this.showNotification('Job postings exported to PDF successfully!', 'success', 'Export Success');
                } catch (error) {
                    console.error('Error exporting to PDF:', error);
                    this.showNotification('Failed to export job postings to PDF.', 'error', 'Export Error');
                }
            },
            showNotification(message, type = 'success', title = 'Success') {
                const id = this.notificationId++;
                this.notifications.push({ id, type, title, message });
                setTimeout(() => this.removeNotification(id), 3000); // Auto-dismiss after 3 seconds
            },
            removeNotification(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }
        }
    }).mount('#app');