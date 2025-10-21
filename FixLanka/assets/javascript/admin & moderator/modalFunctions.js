// Modal Functions for Notifications System

// Global variables
let currentNotificationId = null

/**
 * Open a modal by ID
 */
function openModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.classList.add("active")
    document.body.style.overflow = "hidden"

    // Initialize Lucide icons in modal
    if (typeof lucide !== "undefined") {
      lucide.createIcons()
    }
  }
}

/**
 * Close a modal by ID
 */
function closeModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.classList.add("closing")

    setTimeout(() => {
      modal.classList.remove("active", "closing")
      document.body.style.overflow = ""
    }, 300)
  }
}

/**
 * Close modal when clicking outside
 */
document.addEventListener("click", (event) => {
  if (event.target.classList.contains("modal-overlay")) {
    const modalId = event.target.id
    closeModal(modalId)
  }
})

/**
 * Close modal on Escape key
 */
document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    const activeModal = document.querySelector(".modal-overlay.active")
    if (activeModal) {
      closeModal(activeModal.id)
    }
  }
})

/**
 * View notification details
 */
function viewNotificationDetails(notification) {
  const content = document.getElementById("notificationDetailsContent")

  if (!content) return

  const priorityColors = {
    low: "text-blue-600",
    medium: "text-yellow-600",
    high: "text-red-600",
  }

  const statusColors = {
    Sent: "text-green-600",
    Draft: "text-gray-600",
    Scheduled: "text-blue-600",
  }

  content.innerHTML = `
        <div class="space-y-4">
            ${
              notification.title
                ? `
            <div>
                <h4 class="text-sm font-medium text-muted-foreground mb-2">Title</h4>
                <p class="text-foreground">${notification.title}</p>
            </div>
            `
                : ""
            }
            
            <div>
                <h4 class="text-sm font-medium text-muted-foreground mb-2">Message</h4>
                <p class="text-foreground">${notification.message}</p>
            </div>
            
            <div class="detail-grid">
                <div class="detail-group">
                    <div class="detail-label">Status</div>
                    <div class="detail-value ${statusColors[notification.status] || ""}">${notification.status}</div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Priority</div>
                    <div class="detail-value ${priorityColors[notification.priority] || ""}">${notification.priority.toUpperCase()}</div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Recipients</div>
                    <div class="detail-value">${notification.recipients}</div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Category</div>
                    <div class="detail-value">${notification.category}</div>
                </div>
            </div>
            
            ${
              notification.status === "Sent" && notification.delivery_rate
                ? `
                <div class="detail-grid">
                    <div class="detail-group">
                        <div class="detail-label">Sent At</div>
                        <div class="detail-value">${notification.sent_at}</div>
                    </div>
                    
                    <div class="detail-group">
                        <div class="detail-label">Delivery Rate</div>
                        <div class="detail-value">${notification.delivery_rate}%</div>
                    </div>
                    
                    <div class="detail-group">
                        <div class="detail-label">Open Rate</div>
                        <div class="detail-value">${notification.open_rate}%</div>
                    </div>
                    
                    ${
                      notification.total_recipients
                        ? `
                    <div class="detail-group">
                        <div class="detail-label">Total Recipients</div>
                        <div class="detail-value">${notification.total_recipients}</div>
                    </div>
                    `
                        : ""
                    }
                </div>
            `
                : ""
            }
            
            <div class="detail-group">
                <div class="detail-label">Created At</div>
                <div class="detail-value">${notification.created_at}</div>
            </div>
        </div>
    `

  openModal("notificationDetailsModal")
}

/**
 * Edit notification
 */
function editNotification(notificationId) {
    // Find notification from global notifications array
    const notification = window.allNotifications?.find((n) => n.id === notificationId);

    if (!notification) {
        showError("Notification not found");
        return;
    }

    // Populate form
    const form = document.getElementById("editNotificationForm");
    if (form) {
        form.querySelector('[name="id"]').value = notification.id || "";
        form.querySelector('[name="title"]').value = notification.title || "";
        form.querySelector('[name="message"]').value = notification.message || "";
        form.querySelector('[name="priority"]').value = notification.priority || "low";
        form.querySelector('[name="status"]').value = notification.status || "Draft";

        // Open the modal
        const modal = document.getElementById("editNotificationModal");
        if (modal) {
            modal.classList.add("active");
            document.body.style.overflow = "hidden";
        }
    }

    currentNotificationId = notificationId;
}

