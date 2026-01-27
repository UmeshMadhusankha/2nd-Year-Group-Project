// ================================================
// REPAIRER JOBS & APPLICATIONS MANAGEMENT
// ================================================

// Mock Data - Replace with API calls in production
const jobPostingsData = [
    {
        id: 1,
        title: 'Senior HVAC Technician',
        company: 'TechCorp Solutions',
        companyAvatar: 'TC',
        category: 'hvac',
        employmentType: 'Contract',
        description: 'We are seeking an experienced HVAC technician to join our team for ongoing maintenance and repair projects. The ideal candidate will have extensive experience in commercial HVAC systems and excellent problem-solving skills.',
        budget: 'LKR 2,500 - 3,200/hr',
        minBudget: 2500,
        maxBudget: 3200,
        experience: '3+ years',
        minExperience: 3,
        priority: 'Normal',
        deadline: '15 days remaining',
        location: 'Colombo, Sri Lanka',
        postedDate: '2025-10-19',
        applicationCount: 12,
        status: 'active',
        requiredSkills: ['HVAC Systems', 'Refrigeration', 'Electrical Troubleshooting', 'Customer Service'],
        locationRequirements: 'Colombo and surrounding areas, must have own transportation'
    },
    {
        id: 2,
        title: 'Electrical Repair Specialist',
        company: 'BuildPro Lanka',
        companyAvatar: 'BP',
        category: 'electrical',
        employmentType: 'Full-time',
        description: 'Looking for a skilled electrician to handle residential and commercial electrical repairs, installations, and maintenance. Safety certifications required.',
        budget: 'LKR 2,000 - 2,800/hr',
        minBudget: 2000,
        maxBudget: 2800,
        experience: '2+ years',
        minExperience: 2,
        priority: 'High',
        deadline: '10 days remaining',
        location: 'Kandy, Sri Lanka',
        postedDate: '2025-10-18',
        applicationCount: 8,
        status: 'active',
        requiredSkills: ['Electrical Wiring', 'Circuit Design', 'Safety Compliance', 'Troubleshooting'],
        locationRequirements: 'Kandy district, flexible schedule required'
    },
    {
        id: 3,
        title: 'Emergency Plumber',
        company: 'HomeServices Inc',
        companyAvatar: 'HS',
        category: 'plumbing',
        employmentType: 'On-call',
        description: 'Urgent need for emergency plumber available for 24/7 on-call service. Must respond to emergency calls within 2 hours.',
        budget: 'LKR 2,200 - 3,000/hr',
        minBudget: 2200,
        maxBudget: 3000,
        experience: '4+ years',
        minExperience: 4,
        priority: 'Urgent',
        deadline: '5 days remaining',
        location: 'Galle, Sri Lanka',
        postedDate: '2025-10-20',
        applicationCount: 5,
        status: 'active',
        requiredSkills: ['Pipe Fitting', 'Leak Detection', 'Emergency Response', 'Water Systems'],
        locationRequirements: 'Galle and Southern Province, 24/7 availability'
    },
    {
        id: 4,
        title: 'Carpentry Contractor',
        company: 'WoodWorks Lanka',
        companyAvatar: 'WW',
        category: 'carpentry',
        employmentType: 'Project-based',
        description: 'Seeking experienced carpenter for custom furniture and woodwork projects. Portfolio review required.',
        budget: 'LKR 1,800 - 2,500/hr',
        minBudget: 1800,
        maxBudget: 2500,
        experience: '5+ years',
        minExperience: 5,
        priority: 'Normal',
        deadline: '20 days remaining',
        location: 'Negombo, Sri Lanka',
        postedDate: '2025-10-17',
        applicationCount: 15,
        status: 'active',
        requiredSkills: ['Custom Woodwork', 'Furniture Making', 'Blueprint Reading', 'Wood Finishing'],
        locationRequirements: 'Negombo area, own tools required'
    }
];

const applicationsData = [
    {
        id: 1,
        jobId: 1,
        jobTitle: 'Senior HVAC Technician',
        company: 'TechCorp Solutions',
        companyAvatar: 'TC',
        category: 'hvac',
        jobBudget: 'LKR 2,500 - 3,200/hr',
        proposedRate: 2800,
        availability: 'Immediately',
        appliedDate: '2025-10-20',
        status: 'pending',
        coverLetter: 'I am an experienced HVAC technician with over 5 years of hands-on experience in commercial and residential systems. I am confident I can deliver high-quality service for your projects.',
        timeline: [
            { event: 'Application Submitted', date: '2025-10-20', time: '10:30 AM' }
        ]
    },
    {
        id: 2,
        jobId: 2,
        jobTitle: 'Electrical Repair Specialist',
        company: 'BuildPro Lanka',
        companyAvatar: 'BP',
        category: 'electrical',
        jobBudget: 'LKR 2,000 - 2,800/hr',
        proposedRate: 2500,
        availability: 'Within 1 week',
        appliedDate: '2025-10-18',
        status: 'accepted',
        coverLetter: 'With my electrical certifications and 4 years of experience, I am well-equipped to handle all your electrical needs professionally and safely.',
        timeline: [
            { event: 'Application Submitted', date: '2025-10-18', time: '2:15 PM' },
            { event: 'Application Reviewed', date: '2025-10-19', time: '9:45 AM' },
            { event: 'Application Accepted', date: '2025-10-19', time: '3:30 PM' }
        ]
    },
    {
        id: 3,
        jobId: 4,
        jobTitle: 'Carpentry Contractor',
        company: 'WoodWorks Lanka',
        companyAvatar: 'WW',
        category: 'carpentry',
        jobBudget: 'LKR 1,800 - 2,500/hr',
        proposedRate: 2200,
        availability: 'Within 2 weeks',
        appliedDate: '2025-10-15',
        status: 'rejected',
        coverLetter: 'I am a skilled carpenter specializing in custom furniture with a portfolio of over 50 completed projects.',
        timeline: [
            { event: 'Application Submitted', date: '2025-10-15', time: '11:20 AM' },
            { event: 'Application Reviewed', date: '2025-10-16', time: '4:00 PM' },
            { event: 'Application Declined', date: '2025-10-17', time: '10:15 AM' }
        ]
    }
];

