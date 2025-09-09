<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
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
    <title>Alumni | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/admin_alumni.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
</head>
<body :class="[darkMode ? 'dark' : '', 'font-sans bg-slate-200 dark:bg-slate-600 min-h-screen']" id="app" v-cloak>
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
        <div class="bg-white dark:bg-gray-700 shadow-sm h-[70px] border-b border-slate-200 dark:border-slate-700">
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
            <a href="admin_applicant" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
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
            
            <!-- Alumni Dropdown with Active State -->
            <div class="mx-2 mb-1">
                <!-- Button (Blue) -->
                <button 
                    @click="alumniDropdownOpen = !alumniDropdownOpen" 
                    class="flex items-center w-full px-6 py-3 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 dark:hover:bg-blue-500/30 transition-colors duration-200 borde            r-l-4 border-blue-500 dark:border-blue-400"
                >
                    <i class="fas fa-user-graduate w-5 mr-3 text-center text-cyan-500 dark:text-cyan-400"></i>
                    <span class="font-medium">Alumni</span>
                    <i class="fas fa-chevron-down ml-auto text-xs transition-transform duration-200" :class="{'rotate-180': alumniDropdownOpen}"></i>
                </button>

                <!-- Dropdown Links -->
                <div class="overflow-hidden mt-[10px] transition-all duration-300 ease-in-out" :style="alumniDropdownOpen ? 'max-height: 100px' : 'max-height: 0'">
                    <!-- Active Link (Darker Blue) -->
                    <a 
                        href="admin_alumni" 
                        class="block py-2 pl-14 pr-6 mx-2 rounded-lg text-sm text-blue-700 dark:text-blue-300 hover:bg-blue-500/40 dark:hover:bg-blue-500/40 transition-colors duration-200"
                        @click="handleNavClick"
                    >
                        Manage Alumni
                    </a>

                    <!-- Inactive Link (Lighter Blue) -->
                    <a 
                        href="admin_alumni_pending"
                       class="block py-2 pl-14 pr-6 mx-2 rounded-lg text-sm hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200"
                        @click="handleNavClick"
                    >
                        Pending Alumni
                    </a>
                </div>
            </div>
            
            <!-- Accounts -->
            <a href="admin_user" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200">
                <i class="fas fa-user-shield w-5 mr-3 text-center text-red-500 dark:text-red-400"></i>
                <span class="font-medium">Accounts</span>
            </a>
            
            <!-- Reports -->
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
    
    <!-- Header (always fixed, not pushed by sidebar) -->
    <header class="fixed top-0 left-0 right-0 h-[70px] bg-white dark:bg-slate-600 shadow-md z-40 flex items-center px-4">
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
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-red-100 dark:hover:bg-red-500  hover:text-red-400 dark:hover:text-red-200" href="#" @click.prevent="showLogoutModal = true">
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
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <h2 class="text-2xl font-bold mb-2 md:mb-0 text-gray-800 dark:text-gray-100">Alumni Profiles</h2>
                    <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full md:w-auto">
                        <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition w-full sm:w-auto justify-center" @click="openAddModal">
                            <i class="fas fa-plus"></i> Add Alumni
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition w-full sm:w-auto justify-center" @click="exportToExcel">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition w-full sm:w-auto justify-center" @click="exportToPDF">
                            <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:items-center md:justify-start gap-4 mb-4">
                    <div class="flex items-center gap-2 w-full md:w-auto mb-2 md:mb-0">
                        <div class="relative w-full md:w-80">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" class="form-input w-full pl-10 px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search alumni..." v-model="searchQuery" @input="filterAlumni">
                        </div>
                    </div>
                    <!-- Advanced Filter Dropdowns -->
                    <div class="flex flex-col sm:flex-row gap-2 w-full md:ml-auto md:w-auto">
                        <select class="form-select px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-[150px] md:w-[180px]" v-model="filters.college" @change="updateFilterCourseOptions">
                            <option value="">All Colleges</option>
                            <option v-for="college in colleges" :key="college.name" :value="college.name">{{ college.name }}</option>
                        </select>
                        <select class="form-select px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-[150px] md:w-[180px] lg:w-[250px]" v-model="filters.course" :disabled="!filters.college">
                            <option value="">All Courses</option>
                            <option v-for="course in filterCourseOptions" :key="course" :value="course">{{ course }}</option>
                        </select>
                        <select class="form-select px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 w-full sm:w-[150px] md:w-[180px] lg:w-[210px]" v-model="filters.status">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Pending">Pending</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left data-table">
                            <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Gender</th>
                                <th class="px-4 py-2">Year Graduated</th>
                                <th class="px-4 py-2">Course</th>
                                <th class="px-4 py-2">College</th>
                                <th class="px-4 py-2">Province</th>
                                <th class="px-4 py-2">City/Municipality</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr v-for="alumni in paginatedAlumni" :key="alumni.id" class="border-b border-gray-200 dark:border-gray-600 text-center text-gray-800 dark:text-gray-200">
                                <td class="px-4 py-2 font-semibold">{{ alumni.first_name }} {{ alumni.middle_name }} {{ alumni.last_name }}</td>
                                <td class="px-4 py-2">{{ alumni.email }}</td>
                                <td class="px-4 py-2">{{ alumni.gender }}</td>
                                <td class="px-4 py-2">{{ alumni.year_graduated }}</td>
                                <td class="px-4 py-2">{{ alumni.course }}</td>
                                <td class="px-4 py-2">{{ alumni.college }}</td>
                                <td class="px-4 py-2">{{ alumni.province }}</td>
                                <td class="px-4 py-2">{{ alumni.city }}</td>
                                <td class="px-4 py-2">
                                    <span :class="['inline-block px-2 py-1 rounded text-xs font-semibold', alumni.status === 'Active' ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200' : alumni.status === 'Pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-200' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200']">
                                            {{ alumni.status }}
                                        </span>
                                    </td>
                                <td class="px-4 py-2">
                                    <div class="relative inline-block text-left">
                                        <button @click="toggleActionDropdown(alumni.id)" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none text-gray-600 dark:text-gray-300">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                        <div v-if="actionDropdown === alumni.id" class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-10">
                                            <div class="py-1">
                                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" @click.prevent="viewAlumniDetails(alumni)"><i class="fas fa-eye mr-2"></i>View</a>
                                                <a href="#" class="block px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-100 dark:hover:bg-yellow-800" @click.prevent="editAlumni(alumni)"><i class="fas fa-edit mr-2"></i>Edit</a>
                                                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-100 dark:hover:bg-red-800" @click.prevent="confirmDelete(alumni)"><i class="fas fa-trash mr-2"></i>Delete</a>
                                            </div>
                                        </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredAlumni.length === 0">
                                <td colspan="10" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-user-graduate text-4xl text-gray-300 mb-2"></i>
                                        <span class="text-lg text-gray-400">No alumni found</span>
                                    </div>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <!-- Pagination -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 gap-2">
                    <div class="text-gray-600 dark:text-gray-300 text-sm text-center md:text-left">
                        Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredAlumni.length) }} of {{ filteredAlumni.length }} entries
                    </div>
                    <div class="flex gap-1 justify-center">
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
    </main>
    <!-- Add/Edit Alumni Modal -->
    <div v-if="showAlumniModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50" role="dialog" aria-modal="true" data-modal="alumni-modal">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeAlumniModal" aria-label="Close"><i class="fas fa-times"></i></button>
            <div class="mt-3 text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                    {{ selectedAlumni ? 'Edit Alumni' : 'Add New Alumni' }}
                </h3>
                <form @submit.prevent="selectedAlumni ? updateAlumni() : addAlumni()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name*</label>
                            <input type="text" v-model="alumniForm.first_name" required autocomplete="off"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" id="first-name-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Middle Name</label>
                            <input type="text" v-model="alumniForm.middle_name" autocomplete="off"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name*</label>
                            <input type="text" v-model="alumniForm.last_name" required autocomplete="off"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email*</label>
                            <input type="email" v-model="alumniForm.email" required autocomplete="off"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Secondary Email</label>
                            <input type="email" v-model="alumniForm.secondary_email" autocomplete="off"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender*</label>
                            <select v-model="alumniForm.gender" required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year Graduated*</label>
                            <input type="text" v-model="alumniForm.year_graduated" required autocomplete="off"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">College*</label>
                            <select v-model="alumniForm.college" required @change="updateCourseOptions"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select College</option>
                                <option v-for="college in colleges" :key="college.name" :value="college.name">{{ college.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Course*</label>
                            <select v-model="alumniForm.course" required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Course</option>
                                <option v-for="course in courseOptions" :key="course">{{ course }}</option>
                            </select>
                                </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Province*</label>
                            <select v-model="alumniForm.province" required @change="fetchCities"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Province</option>
                                <option v-for="province in provinces" :key="province.code" :value="province.name">{{ province.name }}</option>
                            </select>
                                </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">City/Municipality*</label>
                            <select v-model="alumniForm.city" required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select City/Municipality</option>
                                <option v-for="city in cities" :key="city.code" :value="city.name">{{ city.name }}</option>
                            </select>
                                </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status*</label>
                            <select v-model="alumniForm.status" required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="Active">Active</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                            {{ selectedAlumni ? 'Update Alumni' : 'Add Alumni' }}
                        </button>
                        <button type="button" @click="closeAlumniModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- View Alumni Details Modal -->
        <div v-if="showViewModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50" role="dialog" aria-modal="true" data-modal="view-modal">
            <div v-if="isLoading" class="text-center py-4 text-white">Loading alumni details...</div>
            <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xl mx-2 p-0 relative max-h-[95vh] overflow-y-auto">
                <button class="absolute top-2 right-2 flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-full shadow hover:bg-gray-200 dark:hover:bg-gray-600 transition text-base font-semibold z-20" @click="closeViewModal" aria-label="Close">
                    <i class="fas fa-times"></i> <span>Close</span>
                </button>
                <div class="rounded-t-2xl bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 dark:from-blue-900 dark:via-blue-800 dark:to-blue-700 px-0 pt-6 pb-8 flex flex-col items-center relative">
                    <div class="absolute top-4 left-4 bg-white dark:bg-gray-700 rounded-full p-2 shadow-lg">
                        <i class="fas fa-user-graduate text-blue-600 dark:text-blue-300 text-2xl"></i>
                    </div>
                    <img v-if="viewAlumniData.profile_picture" :src="viewAlumniData.profile_picture" alt="Alumni Photo" class="w-28 h-28 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-xl mb-2 mt-2">
                    <div v-else class="w-28 h-28 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-4 border-white dark:border-gray-700 shadow-xl mb-2 mt-2">
                        <i class="fas fa-user-graduate text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-white drop-shadow-lg mb-1 text-center">{{ viewAlumniData.first_name }} {{ viewAlumniData.middle_name }} {{ viewAlumniData.last_name }}</h3>
                    <span :class="['inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold shadow', viewAlumniData.status === 'Active' ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200' : viewAlumniData.status === 'Pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-200' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200']">{{ viewAlumniData.status }}</span>
                </div>
                <div class="px-6 py-6">
                    <!-- Personal Details -->
                    <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500 dark:text-blue-300"></i> <span>Personal Details</span>
                    </h4>
                    <div class="grid grid-cols-1 gap-3 bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-envelope text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Email:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.email || 'N/A' }}</span>
                        </div>
                        <div v-if="viewAlumniData.secondary_email" class="flex items-center gap-3">
                            <i class="fas fa-envelope-open text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Secondary Email:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.secondary_email }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-venus-mars text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Gender:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.gender || 'N/A' }}</span>
                        </div>
                        <div v-if="viewAlumniData.birthdate" class="flex items-center gap-3">
                            <i class="fas fa-birthday-cake text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Birthdate:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ formatDate(viewAlumniData.birthdate) }}</span>
                        </div>
                        <div v-if="viewAlumniData.contact" class="flex items-center gap-3">
                            <i class="fas fa-phone text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Contact:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.contact }}</span>
                        </div>
                        <div v-if="viewAlumniData.civil_status" class="flex items-center gap-3">
                            <i class="fas fa-ring text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Civil Status:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.civil_status }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-calendar-alt text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Year Graduated:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.year_graduated || 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-university text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">College:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.college || 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-book text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Course:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.course || 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">Province:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.province || 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-city text-blue-500 dark:text-blue-300"></i>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">City/Municipality:</span>
                            <span class="ml-1 text-gray-700 dark:text-gray-200">{{ viewAlumniData.city || 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <!-- Education Section -->
                    <div class="my-6 border-t border-gray-200 dark:border-gray-700"></div>
                    <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <i class="fas fa-graduation-cap text-blue-500 dark:text-blue-300"></i> <span>Education</span>
                    </h4>
                    <div v-if="viewAlumniData.education && viewAlumniData.education.length" class="space-y-4 mb-4">
                        <div v-for="edu in viewAlumniData.education" :key="edu.education_id" class="grid grid-cols-1 gap-3 bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-certificate text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Degree:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ edu.degree || 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-school text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">School:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ edu.school || 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-calendar-alt text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Period:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-200">
                                    {{ formatDate(edu.start_date) }} - {{ edu.current ? 'Present' : formatDate(edu.end_date) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-gray-500 dark:text-gray-400 mb-4">No education information available.</div>
                    
                    <!-- Employment History -->
                    <div class="my-6 border-t border-gray-200 dark:border-gray-700"></div>
                    <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <i class="fas fa-briefcase text-blue-500 dark:text-blue-300"></i> <span>Employment History</span>
                    </h4>
                    <div v-if="viewAlumniData.experiences && viewAlumniData.experiences.length" class="space-y-4 mb-4">
                        <div v-for="exp in viewAlumniData.experiences" :key="exp.experience_id" class="grid grid-cols-1 gap-3 bg-gray-50 dark:bg-gray-900 rounded-xl p-4 shadow-sm">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-building text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Company Name:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ exp.company || 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-user-tie text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Position:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ exp.title || 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-calendar-alt text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Period:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-200">
                                    {{ formatDate(exp.start_date) }} - {{ exp.current ? 'Present' : formatDate(exp.end_date) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-clipboard-check text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Employment Status:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ exp.employment_status || 'N/A' }}</span>
                            </div>
                            <div v-if="exp.description" class="flex items-start gap-3">
                                <i class="fas fa-align-left text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Description:</span>
                                <span class="ml-1 text-gray-600 dark:text-gray-300">{{ exp.description }}</span>
                            </div>
                            <div v-if="exp.location_of_work" class="flex items-center gap-3">
                                <i class="fas fa-map-pin text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Location:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ exp.location_of_work }}</span>
                            </div>
                            <div v-if="exp.employment_sector" class="flex items-center gap-3">
                                <i class="fas fa-industry text-blue-500 dark:text-blue-300"></i>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">Sector:</span>
                                <span class="ml-1 text-gray-700 dark:text-gray-200">{{ exp.employment_sector }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-gray-500 dark:text-gray-400 mb-4">No employment history available.</div>
                    
                    <!-- Skills Section -->
                    <div class="my-6 border-t border-gray-200 dark:border-gray-700"></div>
                    <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-blue-500 dark:text-blue-300"></i> <span>Skills</span>
                    </h4>
                    <div v-if="viewAlumniData.skills && viewAlumniData.skills.length" class="flex flex-wrap gap-2 mb-4">
                        <span v-for="(skill, index) in viewAlumniData.skills" :key="index" 
                            class="inline-flex items-center bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-200 px-3 py-1 rounded-full text-xs font-semibold shadow">
                            {{ skill.name || skill }}
                            <span v-if="skill.certificate" class="ml-1 text-xs">(Certified)</span>
                        </span>
                    </div>
                    <div v-else class="text-gray-500 dark:text-gray-400 mb-4">No skills listed.</div>
                    
                    <!-- Documents Section -->
                    <div class="my-6 border-t border-gray-200 dark:border-gray-700"></div>
                    <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <i class="fas fa-file-alt text-blue-500 dark:text-blue-300"></i> <span>Documents Submitted</span>
                    </h4>
                    <div v-if="viewAlumniData.verification_document" class="space-y-3 mb-4">
                        <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i :class="getFileIcon(viewAlumniData.verification_document)"></i>
                                <span class="font-medium text-gray-700 dark:text-gray-200">Verification Document</span>
                            </div>
                            <div class="flex gap-2">
                                <a :href="'uploads/documents/' + viewAlumniData.verification_document" target="_blank" 
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-300 dark:hover:text-blue-200 flex items-center gap-1">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a :href="'uploads/documents/' + viewAlumniData.verification_document" download 
                                    class="text-green-600 hover:text-green-800 dark:text-green-300 dark:hover:text-green-200 flex items-center gap-1">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-gray-500 dark:text-gray-400 mb-4">No documents submitted.</div>
                    
                    <!-- Contact Button -->
                    <div class="flex flex-col md:flex-row gap-3 mt-6 w-full">
                        <button @click="contactAlumni(viewAlumniData)" 
                                class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2 text-sm sm:text-base">
                            <i class="fas fa-envelope"></i> Contact
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <!-- Delete Confirmation Modal for Alumni -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">Confirm Delete</h3>
            <p class="mb-6 text-gray-700 dark:text-gray-200">Are you sure you want to delete this alumni?</p>
            <div class="flex justify-end gap-2">
                <button class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-700" @click="showDeleteModal = false">Cancel</button>
                <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition" @click="confirmDeleteAlumni">Delete</button>
            </div>
        </div>
    </div>
    <!-- Footer (same as jobs page) -->
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
    <script src="js/admin_alumni.js"></script>
</body>
</html>