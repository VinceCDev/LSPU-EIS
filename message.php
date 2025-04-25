<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger Style UI</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0084ff;
            --secondary-color: #f0f2f5;
            --message-bg: #e4e6eb;
            --my-message-bg: #0084ff;
            --my-message-color: white;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            height: 100vh;
            overflow: hidden;
        }

        .messenger-container {
            height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 350px;
            background-color: white;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .sidebar-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-search {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }

        .search-input {
            background-color: #f0f2f5;
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            width: 100%;
        }

        .conversation-list {
            flex: 1;
            overflow-y: auto;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .conversation-item:hover {
            background-color: #f5f5f5;
        }

        .conversation-item.active {
            background-color: #e7f3ff;
        }

        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .conversation-info {
            flex: 1;
        }

        .conversation-name {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .conversation-preview {
            color: #65676b;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-time {
            font-size: 0.8rem;
            color: #65676b;
        }

        /* Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .chat-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            background-color: white;
            display: flex;
            align-items: center;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .chat-name {
            font-weight: 600;
            flex: 1;
        }

        .chat-actions i {
            margin-left: 15px;
            color: #65676b;
            cursor: pointer;
        }

        .messages-container {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #f0f2f5;
        }

        .message {
            display: flex;
            margin-bottom: 15px;
        }

        .message-content {
            max-width: 60%;
            padding: 10px 15px;
            border-radius: 18px;
            background-color: var(--message-bg);
        }

        .message.my-message {
            justify-content: flex-end;
        }

        .message.my-message .message-content {
            background-color: var(--my-message-bg);
            color: var(--my-message-color);
        }

        .message-time {
            font-size: 0.7rem;
            color: #65676b;
            margin-top: 5px;
            text-align: right;
        }

        .message.my-message .message-time {
            color: rgba(255, 255, 255, 0.7);
        }

        .message-input-container {
            padding: 15px;
            background-color: white;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .message-input {
            flex: 1;
            border: none;
            border-radius: 20px;
            padding: 10px 15px;
            background-color: #f0f2f5;
            margin: 0 10px;
        }

        .message-input:focus {
            outline: none;
        }

        .send-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                display: none;
            }

            .sidebar.active {
                display: flex;
            }

            .chat-area {
                display: none;
            }

            .chat-area.active {
                display: flex;
            }

            .mobile-back-button {
                display: block !important;
                margin-right: 10px;
            }
        }

        .mobile-back-button {
            display: none;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="messenger-container">
            <!-- Sidebar -->
            <div class="sidebar" :class="{ 'active': !activeChat }">
                <div class="sidebar-header">
                    <h5 class="mb-0">Messages</h5>
                    <div>
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
                <div class="sidebar-search">
                    <input type="text" class="search-input" placeholder="Search messages" v-model="searchQuery">
                </div>
                <div class="conversation-list">
                    <div
                        v-for="(conversation, index) in filteredConversations"
                        :key="index"
                        class="conversation-item"
                        :class="{ 'active': activeConversation === index }"
                        @click="selectConversation(index)">
                        <img :src="conversation.avatar" class="conversation-avatar">
                        <div class="conversation-info">
                            <div class="conversation-name">{{ conversation.name }}</div>
                            <div class="conversation-preview">{{ conversation.messages[conversation.messages.length-1].text }}</div>
                        </div>
                        <div class="conversation-time">{{ formatTime(conversation.messages[conversation.messages.length-1].time) }}</div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="chat-area" :class="{ 'active': activeChat }">
                <div class="chat-header">
                    <i class="fas fa-arrow-left mobile-back-button" @click="activeChat = false"></i>
                    <img :src="activeConversationData.avatar" class="chat-avatar">
                    <div class="chat-name">{{ activeConversationData.name }}</div>
                    <div class="chat-actions">
                        <i class="fas fa-phone"></i>
                        <i class="fas fa-video"></i>
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
                <div class="messages-container" ref="messagesContainer">
                    <div
                        v-for="(message, index) in activeConversationData.messages"
                        :key="index"
                        class="message"
                        :class="{ 'my-message': message.sender === 'me' }">
                        <div class="message-content">
                            {{ message.text }}
                            <div class="message-time">{{ formatTime(message.time) }}</div>
                        </div>
                    </div>
                </div>
                <div class="message-input-container">
                    <i class="fas fa-plus-circle" style="font-size: 1.5rem; color: var(--primary-color); cursor: pointer;"></i>
                    <input
                        type="text"
                        class="message-input"
                        placeholder="Type a message..."
                        v-model="newMessage"
                        @keyup.enter="sendMessage">
                    <button class="send-button" @click="sendMessage">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Vue.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.2.47/vue.global.min.js"></script>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    searchQuery: '',
                    activeConversation: 0,
                    activeChat: false,
                    newMessage: '',
                    conversations: [{
                            name: 'Sarah Johnson',
                            avatar: 'https://randomuser.me/api/portraits/women/44.jpg',
                            messages: [{
                                    text: 'Hey there! How are you doing?',
                                    sender: 'them',
                                    time: '2023-06-15T09:30:00'
                                },
                                {
                                    text: "I'm good, thanks! How about you?",
                                    sender: 'me',
                                    time: '2023-06-15T09:32:00'
                                },
                                {
                                    text: 'Do you want to meet up later this week?',
                                    sender: 'them',
                                    time: '2023-06-15T09:33:00'
                                },
                                {
                                    text: 'Sure, how about Friday afternoon?',
                                    sender: 'me',
                                    time: '2023-06-15T09:35:00'
                                }
                            ]
                        },
                        {
                            name: 'Mike Peterson',
                            avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
                            messages: [{
                                    text: 'Did you see the game last night?',
                                    sender: 'them',
                                    time: '2023-06-14T20:15:00'
                                },
                                {
                                    text: 'Yes! That was an amazing finish!',
                                    sender: 'me',
                                    time: '2023-06-14T20:20:00'
                                },
                                {
                                    text: "I couldn't believe it when they scored in the last minute",
                                    sender: 'them',
                                    time: '2023-06-14T20:22:00'
                                }
                            ]
                        },
                        {
                            name: 'Emily Wilson',
                            avatar: 'https://randomuser.me/api/portraits/women/68.jpg',
                            messages: [{
                                    text: 'Can you send me those files we discussed?',
                                    sender: 'them',
                                    time: '2023-06-14T14:10:00'
                                },
                                {
                                    text: 'Sure, I just emailed them to you',
                                    sender: 'me',
                                    time: '2023-06-14T14:15:00'
                                },
                                {
                                    text: 'Got them, thanks!',
                                    sender: 'them',
                                    time: '2023-06-14T14:18:00'
                                }
                            ]
                        },
                        {
                            name: 'David Kim',
                            avatar: 'https://randomuser.me/api/portraits/men/75.jpg',
                            messages: [{
                                    text: 'Are we still on for lunch tomorrow?',
                                    sender: 'them',
                                    time: '2023-06-13T11:45:00'
                                },
                                {
                                    text: 'Yes, 12:30 at the usual place',
                                    sender: 'me',
                                    time: '2023-06-13T11:50:00'
                                }
                            ]
                        },
                        {
                            name: 'Lisa Wong',
                            avatar: 'https://randomuser.me/api/portraits/women/23.jpg',
                            messages: [{
                                    text: 'The project deadline has been moved to next Friday',
                                    sender: 'them',
                                    time: '2023-06-12T16:30:00'
                                },
                                {
                                    text: 'That gives us some extra time to polish things',
                                    sender: 'me',
                                    time: '2023-06-12T16:35:00'
                                }
                            ]
                        }
                    ]
                }
            },
            computed: {
                filteredConversations() {
                    if (!this.searchQuery) return this.conversations;
                    return this.conversations.filter(conv =>
                        conv.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        conv.messages.some(msg =>
                            msg.text.toLowerCase().includes(this.searchQuery.toLowerCase())
                        )
                    );
                },
                activeConversationData() {
                    return this.conversations[this.activeConversation] || {};
                }
            },
            methods: {
                selectConversation(index) {
                    this.activeConversation = index;
                    this.activeChat = true;
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                },
                sendMessage() {
                    if (!this.newMessage.trim()) return;

                    const newMsg = {
                        text: this.newMessage,
                        sender: 'me',
                        time: new Date().toISOString()
                    };

                    this.conversations[this.activeConversation].messages.push(newMsg);
                    this.newMessage = '';

                    this.$nextTick(() => {
                        this.scrollToBottom();

                        // Simulate reply after 1-3 seconds
                        setTimeout(() => {
                            const replies = [
                                "Sounds good!",
                                "I'll get back to you on that",
                                "Thanks for letting me know",
                                "Can we talk about this later?",
                                "Interesting point!",
                                "I agree with you",
                                "Let me think about it"
                            ];
                            const randomReply = replies[Math.floor(Math.random() * replies.length)];

                            this.conversations[this.activeConversation].messages.push({
                                text: randomReply,
                                sender: 'them',
                                time: new Date().toISOString()
                            });

                            this.$nextTick(() => {
                                this.scrollToBottom();
                            });
                        }, 1000 + Math.random() * 2000);
                    });
                },
                scrollToBottom() {
                    const container = this.$refs.messagesContainer;
                    container.scrollTop = container.scrollHeight;
                },
                formatTime(dateString) {
                    const date = new Date(dateString);
                    const now = new Date();

                    // If same day, show time only
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

                    // If within the last week, show day name
                    const lastWeek = new Date(now);
                    lastWeek.setDate(lastWeek.getDate() - 7);
                    if (date > lastWeek) {
                        return date.toLocaleDateString([], {
                            weekday: 'short'
                        });
                    }

                    // Otherwise show date
                    return date.toLocaleDateString([], {
                        month: 'short',
                        day: 'numeric'
                    });
                }
            },
            mounted() {
                this.scrollToBottom();

                // Check screen size and adjust view
                const checkScreenSize = () => {
                    this.activeChat = window.innerWidth > 768;
                };

                checkScreenSize();
                window.addEventListener('resize', checkScreenSize);
            }
        }).mount('#app');
    </script>
</body>

</html>