const contractsData = [
    {
        id: 1,
        company: 'TechCorp Solutions',
        companyAvatar: 'TC',
        role: 'HVAC Technician',
        type: 'Long-term',
        rate: 2800,
        startDate: '2025-10-01',
        status: 'active',
        totalAssignments: 5,
        completedAssignments: 3,
        totalEarnings: 45000,
        email: 'contact@techcorp.com',
        phone: '+94 11 234 5678'
    },
    {
        id: 2,
        company: 'BuildPro Lanka',
        companyAvatar: 'BP',
        role: 'Electrical Specialist',
        type: 'Contract',
        rate: 2500,
        startDate: '2025-10-10',
        status: 'active',
        totalAssignments: 3,
        completedAssignments: 1,
        totalEarnings: 22500,
        email: 'info@buildpro.lk',
        phone: '+94 77 345 6789'
    }
];

const assignmentsData = [
    {
        id: 1,
        contractId: 1,
        title: 'HVAC System Maintenance',
        company: 'TechCorp Solutions',
        companyAvatar: 'TC',
        description: 'Perform routine maintenance on HVAC systems at the office complex including filter replacement, system checks, and performance optimization.',
        date: '2025-10-22',
        time: '9:00 AM - 5:00 PM',
        location: 'Colombo 07',
        estimatedHours: 8,
        hoursWorked: 7.5,
        hourlyRate: 2800,
        totalPaid: '21,000',
        paymentStatus: 'paid',
        status: 'completed',
        priority: 'normal',
        completedDate: '2025-10-22',
        notes: 'All systems checked and filters replaced. Client satisfied with the service.'
    },
    {
        id: 2,
        contractId: 1,
        title: 'Emergency AC Repair',
        company: 'TechCorp Solutions',
        companyAvatar: 'TC',
        description: 'Emergency repair of malfunctioning air conditioning unit in server room.',
        date: '2025-10-23',
        time: '2:00 PM - 6:00 PM',
        location: 'Colombo 03',
        estimatedHours: 4,
        hoursWorked: 4,
        hourlyRate: 2800,
        totalPaid: '11,200',
        paymentStatus: 'pending',
        status: 'in-progress',
        priority: 'urgent',
        startedDate: '2025-10-23',
        notes: 'Compressor replacement needed. Parts ordered.'
    },
    {
        id: 3,
        contractId: 2,
        title: 'Electrical Panel Installation',
        company: 'BuildPro Lanka',
        companyAvatar: 'BP',
        description: 'Install new electrical panel and circuit breakers in residential building.',
        date: '2025-10-24',
        time: '8:00 AM - 4:00 PM',
        location: 'Kandy',
        estimatedHours: 8,
        hoursWorked: 0,
        hourlyRate: 2500,
        totalPaid: '0',
        paymentStatus: 'not-started',
        status: 'pending',
        priority: 'normal',
        notes: 'Awaiting client confirmation on installation date.'
    },
    {
        id: 4,
        contractId: 1,
        title: 'Ductwork Inspection',
        company: 'TechCorp Solutions',
        companyAvatar: 'TC',
        description: 'Comprehensive inspection of all ductwork systems in the building.',
        date: '2025-10-25',
        time: '10:00 AM - 2:00 PM',
        location: 'Colombo 07',
        estimatedHours: 4,
        hoursWorked: 0,
        hourlyRate: 2800,
        totalPaid: '0',
        paymentStatus: 'not-started',
        status: 'pending',
        priority: 'normal',
        notes: 'Scheduled for next week.'
    },
    {
        id: 5,
        contractId: 2,
        title: 'Wiring Upgrade - Floor 2',
        company: 'BuildPro Lanka',
        companyAvatar: 'BP',
        description: 'Upgrade electrical wiring for second floor commercial space.',
        date: '2025-10-26',
        time: '8:00 AM - 5:00 PM',
        location: 'Kandy',
        estimatedHours: 9,
        hoursWorked: 0,
        hourlyRate: 2500,
        totalPaid: '0',
        paymentStatus: 'not-started',
        status: 'pending',
        priority: 'high',
        notes: 'Client requesting completion before month end.'
    },
    {
        id: 6,
        contractId: 1,
        title: 'Quarterly HVAC Service',
        company: 'TechCorp Solutions',
        companyAvatar: 'TC',
        description: 'Quarterly preventive maintenance service for all HVAC units.',
        date: '2025-10-20',
        time: '9:00 AM - 3:00 PM',
        location: 'Colombo 07',
        estimatedHours: 6,
        hoursWorked: 6,
        hourlyRate: 2800,
        totalPaid: '16,800',
        paymentStatus: 'paid',
        status: 'completed',
        priority: 'normal',
        completedDate: '2025-10-20',
        notes: 'Regular maintenance completed successfully.'
    }
];

const messagesData = [
    {
        id: 1,
        contractId: 1,
        company: 'TechCorp Solutions',
        companyAvatar: 'TC',
        projectName: 'HVAC Maintenance Contract',
        lastMessage: 'Please confirm availability for tomorrow\'s emergency repair.',
        time: '10:30 AM',
        unread: true,
        messages: [
            {
                sender: 'company',
                text: 'Hello! We have an urgent AC repair needed tomorrow. Are you available?',
                timestamp: '2025-10-22 9:15 AM'
            },
            {
                sender: 'repairer',
                text: 'Yes, I can make it. What time works best?',
                timestamp: '2025-10-22 9:30 AM'
            },
            {
                sender: 'company',
                text: 'Please confirm availability for tomorrow\'s emergency repair.',
                timestamp: '2025-10-22 10:30 AM'
            }
        ]
    },
    {
        id: 2,
        contractId: 2,
        company: 'BuildPro Lanka',
        companyAvatar: 'BP',
        projectName: 'Electrical Work Contract',
        lastMessage: 'Great job on the last installation! Here\'s your next assignment.',
        time: 'Yesterday',
        unread: true,
        messages: [
            {
                sender: 'company',
                text: 'Great job on the last installation! Here\'s your next assignment.',
                timestamp: '2025-10-21 3:45 PM'
            }
        ]
    }
];

let currentRepairerStatus = 'available'; // available, busy

