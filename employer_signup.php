<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSPU Alumni Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/sign_up.css">
</head>

<body>

    <div class="header d-flex flex-column flex-md-row align-items-center justify-content-center text-white p-3text-center">
        <img src="images/logo.png" alt="LSPU Logo" class="mb-1 mb-md-0 me-md-1" style="width: 150px; height: auto;">
        <div>
            <h2 class="font-lspu m-0 p-0">Laguna State Polytechnic University</h2>
            <p class="motto m-0 small">INTEGRITY • PROFESSIONALISM • INNOVATION</p>
        </div>
    </div>
    <div class="menu-bar">
    </div>

    <div class="login-container">
        <div class="d-flex align-items-center pb-10 justify-content-center">
            <img src="images/alumni.png" alt="LSPU Logo" class="me-0" style="width: 90px; height: auto;">
            <div class="lspu-eis-container">
                <p class="lspu-eis m-0"><span class="bold-text">LSPU</span><span class="thin-text">EIS</span></p>
            </div>
        </div>

        <div class="p-3 mt-2">
            <form action="functions/employer_registration.php" method="POST" enctype="multipart/form-data">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="row">
                    <!-- Left: Account Information -->
                    <div class="col-md-4 info">
                        <h5 class="fw-bold">Account Information</h5>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-icon">
                                <i class="bi bi-envelope"></i>
                                <input type="email" class="form-control" id="email" placeholder="Enter your email" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-icon">
                                <i class="bi bi-lock"></i>
                                <input type="password" class="form-control" id="password" placeholder="Enter your password" name="password" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="current_password">Current Password</label>
                            <div class="input-icon">
                                <i class="bi bi-shield-lock"></i>
                                <input type="password" class="form-control" id="current_password" placeholder="Enter current password" name="current_password" required>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Company Details -->
                    <div class="col-md-8">
                        <h5 class="fw-bold">Company Details</h5>
                        <div class="row">
                            <!-- Company Name -->
                            <div class="col-md-4">
                                <label class="form-label" for="company_name">Company Name</label>
                                <div class="input-icon">
                                    <i class="bi bi-building"></i>
                                    <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name" required>
                                </div>
                            </div>

                            <!-- Company Logo (optional) -->
                            <div class="col-md-4">
                                <label class="form-label" for="company_logo">Company Logo (Optional)</label>
                                <input type="file" class="form-control" id="company_logo" name="company_logo">
                            </div>

                            <!-- Company Address -->
                            <div class="col-md-4">
                                <label class="form-label" for="company_location">Company Address</label>
                                <div class="input-icon">
                                    <i class="bi bi-geo-alt"></i>
                                    <input type="text" class="form-control" id="company_location" name="company_location" placeholder="Company Address" required>
                                </div>
                            </div>

                            <!-- Contact Email -->
                            <div class="col-md-4">
                                <label class="form-label" for="contact_email">Contact Email</label>
                                <div class="input-icon">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" placeholder="Contact Email" required>
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-4">
                                <label class="form-label" for="contact_number">Contact Number</label>
                                <div class="input-icon">
                                    <i class="bi bi-telephone"></i>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Landline or Mobile Number" required>
                                </div>
                            </div>

                            <!-- Industry Type -->
                            <div class="col-md-4">
                                <label class="form-label" for="industry_type">Industry Type</label>
                                <div class="input-icon">
                                    <i class="bi bi-layers"></i>
                                    <select class="form-control" id="industry_type" name="industry_type" required>
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

                            <!-- Nature of Business -->
                            <div class="col-md-4">
                                <label class="form-label" for="nature_of_business">Nature of Business</label>
                                <div class="input-icon">
                                    <i class="bi bi-briefcase"></i>
                                    <input type="text" class="form-control" id="nature_of_business" name="nature_of_business" placeholder="e.g., IT Services, Consulting" required>
                                </div>
                            </div>

                            <!-- TIN -->
                            <div class="col-md-4">
                                <label class="form-label" for="tin">TIN</label>
                                <div class="input-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <input type="text" class="form-control" id="tin" name="tin" placeholder="e.g., 123-456-789" required>
                                </div>
                            </div>

                            <!-- Date Established -->
                            <div class="col-md-4">
                                <label class="form-label" for="date_established">Date Established</label>
                                <div class="input-icon">
                                    <i class="bi bi-calendar"></i>
                                    <input type="date" class="form-control" id="date_established" name="date_established" required>
                                </div>
                            </div>

                            <!-- Company Type -->
                            <div class="col-md-4">
                                <label class="form-label" for="company_type">Type of Company</label>
                                <div class="input-icon">
                                    <i class="bi bi-diagram-3"></i>
                                    <select class="form-control" id="company_type" name="company_type" required>
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="LLC">LLC</option>
                                        <option value="Corporation">Corporation</option>
                                        <option value="Partnership">Partnership</option>
                                        <option value="Sole Proprietorship">Sole Proprietorship</option>
                                        <option value="Non-profit">Non-profit</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Accreditation Status -->
                            <div class="col-md-4">
                                <label class="form-label" for="accreditation_status">Accreditation Status</label>
                                <div class="input-icon">
                                    <i class="bi bi-patch-check"></i>
                                    <select class="form-control" id="accreditation_status" name="accreditation_status" required>
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

                            <!-- Upload Document (Preserved) -->
                            <div class="col-md-4">
                                <label class="form-label" for="document">Upload Document</label>
                                <input type="file" class="form-control" id="document" name="document_file" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn-register mt-3">REGISTER</button>
                </div>
            </form>
        </div>

        <div class="text-center mt-1">
            <a href="employer_login.php">Already have an employer account? Login here</a><br>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>