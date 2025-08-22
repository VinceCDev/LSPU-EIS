<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Sign Up - LSPU EIS</title>
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
    <!-- Header Section with Sky Blue to Black Gradient -->
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

    <!-- Main Container -->
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- White Container Box -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden animate-slide-in">
            <!-- Blue Background LSPU EIS Header -->
            <div class="flex items-center justify-center pt-5">
                <img src="images/alumni.png" alt="LSPU Logo" class="mr-0 w-[90px] h-auto">
                <div class="border-b-2 border-lspu-blue">
                    <p class="text-[2.5rem] font-bold uppercase flex items-center m-0">
                        <span class="font-black text-gray-800">LSPU</span>
                        <span class="font-light text-lspu-blue font-sans">EIS</span>
                    </p>
                </div>
            </div>

            <!-- Registration Form -->
            <!-- Feedback message above the form -->
            <!-- Feedback message above the form -->

            <form @submit.prevent="submitForm" enctype="multipart/form-data" class="p-6 md:p-8">
            <div v-if="message" :class="['my-4', messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700', 'border px-4 py-3 rounded']">
  {{ message }}
</div>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Account Information -->
                    <div class="md:col-span-1 space-y-6">
                        <h4 class="text-lg font-semibold text-gray-800 border-b-2 border-lspu-blue pb-2">Account Information</h4>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="relative">
                                <i class="bi bi-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="email" name="email" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="your@email.com" required>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="relative">
                                <i class="bi bi-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="password" name="password" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="Create password" required minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Password must be at least 8 characters with uppercase, lowercase, number, and special character">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters with uppercase, lowercase, number, and special character</p>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <div class="relative">
                                <i class="bi bi-shield-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="password" name="current_password" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="Confirm password" required minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$">
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="md:col-span-2 md:border-l md:border-gray-200 md:pl-8 space-y-6">
                        <h4 class="text-lg font-semibold text-gray-800 border-b-2 border-lspu-blue pb-2">Company Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Company Name</label>
                                <div class="relative">
                                    <i class="bi bi-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="company_name" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="Company Name" required>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Company Logo (Optional)</label>
                                <input type="file" name="company_logo" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" accept="image/jpeg, image/png, image/gif">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Company Address</label>
                                <div class="relative">
                                    <i class="bi bi-geo-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="company_location" v-model="companyLocation" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="Company Address" required autocomplete="off">
                                    <ul v-if="addressSuggestions.length" class="bg-white border rounded shadow mt-1 absolute z-10 w-full">
                                        <li v-for="suggestion in addressSuggestions"
                                            :key="suggestion"
                                            @click="selectSuggestion(suggestion)"
                                            class="px-4 py-2 hover:bg-lspu-blue hover:text-white cursor-pointer">
                                            {{ suggestion }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                                <div class="relative">
                                    <i class="bi bi-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" name="contact_email" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="Contact Email" required>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                                <div class="relative">
                                    <i class="bi bi-telephone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="contact_number" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="Landline or Mobile Number" required>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Industry Type</label>
                                <div class="relative">
                                    <i class="bi bi-layers absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <select name="industry_type" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition appearance-none" required>
                                        <option value="" disabled selected>Select Industry</option>
                                        <option value="Retail">Retail</option>
                                        <option value="Technology">Technology</option>
                                        <option value="Healthcare">Healthcare</option>
                                        <option value="Education">Education</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Manufacturing">Manufacturing</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Nature of Business</label>
                                <div class="relative">
                                    <i class="bi bi-briefcase absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="nature_of_business" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="e.g., IT Services, Consulting" required>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">TIN</label>
                                <div class="relative">
                                    <i class="bi bi-file-earmark-text absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="tin" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="e.g., 123-456-789" required>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Date Established</label>
                                <div class="relative">
                                    <i class="bi bi-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="date" name="date_established" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" required>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Type of Company</label>
                                <div class="relative">
                                    <i class="bi bi-diagram-3 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <select name="company_type" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition appearance-none" required>
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="LLC">LLC</option>
                                        <option value="Corporation">Corporation</option>
                                        <option value="Partnership">Partnership</option>
                                        <option value="Sole Proprietorship">Sole Proprietorship</option>
                                        <option value="Non-profit">Non-profit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Accreditation Status</label>
                                <div class="relative">
                                    <i class="bi bi-patch-check absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <select name="accreditation_status" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition appearance-none" required>
                                        <option value="" disabled selected>Select Accreditation</option>
                                        <option value="None">None</option>
                                        <option value="DOLE">DOLE Accredited</option>
                                        <option value="ISO">ISO Certified</option>
                                        <option value="CHED">CHED Recognized</option>
                                        <option value="TESDA">TESDA Recognized</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Upload Document</label>
                                <input type="file" name="document_file" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" required>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit Button -->
                <div class="mt-8 flex justify-center">
                    <button type="submit" class="px-8 py-3 bg-lspu-blue hover:bg-lspu-dark text-white font-semibold rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                        REGISTER NOW
                    </button>
                </div>
            </form>
            <div class="text-center pb-6 px-6">
                <p class="text-gray-600">Already have an employer account? 
                    <a href="employer_login" class="text-lspu-blue hover:text-lspu-dark font-medium transition">Login here</a>
                </p>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/employer_signup.js"></script>
</body>
</html>