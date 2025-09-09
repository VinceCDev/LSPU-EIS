<?php
/**
 * Admin Interface for Reminder Settings
 * Manage reminder system settings stored in database.
 */
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

require_once 'functions/reminder_config.php';

// Handle form submission will be done via AJAX

// Load current settings
$sql = 'SELECT setting_key, setting_value FROM reminder_settings';
$result = $db->query($sql);
$current_settings = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $current_settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Get recent statistics
$sql = 'SELECT * FROM reminder_statistics ORDER BY date DESC LIMIT 7';
$stats_result = $db->query($sql);
$recent_stats = [];
if ($stats_result) {
    while ($row = $stats_result->fetch_assoc()) {
        $recent_stats[] = $row;
    }
}

// Get recent logs
$sql = 'SELECT * FROM reminder_logs ORDER BY sent_at DESC LIMIT 20';
$logs_result = $db->query($sql);
$recent_logs = [];
if ($logs_result) {
    while ($row = $logs_result->fetch_assoc()) {
        $recent_logs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admininistrator Reminder Settings | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link rel="stylesheet" href="css/admin_reminder.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
</head>

<body :class="[darkMode ? 'dark' : '', 'font-sans bg-gray-50 dark:bg-gray-800 min-h-screen']" id="app" v-cloak>
    <!-- Logout Confirmation Modal - Top positioning -->
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
                <a href="admin_applicant" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200"  @click="handleNavClick">
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
                <a href="admin_user" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" >
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
    <style>
    .slide-enter-active {
      transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .slide-leave-active {
      transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
    }
    .slide-enter-from, .slide-leave-to {
      transform: translateY(-32px);
      opacity: 0;
    }
    .slide-enter-to, .slide-leave-from {
      transform: translateY(0);
      opacity: 1;
    }
    </style>

    <!-- Header (always fixed, not pushed by sidebar) -->
    <header class="fixed top-0 left-0 right-0 h-[70px] bg-white dark:bg-gray-700 shadow-md z-40 flex items-center px-4">
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
                    <div v-if="profileDropdownOpen" class="origin-top-right absolute right-0 mt-2 w-48 bg-white dark:bg-gray-600 rounded-md shadow-lg py-1 z-[200] border border-gray-200 dark:border-gray-500">
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
                        <a class="block px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="admin_profile">
                            <i class="fas fa-user mr-2"></i> View Profile
                        </a>
                        <a class="flex items-center px-4 py-2 text-blue-800 dark:text-blue-200 hover:bg-blue-100 dark:hover:bg-blue-500" href="admin_reminder_settings">
                            <i class="fas fa-bell mr-2"></i> Reminder Settings
                        </a>
                        <a href="admin_success_stories"  class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500">
                            <i class="fas fa-book-open mr-3"></i>Success Stories
                        </a>
                        <a class="block px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="forgot_password">
                            <i class="fas fa-cog mr-2"></i> Forgot Password
                        </a>
                        <div class="border-t border-gray-200 dark:border-gray-500 my-1"></div>
                        <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-red-100 dark:hover:bg-red-500  hover:text-red-400 dark:hover:text-red-200" href="#" @click.prevent="confirmLogout">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
                </div>
            </div>
        </header>

    <!-- Main Content -->
    <main :class="[isMobile ? 'ml-0' : (sidebarActive ? 'ml-[280px]' : 'ml-0'), 'transition-all duration-300 min-h-[calc(100vh-70px)] p-6 pt-lg-5 mt-[70px] bg-gray-50 dark:bg-gray-800']">
        <div class="container mx-auto px-6 py-8">

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">Reminder System Settings</h1>
                <p class="text-gray-600 dark:text-gray-400">Configure automated reminder notifications for alumni</p>
                </div>

                <!-- Settings Form -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-6">General Settings</h2>
                    <form @submit.prevent="saveSettings" class="space-y-6">
                    <!-- Time Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Business Hours Start</label>
                            <select v-model="settings.business_hours_start" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200">
                                <option v-for="i in 24" :key="i-1" :value="(i-1).toString()">
                                    {{ String(i-1).padStart(2, '0') }}:00
                                </option>
                            </select>
                            </div>
                            <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Business Hours End</label>
                            <select v-model="settings.business_hours_end" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200">
                                <option v-for="i in 24" :key="i-1" :value="(i-1).toString()">
                                    {{ String(i-1).padStart(2, '0') }}:00
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                            <select v-model="settings.timezone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200">
                                <option value="Asia/Manila">Asia/Manila (GMT+8)</option>
                                <option value="UTC">UTC (GMT+0)</option>
                            </select>
                            </div>
                        </div>

                    <!-- Frequency Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Send Every (minutes)</label>
                            <select v-model="settings.frequency_minutes" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200">
                                <option value="1">1 minute</option>
                                <option value="5">5 minutes</option>
                                <option value="15">15 minutes</option>
                                <option value="30">30 minutes</option>
                                <option value="60">1 hour</option>
                            </select>
                            </div>
                            <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Reminders Per Day</label>
                            <select v-model="settings.max_reminders_per_day" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200">
                                <option v-for="i in 10" :key="i" :value="i.toString()">
                                    {{ i }} reminder{{ i > 1 ? 's' : '' }}
                                </option>
                            </select>
                            </div>
                        </div>

                    <!-- Notification Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Notification Methods</h3>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="settings.email_enabled"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 checked:bg-blue-600 checked:border-blue-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Email Notifications</span>
                                </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="settings.sms_enabled"  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 checked:bg-blue-600 checked:border-blue-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">SMS Notifications</span>
                                </label>
                            </div>
                        </div>

                    <!-- Message Templates -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Message Templates</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Subject</label>
                            <input type="text" v-model="settings.email_subject" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Message</label>
                            <textarea v-model="settings.email_message" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMS Message</label>
                            <textarea v-model="settings.sms_message" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-gray-200"></textarea>
                        </div>
                        </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition-colors">
                            Save Settings
                        </button>
                    </div>
                    </form>
                </div>

                <!-- Statistics and Logs -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Statistics -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Recent Statistics</h2>
                        <?php if (!empty($recent_stats)) { ?>
                        <div class="space-y-3">
                                        <?php foreach ($recent_stats as $stat) { ?>
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-600 rounded">
                                    <div>
                                        <div class="font-medium text-gray-800 dark:text-gray-200"><?php echo date('M j, Y', strtotime($stat['date'])); ?></div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            <?php echo $stat['emails_sent']; ?> emails, <?php echo $stat['sms_sent']; ?> SMS sent
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-green-600"><?php echo $stat['total_sent']; ?> total</div>
                                        <div class="text-xs text-red-600"><?php echo $stat['total_failed']; ?> failed</div>
                                    </div>
                                </div>
                                        <?php } ?>
                            </div>
                        <?php } else { ?>
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No statistics available</p>
                        <?php } ?>
                    </div>

                    <!-- Recent Logs -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Recent Activity</h2>
                        <?php if (!empty($recent_logs)) { ?>
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                                <?php foreach ($recent_logs as $log) { ?>
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-600 rounded">
                                    <div class="flex-shrink-0">
                                        <?php if ($log['type'] === 'email') { ?>
                                            <i class="fas fa-envelope text-blue-500"></i>
                                        <?php } else { ?>
                                            <i class="fas fa-sms text-green-500"></i>
                                        <?php } ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            <?php echo ucfirst($log['type']); ?> to <?php echo substr($log['recipient'], 0, 20).(strlen($log['recipient']) > 20 ? '...' : ''); ?>
                                        </div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                            <?php echo date('M j, Y g:i A', strtotime($log['sent_at'])); ?>
                                        </div>
                                        <div class="text-xs <?php echo $log['status'] === 'sent' ? 'text-green-600' : 'text-red-600'; ?>">
                                            <?php echo ucfirst($log['status']); ?>
                                        </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent activity</p>
                        <?php } ?>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.0.77/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsPDF/2.5.1/jspdf.umd.min.js"></script>
    <script src="js/admin_reminder_settings.js"></script>
</body>
</html> 