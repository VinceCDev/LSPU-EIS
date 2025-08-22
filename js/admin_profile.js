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
                notifications: [],
                notificationId: 0,
                showEditModal: false,
                modalMode: 'profile',
                editingExperienceIndex: null,
                profile: {
                    profile_pic: '',
                    name: '',
                    email: '',
                    phone: '',
                    address: '',
                    position: '',
                    department: ''
                },
                editForm: {
                    first_name: '',
                    middle_name: '',
                    last_name: '',
                    contact: '',
                    position: '',
                    department: '',
                    address: ''
                },
                addressSuggestions: [],
                showAddressSuggestions: false,
                showPhotoModal: false,
                newPhotoPreview: null,
                newPhotoFile: null,
                showDeleteModal: false,
                experienceToDelete: null
            }
        },
        mounted() {
            this.applyDarkMode();
            window.addEventListener('resize', this.handleResize);
            // Fetch admin details
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
            },
            handleResize() {
                this.isMobile = window.innerWidth < 768;
                if (window.innerWidth >= 768) {
                    this.sidebarActive = true;
                } else {
                    this.sidebarActive = false;
                }
            },
            formatDate(dateString) {
                if (!dateString) return 'Present';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
            },
            showNotification(message, type = 'success') {
                const id = this.notificationId++;
                this.notifications.push({ id, type, message });
                setTimeout(() => this.removeNotification(id), 3000);
            },
            removeNotification(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
            },
            editProfile() {
                this.modalMode = 'profile';
                const [first_name = '', middle_name = '', last_name = ''] = (this.profile.name || '').split(' ');
                this.editForm = {
                    first_name,
                    middle_name,
                    last_name,
                    contact: this.profile.phone || '',
                    position: this.profile.position || '',
                    department: this.profile.department || '',
                    address: this.profile.address || ''
                };
                this.showEditModal = true;
            },
            async fetchAddressSuggestions() {
                const val = this.editForm.address;
                if (!val || val.length < 3) {
                    this.addressSuggestions = [];
                    this.showAddressSuggestions = false;
                    return;
                }
                const apiKey = 'b25cb94f83684f6aa21cbd86f93c9417';
                const url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(val)}&limit=5&apiKey=${apiKey}`;
                try {
                    const res = await fetch(url);
                    const data = await res.json();
                    this.addressSuggestions = data.features.map(f => f.properties.formatted);
                    this.showAddressSuggestions = true;
                } catch (e) {
                    this.addressSuggestions = [];
                    this.showAddressSuggestions = false;
                }
            },
            selectAddressSuggestion(suggestion) {
                this.editForm.address = suggestion;
                this.addressSuggestions = [];
                this.showAddressSuggestions = false;
            },
            hideAddressSuggestions() {
                setTimeout(() => { this.showAddressSuggestions = false; }, 150);
            },
            addExperience() {
                this.modalMode = 'experience';
                this.editingExperienceIndex = null;
                this.editForm = {
                    title: '',
                    company: '',
                    start_date: '',
                    end_date: '',
                    current: false,
                    description: ''
                };
                this.showEditModal = true;
            },
            editExperience(index) {
                this.modalMode = 'experience';
                this.editingExperienceIndex = index;
                this.editForm = { ...this.profile.experiences[index] };
                this.showEditModal = true;
            },
            deleteExperience(index) {
                this.experienceToDelete = this.profile.experiences[index];
                this.showDeleteModal = true;
            },
            cancelDelete() {
                this.showDeleteModal = false;
                this.experienceToDelete = null;
            },
            confirmDeleteExperience() {
                if (this.experienceToDelete) {
                    this.profile.experiences.splice(this.profile.experiences.indexOf(this.experienceToDelete), 1);
                    this.showNotification('Experience deleted successfully!', 'success');
                    this.showDeleteModal = false;
                    this.experienceToDelete = null;
                }
            },
            saveProfile() {
                const formData = new FormData();
                formData.append('first_name', this.editForm.first_name);
                formData.append('middle_name', this.editForm.middle_name);
                formData.append('last_name', this.editForm.last_name);
                formData.append('contact', this.editForm.contact);
                formData.append('position', this.editForm.position);
                formData.append('department', this.editForm.department);
                formData.append('address', this.editForm.address);
                fetch('functions/update_admin_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification('Profile updated successfully!', 'success');
                        this.closeEditModal();
                        fetch('functions/fetch_admin_details.php')
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.profile) {
                                    this.profile = data.profile;
                                }
                            });
                    } else {
                        this.showNotification(data.message || 'Failed to update profile.', 'error');
                    }
                })
                .catch(() => {
                    this.showNotification('Failed to update profile.', 'error');
                });
            },
            saveExperience() {
                if (this.editingExperienceIndex !== null) {
                    // Update existing experience
                    this.profile.experiences[this.editingExperienceIndex] = { ...this.editForm };
                    this.showNotification('Experience updated successfully!', 'success');
                } else {
                    // Add new experience
                    this.profile.experiences.push({ ...this.editForm });
                    this.showNotification('Experience added successfully!', 'success');
                }
                this.closeEditModal();
            },
            closeEditModal() {
                this.showEditModal = false;
                this.editingExperienceIndex = null;
                this.editForm = {};
            },
            openPhotoUpload() {
                this.showPhotoModal = true;
                this.newPhotoPreview = null;
                this.newPhotoFile = null;
            },
            handlePhotoUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.newPhotoPreview = URL.createObjectURL(file);
                    this.newPhotoFile = file;
                }
            },
            savePhoto() {
                if (!this.newPhotoFile) return;
                const formData = new FormData();
                formData.append('profile_pic', this.newPhotoFile);
                fetch('functions/update_admin_profile_pic.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.showNotification('Profile photo updated successfully!', 'success');
                        this.closePhotoModal();
                        fetch('functions/fetch_admin_details.php')
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.profile) {
                                    this.profile = data.profile;
                                }
                            });
                    } else {
                        this.showNotification(data.message || 'Failed to update photo.', 'error');
                    }
                })
                .catch(() => {
                    this.showNotification('Failed to update photo.', 'error');
                });
            },
            closePhotoModal() {
                this.showPhotoModal = false;
                this.newPhotoPreview = null;
                this.newPhotoFile = null;
            }
        }
    }).mount('#app');