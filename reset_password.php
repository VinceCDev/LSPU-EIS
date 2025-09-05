<?php
// PHP: Only check token/email and set variables for the form
$show_form = false;
$token = isset($_GET['token']) ? $_GET['token'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';
if ($token && $email) {
    require_once 'conn/db_conn.php';
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT user_id, reset_token, reset_token_expiry FROM user WHERE (email = ? OR secondary_email = ?) AND reset_token = ? LIMIT 1");
    $stmt->bind_param('sss', $email, $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    if ($user && $user['reset_token'] && strtotime($user['reset_token_expiry']) > time()) {
        $show_form = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | LSPU EIS</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=UnifrakturCook:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js" defer></script>
    <script>tailwind.config = {theme: {extend: {fontFamily: {'unifraktur': ['UnifrakturCook', 'serif'],'poppins': ['Poppins', 'sans-serif']},colors: {'lspu-blue': '#00A0E9','lspu-dark': '#1A1A1A','lspu-gold': '#FFD54F'},animation: {'slide-in': 'slideIn 0.5s ease-out',},keyframes: {slideIn: {'0%': { transform: 'translateY(10px)', opacity: '0' },'100%': { transform: 'translateY(0)', opacity: '1' },}}}}}</script>
</head>
<body class="bg-gray-50 font-poppins">
    <header class="bg-gradient-to-r from-lspu-blue to-lspu-dark text-white shadow-md"><div class="container mx-auto flex flex-col md:flex-row items-center justify-center gap-4 py-4 px-6 animate-slide-in"><img src="images/logo.png" alt="LSPU Logo" class="h-20 w-auto"><div class="text-center md:text-left"><h1 class="font-unifraktur text-2xl md:text-3xl leading-tight">Laguna State Polytechnic University</h1><p class="font-semibold text-sm md:text-base">INTEGRITY • PROFESSIONALISM • INNOVATION</p></div></div></header><div class="bg-lspu-gold py-2 shadow-sm"></div><div class="container mx-auto px-4 py-8 w-full max-w-[500px]"><div class="bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden animate-slide-in"><div class="flex items-center justify-center pt-5"><img src="images/alumni.png" alt="LSPU Logo" class="mr-0 w-[90px] h-auto"><div class="border-b-2 border-lspu-blue"><p class="text-[2.5rem] font-bold uppercase flex items-center m-0"><span class="font-black text-gray-800">LSPU</span><span class="font-light text-lspu-blue font-sans">EIS</span></p></div></div><div class="px-6 pb-6">
    <?php if ($show_form): ?>
        <div id="reset-app">
            <div v-if="message" :class="['my-4', messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700', 'border px-4 py-3 rounded']">{{ message }}</div>
            <form @submit.prevent="submitReset" class="space-y-4">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <div class="relative">
                        <i class="bi bi-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" name="password" v-model="password" placeholder="Enter your new password" required>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Re-Type Password</label>
                    <div class="relative">
                        <i class="bi bi-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-lspu-blue focus:border-lspu-blue transition" name="password2" v-model="password2" placeholder="Re-type your password" required>
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-2.5 bg-lspu-blue hover:bg-lspu-dark text-white font-semibold rounded-lg shadow-md transition duration-300" :disabled="isLoading">RESET PASSWORD</button>
            </form>
        </div>
    <?php else: ?>
        <div class="text-center text-red-600 font-semibold">Invalid or expired reset link.</div>
    <?php endif; ?>
</div><div class="text-center mt-4 pt-4 border-t border-gray-200"><p class="text-gray-600 text-xs">© All Rights Reserved | Laguna State Polytechnic University Employment and Information System</p></div></div></div></body>
<script src="js/reset_password.js"></script>
</body>
</html>