// ================================================
// INITIALIZATION
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname;
    
    if (currentPage.includes('job-postings.php')) {
        loadJobPostings();
    } else if (currentPage.includes('my-applications.php')) {
        loadApplications();
        updateApplicationStats();
    } else if (currentPage.includes('my-contracts.php')) {
        loadContracts();
        loadAssignments();
        loadMessages();
        updateContractStats();
    }
    
    // Initialize cover letter character counter
    const coverLetterInput = document.getElementById('coverLetter');
    if (coverLetterInput) {
        coverLetterInput.addEventListener('input', updateCoverLetterCount);
    }
});

// ================================================
// JOB POSTINGS FUNCTIONS
// ================================================

function loadJobPostings() {
    const jobsGrid = document.getElementById('jobsGrid');
    if (!jobsGrid) return;
    
    jobsGrid.innerHTML = '';
    
    jobPostingsData.forEach(job => {
        const jobCard = createJobCard(job);
        jobsGrid.appendChild(jobCard);
    });
    
    // Update job count
    const jobCountEl = document.getElementById('jobCount');
    if (jobCountEl) {
        jobCountEl.textContent = `${jobPostingsData.length} jobs available`;
    }
}

function createJobCard(job) {
    const card = document.createElement('div');
    card.className = 'job-card';
    card.onclick = () => viewJobDetails(job.id);
    
    const priorityBadge = job.priority === 'Urgent' ? 'urgent' : 'active';
    
    card.innerHTML = `
        <div class="job-card-header">
            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                <div class="company-avatar">${job.companyAvatar}</div>
                <div class="job-card-title">
                    <h3>${job.title}</h3>
                    <p class="company-name">${job.company}</p>
                </div>
            </div>
            <span class="job-badge ${priorityBadge}">${job.priority}</span>
        </div>
        <div class="job-card-info">
            <div class="job-info-item">
                <i class="fas fa-briefcase"></i>
                <span>${job.employmentType}</span>
            </div>
            <div class="job-info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>${job.location}</span>
            </div>
            <div class="job-info-item">
                <i class="fas fa-calendar"></i>
                <span>Posted ${getRelativeTime(job.postedDate)}</span>
            </div>
            <div class="job-info-item">
                <i class="fas fa-users"></i>
                <span>${job.applicationCount} applicants</span>
            </div>
        </div>
        <div class="job-card-footer">
            <span class="job-budget">${job.budget}</span>
            <button class="btn-apply" onclick="event.stopPropagation(); viewJobDetails(${job.id})">
                <i class="fas fa-arrow-right"></i> View Details
            </button>
        </div>
    `;
    
    return card;
}

function viewJobDetails(jobId) {
    const job = jobPostingsData.find(j => j.id === jobId);
    if (!job) return;
    
    // Populate drawer with job details
    document.getElementById('jobCompanyAvatar').textContent = job.companyAvatar;
    document.getElementById('jobDetailTitle').textContent = job.title;
    document.getElementById('jobCompanyName').textContent = job.company;
    document.getElementById('jobPostedDate').textContent = `Posted ${getRelativeTime(job.postedDate)}`;
    document.getElementById('jobApplicationCount').textContent = `${job.applicationCount} applicants`;
    document.getElementById('jobLocation').textContent = job.location;
    document.getElementById('jobCategory').textContent = job.category.toUpperCase();
    document.getElementById('jobEmploymentType').textContent = job.employmentType;
    document.getElementById('jobBudget').textContent = job.budget;
    document.getElementById('jobExperience').textContent = job.experience;
    document.getElementById('jobPriority').textContent = job.priority;
    document.getElementById('jobDeadline').textContent = job.deadline;
    document.getElementById('jobDescription').textContent = job.description;
    document.getElementById('jobLocationRequirements').textContent = job.locationRequirements;
    
    // Update status badge
    const statusBadge = document.getElementById('jobStatusBadge');
    statusBadge.textContent = job.status.charAt(0).toUpperCase() + job.status.slice(1);
    statusBadge.className = `job-badge ${job.status}`;
    
    // Populate skills
    const skillsContainer = document.getElementById('jobSkillsTags');
    skillsContainer.innerHTML = '';
    job.requiredSkills.forEach(skill => {
        const skillTag = document.createElement('span');
        skillTag.className = 'skill-tag';
        skillTag.textContent = skill;
        skillsContainer.appendChild(skillTag);
    });
    
    // Store job ID for application
    document.getElementById('jobDetailsDrawer').dataset.jobId = jobId;
    
    // Open drawer
    document.getElementById('jobDetailsDrawer').classList.add('active');
}

function closeJobDetailsDrawer() {
    document.getElementById('jobDetailsDrawer').classList.remove('active');
}

function openApplicationForm() {
    const jobId = document.getElementById('jobDetailsDrawer').dataset.jobId;
    const job = jobPostingsData.find(j => j.id == jobId);
    if (!job) return;
    
    // Populate application form
    document.getElementById('applyingJobTitle').textContent = job.title;
    document.getElementById('applyingCompanyName').textContent = job.company;
    document.getElementById('jobBudgetRange').textContent = job.budget;
    
    // Store job ID
    document.getElementById('applicationFormDrawer').dataset.jobId = jobId;
    
    // Close job details drawer
    closeJobDetailsDrawer();
    
    // Open application form drawer
    document.getElementById('applicationFormDrawer').classList.add('active');
}

function closeApplicationFormDrawer() {
    document.getElementById('applicationFormDrawer').classList.remove('active');
    document.getElementById('jobApplicationForm').reset();
}

function submitApplication() {
    const jobId = document.getElementById('applicationFormDrawer').dataset.jobId;
    const proposedRate = document.getElementById('proposedRate').value;
    const availability = document.getElementById('availability').value;
    const coverLetter = document.getElementById('coverLetter').value;
    
    if (!proposedRate || !availability || !coverLetter) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    // In production, send to server
    console.log('Submitting application:', {
        jobId,
        proposedRate,
        availability,
        coverLetter
    });
    
    showNotification('Application submitted successfully!', 'success');
    closeApplicationFormDrawer();
    
    // Optionally reload the applications tab
    setTimeout(() => {
        // Reload applications instead of redirecting to a non-existent page
        loadApplicationsList();
        switchMainTab('applications');
    }, 1500);
}

