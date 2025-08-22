const { createApp } = Vue;
    createApp({
        data() {
            return {
                sidebarActive: window.innerWidth >= 768,
                companiesDropdownOpen: false,
                alumniDropdownOpen: false,
                profileDropdownOpen: false,
                darkMode: localStorage.getItem('darkMode') === 'true' || 
                     (localStorage.getItem('darkMode') === null && 
                      window.matchMedia('(prefers-color-scheme: dark)').matches),
                showLogoutModal: false,
                isMobile: window.innerWidth < 768,
                // Job Posting Management Data
                jobs: [
                    { id: 1, title: 'Software Engineer', company: 'Tech Corp', type: 'Full-time', location: 'Remote', status: 'Active', created_at: '2023-01-15', description: 'Looking for a skilled software engineer with 3+ years of experience.', requirements: 'Bachelor\'s degree in Computer Science, 3+ years of experience in software development.', qualifications: 'Strong knowledge of Java/Python, experience with frameworks like Spring/Django.', employerQuestion: 'Do you have experience with microservices architecture?' },
                    { id: 2, title: 'Data Analyst', company: 'Data Solutions Inc.', type: 'Part-time', location: 'San Pablo City', status: 'Active', created_at: '2023-02-01', description: 'Looking for a data analyst to help with data processing and reporting.', requirements: 'Bachelor\'s degree in Statistics or related field, proficiency in SQL and data visualization tools.', qualifications: 'Experience with data cleaning, analysis, and reporting using tools like Excel, Power BI, Tableau.', employerQuestion: 'Can you explain how you would approach a data-driven decision-making process?' },
                    { id: 3, title: 'UI/UX Designer', company: 'Design Studio', type: 'Contract', location: 'Santa Cruz', status: 'Closed', created_at: '2023-03-10', description: 'Looking for a UI/UX designer to create engaging user experiences.', requirements: 'Bachelor\'s degree in Design or related field, proficiency in Figma, Adobe XD, or similar tools.', qualifications: 'Experience with user research, wireframing, prototyping, and testing user flows.', employerQuestion: 'How do you validate design decisions with data?' },
                    { id: 4, title: 'Marketing Manager', company: 'Marketing Masters', type: 'Full-time', location: 'Los Baños', status: 'Active', created_at: '2023-04-05', description: 'Looking for a marketing manager to lead digital marketing efforts.', requirements: 'Bachelor\'s degree in Marketing or related field, 5+ years of experience in digital marketing.', qualifications: 'Strong knowledge of SEO, SEM, social media marketing, and content marketing.', employerQuestion: 'What\'s your strategy for increasing website traffic?' },
                    { id: 5, title: 'Project Manager', company: 'Project Pros', type: 'Contract', location: 'Siniloan', status: 'Closed', created_at: '2023-05-20', description: 'Looking for a project manager for a new software development project.', requirements: 'Bachelor\'s degree in Business Administration or related field, 3+ years of experience in project management.', qualifications: 'Experience with Agile methodologies, JIRA, Confluence, and MS Project.', employerQuestion: 'How do you manage stakeholder expectations during project phases?' }
                ],
                searchQuery: '',
                filters: {
                    company: '',
                    type: '',
                    status: ''
                },
                sortBy: 'created_at',
                sortOrder: 'desc',
                itemsPerPage: 5,
                currentPage: 1,
                showJobModal: false,
                modalMode: 'add', // 'add', 'edit', 'view'
                selectedJob: null,
                showDeleteModal: false,
                notifications: [], // New for multiple notifications
                notificationId: 0, // New for notification ID
                actionDropdown: null,
                // New for Job Posting Management
                jobForm: {
                    title: '',
                    company: '',
                    type: '',
                    location: '',
                    description: '',
                    requirements: '',
                    qualifications: '',
                    employerQuestion: '',
                    salary: '',
                    status: 'Active',
                    created_at: '' // Added for new jobs
                },
                uniqueCompanies: [],
                uniqueTypes: [],
                companiesList: [], // For dropdown
                jobLocationSuggestions: [],
                showJobLocationSuggestions: false,
                profile: { // New for profile data
                    profile_pic: '',
                    name: '',
                },
            }
        },
        mounted() {
            this.applyDarkMode();
            window.addEventListener('resize', this.handleResize);
            this.initializeUniqueLists();
            this.fetchCompaniesList();
            this.fetchJobs(); // Fetch jobs from backend
            fetch('functions/fetch_admin_details.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.profile) {
                        this.profile = data.profile;
                    }
                });
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
                        job.company.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        job.type.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        job.location.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        job.status.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                }
                if (this.filters.company) {
                    filtered = filtered.filter(job => job.company === this.filters.company);
                }
                if (this.filters.type) {
                    filtered = filtered.filter(job => job.type === this.filters.type);
                }
                if (this.filters.status) {
                    filtered = filtered.filter(job => job.status === this.filters.status);
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
                this.companiesDropdownOpen = false;
                this.alumniDropdownOpen = false;
            },
            handleNavClick() {
                if (this.isMobile) {
                    this.sidebarActive = false;
                }
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
            confirmLogout() {
                this.showLogoutModal = true;
            },
            logout() {
                window.location.href = 'logout.php';
            },
            handleResize() {
                this.isMobile = window.innerWidth < 768;
                if (window.innerWidth >= 768) {
                    this.sidebarActive = true;
                } else {
                    this.sidebarActive = false;
                }
            },
            // Job Posting Management Methods
            initializeUniqueLists() {
                this.uniqueCompanies = [...new Set(this.jobs.map(job => job.company))];
                this.uniqueTypes = [...new Set(this.jobs.map(job => job.type))];
            },
            filterJobs() {
                this.currentPage = 1; // Reset to first page when filters change
            },
            sortJobs(field) {
                if (this.sortBy === field) {
                    this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortBy = field;
                    this.sortOrder = 'asc';
                }
            },
            openAddModal() {
                this.modalMode = 'add';
                this.selectedJob = null;
                this.jobForm = {
                    title: '',
                    company: '',
                    type: '',
                    location: '',
                    description: '',
                    requirements: '',
                    qualifications: '',
                    employerQuestion: '',
                    salary: '',
                    status: 'Active',
                    created_at: '' // Initialize created_at for new jobs
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
                    const res = await fetch('functions/admin_job_add_edit.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    this.showNotification(data.message, data.success ? 'success' : 'error');
                    if (data.success) {
                        this.closeJobModal();
                        this.fetchJobs && this.fetchJobs();
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
                    const res = await fetch('functions/admin_job_add_edit.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    this.showNotification(data.message, data.success ? 'success' : 'error');
                    if (data.success) {
                        this.closeJobModal();
                        this.fetchJobs && this.fetchJobs();
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
                    const res = await fetch('functions/admin_job_add_edit.php', {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ job_id: this.selectedJob.id || this.selectedJob.job_id })
                    });
                    const data = await res.json();
                    this.showNotification(data.message, data.success ? 'success' : 'error');
                    if (data.success) {
                        this.closeJobModal();
                        this.fetchJobs && this.fetchJobs();
                    }
                    this.showDeleteModal = false;
                } catch (error) {
                    this.showNotification('Failed to delete job.', 'error');
                }
            },
            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString();
            },
            showNotification(message, type = 'success', title = 'Success') {
                const id = this.notificationId++;
                this.notifications.push({ id, type, title, message });
                setTimeout(() => this.removeNotification(id), 3000); // Auto-dismiss after 3 seconds
            },
            removeNotification(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
            },
            toggleActionDropdown(jobId) {
                this.actionDropdown = this.actionDropdown === jobId ? null : jobId;
            },
            closeJobModal() {
                this.showJobModal = false;
                this.actionDropdown = null;
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
            toggleProfileDropdown() {
                this.profileDropdownOpen = !this.profileDropdownOpen;
            },
            async fetchCompaniesList() {
                try {
                    const res = await fetch('functions/get_employers.php');
                    const data = await res.json();
                    this.companiesList = data;
                    // For dropdown, set uniqueCompanies to companiesList
                    this.uniqueCompanies = data;
                } catch (e) {
                    this.showNotification('Failed to fetch companies.', 'error');
                }
            },
            async fetchJobs() {
                try {
                    const res = await fetch('functions/get_jobs.php');
                    const data = await res.json();
                    // Map company name from companiesList
                    this.jobs = data.map(job => {
                        const company = this.companiesList.find(c => c.user_id == job.employer_id);
                        return {
                            ...job,
                            id: job.job_id,
                            company: company ? company.company_name : 'Unknown',
                            employer_id: job.employer_id,
                            employerQuestion: job.employer_question
                        };
                    });
                    this.initializeUniqueLists();
                } catch (e) {
                    this.showNotification('Failed to fetch jobs.', 'error');
                }
            },
            async fetchJobLocationSuggestions() {
                const val = this.jobForm.location;
                if (!val || val.length < 3) {
                    this.jobLocationSuggestions = [];
                    this.showJobLocationSuggestions = false;
                    return;
                }
                const apiKey = 'b25cb94f83684f6aa21cbd86f93c9417'; // Geoapify API key
                const url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(val)}&limit=5&apiKey=${apiKey}`;
                try {
                    const res = await fetch(url);
                    const data = await res.json();
                    this.jobLocationSuggestions = data.features.map(f => f.properties.formatted);
                    this.showJobLocationSuggestions = true;
                } catch (e) {
                    this.jobLocationSuggestions = [];
                    this.showJobLocationSuggestions = false;
                }
            },
            selectJobLocationSuggestion(suggestion) {
                this.jobForm.location = suggestion;
                this.jobLocationSuggestions = [];
                this.showJobLocationSuggestions = false;
            },
            hideJobLocationSuggestions() {
                setTimeout(() => { this.showJobLocationSuggestions = false; }, 150);
            }
        }
    }).mount('#app');