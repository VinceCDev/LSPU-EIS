const { createApp } = Vue;

        createApp({
            data() {
                return {
                    mobileMenuOpen: false,
                    notifications: [],
                    notificationId: 0,
                    darkMode: false,
                    contactForm: {
                        name: '',
                        age: '',
                        email: '',
                        message: ''
                    }
                }
            },
            mounted() {
                // Check for saved dark mode preference or default to light mode
                const savedMode = localStorage.getItem('darkMode');
                if (savedMode !== null) {
                    this.darkMode = savedMode === 'true';
                } else {
                    this.darkMode = false; // Default to light mode
                }
                this.applyDarkMode();
            },
            methods: {
                scrollToSection(sectionId) {
                    const element = document.getElementById(sectionId);
                    if (element) {
                        element.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                },
                showNotification(message, type = 'success') {
                    const id = this.notificationId++;
                    this.notifications.push({ id, type, message });
                    setTimeout(() => {
                        this.removeNotification(id);
                    }, 5000);
                },
                
                removeNotification(id) {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                },
                
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode.toString());
                    this.applyDarkMode();
                },
                
                applyDarkMode() {
                    const html = document.documentElement;
                    if (this.darkMode) {
                        html.classList.add('dark');
} else {
                        html.classList.remove('dark');
                    }
                },
                
                async submitContactForm() {
                    try {
                        const response = await fetch('functions/send_contact_email.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(this.contactForm)
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.showNotification(data.message, 'success');
                            this.contactForm = {
                                name: '',
                                age: '',
                                email: '',
                                message: ''
                            };
                        } else {
                            this.showNotification(data.message || 'Failed to send message. Please try again.', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showNotification('Failed to send message. Please try again later.', 'error');
                    }
                }
            }
        }).mount('#app');