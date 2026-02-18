<?php
function renderCard($title, $value, $changeText, $icon, $color = "blue")
{
    // Icon mapping: Lucide → Font Awesome
    $iconMap = [
        'monitor' => 'fa-desktop',
        'clock' => 'fa-clock',
        'check-circle' => 'fa-circle-check',
        'x-circle' => 'fa-circle-xmark',
        'calendar' => 'fa-calendar',
        'play-circle' => 'fa-circle-play',
        'layers' => 'fa-layer-group',
        'dollar-sign' => 'fa-dollar-sign',
        'users' => 'fa-users',
        'megaphone' => 'fa-bullhorn',
        'file-text' => 'fa-file-lines',
        'alert-circle' => 'fa-circle-exclamation',
        'shield' => 'fa-shield',
        'percent' => 'fa-percent',
        'credit-card' => 'fa-credit-card',
        'trending-up' => 'fa-arrow-trend-up',
        'activity' => 'fa-chart-line'
    ];
    
    $faIcon = $iconMap[$icon] ?? 'fa-circle';
?>
    <div class="stat-card" data-color="<?php echo $color; ?>">
        <div class="stat-card-inner">
            <div class="stat-info">
                <h4><?php echo $title; ?></h4>
                <div class="stat-value"><?php echo $value; ?></div>
                <div class="stat-change"><?php echo $changeText; ?></div>
            </div>
            <div class="stat-icon">
                <i class="fa-solid <?php echo $faIcon; ?>"></i>
            </div>
        </div>
    </div>
<?php
}


function renderBadge($text, $variant = 'default')
{
    $classes = [
        'default' => 'badge badge-default',
        'secondary' => 'badge badge-secondary',
        'destructive' => 'badge badge-destructive',
        'outline' => 'badge badge-outline'
    ];

    $class = $classes[$variant] ?? $classes['default'];
    echo '<span class="' . $class . '">' . htmlspecialchars($text) . '</span>';
}

?>