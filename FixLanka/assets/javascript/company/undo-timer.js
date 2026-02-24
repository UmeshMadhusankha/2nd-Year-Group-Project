/**
 * 24-Hour Undo Window System
 * Provides countdown timer and undo functionality for contracts
 * 
 * @package FixLanka
 * @version 1.0.0
 */

// Active timers storage
const activeTimers = new Map();

/**
 * Check if contract has active undo window
 * @param {Object} contract - Contract object with undo_deadline
 * @returns {boolean} - True if undo window is active
 */
function hasActiveUndoWindow(contract) {
    if (!contract.undo_deadline || contract.status !== 'active') {
        return false;
    }
    
    const deadlineTime = new Date(contract.undo_deadline).getTime();
    const now = new Date().getTime();
    const timeLeft = deadlineTime - now;
    
    // Active if within 24 hours (86400000 ms)
    return timeLeft > 0 && timeLeft <= 86400000;
}

/**
 * Initialize undo window UI for a contract
 * @param {Object} contract - Contract object
 * @param {string} containerId - ID of container element
 */
function initializeUndoWindow(contract, containerId) {
    if (!hasActiveUndoWindow(contract)) {
        return;
    }
    
    const container = document.getElementById(containerId);
    if (!container) {
        console.warn(`Container ${containerId} not found`);
        return;
    }
    
    // Create undo window HTML
    const undoHTML = createUndoWindowHTML(contract);
    container.innerHTML = undoHTML;
    container.style.display = 'block';
    
    // Start countdown timer
    const deadlineTime = new Date(contract.undo_deadline).getTime();
    startUndoCountdown(contract.contract_id, deadlineTime);
}

/**
 * Create HTML for undo window
 * @param {Object} contract - Contract object
 * @returns {string} - HTML string
 */
function createUndoWindowHTML(contract) {
    return `
        <div class="undo-timer-card" data-contract-id="${contract.contract_id}">
            <div class="undo-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="undo-content">
                <h4>24-Hour Cancellation Window</h4>
                <p>You have <span class="countdown-timer" id="undoTimer-${contract.contract_id}">calculating...</span> remaining to cancel this contract free of charge.</p>
                <small class="undo-note">No questions asked, full refund guaranteed</small>
            </div>
            <button class="btn-undo-contract" onclick="requestUndo(${contract.contract_id}, '${contract.project_title || 'this contract'}')">
                <i class="fas fa-times-circle"></i>
                Cancel Contract
            </button>
        </div>
    `;
}

/**
 * Start countdown timer for undo window
 * @param {number} contractId - Contract ID
 * @param {number} deadlineTime - Deadline timestamp
 */
