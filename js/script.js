const { createApp } = Vue;

createApp({
    data() {
        return {
            jobs: [],
            selectedJob: null,
            loading: false,
            searchQuery: "",
            selectedJobType: "",
            selectedLocation: "",
            jobData: [
                {
                    title: "Software Engineer",
                    company: "ABC Tech",
                    location: "Metro Manila",
                    jobType: "Full-time",
                    postedDate: "7 days ago",
                    salary: "99.99",
                    rating: "4.5",
                    reviews: "102",
                    industry: "Software Development",
                    employees: "500-1,000 employees",
                    description: "Develop and maintain software applications for enterprise clients.",
                    requirements: [
                        "Experience in JavaScript, React, or Vue",
                        "Knowledge of REST APIs and databases",
                        "Strong problem-solving skills",
                        "Experience with Agile methodologies"
                    ],
                    qualifications: [
                        "Bachelor’s degree in Computer Science or related field",
                        "2+ years of software development experience",
                        "Experience with Git and version control"
                    ],
                    questions: [
                        "How many years of experience do you have in software development?",
                        "Are you familiar with frontend frameworks like React or Vue?",
                        "What’s your expected salary?"
                    ],
                    companyDescription: "ABC Tech is a leading software development company, offering innovative tech solutions worldwide.",
                    companyBanner: "../images/alumni.png",
                    companyLogo: "../images/alumni.png",
                    companyProfileLink: "company-profile.html"
                },
                {
                    title: "Marketing Specialist",
                    company: "XYZ Marketing",
                    location: "Makati",
                    jobType: "Part-time",
                    postedDate: "12 days ago",
                    rating: "4.2",
                    reviews: "76",
                    industry: "Marketing & Advertising",
                    employees: "200-500 employees",
                    description: "Create and execute marketing campaigns to increase brand awareness.",
                    requirements: [
                        "Experience with digital marketing and SEO",
                        "Ability to analyze marketing data",
                        "Proficiency in social media management",
                        "Strong communication skills"
                    ],
                    qualifications: [
                        "Bachelor’s degree in Marketing or related field",
                        "1-3 years of experience in marketing",
                        "Google Ads certification is a plus"
                    ],
                    questions: [
                        "Do you have experience with social media marketing?",
                        "What marketing tools have you used?",
                        "Are you comfortable working on a part-time basis?"
                    ],
                    companyDescription: "XYZ Marketing is an award-winning marketing agency specializing in digital strategies and brand growth.",
                    companyBanner: "images/xyz-marketing-banner.jpg",
                    companyLogo: "images/xyz-marketing-logo.png",
                    companyProfileLink: "company-profile.html"
                },
                {
                    title: "Graphic Designer",
                    company: "Creatives Inc.",
                    location: "Quezon City",
                    jobType: "Remote",
                    postedDate: "5 days ago",
                    rating: "4.6",
                    reviews: "89",
                    industry: "Design & Multimedia",
                    employees: "100-300 employees",
                    description: "Design branding materials, social media graphics, and web assets.",
                    requirements: [
                        "Proficiency in Adobe Photoshop & Illustrator",
                        "Experience in UI/UX design is a plus",
                        "Ability to create engaging content",
                        "Strong portfolio of previous work"
                    ],
                    qualifications: [
                        "Bachelor’s degree in Graphic Design or related field",
                        "1-3 years of experience in design",
                        "Knowledge of Figma or Adobe XD is a plus"
                    ],
                    questions: [
                        "Can you share your portfolio?",
                        "Are you comfortable working remotely?",
                        "What design tools are you proficient in?"
                    ],
                    companyDescription: "Creatives Inc. specializes in branding, digital marketing, and UI/UX design for global clients.",
                    companyBanner: "images/creatives-inc-banner.jpg",
                    companyLogo: "images/creatives-inc-logo.png",
                    companyProfileLink: "company-profile.html"
                }
            ]
        };
    },
    computed: {
        filteredJobs() {
            return this.jobs.filter(job =>
                job.title.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        }
    },
    mounted() {
        this.reloadJobs();
    },
    methods: {
        reloadJobs() {
            this.loading = true;
            setTimeout(() => {
                this.jobs = [...this.jobData];
                this.loading = false;
            }, 1000);
        },
        showJobDetails(job) {
            this.selectedJob = job;
        },
        saveJob(job) {
            Swal.fire("Saved!", `You have saved ${job.title} for later.`, "success");
        },
        applyJob(job) {
            Swal.fire("Application Sent!", `You applied for ${job.title}.`, "success");
        }
    }
}).mount("#app");

// Dark Mode Toggle
function toggleDarkMode() {
    document.body.classList.toggle("bg-dark");
    document.body.classList.toggle("text-white");
}
