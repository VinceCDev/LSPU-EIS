<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | LSPU EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=UnifrakturCook:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'unifraktur': ['UnifrakturCook', 'serif'],
                        'poppins': ['Poppins', 'sans-serif']
                    },
                    colors: {
                        'lspu-blue': '#00A0E9',
                        'lspu-dark': '#1A1A1A',
                        'lspu-gold': '#FFD54F'
                    },
                    animation: {
                        'slide-in': 'slideIn 0.5s ease-out',
                    },
                    keyframes: {
                        slideIn: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-poppins">
    <!-- Header Section -->
    <header class="bg-gradient-to-r from-lspu-blue to-lspu-dark text-white shadow-md">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-center gap-4 py-4 px-6 animate-slide-in">
            <img src="images/logo.png" alt="LSPU Logo" class="h-20 w-auto">
            <div class="text-center md:text-left">
                <h1 class="font-unifraktur text-2xl md:text-3xl leading-tight">Laguna State Polytechnic University</h1>
                <p class="font-semibold text-sm md:text-base">INTEGRITY • PROFESSIONALISM • INNOVATION</p>
            </div>
        </div>
    </header>

    <!-- Menu Bar -->
    <div class="bg-lspu-gold py-2 shadow-sm"></div>

    <!-- Main Content - Facebook-style container width -->
    <div class="container mx-auto px-4 py-8 w-full max-w-[500px]"> <!-- FB-like width -->
        <!-- White Container Box -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden animate-slide-in">
            <!-- LSPU EIS Header -->
            <div class="flex items-center justify-center pt-5">
                <img src="images/alumni.png" alt="LSPU Logo" class="mr-0 w-[90px] h-auto">
                <div class="border-b-2 border-lspu-blue">
                    <p class="text-[2.5rem] font-bold uppercase flex items-center m-0">
                        <span class="font-black text-gray-800">LSPU</span>
                        <span class="font-light text-lspu-blue font-sans">EIS</span>
                    </p>
                </div>
            </div>

            <!-- Forgot Password Form -->
            <div class="px-6 pb-6">
                <!-- Add message display above the form -->
                <div v-if="message" :class="['my-4', messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700', 'border px-4 py-3 rounded']">{{ message }}</div>
                <form @submit.prevent="submitForgot" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="relative">
                            <i class="bi bi-person absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="email" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" 
                                   name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full px-4 py-2.5 bg-lspu-blue hover:bg-lspu-dark text-white font-semibold rounded-lg shadow-md transition duration-300">
                        FORGOT PASSWORD
                    </button>
                </form>

                <!-- Footer -->
                <div class="text-center mt-4 pt-4 border-t border-gray-200">
                    <p class="text-gray-600 text-xs">© All Rights Reserved | Laguna State Polytechnic University Employment and Information System</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js" defer></script>
    <script src="js/forgot_password.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>