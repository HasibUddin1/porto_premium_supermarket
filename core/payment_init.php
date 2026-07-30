<?php
include_once "../config/config.php";

// Include the Stripe PHP library 
require_once '../assets/plugins/stripe-php/init.php';

// Set API key 
\Stripe\Stripe::setApiKey(STRIPE_API_KEY);

// Retrieve JSON from POST body 
$jsonStr = file_get_contents('php://input');
$jsonObj = json_decode($jsonStr);


if ($jsonObj->request_type == 'create_payment_intent') {


    $itemPrice = $jsonObj->product_price;
    $itemName = $jsonObj->product_name;
    $currency = 'USD';

    // Define item price and convert to cents 
    $itemPriceCents = round($itemPrice * 100);

    // Set content type to JSON 
    header('Content-Type: application/json');

    try {
        // Create PaymentIntent with amount and currency 
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $itemPriceCents,
            'currency' => $currency,
            'description' => $itemName,
            'payment_method_types' => [
                'card'
            ]
        ]);

        $output = [
            'id' => $paymentIntent->id,
            'clientSecret' => $paymentIntent->client_secret
        ];

        echo json_encode($output);
    } catch (Error $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} elseif ($jsonObj->request_type == 'create_customer') {
    $payment_intent_id = !empty($jsonObj->payment_intent_id) ? $jsonObj->payment_intent_id : '';
    $name = !empty($jsonObj->name) ? $jsonObj->name : '';
    $email = !empty($jsonObj->email) ? $jsonObj->email : '';


    // Add customer to stripe 
    try {
        $customer = \Stripe\Customer::create(array(
            'name' => $name,
            'email' => $email
        ));
    } catch (Exception $e) {
        $api_error = $e->getMessage();
    }

    if (empty($api_error) && $customer) {
        try {
            // Update PaymentIntent with the customer ID 
            $paymentIntent = \Stripe\PaymentIntent::update($payment_intent_id, [
                'customer' => $customer->id
            ]);
        } catch (Exception $e) {
            // log or do what you want 
        }

        $output = [
            'id' => $payment_intent_id,
            'customer_id' => $customer->id
        ];

        $_SESSION["customer_id"] = $customer->id;


        echo json_encode($output);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $api_error]);
    }
} elseif ($jsonObj->request_type == 'payment_insert') {
    $payment_intent = !empty($jsonObj->payment_intent) ? $jsonObj->payment_intent : '';
    $customer_id = $_SESSION["customer_id"];

    // Retrieve customer info 
    try {
        $customer = \Stripe\Customer::retrieve($customer_id);
    } catch (Exception $e) {
        $api_error = $e->getMessage();
    }


    // Check whether the charge was successful 
    if (!empty($payment_intent) && $payment_intent->status == 'succeeded') {
        // Transaction details  
        $transaction_id = $payment_intent->id;
        $paid_amount = $payment_intent->amount;
        $paid_amount = ($paid_amount / 100);
        $paid_currency = $payment_intent->currency;
        $payment_status = $payment_intent->status;


        $customer_name = $customer_email = '';
        if (!empty($customer)) {
            $customer_name = !empty($customer->name) ? $customer->name : '';
            $customer_email = !empty($customer->email) ? $customer->email : '';
        }


        $itemPrice = $jsonObj->product_price;
        $itemName = $jsonObj->product_name;
        $currency = 'USD';

        // Check if any transaction data is exists already with the same TXN ID 

        // $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        // $result = mysqli_query($conn, $sql);

        // if(mysqli_num_rows($result) > 0){
        //     $row = mysqli_fetch_assoc($result);


        // $sql = "SELECT id FROM transactions WHERE txn_id = '$transaction_id'";
        // $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));




        // $payment_id = 0;
        // if (mysqli_num_rows($result) > 0) {
        //     $row = mysqli_fetch_assoc($result);
        //     $payment_id = $row['id'];
        // } else {
        //     $sql = "INSERT INTO transactions (customer_name,customer_email,item_name,item_price,item_price_currency,paid_amount,paid_amount_currency,txn_id,payment_status,created,modified) 
        //     VALUES ('$customer_name','$customer_email','$itemName','$itemPrice','$currency','$paid_amount','$paid_currency','$transaction_id','$payment_status',NOW(),NOW())";

        //     $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));


        //     if ($result) {

        //         $payment_id = mysqli_insert_id($conn);
        //     }
        // }

        $output = [
            'payment_txn_id' => base64_encode($transaction_id)
        ];
        echo json_encode($output);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Transaction has been failed!']);
    }
}
