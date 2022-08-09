<?php
require_once('./M-PESA.php');
require_once('./connect.php');

if (isset($_POST['STK'])) {
    $object = json_decode($_POST['STK']);
    $amount = $object->Amount;
    $phone = $object->PhoneNumber;

    $mpesa = new Mpesa();
    $result = $mpesa->mpesaExpress($amount, $phone);

    $response = json_decode($result);

    $sql = 'INSERT INTO mpesa_transactions (user_id, merchant_request_id, checkout_request_id, result_code, result_desc, amount, phone, status) VALUES (:user_id, :merchant_request_id, :checkout_request_id, :result_code, :result_desc, :amount, :phone, :status)';
    $stmt = $pdo->prepare($sql);

    $re = $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'merchant_request_id' => $response->MerchantRequestID,
        'checkout_request_id' => $response->CheckoutRequestID,
        'result_code' => $response->ResultCode,
        'result_desc' => $response->ResultDesc,
        'amount' => $amount,
        'phone' => $phone,
        'status' => 'PENDING'
    ]);

    sleep(5);

    if ($result) {
        if (checkPaymentStatus($response->CheckoutRequestID)) {
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Transaction successful'
            ));
        }else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Transaction not successful'
            ));
        }
    }
}
    function checkPaymentStatus($checkoutRequestID) {
        $mpesa = new Mpesa();
        $result = $mpesa->checkTransactionStatus($checkoutRequestID);
        $response = json_decode($result);
        
        $sql = 'UPDATE mpesa_transactions SET status = :status, result_code = :result_code, result_desc = :result_desc WHERE checkout_request_id = :checkout_request_id';
        
        require_once('./connect.php');
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'status' => $response->Body->stkCallback->ResultCode == 0 ? 'SUCCESSFUL' : 'FAILED',
            'result_code' => $response->Body->stkCallback->ResultCode,
            'result_desc' => $response->Body->stkCallback->ResultDesc,
            'checkout_request_id' => $response->Body->stkCallback->CheckoutRequestID
        ]);

        if ($response->Body->stkCallback->ResultCode == 0) {
            return true;
        }else {
            return false;
        }

    }
?>
