<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPU - EIS | Laguna State Polytechnic University Employment Information System</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.prod.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'lspu-blue': '#1e40af',
                        'lspu-gold': '#f59e0b'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out infinite 3s',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    },
                    extend: {
                        animation: {
                            'delay-2000': 'delay-2000 2s',
                            'delay-4000': 'delay-4000 4s',
                        }
                    },
                    animation: {
                        slideInDown: 'slideInDown 0.8s ease-out',
                        slideInLeft: 'slideInLeft 0.8s ease-out',
                        slideInRight: 'slideInRight 0.8s ease-out',
                        slideInUp: 'slideInUp 0.8s ease-out',
                    },
                    keyframes: {
                        slideInDown: {
                        '0%': { transform: 'translateY(-100px)', opacity: '0' },
                        '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideInLeft: {
                        '0%': { transform: 'translateX(-100px)', opacity: '0' },
                        '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                        slideInRight: {
                        '0%': { transform: 'translateX(100px)', opacity: '0' },
                        '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                        slideInUp: {
                        '0%': { transform: 'translateY(100px)', opacity: '0' },
                        '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                    }
                }
            }
        }
    </script>

</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div id="app">
        <!-- Toast Notifications -->
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
                         'bg-red-100 border-red-500 text-red-700': notification.type === 'error'
                     }" 
                     class="border-l-4 p-4 rounded-lg shadow-lg relative pr-8 flex items-start" role="alert" tabindex="0">
                    <div class="flex-shrink-0 mt-1">
                        <i v-if="notification.type === 'success'" class="fas fa-check-circle text-lg text-green-600"></i>
                        <i v-if="notification.type === 'error'" class="fas fa-exclamation-circle text-lg text-red-600"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-bold capitalize">{{ notification.type }}</h3>
                        <p class="text-sm mt-1">{{ notification.message }}</p>
                    </div>
                    <button @click="removeNotification(notification.id)" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" aria-label="Close notification">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </transition-group>
        </div>

        <!-- Navigation -->
        <nav class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-md shadow-lg dark:shadow-gray-800/50 fixed w-full z-50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo with fade-in animation -->
                    <div class="flex items-center animate-[fadeIn_0.5s_ease-in-out]">
                        <div class="flex-shrink-0 flex items-center">
                            <img 
                                src="images/logo.png" 
                                alt="LSPU Logo" 
                                class="h-10 w-10 object-contain p-1 transition-transform duration-300 hover:scale-110"
                            >
                        </div>
                        <div class="ml-3">
                            <span class="text-2xl font-bold text-gray-800 dark:text-white transition-colors duration-200 hover:text-lspu-blue dark:hover:text-blue-400">LSPU</span>
                            <span class="text-2xl font-light text-lspu-blue dark:text-blue-400 transition-colors duration-200 hover:text-blue-700 dark:hover:text-blue-300">EIS</span>
                        </div>
                    </div>
                    
                    <!-- Desktop Navigation with staggered animations -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#home" @click="scrollToSection('home')" 
                        class="text-gray-700 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800 transform hover:-translate-y-0.5"
                        style="animation: slideInDown 0.5s ease-out 0.1s both">Home</a>
                        
                        <a href="#about" @click="scrollToSection('about')" 
                        class="text-gray-700 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800 transform hover:-translate-y-0.5"
                        style="animation: slideInDown 0.5s ease-out 0.2s both">About</a>
                        
                        <a href="#contact" @click="scrollToSection('contact')" 
                        class="text-gray-700 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800 transform hover:-translate-y-0.5"
                        style="animation: slideInDown 0.5s ease-out 0.3s both">Contact</a>
                        
                        <a href="login" 
                        class="bg-lspu-blue hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95"
                        style="animation: slideInDown 0.5s ease-out 0.4s both">Login</a>
                    </div>
                    
                    <!-- Mobile menu button with bounce animation -->
                    <div class="md:hidden flex items-center space-x-4">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                class="text-gray-700 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-400 focus:outline-none transition-colors duration-200 animate-[bounceIn_0.5s_ease-in-out]">
                            <i :class="{'fa-bars': !mobileMenuOpen, 'fa-times': mobileMenuOpen}" class="fas text-xl transform transition-transform duration-300 hover:scale-125"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Navigation with slide-down animation -->
            <transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-4"
            >
                <div v-show="mobileMenuOpen" class="md:hidden bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-700">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <a href="#home" @click="scrollToSection('home'); mobileMenuOpen = false" 
                        class="block px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md text-base font-medium transition-all duration-200 transform hover:translate-x-2">
                        <i class="fas fa-home mr-2"></i> Home
                        </a>
                        
                        <a href="#about" @click="scrollToSection('about'); mobileMenuOpen = false" 
                        class="block px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md text-base font-medium transition-all duration-200 transform hover:translate-x-2">
                        <i class="fas fa-info-circle mr-2"></i> About
                        </a>
                        
                        <a href="#contact" @click="scrollToSection('contact'); mobileMenuOpen = false" 
                        class="block px-3 py-2 text-gray-700 dark:text-gray-300 hover:text-lspu-blue dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md text-base font-medium transition-all duration-200 transform hover:translate-x-2">
                        <i class="fas fa-envelope mr-2"></i> Contact
                        </a>
                        
                        <a href="login" 
                        class="block px-3 py-2 bg-lspu-blue dark:bg-blue-600 hover:bg-blue-700 text-white rounded-md text-base font-medium transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                        </a>
                    </div>
                </div>
            </transition>
        </nav>

       <!-- Hero Section with Enhanced Design -->
       <section id="home" class="relative pt-16 min-h-screen flex items-center overflow-hidden">
            <!-- Enhanced Background with LSPU Campus Image -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 via-blue-800/85 to-indigo-900/90 dark:from-gray-900/95 dark:via-gray-800/90 dark:to-gray-900/95">
                <!-- LSPU Campus Background Image -->
                <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30" style="background-image: url('images/lspu_campus.jpg'), linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #4f46e5 100%);"></div>
                
                <!-- Animated Background Pattern Overlay -->
                <div class="absolute inset-0 opacity-5">
                    <div class="absolute top-0 left-0 w-48 h-48 md:w-72 md:h-72 bg-lspu-gold rounded-full mix-blend-multiply filter blur-xl animate-pulse"></div>
                    <div class="absolute top-0 right-0 w-48 h-48 md:w-72 md:h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl animate-pulse" style="animation-delay: 2s;"></div>
                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-48 h-48 md:w-72 md:h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-xl animate-pulse" style="animation-delay: 4s;"></div>
                </div>
                
                <!-- Subtle Grid Pattern -->
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 30px 30px, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 60px 60px;"></div>
            </div>
            
            <!-- Content Overlay -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 items-center">
                    <!-- Left Content -->
                    <div class="space-y-6 md:space-y-8 text-white text-center lg:text-left">
                        <!-- Badge - Slide Down -->
                        <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-sm font-medium text-white/90 animate-[slideInDown_0.8s_ease-out] mx-auto lg:mx-0">
                            <i class="fas fa-star text-lspu-gold mr-2"></i>
                            Official LSPU Employment Platform
                        </div>
                        
                        <!-- Main Heading - Slide Left -->
                        <div class="space-y-4 md:space-y-6">
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold leading-tight animate-[slideInLeft_0.8s_ease-out]">
                                Welcome to 
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-lspu-gold to-yellow-400 block sm:inline-block">LSPU-EIS</span>
                            </h1>
                            <p class="text-lg sm:text-xl lg:text-2xl text-blue-100 leading-relaxed max-w-2xl mx-auto lg:mx-0 animate-[slideInLeft_1s_ease-out]">
                                The official Employment Information System of Laguna State Polytechnic University. 
                                Connecting our talented alumni with exceptional career opportunities worldwide.
                            </p>
                        </div>
                        
                        <!-- Enhanced CTA Button - Slide Up -->
                        <div class="flex flex-col sm:flex-row gap-4 md:gap-6 justify-center lg:justify-start animate-[slideInUp_1.2s_ease-out]">
                            <a href="signup" class="group relative inline-flex items-center justify-center px-6 py-3 md:px-8 md:py-4 bg-gradient-to-r from-lspu-gold to-yellow-500 text-white font-semibold text-base md:text-lg rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl shadow-lg overflow-hidden w-full sm:w-auto">
                                <span class="absolute inset-0 bg-gradient-to-r from-yellow-600 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative flex items-center justify-center">
                                    <i class="fas fa-rocket mr-3 text-lg md:text-xl group-hover:animate-bounce"></i>
                                    Get Started
                                </span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right Content - Enhanced Logo Display - Slide Right -->
                    <div class="relative animate-[slideInRight_1s_ease-out] mt-10 lg:mt-0 lg:col-span-1">
                        <!-- Main Logo Container - Made Wider -->
                        <div class="relative group w-full max-w-lg mx-auto lg:mx-0 lg:ml-auto">
                            <!-- Glow Effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-lspu-gold to-yellow-400 rounded-3xl blur-2xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
                            
                            <!-- Logo Container - Made Wider -->
                            <div class="relative bg-white/5 backdrop-blur-sm p-6 md:p-10 lg:p-12 rounded-3xl border border-white/10 shadow-2xl group-hover:shadow-3xl transition-all duration-500 transform group-hover:scale-105 w-full">
                                <!-- Wider Image Container -->
                                <div class="w-full h-[405px] mx-auto flex justify-center">
                                    <img src="images/logo.png" alt="LSPU Logo" class="w-full max-w-md object-contain">
                                </div>
                                
                                <!-- Floating Elements -->
                                <div class="absolute -top-3 -right-3 md:-top-4 md:-right-4 w-6 h-6 md:w-8 md:h-8 bg-lspu-gold rounded-full flex items-center justify-center animate-bounce">
                                    <i class="fas fa-check text-white text-xs md:text-sm"></i>
                                </div>
                                <div class="absolute -bottom-3 -left-3 md:-bottom-4 md:-left-4 w-5 h-5 md:w-6 md:h-6 bg-blue-400 rounded-full animate-pulse"></div>
                            </div>
                        </div>
                        
                        <!-- Floating Cards -->
                        <div class="absolute -top-6 -left-2 md:-top-8 md:-left-4 bg-white/10 backdrop-blur-sm p-3 md:p-4 rounded-2xl border border-white/20 shadow-lg animate-[slideInRight_1.2s_ease-out] hidden sm:block">
                            <div class="flex items-center space-x-2 md:space-x-3">
                                <div class="w-2 h-2 md:w-3 md:h-3 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="text-white text-xs md:text-sm font-medium">Live Platform</span>
                            </div>
                        </div>
                        
                        <div class="absolute -bottom-6 -right-4 md:-bottom-8 md:-right-8 bg-white/10 backdrop-blur-sm p-3 md:p-4 rounded-2xl border border-white/20 shadow-lg animate-[slideInRight_1.4s_ease-out] hidden sm:block">
                            <div class="flex items-center space-x-2 md:space-x-3">
                                <i class="fas fa-shield-alt text-lspu-gold text-xs md:text-base"></i>
                                <span class="text-white text-xs md:text-sm font-medium">Secure & Verified</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scroll Indicator - Slide Up -->
            <div class="absolute bottom-4 md:bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce animate-[slideInUp_1.6s_ease-out] hidden md:block">
                <div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-white/60 rounded-full mt-2 animate-pulse"></div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white dark:bg-gray-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 transition-colors duration-200">Why Choose LSPU-EIS?</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto transition-colors duration-200">
                        Our comprehensive platform bridges the gap between LSPU alumni and employers, 
                        providing seamless job matching and career development opportunities.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-8 rounded-2xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-blue-200 dark:border-blue-700">
                        <div class="bg-lspu-blue p-4 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                            <i class="fas fa-search text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 transition-colors duration-200">Smart Job Matching</h3>
                        <p class="text-gray-600 dark:text-gray-300 transition-colors duration-200">
                            Match jobs based on your programs or skills with advanced algorithms.
                        </p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 p-8 rounded-2xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-yellow-200 dark:border-yellow-700">
                        <div class="bg-lspu-gold p-4 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                            <i class="fas fa-briefcase text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 transition-colors duration-200">Employer Portal</h3>
                        <p class="text-gray-600 dark:text-gray-300 transition-colors duration-200">
                            Employers post and hire jobs directly through our platform.
                        </p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-8 rounded-2xl hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-green-200 dark:border-green-700">
                        <div class="bg-green-500 p-4 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 transition-colors duration-200">Career Analytics</h3>
                        <p class="text-gray-600 dark:text-gray-300 transition-colors duration-200">
                            Track your career progress and get insights into industry trends.
                        </p>
                    </div>
                    

                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-8 transition-colors duration-200">About LSPU-EIS</h2>
                        <div class="space-y-6 text-gray-600 dark:text-gray-300 transition-colors duration-200">
                            <p class="text-lg leading-relaxed">
                                The Laguna State Polytechnic University Employment Information System (LSPU-EIS) 
                                is a comprehensive digital platform designed to facilitate seamless connections 
                                between LSPU alumni and potential employers.
                            </p>
                            <p class="text-lg leading-relaxed">
                                Our system is deeply integrated with the university's academic programs and 
                                maintains strong partnerships with the Alumni Affairs and Placement Services Office (AAPs) 
                                to ensure quality job placements and career development opportunities.
                            </p>
                            <p class="text-lg leading-relaxed">
                                Through our partnership with AAPs, we provide access to quality employers 
                                and ensure that our alumni are connected with organizations that meet the 
                                highest standards of professional excellence.
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center mb-4">
                                <div class="bg-lspu-blue p-3 rounded-full mr-4">
                                    <i class="fas fa-university text-white"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white transition-colors duration-200">LSPU Integration</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 transition-colors duration-200">
                                Direct integration with LSPU's academic records, ensuring accurate 
                                credential verification and seamless profile management.
                            </p>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center mb-4">
                                <div class="bg-lspu-gold p-3 rounded-full mr-4">
                                    <i class="fas fa-handshake text-white"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white transition-colors duration-200">AAPs Partnership</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 transition-colors duration-200">
                                Strategic partnership with the Alumni Affairs and Placement Services Office, 
                                providing access to quality employers and verified job opportunities.
                            </p>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center mb-4">
                                <div class="bg-green-500 p-3 rounded-full mr-4">
                                    <i class="fas fa-shield-alt text-white"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white transition-colors duration-200">Quality Assurance</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 transition-colors duration-200">
                                Rigorous verification processes ensure that all employers and job 
                                postings meet our high standards for quality and legitimacy.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-white dark:bg-gray-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 transition-colors duration-200">Get in Touch</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 transition-colors duration-200">
                        Have questions? We're here to help you succeed in your career journey.
                    </p>
                </div>
                
                <!-- Contact Section with Form on Right -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
    <!-- Contact Information - Left Side -->
                    <div class="space-y-6">
                        <!-- Location Card -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/50 dark:to-blue-800/50 p-6 rounded-2xl border border-blue-200 dark:border-blue-700 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="bg-lspu-blue p-3 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                                    <i class="fas fa-map-marker-alt text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Location</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">LSPU San Pablo City Campus<br>Brgy. Del Remedio, San Pablo City, Laguna</p>
                                </div>
                            </div>
                        </div>

                        <!-- Phone Card -->
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/50 dark:to-yellow-800/50 p-6 rounded-2xl border border-yellow-200 dark:border-yellow-700 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="bg-lspu-gold p-3 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                                    <i class="fas fa-phone text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Phone</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">+63 (049) 554 9910</p>
                                </div>
                            </div>
                        </div>

                        <!-- Email Card -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/50 dark:to-green-800/50 p-6 rounded-2xl border border-green-200 dark:border-green-700 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="bg-green-500 p-3 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                                    <i class="fas fa-envelope text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Email</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">lspuspcc.alumni@lspu.edu.ph</p>
                                </div>
                            </div>
                        </div>

                        <!-- Office Hours Card -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/50 dark:to-purple-800/50 p-6 rounded-2xl border border-purple-200 dark:border-purple-700 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="bg-purple-500 p-3 rounded-full w-12 h-12 flex items-center justify-center mr-4">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Office Hours</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Mon-Fri: 8AM-5PM</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">AAPs - Alumni Affairs and Placement Services</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Form - Right Side -->
                    <div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 p-8 rounded-2xl border border-gray-200 dark:border-gray-600 h-fit transition-colors duration-300">
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 text-left transition-colors duration-200">Send us a Message</h3>
                        <form @submit.prevent="submitContactForm" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">Name</label>
                                    <input type="text" v-model="contactForm.name" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-lspu-blue focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">Age</label>
                                    <input type="number" v-model="contactForm.age" required min="1" max="120" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-lspu-blue focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">Email</label>
                                <input type="email" v-model="contactForm.email" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-lspu-blue focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-200">Message</label>
                                <textarea v-model="contactForm.message" rows="3" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-lspu-blue focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-lspu-blue hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-paper-plane mr-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gradient-to-r from-gray-900 to-gray-800 dark:from-gray-950 dark:to-gray-900 text-white py-12 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <img src="images/logo.png" alt="LSPU Logo" class="h-10 w-auto">
                            <div class="ml-3">
                                <span class="text-xl font-bold">LSPU</span>
                                <span class="text-xl font-light text-blue-300">EIS</span>
                            </div>
                        </div>
                        <p class="text-gray-400">
                            Empowering LSPU alumni with exceptional career opportunities through our comprehensive employment information system.
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="#home" @click="scrollToSection('home')" class="text-gray-400 hover:text-white transition-colors duration-200">Home</a></li>
                            <li><a href="#about" @click="scrollToSection('about')" class="text-gray-400 hover:text-white transition-colors duration-200">About</a></li>
                            <li><a href="#contact" @click="scrollToSection('contact')" class="text-gray-400 hover:text-white transition-colors duration-200">Contact</a></li>
                            <li><a href="login" class="text-gray-400 hover:text-white transition-colors duration-200">Login</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-4">For Alumni</h3>
                        <ul class="space-y-2">
                            <li><a href="signup" class="text-gray-400 hover:text-white transition-colors duration-200">Create Account</a></li>
                            <li><a href="login" class="text-gray-400 hover:text-white transition-colors duration-200">Job Search</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Career Resources</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Alumni Network</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-4">For Employers</h3>
                        <ul class="space-y-2">
                            <li><a href="employer_signup" class="text-gray-400 hover:text-white transition-colors duration-200">Post Jobs</a></li>
                            <li><a href="employer_login" class="text-gray-400 hover:text-white transition-colors duration-200">Employer Login</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Partnership</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Contact Sales</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400">
                        &copy; 2025 Laguna State Polytechnic University Employment Information System. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>

        <!-- Floating Dark Mode Toggle -->
        <button @click="toggleDarkMode" class="fixed bottom-6 right-6 z-50 bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 border border-gray-200 dark:border-gray-600">
            <i v-if="darkMode" class="fas fa-sun text-xl text-yellow-500"></i>
            <i v-else class="fas fa-moon text-xl text-gray-600"></i>
        </button>
    </div>

    <script src="js/index.js"></script>
</body>
</html>
