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
    <title>Administrator Dashboard | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.0.77/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsPDF/2.5.1/jspdf.umd.min.js"></script>
    <style>
        [v-cloak] { display: none !important; }
    </style>
</head>

<body :class="[darkMode ? 'dark' : '', 'font-sans bg-gray-50 dark:bg-gray-800 min-h-screen']" id="app" v-cloak>
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
    
    <!-- Rest of your sidebar content remains the same -->
            <div class="overflow-y-auto pt-4 pb-20 h-[calc(100%-64px)] scrollbar-thin scrollbar-thumb-slate-300 scrollbar-track-slate-100 dark:scrollbar-thumb-slate-600 dark:scrollbar-track-slate-800/50">
                <!-- Main Section -->
                <div class="px-6 py-2 mb-2">
                    <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Main</span>
                </div>
                
                <!-- Dashboard -->
                <a href="admin_dashboard" class="flex items-center px-6 py-3 mx-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 dark:hover:bg-blue-500/30 transition-colors duration-200 border-l-4 border-blue-500 dark:border-blue-400" @click="handleNavClick">
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

    <!-- Header (always fixed, not pushed by sidebar) -->
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

    <!-- Main Content -->
    <main :class="[isMobile ? 'ml-0' : (sidebarActive ? 'ml-[280px]' : 'ml-0'), 'transition-all duration-300 min-h-[calc(100vh-70px)] p-6 pt-lg-5 mt-[70px] bg-gray-50 dark:bg-gray-800']">
        <div class="container-fluid">
            <!-- Dashboard Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 relative overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-md">
                    <div class="text-gray-600 dark:text-gray-300 font-semibold mb-4">Jobs</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ dashboardStats && dashboardStats.total_jobs ? dashboardStats.total_jobs : 0 }}</div>
                    <div class="text-sm text-green-500">
                        <i class="fas fa-arrow-up mr-1"></i>
                        {{ dashboardStats && dashboardStats.jobs_yesterday ? dashboardStats.jobs_yesterday + ' from yesterday' : '0 from yesterday' }}
                    </div>
                    <i class="fas fa-briefcase text-blue-600 dark:text-blue-400 opacity-10 text-5xl absolute right-5 top-5"></i>
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 relative overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-md">
                    <div class="text-gray-600 dark:text-gray-300 font-semibold mb-4">Applications</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ dashboardStats && dashboardStats.total_applications ? dashboardStats.total_applications : 0 }}</div>
                    <div class="text-sm text-green-500">
                        <i class="fas fa-arrow-up mr-1"></i>
                        {{ dashboardStats && dashboardStats.applications_yesterday ? dashboardStats.applications_yesterday + ' from yesterday' : '0 from yesterday' }}
                    </div>
                    <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 opacity-10 text-5xl absolute right-5 top-5"></i>
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 relative overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-md">
                    <div class="text-gray-600 dark:text-gray-300 font-semibold mb-4">Companies</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ dashboardStats && dashboardStats.total_companies ? dashboardStats.total_companies : 0 }}</div>
                    <div class="text-sm text-green-500">
                        <i class="fas fa-arrow-up mr-1"></i>
                        {{ dashboardStats && dashboardStats.companies_yesterday ? dashboardStats.companies_yesterday + ' from yesterday' : '0 from yesterday' }}
                    </div>
                    <i class="fas fa-building text-blue-600 dark:text-blue-400 opacity-10 text-5xl absolute right-5 top-5"></i>
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 relative overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-md">
                    <div class="text-gray-600 dark:text-gray-300 font-semibold mb-4">Alumni</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ dashboardStats && dashboardStats.total_alumni ? dashboardStats.total_alumni : 0 }}</div>
                    <div class="text-sm text-green-500">
                        <i class="fas fa-arrow-up mr-1"></i>
                        {{ dashboardStats && dashboardStats.alumni_yesterday ? dashboardStats.alumni_yesterday + ' from yesterday' : '0 from yesterday' }}
                    </div>
                    <i class="fas fa-user-graduate text-blue-600 dark:text-blue-400 opacity-10 text-5xl absolute right-5 top-5"></i>
                </div>
            </div>
            <!-- First Row of Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Chart 1: Graduates and Employment per College -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                    <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Graduates and Employment per College</h5>
                    <div class="relative h-80 w-full">
                        <canvas id="graduatesChart"></canvas>
                    </div>
                </div>
                
                <!-- Chart 2: Course-Work Alignment (Donut Chart) -->
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                    <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Course-Work Alignment</h5>
                    <div class="relative h-80 w-full">
                        <canvas id="alignmentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Second Row of Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Chart 3: Employment Status per Program -->
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                    <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Employment Status per Program</h5>
                    <div class="relative h-80 w-full">
                        <canvas id="employmentStatusChart"></canvas>
                    </div>
                </div>
                
                <!-- Chart 4: Work Location Distribution -->
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                    <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Work Location Distribution</h5>
                    <div class="relative h-80 w-full">
                        <canvas id="locationChart"></canvas>
                    </div>
                </div>
                
                <!-- Chart 5: Employment Sector -->
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6">
                    <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Employment Sector</h5>
                    <div class="relative h-80 w-full">
                        <canvas id="sectorChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 mb-6">
                <h5 class="font-semibold text-lg dark:text-gray-200 mb-4">Alumni Location Map</h5>
                <div id="alumniMap" class="h-96 rounded-lg z-0"></div>
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
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="js/admin_dashboard.js"></script>
</body>
</html>