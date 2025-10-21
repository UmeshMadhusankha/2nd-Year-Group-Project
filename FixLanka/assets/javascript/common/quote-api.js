/**
 * Quote API Helper Functions
 * JavaScript library for easy integration with Quote CRUD API
 * 
 * NOTE: Database functionality removed - API calls will return error responses
 * This file is kept for UI structure only
 * 
 * Usage: Include this file in your HTML pages
 * <script src="assets/javascript/common/quote-api.js"></script>
 */

const QuoteAPI = {
    baseURL: 'api/quotes.php',

    /**
     * CREATE - Submit a new quote
     */
    createQuote: async function(data) {
        try {
            const formData = new FormData();
            formData.append('request_id', data.request_id);
            formData.append('repairer_id', data.repairer_id);
            formData.append('quoteAmount', data.quoteAmount);
            
            if (data.completion_date) {
                formData.append('completion_date', data.completion_date);
            }
            if (data.message) {
                formData.append('message', data.message);
            }

            const response = await fetch(`${this.baseURL}?action=create`, {
                method: 'POST',
                body: formData
            });

            return await response.json();
        } catch (error) {
            console.error('Error creating quote:', error);
            return { success: false, message: 'Network error occurred' };
        }
    },

    /**
     * READ - Get quote by ID
     */
    getQuoteById: async function(quoteId) {
        try {
            const response = await fetch(`${this.baseURL}?action=get&quote_id=${quoteId}`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching quote:', error);
            return { success: false, message: 'Network error occurred' };
        }
    },

    /**
     * READ - Get all quotes by repairer
     */
    getQuotesByRepairer: async function(repairerId, status = null) {
        try {
            let url = `${this.baseURL}?action=getByRepairer&repairer_id=${repairerId}`;
            if (status) {
                url += `&status=${status}`;
            }

            const response = await fetch(url);
            return await response.json();
        } catch (error) {
            console.error('Error fetching repairer quotes:', error);
            return { success: false, message: 'Network error occurred' };
        }
    },

    /**
     * READ - Get all quotes for a job request
     */
    getQuotesByRequest: async function(requestId) {
        try {
            const response = await fetch(`${this.baseURL}?action=getByRequest&request_id=${requestId}`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching request quotes:', error);
            return { success: false, message: 'Network error occurred' };
        }
    },

    /**
     * READ - Get accepted jobs for repairer
     */
    getAcceptedJobs: async function(repairerId) {
        try {
            const response = await fetch(`${this.baseURL}?action=getAccepted&repairer_id=${repairerId}`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching accepted jobs:', error);
            return { success: false, message: 'Network error occurred' };
        }
    },

    /**
     * READ - Get quote statistics
     */
    getStatistics: async function(repairerId) {
        try {
            const response = await fetch(`${this.baseURL}?action=getStats&repairer_id=${repairerId}`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching statistics:', error);
            return { success: false, message: 'Network error occurred' };
        }
    },

    /**
     * UPDATE - Update a quote
     */
    updateQuote: async function(quoteId, repairerId, updateData) {
        try {
            const formData = new FormData();
            formData.append('quote_id', quoteId);
            formData.append('repairer_id', repairerId);
            
            if (updateData.quoteAmount) {
                formData.append('quoteAmount', updateData.quoteAmount);
            }
            if (updateData.completion_date) {
                formData.append('completion_date', updateData.completion_date);
            }
            if (updateData.message !== undefined) {
                formData.append('message', updateData.message);
            }

            const response = await fetch(`${this.baseURL}?action=update`, {
                method: 'POST',
                body: formData
            });

            return await response.json();
        } catch (error) {
            console.error('Error updating quote:', error);
            return { success: false, message: 'Network error occurred' };
        }
    },

    /**
     * DELETE - Delete a quote
     */
    deleteQuote: async function(quoteId, repairerId) {
        try {
            const formData = new FormData();
            formData.append('quote_id', quoteId);
            formData.append('repairer_id', repairerId);

            const response = await fetch(`${this.baseURL}?action=delete`, {
                method: 'POST',
                body: formData
            });

            return await response.json();
        } catch (error) {
            console.error('Error deleting quote:', error);
            return { success: false, message: 'Network error occurred' };
        }
    },

    /**
     * ACCEPT - Accept a quote (User/Company)
     */
    acceptQuote: async function(quoteId, userId) {
        try {
            const formData = new FormData();
            formData.append('quote_id', quoteId);
            formData.append('user_id', userId);

            const response = await fetch(`${this.baseURL}?action=accept`, {
                method: 'POST',
                body: formData
            });

            return await response.json();
        } catch (error) {
            console.error('Error accepting quote:', error);
            return { success: false, message: 'Network error occurred' };
        }
    },

    /**
     * Helper: Format currency
     */
    formatCurrency: function(amount) {
        return `LKR ${parseFloat(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`;
    },

    /**
     * Helper: Format date
     */
    formatDate: function(dateString) {
        if (!dateString) return 'Not specified';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    },

    /**
     * Helper: Get status badge HTML
     */
    getStatusBadge: function(status) {
        const badges = {
            'pending': '<span class="badge badge-warning">Pending</span>',
            'accepted': '<span class="badge badge-success">Accepted</span>',
            'rejected': '<span class="badge badge-danger">Rejected</span>',
            'expired': '<span class="badge badge-secondary">Expired</span>'
        };
        return badges[status] || `<span class="badge badge-info">${status}</span>`;
    },

    /**
     * Helper: Show notification
     */
    showNotification: function(message, type = 'info') {
        // Basic implementation - can be replaced with better UI library
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show`;
        notification.role = 'alert';
        notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        document.body.insertBefore(notification, document.body.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
};

// Example usage functions that can be used in your pages

/**
 * Example: Submit Quote Form Handler
 */
function handleSubmitQuote(formElement) {
    formElement.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            request_id: document.getElementById('request_id').value,
            repairer_id: document.getElementById('repairer_id').value,
            quoteAmount: document.getElementById('quoteAmount').value,
            completion_date: document.getElementById('completion_date').value,
            message: document.getElementById('message').value
        };
        
        const result = await QuoteAPI.createQuote(formData);
        
        if (result.success) {
            QuoteAPI.showNotification('Quote submitted successfully!', 'success');
            formElement.reset();
            // Optionally reload quotes list or redirect
        } else {
            QuoteAPI.showNotification('Error: ' + result.message, 'error');
        }
    });
}

/**
 * Example: Load and Display Repairer's Quotes
 */
async function displayRepairerQuotes(repairerId, containerId, statusFilter = null) {
    const result = await QuoteAPI.getQuotesByRepairer(repairerId, statusFilter);
    const container = document.getElementById(containerId);
    
    if (!result.success) {
        container.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        return;
    }
    
    if (result.count === 0) {
        container.innerHTML = '<div class="alert alert-info">No quotes found.</div>';
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-striped">';
    html += '<thead><tr><th>ID</th><th>Job</th><th>Amount</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
    
    result.data.forEach(quote => {
        const actions = quote.status === 'pending' 
            ? `<button class="btn btn-sm btn-primary" onclick="editQuote(${quote.quote_id})">Edit</button>
               <button class="btn btn-sm btn-danger" onclick="confirmDeleteQuote(${quote.quote_id}, ${repairerId})">Delete</button>`
            : '-';
        
        html += `
            <tr>
                <td>${quote.quote_id}</td>
                <td>${quote.job_description}</td>
                <td>${QuoteAPI.formatCurrency(quote.quoteAmount)}</td>
                <td>${QuoteAPI.formatDate(quote.completion_date)}</td>
                <td>${QuoteAPI.getStatusBadge(quote.status)}</td>
                <td>${actions}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

/**
 * Example: Load and Display Quotes for a Job Request (User View)
 */
async function displayJobQuotes(requestId, containerId) {
    const result = await QuoteAPI.getQuotesByRequest(requestId);
    const container = document.getElementById(containerId);
    
    if (!result.success) {
        container.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        return;
    }
    
    if (result.count === 0) {
        container.innerHTML = '<div class="alert alert-info">No quotes received yet.</div>';
        return;
    }
    
    let html = '';
    result.data.forEach(quote => {
        html += `
            <div class="quote-card card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">${quote.repairer_fname} ${quote.repairer_lname}</h5>
                            <p class="card-text">
                                <strong>Rating:</strong> ${quote.repairer_rating} ⭐ | 
                                <strong>Completed Jobs:</strong> ${quote.completedJobsCount}
                            </p>
                            <p class="card-text">${quote.message}</p>
                            <p class="text-muted">
                                <small>Submitted: ${QuoteAPI.formatDate(quote.dateSubmitted)}</small>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <h3 class="text-primary">${QuoteAPI.formatCurrency(quote.quoteAmount)}</h3>
                            <p>Completion: ${QuoteAPI.formatDate(quote.completion_date)}</p>
                            ${quote.status === 'pending' 
                                ? `<button class="btn btn-success" onclick="confirmAcceptQuote(${quote.quote_id})">Accept Quote</button>`
                                : QuoteAPI.getStatusBadge(quote.status)
                            }
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * Example: Confirm and Delete Quote
 */
async function confirmDeleteQuote(quoteId, repairerId) {
    if (!confirm('Are you sure you want to delete this quote?')) {
        return;
    }
    
    const result = await QuoteAPI.deleteQuote(quoteId, repairerId);
    
    if (result.success) {
        QuoteAPI.showNotification('Quote deleted successfully!', 'success');
        // Refresh the quotes list
        displayRepairerQuotes(repairerId, 'quotesContainer');
    } else {
        QuoteAPI.showNotification('Error: ' + result.message, 'error');
    }
}

/**
 * Example: Confirm and Accept Quote
 */
async function confirmAcceptQuote(quoteId, userId) {
    if (!confirm('Are you sure you want to accept this quote? Other quotes will be rejected.')) {
        return;
    }
    
    const result = await QuoteAPI.acceptQuote(quoteId, userId);
    
    if (result.success) {
        QuoteAPI.showNotification('Quote accepted! Redirecting to job details...', 'success');
        // Redirect to job page
        setTimeout(() => {
            window.location.href = `job-details.php?job_id=${result.job_id}`;
        }, 2000);
    } else {
        QuoteAPI.showNotification('Error: ' + result.message, 'error');
    }
}

/**
 * Example: Display Quote Statistics Dashboard
 */
async function displayQuoteStatistics(repairerId, containerId) {
    const result = await QuoteAPI.getStatistics(repairerId);
    const container = document.getElementById(containerId);
    
    if (!result.success) {
        container.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        return;
    }
    
    const stats = result.data;
    const acceptanceRate = stats.total_quotes > 0 
        ? ((stats.accepted_quotes / stats.total_quotes) * 100).toFixed(1)
        : 0;
    
    const html = `
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card card text-center">
                    <div class="card-body">
                        <h3>${stats.total_quotes}</h3>
                        <p>Total Quotes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card card text-center">
                    <div class="card-body">
                        <h3 class="text-warning">${stats.pending_quotes}</h3>
                        <p>Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card card text-center">
                    <div class="card-body">
                        <h3 class="text-success">${stats.accepted_quotes}</h3>
                        <p>Accepted</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card card text-center">
                    <div class="card-body">
                        <h3>${acceptanceRate}%</h3>
                        <p>Success Rate</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <div class="card">
                <div class="card-body">
                    <h5>Average Quote Amount</h5>
                    <h3 class="text-primary">${QuoteAPI.formatCurrency(stats.avg_quote_amount || 0)}</h3>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}
