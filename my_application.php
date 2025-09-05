<?php
session_start();
if (isset($_GET['job_id']) && is_numeric($_GET['job_id'])) {
    $_SESSION['highlight_application_job_id'] = $_GET['job_id'];
}

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link rel="stylesheet" href="css/my_application.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans pt-[70px] transition-colors duration-200" id="app" v-cloak>
    <!-- Add this modal code right after the opening <body> tag -->
    <div v-if="showWelcomeModal" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black bg-opacity-70">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-5 flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-graduation-cap mr-3"></i> Welcome to LSPU Alumni Portal!
                </h2>
                <button @click="closeWelcomeModal" class="text-white hover:text-blue-200 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Content with carousel -->
            <div class="flex-1 overflow-y-auto p-6">
                <!-- Carousel indicators -->
                <div class="flex justify-center mb-6">
                    <div v-for="(slide, index) in welcomeSlides" :key="index" 
                        :class="['w-3 h-3 rounded-full mx-1 cursor-pointer', 
                                currentWelcomeSlide === index ? 'bg-blue-600' : 'bg-gray-300']"
                        @click="currentWelcomeSlide = index">
                    </div>
                </div>
                
                <!-- Slide 1: Introduction -->
                <div v-if="currentWelcomeSlide === 0" class="text-center">
                    <div class="text-blue-500 text-6xl mb-6">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Welcome, Alumni!</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        We're excited to have you here. This portal connects you with job opportunities, 
                        fellow alumni, and valuable resources from LSPU.
                    </p>
                    <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg text-left">
                        <p class="text-blue-700 dark:text-blue-300 flex items-center">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            <span>Take a quick tour to learn how to make the most of your alumni portal.</span>
                        </p>
                    </div>
                </div>
                
                <!-- Slide 2: Navigation -->
                <div v-if="currentWelcomeSlide === 1" class="">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-compass text-blue-500 mr-2"></i> Navigation Guide
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-blue-500 text-2xl mb-2">
                                <i class="fas fa-home"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Home</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Browse and search for job opportunities.</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-blue-500 text-2xl mb-2">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">My Applications</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Track your job applications status.</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-blue-500 text-2xl mb-2">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Notifications</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Get updates on applications and messages.</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-blue-500 text-2xl mb-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Profile</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Manage your personal information.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 3: Job Search -->
                <div v-if="currentWelcomeSlide === 2" class="">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-search text-blue-500 mr-2"></i> Finding Jobs
                    </h3>
                    <div class="space-y-4 mb-6">
                        <div class="flex items-start">
                            <div class="bg-blue-100 dark:bg-blue-900/40 p-2 rounded-full mr-3">
                                <i class="fas fa-search text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 dark:text-white">Search & Filter</h4>
                                <p class="text-gray-600 dark:text-gray-300">Use the search bar and filters to find jobs that match your skills and preferences.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-blue-100 dark:bg-blue-900/40 p-2 rounded-full mr-3">
                                <i class="fas fa-bookmark text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 dark:text-white">Save Jobs</h4>
                                <p class="text-gray-600 dark:text-gray-300">Click the bookmark icon to save interesting jobs for later.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-blue-100 dark:bg-blue-900/40 p-2 rounded-full mr-3">
                                <i class="fas fa-paper-plane text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 dark:text-white">Apply Easily</h4>
                                <p class="text-gray-600 dark:text-gray-300">Use your pre-filled profile information to apply quickly to jobs.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 4: Profile -->
                <div v-if="currentWelcomeSlide === 3" class="">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-user-edit text-blue-500 mr-2"></i> Complete Your Profile
                    </h3>
                    <div class="space-y-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                            <p class="text-blue-700 dark:text-blue-300">
                                <i class="fas fa-info-circle mr-2"></i>
                                A complete profile increases your chances of getting hired by 70%!
                            </p>
                        </div>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Add your education history
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                List your skills and certifications
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Include work experience
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Upload your resume
                            </li>
                        </ul>
                        <div class="mt-4">
                            <a href="my_profile" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-user-edit mr-2"></i> Complete My Profile Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer with navigation -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4 flex justify-between">
                <button v-if="currentWelcomeSlide > 0" 
                        @click="currentWelcomeSlide--" 
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <div v-else></div>
                
                <button v-if="currentWelcomeSlide < welcomeSlides.length - 1" 
                        @click="currentWelcomeSlide++" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Next <i class="fas fa-arrow-right ml-2"></i>
                </button>
                
                <button v-else 
                        @click="closeWelcomeModal" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Finish <i class="fas fa-check ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <div>
        <!-- Loading Spinner Overlay -->
        <div v-if="loading" class="fixed inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-[9999]">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500" role="status" aria-live="polite"></div>
            <span class="sr-only">Loading...</span>
        </div>
        <!-- Notification Area -->
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
                        <h3 class="text-sm font-bold">{{ notification.title }}</h3>
                            <p class="text-sm mt-1">{{ notification.message }}</p>
                    </div>
                    <button @click="removeNotification(notification.id)" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" aria-label="Close notification">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </transition-group>
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
                            <a href="home" class="text-gray-600 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:border-b-4 hover:border-blue-400 dark:hover:border-blue-300 pb-1 transition-all duration-200 px-2">Home</a>
                            <a href="my_application" class="text-blue-700 dark:text-blue-300 font-bold border-b-4 border-blue-700 dark:border-blue-300 pb-1 bg-blue-50 dark:bg-blue-900 rounded-t transition-all duration-200 px-2">My Applications</a>
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
                                
                                <div v-show="profileDropdownOpen" @click.away="profileDropdownOpen = false" class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-700 rounded-md shadow-lg overflow-hidden z-50">
                                    <!-- Profile section -->
                                    <div class="px-4 py-3 flex items-center">
                                        <img v-if="profilePicData && profilePicData.file_name" :src="'uploads/profile_picture/' + profilePicData.file_name" alt="Profile" class="w-8 h-8 rounded-full mr-2">
                                        <img v-else src="images/alumni.png" alt="Profile" class="w-8 h-8 rounded-full mr-2">
                                        <span class="dark:text-white">{{ profile.name || 'Alumni' }}</span>
                                    </div>
                                    
                                    <!-- Divider with padding -->
                                    <div class="px-4"><div class="border-t border-gray-200 dark:border-gray-600"></div></div>
                                    
                                    <!-- Menu items -->
                                    <a href="my_profile" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                                        <i class="fas fa-user mr-2"></i> View Profile
                                    </a>
                                    <a href="message" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                                        <i class="fas fa-envelope mr-2"></i> Messages
                                    </a>
                                    <a href="forgot_password" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                                        <i class="fas fa-key mr-2"></i> Forgot Password
                                    </a>
                                    <a href="#" @click.prevent="openTutorial" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                                        <i class="fas fa-graduation-cap mr-2"></i> Show Tutorial
                                    </a>
                                    <a href="employer_login" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                                        <i class="fas fa-briefcase mr-2"></i> Employer Site
                                    </a>
                                    
                                    <!-- Divider with padding -->
                                    <div class="px-4"><div class="border-t border-gray-200 dark:border-gray-600"></div></div>
                                    
                                    <!-- Dark Mode Toggle with icon -->
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
                                    
                                    <!-- Divider with padding -->
                                    <div class="px-4"><div class="border-t border-gray-200 dark:border-gray-600"></div></div>
                                    
                                    <!-- Logout -->
                                    <a href="#" @click.prevent="showLogoutModal = true" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-red-100 hover:text-red-700 dark:hover:bg-blue-500 transition-colors duration-200">
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
                    <a href="home" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-300">Home</a>
                    <a href="my_application" class="block py-2 text-blue-700 dark:text-blue-300 hover:text-lspu-blue dark:hover:text-blue-300 font-bold">My Applications</a>
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
                            <a href="#" @click.prevent="openTutorial" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-blue-300">
                                <i class="fas fa-graduation-cap mr-2"></i> Show Tutorial
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

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-6 min-h-[calc(100vh-80px)] pb-24 bg-gray-100 dark:bg-gray-900 mb-8 transition-colors duration-200">
            <!-- Tab Navigation -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-2xl font-bold text-blue-700 dark:text-blue-300 tracking-wide">My Applications</h1>
                <div class="flex space-x-2 bg-blue-50 dark:bg-blue-900 rounded-lg p-1 shadow-inner">
                    <button @click="activeTab = 'applied'" :class="{'bg-blue-700 text-white shadow': activeTab === 'applied', 'bg-transparent text-blue-700 dark:text-blue-300': activeTab !== 'applied'}" class="px-5 py-2 rounded-md text-sm font-semibold transition-colors duration-200 focus:outline-none">Jobs Applied</button>
                    <button @click="activeTab = 'saved'" :class="{'bg-blue-700 text-white shadow': activeTab === 'saved', 'bg-transparent text-blue-700 dark:text-blue-300': activeTab !== 'saved'}" class="px-5 py-2 rounded-md text-sm font-semibold transition-colors duration-200 focus:outline-none">Saved Jobs</button>
                </div>
            </div>
            <!-- Applied Jobs Tab -->
            <div v-if="activeTab === 'applied'">
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-700 border-t-transparent"></div>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Loading your applications...</p>
                </div>
                <div v-else-if="appliedJobs.length === 0" class="text-center py-12 rounded-lg">
                    <i class="far fa-folder-open text-4xl text-gray-300 dark:text-gray-500 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">No applications yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">When you apply for jobs, they'll appear here.</p>
                    <a href="home" class="inline-block mt-4 px-4 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-800 transition-colors duration-200 font-semibold shadow">Browse Jobs</a>
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-8">
                    <div v-for="job in appliedJobs" 
                        :key="job.id" 
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-white border border-black-100 shadow-md hover:shadow-xl hover:border-blue-400 dark:hover:border-blue-500 hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 cursor-pointer group relative overflow-hidden transition-all duration-200 hover:shadow-xl hover:border-blue-400 hover:-translate-y-1 cursor-pointer group relative flex flex-col h-full"
                        :class="{'highlight-pulse': shouldHighlightJob(job)}"
                        :data-job-id="job.job_id || job.id">
                        <!-- Status indicator bar -->
                        <div class="w-full h-2 bg-blue-200 dark:bg-blue-900"></div>
                        
                        <!-- Main content container -->
                        <div class="p-6 flex-1 flex flex-col">
                            <!-- Top section with job info and date -->
                            <div class="flex justify-between items-start mb-4 gap-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl font-bold text-blue-700 dark:text-blue-300 group-hover:underline mb-2">{{ job.title }}</h3>
                                    <p class="text-blue-500 dark:text-blue-200 font-semibold mb-2 company">{{ job.company }}</p>
                                </div>
                                <!-- Date in top-right corner with white bg and blue border -->
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-white dark:bg-gray-800 border border-blue-500 text-blue-600 dark:text-blue-300 whitespace-nowrap shrink-0">
                                    Applied {{ formatDate(job.appliedDate) }}
                                </span>
                            </div>

                            <!-- Location (full width) -->
                            <div class="w-full mb-4 text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-sm"></i>
                                <span class="truncate">{{ job.location }}</span>
                            </div>

                            <div class="w-full mb-4 text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-sm"></i>
                                <span :class="{
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': job.application_status === 'Pending',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': job.application_status === 'Interview',
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': job.application_status === 'Hired',
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': job.application_status === 'Rejected',
                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200': true
                                }" class="px-3 py-1 rounded-full text-xs font-medium">
                                    {{ job.application_status }}
                                </span>
                            </div>

                            <!-- Description (middle section) -->
                            <p class="text-gray-600 dark:text-gray-400 mb-6 line-clamp-3 text-sm flex-1">{{ job.description }}</p>

                            <!-- Full-width button container with proper spacing -->
                            <div class="w-full flex flex-col sm:flex-row justify-between gap-3 mt-auto">
                                <button @click.stop="viewJob(job)" class="flex-1 px-4 py-2 text-sm border border-blue-700 text-blue-700 dark:border-blue-500 dark:text-blue-300 rounded-lg hover:bg-blue-700 hover:text-white dark:hover:bg-blue-600 transition-colors duration-200 font-medium flex items-center justify-center">
                                    <i class="fas fa-eye mr-2"></i> View
                                </button>
                                <button @click.stop="confirmRemoveApplication(job.id)" class="flex-1 px-4 py-2 text-sm border border-red-500 text-red-500 dark:border-red-400 dark:text-red-400 rounded-lg hover:bg-red-500 hover:text-white dark:hover:bg-red-600 transition-colors duration-200 font-medium flex items-center justify-center">
                                    <i class="fas fa-times mr-2"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Saved Jobs Tab -->
            <div v-if="activeTab === 'saved'">
                <div v-if="loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-700 border-t-transparent"></div>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Loading your saved jobs...</p>
                </div>
                <div v-else-if="savedJobs.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <i class="far fa-bookmark text-4xl text-gray-300 dark:text-gray-500 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">No saved jobs yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">When you save jobs, they'll appear here.</p>
                    <a href="home" class="inline-block mt-4 px-4 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-800 transition-colors duration-200 font-semibold shadow">Browse Jobs</a>
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-8">
                    <div v-for="job in savedJobs" :key="job.id" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-white overflow-hidden transition-all duration-200 hover:shadow-xl hover:border-blue-400 hover:-translate-y-1 cursor-pointer group relative flex flex-col h-full border border-black-100 shadow-md hover:shadow-xl hover:border-blue-400 dark:hover:border-blue-500 hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 cursor-pointer group relative">
                        <!-- Status indicator bar -->
                        <div class="w-full h-2 bg-blue-200 dark:bg-blue-900"></div>
                        
                        <!-- Main content container -->
                        <div class="p-6 flex-1 flex flex-col">
                            <!-- Top section with job info and date -->
                            <div class="flex justify-between items-start mb-4 gap-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl font-bold text-purple-700 dark:text-purple-300 group-hover:underline mb-2">{{ job.title }}</h3>
                                    <p class="text-purple-500 dark:text-purple-200 font-semibold mb-2">{{ job.company }}</p>
                                </div>
                                <!-- Date in top-right corner -->
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-white dark:bg-gray-800 border border-purple-500 text-purple-600 dark:text-purple-300 whitespace-nowrap shrink-0">
                                    Saved {{ formatDate(job.savedDate) }}
                                </span>
                            </div>

                            <!-- Location (full width) -->
                            <div class="w-full mb-4 text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-sm"></i>
                                <span class="truncate">{{ job.location }}</span>
                            </div>

                            <!-- Description (middle section) -->
                            <p class="text-gray-600 dark:text-gray-400 mb-6 line-clamp-3 text-sm flex-1">{{ job.description }}</p>

                            <!-- Action buttons - equal width with Apply centered -->
                            <div class="w-full grid grid-cols-3 gap-3 mt-auto">
                                <button @click.stop="viewJob(job)" class="px-4 py-2 text-sm border border-purple-700 text-purple-700 dark:border-purple-500 dark:text-purple-300 rounded-lg hover:bg-purple-700 hover:text-white dark:hover:bg-purple-600 transition-colors duration-200 font-medium flex items-center justify-center">
                                    <i class="fas fa-eye mr-2"></i> View
                                </button>
                                <button @click.stop="applyJob(job.id)" 
                                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors duration-200 font-medium flex items-center justify-center">
                                    <i class="fas fa-paper-plane mr-2"></i> Apply
                                </button>
                                <button @click.stop="unsaveJob(job.id)" class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 font-medium flex items-center justify-center">
                                    <i class="far fa-trash-alt mr-2"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Job Detail Slide Panel -->
        <transition
            enter-active-class="transition-transform duration-500 ease-out"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-300 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div v-if="showJobPanel">
                <div :class="['fixed inset-0 z-40 transition-opacity duration-200', showJobPanel ? 'bg-black bg-opacity-50' : 'pointer-events-none opacity-0']" @click="closeJobPanel" id="overlay"></div>
                <div :class="['fixed top-0 right-0 h-full w-full max-w-[600px] z-50 transform transition-transform duration-500 overflow-y-auto', showJobPanel ? 'translate-x-0' : 'translate-x-full', 'backdrop-blur', darkMode ? 'bg-gray-900 bg-opacity-90' : 'bg-white bg-opacity-85']" id="jobDetailsSidebar">
                    <div class="relative p-6 min-h-full flex flex-col">
                        <button class="absolute top-4 right-4 text-gray-500 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 text-2xl z-10" @click="closeJobPanel">
                            <i class="fas fa-times"></i>
                        </button>
                        <div v-if="jobDetailsLoading" class="flex items-center justify-center h-40">
                            <div class="animate-spin rounded-full h-10 w-10 border-t-4 border-blue-500"></div>
                        </div>
                        <div v-else-if="selectedJobDetails">
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center">
                                    <img v-if="selectedJobDetails.companyDetails && selectedJobDetails.companyDetails.company_logo" 
                                        :src="'uploads/logos/' + selectedJobDetails.companyDetails.company_logo" 
                                        alt="Logo" 
                                        class="w-12 h-12 min-w-[48px] object-contain border border-gray-200 dark:border-gray-600 rounded-full mr-4
                                            bg-white dark:bg-gray-700 p-1 shadow-sm">
                                    <img v-else src="images/logo.png" 
                                        :alt="selectedJobDetails.company_name" 
                                        class="w-12 h-12 min-w-[48px] object-contain border border-gray-200 dark:border-gray-600 rounded-full mr-4
                                        bg-white dark:bg-gray-700 p-1 shadow-sm">
                                    <div>
                                        <h3 class="text-2xl font-bold text-blue-700 dark:text-blue-300 mb-1">{{ selectedJobDetails.title }}</h3>
                                        <p class="text-gray-600 dark:text-gray-400 text-lg">{{ selectedJobDetails.company_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="inline-flex items-center px-3 py-1 bg-blue-50 dark:bg-blue-900 rounded-full text-sm text-blue-700 dark:text-blue-200">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ selectedJobDetails.location }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-briefcase mr-1"></i> {{ selectedJobDetails.type }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-money-bill-wave mr-1"></i> {{ selectedJobDetails.salary }}
                                </span>
                                <span v-if="selectedJobDetails.created_at" class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-calendar-alt mr-1"></i> {{ selectedJobDetails.created_at }}
                                </span>
                                <span v-if="selectedJobDetails.status" class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-info-circle mr-1"></i> {{ selectedJobDetails.status }}
                                </span>
                                <span v-if="selectedJobDetails.application_status" class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-info-circle mr-1"></i> Application Status: {{ selectedJobDetails.application_status }}
                                </span>
                            </div>
                            <!-- Leaflet Map -->
                            <div class="mb-6 rounded-lg overflow-hidden shadow" style="height: 220px;">
                                <div id="job-map" style="height: 220px; width: 100%;"></div>
                            </div>
                            <div class="mb-6">
                                <h5 class="text-lg font-bold text-blue-700 dark:text-blue-300 mb-3">Job Description</h5>
                                <p class="text-gray-700 dark:text-gray-200">{{ selectedJobDetails.description }}</p>
                            </div>
                            <div class="mb-6">
                                <h5 class="text-lg font-bold text-blue-700 dark:text-blue-300 mb-3">Job Requirements</h5>
                                <ul class="list-disc pl-5 text-gray-700 dark:text-gray-200">
                                    <li v-for="(req, idx) in (selectedJobDetails.requirements ? selectedJobDetails.requirements.split('\n') : [])" :key="'req'+idx">{{ req }}</li>
                                </ul>
                            </div>
                            <div class="mb-6" v-if="selectedJobDetails.qualifications">
                                <h5 class="text-lg font-bold text-blue-700 dark:text-blue-300 mb-3">Qualifications</h5>
                                <ul class="list-disc pl-5 text-gray-700 dark:text-gray-200">
                                    <li v-for="(qual, idx) in (selectedJobDetails.qualifications ? selectedJobDetails.qualifications.split('\n') : [])" :key="'qual'+idx">{{ qual }}</li>
                                </ul>
                            </div>
                            <div v-if="selectedJobDetails.employer_question" class="mb-6">
                                <h5 class="text-lg font-bold text-blue-700 dark:text-blue-300 mb-3">Employer Question</h5>
                                <div class="space-y-3">
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                        <div class="w-full px-4 py-3 text-left bg-gray-50 dark:bg-gray-700 flex justify-between items-center transition-colors duration-200">
                                            <span class="text-gray-800 dark:text-gray-300">{{ selectedJobDetails.employer_question }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Company Details Section -->
                            <div v-if="selectedJobDetails.companyDetails" class="mb-6 border border-blue-200 dark:border-blue-700 rounded-lg bg-blue-50 dark:bg-blue-900/30 p-4">
                                <div class="flex items-center mb-3">
                                    <img v-if="selectedJobDetails.companyDetails.company_logo" :src="'uploads/logos/' + selectedJobDetails.companyDetails.company_logo" alt="Logo" class="w-14 h-14 rounded-full object-cover border-2 border-blue-300 dark:border-blue-700 mr-3">
                                    <div>
                                        <h5 class="text-xl font-bold text-blue-700 dark:text-blue-300 mb-1">{{ selectedJobDetails.companyDetails.company_name }}</h5>
                                        <div class="text-sm text-gray-700 dark:text-gray-200">{{ selectedJobDetails.companyDetails.nature_of_business }}</div>
                                    </div>
                                </div>
                                <div class="space-y-2 mt-2">
                                    <div v-if="selectedJobDetails.companyDetails.contact_email">
                                        <span class="font-semibold text-gray-700 dark:text-gray-200">Email:</span>
                                        <a 
                                            :href="'https://mail.google.com/mail/?view=cm&fs=1&to=' + selectedJob.companyDetails.contact_email + '&su=Job%20Application&body=Hello,'" 
                                            target="_blank"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 hover:underline transition-colors duration-200 inline-flex items-center ml-1 px-3 py-1.5"
                                        >
                                            <i class="fas fa-envelope mr-2 text-blue-500 dark:text-blue-300"></i>
                                            <span class="font-medium">{{ selectedJob.companyDetails.contact_email }}</span>
                                        </a>
                                    </div>
                                    <div v-if="selectedJobDetails.companyDetails.contact_number">
                                        <span class="font-semibold text-gray-700 dark:text-gray-200">Contact:</span>
                                        <a :href="'tel:' + selectedJobDetails.companyDetails.contact_number" class="text-blue-700 dark:text-blue-300 hover:underline inline-flex items-center ml-1">
                                            <i class="fas fa-phone mr-1"></i>{{ selectedJobDetails.companyDetails.contact_number }}
                                        </a>
                                    </div>
                                    <div v-if="selectedJobDetails.companyDetails.company_location">
                                        <span class="font-semibold text-gray-700 dark:text-gray-200">Address:</span>
                                        <span class="text-gray-800 dark:text-gray-100 ml-1">{{ selectedJobDetails.companyDetails.company_location }}</span>
                                    </div>
                                    <div v-if="selectedJobDetails.companyDetails.industry_type">
                                        <span class="font-semibold text-gray-700 dark:text-gray-200">Industry:</span>
                                        <span class="text-gray-800 dark:text-gray-100 ml-1">{{ selectedJobDetails.companyDetails.industry_type }}</span>
                                    </div>
                                    <div v-if="selectedJobDetails.companyDetails.accreditation_status">
                                        <span class="font-semibold text-gray-700 dark:text-gray-200">Accreditation:</span>
                                        <span class="text-gray-800 dark:text-gray-100 ml-1">{{ selectedJobDetails.companyDetails.accreditation_status }}</span>
                                    </div>

                                    <div class="pt-3">
                                        <button @click="messageEmployer(selectedJob.companyDetails.contact_email)" 
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-colors">
                                            <i class="fas fa-envelope mr-2"></i> Message Employer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500">No job details available.</div>
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                            <button @click="closeJobPanel" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 font-semibold">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Confirmation Modal -->
        <transition
            enter-active-class="transition-opacity duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showConfirmation" class="fixed inset-0 bg-black/50 dark:bg-black/70 z-[90] flex items-center justify-center p-4" role="dialog" aria-modal="true">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-8 border border-gray-200 dark:border-gray-700 relative">
                    <div class="flex items-center mb-4">
                        <div :class="{
                            'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400': confirmationIsDestructive,
                            'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400': !confirmationIsDestructive
                        }" class="flex items-center justify-center h-12 w-12 rounded-full mr-4">
                            <i :class="{
                                'fas fa-exclamation text-2xl': confirmationIsDestructive,
                                'fas fa-info-circle text-2xl': !confirmationIsDestructive
                            }"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ confirmationTitle }}</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ confirmationMessage }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="showConfirmation = false; confirmationAction = null" type="button" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors font-semibold">Cancel</button>
                        <button @click="executeConfirmation" type="button" :class="{
                                'bg-red-600 hover:bg-red-700 focus:ring-red-500': confirmationIsDestructive,
                            'bg-blue-700 hover:bg-blue-800 focus:ring-blue-500': !confirmationIsDestructive
                        }" class="px-4 py-2 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 font-semibold transition-colors">Confirm</button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Multi-Step Application Modal -->
        <transition 
            enter-active-class="transition-opacity duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showApplicationModal" class="fixed inset-0 z-[200] flex items-start md:items-center justify-center bg-black bg-opacity-50 pt-20 md:pt-0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-2 p-6 relative max-h-[90vh] overflow-y-auto mt-8 md:mt-0">
                    <!-- Close Button -->
                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeApplicationModal">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <!-- Stepper -->
                    <div class="flex justify-center mb-6 gap-4">
                        <template v-for="step in 5" :key="step">
                            <div class="flex flex-col items-center">
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
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2">
                            <i class="fas fa-user-circle"></i> Personal Details
                        </h3>
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
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2">
                            <i class="fas fa-graduation-cap"></i> Education
                        </h3>
                        <div v-if="applicationEducation && applicationEducation.length">
                            <div class="space-y-3">
                                <div v-for="edu in applicationEducation" :key="edu.education_id" class="bg-white dark:bg-gray-900 rounded-xl shadow p-4 border border-blue-100 dark:border-blue-800">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fas fa-university text-blue-500"></i> 
                                        <span class="font-semibold">{{ edu.school }}</span>
                                    </div>
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        Degree: <span class="font-semibold">{{ edu.degree }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        From: {{ edu.start_date }} 
                                        <span v-if="edu.current">- Present</span>
                                        <span v-else> to {{ edu.end_date }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500">No education information.</div>
                    </div>
                    
                    <div v-else-if="applicationStep === 3">
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2">
                            <i class="fas fa-lightbulb"></i> Skills
                        </h3>
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
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2">
                            <i class="fas fa-briefcase"></i> Work Experience
                        </h3>
                        <div v-if="applicationExperience && applicationExperience.length">
                            <div class="space-y-3">
                                <div v-for="exp in applicationExperience" :key="exp.experience_id" class="bg-white dark:bg-gray-900 rounded-xl shadow p-4 border border-blue-100 dark:border-blue-800">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fas fa-building text-blue-500"></i> 
                                        <span class="font-semibold">{{ exp.company }}</span>
                                    </div>
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        Title: <span class="font-semibold">{{ exp.title }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        From: {{ exp.start_date }} 
                                        <span v-if="exp.current">- Present</span>
                                        <span v-else> to {{ exp.end_date }}</span>
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        {{ exp.description }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500">No work experience listed.</div>
                    </div>
                    
                    <div v-else-if="applicationStep === 5">
                        <h3 class="text-lg font-semibold mb-4 text-blue-700 dark:text-blue-300 flex items-center gap-2">
                            <i class="fas fa-file-pdf"></i> Resume Upload
                        </h3>
                        <div v-if="applicationResume && applicationResume.file_name && !applicationResume.file">
                            <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-4 border border-blue-100 dark:border-blue-800 flex items-center gap-4">
                                <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                                <div>
                                    <a :href="'uploads/resumes/' + applicationResume.file_name" target="_blank" class="underline hover:text-blue-700 dark:hover:text-blue-300">
                                        {{ applicationResume.file_name }}
                                    </a>
                                    <span class="ml-2 text-xs text-gray-500">(Uploaded: {{ applicationResume.uploaded_at }})</span>
                                    <div class="mt-2">
                                        <iframe v-if="applicationResume.file_name.endsWith('.pdf')" 
                                                :src="'uploads/resumes/' + applicationResume.file_name" 
                                                style="width:200px;height:120px;" 
                                                class="border rounded">
                                        </iframe>
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
                            <input type="file" 
                                @change="handleApplicationResumeUpload" 
                                accept="application/pdf" 
                                class="block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        </div>
                    </div>
                    
                    <!-- Stepper Navigation -->
                    <div class="flex justify-between mt-8">
                        <button v-if="applicationStep > 1" 
                                @click="applicationStep--" 
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Back
                        </button>
                        <div class="flex-1"></div>
                        <button v-if="applicationStep < 5" 
                                @click="applicationStep++" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Next
                        </button>
                        <button v-else 
                                @click="submitApplication" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Submit Application
                        </button>
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

        <!-- Footer -->
        <footer class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-3 transition-colors duration-200 shadow-md">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center justify-center space-y-2 md:space-y-0 md:space-x-4">
                    <div class="text-gray-500 dark:text-gray-400 text-sm">
                        &copy; 2025 LSPU EIS. All rights reserved.
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-300 dark:text-gray-600 hidden md:inline">|</span>
                        <a href="alumni_terms" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200 font-medium flex items-center md:ml-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Terms & Agreement
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts after #app -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
                const highlightJobId = " . (isset($_SESSION['highlight_application_job_id']) ? $_SESSION['highlight_application_job_id'] : 'null') . ";
                
                if (highlightJobId) {
                    // Scroll to the application card and highlight it
                    const applicationCard = document.querySelector('[data-job-id=\"' + highlightJobId + '\"]');
                    if (applicationCard) {
                        applicationCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        applicationCard.classList.add('highlight-application');
                        
                        // Remove highlight after 5 seconds
                        setTimeout(() => {
                            applicationCard.classList.remove('highlight-application');
                        }, 5000);
                    }
                    
                    // Remove the session variable after use
                    fetch('functions/clear_application_highlight.php');
            }
        });
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.js"></script>
    <script>
        (function() {
            try {
            var dark = localStorage.getItem('darkMode');
            if (dark === 'true' || (dark === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
            } catch(e){}
        })();
    </script>
    <script src="js/my_application.js"></script>
</body>
</html>