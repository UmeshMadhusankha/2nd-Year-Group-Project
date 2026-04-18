<?php

require_once __DIR__ . '/../controllers/JobCollaborationController.php';

$controller = new JobCollaborationController();
$action = $_GET['action'] ?? 'get';

switch ($action) {
    case 'get':
        $controller->get();
        break;

    case 'post_note':
        $controller->postNote();
        break;

    case 'propose_price':
        $controller->proposePrice();
        break;

    case 'respond_price':
        $controller->respondPrice();
        break;

    case 'mark_completed':
        $controller->markCompleted();
        break;

    case 'confirm_payment':
        $controller->confirmPayment();
        break;

    case 'reset_completed':
        $controller->resetCompleted();
        break;

    case 'reset_payment':
        $controller->resetPayment();
        break;

    case 'submit_rating':
        $controller->submitRating();
        break;

    default:
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
