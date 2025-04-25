<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();

    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPU EIS - My Profile</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/my_profile.css">
</head>

<body>
    <div id="app">
        <!-- Header -->
        <header class="bg-white shadow-sm fixed-top">
            <div class="container h-100">
                <nav class="navbar navbar-expand-lg navbar-light h-100 py-0">
                    <div class="d-flex align-items-center">
                        <img src="images/alumni.png" alt="LSPU Logo" class="me-2" style="height: var(--logo-size);">
                        <span class="navbar-brand">LSPU EIS Job Portal</span>
                    </div>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto align-items-lg-center">
                            <li class="nav-item">
                                <a class="nav-link" href="home.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="my_application.php">My Applications</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="notif.php">Notifications</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle active" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Profile
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                    <li class="px-3 py-2">
                                        <div class="d-flex align-items-center">
                                            <img :src="profile.image || 'https://via.placeholder.com/150'" alt="Profile" class="profile-img me-2" style="width: var(--profile-img-size); height: var(--profile-img-size); border-radius: 50%;">
                                            <span>{{ profile.name }}</span>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item active" href="#"><i class="fas fa-user me-2"></i> View Profile</a></li>
                                    <li><a class="dropdown-item" href="forgot_password.php"><i class="fas fa-key me-2"></i> Change Password</a></li>
                                    <li><a class="dropdown-item" href="employer_login.php"><i class="fas fa-question-circle me-2"></i> Employer Site</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Profile Main Content -->
        <main class="profile-container">
            <!-- Hero Section -->
            <section class="profile-hero">
                <img :src="profile.image || 'https://via.placeholder.com/150'" alt="Profile Photo" class="profile-img-large">
                <h1 class="profile-name">{{ profile.name }}</h1>
                <p class="profile-title">{{ profile.title || 'No title specified' }}</p>
                <div class="profile-contact">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>{{ profile.email || 'No email specified' }}</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>{{ profile.phone || 'No phone specified' }}</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ profile.address || 'No location specified' }}</span>
                    </div>
                </div>
                <button @click="editProfile" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </button>
            </section>

            <!-- Profile Info Section -->
            <section class="profile-section">
                <div class="section-header">
                    <h2 class="section-title">Personal Information</h2>
                    <button @click="editProfile" class="edit-btn">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ profile.name || 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ profile.email || 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ profile.phone || 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ profile.address || 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">{{ profile.dob || 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value">{{ profile.gender || 'Not specified' }}</div>
                    </div>
                    <!-- College Information -->
                    <div class="info-item">
                        <div class="info-label">College</div>
                        <div class="info-value">{{ profile.college || 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Degree</div>
                        <div class="info-value">{{ profile.degree || 'Not specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Year Graduated</div>
                        <div class="info-value">{{ profile.graduationYear || 'Not specified' }}</div>
                    </div>
                </div>
            </section>

            <!-- Education Section -->
            <section class="profile-section">
                <div class="section-header">
                    <h2 class="section-title">Education</h2>
                    <button @click="addEducation" class="edit-btn">
                        <i class="fas fa-plus me-1"></i>Add
                    </button>
                </div>
                <div v-if="profile.education.length > 0">
                    <div v-for="(edu, index) in profile.education" :key="index" class="education-item">
                        <div class="education-degree">{{ edu.degree }}</div>
                        <div class="education-school">{{ edu.school }}</div>
                        <div class="education-duration">
                            {{ formatDate(edu.startDate) }} - {{ edu.current ? 'Present' : formatDate(edu.endDate) }}
                        </div>
                        <div class="d-flex gap-2">
                            <button @click="editEducation(index)" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button @click="deleteEducation(index)" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="text-muted">
                    No education information added yet
                </div>
            </section>

            <!-- Skills Section -->
            <section class="profile-section">
                <div class="section-header">
                    <h2 class="section-title">Skills</h2>
                    <button @click="editSkills" class="edit-btn">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>
                </div>
                <div class="skills-list">
                    <div v-for="(skill, index) in profile.skills" :key="index" class="skill-tag">
                        {{ skill.name }}
                        <span v-if="skill.certificate" class="certificate-badge">
                            <i class="fas fa-certificate"></i>
                            <span class="certificate-tooltip">Certificate: {{ skill.certificate }}</span>
                        </span>
                        <span @click="removeSkill(index)" class="remove-skill">
                            <i class="fas fa-times"></i>
                        </span>
                    </div>
                    <div v-if="profile.skills.length === 0" class="text-muted">
                        No skills added yet
                    </div>
                </div>
            </section>

            <!-- Experience Section -->
            <section class="profile-section">
                <div class="section-header">
                    <h2 class="section-title">Work Experience</h2>
                    <button @click="addExperience" class="edit-btn">
                        <i class="fas fa-plus me-1"></i>Add
                    </button>
                </div>
                <div v-if="profile.experiences.length > 0">
                    <div v-for="(exp, index) in profile.experiences" :key="index" class="experience-item">
                        <div class="experience-title">{{ exp.title }}</div>
                        <div class="experience-company">{{ exp.company }}</div>
                        <div class="experience-duration">
                            {{ formatDate(exp.startDate) }} - {{ exp.current ? 'Present' : formatDate(exp.endDate) }}
                        </div>
                        <p class="experience-description">{{ exp.description }}</p>
                        <div class="d-flex gap-2">
                            <button @click="editExperience(index)" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button @click="deleteExperience(index)" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="text-muted">
                    No work experience added yet
                </div>
            </section>

            <!-- Resume Section -->
            <section class="profile-section">
                <div class="section-header">
                    <h2 class="section-title">Resume</h2>
                    <button @click="uploadResume" class="edit-btn">
                        <i class="fas fa-upload me-1"></i>Upload
                    </button>
                </div>
                <div class="resume-container" @click="viewResume">
                    <i class="fas fa-file-pdf resume-icon"></i>
                    <div v-if="profile.resume" class="resume-filename">
                        {{ profile.resume.name }}
                    </div>
                    <div v-else class="resume-filename text-muted">
                        No resume uploaded
                    </div>
                    <div class="text-muted">
                        Click to view or upload new resume
                    </div>
                </div>
            </section>
        </main>

        <!-- Profile Edit Modal -->
        <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="profileModalLabel">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="edit-modal-section">
                            <h6 class="edit-modal-section-title">Basic Information</h6>
                            <div class="row g-3">

                                <!-- Full Name -->
                                <div class="col-md-6">
                                    <label for="editName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="editName" v-model="editProfileData.name">
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="editEmail" v-model="editProfileData.email">
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="editPhone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="editPhone" v-model="editProfileData.phone">
                                </div>

                                <!-- Address -->
                                <div class="col-6">
                                    <label for="editAddress" class="form-label">Address</label>
                                    <select class="form-select" id="editAddress" v-model="editProfileData.address">
                                        <option value="">Select City/Municipality</option>
                                        <option v-for="city in cityList" :key="city.code" :value="city.name">
                                            {{ city.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Date of Birth -->
                                <div class="col-md-6">
                                    <label for="editDob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="editDob" v-model="editProfileData.dob">
                                </div>

                                <!-- Gender -->
                                <div class="col-md-6">
                                    <label for="editGender" class="form-label">Gender</label>
                                    <select class="form-select" id="editGender" v-model="editProfileData.gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <!-- Civil Status -->
                                <div class="col-md-6">
                                    <label for="editCivilStatus" class="form-label">Civil Status</label>
                                    <select class="form-select" id="editCivilStatus" v-model="editProfileData.civilStatus">
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="editCity" class="form-label">City</label>
                                    <input type="text" class="form-control" id="editGraduationYear" v-model="editProfileData.graduationYear">
                                </div>

                                <div class="col-md-6">
                                    <label for="editGraduationYear" class="form-label">Year Graduated</label>
                                    <input type="text" class="form-control" id="editGraduationYear" v-model="editProfileData.graduationYear">
                                </div>

                                <!-- Year Graduated -->
                                <div class="col-md-6">
                                    <label for="editGraduationYear" class="form-label">Year Graduated</label>
                                    <input type="number" class="form-control" id="editGraduationYear" v-model="editProfileData.graduationYear">
                                </div>

                                <!-- College -->
                                <div class="col-md-6">
                                    <label for="editCollege" class="form-label">Campus</label>
                                    <select class="form-select" id="editCollege" v-model="editProfileData.college">
                                        <option value="">Select Campus</option>
                                        <option value="LSPU - San Pablo">LSPU - San Pablo</option>
                                        <option value="LSPU - Los Baños">LSPU - Los Baños</option>
                                        <option value="LSPU - Siniloan">LSPU - Siniloan</option>
                                        <option value="LSPU - Sta. Cruz">LSPU - Sta. Cruz</option>
                                    </select>
                                </div>

                                <!-- Course/Degree (Updated to Select) -->
                                <div class="col-md-6">
                                    <label for="editDegree" class="form-label">Course</label>
                                    <select class="form-select" id="editDegree" v-model="editProfileData.degree">
                                        <option value="">Select Course</option>
                                        <option value="BS Information Technology">BS Information Technology</option>
                                        <option value="BS Computer Science">BS Computer Science</option>
                                        <option value="BS Business Administration">BS Business Administration</option>
                                        <option value="BS Criminology">BS Criminology</option>
                                        <option value="BS Education">BS Education</option>
                                        <option value="BS Hospitality Management">BS Hospitality Management</option>
                                    </select>
                                </div>

                                <!-- Status Prior to Graduation -->
                                <div class="col-md-6">
                                    <label for="editStatusPrior" class="form-label">Status Prior to Graduation</label>
                                    <select class="form-select" id="editStatusPrior" v-model="editProfileData.statusPrior">
                                        <option value="">Select Status</option>
                                        <option value="Studying">Studying</option>
                                        <option value="Working">Working</option>
                                        <option value="Unemployed">Unemployed</option>
                                    </select>
                                </div>

                                <!-- Status of Employment (Completed) -->
                                <div class="col-md-6">
                                    <label for="editEmploymentStatus" class="form-label">Status of Employment</label>
                                    <select class="form-select" id="editEmploymentStatus" v-model="editProfileData.employmentStatus">
                                        <option value="">Select Employment Status</option>
                                        <option value="Employed - Full Time">Employed - Full Time</option>
                                        <option value="Employed - Part Time">Employed - Part Time</option>
                                        <option value="Self-Employed">Self-Employed</option>
                                        <option value="Unemployed - Looking for Work">Unemployed - Looking for Work</option>
                                        <option value="Unemployed - Not Looking">Unemployed - Not Looking</option>
                                    </select>
                                </div>

                            </div> <!-- /.row -->
                        </div> <!-- /.edit-modal-section -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="updateProfile">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skills Edit Modal -->
        <div class="modal fade" id="skillsModal" tabindex="-1" aria-labelledby="skillsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="skillsModalLabel">Edit Skills</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newSkill" class="form-label">Skill Name</label>
                            <input type="text" class="form-control" id="newSkill" v-model="newSkill.name" placeholder="Enter skill name">
                        </div>
                        <button class="btn btn-primary mb-3" @click="addSkill">
                            <i class="fas fa-plus me-1"></i>Add Skill
                        </button>
                        <div class="skills-list">
                            <div v-for="(skill, index) in editSkillsData" :key="index" class="skill-tag mb-2">
                                {{ skill.name }}
                                <span v-if="skill.certificate" class="certificate-badge">
                                    <i class="fas fa-certificate"></i>
                                    <span class="certificate-tooltip">Certificate: {{ skill.certificate }}</span>
                                </span>
                                <span @click="removeEditSkill(index)" class="remove-skill">
                                    <i class="fas fa-times"></i>
                                </span>
                            </div>
                            <div v-if="editSkillsData.length === 0" class="text-muted">
                                No skills added yet
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="saveSkills">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Education Edit Modal -->
        <div class="modal fade" id="educationModal" tabindex="-1" aria-labelledby="educationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="educationModalLabel">{{ editingEducationIndex === null ? 'Add' : 'Edit' }} Education</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="eduDegree" class="form-label">Course</label>
                                <input type="text" class="form-control" id="eduDegree" v-model="editEducationData.degree">
                            </div>
                            <div class="col-md-6">
                                <label for="eduSchool" class="form-label">School/University</label>
                                <input type="text" class="form-control" id="eduSchool" v-model="editEducationData.school">
                            </div>
                            <div class="col-md-6">
                                <label for="eduStartDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="eduStartDate" v-model="editEducationData.startDate">
                            </div>
                            <div class="col-md-6">
                                <label for="eduEndDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="eduEndDate" v-model="editEducationData.endDate" :disabled="editEducationData.current">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="eduCurrent" v-model="editEducationData.current">
                                    <label class="form-check-label" for="eduCurrent">
                                        I currently study here
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="saveEducation">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Experience Edit Modal -->
        <div class="modal fade" id="experienceModal" tabindex="-1" aria-labelledby="experienceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="experienceModalLabel">
                            {{ editingExperienceIndex === null ? 'Add' : 'Edit' }} Work Experience
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="expTitle" class="form-label">Job Title</label>
                                <input type="text" class="form-control" id="expTitle" v-model="editExperienceData.title">
                            </div>

                            <div class="col-md-6">
                                <label for="expCompany" class="form-label">Company</label>
                                <input type="text" class="form-control" id="expCompany" v-model="editExperienceData.company">
                            </div>

                            <div class="col-md-6">
                                <label for="expCompanyAddress" class="form-label">Company Address</label>
                                <input type="text" class="form-control" id="expCompanyAddress" v-model="editExperienceData.companyAddress">
                            </div>

                            <div class="col-md-6">
                                <label for="expSector" class="form-label">Sector</label>
                                <select class="form-select" id="expSector" v-model="editExperienceData.sector">
                                    <option value="">Select Sector</option>
                                    <option value="Private">Private</option>
                                    <option value="Government">Government</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="expLocation" class="form-label">Location</label>
                                <select class="form-select" id="expLocation" v-model="editExperienceData.location">
                                    <option value="">Select Location</option>
                                    <option value="Local">Local</option>
                                    <option value="Abroad">Abroad</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="expSalary" class="form-label">Salary</label>
                                <input type="text" class="form-control" id="expSalary" v-model="editExperienceData.salary">
                            </div>

                            <div class="col-md-6">
                                <label for="expTypeOfEmployment" class="form-label">Student Employment Type</label>
                                <select class="form-select" id="expTypeOfEmployment" v-model="editExperienceData.employmentType">
                                    <option value="">Select Employment Type</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Contractual">Contractual</option>
                                    <option value="Probationary">Probationary</option>
                                    <option value="Self-Employed">Self-Employed</option>
                                    <option value="Freelance">Freelance</option>
                                    <option value="Unemployed">Unemployed</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="expIndustry" class="form-label">Type of Industry/Company</label>
                                <select class="form-select" id="expIndustry" v-model="editExperienceData.industry">
                                    <option value="">Select Industry</option>
                                    <option value="IT Industry">IT Industry</option>
                                    <option value="Education">Education</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Manufacturing">Manufacturing</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Retail">Retail</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="expStartDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="expStartDate" v-model="editExperienceData.startDate">
                            </div>

                            <div class="col-md-6">
                                <label for="expEndDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="expEndDate" v-model="editExperienceData.endDate" :disabled="editExperienceData.current">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="expCurrent" v-model="editExperienceData.current">
                                    <label class="form-check-label" for="expCurrent">I currently work here</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="expDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="expDescription" rows="3" v-model="editExperienceData.description"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="saveExperience">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resume Modal -->
        <div class="modal fade" id="resumeModal" tabindex="-1" aria-labelledby="resumeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resumeModalLabel">My Resume</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="profile.resume" class="text-center">
                            <iframe :src="profile.resume.url" style="width: 100%; height: 500px; border: 1px solid #ddd;"></iframe>
                        </div>
                        <div v-else class="text-center py-4">
                            <i class="fas fa-file-pdf fa-4x text-muted mb-3"></i>
                            <p>No resume uploaded yet</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="uploadResume">
                            <i class="fas fa-upload me-2"></i>Upload New Resume
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <footer class="fixed-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p class="mb-0">© 2023 LSPU EIS Job Portal. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vue.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.2.47/vue.global.min.js"></script>
    <!-- SweetAlert2 for confirmation dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/my_profile.js"></script>
</body>

</html>