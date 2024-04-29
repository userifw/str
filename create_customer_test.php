<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$stripeSecretKey = 'sk_test_51NsPv0AXV3XqsQb47u6Fps8IWSuTJSzVSDbyptKDWt9nqVaC8GLACdEuWCQYNZJJStNU4zS4gFcxNEh8N526r9B300NlrprFdE';
header("Content-type: application/json");
\Stripe\Stripe::setApiKey($stripeSecretKey);


// $stripeToken = $_POST['stripeToken'];

// $customer = \Stripe\Customer::create([
//     'source' => $stripeToken
// ]);

// \Stripe\Subscription::create([
//   'customer' => $customer->id,
//   'items' => [
//       [
//         'price' => 'price_1NzbMhAXV3XqsQb4vtOHPYJG',
//       ],
//     ]
// ]);
// 
//$p = json_decode($_POST);
//$p = json_encode($_POST);

$email = 'test-'.date('d.m.Y-H:i:s').'d@email.email';

$incoming = file_get_contents('php://input');

if (isset($incoming) && $incoming) {
  $payment_info = json_decode($incoming, 1);
}


//print_r($payment_info);
$phone = preg_replace("/[^,.0-9]/", '', $payment_info['phone']);

if(empty($payment_info['email'])){
  $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $payment_info['email'] = 'search-'.substr(str_shuffle($permitted_chars), 0, 4).$phone.'@gmail.com';
}

$email = $payment_info['email'];
$price = 'price_1O1lluAXV3XqsQb4j7X9IcM5';//$payment_info['price']; Андрея цена price_1O0efkAXV3XqsQb4ogJjo5u8, была price_1O1pU0AXV3XqsQb4KG2AFRnw
//$price = 'price_1O1lluAXV3XqsQb4j7X9IcM5'; // test price
$site = $payment_info['site'];



$paymentMethodId = $payment_info['paymentMethodId']['id'];
//echo $email;echo $paymentMethodId;
$customer = \Stripe\Customer::create([
  'email' => $email,
  'name' => $email,
  'metadata' => [
        'customerEmail' => $email,
        'site' => $site,
        'phone' => $phone,
        'card' => $payment_info['paymentMethodId']['card']['exp_year'].'-'.$payment_info['paymentMethodId']['card']['last4'],
  ],
  'payment_method' => $paymentMethodId
]);



$res = \Stripe\Subscription::create([
  'customer' => $customer->id,
  'items' => [
      [
        'price' => $price,//price_1O0IwIAXV3XqsQb4r85mTFYO test mode //test mode 1$ price_1O00XzAXV3XqsQb4LaGryKQr //NOT test price_1O0efkAXV3XqsQb4ogJjo5u8
      ],
    ],
    'metadata' => [
        'customerEmail' => $email,
        'site' => $site,
        'phone' => $phone,
        'card' => $payment_info['paymentMethodId']['card']['exp_year'].'-'.$payment_info['paymentMethodId']['card']['last4'],
    ],
    'default_payment_method' => $paymentMethodId
]);

$array_Subscription = json_decode(json_encode($res), true);

$arr[] = array(
      'email'=>$email,
      'customer'=>$customer->id,
      'paymentMethodId'=>$paymentMethodId,
);

//sleep(1);
//print_r($customer);
//print_r($array_Subscription);

if(!empty($array_Subscription['id'])){
  echo json_encode(1);
}else{
  echo json_encode(2);
}


