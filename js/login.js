const { createApp, ref } = Vue;

createApp({
    setup() {
        const password = ref('');

        const handleCopy = () => alert("Copying password is not allowed!");
        const handlePaste = () => alert("Pasting password is not allowed!");
        const handleCut = () => alert("Cutting password is not allowed!");

        return {
            password,
            handleCopy,
            handlePaste,
            handleCut
        };
    }
}).mount('#app');