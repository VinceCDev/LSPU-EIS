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
            resources: [],
            jobOptions: [],
            jobResources: [],
            searchQuery: '',
            itemsPerPage: 5,
            currentPage: 1,
            showResourceModal: false,
            modalMode: 'add', // 'add', 'edit', 'view'
            selectedResource: null,
            showDeleteModal: false,
            actionDropdown: null,
            resourceForm: {
                id: '',
                job_id: '',
                department: '',
                financial_budget: 0,
                technology_needed: '',
                training_required: '',
                physical_objects: '',
                staffing_requirements: '',
                timeline: '',
                status: 'Planning',
                notes: ''
            },
            notifications: [],
            notificationId: 0,
            showAdvancedFilters: false,
            filters: {
                job: '',
                status: '',
                department: '', // Add this
                // ... other filter properties
            },
            departmentOptions: [],
            employerProfile: {
                company_name: '',
                company_logo: ''
            },
            activePage: 'job_resources',
        }
    },
    mounted() {
        this.applyDarkMode();
        this.fetchJobResources(); // Fetch resources on mount
        this.fetchEmployerProfile(); // Fetch employer profile
        this.fetchJobOptions();
        window.addEventListener('resize', this.handleResize);
        const path = document.location.pathname;
        if (path.endsWith('employer_dashboard.php')) this.activePage = 'dashboard';
        else if (path.endsWith('employer_jobposting.php')) this.activePage = 'jobs';
        else if (path.endsWith('employer_resources.php')) this.activePage = 'job_resources';
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
        filteredResources() {
            let filtered = this.resources;
            
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(resource =>
                    (resource.department && resource.department.toLowerCase().includes(query)) ||
                    (this.getJobTitle(resource.job_id) && this.getJobTitle(resource.job_id).toLowerCase().includes(query)) ||
                    (resource.technology_needed && resource.technology_needed.toLowerCase().includes(query)) ||
                    (resource.notes && resource.notes.toLowerCase().includes(query)) ||
                    (resource.training_required && resource.training_required.toLowerCase().includes(query)) ||
                    (resource.physical_objects && resource.physical_objects.toLowerCase().includes(query)) ||
                    (resource.staffing_requirements && resource.staffing_requirements.toLowerCase().includes(query))
                );
            }
            
            if (this.filters.job) {
                filtered = filtered.filter(resource => resource.job_id == this.filters.job);
            }
            
            if (this.filters.status) {
                filtered = filtered.filter(resource => resource.status === this.filters.status);
            }
            
            if (this.filters.department) {
                filtered = filtered.filter(resource => resource.department === this.filters.department);
            }
            
            return filtered;
        },
        paginatedResources() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredResources.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.filteredResources.length / this.itemsPerPage);
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
        filterResources() {
            this.currentPage = 1; // Reset to first page when filters change
        },
        openEditResourceModal(resource) {
            this.resourceForm = { ...resource };
            this.modalMode = 'edit';
            this.showResourceModal = true;
        },
        
        // Open add modal
        openAddResourceModal(jobId) {
            this.resourceForm = {
                id: '',
                job_id: jobId,
                department: '',
                financial_budget: 0,
                technology_needed: '',
                training_required: '',
                physical_objects: '',
                staffing_requirements: '',
                timeline: '',
                status: 'Planning',
                notes: ''
            };
            this.modalMode = 'add';
            this.showResourceModal = true;
        },    
        
        // Add these methods to your Vue app:
        openAddModal() {
            this.resourceForm = {
                id: '',
                job_id: '',
                department: '',
                financial_budget: 0,
                technology_needed: '',
                training_required: '',
                physical_objects: '',
                staffing_requirements: '',
                timeline: '',
                status: 'Planning',
                notes: ''
            };
            this.modalMode = 'add';
            this.showResourceModal = true;
        },

        openEditModal(resource) {
            this.resourceForm = { ...resource };
            this.modalMode = 'edit';
            this.showResourceModal = true;
        },

        closeResourceModal() {
            this.showResourceModal = false;
            this.modalMode = 'add';
            this.selectedResource = null;
        },

        viewResource(resource) {
            this.selectedResource = resource;
            this.modalMode = 'view';
            this.showResourceModal = true;
        },

        confirmDelete(resource) {
            this.selectedResource = resource;
            this.showDeleteModal = true;
        },
        
        getJobTitle(jobId) {
            const job = this.jobOptions.find(j => j.job_id == jobId);
            return job ? job.title : 'Unknown Job';
        },
        getResourceIcon(type) {
            switch(type) {
                case 'Document': return 'fa-file-pdf';
                case 'Link': return 'fa-link';
                case 'Video': return 'fa-video';
                default: return 'fa-file-alt';
            }
        },
        async fetchEmployerProfile() {
            try {
                const res = await fetch('functions/fetch_employer_details.php');
                const data = await res.json();
                if (data.success && data.profile) {
                    this.employerProfile = data.profile;
                    // Add uploads/logos/ path prefix to the logo
                    if (this.employerProfile.company_logo) {
                        this.employerProfile.company_logo = this.employerProfile.company_logo;
                    }
                }
            } catch (error) {
                console.error('Failed to fetch employer profile:', error);
            }
        },

        // In your Vue.js methods
        async fetchJobOptions() {
            try {
                const res = await fetch('functions/fetch_employer_jobs.php');
                const text = await res.text();
                
                // Check if response is HTML instead of JSON
                if (text.trim().startsWith('<')) {
                    console.error('API returned HTML instead of JSON:', text.substring(0, 200));
                    this.showNotification('Server error: Invalid response format', 'error');
                    return;
                }
                
                const data = JSON.parse(text);
                
                if (data.success) {
                    this.jobOptions = data.jobs;
                } else {
                    this.showNotification(data.message || 'Failed to fetch job options.', 'error');
                }
            } catch (error) {
                console.error('Error fetching job options:', error);
                this.showNotification('Error fetching job options', 'error');
            }
        },

        extractDepartmentOptions() {
            // Extract unique departments from your resources
            const departments = new Set();
            this.resources.forEach(resource => {
                if (resource.department) {
                    departments.add(resource.department);
                }
            });
            this.departmentOptions = Array.from(departments).sort();
        },
        
        // Update your filteredResources computed property:
        filteredResources() {
            let filtered = this.resources;
            
            if (this.searchQuery) {
                filtered = filtered.filter(resource =>
                    resource.department.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    this.getJobTitle(resource.job_id).toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (resource.technology_needed && resource.technology_needed.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (resource.notes && resource.notes.toLowerCase().includes(this.searchQuery.toLowerCase()))
                );
            }
            
            if (this.filters.job) {
                filtered = filtered.filter(resource => resource.job_id == this.filters.job);
            }
            
            if (this.filters.status) {
                filtered = filtered.filter(resource => resource.status === this.filters.status);
            }
            
            if (this.filters.department) {
                filtered = filtered.filter(resource => resource.department === this.filters.department);
            }
            
            return filtered;
        },
        
        // Call this after fetching resources
        async fetchJobResources() {
            try {
                const res = await fetch('functions/get_job_resources.php');
                const text = await res.text();
                
                if (text.trim().startsWith('<')) {
                    console.error('API returned HTML instead of JSON:', text.substring(0, 200));
                    this.showNotification('Server error: Invalid response format', 'error');
                    return;
                }
                
                const data = JSON.parse(text);
                
                if (data.success) {
                    this.resources = data.resources;
                    this.extractDepartmentOptions(); // Extract departments after loading resources
                } else {
                    this.showNotification(data.message || 'Failed to fetch job resources.', 'error');
                }
            } catch (error) {
                console.error('Error fetching job resources:', error);
                this.showNotification('Error fetching job resources', 'error');
            }
        },   
        
        // Add a new resource
        async addResource() {
            try {
                const formData = new FormData();
                for (const key in this.resourceForm) {
                    if (key !== 'id') { // Don't include ID for new resources
                        formData.append(key, this.resourceForm[key]);
                    }
                }
                
                const response = await fetch('functions/add_job_resource.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('Resource added successfully', 'success');
                    this.fetchJobResources(this.resourceForm.job_id);
                    this.closeResourceModal();
                } else {
                    this.showNotification(data.message || 'Failed to add resource', 'error');
                }
            } catch (error) {
                console.error('Error adding resource:', error);
                this.showNotification('Error adding resource', 'error');
            }
        },
        
        // Update a resource
        async updateResource() {
            try {
                const formData = new FormData();
                for (const key in this.resourceForm) {
                    formData.append(key, this.resourceForm[key]);
                }
                
                const response = await fetch('functions/update_job_resource.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('Resource updated successfully', 'success');
                    this.fetchJobResources(this.resourceForm.job_id);
                    this.closeResourceModal();
                } else {
                    this.showNotification(data.message || 'Failed to update resource', 'error');
                }
            } catch (error) {
                console.error('Error updating resource:', error);
                this.showNotification('Error updating resource', 'error');
            }
        },
        
        // Delete a resource
        // Delete a resource
        async deleteResource() {
            try {
                if (!this.selectedResource || !this.selectedResource.id) {
                    this.showNotification('No resource selected for deletion', 'error');
                    return;
                }
                
                const response = await fetch('functions/delete_job_resource.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: this.selectedResource.id })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('Resource deleted successfully', 'success');
                    this.fetchJobResources(); // Refresh the list
                } else {
                    this.showNotification(data.message || 'Failed to delete resource', 'error');
                }
                
                this.showDeleteModal = false;
                this.selectedResource = null;
            } catch (error) {
                console.error('Error deleting resource:', error);
                this.showNotification('Error deleting resource', 'error');
            }
        },
        
        toggleActionDropdown(resourceId) {
            this.actionDropdown = this.actionDropdown === resourceId ? null : resourceId;
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
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.resourceForm.file = file;
                this.resourceForm.file_name = file.name;
            }
        },
        
        async exportToExcel() {
            try {
                // Prepare data for export
                const exportData = this.jobResources.map(resource => ({
                    'Department': resource.department,
                    'Job Title': this.getJobTitle(resource.job_id),
                    'Financial Budget': resource.financial_budget,
                    'Technology Needed': resource.technology_needed,
                    'Training Required': resource.training_required,
                    'Physical Objects': resource.physical_objects,
                    'Staffing Requirements': resource.staffing_requirements,
                    'Timeline': resource.timeline,
                    'Status': resource.status,
                    'Notes': resource.notes,
                    'Created Date': this.formatDate(resource.created_at)
                }));
                
                const workbook = XLSX.utils.book_new();
                const worksheet = XLSX.utils.json_to_sheet(exportData);
                XLSX.utils.book_append_sheet(workbook, worksheet, "Job Resources");
                XLSX.writeFile(workbook, "job_resources.xlsx");
                this.showNotification('Job resources exported to Excel successfully!', 'success', 'Export Success');
            } catch (error) {
                console.error('Error exporting to Excel:', error);
                this.showNotification('Failed to export job resources to Excel.', 'error', 'Export Error');
            }
        },

        async exportToPDF() {
            try {
                const doc = new window.jspdf.jsPDF();
                const tableColumn = ["Department", "Job Title", "Budget", "Status", "Timeline"];
                const tableRows = [];

                this.jobResources.forEach(resource => {
                    const resourceData = [
                        resource.department,
                        this.getJobTitle(resource.job_id),
                        '₱' + this.formatCurrency(resource.financial_budget),
                        resource.status,
                        resource.timeline || 'N/A'
                    ];
                    tableRows.push(resourceData);
                });

                doc.autoTable({
                    head: [tableColumn],
                    body: tableRows,
                    theme: 'grid',
                    styles: { fontSize: 8 },
                    headStyles: { fillColor: [41, 128, 185] }
                });
                
                // Add additional details on second page
                doc.addPage();
                doc.setFontSize(12);
                doc.text('Detailed Job Resources', 14, 15);
                
                this.jobResources.forEach((resource, index) => {
                    const yPosition = 25 + (index * 60);
                    if (yPosition > 270) {
                        doc.addPage();
                        doc.text('Detailed Job Resources (Continued)', 14, 15);
                    }
                    
                    doc.setFontSize(10);
                    doc.text(`Department: ${resource.department}`, 14, yPosition);
                    doc.text(`Job: ${this.getJobTitle(resource.job_id)}`, 14, yPosition + 5);
                    doc.text(`Budget: ₱${this.formatCurrency(resource.financial_budget)}`, 14, yPosition + 10);
                    doc.text(`Technology: ${resource.technology_needed || 'N/A'}`, 14, yPosition + 15);
                    doc.text(`Training: ${resource.training_required || 'N/A'}`, 14, yPosition + 20);
                    doc.text(`Physical Objects: ${resource.physical_objects || 'N/A'}`, 14, yPosition + 25);
                    doc.text(`Staffing: ${resource.staffing_requirements || 'N/A'}`, 14, yPosition + 30);
                    doc.text(`Timeline: ${resource.timeline || 'N/A'}`, 14, yPosition + 35);
                    doc.text(`Status: ${resource.status}`, 14, yPosition + 40);
                    doc.text(`Notes: ${resource.notes || 'N/A'}`, 14, yPosition + 45);
                });

                doc.save("job_resources.pdf");
                this.showNotification('Job resources exported to PDF successfully!', 'success', 'Export Success');
            } catch (error) {
                console.error('Error exporting to PDF:', error);
                this.showNotification('Failed to export job resources to PDF.', 'error', 'Export Error');
            }
        },

        // Add helper method for currency formatting
        formatCurrency(amount) {
            return parseFloat(amount || 0).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
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