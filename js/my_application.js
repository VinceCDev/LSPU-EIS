const { createApp } = Vue;

        createApp({
            data() {
                return {
                    darkMode: localStorage.getItem('darkMode') === 'true' || 
                     (localStorage.getItem('darkMode') === null && 
                      window.matchMedia('(prefers-color-scheme: dark)').matches),
                    mobileMenuOpen: false,
                    profileDropdownOpen: false,
                    showTutorialButton: true, // Start as false, will be updated after check
                    showWelcomeModal: false, // Start as false
                    currentWelcomeSlide: 0,
                    welcomeSlides: [
                        { title: "Welcome", content: "intro" },
                        { title: "Navigation", content: "navigation" },
                        { title: "Job Search", content: "job_search" },
                        { title: "Profile", content: "profile" }
                    ],
                    unreadNotifications: 0,
                    notifications: [],
                    activeTab: 'applied',
                    loading: true, // Add loading state
                    showJobPanel: false,
                    selectedJob: null,
                    selectedJobDetails: null, // Store fetched job details
                    jobDetailsLoading: false, // Loading state for sidebar
                    showConfirmation: false,
                    confirmationTitle: '',
                    confirmationMessage: '',
                    confirmationAction: null,
                    confirmationIsDestructive: false,
                    mobileProfileDropdownOpen: false,
                    notificationId: 0,
                    appliedJobs: [],
                    savedJobs: [
                        {
                            id: 3,
                            title: 'Data Analyst',
                            company: 'Analytics Inc',
                            location: 'Remote',
                            description: 'We are seeking a Data Analyst to help turn data into information, information into insight and insight into business decisions.',
                            fullDescription: 'We are seeking a Data Analyst to help turn data into information, information into insight and insight into business decisions. You will conduct full lifecycle analysis to include requirements, activities and design. Data analysts will develop analysis and reporting capabilities.',
                            requirements: [
                                'Bachelor\'s degree in Mathematics, Economics, Computer Science or related field',
                                'Strong analytical skills',
                                'Knowledge of SQL and Python',
                                'Experience with data visualization tools'
                            ],
                            qualifications: [
                                'Strong analytical and problem-solving skills',
                                'Proficient in data analysis tools (Excel, Python, R)',
                                'Experience with data modeling and statistical analysis',
                                'Ability to communicate complex findings clearly'
                            ],
                            aboutCompany: 'Analytics Inc provides data solutions to businesses looking to leverage their data for strategic decisions.',
                            savedDate: '2023-05-18',
                            postedDate: '2023-05-05'
                        },
                        {
                            id: 4,
                            title: 'Product Manager',
                            company: 'Product Labs',
                            location: 'Laguna',
                            description: 'We are looking for a Product Manager to join our team and help drive product development from conception to launch.',
                            fullDescription: 'We are looking for a Product Manager to join our team and help drive product development from conception to launch. You will work with cross-functional teams to define product vision, strategy, and roadmap. You will gather and prioritize product and customer requirements.',
                            requirements: [
                                'Bachelor\'s degree in Business, Computer Science or related field',
                                '3+ years of product management experience',
                                'Excellent communication skills',
                                'Strong problem-solving skills'
                            ],
                            qualifications: [
                                'Experience in product management, including roadmap, strategy, and execution',
                                'Strong leadership and communication skills',
                                'Ability to influence cross-functional teams',
                                'Experience with agile methodologies'
                            ],
                            aboutCompany: 'Product Labs is an innovative company focused on building products that solve real-world problems.',
                            savedDate: '2023-05-22',
                            postedDate: '2023-05-15'
                        }
                    ],
                    profile: { name: '' },
                    profilePicData: { file_name: '' },
                    showLogoutModal: false,
                    showApplicationModal: false,
                    applicationStep: 1,
                    applicationPersonal: null,
                    applicationEducation: null,
                    applicationSkills: null,
                    applicationExperience: null,
                    applicationResume: null,
                    highlightJobId: null
                };
            },
            watch: {
                darkMode(val) {
                    localStorage.setItem('darkMode', val.toString());
                    this.applyDarkMode();
                }
            },
            methods: {
                applyDarkMode() {
                    const html = document.documentElement;
                    if (this.darkMode) {
                        html.classList.add('dark');
                    } else {
                        html.classList.remove('dark');
                    }
                },
                shouldHighlightJob(job) {
                    const jobId = job.job_id || job.id;
                    return this.highlightJobId && jobId == this.highlightJobId;
                },
        
                // Also add this method for the highlighting effect
                checkForHighlight() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const jobId = urlParams.get('job_id');
                    const fromNotification = urlParams.get('from_notification');
                    
                    if (jobId && fromNotification) {
                        this.highlightJobId = jobId;
                        
                        // Clean up the URL (remove the parameters without reloading)
                        const cleanUrl = window.location.origin + window.location.pathname;
                        window.history.replaceState({}, document.title, cleanUrl);
                        
                        // Remove highlight after 5 seconds
                        setTimeout(() => {
                            this.highlightJobId = null;
                        }, 5000);
                    }
                },
        
                async fetchUnreadNotifications() {
                    try {
                      const response = await fetch('functions/get_unread_notifications.php');
                      const data = await response.json();
                      if (data.success) {
                        this.unreadNotifications = data.unread_count;
                      }
                    } catch (error) {
                      console.error('Error fetching unread notifications:', error);
                    }
                  },
                formatDate(dateString) {
                    if (!dateString) return '';
                    const options = { year: 'numeric', month: 'short', day: 'numeric' };
                    return new Date(dateString).toLocaleDateString(undefined, options);
                },
                addNotification(type, title, message) {
                    const id = this.notificationId++;
                    this.notifications.push({
                        id,
                        type,
                        title,
                        message
                    });
                    setTimeout(() => {
                        this.removeNotification(id);
                    }, 5000);
                },
                removeNotification(id) {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                },
                async fetchJobDetails(jobId) {
                    this.jobDetailsLoading = true;
                    this.selectedJobDetails = null;
                    try {
                        const res = await fetch(`functions/get_job_details.php?job_id=${jobId}`);
                        const data = await res.json();
                        console.log('API response:', data); // Debug line to see the response structure
                        
                        if (data.success) {
                            // Structure the data to match your template expectations
                            this.selectedJobDetails = {
                                ...data.jobDetails,          // Job details (title, location, etc.)
                                company_name: data.companyDetails.company_name, // Company name at root level
                                companyDetails: data.companyDetails, // All company details as nested object
                                application_status: this.selectedJob.application_status
                            };
                            
                            console.log('Processed job details:', this.selectedJobDetails); // Debug line
                        } else {
                            this.selectedJobDetails = null;
                            this.addNotification('error', 'Failed to fetch job details.', 'Failed to fetch job details.');
                        }
                    } catch (e) {
                        console.error('Error fetching job details:', e);
                        this.selectedJobDetails = null;
                        this.addNotification('error', 'Failed to fetch job details.', 'Failed to fetch job details.');
                    }
                    this.jobDetailsLoading = false;
                },
                messageEmployer(email) {
                    try {
                        // Redirect to messages page with employer email as parameter
                        const messagesUrl = `message.php?compose=true&to=${encodeURIComponent(email)}`;
                        window.location.href = messagesUrl;
                    } catch (error) {
                        console.error('Error redirecting to messages:', error);
                        this.showNotification('Error opening message composer', 'error');
                    }
                },
                closeJobPanel() {
                    this.showJobPanel = false;
                    setTimeout(() => {
                        if (window.jobMap) {
                            window.jobMap.remove();
                            window.jobMap = null;
                        }
                    }, 500);
                },
                viewJob(job) {
                    this.selectedJob = job;
                    this.showJobPanel = true;
                    this.fetchJobDetails(job.job_id || job.id);
                    this.$nextTick(() => {
                        setTimeout(async () => {
                            // Wait for sidebar to be visible in DOM
                            const mapContainer = document.getElementById('job-map');
                            if (!mapContainer) return;
                        if (window.jobMap) {
                            window.jobMap.remove();
                        }
                        window.jobMap = L.map('job-map').setView([13.9644, 121.1631], 13); // Default: San Pablo
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap'
                        }).addTo(window.jobMap);
                        // Geocode location
                            const loc = job.location || (this.selectedJobDetails && this.selectedJobDetails.location);
                            if (loc) {
                            try {
                                const apiKey = 'b25cb94f83684f6aa21cbd86f93c9417';
                                const url = `https://api.geoapify.com/v1/geocode/search?text=${encodeURIComponent(loc)}&apiKey=${apiKey}`;
                                const res = await fetch(url);
                                const data = await res.json();
                                if (data.features && data.features.length > 0) {
                                    const coords = data.features[0].geometry.coordinates;
                                    const latlng = [coords[1], coords[0]];
                                    window.jobMap.setView(latlng, 15);
                                    L.marker(latlng).addTo(window.jobMap).bindPopup(loc).openPopup();
                                }
                            } catch (e) {}
                        }
                        }, 100);
                    });
                },
                showConfirm(title, message, action, isDestructive = true) {
                    this.confirmationTitle = title;
                    this.confirmationMessage = message;
                    this.confirmationAction = action;
                    this.confirmationIsDestructive = isDestructive;
                    this.showConfirmation = true;
                },
                executeConfirmation() {
                    if (this.confirmationAction) {
                        this.confirmationAction();
                    }
                    this.showConfirmation = false;
                    this.confirmationAction = null;
                },
                async removeApplication(jobId) {
                    try {
                        const res = await fetch('functions/delete_application.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `job_id=${jobId}`
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.appliedJobs = this.appliedJobs.filter(job => job.id !== jobId);
                            this.addNotification('success', 'Success', 'Application removed successfully');
                            this.showJobPanel = false;
                        } else {
                            this.addNotification('error', 'Error', data.message || 'Failed to remove application');
                        }
                    } catch (e) {
                        this.addNotification('error', 'Error', 'Failed to remove application');
                    }
                },
                confirmRemoveApplication(jobId) {
                    this.showConfirm(
                        'Remove Application',
                        'Are you sure you want to remove this application? This action cannot be undone.',
                        () => this.removeApplication(jobId),
                        true
                    );
                },
                withdrawApplication(jobId) {
                    this.removeApplication(jobId);
                },
                applyJob(jobId) {
                    this.showConfirm(
                        'Apply for Job',
                        'Are you sure you want to apply for this job? Please review your application before submitting.',
                        () => {
                            // Store the job ID for the application
                            this.selectedJob = this.savedJobs.find(j => j.id === jobId);
                            // Open the application modal
                            this.openApplicationModal(jobId);
                        },
                        false
                    );
                },
                unsaveJob(jobId) {
                    this.showConfirm(
                        'Remove Saved Job',
                        'Are you sure you want to remove this saved job? This action cannot be undone.',
                        async () => {
                            try {
                                const res = await fetch('functions/delete_saved_job.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: `job_id=${jobId}`
                                });
                                const data = await res.json();
                                if (data.success) {
                            this.savedJobs = this.savedJobs.filter(job => job.id !== jobId);
                            this.addNotification('success', 'Success', 'Job removed from saved list');
                                } else {
                                    this.addNotification('error', 'Error', data.message || 'Failed to remove saved job');
                                }
                            } catch (e) {
                                this.addNotification('error', 'Error', 'Failed to remove saved job');
                            }
                        }
                    );
                },
                logout() {
                    window.location.href = 'logout.php';
                },
                async fetchApplicationData() {
                    // Fetch all data using the same fetch files as my_profile.php
                    try {
                        // Personal Details
                        const personalRes = await fetch('functions/fetch_alumni_details.php');
                        const personalData = await personalRes.json();
                        if (personalData.success && personalData.profile) {
                            this.applicationPersonal = personalData.profile;
                            this.profile.name = `${personalData.profile.first_name} ${personalData.profile.last_name}`;
                        } else {
                            this.applicationPersonal = null;
                        }
                        // Education
                        const eduRes = await fetch('functions/fetch_education.php');
                        const eduData = await eduRes.json();
                        if (eduData.success && Array.isArray(eduData.education)) {
                            this.applicationEducation = eduData.education;
                        } else {
                            this.applicationEducation = [];
                        }
                        // Skills
                        const skillRes = await fetch('functions/fetch_skill.php');
                        const skillData = await skillRes.json();
                        if (skillData.success && Array.isArray(skillData.skills)) {
                            this.applicationSkills = skillData.skills;
                        } else {
                            this.applicationSkills = [];
                        }
                        // Work Experience
                        const expRes = await fetch('functions/fetch_experience.php');
                        const expData = await expRes.json();
                        if (expData.success && Array.isArray(expData.experience)) {
                            this.applicationExperience = expData.experience;
                        } else {
                            this.applicationExperience = [];
                        }
                        // Resume
                        const resumeRes = await fetch('functions/fetch_resume.php');
                        const resumeData = await resumeRes.json();
                        if (resumeData.success && resumeData.resume) {
                            this.applicationResume = resumeData.resume;
                        } else {
                            this.applicationResume = null;
                        }
                    } catch (e) {
                        this.showNotification('Failed to fetch application data.', 'error');
                    }
                },
                async fetchEducationDetails() {
                    try {
                        const res = await fetch(`functions/get_education_details.php?user_id=${window.USER_ID}`);
                        const data = await res.json();
                        if (data.success) {
                            this.applicationEducation = data.data;
                        } else {
                            this.showNotification('Failed to fetch education details.', 'error');
                        }
                    } catch (e) {
                        this.showNotification('Failed to fetch education details.', 'error');
                    }
                },
                async fetchSkills() {
                    try {
                        const res = await fetch(`functions/get_skills.php?user_id=${window.USER_ID}`);
                        const data = await res.json();
                        if (data.success) {
                            this.applicationSkills = data.data;
                        } else {
                            this.showNotification('Failed to fetch skills.', 'error');
                        }
                    } catch (e) {
                        this.showNotification('Failed to fetch skills.', 'error');
                    }
                },
                async fetchExperience() {
                    try {
                        const res = await fetch(`functions/get_experience.php?user_id=${window.USER_ID}`);
                        const data = await res.json();
                        if (data.success) {
                            this.applicationExperience = data.data;
                        } else {
                            this.showNotification('Failed to fetch work experience.', 'error');
                        }
                    } catch (e) {
                        this.showNotification('Failed to fetch work experience.', 'error');
                    }
                },
                async fetchResume() {
                    try {
                        const res = await fetch(`functions/get_resume.php?user_id=${window.USER_ID}`);
                        const data = await res.json();
                        if (data.success) {
                            this.applicationResume = data.data;
                        } else {
                            this.showNotification('Failed to fetch resume.', 'error');
                        }
                    } catch (e) {
                        this.showNotification('Failed to fetch resume.', 'error');
                    }
                },
                async submitApplication() {
                    if (!this.applicationResume) {
                        this.addNotification('error', 'Error', 'Please upload a resume before submitting.');
                        return;
                    }
                
                    const formData = new FormData();
                    formData.append('job_id', this.selectedJob.id);
                    
                    if (this.applicationResume.file) {
                        formData.append('resume_file', this.applicationResume.file);
                    } else if (this.applicationResume.file_name) {
                        formData.append('resume_file_name', this.applicationResume.file_name);
                    }
                
                    try {
                        const res = await fetch('functions/insert_application.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await res.json();
                
                        if (data.success) {
                            this.addNotification('success', 'Success', 'Application submitted successfully!');
                            this.closeApplicationModal();
                            
                            // Move job from saved to applied
                            const jobIndex = this.savedJobs.findIndex(j => j.id === this.selectedJob.id);
                            if (jobIndex !== -1) {
                                const appliedJob = {...this.savedJobs[jobIndex]};
                                appliedJob.appliedDate = new Date().toISOString().split('T')[0];
                                this.appliedJobs.unshift(appliedJob);
                                this.savedJobs.splice(jobIndex, 1);
                            }
                            
                            // Reset application data
                            this.applicationStep = 1;
                            this.applicationPersonal = null;
                            this.applicationEducation = null;
                            this.applicationSkills = null;
                            this.applicationExperience = null;
                            this.applicationResume = null;
                        } else {
                            this.addNotification('error', 'Error', data.message || 'You already submit application');
                        }
                    } catch (e) {
                        this.addNotification('error', 'Error', 'Failed to submit application');
                    }
                },
                handleApplicationResumeUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.applicationResume = {
                            ...this.applicationResume,
                            file: file,
                            file_name: file.name,
                            uploaded_at: new Date().toISOString()
                        };
                    }
                },
                openApplicationModal(jobId) {
                    this.showApplicationModal = true;
                    this.applicationStep = 1;
                    this.selectedJob = this.savedJobs.find(j => j.id === jobId) || this.appliedJobs.find(j => j.id === jobId);
                    this.fetchApplicationData(); // Fetch all data on modal open
                },
                closeApplicationModal() {
                    this.showApplicationModal = false;
                    this.applicationStep = 1;
                    this.applicationPersonal = null;
                    this.applicationEducation = null;
                    this.applicationSkills = null;
                    this.applicationExperience = null;
                    this.applicationResume = null;
                },
                nextStep() {
                    this.applicationStep++;
                    if (this.applicationStep === 2) this.fetchPersonalDetails();
                    else if (this.applicationStep === 3) this.fetchEducationDetails();
                    else if (this.applicationStep === 4) this.fetchSkills();
                    else if (this.applicationStep === 5) this.fetchExperience();
                },
                prevStep() {
                    this.applicationStep--;
                    if (this.applicationStep === 1) this.fetchPersonalDetails();
                    else if (this.applicationStep === 2) this.fetchPersonalDetails();
                    else if (this.applicationStep === 3) this.fetchEducationDetails();
                    else if (this.applicationStep === 4) this.fetchSkills();
                    else if (this.applicationStep === 5) this.fetchExperience();
                },
                formatTime(time) {
                    const [hours, minutes] = time.split(':');
                    const date = new Date();
                    date.setHours(hours, minutes, 0, 0);
                    return date.toLocaleTimeString([], { hour: 'numeric', minute: 'numeric' });
                },
                async fetchAppliedJobs() {
                    try {
                        const res = await fetch('functions/get_applied_jobs.php');
                        const data = await res.json();
                        if (Array.isArray(data.appliedJobs)) {
                            this.appliedJobs = data.appliedJobs.map(job => ({
                                id: job.job_id, // for v-for and button
                                job_id: job.job_id, // for fetching details
                                title: job.title,
                                company: job.company_name || job.company || '',
                                location: job.location,
                                description: job.description,
                                fullDescription: job.fullDescription || job.description,
                                requirements: job.requirements ? job.requirements.split('\n') : [],
                                qualifications: job.qualifications ? job.qualifications.split('\n') : [],
                                appliedDate: job.appliedDate || job.applied_at || '',
                                postedDate: job.postedDate || job.created_at || '',
                                application_status: job.application_status || 'Pending',
                                companyDetails: {
                                    company_logo: job.company_logo || '',
                                    company_name: job.company_name || '',
                                    company_location: job.company_location || '',
                                    contact_email: job.contact_email || '',
                                    contact_number: job.contact_number || '',
                                    nature_of_business: job.nature_of_business || '',
                                    industry_type: job.industry_type || '',
                                    accreditation_status: job.accreditation_status || ''
                                }
                            }));
                        }
                    } catch (e) {
                        this.addNotification('error', 'Failed to fetch applied jobs.', 'Failed to fetch applied jobs.');
                    }
                },
                async fetchSavedJobs() {
                    try {
                        const res = await fetch('functions/get_saved_jobs.php');
                        const data = await res.json();
                        if (Array.isArray(data.savedJobs)) {
                            this.savedJobs = data.savedJobs.map(job => ({
                                id: job.job_id,
                                title: job.title,
                                company: job.company_name || job.company || '',
                                location: job.location,
                                description: job.description,
                                fullDescription: job.fullDescription || job.description,
                                requirements: job.requirements ? job.requirements.split('\n') : [],
                                qualifications: job.qualifications ? job.qualifications.split('\n') : [],
                                aboutCompany: job.aboutCompany || '',
                                savedDate: job.savedDate || '',
                                postedDate: job.postedDate || '',
                                companyDetails: job.companyDetails || {},
                            }));
                        }
                    } catch (e) {
                        // fallback: keep hardcoded jobs if fetch fails
                    }
                },
                openTutorial() {
                    this.showWelcomeModal = true;
                    this.currentWelcomeSlide = 0;
                    
                    // Mark tutorial as viewed in session storage
                    sessionStorage.setItem('tutorial_viewed', 'true');
                },
                
                closeWelcomeModal() {
                    console.log('Closing welcome modal');
                    this.showWelcomeModal = false;
                    
                    // Always mark as shown when user closes the modal
                    localStorage.setItem('welcomeModalShown', 'true');
                    console.log('Set welcomeModalShown to true in localStorage');
                    
                    // If user completed the tutorial (reached the end), mark it as completed
                    if (this.currentWelcomeSlide === this.welcomeSlides.length - 1) {
                        console.log('User completed tutorial, marking as completed');
                        this.markTutorialCompleted();
                    }
                },
                async markTutorialCompleted() {
                    try {
                        const response = await fetch('functions/mark_tutorial_completed.php', {
                            method: 'POST'
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            this.showTutorialButton = false;
                            sessionStorage.setItem('tutorial_completed', 'true');
                        }
                    } catch (error) {
                        console.error('Error marking tutorial as completed:', error);
                    }
                }
            },
            mounted() {
                // Set dark mode on initial load
                const storedMode = localStorage.getItem('darkMode');
                if (storedMode !== null) {
                    this.darkMode = storedMode === 'true';
                } else {
                    this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                }
                this.applyDarkMode();
                this.fetchUnreadNotifications();
  
                this.checkForHighlight();
                // Optional: Poll for new notifications every 30 seconds
                this.notificationInterval = setInterval(this.fetchUnreadNotifications, 30000);
                // Fetch applied and saved jobs for the current user
                Promise.all([
                    this.fetchAppliedJobs(),
                    this.fetchSavedJobs()
                ]).finally(() => {
                    this.loading = false;
                });
                fetch('functions/fetch_profile_pic.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.file_name) {
                            this.profilePicData.file_name = data.file_name;
                        } else {
                            this.profilePicData.file_name = '';
                        }
                    });
                fetch('functions/fetch_alumni_details.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.profile) {
                            this.profile.name = `${data.profile.first_name} ${data.profile.last_name}`;
                        }
                    });
            },
            beforeUnmount() {
                // Clean up the interval
                if (this.notificationInterval) {
                  clearInterval(this.notificationInterval);
                }
              }
        }).mount('#app');