function updateCoverLetterCount() {
    const coverLetter = document.getElementById('coverLetter');
    const counter = document.getElementById('coverLetterCount');
    if (coverLetter && counter) {
        counter.textContent = coverLetter.value.length;
    }
}

function filterJobsByCategory(category) {
    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Filter jobs
    const jobCards = document.querySelectorAll('.job-card');
    jobCards.forEach(card => {
        if (category === 'all') {
            card.style.display = 'block';
        } else {
            const jobCategory = card.querySelector('.job-card-title h3').textContent.toLowerCase();
            const matchesCategory = jobCategory.includes(category);
            card.style.display = matchesCategory ? 'block' : 'none';
        }
    });
}

function searchJobs() {
    const searchTerm = document.getElementById('jobSearchInput').value.toLowerCase();
    const jobCards = document.querySelectorAll('.job-card');
    
    jobCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? 'block' : 'none';
    });
}

function refreshJobPostings() {
    showNotification('Refreshing job postings...', 'info');
    loadJobPostings();
}

// ================================================
// APPLICATIONS FUNCTIONS
// ================================================

function loadApplications() {
    const applicationsList = document.getElementById('applicationsList');
    if (!applicationsList) return;
    
    applicationsList.innerHTML = '';
    
    if (applicationsData.length === 0) {
        applicationsList.innerHTML = `
            <div style="text-align: center; padding: 60px 20px; color: var(--text-secondary);">
                <i class="fas fa-inbox" style="font-size: 64px; opacity: 0.3; margin-bottom: 16px;"></i>
                <h3>No Applications Yet</h3>
                <p>You haven't submitted any job applications. Browse available jobs to get started!</p>
                <button class="btn btn-primary" onclick="switchMainTab('browse')" style="margin-top: 16px;">
                    <i class="fas fa-search"></i> Browse Jobs
                </button>
            </div>
        `;
        return;
    }
    
    applicationsData.forEach(app => {
        const appCard = createApplicationCard(app);
        applicationsList.appendChild(appCard);
    });
    
    // Update application count
    const appCountEl = document.getElementById('appCount');
    if (appCountEl) {
        appCountEl.textContent = `${applicationsData.length} applications submitted`;
    }
}

function createApplicationCard(app) {
    const card = document.createElement('div');
    card.className = 'application-card';
    card.onclick = () => viewApplicationDetails(app.id);
    
    const iconClass = app.status === 'pending' ? 'pending' : 
                      app.status === 'accepted' ? 'accepted' : 'rejected';
    const icon = app.status === 'pending' ? 'fa-clock' :
                 app.status === 'accepted' ? 'fa-check-circle' : 'fa-times-circle';
    
    card.innerHTML = `
        <div class="application-icon ${iconClass}">
            <i class="fas ${icon}"></i>
        </div>
        <div class="application-content">
            <h4>${app.jobTitle}</h4>
            <p class="application-company">${app.company}</p>
            <div class="application-meta">
                <span><i class="fas fa-calendar"></i> Applied ${getRelativeTime(app.appliedDate)}</span>
                <span><i class="fas fa-money-bill-wave"></i> LKR ${app.proposedRate.toLocaleString()}/hr</span>
                <span><i class="fas fa-tag"></i> ${app.category.toUpperCase()}</span>
            </div>
        </div>
        <span class="status-badge ${app.status}">${app.status.charAt(0).toUpperCase() + app.status.slice(1)}</span>
    `;
    
    return card;
}

function viewApplicationDetails(applicationId) {
    const app = applicationsData.find(a => a.id === applicationId);
    if (!app) return;
    
    // Update status banner
    const statusBanner = document.getElementById('appStatusBanner');
    const statusIcon = document.getElementById('appStatusIcon');
    const statusTitle = document.getElementById('appStatusTitle');
    const statusMessage = document.getElementById('appStatusMessage');
    
    statusBanner.className = 'application-status-banner';
    if (app.status === 'accepted') {
        statusBanner.classList.add('accepted');
        statusIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
        statusTitle.textContent = 'Application Accepted!';
        statusMessage.textContent = 'Congratulations! The company has accepted your application.';
    } else if (app.status === 'rejected') {
        statusBanner.classList.add('rejected');
        statusIcon.innerHTML = '<i class="fas fa-times-circle"></i>';
        statusTitle.textContent = 'Application Declined';
        statusMessage.textContent = 'Unfortunately, the company has declined your application.';
    } else {
        statusIcon.innerHTML = '<i class="fas fa-clock"></i>';
        statusTitle.textContent = 'Application Under Review';
        statusMessage.textContent = 'Your application is being reviewed by the company';
    }
    
    // Populate application details
    document.getElementById('appJobTitle').textContent = app.jobTitle;
    document.getElementById('appCompanyName').textContent = app.company;
    document.getElementById('appJobCategory').textContent = app.category.toUpperCase();
    document.getElementById('appJobBudget').textContent = app.jobBudget;
    document.getElementById('appAppliedDate').textContent = formatDate(app.appliedDate);
    document.getElementById('appProposedRate').textContent = `LKR ${app.proposedRate.toLocaleString()}/hr`;
    document.getElementById('appAvailability').textContent = app.availability;
    document.getElementById('appCoverLetter').textContent = app.coverLetter;
    
    // Update status badge
    const statusBadge = document.getElementById('appStatusBadge');
    statusBadge.textContent = app.status.charAt(0).toUpperCase() + app.status.slice(1);
    statusBadge.className = `status-badge ${app.status}`;
    
    // Populate timeline
    const timeline = document.getElementById('appTimeline');
    timeline.innerHTML = '';
    app.timeline.forEach(item => {
        const timelineItem = document.createElement('div');
        timelineItem.className = 'timeline-item';
        timelineItem.innerHTML = `
            <div class="timeline-marker"></div>
            <div class="timeline-content">
                <h5>${item.event}</h5>
                <p>${item.date} at ${item.time}</p>
            </div>
        `;
        timeline.appendChild(timelineItem);
    });
    
    // Show/hide withdraw button
    const withdrawBtn = document.getElementById('withdrawBtn');
    withdrawBtn.style.display = app.status === 'pending' ? 'inline-flex' : 'none';
    withdrawBtn.onclick = () => withdrawApplication(app.id);
    
    // Open drawer
    document.getElementById('applicationDetailsDrawer').classList.add('active');
}

