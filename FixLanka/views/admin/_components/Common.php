<?php

function renderCard($label, $value, $description, $icon, $iconColor)
{
?>
    <div class="card dashboard-stat-card">
        <div class="dashboard-stat-header">
            <div>
                <p class="text-sm font-medium text-muted-foreground"><?php echo $label; ?></p>
                <p class="dashboard-stat-value"><?php echo $value; ?></p>
                <p class="text-xs text-muted-foreground"><?php echo $description; ?></p>
            </div>
            <i data-lucide="<?php echo $icon; ?>" class="h-8 w-8 <?php echo $iconColor; ?>"></i>
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


function renderStatCard($title, $value, $description = '', $icon = 'trending-up', $color = 'blue')
{
    $colorClasses = [
        'blue' => 'text-fixlanka-primary',
        'green' => 'text-fixlanka-highlight',
        'yellow' => 'text-fixlanka-primary',
        'red' => 'text-fixlanka-error',
        'purple' => 'text-fixlanka-primary',
        'orange' => 'text-fixlanka-error',
        'emerald' => 'text-fixlanka-highlight'
    ];

    $iconColor = $colorClasses[$color] ?? 'text-fixlanka-primary';

    return '<div class="card p-6">
                <div class="flex items-center justify-between w-full mb-4">
                    <h3 class="text-sm font-medium text-muted-foreground">' . $title . '</h3>
                    <i data-lucide="' . $icon . '" class="h-4 w-4 ' . $iconColor . '"></i>
                </div>
                <div class="text-2xl font-bold text-foreground">' . $value . '</div>' .
        ($description ? '<p class="text-xs text-muted-foreground mt-1">' . $description . '</p>' : '') .
        '</div>';
}

?>
