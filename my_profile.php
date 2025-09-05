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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | LSPU EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/my_profile.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-gray-200 font-sans transition-colors duration-200" id="app" v-cloak>
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

    <div >
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
                                    <a href="my_profile" class="block px-4 py-2 bg-blue-100 text-blue-700 dark:text-white hover:bg-blue-100 hover:text-blue-300 dark:bg-blue-500 dark:hover:bg-blue-500 transition-colors duration-200">
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
                </nav>
            </div>
            <!-- Mobile Menu -->
            <div v-show="mobileMenuOpen" class="md:hidden bg-white dark:bg-gray-800 shadow-lg absolute top-[70px] left-0 right-0 transition-colors duration-200 z-40">
                <div class="container mx-auto px-4 py-3">
                    <a href="home" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-300">Home</a>
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
                            <a href="my_profile" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-blue-300 font-bold">
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

        <!-- Profile Main Content -->
        <main class="max-w-5xl mx-auto px-4 py-8 mt-[30px]">
    <!-- Hero/Profile Card -->
        <section class="relative bg-white dark:bg-gray-700 rounded-xl shadow-md p-4 mt-10 sm:p-6 mb-6 overflow-hidden">
        <!-- Background image with blue opacity overlay -->
        <div class="absolute inset-0 bg-[url('images/lspu_campus.jpg')] bg-cover bg-center">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-800/60 via-slate-700/50 to-blue-700/60"></div>
        </div>
        
        <div class="relative z-10 flex flex-col items-center text-center">
            <div class="relative mb-4 sm:mb-6">
                <img v-if="profilePicData.file_name" :src="'uploads/profile_picture/' + profilePicData.file_name" alt="Profile Photo" class="w-24 h-24 sm:w-40 sm:h-40 rounded-full object-cover border-4 border-white dark:border-gray-300 shadow-lg">
                <div v-else class="w-24 h-24 sm:w-40 sm:h-40 rounded-full border-4 border-white dark:border-gray-300 shadow-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-3xl text-gray-400 dark:text-gray-300">
                    <i class="fas fa-user"></i>
                </div>
                <div class="absolute -bottom-1 -right-1 sm:-bottom-2 sm:-right-2 bg-blue-600 text-white rounded-full p-1.5 sm:p-2 cursor-pointer hover:bg-blue-700 transition-colors" @click="openPhotoModal">
                    <i class="fas fa-camera text-xs sm:text-sm"></i>
                </div>
            </div>
            <div class="mb-4 sm:mb-6 w-full">
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 drop-shadow-lg">{{ profile.name }}</h1>
                <p class="text-lg sm:text-xl text-white mb-4 drop-shadow-lg">Alumni</p>
                <div class="flex flex-row flex-wrap gap-2 sm:gap-4 justify-center mb-4 sm:mb-6 w-full">
                    <div class="flex items-center justify-center bg-white bg-opacity-20 backdrop-blur-sm rounded-lg px-3 py-2 sm:px-4 sm:py-2 min-w-[120px]">
                        <i class="fas fa-envelope text-white mr-2 text-sm sm:text-base"></i>
                        <span class="text-white text-sm sm:text-base">{{ profile.email || 'No email specified' }}</span>
                    </div>
                    <div class="flex items-center justify-center bg-white bg-opacity-20 backdrop-blur-sm rounded-lg px-3 py-2 sm:px-4 sm:py-2 min-w-[120px]">
                        <i class="fas fa-phone text-white mr-2 text-sm sm:text-base"></i>
                        <span class="text-white text-sm sm:text-base">{{ profile.contact || 'No phone specified' }}</span>
                    </div>
                    <div class="flex items-center justify-center bg-white bg-opacity-20 backdrop-blur-sm rounded-lg px-3 py-2 sm:px-4 sm:py-2 min-w-[120px]">
                        <i class="fas fa-map-marker-alt text-white mr-2 text-sm sm:text-base"></i>
                        <span class="text-white text-sm sm:text-base">{{ (profile.city && profile.province) ? (profile.city + ', ' + profile.province) : 'No location specified' }}</span>
                    </div>
                </div>
            </div>
            <button @click="editProfile" class="bg-white text-blue-600 px-6 py-2 sm:px-8 sm:py-3 rounded-lg hover:bg-gray-100 transition-colors shadow-md font-semibold text-sm sm:text-base">
                <i class="fas fa-edit mr-2"></i>Edit Profile
            </button>
        </div>
    </section>

    <!-- Personal Information Section -->
    <section class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 uppercase">Personal Information</h2>
            <button @click="editProfile" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Full Name</div>
                <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.first_name }} {{ profile.middle_name }} {{ profile.last_name }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Email</div>
                <div class="text-gray-800 dark:text-gray-200">{{ profile.email || 'Not specified' }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Phone</div>
                <div class="text-gray-800 dark:text-gray-200">{{ profile.contact || 'Not specified' }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">City</div>
                <div class="text-gray-800 dark:text-gray-200">{{ profile.city || 'Not specified' }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Province</div>
                <div class="text-gray-800 dark:text-gray-200">{{ profile.province || 'Not specified' }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Birthdate</div>
                <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.birthdate || 'Not specified' }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Gender</div>
                <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.gender || 'Not specified' }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Civil Status</div>
                <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.civil_status || 'Not specified' }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">College</div>
                <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.college || 'Not specified' }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Course</div>
                <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.course || 'Not specified' }}</div>
                    </div>
            <div class="space-y-2">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Year Graduated</div>
                <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.year_graduated || 'Not specified' }}</div>
                    </div>
                </div>
            </section>

            <!-- Education Section -->
    <section class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 uppercase">Education</h2>
            <button @click="showEducationModal = true" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors w-auto">
                <i class="fas fa-plus mr-1"></i>Add
                    </button>
                </div>
        <div v-if="profile.education.length > 0" class="space-y-4 sm:space-y-6">
            <div v-for="(edu, index) in profile.education" :key="index" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 sm:p-6">
                <!-- Invisible education_id for internal use -->
                <input type="hidden" :value="edu.education_id" />
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100 uppercase mb-1">{{ edu.degree }}</h3>
                        <p class="text-base sm:text-lg text-blue-600 dark:text-blue-400 uppercase mb-1">{{ edu.school }}</p>
                        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                            {{ formatDate(edu.start_date) }} - {{ edu.current ? 'Present' : formatDate(edu.end_date) }}
                        </p>
                        </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <button @click="editEducation(index)" class="bg-blue-600 text-white px-3 py-1.5 sm:px-3 sm:py-1 rounded hover:bg-blue-700 transition-colors text-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                        <button @click="deleteEducation(index)" class="bg-red-600 text-white px-3 py-1.5 sm:px-3 sm:py-1 rounded hover:bg-red-700 transition-colors text-sm">
                            <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
        </div>
        <div v-else class="text-center py-8 sm:py-12">
            <i class="fas fa-graduation-cap text-3xl sm:text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">No education information added yet</p>
                </div>
            </section>

            <!-- Skills Section -->
    <section class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 uppercase">Skills</h2>
            <button @click="showSkillsModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                </div>
        <div class="flex flex-wrap gap-2">
            <div v-for="(skill, index) in profile.skills" :key="skill.skill_id" class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full flex items-center gap-2">
                        {{ skill.name }}
                <span v-if="skill.certificate" class="ml-1 text-xs bg-green-200 dark:bg-green-700 text-green-800 dark:text-green-100 px-2 py-0.5 rounded-full">
                    <i class="fas fa-certificate"></i> {{ skill.certificate }}
                        </span>
                <button @click="removeSkill(index)" class="ml-1 text-red-500 hover:text-red-700 dark:hover:text-red-300">
                            <i class="fas fa-times"></i>
                </button>
                    </div>
            <div v-if="profile.skills.length === 0" class="text-gray-500 dark:text-gray-400 text-sm">No skills added yet</div>
                </div>
            </section>

            <!-- Work Experience Section -->
            <section class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100 uppercase">Work Experience</h2>
                    <button @click="showExperienceModal = true" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors w-auto">
                        <i class="fas fa-plus mr-1"></i>Add
                            </button>
                        </div>
                <div v-if="profile.experiences.length > 0" class="space-y-4 sm:space-y-6">
                    <div v-for="(exp, index) in profile.experiences" :key="index" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 sm:p-6">
                        <!-- Invisible experience_id for internal use -->
                        <input type="hidden" :value="exp.experience_id" />
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100 uppercase mb-1">{{ exp.title }}</h3>
                                <p class="text-base sm:text-lg text-blue-600 dark:text-blue-400 uppercase mb-1">{{ exp.company }}</p>
                                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                                    {{ formatDate(exp.start_date) }} - {{ exp.current ? 'Present' : formatDate(exp.end_date) }}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">
                                    <span class="font-semibold">Location of Work:</span> {{ exp.location_of_work || 'Not specified' }}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">
                                    <span class="font-semibold">Employment Status:</span> {{ exp.employment_status || 'Not specified' }}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">
                                    <span class="font-semibold">Employment Sector:</span> {{ exp.employment_sector || 'Not specified' }}
                                </p>
                                </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <button @click="editExperience(index)" class="bg-blue-600 text-white px-3 py-1.5 sm:px-3 sm:py-1 rounded hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                <button @click="deleteExperience(index)" class="bg-red-600 text-white px-3 py-1.5 sm:px-3 sm:py-1 rounded hover:bg-red-700 transition-colors text-sm">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                        <p class="text-sm sm:text-base text-gray-700 dark:text-gray-200 leading-relaxed">{{ exp.description }}</p>
                        </div>
                </div>
                <div v-else class="text-center py-8 sm:py-12">
                    <i class="fas fa-briefcase text-3xl sm:text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">No work experience added yet</p>
                </div>
            </section>

            <!-- Success Stories Section -->
            <section class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100 uppercase">Success Stories
            </h2>
        </div>
        <button @click="showSuccessStoryModal = true" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2.5 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
            <i class="fas fa-plus mr-2 text-sm"></i>Share Story
        </button>
    </div>
    <div v-if="successStories.length > 0" class="space-y-6">
        <div v-for="(story, index) in successStories" :key="index" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 sm:p-6">
            <div class="flex items-start justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center">
                    <i class="fas fa-trophy text-yellow-500 mr-3 text-sm"></i>
                    {{ story.title }}
                </h3>
                <div class="flex gap-2">
                    <button @click="editSuccessStory(index)" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-3 py-1.5 rounded-md hover:from-blue-600 hover:to-blue-700 transition-all duration-200 text-sm shadow-sm flex items-center">
                        <i class="fas fa-edit mr-1.5 text-xs"></i> Edit
                    </button>
                    <button @click="deleteSuccessStory(index)" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-3 py-1.5 rounded-md hover:from-red-600 hover:to-red-700 transition-all duration-200 text-sm shadow-sm flex items-center">
                        <i class="fas fa-trash mr-1.5 text-xs"></i> Delete
                    </button>
                </div>
            </div>
            <p class="text-gray-700 dark:text-gray-200 mb-4 leading-relaxed italic">{{ story.content }}</p>
            <div class="text-sm text-gray-500 dark:text-gray-400 pt-3 border-t border-gray-100 dark:border-gray-500 flex flex-wrap items-center">
                <span class="flex items-center mr-4">
                    <i class="far fa-calendar mr-1.5"></i> Posted on: {{ formatDate(story.created_at) }}
                </span>
                <span class="flex items-center">
                    <i class="far fa-flag mr-1.5"></i> Status: 
                    <span :class="{
                        'text-yellow-600 dark:text-yellow-400': story.status === 'draft',
                        'text-green-600 dark:text-green-400': story.status === 'published',
                        'text-gray-600 dark:text-gray-400': story.status === 'archived'
                    }" class="font-semibold capitalize ml-1.5 px-2 py-0.5 rounded-full text-xs bg-opacity-10" :class="{
                        'bg-yellow-500': story.status === 'draft',
                        'bg-green-500': story.status === 'published',
                        'bg-gray-500': story.status === 'archived'
                    }">
                        {{ story.status }}
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div v-else class="text-center py-12 px-4">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-5">
            <i class="fas fa-trophy text-3xl text-green-500 dark:text-green-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">No success stories yet</h3>
        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">Be the first to share your achievement and inspire others!</p>
        <button @click="showSuccessStoryModal = true" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-5 py-2.5 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
            Share Your First Success Story
        </button>
    </div>