function closeApplicationDetailsDrawer() {
    document.getElementById('applicationDetailsDrawer').classList.remove('active');
}

function withdrawApplication(applicationId) {
    if (!confirm('Are you sure you want to withdraw this application?')) {
        return;
    }
    
    // In production, send to server
    console.log('Withdrawing application:', applicationId);
    
    showNotification('Application withdrawn successfully', 'success');
    closeApplicationDetailsDrawer();
    
    // Refresh applications
    setTimeout(() => {
        loadApplications();
        updateApplicationStats();
    }, 500);
}

function filterApplications(status) {
    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Filter applications
    const appCards = document.querySelectorAll('.application-card');
    appCards.forEach(card => {
        if (status === 'all') {
            card.style.display = 'flex';
        } else {
            const appStatus = card.querySelector('.status-badge').textContent.toLowerCase();
            card.style.display = appStatus === status ? 'flex' : 'none';
        }
    });
}

function searchApplications() {
    const searchTerm = document.getElementById('applicationSearchInput').value.toLowerCase();
    const appCards = document.querySelectorAll('.application-card');
    
    appCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? 'flex' : 'none';
    });
}

function updateApplicationStats() {
    const pending = applicationsData.filter(a => a.status === 'pending').length;
    const accepted = applicationsData.filter(a => a.status === 'accepted').length;
    const rejected = applicationsData.filter(a => a.status === 'rejected').length;
    const total = applicationsData.length;
    
    const pendingEl = document.getElementById('pendingCount');
    const acceptedEl = document.getElementById('acceptedCount');
    const rejectedEl = document.getElementById('rejectedCount');
    const totalEl = document.getElementById('totalCount');
    
    if (pendingEl) pendingEl.textContent = pending;
    if (acceptedEl) acceptedEl.textContent = accepted;
    if (rejectedEl) rejectedEl.textContent = rejected;
    if (totalEl) totalEl.textContent = total;
}

function refreshApplications() {
    showNotification('Refreshing applications...', 'info');
    loadApplications();
    updateApplicationStats();
}

// ================================================
// CONTRACTS & ASSIGNMENTS FUNCTIONS
// ================================================

function loadContracts() {
    const contractsGrid = document.getElementById('contractsGrid');
    if (!contractsGrid) return;
    
    contractsGrid.innerHTML = '';
    
    contractsData.forEach(contract => {
        const contractCard = createContractCard(contract);
        contractsGrid.appendChild(contractCard);
    });
}

function createContractCard(contract) {
    const card = document.createElement('div');
    card.className = 'contract-card';
    card.onclick = () => viewContractDetails(contract.id);
    
    card.innerHTML = `
        <div class="contract-header">
            <div class="company-avatar">${contract.companyAvatar}</div>
            <div>
                <h4>${contract.company}</h4>
                <p style="color: var(--text-secondary); font-size: 14px;">${contract.role}</p>
            </div>
        </div>
        <div style="margin: 12px 0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="color: var(--text-secondary); font-size: 14px;">Progress</span>
                <span style="color: var(--text-primary); font-weight: 600;">${contract.completedAssignments}/${contract.totalAssignments}</span>
            </div>
            <div style="height: 8px; background: var(--bg-tertiary); border-radius: 4px; overflow: hidden;">
                <div style="width: ${(contract.completedAssignments / contract.totalAssignments) * 100}%; height: 100%; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));"></div>
            </div>
        </div>
        <div class="contract-stats">
            <div class="contract-stat">
                <span class="contract-stat-value">LKR ${contract.rate.toLocaleString()}</span>
                <span class="contract-stat-label">Hourly Rate</span>
            </div>
            <div class="contract-stat">
                <span class="contract-stat-value">LKR ${contract.totalEarnings.toLocaleString()}</span>
                <span class="contract-stat-label">Total Earned</span>
            </div>
        </div>
    `;
    
    return card;
}

function viewContractDetails(contractId) {
    const contract = contractsData.find(c => c.id === contractId);
    if (!contract) return;
    
    // Populate contract details
    document.getElementById('contractCompanyAvatar').textContent = contract.companyAvatar;
    document.getElementById('contractCompanyName').textContent = contract.company;
    document.getElementById('contractRole').textContent = contract.role;
    document.getElementById('contractStartDate').textContent = formatDate(contract.startDate);
    document.getElementById('contractRate').textContent = `LKR ${contract.rate.toLocaleString()}/hr`;
    document.getElementById('contractType').textContent = contract.type;
    document.getElementById('totalAssignments').textContent = contract.totalAssignments;
    document.getElementById('completedAssignments').textContent = contract.completedAssignments;
    document.getElementById('totalEarnings').textContent = `LKR ${contract.totalEarnings.toLocaleString()}`;
    document.getElementById('companyEmail').textContent = contract.email;
    document.getElementById('companyPhone').textContent = contract.phone;
    
    // Update status badge
    const statusBadge = document.getElementById('contractStatus');
    statusBadge.textContent = contract.status.charAt(0).toUpperCase() + contract.status.slice(1);
    statusBadge.className = `status-badge ${contract.status}`;
    
    // Load contract assignments
    const assignmentsTimeline = document.getElementById('contractAssignments');
    assignmentsTimeline.innerHTML = '';
    const contractAssignments = assignmentsData.filter(a => a.contractId === contractId);
    
    contractAssignments.slice(0, 3).forEach(assignment => {
        const assignmentItem = document.createElement('div');
        assignmentItem.className = 'assignment-card';
        assignmentItem.onclick = (e) => {
            e.stopPropagation();
            viewAssignmentDetails(assignment.id);
        };
        assignmentItem.innerHTML = `
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <h5 style="margin: 0; font-size: 16px;">${assignment.title}</h5>
                <span class="status-badge ${assignment.status}">${assignment.status.replace('-', ' ')}</span>
            </div>
            <p style="color: var(--text-secondary); font-size: 14px; margin: 4px 0;">
                <i class="fas fa-calendar"></i> ${assignment.date} • ${assignment.time}
            </p>
        `;
        assignmentsTimeline.appendChild(assignmentItem);
    });
    
    // Store contract ID
    document.getElementById('contractDetailsDrawer').dataset.contractId = contractId;
    
    // Open drawer
    document.getElementById('contractDetailsDrawer').classList.add('active');
}

