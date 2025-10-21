// Main Notifications JavaScript

// Global variables
let currentView = "list"
let currentPage = 1
const itemsPerPage = 10
let filteredNotifications = []
let currentDeleteId = null

/**
 * Initialize the page
 */
document.addEventListener("DOMContentLoaded", () => {
  initializePage()
  setupEventListeners()
})

/**
 * Initialize page data
 */
async function initializePage() {
  await loadNotifications()

  // Initialize Lucide icons
  if (typeof window.lucide !== "undefined") {
    window.lucide.createIcons()
  }
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
  // Notification form submission
  const notificationForm = document.getElementById("notificationForm")
  if (notificationForm) {
    notificationForm.addEventListener("submit", handleNotificationSubmit)
  }
}

/**
 * Load notifications from server
 */
async function loadNotifications() {
  showLoading()

  try {
    const basePath = window.basePath || ""
    const response = await fetch(`${basePath}/api/notifications?limit=100&sort=newest`)

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const result = await response.json()
    console.log("[v0] Notifications loaded:", result)

    if (result.success) {
      window.allNotifications = result.data
      filteredNotifications = [...window.allNotifications]

      // Update notification count
      const notificationCount = document.getElementById("notificationCount")
      if (notificationCount) {
        notificationCount.textContent = `(${window.allNotifications.length} total)`
      }

      hideLoading()
      renderStats()
      renderNotifications()
      updatePagination()

      // Initialize Lucide icons
      if (typeof window.lucide !== "undefined") {
        window.lucide.createIcons()
      }
    } else {
      throw new Error(result.error || "Failed to load notifications")
    }
  } catch (error) {
    console.error("[v0] Error loading notifications:", error)
    hideLoading()
    showError("Failed to load notifications: " + error.message)
  }
}

/**
 * Render statistics
 */
function renderStats() {
  const statsContainer = document.getElementById("statsContainer")
  if (!statsContainer) return

  const totalCount = window.allNotifications.length
  const sentCount = window.allNotifications.filter((n) => n.status === "Sent").length
  const draftCount = window.allNotifications.filter((n) => n.status === "Draft").length
  const scheduledCount = window.allNotifications.filter((n) => n.status === "Scheduled").length

  const todayCount = window.allNotifications.filter((n) => {
    if (!n.sent_at) return false
    const today = new Date().toISOString().slice(0, 10)
    return n.sent_at.startsWith(today)
  }).length

  const sentNotifications = window.allNotifications.filter((n) => n.delivery_rate)
  const avgDelivery =
    sentNotifications.length > 0
      ? sentNotifications.reduce((sum, n) => sum + Number.parseFloat(n.delivery_rate), 0) / sentNotifications.length
      : 0

  const avgOpen =
    sentNotifications.length > 0
      ? sentNotifications.reduce((sum, n) => sum + Number.parseFloat(n.open_rate || 0), 0) / sentNotifications.length
      : 0

  statsContainer.innerHTML = `
        <div class="grid gap-4 grid-cols-4">
            <div class="bg-card rounded-lg border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Total</p>
                        <p class="text-2xl font-bold text-foreground mt-2">${totalCount}</p>
                        <p class="text-xs text-muted-foreground mt-1">All notifications</p>
                    </div>
                    <i data-lucide="bell" class="h-8 w-8 text-blue-600"></i>
                </div>
            </div>
            
            <div class="bg-card rounded-lg border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Sent</p>
                        <p class="text-2xl font-bold text-foreground mt-2">${sentCount}</p>
                        <p class="text-xs text-muted-foreground mt-1">Successfully delivered</p>
                    </div>
                    <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
                </div>
            </div>
            
            <div class="bg-card rounded-lg border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Drafts</p>
                        <p class="text-2xl font-bold text-foreground mt-2">${draftCount}</p>
                        <p class="text-xs text-muted-foreground mt-1">Unsent messages</p>
                    </div>
                    <i data-lucide="edit" class="h-8 w-8 text-yellow-600"></i>
                </div>
            </div>
            
            <div class="bg-card rounded-lg border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Today</p>
                        <p class="text-2xl font-bold text-foreground mt-2">${todayCount}</p>
                        <p class="text-xs text-muted-foreground mt-1">Sent today</p>
                    </div>
                    <i data-lucide="calendar" class="h-8 w-8 text-purple-600"></i>
                </div>
            </div>
            
            <div class="bg-card rounded-lg border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Delivery</p>
                        <p class="text-2xl font-bold text-foreground mt-2">${Math.round(avgDelivery)}%</p>
                        <p class="text-xs text-muted-foreground mt-1">Average rate</p>
                    </div>
                    <i data-lucide="trending-up" class="h-8 w-8 text-green-600"></i>
                </div>
            </div>
            
            <div class="bg-card rounded-lg border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Open Rate</p>
                        <p class="text-2xl font-bold text-foreground mt-2">${Math.round(avgOpen)}%</p>
                        <p class="text-xs text-muted-foreground mt-1">Average rate</p>
                    </div>
                    <i data-lucide="eye" class="h-8 w-8 text-blue-600"></i>
                </div>
            </div>
        </div>
    `

  // Reinitialize icons
  if (typeof window.lucide !== "undefined") {
    window.lucide.createIcons()
  }
}

