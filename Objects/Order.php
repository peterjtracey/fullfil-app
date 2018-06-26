<?php

namespace FLApp\Objects;

use Sabre\Xml\XmlDeserializable;

class Order implements XmlDeserializable {
	const STATUS_NOTSENT = "NOTSENT";
	const STATUS_NORESPONSE = "NORESPONSE";

	const POST_STATUS_RECEIVED = "RECEIVED";
	const POST_STATUS_DUPLICATE = "DUPLICATE";
	const POST_STATUS_ERROR = "ERROR";

	const SHIP_STATUS_BACKORDER = 'BACKORDER';
	const SHIP_STATUS_BADADDRESS = 'BADADDRESS';
	const SHIP_STATUS_BADSKUHOLD = 'BADSKUHOLD';
	const SHIP_STATUS_CANCELLED = 'CANCELLED';
	const SHIP_STATUS_TFLHOLD = 'TFLHOLD';
	const SHIP_STATUS_PROCESSING = 'PROCESSING';
	const SHIP_STATUS_RECEIVED = 'RECEIVED';
	const SHIP_STATUS_RETURNED = 'RETURNED';
	const SHIP_STATUS_SHIPPED = 'SHIPPED';


	const RETURN_STATUS_AUTHORIZED = 'AUTHORIZED';
	const RETURN_STATUS_UNAUTHORIZED = 'UNAUTHORIZED';
	const RETURN_STATUS_UNDELIVERABLE = 'UNDELIVERABLE';

	public $origObject;

	// data to post
	public $id;
	public $company;
	public $first_name;
	public $last_name;
	public $address1;
	public $address2;
	public $city;
	public $state;
	public $postalCode;
	public $country;
	public $email;
	public $phone;
	public $fax;
	public $order_date;
	public $shipby;
	public $signature;

	// data in post/update responses 
	public $status;
	public $errors;
	
	// data in search responses
	public $OrderDate;
	public $ReceiveDate;
	public $ShipDate;
	public $ShipStatus;
	public $ShipMethod;
	public $Tracking;
	public $DeliveryDate;
	public $Postage;

	// data in search returns responses
	public $ReturnType;
	public $ReturnDate;
	public $RMANumber;
	public $RMADate;

	public static $map = [
		'OrderDate' => 'OrderDate',
		'ReceiveDate' => 'ReceiveDate',
		'ShipDate' => 'ShipDate',
		'ShipStatus' => 'ShipStatus',
		'ShipMethod' => 'ShipMethod',
		'Tracking' => 'Tracking',
		'DeliveryDate' => 'DeliveryDate',
		'Postage' => 'Postage',
		// returned orders
		'ReturnType' => 'ReturnType',
		'ReturnDate' => 'ReturnDate',
		'RMANumber' => 'RMANumber',
		'RMADate' => 'RMADate',
	];

	function __construct() {
		$this->status = self::STATUS_NOTSENT;
		$this->ReturnType = self::STATUS_NOTSENT;
	}


	public function addItem($sku, $name, $quantity) {
		$item = new OrderItem();
		$item->sku = $sku;
		$item->name = $name;
		$item->quantity = $quantity;
		$this->items[] = $item;
	}

    static function xmlDeserialize(\Sabre\Xml\Reader $reader) {
        $order = new self();

        // Borrowing a parser from the KeyValue class.
        $keyValue = \Sabre\Xml\Element\KeyValue::xmlDeserialize($reader);

        foreach (self::$map as $element => $property) {

	        if (isset($keyValue['{}' . $element])) {
	            $order->$property = $keyValue['{}' . $element];
	        }
        }

        $order->ShipStatus = strtoupper($order->ShipStatus);
        $order->ReturnType = strtoupper($order->ReturnType);

        return $order;
    }
}