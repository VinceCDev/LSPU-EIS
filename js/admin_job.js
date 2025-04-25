const { createApp } = Vue;

createApp({
    data() {
        return {
            sidebarActive: false,
            searchQuery: '',
            filters: {
                department: '',
                type: '',
                status: ''
            },
            employer: {
                name: "Tech Solutions Inc.",
                email: "contact@techsolutions.com",
                logo: "https://via.placeholder.com/150"
            },
            jobs: [
                // Example jobs, populate dynamically as needed
            ],
            newJob: {
                title: "",
                department: "",
                type: "",
                location: "",
                description: "",
                requirements: "",
                salary: "",
                status: "Active",
                employerQuestion: "",
                qualifications: "",
                company_id: 1 // Example company_id
            },
            selectedJob: null,
            currentPage: 1,
            itemsPerPage: 5,
            departments: [] // Array to store fetched departments
        };
    },
    created() {
        this.fetchDepartments(); // Fetch departments when the component is created
        this.fetchJobs();
    },
    computed: {
        filteredJobs() {
            return this.jobs.filter(job => {
                const q = this.searchQuery.toLowerCase();
                const matchesSearch = job.title.toLowerCase().includes(q) ||
                    job.department.toLowerCase().includes(q) ||
                    job.type.toLowerCase().includes(q) ||
                    job.location.toLowerCase().includes(q) ||
                    job.status.toLowerCase().includes(q);

                const matchesDept = !this.filters.department || job.department === this.filters.department;
                const matchesType = !this.filters.type || job.type === this.filters.type;
                const matchesStatus = !this.filters.status || job.status === this.filters.status;

                return matchesSearch && matchesDept && matchesType && matchesStatus;
            });
        },
        paginatedJobs() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredJobs.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.filteredJobs.length / this.itemsPerPage);
        }
    },
    methods: {
        toggleSidebar() {
            this.sidebarActive = !this.sidebarActive;
        },

        viewJob(job) {
            this.selectedJob = job;
            // Show the modal
            var myModal = new bootstrap.Modal(document.getElementById('jobDetailsModal'));
            myModal.show();
        },
    
        // Hide job details modal
        hideJobDetails() {
            this.selectedJob = null;
            // Close the modal
            var myModal = new bootstrap.Modal(document.getElementById('jobDetailsModal'));
            myModal.hide();
        },

        openEditModal(job) {
            this.newJob = { ...job }; // Pre-fill modal form
            const modal = new bootstrap.Modal(document.getElementById('editJobModal'));
            modal.show();
          },
        
          updateJob() {
            fetch('functions/update_job.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(this.newJob)
            })
            .then(res => res.json())
            .then(response => {
              if (response.success) {
                alert('Job updated successfully!');
                this.fetchJobs(); // Refresh the list
                bootstrap.Modal.getInstance(document.getElementById('editJobModal')).hide();
              } else {
                alert('Failed to update job.');
              }
            })
            .catch(err => console.error(err));
          },

        // Fetch departments from the backend
        fetchDepartments() {
            fetch('functions/get_company.php') // Replace with your actual backend path
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Log the response for debugging
                    if (data.success) {
                        this.departments = data.departments; // Update departments with the response
                    } else {
                        alert('Error fetching departments: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching departments.');
                });
        },

        fetchJobs() {
            fetch('functions/get_jobs.php') // Adjust the path as needed
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.jobs = data.jobs;
                    } else {
                        alert('Error fetching jobs: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error fetching jobs:', error);
                    alert('Error fetching jobs.');
                });
        },

        confirmDelete(job) {
            Swal.fire({
              title: 'Are you sure?',
              text: `Do you really want to delete the job posting "${job.title}"?`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#6c757d',
              confirmButtonText: 'Yes, delete it!',
              reverseButtons: true
            }).then((result) => {
              if (result.isConfirmed) {
                this.deleteJob(job.id);
              }
            });
          },
        
          deleteJob(jobId) {
            fetch('functions/delete_job.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({ id: jobId })
            })
            .then(res => res.json())
            .then(response => {
              if (response.success) {
                Swal.fire('Deleted!', 'The job has been removed.', 'success');
                this.fetchJobs(); // or whatever method reloads your job list
              } else {
                Swal.fire('Error!', response.error || 'Failed to delete job.', 'error');
              }
            })
            .catch(error => {
              console.error(error);
              Swal.fire('Error!', 'Something went wrong.', 'error');
            });
          },
        

        // Submit new job to the backend
        submitJob() {
            const jobData = {
                title: this.newJob.title,
                department: this.newJob.department,
                type: this.newJob.type,
                location: this.newJob.location,
                description: this.newJob.description,
                requirements: this.newJob.requirements,
                salary: this.newJob.salary,
                status: this.newJob.status,
                employerQuestion: this.newJob.employerQuestion,
                qualifications: this.newJob.qualifications,
                company_id: this.newJob.company_id
            };

            fetch('functions/insert_job.php', { // Endpoint to submit job data
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(jobData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Job posted successfully!');
                    this.resetForm(); // Reset form after success
                } else {
                    alert('Error posting job: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting job.');
            });
        },

        // Reset the form after successful submission
        resetForm() {
            this.newJob = {
                title: '',
                department: '',
                type: '',
                location: '',
                description: '',
                requirements: '',
                salary: '',
                status: 'Active',
                employerQuestion: '',
                qualifications: '',
                company_id: 1 // Reset to default company ID or dynamically set
            };
        },

        // Change to the previous page for pagination
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        // Change to the next page for pagination
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        // Export filtered jobs to an Excel file
        exportToExcel() {
            const worksheet = XLSX.utils.json_to_sheet(this.filteredJobs.map(job => ({
                Title: job.title,
                Department: job.department,
                Type: job.type,
                Location: job.location,
                Applications: job.applications,
                Status: job.status,
                Posted: this.formatDate(job.postedDate)
            })));
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Jobs");
            XLSX.writeFile(workbook, "job_postings.xlsx");
        },

        // Export filtered jobs to a PDF file
        exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const tableBody = this.filteredJobs.map(job => [
                job.title,
                job.department,
                job.type,
                job.location,
                job.applications,
                job.status,
                this.formatDate(job.postedDate)
            ]);

            doc.autoTable({
                head: [['Title', 'Department', 'Type', 'Location', 'Applications', 'Status', 'Posted']],
                body: tableBody
            });

            doc.save("job_postings.pdf");
        },

        // Helper method to format dates
        formatDate(dateString) {
            const date = new Date(dateString);
            return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
        }
    }
}).mount('#app');
