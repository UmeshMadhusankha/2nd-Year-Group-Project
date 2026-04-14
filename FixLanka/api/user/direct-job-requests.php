<?php
require_once __DIR__ . '/../../controllers/user/directRequestFlowController.php';

$controller = new DirectRequestFlowController();
$controller->handleDirectJobRequests();