</section>

            <!-- Success Story Modal -->
            <transition 
                enter-active-class="modal-enter-active"
                enter-from-class="modal-enter-from"
                enter-to-class="modal-enter-to"
                leave-active-class="modal-leave-active"
                leave-from-class="modal-leave-from"
                leave-to-class="modal-leave-to"
            >
                <div v-if="showSuccessStoryModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
                        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeSuccessStoryModal">
                            <i class="fas fa-times"></i>
                        </button>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ editingSuccessStoryIndex === null ? 'Share' : 'Edit' }} Success Story</h3>
                        <form @submit.prevent="saveSuccessStory">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title*</label>
                                    <input type="text" v-model="editSuccessStoryData.title" placeholder="Enter a title for your story" required
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your Story*</label>
                                    <textarea v-model="editSuccessStoryData.content" rows="8" placeholder="Share your career success, achievement, or inspiring journey..." required
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="closeSuccessStoryModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Story</button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>

            <!-- Delete Success Story Confirmation Modal -->
            <transition enter-active-class="modal-enter-active" enter-from-class="modal-enter-from" enter-to-class="modal-enter-to" leave-active-class="modal-leave-active" leave-from-class="modal-leave-from" leave-to-class="modal-leave-to">
                <div v-if="showDeleteSuccessStoryModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Confirm Delete</h3>
                        <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete this success story?</p>
                        <div class="flex justify-end gap-3">
                            <button @click="cancelDeleteSuccessStory" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button @click="confirmDeleteSuccessStory" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Resume Section -->
            <section class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 uppercase">Resume</h2>
                    <div class="flex gap-2">
                        <button v-if="!resumeData.resume_id" @click="showResumeModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-upload mr-1"></i>Upload
                        </button>
                        <button v-else @click="showResumeModal = true" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                            <i class="fas fa-edit mr-1"></i>Change
                        </button>
                        <button v-if="resumeData.resume_id" @click="openDeleteResumeModal" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                    <div v-if="resumeData.resume_id" class="text-gray-800 dark:text-gray-200 min-w-0 flex-1">
                        <!-- Wrap the link in a container with truncation -->
                        <div class="flex items-center gap-2 truncate">
                        <a 
                            :href="'uploads/resumes/' + resumeData.file_name" 
                            target="_blank" 
                            class="underline hover:text-blue-700 dark:hover:text-blue-300 truncate"
                            :title="resumeData.file_name" 
                        >
                            {{ resumeData.file_name }}
                        </a>
                        <span class="flex-shrink-0 text-xs text-gray-500">(Uploaded: {{ formatDate(resumeData.uploaded_at) }})</span>
                        </div>
                    </div>
                    <div v-else class="text-gray-500 dark:text-gray-400">No resume uploaded</div>
                </div>
            </section>

            <!-- Resume Modal -->
            <transition 
                enter-active-class="modal-enter-active"
                enter-from-class="modal-enter-from"
                enter-to-class="modal-enter-to"
                leave-active-class="modal-leave-active"
                leave-from-class="modal-leave-from"
                leave-to-class="modal-leave-to"
            >
                <div v-if="showResumeModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
                        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeResumeModal">
                            <i class="fas fa-times"></i>
                        </button>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ resumeData.resume_id ? 'Change Resume' : 'Upload Resume' }}</h3>
                        <form @submit.prevent="resumeData.resume_id ? changeResume() : saveResume()">
                            <div class="mb-4">
                                <input type="file" @change="handleResumeUpload" accept="application/pdf" class="block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            </div>
                            <div v-if="resumePreview" class="mb-4">
                                <iframe :src="resumePreview" style="width:100%;height:300px;" class="border rounded"></iframe>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="closeResumeModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ resumeData.resume_id ? 'Change Resume' : 'Save Resume' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>

            <!-- Delete Resume Confirmation Modal -->
            <transition enter-active-class="modal-enter-active" enter-from-class="modal-enter-from" enter-to-class="modal-enter-to" leave-active-class="modal-leave-active" leave-from-class="modal-leave-from" leave-to-class="modal-leave-to">
                <div v-if="showDeleteResumeModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Confirm Delete</h3>
                        <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete your resume?</p>
                        <div class="flex justify-end gap-3">
                            <button @click="closeDeleteResumeModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button @click="deleteResume" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Verification Document Section -->
            <section class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 uppercase">Verification Document</h2>
                    <button @click="showDocumentModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-upload mr-1"></i>Change
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-file-alt text-3xl text-blue-500"></i>
                    <div v-if="profile.verification_document" class="text-gray-800 dark:text-gray-200 min-w-0 flex-1">
                        <!-- Truncated link with hover tooltip -->
                        <a
                        :href="'uploads/documents/' + profile.verification_document"
                        target="_blank"
                        class="underline hover:text-blue-700 dark:hover:text-blue-300 truncate block"
                        :title="profile.verification_document" 
                        >
                        {{ profile.verification_document }}
                        </a>
                    </div>
                    <div v-else class="text-gray-500 dark:text-gray-400">No document uploaded</div>
                </div>
            </section>
        </main>

        <!-- Profile Edit Modal -->
        <transition 
            enter-active-class="modal-enter-active"
            enter-from-class="modal-enter-from"
            enter-to-class="modal-enter-to"
            leave-active-class="modal-leave-active"
            leave-from-class="modal-leave-from"
            leave-to-class="modal-leave-to"
        >
            <div v-if="showEditModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeEditModal">
                        <i class="fas fa-times"></i>
                    </button>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Edit Profile</h3>
                    <form @submit.prevent="saveProfile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name*</label>
                                <input type="text" v-model="editProfileData.first_name" required
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Middle Name</label>
                                <input type="text" v-model="editProfileData.middle_name"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name*</label>
                                <input type="text" v-model="editProfileData.last_name" required
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email*</label>
                                <input type="email" v-model="editProfileData.email" required
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <input type="tel" v-model="editProfileData.contact"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                                <select v-model="editProfileData.city" :disabled="!cities.length" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select City/Municipality</option>
                                    <option v-for="city in cities" :key="city.code" :value="city.name">{{ city.name }}</option>
                                </select>
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Province</label>
                                <select v-model="editProfileData.province" @change="fetchCities" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Province</option>
                                    <option v-for="province in provinces" :key="province.code" :value="province.name">{{ province.name }}</option>
                                </select>
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Birthdate</label>
                                <input type="date" v-model="editProfileData.birthdate"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                                <select v-model="editProfileData.gender"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                    <option value="">Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Civil Status</label>
                                <select v-model="editProfileData.civil_status"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Civil Status</option>
                                                    <option value="Single">Single</option>
                                                    <option value="Married">Married</option>
                                                    <option value="Divorced">Divorced</option>
                                                    <option value="Widowed">Widowed</option>
                                                </select>
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">College</label>
                                <select v-model="editProfileData.college" @change="updateCourseOptions" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select College</option>
                                    <option v-for="college in colleges" :key="college" :value="college">{{ college }}</option>
                                                </select>
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Course</label>
                                <select v-model="editProfileData.course" :disabled="!editProfileData.college" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                    <option value="">Select Course</option>
                                    <option v-for="course in collegeCourses[editProfileData.college] || []" :key="course" :value="course">{{ course }}</option>
                                                </select>
                                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year Graduated</label>
                                <input type="number" v-model="editProfileData.year_graduated" min="1950" max="2030"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                                            </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="closeEditModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Save Changes
                            </button>
                                </div>
                            </form>
                        </div>
                    </div>
        </transition>

        <transition enter-active-class="modal-enter-active" enter-from-class="modal-enter-from" enter-to-class="modal-enter-to" leave-active-class="modal-leave-active" leave-from-class="modal-leave-from" leave-to-class="modal-leave-to">
            <div v-if="showDeleteEducationModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Confirm Delete</h3>
                    <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete this education record?</p>
                    <div class="flex justify-end gap-3">
                        <button @click="cancelDeleteEducation" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                        <button @click="confirmDeleteEducation" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
                </div>
                </div>
            </div>
        </transition>

        <!-- Skills Modal -->
        <transition 
    enter-active-class="modal-enter-active"
    enter-from-class="modal-enter-from"
    enter-to-class="modal-enter-to"
    leave-active-class="modal-leave-active"
    leave-from-class="modal-leave-from"
    leave-to-class="modal-leave-to"
