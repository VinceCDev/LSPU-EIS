
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox Messages</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
</head>
<body class="bg-gray-100 min-h-screen" id="app" v-cloak>
<div class="flex flex-col md:flex-row gap-6 p-6 max-w-7xl mx-auto">
    <!-- Sidebar -->
    <div class="w-full md:w-1/4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="border-b-2 border-yellow-400 pb-2 mb-4">
                <h2 class="text-lg font-semibold">Mailbox Folder</h2>
            </div>
            <button class="w-full flex items-center justify-center gap-2 bg-yellow-100 text-gray-700 font-semibold py-2 rounded mb-4 border border-yellow-200 cursor-pointer hover:bg-yellow-200 transition" @click="showCompose = true">
                <i class="fas fa-edit"></i> Compose
            </button>
            <nav class="flex flex-col gap-2">
                <a href="#" @click.prevent="activeFolder = 'inbox'; showCompose = false" :class="['flex items-center justify-between px-3 py-2 rounded font-medium', activeFolder === 'inbox' ? 'bg-yellow-50 text-gray-700 border-l-4 border-yellow-400' : 'text-gray-600 hover:bg-gray-50']">
                    <span><i class="fas fa-envelope mr-2"></i>Inbox</span>
                    <span class="bg-yellow-400 text-white text-xs font-bold px-2 py-0.5 rounded">0</span>
                </a>
                <a href="#" @click.prevent="activeFolder = 'sent'; showCompose = false" :class="['flex items-center justify-between px-3 py-2 rounded font-medium', activeFolder === 'sent' ? 'bg-yellow-50 text-gray-700 border-l-4 border-yellow-400' : 'text-gray-600 hover:bg-gray-50']">
                    <span><i class="fas fa-paper-plane mr-2"></i>Sent</span>
                    <span class="bg-yellow-400 text-white text-xs font-bold px-2 py-0.5 rounded">0</span>
                </a>
                <a href="#" @click.prevent="activeFolder = 'important'; showCompose = false" :class="['flex items-center px-3 py-2 rounded font-medium', activeFolder === 'important' ? 'bg-yellow-50 text-gray-700 border-l-4 border-yellow-400' : 'text-gray-600 hover:bg-gray-50']">
                    <i class="fas fa-bell mr-2"></i>Important
                </a>
                <a href="#" @click.prevent="activeFolder = 'trash'; showCompose = false" :class="['flex items-center px-3 py-2 rounded font-medium', activeFolder === 'trash' ? 'bg-yellow-50 text-gray-700 border-l-4 border-yellow-400' : 'text-gray-600 hover:bg-gray-50']">
                    <i class="fas fa-trash mr-2"></i>Trash
                </a>
            </nav>
        </div>
    </div>
    <!-- Main Panel -->
    <div class="w-full md:w-3/4">
        <!-- Compose Message Panel -->
        <div v-if="showCompose" class="bg-white rounded-lg shadow p-4">
            <div class="border-b-2 border-yellow-400 pb-2 mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center gap-2"><i class="fas fa-pen"></i> Write Message</h2>
            </div>
            <form @submit.prevent="sendMessage" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Role <span class="text-red-500">*</span></label>
                    <select v-model="compose.role" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select</option>
                        <option value="admin">Admin</option>
                        <option value="employer">Employer</option>
                        <option value="alumni">Alumni</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Receiver <span class="text-red-500">*</span></label>
                    <select v-model="compose.receiver" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">Select</option>
                        <option value="user1">User 1</option>
                        <option value="user2">User 2</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Subject <span class="text-red-500">*</span></label>
                    <input v-model="compose.subject" required type="text" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Message <span class="text-red-500">*</span></label>
                    <div id="editor" class="bg-white border border-gray-300 rounded"></div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300" @click="showCompose = false">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded bg-yellow-400 text-white font-semibold hover:bg-yellow-500">Send</button>
                </div>
            </form>
        </div>
        <!-- Folder Panels -->
        <div v-else class="bg-white rounded-lg shadow p-4">
            <div class="border-b-2 border-yellow-400 pb-2 mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <i :class="folderIcon"></i> {{ folderTitle }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <button class="bg-gray-100 hover:bg-gray-200 p-2 rounded" title="Copy"><i class="fas fa-copy"></i></button>
                <button class="bg-gray-100 hover:bg-gray-200 p-2 rounded" title="Export Excel"><i class="fas fa-file-excel"></i></button>
                <button class="bg-gray-100 hover:bg-gray-200 p-2 rounded" title="Export PDF"><i class="fas fa-file-pdf"></i></button>
                <button class="bg-gray-100 hover:bg-gray-200 p-2 rounded" title="Print"><i class="fas fa-print"></i></button>
                <input type="text" v-model="search" placeholder="Search..." class="ml-auto px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <div class="overflow-x-auto">
                <!-- Inbox Table -->
                <table v-if="activeFolder === 'inbox'" class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="px-4 py-2"><input type="checkbox" v-model="selectAll"></th>
                            <th class="px-4 py-2 text-left">Sender</th>
                            <th class="px-4 py-2 text-left">Subjects</th>
                            <th class="px-4 py-2 text-left">Message</th>
                            <th class="px-4 py-2 text-left">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(msg, idx) in paginatedMessages" :key="msg.id" class="border-b border-gray-200">
                            <td class="px-4 py-2"><input type="checkbox" v-model="selected" :value="msg.id"></td>
                            <td class="px-4 py-2"><i class="fas fa-bell text-yellow-500 mr-1"></i> {{ msg.sender }}</td>
                            <td class="px-4 py-2">{{ msg.subject }}</td>
                            <td class="px-4 py-2">{{ msg.message }}</td>
                            <td class="px-4 py-2">{{ msg.time }}</td>
                        </tr>
                        <tr v-if="paginatedMessages.length === 0">
                            <td colspan="5" class="text-center py-8 text-gray-400">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Sent Table -->
                <table v-if="activeFolder === 'sent'" class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="px-4 py-2"><input type="checkbox" v-model="selectAll"></th>
                            <th class="px-4 py-2 text-left">Receiver</th>
                            <th class="px-4 py-2 text-left">Subjects</th>
                            <th class="px-4 py-2 text-left">Message</th>
                            <th class="px-4 py-2 text-left">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="paginatedMessages.length === 0">
                            <td colspan="5" class="text-center py-8 text-gray-400">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Important Table -->
                <table v-if="activeFolder === 'important'" class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="px-2 py-2">#</th>
                            <th class="px-2 py-2">Type</th>
                            <th class="px-2 py-2">Sender / Receiver</th>
                            <th class="px-2 py-2">Subjects</th>
                            <th class="px-2 py-2">Message</th>
                            <th class="px-2 py-2">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(msg, idx) in importantMessages" :key="msg.id" class="border-b border-gray-200">
                            <td class="px-2 py-2">{{ idx + 1 }}</td>
                            <td class="px-2 py-2"><i class="fas fa-share"></i></td>
                            <td class="px-2 py-2">{{ msg.sender }}</td>
                            <td class="px-2 py-2">{{ msg.subject }}</td>
                            <td class="px-2 py-2">{{ msg.message }}</td>
                            <td class="px-2 py-2">{{ msg.time }}</td>
                        </tr>
                        <tr v-if="importantMessages.length === 0">
                            <td colspan="6" class="text-center py-8 text-gray-400">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Trash Table -->
                <table v-if="activeFolder === 'trash'" class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="px-4 py-2"><input type="checkbox" v-model="selectAll"></th>
                            <th class="px-4 py-2 text-left">Receiver</th>
                            <th class="px-4 py-2 text-left">Subjects</th>
                            <th class="px-4 py-2 text-left">Message</th>
                            <th class="px-4 py-2 text-left">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="paginatedMessages.length === 0">
                            <td colspan="5" class="text-center py-8 text-gray-400">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="flex justify-end mt-4">
                <button class="px-3 py-1 rounded-l border border-gray-300 bg-white hover:bg-yellow-100" :disabled="currentPage === 1" @click="prevPage"><i class="fas fa-chevron-left"></i></button>
                <span class="px-4 py-1 bg-yellow-400 text-white font-bold">{{ currentPage }}</span>
                <button class="px-3 py-1 rounded-r border border-gray-300 bg-white hover:bg-yellow-100" :disabled="currentPage === totalPages" @click="nextPage"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>
