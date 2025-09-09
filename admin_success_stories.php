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
    <title>Success Stories | LSPU - EIS</title>
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
                        <a href="admin_success_stories" class="flex items-center px-4 py-2 text-blue-800 dark:text-blue-200 hover:bg-blue-100 dark:hover:bg-blue-500">
                            <i class="fas fa-book-open mr-3"></i>Success Stories
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
        <main :class="[isMobile ? 'ml-0' : (sidebarActive ? 'ml-[280px]' : 'ml-0'), 'transition-all duration-300 min-h-[calc(100vh-70px)] p-6 pt-lg-5 mt-[70px] bg-gray-50 dark:bg-gray-800']">
        <div class="container-fluid max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 mb-6 border border-gray-100 dark:border-gray-600">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Success Stories</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage inspiring stories from alumni</p>
                    </div>
                    <button class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md w-full md:w-auto justify-center" @click="openAddStoryModal">
                        <i class="fas fa-plus-circle"></i> Add New Story
                    </button>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-5 mb-6 border border-gray-100 dark:border-gray-600">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Stories</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Search by title or content..." v-model="filters.search">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full md:w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" v-model="filters.status">
                            <option value="">All Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="w-full md:w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Range</label>
                        <select class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" v-model="filters.dateRange">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="lastWeek">Last Week</option>
                            <option value="month">This Month</option>
                            <option value="lastMonth">Last Month</option>
                            <option value="year">This Year</option>
                            <option value="lastYear">Last Year</option>
                        </select>
                    </div>

                    <!-- Author Filter -->
                    <div class="w-full md:w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Author</label>
                        <select class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-600 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" v-model="filters.author">
                            <option value="">All Authors</option>
                            <option v-for="alumni in alumniList" :key="alumni.user_id" :value="alumni.user_id">{{ alumni.full_name }} ({{ alumni.email }})</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Success Stories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                <div v-for="story in filteredStories" :key="story.story_id" class="bg-white dark:bg-gray-700 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-600 group">
                    <!-- Story Image (using alumni profile picture) -->
                    <div class="h-48 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-600 dark:to-gray-800 relative overflow-hidden">
                        <img v-if="story.profile_picture" :src="'/lspu_eis/uploads/profile_picture/' + story.profile_picture" :alt="story.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div v-else class="w-full h-full flex items-center justify-center text-blue-400 dark:text-blue-300">
                            <i class="fas fa-user-circle text-5xl opacity-50"></i>
                        </div>
                        <span :class="['absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-semibold shadow-sm', 
                            story.status === 'Published' ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200' : 
                            story.status === 'Draft' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-200' : 
                            'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-200']">
                            {{ story.status }}
                        </span>
                    </div>
                    
                    <!-- Story Content -->
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2 line-clamp-2 leading-tight">{{ story.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">{{ story.content.substring(0, 120) + '...' }}</p>
                        
                        <!-- Author Info -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center mr-2 text-white text-sm overflow-hidden">
                                    <img v-if="story.profile_picture" :src="'/lspu_eis/uploads/profile_picture/' + story.profile_picture" :alt="story.author_full_name" class="w-full h-full object-cover">
                                    <span v-else>{{ story.author_full_name ? story.author_full_name.charAt(0).toUpperCase() : 'A' }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ story.author_full_name }}</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ story.author_email }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(story.created_at) }}</span>
                        </div>
                        
                        <!-- Status Action Buttons -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button v-if="story.status !== 'Published'" @click="updateStoryStatus(story, 'Published')" class="flex-1 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-check mr-1 text-xs"></i> Publish
                            </button>
                            <button v-if="story.status !== 'Draft'" @click="updateStoryStatus(story, 'Draft')" class="flex-1 px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-edit mr-1 text-xs"></i> Draft
                            </button>
                            <button v-if="story.status !== 'Archived'" @click="updateStoryStatus(story, 'Archived')" class="flex-1 px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-archive mr-1 text-xs"></i> Archive
                            </button>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-600">
                            <button @click="viewStory(story)" class="text-blue-600 hover:text-blue-800 dark:text-blue-300 dark:hover:text-blue-200 text-sm flex items-center">
                                <i class="fas fa-eye mr-1.5"></i> View
                            </button>
                            <div class="flex space-x-3">
                                <button @click="editStory(story)" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-300 dark:hover:text-yellow-200 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="deleteStory(story)" class="text-red-600 hover:text-red-800 dark:text-red-300 dark:hover:text-red-200 transition-colors" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div v-if="filteredStories.length === 0" class="col-span-full py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center mb-6">
                            <i class="fas fa-book-open text-4xl text-blue-400 dark:text-blue-300"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-600 dark:text-gray-300 mb-2">No success stories found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md">Create your first success story to inspire and motivate others.</p>
                        <button @click="openAddStoryModal" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-plus"></i> Add Your First Story
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Story Modal -->
        <div v-if="showStoryModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50" role="dialog" aria-modal="true" data-modal="story-modal">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
                <button class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors" @click="closeStoryModal" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <div class="mt-3 text-left w-full">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-600">
                        {{ editingStory ? 'Edit Success Story' : 'Add New Success Story' }}
                    </h3>
                    
                    <form @submit.prevent="editingStory ? updateStory() : addStory()">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Author*</label>
                                <select v-model="storyForm.user_id" required class="w-full border border-gray-200 dark:border-gray-600 rounded-lg py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all">
                                    <option value="">Select Alumni Author</option>
                                    <option v-for="alumni in alumniList" :key="alumni.user_id" :value="alumni.user_id">
                                        {{ alumni.full_name }} ({{ alumni.email }})
                                    </option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title*</label>
                                <input type="text" v-model="storyForm.title" required autocomplete="off"
                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content*</label>
                                <textarea v-model="storyForm.content" required rows="6"
                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all"
                                    placeholder="Share the inspiring success story..."></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status*</label>
                                <select v-model="storyForm.status" required
                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all">
                                    <option value="Published">Published</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Archived">Archived</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="button" @click="closeStoryModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancel
                            </button>
                            <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                                {{ editingStory ? 'Update Story' : 'Add Story' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Story Modal -->
        <div v-if="showViewStoryModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50" role="dialog" aria-modal="true" data-modal="view-story-modal">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl mx-2 p-0 relative max-h-[95vh] overflow-y-auto">
                <button class="absolute top-4 right-4 flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-full shadow hover:bg-gray-200 dark:hover:bg-gray-600 transition text-base font-semibold z-20" @click="closeViewStoryModal" aria-label="Close">
                    <i class="fas fa-times"></i> <span>Close</span>
                </button>
                
                <!-- Story Header with Author Profile Picture -->
                <div class="relative h-64 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 dark:from-blue-900 dark:via-blue-800 dark:to-blue-700">
                    <div v-if="viewingStory.profile_picture" class="absolute inset-0">
                        <img :src="'/lspu_eis/uploads/profile_picture/' + viewingStory.profile_picture" :alt="viewingStory.author_full_name" class="w-full h-full object-cover opacity-20">
                    </div>
                    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-6 text-white">
                        <h1 class="text-3xl font-bold drop-shadow-lg mb-2">{{ viewingStory.title }}</h1>
                        <span :class="['inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold shadow', 
                            viewingStory.status === 'Published' ? 'bg-green-100 text-green-700' : 
                            viewingStory.status === 'Draft' ? 'bg-yellow-100 text-yellow-700' : 
                            'bg-gray-200 text-gray-700']">
                            {{ viewingStory.status }}
                        </span>
                    </div>
                </div>
                
                <!-- Story Content -->
                <div class="px-6 py-6">
                    <!-- Author Info -->
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center mr-3 overflow-hidden">
                            <img v-if="viewingStory.profile_picture" :src="'/lspu_eis/uploads/profile_picture/' + viewingStory.profile_picture" :alt="viewingStory.author_full_name" class="w-full h-full object-cover">
                            <i v-else class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100">{{ viewingStory.author_full_name }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ viewingStory.author_email }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(viewingStory.created_at) }}</p>
                        </div>
                    </div>
                    
                    <!-- Story Content -->
                    <div class="prose dark:prose-invert max-w-none mb-6">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ viewingStory.content }}</p>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="editStory(viewingStory)" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </button>
                        <button @click="deleteStory(viewingStory)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal for Success Story -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-[1000] flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">Confirm Delete</h3>
                <p class="mb-6 text-gray-700 dark:text-gray-200">Are you sure you want to delete this success story?</p>
                <div class="flex justify-end gap-2">
                    <button class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-700" @click="showDeleteModal = false">Cancel</button>
                    <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition" @click="confirmDeleteStory">Delete</button>
                </div>
            </div>
        </div>
    </main>
    
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
    <script src="js/admin_success_stories.js"></script>
</body>
</html>