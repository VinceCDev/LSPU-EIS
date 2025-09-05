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
    <title>Employer Dashboard | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/employer_dashboard.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
</head>
<body :class="[darkMode ? 'dark' : '', 'font-sans bg-gray-50 dark:bg-gray-800 min-h-screen']" id="app" v-cloak>
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
            <a href="employer_dashboard" class="flex items-center px-6 py-3 mx-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 dark:hover:bg-blue-500/30 transition-colors duration-200 border-l-4 border-blue-500 dark:border-blue-400" @click="handleNavClick">
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
                    <div class="cursor-pointer flex items-center" @click.stop="toggleProfileDropdown">
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
                        <div v-if="profileDropdownOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-600 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-500 transform origin-top-right" @click.stop>
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
        <div class="container-fluid">
            <div class="pb-5">
                <h2 class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                    Welcome, {{ employerProfile.company_name ? employerProfile.company_name.split(' ')[0] : 'Employer' }}!
                </h2>
                <p class="text-gray-600 dark:text-gray-300">
                Welcome to the Employer Portal of the Laguna State Polytechnic University Employment Information System.
                </p>
            </div>
            <!-- Dashboard Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 relative overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-md">
                    <div class="text-gray-600 dark:text-gray-300 font-semibold mb-4">Active Jobs</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ activeJobs }}</div>
                    <div class="text-sm text-green-500">
                        <i class="fas fa-arrow-up mr-1"></i> {{ jobsChange }} from last week
                    </div>
                    <i class="fas fa-briefcase text-blue-600 dark:text-blue-400 opacity-10 text-5xl absolute right-5 top-5"></i>
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 relative overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-md">
                    <div class="text-gray-600 dark:text-gray-300 font-semibold mb-4">Total Applicants</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ totalApplicants }}</div>
                    <div class="text-sm text-green-500">
                        <i class="fas fa-arrow-up mr-1"></i> {{ applicantsChange }} from last week
                    </div>
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 opacity-10 text-5xl absolute right-5 top-5"></i>
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 relative overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-md">
                    <div class="text-gray-600 dark:text-gray-300 font-semibold mb-4">Applications</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ applicationsCount }}</div>
                    <div class="text-sm text-green-500">
                        <i class="fas fa-arrow-up mr-1"></i> 0 from last week
                    </div>
                    <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 opacity-10 text-5xl absolute right-5 top-5"></i>
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 relative overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-md">
                    <div class="text-gray-600 dark:text-gray-300 font-semibold mb-4">Hired</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ hiredCount }}</div>
                    <div class="text-sm text-green-500">
                        <i class="fas fa-arrow-up mr-1"></i> 0 from last week
                    </div>
                    <i class="fas fa-user-check text-blue-600 dark:text-blue-400 opacity-10 text-5xl absolute right-5 top-5"></i>
                </div>
            </div>
            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                    <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Applicants by Course</h5>
                    <div class="relative h-80 w-full min-h-[350px]">
                        <canvas id="applicantsByCourseChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                    <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Applicants by Status</h5>
                    <div class="relative h-80 w-full">
                        <canvas id="applicantsByStatusChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                    <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Job Listings by Type</h5>
                    <div class="relative h-80 w-full">
                        <canvas id="jobsByTypeChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                    <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Applicants by Year Graduated</h5>
                    <div class="relative h-80 w-full">
                        <canvas id="applicantsByYearChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-4 sm:p-6 flex flex-col">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-3">
                        <h5 class="font-semibold text-lg dark:text-gray-200 text-center sm:text-left">Calendar</h5>
                        <div class="flex items-center justify-center space-x-2">
                            <button @click="prevMonth" class="px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                <i class="fas fa-chevron-left text-xs sm:text-sm"></i>
                            </button>
                            <span class="font-semibold text-gray-700 dark:text-gray-200 text-sm sm:text-base min-w-[120px] text-center">{{ calendarMonthYear }}</span>
                            <button @click="nextMonth" class="px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                <i class="fas fa-chevron-right text-xs sm:text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div id="calendar" class="flex-1 flex items-center justify-center overflow-x-auto">
                        <table class="w-full text-center border-collapse min-w-[300px]">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="py-2 px-1 sm:px-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-white font-medium text-xs sm:text-sm">Sun</th>
                                    <th class="py-2 px-1 sm:px-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-white font-medium text-xs sm:text-sm">Mon</th>
                                    <th class="py-2 px-1 sm:px-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-white font-medium text-xs sm:text-sm">Tue</th>
                                    <th class="py-2 px-1 sm:px-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-white font-medium text-xs sm:text-sm">Wed</th>
                                    <th class="py-2 px-1 sm:px-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-white font-medium text-xs sm:text-sm">Thu</th>
                                    <th class="py-2 px-1 sm:px-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-white font-medium text-xs sm:text-sm">Fri</th>
                                    <th class="py-2 px-1 sm:px-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-white font-medium text-xs sm:text-sm">Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(week, widx) in calendarWeeks" :key="widx">
                                    <td v-for="(day, didx) in week" :key="didx" class="p-1 sm:p-2 border border-gray-300 dark:border-gray-600">
                                        <span v-if="day.day > 0" :class="[
                                            isToday(day.day, day.monthOffset) ? 'bg-blue-500 text-white' : (day.monthOffset === 0 ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-400'),
                                            'rounded-full px-1 sm:px-2 inline-block w-6 h-6 sm:w-8 sm:h-8 leading-6 sm:leading-8 text-center select-none transition-colors duration-150 text-xs sm:text-sm'
                                        ]">{{ day.day }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="js/employer_dashboar.js"></script>
</body>
</html>