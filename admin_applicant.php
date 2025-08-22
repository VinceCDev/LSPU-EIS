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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants | LSPU -EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/admin_applicant.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
</head>
<body :class="[darkMode ? 'dark' : '', 'font-sans bg-gray-300 dark:bg-gray-800 min-h-screen']" id="app" v-cloak>
    <!-- Notification Toast (single, overlay, slide animation) -->
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
    <!-- Logout Confirmation Modal - Top positioning -->
    <div v-if="showLogoutModal" class="fixed inset-0 flex items-start justify-center z-[100]">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showLogoutModal = false"></div>
        <div class="absolute top-8 left-1/2 -translate-x-1/2 bg-white dark:bg-gray-700 rounded-lg shadow-xl p-6 w-full max-w-md mx-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Confirm Logout</h3>
                <button @click="showLogoutModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-6">Are you sure you want to logout?</p>
            <div class="flex justify-end space-x-3">
                <button @click="showLogoutModal = false" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button @click="logout" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors">
                    Logout
                </button>
            </div>
        </div>
    </div>
    <!-- Sidebar: match admin_job.php design -->
    <div v-if="sidebarActive" class="fixed top-0 left-0 bottom-0 w-[280px] bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 shadow-xl z-50 transition-all duration-300 ease-in-out transform md:translate-x-0" :class="{'-translate-x-full': !sidebarActive && isMobile}">
            <!-- Header -->
            <div class="bg-white dark:bg-slate-700 shadow-sm h-[70px] border-b border-slate-200 dark:border-gray-700">
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
                <a href="admin_dashboard" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                    <i class="fas fa-tachometer-alt w-5 mr-3 text-center text-blue-500 dark:text-blue-400"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <!-- Jobs -->
                <a href="admin_job" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                    <i class="fas fa-briefcase w-5 mr-3 text-center text-emerald-500 dark:text-emerald-400"></i>
                    <span class="font-medium">Jobs</span>
                </a>
                
                <!-- Applicants -->
                <a href="admin_applicant" class="flex items-center px-6 py-3 mx-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 dark:hover:bg-blue-500/30 transition-colors duration-200 border-l-4 border-blue-500 dark:border-blue-400" @click="handleNavClick">
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
                        <a href="admin_company" class="block py-2 pl-14 pr-6 mx-2 rounded-lg text-sm hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">Manage Companies</a>
                        <a href="admin_company_pending" class="block py-2 pl-14 pr-6 mx-2 rounded-lg text-sm hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">Pending Companies</a>
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
                <a href="admin_reports" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
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
    <!-- Header: match admin_job.php design -->
    <header class="fixed top-0 left-0 right-0 h-[70px] bg-white dark:bg-gray-700 shadow-md z-40 flex items-center px-4 md:ml-[280px]">
        <div class="flex items-center justify-between w-full">
            <button class="md:hidden text-gray-600 dark:text-gray-300 p-1" @click="toggleSidebar">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="flex items-center space-x-4 ml-auto">
                <div class="relative">
                    <div class="cursor-pointer flex items-center" @click="toggleProfileDropdown">
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
                                    <input type="checkbox" class="sr-only peer" v-model="darkMode" @change="toggleDarkMode">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-500 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-400 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="admin_profile">
                                <i class="fas fa-user mr-3"></i> Profile
                            </a>
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="admin_reminder_settings">
                                <i class="fas fa-bell mr-3"></i> Reminder Settings
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
    <!-- Main Content: match admin_job.php design -->
    <main id="main-content" class="transition-all duration-300 min-h-[calc(100vh-70px)] p-6 pt-lg-5 mt-[70px] bg-gray-50 dark:bg-gray-800 md:ml-[280px]">
        <div class="container-fluid max-w-7xl mx-auto">
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <h2 class="text-2xl font-bold mb-2 md:mb-0 text-gray-800 dark:text-gray-100">Applicants</h2>
                    <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full md:w-auto">
                        <button class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition w-full sm:w-auto justify-center" @click="exportToExcel">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition w-full sm:w-auto justify-center" @click="exportToPDF">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div class="flex items-center gap-2 w-full md:w-auto mb-2 md:mb-0">
                        <div class="relative w-full md:w-80">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" class="form-input w-full pl-10 px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search applicants..." v-model="searchQuery" @input="filterApplicants">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full md:w-auto">
                        <select class="form-select px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-auto" v-model="filters.status">
                            <option value="">All Statuses</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Pending">Pending</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <select class="form-select px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-auto" v-model="filters.appliedFor">
                            <option value="">All Positions</option>
                            <option v-for="position in uniquePositions" :key="position">{{ position }}</option>
                        </select>
                        <select class="form-select px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-auto" v-model="filters.experience">
                            <option value="">All Experience</option>
                            <option v-for="exp in uniqueExperiences" :key="exp">{{ exp }} years</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-center">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                <th class="px-4 py-2">Applicant</th>
                                <th class="px-4 py-2">Applied For</th>
                                <th class="px-4 py-2">Applied Date</th>
                                <th class="px-4 py-2">Experience</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="applicant in paginatedApplicants" :key="applicant.id" class="border-b border-gray-200 dark:border-gray-600">
                                <td class="px-4 py-2 font-semibold text-center text-gray-700 dark:text-gray-200">
                                    <div>{{ applicant.name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ applicant.email }}</div>
                                </td>
                                <td class="px-4 py-2 text-center text-gray-700 dark:text-gray-200">{{ applicant.appliedFor }}</td>
                                <td class="px-4 py-2 text-center text-gray-700 dark:text-gray-200">{{ formatDate(applicant.appliedDate) }}</td>
                                <td class="px-4 py-2 text-center text-gray-700 dark:text-gray-200">{{ applicant.experience }} years</td>
                                <td class="px-4 py-2 text-center">
                                    <span :class="['inline-block px-2 py-1 rounded text-xs font-semibold', applicant.status === 'Accepted' ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200' : applicant.status === 'Pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-200' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200']">
                                        {{ applicant.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <div class="relative inline-block text-left">
                                        <button @click="toggleActionDropdown(applicant.id)"class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none text-gray-500 dark:text-gray-200">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div v-if="actionDropdown === applicant.id" class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" @click.prevent="viewApplicant(applicant)"><i class="fas fa-eye mr-2"></i>View</a>
                                                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-100 dark:hover:bg-red-800" @click.prevent="confirmDelete(applicant)"><i class="fas fa-trash mr-2"></i>Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredApplicants.length === 0">
                                <td colspan="6" class="py-12 text-center text-gray-700 dark:text-gray-200">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                                        <span class="text-lg text-gray-400">No applicants found</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination: Centered below the table -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 gap-2">
                    <div class="text-gray-600 dark:text-gray-300 text-sm w-full md:w-auto flex justify-center md:justify-start">
                        Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredApplicants.length) }} of {{ filteredApplicants.length }} entries
                    </div>
                    <div class="flex gap-1 justify-center w-full md:w-auto">
                        <button class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" :disabled="currentPage === 1" @click="prevPage">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button v-for="page in totalPages" :key="page" class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-blue-100 dark:hover:bg-blue-900" :class="{'bg-blue-600 text-white dark:bg-blue-500 dark:text-white': page === currentPage}" @click="goToPage(page)">{{ page }}</button>
                        <button class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" :disabled="currentPage === totalPages" @click="nextPage">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- View Applicant Modal: Modern card layout, icons, section headers -->
            <div v-if="showApplicantModal" class="fixed inset-0 z-[210] flex items-center justify-center bg-black bg-opacity-50 pointer-events-auto" role="dialog" aria-modal="true" data-modal="view-applicant-modal">
              <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xl mx-2 p-0 relative max-h-[95vh] overflow-y-auto pointer-events-auto">
                <button class="absolute top-3 right-3 flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-full shadow hover:bg-gray-200 dark:hover:bg-gray-600 transition text-base font-semibold z-20" @click="showApplicantModal = false">
                  <i class="fas fa-times"></i> <span>Close</span>
                </button>
                <div class="rounded-t-2xl bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 dark:from-blue-900 dark:via-blue-800 dark:to-blue-700 px-0 pt-6 pb-8 flex flex-col items-center relative">
                  <div class="absolute top-4 left-4 bg-white dark:bg-gray-700 rounded-full p-2 shadow-lg">
                    <i class="fas fa-user-graduate text-blue-600 dark:text-blue-300 text-2xl"></i>
                  </div>
                  <img v-if="selectedApplicant.alumni && selectedApplicant.alumni.profile_image" :src="selectedApplicant.alumni.profile_image" alt="Alumni Photo" class="w-28 h-28 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-xl mb-2 mt-2">
                  <div v-else class="w-28 h-28 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-4 border-white dark:border-gray-700 shadow-xl mb-2 mt-2">
                    <i class="fas fa-user-graduate text-4xl text-gray-400"></i>
                  </div>
                  <h3 class="text-3xl font-extrabold text-white drop-shadow-lg mb-1 text-center">{{ selectedApplicant.alumni ? selectedApplicant.alumni.alumni_name : selectedApplicant.name }}</h3>
                  <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold shadow bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200">Applicant</span>
                </div>
                <div class="px-6 py-6">
                  <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-info-circle text-blue-500 dark:text-blue-300"></i> <span>Alumni Details</span></h4>
                  <div class="grid grid-cols-1 gap-3 bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-6">
                    <div class="flex items-center gap-3"><i class="fas fa-envelope text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Email:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.alumni ? selectedApplicant.alumni.email : selectedApplicant.email }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-phone text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Contact:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.alumni ? selectedApplicant.alumni.contact : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-calendar-alt text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Birthdate:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.alumni ? selectedApplicant.alumni.birthdate : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-venus-mars text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Gender:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.alumni ? selectedApplicant.alumni.gender : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-user-friends text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Civil Status:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.alumni ? selectedApplicant.alumni.civil_status : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-university text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">College:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.alumni ? selectedApplicant.alumni.college : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-graduation-cap text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Course:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.alumni ? selectedApplicant.alumni.course : '' }}</span></div>
                  </div>
                  <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-briefcase text-blue-500 dark:text-blue-300"></i> <span>Job Details</span></h4>
                  <div class="grid grid-cols-1 gap-3 bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-6">
                    <div class="flex items-center gap-3"><i class="fas fa-briefcase text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Title:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.job ? selectedApplicant.job.title : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-map-marker-alt text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Location:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.job ? selectedApplicant.job.location : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-money-bill-wave text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Salary:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.job ? selectedApplicant.job.salary : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-building text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Company:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.job ? selectedApplicant.job.company_name : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-calendar-alt text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Posted:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.job ? formatDate(selectedApplicant.job.created_at) : '' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-info-circle text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Status:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.job ? selectedApplicant.job.job_status : '' }}</span></div>
                  </div>
                  <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-file-alt text-blue-500 dark:text-blue-300"></i> <span>Job Description</span></h4>
                  <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-6 text-gray-700 dark:text-gray-200">
                    {{ selectedApplicant.job ? selectedApplicant.job.description : '' }}
                  </div>
                  <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-tasks text-blue-500 dark:text-blue-300"></i> <span>Requirements</span></h4>
                  <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-6 text-gray-700 dark:text-gray-200">
                    <ul>
                      <li v-for="(req, idx) in (selectedApplicant.job && selectedApplicant.job.requirements ? selectedApplicant.job.requirements.split('\n') : [])" :key="'req'+idx">{{ req }}</li>
                    </ul>
                  </div>
                  <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-award text-blue-500 dark:text-blue-300"></i> <span>Qualifications</span></h4>
                  <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-6 text-gray-700 dark:text-gray-200">
                    <ul>
                      <li v-for="(qual, idx) in (selectedApplicant.job && selectedApplicant.job.qualifications ? selectedApplicant.job.qualifications.split('\n') : [])" :key="'qual'+idx">{{ qual }}</li>
                    </ul>
                  </div>
                  <div v-if="selectedApplicant.job && selectedApplicant.job.employer_question">
                    <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-question-circle text-blue-500 dark:text-blue-300"></i> <span>Employer Question</span></h4>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-6 text-gray-700 dark:text-gray-200">
                      {{ selectedApplicant.job.employer_question }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Delete Confirmation Modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">Confirm Delete</h3>
                    <p class="mb-6 text-gray-700 dark:text-gray-200">Are you sure you want to delete this applicant?</p>
                    <div class="flex justify-end gap-2">
                        <button class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-700" @click="showDeleteModal = false">Cancel</button>
                        <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition" @click="confirmDeleteApplicant">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="bg-white dark:bg-gray-700 border-t dark:border-gray-600 py-3 sticky bottom-0 w-full">
        <div class="container text-center">
            <small class="text-gray-600 dark:text-gray-300">
                &copy; 2025 Laguna State Polytechnic University - Employment and Information System. All rights reserved.
            </small>
        </div>
    </footer>
    <script src="js/admin_applicant.js"></script>
</body>
</html>