<!-- Quill.js for rich text editor -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
const { createApp } = Vue;
createApp({
    data() {
        return {
            showCompose: false,
            activeFolder: 'inbox',
            compose: {
                role: '',
                receiver: '',
                subject: '',
                message: ''
            },
            search: '',
            messages: [
                { id: 1, sender: 'System', subject: 'Enrollment Approved!', message: 'Congratulations!Your application for enrollment has been ...', time: '01/22/2025' },
                { id: 2, sender: 'System', subject: 'Enrollment Approved!', message: 'Congratulations!Your application for enrollment has been ...', time: '08/13/2024' },
                { id: 3, sender: 'System', subject: 'Enrollment Approved!', message: 'Congratulations!Your application for enrollment has been ...', time: '01/29/2024' },
                { id: 4, sender: 'System', subject: 'Enrollment Approved!', message: 'Congratulations!Your application for enrollment has been ...', time: '08/22/2023' },
                { id: 5, sender: 'System', subject: 'Enrollment Approved!', message: 'Congratulations!Your application for enrollment has been ...', time: '02/02/2023' },
            ],
            importantMessages: [
                { id: 1, sender: 'System', subject: 'Enrollment Approved!', message: 'Congratulations!Your application for enrollment has been approved.', time: '01/22/2025' },
                { id: 2, sender: 'System', subject: 'Enrollment Approved!', message: 'Congratulations!Your application for enrollment has been approved.', time: '08/13/2024' },
                { id: 3, sender: 'System', subject: 'Enrollment Approved!', message: 'Congratulations!Your application for enrollment has been approved.', time: '08/22/2023' },
                { id: 4, sender: 'System', subject: 'Enrollment Approved!', message: 'Congratulations!Your application for enrollment has been approved.', time: '02/02/2023' },
            ],
            selected: [],
            selectAll: false,
            currentPage: 1,
            pageSize: 5,
            quill: null
        };
    },
    computed: {
        folderTitle() {
            switch (this.activeFolder) {
                case 'inbox': return 'Inbox';
                case 'sent': return 'Sent';
                case 'important': return 'Important';
                case 'trash': return 'Trash';
                default: return '';
            }
        },
        folderIcon() {
            switch (this.activeFolder) {
                case 'inbox': return 'fas fa-inbox';
                case 'sent': return 'fas fa-paper-plane';
                case 'important': return 'fas fa-bell';
                case 'trash': return 'fas fa-trash';
                default: return '';
            }
        },
        filteredMessages() {
            if (this.activeFolder === 'important') return this.importantMessages;
            if (!this.search) return this.messages;
            const s = this.search.toLowerCase();
            return this.messages.filter(m =>
                m.sender.toLowerCase().includes(s) ||
                m.subject.toLowerCase().includes(s) ||
                m.message.toLowerCase().includes(s) ||
                m.time.toLowerCase().includes(s)
            );
        },
        paginatedMessages() {
            const start = (this.currentPage - 1) * this.pageSize;
            return this.filteredMessages.slice(start, start + this.pageSize);
        },
        totalPages() {
            return Math.ceil(this.filteredMessages.length / this.pageSize) || 1;
        }
    },
    watch: {
        selectAll(val) {
            if (val) {
                this.selected = this.paginatedMessages.map(m => m.id);
            } else {
                this.selected = [];
            }
        },
        paginatedMessages() {
            // Uncheck selectAll if not all are selected
            this.selectAll = this.paginatedMessages.length > 0 && this.selected.length === this.paginatedMessages.length;
        },
        showCompose(val) {
            if (val) {
                this.$nextTick(() => {
                    if (!this.quill) {
                        this.quill = new Quill('#editor', {
                            theme: 'snow',
                            placeholder: 'Write your message...'
                        });
                    }
                });
            }
        }
    },
    methods: {
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        sendMessage() {
            this.compose.message = this.quill.root.innerHTML;
            // Here you would send the message to the backend
            alert('Message sent!\n' + JSON.stringify(this.compose, null, 2));
            this.showCompose = false;
            // Reset form
            this.compose = { role: '', receiver: '', subject: '', message: '' };
            this.quill.setContents([]);
        }
    }
}).mount('#app');
</script>
</body>
</html> 