window.addEventListener('DOMContentLoaded', function() {
    const { createApp, ref, computed, watch } = Vue;
    createApp({
        setup() {
            const email = ref('');
            const password = ref('');
            const confirmPassword = ref('');
            const companyName = ref('');
            const companyLogo = ref(null);
            const companyLocation = ref('');
            const contactEmail = ref('');
            const contactNumber = ref('');
            const industryType = ref('');
            const natureOfBusiness = ref('');
            const tin = ref('');
            const dateEstablished = ref('');
            const companyType = ref('');
            const accreditationStatus = ref('');
            const documentFile = ref(null);
            const addressSuggestions = ref([]);
            const isLoading = ref(false);
            const message = ref('');
            const messageType = ref('');
            const logoError = ref('');
            const documentError = ref('');
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            // Password policy
            const passwordValid = computed(() => {
                return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(password.value);
            });
            const passwordsMatch = computed(() => password.value === confirmPassword.value);

            // Address autocomplete (Geoapify)
            watch(companyLocation, async (val) => {
                if (val.length < 3) { addressSuggestions.value = []; return; }
                const apiKey = 'b25cb94f83684f6aa21cbd86f93c9417'; // Geoapify API key
                const url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(val)}&limit=5&apiKey=${apiKey}`;
                try {
                    const res = await fetch(url);
                    const data = await res.json();
                    addressSuggestions.value = data.features.map(f => f.properties.formatted);
                } catch (e) { addressSuggestions.value = []; }
            });

            const selectSuggestion = (suggestion) => {
                companyLocation.value = suggestion;
                addressSuggestions.value = [];
            };

            const showDisclaimerModal = ref(false);

            const openDisclaimerModal = () => {
                showDisclaimerModal.value = true;
            };
            
            const closeDisclaimerModal = () => {
                showDisclaimerModal.value = false;
            };

            // File validation function
            const validateFile = (event, fileType) => {
                const fileInput = event.target;
                const files = fileInput.files;
                
                // Reset the appropriate error
                if (fileType === 'logo') {
                    logoError.value = '';
                } else {
                    documentError.value = '';
                }
                
                // Check if any file is selected
                if (!files || files.length === 0) {
                    if (fileType === 'logo') {
                        companyLogo.value = null;
                    } else {
                        documentFile.value = null;
                    }
                    return true;
                }
                
                const file = files[0];
                
                // Validate file type
                if (!allowedTypes.includes(file.type)) {
                    const errorMsg = 'Invalid file type. Only JPG, PNG, and PDF files are allowed.';
                    if (fileType === 'logo') {
                        logoError.value = errorMsg;
                    } else {
                        documentError.value = errorMsg;
                    }
                    fileInput.value = '';
                    if (fileType === 'logo') {
                        companyLogo.value = null;
                    } else {
                        documentFile.value = null;
                    }
                    return false;
                }
                
                // Validate file size
                if (file.size > maxSize) {
                    const errorMsg = `File "${file.name}" is too large. Maximum size is 5MB.`;
                    if (fileType === 'logo') {
                        logoError.value = errorMsg;
                    } else {
                        documentError.value = errorMsg;
                    }
                    fileInput.value = '';
                    if (fileType === 'logo') {
                        companyLogo.value = null;
                    } else {
                        documentFile.value = null;
                    }
                    return false;
                }
                
                // Validate file name (prevent path traversal attacks)
                if (file.name.includes('..') || file.name.includes('/') || file.name.includes('\\')) {
                    const errorMsg = 'Invalid file name.';
                    if (fileType === 'logo') {
                        logoError.value = errorMsg;
                    } else {
                        documentError.value = errorMsg;
                    }
                    fileInput.value = '';
                    if (fileType === 'logo') {
                        companyLogo.value = null;
                    } else {
                        documentFile.value = null;
                    }
                    return false;
                }
                
                // If all validations pass
                if (fileType === 'logo') {
                    companyLogo.value = file;
                } else {
                    documentFile.value = file;
                }
                return true;
            };

            // File handlers
            const handleLogo = (e) => { 
                validateFile(e, 'logo');
            };

            const handleDocument = (e) => { 
                validateFile(e, 'document');
            };

            // Submit handler
            const submitForm = async (e) => {
                e.preventDefault();
                message.value = '';
                messageType.value = '';

                // In your submitForm function, update the file validation part:
                // Add file validation
                if (documentFile.value) {
                    const tempEvent = { target: { files: [documentFile.value] } };
                    if (!validateFile(tempEvent, 'document')) {
                        message.value = documentError.value || 'Invalid document file.';
                        messageType.value = 'error';
                        return;
                    }
                } else {
                    message.value = 'Please upload a required document.';
                    messageType.value = 'error';
                    return;
                }
                
                if (companyLogo.value) {
                    const tempEvent = { target: { files: [companyLogo.value] } };
                    if (!validateFile(tempEvent, 'logo')) {
                        message.value = logoError.value || 'Invalid logo file.';
                        messageType.value = 'error';
                        return;
                    }
                }
                
                if (!passwordValid.value) {
                    message.value = 'Password does not meet the policy.';
                    messageType.value = 'error';
                    return;
                }
                
                if (!passwordsMatch.value) {
                    message.value = 'Passwords do not match.';
                    messageType.value = 'error';
                    return;
                }
                
                isLoading.value = true;
                const formData = new FormData();
                formData.append('email', email.value);
                formData.append('password', password.value);
                formData.append('current_password', confirmPassword.value);
                formData.append('company_name', companyName.value);
                if (companyLogo.value) formData.append('company_logo', companyLogo.value);
                formData.append('company_location', companyLocation.value);
                formData.append('contact_email', contactEmail.value);
                formData.append('contact_number', contactNumber.value);
                formData.append('industry_type', industryType.value);
                formData.append('nature_of_business', natureOfBusiness.value);
                formData.append('tin', tin.value);
                formData.append('date_established', dateEstablished.value);
                formData.append('company_type', companyType.value);
                formData.append('accreditation_status', accreditationStatus.value);
                if (documentFile.value) formData.append('document_file', documentFile.value);
                
                try {
                    const res = await fetch('functions/employer_registration.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'include'
                    });
                    const data = await res.json();
                    message.value = data.message;
                    messageType.value = data.success ? 'success' : 'error';
                } catch (err) {
                    message.value = 'Error submitting form. Please try again.';
                    messageType.value = 'error';
                }
                
                isLoading.value = false;
            };

            return {
                email, password, confirmPassword, companyName, companyLogo, companyLocation, 
                contactEmail, contactNumber, industryType, natureOfBusiness, tin, dateEstablished, 
                companyType, accreditationStatus, documentFile, addressSuggestions, isLoading, 
                message, messageType, passwordValid, passwordsMatch, handleLogo, handleDocument, 
                selectSuggestion, submitForm, openDisclaimerModal, closeDisclaimerModal, 
                showDisclaimerModal,  logoError, // Add this
                documentError,
                validateLogoFile: handleLogo,
                validateDocumentFile: handleDocument
            };
        }
    }).mount('form');
});