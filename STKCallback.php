<?php
header('Content-Type: application/json');

require('./connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = file_get_contents('php://input');

    $response = json_decode($result);
    $responseCode = $response->Body->stkCallback->ResultCode;

    if ($responseCode == 0) {
        $sql = 'UPDATE mpesa_transactions SET status = :status, result_code = :result_code, result_desc = :result_desc WHERE checkout_request_id = :checkout_request_id';
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'status' => 'SUCCESSFUL',
            'result_code' => $responseCode,
            'result_desc' => $response->Body->stkCallback->ResultDesc,
            'checkout_request_id' => $response->Body->stkCallback->CheckoutRequestID
        ]);
    }else {
        $sql = 'UPDATE mpesa_transactions SET status = :status, result_code = :result_code, result_desc = :result_desc WHERE checkout_request_id = :checkout_request_id';
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'status' => 'FAILED',
            'result_code' => $responseCode,
            'result_desc' => $response->Body->stkCallback->ResultDesc,
            'checkout_request_id' => $response->Body->stkCallback->CheckoutRequestID
        ]);
    }

    if ($result) {
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Transaction successfully recorded'
        ));
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Transaction not recorded'
        ));
    }
} else {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Method not allowed'
    ));
}
?>
