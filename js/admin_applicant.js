const {
    createApp
} = Vue;

createApp({
    data() {
        return {
            sidebarActive: false,
            employer: {
                name: "Tech Solutions Inc.",
                email: "contact@techsolutions.com",
                logo: "https://via.placeholder.com/150"
            },
            applicants: [{
                    id: 1,
                    name: "Juan Dela Cruz",
                    email: "juan.delacruz@example.com",
                    phone: "+63 912 345 6789",
                    location: "Manila, Philippines",
                    profileImage: "https://randomuser.me/api/portraits/men/32.jpg",
                    appliedFor: "Frontend Developer",
                    appliedDate: "2023-06-15",
                    experience: 3,
                    status: "New",
                    skills: "HTML, CSS, JavaScript, React, Vue.js",
                    notes: "Strong portfolio with impressive React projects."
                },
                {
                    id: 2,
                    name: "Maria Santos",
                    email: "maria.santos@example.com",
                    phone: "+63 917 890 1234",
                    location: "Cebu, Philippines",
                    profileImage: "https://randomuser.me/api/portraits/women/44.jpg",
                    appliedFor: "Marketing Specialist",
                    appliedDate: "2023-06-14",
                    experience: 5,
                    status: "Reviewed",
                    skills: "Digital Marketing, Social Media, SEO, Content Creation",
                    notes: "Excellent communication skills. Good fit for customer-facing roles."
                },
                {
                    id: 3,
                    name: "Robert Lim",
                    email: "robert.lim@example.com",
                    phone: "+63 918 765 4321",
                    location: "Davao, Philippines",
                    profileImage: "https://randomuser.me/api/portraits/men/67.jpg",
                    appliedFor: "DevOps Engineer",
                    appliedDate: "2023-06-12",
                    experience: 4,
                    status: "Interview",
                    skills: "AWS, Docker, Kubernetes, CI/CD, Terraform",
                    notes: "Technical interview scheduled for June 20."
                },
                {
                    id: 4,
                    name: "Anna Reyes",
                    email: "anna.reyes@example.com",
                    phone: "+63 920 123 4567",
                    location: "Quezon City, Philippines",
                    profileImage: "https://randomuser.me/api/portraits/women/28.jpg",
                    appliedFor: "HR Manager",
                    appliedDate: "2023-06-10",
                    experience: 6,
                    status: "Rejected",
                    skills: "Recruitment, Employee Relations, HR Policies",
                    notes: "Not enough experience in tech industry."
                },
                {
                    id: 5,
                    name: "Michael Tan",
                    email: "michael.tan@example.com",
                    phone: "+63 921 987 6543",
                    location: "Makati, Philippines",
                    profileImage: "https://randomuser.me/api/portraits/men/52.jpg",
                    appliedFor: "Financial Analyst",
                    appliedDate: "2023-06-08",
                    experience: 2,
                    status: "Hired",
                    skills: "Financial Analysis, Excel, Financial Modeling",
                    notes: "Offer accepted. Starting date July 1."
                },
                {
                    id: 6,
                    name: "Sarah Gomez",
                    email: "sarah.gomez@example.com",
                    phone: "+63 923 456 7890",
                    location: "Iloilo, Philippines",
                    profileImage: "https://randomuser.me/api/portraits/women/63.jpg",
                    appliedFor: "UI/UX Designer",
                    appliedDate: "2023-06-18",
                    experience: 4,
                    status: "New",
                    skills: "UI Design, UX Research, Figma, Adobe XD",
                    notes: ""
                },
                {
                    id: 7,
                    name: "David Ong",
                    email: "david.ong@example.com",
                    phone: "+63 925 678 9012",
                    location: "Baguio, Philippines",
                    profileImage: "https://randomuser.me/api/portraits/men/29.jpg",
                    appliedFor: "Backend Developer",
                    appliedDate: "2023-06-14",
                    experience: 5,
                    status: "Interview",
                    skills: "Node.js, Python, SQL, MongoDB, REST APIs",
                    notes: "Technical interview scheduled for June 21."
                },
                {
                    id: 8,
                    name: "Carla Ramirez",
                    email: "carla.ramirez@example.com",
                    phone: "+63 927 890 1234",
                    location: "Cavite, Philippines",
                    profileImage: "https://randomuser.me/api/portraits/women/35.jpg",
                    appliedFor: "Content Writer",
                    appliedDate: "2023-06-08",
                    experience: 3,
                    status: "Reviewed",
                    skills: "Content Writing, Copywriting, SEO, Blogging",
                    notes: "Writing samples are excellent. May be a good fit."
                }
            ],
            newApplicant: {
                name: "",
                email: "",
                phone: "",
                location: "",
                profileImage: "",
                appliedFor: "",
                appliedDate: new Date().toISOString().split('T')[0],
                experience: 0,
                status: "New",
                skills: "",
                notes: ""
            },
            selectedApplicant: null,
            searchQuery: "",
            filteredApplicants: [],
            currentPage: 1,
            itemsPerPage: 5
        }
    },
    created() {
        this.filteredApplicants = [...this.applicants];
    },
    computed: {
        totalPages() {
            return Math.ceil(this.filteredApplicants.length / this.itemsPerPage);
        },
        paginatedApplicants() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredApplicants.slice(start, end);
        }
    },
    methods: {
        toggleSidebar() {
            this.sidebarActive = !this.sidebarActive;
        },
        filterApplicants() {
            if (!this.searchQuery) {
                this.filteredApplicants = [...this.applicants];
                this.currentPage = 1;
                return;
            }

            const query = this.searchQuery.toLowerCase();
            this.filteredApplicants = this.applicants.filter(applicant =>
                applicant.name.toLowerCase().includes(query) ||
                applicant.email.toLowerCase().includes(query) ||
                applicant.appliedFor.toLowerCase().includes(query) ||
                applicant.location.toLowerCase().includes(query) ||
                applicant.status.toLowerCase().includes(query)
            );
            this.currentPage = 1;
        },
        formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString('en-US', options);
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
        viewApplicant(applicant) {
            this.selectedApplicant = applicant;
            const modal = new bootstrap.Modal(document.getElementById('viewApplicantModal'));
            modal.show();
        },
        editApplicant(applicant) {
            this.selectedApplicant = applicant;
            this.newApplicant = {
                ...applicant
            };
            const modal = new bootstrap.Modal(document.getElementById('addApplicantModal'));
            modal.show();
        },
        confirmDelete(applicant) {
            this.selectedApplicant = applicant;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        },
        deleteApplicant() {
            this.applicants = this.applicants.filter(applicant => applicant.id !== this.selectedApplicant.id);
            this.filterApplicants();
            this.selectedApplicant = null;
        },
        submitApplicant() {
            if (this.selectedApplicant) {
                // Update existing applicant
                const index = this.applicants.findIndex(applicant => applicant.id === this.selectedApplicant.id);
                if (index !== -1) {
                    this.applicants[index] = {
                        ...this.newApplicant,
                        id: this.selectedApplicant.id
                    };
                }
            } else {
                // Add new applicant
                const newId = Math.max(...this.applicants.map(applicant => applicant.id)) + 1;
                this.applicants.push({
                    ...this.newApplicant,
                    id: newId,
                    profileImage: "https://randomuser.me/api/portraits/" +
                        (Math.random() > 0.5 ? "men" : "women") +
                        "/" + Math.floor(Math.random() * 100) + ".jpg"
                });
            }

            // Reset form and close modal
            this.newApplicant = {
                name: "",
                email: "",
                phone: "",
                location: "",
                profileImage: "",
                appliedFor: "",
                appliedDate: new Date().toISOString().split('T')[0],
                experience: 0,
                status: "New",
                skills: "",
                notes: ""
            };
            this.selectedApplicant = null;
            this.filterApplicants();

            const modal = bootstrap.Modal.getInstance(document.getElementById('addApplicantModal'));
            modal.hide();
        }
    }
}).mount('#app');