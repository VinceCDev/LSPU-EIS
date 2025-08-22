const { createApp } = Vue;
    createApp({
        data() {
            return {
                accounts: [],
                search: '',
                showViewModal: false,
                viewedAccount: {},
                profile: {
                    profile_pic: '',
                    name: '',
                },
                profileDropdownOpen: false,
                sidebarActive: window.innerWidth >= 768,
                companiesDropdownOpen: false,
                alumniDropdownOpen: false,
                darkMode: localStorage.getItem('darkMode') === 'true' || 
                     (localStorage.getItem('darkMode') === null && 
                      window.matchMedia('(prefers-color-scheme: dark)').matches),
                showLogoutModal: false,
                isMobile: window.innerWidth < 768,
                notifications: [],
                notificationId: 0,
                showFilterDropdown: false,
                filterRole: '',
                filterStatus: '',
                currentPage: 1,
                pageSize: 5,
                actionDropdown: null, // For action dropdown
                showAdminModal: false,
                adminModalMode: 'add', // 'add' or 'edit'
                adminForm: {
                    user_id: null,
                    user_role: '',
                    first_name: '',
                    middle_name: '',
                    last_name: '',
                    company_name: '',
                    industry_type: '',
                    email: '',
                    status: 'Active',
                    profile_pic: null,
                },
                adminPhotoPreview: null,
                showDeleteModal: false,
                roleToAdd: '',
                roleSelectionStep: false,
            };
        },
        computed: {
            filteredAccounts() {
                let filtered = this.accounts;
                if (this.filterRole) {
                    filtered = filtered.filter(a => a.user_role === this.filterRole);
                }
                if (this.filterStatus) {
                    filtered = filtered.filter(a => a.status === this.filterStatus);
                }
                if (this.search) {
                    const s = this.search.toLowerCase();
                    filtered = filtered.filter(a =>
                        a.name.toLowerCase().includes(s) ||
                        a.email.toLowerCase().includes(s) ||
                        (a.user_role && a.user_role.toLowerCase().includes(s))
                    );
                }
                return filtered;
            },
            paginatedAccounts() {
                const start = (this.currentPage - 1) * this.pageSize;
                return this.filteredAccounts.slice(start, start + this.pageSize);
            },
            totalPages() {
                return Math.ceil(this.filteredAccounts.length / this.pageSize) || 1;
            },
            startItem() {
                return (this.currentPage - 1) * this.pageSize;
            },
            endItem() {
                return Math.min(this.startItem + this.pageSize, this.filteredAccounts.length);
            }
        },
        mounted() {
            this.applyDarkMode();
            window.addEventListener('resize', this.handleResize);
            fetch('functions/fetch_all_accounts.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.accounts) {
                        this.accounts = data.accounts;
                    }
                });
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
            prevPage() {
                if (this.currentPage > 1) this.currentPage--;
            },
            nextPage() {
                if (this.currentPage < this.totalPages) this.currentPage++;
            },
            goToPage(page) {
                this.currentPage = page;
            },
            addAdmin() {
                this.adminModalMode = 'add';
                this.roleToAdd = '';
                this.roleSelectionStep = true;
                this.adminForm = {
                    user_id: null,
                    user_role: '',
                    first_name: '',
                    middle_name: '',
                    last_name: '',
                    company_name: '',
                    industry_type: '',
                    email: '',
                    status: 'Active',
                    profile_pic: null,
                };
                this.adminPhotoPreview = null;
                this.showAdminModal = true;
            },
            openEditModal(account) {
                this.adminModalMode = 'edit';
                if (account.user_role === 'employer') {
                    this.adminForm = {
                        user_id: account.user_id,
                        user_role: account.user_role,
                        company_name: account.company_name || '',
                        industry_type: account.industry_type || '',
                        email: account.email || '',
                        status: account.status || 'Active',
                        profile_pic: null,
                    };
                } else {
                    this.adminForm = {
                        user_id: account.user_id,
                        user_role: account.user_role,
                        first_name: account.first_name || '',
                        middle_name: account.middle_name || '',
                        last_name: account.last_name || '',
                        email: account.email || '',
                        status: account.status || 'Active',
                        profile_pic: null,
                    };
                }
                this.adminPhotoPreview = null;
                this.showAdminModal = true;
            },
            closeAdminModal() {
                this.showAdminModal = false;
                this.adminModalMode = 'add';
                this.roleToAdd = '';
                this.roleSelectionStep = false;
                this.adminForm = {
                    user_id: null,
                    user_role: '',
                    first_name: '',
                    middle_name: '',
                    last_name: '',
                    company_name: '',
                    industry_type: '',
                    email: '',
                    status: 'Active',
                    profile_pic: null,
                };
                this.adminPhotoPreview = null;
            },
            addAdminSubmit() {
                this.submitAccountForm('add');
            },
            updateAdmin() {
                this.submitAccountForm('edit');
            },
            submitAccountForm(mode) {
                const formData = new FormData();
                if (mode === 'edit') {
                    formData.append('user_id', this.adminForm.user_id);
                    formData.append('user_role', this.adminForm.user_role);
                } else {
                    formData.append('user_role', this.adminForm.user_role);
                }
                formData.append('first_name', this.adminForm.first_name || '');
                formData.append('middle_name', this.adminForm.middle_name || '');
                formData.append('last_name', this.adminForm.last_name || '');
                formData.append('company_name', this.adminForm.company_name || '');
                formData.append('industry_type', this.adminForm.industry_type || '');
                formData.append('email', this.adminForm.email || '');
                formData.append('status', this.adminForm.status || 'Active');
                if (this.adminForm.profile_pic) {
                    formData.append('profile_pic', this.adminForm.profile_pic);
                }
                let url = '';
                if (mode === 'add') {
                    url = 'functions/insert_account.php';
                } else {
                    url = 'functions/update_accounts.php';
                }
                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification(mode === 'add' ? 'Account created successfully!' : 'Account updated successfully!', 'success');
                        this.closeAdminModal();
                        this.fetchAccounts();
                    } else {
                        this.showNotification(data.message || (mode === 'add' ? 'Failed to create account.' : 'Failed to update account.'), 'error');
                    }
                })
                .catch(error => {
                    this.showNotification('Error: ' + error.message, 'error');
                });
            },
            confirmDelete(account) {
                this.viewedAccount = account; // Set for view modal
                this.showDeleteModal = true;
            },
            deleteAdmin() {
                if (!this.viewedAccount.user_id) return;
                fetch('functions/delete_account.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `user_id=${this.viewedAccount.user_id}&user_role=${this.viewedAccount.user_role}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification('Account deleted successfully!', 'success');
                        this.showDeleteModal = false;
                        this.fetchAccounts();
                    } else {
                        this.showNotification(data.message || 'Failed to delete account.', 'error');
                    }
                })
                .catch(error => {
                    this.showNotification('Error deleting account: ' + error.message, 'error');
                });
            },
            viewAccount(account) {
                this.viewedAccount = account;
                this.showViewModal = true;
            },
            toggleActionDropdown(userId) {
                this.actionDropdown = userId;
            },
            handleResize() {
                this.isMobile = window.innerWidth < 768;
                if (window.innerWidth >= 768) {
                    this.sidebarActive = true;
                } else {
                    this.sidebarActive = false;
                }
            },
            fetchAccounts() {
                fetch('functions/fetch_all_accounts.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.accounts) {
                            this.accounts = data.accounts;
                        }
                    });
            },
            handleAdminPhotoUpload(event) {
                this.adminForm.profile_pic = event.target.files[0];
                if (this.adminForm.profile_pic) {
                    this.adminPhotoPreview = URL.createObjectURL(this.adminForm.profile_pic);
                } else {
                    this.adminPhotoPreview = null;
                }
            },
            getProfilePic(account) {
                if (!account.profile_pic) return 'images/logo.png';
                if (account.user_role === 'alumni') {
                    if (account.profile_pic.startsWith('uploads/')) {
                        return account.profile_pic;
                    } else {
                        return 'uploads/profile_picture/' + account.profile_pic;
                    }
                }
                if (account.user_role === 'employer') {
                    if (account.profile_pic.startsWith('uploads/')) {
                        return account.profile_pic;
                    } else {
                        return 'uploads/logos/' + account.profile_pic;
                    }
                }
                return account.profile_pic;
            },
            showNotification(message, type = 'success') {
                const id = this.notificationId++;
                this.notifications.push({ id, type, message });
                setTimeout(() => this.removeNotification(id), 3000);
            },
            removeNotification(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
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
            confirmLogout() {
                this.showLogoutModal = true;
            },
            logout() {
                window.location.href = 'logout.php';
            }
        }
    }).mount('#app');