/**
 * Handle edit form submission (if separate edit modal exists)
 */
document.addEventListener("DOMContentLoaded", () => {
  const editForm = document.getElementById("editNotificationForm")

  if (editForm) {
    editForm.addEventListener("submit", async (e) => {
      e.preventDefault()

      const formData = new FormData(editForm)
      const notificationId = formData.get("id")

      const saveBtn = document.getElementById("saveEditBtn")
      const saveBtnText = document.getElementById("saveEditBtnText")

      // Show loading state
      saveBtn.disabled = true
      saveBtnText.textContent = "Saving..."

      try {
        const response = await fetch(`${window.basePath || ""}/api/notifications/${notificationId}`, {
          method: "PUT",
          body: formData,
        })

        const result = await response.json()

        if (!response.ok || !result.success) {
          throw new Error(result.error || "Failed to update notification")
        }

        // Update notification in global array
        if (window.allNotifications) {
          console.log("Updating notification in global array...");
          const index = window.allNotifications.findIndex((n) => n.id === Number.parseInt(notificationId));
          console.log("Notification index found:", index);
          if (index !== -1) {
            console.log("Existing notification data:", window.allNotifications[index]);
            window.allNotifications[index] = {
              ...window.allNotifications[index],
              ...result.data,
            };
            filteredNotifications = [...window.allNotifications]
            renderNotifications()
            console.log("Updated notification data:", window.allNotifications[index]);
          } else {
            console.log("Notification not found in global array.");
          }
        } else {
          console.log("Global notifications array is not defined.");
        }

        // Close modal and show success
        closeModal("editNotificationModal")
        showSuccess(result.message || "Notification updated successfully")

        // Refresh notifications display
        if (typeof renderNotifications === "function") {
          renderNotifications()
        }
      } catch (error) {
        console.error("[v0] Error updating notification:", error)
        showError(error.message)
      } finally {
        // Reset button
        saveBtn.disabled = false
        saveBtnText.textContent = "Save Changes"
      }
    })
  }
})

/**
 * Open video notification modal
 */
function openVideoModal() {
  openModal("videoNotificationModal")
}

/**
 * Toggle video source (upload vs URL)
 */
function toggleVideoSource(source) {
  const uploadSection = document.getElementById("videoUploadSection")
  const urlSection = document.getElementById("videoUrlSection")

  if (source === "upload") {
    uploadSection.classList.remove("hidden")
    urlSection.classList.add("hidden")
  } else {
    uploadSection.classList.add("hidden")
    urlSection.classList.remove("hidden")
  }
}

/**
 * Handle video file upload
 */
function handleVideoUpload(event) {
  const file = event.target.files[0]

  if (!file) return

  // Validate file size (50MB max)
  if (file.size > 50 * 1024 * 1024) {
    showError("Video file size must be less than 50MB")
    event.target.value = ""
    return
  }

  // Validate file type
  if (!file.type.startsWith("video/")) {
    showError("Please select a valid video file")
    event.target.value = ""
    return
  }

  // Show preview
  const preview = document.getElementById("videoPreview")
  const previewVideo = document.getElementById("previewVideo")
  const fileName = document.getElementById("videoFileName")

  const url = URL.createObjectURL(file)
  previewVideo.src = url
  fileName.textContent = file.name
  preview.classList.remove("hidden")
}

/**
 * Handle video notification form submission
 */
