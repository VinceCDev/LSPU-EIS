<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
    header('Location: login.php');
    exit();
}
// Fetch user_id from user table using email
require_once 'conn/db_conn.php';
$db = Database::getInstance()->getConnection();
$user_id = null;
$email = $_SESSION['email'];
$stmt = $db->prepare('SELECT user_id FROM user WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();
$_SESSION['user_id'] = $user_id;
?>
<!DOCTYPE html>
<html lang="en" id="app">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link rel="stylesheet" href="css/home.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-segoe pt-[70px] transition-colors duration-200" id="app" v-cloak>
    <div>
        <!-- Loading Spinner Overlay -->
        <div v-if="loading" class="fixed inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-[9999]">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500" role="status" aria-live="polite"></div>
            <span class="sr-only">Loading...</span>
        </div>
        <!-- Toast Notification Area -->
        <div class="fixed top-4 right-4 z-[100] space-y-3 w-full max-w-xs" aria-live="polite">
            <transition-group 
                enter-active-class="transform transition duration-300 ease-out"
                enter-from-class="translate-x-20 opacity-0"
                enter-to-class="translate-x-0 opacity-100"
                leave-active-class="transform transition duration-200 ease-in"
                leave-from-class="translate-x-0 opacity-100"
                leave-to-class="translate-x-20 opacity-0"
            >
                <div v-for="notification in notifications" :key="notification.id" 
                     :class="{
                         'bg-green-100 border-green-500 text-green-700': notification.type === 'success',
                         'bg-blue-100 border-blue-500 text-blue-700': notification.type === 'info',
                         'bg-red-100 border-red-500 text-red-700': notification.type === 'error',
                         'dark:bg-green-900/80 dark:border-green-700 dark:text-green-200': notification.type === 'success' && darkMode,
                         'dark:bg-blue-900/80 dark:border-blue-700 dark:text-blue-200': notification.type === 'info' && darkMode,
                         'dark:bg-red-900/80 dark:border-red-700 dark:text-red-200': notification.type === 'error' && darkMode
                     }" 
                     class="border-l-4 p-4 rounded-lg shadow-lg relative pr-8 flex items-start animate-slide-in" role="alert" tabindex="0">
                    <div class="flex-shrink-0 mt-1">
                            <i v-if="notification.type === 'success'" class="fas fa-check-circle text-lg"></i>
                            <i v-if="notification.type === 'info'" class="fas fa-info-circle text-lg"></i>
                            <i v-if="notification.type === 'error'" class="fas fa-exclamation-circle text-lg"></i>
                        </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-bold capitalize">{{ notification.type }}</h3>
                            <p class="text-sm mt-1">{{ notification.message }}</p>
                    </div>
                    <button @click="notifications = notifications.filter(n => n.id !== notification.id)" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" aria-label="Close notification">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </transition-group>
        </div>
        <!-- Multi-Step Application Modal -->
        <transition enter-active-class="modal-enter-active" enter-from-class="modal-enter-from" enter-to-class="modal-enter-to" leave-active-class="modal-leave-active" leave-from-class="modal-leave-from" leave-to-class="modal-leave-to">
            <div v-if="showApplicationModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeApplicationModal">
                        <i class="fas fa-times"></i>
                    </button>
                    <!-- Stepper -->
                    <div class="flex justify-center mb-6 gap-4">
                        <template v-for="step in 5">
                            <div :key="step" class="flex flex-col items-center">
                                <div :class="['w-10 h-10 flex items-center justify-center rounded-full border-2',
                                    applicationStep === step ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-400 dark:border-gray-500',
                                    'font-bold text-lg transition']">
                                    {{ step }}
                                </div>
                                <span class="text-xs mt-1" v-if="step === 1">Personal</span>
                                <span class="text-xs mt-1" v-else-if="step === 2">Education</span>
                                <span class="text-xs mt-1" v-else-if="step === 3">Skills</span>
                                <span class="text-xs mt-1" v-else-if="step === 4">Experience</span>
                                <span class="text-xs mt-1" v-else-if="step === 5">Resume</span>
                            </div>
                            <div v-if="step < 5" class="w-8 h-1 bg-gray-300 dark:bg-gray-600 mt-4"></div>
                        </template>
                    </div>
                    <!-- Step Content -->
                    <div v-if="applicationStep === 1">
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2"><i class="fas fa-user-circle"></i> Personal Details</h3>
                        <div v-if="applicationPersonal">
                            <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-4 mb-2 border border-blue-100 dark:border-blue-800">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><strong>Name:</strong> {{ applicationPersonal.first_name }} {{ applicationPersonal.middle_name }} {{ applicationPersonal.last_name }}</div>
                                    <div><strong>Email:</strong> {{ applicationPersonal.email }}</div>
                                    <div><strong>Contact:</strong> {{ applicationPersonal.contact }}</div>
                                    <div><strong>Birthdate:</strong> {{ applicationPersonal.birthdate }}</div>
                                    <div><strong>Gender:</strong> {{ applicationPersonal.gender }}</div>
                                    <div><strong>Civil Status:</strong> {{ applicationPersonal.civil_status }}</div>
                                    <div><strong>College:</strong> {{ applicationPersonal.college }}</div>
                                    <div><strong>Course:</strong> {{ applicationPersonal.course }}</div>
                                    <div><strong>Year Graduated:</strong> {{ applicationPersonal.year_graduated }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500">Loading...</div>
                    </div>
                    <div v-else-if="applicationStep === 2">
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2"><i class="fas fa-graduation-cap"></i> Education</h3>
                        <div v-if="applicationEducation && applicationEducation.length">
                            <div class="space-y-3">
                                <div v-for="edu in applicationEducation" :key="edu.education_id" class="bg-white dark:bg-gray-900 rounded-xl shadow p-4 border border-blue-100 dark:border-blue-800">
                                    <div class="flex items-center gap-2 mb-2"><i class="fas fa-university text-blue-500"></i> <span class="font-semibold">{{ edu.school }}</span></div>
                                    <div class="text-sm text-gray-700 dark:text-gray-300">Degree: <span class="font-semibold">{{ edu.degree }}</span></div>
                                    <div class="text-xs text-gray-500">From: {{ edu.start_date }} <span v-if="edu.current">- Present</span><span v-else> to {{ edu.end_date }}</span></div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500">No education information.</div>
                    </div>
                    <div v-else-if="applicationStep === 3">
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2"><i class="fas fa-lightbulb"></i> Skills</h3>
                        <div v-if="applicationSkills && applicationSkills.length">
                            <div class="flex flex-wrap gap-2">
                                <div v-for="skill in applicationSkills" :key="skill.skill_id" class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-4 py-2 rounded-full flex items-center gap-2 shadow border border-blue-200 dark:border-blue-800">
                                    <i class="fas fa-check-circle"></i> {{ skill.name }}
                                    <span v-if="skill.certificate" class="ml-1 text-xs bg-green-200 dark:bg-green-700 text-green-800 dark:text-green-100 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-certificate"></i> {{ skill.certificate }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500">No skills listed.</div>
                    </div>
                    <div v-else-if="applicationStep === 4">
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2"><i class="fas fa-briefcase"></i> Work Experience</h3>
                        <div v-if="applicationExperience && applicationExperience.length">
                            <div class="space-y-3">
                                <div v-for="exp in applicationExperience" :key="exp.experience_id" class="bg-white dark:bg-gray-900 rounded-xl shadow p-4 border border-blue-100 dark:border-blue-800">
                                    <div class="flex items-center gap-2 mb-2"><i class="fas fa-building text-blue-500"></i> <span class="font-semibold">{{ exp.company }}</span></div>
                                    <div class="text-sm text-gray-700 dark:text-gray-300">Title: <span class="font-semibold">{{ exp.title }}</span></div>
                                    <div class="text-xs text-gray-500">From: {{ exp.start_date }} <span v-if="exp.current">- Present</span><span v-else> to {{ exp.end_date }}</span></div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ exp.description }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500">No work experience listed.</div>
                    </div>
                    <div v-else-if="applicationStep === 5">
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2"><i class="fas fa-file-pdf"></i> Resume Upload</h3>
                        <div v-if="applicationResume && applicationResume.file_name && !applicationResume.file">
                            <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-4 border border-blue-100 dark:border-blue-800 flex items-center gap-4">
                                <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                                <div>
                                    <a :href="'uploads/resumes/' + applicationResume.file_name" target="_blank" class="underline hover:text-blue-700 dark:hover:text-blue-300">{{ applicationResume.file_name }}</a>
                                    <span class="ml-2 text-xs text-gray-500">(Uploaded: {{ applicationResume.uploaded_at }})</span>
                                    <div class="mt-2">
                                        <iframe v-if="applicationResume.file_name.endsWith('.pdf')" :src="'uploads/resumes/' + applicationResume.file_name" style="width:200px;height:120px;" class="border rounded"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="applicationResume && applicationResume.file">
                            <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-4 border border-blue-100 dark:border-blue-800 flex items-center gap-4">
                                <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300 font-semibold">{{ applicationResume.file_name }}</span>
                                    <span class="ml-2 text-xs text-gray-500">(To be uploaded)</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500">No resume uploaded.</div>
                        <div class="mt-4">
                            <input type="file" @change="handleApplicationResumeUpload" accept="application/pdf" class="block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        </div>
                    </div>
                    <!-- Stepper Navigation -->
                    <div class="flex justify-between mt-8">
                        <button v-if="applicationStep > 1" @click="applicationStep--" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Back</button>
                        <div class="flex-1"></div>
                        <button v-if="applicationStep < 5" @click="applicationStep++" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Next</button>
                        <button v-else @click="submitApplication" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Submit Application</button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Logout Confirmation Modal - Responsive positioning -->
        <div v-if="showLogoutModal" class="fixed inset-0 z-[100] flex items-center justify-center md:items-start md:justify-center bg-black bg-opacity-50">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showLogoutModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-4 md:mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Logout</h3>
                    <button @click="showLogoutModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to logout?</p>
                <div class="flex justify-end gap-3">
                    <button @click="showLogoutModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                    <button @click="logout" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">Logout</button>
                </div>
            </div>
        </div>
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-200 to-white dark:from-blue-900 dark:to-gray-800 shadow-sm fixed top-0 left-0 right-0 z-50 h-[70px] transition-colors duration-200">
        <div class="container mx-auto h-full px-4">
            <nav class="flex items-center justify-between h-full">
                <div class="flex items-center">
                    <img src="images/alumni.png" alt="LSPU Logo" class="h-[60px] w-auto">
                    <span class="text-2xl font-bold text-gray-800 dark:text-white ml-2">LSPU</span>
                    <span class="text-2xl font-light text-blue-700 dark:text-blue-300">EIS</span>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Mobile Menu Button -->
                    <button class="md:hidden text-gray-600 dark:text-gray-300 focus:outline-none" @click="mobileMenuOpen = !mobileMenuOpen">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="home" class="text-blue-700 dark:text-blue-300 font-bold border-b-4 border-blue-700 dark:border-blue-300 pb-1 bg-blue-50 dark:bg-blue-900 rounded-t transition-all duration-200 px-2">Home</a>
                        <a href="my_application" class="text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:border-b-4 hover:border-blue-400 dark:hover:border-blue-300 pb-1 transition-all duration-200 px-2">My Applications</a>
                        <a href="notification" class="relative inline-flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:border-b-4 hover:border-blue-400 dark:hover:border-blue-300 pb-1 transition-all duration-200 px-2">
                            Notifications
                            <span 
                                v-if="unreadNotifications > 0" 
                                class="absolute -top-2 -right-2 inline-flex items-center justify-center h-5 w-5 text-xs font-bold text-white bg-red-500 rounded-full transform transition-transform hover:scale-110"
                            >
                                {{ unreadNotifications > 99 ? '99+' : unreadNotifications }}
                            </span>
                        </a>
                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button class="flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 focus:outline-none" @click="profileDropdownOpen = !profileDropdownOpen">
                                Profile <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div v-show="profileDropdownOpen" @mouseleave="profileDropdownOpen = false" class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-700 rounded-md shadow-lg overflow-hidden z-50">
                                <!-- Profile section -->
                                <div class="px-4 py-3 flex items-center">
                                    <img v-if="profilePicData && profilePicData.file_name" :src="'uploads/profile_picture/' + profilePicData.file_name" alt="Profile" class="w-8 h-8 rounded-full mr-2">
                                    <img v-else src="images/alumni.png" alt="Profile" class="w-8 h-8 rounded-full mr-2">
                                    <span class="dark:text-white">{{ profile.name || 'Alumni' }}</span>
                                </div>
                                <div class="px-4"><div class="border-t border-gray-200 dark:border-gray-600"></div></div>
                                <a href="my_profile" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                                    <i class="fas fa-user mr-2"></i> View Profile
                                </a>
                                <a href="message" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                                    <i class="fas fa-envelope mr-2"></i> Messages
                                </a>
                                <a href="forgot_password" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                                    <i class="fas fa-key mr-2"></i> Forgot Password
                                </a>
                                <a href="employer_login" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                                    <i class="fas fa-briefcase mr-2"></i> Employer Site
                                </a>
                                <div class="px-4"><div class="border-t border-gray-200 dark:border-gray-600"></div></div>
                                <div class="px-4 py-2 flex items-center justify-between">
                                    <div class="flex items-center text-gray-700 dark:text-gray-200">
                                        <i class="fas fa-moon mr-2"></i>
                                        <span>Dark Mode</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" v-model="darkMode">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="px-4"><div class="border-t border-gray-200 dark:border-gray-600"></div></div>
                                <a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-red-100 hover:text-red-700 dark:hover:bg-blue-500 transition-colors duration-200" @click.prevent="showLogoutModal = true">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Mobile Menu -->
        <div v-show="mobileMenuOpen" class="md:hidden bg-white dark:bg-gray-800 shadow-lg absolute top-[70px] left-0 right-0 transition-colors duration-200 z-40">
                <div class="container mx-auto px-4 py-3">
                    <a href="home" class="block py-2 text-blue-700 dark:text-blue-300 hover:text-lspu-blue dark:hover:text-blue-300 font-bold">Home</a>
                    <a href="my_application" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-300">My Applications</a>
                    <a href="notification" class="relative block py-2 text-gray-600 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-300">
                        Notifications
                        <span v-if="unreadNotifications > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                            {{ unreadNotifications }}
                        </span>
                    </a>
                    
                    <!-- Mobile Profile Dropdown -->
                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700 mt-2">
                        <button @click="mobileProfileDropdownOpen = !mobileProfileDropdownOpen" class="flex items-center justify-between w-full py-2 text-gray-600 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-300">
                            <div class="flex items-center">
                                <img v-if="profilePicData.file_name" :src="'uploads/profile_picture/' + profilePicData.file_name" alt="Profile" class="w-8 h-8 rounded-full mr-2">
                                <img v-else src="images/alumni.png" alt="Profile" class="w-8 h-8 rounded-full mr-2">
                                <span class="dark:text-white">{{ profile.name || 'Alumni' }}</span>
                            </div>
                            <i :class="['fas', mobileProfileDropdownOpen ? 'fa-chevron-up' : 'fa-chevron-down', 'text-xs']"></i>
                        </button>
                        
                        <!-- Mobile Profile Dropdown Content -->
                        <div v-show="mobileProfileDropdownOpen" class="pl-6 mt-2 space-y-2">
                            <a href="my_profile" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-blue-300">
                                <i class="fas fa-user mr-2"></i> View Profile
                            </a>
                            <a href="message" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-blue-300">
                                <i class="fas fa-envelope mr-2"></i> Messages
                            </a>
                            <a href="forgot_password" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-blue-300">
                                <i class="fas fa-key mr-2"></i> Forgot Password
                            </a>
                            <a href="employer_login" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-blue-300">
                                <i class="fas fa-briefcase mr-2"></i> Employer Site
                            </a>
                            <div class="py-2 flex items-center justify-between">
                                <div class="flex items-center text-gray-600 dark:text-gray-300 mr-3">
                                    <i class="fas fa-moon mr-2"></i>
                                    <span>Dark Mode</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" v-model="darkMode">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <a href="#" @click.prevent="showLogoutModal = true" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-blue-300">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

    <!-- Search Section -->
    <section class="bg-gray-100 dark:bg-gray-900 py-6 transition-colors duration-200">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue dark:focus:ring-blue-300 dark:focus:border-blue-300 bg-white dark:bg-gray-700 dark:text-white transition-all duration-200 shadow-sm" 
                               placeholder="Search for jobs..." 
                               v-model="searchQuery">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button class="p-1 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button class="w-full md:w-auto px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-lspu-blue hover:text-white dark:hover:bg-blue-600 focus:outline-none transition-colors duration-200 flex items-center justify-center" @click="filtersOpen = !filtersOpen">
                    <i class="fas fa-sliders-h mr-2"></i> Filters
                </button>
            </div>
            <!-- Advanced Filters -->
            <div v-show="filtersOpen" class="mt-4 bg-white dark:bg-gray-700 rounded-lg shadow p-4 transition-all duration-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-lspu-blue focus:border-lspu-blue dark:focus:ring-blue-300 dark:focus:border-blue-300 bg-white dark:bg-gray-700 dark:text-white" v-model="selectedLocation">
                            <option value="">All Locations</option>
                            <option v-for="location in locations" :value="location">{{ location }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Job Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-lspu-blue focus:border-lspu-blue dark:focus:ring-blue-300 dark:focus:border-blue-300 bg-white dark:bg-gray-700 dark:text-white" v-model="selectedJobType">
                            <option value="">All Types</option>
                            <option v-for="type in jobTypes" :value="type">{{ type }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary Range</label>
                        <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-lspu-blue focus:border-lspu-blue dark:focus:ring-blue-300 dark:focus:border-blue-300 bg-white dark:bg-gray-700 dark:text-white" v-model="selectedSalary">
                            <option value="">Any Salary</option>
                            <option v-for="salary in salaryRanges" :value="salary.value">{{ salary.label }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Job Listings -->
    <section class="min-h-[calc(100vh-180px)] bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <!-- Added pb-20 for footer space and dark mode bg -->
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-400 mb-6 tracking-wide">Available Jobs</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-black-100 shadow-md hover:shadow-xl hover:border-blue-400 dark:hover:border-blue-500 hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 cursor-pointer group relative overflow-hidden" v-for="(job, index) in filteredJobs" :key="job.id" @click="showJobDetails(job)">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-blue-400 to-blue-300 dark:from-blue-900 dark:via-blue-800 dark:to-blue-700 opacity-80"></div>
                    <div class="p-5 h-full flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                            <img 
                                :src="job.logoUrl || job.companyDetails.logoUrl || 'images/logo.png'" 
                                :alt="job.company + ' logo'"
                                class="w-12 h-12 min-w-[48px] object-cover border border-gray-200 dark:border-gray-600 rounded-full mr-3
                                    bg-white dark:bg-gray-700 p-1 shadow-sm"
                                loading="lazy"
                                @error="handleLogoError"
                            >
                                <div>
                                    <h3 class="font-bold text-lg text-blue-700 dark:text-blue-300 group-hover:underline">{{ job.title }}</h3>
                                    <p class="text-sm text-blue-500 dark:text-blue-200 font-semibold">{{ job.company }}</p>
                                </div>
                            </div>
                            <i class="fas fa-bookmark text-gray-400 hover:text-red-500 dark:hover:text-red-400" :class="{ 'text-red-500 dark:text-red-400': job.saved }" @click.stop="toggleSave(job)"></i>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="inline-flex items-center px-3 py-1 bg-blue-50 dark:bg-blue-900 rounded-full text-sm text-blue-700 dark:text-blue-200">
                                <i class="fas fa-map-marker-alt mr-1"></i> {{ job.location }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-briefcase mr-1"></i> {{ job.type }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-money-bill-wave mr-1"></i> {{ job.salary }}
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 flex-grow">{{ job.description }}</p>
                        <div class="mt-4 pt-2 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ postedDays(job.created_at) }}</span>
                            <span class="inline-block px-2 py-1 rounded text-xs font-semibold bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200">View Details</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="filteredJobs.length === 0" class="text-center py-12">
                <i class="fas fa-briefcase text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h4 class="text-xl font-medium text-gray-600 dark:text-gray-400 mb-2">No jobs found</h4>
                <p class="text-gray-500 dark:text-gray-500">Try adjusting your filters to find more opportunities.</p>
            </div>
        </div>
    </section>

    <!-- Job Details Sidebar -->
    <div :class="['fixed inset-0 z-40 transition-opacity duration-200', showDetails ? 'bg-black bg-opacity-50' : 'pointer-events-none opacity-0']" @click="hideJobDetails" id="overlay"></div>
    <div :class="['fixed top-0 right-0 h-full w-full max-w-[600px] z-50 transform transition-transform duration-500 overflow-y-auto', showDetails ? 'translate-x-0' : 'translate-x-full', 'backdrop-blur', darkMode ? 'bg-gray-900 bg-opacity-90' : 'bg-white bg-opacity-85']" id="jobDetailsSidebar">
        <div class="relative p-6 min-h-full flex flex-col">
            <button class="absolute top-4 right-4 text-gray-500 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 text-2xl z-10" @click="hideJobDetails">
                <i class="fas fa-times"></i>
            </button>
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center">
                    <img 
                    :src="selectedJob.logoUrl || selectedJob.companyDetails?.logoUrl || 'images/logo.png'"
                    :alt="selectedJob.company + ' logo'"
                    class="w-12 h-12 min-w-[48px] object-contain border border-gray-200 dark:border-gray-600 rounded-full mr-4
                            bg-white dark:bg-gray-700 p-1 shadow-sm"
                    loading="lazy"
                    @error="handleLogoError"
                    >
                    <div>
                        <h3 class="text-2xl font-bold text-blue-700 dark:text-blue-300 mb-1">{{ selectedJob.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">{{ selectedJob.company }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-6">
                <span class="inline-flex items-center px-3 py-1 bg-blue-50 dark:bg-blue-900 rounded-full text-sm text-blue-700 dark:text-blue-200">
                    <i class="fas fa-map-marker-alt mr-1"></i> {{ selectedJob.location }}
                </span>
                <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                    <i class="fas fa-briefcase mr-1"></i> {{ selectedJob.type }}
                </span>
                <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                    <i class="fas fa-money-bill-wave mr-1"></i> {{ selectedJob.salary }}
                </span>
            </div>
            <!-- Leaflet Map -->
            <div class="mb-6 rounded-lg overflow-hidden shadow" style="height: 220px;">
                <div id="job-map" style="height: 220px; width: 100%;"></div>
            </div>
            <div class="flex gap-3 mb-6">
                <button class="flex-1 bg-blue-600 hover:bg-blue-700 dark:hover:bg-blue-600 text-white py-2.5 rounded-lg font-semibold transition flex items-center justify-center text-lg shadow"
                    @click="openApplicationModal(selectedJob.id)">
                    <i class="fas fa-paper-plane mr-2"></i> Apply Now
                </button>
            </div>
            <div class="mb-6">
                <h5 class="text-lg font-bold text-blue-700 dark:text-blue-300 mb-3">Job Description</h5>
                <p class="text-gray-700 dark:text-gray-200">{{ selectedJob.description }}</p>
            </div>
            <div class="mb-6">
                <h5 class="text-lg font-bold text-blue-700 dark:text-blue-300 mb-3">Job Requirements</h5>
                <ul class="list-disc pl-5 text-gray-700 dark:text-gray-200">
                    <li>{{ selectedJob.requirements }}</li>
                </ul>
            </div>
            <div class="mb-6">
                <h5 class="text-lg font-bold text-blue-700 dark:text-blue-300 mb-3">Qualifications</h5>
                <ul class="list-disc pl-5 text-gray-700 dark:text-gray-200">
                    <li v-for="(qualification, qIndex) in selectedJob.qualifications" :key="qIndex">
                        {{ qualification }}
                    </li>
                </ul>
            </div>
            <div v-if="selectedJob.questions && selectedJob.questions.length" class="mb-6">
                <h5 class="text-lg font-bold text-blue-700 dark:text-blue-300 mb-3">Employer Questions</h5>
                <div class="space-y-3" id="employerQuestions">
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden" v-for="(question, eqIndex) in selectedJob.questions" :key="eqIndex">
                        <div class="w-full px-4 py-3 text-left bg-gray-50 dark:bg-gray-700 flex justify-between items-center transition-colors duration-200">
                            <span class="text-gray-800 dark:text-gray-300">{{ question.text }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Company Details Section -->
            <div v-if="selectedJob.companyDetails" class="mb-6 border border-blue-200 dark:border-blue-700 rounded-lg bg-blue-50 dark:bg-blue-900/30 p-4">
                <div class="flex items-center mb-3">
                    <img v-if="selectedJob.companyDetails.company_logo" :src="'uploads/logos/' + selectedJob.companyDetails.company_logo" alt="Logo" class="w-14 h-14 rounded-full object-cover border-2 border-blue-300 dark:border-blue-700 mr-3">
                    <div>
                        <h5 class="text-xl font-bold text-blue-700 dark:text-blue-300 mb-1">{{ selectedJob.companyDetails.company_name }}</h5>
                        <div class="text-sm text-gray-700 dark:text-gray-200">{{ selectedJob.companyDetails.nature_of_business }}</div>
                    </div>
                </div>
                <div class="space-y-2 mt-2">
                    <div v-if="selectedJob.companyDetails.contact_email">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Email:</span>
                        <a :href="'mailto:' + selectedJob.companyDetails.contact_email" class="text-blue-700 dark:text-blue-300 hover:underline inline-flex items-center ml-1">
                            <i class="fas fa-envelope mr-1"></i>{{ selectedJob.companyDetails.contact_email }}
                        </a>
                    </div>
                    <div v-if="selectedJob.companyDetails.contact_number">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Contact:</span>
                        <a :href="'tel:' + selectedJob.companyDetails.contact_number" class="text-blue-700 dark:text-blue-300 hover:underline inline-flex items-center ml-1">
                            <i class="fas fa-phone mr-1"></i>{{ selectedJob.companyDetails.contact_number }}
                        </a>
                    </div>
                    <div v-if="selectedJob.companyDetails.company_location">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Address:</span>
                        <span class="text-gray-800 dark:text-gray-100 ml-1">{{ selectedJob.companyDetails.company_location }}</span>
                    </div>
                    <div v-if="selectedJob.companyDetails.industry_type">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Industry:</span>
                        <span class="text-gray-800 dark:text-gray-100 ml-1">{{ selectedJob.companyDetails.industry_type }}</span>
                    </div>
                    <div v-if="selectedJob.companyDetails.accreditation_status">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Accreditation:</span>
                        <span class="text-gray-800 dark:text-gray-100 ml-1">{{ selectedJob.companyDetails.accreditation_status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Leaflet CSS/JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    </div> <!-- Close #app div -->

    <!-- Footer -->
        <footer class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-2 transition-colors duration-200">
        <div class="container mx-auto px-4 text-center text-gray-500 dark:text-gray-400 text-sm">
            &copy; 2025 LSPU EIS. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.prod.js"></script>
    <script>
       window.USER_ID = <?php echo json_encode($user_id); ?>;

       (function() {
            try {
            var dark = localStorage.getItem('darkMode');
            if (dark === 'true' || (dark === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
            } catch(e){}
        })();
    </script>
    <script src="js/home.js"></script>
</body>
</html>