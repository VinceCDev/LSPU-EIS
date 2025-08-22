<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - LSPU EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=UnifrakturCook:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Note: For production, install Tailwind locally instead of using the CDN. See https://tailwindcss.com/docs/installation -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script>
        // College and course data for LSPU San Pablo
        const collegeCourses = {
            "College of Computer Studies": [
                "BS Information Technology",
                "BS Computer Science"
            ],
            "College of Engineering": [
                "BS Electronics Engineering",
                "BS Electrical Engineering",
                "BS Computer Engineering"
            ],
            "College of Business Administration": [
                "BS Office Administration",
                "BS Business Administration Major in Financial Management",
                "BS Business Administration Major in Marketing Management",
                "BS Accountancy"
            ],
            "College of Education": [
                "BS Elementary Education",
                "BS Physical Education",
                "BS Secondary Education Major in English",
                "BS Secondary Education Major in Filipino",
                "BS Secondary Education Major in Mathematics",
                "BS Secondary Education Major in Science",
                "BS Secondary Education Major in Social Studies",
                "BS Technology and Livelihood Education Major in Home Economics",
                "BS Technical-Vocational Teacher Education Major in Electrical Technology",
                "BS Technical-Vocational Teacher Education Major in Electronics Technology",
                "BS Technical-Vocational Teacher Education Major in Food & Service Management",
                "BS Technical-Vocational Teacher Education Major in Garments, Fashion & Design"
            ],
            "College of Arts and Sciences": [
                "BS Psychology",
                "BS Biology"
            ],
            "College of Industrial Technology": [
                "BS Industrial Technology Major in Automotive Technology",
                "BS Industrial Technology Major in Architectural Drafting",
                "BS Industrial Technology Major in Electrical Technology",
                "BS Industrial Technology Major in Electronics Technology",
                "BS Industrial Technology Major in Food & Beverage Preparation and Service Management Technology",
                "BS Industrial Technology Major in Heating, Ventilating, Air-Conditioning & Refrigeration Technology"
            ],
            "College of Criminal Justice Education": [
                "BS Criminology"
            ],
            "College of Hospitality Management and Tourism": [
                "BS Hospitality Management",
                "BS Tourism Management"
            ]
        };

        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'unifraktur': ['UnifrakturCook', 'serif'],
                        'poppins': ['Poppins', 'sans-serif']
                    },
                    colors: {
                        'lspu-blue': '#00A0E9',  // Sky blue color
                        'lspu-dark': '#1A1A1A',  // Dark color
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

        function updateCourses() {
            const collegeSelect = document.getElementById('college');
            const courseSelect = document.getElementById('course');
            
            // Clear existing options except the first one
            while(courseSelect.options.length > 1) {
                courseSelect.remove(1);
            }
            
            // Get selected college
            const selectedCollege = collegeSelect.value;
            
            // Add new options if a college is selected
            if(selectedCollege && collegeCourses[selectedCollege]) {
                collegeCourses[selectedCollege].forEach(course => {
                    const option = new Option(course, course);
                    courseSelect.add(option);
                });
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

            <div id="signupApp">
                <div v-if="message" :class="{'bg-green-100 border-green-400 text-green-700': success, 'bg-red-100 border-red-400 text-red-700': !success}" class="border px-4 py-3 rounded relative max-w-xl mx-auto mt-6 mb-4 flex items-center gap-2">
                    <i v-if="success" class="bi bi-check-circle-fill text-green-500 text-xl"></i>
                    <i v-else class="bi bi-x-circle-fill text-red-500 text-xl"></i>
                    <span class="block">{{ message }}</span>
                </div>
                <form @submit.prevent="submitForm" enctype="multipart/form-data" class="p-6 md:p-8" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Account Information -->
                        <div class="md:col-span-1 space-y-6">
                            <h4 class="text-lg font-semibold text-gray-800 border-b-2 border-lspu-blue pb-2">Account Information</h4>
                            
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                <div class="relative">
                                    <i class="bi bi-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" name="email" v-model="form.email" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" 
                                           placeholder="your@email.com" required>
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Secondary Email Address</label>
                                <div class="relative">
                                    <i class="bi bi-envelope-plus absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" name="secondary_email" v-model="form.secondary_email" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" 
                                           placeholder="secondary@email.com">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Optional - for backup contact purposes</p>
                            </div>
                            
                            <!-- Password Field with helper text -->
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Password</label>
                                <div class="relative">
                                    <i class="bi bi-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="password" name="password" v-model="form.password" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" placeholder="Create password" required @input="validatePassword">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters with uppercase, lowercase, number, and special character</p>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <div class="relative">
                                    <i class="bi bi-shield-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="password" name="current_password" v-model="form.current_password" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition"
                                           placeholder="Confirm password" required
                                           minlength="8"
                                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$">
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="md:col-span-2 md:border-l md:border-gray-200 md:pl-8 space-y-6">
                            <h4 class="text-lg font-semibold text-gray-800 border-b-2 border-lspu-blue pb-2">Personal Information</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Name Fields -->
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">First Name</label>
                                    <div class="relative">
                                        <i class="bi bi-person absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" name="first_name" v-model="form.first_name" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" 
                                               placeholder="First name" required>
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                                    <div class="relative">
                                        <i class="bi bi-person absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" name="middle_name" v-model="form.middle_name" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" 
                                               placeholder="Middle name">
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <div class="relative">
                                        <i class="bi bi-person absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" name="last_name" v-model="form.last_name" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" 
                                               placeholder="Last name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Personal Details -->
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Birth Date</label>
                                    <div class="relative">
                                        <i class="bi bi-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="date" name="birthdate" v-model="form.birthdate" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" required>
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                                    <div class="relative">
                                        <i class="bi bi-telephone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="tel" name="contact" v-model="form.contact" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" 
                                               placeholder="09123456789" required>
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Gender</label>
                                    <div class="relative">
                                        <i class="bi bi-gender-ambiguous absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select name="gender" v-model="form.gender" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition appearance-none" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Province before City -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Province</label>
                                    <div class="relative">
                                        <i class="bi bi-geo absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select name="province" v-model="form.province" @change="fetchCities" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition appearance-none" required>
                                            <option value="">Select Province</option>
                                            <option v-for="province in provinces" :key="province.code" :value="province.name">{{ province.name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">City/Municipality</label>
                                    <div class="relative">
                                        <i class="bi bi-geo-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select name="city" v-model="form.city" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition appearance-none" required :disabled="!cities.length">
                                            <option value="">Select City/Municipality</option>
                                            <option v-for="city in cities" :key="city.code" :value="city.name">{{ city.name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Civil Status -->
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Civil Status</label>
                                    <div class="relative">
                                        <i class="bi bi-people absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select name="civil_status" v-model="form.civil_status" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition appearance-none" required>
                                            <option value="">Select Status</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Divorced">Divorced</option>
                                            <option value="Widowed">Widowed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Academic Information -->
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Year Graduated</label>
                                    <div class="relative">
                                        <i class="bi bi-calendar-check absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="number" name="year_graduated" v-model="form.year_graduated" min="1900" max="2099" step="1" 
                                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" 
                                               placeholder="YYYY" required>
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">College</label>
                                    <div class="relative">
                                        <i class="bi bi-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select id="college" name="college" v-model="form.college" onchange="updateCourses()" 
                                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition appearance-none" required>
                                            <option value="">Select College</option>
                                            <option value="College of Computer Studies">College of Computer Studies</option>
                                            <option value="College of Engineering">College of Engineering</option>
                                            <option value="College of Business Administration">College of Business Administration</option>
                                            <option value="College of Education">College of Education</option>
                                            <option value="College of Arts and Sciences">College of Arts and Sciences</option>
                                            <option value="College of Industrial Technology">College of Industrial Technology</option>
                                            <option value="College of Criminal Justice Education">College of Criminal Justice Education</option>
                                            <option value="College of Hospitality Management and Tourism">College of Hospitality Management and Tourism</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">Course</label>
                                    <div class="relative">
                                        <i class="bi bi-book absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select id="course" name="course" v-model="form.course" 
                                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition appearance-none" required>
                                            <option value="">Select College First</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Documents for Verification -->
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Documents for Verification</label>
                                <div class="relative">
                                    <i class="bi bi-file-earmark-text absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="file" name="verification_documents" @change="handleFileUpload" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" 
                                           accept=".pdf,.jpg,.jpeg,.png,.gif" required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Upload your Alumni Card, Diploma, or any LSPU graduate-related document (PDF, JPG, PNG, GIF - Max 5MB)</p>
                                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-xs text-blue-800 font-medium mb-1">Accepted Documents:</p>
                                    <ul class="text-xs text-blue-700 space-y-1">
                                        <li>• Alumni ID Card</li>
                                        <li>• Diploma/Certificate of Graduation</li>
                                        <li>• Transcript of Records</li>
                                        <li>• Certificate of Enrollment (if recent graduate)</li>
                                        <li>• Any official LSPU document with your name and graduation details</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-center">
                        <button type="submit" :disabled="loading" class="px-8 py-3 bg-lspu-blue hover:bg-lspu-dark text-white font-semibold rounded-lg shadow-md transition duration-300 transform hover:scale-105 flex items-center gap-2">
                            <span v-if="loading"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                            <span>{{ loading ? 'Registering...' : 'REGISTER NOW' }}</span>
                        </button>
                    </div>
                </form>

                <div class="text-center pb-6 px-6">
                    <p class="text-gray-600">Already have an account? 
                        <a href="login" class="text-lspu-blue hover:text-lspu-dark font-medium transition">Sign in here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script src="js/signup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>