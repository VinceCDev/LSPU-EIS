<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard - Messages</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Vue.js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="css/admin_message.css">
</head>

<body>
    <div id="app">
        <!-- Sidebar -->
        <div class="sidebar" :class="{ 'active': sidebarActive }">
            <div class="sidebar-brand">
                <img src="https://via.placeholder.com/150" alt="Logo" class="sidebar-logo">
                <span class="sidebar-brand-name">LSPU Employer</span>
            </div>
            <div class="sidebar-menu">
                <a href="admin_dashboard" class="sidebar-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-briefcase"></i>
                    <span>Job Postings</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-users"></i>
                    <span>Applicants</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-building"></i>
                    <span>Companies</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-user-graduate"></i>
                    <span>Alumni</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
                <a href="#" class="sidebar-item active">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
                <div class="mt-auto px-3 py-4">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/150" alt="Profile" class="profile-img">
                        <div class="ms-2">
                            <div class="text-white small">Admin User</div>
                            <div class="text-muted small">admin@example.com</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header>
            <div class="container-fluid">
                <div class="header-content">
                    <button class="mobile-menu-btn" @click="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="profile-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="profile-name d-none d-md-inline">Admin User</span>
                        <img src="https://via.placeholder.com/150" alt="Profile" class="profile-img">
                        <i class="fas fa-chevron-down small ms-1 d-none d-md-inline"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid">
                <!-- Email Container -->
                <div class="email-container">
                    <!-- Email Sidebar -->
                    <div class="email-sidebar">
                        <div class="email-sidebar-header">
                            <h5>Mailbox</h5>
                        </div>
                        <button class="btn btn-primary compose-btn" @click="showComposeModal">
                            <i class="fas fa-plus me-2"></i> Compose
                        </button>
                        <ul class="email-folders">
                            <li class="email-folder" :class="{ 'active': currentFolder === 'inbox' }" @click="changeFolder('inbox')">
                                <i class="fas fa-inbox"></i>
                                <span>Inbox</span>
                                <span class="email-folder-count">{{ unreadCount }}</span>
                            </li>
                            <li class="email-folder" :class="{ 'active': currentFolder === 'sent' }" @click="changeFolder('sent')">
                                <i class="fas fa-paper-plane"></i>
                                <span>Sent</span>
                            </li>
                            <li class="email-folder" :class="{ 'active': currentFolder === 'drafts' }" @click="changeFolder('drafts')">
                                <i class="fas fa-file"></i>
                                <span>Drafts</span>
                            </li>
                            <li class="email-folder" :class="{ 'active': currentFolder === 'trash' }" @click="changeFolder('trash')">
                                <i class="fas fa-trash"></i>
                                <span>Trash</span>
                            </li>
                            <li class="email-folder" :class="{ 'active': currentFolder === 'starred' }" @click="changeFolder('starred')">
                                <i class="fas fa-star"></i>
                                <span>Starred</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Email List -->
                    <div class="email-list">
                        <div class="email-list-header">
                            <div class="email-list-search">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search messages..." v-model="searchQuery" @input="filterMessages">
                            </div>
                        </div>
                        <div v-if="filteredMessages.length > 0">
                            <div class="email-item"
                                v-for="message in filteredMessages"
                                :key="message.id"
                                :class="{ 
                                     'active': selectedMessage?.id === message.id,
                                     'unread': !message.read && currentFolder === 'inbox'
                                 }"
                                @click="selectMessage(message)">
                                <div class="email-item-header">
                                    <div class="email-item-sender">
                                        {{ currentFolder === 'sent' ? message.to : message.from }}
                                    </div>
                                    <div class="email-item-time">
                                        {{ formatTime(message.date) }}
                                    </div>
                                </div>
                                <div class="email-item-subject">
                                    {{ message.subject }}
                                </div>
                                <div class="email-item-preview">
                                    {{ message.preview }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center p-4">
                            <i class="fas fa-envelope-open-text fa-2x text-muted mb-3"></i>
                            <p>No messages found</p>
                        </div>
                    </div>

                    <!-- Email Content -->
                    <div class="email-content" v-if="selectedMessage">
                        <div class="email-content-header">
                            <div class="email-content-subject">
                                {{ selectedMessage.subject }}
                            </div>
                            <div class="email-content-meta">
                                <div class="email-sender-avatar">
                                    {{ getInitials(currentFolder === 'sent' ? selectedMessage.to : selectedMessage.from) }}
                                </div>
                                <div class="email-sender-info">
                                    <div class="email-sender-name">
                                        {{ currentFolder === 'sent' ? selectedMessage.to : selectedMessage.from }}
                                    </div>
                                    <div class="email-sender-email">
                                        {{ currentFolder === 'sent' ? selectedMessage.toEmail : selectedMessage.fromEmail }}
                                    </div>
                                </div>
                                <div class="email-date">
                                    {{ formatDate(selectedMessage.date) }}
                                </div>
                            </div>
                        </div>
                        <div class="email-content-body">
                            <p>{{ selectedMessage.body }}</p>
                        </div>
                        <div class="email-content-actions">
                            <button class="btn btn-outline-secondary" @click="replyMessage">
                                <i class="fas fa-reply me-2"></i> Reply
                            </button>
                            <button class="btn btn-outline-secondary" @click="forwardMessage">
                                <i class="fas fa-share me-2"></i> Forward
                            </button>
                            <button class="btn btn-outline-danger ms-auto" @click="deleteMessage">
                                <i class="fas fa-trash me-2"></i> Delete
                            </button>
                        </div>
                    </div>
                    <div v-else class="d-flex align-items-center justify-content-center email-content">
                        <div class="text-center">
                            <i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
                            <h5>Select a message to read</h5>
                            <p class="text-muted">No message selected</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Compose Modal -->
        <div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content compose-modal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="composeModalLabel">New Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="compose-form" @submit.prevent="sendMessage">
                            <div class="compose-recipients">
                                <input type="text" class="form-control" placeholder="To" v-model="composeData.to" required>
                            </div>
                            <div class="compose-subject">
                                <input type="text" class="form-control" placeholder="Subject" v-model="composeData.subject" required>
                            </div>
                            <div class="compose-editor">
                                <textarea class="form-control h-100" placeholder="Write your message here..." v-model="composeData.body" required></textarea>
                            </div>
                            <div class="compose-footer">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary">
                                        <i class="fas fa-image"></i>
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">
                                        Discard
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        Send
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const {
            createApp,
            ref,
            computed,
            onMounted
        } = Vue;

        createApp({
            setup() {
                // Data properties
                const sidebarActive = ref(false);
                const currentFolder = ref('inbox');
                const searchQuery = ref('');
                const selectedMessage = ref(null);
                const composeData = ref({
                    to: '',
                    subject: '',
                    body: ''
                });

                // Sample messages data
                const messages = ref({
                    inbox: [{
                            id: 1,
                            from: 'John Smith',
                            fromEmail: 'john.smith@example.com',
                            subject: 'Job Application Update',
                            preview: 'Thank you for applying to our company. We would like to invite you for an interview...',
                            body: 'Dear Applicant,\n\nThank you for applying to our company. We would like to invite you for an interview on Friday, June 10th at 2:00 PM.\n\nPlease bring your resume and any relevant documents.\n\nBest regards,\nJohn Smith\nHR Manager',
                            date: '2023-06-05T10:30:00',
                            read: false,
                            starred: false
                        },
                        {
                            id: 2,
                            from: 'Sarah Johnson',
                            fromEmail: 'sarah.j@company.com',
                            subject: 'Follow-up on our meeting',
                            preview: 'It was great meeting with you yesterday. As discussed, I\'m attaching the documents...',
                            body: 'Hello,\n\nIt was great meeting with you yesterday. As discussed, I\'m attaching the documents we talked about.\n\nPlease review them and let me know if you have any questions.\n\nRegards,\nSarah Johnson',
                            date: '2023-06-04T14:15:00',
                            read: true,
                            starred: true
                        },
                        {
                            id: 3,
                            from: 'Recruitment Team',
                            fromEmail: 'recruitment@lspu.edu',
                            subject: 'New Job Postings',
                            preview: 'We have new job postings that match your profile. Check them out on our portal...',
                            body: 'Dear Employer,\n\nWe have new job postings that match your profile. Check them out on our portal and consider applying.\n\nBest regards,\nRecruitment Team',
                            date: '2023-06-03T09:00:00',
                            read: true,
                            starred: false
                        }
                    ],
                    sent: [{
                            id: 4,
                            to: 'applicant@example.com',
                            subject: 'Interview Confirmation',
                            preview: 'This email confirms your interview scheduled for June 10th at 2:00 PM...',
                            body: 'Dear Applicant,\n\nThis email confirms your interview scheduled for June 10th at 2:00 PM at our main office.\n\nLooking forward to meeting you.\n\nBest regards,\nHR Team',
                            date: '2023-06-02T16:45:00',
                            read: true
                        },
                        {
                            id: 5,
                            to: 'partner@company.com',
                            subject: 'Partnership Proposal',
                            preview: 'I\'m reaching out to discuss a potential partnership between our organizations...',
                            body: 'Hello,\n\nI\'m reaching out to discuss a potential partnership between our organizations. I believe there are mutual benefits we can explore.\n\nLet me know when you might be available for a call.\n\nBest regards,\nYour Name',
                            date: '2023-05-30T11:20:00',
                            read: true
                        }
                    ],
                    drafts: [{
                        id: 6,
                        to: 'team@department.com',
                        subject: 'Project Update',
                        preview: 'Here\'s the latest update on our current project. I need to add more details before sending...',
                        body: 'Team,\n\nHere\'s the latest update on our current project. I need to add more details before sending this out.\n\n- Item 1\n- Item 2\n- Item 3',
                        date: '2023-05-28T15:10:00',
                        read: true
                    }],
                    trash: [],
                    starred: []
                });

                // Computed properties
                const filteredMessages = computed(() => {
                    let folderMessages = messages.value[currentFolder.value];

                    // Filter by search query
                    if (searchQuery.value) {
                        const query = searchQuery.value.toLowerCase();
                        folderMessages = folderMessages.filter(message =>
                            message.subject.toLowerCase().includes(query) ||
                            (currentFolder.value === 'sent' ? message.to.toLowerCase().includes(query) : message.from.toLowerCase().includes(query)) ||
                            message.preview.toLowerCase().includes(query)
                        );
                    }

                    // For starred folder, get all starred messages
                    if (currentFolder.value === 'starred') {
                        folderMessages = [
                            ...messages.value.inbox.filter(m => m.starred),
                            ...messages.value.sent.filter(m => m.starred)
                        ];
                    }

                    // Sort by date (newest first)
                    return [...folderMessages].sort((a, b) => new Date(b.date) - new Date(a.date));
                });

                const unreadCount = computed(() => {
                    return messages.value.inbox.filter(m => !m.read).length;
                });

                // Methods
                const toggleSidebar = () => {
                    sidebarActive.value = !sidebarActive.value;
                };

                const changeFolder = (folder) => {
                    currentFolder.value = folder;
                    selectedMessage.value = null;
                    searchQuery.value = '';
                };

                const selectMessage = (message) => {
                    selectedMessage.value = message;
                    // Mark as read if in inbox
                    if (currentFolder.value === 'inbox' && !message.read) {
                        message.read = true;
                    }
                };

                const formatDate = (dateString) => {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                };

                const formatTime = (dateString) => {
                    const date = new Date(dateString);
                    const now = new Date();

                    // If today, show time
                    if (date.toDateString() === now.toDateString()) {
                        return date.toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }

                    // If yesterday, show "Yesterday"
                    const yesterday = new Date(now);
                    yesterday.setDate(yesterday.getDate() - 1);
                    if (date.toDateString() === yesterday.toDateString()) {
                        return 'Yesterday';
                    }

                    // If within last 7 days, show day name
                    const lastWeek = new Date(now);
                    lastWeek.setDate(lastWeek.getDate() - 7);
                    if (date > lastWeek) {
                        return date.toLocaleDateString([], {
                            weekday: 'short'
                        });
                    }

                    // Otherwise, show short date
                    return date.toLocaleDateString([], {
                        month: 'short',
                        day: 'numeric'
                    });
                };

                const getInitials = (name) => {
                    if (!name) return '';
                    const parts = name.split(' ');
                    return parts.map(part => part[0]).join('').toUpperCase();
                };

                const showComposeModal = () => {
                    composeData.value = {
                        to: '',
                        subject: '',
                        body: ''
                    };
                    const modal = new bootstrap.Modal(document.getElementById('composeModal'));
                    modal.show();
                };

                const sendMessage = () => {
                    const newMessage = {
                        id: messages.value.sent.length + 1,
                        to: composeData.value.to,
                        subject: composeData.value.subject,
                        preview: composeData.value.body.substring(0, 100) + '...',
                        body: composeData.value.body,
                        date: new Date().toISOString(),
                        read: true
                    };

                    messages.value.sent.unshift(newMessage);

                    const modal = bootstrap.Modal.getInstance(document.getElementById('composeModal'));
                    modal.hide();

                    // Show success message
                    alert('Message sent successfully!');
                };

                const replyMessage = () => {
                    if (!selectedMessage.value) return;

                    composeData.value = {
                        to: currentFolder.value === 'sent' ? selectedMessage.value.to : selectedMessage.value.fromEmail,
                        subject: `Re: ${selectedMessage.value.subject}`,
                        body: `\n\n----- Original Message -----\nFrom: ${selectedMessage.value.from}\nDate: ${formatDate(selectedMessage.value.date)}\n\n${selectedMessage.value.body}`
                    };

                    const modal = new bootstrap.Modal(document.getElementById('composeModal'));
                    modal.show();
                };

                const forwardMessage = () => {
                    if (!selectedMessage.value) return;

                    composeData.value = {
                        to: '',
                        subject: `Fwd: ${selectedMessage.value.subject}`,
                        body: `\n\n----- Forwarded Message -----\nFrom: ${selectedMessage.value.from}\nDate: ${formatDate(selectedMessage.value.date)}\n\n${selectedMessage.value.body}`
                    };

                    const modal = new bootstrap.Modal(document.getElementById('composeModal'));
                    modal.show();
                };

                const deleteMessage = () => {
                    if (!selectedMessage.value) return;

                    if (confirm('Are you sure you want to delete this message?')) {
                        // Remove from current folder
                        const folderMessages = messages.value[currentFolder.value];
                        const index = folderMessages.findIndex(m => m.id === selectedMessage.value.id);
                        if (index !== -1) {
                            folderMessages.splice(index, 1);
                        }

                        // Add to trash
                        messages.value.trash.push(selectedMessage.value);

                        selectedMessage.value = null;
                    }
                };

                const filterMessages = () => {
                    // Just triggers the computed property update
                };

                // Initialize
                onMounted(() => {
                    // Any initialization code if needed
                });

                return {
                    sidebarActive,
                    currentFolder,
                    searchQuery,
                    selectedMessage,
                    composeData,
                    filteredMessages,
                    unreadCount,
                    toggleSidebar,
                    changeFolder,
                    selectMessage,
                    formatDate,
                    formatTime,
                    getInitials,
                    showComposeModal,
                    sendMessage,
                    replyMessage,
                    forwardMessage,
                    deleteMessage,
                    filterMessages
                };
            }
        }).mount('#app');
    </script>
</body>

</html>