function closeContractDetailsDrawer() {
    document.getElementById('contractDetailsDrawer').classList.remove('active');
}

function loadAssignments() {
    const assignmentsList = document.getElementById('assignmentsList');
    if (!assignmentsList) return;
    
    assignmentsList.innerHTML = '';
    
    assignmentsData.forEach(assignment => {
        const assignmentCard = createAssignmentCard(assignment);
        assignmentsList.appendChild(assignmentCard);
    });
}

function createAssignmentCard(assignment) {
    const card = document.createElement('div');
    card.className = `assignment-card ${assignment.priority === 'urgent' ? 'urgent' : ''}`;
    card.onclick = () => viewAssignmentDetails(assignment.id);
    
    card.innerHTML = `
        <div class="assignment-header">
            <div class="assignment-info">
                <h4>${assignment.title}</h4>
                <p class="assignment-company">${assignment.company}</p>
                <div class="assignment-priority">
                    <span class="priority-badge ${assignment.priority}">${assignment.priority.charAt(0).toUpperCase() + assignment.priority.slice(1)} Priority</span>
                </div>
            </div>
            <span class="status-badge ${assignment.status}">${assignment.status.replace('-', ' ')}</span>
        </div>
        <div class="assignment-details">
            <div class="assignment-detail">
                <i class="fas fa-calendar"></i>
                <span>${assignment.date}</span>
            </div>
            <div class="assignment-detail">
                <i class="fas fa-clock"></i>
                <span>${assignment.time}</span>
            </div>
            <div class="assignment-detail">
                <i class="fas fa-map-marker-alt"></i>
                <span>${assignment.location}</span>
            </div>
            <div class="assignment-detail">
                <i class="fas fa-hourglass-half"></i>
                <span>${assignment.estimatedHours} hours</span>
            </div>
        </div>
    `;
    
    return card;
}

function viewAssignmentDetails(assignmentId) {
    const assignment = assignmentsData.find(a => a.id === assignmentId);
    if (!assignment) return;
    
    // Populate assignment details
    document.getElementById('assignmentTitle').textContent = assignment.title;
    document.getElementById('assignmentCompany').textContent = assignment.company;
    document.getElementById('assignmentDate').textContent = assignment.date;
    document.getElementById('assignmentTime').textContent = assignment.time;
    document.getElementById('assignmentLocation').textContent = assignment.location;
    document.getElementById('estimatedHours').textContent = `${assignment.estimatedHours} hours`;
    document.getElementById('assignmentDescription').textContent = assignment.description;
    
    // Update priority badge
    const priorityBadge = document.getElementById('assignmentPriority');
    priorityBadge.innerHTML = `<span class="priority-badge ${assignment.priority}">${assignment.priority.charAt(0).toUpperCase() + assignment.priority.slice(1)} Priority</span>`;
    
    // Update status badge
    const statusBadge = document.getElementById('assignmentStatus');
    statusBadge.textContent = assignment.status.replace('-', ' ').split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
    statusBadge.className = `status-badge ${assignment.status}`;
    
    // Set status dropdown
    document.getElementById('jobStatus').value = assignment.status;
    
    // Store assignment ID
    document.getElementById('assignmentDetailsDrawer').dataset.assignmentId = assignmentId;
    
    // Open drawer
    document.getElementById('assignmentDetailsDrawer').classList.add('active');
}

function closeAssignmentDetailsDrawer() {
    document.getElementById('assignmentDetailsDrawer').classList.remove('active');
}

function updateAssignmentStatus() {
    const status = document.getElementById('jobStatus').value;
    console.log('Status changed to:', status);
    // Status will be saved when user clicks "Save Update"
}

function saveProgressUpdate() {
    const assignmentId = document.getElementById('assignmentDetailsDrawer').dataset.assignmentId;
    const status = document.getElementById('jobStatus').value;
    const note = document.getElementById('progressNote').value;
    
    // In production, send to server
    console.log('Saving progress update:', { assignmentId, status, note });
    
    showNotification('Progress updated successfully!', 'success');
    
    // Update local data
    const assignment = assignmentsData.find(a => a.id == assignmentId);
    if (assignment) {
        assignment.status = status;
    }
    
    // Refresh UI
    loadAssignments();
}

function markAssignmentComplete() {
    const assignmentId = document.getElementById('assignmentDetailsDrawer').dataset.assignmentId;
    
    if (!confirm('Mark this assignment as complete?')) {
        return;
    }
    
    // In production, send to server
    console.log('Marking assignment complete:', assignmentId);
    
    showNotification('Assignment marked as complete!', 'success');
    closeAssignmentDetailsDrawer();
    
    // Update local data
    const assignment = assignmentsData.find(a => a.id == assignmentId);
    if (assignment) {
        assignment.status = 'completed';
    }
    
    // Refresh UI
    setTimeout(() => {
        loadAssignments();
        updateContractStats();
    }, 500);
}

function loadMessages() {
    const conversationsList = document.getElementById('conversationsList');
    if (!conversationsList) return;
    
    conversationsList.innerHTML = '';
    
    messagesData.forEach(message => {
        const conversationCard = createConversationCard(message);
        conversationsList.appendChild(conversationCard);
    });
    
    // Update unread count
    const unreadCount = messagesData.filter(m => m.unread).length;
    const unreadBadge = document.getElementById('sidebarUnreadCount');
    if (unreadBadge) {
        unreadBadge.textContent = unreadCount;
        unreadBadge.style.display = unreadCount > 0 ? 'inline-flex' : 'none';
    }
}

function createConversationCard(message) {
    const card = document.createElement('div');
    card.className = `conversation-card ${message.unread ? 'unread' : ''}`;
    card.onclick = () => openChatView(message.id);
    
    // Get last message preview
    const lastMsg = message.messages && message.messages.length > 0 
        ? message.messages[message.messages.length - 1].text 
        : message.lastMessage;
    
    card.innerHTML = `
        <div class="conversation-avatar">${message.companyAvatar}</div>
        <div class="conversation-content">
            <div class="conversation-header">
                <h4>${message.company}</h4>
                <span class="conversation-time">${message.time}</span>
            </div>
            <p class="conversation-project">${message.projectName}</p>
            <p class="conversation-preview">${lastMsg}</p>
        </div>
        ${message.unread ? '<div class="unread-indicator"></div>' : ''}
    `;
    
    return card;
}