/**
 * Render notifications based on current view
 */
function renderNotifications() {
  const loader = document.getElementById("notificationsLoader")
  const listView = document.getElementById("notificationsList")
  const gridView = document.getElementById("notificationsGrid")
  const tableView = document.getElementById("notificationsTable")
  const notificationCount = document.getElementById("notificationCount")

  if (loader) loader.style.display = "none"

  // Update count
  if (notificationCount) {
    notificationCount.textContent = `(${filteredNotifications.length} notifications)`
  }

  // Get paginated data
  const startIndex = (currentPage - 1) * itemsPerPage
  const endIndex = startIndex + itemsPerPage
  const paginatedNotifications = filteredNotifications.slice(startIndex, endIndex)

  // Render based on view
  if (currentView === "list") {
    if (listView) {
      listView.style.display = "block"
      listView.innerHTML = paginatedNotifications.map(renderNotificationCard).join("")
    }
    if (gridView) gridView.style.display = "none"
    if (tableView) tableView.style.display = "none"
  } else if (currentView === "grid") {
    if (gridView) {
      gridView.style.display = "grid"
      gridView.innerHTML = paginatedNotifications.map(renderNotificationCard).join("")
    }
    if (listView) listView.style.display = "none"
    if (tableView) tableView.style.display = "none"
  } else if (currentView === "table") {
    if (tableView) {
      tableView.style.display = "block"
      tableView.innerHTML = renderNotificationTable(paginatedNotifications)
    }
    if (listView) listView.style.display = "none"
    if (gridView) gridView.style.display = "none"
  }

  // Reinitialize icons
  if (typeof window.lucide !== "undefined") {
    window.lucide.createIcons()
  }
}

/**
 * Render a single notification card
 */
function renderNotificationCard(notification) {
  const priorityClasses = {
    low: "badge-low",
    medium: "badge-medium",
    high: "badge-high",
  }

  const statusClasses = {
    Sent: "badge-sent",
    Draft: "badge-draft",
    Scheduled: "badge-scheduled",
  }

  return `
        <div class="notification-item" 
             data-status="${notification.status}"
             data-priority="${notification.priority}"
             data-category="${notification.category}">
            <div class="notification-header">
                <div class="flex items-start justify-between w-full">
                    <div class="flex-1">
                        <p class="text-sm font-medium leading-relaxed text-foreground">${notification.message}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <span class="badge ${priorityClasses[notification.priority]}">
                                ${notification.priority.toUpperCase()}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-muted text-muted-foreground">
                                ${notification.category}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-1">
                        <span class="badge ${statusClasses[notification.status]}">
                            ${notification.status}
                        </span>
                    </div>
                </div>
            </div>
            <div class="notification-meta">
                <div class="flex items-center justify-between w-full text-xs text-muted-foreground">
                    <div class="flex items-center space-x-4">
                        <span>To: ${notification.recipients}</span>
                        <span>${notification.sent_at || "Not sent"}</span>
                    </div>
                    ${
                      notification.delivery_rate
                        ? `
                        <div class="flex items-center space-x-2">
                            <span>Delivery: ${notification.delivery_rate}%</span>
                            <span>Open: ${notification.open_rate || 0}%</span>
                        </div>
                    `
                        : ""
                    }
                </div>
            </div>
            <div class="notification-footer">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center space-x-2">
                        ${
                          notification.status === "Draft"
                            ? `
                            <button onclick="editNotification(${notification.id})" class="text-fixlanka-primary hover:text-fixlanka-primary/80 text-xs font-medium">
                                Edit
                            </button>
                            <button onclick="sendDraft(${notification.id})" class="text-green-600 hover:text-green-600/80 text-xs font-medium">
                                Send Now
                            </button>
                        `
                            : ""
                        }
                        <button onclick="deleteNotification(${notification.id})" class="text-red-600 hover:text-red-600/80 text-xs font-medium">
                            Delete
                        </button>
                    </div>
                    <button onclick='viewNotificationDetails(${JSON.stringify(notification).replace(/'/g, "&apos;")})' class="text-muted-foreground hover:text-foreground text-xs font-medium">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    `
}

