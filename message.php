<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'alumni') {
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
    <title>Messages | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="css/messages.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gmailgray: '#202124',
                        gmaildark: '#18191a',
                        gmailside: '#f8fafc',
                        gmailblue: '#1a73e8',
                        gmailborder: '#e5e7eb',
                        alumniyellow: '#facc15',
                        alumniyellowlight: '#fef9c3',
                        alumniyellowhover: '#fef08a',
                    }
                }
            }
        }
    </script>
</head>
<body id="app" v-cloak :class="[darkMode ? 'dark bg-gmailgray' : 'bg-gray-100']" class="h-full">

    <!-- Toast Notification Area -->
    <div class="fixed top-4 right-4 z-[100] space-y-3 w-full max-w-xs" aria-live="polite">
            <transition-group 
                enter-active-class="transform transition duration-300 ease-out"
                enter-from-class="translate-x-20 opacity-0"
                enter-to-class="translate-x-0 opacity-100"
                leave-active-class="transform transition duration-200 ease-in"
                leave-from-class="translate-x-0 opacity-100"
                leave-to-class="translate-x-20 opacity-0"
            >
                <div v-for="notification in notifications" :key="notification.id" 
                     :class="{
                         'bg-green-100 border-green-500 text-green-700': notification.type === 'success',
                         'bg-blue-100 border-blue-500 text-blue-700': notification.type === 'info',
                         'bg-red-100 border-red-500 text-red-700': notification.type === 'error',
                         'dark:bg-green-900/80 dark:border-green-700 dark:text-green-200': notification.type === 'success' && darkMode,
                         'dark:bg-blue-900/80 dark:border-blue-700 dark:text-blue-200': notification.type === 'info' && darkMode,
                         'dark:bg-red-900/80 dark:border-red-700 dark:text-red-200': notification.type === 'error' && darkMode
                     }" 
                     class="border-l-4 p-4 rounded-lg shadow-lg relative pr-8 flex items-start animate-slide-in" role="alert" tabindex="0">
                    <div class="flex-shrink-0 mt-1">
                            <i v-if="notification.type === 'success'" class="fas fa-check-circle text-lg"></i>
                            <i v-if="notification.type === 'info'" class="fas fa-info-circle text-lg"></i>
                            <i v-if="notification.type === 'error'" class="fas fa-exclamation-circle text-lg"></i>
                        </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-bold capitalize">{{ notification.type }}</h3>
                            <p class="text-sm mt-1">{{ notification.message }}</p>
                    </div>
                    <button @click="notifications = notifications.filter(n => n.id !== notification.id)" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" aria-label="Close notification">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </transition-group>
    </div>

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
        <!-- Floating Dark Mode Button -->
        <button class="fixed bottom-6 right-6 z-50 p-3 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 transition-all duration-200" @click="toggleDarkMode">
            <i :class="darkMode ? 'fas fa-sun text-yellow-500' : 'fas fa-moon text-blue-500'" class="text-lg"></i>
        </button>
        <!-- Header -->
        <header class="sticky top-0 z-40 w-full bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center px-2 md:px-6 h-16">
            <button class="mr-2 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-800" @click="goBack" title="Back">
                <i class="fas fa-arrow-left text-xl text-gray-700 dark:text-white"></i>
            </button>
            <img src="images/alumni.png" class="w-8 h-8" alt="Logo">
            <span class="ml-3 text-2xl font-bold text-gray-800 dark:text-white">Messages</span>
            <div class="flex-1"></div>
            <!-- Hamburger for mobile sidebar -->
            <button class="md:hidden p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-800 ml-2" @click="sidebarOpen = !sidebarOpen">
                <i class="fas fa-bars text-xl text-gray-700 dark:text-white"></i>
            </button>
            <div class="flex items-center gap-2 ml-2 hidden md:flex">
                <!-- Profile Dropdown -->
                <!-- Profile Dropdown for Desktop -->
                <div class="relative hidden md:block">
                    <button class="p-0 rounded-full focus:outline-none border-2 border-blue-700 dark:border-blue-300" 
                            @click="profileDropdownOpen = !profileDropdownOpen">
                        <img v-if="profilePicData.file_name" :src="'uploads/profile_picture/' + profilePicData.file_name" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                        <img v-else src="images/alumni.png" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                    </button>
                    <div v-show="profileDropdownOpen" class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-700 rounded-md shadow-lg overflow-hidden z-50 py-2 border border-gray-100 dark:border-gray-700 animate-slide-in">
                        <div class="px-4 py-3 flex items-center">
                            <img v-if="profilePicData.file_name" :src="'uploads/profile_picture/' + profilePicData.file_name" alt="Profile" class="w-8 h-8 rounded-full mr-2">
                            <img v-else src="images/alumni.png" alt="Profile" class="w-8 h-8 rounded-full mr-2">
                            <span class="dark:text-white">{{ profile.name || 'Alumni' }}</span>
                        </div>
                        <div class="px-4"><div class="border-t border-gray-200 dark:border-gray-600"></div></div>
                        <a href="my_profile" class="block px-4 py-2 text-gray-700 dark:text-gray-200  hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                            <i class="fas fa-user mr-2"></i> View Profile
                        </a>
                        <a href="message" class="block px-4 py-2 text-gray-700 dark:text-gray-200  hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                            <i class="fas fa-envelope mr-2"></i> Messages
                        </a>
                        <a href="forgot_password" class="block px-4 py-2 text-gray-700 dark:text-gray-200  hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                            <i class="fas fa-key mr-2"></i> Forgot Password
                        </a>
                        <a href="employer_login" class="block px-4 py-2 text-gray-700 dark:text-gray-200  hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 transition-colors duration-200">
                            <i class="fas fa-briefcase mr-2"></i> Employer Site
                        </a>
                        <div class="px-4"><div class="border-t border-gray-200 dark:border-gray-600"></div></div>
                        <a href="#" @click.prevent="showLogoutModal = true" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-red-100 hover:text-red-700 dark:hover:bg-blue-500 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <!-- Responsive Main Layout -->
        <div class="flex w-full h-[calc(100vh-4rem)] relative">
            <!-- Sidebar -->
            <aside class="fixed md:static z-50 md:z-auto top-0 left-0 w-64 h-full bg-gray-50 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col pt-8 px-0 overflow-y-auto scrollbar-thin transition-transform duration-200" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'md:translate-x-0': true}">
                <div class="flex flex-col gap-6 w-full px-4">
                    <button class="flex items-center gap-3 w-full bg-blue-700 text-white font-semibold text-sm py-3 px-6 rounded-lg shadow hover:bg-blue-800 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 transition" @click="showCompose = true">
                        <i class="fas fa-pen-to-square text-lg"></i> Compose
                    </button>
                    <nav class="flex flex-col gap-2">
                        <a href="#" @click.prevent="activeFolder = 'inbox'" :class="['flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-sm transition', activeFolder === 'inbox' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border-l-4 border-blue-700 dark:border-blue-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800']">
                            <span class="flex items-center"><i class="fas fa-envelope mr-2"></i>Inbox</span>
                            <span class="ml-auto bg-blue-700 text-white text-xs font-bold px-2 py-0.5 rounded">{{ inboxCount }}</span>
                        </a>
                        <a href="#" @click.prevent="activeFolder = 'sent'" :class="['flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-sm transition', activeFolder === 'sent' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border-l-4 border-blue-700 dark:border-blue-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800']">
                            <span class="flex items-center"><i class="fas fa-paper-plane mr-2"></i>Sent</span>
                            <span class="ml-auto bg-blue-700 text-white text-xs font-bold px-2 py-0.5 rounded">{{ sentCount }}</span>
                        </a>
                        <a href="#" @click.prevent="activeFolder = 'important'" :class="['flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-sm transition', activeFolder === 'important' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border-l-4 border-blue-700 dark:border-blue-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800']">
                            <i class="fas fa-bell mr-2"></i> Important
                        </a>
                        <a href="#" @click.prevent="activeFolder = 'trash'" :class="['flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-sm transition', activeFolder === 'trash' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border-l-4 border-blue-700 dark:border-blue-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800']">
                            <i class="fas fa-trash mr-2"></i> Trash
                        </a>
                    </nav>
                </div>
                <div class="mt-auto pb-8 px-4 md:hidden">
                    <div class="relative" @mouseleave="profileDropdownOpen = false">
                        <button class="flex items-center gap-3 w-full p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" @click="profileDropdownOpen = !profileDropdownOpen">
                            <img v-if="profilePicData.file_name" :src="'uploads/profile_picture/' + profilePicData.file_name" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                            <img v-else src="images/alumni.png" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-gray-700 dark:text-gray-200">{{ profile.name || 'Alumni' }}</span>
                            <i class="fas fa-chevron-down ml-auto text-gray-500 text-xs transition-transform duration-200" :class="{'rotate-180': profileDropdownOpen}"></i>
                        </button>
                        <div v-show="profileDropdownOpen" class="mt-1 w-full bg-white dark:bg-gray-700 rounded-md shadow-lg overflow-hidden z-50 py-2 border border-gray-100 dark:border-gray-700 animate-slide-in">
                            <a href="my_profile" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 dark:hover:text-white transition-colors duration-200">
                                <i class="fas fa-user mr-2"></i> View Profile
                            </a>
                            <a href="message" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 dark:hover:text-white transition-colors duration-200">
                                <i class="fas fa-envelope mr-2"></i> Messages
                            </a>
                            <a href="forgot_password" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 dark:hover:text-white transition-colors duration-200">
                                <i class="fas fa-key mr-2"></i> Forgot Password
                            </a>
                            <a href="employer_login" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-500 dark:hover:text-white transition-colors duration-200">
                                <i class="fas fa-briefcase mr-2"></i> Employer Site
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                            <a href="#" @click.prevent="showLogoutModal = true" class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-red-100 hover:text-red-700 dark:hover:bg-red-500 dark:hover:text-white transition-colors duration-200">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </aside>
            <!-- Close button for mobile sidebar -->
            <button v-show="sidebarOpen" class="fixed md:hidden top-4 right-4 z-60 p-2 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700" @click="sidebarOpen = false">
                <i class="fas fa-times text-xl text-gray-700 dark:text-white"></i>
            </button>
            <!-- Main Panel -->
            <main class="flex-1 flex flex-col h-full min-h-0 bg-white dark:bg-gray-900 overflow-hidden">
                <!-- Section Title and Action Buttons -->
                <div class="flex flex-col gap-0 w-full">
                    <div class="flex items-center border-b-2 border-blue-700 dark:border-blue-300 bg-white dark:bg-gray-900 px-4 md:px-8 pt-4 md:pt-8 pb-4">
                        <h2 class="text-lg md:text-2xl font-bold flex items-center gap-2 text-blue-700 dark:text-blue-300">
                            <i :class="folderIcon"></i> {{ folderTitle }}
                        </h2>
                        <div class="flex-1"></div>
                        <div class="relative w-48 md:w-64 lg:w-80 ml-2 md:ml-4">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-search text-sm"></i></span>
                            <input v-model="search" type="text" placeholder="Search mail" class="w-full pl-8 md:pl-10 pr-3 md:pr-4 py-1.5 md:py-2 rounded-full bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:focus:ring-blue-500 transition text-xs md:text-sm"/>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 px-6 md:px-8 py-4">
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300 text-sm" title="Star" @click="toggleImportantSelected"><i class="fas fa-star"></i></button>
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300 text-sm" title="Trash" @click="moveToTrashSelected"><i class="fas fa-trash"></i></button>
                        <button v-if="activeFolder === 'trash'" class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300 text-sm" title="Restore" @click="restoreFromTrashSelected"><i class="fas fa-undo"></i></button>
                        <!-- Export buttons -->
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300 text-sm" title="Copy" @click="copyTable"><i class="fas fa-copy"></i></button>
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300 text-sm" title="Export Excel" @click="exportToExcel"><i class="fas fa-file-excel"></i></button>
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300 text-sm" title="Export PDF" @click="exportToPDF"><i class="fas fa-file-pdf"></i></button>
                        <button class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded text-blue-700 dark:text-blue-300 text-sm" title="Print" @click="printTable"><i class="fas fa-print"></i></button>
                    </div>
                </div>
                <!-- Message Table with Horizontal Scroll -->
                <!-- Replace your current table with this -->
                <div class="flex-1 min-h-0 overflow-auto bg-white dark:bg-gray-900">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-blue-100 dark:bg-blue-900">
                                <tr>
                                    <th v-if="filteredMessages.length > 0" scope="col" class="px-3 py-2 text-center w-8">
                                        <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" class="h-4 w-4">
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs sm:text-sm md:text-md font-medium text-blue-700 dark:text-blue-300 tracking-wider">
                                        Sender/Receiver
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs sm:text-sm md:text-md font-medium text-blue-700 dark:text-blue-300 tracking-wider">
                                        Subject
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs sm:text-sm md:text-md font-medium text-blue-700 dark:text-blue-300 tracking-wider">
                                        Message
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs sm:text-sm md:text-md font-medium text-blue-700 dark:text-blue-300 tracking-wider">
                                        Time
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="(msg, idx) in filteredMessages" :key="msg.id" 
                                    @click="openMessage(msg)"
                                    class="hover:bg-blue-50 dark:hover:bg-blue-800 cursor-pointer transition-colors duration-150">
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <input type="checkbox" :value="msg.id" v-model="selectedMessages" @click.stop class="h-4 w-4">
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs sm:text-sm md:text-md text-gray-700 dark:text-gray-300">
                                        {{ msg.sender }}
                                    </td>
                                    <td class="px-3 py-2 text-xs sm:text-sm md:text-md text-gray-700 dark:text-gray-300 max-w-[120px] sm:max-w-[180px] md:max-w-[240px] truncate">
                                        {{ msg.subject }}
                                    </td>
                                    <td class="px-3 py-2 text-xs sm:text-sm md:text-md text-gray-700 dark:text-gray-300 max-w-[180px] sm:max-w-[280px] md:max-w-[360px] truncate">
                                        {{ stripHtml(msg.message) }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs sm:text-sm md:text-md text-gray-700 dark:text-gray-300">
                                        {{ msg.time }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        <!-- Compose Modal (floating) -->
        <div v-if="showCompose" class="compose-float flex items-end justify-end">
            <div class="compose-modal bg-white rounded-2xl shadow-2xl border border-gray-200 p-0 relative overflow-hidden pointer-events-auto">
                <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 bg-blue-50 dark:bg-blue-900">
                    <span class="font-semibold text-gray-800 dark:text-gray-100">New Message</span>
                    <button class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" @click="showCompose = false"><i class="fas fa-times"></i></button>
                </div>
                <form @submit.prevent="sendMessage" class="px-4 py-4 space-y-3">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Receiver <span class="text-red-500">*</span></label>
                        <select v-model="compose.receiver" required @change="onReceiverChange" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">Select</option>
                            <option v-for="user in allUsers" :key="user.email" :value="user.email">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Role</label>
                        <input type="text" v-model="compose.role" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100" readonly disabled>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Subject <span class="text-red-500">*</span></label>
                        <input v-model="compose.subject" required type="text" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Message <span class="text-red-500">*</span></label>
                        <div id="editor" class="bg-white border border-gray-300 rounded"></div>
                    </div>
                    <div class="flex justify-end gap-2 mt-2">
                        <button type="button" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300" @click="showCompose = false">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white font-semibold hover:bg-blue-700">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Message Detail Modal -->
    <transition enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0">
        <div v-if="selectedMessage" class="fixed inset-0 z-[100] overflow-y-auto">
            <!-- Overlay -->
            <transition enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                        leave-to-class="opacity-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
                @click="selectedMessage = null"></div>
            </transition>

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <transition enter-active-class="ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="ease-in duration-200"
                        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
                <!-- Header -->
                <div class="px-6 py-4 bg-gray-700 border-b border-gray-600 flex justify-between items-start">
                    <div>
                    <h3 class="text-xl font-bold text-gray-100">
                        {{ selectedMessage.subject || '(No Subject)' }}
                    </h3>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
                        <span class="text-gray-300">
                        <span class="font-medium">{{ activeFolder === 'inbox' ? 'From:' : 'To:' }}</span>
                        {{ activeFolder === 'inbox' ? selectedMessage.sender_email : selectedMessage.receiver_email }}
                        </span>
                        <span class="text-gray-400">•</span>
                        <span class="text-gray-400">{{ formatDate(selectedMessage.created_at) }}</span>
                    </div>
                    </div>
                    <button @click="selectedMessage = null" 
                            class="text-gray-300 hover:text-white p-1 rounded-full hover:bg-gray-600">
                    <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Message Body -->
                <div class="px-6 py-4 bg-gray-800 max-h-[70vh] overflow-y-auto">
                    <div class="prose prose-invert max-w-none text-gray-200" v-html="selectedMessage.message"></div>
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 bg-gray-700 border-t border-gray-600 flex justify-between">
                    <div class="flex space-x-2">
                    <button @click="toggleImportant(selectedMessage)"
                            class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition"
                            :class="selectedMessage.is_important ? 'bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30' : 'bg-gray-600 text-gray-300 hover:bg-gray-500'">
                        <i class="fas fa-star mr-2"></i>
                        {{ selectedMessage.is_important ? 'Unmark Important' : 'Mark Important' }}
                    </button>
                    <button @click="moveToTrash(selectedMessage)"
                            class="inline-flex items-center px-3 py-2 bg-gray-600 text-gray-300 hover:bg-gray-500 rounded-md text-sm font-medium transition">
                        <i class="fas fa-trash mr-2"></i> Move to Trash
                    </button>
                    </div>
                    <button @click="selectedMessage = null"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition">
                    Close
                    </button>
                </div>
                </div>
            </transition>
            </div>
        </div>
    </transition>
    
    <script>
        (function() {
            try {
                var dark = localStorage.getItem('darkMode');
                if (dark === 'true' || (dark === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch(e){}
        })();
    </script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
    <script src="js/user_messages.js"></script>
</body>
</html> 