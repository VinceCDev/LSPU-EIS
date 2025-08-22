const { createApp } = Vue;
createApp({
    data() {
        return {
            darkMode: false,
            showCompose: false,
            activeFolder: 'inbox',
            compose: {
                role: '',
                receiver: '',
                subject: '',
                message: ''
            },
            search: '',
            inboxMessages: [],
            sentMessages: [],
            importantMessages: [],
            trashMessages: [],
            selectedMessages: [],
            selectAll: false,
            quill: null,
            allUsers: [], // List of all users with email, name, and role
            inboxCount: 0,
            sentCount: 0,
            importantCount: 0,
            trashCount: 0,
            profileDropdownOpen: false,
            profile: {},
            profilePicData: {},
            showLogoutModal: false,
            sidebarOpen: false, // Changed to false for mobile
            notifications: [],
            selectedMessage: null
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
            let base = [];
            if (this.activeFolder === 'inbox') base = this.inboxMessages || [];
            else if (this.activeFolder === 'sent') base = this.sentMessages || [];
            else if (this.activeFolder === 'important') base = this.importantMessages || [];
            else if (this.activeFolder === 'trash') base = this.trashMessages || [];
            else base = [];

            if (!this.search) return base.map(m => ({
                ...m,
                sender: this.activeFolder === 'inbox' ? m.sender_email : m.receiver_email,
                time: m.created_at || m.time
            }));
            const s = this.search.toLowerCase();
            return base.filter(m =>
                ((m.sender_email && m.sender_email.toLowerCase().includes(s)) ||
                (m.receiver_email && m.receiver_email.toLowerCase().includes(s)) ||
                (m.subject && m.subject.toLowerCase().includes(s)) ||
                (m.message && m.message.toLowerCase().includes(s)) ||
                (m.created_at && m.created_at.toLowerCase().includes(s)) ||
                (m.time && m.time.toLowerCase().includes(s)))
            ).map(m => ({
                ...m,
                sender: this.activeFolder === 'inbox' ? m.sender_email : m.receiver_email,
                time: m.created_at || m.time
            }));
        }
    },
    mounted() {
        // Fetch all users for receiver dropdown
        fetch('functions/fetch_all_accounts.php')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.accounts) {
                    this.allUsers = data.accounts;
                }
            });
        // Fetch profile pic and alumni details
        Promise.all([
            fetch('functions/fetch_profile_pic.php').then(res => res.json()),
            fetch('functions/fetch_alumni_details.php').then(res => res.json())
        ]).then(([picData, profileData]) => {
            if (picData.success) {
                this.profilePicData = { file_name: picData.file_name };
            }
            if (profileData.success) {
                this.profile = profileData.profile;
            }
        });
        // Fetch messages for inbox and sent
        this.fetchMessages();
        // Initialize dark mode from localStorage
        const savedDarkMode = localStorage.getItem('darkMode');
        if (savedDarkMode === 'true') {
            this.darkMode = true;
        } else {
            this.darkMode = false;
        }
    },
    watch: {
        selectAll(val) {
            if (val) {
                this.selectedMessages = this.filteredMessages.map(m => m.id);
            } else {
                this.selectedMessages = [];
            }
        },
        darkMode(newVal) {
            if (newVal) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            }
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
        goBack() {
            window.history.back();
        },
        copyTable() {
            // Prepare data
            const data = [
                ['Sender/Receiver', 'Subject', 'Message', 'Time'], // Header row
                ...this.filteredMessages.map(msg => [
                    msg.sender,
                    msg.subject,
                    this.stripHtml(msg.message),
                    msg.time
                ])
            ];
        
            // Convert to tab-delimited text
            const text = data.map(row => row.join('\t')).join('\n');
            
            // Copy to clipboard
            navigator.clipboard.writeText(text)
                .then(() => {
                    this.showSuccess('Table copied to clipboard!');
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                    this.showError('Failed to copy table to clipboard');
                });
        },
        addNotification(type, message) {
            const id = Date.now();
            this.notifications.push({
                id,
                type,
                message
            });
            
            // Auto-remove notification after 5 seconds
            setTimeout(() => {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }, 5000);
        },
        
        showSuccess(message) {
            this.addNotification('success', message);
        },
        
        showError(message) {
            this.addNotification('error', message);
        },
        
        showInfo(message) {
            this.addNotification('info', message);
        },
        printTable() {
            // Create a printable version of the table
            const printWindow = window.open('', '', 'width=800,height=600');
            const title = `${this.folderTitle} Messages - ${new Date().toLocaleDateString()}`;
            
            printWindow.document.write(`
                <html>
                    <head>
                        <title>${title}</title>
                        <style>
                            body { font-family: Arial, sans-serif; }
                            h1 { color: #1a73e8; margin-bottom: 20px; }
                            table { width: 100%; border-collapse: collapse; }
                            th { background-color: #f1f5f9; text-align: left; padding: 8px; }
                            td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
                            .no-messages { text-align: center; padding: 20px; color: #6b7280; }
                        </style>
                    </head>
                    <body>
                        <h1>${title}</h1>
                        ${this.generatePrintableTable()}
                        <script>
                            setTimeout(() => {
                                window.print();
                                window.close();
                            }, 200);
                        <\/script>
                    </body>
                </html>
            `);
        },
    
        generatePrintableTable() {
            if (this.filteredMessages.length === 0) {
                return `
                    <div class="no-messages">
                        <i class="fas fa-envelope-open-text"></i>
                        <p>No messages found in ${this.folderTitle}</p>
                    </div>
                `;
            }
        
            let table = `
                <table class="print-table">
                    <thead>
                        <tr>
                            <th class="sender-col">${this.activeFolder === 'inbox' ? 'Sender' : 'Recipient'}</th>
                            <th class="subject-col">Subject</th>
                            <th class="message-col">Message Preview</th>
                            <th class="time-col">Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
        
            this.filteredMessages.forEach((msg, index) => {
                // Highlight unread messages (if you track read status)
                const isUnread = msg.is_unread ? 'unread' : '';
                
                // Truncate message for better printing
                const messagePreview = this.stripHtml(msg.message);
                const truncatedMsg = messagePreview.length > 100 
                    ? messagePreview.substring(0, 100) + '...' 
                    : messagePreview;
        
                table += `
                    <tr class="${isUnread} ${index % 2 === 0 ? 'even' : 'odd'}">
                        <td class="sender-cell">
                            <div class="sender-info">
                                ${this.activeFolder === 'inbox' ? 
                                  `<strong>From:</strong> ${msg.sender}` : 
                                  `<strong>To:</strong> ${msg.sender}`}
                            </div>
                        </td>
                        <td class="subject-cell">
                            <strong>${msg.subject || '(No Subject)'}</strong>
                        </td>
                        <td class="message-cell">${truncatedMsg}</td>
                        <td class="time-cell">${msg.time}</td>
                    </tr>
                `;
            });
        
            table += `
                    </tbody>
                </table>
                <div class="print-footer">
                    <div class="print-meta">
                        <p>Generated by: ${this.profile.name || 'Alumni User'}</p>
                        <p>Generated on: ${new Date().toLocaleString()}</p>
                        <p>Total messages: ${this.filteredMessages.length}</p>
                    </div>
                    <div class="print-watermark">
                       LSPU - EIS - Alumni Portal
                    </div>
                </div>
            `;
        
            return table;
        },
    
        exportToExcel() {
            // Prepare data
            const data = [
                ['Sender/Receiver', 'Subject', 'Message', 'Time'], // Header row
                ...this.filteredMessages.map(msg => [
                    msg.sender,
                    msg.subject,
                    this.stripHtml(msg.message),
                    msg.time
                ])
            ];
    
            // Create worksheet
            const ws = XLSX.utils.aoa_to_sheet(data);
            
            // Create workbook
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Messages');
            
            // Export to file
            const fileName = `messages_${this.activeFolder}_${new Date().toISOString().slice(0,10)}.xlsx`;
            XLSX.writeFile(wb, fileName);

            this.showSuccess(`Exported ${this.folderTitle} messages successfully`);
        },
    
        exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Set document metadata
            doc.setProperties({
                title: `${this.folderTitle} Messages Export`,
                subject: 'Messages export from Alumni Portal',
                author: 'Alumni Portal System',
                creator: 'Alumni Portal'
            });
        
            // Add header with logo and title
            doc.setFontSize(20);
            doc.setTextColor(26, 115, 232); // LSPU blue color
            doc.setFont('helvetica', 'bold');
            doc.text('Alumni Portal', 105, 15, { align: 'center' });
            
            doc.setFontSize(16);
            doc.setTextColor(40, 40, 40);
            doc.text(`${this.folderTitle} Messages`, 105, 25, { align: 'center' });
            
            // Add decorative line
            doc.setDrawColor(26, 115, 232);
            doc.setLineWidth(0.5);
            doc.line(20, 30, 190, 30);
            
            // Add generation info
            doc.setFontSize(10);
            doc.setTextColor(100, 100, 100);
            doc.setFont('helvetica', 'normal');
            doc.text(`Generated by: ${this.profile.name || 'Alumni User'}`, 20, 40);
            doc.text(`Generated on: ${new Date().toLocaleString()}`, 20, 45);
            
            // Add table
            if (this.filteredMessages.length === 0) {
                doc.setFontSize(12);
                doc.setTextColor(100, 100, 100);
                doc.text('No messages found', 105, 60, { align: 'center' });
            } else {
                // Prepare data with truncated message preview
                const columns = [
                    { title: "From/To", dataKey: "sender" },
                    { title: "Subject", dataKey: "subject" },
                    { title: "Message Preview", dataKey: "message" },
                    { title: "Date/Time", dataKey: "time" }
                ];
                
                const rows = this.filteredMessages.map(msg => ({
                    sender: this.activeFolder === 'inbox' ? 
                           `From: ${msg.sender}` : `To: ${msg.sender}`,
                    subject: msg.subject,
                    message: this.stripHtml(msg.message).substring(0, 100) + 
                           (this.stripHtml(msg.message).length > 100 ? '...' : ''),
                    time: msg.time
                }));
                
                // AutoTable with improved styling
                doc.autoTable({
                    head: [columns.map(col => col.title)],
                    body: rows.map(row => columns.map(col => row[col.dataKey])),
                    startY: 50,
                    margin: { top: 50 },
                    styles: {
                        fontSize: 9,
                        cellPadding: 3,
                        overflow: 'linebreak',
                        font: 'helvetica',
                        textColor: [40, 40, 40],
                        fillColor: [255, 255, 255],
                        lineWidth: 0.1
                    },
                    headStyles: {
                        fillColor: [26, 115, 232],
                        textColor: [255, 255, 255],
                        fontStyle: 'bold',
                        halign: 'center'
                    },
                    alternateRowStyles: {
                        fillColor: [240, 240, 240]
                    },
                    columnStyles: {
                        0: { cellWidth: 35, fontStyle: 'bold' }, // Sender/Receiver
                        1: { cellWidth: 40 }, // Subject
                        2: { cellWidth: 70 }, // Message
                        3: { cellWidth: 25, halign: 'center' } // Time
                    },
                    didDrawPage: (data) => {
                        // Footer
                        doc.setFontSize(8);
                        doc.setTextColor(150, 150, 150);
                        doc.text(
                            `Page ${data.pageCount}`, 
                            105, 
                            doc.internal.pageSize.height - 10,
                            { align: 'center' }
                        );
                    }
                });
            }
            
            // Add watermark for confidential documents
            if (this.activeFolder === 'inbox' || this.activeFolder === 'sent') {
                doc.setFontSize(60);
                doc.setTextColor(230, 230, 230);
                doc.setFont('helvetica', 'bold');
            }
            
            // Save the PDF with a more descriptive filename
            const fileName = `AlumniPortal_Messages_${this.folderTitle}_${new Date().toISOString().slice(0,10)}.pdf`;
            doc.save(fileName);
        
            this.showSuccess(`PDF export of ${this.filteredMessages.length} ${this.folderTitle.toLowerCase()} messages completed!`);
        },

        openMessage(msg) {
            // Mark as read if needed
            if (msg.is_unread) {
              this.markAsRead(msg.id);
            }
            this.selectedMessage = msg;
          },
          
          markAsRead(messageId) {
            fetch('functions/mark_message_read.php', {
              method: 'POST',
              body: JSON.stringify({ message_id: messageId }),
              headers: { 'Content-Type': 'application/json' }
            }).then(() => {
              // Update local state if needed
            });
          },
          
          formatDate(dateString) {
            return new Date(dateString).toLocaleString();
          },
        fetchMessages() {
            fetch('functions/fetch_messages.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.inboxMessages = data.inbox || [];
                        this.sentMessages = data.sent || [];
                        this.importantMessages = data.important || [];
                        this.trashMessages = data.trash || [];
                        this.inboxCount = data.inbox_count || 0;
                        this.sentCount = data.sent_count || 0;
                        this.importantCount = data.important_count || 0;
                        this.trashCount = data.trash_count || 0;
                    }
                });
        },
        sendMessage() {
            this.compose.message = this.quill.root.innerHTML;
            fetch('functions/insert_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    receiver_email: this.compose.receiver,
                    subject: this.compose.subject,
                    message: this.compose.message,
                    role: this.compose.role
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.showCompose = false;
                    this.compose = { role: '', receiver: '', subject: '', message: '' };
                    if (this.quill) this.quill.setContents([]);
                    this.fetchMessages();
                } else {
                    if (data.success) {
                        this.showSuccess('Message sent successfully!');
                    } else {
                        this.showError(data.message || 'Failed to send message.');
                    }
                }
            });
        },
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
        },
        onReceiverChange() {
            const selected = this.allUsers.find(u => u.email === this.compose.receiver);
            if (selected) {
                // Map backend role to user-friendly label
                const roleMap = { admin: 'Administrator', employer: 'Employer', alumni: 'Alumni' };
                this.compose.role = roleMap[selected.user_role] || selected.user_role;
            } else {
                this.compose.role = '';
            }
        },
        stripHtml(html) {
            const div = document.createElement('div');
            div.innerHTML = html;
            return div.textContent || div.innerText || '';
        },
        moveToFolder(msg, folder) {
            fetch('functions/update_message_folder.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ id: msg.id, folder })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.fetchMessages();
                } else {
                    if (data.success) {
                        this.showSuccess('Message sent successfully!');
                    } else {
                        this.showError(data.message || 'Failed to send message.');
                    }
                }
            });
        },
        toggleImportant(msg) {
            const newFolder = msg.folder === 'important' ? (this.activeFolder === 'inbox' ? 'inbox' : 'sent') : 'important';
            this.moveToFolder(msg, newFolder);
            if (data.success) {
                this.showSuccess('Operation completed successfully');
                this.fetchMessages();
            } else {
                this.showError(data.message || 'Operation failed');
            }
        },
        moveToTrash(msg) {
            this.moveToFolder(msg, 'trash');
            if (data.success) {
                this.showSuccess('Operation completed successfully');
                this.fetchMessages();
            } else {
                this.showError(data.message || 'Operation failed');
            }
        },
        restoreFromTrash(msg) {
            // Restore to inbox if user is receiver, sent if user is sender
            const userEmail = this.profile?.email || this.$root.email || '';
            const newFolder = msg.receiver_email === userEmail ? 'inbox' : 'sent';
            
            fetch('functions/update_message_folder.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ 
                    id: msg.id, 
                    folder: newFolder 
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.showSuccess('Message restored successfully');
                    this.fetchMessages();
                } else {
                    this.showError(data.message || 'Failed to restore message');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showError('An error occurred while restoring the message');
            });
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedMessages = this.filteredMessages.map(m => m.id);
            } else {
                this.selectedMessages = [];
            }
        },
        toggleImportantSelected() {
            this.selectedMessages.forEach(id => {
                const msg = this.filteredMessages.find(m => m.id === id);
                if (msg) this.toggleImportant(msg);
            });
            this.selectedMessages = [];
            this.selectAll = false;
            if (data.success) {
                this.showSuccess('Operation completed successfully');
                this.fetchMessages();
            } else {
                this.showError(data.message || 'Operation failed');
            }
        },
        moveToTrashSelected() {
            this.selectedMessages.forEach(id => {
                const msg = this.filteredMessages.find(m => m.id === id);
                if (msg) this.moveToTrash(msg);
            });
            this.selectedMessages = [];
            this.selectAll = false;
        },
        restoreFromTrashSelected() {
            this.selectedMessages.forEach(id => {
                const msg = this.filteredMessages.find(m => m.id === id);
                if (msg) this.restoreFromTrash(msg);
            });
            this.selectedMessages = [];
            this.selectAll = false;
        },
        confirmLogout: function() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        },
        logout() {
            window.location.href = 'logout.php';
        }
    }
}).mount('#app');