/**
 * Render notification table
 */
function renderNotificationTable(notifications) {
  return `
        <table class="w-full">
            <thead class="bg-muted/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Message</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Recipients</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Priority</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Sent At</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-card divide-y divide-border">
                ${notifications
                  .map(
                    (n) => `
                    <tr class="hover:bg-muted/30">
                        <td class="px-4 py-3 text-sm text-foreground">${n.message.substring(0, 50)}...</td>
                        <td class="px-4 py-3 text-sm text-foreground">${n.recipients}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="badge badge-${n.priority}">${n.priority.toUpperCase()}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="badge badge-${n.status.toLowerCase()}">${n.status}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">${n.sent_at || "Not sent"}</td>
                        <td class="px-4 py-3 text-sm">
                            <button onclick='viewNotificationDetails(${JSON.stringify(n).replace(/'/g, "&apos;")})' class="text-fixlanka-primary hover:underline">
                                View
                            </button>
                        </td>
                    </tr>
                `,
                  )
                  .join("")}
            </tbody>
        </table>
    `
}

/**
 * Toggle notification view
 */
function toggleNotificationView(view) {
  currentView = view

  // Update button states
  const listBtn = document.getElementById("listViewBtn")
  const gridBtn = document.getElementById("gridViewBtn")
  const tableBtn = document.getElementById("tableViewBtn")
  ;[listBtn, gridBtn, tableBtn].forEach((btn) => {
    if (btn) {
      btn.classList.remove("btn-primary")
      btn.classList.add("btn-secondary")
    }
  })

  if (view === "list" && listBtn) {
    listBtn.classList.remove("btn-secondary")
    listBtn.classList.add("btn-primary")
  } else if (view === "grid" && gridBtn) {
    gridBtn.classList.remove("btn-secondary")
    gridBtn.classList.add("btn-primary")
  } else if (view === "table" && tableBtn) {
    tableBtn.classList.remove("btn-secondary")
    tableBtn.classList.add("btn-primary")
  }

  renderNotifications()
}

/**
 * Handle notification form submission
 */
async function handleNotificationSubmit(e) {
  e.preventDefault()

  const formData = new FormData(e.target)
  const sendBtn = document.getElementById("sendBtn")
  const draftBtn = document.getElementById("draftBtn")
  const sendBtnText = document.getElementById("sendBtnText")
  const draftBtnText = document.getElementById("draftBtnText")
  const isSending = e.submitter.value === "send"
  
  formData.append("send_type" , e.submitter.value)

  // Show loading state
  if (isSending) {
    sendBtn.disabled = true
    sendBtnText.textContent = "Sending..."
  } else {
    draftBtn.disabled = true
    draftBtnText.textContent = "Saving..."
  }

  try {
    const basePath = window.basePath || ""
    const response = await fetch(`${basePath}/api/notifications`, {
      method: "POST",
      body: formData,
    })

    const result = await response.json()
    console.log("[v0] Form submission result:", result)

    if (!response.ok || !result.success) {
      throw new Error(result.error || "Failed to create notification")
    }

    // Reset form
    e.target.reset()

    // Show success message
    showSuccess(result.message || (isSending ? "Notification sent successfully" : "Draft saved successfully"))

    // Refresh display
    await loadNotifications()
  } catch (error) {
    console.error("[v0] Error submitting form:", error)
    showError(error.message)
  } finally {
    // Reset buttons
    sendBtn.disabled = false
    draftBtn.disabled = false
    sendBtnText.textContent = "Send Now"
    draftBtnText.textContent = "Save Draft"
  }
}

/**
 * Use template
 */
function useTemplate(template) {
  const form = document.getElementById("notificationForm")
  if (!form) return

  const titleInput = form.querySelector('[name="title"]')
  if (titleInput) titleInput.value = template.title
  form.querySelector('[name="message"]').value = template.message
  form.querySelector('[name="priority"]').value = template.priority

  // Scroll to form
  form.scrollIntoView({ behavior: "smooth", block: "start" })

  showSuccess("Template loaded successfully")
}

/**
 * Refresh notifications
 */
async function refreshNotifications() {
  const refreshBtn = document.getElementById("refreshBtn")
  if (refreshBtn) {
    refreshBtn.disabled = true
    const icon = refreshBtn.querySelector("i")
    if (icon) {
      icon.classList.add("animate-spin")
    }
  }

  await loadNotifications()

  if (refreshBtn) {
    refreshBtn.disabled = false
    const icon = refreshBtn.querySelector("i")
    if (icon) {
      icon.classList.remove("animate-spin")
    }
  }
}

