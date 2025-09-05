<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
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
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admininistrator Reports | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_reports.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900" id="app" v-cloak>
    <div class="h-full">
        <!-- Logout Modal -->
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

        <!-- Sidebar -->
        <div v-if="sidebarActive" class="fixed top-0 left-0 bottom-0 w-[280px] bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 shadow-xl z-50 transition-all duration-300 ease-in-out transform md:translate-x-0" :class="{'-translate-x-full': !sidebarActive && isMobile}">
            <!-- Header -->
            <div class="bg-white dark:bg-slate-700 shadow-sm h-[70px] border-b border-slate-200 dark:border-slate-600">
                <div class="flex items-center h-full px-6 mx-auto max-w-7xl">
                    <!-- Logo with increased size -->
                    <div class="flex items-center">
                        <img 
                            src="images/logo.png" 
                            alt="Logo" 
                            class="w-12 h-12 mr-4 rounded-lg bg-white p-1 shadow-md ring-1 ring-slate-200/50 dark:bg-slate-700 dark:ring-slate-600/50"
                        >
                        
                        <!-- Text with better visibility -->
                        <span class="text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                            LSPU EIS
                        </span>
                    </div>

                    <!-- Close button -->
                    <button class="md:hidden ml-auto p-2 rounded-full hover:bg-slate-100/50 dark:hover:bg-slate-700/50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500/30" @click="toggleSidebar">
                        <i class="fas fa-times text-xl text-slate-600 dark:text-slate-300"></i>
                    </button>
                </div>
            </div>
    
    <!-- Rest of your sidebar content remains the same -->
            <div class="overflow-y-auto pt-4 pb-20 h-[calc(100%-64px)] scrollbar-thin scrollbar-thumb-slate-300 scrollbar-track-slate-100 dark:scrollbar-thumb-slate-600 dark:scrollbar-track-slate-800/50">
                <!-- Main Section -->
                <div class="px-6 py-2 mb-2">
                    <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Main</span>
                </div>
                
                <!-- Dashboard -->
                <a href="admin_dashboard.php" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200">
                    <i class="fas fa-tachometer-alt w-5 mr-3 text-center text-blue-500 dark:text-blue-400"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <!-- Jobs -->
                <a href="admin_job.php" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                    <i class="fas fa-briefcase w-5 mr-3 text-center text-emerald-500 dark:text-emerald-400"></i>
                    <span class="font-medium">Jobs</span>
                </a>
                
                <!-- Applicants -->
                <a href="admin_applicant.php" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                    <i class="fas fa-users w-5 mr-3 text-center text-amber-500 dark:text-amber-400"></i>
                    <span class="font-medium">Applicants</span>
                </a>
                
                <!-- Companies Dropdown -->
                <div class="mx-2 mb-1">
                    <button @click="companiesDropdownOpen = !companiesDropdownOpen" class="flex items-center w-full px-6 py-3 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200">
                        <i class="fas fa-building w-5 mr-3 text-center text-purple-500 dark:text-purple-400"></i>
                        <span class="font-medium">Companies</span>
                        <i class="fas fa-chevron-down ml-auto text-xs transition-transform duration-200" :class="{'rotate-180': companiesDropdownOpen}"></i>
                    </button>
                    <div class="overflow-hidden transition-all duration-300 ease-in-out" :style="companiesDropdownOpen ? 'max-height: 100px' : 'max-height: 0'">
                        <a href="admin_company.php" class="block py-2 pl-14 pr-6 mx-2 rounded-lg text-sm hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">Manage Companies</a>
                        <a href="admin_company_pending.php" class="block py-2 pl-14 pr-6 mx-2 rounded-lg text-sm hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">Pending Companies</a>
                    </div>
                </div>
                
                <!-- Alumni Dropdown -->
                <div class="mx-2 mb-1">
                    <button @click="alumniDropdownOpen = !alumniDropdownOpen" class="flex items-center w-full px-6 py-3 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200">
                        <i class="fas fa-user-graduate w-5 mr-3 text-center text-cyan-500 dark:text-cyan-400"></i>
                        <span class="font-medium">Alumni</span>
                        <i class="fas fa-chevron-down ml-auto text-xs transition-transform duration-200" :class="{'rotate-180': alumniDropdownOpen}"></i>
                    </button>
                    <div class="overflow-hidden transition-all duration-300 ease-in-out" :style="alumniDropdownOpen ? 'max-height: 100px' : 'max-height: 0'">
                        <a href="admin_alumni" class="block py-2 pl-14 pr-6 mx-2 rounded-lg text-sm hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">Manage Alumni</a>
                        <a href="admin_alumni_pending" class="block py-2 pl-14 pr-6 mx-2 rounded-lg text-sm hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">Pending Alumni</a>
                    </div>
                </div>
                
                <!-- Accounts -->
                <a href="admin_user" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200">
                    <i class="fas fa-user-shield w-5 mr-3 text-center text-red-500 dark:text-red-400"></i>
                    <span class="font-medium">Accounts</span>
                </a>
                
                <!-- Reports (Active) -->
                <a href="admin_reports" class="flex items-center px-6 py-3 mx-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 dark:hover:bg-blue-500/30 transition-colors duration-200 border-l-4 border-blue-500 dark:border-blue-400" @click="handleNavClick">
                    <i class="fas fa-chart-bar w-5 mr-3 text-center text-blue-500 dark:text-blue-400"></i>
                    <span class="font-medium">Reports</span>
                </a>
                
                <!-- Messages -->
                <a href="admin_message" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                    <i class="fas fa-envelope w-5 mr-3 text-center text-pink-500 dark:text-pink-400"></i>
                    <span class="font-medium">Messages</span>
                </a>
            </div>
        </div>

        <!-- Sidebar overlay for mobile only -->
        <div v-if="sidebarActive && isMobile" class="fixed inset-0 bg-black bg-opacity-40 z-40 md:hidden" @click="toggleSidebar"></div>

        <!-- Notification Toast -->
        <transition 
            enter-active-class="slide-enter-active"
            enter-from-class="slide-enter-from"
            leave-active-class="slide-leave-active"
            leave-to-class="slide-leave-to"
        >
            <div v-if="notifications.length > 0" @click="removeNotification(notifications[0].id)"
                :class="[
                    'notification-toast cursor-pointer fixed top-4 right-4 z-[100] max-w-sm w-full pointer-events-auto',
                    notifications[0].type === 'success' ? 'bg-green-100 border-green-500 text-green-700 dark:bg-green-900 dark:border-green-700 dark:text-green-100' : '',
                    notifications[0].type === 'error' ? 'bg-red-100 border-red-500 text-red-700 dark:bg-red-900 dark:border-red-700 dark:text-red-100' : '',
                    notifications[0].type === 'info' ? 'bg-blue-100 border-blue-500 text-blue-700 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-100' : '',
                    'border-l-4 p-4 rounded shadow-lg'
                ]">
                <div class="flex items-center">
                    <i v-if="notifications[0].type === 'success'" class="fas fa-check-circle text-green-500 dark:text-green-300 mr-3"></i>
                    <i v-if="notifications[0].type === 'error'" class="fas fa-exclamation-circle text-red-500 dark:text-red-300 mr-3"></i>
                    <i v-if="notifications[0].type === 'info'" class="fas fa-info-circle text-blue-500 dark:text-blue-300 mr-3"></i>
                    <div>
                        <p class="font-medium">{{ notifications[0].message }}</p>
                    </div>
                </div>
            </div>
        </transition>
        
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 h-[70px] bg-white dark:bg-gray-700 shadow-md z-40 flex items-center px-4">
            <div class="flex items-center justify-between w-full">
                <button class="md:hidden text-gray-600 dark:text-gray-300 p-1" @click="toggleSidebar">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-4 ml-auto">
                    <div class="relative">
                        <div class="cursor-pointer flex items-center" @click="toggleProfileDropdown()">
                            <img :src="profile.profile_pic || 'images/logo.png'" alt="Profile" class="w-10 h-10 rounded-full border-2 border-gray-200 dark:border-gray-500">
                            <span class="ml-2 font-medium text-gray-700 dark:text-gray-200">{{ profile.name ? profile.name.split(' ')[0] : 'Admin' }}</span>
                            <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200 text-gray-700 dark:text-gray-200" :class="{'rotate-180': profileDropdownOpen}"></i>
                        </div>
                        <transition 
                            enter-active-class="dropdown-enter-active"
                            enter-from-class="dropdown-enter-from"
                            enter-to-class="dropdown-enter-to"
                            leave-active-class="dropdown-leave-active"
                            leave-from-class="dropdown-leave-from"
                            leave-to-class="dropdown-leave-to"
                        >
                            <div v-if="profileDropdownOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-600 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-500 transform origin-top-right">
                                <div class="flex items-center justify-between px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500 cursor-pointer" @click="toggleDarkMode">
                                    <div class="flex items-center">
                                        <i class="fas fa-sun mr-3 theme-light" v-if="!darkMode"></i>
                                        <i class="fas fa-moon mr-3 theme-dark" v-if="darkMode"></i>
                                        <span class="text-sm">Theme</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                                        <input type="checkbox" class="sr-only peer" v-model="darkMode">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-400 peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="admin_profile">
                                    <i class="fas fa-user mr-3"></i> Profile
                                </a>
                                <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="admin_reminder_settings">
                                    <i class="fas fa-bell mr-3"></i> Reminder Settings
                                </a>
                                <a href="admin_success_stories"  class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500">
                                    <i class="fas fa-book-open mr-3"></i>
                                    <span class="font-medium">Success Stories</span>
                                </a>
                                <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="forgot_password">
                                    <i class="fas fa-key mr-3"></i> Forgot Password
                                </a>
                                <div class="border-t border-gray-200 dark:border-gray-500 my-1"></div>
                                <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="#" @click.prevent="showLogoutModal = true">
                                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                                </a>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main :class="[isMobile ? 'ml-0' : (sidebarActive ? 'ml-[280px]' : 'ml-0'), 'transition-all duration-300 min-h-[calc(100vh-70px)] p-6 pt-lg-5 mt-[70px] bg-gray-50 dark:bg-gray-800']">
            <div class="container mx-auto px-6 py-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200 mb-4">General Reports</h1>
                </div>

                <!-- Report Types -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Employment Summary Report -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Employment Summary</h3>
                            <i class="fas fa-chart-pie text-blue-500 text-xl"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Comprehensive employment statistics by program</p>
                        <div class="flex space-x-2">
                            <button @click="exportEmploymentSummary('excel')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                        </div>
                    </div>

                    <!-- Detailed Employment Report -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Detailed Employment</h3>
                            <i class="fas fa-table text-green-500 text-xl"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Employment status, sector, and location by college</p>
                        <div class="flex space-x-2">
                            <button @click="exportDetailedEmployment('excel')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                        </div>
                    </div>

                    <!-- Industry Analysis Report -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Industry Analysis</h3>
                            <i class="fas fa-industry text-purple-500 text-xl"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Comprehensive analysis of graduate employment distribution</p>
                        <div class="flex space-x-2">
                            <button @click="exportIndustryAnalysis('excel')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                                <i class="fas fa-graduation-cap text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Graduates</p>
                                <p v-if="loading" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </p>
                                <p v-else class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ summaryStats.totalGraduates }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                                <i class="fas fa-briefcase text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Employed</p>
                                <p v-if="loading" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </p>
                                <p v-else class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ summaryStats.employedCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                                <i class="fas fa-percentage text-yellow-600 dark:text-yellow-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Employment Rate</p>
                                <p v-if="loading" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </p>
                                <p v-else class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ summaryStats.employmentRate }}%</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                                <i class="fas fa-check-circle text-purple-600 dark:text-purple-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Job Match Rate</p>
                                <p v-if="loading" class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </p>
                                <p v-else class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ summaryStats.jobMatchRate }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program Statistics Table -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Employment by Program</h2>
                    <div v-if="loading" class="flex justify-center items-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-500"></i>
                        <span class="ml-2 text-gray-500">Loading program statistics...</span>
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Program</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Graduates</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employment Rate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Job Related</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Match Rate</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                <tr v-for="program in programStats" :key="program.course" class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ program.course }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ program.total_graduates }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ program.employed_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ calculatePercentage(program.employed_count, program.total_graduates) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ program.related_job_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ calculatePercentage(program.related_job_count, program.employed_count) }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-700 border-t dark:border-gray-600 py-3 sticky bottom-0 w-full">
            <div class="container text-center">
                <small class="text-gray-600 dark:text-gray-300">
                    &copy; 2025 Laguna State Polytechnic University - Employment and Information System. All rights reserved.
                </small>
            </div>
        </footer>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
    <script src="js/admin_reports.js"></script>
</body>
</html> 