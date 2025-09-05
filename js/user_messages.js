const { createApp } = Vue;

createApp({
    data() {
        return {
            darkMode: false,
            showCompose: false,
            activeFolder: 'inbox',
            showTutorialButton: true, // Start as false, will be updated after check
            showWelcomeModal: false, // Start as false
            currentWelcomeSlide: 0,
            welcomeSlides: [
                { title: "Welcome", content: "intro" },
                { title: "Navigation", content: "navigation" },
                { title: "Job Search", content: "job_search" },
                { title: "Profile", content: "profile" }
            ],
            compose: {
                role: 'Alumni',
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
            allUsers: [],
            inboxCount: 0,
            sentCount: 0,
            importantCount: 0,
            trashCount: 0,
            profileDropdownOpen: false,
            profile: {},
            profilePicData: {},
            showLogoutModal: false,
            sidebarOpen: false,
            notifications: [],
            selectedMessage: null,
            quillInitialized: false // Track if Quill is initialized
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
        // Check if Quill is available
        if (typeof Quill === 'undefined') {
            console.error('Quill editor is not loaded. Please include Quill.js in your project.');
            this.showError('Rich text editor is not available. Please refresh the page.');
        }
        
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

        this.checkUrlParameters();
        
        // Fetch messages for inbox and sent
        this.fetchMessages();
        
        // Initialize dark mode from localStorage
        const savedDarkMode = localStorage.getItem('darkMode');
        this.darkMode = savedDarkMode === 'true';
        
        // Apply dark mode class immediately
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
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
                    this.initQuill();
                });
            } else if (this.quill) {
                // Clear the editor when closing
                this.quill.setContents([]);
            }
        }
    },
    methods: {
        initQuill() {
            // Only initialize Quill if it's available and not already initialized
            if (typeof Quill === 'undefined') {
                console.error('Quill is not available');
                this.showError('Rich text editor is not available');
                return;
            }
            
            if (!this.quillInitialized && document.getElementById('editor')) {
                try {
                    this.quill = new Quill('#editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['link', 'image'],
                                ['clean']
                            ]
                        }
                    });
                    this.quillInitialized = true;
                } catch (error) {
                    console.error('Error initializing Quill:', error);
                    this.showError('Failed to initialize text editor');
                }
            }
        },
        checkUrlParameters() {
            const urlParams = new URLSearchParams(window.location.search);
            const composeParam = urlParams.get('compose');
            const toParam = urlParams.get('to');
            
            if (composeParam === 'true') {
                this.showCompose = true;
                
                if (toParam) {
                    // Set the receiver email if provided
                    this.compose.receiver = decodeURIComponent(toParam);
                    
                    // Try to find the user in allUsers to set the role
                    const user = this.allUsers.find(u => u.email === this.compose.receiver);
                    if (user) {
                        this.compose.role = user.role;
                    } else {
                        // If user not found, try to determine role from email pattern
                        this.compose.role = this.determineRoleFromEmail(this.compose.receiver);
                    }
                }
            }
        },
        
        goBack() {
            window.history.back();
        },
        copyTable() {
            // Prepare data
            const data = [
                ['Sender/Receiver', 'Subject', 'Message', 'Time'],
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
        replyToMessage(message) {
            // Set up the compose modal for reply
            this.compose = {
                receiver: message.sender_email,
                role: this.getRoleFromEmail(message.sender_email),
                subject: `Re: ${message.subject}`,
                message: ''
            };
            
            // Initialize Quill editor with quoted message
            this.showCompose = true;
            this.$nextTick(() => {
                this.initQuill();
                
                if (this.quill) {
                    // Add quoted message
                    const quotedMessage = `
                        <br><br>
                        <div style="border-left: 3px solid #ccc; padding-left: 10px; margin-left: 10px; color: #666;">
                            <p><strong>Original message from ${message.sender_email}:</strong></p>
                            <div>${message.message}</div>
                        </div>
                    `;
                    
                    this.quill.clipboard.dangerouslyPasteHTML(0, quotedMessage);
                    
                    // Set cursor to the beginning
                    this.quill.setSelection(0, 0);
                }
            });
            
            // Close the message view
            this.selectedMessage = null;
        },
        
        forwardMessage(message) {
            // Set up the compose modal for forwarding
            this.compose = {
                receiver: '',
                role: '',
                subject: `Fwd: ${message.subject}`,
                message: ''
            };
            
            this.showCompose = true;
            this.$nextTick(() => {
                this.initQuill();
                
                if (this.quill) {
                    // Add forwarded message content
                    const forwardedMessage = `
                        <br><br>
                        <div style="border-left: 3px solid #ccc; padding-left: 10px; margin-left: 10px; color: #666;">
                            <p><strong>---------- Forwarded message ----------</strong></p>
                            <p><strong>From:</strong> ${message.sender_email}</p>
                            <p><strong>Date:</strong> ${this.formatDate(message.created_at)}</p>
                            <p><strong>Subject:</strong> ${message.subject}</p>
                            <p><strong>To:</strong> ${message.receiver_email}</p>
                            <br>
                            <div>${message.message}</div>
                        </div>
                    `;
                    
                    this.quill.clipboard.dangerouslyPasteHTML(0, forwardedMessage);
                    
                    // Set cursor to the beginning
                    this.quill.setSelection(0, 0);
                }
            });
            
            // Close the message view
            this.selectedMessage = null;
        },
        
        getRoleFromEmail(email) {
            const user = this.allUsers.find(u => u.email === email);
            if (user) {
                // Map backend role to user-friendly label
                const roleMap = { admin: 'Administrator', employer: 'Employer', alumni: 'Alumni' };
                return roleMap[user.user_role] || user.user_role;
            }
            return '';
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
                // Truncate message for better printing
                const messagePreview = this.stripHtml(msg.message);
                const truncatedMsg = messagePreview.length > 100 
                    ? messagePreview.substring(0, 100) + '...' 
                    : messagePreview;
        
                table += `
                    <tr class="${index % 2 === 0 ? 'even' : 'odd'}">
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
            // Check if XLSX is available
            if (typeof XLSX === 'undefined') {
                this.showError('Excel export library is not loaded');
                return;
            }
            
            // Prepare data
            const data = [
                ['Sender/Receiver', 'Subject', 'Message', 'Time'],
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
            // Check if jsPDF is available
            if (typeof jsPDF === 'undefined') {
                this.showError('PDF export library is not loaded');
                return;
            }
            
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
            doc.setTextColor(26, 115, 232);
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
                        0: { cellWidth: 35, fontStyle: 'bold' },
                        1: { cellWidth: 40 },
                        2: { cellWidth: 70 },
                        3: { cellWidth: 25, halign: 'center' }
                    },
                    didDrawPage: (data) => {
                        // Footer
                        doc.setFontSize(8);
                        doc.setTextColor(150, 150, 150);
                        doc.text(
                            `Page ${data.pageNumber}`, 
                            105, 
                            doc.internal.pageSize.height - 10,
                            { align: 'center' }
                        );
                    }
                });
            }
            
            // Save the PDF
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
                const msg = this.filteredMessages.find(m => m.id === messageId);
                if (msg) {
                    msg.is_unread = false;
                }
            });
        },

        openTutorial() {
            this.showWelcomeModal = true;
            this.currentWelcomeSlide = 0;
            
            // Mark tutorial as viewed in session storage
            sessionStorage.setItem('tutorial_viewed', 'true');
        },
        
        closeWelcomeModal() {
            console.log('Closing welcome modal');
            this.showWelcomeModal = false;
            
            // Always mark as shown when user closes the modal
            localStorage.setItem('welcomeModalShown', 'true');
            console.log('Set welcomeModalShown to true in localStorage');
            
            // If user completed the tutorial (reached the end), mark it as completed
            if (this.currentWelcomeSlide === this.welcomeSlides.length - 1) {
                console.log('User completed tutorial, marking as completed');
                this.markTutorialCompleted();
            }
        },
        async markTutorialCompleted() {
            try {
                const response = await fetch('functions/mark_tutorial_completed.php', {
                    method: 'POST'
                });
                
                const data = await response.json();
                if (data.success) {
                    this.showTutorialButton = false;
                    sessionStorage.setItem('tutorial_completed', 'true');
                }
            } catch (error) {
                console.error('Error marking tutorial as completed:', error);
            }
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
            if (!this.quill) {
                this.showError('Editor is not ready. Please try again.');
                return;
            }
            
            this.compose.message = this.quill.root.innerHTML;
            
            // Validate inputs
            if (!this.compose.receiver) {
                this.showError('Please select a recipient');
                return;
            }
            
            if (!this.compose.subject.trim()) {
                this.showError('Please enter a subject');
                return;
            }
            
            if (!this.compose.message.trim() || this.compose.message === '<p><br></p>') {
                this.showError('Please enter a message');
                return;
            }
            
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
                    this.showSuccess('Message sent successfully!');
                    this.showCompose = false;
                    this.compose = { role: '', receiver: '', subject: '', message: '' };
                    if (this.quill) this.quill.setContents([]);
                    this.fetchMessages();
                } else {
                    this.showError(data.message || 'Failed to send message.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showError('An error occurred while sending the message');
            });
        },
        
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
        },
        
        onReceiverChange() {
            const selected = this.allUsers.find(u => u.email === this.compose.receiver);
            if (selected) {
                const roleMap = { admin: 'Administrator', employer: 'Employer', alumni: 'Alumni' };
                this.compose.role = roleMap[selected.user_role] || selected.user_role;
            } else {
                this.compose.role = '';
            }
        },
        
        stripHtml(html) {
            if (!html) return '';
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
                    this.showSuccess('Message moved successfully');
                } else {
                    this.showError(data.message || 'Failed to move message');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showError('An error occurred while moving the message');
            });
        },
        
        toggleImportant(msg) {
            const newFolder = msg.folder === 'important' ? (this.activeFolder === 'inbox' ? 'inbox' : 'sent') : 'important';
            this.moveToFolder(msg, newFolder);
        },
        
        moveToTrash(msg) {
            this.moveToFolder(msg, 'trash');
        },
        
        restoreFromTrash(msg) {
            // Restore to inbox if user is receiver, sent if user is sender
            const userEmail = this.profile?.email || '';
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
            if (this.selectedMessages.length === 0) {
                this.showInfo('Please select messages to mark as important');
                return;
            }
            
            this.selectedMessages.forEach(id => {
                const msg = this.filteredMessages.find(m => m.id === id);
                if (msg) this.toggleImportant(msg);
            });
            this.selectedMessages = [];
            this.selectAll = false;
        },
        
        moveToTrashSelected() {
            if (this.selectedMessages.length === 0) {
                this.showInfo('Please select messages to move to trash');
                return;
            }
            
            this.selectedMessages.forEach(id => {
                const msg = this.filteredMessages.find(m => m.id === id);
                if (msg) this.moveToTrash(msg);
            });
            this.selectedMessages = [];
            this.selectAll = false;
        },
        
        restoreFromTrashSelected() {
            if (this.selectedMessages.length === 0) {
                this.showInfo('Please select messages to restore');
                return;
            }
            
            this.selectedMessages.forEach(id => {
                const msg = this.filteredMessages.find(m => m.id === id);
                if (msg) this.restoreFromTrash(msg);
            });
            this.selectedMessages = [];
            this.selectAll = false;
        },
        
        confirmLogout() {
            this.showLogoutModal = true;
        },
        
        logout() {
            window.location.href = 'logout.php';
        }
    }
}).mount('#app');