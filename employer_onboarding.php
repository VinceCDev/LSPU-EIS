<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
    header('Location: employer_login.php');
    exit;
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
    <title>Job Onboarding | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/employer_job.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
</head>
<body :class="[darkMode ? 'dark' : '', 'font-sans bg-gray-50 dark:bg-gray-800 min-h-screen']" id="app" v-cloak>
    <!-- Toast Notification (single, overlay, slide animation) -->
    <transition 
        enter-active-class="slide-enter-active"
        enter-from-class="slide-enter-from"
        leave-active-class="slide-leave-active"
        leave-to-class="slide-leave-to">
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

        <!-- Menu Items -->
        <div class="overflow-y-auto pt-4 pb-20 h-[calc(100%-64px)] scrollbar-thin scrollbar-thumb-slate-300 scrollbar-track-slate-100 dark:scrollbar-thumb-slate-600 dark:scrollbar-track-slate-800/50">
            <!-- Main Section -->
            <div class="px-6 py-2 mb-2">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Main</span>
            </div>
            
            <!-- Dashboard -->
            <a href="employer_dashboard" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-tachometer-alt w-5 mr-3 text-center text-blue-500 dark:text-blue-400"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <a href="employer_leaderboard" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-trophy w-5 mr-3 text-center text-amber-500 dark:text-amber-400"></i>
                <span class="font-medium">Leaderboard</span>
            </a>

            <!-- Jobs -->
            <a href="employer_jobposting" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-briefcase w-5 mr-3 text-center text-emerald-500 dark:text-emerald-400"></i>
                <span class="font-medium">Jobs</span>
            </a>
            
            <!-- Job Resources -->
            <a href="employer_onboarding" class="flex items-center px-6 py-3 mx-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 dark:hover:bg-blue-500/30 transition-colors duration-200 border-l-4 border-blue-500 dark:border-blue-400" @click="handleNavClick">
                <i class="fas fa-user-check w-5 mr-3 text-center text-blue-500 dark:text-blue-400"></i>
                <span class="font-medium">Onboarding</span>
            </a>
            
            <!-- Applicants -->
            <a href="employer_applicants" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-users w-5 mr-3 text-center text-amber-500 dark:text-amber-400"></i>
                <span class="font-medium">Applicants</span>
            </a>

            <a href="employer_interview"  class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-calendar-alt w-5 mr-3 text-center text-violet-500 dark:text-violet-400"></i>
                <span class="font-medium">Interviews</span>
            </a>
            
            <!-- Messages -->
            <a href="employer_messages" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-envelope w-5 mr-3 text-center text-pink-500 dark:text-pink-400"></i>
                <span class="font-medium">Messages</span>
            </a>
        </div>
    </div>
    <!-- Sidebar overlay for mobile only -->
    <div v-if="sidebarActive && isMobile" class="fixed inset-0 bg-black bg-opacity-40 z-40 md:hidden" @click="toggleSidebar"></div>
        <!-- Header -->
    <header class="fixed top-0 left-0 right-0 h-[70px] bg-white dark:bg-gray-700 shadow-md z-40 flex items-center px-4">
        <div class="flex items-center justify-between w-full">
            <button class="md:hidden text-gray-600 dark:text-gray-300 p-1" @click="toggleSidebar">
                <i class="fas fa-bars text-xl"></i>
                    </button>
            <div class="flex items-center space-x-4 ml-auto">
                <div class="relative">
                    <div class="cursor-pointer flex items-center" @click="toggleProfileDropdown()">
                        <img :src="employerProfile.company_logo || 'images/logo.png'" alt="Profile" class="w-10 h-10 rounded-full border-2 border-gray-200 dark:border-gray-500">
                        <span class="ml-2 font-medium text-gray-700 dark:text-gray-200">{{ employerProfile.company_name || 'Employer' }}</span>
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
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="employer_profile">
                                <i class="fas fa-user mr-3"></i> Profile
                            </a>
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="employer_terms">
                                <i class="fas fa-file-contract mr-3"></i> Terms
                            </a>
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="employer_forgot_password">
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
    
        <main :class="[isMobile ? 'ml-0' : (sidebarActive ? 'ml-[280px]' : 'ml-0'), 'transition-all duration-300 min-h-[calc(100vh-70px)] p-6 pt-5 mt-[70px] bg-gray-50 dark:bg-gray-800']">
            <div class="container max-w-7xl mx-auto">
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-xl p-6">
                    <!-- Header with Buttons -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Onboarding Management</h2>
                        
                        <div class="flex flex-wrap gap-2">
                            <button @click="showChecklistManager" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition w-full sm:w-auto justify-center border border-blue-700">
                                <i class="fas fa-list-check"></i> Manage Checklists
                            </button>
                            <button @click="exportToExcel" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition w-full sm:w-auto justify-center border border-green-700">
                                <i class="fas fa-file-excel text-white-600 mr-2"></i> Export to Excel
                            </button>
                            <button @click="exportToPDF" class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition w-full sm:w-auto justify-center border border-red-700">
                                <i class="fas fa-file-pdf text-white-600 mr-2"></i> Export to PDF
                            </button>
                        </div>
                    </div>

                    <!-- Filters and Search -->
                    <!-- Enhanced Filters Section -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                        <div class="flex items-center gap-2 w-full md:w-auto mb-2 md:mb-0">
                            <div class="relative w-full md:w-80">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </span>
                                <input type="text" v-model="searchQuery" class="w-full pl-10 px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search candidate or position...">
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full md:w-auto">
                            <!-- Status Filter -->
                            <select v-model="onboardingFilters.status" class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-auto lg:w-[180px]">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                            
                            <!-- Progress Range Filter -->
                            <select v-model="onboardingFilters.progress" class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-auto lg:w-[180px]">
                                <option value="">All Progress</option>
                                <option value="0-25">0-25% Complete</option>
                                <option value="26-50">26-50% Complete</option>
                                <option value="51-75">51-75% Complete</option>
                                <option value="76-99">76-99% Complete</option>
                                <option value="100">100% Complete</option>
                            </select>
                            
                            <!-- Checklist Filter -->
                            <select v-model="onboardingFilters.checklist" class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-auto lg:w-[180px]">
                                <option value="">All Checklists</option>
                                <option v-for="checklist in checklists" :key="checklist.id" :value="checklist.id">
                                    {{ checklist.title }}
                                </option>
                                <option value="unassigned">Unassigned</option>
                            </select>
                        </div>
                    </div>

                    <!-- Onboarding Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <th class="px-4 py-2 text-left">Candidate</th>
                                    <th class="px-4 py-2 text-left">Position</th>
                                    <th class="px-4 py-2 text-left">Checklist</th>
                                    <th class="px-4 py-2 text-left">Progress</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                    <th class="px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="applicant in paginatedApplicants" :key="applicant.id" class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <img :src="applicant.profile_image" alt="Profile" class="w-8 h-8 rounded-full mr-3">
                                            <div>
                                                <div class="font-medium text-gray-800 dark:text-gray-200">{{ applicant.name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ applicant.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ applicant.position }}</td>
                                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                        {{ applicant.checklist_name || 'No checklist assigned' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="w-full bg-gray-200 dark:bg-gray-400 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: applicant.progress + '%' }"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-300 mt-1">{{ applicant.progress }}% complete</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                            :class="{
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100': applicant.status === 'pending',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100': applicant.status === 'in_progress',
                                                'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100': applicant.status === 'completed'
                                            }">
                                            {{ formatStatus(applicant.status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block text-left">
                                            <button @click="toggleActionDropdown(applicant.application_id)" class="p-2 rounded focus:outline-none transition-colors"
                                                :class="darkMode ? 'text-gray-200 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-200'">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div v-if="actionDropdown === applicant.application_id" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200" @click.prevent="viewOnboarding(applicant)">
                                                        <i class="fas fa-eye mr-2"></i> View Details
                                                    </a>
                                                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors duration-200" @click.prevent="editOnboarding(applicant)" v-if="applicant.status === 'pending' || applicant.status === 'in_progress'">
                                                        <i class="fas fa-edit mr-2"></i> Edit Progress
                                                    </a>
                                                    <a href="#" class="block px-4 py-2 text-sm text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors duration-200" @click.prevent="markAsComplete(applicant)" v-if="applicant.status === 'in_progress'">
                                                        <i class="fas fa-check-circle mr-2"></i> Mark Complete
                                                    </a>
                                                    <a href="#" class="block px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors duration-200" @click.prevent="assignChecklist(applicant)" v-if="!applicant.checklist_name || applicant.checklist_name === 'No checklist assigned'">
                                                        <i class="fas fa-list-check mr-2"></i> Assign Checklist
                                                    </a>
                                                    <a href="#" class="block px-4 py-2 text-sm text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/30 transition-colors duration-200" @click.prevent="sendWelcomeEmail(applicant)" v-if="applicant.status === 'pending'">
                                                        <i class="fas fa-envelope mr-2"></i> Send Welcome
                                                    </a>
                                                    <a href="#" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200" @click="openContactModal(applicant)">
                                                        <i class="fas fa-envelope mr-2"></i> Contact
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredApplicants.length === 0">
                                    <td colspan="6" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-user-times text-4xl text-gray-300 mb-2"></i>
                                            <span class="text-lg text-gray-400">No onboarding records found</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col md:flex-row md:items-center justify-center md:justify-between mt-4 gap-2">
                        <div class="text-gray-600 dark:text-gray-300 text-sm text-center md:text-left w-full md:w-auto flex justify-center md:justify-start">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredApplicants.length) }} of {{ filteredApplicants.length }} entries
                        </div>
                        <div class="flex gap-1 justify-center md:justify-start">
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
            </div>

            <!-- Checklist Manager Modal -->
            <div v-if="showChecklistModal" class="fixed inset-0 z-[210] flex items-center justify-center bg-black bg-opacity-50 pointer-events-auto">
                <div  class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-4 p-6 relative max-h-[90vh] overflow-y-auto">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Manage Onboarding Checklists</h3>
                        
                        <form @submit.prevent="saveChecklist" class="space-y-4">
                            <!-- Checklist Title -->
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Checklist Title*</label>
                                <input type="text" v-model="newChecklist.title" required 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            
                            <!-- Description -->
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea v-model="newChecklist.description" rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                            </div>
                            
                            <!-- Checklist Items -->
                            <div class="form-group">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Checklist Items*</label>
                                    <button type="button" @click="addChecklistItem" 
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                        <i class="fas fa-plus mr-1"></i> Add Item
                                    </button>
                                </div>
                                
                                <div v-for="(item, index) in newChecklist.items" :key="index" class="flex items-center gap-2 mb-2 p-2">
                                    <input type="checkbox" v-model="item.is_required" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <input type="text" v-model="item.text" required
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" 
                                        :placeholder="'Checklist item ' + (index + 1)">
                                    <button type="button" @click="removeChecklistItem(index)" 
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <p v-if="newChecklist.items.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">
                                    No items added yet. Click "Add Item" to create your checklist.
                                </p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <button type="button" @click="showChecklistModal = false" 
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                                    :disabled="newChecklist.items.length === 0">
                                    Save Checklist
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Onboarding Detail Modal -->
            <div v-if="showOnboardingModal" class="fixed inset-0 z-[210] flex items-center justify-center bg-black bg-opacity-50 pointer-events-auto" role="dialog" aria-modal="true">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl mx-2 p-0 relative max-h-[95vh] overflow-y-auto pointer-events-auto">
                    <button class="absolute top-3 right-3 flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-full shadow hover:bg-gray-200 dark:hover:bg-gray-600 transition text-base font-semibold z-20" @click="showOnboardingModal = false">
                        <i class="fas fa-times"></i> <span>Close</span>
                    </button>
                    
                    <!-- Header Section -->
                    <div class="rounded-t-2xl bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 dark:from-blue-900 dark:via-blue-800 dark:to-blue-700 px-0 pt-6 pb-8 flex flex-col items-center relative">
                        <div class="absolute top-4 left-4 bg-white dark:bg-gray-700 rounded-full p-2 shadow-lg">
                            <i class="fas fa-user-check text-blue-600 dark:text-blue-300 text-2xl"></i>
                        </div>
                        <div class="w-28 h-28 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-4 border-white dark:border-gray-700 shadow-xl mb-2 mt-2">
                            <img v-if="selectedApplicant.profile_image" :src="selectedApplicant.profile_image" alt="Candidate" class="w-full h-full rounded-full object-cover">
                            <i v-else class="fas fa-user text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-3xl font-extrabold text-white drop-shadow-lg mb-1 text-center">{{ selectedApplicant.alumni_name }}</h3>
                        <span :class="['inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold shadow', 
                            selectedApplicant.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 
                            selectedApplicant.status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 
                            selectedApplicant.status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                            'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100']">
                            {{ formatStatus(selectedApplicant.status) }}
                        </span>
                    </div>
                    
                    <!-- Content Section -->
                    <div class="px-6 py-6">
                        <!-- Basic Information -->
                        <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500 dark:text-blue-300"></i> 
                            <span>Onboarding Details</span>
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-6">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-user-graduate text-blue-500 dark:text-blue-300 w-5"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Candidate:</span> 
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.alumni_name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-envelope text-blue-500 dark:text-blue-300 w-5"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Email:</span> 
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.email }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-briefcase text-blue-500 dark:text-blue-300 w-5"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Position:</span> 
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.position }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-calendar-day text-blue-500 dark:text-blue-300 w-5"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Hire Date:</span> 
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ formatDate(selectedApplicant.applied_at) }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-list-check text-blue-500 dark:text-blue-300 w-5"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Checklist:</span> 
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedOnboardingDetails?.checklist_name || 'Not assigned' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-chart-line text-blue-500 dark:text-blue-300 w-5"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Progress:</span> 
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedApplicant.progress }}% complete</span>
                            </div>
                        </div>

                        <!-- Checklist Progress -->
                        <div v-if="selectedOnboardingDetails && selectedOnboardingDetails.checklist_items && selectedOnboardingDetails.checklist_items.length > 0">
                            <h4 class="text-lg font-bold mb-3 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                <i class="fas fa-tasks text-blue-500 dark:text-blue-300"></i> 
                                <span>Checklist Progress - {{ selectedOnboardingDetails.checklist_name }}</span>
                            </h4>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-6">
                                <div v-for="item in selectedOnboardingDetails.checklist_items" :key="item.id" class="flex items-center justify-between mb-3 p-3 rounded hover:bg-gray-100 dark:hover:bg-gray-800">
                                    <div class="flex-1">
                                        <span :class="['text-lg', item.is_completed ? 'text-gray-500 dark:text-gray-400 line-through' : 'text-gray-700 dark:text-gray-200 font-medium']">
                                            {{ item.item_text }}
                                            <span v-if="item.is_required" class="text-red-500 text-sm ml-2">(Required)</span>
                                        </span>
                                        <div v-if="item.is_completed && item.completed_date" class="text-sm text-green-600 dark:text-green-400 mt-1">
                                            <i class="fas fa-check-circle"></i> Completed on {{ formatDate(item.completed_date) }}
                                        </div>
                                    </div>
                                    <span v-if="item.is_completed" class="text-green-500 text-2xl">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                                    <span class="text-yellow-700 dark:text-yellow-300">No checklist assigned yet. Assign a checklist to start tracking onboarding progress.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div v-if="selectedApplicant.onboarding_id">
                            <h4 class="text-lg font-bold mb-3 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-blue-500 dark:text-blue-300"></i> 
                                <span>Onboarding Notes</span>
                            </h4>
                            <textarea v-model="selectedApplicant.onboarding_notes" placeholder="Add notes about this onboarding process..." 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100" 
                                rows="3"></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button @click="saveOnboardingNotes" class="w-full px-4 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-save mr-2"></i> Save Notes
                            </button>
                            <button @click="assignChecklist(selectedApplicant)" 
                                class="w-full px-4 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2"
                                v-if="!selectedApplicant.checklist_name || selectedApplicant.checklist_name === 'Not assigned' || selectedApplicant.checklist_name === 'Unknown Checklist'">
                                <i class="fas fa-list-check"></i> Assign Checklist
                            </button>
                            
                            <button @click="markAsComplete(selectedApplicant)" 
                                class="w-full px-4 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center justify-center gap-2" 
                                v-if="selectedApplicant.status === 'in_progress'">
                                <i class="fas fa-check"></i> Mark Complete (Set to 100%)
                            </button>
                            
                            <button @click="showOnboardingModal = false" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add this modal at the bottom of your template, before the closing </main> tag -->
            <!-- Contact Options Modal -->
            <div v-if="showContactModal" class="fixed inset-0 z-[210] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-4 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Contact Options</h3>
                        <button @click="showContactModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        <button @click="contactViaGmail(selectedContactApplicant)" 
                            class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fab fa-google mr-2"></i> Contact via Gmail
                        </button>
                        
                        <button @click="contactViaMessages(selectedContactApplicant)" 
                            class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            <i class="fas fa-comments mr-2"></i> Contact via Messages
                        </button>
                        
                        <a :href="`mailto:${selectedContactApplicant?.email}`" 
                            class="w-full flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                            <i class="fas fa-envelope mr-2"></i> Default Email Client
                        </a>
                        
                        <button @click="showContactModal = false" 
                            class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                    </div>
                    
                    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <p class="text-sm text-gray-600 dark:text-gray-300 text-center">
                            Contacting: <strong>{{ selectedContactApplicant?.alumni_name }}</strong><br>
                            Email: <strong>{{ selectedContactApplicant?.email }}</strong>
                        </p>
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

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
    <script src="js/employer_onboarding.js"></script>
</body>
</html>