<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="icon" type="image/png" href="images/logo.png">
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
            <form action="functions/register.php" method="POST" enctype="multipart/form-data">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="row">
                    <!-- Left: Account Information -->
                    <div class="col-md-4 info">
                        <h5 class="fw-bold">Account Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-icon">
                                <i class="bi bi-envelope"></i>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-icon">
                                <i class="bi bi-lock"></i>
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" required
                                    minlength="8"
                                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                    title="Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&)">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <div class="input-icon">
                                <i class="bi bi-shield-lock"></i>
                                <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required minlength="8"
                                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                    title="Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&)">
                            </div>
                        </div>
                    </div>

                    <!-- Right: Personal Information -->
                    <div class="col-md-8">
                        <h5 class="fw-bold">Personal Information</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">First Name</label>
                                <div class="input-icon">
                                    <i class="bi bi-person"></i>
                                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle Name</label>
                                <div class="input-icon">
                                    <i class="bi bi-person"></i>
                                    <input type="text" name="middle_name" class="form-control" placeholder="Middle Name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name</label>
                                <div class="input-icon">
                                    <i class="bi bi-person"></i>
                                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Birth Date</label>
                                <div class="input-icon">
                                    <i class="bi bi-calendar"></i>
                                    <input type="date" name="birthdate" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contact Number</label>
                                <div class="input-icon">
                                    <i class="bi bi-telephone"></i>
                                    <input type="text" name="contact" class="form-control" placeholder="Contact Number" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                                <div class="input-icon">
                                    <i class="bi bi-telephone"></i>
                                    <select class="form-select form-control" name="gender" aria-placeholder="Gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Civil Status</label>
                                <div class="input-icon">
                                    <i class="bi bi-telephone"></i>
                                    <select class="form-select form-control" name="civil_status" aria-placeholder="Civil Status" required>
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <div class="input-icon">
                                    <i class="bi bi-geo-alt"></i>
                                    <input type="text" name="city" class="form-control" placeholder="City" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Province</label>
                                <div class="input-icon">
                                    <i class="bi bi-geo"></i>
                                    <input type="text" name="province" class="form-control" placeholder="Province" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Year Graduated</label>
                                <div class="input-icon">
                                    <i class="bi bi-calendar"></i>
                                    <input type="number" name="year_graduated" class="form-control" placeholder="Year Graduated" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Campus Graduated</label>
                                <div class="input-icon">
                                    <i class="bi bi-building"></i>
                                    <select class="form-select form-control" name="campus" aria-placeholder="Campus" required>
                                        <option value="">Select Campus</option>
                                        <option value="LSPU - San Pablo">LSPU - San Pablo</option>
                                        <option value="LSPU - Los Baños">LSPU - Los Baños</option>
                                        <option value="LSPU - Siniloan">LSPU - Siniloan</option>
                                        <option value="LSPU - Sta. Cruz">LSPU - Sta. Cruz</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Course</label>
                                <div class="input-icon">
                                    <i class="bi bi-building"></i>
                                    <select class="form-select form-control" name="course" aria-placeholder="Course" required>
                                        <option value="">Select Course</option>
                                        <option value="BS Information Technology">BS Information Technology</option>
                                        <option value="BS Computer Science">BS Computer Science</option>
                                        <option value="BS Electrical Engineering">BS Electrical Engineering</option>
                                        <option value="BS Electronics Engineering">BS Electronics Engineering</option>
                                        <option value="BS Industrial Technology">BS Industrial Technology</option>
                                        <option value="BS Mechanical Engineering">BS Mechanical Engineering</option>
                                        <option value="BS in Business Administration">BS in Business Administration</option>
                                        <option value="BS in Hospitality Management">BS in Hospitality Management</option>
                                        <option value="BS in Tourism Management">BS in Tourism Management</option>
                                        <option value="BS in Psychology">BS in Psychology</option>
                                        <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
                                        <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
                                        <option value="BS in Criminology">BS in Criminology</option>
                                        <option value="BS in Nursing">BS in Nursing</option>
                                        <option value="Bachelor of Science in Agriculture">Bachelor of Science in Agriculture</option>
                                        <option value="Bachelor of Science in Fisheries">Bachelor of Science in Fisheries</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/jpeg, image/png, image/gif">
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
            <a href="login.php">Already have an account? Login here</a><br>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>