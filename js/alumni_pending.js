const { createApp } = Vue;

const app = createApp({
    data() {
        return {
            sidebarActive: false,
            searchQuery: '',
            currentPage: 1,
            itemsPerPage: 5,
            alumniList: [],
            filters: {
                campus: '', // Make sure this is defined here or in profile
                year: '',
                course: ''
            },
            profile: {
                name: '',
                title: '',
                email: '',
                phone: '',
                address: '',
                dob: '',
                gender: '',
                location: '',
                campus: '',
                degree: '',
                graduationYear: ''
            },
            selectedAlumni: null,
            selectedJob: null,
            
        };
    },
    computed: {
        uniqueDepartments() {
            return [...new Set(this.alumniList.map(a => a.campus))];
        },
        uniqueYear() {
            return [...new Set(this.alumniList.map(a => a.year_graduated))];
        },
        uniqueTypes() {
            return [...new Set(this.alumniList.map(a => a.course || a.year_graduated))];
        },
        filteredAlumni() {
            let result = this.alumniList;

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                result = result.filter(a =>
                    `${a.first_name} ${a.last_name}`.toLowerCase().includes(query)
                );
            }

            if (this.filters.campus) {
                result = result.filter(a => a.campus === this.filters.campus);
            }

            if (this.filters.year) {
                result = result.filter(a => a.year_graduated === this.filters.year);
            }

            if (this.filters.course) {
                result = result.filter(a => a.course === this.filters.course);
            }

            return result;
        },
        closeModal() {
            const modalElement = document.getElementById('alumniDetailsModal');
            modalElement.setAttribute('aria-hidden', 'true');
        },
        paginatedAlumni() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredAlumni.slice(start, start + this.itemsPerPage);
        },
        totalPages() {
            return Math.ceil(this.filteredAlumni.length / this.itemsPerPage);
        }
    },
    methods: {
        toggleSidebar() {
            this.sidebarActive = !this.sidebarActive;
        },
        confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = 'logout.php';
            }
        },
        fetchAlumni() {
            fetch('functions/get_alumni.php')
                .then(res => res.json())
                .then(data => {
                    this.alumniList = data;
                })
                .catch(err => console.error('Fetch Error:', err));
        },
        viewAlumni(alumni) {
            this.selectedAlumni = alumni;
            this.profile = {
                name: `${alumni.first_name} ${alumni.last_name}`,
                title: '',
                email: alumni.email,
                phone: alumni.contact_number,
                address: `${alumni.city}, ${alumni.province}`,
                dob: alumni.birthdate,
                gender: alumni.gender,
                location: alumni.province,
                campus: alumni.campus,
                degree: alumni.course,
                graduationYear: alumni.year_graduated
            };

            const modal = new bootstrap.Modal(document.getElementById('alumniDetailsModal'));
        modal.show();

        // Modify the aria-hidden attribute when opening the modal
        const modalElement = document.getElementById('alumniDetailsModal');
        modalElement.removeAttribute('aria-hidden');
        },
        approveAlumni(alumni) {
            // Show confirmation dialog before proceeding with the approval
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `Do you want to approve ${alumni.first_name} ${alumni.last_name} as an active alumni?`,
                showCancelButton: true,
                confirmButtonText: 'Yes, approve it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send POST request to approve_alumni.php with the alumni's ID
                    fetch('functions/approve_alumni.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: alumni.alumni_id })
                    })
                    .then(res => res.json())  // Parse the response as JSON
                    .then(response => {
                        if (response.success) {
                            // If the request is successful, update alumni's status to 'Active'
                            alumni.status = 'Active';
                
                            // Use SweetAlert to display a success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Approved!',
                                text: `Alumni ${alumni.first_name} ${alumni.last_name} has been approved and is now Active.`,
                                confirmButtonText: 'OK'
                            });
                        } else {
                            // Use SweetAlert to display an error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'There was an error approving the alumni. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(err => {
                        // Handle any network or fetch errors
                        console.error('Error:', err);
                
                        // Use SweetAlert to display a network error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'An error occurred while approving the alumni. Please check your network connection.',
                            confirmButtonText: 'OK'
                        });
                    });
                } else {
                    // If the user cancels, display a cancel message
                    Swal.fire({
                        icon: 'info',
                        title: 'Cancelled',
                        text: 'The alumni approval has been cancelled.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        },
                confirmDelete(alumni) {
            this.selectedJob = alumni;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        },
        deleteJob() {
            if (!this.selectedJob) return;

            fetch('delete_alumni.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: this.selectedJob.id })
            })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        this.alumniList = this.alumniList.filter(a => a.id !== this.selectedJob.id);
                    }
                });

            this.selectedJob = null;
        },
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
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
        }
    },
    mounted() {
        this.fetchAlumni();
        this.sidebarActive = window.innerWidth >= 768;
    }
});

app.mount("#app");