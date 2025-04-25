<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
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
        <div class="d-flex align-items-center justify-content-center">
            <img src="images/alumni.png" alt="LSPU Logo" class="me-0" style="width: 90px; height: auto;">
            <div class="lspu-eis-container">
                <p class="lspu-eis m-0"><span class="bold-text">LSPU</span><span class="thin-text">EIS</span></p>
            </div>
        </div>

        <div class="p-3">
            <form action="functions/login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="input-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password"
                            class="form-control"
                            v-model="password"
                            name="password"
                            placeholder="Enter your new password"
                            required
                            @copy.prevent="handleCopy"
                            @paste.prevent="handlePaste"
                            @cut.prevent="handleCut">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Re-Type Password</label>
                    <div class="input-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password"
                            class="form-control"
                            v-model="password"
                            name="password"
                            placeholder="Re-type your password"
                            required
                            @copy.prevent="handleCopy"
                            @paste.prevent="handlePaste"
                            @cut.prevent="handleCut">
                    </div>
                </div>

                <button type="submit" class="btn-signin">RESET PASSWORD</button>
            </form>

            <div class="text-center mt-3">
                <a href=""> © All Rights Reserved | Laguna State Polytechnic University Employment and Information System</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>