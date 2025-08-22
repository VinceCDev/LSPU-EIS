const { createApp } = Vue;

            createApp({
                data() {
                    return {
                        sidebarActive: window.innerWidth >= 768,
                        profileDropdownOpen: false,
                        darkMode: localStorage.getItem('darkMode') === 'true' || 
                     (localStorage.getItem('darkMode') === null && 
                      window.matchMedia('(prefers-color-scheme: dark)').matches),
                        showLogoutModal: false,
                        isMobile: window.innerWidth < 768,
                        notifications: [],
                        notificationId: 0,
                        profile: {
                            profile_pic: '',
                            name: '',
                        },
                        programStats: [],
                        sectorStats: [],
                        locationStats: [],
                        statusStats: [],
                        loading: true,
                        companiesDropdownOpen: false,
                        alumniDropdownOpen: false
                    }
                },
                computed: {
                    summaryStats() {
                        const totalGraduates = this.programStats.reduce((sum, program) => sum + parseInt(program.total_graduates), 0);
                        const employedCount = this.programStats.reduce((sum, program) => sum + parseInt(program.employed_count), 0);
                        const relatedJobCount = this.programStats.reduce((sum, program) => sum + parseInt(program.related_job_count), 0);
                        
                        return {
                            totalGraduates,
                            employedCount,
                            employmentRate: totalGraduates > 0 ? Math.round((employedCount / totalGraduates) * 100) : 0,
                            jobMatchRate: employedCount > 0 ? Math.round((relatedJobCount / employedCount) * 100) : 0
                        };
                    }
                },
                mounted() {
                    this.applyDarkMode();
                    this.fetchSummaryData();
                    fetch('functions/fetch_admin_details.php')
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.profile) {
                                this.profile = data.profile;
                            }
                        });
                    window.addEventListener('resize', this.handleResize);
                },
                beforeUnmount() {
                    window.removeEventListener('resize', this.handleResize);
                },
                watch: {
                    darkMode(val) {
                        this.applyDarkMode();
                    }
                },
                methods: {
                    async fetchSummaryData() {
                        try {
                            const response = await fetch('functions/fetch_report_summary_data.php');
                            const data = await response.json();
                            
                            if (data.success) {
                                this.programStats = data.program_stats;
                                this.sectorStats = data.sector_stats;
                                this.locationStats = data.location_stats;
                                this.statusStats = data.status_stats;
                            } else {
                                this.showNotification('Failed to load summary data', 'error');
                            }
                        } catch (error) {
                            console.error('Error fetching summary data:', error);
                            this.showNotification('Failed to load summary data', 'error');
                        } finally {
                            this.loading = false;
                        }
                    },
                    toggleSidebar() {
                        this.sidebarActive = !this.sidebarActive;
                    },
                    handleResize() {
                        this.isMobile = window.innerWidth < 768;
                        if (window.innerWidth >= 768) {
                            this.sidebarActive = true;
                        } else {
                            this.sidebarActive = false;
                        }
                    },
                    toggleDarkMode() {
                        this.darkMode = !this.darkMode;
                        localStorage.setItem('darkMode', this.darkMode.toString());
                        this.applyDarkMode();
                        // Force a small delay to ensure the DOM updates
                        this.$nextTick(() => {
                            this.applyDarkMode();
                        });
                    },
                    applyDarkMode() {
                        if (this.darkMode) {
                            document.documentElement.classList.add('dark');
                            document.body.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            document.body.classList.remove('dark');
                        }
                    },
                    toggleProfileDropdown() {
                        this.profileDropdownOpen = !this.profileDropdownOpen;
                    },
                    handleNavClick() {
                        // Close dropdowns when navigating
                        this.companiesDropdownOpen = false;
                        this.alumniDropdownOpen = false;
                    },
                    confirmLogout() {
                        this.showLogoutModal = true;
                    },
                    logout() {
                        window.location.href = 'logout.php';
                    },
                    showNotification(message, type = 'success') {
                        const id = this.notificationId++;
                        this.notifications.push({ id, type, message });
                        setTimeout(() => this.removeNotification(id), 3000);
                    },
                    removeNotification(id) {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                    },
                    calculatePercentage(part, total) {
                        if (total === 0) return 0;
                        return Math.round((part / total) * 100);
                    },
                    // Enhanced applyEmploymentSummaryStyling function with proper header colors and styling
                    applyEmploymentSummaryStyling(worksheet) {
                        const headerRange = XLSX.utils.decode_range(worksheet['!ref']);
                        
                        // Apply enhanced styling to headers (first row) - dark blue background with borders
                        for (let col = headerRange.s.c; col <= headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            worksheet[cellAddress].s = {
                                fill: { 
                                    patternType: "solid",
                                    fgColor: { rgb: "4472C4" } // Dark blue background
                                },
                                font: { 
                                    bold: true, 
                                    color: { rgb: "FFFFFF" }, // White text
                                    sz: 12 // Font size
                                },
                                alignment: { 
                                    horizontal: "center", 
                                    vertical: "center",
                                    wrapText: true
                                },
                                border: {
                                    top: { style: "thin", color: { rgb: "000000" } },
                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                    left: { style: "thin", color: { rgb: "000000" } },
                                    right: { style: "thin", color: { rgb: "000000" } }
                                }
                            };
                        }
                    
                        // Rest of your styling logic...
                        return worksheet;
                    },
                    
                    applyDetailedReportStyling(worksheet) {
                        const headerRange = XLSX.utils.decode_range(worksheet['!ref']);
                        
                        // Apply enhanced styling to first header row (employment summary) with borders
                        for (let col = 0; col < 10; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            
                            let headerColor = "4472C4"; // Default blue
                            if (col === 0) headerColor = "006400"; // Dark green
                            else if (col >= 1 && col <= 3) headerColor = "000080"; // Dark blue
                            else if (col >= 4 && col <= 6) headerColor = "800020"; // Maroon
                            else if (col >= 7 && col <= 9) headerColor = "FF0000"; // Red
                            
                            worksheet[cellAddress].s = {
                                fill: { 
                                    patternType: "solid",
                                    fgColor: { rgb: headerColor }
                                },
                                font: { 
                                    bold: true, 
                                    color: { rgb: "FFFFFF" },
                                    sz: 12 // Font size
                                },
                                alignment: { 
                                    horizontal: "center", 
                                    vertical: "center",
                                    wrapText: true
                                },
                                border: {
                                    top: { style: "thin", color: { rgb: "000000" } },
                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                    left: { style: "thin", color: { rgb: "000000" } },
                                    right: { style: "thin", color: { rgb: "000000" } }
                                }
                            };
                        }
                        
                        // Apply styling to second header row (employment summary)
                        for (let col = 0; col < 10; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 1, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            
                            if (col === 0) {
                                // Program header - dark green background
                                worksheet[cellAddress].s = {
                                    fill: { 
                                        patternType: "solid",
                                        fgColor: { rgb: "006400" } 
                                    },
                                    font: { 
                                        color: { rgb: "FFFFFF" }, 
                                        bold: true 
                                    },
                                    alignment: { 
                                        horizontal: "center", 
                                        vertical: "center" 
                                    }
                                };
                            } else if (col === 1 || col === 4 || col === 7) {
                                // Category columns - same colors as their sections
                                const sectionColors = {
                                    1: "000080", // Dark blue for Status
                                    4: "800020", // Dark purple/maroon for Sector
                                    7: "FF0000"  // Red for Location
                                };
                                worksheet[cellAddress].s = {
                                    fill: { 
                                        patternType: "solid",
                                        fgColor: { rgb: sectionColors[col] } 
                                    },
                                    font: { 
                                        color: { rgb: "FFFFFF" }, 
                                        bold: true 
                                    },
                                    alignment: { 
                                        horizontal: "center", 
                                        vertical: "center" 
                                    }
                                };
                            } else if (col === 2 || col === 5 || col === 8) {
                                // TOTAL columns - same colors as their sections
                                const sectionColors = {
                                    2: "000080", // Dark blue for Status
                                    5: "800020", // Dark purple/maroon for Sector
                                    8: "FF0000"  // Red for Location
                                };
                                worksheet[cellAddress].s = {
                                    fill: { 
                                        patternType: "solid",
                                        fgColor: { rgb: sectionColors[col] } 
                                    },
                                    font: { 
                                        color: { rgb: "FFFFFF" }, 
                                        bold: true 
                                    },
                                    alignment: { 
                                        horizontal: "center", 
                                        vertical: "center" 
                                    }
                                };
                            } else {
                                // Empty columns - same colors as their sections
                                const sectionColors = {
                                    3: "000080", // Dark blue for Status
                                    6: "800020", // Dark purple/maroon for Sector
                                    9: "FF0000"  // Red for Location
                                };
                                worksheet[cellAddress].s = {
                                    fill: { 
                                        patternType: "solid",
                                        fgColor: { rgb: sectionColors[col] } 
                                    },
                                    font: { 
                                        color: { rgb: "FFFFFF" }, 
                                        bold: true 
                                    },
                                    alignment: { 
                                        horizontal: "center", 
                                        vertical: "center" 
                                    }
                                };
                            }
                        }
                        
                        // Apply styling to employment summary data rows
                        for (let row = 2; row <= headerRange.e.r; row++) {
                            for (let col = 0; col < 10; col++) {
                                const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                if (worksheet[cellAddress]) {
                                    if (col === 0) {
                                        // Program column - white background, black text
                                        worksheet[cellAddress].s = {
                                            font: { bold: false },
                                            alignment: { 
                                                horizontal: "left", 
                                                vertical: "center" 
                                            }
                                        };
                                    } else {
                                        // Data columns - white background, black text
                                        worksheet[cellAddress].s = {
                                            font: { bold: false },
                                            alignment: { 
                                                horizontal: "center", 
                                                vertical: "center" 
                                            }
                                        };
                                    }
                                }
                            }
                        }
                    
                        // Find and style the graduates table section
                        let graduatesStartRow = -1;
                        for (let row = 2; row <= headerRange.e.r; row++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: row, c: 0 });
                            if (worksheet[cellAddress] && worksheet[cellAddress].v === 'Campus') {
                                graduatesStartRow = row;
                                break;
                            }
                        }
                    
                        if (graduatesStartRow !== -1) {
                            // Apply styling to graduates table header row
                            for (let col = 0; col < headerRange.e.c; col++) {
                                const cellAddress = XLSX.utils.encode_cell({ r: graduatesStartRow, c: col });
                                if (!worksheet[cellAddress]) {
                                    worksheet[cellAddress] = { v: '', t: 's' };
                                }
                                
                                if (col >= 13 && col <= 15) {
                                    // Relevance of Employment section - green background
                                    worksheet[cellAddress].s = {
                                        fill: { 
                                            patternType: "solid",
                                            fgColor: { rgb: "008000" } 
                                        },
                                        font: { 
                                            bold: true, 
                                            color: { rgb: "FFFFFF" } 
                                        },
                                        alignment: { 
                                            horizontal: "center", 
                                            vertical: "center" 
                                        }
                                    };
                                } else if (col >= 16 && col <= 20) {
                                    // Personal Details section - red background
                                    worksheet[cellAddress].s = {
                                        fill: { 
                                            patternType: "solid",
                                            fgColor: { rgb: "FF0000" } 
                                        },
                                        font: { 
                                            bold: true, 
                                            color: { rgb: "FFFFFF" } 
                                        },
                                        alignment: { 
                                            horizontal: "center", 
                                            vertical: "center" 
                                        }
                                    };
                                } else {
                                    // Regular header styling
                                    worksheet[cellAddress].s = {
                                        fill: { 
                                            patternType: "solid",
                                            fgColor: { rgb: "4472C4" } 
                                        },
                                        font: { 
                                            bold: true,
                                            color: { rgb: "FFFFFF" } 
                                        },
                                        alignment: { 
                                            horizontal: "center", 
                                            vertical: "center" 
                                        }
                                    };
                                }
                            }
                    
                            // Apply styling to sub-headers row
                            for (let col = 0; col < headerRange.e.c; col++) {
                                const cellAddress = XLSX.utils.encode_cell({ r: graduatesStartRow + 1, c: col });
                                if (!worksheet[cellAddress]) {
                                    worksheet[cellAddress] = { v: '', t: 's' };
                                }
                                
                                if (col >= 13 && col <= 15) {
                                    // Relevance of Employment sub-headers - green background
                                    worksheet[cellAddress].s = {
                                        fill: { 
                                            patternType: "solid",
                                            fgColor: { rgb: "008000" } 
                                        },
                                        font: { 
                                            bold: true, 
                                            color: { rgb: "FFFFFF" } 
                                        },
                                        alignment: { 
                                            horizontal: "center", 
                                            vertical: "center" 
                                        }
                                    };
                                } else if (col >= 16 && col <= 20) {
                                    // Personal Details sub-headers - red background
                                    worksheet[cellAddress].s = {
                                        fill: { 
                                            patternType: "solid",
                                            fgColor: { rgb: "FF0000" } 
                                        },
                                        font: { 
                                            bold: true, 
                                            color: { rgb: "FFFFFF" } 
                                        },
                                        alignment: { 
                                            horizontal: "center", 
                                            vertical: "center" 
                                        }
                                    };
                                } else {
                                    // Regular sub-header styling
                                    worksheet[cellAddress].s = {
                                        fill: { 
                                            patternType: "solid",
                                            fgColor: { rgb: "4472C4" } 
                                        },
                                        font: { 
                                            bold: true,
                                            color: { rgb: "FFFFFF" } 
                                        },
                                        alignment: { 
                                            horizontal: "center", 
                                            vertical: "center" 
                                        }
                                    };
                                }
                            }
                    
                            // Apply styling to graduates data rows
                            for (let row = graduatesStartRow + 2; row <= headerRange.e.r; row++) {
                                for (let col = 0; col < headerRange.e.c; col++) {
                                    const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                    if (worksheet[cellAddress]) {
                                        if (col === 0) {
                                            // Course header row - yellow background
                                            if (worksheet[cellAddress].v && (worksheet[cellAddress].v.includes('BS ') || worksheet[cellAddress].v.includes('Bachelor'))) {
                                                worksheet[cellAddress].s = {
                                                    fill: { 
                                                        patternType: "solid",
                                                        fgColor: { rgb: "FFFF00" } 
                                                    },
                                                    font: { bold: true },
                                                    alignment: { 
                                                        horizontal: "center", 
                                                        vertical: "center" 
                                                    }
                                                };
                                            } else if (col >= 13 && col <= 15) {
                                                // Relevance of Employment data - light green background
                                                worksheet[cellAddress].s = {
                                                    fill: { 
                                                        patternType: "solid",
                                                        fgColor: { rgb: "E6FFE6" } 
                                                    },
                                                    font: { bold: false },
                                                    alignment: { 
                                                        horizontal: "center", 
                                                        vertical: "center" 
                                                    }
                                                };
                                            } else if (col >= 16 && col <= 20) {
                                                // Personal Details data - light red background
                                                worksheet[cellAddress].s = {
                                                    fill: { 
                                                        patternType: "solid",
                                                        fgColor: { rgb: "FFE6E6" } 
                                                    },
                                                    font: { bold: false },
                                                    alignment: { 
                                                        horizontal: "left", 
                                                        vertical: "center" 
                                                    }
                                                };
                                            } else if (col === 1) {
                                                // Program Name column - light blue background
                                                worksheet[cellAddress].s = {
                                                    fill: { 
                                                        patternType: "solid",
                                                        fgColor: { rgb: "E6F3FF" } 
                                                    },
                                                    font: { bold: false },
                                                    alignment: { 
                                                        horizontal: "left", 
                                                        vertical: "center" 
                                                    }
                                                };
                                            } else {
                                                // Regular data styling
                                                worksheet[cellAddress].s = {
                                                    font: { bold: false },
                                                    alignment: { 
                                                        horizontal: "center", 
                                                        vertical: "center" 
                                                    }
                                                };
                                            }
                                        } else {
                                            if (col >= 13 && col <= 15) {
                                                // Relevance of Employment data - light green background
                                                worksheet[cellAddress].s = {
                                                    fill: { 
                                                        patternType: "solid",
                                                        fgColor: { rgb: "E6FFE6" } 
                                                    },
                                                    font: { bold: false },
                                                    alignment: { 
                                                        horizontal: "center", 
                                                        vertical: "center" 
                                                    }
                                                };
                                            } else if (col >= 16 && col <= 20) {
                                                // Personal Details data - light red background
                                                worksheet[cellAddress].s = {
                                                    fill: { 
                                                        patternType: "solid",
                                                        fgColor: { rgb: "FFE6E6" } 
                                                    },
                                                    font: { bold: false },
                                                    alignment: { 
                                                        horizontal: "left", 
                                                        vertical: "center" 
                                                    }
                                                };
                                            } else if (col === 1) {
                                                // Program Name column - light blue background
                                                worksheet[cellAddress].s = {
                                                    fill: { 
                                                        patternType: "solid",
                                                        fgColor: { rgb: "E6F3FF" } 
                                                    },
                                                    font: { bold: false },
                                                    alignment: { 
                                                        horizontal: "left", 
                                                        vertical: "center" 
                                                    }
                                                };
                                            } else {
                                                // Regular data styling
                                                worksheet[cellAddress].s = {
                                                    font: { bold: false },
                                                    alignment: { 
                                                        horizontal: "center", 
                                                        vertical: "center" 
                                                    }
                                                };
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        
                        return worksheet;
                    },

                    async exportEmploymentSummary(format) {
                        try {
                            if (format === 'excel') {
                                // Fetch data from fetch_all_report_data.php
                                const response = await fetch('functions/fetch_all_report_data.php');
                                const data = await response.json();
                                
                                if (!data.success) {
                                    throw new Error(data.error || 'Failed to fetch data');
                                }
                    
                                // Create new ExcelJS workbook
                                const workbook = new ExcelJS.Workbook();
                                const worksheet = workbook.addWorksheet("Employment Summary");
                    
                                // Define colors
                                const colors = {
                                    headerBg: '4F46E5', // Indigo
                                    headerText: 'FFFFFF', // White
                                    collegeBg: 'E0E7FF', // Light indigo
                                    collegeText: '1E3A8A', // Dark blue
                                    courseBg: 'FFFFFF', // White
                                    courseText: '000000', // Black
                                    highlightGreen: 'D1FAE5', // Light green
                                    highlightRed: 'FEE2E2'  // Light red
                                };
                    
                                // Add header row
                                const headerRow = worksheet.addRow([
                                    '', 'No. of Graduates', 'No. of Employed', 
                                    'Percentage', 'Work Related/in-line to course', '% Matched'
                                ]);
                    
                                // Style header row
                                headerRow.eachCell((cell) => {
                                    cell.fill = {
                                        type: 'pattern',
                                        pattern: 'solid',
                                        fgColor: { argb: colors.headerBg }
                                    };
                                    cell.font = {
                                        bold: true,
                                        color: { argb: colors.headerText }
                                    };
                                    cell.alignment = { 
                                        horizontal: 'center', 
                                        vertical: 'middle',
                                        wrapText: true
                                    };
                                    cell.border = {
                                        top: { style: 'thin' },
                                        left: { style: 'thin' },
                                        bottom: { style: 'thin' },
                                        right: { style: 'thin' }
                                    };
                                });
                    
                                // Group data by college
                                const collegeGroups = {};
                                data.employment_summary.forEach(row => {
                                    if (row.is_header) {
                                        if (!collegeGroups[row.college]) {
                                            collegeGroups[row.college] = [];
                                        }
                                        collegeGroups[row.college].push({
                                            type: 'college_header',
                                            data: row
                                        });
                                    } else if (row.course && row.course.trim() !== '') {
                                        const collegeName = this.findCollegeForCourse(data.employment_summary, row.course);
                                        if (!collegeGroups[collegeName]) {
                                            collegeGroups[collegeName] = [];
                                        }
                                        collegeGroups[collegeName].push({
                                            type: 'course_data',
                                            data: row
                                        });
                                    }
                                });
                    
                                // Add data rows with styling
                                Object.keys(collegeGroups).forEach(collegeName => {
                                    const collegeData = collegeGroups[collegeName];
                                    
                                    // Add college header row
                                    const collegeRow = worksheet.addRow([
                                        collegeName, '', '', '', '', ''
                                    ]);
                                    
                                    // Style college header row
                                    collegeRow.eachCell((cell) => {
                                        cell.fill = {
                                            type: 'pattern',
                                            pattern: 'solid',
                                            fgColor: { argb: colors.collegeBg }
                                        };
                                        cell.font = {
                                            bold: true,
                                            color: { argb: colors.collegeText }
                                        };
                                        cell.border = {
                                            top: { style: 'thin' },
                                            left: { style: 'thin' },
                                            bottom: { style: 'thin' },
                                            right: { style: 'thin' }
                                        };
                                    });
                    
                                    // Add course data rows
                                    collegeData.forEach(item => {
                                        if (item.type === 'course_data') {
                                            const courseRow = worksheet.addRow([
                                                item.data.course,
                                                item.data.total_graduates,
                                                item.data.employed_count,
                                                item.data.employment_rate,
                                                item.data.related_job_count,
                                                item.data.match_rate
                                            ]);
                    
                                            // Style course data row
                                            courseRow.eachCell((cell, colNumber) => {
                                                cell.fill = {
                                                    type: 'pattern',
                                                    pattern: 'solid',
                                                    fgColor: { argb: colors.courseBg }
                                                };
                                                cell.font = {
                                                    color: { argb: colors.courseText }
                                                };
                                                cell.border = {
                                                    top: { style: 'thin' },
                                                    left: { style: 'thin' },
                                                    bottom: { style: 'thin' },
                                                    right: { style: 'thin' }
                                                };
                    
                                                // Right-align numeric columns
                                                if (colNumber > 1) {
                                                    cell.alignment = { horizontal: 'right' };
                                                }
                    
                                                // Highlight low/high percentages
                                                if (colNumber === 4) { // Percentage column
                                                    const rate = parseFloat(item.data.employment_rate) || 0;
                                                    if (rate < 50) {
                                                        cell.fill.fgColor = { argb: colors.highlightRed };
                                                    } else if (rate > 80) {
                                                        cell.fill.fgColor = { argb: colors.highlightGreen };
                                                    }
                                                }
                                            });
                                        }
                                    });
                                });
                    
                                // Set column widths
                                worksheet.columns = [
                                    { width: 30 }, // College/Course names
                                    { width: 18 }, // No. of Graduates
                                    { width: 18 }, // No. of Employed
                                    { width: 15 }, // Percentage
                                    { width: 25 }, // Work Related/in-line to course
                                    { width: 15 }  // % Matched
                                ];
                    
                                // Generate filename with current date
                                const filename = `employment_summary_${new Date().toISOString().split('T')[0]}.xlsx`;
                                
                                // Export to Excel file
                                const buffer = await workbook.xlsx.writeBuffer();
                                saveAs(new Blob([buffer]), filename);
                                
                                this.showNotification('Excel report exported successfully!', 'success');
                            }
                        } catch (error) {
                            console.error('Export error:', error);
                            this.showNotification('Failed to export report', 'error');
                        }
                    },

                    async exportDetailedEmployment(format) {
                        try {
                            if (format === 'excel') {
                                const response = await fetch('functions/fetch_all_report_data.php');
                                const data = await response.json();
                                
                                if (!data.success) {
                                    throw new Error(data.error || 'Failed to fetch data');
                                }
                                
                                const workbook = new ExcelJS.Workbook();
                                
                                const styles = {
                                    header: {
                                        fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: '4F46E5' } },
                                        font: { bold: true, color: { argb: 'FFFFFF' }, size: 12 },
                                        alignment: { horizontal: 'center', vertical: 'middle', wrapText: true }
                                    },
                                    subHeader: {
                                        fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: '93C5FD' } },
                                        font: { bold: true, size: 11 },
                                        alignment: { horizontal: 'center', vertical: 'middle' }
                                    },
                                    collegeHeader: {
                                        fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: 'E0E7FF' } },
                                        font: { bold: true, color: { argb: '1E3A8A' } },
                                        alignment: { horizontal: 'left', vertical: 'middle' }
                                    },
                                    courseHeader: {
                                        fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: 'F3F4F6' } },
                                        font: { bold: true, italic: true },
                                        alignment: { horizontal: 'left', vertical: 'middle' }
                                    },
                                    dataRow: {
                                        font: { color: { argb: '000000' } },
                                        alignment: { vertical: 'middle' }
                                    },
                                    border: {
                                        top: { style: 'thin' },
                                        left: { style: 'thin' },
                                        bottom: { style: 'thin' },
                                        right: { style: 'thin' }
                                    }
                                };
                    
                                const collegeCourses = {
                                    "College of Computer Studies": [
                                        "BS Information Technology",
                                        "BS Computer Science"
                                    ],
                                    "College of Engineering": [
                                        "BS Electronics Engineering",
                                        "BS Electrical Engineering",
                                        "BS Computer Engineering"
                                    ],
                                    "College of Business Administration": [
                                        "BS Office Administration",
                                        "BS Business Administration Major in Financial Management",
                                        "BS Business Administration Major in Marketing Management",
                                        "BS Accountancy"
                                    ],
                                    "College of Education": [
                                        "BS Elementary Education",
                                        "BS Physical Education",
                                        "BS Secondary Education Major in English",
                                        "BS Secondary Education Major in Filipino",
                                        "BS Secondary Education Major in Mathematics",
                                        "BS Secondary Education Major in Science",
                                        "BS Secondary Education Major in Social Studies",
                                        "BS Technology and Livelihood Education Major in Home Economics",
                                        "BS Technical-Vocational Teacher Education Major in Electrical Technology",
                                        "BS Technical-Vocational Teacher Education Major in Electronics Technology",
                                        "BS Technical-Vocational Teacher Education Major in Food & Service Management",
                                        "BS Technical-Vocational Teacher Education Major in Garments, Fashion & Design"
                                    ],
                                    "College of Arts and Sciences": [
                                        "BS Psychology",
                                        "BS Biology"
                                    ],
                                    "College of Industrial Technology": [
                                        "BS Industrial Technology Major in Automotive Technology",
                                        "BS Industrial Technology Major in Architectural Drafting",
                                        "BS Industrial Technology Major in Electrical Technology",
                                        "BS Industrial Technology Major in Electronics Technology",
                                        "BS Industrial Technology Major in Food & Beverage Preparation and Service Management Technology",
                                        "BS Industrial Technology Major in Heating, Ventilating, Air-Conditioning & Refrigeration Technology"
                                    ],
                                    "College of Criminal Justice Education": [
                                        "BS Criminology"
                                    ],
                                    "College of Hospitality Management and Tourism": [
                                        "BS Hospitality Management",
                                        "BS Tourism Management"
                                    ]
                                };
                    
                                const colleges = Object.keys(collegeCourses).map(collegeName => ({
                                    name: collegeName,
                                    courses: collegeCourses[collegeName]
                                }));
                    
                                for (const college of colleges) {
                                    const worksheet = workbook.addWorksheet(this.getCollegeAbbreviation(college.name));
                                    
                                    const headerRow1 = worksheet.addRow([]);
                                    const headerRow2 = worksheet.addRow([]);
                    
                                    headerRow1.getCell(7).value = 'Status of Employment';
                                    worksheet.mergeCells(`G${headerRow1.number}:I${headerRow1.number}`);
                                    headerRow2.getCell(7).value = 'Category';
                                    headerRow2.getCell(8).value = 'TOTAL';
                                    headerRow2.getCell(9).value = '';
                    
                                    headerRow1.getCell(10).value = 'Employment Sector';
                                    worksheet.mergeCells(`J${headerRow1.number}:L${headerRow1.number}`);
                                    headerRow2.getCell(10).value = 'Category';
                                    headerRow2.getCell(11).value = 'TOTAL';
                                    headerRow2.getCell(12).value = '';
                    
                                    headerRow1.getCell(13).value = 'Location of Employment';
                                    worksheet.mergeCells(`M${headerRow1.number}:O${headerRow1.number}`);
                                    headerRow2.getCell(13).value = 'Category';
                                    headerRow2.getCell(14).value = 'TOTAL';
                                    headerRow2.getCell(15).value = '';
                    
                                    headerRow1.eachCell(cell => {
                                        if (cell.value) cell.style = styles.header;
                                    });
                                    headerRow2.eachCell(cell => {
                                        if (cell.value) cell.style = styles.subHeader;
                                    });
                    
                                    for (const course of college.courses) {
                                        const row = worksheet.addRow([]);
                                        row.getCell(7).value = course;
                                        
                                        const statusData = data.employment_status_summary.find(row => row.Course === course && row.College === '');
                                        if (statusData) {
                                            const statuses = ['Regular', 'Probational', 'Contractual'];
                                            let mostCommonStatus = 'Contractual';
                                            let maxCount = 0;
                                            
                                            statuses.forEach(status => {
                                                const count = parseInt(statusData[status]) || 0;
                                                if (count > maxCount) {
                                                    maxCount = count;
                                                    mostCommonStatus = status;
                                                }
                                            });
                                            
                                            const totalStatus = statuses.reduce((sum, status) => sum + (parseInt(statusData[status]) || 0), 0);
                                            
                                            row.getCell(8).value = mostCommonStatus;
                                            row.getCell(9).value = totalStatus;
                                        } else {
                                            row.getCell(8).value = 'Contractual';
                                            row.getCell(9).value = 0;
                                        }
                                        
                                        const sectorData = data.employment_sector_summary.find(row => row.Course === course && row.College === '');
                                        if (sectorData) {
                                            const sectors = ['Government', 'Private', 'Self-employed'];
                                            let mostCommonSector = 'Private';
                                            let maxCount = 0;
                                            
                                            sectors.forEach(sector => {
                                                const count = parseInt(sectorData[sector]) || 0;
                                                if (count > maxCount) {
                                                    maxCount = count;
                                                    mostCommonSector = sector;
                                                }
                                            });
                                            
                                            const totalSector = sectors.reduce((sum, sector) => sum + (parseInt(sectorData[sector]) || 0), 0);
                                            
                                            row.getCell(11).value = mostCommonSector;
                                            row.getCell(12).value = totalSector;
                                        } else {
                                            row.getCell(11).value = 'Private';
                                            row.getCell(12).value = 0;
                                        }
                                        
                                        const locationData = data.location_summary.find(row => row.Course === course && row.College === '');
                                        if (locationData) {
                                            const locations = ['Local', 'Abroad'];
                                            let mostCommonLocation = 'Local';
                                            let maxCount = 0;
                                            
                                            locations.forEach(location => {
                                                const count = parseInt(locationData[location]) || 0;
                                                if (count > maxCount) {
                                                    maxCount = count;
                                                    mostCommonLocation = location;
                                                }
                                            });
                                            
                                            const totalLocation = locations.reduce((sum, location) => sum + (parseInt(locationData[location]) || 0), 0);
                                            
                                            row.getCell(14).value = mostCommonLocation;
                                            row.getCell(15).value = totalLocation;
                                        } else {
                                            row.getCell(14).value = 'Local';
                                            row.getCell(15).value = 0;
                                        }
                    
                                        for (let i = 7; i <= 15; i++) {
                                            const cell = row.getCell(i);
                                            cell.style = {
                                                ...styles.dataRow,
                                                border: styles.border
                                            };
                                        }
                                    }
                    
                                    worksheet.addRow([]);
                                    worksheet.addRow([]);
                    
                                    const graduatesHeader = worksheet.addRow([
                                        'Campus', 'Program Name', 'Name of Graduates', 'Gender', 
                                        'Date of Graduation', 'Date Hired for Current Job', 'CTR',
                                        'Status of Employment prior to graduation',
                                        'Status of Employment after graduation',
                                        'Sector (Private/Government)',
                                        'Location of Employment (Local/Abroad)',
                                        'Average Monthly Income',
                                        'Company/Organization',
                                        '', 'Relevance of Employment', '',
                                        '', '', 'Personal Details', '', ''
                                    ]);
                    
                                    const subHeaders = worksheet.addRow([
                                        '', '', '', '', '', '', '', '', '', '', '', '', '',
                                        'Employed-Aligned to their Program',
                                        'Self-Employed Graduates (1)',
                                        'Enrolled in Further Studies (1)',
                                        'Contact Number',
                                        'Email Address',
                                        'Civil Status',
                                        'Birthday',
                                        'Home Address'
                                    ]);
                    
                                    graduatesHeader.eachCell(cell => {
                                        cell.style = {
                                            ...styles.header,
                                            alignment: { ...styles.header.alignment, horizontal: 'center' }
                                        };
                                    });
                    
                                    subHeaders.eachCell(cell => {
                                        cell.style = {
                                            ...styles.subHeader,
                                            alignment: { ...styles.subHeader.alignment, horizontal: 'center' }
                                        };
                                    });
                    
                                    const graduatesByCourse = {};
                                    if (data.detailed_employment && Array.isArray(data.detailed_employment)) {
                                        data.detailed_employment.forEach(row => {
                                            if (row['Section'] === 'Complete Details' && 
                                                row['Full Name'] && row['Full Name'].trim() !== '' &&
                                                row['Course'] && row['Course'].trim() !== '' &&
                                                row['College'] === college.name) {
                                                
                                                const course = row['Course'];
                                                if (!graduatesByCourse[course]) {
                                                    graduatesByCourse[course] = [];
                                                }
                                                graduatesByCourse[course].push(row);
                                            }
                                        });
                                    }
                    
                                    for (const course of Object.keys(graduatesByCourse)) {
                                        const courseHeader = worksheet.addRow(Array(21).fill(''));
                                        courseHeader.getCell(1).value = course;
                                        courseHeader.eachCell(cell => {
                                            cell.style = styles.courseHeader;
                                        });
                    
                                        for (const row of graduatesByCourse[course]) {
                                            const campus = row['College'] ? row['College'].split(' - ')[0] : 'SPCC';
                                            const graduationDate = row['Year Graduated'] ? `July 5, ${row['Year Graduated']}` : '';
                                            const hiringDate = row['Start Date'] ? new Date(row['Start Date']).getFullYear().toString() : '';
                                            const ctr = (row['Employment Status'] && row['Employment Status'] !== 'Unemployed') ? '1' : '';
                                            const statusPrior = 'Student';
                                            let statusAfter = '';
                                            
                                            if (row['Employment Status']) {
                                                switch(row['Employment Status']) {
                                                    case 'Regular': statusAfter = 'Regular'; break;
                                                    case 'Probational': statusAfter = 'Probationary'; break;
                                                    case 'Contractual': statusAfter = 'Contractual'; break;
                                                    default: statusAfter = 'Unemployed';
                                                }
                                            }
                                            
                                            const sector = row['Employment Sector'] || '';
                                            const location = row['Location of Work'] || '';
                                            const income = '';
                                            const employedAligned = row['Job Related'] === 'Yes' ? 'Matched' : 'Mismatched';
                                            const selfEmployed = '';
                                            const furtherStudies = '';
                                            const contactNumber = row['Contact'] || '';
                                            const emailAddress = row['Email'] || '';
                                            const civilStatus = row['Civil Status'] || '';
                                            
                                            const birthday = row['Birthdate'] ? 
                                                new Date(row['Birthdate']).toLocaleDateString('en-US', {
                                                    month: '2-digit',
                                                    day: '2-digit',
                                                    year: 'numeric'
                                                }) : '';
                                            
                                            const homeAddress = row['Address'] || '';
                                            
                                            const gradRow = worksheet.addRow([
                                                campus,
                                                row['Course'] || '',
                                                row['Full Name'] || '',
                                                row['Gender'] || '',
                                                graduationDate,
                                                hiringDate,
                                                ctr,
                                                statusPrior,
                                                statusAfter,
                                                sector,
                                                location,
                                                income,
                                                row['Company'] || '',
                                                employedAligned,
                                                selfEmployed,
                                                furtherStudies,
                                                contactNumber,
                                                emailAddress,
                                                civilStatus,
                                                birthday,
                                                homeAddress
                                            ]);
                    
                                            gradRow.eachCell(cell => {
                                                cell.style = {
                                                    ...styles.dataRow,
                                                    border: styles.border
                                                };
                                            });
                                        }
                    
                                        worksheet.addRow(Array(21).fill(''));
                                    }
                    
                                    worksheet.columns = [
                                        { width: 25 }, { width: 25 }, { width: 30 }, { width: 10 },
                                        { width: 20 }, { width: 20 }, { width: 25 }, { width: 25 },
                                        { width: 25 }, { width: 20 }, { width: 25 }, { width: 20 },
                                        { width: 30 }, { width: 25 }, { width: 25 }, { width: 25 },
                                        { width: 15 }, { width: 30 }, { width: 15 }, { width: 15 },
                                        { width: 50 }
                                    ];
                    
                                    worksheet.views = [
                                        { state: 'frozen', xSplit: 0, ySplit: 2 }
                                    ];
                                }
                    
                                const buffer = await workbook.xlsx.writeBuffer();
                                saveAs(new Blob([buffer]), `detailed_employment_${new Date().toISOString().split('T')[0]}.xlsx`);
                                this.showNotification('Excel report exported successfully!', 'success');
                            }
                        } catch (error) {
                            console.error('Export error:', error);
                            this.showNotification('Failed to export detailed employment report', 'error');
                        }
                    },
                    
                    getCollegeAbbreviation(collegeName) {
                        const abbreviations = {
                            "College of Arts and Sciences": "CAS",
                            "College of Business Administration": "CBAA",
                            "College of Criminal Justice Education": "CCJE",
                            "College of Computer Studies": "CCS",
                            "College of Hospitality Management and Tourism": "CHMT",
                            "College of Industrial Technology": "CIT",
                            "College of Engineering": "COE",
                            "College of Education": "CTE"
                        };
                        return abbreviations[collegeName] || collegeName.substring(0, 3).toUpperCase();
                    },
                    
                    async exportIndustryAnalysis(format) {
                        try {
                            if (format === 'excel') {
                                const response = await fetch('functions/fetch_all_report_data.php');
                                const data = await response.json();
                                
                                if (!data.success) {
                                    throw new Error(data.error || 'Failed to fetch data');
                                }
                                
                                const workbook = new ExcelJS.Workbook();
                                
                                const styles = {
                                    header1: {
                                        fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: '4F46E5' } },
                                        font: { bold: true, color: { argb: 'FFFFFF' } },
                                        alignment: { horizontal: 'center', vertical: 'middle', wrapText: true }
                                    },
                                    header2: {
                                        fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: '93C5FD' } },
                                        font: { bold: true },
                                        alignment: { horizontal: 'center', vertical: 'middle' }
                                    },
                                    dataRow: {
                                        font: { color: { argb: '000000' } },
                                        alignment: { vertical: 'middle' },
                                        border: {
                                            top: { style: 'thin' },
                                            left: { style: 'thin' },
                                            bottom: { style: 'thin' },
                                            right: { style: 'thin' }
                                        }
                                    }
                                };
                    
                                const collegeCourses = {
                                    "College of Computer Studies": [
                                        "BS Information Technology",
                                        "BS Computer Science"
                                    ],
                                    "College of Engineering": [
                                        "BS Electronics Engineering",
                                        "BS Electrical Engineering",
                                        "BS Computer Engineering"
                                    ],
                                    "College of Business Administration": [
                                        "BS Office Administration",
                                        "BS Business Administration Major in Financial Management",
                                        "BS Business Administration Major in Marketing Management",
                                        "BS Accountancy"
                                    ],
                                    "College of Education": [
                                        "BS Elementary Education",
                                        "BS Physical Education",
                                        "BS Secondary Education Major in English",
                                        "BS Secondary Education Major in Filipino",
                                        "BS Secondary Education Major in Mathematics",
                                        "BS Secondary Education Major in Science",
                                        "BS Secondary Education Major in Social Studies",
                                        "BS Technology and Livelihood Education Major in Home Economics",
                                        "BS Technical-Vocational Teacher Education Major in Electrical Technology",
                                        "BS Technical-Vocational Teacher Education Major in Electronics Technology",
                                        "BS Technical-Vocational Teacher Education Major in Food & Service Management",
                                        "BS Technical-Vocational Teacher Education Major in Garments, Fashion & Design"
                                    ],
                                    "College of Arts and Sciences": [
                                        "BS Psychology",
                                        "BS Biology"
                                    ],
                                    "College of Industrial Technology": [
                                        "BS Industrial Technology Major in Automotive Technology",
                                        "BS Industrial Technology Major in Architectural Drafting",
                                        "BS Industrial Technology Major in Electrical Technology",
                                        "BS Industrial Technology Major in Electronics Technology",
                                        "BS Industrial Technology Major in Food & Beverage Preparation and Service Management Technology",
                                        "BS Industrial Technology Major in Heating, Ventilating, Air-Conditioning & Refrigeration Technology"
                                    ],
                                    "College of Criminal Justice Education": [
                                        "BS Criminology"
                                    ],
                                    "College of Hospitality Management and Tourism": [
                                        "BS Hospitality Management",
                                        "BS Tourism Management"
                                    ]
                                };
                    
                                const colleges = Object.keys(collegeCourses).map(collegeName => ({
                                    name: collegeName,
                                    courses: collegeCourses[collegeName]
                                }));
                    
                                const industryCategories = [...new Set(data.industry_analysis.map(row => row.Industry))];
                    
                                for (const college of colleges) {
                                    const worksheet = workbook.addWorksheet(this.getCollegeAbbreviation(college.name));
                    
                                    const headerRow1 = worksheet.addRow(['Nature of Work/Industry']);
                                    const headerRow2 = worksheet.addRow(['Nature of Work/Industry']);
                    
                                    college.courses.forEach(course => {
                                        const startCol = headerRow1.actualCellCount + 1;
                                        
                                        headerRow1.getCell(startCol).value = course;
                                        worksheet.mergeCells(headerRow1.number, startCol, headerRow1.number, startCol + 2);
                                        
                                        headerRow2.getCell(startCol).value = 'MALE';
                                        headerRow2.getCell(startCol + 1).value = 'FEMALE';
                                        headerRow2.getCell(startCol + 2).value = 'TOTAL';
                                    });
                    
                                    headerRow1.eachCell(cell => {
                                        cell.style = styles.header1;
                                    });
                                    headerRow2.eachCell(cell => {
                                        cell.style = styles.header2;
                                    });
                    
                                    industryCategories.forEach(industry => {
                                        const rowValues = [industry];
                                        
                                        college.courses.forEach(course => {
                                            const courseData = data.industry_analysis.find(row => row.Industry === industry);
                                            
                                            if (courseData) {
                                                const maleKey = course + ' - Male';
                                                const femaleKey = course + ' - Female';
                                                const totalKey = course + ' - Total';
                                                
                                                rowValues.push(
                                                    courseData[maleKey] || 0,
                                                    courseData[femaleKey] || 0,
                                                    courseData[totalKey] || 0
                                                );
                                            } else {
                                                rowValues.push(0, 0, 0);
                                            }
                                        });
                                        
                                        const row = worksheet.addRow(rowValues);
                                        row.eachCell(cell => {
                                            cell.style = styles.dataRow;
                                        });
                                    });
                    
                                    worksheet.columns = [
                                        { width: 50 },
                                        ...Array(college.courses.length * 3).fill().map(() => ({ width: 12 }))
                                    ];
                                }
                    
                                const buffer = await workbook.xlsx.writeBuffer();
                                saveAs(new Blob([buffer]), `industry_analysis_${new Date().toISOString().split('T')[0]}.xlsx`);
                                this.showNotification('Excel report exported successfully!', 'success');
                            }
                        } catch (error) {
                            console.error('Export error:', error);
                            this.showNotification('Failed to export report', 'error');
                        }
                    },

                    // Helper function to get college abbreviation for tab names
                    getCollegeAbbreviation(collegeName) {
                        const abbreviations = {
                            "College of Arts and Sciences": "CAS",
                            "College of Business Administration": "CBAA",
                            "College of Criminal Justice Education": "CCJE",
                            "College of Computer Studies": "CCS",
                            "College of Hospitality Management and Tourism": "CHMT",
                            "College of Industrial Technology": "CIT",
                            "College of Engineering": "COE",
                            "College of Education": "CTE"
                        };
                        return abbreviations[collegeName] || collegeName.substring(0, 3).toUpperCase();
                    },
                    // Helper function to find college for a course
                    findCollegeForCourse(employmentSummary, courseName) {
                        // Find the college that contains this course
                        for (let i = 0; i < employmentSummary.length; i++) {
                            if (employmentSummary[i].is_header) {
                                // This is a college header, check if the next course belongs to it
                                const collegeName = employmentSummary[i].college;
                                // Look for the course in the next rows until we hit another header
                                for (let j = i + 1; j < employmentSummary.length; j++) {
                                    if (employmentSummary[j].is_header) {
                                        break; // Found another college header, stop searching
                                    }
                                    if (employmentSummary[j].course === courseName) {
                                        return collegeName;
                                    }
                                }
                            }
                        }
                        return 'Unknown College';
                    },
                    // Helper function to apply styling to Excel worksheets - match image format
                    applyExcelStyling(worksheet) {
                        const headerRange = XLSX.utils.decode_range(worksheet['!ref']);
                        
                        // Apply styling to headers (first row) - colored headers
                        for (let col = headerRange.s.c; col <= headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            
                            // Set different colors for different columns
                            let headerColor = "4472C4"; // Default blue
                            let textColor = "FFFFFF";   // White text
                            
                            // Column-specific colors
                            if (col === 0) { // Logo column
                                headerColor = "5B9BD5"; // Lighter blue
                            } else if (col === 1) { // Company Name column
                                headerColor = "4472C4"; // Blue
                            } else if (col === 2) { // Location column
                                headerColor = "70AD47"; // Green
                            } else if (col === 3) { // Contact Email column
                                headerColor = "FFC000"; // Orange
                            } else if (col === 4) { // Industry Type column
                                headerColor = "5B9BD5"; // Light Blue
                            } else if (col === 5) { // Nature of Business column
                                headerColor = "A5A5A5"; // Gray
                            } else if (col === 6) { // Accreditation Status column
                                headerColor = "ED7D31"; // Dark Orange
                            } else if (col === 7) { // Status column
                                headerColor = "7030A0"; // Purple
                            } else if (col === 8) { // Actions column
                                headerColor = "FF0000"; // Red
                            }
                            
                            worksheet[cellAddress].s = {
                                fill: { fgColor: { rgb: headerColor } },
                                font: { bold: true, color: { rgb: textColor } },
                                alignment: { horizontal: "center", vertical: "center" }
                            };
                        }

                        // Apply styling to data rows
                        for (let row = 1; row <= headerRange.e.r; row++) {
                            for (let col = 0; col <= headerRange.e.c; col++) {
                                const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                if (worksheet[cellAddress]) {
                                    // Status column formatting
                                    if (col === 7 && worksheet[cellAddress].v) {
                                        const status = worksheet[cellAddress].v.toString().toLowerCase();
                                        let bgColor = "FFFFFF"; // Default white
                                        
                                        if (status === 'active') {
                                            bgColor = "C6EFCE"; // Light green
                                            textColor = "006100"; // Dark green text
                                        } else if (status === 'pending') {
                                            bgColor = "FFEB9C"; // Light yellow
                                            textColor = "9C6500"; // Dark yellow text
                                        } else if (status === 'rejected') {
                                            bgColor = "FFC7CE"; // Light red
                                            textColor = "9C0006"; // Dark red text
                                        }
                                        
                                        worksheet[cellAddress].s = {
                                            fill: { fgColor: { rgb: bgColor } },
                                            font: { bold: true, color: { rgb: textColor } },
                                            alignment: { horizontal: "center", vertical: "center" }
                                        };
                                    } else {
                                        // Alternate row coloring
                                        const bgColor = row % 2 === 0 ? "FFFFFF" : "F2F2F2"; // White or light gray
                                        worksheet[cellAddress].s = {
                                            fill: { fgColor: { rgb: bgColor } },
                                            font: { bold: false },
                                            alignment: { horizontal: "center", vertical: "center" }
                                        };
                                    }
                                }
                            }
                        }
                        
                        return worksheet;
                    },
                    // Enhanced helper function to apply styling for industry analysis format with borders
                    applyIndustryAnalysisStyling(worksheet, courseCount) {
                        const headerRange = XLSX.utils.decode_range(worksheet['!ref']);
                        
                        // Apply enhanced styling to first header row (course names spanning columns) with borders
                        for (let col = 0; col < headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            
                            if (col === 0) {
                                // First column header (Nature of Work/Industry)
                                worksheet[cellAddress].s = {
                                    fill: { fgColor: { rgb: "8B4513" } }, // Dark reddish-brown background
                                    font: { 
                                        color: { rgb: "FFFFFF" }, 
                                        bold: true,
                                        sz: 12 // Font size
                                    }, // White bold text
                                    alignment: { horizontal: "center", vertical: "center" },
                                    border: {
                                        top: { style: "thin", color: { rgb: "000000" } },
                                        bottom: { style: "thin", color: { rgb: "000000" } },
                                        left: { style: "thin", color: { rgb: "000000" } },
                                        right: { style: "thin", color: { rgb: "000000" } }
                                    }
                                };
                            } else {
                                // Course headers spanning 3 columns each
                                const courseIndex = Math.floor((col - 1) / 3);
                                if ((col - 1) % 3 === 0) {
                                    // First column of each course group
                                    worksheet[cellAddress].s = {
                                        fill: { fgColor: { rgb: "8B4513" } }, // Dark reddish-brown background
                                        font: { 
                                            color: { rgb: "FFFFFF" }, 
                                            bold: true,
                                            sz: 12 // Font size
                                        }, // White bold text
                                        alignment: { horizontal: "center", vertical: "center" },
                                        border: {
                                            top: { style: "thin", color: { rgb: "000000" } },
                                            bottom: { style: "thin", color: { rgb: "000000" } },
                                            left: { style: "thin", color: { rgb: "000000" } },
                                            right: { style: "thin", color: { rgb: "000000" } }
                                        }
                                    };
                                } else {
                                    // Empty cells in course header span
                                    worksheet[cellAddress].s = {
                                        fill: { fgColor: { rgb: "8B4513" } }, // Dark reddish-brown background
                                        font: { 
                                            color: { rgb: "FFFFFF" }, 
                                            bold: true,
                                            sz: 12 // Font size
                                        }, // White bold text
                                        alignment: { horizontal: "center", vertical: "center" },
                                        border: {
                                            top: { style: "thin", color: { rgb: "000000" } },
                                            bottom: { style: "thin", color: { rgb: "000000" } },
                                            left: { style: "thin", color: { rgb: "000000" } },
                                            right: { style: "thin", color: { rgb: "000000" } }
                                        }
                                    };
                                }
                            }
                        }

                        // Apply enhanced styling to second header row (MALE, FEMALE, TOTAL) with borders
                        for (let col = 0; col < headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 1, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            
                            if (col === 0) {
                                // First column header (Nature of Work/Industry)
                                worksheet[cellAddress].s = {
                                    fill: { fgColor: { rgb: "8B4513" } }, // Dark reddish-brown background
                                    font: { 
                                        color: { rgb: "FFFFFF" }, 
                                        bold: true,
                                        sz: 11 // Font size
                                    }, // White bold text
                                    alignment: { horizontal: "center", vertical: "center" },
                                    border: {
                                        top: { style: "thin", color: { rgb: "000000" } },
                                        bottom: { style: "thin", color: { rgb: "000000" } },
                                        left: { style: "thin", color: { rgb: "000000" } },
                                        right: { style: "thin", color: { rgb: "000000" } }
                                    }
                                };
                            } else {
                                // MALE, FEMALE, TOTAL headers
                                worksheet[cellAddress].s = {
                                    font: { 
                                        bold: true,
                                        sz: 11 // Font size
                                    },
                                    alignment: { horizontal: "center", vertical: "center" },
                                    border: {
                                        top: { style: "thin", color: { rgb: "000000" } },
                                        bottom: { style: "thin", color: { rgb: "000000" } },
                                        left: { style: "thin", color: { rgb: "000000" } },
                                        right: { style: "thin", color: { rgb: "000000" } }
                                    }
                                };
                            }
                        }

                        // Apply enhanced styling to data rows with borders
                        for (let row = 2; row <= headerRange.e.r; row++) {
                            for (let col = 0; col < headerRange.e.c; col++) {
                                const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                if (worksheet[cellAddress]) {
                                    if (col === 0) {
                                        // First column (Industry categories)
                                        worksheet[cellAddress].s = {
                                            font: { 
                                                bold: false,
                                                sz: 10 // Font size
                                            },
                                            alignment: { horizontal: "left", vertical: "center" },
                                            border: {
                                                top: { style: "thin", color: { rgb: "000000" } },
                                                bottom: { style: "thin", color: { rgb: "000000" } },
                                                left: { style: "thin", color: { rgb: "000000" } },
                                                right: { style: "thin", color: { rgb: "000000" } }
                                            }
                                        };
                                    } else {
                                        // Data cells - alternate between light blue and light pink
                                        const courseIndex = Math.floor((col - 1) / 3);
                                        const subColIndex = (col - 1) % 3; // 0=MALE, 1=FEMALE, 2=TOTAL
                                        
                                        if (subColIndex === 0 || subColIndex === 1) {
                                            // MALE and FEMALE columns - light blue background
                                            worksheet[cellAddress].s = {
                                                fill: { fgColor: { rgb: "E6F3FF" } }, // Light blue background
                                                font: { 
                                                    bold: false,
                                                    sz: 10 // Font size
                                                },
                                                alignment: { horizontal: "center", vertical: "center" },
                                                border: {
                                                    top: { style: "thin", color: { rgb: "000000" } },
                                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                                    left: { style: "thin", color: { rgb: "000000" } },
                                                    right: { style: "thin", color: { rgb: "000000" } }
                                                }
                                            };
                                        } else {
                                            // TOTAL columns - light pink background
                                            worksheet[cellAddress].s = {
                                                fill: { fgColor: { rgb: "FFE6E6" } }, // Light pink background
                                                font: { 
                                                    bold: false,
                                                    sz: 10 // Font size
                                                },
                                                alignment: { horizontal: "center", vertical: "center" },
                                                border: {
                                                    top: { style: "thin", color: { rgb: "000000" } },
                                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                                    left: { style: "thin", color: { rgb: "000000" } },
                                                    right: { style: "thin", color: { rgb: "000000" } }
                                                }
                                            };
                                        }
                                    }
                                }
                            }
                        }
                        
                        return worksheet;
                    },
                    // Enhanced helper function to apply styling for graduates per program with borders
                    applyGraduatesPerProgramStyling(worksheet) {
                        const headerRange = XLSX.utils.decode_range(worksheet['!ref']);
                        
                        // Apply enhanced styling to header row (first row) with borders
                        for (let col = 0; col < headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            worksheet[cellAddress].s = {
                                fill: { 
                                    patternType: "solid",
                                    fgColor: { rgb: "4472C4" } // Blue background
                                },
                                font: { 
                                    bold: true,
                                    color: { rgb: "FFFFFF" }, // White text
                                    sz: 11 // Font size
                                },
                                alignment: { horizontal: "center", vertical: "center" },
                                border: {
                                    top: { style: "thin", color: { rgb: "000000" } },
                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                    left: { style: "thin", color: { rgb: "000000" } },
                                    right: { style: "thin", color: { rgb: "000000" } }
                                }
                            };
                        }

                        // Apply enhanced styling to yellow filter row (second row) with borders
                        for (let col = 0; col < headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 1, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            worksheet[cellAddress].s = {
                                fill: { fgColor: { rgb: "FFFF00" } }, // Yellow background
                                font: { 
                                    bold: true,
                                    sz: 11 // Font size
                                },
                                alignment: { horizontal: "center", vertical: "center" },
                                border: {
                                    top: { style: "thin", color: { rgb: "000000" } },
                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                    left: { style: "thin", color: { rgb: "000000" } },
                                    right: { style: "thin", color: { rgb: "000000" } }
                                }
                            };
                        }

                        // Apply enhanced styling to data rows with borders
                        for (let row = 2; row <= headerRange.e.r; row++) {
                            for (let col = 0; col < headerRange.e.c; col++) {
                                const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                if (worksheet[cellAddress]) {
                                    if (col === 1) { // Program Name column - light blue background
                                        worksheet[cellAddress].s = {
                                            fill: { fgColor: { rgb: "E6F3FF" } }, // Light blue background
                                            font: { 
                                                bold: false,
                                                sz: 10 // Font size
                                            },
                                            alignment: { horizontal: "left", vertical: "center" },
                                            border: {
                                                top: { style: "thin", color: { rgb: "000000" } },
                                                bottom: { style: "thin", color: { rgb: "000000" } },
                                                left: { style: "thin", color: { rgb: "000000" } },
                                                right: { style: "thin", color: { rgb: "000000" } }
                                            }
                                        };
                                    } else {
                                        // Apply alternating row colors for other columns
                                        if (row % 2 === 0) { // Even rows (yellow filter row is 1)
                                            worksheet[cellAddress].s = {
                                                fill: { fgColor: { rgb: "F0F0F0" } }, // Light gray background
                                                font: { 
                                                    bold: false,
                                                    sz: 10 // Font size
                                                },
                                                alignment: { horizontal: "center", vertical: "center" },
                                                border: {
                                                    top: { style: "thin", color: { rgb: "000000" } },
                                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                                    left: { style: "thin", color: { rgb: "000000" } },
                                                    right: { style: "thin", color: { rgb: "000000" } }
                                                }
                                            };
                                        } else { // Odd rows (data rows)
                                            worksheet[cellAddress].s = {
                                                font: { 
                                                    bold: false,
                                                    sz: 10 // Font size
                                                },
                                                alignment: { horizontal: "center", vertical: "center" },
                                                border: {
                                                    top: { style: "thin", color: { rgb: "000000" } },
                                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                                    left: { style: "thin", color: { rgb: "000000" } },
                                                    right: { style: "thin", color: { rgb: "000000" } }
                                                }
                                            };
                                        }
                                    }
                                }
                            }
                        }
                        
                        return worksheet;
                    },
                    // Enhanced helper function to apply styling for company details with borders
                    applyCompanyDetailsStyling(worksheet) {
                        const headerRange = XLSX.utils.decode_range(worksheet['!ref']);
                        
                        // Apply enhanced styling to header row (first row) with borders
                        for (let col = 0; col < headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            worksheet[cellAddress].s = {
                                fill: { 
                                    patternType: "solid",
                                    fgColor: { rgb: "4472C4" } // Blue background
                                },
                                font: { 
                                    bold: true,
                                    color: { rgb: "FFFFFF" }, // White text
                                    sz: 11 // Font size
                                },
                                alignment: { horizontal: "center", vertical: "center" },
                                border: {
                                    top: { style: "thin", color: { rgb: "000000" } },
                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                    left: { style: "thin", color: { rgb: "000000" } },
                                    right: { style: "thin", color: { rgb: "000000" } }
                                }
                            };
                        }

                        // Apply enhanced styling to yellow highlight row (second row) with borders
                        for (let col = 0; col < headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 1, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            worksheet[cellAddress].s = {
                                fill: { fgColor: { rgb: "FFFF00" } }, // Yellow background
                                font: { 
                                    bold: true,
                                    sz: 11 // Font size
                                },
                                alignment: { horizontal: "center", vertical: "center" },
                                border: {
                                    top: { style: "thin", color: { rgb: "000000" } },
                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                    left: { style: "thin", color: { rgb: "000000" } },
                                    right: { style: "thin", color: { rgb: "000000" } }
                                }
                            };
                        }

                        // Apply enhanced styling to data rows with borders
                        for (let row = 2; row <= headerRange.e.r; row++) {
                            for (let col = 0; col < headerRange.e.c; col++) {
                                const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                if (worksheet[cellAddress]) {
                                    if (col === 3) { // Employed-Aligned column
                                        const cellValue = worksheet[cellAddress].v;
                                        if (cellValue === 'Matched') {
                                            worksheet[cellAddress].s = {
                                                fill: { fgColor: { rgb: "FF0000" } }, // Red background for Matched
                                                font: { 
                                                    bold: true, 
                                                    color: { rgb: "FFFFFF" },
                                                    sz: 10 // Font size
                                                },
                                                alignment: { horizontal: "center", vertical: "center" },
                                                border: {
                                                    top: { style: "thin", color: { rgb: "000000" } },
                                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                                    left: { style: "thin", color: { rgb: "000000" } },
                                                    right: { style: "thin", color: { rgb: "000000" } }
                                                }
                                            };
                                        } else if (cellValue === 'Mismatched') {
                                            worksheet[cellAddress].s = {
                                                fill: { fgColor: { rgb: "00FF00" } }, // Green background for Mismatched
                                                font: { 
                                                    bold: true, 
                                                    color: { rgb: "000000" },
                                                    sz: 10 // Font size
                                                },
                                                alignment: { horizontal: "center", vertical: "center" },
                                                border: {
                                                    top: { style: "thin", color: { rgb: "000000" } },
                                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                                    left: { style: "thin", color: { rgb: "000000" } },
                                                    right: { style: "thin", color: { rgb: "000000" } }
                                                }
                                            };
                                        } else {
                                            worksheet[cellAddress].s = {
                                                font: { 
                                                    bold: false,
                                                    sz: 10 // Font size
                                                },
                                                alignment: { horizontal: "center", vertical: "center" },
                                                border: {
                                                    top: { style: "thin", color: { rgb: "000000" } },
                                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                                    left: { style: "thin", color: { rgb: "000000" } },
                                                    right: { style: "thin", color: { rgb: "000000" } }
                                                }
                                            };
                                        }
                                    } else {
                                        // Light blue-green background for data rows
                                        worksheet[cellAddress].s = {
                                            fill: { fgColor: { rgb: "E6F3FF" } }, // Light blue-green background
                                            font: { 
                                                bold: false,
                                                sz: 10 // Font size
                                            },
                                            alignment: { horizontal: "left", vertical: "center" },
                                            border: {
                                                top: { style: "thin", color: { rgb: "000000" } },
                                                bottom: { style: "thin", color: { rgb: "000000" } },
                                                left: { style: "thin", color: { rgb: "000000" } },
                                                right: { style: "thin", color: { rgb: "000000" } }
                                            }
                                        };
                                    }
                                }
                            }
                        }
                        
                        return worksheet;
                    },
                    // Enhanced helper function to apply styling for personal details with borders
                    applyPersonalDetailsStyling(worksheet) {
                        const headerRange = XLSX.utils.decode_range(worksheet['!ref']);
                        
                        // Apply enhanced styling to header row (first row) with borders
                        for (let col = 0; col < headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            worksheet[cellAddress].s = {
                                fill: { fgColor: { rgb: "8B0000" } }, // Dark red background
                                font: { 
                                    bold: true, 
                                    color: { rgb: "FFFFFF" },
                                    sz: 11 // Font size
                                }, // White bold text
                                alignment: { horizontal: "center", vertical: "center" },
                                border: {
                                    top: { style: "thin", color: { rgb: "000000" } },
                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                    left: { style: "thin", color: { rgb: "000000" } },
                                    right: { style: "thin", color: { rgb: "000000" } }
                                }
                            };
                        }

                        // Apply enhanced styling to yellow filter row (second row) with borders
                        for (let col = 0; col < headerRange.e.c; col++) {
                            const cellAddress = XLSX.utils.encode_cell({ r: 1, c: col });
                            if (!worksheet[cellAddress]) {
                                worksheet[cellAddress] = { v: '', t: 's' };
                            }
                            worksheet[cellAddress].s = {
                                fill: { fgColor: { rgb: "FFFF00" } }, // Yellow background
                                font: { 
                                    bold: true,
                                    sz: 11 // Font size
                                },
                                alignment: { horizontal: "center", vertical: "center" },
                                border: {
                                    top: { style: "thin", color: { rgb: "000000" } },
                                    bottom: { style: "thin", color: { rgb: "000000" } },
                                    left: { style: "thin", color: { rgb: "000000" } },
                                    right: { style: "thin", color: { rgb: "000000" } }
                                }
                            };
                        }

                        // Apply enhanced styling to data rows with borders
                        for (let row = 2; row <= headerRange.e.r; row++) {
                            for (let col = 0; col < headerRange.e.c; col++) {
                                const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                                if (worksheet[cellAddress]) {
                                    if (col === 0) { // First column - light green background
                                        worksheet[cellAddress].s = {
                                            fill: { fgColor: { rgb: "E6FFE6" } }, // Light green background
                                            font: { 
                                                bold: false,
                                                sz: 10 // Font size
                                            },
                                            alignment: { horizontal: "center", vertical: "center" },
                                            border: {
                                                top: { style: "thin", color: { rgb: "000000" } },
                                                bottom: { style: "thin", color: { rgb: "000000" } },
                                                left: { style: "thin", color: { rgb: "000000" } },
                                                right: { style: "thin", color: { rgb: "000000" } }
                                            }
                                        };
                                    } else { // Other columns - light red/salmon background
                                        worksheet[cellAddress].s = {
                                            fill: { fgColor: { rgb: "FFE6E6" } }, // Light red/salmon background
                                            font: { 
                                                bold: false,
                                                sz: 10 // Font size
                                            },
                                            alignment: { horizontal: "left", vertical: "center" },
                                            border: {
                                                top: { style: "thin", color: { rgb: "000000" } },
                                                bottom: { style: "thin", color: { rgb: "000000" } },
                                                left: { style: "thin", color: { rgb: "000000" } },
                                                right: { style: "thin", color: { rgb: "000000" } }
                                            }
                                        };
                                    }
                                }
                            }
                        }
                        
                        return worksheet;
                    }
                }
            }).mount('#app');