/**
 * Filter notifications
 */
function filterNotifications() {
  const statusFilter = document.getElementById("statusFilter")?.value || ""
  const priorityFilter = document.getElementById("priorityFilter")?.value || ""
  const categoryFilter = document.getElementById("categoryFilter")?.value || ""

  filteredNotifications = window.allNotifications.filter((n) => {
    if (statusFilter && n.status !== statusFilter) return false
    if (priorityFilter && n.priority !== priorityFilter) return false
    if (categoryFilter && n.category !== categoryFilter) return false
    return true
  })

  currentPage = 1
  renderNotifications()
  updatePagination()
}

/**
 * Search notifications
 */
function searchNotifications() {
  const searchInput = document.getElementById("searchInput")
  if (!searchInput) return

  const query = searchInput.value.toLowerCase()

  filteredNotifications = window.allNotifications.filter(
    (n) =>
      n.message.toLowerCase().includes(query) ||
      n.recipients.toLowerCase().includes(query) ||
      n.category.toLowerCase().includes(query),
  )

  currentPage = 1
  renderNotifications()
  updatePagination()
}

/**
 * Sort notifications
 */
function sortNotifications() {
  const sortSelect = document.getElementById("sortSelect")
  if (!sortSelect) return

  const sortBy = sortSelect.value

  filteredNotifications.sort((a, b) => {
    switch (sortBy) {
      case "newest":
        return new Date(b.created_at) - new Date(a.created_at)
      case "oldest":
        return new Date(a.created_at) - new Date(b.created_at)
      case "priority":
        const priorityOrder = { high: 3, medium: 2, low: 1 }
        return priorityOrder[b.priority] - priorityOrder[a.priority]
      case "status":
        return a.status.localeCompare(b.status)
      case "delivery":
        return (Number.parseFloat(b.delivery_rate) || 0) - (Number.parseFloat(a.delivery_rate) || 0)
      default:
        return 0
    }
  })

  renderNotifications()
}

/**
 * Quick filters
 */
function quickFilter(type) {
  const today = new Date().toISOString().slice(0, 10)

  switch (type) {
    case "today":
      filteredNotifications = window.allNotifications.filter((n) => n.sent_at && n.sent_at.startsWith(today))
      break
    case "drafts":
      filteredNotifications = window.allNotifications.filter((n) => n.status === "Draft")
      break
    case "high_priority":
      filteredNotifications = window.allNotifications.filter((n) => n.priority === "high")
      break
  }

  currentPage = 1
  renderNotifications()
  updatePagination()
}

/**
 * Clear filters
 */
function clearFilters() {
  // Reset filter inputs
  const statusFilter = document.getElementById("statusFilter")
  const priorityFilter = document.getElementById("priorityFilter")
  const categoryFilter = document.getElementById("categoryFilter")
  const searchInput = document.getElementById("searchInput")
  const sortSelect = document.getElementById("sortSelect")

  if (statusFilter) statusFilter.value = ""
  if (priorityFilter) priorityFilter.value = ""
  if (categoryFilter) categoryFilter.value = ""
  if (searchInput) searchInput.value = ""
  if (sortSelect) sortSelect.value = "newest"

  // Reset filtered notifications
  filteredNotifications = [...window.allNotifications]
  currentPage = 1

  renderNotifications()
  updatePagination()
}

/**
 * Pagination
 */
function changePage(direction) {
  const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage)
  currentPage = Math.max(1, Math.min(currentPage + direction, totalPages))

  renderNotifications()
  updatePagination()
}

function updatePagination() {
  const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage)
  const startIndex = (currentPage - 1) * itemsPerPage + 1
  const endIndex = Math.min(currentPage * itemsPerPage, filteredNotifications.length)

  const paginationInfo = document.getElementById("paginationInfo")
  const currentPageSpan = document.getElementById("currentPage")
  const prevBtn = document.getElementById("prevBtn")
  const nextBtn = document.getElementById("nextBtn")

  if (paginationInfo) {
    paginationInfo.textContent = `Showing ${startIndex}-${endIndex} of ${filteredNotifications.length} notifications`
  }

  if (currentPageSpan) {
    currentPageSpan.textContent = currentPage
  }

  if (prevBtn) {
    prevBtn.disabled = currentPage === 1
  }

  if (nextBtn) {
    nextBtn.disabled = currentPage === totalPages
  }
}

/**
 * Bulk actions
 */
