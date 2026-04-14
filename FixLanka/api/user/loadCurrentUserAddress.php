<?php
require_once __DIR__ . '/../../controllers/user/loadCurrentUserAddressController.php';

$controller = new LoadCurrentUserAddressController();
$controller->handle();
