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
    <title>Employer Profile | LSPU - EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/employer_profile.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
    
</head>
<body :class="[darkMode ? 'dark' : '', 'font-sans bg-gray-50 dark:bg-gray-800 min-h-screen']" id="app" v-cloak>
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
            <a href="employer_dashboard" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
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
            <a href="employer_messages" class="flex items-center px-6 py-3 mx-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors duration-200" @click="handleNavClick">
                <i class="fas fa-envelope w-5 mr-3 text-center text-pink-500 dark:text-pink-400"></i>
                <span class="font-medium">Messages</span>
            </a>
        </div>
    </div>
    <!-- Sidebar overlay for mobile only -->
    <div v-if="sidebarActive && isMobile" class="fixed inset-0 bg-black bg-opacity-40 z-40 md:hidden" @click="toggleSidebar"></div>
    <!-- Notification Toast -->
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

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 h-[70px] bg-white dark:bg-gray-700 shadow-md z-40 flex items-center px-4">
        <div class="flex items-center justify-between w-full">
            <button class="md:hidden text-gray-600 dark:text-gray-300 p-1" @click="toggleSidebar">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="flex items-center space-x-4 ml-auto">
                <div class="relative">
                    <div class="cursor-pointer flex items-center" @click="profileDropdownOpen = !profileDropdownOpen">
                        <img :src="profile.company_logo || 'images/logo.png'" alt="Profile" class="w-10 h-10 rounded-full border-2 border-gray-200 dark:border-gray-500">
                        <span class="ml-2 font-medium text-gray-700 dark:text-gray-200">{{ profile.company_name }}</span>
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
                            <a class="flex items-center px-4 py-2 text-blue-800 dark:text-blue-200 hover:bg-blue-100 dark:hover:bg-blue-500" href="employer_profile">
                                <i class="fas fa-user mr-3"></i> 
                                <span class="text-sm">View Profile</span>
                            </a>
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="employer_terms">
                                <i class="fas fa-file-contract mr-3"></i> Terms
                            </a>
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500" href="employer_forgot_password">
                                <i class="fas fa-key mr-3"></i> Forgot Password
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-500 my-1"></div>
                            <a class="flex items-center px-4 py-2 text-gray-800 dark:text-gray-200 hover:bg-red-100 dark:hover:bg-red-500  hover:text-red-400 dark:hover:text-red-200" href="#" @click.prevent="confirmLogout">
                                <i class="fas fa-sign-out-alt mr-3"></i> 
                                <span class="text-sm">Logout</span>
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
            <!-- Hero Section -->
            <section class="relative bg-white dark:bg-gray-700 rounded-xl shadow-sm p-4 sm:p-6 mb-6 overflow-hidden">
                <div class="absolute inset-0 bg-[url('images/lspu_campus.jpg')] bg-cover bg-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-800/60 via-slate-700/50 to-blue-700/60"></div>
                </div>
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="relative mb-4 sm:mb-6">
                        <img :src="profile.company_logo || 'images/logo.png'" alt="Company Logo" class="w-24 h-24 sm:w-40 sm:h-40 rounded-full object-cover border-4 border-white dark:border-gray-300 shadow-lg">
                        <div class="absolute -bottom-1 -right-1 sm:-bottom-2 sm:-right-2 bg-blue-600 text-white rounded-full p-1.5 sm:p-2 cursor-pointer hover:bg-blue-700 transition-colors" @click="openPhotoUpload">
                            <i class="fas fa-camera text-xs sm:text-sm"></i>
                        </div>
                    </div>
                    <div class="mb-4 sm:mb-6 w-full">
                        <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2 drop-shadow-lg">{{ profile.company_name }}</h1>
                        <p class="text-lg sm:text-xl text-white mb-4 drop-shadow-lg">{{ profile.nature_of_business || 'Nature of Business' }}</p>
                        <div class="flex flex-row flex-wrap gap-2 sm:gap-4 justify-center mb-4 sm:mb-6 w-full">
                            <div class="flex items-center justify-center bg-white bg-opacity-20 backdrop-blur-sm rounded-lg px-3 py-2 sm:px-4 sm:py-2 min-w-[120px]">
                                <i class="fas fa-envelope text-white mr-2 text-sm sm:text-base"></i>
                                <span class="text-white text-sm sm:text-base">{{ profile.contact_email || 'No email specified' }}</span>
                            </div>
                            <div class="flex items-center justify-center bg-white bg-opacity-20 backdrop-blur-sm rounded-lg px-3 py-2 sm:px-4 sm:py-2 min-w-[120px]">
                                <i class="fas fa-phone text-white mr-2 text-sm sm:text-base"></i>
                                <span class="text-white text-sm sm:text-base">{{ profile.contact_number || 'No phone specified' }}</span>
                            </div>
                            <div class="flex items-center justify-center bg-white bg-opacity-20 backdrop-blur-sm rounded-lg px-3 py-2 sm:px-4 sm:py-2 min-w-[120px]">
                                <i class="fas fa-map-marker-alt text-white mr-2 text-sm sm:text-base"></i>
                                <span class="text-white text-sm sm:text-base">{{ profile.company_location || 'No location specified' }}</span>
                            </div>
                        </div>
                    </div>
                    <button @click="editProfile" class="bg-white text-blue-600 px-6 py-2 sm:px-8 sm:py-3 rounded-lg hover:bg-gray-100 transition-colors shadow-md font-semibold text-sm sm:text-base">
                        <i class="fas fa-edit mr-2"></i>Edit Profile
                    </button>
                </div>
            </section>
            <!-- Company Information Section -->
            <section class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 uppercase">Company Information</h2>
                    <button @click="editProfile" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Company Name</div>
                        <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.company_name || 'Not specified' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Company Address</div>
                        <div class="text-gray-800 dark:text-gray-200">{{ profile.company_location || 'Not specified' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Contact Email</div>
                        <div class="text-gray-800 dark:text-gray-200">{{ profile.contact_email || 'Not specified' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Contact Number</div>
                        <div class="text-gray-800 dark:text-gray-200">{{ profile.contact_number || 'Not specified' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Industry Type</div>
                        <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.industry_type || 'Not specified' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Nature of Business</div>
                        <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.nature_of_business || 'Not specified' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">TIN</div>
                        <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.tin || 'Not specified' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Date Established</div>
                        <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.date_established || 'Not specified' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Type of Company</div>
                        <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.company_type || 'Not specified' }}</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Accreditation Status</div>
                        <div class="text-gray-800 dark:text-gray-200 uppercase">{{ profile.accreditation_status || 'Not specified' }}</div>
                    </div>
                </div>
            </section>
            <!-- Improved Company Document Section with consistent width and button layout -->
            <section class="bg-white dark:bg-gray-700 rounded-xl shadow-sm p-6 mb-6 max-w-7xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 uppercase mb-4">Company Document</h2>
                <div class="flex flex-col lg:flex-row gap-6 items-center lg:items-start">
                    <!-- Preview: full width on small, left on large -->
                    <div class="flex-1 w-full flex flex-col items-center lg:items-start">
                        <div v-if="profile.document_file" class="w-full flex flex-col items-center lg:items-start">
                            <div v-if="isPdf(profile.document_file)" class="mb-2 w-full flex justify-center lg:justify-start">
                                <iframe :src="profile.document_file" style="width:100%;height:350px;" class="border rounded"></iframe>
                            </div>
                            <div v-else-if="isImage(profile.document_file)" class="mb-2 flex justify-center lg:justify-start">
                                <img :src="profile.document_file" alt="Document Preview" class="max-h-48 rounded border mx-auto lg:ml-0">
                            </div>
                            <div class="mb-2 text-gray-700 dark:text-gray-200 flex items-center justify-center lg:justify-start">
                                <i class="fas fa-file-alt mr-2"></i>
                                <span>{{ getFileName(profile.document_file) }}</span>
                            </div>
                        </div>
                        <div v-else class="text-gray-500 dark:text-gray-400 flex items-center gap-2 justify-center lg:justify-start">
                            <i class="fas fa-file-alt"></i> No document uploaded.
                        </div>
                    </div>
                    <!-- Buttons: below preview and centered on small, right and centered vertically on large -->
                    <div class="flex flex-col gap-2 w-full sm:w-64 lg:w-48 lg:items-center lg:justify-center lg:mt-0 mt-4">
                        <button v-if="profile.document_file" @click="openDocumentInNewTab(profile.document_file)" class="bg-blue-600 text-white w-full h-12 rounded flex items-center justify-center gap-2 mb-1 hover:bg-blue-700">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button @click="showDocumentModal = true" class="bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 w-full h-12 rounded flex items-center justify-center gap-2 hover:bg-gray-300 dark:hover:bg-gray-500">
                            <i class="fas fa-upload"></i> Change Document
                        </button>
                        <button v-if="profile.document_file" @click="confirmDeleteDocument" class="bg-red-600 text-white w-full h-12 rounded flex items-center justify-center gap-2 hover:bg-red-700">
                            <i class="fas fa-trash"></i> Delete Document
                        </button>
                    </div>
                </div>
            </section>
            <!-- Document Modal -->
            <transition enter-active-class="modal-enter-active" enter-from-class="modal-enter-from" enter-to-class="modal-enter-to" leave-active-class="modal-leave-active" leave-from-class="modal-leave-from" leave-to-class="modal-leave-to">
                <div v-if="showDocumentModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="showDocumentModal = false">
                            <i class="fas fa-times"></i>
                        </button>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Change Company Document</h3>
                        <input type="file" @change="handleDocumentUpload" accept="application/pdf,image/*" class="mb-4">
                        <div v-if="newDocumentFile">
                            <div v-if="isPdf(newDocumentName)" class="mb-4">
                                <iframe :src="newDocumentPreview" style="width:100%;height:300px;" class="border rounded"></iframe>
                            </div>
                            <div v-else-if="isImage(newDocumentName)" class="mb-4 flex justify-center">
                                <img :src="newDocumentPreview" alt="Document Preview" class="max-h-48 rounded border mx-auto">
                            </div>
                            <div class="mb-2 text-gray-700 dark:text-gray-200 flex items-center">
                                <i class="fas fa-file-alt mr-2"></i>
                                <span>{{ newDocumentName }}</span>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button @click="showDocumentModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button @click="saveDocument" :disabled="!newDocumentFile" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">Save Document</button>
                        </div>
                    </div>
                </div>
            </transition>
            <!-- Delete Document Confirmation Modal -->
            <transition enter-active-class="modal-enter-active" enter-from-class="modal-enter-from" enter-to-class="modal-enter-to" leave-active-class="modal-leave-active" leave-from-class="modal-leave-from" leave-to-class="modal-leave-to">
                <div v-if="showDeleteDocumentModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Confirm Delete</h3>
                        <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete your company document?</p>
                        <div class="flex justify-end gap-3">
                            <button @click="showDeleteDocumentModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                            <button @click="deleteDocument" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
        <!-- Edit Profile Modal -->
        <transition 
            enter-active-class="modal-enter-active"
            enter-from-class="modal-enter-from"
            enter-to-class="modal-enter-to"
            leave-active-class="modal-leave-active"
            leave-from-class="modal-leave-from"
            leave-to-class="modal-leave-to"
        >
            <div v-if="showEditModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-2 p-6 relative max-h-[90vh] overflow-y-auto">
                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closeEditModal">
                        <i class="fas fa-times"></i>
                    </button>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Edit Company Profile</h3>
                    <form @submit.prevent="saveProfile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name*</label>
                                <input type="text" v-model="editForm.company_name" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Address*</label>
                                <input type="text" v-model="editForm.company_location" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Email*</label>
                                <input type="email" v-model="editForm.contact_email" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Number*</label>
                                <input type="text" v-model="editForm.contact_number" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Industry Type*</label>
                                <input type="text" v-model="editForm.industry_type" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nature of Business*</label>
                                <input type="text" v-model="editForm.nature_of_business" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">TIN*</label>
                                <input type="text" v-model="editForm.tin" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Established*</label>
                                <input type="date" v-model="editForm.date_established" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type of Company*</label>
                                <input type="text" v-model="editForm.company_type" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Accreditation Status*</label>
                                <input type="text" v-model="editForm.accreditation_status" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="closeEditModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
        <!-- Photo Upload Modal -->
        <transition 
            enter-active-class="modal-enter-active"
            enter-from-class="modal-enter-from"
            enter-to-class="modal-enter-to"
            leave-active-class="modal-leave-active"
            leave-from-class="modal-leave-from"
            leave-to-class="modal-leave-to"
        >
            <div v-if="showPhotoModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-2 p-6 relative">
                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" @click="closePhotoModal">
                        <i class="fas fa-times"></i>
                    </button>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Update Company Logo</h3>
                    <div class="space-y-4">
                        <div class="flex justify-center">
                            <div class="relative">
                                <img :src="profile.company_logo || 'images/logo.png'" alt="Current Logo" class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-600">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Choose New Logo</label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                                <input type="file" ref="logoInput" @change="handleLogoUpload" accept="image/*" class="hidden">
                                <div class="cursor-pointer" @click="$refs.logoInput.click()">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600 dark:text-gray-300">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 5MB</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="newLogoPreview" class="flex justify-center">
                            <div class="relative">
                                <img :src="newLogoPreview" alt="New Logo Preview" class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                                <div class="absolute -top-2 -right-2 bg-green-500 text-white rounded-full p-1">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="closePhotoModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                        <button @click="saveLogo" :disabled="!newLogoPreview" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Update Logo
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </main>
    <footer class="bg-white dark:bg-gray-700 border-t dark:border-gray-600 py-3 sticky bottom-0 w-full">
        <div class="container text-center">
            <small class="text-gray-600 dark:text-gray-300">
                &copy; 2025 Laguna State Polytechnic University - Employment and Information System. All rights reserved.
            </small>
        </div>
    </footer>

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="js/employer_profile.js"></script>
</body>
</html>
