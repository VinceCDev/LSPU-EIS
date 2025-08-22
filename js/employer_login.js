const { createApp, ref } = Vue;
createApp({
    setup() {
        const password = ref('');
        const email = ref('');
        const message = ref('');
        const messageType = ref('');
        const isLoading = ref(false);
        const csrfToken = ref(document.querySelector('input[name=csrf_token]')?.value || '');
        const handleCopy = () => alert("Copying password is not allowed!");
        const handlePaste = () => alert("Pasting password is not allowed!");
        const handleCut = () => alert("Cutting password is not allowed!");
        const submitLogin = async (e) => {
            e.preventDefault();
            isLoading.value = true;
            message.value = '';
            messageType.value = '';
            const formData = new FormData();
            formData.append('email', email.value);
            formData.append('password', password.value);
            formData.append('csrf_token', csrfToken.value);
            try {
                const res = await fetch('functions/employer_login.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                const text = await res.text();
                if (res.redirected) {
                    window.location.href = res.url;
                    return;
                }
                if (text.includes('Location:')) {
                    // PHP header redirect fallback
                    window.location.href = 'employer_dashboard.php';
                    return;
                }
                if (text.toLowerCase().includes('access denied') || text.toLowerCase().includes('invalid') || text.toLowerCase().includes('not found') || text.toLowerCase().includes('error')) {
                    message.value = text;
                    messageType.value = 'error';
                } else {
                    message.value = text;
                    messageType.value = 'success';
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
            csrfToken,
            handleCopy,
            handlePaste,
            handleCut,
            submitLogin
        };
    }
}).mount('#app');