window.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('reset-app')) return;
    const { createApp, ref } = Vue;
    createApp({
        setup() {
            const password = ref('');
            const password2 = ref('');
            const message = ref('');
            const messageType = ref('');
            const isLoading = ref(false);
            const submitReset = async (e) => {
                e.preventDefault();
                message.value = '';
                messageType.value = '';
                isLoading.value = true;
                const formData = new FormData(e.target);
                try {
                    const res = await fetch('functions/reset_password.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'include'
                    });
                    const data = await res.json();
                    if (data.success) {
                        message.value = data.message || 'Password successfully changed.';
                        messageType.value = 'success';
                        setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                    } else {
                        message.value = data.message || 'Error resetting password.';
                        messageType.value = 'error';
                    }
                } catch (err) {
                    message.value = 'Error sending request. Please try again.';
                    messageType.value = 'error';
                }
                isLoading.value = false;
            };
            return { password, password2, message, messageType, isLoading, submitReset };
        }
    }).mount('#reset-app');
});