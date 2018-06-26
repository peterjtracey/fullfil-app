<?php

require_once(__DIR__ . '/../vendor/autoload.php');

require_once 'objects/Order.php';
require_once 'objects/OrderItem.php';
require_once 'objects/Search.php';
require_once 'objects/OrderResponse.php';
require_once 'services/BaseService.php';
require_once 'requests/BaseRequest.php';
require_once 'requests/PostOrderRequest.php';
require_once 'requests/CancelOrderRequest.php';
require_once 'requests/SearchOrdersRequest.php';
require_once 'services/PostOrders.php';
require_once 'services/UpdateOrders.php';
require_once 'services/CancelOrders.php';
require_once 'services/SearchOrders.php';

$service = new \FLApp\Services\UpdateOrders();
$service->setCreds('12345678', 'xxxx-xxxxxx-xxxxx-xxxxxx');

$orderItem = new stdClass();
$orderItem->name = 'Frisbee';
$orderItem->sku = 'PRODUCT-SKU-005';
$orderItem->quantity = 1;

$order = new stdCLass();

$order->id = 9239923;
$order->company = 'asdf';
$order->first_name = 'asdf';
$order->last_name = 'asdf';
$order->address1 = 'asdf';
$order->address2 = 'asdf';
$order->city = 'asdf';
$order->state = 'asdf';
$order->postalCode = 'asdf';
$order->country = 'asdf';
$order->email = 'asdf';
$order->phone = 'asdf';
$order->fax = 'asdf';
$order->order_date = 'asdf';
$order->shipby = 'asdf';
$order->signature = false;

$order->items = [$orderItem];

echo "<h1>Update Order</h1>";

print_r($service->send([$order]));


$service = new \FLApp\Services\CancelOrders();
$service->setCreds('12345678', 'xxxx-xxxxxx-xxxxx-xxxxxx');

$order = new stdClass();

$order->id = 9239923;
$order->cancel_date = '07/28/2012';
$order->reason = 'Fraud';

echo "<h1>Cancel Order</h1>";

print_r($service->send([$order]));


$service = new \FLApp\Services\SearchOrders();
$service->setCreds('12345678', 'xxxx-xxxxxx-xxxxx-xxxxxx');

$search = new stdClass();

$search->id = 9239923;

echo "<h1>Search Order</h1>";

print_r($service->send($search));
