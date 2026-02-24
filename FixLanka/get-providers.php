<?php
// Direct endpoint for testing provider filters
require_once __DIR__ . '/controllers/ProviderController.php';

$controller = new ProviderController();
$controller->getProviders();
?>
