<?php

// Tested on PHP 5.3

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('ServiceProvider needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('ServiceProvider needs the JSON PHP extension.');
}
if (!function_exists('mb_detect_encoding')) {
  throw new Exception('ServiceProvider needs the Multibyte String PHP extension.');
}

if (!class_exists('\ServiceProvider\Settings')) {
  require_once (__DIR__ . '/ServiceProvider/Settings.php');
  require_once (__DIR__ . '/ServiceProvider/Logger.php');
  require_once (__DIR__ . '/ServiceProvider/Language.php');
  require_once (__DIR__ . '/ServiceProvider/Customer.php');
  require_once (__DIR__ . '/ServiceProvider/ShippingAddress.php');
  require_once (__DIR__ . '/ServiceProvider/Card.php');
  require_once (__DIR__ . '/ServiceProvider/Money.php');
  require_once (__DIR__ . '/ServiceProvider/ResponseBase.php');
  require_once (__DIR__ . '/ServiceProvider/Response.php');
  require_once (__DIR__ . '/ServiceProvider/ResponseCheckout.php');
  require_once (__DIR__ . '/ServiceProvider/ResponseCardToken.php');
  require_once (__DIR__ . '/ServiceProvider/ApiAbstract.php');
  require_once (__DIR__ . '/ServiceProvider/ChildTransaction.php');
  require_once (__DIR__ . '/ServiceProvider/GatewayTransport.php');
  require_once (__DIR__ . '/ServiceProvider/AuthorizationOperation.php');
  require_once (__DIR__ . '/ServiceProvider/PaymentOperation.php');
  require_once (__DIR__ . '/ServiceProvider/CaptureOperation.php');
  require_once (__DIR__ . '/ServiceProvider/VoidOperation.php');
  require_once (__DIR__ . '/ServiceProvider/RefundOperation.php');
  require_once (__DIR__ . '/ServiceProvider/CreditOperation.php');
  require_once (__DIR__ . '/ServiceProvider/QueryByUid.php');
  require_once (__DIR__ . '/ServiceProvider/QueryByTransactionId.php');
  require_once (__DIR__ . '/ServiceProvider/QueryByToken.php');
  require_once (__DIR__ . '/ServiceProvider/GetPaymentToken.php');
  require_once (__DIR__ . '/ServiceProvider/Webhook.php');
  require_once (__DIR__ . '/ServiceProvider/CardToken.php');
  require_once (__DIR__ . '/ServiceProvider/PaymentMethod/Base.php');
  require_once (__DIR__ . '/ServiceProvider/PaymentMethod/Erip.php');
  require_once (__DIR__ . '/ServiceProvider/PaymentMethod/CreditCard.php');
  require_once (__DIR__ . '/ServiceProvider/PaymentMethod/CreditCardHalva.php');
  require_once (__DIR__ . '/ServiceProvider/PaymentMethod/Emexvoucher.php');
}
?>
