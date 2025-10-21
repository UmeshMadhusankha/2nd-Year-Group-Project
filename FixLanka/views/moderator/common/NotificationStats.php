<?php
function renderNotificationStats($notifications) {
    $sentCount = count(array_filter($notifications, fn($n) => $n['status'] === 'Sent'));
    $draftCount = count(array_filter($notifications, fn($n) => $n['status'] === 'Draft'));
    $todayCount = count(array_filter($notifications, fn($n) => 
        $n['sentAt'] && date('Y-m-d', strtotime($n['sentAt'])) === date('Y-m-d')
    ));
    ?>
    
    <div class="grid gap-4 grid-cols-4">
        <?php
        renderCard('Total Notifications', count($notifications), 'All notifications', 'bell', 'text-blue-600');
        renderCard('Sent Today', $todayCount, 'Successfully delivered', 'check-circle', 'text-green-600');
        renderCard('Drafts', $draftCount, 'Unsent messages', 'edit', 'text-yellow-600');
        renderCard('Scheduled', '0', 'Future notifications', 'clock', 'text-purple-600');
        ?>
    </div>
    
    <?php
}
?>