>
    <div v-if="showSkillsModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeSkillsModal">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Edit Skills</h3>
            <form @submit.prevent="saveSkills">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Skill Name</label>
                    <input type="text" v-model="newSkill.name" placeholder="Enter skill name" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                        <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Certificate (optional)</label>
                    <input type="text" v-model="newSkill.certificate" placeholder="Certificate name" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                <button type="button" @click="addSkill" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4"><i class="fas fa-plus mr-1"></i>Add Skill</button>
                <div class="flex flex-wrap gap-2 mb-4">
                    <div v-for="(skill, index) in editSkillsData" :key="index" class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full flex items-center gap-2">
                                {{ skill.name }}
                        <span v-if="skill.certificate" class="ml-1 text-xs bg-green-200 dark:bg-green-700 text-green-800 dark:text-green-100 px-2 py-0.5 rounded-full">
                            <i class="fas fa-certificate"></i> {{ skill.certificate }}
                                </span>
                        <button @click="removeEditSkill(index)" class="ml-1 text-red-500 hover:text-red-700 dark:hover:text-red-300"><i class="fas fa-times"></i></button>
                            </div>
                            </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="closeSkillsModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Changes</button>
                        </div>
            </form>
                    </div>
                    </div>
</transition>


<transition enter-active-class="modal-enter-active" enter-from-class="modal-enter-from" enter-to-class="modal-enter-to" leave-active-class="modal-leave-active" leave-from-class="modal-leave-from" leave-to-class="modal-leave-to">
    <div v-if="showDeleteSkillModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Confirm Delete</h3>
            <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete this skill?</p>
            <div class="flex justify-end gap-3">
                <button @click="cancelDeleteSkill" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                <button @click="confirmDeleteSkill" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