function startUndoCountdown(contractId, deadlineTime) {
    const timerElement = document.getElementById(`undoTimer-${contractId}`);
    if (!timerElement) return;
    
    // Clear existing timer if any
    if (activeTimers.has(contractId)) {
        clearInterval(activeTimers.get(contractId));
    }
    
    const updateTimer = () => {
        const now = new Date().getTime();
        const distance = deadlineTime - now;
        
        if (distance < 0) {
            // Timer expired
            clearInterval(activeTimers.get(contractId));
            activeTimers.delete(contractId);
            timerElement.textContent = 'EXPIRED';
            timerElement.classList.add('expired');
            
            // Hide undo button
            const card = timerElement.closest('.undo-timer-card');
            if (card) {
                card.classList.add('expired');
                const button = card.querySelector('.btn-undo-contract');
                if (button) button.disabled = true;
            }
            
            // Optionally hide entire section after 5 seconds
            setTimeout(() => {
                const container = timerElement.closest('.undo-window-section');
                if (container) {
                    container.style.display = 'none';
                }
            }, 5000);
            
            return;
        }
        
        // Calculate time components
        const hours = Math.floor(distance / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Format as HH:MM:SS
        const formattedTime = 
            `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        
        timerElement.textContent = formattedTime;
        
        // Add urgency styling if less than 1 hour
        if (hours < 1) {
            timerElement.classList.add('urgent');
        }
    };
    
    // Update immediately
    updateTimer();
    
    // Update every second
    const intervalId = setInterval(updateTimer, 1000);
    activeTimers.set(contractId, intervalId);
}

/**
 * Request to undo/cancel a contract
 * @param {number} contractId - Contract ID
 * @param {string} contractTitle - Contract title for confirmation
 */
async function requestUndo(contractId, contractTitle) {
    // Confirmation dialog
    const confirmed = confirm(
        `⚠️ CANCEL CONTRACT\n\n` +
        `Are you sure you want to cancel "${contractTitle}"?\n\n` +
        `This will:\n` +
        `✓ Cancel the contract immediately\n` +
        `✓ Refund any payments made\n` +
        `✓ Notify the company\n` +
        `✓ Release any escrow funds\n\n` +
        `This action cannot be undone.\n\n` +
        `Click OK to proceed with cancellation.`
    );
    
    if (!confirmed) return;
    
    // Show loading state
    const button = document.querySelector(`button[onclick*="requestUndo(${contractId}"]`);
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
    }
    
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'request_undo',
                contract_id: contractId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Success message
            showSuccessMessage('Contract cancelled successfully!', 'Your contract has been cancelled and any payments will be refunded within 3-5 business days.');
            
            // Reload page after 2 seconds
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            // Error message
            showErrorMessage('Cancellation Failed', result.message || 'Unable to cancel contract. Please contact support.');
            
            // Restore button
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-times-circle"></i> Cancel Contract';
            }
        }
    } catch (error) {
        console.error('Undo request error:', error);
        showErrorMessage('Network Error', 'Unable to connect to server. Please check your connection and try again.');
        
        // Restore button
        if (button) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-times-circle"></i> Cancel Contract';
        }
    }
}

/**
 * Show success message modal
 * @param {string} title - Message title
 * @param {string} message - Message content
 */
function showSuccessMessage(title, message) {
    // Create modal if doesn't exist
    let modal = document.getElementById('undoSuccessModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'undoSuccessModal';
        modal.className = 'undo-modal success';
        document.body.appendChild(modal);
    }
    
    modal.innerHTML = `
        <div class="undo-modal-content">
            <div class="undo-modal-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>${title}</h3>
            <p>${message}</p>
            <button class="btn-close-modal" onclick="closeUndoModal()">OK</button>
        </div>
    `;
    modal.style.display = 'flex';
}

/**
 * Show error message modal
 * @param {string} title - Error title
 * @param {string} message - Error message
 */
function showErrorMessage(title, message) {
    // Create modal if doesn't exist
    let modal = document.getElementById('undoErrorModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'undoErrorModal';
        modal.className = 'undo-modal error';
        document.body.appendChild(modal);
    }
    
    modal.innerHTML = `
        <div class="undo-modal-content">
            <div class="undo-modal-icon error">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3>${title}</h3>
            <p>${message}</p>
            <button class="btn-close-modal" onclick="closeUndoModal()">Close</button>
        </div>
    `;
    modal.style.display = 'flex';
}

/**
 * Close undo modal
 */
function closeUndoModal() {
    const successModal = document.getElementById('undoSuccessModal');
    const errorModal = document.getElementById('undoErrorModal');
    if (successModal) successModal.style.display = 'none';
    if (errorModal) errorModal.style.display = 'none';
}

/**
 * Clean up all active timers (call on page unload)
 */
function cleanupUndoTimers() {
    activeTimers.forEach((intervalId) => clearInterval(intervalId));
    activeTimers.clear();
}

// Cleanup on page unload
window.addEventListener('beforeunload', cleanupUndoTimers);

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        hasActiveUndoWindow,
        initializeUndoWindow,
        startUndoCountdown,
        requestUndo,
        cleanupUndoTimers
    };
}
