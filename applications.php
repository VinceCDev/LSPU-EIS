<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alumni Profile - Applications & Saved Jobs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Layout & Spacing */
        .profile-container {
            display: flex;
            align-items: stretch;
            min-height: 85vh;
        }

        .sidebar {
            flex: 1;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            height: 85vh;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-right: 30px;
        }

        .sidebar img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #007bff;
            margin-bottom: 15px;
        }

        .list-group-item {
            border: none;
            padding: 12px;
            font-size: 16px;
            display: flex;
            align-items: center;
            border-left: 4px solid transparent;
            transition: background 0.3s, border-left 0.3s;
        }

        .list-group-item.active {
            background: rgba(0, 123, 255, 0.1);
            border-left: 4px solid #007bff;
        }

        .profile-form {
            flex: 2;
            padding: 30px;
            overflow-y: auto;
        }

        /* Application & Saved Job Cards */
        .job-card {
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            padding: 20px;
            margin-bottom: 15px;
        }

        .job-card:hover {
            transform: scale(1.02);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .profile-container {
                flex-direction: column;
            }

            .sidebar {
                margin-right: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand fw-bold" href="#">JobStreet Clone</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="#">Job search</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Career advice</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Explore companies</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Community</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Layout -->
    <div class="container mt-4">
        <div class="profile-container row">
            <!-- Left Sidebar -->
            <div class="col-lg-4 sidebar">
                <img src="https://via.placeholder.com/120" alt="Profile Picture">
                <h5>John Doe</h5>
                <p>johndoe@email.com</p>
                <ul class="list-group text-start">
                    <li class="list-group-item"><i class="bi bi-person-circle"></i> About Me</li>
                    <li class="list-group-item"><i class="bi bi-lightbulb"></i> Skills</li>
                    <li class="list-group-item"><i class="bi bi-briefcase-fill"></i> Work Experience</li>
                    <li class="list-group-item active"><i class="bi bi-file-earmark-text"></i> Applications & Saved Jobs</li>
                </ul>
                <button class="btn btn-primary w-100"><i class="bi bi-upload"></i> Upload Resume</button>
            </div>

            <!-- Right Applications & Saved Jobs Section -->
            <div class="col-lg-8 profile-form">
                <h4 class="fw-bold">📋 Applications</h4>
                <div class="row" id="applications-list"></div>

                <h4 class="fw-bold text-primary mt-3">💾 Saved Jobs</h4>
                <div class="row" id="saved-jobs-list"></div>
            </div>
        </div>
    </div>

    <!-- Pop-Up for Job Details -->
    <div class="modal fade" id="jobDetailsModal" tabindex="-1" aria-labelledby="jobDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobDetailsModalLabel">Job Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="jobDetailsContent">
                    <!-- Job details will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        const applications = [{
                title: "Software Engineer",
                company: "ABC Tech",
                location: "Manila",
                description: "Develop and maintain applications.",
                date: "Applied: March 15, 2025"
            },
            {
                title: "Marketing Specialist",
                company: "XYZ Marketing",
                location: "Makati",
                description: "Plan and execute marketing strategies.",
                date: "Applied: March 10, 2025"
            }
        ];

        const savedJobs = [{
            title: "Graphic Designer",
            company: "Creatives Inc.",
            location: "Quezon City",
            description: "Design branding and marketing materials.",
            date: "Saved: March 20, 2025"
        }];

        function loadJobs(list, elementId) {
            let container = document.getElementById(elementId);
            container.innerHTML = "";
            list.forEach(job => {
                let jobCard = document.createElement("div");
                jobCard.classList.add("col-md-12");
                jobCard.innerHTML = `
                    <div class="card job-card">
                        <div class="card-body">
                            <h5 class="card-title">${job.title}</h5>
                            <p class="text-muted">${job.company} • ${job.location}</p>
                            <p>${job.description}</p>
                            <p class="text-muted"><i class="bi bi-calendar"></i> ${job.date}</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="viewJobDetails('${job.title}', '${job.company}', '${job.location}', '${job.description}')">
                                <i class="bi bi-eye"></i> View Details
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(jobCard);
            });
        }

        function viewJobDetails(title, company, location, description) {
            document.getElementById("jobDetailsContent").innerHTML = `
                <h5>${title}</h5>
                <p class="text-muted">${company} • ${location}</p>
                <p>${description}</p>
            `;
            let jobDetailsModal = new bootstrap.Modal(document.getElementById("jobDetailsModal"));
            jobDetailsModal.show();
        }

        loadJobs(applications, "applications-list");
        loadJobs(savedJobs, "saved-jobs-list");
    </script>

</body>

</html>