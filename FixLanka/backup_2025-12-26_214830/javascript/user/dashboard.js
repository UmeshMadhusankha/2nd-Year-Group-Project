// Handle navigation selection
        function initializeNavigation() {
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            
            navLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href.startsWith('#')) {
                        e.preventDefault();
                        
                        // Remove active class from all nav items
                        document.querySelectorAll('.sidebar .nav-item').forEach(function(item) {
                            item.classList.remove('active');
                        });
                        
                        // Add active class to clicked item
                        this.parentElement.classList.add('active');
                    }
                });
            });
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', initializeNavigation);

        // Availability Toggle Functionality
        function initializeAvailabilityToggle() {
            const toggleBtn = document.getElementById('toggle-availability');
            const statusElement = document.getElementById('availability-status');
            const statusText = statusElement.querySelector('.status-text');
            
            // Elements to update
            const greetingAvailability = document.getElementById('greeting-availability');
            const availabilityStatCard = document.getElementById('availability-stat-card');
            const availabilityIcon = document.getElementById('availability-icon');
            const availabilityMainStatus = document.getElementById('availability-main-status');
            const availabilityDescription = document.getElementById('availability-description');
            
            toggleBtn.addEventListener('click', function() {
                if (statusElement.classList.contains('online')) {
                    // Switch to offline
                    statusElement.classList.remove('online');
                    statusElement.classList.add('offline');
                    statusText.textContent = 'Offline';
                    this.classList.add('offline');
                    
                    // Update greeting card
                    greetingAvailability.textContent = 'Not accepting new jobs';
                    
                    // Update stats card
                    availabilityStatCard.classList.remove('availability');
                    availabilityStatCard.classList.add('unavailable');
                    availabilityIcon.className = 'fas fa-times-circle';
                    availabilityMainStatus.textContent = 'Unavailable';
                    availabilityDescription.textContent = 'Currently offline';
                    availabilityDescription.className = 'stat-change unavailable';
                    
                } else {
                    // Switch to online
                    statusElement.classList.remove('offline');
                    statusElement.classList.add('online');
                    statusText.textContent = 'Online';
                    this.classList.remove('offline');
                    
                    // Update greeting card
                    greetingAvailability.textContent = 'Ready for new jobs';
                    
                    // Update stats card
                    availabilityStatCard.classList.remove('unavailable');
                    availabilityStatCard.classList.add('availability');
                    availabilityIcon.className = 'fas fa-check-circle';
                    availabilityMainStatus.textContent = 'Available';
                    availabilityDescription.textContent = 'Ready for new jobs';
                    availabilityDescription.className = 'stat-change available';
                }
            });
        }

        // Initialize availability toggle
        document.addEventListener('DOMContentLoaded', initializeAvailabilityToggle);