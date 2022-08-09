<?php
require_once('./M-PESA.php');
require_once('./connect.php');

if (isset($_POST['STK'])) {
    $object = json_decode($_POST['STK']);
    $amount = $object->Amount;
    $phone = $object->PhoneNumber;

    $mpesa = new Mpesa();
    $result = $mpesa->mpesaExpress($amount, $phone);

    $result = json_decode($result);

    $sql = 'INSERT INTO mpesa_transactions (user_id, merchant_request_id, checkout_request_id, result_code, result_desc, amount, phone, status) VALUES (:user_id, :merchant_request_id, :checkout_request_id, :result_code, :result_desc, :amount, :phone, :status)';
    $stmt = $pdo->prepare($sql);

    $result = $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'merchant_request_id' => $result->MerchantRequestID,
        'checkout_request_id' => $result->CheckoutRequestID,
        'result_code' => $result->ResultCode,
        'result_desc' => $result->ResultDesc,
        'amount' => $amount,
        'phone' => $phone,
        'status' => 'PENDING'
    ]);
    if ($result) {
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Transaction successfully recorded'
        ));
    }else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Transaction not recorded'
        )); 
    }

        
}
