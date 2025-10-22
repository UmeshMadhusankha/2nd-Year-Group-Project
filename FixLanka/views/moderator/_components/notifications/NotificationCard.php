<?php
function renderNotificationCard($notification) {
    $priorityVariants = [
        'low' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
        'medium' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
        'high' => 'bg-red-500/10 text-red-500 border-red-500/20'
    ];
    
    $statusVariants = [
        'Sent' => 'bg-green-500/10 text-green-500 border-green-500/20',
        'Draft' => 'bg-gray-500/10 text-gray-500 border-gray-500/20',
        'Scheduled' => 'bg-blue-500/10 text-blue-500 border-blue-500/20'
    ];
    
    $priorityClass = $priorityVariants[$notification['priority']] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
    $statusClass = $statusVariants[$notification['status']] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
    ?>
    
    <div class="notification-item" 
         data-status="<?php echo $notification['status']; ?>"
         data-priority="<?php echo $notification['priority']; ?>"
         data-category="<?php echo $notification['category']; ?>"
         data-date="<?php echo $notification['sentAt'] ?? $notification['createdAt']; ?>">
        <div class="notification-header">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium leading-relaxed text-foreground"><?php echo htmlspecialchars($notification['message']); ?></p>
                    <div class="flex items-center space-x-2 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border <?php echo $priorityClass; ?>">
                            <?php echo ucfirst($notification['priority']); ?>
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-muted text-muted-foreground">
                            <?php echo $notification['category']; ?>
                        </span>
                    </div>
                </div>
                <div class="flex flex-col items-end space-y-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border <?php echo $statusClass; ?>">
                        <?php echo $notification['status']; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="notification-meta">
            <div class="flex items-center justify-between w-full text-xs text-muted-foreground">
                <div class="flex items-center space-x-4">
                    <span>To: <?php echo $notification['recipients']; ?></span>
                    <span><?php echo $notification['sentAt'] ?? 'Not sent'; ?></span>
                </div>
                <?php if ($notification['deliveryRate']): ?>
                <div class="flex items-center space-x-2">
                    <span>Delivery: <?php echo $notification['deliveryRate']; ?></span>
                    <span>Open: <?php echo $notification['openRate']; ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="notification-footer">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center space-x-2">
                    <?php if ($notification['status'] === 'Draft'): ?>
                    <button onclick="editNotification(<?php echo $notification['id']; ?>)" class="text-fixlanka-primary hover:text-fixlanka-primary/80 text-xs">
                        Edit
                    </button>
                    <button onclick="sendDraft(<?php echo $notification['id']; ?>)" class="text-green-600 hover:text-green-600/80 text-xs">
                        Send Now
                    </button>
                    <?php endif; ?>
                </div>
                <button onclick="viewNotificationDetails(<?php echo htmlspecialchars(json_encode($notification)); ?>)" class="text-muted-foreground hover:text-foreground text-xs">
                    View Details
                </button>
            </div>
        </div>
    </div>
    
    <?php
}
?>
