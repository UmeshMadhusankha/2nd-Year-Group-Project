<?php
// pages/moderator/notifications/_components/Modals.php

function renderNotificationDetailsModal() {
    ?>
    <div id="notificationDetailsModal" class="modal-overlay">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Notification Details</h3>
                    <button type="button" class="modal-close" onclick="closeModal('notificationDetailsModal')" aria-label="Close">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="notificationDetailsContent" class="space-y-4">
                         Content will be populated by JavaScript 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('notificationDetailsModal')" class="btn btn-secondary">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function renderEditNotificationModal() {
    ?>
    <div id="editNotificationModal" class="modal-overlay">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edit Notification</h3>
                    <button type="button" class="modal-close" onclick="closeModal('editNotificationModal')" aria-label="Close">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                <form id="editNotificationForm">
                    <div class="modal-body">
                        <input type="hidden" id="editNotificationId" name="id">
                        
                        <div class="form-group">
                            <label class="form-label" for="editTitle">Title</label>
                            <input type="text" id="editTitle" name="title" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="editMessage">Message</label>
                            <textarea id="editMessage" name="message" rows="4" class="form-textarea" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="editPriority">Priority</label>
                                <select id="editPriority" name="priority" class="form-select">
                                    <option value="low">Low Priority</option>
                                    <option value="medium">Medium Priority</option>
                                    <option value="high">High Priority</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="editStatus">Status</label>
                                <select id="editStatus" name="status" class="form-select">
                                    <option value="Draft">Draft</option>
                                    <option value="Sent">Sent</option>
                                    <option value="Scheduled">Scheduled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeModal('editNotificationModal')" class="btn btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="saveEditBtn">
                            <i data-lucide="save" class="mr-2 h-4 w-4"></i>
                            <span id="saveEditBtnText">Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}

function renderVideoNotificationModal() {
    ?>
    <div id="videoNotificationModal" class="modal-overlay">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        <i data-lucide="video" class="h-5 w-5 inline mr-2"></i>
                        Send Video Notification
                    </h3>
                    <button type="button" class="modal-close" onclick="closeModal('videoNotificationModal')" aria-label="Close">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                <form id="videoNotificationForm">
                    <div class="modal-body">
                        <p class="text-sm text-muted-foreground mb-6">Upload a video or provide a video URL to send as a notification</p>
                        
                        <div class="form-group">
                            <label class="form-label">Recipients</label>
                            <select name="recipients" class="form-select" required>
                                <option value="all">All Users</option>
                                <option value="providers">Service Providers</option>
                                <option value="customers">Customers</option>
                                <option value="companies">Companies</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" placeholder="Video notification title..." class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="message" rows="3" placeholder="Brief description of the video..." class="form-textarea" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label mb-2">Video Source</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="videoSource" value="upload" checked class="radio-input" onchange="toggleVideoSource('upload')">
                                    <span>Upload Video</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="videoSource" value="url" class="radio-input" onchange="toggleVideoSource('url')">
                                    <span>Video URL</span>
                                </label>
                            </div>

                            <div id="videoUploadSection" class="mt-3">
                                <div class="upload-area">
                                    <input type="file" id="videoFile" accept="video/*" class="hidden" onchange="handleVideoUpload(event)">
                                    <label for="videoFile" class="upload-label">
                                        <i data-lucide="upload" class="h-12 w-12 mx-auto text-muted-foreground mb-3"></i>
                                        <p class="text-sm font-medium text-foreground">Click to upload video</p>
                                        <p class="text-xs text-muted-foreground mt-1">MP4, WebM, or OGG (max 50MB)</p>
                                    </label>
                                    <div id="videoPreview" class="video-preview hidden">
                                        <video id="previewVideo" controls class="preview-video"></video>
                                        <p id="videoFileName" class="text-sm text-foreground mt-2"></p>
                                    </div>
                                </div>
                            </div>

                            <div id="videoUrlSection" class="hidden mt-3">
                                <input type="url" id="videoUrl" name="videoUrl" placeholder="https://example.com/video.mp4" class="form-input">
                                <p class="text-xs text-muted-foreground mt-1">Supports YouTube, Vimeo, or direct video URLs</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="low">Low Priority</option>
                                    <option value="medium" selected>Medium Priority</option>
                                    <option value="high">High Priority</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="System">System</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Update" selected>Update</option>
                                    <option value="Announcement">Announcement</option>
                                </select>
                            </div>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" name="schedule" id="videoSchedule" class="checkbox-input">
                            <label for="videoSchedule" class="checkbox-label">Schedule for later</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeModal('videoNotificationModal')" class="btn btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="sendVideoBtn">
                            <i data-lucide="send" class="mr-2 h-4 w-4"></i>
                            <span id="sendVideoBtnText">Send Video Notification</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}

function renderDeleteConfirmModal() {
    ?>
    <div id="deleteConfirmModal" class="modal-overlay">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title text-red-600">
                        <i data-lucide="alert-triangle" class="h-5 w-5 inline mr-2"></i>
                        Confirm Delete
                    </h3>
                    <button type="button" class="modal-close" onclick="closeModal('deleteConfirmModal')" aria-label="Close">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-foreground" id="deleteConfirmMessage">Are you sure you want to delete this notification? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('deleteConfirmModal')" class="btn btn-secondary">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmDelete()" class="btn btn-destructive" id="confirmDeleteBtn">
                        <i data-lucide="trash-2" class="mr-2 h-4 w-4"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