document.addEventListener("DOMContentLoaded", () => {
  const videoForm = document.getElementById("videoNotificationForm")

  if (videoForm) {
    videoForm.addEventListener("submit", async (e) => {
      e.preventDefault()

      const formData = new FormData(videoForm)
      const sendBtn = document.getElementById("sendVideoBtn")
      const sendBtnText = document.getElementById("sendVideoBtnText")

      // Show loading state
      sendBtn.disabled = true
      sendBtnText.textContent = "Sending..."

      try {
        const response = await fetch(`${window.basePath || ""}/api/notifications/video`, {
          method: "POST",
          body: formData,
        })

        const result = await response.json()

        if (!response.ok || !result.success) {
          throw new Error(result.error || "Failed to send video notification")
        }

        // Reset form
        videoForm.reset()
        document.getElementById("videoPreview").classList.add("hidden")

        // Close modal and show success
        closeModal("videoNotificationModal")
        showSuccess(result.message || "Video notification sent successfully")

        // Refresh notifications
        if (typeof refreshNotifications === "function") {
          refreshNotifications()
        }
      } catch (error) {
        console.error("[v0] Error sending video notification:", error)
        showError(error.message)
      } finally {
        // Reset button
        sendBtn.disabled = false
        sendBtnText.textContent = "Send Video Notification"
      }
    })
  }
})

/**
 * Delete notification
 */
function deleteNotification(notificationId) {
  currentDeleteId = notificationId
  document.getElementById("deleteConfirmMessage").textContent =
    "Are you sure you want to delete this notification? This action cannot be undone."
  openModal("deleteConfirmModal")
}

/**
 * Confirm delete action
 */
async function confirmDelete() {
  if (!currentDeleteId) return

  const confirmBtn = document.getElementById("confirmDeleteBtn")
  const originalHTML = confirmBtn.innerHTML

  confirmBtn.disabled = true
  confirmBtn.innerHTML = '<i data-lucide="loader" class="mr-2 h-4 w-4 animate-spin"></i> Deleting...'

  try {
    const response = await fetch(`${window.basePath || ""}/api/notifications/${currentDeleteId}`, {
      method: "DELETE",
    })

    const result = await response.json()

    if (!response.ok || !result.success) {
      throw new Error(result.error || "Failed to delete notification")
    }

    // Remove from global array
    if (window.allNotifications) {
      window.allNotifications = window.allNotifications.filter((n) => n.id !== currentDeleteId)
    }

    // Close modal and show success
    closeModal("deleteConfirmModal")
    showSuccess(result.message || "Notification deleted successfully")

    // Refresh notifications display
    if (typeof renderNotifications === "function") {
      renderNotifications()
    }
    if (typeof loadNotifications === "function") {
      loadNotifications()
    }

    currentDeleteId = null
  } catch (error) {
    console.error("[v0] Error deleting notification:", error)
    showError(error.message)
  } finally {
    // Reset button
    confirmBtn.disabled = false
    confirmBtn.innerHTML = originalHTML

    // Reinitialize icons
    if (typeof lucide !== "undefined") {
      lucide.createIcons()
    }
  }
}

/**
 * Send draft notification
 */
async function sendDraft(notificationId) {
  if (!confirm("Are you sure you want to send this notification?")) {
    return
  }

  showLoading()

  try {
    const response = await fetch(`${window.basePath || ""}/api/notifications/send`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: notificationId }),
    })

    const result = await response.json()

    if (!response.ok || !result.success) {
      throw new Error(result.error || "Failed to send notification")
    }

    // Update notification status in global array
    if (window.allNotifications) {
      const notification = window.allNotifications.find((n) => n.id === notificationId)
      if (notification) {
        notification.status = "Sent"
        notification.sent_at = result.data?.sent_at || new Date().toISOString().slice(0, 16).replace("T", " ")
        notification.delivery_rate = result.data?.delivery_rate || "95"
        notification.open_rate = result.data?.open_rate || "32"
      }
    }

    hideLoading()
    showSuccess(result.message || "Draft sent successfully")

    // Refresh notifications display
    if (typeof renderNotifications === "function") {
      renderNotifications()
    }
    if (typeof loadNotifications === "function") {
      loadNotifications()
    }
  } catch (error) {
    console.error("[v0] Error sending draft:", error)
    hideLoading()
    showError(error.message)
  }
}
