const { createApp, reactive, ref } = Vue;
createApp({
    data() {
        return {
            form: {
                email: '',
                secondary_email: '',
                password: '',
                current_password: '',
                first_name: '',
                middle_name: '',
                last_name: '',
                birthdate: '',
                contact: '',
                gender: '',
                civil_status: '',
                city: '',
                province: '',
                year_graduated: '',
                college: '',
                course: '',
                verification_documents: null
            },
            passwordValid: {
                length: false,
                upper: false,
                lower: false,
                number: false,
                special: false
            },
            loading: false,
            agreeToDisclaimer: false, // Add this line
            showDisclaimerModal: false, // Add this line to control modal visibility
            message: '',
            success: false,
            provinces: [],
            cities: [],
            fileError: '',
            allowedTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxSize: 5 * 1024 * 1024
        }
    },
    mounted() {
        this.fetchProvinces();
    },
    methods: {
        async fetchProvinces() {
            // Use a public PSGC API or static JSON fallback
            try {
                const res = await fetch('https://psgc.gitlab.io/api/provinces/');
                const data = await res.json();
                this.provinces = data.map(p => ({ name: p.name, code: p.code }));
            } catch (e) {
                this.provinces = [{ name: 'Laguna', code: '0434' }]; // fallback
            }
        },
        validateFile(event) {
            const fileInput = event.target;
            const files = fileInput.files;
            this.fileError = '';
            
            // Check if any file is selected
            if (!files || files.length === 0) {
                this.form.verification_documents = null;
                return true;
            }
            
            // Check each selected file
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Validate file type
                if (!this.allowedTypes.includes(file.type)) {
                    this.fileError = 'Invalid file type. Only JPG, PNG, and PDF files are allowed.';
                    fileInput.value = '';
                    this.form.verification_documents = null;
                    return false;
                }
                
                // Validate file size
                if (file.size > this.maxSize) {
                    this.fileError = `File "${file.name}" is too large. Maximum size is 5MB.`;
                    fileInput.value = '';
                    this.form.verification_documents = null;
                    return false;
                }
                
                // Validate file name (prevent path traversal attacks)
                if (file.name.includes('..') || file.name.includes('/') || file.name.includes('\\')) {
                    this.fileError = 'Invalid file name.';
                    fileInput.value = '';
                    this.form.verification_documents = null;
                    return false;
                }
            }
            
            // If all validations pass
            this.form.verification_documents = files[0]; // Store first file only
            return true;
        },
        async fetchCities() {
            this.cities = [];
            if (!this.form.province) return;
            try {
                // Find province code
                const province = this.provinces.find(p => p.name === this.form.province);
                if (!province) return;
                const res = await fetch(`https://psgc.gitlab.io/api/provinces/${province.code}/cities-municipalities/`);
                const data = await res.json();
                this.cities = data.map(c => ({ name: c.name, code: c.code }));
            } catch (e) {
                if (this.form.province === 'Laguna') {
                    this.cities = [
                        { name: 'San Pablo City', code: '043404' },
                        { name: 'Calamba City', code: '043405' },
                        { name: 'Santa Cruz', code: '043406' },
                        // ... add more as needed
                    ];
                }
            }
        },
        handleFileUpload(e) {
            if (this.validateFile(e)) {
                // File is valid, proceed with your existing logic
                this.form.verification_documents = e.target.files[0];
            }
        },
        validatePassword() {
            const p = this.form.password;
            this.passwordValid.length = p.length >= 8;
            this.passwordValid.upper = /[A-Z]/.test(p);
            this.passwordValid.lower = /[a-z]/.test(p);
            this.passwordValid.number = /[0-9]/.test(p);
            this.passwordValid.special = /[!@#$%^&*(),.?":{}|<>]/.test(p);
        },
        async submitForm() {
            this.message = '';
            this.success = false;
            this.loading = true;
            // Client-side validation
            if (!this.form.email || !this.form.password || !this.form.current_password || !this.form.first_name || !this.form.last_name || !this.form.birthdate || !this.form.contact || !this.form.gender || !this.form.civil_status || !this.form.city || !this.form.province || !this.form.year_graduated || !this.form.college || !this.form.course || !this.form.verification_documents) {
                this.message = 'Please fill in all required fields.';
                this.success = false;
                this.loading = false;
                return;
            }
            if (this.form.password !== this.form.current_password) {
                this.message = 'Passwords do not match.';
                this.success = false;
                this.loading = false;
                return;
            }
            if (!this.passwordValid.length || !this.passwordValid.upper || !this.passwordValid.lower || !this.passwordValid.number || !this.passwordValid.special) {
                this.message = 'Password does not meet requirements.';
                this.success = false;
                this.loading = false;
                return;
            }
            if (!this.form.verification_documents) {
                this.message = 'Please upload a valid verification document.';
                this.success = false;
                this.loading = false;
                return;
            }
            // Prepare form data
            const formData = new FormData();
            for (const key in this.form) {
                if (key === 'verification_documents') {
                    formData.append(key, this.form[key]);
                } else {
                    formData.append(key, this.form[key]);
                }
            }
            try {
                const response = await fetch('functions/register.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                this.message = data.message;
                this.success = data.success;
                if (data.success) {
                    this.form = {
                        email: '', secondary_email: '', password: '', current_password: '', first_name: '', middle_name: '', last_name: '', birthdate: '', contact: '', gender: '', civil_status: '', city: '', province: '', year_graduated: '', college: '', course: '', verification_documents: null
                    };
                    this.passwordValid = { length: false, upper: false, lower: false, number: false, special: false };
                }
            } catch (e) {
                this.message = 'An error occurred. Please try again.';
                this.success = false;
            }
            this.loading = false;
        },
        openDisclaimerModal() {
            this.showDisclaimerModal = true;
        },
        closeDisclaimerModal() {
            this.showDisclaimerModal = false;
        }
    }
}).mount('#signupApp');