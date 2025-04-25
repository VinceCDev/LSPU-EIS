const {
    createApp
} = Vue;

createApp({
    data() {
        return {
            cityList: [],
            editProfileData: {
                address: '',
                // other fields...
            },
            editExperienceData: {
                title: '',
                company: '',
                companyAddress: '',
                sector: '',
                location: '',
                salary: '',
                employmentType: '',
                industry: '',
                startDate: '',
                endDate: '',
                current: false,
                description: ''
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
                college: '',
                degree: '',
                graduationYear: '',
                 
                skills: [
                ],
                education: [{
                    degree: '',
                    college: '',
                    startDate: '',
                    endDate: '',
                    current: false
                }],
                experiences: [
                ],
                resume: {
                    name: 'applicant.pdf',
                    url: 'https://example.com/resume.pdf'
                }
            },
            editProfileData: {},
            editSkillsData: [],
            newSkill: {
                name: '',
                certificate: ''
            },
            editEducationData: {
                degree: '',
                school: '',
                startDate: '',
                endDate: '',
                current: false
            },
            editingEducationIndex: null,
            editExperienceData: {
                title: '',
                company: '',
                startDate: '',
                endDate: '',
                current: false,
                description: ''
            },
            editingExperienceIndex: null,
            profileModal: null,
            skillsModal: null,
            educationModal: null,
            experienceModal: null,
            resumeModal: null
        }
    },
    methods: {
        
        formatDate(dateString) {
            if (!dateString) return 'Present';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short'
            });
        },
        editProfile() {
            this.editProfileData = JSON.parse(JSON.stringify(this.profile));
            this.profileModal.show();
        },
        saveProfile() {
            this.profile = JSON.parse(JSON.stringify(this.editProfileData));
            this.profileModal.hide();
            Swal.fire(
                'Success!',
                'Your profile has been updated.',
                'success'
            );
        },
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.editProfileData.image = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        editSkills() {
            this.editSkillsData = JSON.parse(JSON.stringify(this.profile.skills));
            this.skillsModal.show();
        },
        saveSkills() {
            this.profile.skills = JSON.parse(JSON.stringify(this.editSkillsData));
            this.skillsModal.hide();
            Swal.fire(
                'Success!',
                'Your skills have been updated.',
                'success'
            );
        },
        addSkill() {
            if (this.newSkill.name.trim()) {
                this.editSkillsData.push({
                    name: this.newSkill.name.trim(),
                    certificate: this.newSkill.certificate.trim()
                });
                this.newSkill = {
                    name: '',
                    certificate: ''
                };
            }
        },
        removeSkill(index) {
            this.profile.skills.splice(index, 1);
        },
        removeEditSkill(index) {
            this.editSkillsData.splice(index, 1);
        },
        addEducation() {
            this.editingEducationIndex = null;
            this.editEducationData = {
                degree: '',
                school: '',
                startDate: '',
                endDate: '',
                current: false
            };
            this.educationModal.show();
        },
        editEducation(index) {
            this.editingEducationIndex = index;
            this.editEducationData = JSON.parse(JSON.stringify(this.profile.education[index]));
            this.educationModal.show();
        },
        saveEducation() {
            if (this.editingEducationIndex === null) {
                this.profile.education.unshift(this.editEducationData);
            } else {
                this.profile.education[this.editingEducationIndex] = this.editEducationData;
            }
            this.educationModal.hide();
            Swal.fire(
                'Success!',
                'Your education has been updated.',
                'success'
            );
        },
        async deleteEducation(index) {
            try {
                const result = await Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                });

                if (result.isConfirmed) {
                    this.profile.education.splice(index, 1);
                    Swal.fire(
                        'Deleted!',
                        'Your education has been deleted.',
                        'success'
                    );
                }
            } catch (error) {
                console.error('Error deleting education:', error);
            }
        },
        addExperience() {
            this.editingExperienceIndex = null;
            this.editExperienceData = {
                title: '',
                company: '',
                startDate: '',
                endDate: '',
                current: false,
                description: ''
            };
            this.experienceModal.show();
        },
        editExperience(index) {
            this.editingExperienceIndex = index;
            this.editExperienceData = JSON.parse(JSON.stringify(this.profile.experiences[index]));
            this.experienceModal.show();
        },
        saveExperience() {
            if (this.editingExperienceIndex === null) {
                this.profile.experiences.unshift(this.editExperienceData);
            } else {
                this.profile.experiences[this.editingExperienceIndex] = this.editExperienceData;
            }
            this.experienceModal.hide();
            Swal.fire(
                'Success!',
                'Your work experience has been updated.',
                'success'
            );
        },
        async deleteExperience(index) {
            try {
                const result = await Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                });

                if (result.isConfirmed) {
                    this.profile.experiences.splice(index, 1);
                    Swal.fire(
                        'Deleted!',
                        'Your experience has been deleted.',
                        'success'
                    );
                }
            } catch (error) {
                console.error('Error deleting experience:', error);
            }
        },
        viewResume() {
            if (this.profile.resume) {
                this.resumeModal.show();
            } else {
                this.uploadResume();
            }
        },
        uploadResume() {
            // In a real app, this would open a file upload dialog
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf,.doc,.docx';
            input.onchange = (e) => {
                const file = e.target.files[0];
                if (file) {
                    this.profile.resume = {
                        name: file.name,
                        url: URL.createObjectURL(file)
                    };
                    Swal.fire(
                        'Success!',
                        'Your resume has been uploaded.',
                        'success'
                    );
                    this.resumeModal.show();
                }
            };
            input.click();
        },
        async fetchCities() {
            try {
              const response = await fetch("https://psgc.gitlab.io/api/cities-municipalities/");
              const data = await response.json();
              // Sort alphabetically by name
              this.cityList = data.sort((a, b) => a.name.localeCompare(b.name));
            } catch (error) {
              console.error("Error fetching cities:", error);
            }
          },
          updateProfile() {
            // Your existing update logic
          }
    },
    mounted() {
        this.profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
        this.skillsModal = new bootstrap.Modal(document.getElementById('skillsModal'));
        this.educationModal = new bootstrap.Modal(document.getElementById('educationModal'));
        this.experienceModal = new bootstrap.Modal(document.getElementById('experienceModal'));
        this.resumeModal = new bootstrap.Modal(document.getElementById('resumeModal'));
        this.fetchCities();
        fetch('functions/fetch_personal_information.php')
        .then(res => res.json())
        .then(data => {
            this.profile.name = `${data.first_name} ${data.middle_name} ${data.last_name}`;
            this.profile.email = data.email;
            this.profile.phone = data.contact_number;
            this.profile.address = `${data.city}, ${data.province}`;
            this.profile.dob = data.birth_date;
            this.profile.gender = data.gender;
            this.profile.college = data.campus_graduated;
            this.profile.degree = data.course;
            this.profile.graduationYear = data.year_graduated;
        })
        .catch(err => {
            console.error('Failed to fetch profile:', err);
        });
    }
}).mount('#app');