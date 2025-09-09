<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'employer') {
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
    <title>Employer Messages | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="css/employer_message.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
</head>
<body :class="[darkMode ? 'dark' : '', 'font-sans bg-gray-50 dark:bg-gray-800 min-h-screen overflow-hidden']" id="app" v-cloak>
    <!-- Toast Notification Area -->
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
            <a href="employer_dashboard" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200"  @click="handleNavClick">
                <i class="fas fa-tachometer-alt w-5 mr-3 text-center text-blue-500 dark:text-blue-400"></i>
                <span class="font-medium">Dashboard</span>
            </a>

           <a href="employer_matchboard" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-handshake w-5 mr-3 text-center text-amber-500 dark:text-amber-400"></i>
                <span class="font-medium">Matchboard</span>
            </a>
            
            <!-- Jobs -->
            <a href="employer_jobposting" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-briefcase w-5 mr-3 text-center text-emerald-500 dark:text-emerald-400"></i>
                <span class="font-medium">Jobs</span>
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

            <!-- Job Resources -->
            <a href="employer_onboarding" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-user-check w-5 mr-3 text-center text-blue-500 dark:text-blue-400"></i>
                <span class="font-medium">Onboarding</span>
            </a>
            
            <!-- Messages -->
            <a href="employer_messages" class="flex items-center px-6 py-3 mx-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 dark:hover:bg-blue-500/30 transition-colors duration-200 border-l-4 border-blue-500 dark:border-blue-400" @click="handleNavClick">
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
                                <i class="fas fa-user mr-3"></i> View Profile
                            </a>
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="employer_terms">
                                <i class="fas fa-file-contract mr-3"></i> Terms
                            </a>
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="employer_forgot_password">
                                <i class="fas fa-key mr-3"></i> Forgot Password
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-500 my-1"></div>
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
    <main :class="[isMobile ? 'ml-0' : (sidebarActive ? 'ml-[280px]' : 'ml-0'), 'transition-all duration-300 min-h-[calc(100vh-70px)] p-6 pt-lg-5 bg-gray-50 dark:bg-gray-800']">
    <div class="container-fluid max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Message Sidebar (Folders) -->
                    <div class="w-full md:w-1/4">
                        <div class="bg-white dark:bg-slate-700 rounded-lg shadow-lg p-4 text-slate-800 dark:text-slate-200 border border-gray-200 dark:border-gray-600">
                            <div class="border-b-2 border-blue-600 dark:border-blue-400 pb-2 mb-4">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Mailbox Folders</h2>
                            </div>
        
                        <!-- Compose Button -->
                        <button 
                            class="w-full flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg mb-4 transition-colors duration-200 shadow-sm"
                            @click="showCompose = true">
                            <i class="fas fa-pen-to-square"></i> 
                            <span>Compose</span>
                        </button>
        
                        <!-- Folder Navigation -->
                        <nav class="flex flex-col gap-1">
                            <!-- Inbox -->
                            <a href="#" 
                            @click.prevent="activeFolder = 'inbox'; showCompose = false" 
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors duration-200',
                                activeFolder === 'inbox' 
                                    ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 border-l-4 border-blue-600 dark:border-blue-400' 
                                    : 'hover:bg-slate-200 dark:hover:bg-slate-600/50 text-slate-700 dark:text-slate-300'
                            ]">
                                <span class="flex items-center">
                                    <i class="fas fa-inbox mr-3 text-blue-500 dark:text-blue-400"></i>
                                    <span class="font-medium">Inbox</span>
                                </span>
                                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    {{ inboxCount }}
                                </span>
                            </a>
                            
                            <!-- Sent -->
                            <a href="#" 
                            @click.prevent="activeFolder = 'sent'; showCompose = false" 
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors duration-200',
                                activeFolder === 'sent' 
                                    ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 border-l-4 border-blue-600 dark:border-blue-400' 
                                    : 'hover:bg-slate-200 dark:hover:bg-slate-600/50 text-slate-700 dark:text-slate-300'
                            ]">
                                <span class="flex items-center">
                                    <i class="fas fa-paper-plane mr-3 text-blue-500 dark:text-blue-400"></i>
                                    <span class="font-medium">Sent</span>
                                </span>
                                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    {{ sentCount }}
                                </span>
                            </a>
                            
                            <!-- Important -->
                            <a href="#" 
                            @click.prevent="activeFolder = 'important'; showCompose = false" 
                            :class="[
                                'flex items-center px-3 py-2.5 rounded-lg transition-colors duration-200',
                                activeFolder === 'important' 
                                    ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 border-l-4 border-blue-600 dark:border-blue-400' 
                                    : 'hover:bg-slate-200 dark:hover:bg-slate-600/50 text-slate-700 dark:text-slate-300'
                            ]">
                                <i class="fas fa-star mr-3 text-yellow-500 dark:text-yellow-400"></i>
                                <span class="font-medium">Important</span>
                            </a>
                            
                            <!-- Trash -->
                            <a href="#" 
                            @click.prevent="activeFolder = 'trash'; showCompose = false" 
                            :class="[
                                'flex items-center px-3 py-2.5 rounded-lg transition-colors duration-200',
                                activeFolder === 'trash' 
                                    ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 border-l-4 border-blue-600 dark:border-blue-400' 
                                    : 'hover:bg-slate-200 dark:hover:bg-slate-600/50 text-slate-700 dark:text-slate-300'
                            ]">
                                <i class="fas fa-trash mr-3 text-red-500 dark:text-red-400"></i>
                                <span class="font-medium">Trash</span>
                            </a>
                        </nav>
                    </div>
            </div>
            <!-- Main Panel -->
            <div class="w-full md:w-3/4 bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <!-- Compose Message Panel (not modal) -->
                <!-- Compose Message Panel (not modal) -->
                <div v-if="showCompose" class="bg-white dark:bg-gray-700 rounded-lg shadow-lg p-6 mb-6 border border-gray-200 dark:border-gray-600">
                    <div class="border-b-2 border-blue-600 dark:border-blue-400 pb-3 mb-6 flex items-center justify-between">
                        <h2 class="text-xl font-bold flex items-center gap-2 text-blue-700 dark:text-blue-300">
                            <i class="fas fa-pen text-blue-500 dark:text-blue-400"></i> 
                            Compose New Message
                        </h2>
                        <button @click="showCompose = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form @submit.prevent="sendMessage" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role <span class="text-red-500">*</span></label>
                            <input v-model="compose.role"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm" readonly disabled>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Receiver <span class="text-red-500">*</span></label>
                            <select v-model="compose.receiver" required @change="onReceiverChange" 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                                <option value="">Select Receiver</option>
                                <option v-for="user in allUsers" :key="user.email" :value="user.email">
                                    {{ user.name }} ({{ user.email }})
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject <span class="text-red-500">*</span></label>
                            <input v-model="compose.subject" required type="text" 
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <div id="editor" class="min-h-[220px] border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800"></div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" 
                                    class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 font-medium shadow-sm">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-sm transition-colors duration-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Folder Panels -->
                <div v-else>
                    <div class="border-b-2 border-blue-700 dark:border-blue-300 pb-2 mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold flex items-center gap-2 text-blue-700 dark:text-blue-300">
                            <i :class="folderIcon"></i> {{ folderTitle }}
                        </h2>                        <!-- Search bar styled like admin_job.php -->
                        <div class="relative w-64 sm:w-72 md:w-80 lg:w-96 ml-8 sm:ml-20 mr-4 sm:mr-0">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input 
                                type="text" 
                                v-model="searchQuery" 
                                placeholder="Search alumni..." 
                                class="w-full form-input pl-10 px-3 py-1.5 sm:py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm sm:text-base text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                                @input="handleSearchInput"
                            >
                            <button 
                                v-if="searchQuery" 
                                @click="clearSearch"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Bulk Action Toolbar -->
                    <div class="flex items-center gap-2 px-2 py-3 mb-2">
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300" title="Star" @click="toggleImportantSelected"><i class="fas fa-star"></i></button>
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300" title="Trash" @click="moveToTrashSelected"><i class="fas fa-trash"></i></button>
                        <button v-if="activeFolder === 'trash'" class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300" title="Restore" @click="restoreFromTrashSelected"><i class="fas fa-undo"></i></button>
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300" title="Copy" @click="copyTable"><i class="fas fa-copy"></i></button>
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300" title="Export Excel" @click="exportToExcel"><i class="fas fa-file-excel"></i></button>
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300" title="Export PDF" @click="exportToPDF"><i class="fas fa-file-pdf"></i></button>
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300" title="Print" @click="printTable"><i class="fas fa-print"></i></button>
                    </div>
                    <div class="overflow-x-auto">
                        <!-- Inbox Table -->
                                                <!-- Inbox Table -->
                        <table v-if="activeFolder === 'inbox'" class="min-w-full text-base select-none">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <th v-if="filteredPaginatedMessages.length > 0" class="pl-2"><input type="checkbox" v-model="selectAll" @change="toggleSelectAll"></th>
                                    <th class="pl-8 pr-4 py-3 text-left">Sender</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Subject</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Message</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="(msg, idx) in filteredPaginatedMessages" 
                                    :key="msg.id" 
                                    class="hover:bg-gray-50 dark:hover:bg-gray-600 transition border-b border-gray-100 dark:border-gray-800"
                                >
                                    <td class="pl-2">
                                        <input 
                                            type="checkbox" 
                                            :value="msg.id" 
                                            v-model="selectedMessages"
                                            class="cursor-pointer"
                                        >
                                    </td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white">{{ msg.sender }}</td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400" @click="selectMessage(msg)">
                                        {{ msg.subject }}
                                    </td>
                                    <td class="pl-8 pr-4 py-3 truncate max-w-xs text-black dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400" @click="selectMessage(msg)">
                                        {{ stripHtml(msg.message) }}
                                    </td>
                                    <td class="pl-8 pr-4 py-3 whitespace-nowrap text-black dark:text-white">{{ msg.time }}</td>
                                </tr>
                                <tr v-if="filteredPaginatedMessages.length === 0">
                                    <td colspan="6" class="text-center py-16">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                                            <span class="text-gray-400 text-lg">No messages found</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Sent Table -->
                        <!-- Sent Table -->
                        <table v-if="activeFolder === 'sent'" class="min-w-full text-base select-none">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <th v-if="filteredPaginatedMessages.length > 0" class="pl-2"><input type="checkbox" v-model="selectAll" @change="toggleSelectAll"></th>
                                    <th class="pl-8 pr-4 py-3 text-left">Receiver</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Subject</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Message</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Time</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(msg, idx) in filteredPaginatedMessages" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-600 transition border-b border-gray-100 dark:border-gray-800">
                                    <td class="pl-2">
                                        <input 
                                            type="checkbox" 
                                            :value="msg.id" 
                                            v-model="selectedMessages"
                                            class="cursor-pointer"
                                        >
                                    </td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white">{{ msg.sender }}</td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400" @click="selectMessage(msg)">
                                        {{ msg.subject }}
                                    </td>
                                    <td class="pl-8 pr-4 py-3 truncate max-w-xs text-black dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400" @click="selectMessage(msg)">
                                        {{ stripHtml(msg.message) }}
                                    </td>
                                    <td class="pl-8 pr-4 py-3 whitespace-nowrap text-black dark:text-white">{{ msg.time }}</td>
                                    <td class="pl-8 pr-4 py-3">
                                        <button @click="selectMessage(msg)" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="View Message">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredPaginatedMessages.length === 0">
                                    <td colspan="6" class="text-center py-16">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                                            <span class="text-gray-400 text-lg">No messages found</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Important Table -->
                        <!-- Important Table -->
                        <table v-if="activeFolder === 'important'" class="min-w-full text-base select-none">
                            <thead>
                               <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <th v-if="filteredPaginatedMessages.length > 0" class="pl-2"><input type="checkbox" v-model="selectAll" @change="toggleSelectAll"></th>
                                    <th class="pl-8 pr-4 py-3 text-left">#</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Type</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Sender / Receiver</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Subject</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Message</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Time</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(msg, idx) in filteredImportantMessages" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-600 transition border-b border-gray-100 dark:border-gray-800">
                                    <td class="pl-2">
                                        <input 
                                            type="checkbox" 
                                            :value="msg.id" 
                                            v-model="selectedMessages"
                                            class="cursor-pointer"
                                        >
                                    </td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white">{{ idx + 1 }}</td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white"><i class="fas fa-share"></i></td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white">{{ msg.sender }}</td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400" @click="selectMessage(msg)">
                                        {{ msg.subject }}
                                    </td>
                                    <td class="pl-8 pr-4 py-3 truncate max-w-xs text-black dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400" @click="selectMessage(msg)">
                                        {{ stripHtml(msg.message) }}
                                    </td>
                                    <td class="pl-8 pr-4 py-3 whitespace-nowrap text-black dark:text-white">{{ msg.time }}</td>
                                    <td class="pl-8 pr-4 py-3">
                                        <button @click="selectMessage(msg)" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="View Message">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredImportantMessages.length === 0">
                                    <td colspan="8" class="text-center py-16">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                                            <span class="text-gray-400 text-lg">No messages found</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Trash Table -->
                        <!-- Trash Table -->
                        <table v-if="activeFolder === 'trash'" class="min-w-full text-base select-none">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                    <th v-if="filteredPaginatedMessages.length > 0" class="pl-2"><input type="checkbox" v-model="selectAll" @change="toggleSelectAll"></th>
                                    <th class="pl-8 pr-4 py-3 text-left">Receiver</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Subject</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Message</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Time</th>
                                    <th class="pl-8 pr-4 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(msg, idx) in filteredPaginatedMessages" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-600 transition border-b border-gray-100 dark:border-gray-800">
                                    <td class="pl-2">
                                        <input 
                                            type="checkbox" 
                                            :value="msg.id" 
                                            v-model="selectedMessages"
                                            class="cursor-pointer"
                                        >
                                    </td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white">{{ msg.sender }}</td>
                                    <td class="pl-8 pr-4 py-3 text-black dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400" @click="selectMessage(msg)">
                                        {{ msg.subject }}
                                    </td>
                                    <td class="pl-8 pr-4 py-3 truncate max-w-xs text-black dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400" @click="selectMessage(msg)">
                                        {{ stripHtml(msg.message) }}
                                    </td>
                                    <td class="pl-8 pr-4 py-3 whitespace-nowrap text-black dark:text-white">{{ msg.time }}</td>
                                    <td class="pl-8 pr-4 py-3">
                                        <button @click="selectMessage(msg)" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="View Message">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredPaginatedMessages.length === 0">
                                    <td colspan="6" class="text-center py-16">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                                            <span class="text-gray-400 text-lg">No messages found</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="flex  gap-1 justify-center md:justify-end mt-4">
                        <button class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" :disabled="currentPage === 1" @click="prevPage">
                            <i class="fas fa-chevron-left text-black dark:text-white"></i>
                        </button>
                        <span class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-blue-500 text-gray-700 dark:text-gray-200 hover:bg-blue-100 dark:hover:bg-blue-900">{{ currentPage }}</span>
                        <button class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" :disabled="currentPage === totalPages" @click="nextPage">
                            <i class="fas fa-chevron-right text-black dark:text-white"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Gmail-style Message View Modal -->
            <div v-if="viewingMessage" class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-[1000] p-4">
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden border border-gray-200 dark:border-gray-700">
                    
                    <!-- Header with Gmail-like actions -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <div class="flex items-center space-x-3">
                        <button @click="viewingMessage = null" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-arrow-left"></i>
                        </button>
                        <button @click="moveToTrash(viewingMessage)" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" title="Delete">
                        <i class="fas fa-trash"></i>
                        </button>
                        <button 
                        @click="toggleImportant(viewingMessage)"
                        class="p-2 rounded-full dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                        :class="{'text-yellow-500': viewingMessage.folder === 'important'}"
                        :title="viewingMessage.folder === 'important' ? 'Unmark Important' : 'Mark Important'"
                        >
                        <i class="fas fa-star"></i>
                        </button>
                    </div>
                    <button @click="viewingMessage = null" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                    </div>
                    
                    <!-- Message Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-3">{{ viewingMessage.subject }}</h2>
                    
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 mr-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">
                            {{ activeFolder === 'inbox' ? viewingMessage.sender_email : viewingMessage.receiver_email }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                            to {{ activeFolder === 'inbox' ? 'me' : viewingMessage.receiver_email }}
                            </div>
                        </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ formatDateTime(viewingMessage.created_at) }}
                        </div>
                    </div>
                    </div>
                    
                    <!-- Message Body -->
                    <div class="flex-1 p-6 overflow-y-auto">
                    <div class="prose max-w-none dark:prose-invert prose-p:text-gray-800 dark:prose-p:text-gray-200 prose-li:text-gray-800 dark:prose-li:text-gray-200 text-black dark:text-white">
                        <div v-html="viewingMessage.message"></div>
                    </div>
                    
                    <!-- Attachments (if any) -->
                    <div v-if="viewingMessage.attachments && viewingMessage.attachments.length" class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments</h4>
                        <div class="flex flex-wrap gap-3">
                        <div v-for="attachment in viewingMessage.attachments" :key="attachment.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <div class="flex items-center">
                            <i class="fas fa-paperclip text-gray-500 dark:text-gray-400 mr-2"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ attachment.name }}</span>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                    
                    <!-- Footer with Reply Options -->
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex justify-between items-center">
                        <button 
                        @click="startReply(viewingMessage)"
                        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors flex items-center"
                        >
                        <i class="fas fa-reply mr-2"></i>
                        Reply
                        </button>
                        
                        <div class="flex items-center space-x-2">
                        <button 
                            @click="startReply(viewingMessage, true)"
                            class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            <i class="fas fa-reply-all mr-1"></i> Reply All
                        </button>
                        <button 
                            @click="forwardMessage(viewingMessage)"
                            class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            <i class="fas fa-share mr-1"></i> Forward
                        </button>
                        </div>
                    </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="js/employer_messages.js"></script>
</body>
</html>
