const { createApp } = Vue;
createApp({
    data() {
        return {
            sidebarActive: window.innerWidth >= 768,
            companiesDropdownOpen: true,
            alumniDropdownOpen: false,
            profileDropdownOpen: false,
            darkMode: localStorage.getItem('darkMode') === 'true' || 
                     (localStorage.getItem('darkMode') === null && 
                      window.matchMedia('(prefers-color-scheme: dark)').matches),
            showLogoutModal: false,
            isMobile: window.innerWidth < 768,
            notifications: [],
            notificationId: 0,
            actionDropdown: null,
            companies: [
                { id: 1, company_name: 'Tech Corp', company_location: 'Manila', contact_email: 'hr@techcorp.com', industry_type: 'IT', nature_of_business: 'Software', status: 'Approved' },
                { id: 2, company_name: 'Agri Foods', company_location: 'Laguna', contact_email: 'info@agrifoods.com', industry_type: 'Agriculture', nature_of_business: 'Food Production', status: 'Pending' },
                { id: 3, company_name: 'Edu Solutions', company_location: 'Quezon City', contact_email: 'contact@edusol.com', industry_type: 'Education', nature_of_business: 'E-Learning', status: 'Approved' },
                { id: 4, company_name: 'BuildIt', company_location: 'Cebu', contact_email: 'admin@buildit.com', industry_type: 'Construction', nature_of_business: 'Infrastructure', status: 'Pending' },
            ],
            searchQuery: '',
            filters: {
                industry_type: '',
                nature_of_business: '',
                accreditation_status: ''
            },
            itemsPerPage: 5,
            currentPage: 1,
            showCompanyModal: false,
            selectedCompany: null,
            companyForm: {},
            uniqueIndustryTypes: [],
            uniqueNatureOfBusiness: [],
            uniqueAccreditationStatus: ['Approved', 'Pending'],
            showDeleteModal: false,
            companyToDelete: null,
            showViewModal: false,
            viewedCompany: {},
            employers: [],
            profile: {
                profile_pic: '',
                name: '',
                email: ''
            }
        }
    },
    mounted() {
        this.darkMode = localStorage.getItem('darkMode') === 'true' || window.matchMedia('(prefers-color-scheme: dark)').matches;
        this.applyDarkMode();
        window.addEventListener('resize', this.handleResize);
        this.fetchEmployers();
        this.initializeUniqueLists();
        // this.fetchProfile(); // Removed: Fetch profile data
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
        filteredEmployers() {
            let filtered = Array.isArray(this.employers) ? this.employers : [];
            if (this.searchQuery) {
                filtered = filtered.filter(company =>
                    (company.company_name || '').toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (company.company_location || '').toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (company.contact_email || '').toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (company.industry_type || '').toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (company.nature_of_business || '').toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (company.status || '').toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
            if (this.filters.industry_type) {
                filtered = filtered.filter(company => company.industry_type === this.filters.industry_type);
            }
            if (this.filters.nature_of_business) {
                filtered = filtered.filter(company => company.nature_of_business === this.filters.nature_of_business);
            }
            if (this.filters.accreditation_status) {
                filtered = filtered.filter(company => company.status === this.filters.accreditation_status);
            }
            return filtered;
        },
        paginatedCompanies() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredCompanies.slice(start, end);
        },
        totalPages() {
            return Math.ceil((this.filteredEmployers ? this.filteredEmployers.length : 0) / this.itemsPerPage) || 1;
        }
    },
    methods: {
        toggleSidebar() {
            this.sidebarActive = !this.sidebarActive;
            this.companiesDropdownOpen = true;
            this.alumniDropdownOpen = false;
        },
        handleResize() {
            this.isMobile = window.innerWidth < 768;
            if (window.innerWidth >= 768) {
                this.sidebarActive = true;
            } else {
                this.sidebarActive = false;
            }
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
        filterCompanies() {
            this.currentPage = 1;
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
        toggleActionDropdown(companyId) {
            this.actionDropdown = this.actionDropdown === companyId ? null : companyId;
        },
        openAddModal() {
            this.showNotification('Add Company functionality is currently disabled.', 'info');
        },
        viewCompany(company) {
            this.viewedCompany = { ...company };
            this.viewedCompany.documents = company.documents || [
                { name: 'SEC Registration.pdf', url: '#' },
                { name: 'Business Permit.pdf', url: '#' }
            ];
            this.showViewModal = true;
        },
        approveCompany(company) {
            company.status = 'Approved';
            this.showNotification('Company approved!', 'success');
        },
        confirmDelete(company) {
            this.showDeleteModal = true;
            this.companyToDelete = company;
        },
        confirmDeleteCompany() {
            if (this.companyToDelete) {
                this.deleteCompany(this.companyToDelete);
                this.companyToDelete = null;
            }
            this.showDeleteModal = false;
        },
        deleteCompany(company) {
            this.companies = this.companies.filter(c => c.id !== company.id);
            this.showNotification('Company deleted!', 'success');
            this.initializeUniqueLists();
        },
        showNotification(message, type = 'success') {
            const id = this.notificationId++;
            this.notifications.push({ id, type, message });
            setTimeout(() => this.removeNotification(id), 3000);
        },
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        },
        initializeUniqueLists() {
            this.uniqueIndustryTypes = [...new Set(this.companies.map(c => c.industry_type))];
            this.uniqueNatureOfBusiness = [...new Set(this.companies.map(c => c.nature_of_business))];
        },
        handleNavClick() {
            if (this.isMobile) {
                this.sidebarActive = false;
            }
        },
        toggleProfileDropdown() {
            this.profileDropdownOpen = !this.profileDropdownOpen;
            if (this.profileDropdownOpen) {
                document.addEventListener('click', this.handleClickOutsideProfile, true);
            } else {
                document.removeEventListener('click', this.handleClickOutsideProfile, true);
            }
        },
        handleClickOutsideProfile(e) {
            const dropdown = document.querySelector('.absolute.right-0.mt-2.w-48');
            const trigger = document.querySelector('.cursor-pointer.flex.items-center');
            if (dropdown && !dropdown.contains(e.target) && trigger && !trigger.contains(e.target)) {
                this.profileDropdownOpen = false;
                document.removeEventListener('click', this.handleClickOutsideProfile, true);
            }
        },
        confirmLogout() {
            this.showLogoutModal = true;
        },
        logout() {
            window.location.href = 'logout.php';
        },
        exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const columns = [
                'Name of Company',
                'Location',
                'Contact Email',
                'Industry Type',
                'Nature of Business',
                'Status'
            ];
            const rows = this.filteredCompanies.map(company => [
                company.company_name,
                company.company_location,
                company.contact_email,
                company.industry_type,
                company.nature_of_business,
                company.status
            ]);
            doc.autoTable({ head: [columns], body: rows });
            doc.save('companies.pdf');
            this.showNotification('PDF generated successfully!', 'success');
        },
        exportToExcel() {
            const wb = XLSX.utils.book_new();
            const wsData = [
                [
                    'Name of Company',
                    'Location',
                    'Contact Email',
                    'Industry Type',
                    'Nature of Business',
                    'Status'
                ],
                ...this.filteredCompanies.map(company => [
                    company.company_name,
                    company.company_location,
                    company.contact_email,
                    company.industry_type,
                    company.nature_of_business,
                    company.status
                ])
            ];
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            XLSX.utils.book_append_sheet(wb, ws, 'Companies');
            XLSX.writeFile(wb, 'companies.xlsx');
            this.showNotification('Excel file generated successfully!', 'success');
        },
        fetchEmployers() {
            fetch('functions/get_company_pending.php')
                .then(res => res.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        this.employers = data;
                    } catch (e) {
                        this.showNotification('Error parsing employer data. See console for details.', 'error');
                        console.error('JSON parse error:', e, text);
                    }
                })
                .catch(err => {
                    this.showNotification('Fetch error: ' + err, 'error');
                    console.error('Fetch error:', err);
                });
        },
        approveEmployer(employer) {
            fetch('functions/approve_company.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: employer.employer_id || employer.user_id })
            })
            .then(async res => {
                let text = await res.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        this.showNotification('Employer approved!', 'success');
                        this.fetchEmployers();
                    } else {
                        this.showNotification(data.message || 'Failed to approve.', 'error');
                    }
                } catch (e) {
                    this.showNotification('Unexpected server response. See console.', 'error');
                    console.error('Approve error:', text);
                }
            });
        },
        deleteEmployer(employer) {
            if (!confirm('Are you sure you want to delete this employer?')) return;
            fetch('functions/delete_company.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ company_id: employer.user_id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('Employer deleted!', 'success');
                    this.fetchEmployers();
                } else {
                    this.showNotification(data.message || 'Failed to delete.', 'error');
                }
            });
        },
        viewEmployer(employer) {
            // Show modal with employer details
            let docArr = [];
            if (employer.document_file) {
                // Try to extract original filename (after first underscore)
                let original = employer.document_file.split('_').slice(1).join('_') || employer.document_file;
                let docUrl = '';
                if (employer.document_file.includes('/uploads/documents/')) {
                    docUrl = '/lspu_eis' + employer.document_file.substring(employer.document_file.indexOf('/uploads/documents/'));
                } else if (employer.document_file.includes('uploads/documents/')) {
                    docUrl = '/lspu_eis/' + employer.document_file.substring(employer.document_file.indexOf('uploads/documents/'));
                } else {
                    docUrl = '/lspu_eis/uploads/documents/' + employer.document_file.replace(/^.*[\\\/]/, '');
                }
                docArr = [{
                    name: original,
                    url: docUrl
                }];
            }
            let logoUrl = '';
            if (employer.company_logo) {
                if (employer.company_logo.includes('/uploads/logos/')) {
                    logoUrl = '/lspu_eis' + employer.company_logo.substring(employer.company_logo.indexOf('/uploads/logos/'));
                } else if (employer.company_logo.includes('uploads/logos/')) {
                    logoUrl = '/lspu_eis/' + employer.company_logo.substring(employer.company_logo.indexOf('uploads/logos/'));
                } else {
                    logoUrl = '/lspu_eis/uploads/logos/' + employer.company_logo.replace(/^.*[\\\/]/, '');
                }
            } else {
                logoUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(employer.company_name || 'Company');
            }
            this.viewedEmployer = {
                ...employer,
                logo: logoUrl,
                documents: docArr
            };
            this.showViewModal = true;
        }
    }
}).mount('#app');