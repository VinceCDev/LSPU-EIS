<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Sign Up | LSPU EIS</title>
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
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs text-blue-800 font-medium mb-1">Accepted Documents:</p>
                            <ul class="text-xs text-blue-700 space-y-1">
                                <li>• Business Permit / Mayor's Permit</li>
                                <li>• SEC/DTI Registration Certificate</li>
                                <li>• BIR Registration Form (2303)</li>
                                <li>• Company Profile with Official Seal</li>
                                <li>• Partnership Documents</li>
                                <li>• Any official government-issued business registration document</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Add this right before the Submit Button section -->

                <!-- Disclaimer Agreement -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start mb-3">
                        <div class="flex items-center h-5">
                            <input id="employer_disclaimer" type="checkbox" v-model="agreeToDisclaimer" class="w-4 h-4 text-lspu-blue bg-gray-100 border-gray-300 rounded focus:ring-lspu-blue focus:ring-2" required>
                        </div>
                        <label for="employer_disclaimer" class="ms-2 text-sm font-medium text-gray-900">
                            I have read, understood, and agree to the terms of the 
                            <a href="#" class="text-lspu-blue hover:underline" @click.prevent="openDisclaimerModal">Employer Terms & Data Privacy Policy</a>.
                            <span class="text-red-600">*</span>
                        </label>
                    </div>
                    <p class="text-xs text-blue-700">
                        <strong>Employer Disclaimer:</strong> By registering, you confirm your company is legitimate and agree to use alumni data solely for legitimate hiring purposes in compliance with the Data Privacy Act. You understand that misuse of alumni information will result in immediate account termination and potential legal action.
                    </p>
                </div>

                <!-- Add this modal right after the Disclaimer Agreement section -->
                <!-- Employer Disclaimer Modal -->
                <div id="employerDisclaimerModal" v-if="showDisclaimerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-start justify-center z-50 pt-10">
                    <div class="relative mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <div class="flex justify-between items-center pb-3 border-b">
                                <h3 class="text-xl font-semibold text-gray-900">Employer Terms of Service & Data Privacy Policy</h3>
                                <button @click="closeDisclaimerModal" class="text-gray-500 hover:text-gray-700">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="mt-4 mb-6 max-h-96 overflow-y-auto text-sm">
                                <p class="font-semibold mb-2 text-lspu-blue">1. EMPLOYER RESPONSIBILITIES</p>
                                <p class="mb-4">By registering as an employer on the LSPU Alumni Employment Information System, you affirm that your company is a legitimate business entity and that all information provided is accurate and truthful. You agree to use this platform exclusively for legitimate hiring purposes and career opportunity postings.</p>

                                <p class="font-semibold mb-2 text-lspu-blue">2. DATA PRIVACY ACT COMPLIANCE (RA 10173)</p>
                                <p class="mb-4">LSPU strictly adheres to the Philippine Data Privacy Act of 2012. All company information collected through this system is processed in accordance with the law's principles of transparency, legitimate purpose, and proportionality. We implement appropriate organizational, physical, and technical security measures to protect your data against unauthorized access, disclosure, or destruction.</p>

                                <p class="font-semibold mb-2 text-lspu-blue">3. ALUMNI DATA USAGE RESTRICTIONS</p>
                                <p class="mb-4">Alumni information accessed through this system must be used exclusively for employment consideration purposes. You are prohibited from:</p>
                                <ul class="list-disc pl-5 mb-4">
                                    <li>Sharing alumni data with third parties without explicit consent</li>
                                    <li>Using alumni information for marketing purposes unrelated to employment</li>
                                    <li>Storing alumni data beyond what is necessary for the hiring process</li>
                                    <li>Discriminating based on protected characteristics (gender, religion, etc.)</li>
                                    <li>Making hiring decisions based on non-job-related criteria</li>
                                </ul>

                                <p class="font-semibold mb-2 text-lspu-blue">4. JOB POSTING GUIDELINES</p>
                                <p class="mb-4">All job postings must represent actual employment opportunities with fair compensation and legitimate working conditions. LSPU reserves the right to remove any job posting that:</p>
                                <ul class="list-disc pl-5 mb-4">
                                    <li>Contains false or misleading information</li>
                                    <li>Requires payment from applicants</li>
                                    <li>Involves pyramid schemes or illegal activities</li>
                                    <li>Discriminates against protected groups</li>
                                    <li>Offers compensation below minimum wage standards</li>
                                </ul>

                                <p class="font-semibold mb-2 text-lspu-blue">5. VERIFICATION PROCESS</p>
                                <p class="mb-4">LSPU conducts verification of employer accounts to ensure legitimacy. This may include contacting your company directly, verifying business registration documents, and checking with appropriate government agencies. Until verification is complete, your access to employer account will be approved.</p>

                                <p class="font-semibold mb-2 text-lspu-blue">6. DATA SECURITY REQUIREMENTS</p>
                                <p class="mb-4">Employers must implement appropriate technical and organizational measures to protect alumni data against unauthorized access, disclosure, or destruction. This includes secure storage, access controls, and proper disposal methods when data is no longer needed for hiring purposes.</p>

                                <p class="font-semibold mb-2 text-lspu-blue">7. CONTACT INFORMATION</p>
                                    <p class="mb-4">For questions, concerns, or requests regarding your personal data, please contact our Alumni Affairs and Placement Services Office at: <br>
                                    <strong>LSPU Alumni Affairs and Placement Services Office</strong><br>
                                    Laguna State Polytechnic University<br>
                                    lspuspcc.alumni@lspu.edu.ph | +63 (049) 554 9910</p>

                                <p class="text-xs text-gray-500 italic">This Privacy Policy and Disclaimer was last updated on August 2025 and may be updated periodically to reflect changes in our practices or legal requirements. Continued use of the system constitutes acceptance of any modifications.</p>
                            </div>
                            <div class="items-center px-4 py-3">
                                <button @click="closeDisclaimerModal" class="px-4 py-2 bg-lspu-blue text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-lspu-dark focus:outline-none focus:ring-2 focus:ring-lspu-blue">
                                    I Understand and Agree
                                </button>
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
                    <a href="login" class="text-lspu-blue hover:text-lspu-dark font-medium transition">Sign in here</a>
                </p>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/employer_signup.js"></script>
</body>
</html>