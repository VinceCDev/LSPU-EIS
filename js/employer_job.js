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
            jobs: [{
                    id: 1,
                    title: "Frontend Developer",
                    department: "Engineering",
                    type: "Full-time",
                    location: "Manila",
                    applications: 24,
                    status: "Active",
                    postedDate: "2023-05-15",
                    description: "We are looking for a skilled Frontend Developer to join our team...",
                    requirements: "3+ years of experience with React, JavaScript, HTML/CSS...",
                    salary: "₱50,000 - ₱70,000"
                },
                {
                    id: 2,
                    title: "Marketing Specialist",
                    department: "Marketing",
                    type: "Full-time",
                    location: "Cebu",
                    applications: 15,
                    status: "Active",
                    postedDate: "2023-06-01",
                    description: "Seeking a creative Marketing Specialist to develop and implement marketing strategies...",
                    requirements: "Bachelor's degree in Marketing or related field, 2+ years experience...",
                    salary: "₱40,000 - ₱50,000"
                },
                {
                    id: 3,
                    title: "HR Manager",
                    department: "Human Resources",
                    type: "Full-time",
                    location: "Davao",
                    applications: 8,
                    status: "Draft",
                    postedDate: "2023-06-10",
                    description: "Looking for an experienced HR Manager to oversee all aspects of human resources...",
                    requirements: "5+ years HR experience, knowledge of labor laws, excellent communication skills...",
                    salary: "₱60,000 - ₱80,000"
                },
                {
                    id: 4,
                    title: "Financial Analyst",
                    department: "Finance",
                    type: "Contract",
                    location: "Remote",
                    applications: 12,
                    status: "Active",
                    postedDate: "2023-05-22",
                    description: "Financial Analyst needed to provide financial planning and analysis support...",
                    requirements: "Degree in Finance or Accounting, 3+ years experience, strong Excel skills...",
                    salary: "₱45,000 - ₱55,000"
                },
                {
                    id: 5,
                    title: "Operations Manager",
                    department: "Operations",
                    type: "Full-time",
                    location: "Manila",
                    applications: 5,
                    status: "Closed",
                    postedDate: "2023-04-30",
                    description: "Operations Manager to oversee daily activities and improve operational systems...",
                    requirements: "Proven experience as Operations Manager, strong leadership skills...",
                    salary: "₱70,000 - ₱90,000"
                },
                {
                    id: 6,
                    title: "UI/UX Designer",
                    department: "Engineering",
                    type: "Part-time",
                    location: "Remote",
                    applications: 18,
                    status: "Active",
                    postedDate: "2023-06-05",
                    description: "Creative UI/UX Designer needed to design user interfaces for our digital products...",
                    requirements: "Portfolio required, 2+ years experience with Figma/Sketch, understanding of UX principles...",
                    salary: "₱35,000 - ₱45,000"
                },
                {
                    id: 7,
                    title: "Content Writer",
                    department: "Marketing",
                    type: "Remote",
                    location: "Anywhere",
                    applications: 22,
                    status: "Active",
                    postedDate: "2023-05-28",
                    description: "Talented Content Writer to create engaging content for our website and marketing materials...",
                    requirements: "Excellent writing skills, SEO knowledge, ability to meet deadlines...",
                    salary: "₱30,000 - ₱40,000"
                },
                {
                    id: 8,
                    title: "DevOps Engineer",
                    department: "Engineering",
                    type: "Full-time",
                    location: "Manila",
                    applications: 7,
                    status: "Draft",
                    postedDate: "2023-06-12",
                    description: "DevOps Engineer to implement and maintain our CI/CD pipelines and cloud infrastructure...",
                    requirements: "Experience with AWS, Docker, Kubernetes, and CI/CD tools...",
                    salary: "₱80,000 - ₱100,000"
                }
            ],
            newJob: {
                title: "",
                department: "",
                type: "",
                location: "",
                description: "",
                requirements: "",
                salary: "",
                status: "Active"
            },
            selectedJob: null,
            searchQuery: "",
            filteredJobs: [],
            currentPage: 1,
            itemsPerPage: 5
        }
    },
    created() {
        this.filteredJobs = [...this.jobs];
    },
    computed: {
        totalPages() {
            return Math.ceil(this.filteredJobs.length / this.itemsPerPage);
        },
        paginatedJobs() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredJobs.slice(start, end);
        }
    },
    methods: {
        toggleSidebar() {
            this.sidebarActive = !this.sidebarActive;
        },
        filterJobs() {
            if (!this.searchQuery) {
                this.filteredJobs = [...this.jobs];
                this.currentPage = 1;
                return;
            }

            const query = this.searchQuery.toLowerCase();
            this.filteredJobs = this.jobs.filter(job =>
                job.title.toLowerCase().includes(query) ||
                job.department.toLowerCase().includes(query) ||
                job.type.toLowerCase().includes(query) ||
                job.location.toLowerCase().includes(query) ||
                job.status.toLowerCase().includes(query)
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
        viewJob(job) {
            this.selectedJob = job;
            // In a real app, you would navigate to a job detail page or show a modal
            alert(`Viewing job: ${job.title}`);
        },
        editJob(job) {
            this.selectedJob = job;
            this.newJob = {
                ...job
            };
            const modal = new bootstrap.Modal(document.getElementById('addJobModal'));
            modal.show();
        },
        confirmDelete(job) {
            this.selectedJob = job;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        },
        deleteJob() {
            this.jobs = this.jobs.filter(job => job.id !== this.selectedJob.id);
            this.filterJobs();
            this.selectedJob = null;
        },
        submitJob() {
            if (this.selectedJob) {
                // Update existing job
                const index = this.jobs.findIndex(job => job.id === this.selectedJob.id);
                if (index !== -1) {
                    this.jobs[index] = {
                        ...this.newJob,
                        id: this.selectedJob.id
                    };
                }
            } else {
                // Add new job
                const newId = Math.max(...this.jobs.map(job => job.id)) + 1;
                this.jobs.push({
                    ...this.newJob,
                    id: newId,
                    applications: 0,
                    postedDate: new Date().toISOString().split('T')[0]
                });
            }

            // Reset form and close modal
            this.newJob = {
                title: "",
                department: "",
                type: "",
                location: "",
                description: "",
                requirements: "",
                salary: "",
                status: "Active"
            };
            this.selectedJob = null;
            this.filterJobs();

            const modal = bootstrap.Modal.getInstance(document.getElementById('addJobModal'));
            modal.hide();
        }
    }
}).mount('#app');