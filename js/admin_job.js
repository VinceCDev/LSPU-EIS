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
                { id: 1, title: 'Software Engineer', company: 'Tech Corp', type: 'Full-time', location: 'Remote', status: 'Active', description: 'Looking for a skilled software engineer with 3+ years of experience.', requirements: 'Bachelor\'s degree in Computer Science, 3+ years of experience in software development.', qualifications: 'Strong knowledge of Java/Python, experience with frameworks like Spring/Django.' },
                { id: 2, title: 'Data Analyst', company: 'Data Solutions Inc.', type: 'Part-time', location: 'San Pablo City', status: 'Active', description: 'Looking for a data analyst to help with data processing and reporting.', requirements: 'Bachelor\'s degree in Statistics or related field, proficiency in SQL and data visualization tools.', qualifications: 'Experience with data cleaning, analysis, and reporting using tools like Excel, Power BI, Tableau.' },
                { id: 3, title: 'UI/UX Designer', company: 'Design Studio', type: 'Contract', location: 'Santa Cruz', status: 'Closed', description: 'Looking for a UI/UX designer to create engaging user experiences.', requirements: 'Bachelor\'s degree in Design or related field, proficiency in Figma, Adobe XD, or similar tools.', qualifications: 'Experience with user research, wireframing, prototyping, and testing user flows.' },
                { id: 4, title: 'Marketing Manager', company: 'Marketing Masters', type: 'Full-time', location: 'Los Baños', status: 'Active', description: 'Looking for a marketing manager to lead digital marketing efforts.', requirements: 'Bachelor\'s degree in Marketing or related field, 5+ years of experience in digital marketing.', qualifications: 'Strong knowledge of SEO, SEM, social media marketing, and content marketing.' },
                { id: 5, title: 'Project Manager', company: 'Project Pros', type: 'Contract', location: 'Siniloan', status: 'Closed', description: 'Looking for a project manager for a new software development project.', requirements: 'Bachelor\'s degree in Business Administration or related field, 3+ years of experience in project management.', qualifications: 'Experience with Agile methodologies, JIRA, Confluence, and MS Project.' }
            ],
            searchQuery: '',
            filters: {
                company: '',
                type: '',
                status: ''
            },
            sortBy: 'id', // Changed default sort to 'id' since 'created_at' is removed
            sortOrder: 'desc',
            itemsPerPage: 5,
            currentPage: 1,
            showJobModal: false,
            modalMode: 'add', // 'add', 'edit', 'view'
            selectedJob: null,
            showDeleteModal: false,
            notifications: [],
            notificationId: 0,
            actionDropdown: null,
            // Job Form without employerQuestion and created_at
            jobForm: {
                title: '',
                company: '',
                type: '',
                location: '',
                description: '',
                requirements: '',
                qualifications: '',
                salary: '',
                status: 'Active'
            },
            isGeneratingSuggestions: false,
            requirementsSuggestions: [],
            qualificationsSuggestions: [],
            showRequirementsSuggestions: false,
            showQualificationsSuggestions: false,
            lastJobTitle: '',
            suggestionDebounce: null,
            uniqueCompanies: [],
            uniqueTypes: [],
            companiesList: [],
            jobLocationSuggestions: [],
            showJobLocationSuggestions: false,
            profile: {
                profile_pic: '',
                name: ''
            }
        }
    },
    mounted() {
        this.applyDarkMode();
        window.addEventListener('resize', this.handleResize);
        this.initializeUniqueLists();
        this.fetchCompaniesList();
        this.fetchJobs();
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
        initializeUniqueLists() {
            this.uniqueCompanies = [...new Set(this.jobs.map(job => job.company))];
            this.uniqueTypes = [...new Set(this.jobs.map(job => job.type))];
        },
        filterJobs() {
            this.currentPage = 1;
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
                salary: '',
                status: 'Active'
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
                        formData.append(key, this.jobForm[key]);
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
                        formData.append(key, this.jobForm[key]);
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
            setTimeout(() => this.removeNotification(id), 3000);
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
            this.requirementsSuggestions = [];
            this.qualificationsSuggestions = [];
            this.showRequirementsSuggestions = false;
            this.showQualificationsSuggestions = false;
            this.isGeneratingSuggestions = false;
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
                const tableColumn = ["ID", "Title", "Company", "Type", "Location", "Status", "Description", "Requirements", "Qualifications", "Salary"];
                const tableRows = [];

                this.jobs.forEach(job => {
                    const jobData = [
                        job.id,
                        job.title,
                        job.company,
                        job.type,
                        job.location,
                        job.status,
                        job.description,
                        job.requirements,
                        job.qualifications,
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
                this.uniqueCompanies = data;
            } catch (e) {
                this.showNotification('Failed to fetch companies.', 'error');
            }
        },
        async fetchJobs() {
            try {
                const res = await fetch('functions/get_jobs.php');
                const data = await res.json();
                this.jobs = data.map(job => {
                    const company = this.companiesList.find(c => c.user_id == job.employer_id);
                    return {
                        ...job,
                        id: job.job_id,
                        company: company ? company.company_name : 'Unknown',
                        employer_id: job.employer_id,
                        logo: company ? company.company_logo : null
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
            const apiKey = 'b25cb94f83684f6aa21cbd86f93c9417';
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
        },
        async generateSuggestions(field) {
            // Check if job title is empty
            if (!this.jobForm.title.trim()) {
                this.showNotification('Please enter a job title first to generate suggestions.', 'warning', 'Suggestion');
                if (field === 'requirements') {
                    this.requirementsSuggestions = [];
                    this.showRequirementsSuggestions = false;
                } else if (field === 'qualifications') {
                    this.qualificationsSuggestions = [];
                    this.showQualificationsSuggestions = false;
                }
                return;
            }
    
            // Debounce to prevent excessive API calls
            clearTimeout(this.suggestionDebounce);
            this.suggestionDebounce = setTimeout(async () => {
                try {
                    // Make API call to suggestions_gemini.php with job title
                    const response = await fetch('functions/suggestions_gemini.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            jobTitle: this.jobForm.title.trim(),
                            field: field
                        })
                    });
                    const data = await response.json();
    
                    // Fallback predefined suggestions if backend fails or for demo
                    if (!data.success) {
                        if (field === 'requirements') {
                            data.suggestions = [
                                'Bachelor\'s degree in relevant field',
                                '3+ years of experience in related role',
                                'Proficiency in relevant tools/technologies',
                                'Strong communication skills'
                            ];
                        } else if (field === 'qualifications') {
                            data.suggestions = [
                                'Experience with industry-standard software',
                                'Ability to work in a team environment',
                                'Strong problem-solving skills',
                                'Relevant certifications preferred'
                            ];
                        }
                    }
    
                    if (data.success) {
                        if (field === 'requirements') {
                            this.requirementsSuggestions = data.suggestions;
                            this.showRequirementsSuggestions = true;
                        } else if (field === 'qualifications') {
                            this.qualificationsSuggestions = data.suggestions;
                            this.showQualificationsSuggestions = true;
                        }
                    } else {
                        this.showNotification('Failed to generate suggestions: ' + (data.error || 'Unknown error'), 'error', 'Suggestion Error');
                    }
                } catch (error) {
                    console.error('Error generating suggestions:', error);
                    this.showNotification('Failed to generate suggestions. Please try again.', 'error', 'Suggestion Error');
                }
            }, 500); // 500ms debounce delay
        },
    
        selectSuggestion(field, suggestion) {
            if (field === 'requirements') {
                const currentRequirements = this.jobForm.requirements.trim();
                const separator = currentRequirements ? '\n• ' : '• ';
                this.jobForm.requirements += separator + suggestion;
                this.showRequirementsSuggestions = false;
            } else if (field === 'qualifications') {
                const currentQualifications = this.jobForm.qualifications.trim();
                const separator = currentQualifications ? '\n• ' : '• ';
                this.jobForm.qualifications += separator + suggestion;
                this.showQualificationsSuggestions = false;
            }
        },
    
        applyAllSuggestions(field) {
            if (field === 'requirements' && this.requirementsSuggestions.length > 0) {
                const currentRequirements = this.jobForm.requirements.trim();
                const separator = currentRequirements ? '\n' : '';
                this.jobForm.requirements += separator + this.requirementsSuggestions.map(s => '• ' + s).join('\n');
                this.showRequirementsSuggestions = false;
            } else if (field === 'qualifications' && this.qualificationsSuggestions.length > 0) {
                const currentQualifications = this.jobForm.qualifications.trim();
                const separator = currentQualifications ? '\n' : '';
                this.jobForm.qualifications += separator + this.qualificationsSuggestions.map(s => '• ' + s).join('\n');
                this.showQualificationsSuggestions = false;
            }
        },
    
        closeSuggestions(field) {
            if (field === 'requirements') {
                this.showRequirementsSuggestions = false;
            } else if (field === 'qualifications') {
                this.showQualificationsSuggestions = false;
            }
        },
    
        hideSuggestionsWithDelay(field) {
            setTimeout(() => {
                if (field === 'requirements') {
                    this.showRequirementsSuggestions = false;
                } else if (field === 'qualifications') {
                    this.showQualificationsSuggestions = false;
                }
            }, 200);
        }    
    }
}).mount('#app');