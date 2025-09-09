// Debounce utility
function debounce(fn, delay) {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), delay);
    };
  }
  
  const { createApp, shallowRef } = Vue;
  createApp({
      data() {
          return {
              sidebarActive: window.innerWidth >= 768,
                  companiesDropdownOpen: true,
              alumniDropdownOpen: false,
              profileDropdownOpen: false,
              darkMode: localStorage.getItem('darkMode') === 'true' || 
                     (localStorage.getItem('darkMode') === null && 
                      window.matchMedia('(prefers-color-scheme: dark)').matches),
              showLogoutModal: false,
              isMobile: window.innerWidth < 768,
              notifications: [],
              notificationId: 0,
              actionDropdown: null,
                  companies: [],
                  filteredCompanies: [],
                  // Pagination
                  currentPage: 1,
                  itemsPerPage: 10,
                  // Filters
              searchQuery: '',
              filters: {
                  industry_type: '',
                  nature_of_business: '',
                  accreditation_status: ''
              },
              uniqueIndustryTypes: [],
              uniqueNatureOfBusiness: [],
                  uniqueAccreditationStatus: [],
              showCompanyModal: false,
              selectedCompany: null,
              companyForm: null,
              showViewModal: false,
              locationSuggestions: [],
              showLocationSuggestions: false,
              profile: {
                  profile_pic: '',
                  name: '',
              },
          }
      },
      mounted() {
        this.applyDarkMode();

        window.addEventListener('resize', this.handleResize);
            this.fetchCompanies();
        fetch('functions/fetch_admin_details.php')
            .then(res => res.json())
            .then(data => {
            if (data.success && data.profile) {
                this.profile = data.profile;
            }
        });
      },
      beforeUnmount() {
          // Clean up object URLs
          if (this.companyForm && this.companyForm.logoUrl) {
              URL.revokeObjectURL(this.companyForm.logoUrl);
          }
          // Remove event listeners
          document.removeEventListener('click', this.handleClickOutsideProfile, true);
    },
    
    watch: {
        darkMode(val) {
            this.applyDarkMode();
        },
        
        searchQuery: {
            handler: debounce(function() {
                this.filterCompanies();
            }, 300),
            immediate: false
        },
        
        filters: {
            handler() {
                this.currentPage = 1;
            },
            deep: true
        },
        companies() { this.filterCompanies(); }
    },

    computed: {
        filteredCompanies() {
                  let filtered = Array.isArray(this.companies) ? this.companies : [];
                  if (this.searchQuery && this.searchQuery.trim() !== '') {
                      const q = this.searchQuery.toLowerCase();
                      filtered = filtered.filter(company =>
                          (company.company_name || '').toLowerCase().includes(q) ||
                          (company.company_location || '').toLowerCase().includes(q) ||
                          (company.contact_email || '').toLowerCase().includes(q) ||
                          (company.industry_type || '').toLowerCase().includes(q) ||
                          (company.nature_of_business || '').toLowerCase().includes(q) ||
                          (company.accreditation_status || '').toLowerCase().includes(q) ||
                          (company.status || '').toLowerCase().includes(q)
                      );
                  }
                  if (this.filters.industry_type && this.filters.industry_type !== '') {
                      filtered = filtered.filter(company => company.industry_type === this.filters.industry_type);
                  }
                  if (this.filters.nature_of_business && this.filters.nature_of_business !== '') {
                      filtered = filtered.filter(company => company.nature_of_business === this.filters.nature_of_business);
                  }
                  if (this.filters.accreditation_status && this.filters.accreditation_status !== '') {
                      filtered = filtered.filter(company => company.accreditation_status === this.filters.accreditation_status);
                  }
                  return filtered;
              },
              paginatedCompanies() {
                  const start = (this.currentPage - 1) * this.itemsPerPage;
                  const end = start + this.itemsPerPage;
                  return this.filteredCompanies.slice(start, end);
              },
              totalPages() {
                  return Math.ceil((this.filteredCompanies ? this.filteredCompanies.length : 0) / this.itemsPerPage) || 1;
              }
    },
    
    methods: {
              toggleSidebar() {
                  this.sidebarActive = !this.sidebarActive;
                  this.companiesDropdownOpen = true;
                  this.alumniDropdownOpen = false;
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
                  async fetchCompanies() {
                      fetch('functions/get_company_active.php')
                          .then(res => res.text())
                          .then(text => {
                              try {
                                  const data = JSON.parse(text);
                                  this.companies = data.map(company => ({
                                      ...company,
                                      id: company.user_id, // for Vue key
                                      logoUrl: company.company_logo
                                          ? (company.company_logo.startsWith('http') ? company.company_logo : '/lspu_eis/uploads/logos/' + company.company_logo.replace(/^.*[\\\/]/, ''))
                                          : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(company.company_name || 'Company'),
                                  }));
                                  this.initUniqueFilters();
                                  this.filterCompanies(); // Always filter after fetching
                              } catch (e) {
                                  this.showNotification('Error parsing company data. See console for details.', 'error');
                                  console.error('JSON parse error:', e, text);
                              }
                          })
                          .catch(err => {
                              this.showNotification('Fetch error: ' + err, 'error');
                              console.error('Fetch error:', err);
                          });
                  },
                  initUniqueFilters() {
                      this.uniqueIndustryTypes = [...new Set(this.companies.map(c => c.industry_type).filter(Boolean))];
                      this.uniqueNatureOfBusiness = [...new Set(this.companies.map(c => c.nature_of_business).filter(Boolean))];
                      this.uniqueAccreditationStatus = [...new Set(this.companies.map(c => c.accreditation_status).filter(Boolean))];
                  },

                // Fixed contactAlumni method with proper error handling
                contactAlumni(company) {
                    // Check if company exists and has email property
                    if (!company || !company.email) {
                        console.error('Company data is not available');
                        this.showNotification('Company information is not available. Please select a valid company.', 'error');
                        return;
                    }
                    
                    try {
                        // Redirect to messages page with company email as parameter
                        const messagesUrl = `admin_message.php?compose=true&to=${encodeURIComponent(company.email)}`;
                        window.location.href = messagesUrl;
                    } catch (error) {
                        console.error('Error redirecting to messages:', error);
                        this.showNotification('Error opening message composer', 'error');
                    }
                },
              filterCompanies() {
                      let filtered = this.companies;
                      // Only filter if the filter value is not empty
                      if (this.searchQuery && this.searchQuery.trim() !== '') {
                          const q = this.searchQuery.toLowerCase();
                          filtered = filtered.filter(company =>
                              (company.company_name || '').toLowerCase().includes(q) ||
                              (company.company_location || '').toLowerCase().includes(q) ||
                              (company.contact_email || '').toLowerCase().includes(q) ||
                              (company.industry_type || '').toLowerCase().includes(q) ||
                              (company.nature_of_business || '').toLowerCase().includes(q)
                          );
                      }
                      if (this.filters.industry_type && this.filters.industry_type !== '') {
                          filtered = filtered.filter(company => company.industry_type === this.filters.industry_type);
                      }
                      if (this.filters.nature_of_business && this.filters.nature_of_business !== '') {
                          filtered = filtered.filter(company => company.nature_of_business === this.filters.nature_of_business);
                      }
                      if (this.filters.accreditation_status && this.filters.accreditation_status !== '') {
                          filtered = filtered.filter(company => company.accreditation_status === this.filters.accreditation_status);
                      }
                      this.filteredCompanies = filtered;
                      // Ensure itemsPerPage is at least 1
                      if (!this.itemsPerPage || this.itemsPerPage < 1) this.itemsPerPage = 10;
                      this.currentPage = 1;
                  },
              prevPage() {
                      if (this.currentPage > 1) this.currentPage--;
              },
              nextPage() {
                      if (this.currentPage < this.totalPages) this.currentPage++;
              },
              goToPage(page) {
                  this.currentPage = page;
              },
              toggleActionDropdown(companyId) {
                  this.actionDropdown = this.actionDropdown === companyId ? null : companyId;
              },
              openAddModal() {
                  this.showCompanyModal = true;
                  this.selectedCompany = null;
                  this.companyForm = {
                      id: null,
                      company_name: '',
                      company_location: '',
                      contact_email: '',
                      contact_number: '',
                      industry_type: '',
                      nature_of_business: '',
                      tin: '',
                      date_established: '',
                      company_type: '',
                      accreditation_status: '',
                      logo: null,
                      document: null,
                      email: ''
                  };
                  this.$nextTick(() => {
                      const input = document.getElementById('company-name-input');
                      if (input) input.focus();
                  });
              },
              editCompany(company) {
                  this.selectedCompany = company;
                  this.companyForm = { ...company, logo: null, document: null, email: company.email || '' };
                  this.showCompanyModal = true;
                  this.$nextTick(() => {
                      const input = document.getElementById('company-name-input');
                      if (input) input.focus();
                  });
              },
              viewCompany(company) {
                  // Map document_file to documents array for the modal
                  let documents = [];
                  if (company.document_file) {
                      // Try to extract original filename (after first underscore)
                      let original = company.document_file.split('_').slice(1).join('_') || company.document_file;
                      let docUrl = '/lspu_eis/uploads/documents/' + company.document_file.replace(/^.*[\\\/]/, '');
                      documents = [{ name: original, url: docUrl }];
                  }
                  this.selectedCompany = {
                      ...company,
                      documents
                  };
                  this.showViewModal = true;
                  this.$nextTick(() => {
                      const btn = document.getElementById('close-view-modal-btn');
                      if (btn) btn.focus();
                  });
              },
              confirmDelete(company) {
                  this.selectedCompany = { ...company };
                  this.showDeleteModal = true;
              },
              deleteCompany() {
                  fetch('functions/admin_company_add_edit.php', {
                      method: 'DELETE',
                      headers: { 'Content-Type': 'application/json' },
                      body: JSON.stringify({ id: this.selectedCompany.id })
                  })
                  .then(res => res.json())
                  .then(data => {
                      this.showDeleteModal = false;
                      this.showNotification(data.message, data.success ? 'success' : 'error');
                      this.fetchCompanies();
                  });
              },
              addCompany() {
                  const formData = new FormData();
                  for (const key in this.companyForm) {
                      if (this.companyForm[key] !== null && this.companyForm[key] !== undefined && this.companyForm[key] !== '') {
                          formData.append(key, this.companyForm[key]);
                      }
                  }
                  if (this.companyForm.logo) formData.append('company_logo', this.companyForm.logo);
                  if (this.companyForm.document) formData.append('document_file', this.companyForm.document);
                  fetch('functions/admin_company_add_edit.php', {
                      method: 'POST',
                      body: formData
                  })
                  .then(async res => {
                      let data;
                      try {
                          data = await res.json();
                      } catch (e) {
                          this.showNotification('Server error: Invalid response.', 'error');
                          return;
                      }
                      this.showCompanyModal = false;
                      this.showNotification(data.message, data.success ? 'success' : 'error');
                      this.fetchCompanies();
                  })
                  .catch(() => {
                      this.showNotification('Network error. Please try again.', 'error');
                  });
              },
              updateCompany() {
                  const formData = new FormData();
                  for (const key in this.companyForm) {
                      if (this.companyForm[key] !== null && this.companyForm[key] !== undefined && this.companyForm[key] !== '') {
                          formData.append(key, this.companyForm[key]);
                      }
                  }
                  if (this.companyForm.logo) formData.append('company_logo', this.companyForm.logo);
                  if (this.companyForm.document) formData.append('document_file', this.companyForm.document);
                  formData.append('id', this.companyForm.id);
                  fetch('functions/admin_company_add_edit.php', {
                      method: 'POST',
                      body: formData
                  })
                  .then(async res => {
                      let data;
                      try {
                          data = await res.json();
                      } catch (e) {
                          this.showNotification('Server error: Invalid response.', 'error');
                          return;
                      }
                      this.showCompanyModal = false;
                      this.showNotification(data.message, data.success ? 'success' : 'error');
                      this.fetchCompanies();
                  })
                  .catch(() => {
                      this.showNotification('Network error. Please try again.', 'error');
                  });
              },
              handleLogoUpload(event) {
                  const file = event.target.files[0];
                  if (file) {
                      this.companyForm.logo = file;
                      this.companyForm.logoUrl = URL.createObjectURL(file);
                  } else {
                      this.companyForm.logo = null;
                      this.companyForm.logoUrl = '';
                  }
              },
              handleDocumentUpload(event) {
                  const file = event.target.files[0];
                  if (file) {
                      this.companyForm.document = file;
                  } else {
                      this.companyForm.document = null;
                  }
              },
              exportToExcel() {
                  const wb = XLSX.utils.book_new();
                  const wsData = [
                      ['Name of Company', 'Location', 'Contact Email', 'Industry Type', 'Nature of Business', 'Accreditation Status', 'Status'],
                      ...this.filteredCompanies.map(a => [
                          a.company_name, a.company_location, a.contact_email, a.industry_type, a.nature_of_business, a.accreditation_status, a.status
                      ])
                  ];
                  const ws = XLSX.utils.aoa_to_sheet(wsData);
                  XLSX.utils.book_append_sheet(wb, ws, 'Companies');
                  XLSX.writeFile(wb, 'companies.xlsx');
                  this.showNotification('Excel file generated successfully!', 'success');
              },
              exportToPDF() {
                  const { jsPDF } = window.jspdf;
                  const doc = new jsPDF();
                  const columns = ['Name of Company', 'Location', 'Contact Email', 'Industry Type', 'Nature of Business', 'Accreditation Status', 'Status'];
                  const rows = this.filteredCompanies.map(a => [
                      a.company_name, a.company_location, a.contact_email, a.industry_type, a.nature_of_business, a.accreditation_status, a.status
                  ]);
                  doc.autoTable({ head: [columns], body: rows });
                  doc.save('companies.pdf');
                  this.showNotification('PDF generated successfully!', 'success');
              },
              showNotification(message, type = 'success') {
                  const id = this.notificationId++;
                  this.notifications.push({ id, type, message });
                  setTimeout(() => this.removeNotification(id), 3000);
              },
              removeNotification(id) {
                  this.notifications = this.notifications.filter(n => n.id !== id);
              },
              handleNavClick() {
                  // Optionally, add logic to close sidebar/dropdowns on navigation
                  this.profileDropdownOpen = !this.profileDropdownOpen;
                  if (this.profileDropdownOpen) {
                      this.actionDropdown = null;
                      this.companiesDropdownOpen = false;
                      this.alumniDropdownOpen = false;
                  }
                  this.companiesDropdownOpen = false;
                  this.alumniDropdownOpen = false;
                  if (this.isMobile) {
                      this.sidebarActive = false;
                  }
              },
              // Profile dropdown logic
              toggleProfileDropdown() {
                  this.profileDropdownOpen = !this.profileDropdownOpen;
                  if (this.profileDropdownOpen) {
                      document.addEventListener('click', this.handleClickOutsideProfile, true);
                  } else {
                      document.removeEventListener('click', this.handleClickOutsideProfile, true);
                  }
              },
              handleClickOutsideProfile(e) {
                  const dropdown = document.querySelector('.origin-top-right.absolute.right-0.mt-2.w-48');
                  const trigger = document.querySelector('.cursor-pointer.flex.items-center');
                  if (dropdown && !dropdown.contains(e.target) && trigger && !trigger.contains(e.target)) {
                      this.profileDropdownOpen = false;
                      document.removeEventListener('click', this.handleClickOutsideProfile, true);
                  }
              },
              confirmLogout() {
                this.showLogoutModal = true;
            },
            logout() {
                window.location.href = 'logout.php';
            },
              closeCompanyModal() {
                  this.showCompanyModal = false;
                  this.selectedCompany = null;
                  this.companyForm = null;
              },
              closeViewModal() {
                  this.showViewModal = false;
                  this.selectedCompany = null;
              },
              async fetchLocationSuggestions() {
                  const val = this.companyForm.company_location;
                  if (!val || val.length < 3) {
                      this.locationSuggestions = [];
                      this.showLocationSuggestions = false;
                      return;
                  }
                  const apiKey = 'b25cb94f83684f6aa21cbd86f93c9417'; // Geoapify API key
                  const url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(val)}&limit=5&apiKey=${apiKey}`;
                  try {
                      const res = await fetch(url);
                      const data = await res.json();
                      this.locationSuggestions = data.features.map(f => f.properties.formatted);
                      this.showLocationSuggestions = true;
                  } catch (e) {
                      this.locationSuggestions = [];
                      this.showLocationSuggestions = false;
                  }
              },
              selectLocationSuggestion(suggestion) {
                  this.companyForm.company_location = suggestion;
                  this.locationSuggestions = [];
                  this.showLocationSuggestions = false;
              },
              hideLocationSuggestions() {
                  // Delay to allow click event to register
                  setTimeout(() => { this.showLocationSuggestions = false; }, 150);
              },
          }
      }).mount('#app');