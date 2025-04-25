const {
    createApp
} = Vue;

createApp({
    data() {
        return {
            loading: true,
            activeTab: 'applied',
            appliedJobs: [],
            savedJobs: [],
            selectedJob: null,
            jobModal: null
        }
    },
    methods: {
        async fetchJobs() {
            try {
                this.loading = true;
                // Simulate API call with timeout
                await new Promise(resolve => setTimeout(resolve, 800));

                // Mock data for applied jobs
                this.appliedJobs = [{
                        id: 1,
                        title: 'Frontend Developer',
                        company: 'Tech Solutions Inc.',
                        location: 'Manila, Philippines (Remote)',
                        description: 'We are looking for a skilled Frontend Developer to join our team. You will be responsible for building user interfaces and implementing features for our web applications.',
                        fullDescription: 'We are looking for a skilled Frontend Developer to join our growing team. In this role, you will collaborate with designers and backend developers to create responsive, user-friendly web applications using modern JavaScript frameworks. The ideal candidate has 3+ years of experience with Vue.js or React and a strong understanding of responsive design principles.',
                        postedDate: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000), // 5 days ago
                        appliedDate: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000), // 2 days ago
                        status: 'Under Review',
                        requirements: [
                            '3+ years experience with Vue.js or React',
                            'Strong understanding of HTML5, CSS3, and JavaScript ES6+',
                            'Experience with responsive design',
                            'Familiarity with RESTful APIs',
                            'Bachelor\'s degree in Computer Science or related field'
                        ],
                        aboutCompany: 'Tech Solutions Inc. is a leading software development company specializing in enterprise solutions for businesses worldwide. Founded in 2010, we have grown to over 200 employees across 5 countries.'
                    },
                    {
                        id: 2,
                        title: 'Backend Engineer',
                        company: 'Data Systems Co.',
                        location: 'Laguna, Philippines',
                        description: 'Join our backend team to develop and maintain our server infrastructure. Experience with Node.js and databases required.',
                        fullDescription: 'We are seeking a Backend Engineer to help build and scale our server infrastructure. You will work closely with our product team to design and implement APIs, optimize database queries, and ensure system reliability. The ideal candidate has experience with Node.js, MongoDB, and cloud services like AWS or Azure.',
                        postedDate: new Date(Date.now() - 10 * 24 * 60 * 60 * 1000), // 10 days ago
                        appliedDate: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000), // 7 days ago
                        status: 'Interview Scheduled',
                        requirements: [
                            'Proficiency in Node.js and Express',
                            'Experience with MongoDB or other NoSQL databases',
                            'Knowledge of REST API design',
                            'Understanding of authentication protocols',
                            'Experience with cloud platforms preferred'
                        ],
                        aboutCompany: 'Data Systems Co. provides data management solutions for financial institutions. Our platform processes millions of transactions daily with 99.99% uptime.'
                    }
                ];

                // Mock data for saved jobs
                this.savedJobs = [{
                        id: 3,
                        title: 'UI/UX Designer',
                        company: 'Creative Minds Agency',
                        location: 'Cavite, Philippines (Hybrid)',
                        description: 'We need a talented UI/UX designer to create beautiful and functional interfaces for our clients. Portfolio required.',
                        fullDescription: 'Creative Minds Agency is looking for a UI/UX Designer to join our creative team. You will be responsible for designing user interfaces, creating prototypes, and conducting user research. The ideal candidate has a strong portfolio showcasing mobile and web design projects and experience with tools like Figma or Adobe XD.',
                        postedDate: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000), // 3 days ago
                        savedDate: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000), // 1 day ago,
                        requirements: [
                            '2+ years UI/UX design experience',
                            'Proficiency in Figma or Adobe XD',
                            'Understanding of user-centered design',
                            'Ability to create wireframes and prototypes',
                            'Strong visual design skills'
                        ],
                        aboutCompany: 'Creative Minds Agency is a boutique design studio specializing in digital products. We work with startups and established brands to create memorable user experiences.'
                    },
                    {
                        id: 4,
                        title: 'Full Stack Developer',
                        company: 'Innovate Tech',
                        location: 'Batangas, Philippines',
                        description: 'Looking for a full stack developer proficient in both frontend and backend technologies to work on exciting new projects.',
                        fullDescription: 'Innovate Tech is seeking a Full Stack Developer to join our product development team. You will work across our entire stack, from frontend interfaces to backend services and database design. The ideal candidate is comfortable with both client-side and server-side programming and enjoys solving complex problems.',
                        postedDate: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000), // 7 days ago
                        savedDate: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000), // 2 days ago,
                        requirements: [
                            'Experience with React and Node.js',
                            'Knowledge of SQL databases',
                            'Understanding of full SDLC',
                            'Ability to write clean, maintainable code',
                            'Strong problem-solving skills'
                        ],
                        aboutCompany: 'Innovate Tech builds custom software solutions for businesses of all sizes. Our team of 50+ engineers delivers high-quality products on time and on budget.'
                    }
                ];
            } catch (error) {
                console.error('Error fetching jobs:', error);
            } finally {
                this.loading = false;
            }
        },
        formatDate(date) {
            const now = new Date();
            const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));

            if (diffInDays === 0) return 'today';
            if (diffInDays === 1) return 'yesterday';
            if (diffInDays < 7) return `${diffInDays} days ago`;
            if (diffInDays < 30) return `${Math.floor(diffInDays / 7)} weeks ago`;

            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },
        viewJob(job) {
            this.selectedJob = job;
            this.jobModal.show();
        },
        async withdrawApplication(jobId) {
            try {
                const result = await Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, withdraw it!'
                });

                if (result.isConfirmed) {
                    this.appliedJobs = this.appliedJobs.filter(job => job.id !== jobId);
                    Swal.fire(
                        'Withdrawn!',
                        'Your application has been withdrawn.',
                        'success'
                    );
                }
            } catch (error) {
                console.error('Error withdrawing application:', error);
                Swal.fire(
                    'Error',
                    'There was a problem withdrawing your application.',
                    'error'
                );
            }
        },
        async applyJob(jobId) {
            try {
                const result = await Swal.fire({
                    title: 'Apply for this job?',
                    text: "Make sure your profile is up to date before applying.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, apply now!'
                });

                if (result.isConfirmed) {
                    // In a real app, this would submit the application
                    const job = this.savedJobs.find(j => j.id === jobId);
                    this.savedJobs = this.savedJobs.filter(j => j.id !== jobId);
                    job.appliedDate = new Date();
                    this.appliedJobs.unshift(job);

                    Swal.fire(
                        'Applied!',
                        'Your application has been submitted.',
                        'success'
                    );

                    this.jobModal.hide();
                }
            } catch (error) {
                console.error('Error applying to job:', error);
                Swal.fire(
                    'Error',
                    'There was a problem submitting your application.',
                    'error'
                );
            }
        },
        async unsaveJob(jobId) {
            try {
                const result = await Swal.fire({
                    title: 'Remove this job?',
                    text: "This job will be removed from your saved list.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                });

                if (result.isConfirmed) {
                    this.savedJobs = this.savedJobs.filter(job => job.id !== jobId);
                    Swal.fire(
                        'Removed!',
                        'This job has been removed from your saved list.',
                        'success'
                    );
                }
            } catch (error) {
                console.error('Error removing saved job:', error);
                Swal.fire(
                    'Error',
                    'There was a problem removing this job.',
                    'error'
                );
            }
        },
        markAllAsRead() {
            // In a real app, this would mark all notifications as read
            Swal.fire(
                'Marked as read',
                'All applications have been marked as read.',
                'success'
            );
        },
        clearAllApplications() {
            Swal.fire({
                title: 'Clear all applications?',
                text: "This will remove all your application history. This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, clear all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.appliedJobs = [];
                    Swal.fire(
                        'Cleared!',
                        'All applications have been cleared.',
                        'success'
                    );
                }
            });
        }
    },
    mounted() {
        this.fetchJobs();
        this.jobModal = new bootstrap.Modal(document.getElementById('jobModal'));

        // Listen for tab changes to update activeTab
        const tabEls = document.querySelectorAll('#myTab button[data-bs-toggle="tab"]');
        tabEls.forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', event => {
                this.activeTab = event.target.id.includes('applied') ? 'applied' : 'saved';
            });
        });
    }
}).mount('#app');