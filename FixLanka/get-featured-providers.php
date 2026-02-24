<?php
// Direct endpoint for testing featured providers
require_once __DIR__ . '/controllers/ProviderController.php';

$controller = new ProviderController();
$controller->getFeatured();
?>
