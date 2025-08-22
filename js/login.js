window.addEventListener('DOMContentLoaded', function() {
    const { createApp, ref } = Vue;

    createApp({
        setup() {
            const password = ref('');
            const email = ref('');
            const message = ref('');
            const messageType = ref('');
            const isLoading = ref(false);

            const handleCopy = () => alert("Copying password is not allowed!");
            const handlePaste = () => alert("Pasting password is not allowed!");
            const handleCut = () => alert("Cutting password is not allowed!");

            function getCsrfToken() {
                const input = document.querySelector('input[name="csrf_token"]');
                if (input) return input.value;
                const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
                return match ? decodeURIComponent(match[1]) : '';
            }

            const submitLogin = async (e) => {
                e.preventDefault();
                isLoading.value = true;
                message.value = '';
                messageType.value = '';
                const formData = new FormData(e.target);
                try {
                    const res = await fetch('functions/login.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'include',
                        headers: {
                            'X-XSRF-TOKEN': getCsrfToken()
                        }
                    });
                    const result = await res.json();
                    if (result.success && result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        message.value = result.message || 'Login failed.';
                        messageType.value = 'error';
                    }
                } catch (err) {
                    message.value = 'Login error. Please try again.';
                    messageType.value = 'error';
                }
                isLoading.value = false;
            };

            return {
                password,
                email,
                message,
                messageType,
                isLoading,
                handleCopy,
                handlePaste,
                handleCut,
                submitLogin
            };
        }
    }).mount('#app');
});