let activeMessageId = null;

function openChatView(messageId) {
    const message = messagesData.find(m => m.id === messageId);
    if (!message) return;
    
    activeMessageId = messageId;
    
    // Hide empty state, show chat
    document.querySelector('.chat-empty-state').style.display = 'none';
    document.getElementById('chatActive').style.display = 'flex';
    
    // Populate chat header
    document.getElementById('activeChatAvatar').textContent = message.companyAvatar;
    document.getElementById('activeChatCompany').textContent = message.company;
    document.getElementById('activeChatProject').textContent = message.projectName;
    
    // Load messages
    loadChatMessages(message);
    
    // Mark as read
    message.unread = false;
    
    // Refresh conversation list to update unread status
    loadMessages();
    updateContractStats();
    
    // Highlight active conversation
    document.querySelectorAll('.conversation-card').forEach(card => {
        card.classList.remove('active');
    });
    event.target.closest('.conversation-card').classList.add('active');
}

function loadChatMessages(message) {
    const chatMessagesArea = document.getElementById('chatMessagesArea');
    chatMessagesArea.innerHTML = '';
    
    if (!message.messages || message.messages.length === 0) {
        chatMessagesArea.innerHTML = '<div class="no-messages">No messages yet</div>';
        return;
    }
    
    message.messages.forEach(msg => {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message-item ${msg.sender === 'repairer' ? 'sent' : 'received'}`;
        
        const senderName = msg.sender === 'repairer' ? 'FixLanka Team' : message.company;
        const avatarText = msg.sender === 'repairer' ? 'FL' : message.companyAvatar;
        
        messageDiv.innerHTML = `
            <div class="message-avatar">${avatarText}</div>
            <div class="message-content-wrapper">
                <div class="message-sender-info">
                    <span class="message-sender-name">${senderName}</span>
                    <span class="message-time">${msg.timestamp}</span>
                </div>
                <div class="message-bubble-text">${msg.text}</div>
            </div>
        `;
        
        chatMessagesArea.appendChild(messageDiv);
    });
    
    // Scroll to bottom
    chatMessagesArea.scrollTop = chatMessagesArea.scrollHeight;
}

function closeChatView() {
    document.querySelector('.chat-empty-state').style.display = 'flex';
    document.getElementById('chatActive').style.display = 'none';
    activeMessageId = null;
    
    // Remove active state from all conversations
    document.querySelectorAll('.conversation-card').forEach(card => {
        card.classList.remove('active');
    });
}

function sendChatMessage() {
    const chatInput = document.getElementById('chatInput');
    const messageText = chatInput.value.trim();
    
    if (!messageText) {
        showNotification('Please enter a message', 'error');
        return;
    }
    
    const message = messagesData.find(m => m.id == activeMessageId);
    if (!message) return;
    
    // Create timestamp
    const now = new Date();
    const hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    const timeStr = `Today, ${displayHours}:${minutes} ${ampm}`;
    
    // Add to local data
    message.messages.push({
        sender: 'repairer',
        text: messageText,
        timestamp: timeStr
    });
    
    // Update last message
    message.lastMessage = messageText;
    message.time = 'Just now';
    
    // Reload messages in chat
    loadChatMessages(message);
    
    // Clear input
    chatInput.value = '';
    chatInput.style.height = 'auto';
    
    // Refresh conversation list
    loadMessages();
    
    showNotification('Message sent successfully', 'success');
}

function handleChatKeyPress(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendChatMessage();
    }
}

// Legacy functions for drawer (kept for compatibility)
function viewMessageThread(messageId) {
    openChatView(messageId);
}

function closeMessageThreadDrawer() {
    closeChatView();
}

function sendMessage() {
    sendChatMessage();
}

function handleMessageKeyPress(event) {
    handleChatKeyPress(event);
}

function sendMessageToCompany() {
    const contractId = document.getElementById('contractDetailsDrawer').dataset.contractId;
    const contract = contractsData.find(c => c.id == contractId);
    
    if (!contract) return;
    
    // Find or create message thread
    let message = messagesData.find(m => m.contractId == contractId);
    
    if (!message) {
        // Create new message thread
        message = {
            id: messagesData.length + 1,
            contractId: contractId,
            company: contract.company,
            companyAvatar: contract.companyAvatar,
            projectName: `${contract.role} Contract`,
            lastMessage: '',
            time: 'Now',
            unread: false,
            messages: []
        };
        messagesData.push(message);
    }
    
    closeContractDetailsDrawer();
    
    // Switch to messages tab and open thread
    switchTab('messages');
    setTimeout(() => {
        viewMessageThread(message.id);
    }, 300);
}

function updateContractStats() {
    const activeContracts = contractsData.filter(c => c.status === 'active').length;
    const activeAssignments = assignmentsData.filter(a => a.status !== 'completed').length;
    const unreadMessages = messagesData.filter(m => m.unread).length;
    const completedJobs = assignmentsData.filter(a => a.status === 'completed').length;
    
    // Calculate total earnings from contracts
    let totalEarnings = 0;
    let pendingPayments = 0;
    
    contractsData.forEach(contract => {
        totalEarnings += contract.totalEarnings || 0;
    });
    
    assignmentsData.forEach(assignment => {
        if (assignment.paymentStatus === 'pending') {
            const amount = typeof assignment.totalPaid === 'string' 
                ? parseFloat(assignment.totalPaid.replace(/[^\d.]/g, ''))
                : assignment.totalPaid;
            pendingPayments += amount || 0;
        }
    });
    
    // Update contract tab stats
    const activeContractsEl = document.getElementById('activeContractsCount');
    const totalContractEarningsEl = document.getElementById('totalContractEarnings');
    const pendingPaymentsEl = document.getElementById('pendingPayments');
    const completedJobsEl = document.getElementById('completedJobsCount');
    
    if (activeContractsEl) activeContractsEl.textContent = activeContracts;
    if (totalContractEarningsEl) totalContractEarningsEl.textContent = `LKR ${totalEarnings.toLocaleString()}`;
    if (pendingPaymentsEl) pendingPaymentsEl.textContent = `LKR ${pendingPayments.toLocaleString()}`;
    if (completedJobsEl) completedJobsEl.textContent = completedJobs;
    
    // Update assignments tab stats
    const activeAssignmentsCountEl = document.getElementById('activeAssignmentsCount');
    const pendingAssignmentsEl = document.getElementById('pendingAssignments');
    const completedAssignmentsEl = document.getElementById('completedAssignments');
    const totalHoursEl = document.getElementById('totalHours');
    
    if (activeAssignmentsCountEl) activeAssignmentsCountEl.textContent = activeAssignments;
    if (pendingAssignmentsEl) {
        const pending = assignmentsData.filter(a => a.status === 'pending').length;
        pendingAssignmentsEl.textContent = pending;
    }
    if (completedAssignmentsEl) completedAssignmentsEl.textContent = completedJobs;
    if (totalHoursEl) {
        let totalHours = 0;
        assignmentsData.forEach(a => {
            totalHours += parseFloat(a.hoursWorked || 0);
        });
        totalHoursEl.textContent = `${totalHours}h`;
    }
    
    // Update messages tab stats
    const unreadMessagesCountEl = document.getElementById('unreadMessagesCount');
    const totalThreadsEl = document.getElementById('totalThreads');
    const sentMessagesEl = document.getElementById('sentMessages');
    
    if (unreadMessagesCountEl) unreadMessagesCountEl.textContent = unreadMessages;
    if (totalThreadsEl) totalThreadsEl.textContent = messagesData.length;
    if (sentMessagesEl) {
        let sentCount = 0;
        messagesData.forEach(msg => {
            sentCount += msg.messages.filter(m => m.sender === 'repairer').length;
        });
        sentMessagesEl.textContent = sentCount;
    }
    
    // Update badges
    const contractsBadge = document.getElementById('contractsBadge');
    const assignmentsBadge = document.getElementById('assignmentsBadge');
    const messagesBadge = document.getElementById('messagesBadge');
    
    if (contractsBadge) {
        contractsBadge.textContent = activeContracts;
        contractsBadge.style.display = activeContracts > 0 ? 'inline-block' : 'none';
    }
    if (assignmentsBadge) {
        assignmentsBadge.textContent = activeAssignments;
        assignmentsBadge.style.display = activeAssignments > 0 ? 'inline-block' : 'none';
    }
    if (messagesBadge) {
        messagesBadge.textContent = unreadMessages;
        messagesBadge.style.display = unreadMessages > 0 ? 'inline-block' : 'none';
    }
}

function switchTab(tabName) {
    // Update tab buttons within the current section
    const parentTabs = event.target.closest('.tabs');
    parentTabs.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Update tab content within the current section
    const parentContent = parentTabs.closest('.tab-content');
    if (parentContent) {
        parentContent.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        parentContent.querySelector(`#${tabName}Tab`).classList.add('active');
    }
}

function switchMainTab(tabName) {
    // Update main tab buttons
    document.querySelectorAll('.tabs-container > .tabs > .tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Update main tab content
    document.querySelectorAll('.content-wrapper > .tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    const targetTab = document.getElementById(`${tabName}Tab`);
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    // Load content for the active tab
    if (tabName === 'browse') {
        loadJobPostings();
    } else if (tabName === 'applications') {
        loadApplications();
    } else if (tabName === 'contracts') {
        loadContracts();
    } else if (tabName === 'assignments') {
        loadAssignments();
    } else if (tabName === 'messages') {
        loadMessages();
    }
}

function refreshCurrentTab() {
    const activeTab = document.querySelector('.tabs-container > .tabs > .tab-btn.active');
    if (!activeTab) return;
    
    const tabText = activeTab.textContent.trim().toLowerCase();
    
    if (tabText.includes('browse')) {
        showNotification('Refreshing job postings...', 'info');
        loadJobPostings();
    } else if (tabText.includes('applications')) {
        showNotification('Refreshing applications...', 'info');
        loadApplications();
    } else if (tabText.includes('contracts')) {
        showNotification('Refreshing contracts...', 'info');
        loadContracts();
    } else if (tabText.includes('assignments')) {
        showNotification('Refreshing assignments...', 'info');
        loadAssignments();
    } else if (tabText.includes('messages')) {
        showNotification('Refreshing messages...', 'info');
        loadMessages();
    }
}

function refreshContracts() {
    showNotification('Refreshing contracts...', 'info');
    loadContracts();
    updateContractStats();
}

function toggleAvailabilityStatus() {
    currentRepairerStatus = currentRepairerStatus === 'available' ? 'busy' : 'available';
    
    const statusBtn = document.getElementById('statusToggleBtn');
    const statusText = document.getElementById('statusText');
    const statusIcon = statusBtn.querySelector('i');
    
    if (currentRepairerStatus === 'available') {
        statusText.textContent = 'Available';
        if (statusIcon) statusIcon.style.color = '#2ecc71';
        showNotification('Status updated to Available', 'success');
    } else {
        statusText.textContent = 'Busy';
        if (statusIcon) statusIcon.style.color = '#e74c3c';
        showNotification('Status updated to Busy', 'info');
    }
    
    // In production, send to server
    console.log('Status changed to:', currentRepairerStatus);
}

// ================================================
// UTILITY FUNCTIONS
// ================================================

function getRelativeTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'today';
    if (diffDays === 1) return 'yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
    return date.toLocaleDateString();
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'long', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.notification').forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const icon = type === 'success' ? 'check-circle' : 
                 type === 'error' ? 'exclamation-circle' : 
                 type === 'warning' ? 'exclamation-triangle' : 'info-circle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--bg-secondary);
        color: var(--text-primary);
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 10000;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
        border-left: 4px solid ${type === 'success' ? '#27ae60' : 
                                  type === 'error' ? '#e74c3c' : 
                                  type === 'warning' ? '#f39c12' : '#3498db'};
    `;
    
    notification.querySelector('button').style.cssText = `
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 4px;
        margin-left: auto;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