function toggleSelectAll() {
  const selectAll = document.getElementById("selectAll")
  const checkboxes = document.querySelectorAll(".notification-checkbox")

  checkboxes.forEach((cb) => {
    cb.checked = selectAll.checked
  })

  updateBulkActions()
}

function updateBulkActions() {
  const checkboxes = document.querySelectorAll(".notification-checkbox:checked")
  const selectedCount = document.getElementById("selectedCount")
  const bulkActions = document.getElementById("bulkActions")

  if (selectedCount) {
    selectedCount.textContent = `${checkboxes.length} selected`
  }

  if (bulkActions) {
    bulkActions.style.display = checkboxes.length > 0 ? "flex" : "none"
  }
}

function bulkDelete() {
  const checkboxes = document.querySelectorAll(".notification-checkbox:checked")
  const count = checkboxes.length

  if (count === 0) return

  currentDeleteId = "bulk"
  document.getElementById("deleteConfirmMessage").textContent =
    `Are you sure you want to delete ${count} notification(s)? This action cannot be undone.`
  openModal("deleteConfirmModal")
}

async function bulkResend() {
  const checkboxes = document.querySelectorAll(".notification-checkbox:checked")
  const count = checkboxes.length

  if (count === 0) return

  showLoading()

  try {
    const ids = Array.from(checkboxes).map((cb) => cb.value)
    const basePath = window.basePath || ""
    const response = await fetch(`${basePath}/api/notifications/bulk-resend`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ ids }),
    })

    const result = await response.json()

    if (!response.ok || !result.success) {
      throw new Error(result.error || "Failed to resend notifications")
    }

    hideLoading()
    showSuccess(result.message || `${count} notification(s) resent successfully`)

    // Uncheck all
    checkboxes.forEach((cb) => (cb.checked = false))
    const selectAll = document.getElementById("selectAll")
    if (selectAll) selectAll.checked = false
    updateBulkActions()

    // Reload notifications
    await loadNotifications()
  } catch (error) {
    console.error("[v0] Error bulk resending:", error)
    hideLoading()
    showError(error.message)
  }
}

/**
 * Export notifications
 */
function exportNotifications() {
  showLoading()

  setTimeout(() => {
    // Create CSV content
    const headers = [
      "ID",
      "Message",
      "Recipients",
      "Priority",
      "Status",
      "Category",
      "Sent At",
      "Delivery Rate",
      "Open Rate",
    ]
    const rows = filteredNotifications.map((n) => [
      n.id,
      `"${n.message.replace(/"/g, '""')}"`,
      n.recipients,
      n.priority,
      n.status,
      n.category,
      n.sent_at || "Not sent",
      n.delivery_rate || "N/A",
      n.open_rate || "N/A",
    ])

    const csv = [headers, ...rows].map((row) => row.join(",")).join("\n")

    // Download
    const blob = new Blob([csv], { type: "text/csv" })
    const url = URL.createObjectURL(blob)
    const a = document.createElement("a")
    a.href = url
    a.download = `notifications-${new Date().toISOString().slice(0, 10)}.csv`
    a.click()
    URL.revokeObjectURL(url)

    hideLoading()
    showSuccess("Notifications exported successfully")
  }, 1000)
}

/**
 * Utility functions
 */
function showLoading() {
  const overlay = document.getElementById("loadingOverlay")
  if (overlay) {
    overlay.style.display = "flex"
  }
}

function hideLoading() {
  const overlay = document.getElementById("loadingOverlay")
  if (overlay) {
    overlay.style.display = "none"
  }
}

function showError(message) {
  const alert = document.getElementById("errorAlert")
  const messageEl = document.getElementById("errorMessage")

  if (alert && messageEl) {
    messageEl.textContent = message
    alert.classList.remove("hidden")

    // Auto hide after 5 seconds
    setTimeout(() => {
      closeError()
    }, 5000)
  }

  // Reinitialize icons
  if (typeof window.lucide !== "undefined") {
    window.lucide.createIcons()
  }
}

function closeError() {
  const alert = document.getElementById("errorAlert")
  if (alert) {
    alert.classList.add("hidden")
  }
}

function showSuccess(message) {
  const alert = document.getElementById("successAlert")
  const messageEl = document.getElementById("successMessage")

  if (alert && messageEl) {
    messageEl.textContent = message
    alert.classList.remove("hidden")

    // Auto hide after 3 seconds
    setTimeout(() => {
      closeSuccess()
    }, 3000)
  }

  // Reinitialize icons
  if (typeof window.lucide !== "undefined") {
    window.lucide.createIcons()
  }
}

function closeSuccess() {
  const alert = document.getElementById("successAlert")
  if (alert) {
    alert.classList.add("hidden")
  }
}
