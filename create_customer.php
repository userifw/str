<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
//$stripeSecretKey = 'sk_test_51NsPv0AXV3XqsQb47u6Fps8IWSuTJSzVSDbyptKDWt9nqVaC8GLACdEuWCQYNZJJStNU4zS4gFcxNEh8N526r9B300NlrprFdE';
$stripeSecretKey = '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';
header("Content-type: application/json");
\Stripe\Stripe::setApiKey($stripeSecretKey);


$incoming = file_get_contents('php://input');

if (isset($incoming) && $incoming) {
  $payment_info = json_decode($incoming, 1);
}


$phone = preg_replace("/[^,.0-9]/", '', $payment_info['phone']);
$email = $payment_info['email'];
$price = 'price_1OUv9tAXV3XqsQb4N6AitnxM';
$site = $payment_info['site'];


$paymentMethodId = $payment_info['paymentMethodId']['id'];

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

$data = array(
      'customer'=>$customer->id,
      'email'=>$email,
      'phone' => $phone,
      'card' => $payment_info['paymentMethodId']['card']['exp_year'].'-'.$payment_info['paymentMethodId']['card']['last4'],
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