</transition>

        <!-- Education Modal -->
        <transition 
    enter-active-class="modal-enter-active"
    enter-from-class="modal-enter-from"
    enter-to-class="modal-enter-to"
    leave-active-class="modal-leave-active"
    leave-from-class="modal-leave-from"
    leave-to-class="modal-leave-to"
>
    <div v-if="showEducationModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeEducationModal">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ editingEducationIndex === null ? 'Add' : 'Edit' }} Education</h3>
            <form @submit.prevent="saveEducation">
                <div class="space-y-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Degree*</label>
                        <input type="text" v-model="degreeInput" placeholder="Enter degree" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white bg-white dark:bg-gray-700">
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">School/University*</label>
                        <input type="text" v-model="schoolInput" placeholder="Enter school/university" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white bg-white dark:bg-gray-700">
                            </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date*</label>
                            <input type="date" v-model="editEducationData.start_date" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white bg-white dark:bg-gray-700">
                            </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <input type="date" v-model="editEducationData.end_date" :disabled="editEducationData.current" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white bg-white dark:bg-gray-700">
                            <div class="flex items-center mt-2">
                                <input type="checkbox" v-model="editEducationData.current" id="eduCurrent" class="mr-2 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500">
                                <label for="eduCurrent" class="text-sm text-gray-700 dark:text-gray-300">I currently study here</label>
                            </div>
                                </div>
                            </div>
                        </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="closeEducationModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
                    </div>
            </form>
                    </div>
                </div>
