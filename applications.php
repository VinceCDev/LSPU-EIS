<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Form</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Vue.js 3 CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <style>
        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .step-circle.active {
            background: #0d6efd;
            color: white;
        }

        .step-circle.completed {
            background: #198754;
            color: white;
        }

        .skill-tag {
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        .step-title {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 500;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container py-4" id="app">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container">
                    <h2 class="text-center mb-4">Job Application Form</h2>

                    <!-- Numbered Step Indicator -->
                    <div class="step-indicator">
                        <div class="step-circle"
                            :class="{active: currentStep === 1, completed: currentStep > 1}"
                            @click="goToStep(1)">1</div>
                        <div class="step-circle"
                            :class="{active: currentStep === 2, completed: currentStep > 2}"
                            @click="goToStep(2)">2</div>
                        <div class="step-circle"
                            :class="{active: currentStep === 3, completed: currentStep > 3}"
                            @click="goToStep(3)">3</div>
                        <div class="step-circle"
                            :class="{active: currentStep === 4, completed: currentStep > 4}"
                            @click="goToStep(4)">4</div>
                        <div class="step-circle"
                            :class="{active: currentStep === 5, completed: currentStep > 5}"
                            @click="goToStep(5)">5</div>
                    </div>

                    <!-- Step Title -->
                    <h5 class="step-title">{{ steps[currentStep-1].title }}</h5>

                    <!-- Personal Information -->
                    <div class="form-section" :class="{active: currentStep === 1}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" v-model="application.firstName" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" v-model="application.lastName" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" v-model="application.email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" v-model="application.phone" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" v-model="application.address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Education -->
                    <div class="form-section" :class="{active: currentStep === 2}">
                        <div v-for="(edu, index) in application.education" :key="index" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Institution</label>
                                    <input type="text" class="form-control" v-model="edu.institution" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Degree</label>
                                    <input type="text" class="form-control" v-model="edu.degree" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Field of Study</label>
                                    <input type="text" class="form-control" v-model="edu.field" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" v-model="edu.startDate" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" v-model="edu.endDate" required>
                                </div>
                                <div class="col-12 text-end" v-if="application.education.length > 1">
                                    <button class="btn btn-sm btn-outline-danger" @click="removeEducation(index)">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <hr v-if="index < application.education.length - 1">
                        </div>
                        <button class="btn btn-outline-secondary" @click="addEducation">
                            + Add Education
                        </button>
                    </div>

                    <!-- Skills -->
                    <div class="form-section" :class="{active: currentStep === 3}">
                        <div class="mb-3">
                            <label class="form-label">Add Skills</label>
                            <input type="text" class="form-control"
                                v-model="newSkill" @keyup.enter="addSkill"
                                placeholder="Type skill and press Enter">
                        </div>

                        <div class="d-flex flex-wrap mb-4">
                            <span class="badge bg-primary p-2 skill-tag" v-for="(skill, index) in application.skills" :key="index">
                                {{ skill }}
                                <button type="button" class="btn-close btn-close-white ms-2"
                                    @click="removeSkill(index)"></button>
                            </span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Skills</label>
                            <textarea class="form-control" v-model="application.additionalSkills" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Work Experience -->
                    <div class="form-section" :class="{active: currentStep === 4}">
                        <div v-for="(exp, index) in application.experience" :key="index" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company</label>
                                    <input type="text" class="form-control" v-model="exp.company" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Position</label>
                                    <input type="text" class="form-control" v-model="exp.position" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" v-model="exp.startDate" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" v-model="exp.endDate" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Responsibilities</label>
                                    <textarea class="form-control" v-model="exp.responsibilities" rows="3"></textarea>
                                </div>
                                <div class="col-12 text-end" v-if="application.experience.length > 1">
                                    <button class="btn btn-sm btn-outline-danger" @click="removeExperience(index)">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <hr v-if="index < application.experience.length - 1">
                        </div>
                        <button class="btn btn-outline-secondary" @click="addExperience">
                            + Add Experience
                        </button>
                    </div>

                    <!-- Resume -->
                    <div class="form-section" :class="{active: currentStep === 5}">
                        <div class="mb-3">
                            <label class="form-label">Upload Resume</label>
                            <input type="file" class="form-control" accept=".pdf,.doc,.docx" @change="handleFileUpload">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cover Letter</label>
                            <textarea class="form-control" v-model="application.coverLetter" rows="5"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" v-model="application.agreeTerms" required>
                            <label class="form-check-label">
                                I certify that all information is accurate
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-secondary" @click="prevStep" :disabled="currentStep === 1">
                            Back
                        </button>
                        <button class="btn btn-primary" @click="nextStep" v-if="currentStep < 5">
                            Continue
                        </button>
                        <button class="btn btn-success" @click="submitApplication" v-if="currentStep === 5">
                            Submit Application
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const {
            createApp,
            ref
        } = Vue;

        createApp({
            setup() {
                const currentStep = ref(1);
                const newSkill = ref('');

                const steps = ref([{
                        title: 'Personal Information'
                    },
                    {
                        title: 'Education Background'
                    },
                    {
                        title: 'Skills & Qualifications'
                    },
                    {
                        title: 'Work Experience'
                    },
                    {
                        title: 'Resume & Final Details'
                    }
                ]);

                const application = ref({
                    firstName: '',
                    lastName: '',
                    email: '',
                    phone: '',
                    address: '',
                    education: [{
                        institution: '',
                        degree: '',
                        field: '',
                        startDate: '',
                        endDate: ''
                    }],
                    skills: [],
                    additionalSkills: '',
                    experience: [{
                        company: '',
                        position: '',
                        startDate: '',
                        endDate: '',
                        responsibilities: ''
                    }],
                    resume: null,
                    coverLetter: '',
                    agreeTerms: false
                });

                const nextStep = () => {
                    if (validateStep()) {
                        currentStep.value++;
                    }
                };

                const prevStep = () => {
                    currentStep.value--;
                };

                const goToStep = (step) => {
                    if (step <= currentStep.value) {
                        currentStep.value = step;
                    }
                };

                const validateStep = () => {
                    switch (currentStep.value) {
                        case 1:
                            if (!application.value.firstName || !application.value.lastName ||
                                !application.value.email || !application.value.phone) {
                                alert('Please complete all required fields');
                                return false;
                            }
                            return true;
                        case 2:
                            const eduValid = application.value.education.every(edu =>
                                edu.institution && edu.degree && edu.startDate);
                            if (!eduValid) {
                                alert('Please complete all education fields');
                                return false;
                            }
                            return true;
                        case 4:
                            const expValid = application.value.experience.every(exp =>
                                exp.company && exp.position && exp.startDate);
                            if (!expValid) {
                                alert('Please complete all work experience fields');
                                return false;
                            }
                            return true;
                        default:
                            return true;
                    }
                };

                const addEducation = () => {
                    application.value.education.push({
                        institution: '',
                        degree: '',
                        field: '',
                        startDate: '',
                        endDate: ''
                    });
                };

                const removeEducation = (index) => {
                    application.value.education.splice(index, 1);
                };

                const addExperience = () => {
                    application.value.experience.push({
                        company: '',
                        position: '',
                        startDate: '',
                        endDate: '',
                        responsibilities: ''
                    });
                };

                const removeExperience = (index) => {
                    application.value.experience.splice(index, 1);
                };

                const addSkill = () => {
                    if (newSkill.value.trim()) {
                        const skills = newSkill.value.split(',').map(s => s.trim()).filter(s => s);
                        skills.forEach(skill => {
                            if (!application.value.skills.includes(skill)) {
                                application.value.skills.push(skill);
                            }
                        });
                        newSkill.value = '';
                    }
                };

                const removeSkill = (index) => {
                    application.value.skills.splice(index, 1);
                };

                const handleFileUpload = (e) => {
                    application.value.resume = e.target.files[0];
                };

                const submitApplication = () => {
                    if (!application.value.agreeTerms) {
                        alert('Please certify that your information is accurate');
                        return;
                    }

                    console.log('Submitted:', application.value);
                    alert('Application submitted successfully!');
                };

                return {
                    currentStep,
                    steps,
                    application,
                    newSkill,
                    nextStep,
                    prevStep,
                    goToStep,
                    addEducation,
                    removeEducation,
                    addExperience,
                    removeExperience,
                    addSkill,
                    removeSkill,
                    handleFileUpload,
                    submitApplication
                };
            }
        }).mount('#app');
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>