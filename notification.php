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
    <title>Notifications | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/notification.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-segoe pt-[70px] transition-colors duration-200" id="app" v-cloak>
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
        <!-- Toast Notification Area (optional, can be added if needed) -->
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-200 to-white dark:from-blue-900 dark:to-gray-800 shadow-sm fixed top-0 left-0 right-0 bottom-0 z-50 h-[70px] transition-colors duration-200">
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
                            <a href="notification" class="relative inline-flex items-center text-blue-700 dark:text-blue-300 font-bold border-b-4 border-blue-700 dark:border-blue-300 pb-1 bg-blue-50 dark:bg-blue-900 rounded-t transition-all duration-200 px-2">
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
            <!-- Mobile Menu -->
            <div v-show="mobileMenuOpen" class="md:hidden bg-white dark:bg-gray-800 shadow-lg absolute top-[70px] left-0 right-0 transition-colors duration-200 z-40">
                <div class="container mx-auto px-4 py-3">
                    <a href="home" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-300">Home</a>
                    <a href="my_application" class="block py-2 text-gray-600 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-300">My Applications</a>
                    <a href="notification" class="relative block py-2 text-blue-700 dark:text-blue-300 hover:text-lspu-blue dark:hover:text-blue-300 font-blue">
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
        <main class="container mx-auto px-4 pt-4 pb-20 min-h-[calc(100vh-80px)] bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
            <!-- Title and Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-2 gap-4 pb-4 pt-4">
                <h1 class="text-2xl font-bold text-blue-700 dark:text-blue-300 tracking-wide">
                    Notifications
                </h1>
                <div class="flex justify-between gap-2">
                    <button @click="markAllAsRead" :disabled="notifications.length === 0 || allRead" class="px-5 py-2 rounded-md text-sm font-semibold transition-colors duration-200 focus:outline-none flex items-center gap-2 disabled:opacity-50 bg-blue-700 dark:bg-blue-600 text-white hover:bg-blue-800 dark:hover:bg-blue-700">
                        <i class="fas fa-check"></i> Mark all as read
                    </button>
                    <button @click="fetchNotifications" :disabled="loading" class="px-5 py-2 rounded-md text-sm font-semibold transition-colors duration-200 focus:outline-none flex items-center gap-2 disabled:opacity-50 bg-gray-500 dark:bg-gray-700 text-white hover:bg-gray-600 dark:hover:bg-gray-600">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <!-- Loading state -->
            <div v-if="loading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-700 border-t-transparent"></div>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Loading your notifications...</p>
            </div>
            <!-- Empty state -->
            <div v-else-if="notifications.length === 0" class="flex justify-center items-center py-12">
                <div class="w-full max-w-md mx-auto p-8 flex flex-col items-center">
                    <i class="far fa-bell-slash text-4xl text-gray-300 dark:text-gray-500 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200">No notifications yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 mb-4 text-center">When you have new notifications, they'll appear here.</p>
                    <a href="home" class="inline-block px-4 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-800 transition-colors duration-200 font-semibold shadow">Go to Home</a>
                </div>
            </div>
            <!-- Notifications list -->
            <div v-else class="flex flex-col gap-4 pb-8">
                <div v-for="notification in notifications" :key="notification.id"
                    class="rounded-2xl shadow-md border border-blue-100 dark:border-gray-700 bg-white dark:bg-gray-800/80 overflow-hidden transition-all duration-200 hover:shadow-xl hover:border-blue-400 hover:-translate-y-1 hover:scale-[1.02] cursor-pointer flex flex-col gap-2 p-6"
                    :class="{ 'opacity-70': notification.read }"
                    @click="markNotificationAsRead(notification)">
                    
                    <!-- Add icon here -->
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-1">
                            <i :class="[notificationIcons[notification.type] || 'fas fa-bell', 'text-blue-500 dark:text-blue-400 text-lg']"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800 dark:text-gray-100 text-base">{{ notification.message }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ notification.details }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mt-1">
                        <div class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                            <i class="far fa-clock"></i> {{ formatTime(notification.time) }}
                        </div>
                        <span v-if="!notification.read" class="bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 px-2 py-0.5 rounded-full text-xs font-semibold">Unread</span>
                    </div>
                </div>
            </div>
        </main>
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
   
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.prod.js"></script>
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
    <script src="js/notif.js"></script>
</body>
</html>