</transition>

        <!-- Experience Modal -->
        <transition 
    enter-active-class="modal-enter-active"
    enter-from-class="modal-enter-from"
    enter-to-class="modal-enter-to"
    leave-active-class="modal-leave-active"
    leave-from-class="modal-leave-from"
    leave-to-class="modal-leave-to"
>
    <div v-if="showExperienceModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeExperienceModal">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ editingExperienceIndex === null ? 'Add' : 'Edit' }} Work Experience</h3>
            <form @submit.prevent="saveExperience">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Job Title*</label>
                        <input type="text" v-model="editExperienceData.title" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company*</label>
                        <input type="text" v-model="editExperienceData.company" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
        </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location of Work*</label>
                        <select v-model="editExperienceData.location_of_work" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Location</option>
                            <option value="Local">Local</option>
                            <option value="Abroad">Abroad</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employment Status*</label>
                        <select v-model="editExperienceData.employment_status" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Status</option>
                            <option value="Probational">Probational</option>
                            <option value="Contractual">Contractual</option>
                            <option value="Regular">Regular</option>
                            <option value="Self-employed">Self-employed</option>
                            <option value="Unemployed">Unemployed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date*</label>
                        <input type="date" v-model="editExperienceData.start_date" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input type="date" v-model="editExperienceData.end_date" :disabled="editExperienceData.current" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <div class="flex items-center mt-2">
                            <input type="checkbox" v-model="editExperienceData.current" id="expCurrent" class="mr-2 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500">
                            <label for="expCurrent" class="text-sm text-gray-700 dark:text-gray-300">I currently work here</label>
                                </div>
                                </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description*</label>
                        <textarea v-model="editExperienceData.description" rows="4" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                                </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 mt-4">Employment Sector*</label>
                        <select v-model="editExperienceData.employment_sector" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Sector</option>
                            <option value="Government">Government</option>
                            <option value="Private">Private</option>
                        </select>
                    </div>
                                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="closeExperienceModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
                                </div>
            </form>
                                </div>
                                </div>
