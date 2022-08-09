<?php
// SQL statement for creating new tables
$statements = [
    'CREATE TABLE IF NOT EXISTS mpesa_transactions(
                id INT(11) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                amount VARCHAR(20) NOT NULL,
                merchant_request_id  VARCHAR(20) NOT NULL,
                checkout_request_id  VARCHAR(20) NOT NULL,
                response_code  VARCHAR(20) NOT NULL,
                response_description  VARCHAR(20) NOT NULL,
                result_desc  VARCHAR(20) NULL,
                result_code  VARCHAR(20) NULL,
                transaction_reference VARCHAR(20) NULL,
                status  VARCHAR(20) NOT NULL DEFAULT "PENDING",
                date_paid  DATETIME NULL,
                PRIMARY KEY (id)
            )',
            'CREATE TABLE IF NOT EXISTS mpesa_c2b_results(
                id INT(11) NOT NULL AUTO_INCREMENT,
                transaction_type VARCHAR(20) NOT NULL,
                transaction_id VARCHAR(20) NOT NULL,
                timestamp VARCHAR(20) NOT NULL, 
                amount VARCHAR(20) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                short_code VARCHAR(20) NOT NULL,
                account_reference VARCHAR(20) NOT NULL,
                organization_balance VARCHAR(20) NULL,
                third_party_transaction_id VARCHAR(20) NULL,
                first_name VARCHAR(20) NULL,
                middle_name VARCHAR(20) NULL,
                last_name VARCHAR(20) NULL,
                PRIMARY KEY (id)
            )'
];

// connect to the database
$pdo = require 'connect.php';

// execute SQL statements
foreach ($statements as $sql) {
    $pdo->exec($sql);
}

?>

