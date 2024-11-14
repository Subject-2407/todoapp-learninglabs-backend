<?php
function sendJsonResponse($code, $message, $data = []) {
    http_response_code($code);
    $response = array(
        'code' => $code,
        'message' => $message,
    );

    if (count($data) > 0) {
        $response['data'] = $data;
    }

    header('Content-Type: application/json');
    
    echo json_encode($response);
    
    exit;
}