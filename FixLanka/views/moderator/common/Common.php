<?php

function renderCard($label, $value, $description, $icon, $iconColor)
{
?>
    <div class="bg-card rounded-lg border p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground"><?php echo $label; ?></p>
                <p class="text-2xl font-bold text-foreground mt-2"><?php echo $value; ?></p>
                <p class="text-xs text-muted-foreground mt-1"><?php echo $description; ?></p>
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

?>