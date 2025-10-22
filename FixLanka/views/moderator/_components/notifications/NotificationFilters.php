<?php
function renderNotificationFilters($showAdvanced = true) {
    ?>
    <div class="bg-card rounded-lg border">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4">Filter & Sort Notifications</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Sort By</label>
                    <select id="sortSelect" class="form-select" onchange="sortNotifications()">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="priority">Priority</option>
                        <option value="status">Status</option>
                        <option value="delivery">Delivery Rate</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                    <select id="statusFilter" class="form-select" onchange="filterNotifications()">
                        <option value="">All Status</option>
                        <option value="Sent">Sent</option>
                        <option value="Draft">Draft</option>
                        <option value="Scheduled">Scheduled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Priority</label>
                    <select id="priorityFilter" class="form-select" onchange="filterNotifications()">
                        <option value="">All Priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Category</label>
                    <select id="categoryFilter" class="form-select" onchange="filterNotifications()">
                        <option value="">All Categories</option>
                        <option value="System">System</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Alert">Alert</option>
                        <option value="Update">Update</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2" for="searchInput">Search</label>
                    <div class="relative">
                        <i data-lucide="search" class="absolute searchInputIcon text-muted-foreground"></i>
                        <input type="text" id="searchInput" placeholder="Search notifications..." class="form-input pl-10" oninput="searchNotifications()">
                    </div>
                </div>
            </div>
            
            <?php if ($showAdvanced): ?>
            <div class="flex flex-wrap gap-2 mt-4">
                <button onclick="quickFilter('today')" class="btn btn-sm btn-secondary">
                    <i data-lucide="calendar" class="mr-1 h-3 w-3"></i>
                    Today's Notifications
                </button>
                <button onclick="quickFilter('drafts')" class="btn btn-sm btn-secondary">
                    <i data-lucide="edit" class="mr-1 h-3 w-3"></i>
                    Drafts Only
                </button>
                <button onclick="quickFilter('high_priority')" class="btn btn-sm btn-secondary">
                    <i data-lucide="alert-triangle" class="mr-1 h-3 w-3"></i>
                    High Priority
                </button>
                <button onclick="clearFilters()" class="btn btn-sm btn-outline">
                    <i data-lucide="x" class="mr-1 h-3 w-3"></i>
                    Clear Filters
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>
