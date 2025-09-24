const { createApp } = Vue;
createApp({
    data() {
        return {
            sidebarActive: window.innerWidth >= 768,
            companiesDropdownOpen: false,
            alumniDropdownOpen: false,
            profileDropdownOpen: false,
            darkMode: localStorage.getItem('darkMode') === 'true' || 
                     (localStorage.getItem('darkMode') === null && 
                      window.matchMedia('(prefers-color-scheme: dark)').matches),
            showLogoutModal: false,
            markers: [
                { lat: 14.1667, lng: 121.2167, title: 'Main Campus', alumni: 120 },
                { lat: 14.2775, lng: 121.4158, title: 'San Pablo City', alumni: 85 },
                { lat: 14.1833, lng: 121.3000, title: 'Santa Cruz', alumni: 65 },
                { lat: 13.9319, lng: 121.4233, title: 'Siniloan', alumni: 90 },
                { lat: 14.0333, lng: 121.3167, title: 'Los Baños', alumni: 45 }
            ],
            charts: {},
            chartInitialized: false,
            isMobile: window.innerWidth < 768,
            notifications: [],
            notificationId: 0,
            dashboardStats: null,
            drilldown: {
              active: false,
              type: '', // 'college', 'location', 'sector'
              label: '',
              data: null
            },
            geocodeCache: {},
            profile: {
                profile_pic: '',
                name: '',
            }
        }
    },
    mounted() {
        this.applyDarkMode();
        // Fetch dashboard stats and then initialize charts and map
        fetch('functions/fetch_dashboard_stats.php')
            .then(res => res.json())
            .then(data => {
                this.dashboardStats = data;
                setTimeout(() => {
                    this.initCharts();
                    this.initMap();
                    this.showLogoutModal = false; // Ensure modal is hidden on load
                }, 100);
            });
        fetch('functions/fetch_admin_details.php')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.profile) {
                    this.profile = data.profile;
                }
            });
        window.addEventListener('resize', this.handleResize);
    },
    watch: {
        darkMode(val) {
            this.applyDarkMode();
            this.reinitializeCharts();
        }
    },
    methods: {
        toggleSidebar() {
            this.sidebarActive = !this.sidebarActive;
            this.companiesDropdownOpen = false;
            this.alumniDropdownOpen = false;
        },
        handleNavClick() {
            if (this.isMobile) {
                this.sidebarActive = false;
            }
        },
        toggleProfileDropdown() {
            this.profileDropdownOpen = !this.profileDropdownOpen;
        },
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode.toString());
            this.applyDarkMode();
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
        reinitializeCharts() {
            if (this.chartInitialized) {
                this.destroyCharts();
            }
            this.initCharts();
        },
        destroyCharts() {
            Object.values(this.charts).forEach(chart => {
                if (chart && !chart._destroyed) {
                    chart.destroy();
                }
            });
            this.charts = {};
            this.chartInitialized = false;
        },
        initCharts() {
            if (this.chartInitialized) return;
            const textColor = this.darkMode ? '#e5e7eb' : '#374151';
            const gridColor = this.darkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            try {
                // Chart 1: Graduates and Employment per College
                const graduatesCtx = document.getElementById('graduatesChart');
                if (graduatesCtx && this.dashboardStats) {
                    const colleges = Object.keys(this.dashboardStats.graduates_per_college || {});
                    const graduates = colleges.map(c => this.dashboardStats.graduates_per_college[c].graduates);
                    const employed = colleges.map(c => this.dashboardStats.graduates_per_college[c].employed);
                    this.charts.graduates = new Chart(graduatesCtx, {
                        type: 'bar',
                        data: {
                            labels: colleges,
                            datasets: [
                                {
                                    label: 'Graduates',
                                    data: graduates,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Employed',
                                    data: employed,
                                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 0 },
                            scales: {
                                x: { grid: { color: gridColor }, ticks: { color: textColor } },
                                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } }
                            },
                            plugins: { legend: { labels: { color: textColor } } }
                        }
                    });
                    graduatesCtx.onclick = (evt) => {
                        const points = this.charts.graduates.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                        if (points.length) {
                            const idx = points[0].index;
                            const college = colleges[idx];
                            this.showDrilldown('college', college);
                        }
                    };
                }
                // Chart 2: Course-Work Alignment (Donut Chart)
                const alignmentCtx = document.getElementById('alignmentChart');
                if (alignmentCtx && this.dashboardStats && this.dashboardStats.course_work_alignment) {
                    const alignmentLabels = ['Highly Aligned', 'Moderately Aligned', 'Slightly Aligned', 'Not Aligned'];
                    // Aggregate total counts for each label across all courses
                    const totalCounts = alignmentLabels.map(label => {
                        return Object.values(this.dashboardStats.course_work_alignment).reduce((sum, course) => {
                            if (course.counts && typeof course.counts[label] === 'number') {
                                return sum + course.counts[label];
                            }
                            return sum;
                        }, 0);
                    });
                    this.charts.alignment = new Chart(alignmentCtx, {
                        type: 'doughnut',
                        data: {
                            labels: alignmentLabels,
                            datasets: [{
                                data: totalCounts,
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(255, 99, 132, 0.7)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { color: textColor } } }
                        }
                    });
                }
                // Chart 3: Employment Status per Program (force all status labels)
                const statusCtx = document.getElementById('employmentStatusChart');
                if (statusCtx && this.dashboardStats) {
                    const programs = Object.keys(this.dashboardStats.employment_status_per_program || {});
                    const statusLabels = ['Probational', 'Contractual', 'Regular', 'Self-employed', 'Unemployed'];
                    const datasets = statusLabels.map((status, i) => ({
                        label: status,
                        data: programs.map(p => (this.dashboardStats.employment_status_per_program[p] && typeof this.dashboardStats.employment_status_per_program[p][status] === 'number') ? this.dashboardStats.employment_status_per_program[p][status] : 0),
                        backgroundColor: [
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
                        ][i],
                        borderColor: [
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ][i],
                        borderWidth: 1
                    }));
                    this.charts.status = new Chart(statusCtx, {
                        type: 'bar',
                        data: {
                            labels: programs,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 0 },
                            scales: {
                                x: { grid: { color: gridColor }, ticks: { color: textColor } },
                                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } }
                            },
                            plugins: { legend: { labels: { color: textColor } } }
                        }
                    });
                }
                // Chart 4: Work Location Distribution
                const locationCtx = document.getElementById('locationChart');
                if (locationCtx && this.dashboardStats) {
                    const locLabels = ['Local', 'Abroad'];
                    const locData = locLabels.map(l => (this.dashboardStats.work_location_distribution && typeof this.dashboardStats.work_location_distribution[l] === 'number') ? this.dashboardStats.work_location_distribution[l] : 0);
                    this.charts.location = new Chart(locationCtx, {
                        type: 'pie',
                        data: {
                            labels: locLabels,
                            datasets: [{
                                data: locData,
                                backgroundColor: [
                                    'rgba(255, 159, 64, 0.7)',
                                    'rgba(255, 99, 132, 0.7)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 0 },
                            plugins: { legend: { position: 'bottom', labels: { color: textColor } } }
                        }
                    });
                    locationCtx.onclick = (evt) => {
                        const points = this.charts.location.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                        if (points.length) {
                            const idx = points[0].index;
                            const location = locLabels[idx];
                            this.showDrilldown('location', location);
                        }
                    };
                }
                // Chart 5: Employment Sector
                const sectorCtx = document.getElementById('sectorChart');
                if (sectorCtx && this.dashboardStats) {
                    const sectorLabels = ['Government', 'Private'];
                    const sectorData = sectorLabels.map(s => (this.dashboardStats.employment_sector_distribution && typeof this.dashboardStats.employment_sector_distribution[s] === 'number') ? this.dashboardStats.employment_sector_distribution[s] : 0);
                    this.charts.sector = new Chart(sectorCtx, {
                        type: 'polarArea',
                        data: {
                            labels: sectorLabels,
                            datasets: [{
                                data: sectorData,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 0 },
                            plugins: { legend: { position: 'bottom', labels: { color: textColor } } }
                        }
                    });
                    sectorCtx.onclick = (evt) => {
                        const points = this.charts.sector.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                        if (points.length) {
                            const idx = points[0].index;
                            const sector = sectorLabels[idx];
                            this.showDrilldown('sector', sector);
                        }
                    };
                }
                this.chartInitialized = true;
            } catch (e) {
                console.error('Chart initialization error:', e);
                this.chartInitialized = false;
            }
        },
        initMap() {
            console.log('initMap called');
            try {
                const map = L.map('alumniMap').setView([14.1667, 121.2167], 10);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                if (this.dashboardStats && this.dashboardStats.alumni_map) {
                    Object.entries(this.dashboardStats.alumni_map).forEach(([location, alumniList]) => {
                        this.geocodeLocation(location).then(({lat, lng}) => {
                            console.log('Adding pin:', location, lat, lng, alumniList);
                            const popupHtml = alumniList.map(a =>
                                `<div style='margin-bottom:10px;'>
                                    ${a.profile_pic ? `<div style='text-align:center;margin-bottom:4px;'><img src='${a.profile_pic}' style='width:48px;height:48px;object-fit:cover;border-radius:50%;border:2px solid #3b82f6;'></div>` : ''}
                                    <b>${a.name}</b><br>
                                    <span style='font-size:12px;'>${a.course} (${a.year_graduated})</span><br>
                                    <span style='font-size:12px;'>Status: <b>${a.status}</b></span><br>
                                    ${a.status === 'Employed' && a.work_details ? `
                                        <div style='margin-top:4px; padding-left:8px; border-left:2px solid #3b82f6;'>
                                            <span style='font-size:12px;'><b>Position:</b> ${a.work_details.title}</span><br>
                                            <span style='font-size:12px;'><b>Company:</b> ${a.work_details.company}</span><br>
                                            <span style='font-size:12px;'><b>From:</b> ${a.work_details.start_date || '-'} <b>To:</b> ${a.work_details.end_date || 'Present'}</span><br>
                                            <span style='font-size:12px;'><b>Description:</b> ${a.work_details.description || '-'}</span>
                                        </div>
                                    ` : ''}
                                </div>`
                            ).join('<hr style="margin:6px 0;">');
                            L.marker([lat, lng]).addTo(map)
                                .bindPopup(`<b>${location}</b><br><br>${popupHtml}`);
                });
                    });
                }
            } catch (e) {
                console.error('Map initialization error:', e);
            }
        },
        async geocodeLocation(location) {
            if (this.geocodeCache[location]) return this.geocodeCache[location];
            
            // Try different CORS proxies
            const proxies = [
                'https://api.allorigins.win/raw?url=',
                'https://corsproxy.io/?',
                'https://proxy.cors.sh/'
            ];
            
            const targetUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location + ', Philippines')}`;
            
            for (const proxy of proxies) {
                try {
                    const res = await fetch(proxy + encodeURIComponent(targetUrl));
                    
                    if (!res.ok) continue;
                    
                    const text = await res.text();
                    const data = JSON.parse(text);
                    
                    if (data && data.length > 0) {
                        const coords = { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
                        this.geocodeCache[location] = coords;
                        return coords;
                    }
                } catch (error) {
                    console.log(`Proxy ${proxy} failed, trying next...`);
                    continue;
                }
            }
            
            // Fallback if all proxies fail
            const fallback = { lat: 14.1667, lng: 121.2167 };
            this.geocodeCache[location] = fallback;
            return fallback;
        },
        confirmLogout() {
            this.showLogoutModal = true;
        },
        logout() {
            window.location.href = 'logout.php';
        },
        handleResize() {
            this.isMobile = window.innerWidth < 768;
            if (window.innerWidth >= 768) {
                this.sidebarActive = true;
            } else {
                this.sidebarActive = false;
            }
        },
        addNotification(type, title, message) {
            const id = this.notificationId++;
            this.notifications.push({ id, type, title, message });
            setTimeout(() => this.removeNotification(id), 5000); // Auto-dismiss after 5 seconds
        },
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        },
        async exportToExcel() {
            const data = {
                "General Insights": {
                    "Total Jobs": 12,
                    "Total Employers": 10,
                    "Total Applicants": 24,
                    "Total Alumni": 156
                },
                "Employment Status": {
                    "Probational": 35,
                    "Contractual": 25,
                    "Regular": 40,
                    "Self-employed": 15,
                    "Unemployed": 10
                },
                "Work Location": {
                    "Local": 75,
                    "Abroad": 25
                },
                "Employment Sector": {
                    "Government": 20,
                    "Private": 50,
                    "Non-profit": 10,
                    "Academe": 15,
                    "Others": 5
                },
                "Course-Work Alignment": {
                    "Highly Aligned": 45,
                    "Moderately Aligned": 30,
                    "Slightly Aligned": 15,
                    "Not Aligned": 10
                },
                "Graduates per College": {
                    "Engineering": 120,
                    "Business": 95,
                    "Arts": 80,
                    "Science": 65,
                    "Education": 110
                }
            };

            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.json_to_sheet(data);
            XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
            XLSX.writeFile(workbook, "LSPU_Employment_Insights.xlsx");
        },
        async exportToPDF() {
            const doc = new jsPDF();
            const tableColumn = ["Category", "Value"];
            const tableRows = [];

            Object.entries(data).forEach(([key, value]) => {
                if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
                    Object.entries(value).forEach(([subKey, subValue]) => {
                        tableRows.push([`${key} - ${subKey}`, subValue]);
                    });
                } else {
                    tableRows.push([key, value]);
                }
            });

            doc.autoTable({
                head: [tableColumn],
                body: tableRows
            });
            doc.save("LSPU_Employment_Insights.pdf");
        },
        showDrilldown(type, label) {
          this.drilldown.active = true;
          this.drilldown.type = type;
          this.drilldown.label = label;
          let courseData = null;
          if (type === 'college') {
            courseData = this.dashboardStats.courses_per_college && this.dashboardStats.courses_per_college[label];
          } else if (type === 'location') {
            courseData = this.dashboardStats.courses_per_location && this.dashboardStats.courses_per_location[label];
          } else if (type === 'sector') {
            courseData = this.dashboardStats.courses_per_sector && this.dashboardStats.courses_per_sector[label];
          }
          if (courseData && Object.keys(courseData).length > 0) {
            this.drilldown.data = {
              labels: Object.keys(courseData),
              graduates: Object.values(courseData).map(c => c.graduates),
              employed: Object.values(courseData).map(c => c.employed)
            };
          } else {
            this.drilldown.data = {
              labels: [],
              graduates: [],
              employed: []
            };
          }
          this.$nextTick(() => this.renderDrilldownChart());
        },
        closeDrilldown() {
          this.drilldown.active = false;
          if (this.charts.drilldown) {
            this.charts.drilldown.destroy();
            this.charts.drilldown = null;
          }
        },
        renderDrilldownChart() {
          if (!this.drilldown.active) return;
          const ctx = document.getElementById('drilldownChart');
          if (ctx) {
            if (this.charts.drilldown) this.charts.drilldown.destroy();
            if (this.drilldown.data) {
              this.charts.drilldown = new Chart(ctx, {
                type: 'bar',
                data: {
                  labels: this.drilldown.data.labels,
                  datasets: [
                    {
                      label: 'Graduates',
                      data: this.drilldown.data.graduates,
                      backgroundColor: 'rgba(54, 162, 235, 0.7)'
                    },
                    {
                      label: 'Employed',
                      data: this.drilldown.data.employed,
                      backgroundColor: 'rgba(75, 192, 192, 0.7)'
                    }
                  ]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: { legend: { labels: { color: '#374151' } } }
                }
              });
            }
          }
        }
    }
}).mount('#app');