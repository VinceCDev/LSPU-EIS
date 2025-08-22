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
            applicants: [],
            searchQuery: '',
            itemsPerPage: 5,
            currentPage: 1,
            showApplicantModal: false,
            selectedApplicant: {},
            showDeleteModal: false,
            applicantToDelete: null,
            actionDropdown: null,
            filters: {
                status: '',
                appliedFor: '',
                experience: ''
            },
            uniquePositions: [],
            uniqueExperiences: [],
            notifications: [],
            notificationId: 0,
            profile: {
                profile_pic: '',
                name: '',
            }
        };
    },
    mounted() {
        this.fetchApplications();
        this.applyDarkMode();
        window.addEventListener('resize', this.handleResize);
        this.handleResize(); // Ensure correct state on mount
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
        darkMode(val) {
            this.applyDarkMode();
        }
    },
    computed: {
        filteredApplicants() {
            let filtered = this.applicants;
            if (this.searchQuery) {
                filtered = filtered.filter(applicant =>
                    applicant.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    applicant.email.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    applicant.appliedFor.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    applicant.status.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
            if (this.filters.status) {
                filtered = filtered.filter(applicant => applicant.status === this.filters.status);
            }
            if (this.filters.appliedFor) {
                filtered = filtered.filter(applicant => applicant.appliedFor === this.filters.appliedFor);
            }
            if (this.filters.experience) {
                filtered = filtered.filter(applicant => applicant.experience == this.filters.experience);
            }
            return filtered;
        },
        paginatedApplicants() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredApplicants.slice(start, start + this.itemsPerPage);
        },
        totalPages() {
            return Math.ceil(this.filteredApplicants.length / this.itemsPerPage) || 1;
        }
    },
    methods: {
        handleResize() {
            this.isMobile = window.innerWidth < 768;
            this.sidebarActive = !this.isMobile;
        },
        toggleSidebar() {
            if (this.isMobile) {
                this.sidebarActive = !this.sidebarActive;
                this.companiesDropdownOpen = false;
                this.alumniDropdownOpen = false;
            }
        },
        handleNavClick() {
            if (this.isMobile) {
                this.sidebarActive = false;
            }
        },
        toggleProfileDropdown() {
            this.profileDropdownOpen = !this.profileDropdownOpen;
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
        filterApplicants() {
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
        async fetchApplications() {
            try {
                const res = await fetch('functions/get_applications_admin.php');
                const data = await res.json();
                if (Array.isArray(data.applications)) {
                    this.applicants = data.applications.map(app => ({
                        id: app.application_id,
                        name: app.alumni_name,
                        email: app.email,
                        appliedFor: app.title,
                        appliedDate: app.applied_at,
                        experience: app.year_graduated ? (new Date().getFullYear() - parseInt(app.year_graduated)) : '',
                        status: app.job_status,
                        alumni: app, // full alumni details
                        job: app // full job details
                    }));
                    this.initializeUniqueLists();
                }
            } catch (e) {
                this.showNotification('Failed to fetch applications', 'error');
            }
        },
        viewApplicant(applicant) {
            this.selectedApplicant = applicant;
            this.showApplicantModal = true;
        },
        closeApplicantModal() {
            this.showApplicantModal = false;
            this.selectedApplicant = {};
        },
        confirmDelete(applicant) {
            this.showDeleteModal = true;
            this.applicantToDelete = applicant;
        },
        confirmDeleteApplicant() {
            if (this.applicantToDelete) {
                this.deleteApplicant(this.applicantToDelete);
                this.applicantToDelete = null;
            }
            this.showDeleteModal = false;
        },
        deleteApplicant(applicant) {
            this.applicants = this.applicants.filter(a => a.id !== applicant.id);
            this.showNotification('Applicant deleted successfully!', 'success');
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString();
        },
        confirmLogout() {
            this.showLogoutModal = true;
        },
        logout() {
            window.location.href = 'logout.php';
        },
        initializeUniqueLists() {
            this.uniquePositions = [...new Set(this.applicants.map(a => a.appliedFor))];
            this.uniqueExperiences = [...new Set(this.applicants.map(a => a.experience))].sort((a, b) => a - b);
        },
        exportToExcel() {
            let csv = 'Name,Email,Applied For,Applied Date,Experience,Status\n';
            this.filteredApplicants.forEach(a => {
                csv += `${a.name},${a.email},${a.appliedFor},${a.appliedDate},${a.experience},${a.status}\n`;
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'applicants.csv';
            a.click();
            window.URL.revokeObjectURL(url);
            this.showNotification('Applicants exported to Excel successfully!', 'success');
        },
        exportToPDF() {
            try {
                const doc = new window.jspdf.jsPDF();
                const tableColumn = ["Name", "Email", "Applied For", "Applied Date", "Experience", "Status"];
                const tableRows = [];
                this.filteredApplicants.forEach(a => {
                    tableRows.push([
                        a.name,
                        a.email,
                        a.appliedFor,
                        a.appliedDate,
                        a.experience,
                        a.status
                    ]);
                });
                doc.autoTable({
                    head: [tableColumn],
                    body: tableRows
                });
                doc.save("applicants.pdf");
                this.showNotification('Applicants exported to PDF successfully!', 'success');
            } catch (error) {
                this.showNotification('Failed to export applicants to PDF.', 'error');
            }
        },
        showNotification(message, type = 'success') {
            const id = this.notificationId++;
            this.notifications.push({ id, type, message });
            setTimeout(() => this.removeNotification(id), 3000);
        },
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }
}).mount('#app');