<?php
header('Content-Type: application/json');
require('./connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$response = json_decode(file_get_contents('php://input'));

    $sql = "INSERT INTO mpesa_c2b_results (transaction_type, transaction_id, timestamp, amount, phone, short_code, account_reference, organization_balance, third_party_transaction_id, first_name, middle_name, last_name) VALUES (:transaction_type, :transaction_id, :timestamp, :amount, :phone, :short_code, :account_reference, :organization_balance)";
    $stmt = $pdo->prepare($sql);

    $result = $stmt->execute([
        'transaction_type' => $response->TransactionType,
        'transaction_id' => $response->TransID,
        'timestamp' => $response->TransTime,
        'amount' => $response->TransAmount,
        'phone' => $response->MSISDN,
        'short_code' => $response->BillRefNumber,
        'account_reference' => $response->AccountReference,
        'organization_balance' => $response->OrgAccountBalance,
        'third_party_transaction_id' => $response->ThirdPartyTransID,
        'first_name' => $response->FirstName,
        'middle_name' => $response->MiddleName,
        'last_name' => $response->LastName
    ]);
//Here you can consolidate payments to the same account and update fields in the database accordingly
//such as the amount paid, date paid, etc.

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


}else {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Method not allowed'
    ));
}
?>