</transition>

</transition>

<!-- Delete Work Experience Confirmation Modal -->
        <transition enter-active-class="modal-enter-active" enter-from-class="modal-enter-from" enter-to-class="modal-enter-to" leave-active-class="modal-leave-active" leave-from-class="modal-leave-from" leave-to-class="modal-leave-to">
            <div v-if="showDeleteExperienceModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Confirm Delete</h3>
                    <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete this work experience?</p>
                    <div class="flex justify-end gap-3">
                        <button @click="cancelDeleteExperience" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                        <button @click="confirmDeleteExperience" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
                                        </div>
                                            </div>
                                        </div>
        </transition>


                <!-- Change Photo Modal -->
                <transition 
            enter-active-class="modal-enter-active"
            enter-from-class="modal-enter-from"
            enter-to-class="modal-enter-to"
            leave-active-class="modal-leave-active"
            leave-from-class="modal-leave-from"
            leave-to-class="modal-leave-to"
        >
            <div v-if="showPhotoModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closePhotoModal">
                        <i class="fas fa-times"></i>
                    </button>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Update Profile Photo</h3>
                    <div class="space-y-4">
                        <div class="flex justify-center">
                            <div class="relative">
                                <img v-if="newPhotoPreview" :src="newPhotoPreview" alt="New Photo Preview" class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                                <img v-else-if="profilePicData.file_name" :src="'uploads/profile_picture/' + profilePicData.file_name" alt="Current Photo" class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-600">
                                <div v-else class="w-32 h-32 rounded-full border-4 border-gray-200 dark:border-gray-600 bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-4xl text-gray-400 dark:text-gray-300">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Choose New Photo</label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                                <input type="file" ref="photoInput" @change="handlePhotoUpload" accept="image/*" class="hidden">
                                <div class="cursor-pointer" @click="$refs.photoInput.click()">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600 dark:text-gray-300">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 5MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="closePhotoModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                        <button v-if="profilePicData.file_name" @click="openDeleteProfilePicModal" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete Photo</button>
                        <!-- Change Photo button only if a profile picture exists -->
                        <button v-if="profilePicData.file_name" @click="$refs.photoInput.click()" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Change Photo</button>
                        <!-- Save button only enabled if a file is selected -->
                        <button @click="saveProfilePic" :disabled="!newPhotoFile" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">Save Photo</button>
                    </div>
                </div>
            </div>
        </transition>

                <!-- Change Verification Document Modal -->
        <transition 
            enter-active-class="modal-enter-active"
            enter-from-class="modal-enter-from"
            enter-to-class="modal-enter-to"
            leave-active-class="modal-leave-active"
            leave-from-class="modal-leave-from"
            leave-to-class="modal-leave-to"
        >
            <div v-if="showDocumentModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeDocumentModal">
                        <i class="fas fa-times"></i>
                                </button>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Change Verification Document</h3>
                    <form @submit.prevent="updateVerificationDocument">
                        <div class="mb-4">
                            <input type="file" @change="handleDocumentUpload" accept=".pdf,.jpg,.jpeg,.png,.gif" class="block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            </div>
                        <div v-if="documentPreview" class="mb-4">
                            <iframe v-if="documentPreviewType === 'pdf'" :src="documentPreview" style="width:100%;height:300px;" class="border rounded"></iframe>
                            <img v-else :src="documentPreview" alt="Document Preview" class="max-h-48 rounded border mx-auto" />
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="closeDocumentModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Document</button>
                    </div>
                    </form>
                </div>
            </div>
        </transition>

        <transition enter-active-class="modal-enter-active" enter-from-class="modal-enter-from" enter-to-class="modal-enter-to" leave-active-class="modal-leave-active" leave-from-class="modal-leave-from" leave-to-class="modal-leave-to">
            <div v-if="showDeleteProfilePicModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Confirm Delete</h3>
                    <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete your profile photo?</p>
                    <div class="flex justify-end gap-3">
                        <button @click="closeDeleteProfilePicModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                        <button @click="deleteProfilePic" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
                    </div>
                </div>
            </div>
        </transition>

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
        <!-- Floating Resume Generator Button -->
        <button
            @click="generateResumePDF"
            class="fixed bottom-8 right-8 z-[200] bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg p-4 flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
            aria-label="Generate Resume PDF"
            title="Generate Resume PDF"
        >
            <i class="fas fa-file-pdf text-xl"></i>
            <span class="hidden sm:inline font-semibold">Resume Generator</span>
        </button>
        <!-- Logout Confirmation Modal -->
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
    </div>
    
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
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.js"></script>
    <script src="js/my_profile.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</body>
</html>