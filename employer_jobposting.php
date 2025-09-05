<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
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
    <title>Employer Job Postings | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
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
            <a href="employer_jobposting" class="flex items-center px-6 py-3 mx-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 dark:hover:bg-blue-500/30 transition-colors duration-200 border-l-4 border-blue-500 dark:border-blue-400" @click="handleNavClick">
                <i class="fas fa-briefcase w-5 mr-3 text-center text-emerald-500 dark:text-emerald-400"></i>
                <span class="font-medium">Jobs</span>
            </a>

            <!-- Job Resources -->
            <a href="employer_resources" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-file-alt w-5 mr-3 text-center text-blue-500 dark:text-blue-400"></i>
                <span class="font-medium">Resources</span>
            </a>
            
            <!-- Applicants -->
            <a href="employer_applicants" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-users w-5 mr-3 text-center text-amber-500 dark:text-amber-400"></i>
                <span class="font-medium">Applicants</span>
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
        <!-- Main Content -->
    <main :class="[isMobile ? 'ml-0' : (sidebarActive ? 'ml-[280px]' : 'ml-0'), 'transition-all duration-300 min-h-[calc(100vh-70px)] p-6 pt-lg-5 mt-[70px] bg-gray-50 dark:bg-gray-800']">
        <div class="container-fluid max-w-7xl mx-auto">
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-xl p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <h2 class="text-2xl font-bold mb-2 md:mb-0 text-gray-800 dark:text-gray-100">Job Postings</h2>
                    <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full md:w-auto">
                        <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition w-full sm:w-auto justify-center" @click="openAddModal">
                                <i class="fas fa-plus"></i> Post New Job
                            </button>
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
                            <input type="text" class="form-input w-full pl-10 px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search jobs..." v-model="searchQuery" @input="filterJobs">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 w-full lg:ml-20">
                        <select class="form-select px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-auto lg:w-[250px]" v-model="filters.type">
                            <option value="">All Types</option>
                            <option v-for="type in uniqueTypes" :key="type">{{ type }}</option>
                        </select>
                        <select class="form-select px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-auto lg:w-[250px]" v-model="filters.location">
                            <option value="">All Locations</option>
                            <option v-for="location in uniqueLocations" :key="location">{{ location }}</option>
                        </select>
                        <select class="form-select px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-auto lg:w-[210px]"v-model="filters.status">
                            <option value="">All Statuses</option>
                            <option v-for="status in uniqueStatuses" :key="status">{{ status }}</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-center">
                            <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                <th class="px-4 py-2 text-center">Job Title</th>
                                <th class="px-4 py-2 text-center">Type</th>
                                <th class="px-4 py-2 text-center">Location</th>
                                <th class="px-4 py-2 text-center">Status</th>
                                <th class="px-4 py-2 text-center">Posted Date</th>
                                <th class="px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr v-for="job in paginatedJobs" :key="job.id" class="border-b border-gray-200 dark:border-gray-600">
                                <td class="px-4 py-2 font-semibold text-gray-800 dark:text-gray-200 text-center">{{ job.title }}</td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200 text-center">{{ job.type }}</td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200 text-center">{{ job.location }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span :class="['inline-block px-2 py-1 rounded text-xs font-semibold', job.status === 'Active' ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200']">
                                            {{ job.status }}
                                        </span>
                                    </td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200 text-center">{{ formatDate(job.created_at) }}</td>
                                <td class="px-4 py-2 text-center">
                                    <div class="relative inline-block text-left">
                                        <button @click="toggleActionDropdown(job.id)" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none text-gray-500 dark:text-gray-200">
                                            <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                        <div v-if="actionDropdown === job.id" class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" @click.prevent="viewJob(job)"><i class="fas fa-eye mr-2"></i>View</a>
                                                <a href="#" class="block px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-100 dark:hover:bg-yellow-800" @click.prevent="openEditModal(job)"><i class="fas fa-edit mr-2"></i>Edit</a>
                                                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-100 dark:hover:bg-red-800" @click.prevent="confirmDelete(job)"><i class="fas fa-trash mr-2"></i>Delete</a>
                                            </div>
                                        </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredJobs.length === 0">
                                <td colspan="6" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-briefcase text-4xl text-gray-300 mb-2"></i>
                                        <span class="text-lg text-gray-400">No job postings found</span>
                                    </div>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <!-- Pagination -->
                <div class="flex flex-col md:flex-row md:items-center justify-center md:justify-between mt-4 gap-2">
                    <div class="text-gray-600 dark:text-gray-300 text-sm text-center md:text-left w-full md:w-auto flex justify-center md:justify-start">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredJobs.length) }} of {{ filteredJobs.length }} entries
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
            <!-- Add/Edit Job Modal -->
            <div v-if="showJobModal && (modalMode === 'add' || modalMode === 'edit')" class="fixed inset-0 z-[210] flex items-center justify-center bg-black bg-opacity-50 pointer-events-auto" role="dialog" aria-modal="true" data-modal="add-edit-job-modal">
              <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-2 p-6 relative max-h-[90vh] overflow-y-auto pointer-events-auto">
                <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeJobModal"><i class="fas fa-times"></i></button>
                <div class="mt-3 text-left w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                    {{ modalMode === 'edit' ? 'Edit Job Posting' : 'Post New Job' }}
                  </h3>
                  <form @submit.prevent="modalMode === 'edit' ? updateJob() : addJob()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="jobTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Job Title*</label>
                            <input id="jobTitle" type="text" v-model="jobForm.title" required autocomplete="off"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            @input="onJobTitleChange">
                        </div>
                      <div>
                        <label for="jobType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type*</label>
                        <select id="jobType" v-model="jobForm.type" required
                          class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Type</option>
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Freelance">Freelance</option>
                                    </select>
                                </div>
                     <div>
                        <label for="jobLocation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location*</label>
                        <div class="relative">
                            <input id="jobLocation" type="text" v-model="jobForm.location" required autocomplete="off"
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                              @input="fetchJobLocationSuggestions" @focus="showJobLocationSuggestions = true" @blur="hideJobLocationSuggestions" placeholder="Start typing address...">
                            <ul v-if="showJobLocationSuggestions && jobLocationSuggestions.length" class="absolute z-10 left-0 right-0 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow mt-1 max-h-48 overflow-y-auto">
                              <li v-for="suggestion in jobLocationSuggestions" :key="suggestion" @mousedown.prevent="selectJobLocationSuggestion(suggestion)" class="px-4 py-2 cursor-pointer text-gray-800 dark:text-gray-100 hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-900 dark:hover:text-white transition">
                                {{ suggestion }}
                              </li>
                            </ul>
                        </div>
                      </div>
                      <div>
                        <label for="jobSalary" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Salary</label>
                        <input id="jobSalary" type="text" v-model="jobForm.salary" autocomplete="off"
                          class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                      <div>
                        <label for="jobStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status*</label>
                        <select id="jobStatus" v-model="jobForm.status" required
                          class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="Active">Active</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <div>
                            <label for="jobDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description*</label>
                            <textarea id="jobDescription" v-model="jobForm.description" rows="2" required
                          class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                        <!-- Requirements -->
                        <div>
                            <label for="jobRequirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Requirements*
                            </label>
                            <div class="relative mt-1">
                                <textarea id="jobRequirements" v-model="jobForm.requirements" rows="3" required
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        @input="generateSuggestions('requirements')"
                                        @focus="generateSuggestions('requirements')"
                                        @blur="hideSuggestionsWithDelay('requirements')"></textarea>
                                <!-- Requirements Suggestions Dropdown -->
                                <div v-if="showRequirementsSuggestions && requirementsSuggestions.length" 
                                    class="absolute z-20 left-0 right-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow-lg max-h-60 overflow-y-auto">
                                <div class="p-2 bg-gray-100 dark:bg-gray-700 flex justify-between items-center text-gray-800 dark:text-white">
                                    <span class="text-sm font-medium">Suggestions for "{{ jobForm.title }}"</span>
                                    <button @click="closeSuggestions('requirements')" class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <button @click="applyAllSuggestions('requirements')" 
                                        class="w-full text-left px-4 py-2 text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50">
                                    <i class="fas fa-check-double mr-2"></i> Apply All Suggestions
                                </button>
                                <div v-for="(suggestion, index) in requirementsSuggestions" :key="index" 
                                    @mousedown.prevent="selectSuggestion('requirements', suggestion)"
                                    class="px-4 py-2 cursor-pointer text-gray-800 dark:text-gray-100 hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-900 dark:hover:text-blue-100 transition border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                     {{ suggestion }}
                                </div>
                                </div>
                            </div>
                        </div>

                            <!-- Qualifications -->
                        <div>
                            <label for="jobQualifications" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Qualifications*
                            </label>
                            <div class="relative mt-1">
                                <textarea id="jobQualifications" v-model="jobForm.qualifications" rows="3" required
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        @input="generateSuggestions('qualifications')"
                                        @focus="generateSuggestions('qualifications')"
                                        @blur="hideSuggestionsWithDelay('qualifications')"></textarea>
                                <!-- Qualifications Suggestions Dropdown -->
                                <div v-if="showQualificationsSuggestions && qualificationsSuggestions.length" 
                                    class="absolute z-20 left-0 right-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow-lg max-h-60 overflow-y-auto">
                                <div class="p-2 bg-gray-100 dark:bg-gray-700 flex justify-between items-center text-gray-800 dark:text-white">
                                    <span class="text-sm font-medium">Suggestions for "{{ jobForm.title }}"</span>
                                    <button @click="closeSuggestions('qualifications')" class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <button @click="applyAllSuggestions('qualifications')" 
                                        class="w-full text-left px-4 py-2 text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50">
                                    <i class="fas fa-check-double mr-2"></i> Apply All Suggestions
                                </button>
                                <div v-for="(suggestion, index) in qualificationsSuggestions" :key="index" 
                                    @mousedown.prevent="selectSuggestion('qualifications', suggestion)"
                                    class="px-4 py-2 cursor-pointer text-gray-800 dark:text-gray-100 hover:bg-blue-100 dark:hover:bg-blue-900 hover:text-blue-900 dark:hover:text-blue-100 transition border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                     {{ suggestion }}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                      <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                        {{ modalMode === 'edit' ? 'Update Job' : 'Post Job' }}
                      </button>
                      <button type="button" @click="closeJobModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                      </button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
        <!-- Delete Confirmation Modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50 lg:items-start lg:pt-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative lg:mx-0">
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">Confirm Delete</h3>
                    <p class="mb-6 text-gray-700 dark:text-gray-200">Are you sure you want to delete this job posting?</p>
                    <div class="flex justify-end gap-2">
                        <button class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-700" @click="showDeleteModal = false">Cancel</button>
                        <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition" @click="deleteJob">Delete</button>
                    </div>
                </div>
            </div>
            <!-- View Job Modal -->
            <div v-if="showJobModal && modalMode === 'view'" class="fixed inset-0 z-[210] flex items-center justify-center bg-black bg-opacity-50 pointer-events-auto" role="dialog" aria-modal="true" data-modal="view-job-modal">
              <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xl mx-2 p-0 relative max-h-[95vh] overflow-y-auto pointer-events-auto">
                <button class="absolute top-3 right-3 flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-full shadow hover:bg-gray-200 dark:hover:bg-gray-600 transition text-base font-semibold z-20" @click="closeJobModal" id="close-view-job-modal-btn">
                  <i class="fas fa-times"></i> <span>Close</span>
                </button>
                <div class="rounded-t-2xl bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 dark:from-blue-900 dark:via-blue-800 dark:to-blue-700 px-0 pt-6 pb-8 flex flex-col items-center relative">
                  <div class="absolute top-4 left-4 bg-white dark:bg-gray-700 rounded-full p-2 shadow-lg">
                    <i class="fas fa-briefcase text-blue-600 dark:text-blue-300 text-2xl"></i>
                  </div>
                  <div class="w-28 h-28 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-4 border-white dark:border-gray-700 shadow-xl mb-2 mt-2">
                    <i class="fas fa-briefcase text-4xl text-gray-400"></i>
                  </div>
                  <h3 class="text-3xl font-extrabold text-white drop-shadow-lg mb-1 text-center">{{ selectedJob.title }}</h3>
                  <span :class="['inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold shadow', selectedJob.status === 'Active' ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200' : 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200']">{{ selectedJob.status }}</span>
                </div>
                <div class="px-6 py-6">
                  <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-info-circle text-blue-500 dark:text-blue-300"></i> <span>Job Details</span></h4>
                  <div class="grid grid-cols-1 gap-3 bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-3"><i class="fas fa-map-marker-alt text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Location:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedJob.location }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-calendar-alt text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Posted Date:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ formatDate(selectedJob.created_at) }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-money-bill-wave text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Salary:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedJob.salary || 'N/A' }}</span></div>
                    <div class="flex items-center gap-3"><i class="fas fa-clipboard-list text-blue-500 dark:text-blue-300"></i><span class="font-semibold text-gray-700 dark:text-gray-200">Type:</span> <span class="ml-1 text-gray-700 dark:text-gray-200">{{ selectedJob.type }}</span></div>
                  </div>
                  <div class="my-6 border-t border-gray-200 dark:border-gray-700"></div>
                  <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-align-left text-blue-500 dark:text-blue-300"></i> <span>Description</span></h4>
                  <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-4 whitespace-pre-line text-gray-700 dark:text-gray-200">{{ selectedJob.description }}</div>
                  <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-tasks text-blue-500 dark:text-blue-300"></i> <span>Requirements</span></h4>
                  <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-4 whitespace-pre-line text-gray-700 dark:text-gray-200">• {{ selectedJob.requirements }}</div>
                  <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-graduation-cap text-blue-500 dark:text-blue-300"></i> <span>Qualifications</span></h4>
                  <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm mb-4 whitespace-pre-line text-gray-700 dark:text-gray-200">• {{ selectedJob.qualifications }}</div>
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

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
    <script src="js/employer_jobposting.js"></script>
</body>
</html>