window.addEventListener('DOMContentLoaded', function() {
    const { createApp, ref } = Vue;
    createApp({
        setup() {
            const email = ref('');
            const message = ref('');
            const messageType = ref('');
            const isLoading = ref(false);
            const submitForgot = async (e) => {
                e.preventDefault();
                isLoading.value = true;
                message.value = '';
                messageType.value = '';
                const formData = new FormData(e.target);
                try {
                    const res = await fetch('functions/forgot_password.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'include'
                    });
                    // Try to get message from session or fallback
                    if (res.redirected) {
                        window.location.href = res.url;
                        return;
                    }
                    const text = await res.text();
                    if (text.includes('Password reset email sent')) {
                        message.value = 'Password reset email sent successfully.';
                        messageType.value = 'success';
                    } else if (text.includes('Email address not found')) {
                        message.value = 'Email address not found.';
                        messageType.value = 'error';
                    } else {
                        message.value = 'Request processed. Check your email.';
                        messageType.value = 'info';
                    }
                } catch (err) {
                    message.value = 'Error sending request. Please try again.';
                    messageType.value = 'error';
                }
                isLoading.value = false;
            };
            return { email, message, messageType, isLoading, submitForgot };
        }
    }).mount('body');
});