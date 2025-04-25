const {
    createApp,
    ref,
    computed,
    onMounted 
} = Vue;

createApp({
    setup() {
        // Search and filter data
        const searchQuery = ref('');
        const selectedLocation = ref('');
        const selectedJobType = ref('');
        const selectedSalary = ref('');

        // Job details sidebar
        const showDetails = ref(false);
        const selectedJob = ref(null);

        const loading = ref(true);

        // Sample data
        const locations = ref(['Manila', 'Laguna', 'Cavite', 'Batangas', 'Rizal', 'Quezon']);
        const jobTypes = ref(['Full-time', 'Part-time', 'Contract', 'Internship', 'Temporary']);
        const salaryRanges = ref([{
                value: '0-20000',
                label: 'Below ₱20,000'
            },
            {
                value: '20000-40000',
                label: '₱20,000 - ₱40,000'
            },
            {
                value: '40000-60000',
                label: '₱40,000 - ₱60,000'
            },
            {
                value: '60000+',
                label: 'Above ₱60,000'
            }
        ]);

        const jobs = ref([]);

        onMounted(async () => {
            loading.value = true;
            try {
              const response = await fetch('functions/fetch_jobs.php');
              const data = await response.json();
              jobs.value = data;
            } catch (error) {
              console.error('Error fetching job data:', error);
            } finally {
                loading.value = false;
            }
          });

        // Computed property for filtered jobs
        const filteredJobs = computed(() => {
            return jobs.value.filter(job => {
                const matchesSearch = job.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                    job.company.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                    job.description.toLowerCase().includes(searchQuery.value.toLowerCase());

                const matchesLocation = !selectedLocation.value || job.location === selectedLocation.value;
                const matchesJobType = !selectedJobType.value || job.type === selectedJobType.value;

                // Simple salary filter (in a real app, you'd parse the salary ranges)
                const matchesSalary = !selectedSalary.value ||
                    (selectedSalary.value === '60000+' && job.salary.includes('₱60,000')) ||
                    (selectedSalary.value === '40000-60000' && job.salary.includes('₱40,000')) ||
                    (selectedSalary.value === '20000-40000' && job.salary.includes('₱20,000')) ||
                    (selectedSalary.value === '0-20000' && job.salary.includes('Below ₱20,000'));

                return matchesSearch && matchesLocation && matchesJobType && matchesSalary;
            });
        });

        // Methods
        const showJobDetails = (job) => {
            selectedJob.value = job;
            showDetails.value = true;
            document.body.style.overflow = 'hidden';
        };

        const hideJobDetails = () => {
            showDetails.value = false;
            document.body.style.overflow = '';
        };

        const toggleSave = (job) => {
            job.saved = !job.saved;
        };

        return {
            searchQuery,
            selectedLocation,
            selectedJobType,
            selectedSalary,
            locations,
            jobTypes,
            salaryRanges,
            jobs,
            filteredJobs,
            showDetails,
            selectedJob,
            showJobDetails,
            hideJobDetails,
            toggleSave,
            loading
        };
    }
}).mount('#app');