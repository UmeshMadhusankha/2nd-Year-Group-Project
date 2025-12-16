<?php
function renderCard($title, $value, $changeText, $icon, $color = "blue")
{
?>
    <div class="stat-card" data-color="<?php echo $color; ?>">
        <div class="stat-card-inner">
            <div class="stat-info">
                <h4><?php echo $title; ?></h4>
                <div class="stat-value"><?php echo $value; ?></div>
                <div class="stat-change"><?php echo $changeText; ?></div>
            </div>
            <div class="stat-icon">
                <i data-lucide="<?php echo $icon; ?>"></i>
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
