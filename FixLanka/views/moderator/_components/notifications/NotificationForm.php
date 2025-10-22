<?php
function renderNotificationForm($templates = []) {
    ?>
    <div class="send-notification-card">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground flex items-center gap-2">
                <i data-lucide="send" class="h-5 w-5"></i>
                Send Notification
            </h3>
            <p class="text-sm text-muted-foreground">Broadcast messages to users</p>
            
            <form method="POST" action="/moderator/notifications/send" class="notification-form">
                <input type="hidden" name="action" value="send_notification">
                
                <div>
                    <label class="block text-sm font-medium text-foreground">Recipients</label>
                    <select name="recipients" class="form-select mt-1">
                        <option value="all">All Users</option>
                        <option value="providers">Service Providers</option>
                        <option value="customers">Customers</option>
                        <option value="companies">Companies</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground">Priority</label>
                    <select name="priority" class="form-select mt-1">
                        <option value="low">Low Priority</option>
                        <option value="medium" selected>Medium Priority</option>
                        <option value="high">High Priority</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground">Title</label>
                    <input type="text" name="title" placeholder="Notification title..." required class="form-input mt-1">
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground">Message</label>
                    <textarea name="message" rows="4" placeholder="Enter your notification message..." required class="form-textarea mt-1"></textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="schedule" id="schedule" class="h-4 w-4 text-fixlanka-primary focus:ring-fixlanka-primary border-border rounded">
                    <label for="schedule" class="ml-2 block text-sm text-foreground">Schedule for later</label>
                </div>

                <div class="form-actions">
                    <button type="submit" name="send_type" value="send" class="btn btn-primary flex-1">
                        <i data-lucide="send" class="mr-2 h-4 w-4"></i>
                        Send Now
                    </button>
                    <button type="submit" name="send_type" value="draft" class="btn btn-secondary flex-1">
                        <i data-lucide="save" class="mr-2 h-4 w-4"></i>
                        Save Draft
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>
