<?php

namespace FLApp\Requests;

use Sabre\Xml\XmlSerializable;

class PostOrdersRequest extends BaseRequest 
	implements \Sabre\Xml\XmlSerializable {

	public $containerName = 'Orders';

	function __construct($orders) {
		$this->orders = $orders;
	}

    function xmlSerialize(\Sabre\Xml\Writer $writer) {
    	$elements = $this->requestStart();

    	foreach ($this->orders as $order) {
    		$items = [];
	        foreach ($order->items as $item) {
	        	$items[] = [
	        		self::NS . 'OrderItem' => [
	        			self::NS . 'Name' => $item->name,
	        			self::NS . 'SKU' => $item->sku,
	        			self::NS . 'Qty' => $item->quantity
	        		]
	        	];
	        }
    		$elements[] = [[
					        'name' => self::NS . 'Order',
					        'attributes' => [
					            'OrderID' => $order->id,
				        ],
			        'value' => [
		            self::NS . 'Company' => $order->company,
		            self::NS . 'FirstName' => $order->first_name,
		            self::NS . 'LastName' => $order->last_name,
		            self::NS . 'Address1' => $order->address1,
		            self::NS . 'Address2' => $order->address2,
		            self::NS . 'City' => $order->city,
		            self::NS . 'State' => $order->state,
		            self::NS . 'postalCode' => $order->postalCode,
		            self::NS . 'Country' => $order->country,
		            self::NS . 'Email' => $order->email,
		            self::NS . 'Phone' => $order->phone,
		            self::NS . 'Fax' => $order->fax,
		            self::NS . 'OrderDate' => $order->order_date,
		            self::NS . 'ShipMethod' => $order->shipby,
		            self::NS . 'SignatureRequired' => $order->signature ? 'Y' : 'N',
		     		       $items 
	        		]
	            ]
	        ];

	    }

	    $writer->write($elements);
    }


    function getResult($result) {
    	$orderResults = $result[0]['value'];
    	$found = false;
	    foreach ($this->orders as $order) {
	    	for ($i=0; $i<count($orderResults); $i++) {
	    		$orderId = $orderResults[$i]['attributes']['OrderID'];
	    		$status = $orderResults[$i]['attributes']['Status'];

	    		if ($order->id == $orderId) {
	    			$found = true;
	    			$order->status = strtoupper($status);
	    			if ($order->status == \FLApp\Objects\Order::POST_STATUS_ERROR) {
	    				$order->errors = $this->parseErrors($orderId, $result);
	    			}
	    			break;
		    	}
	    	}
	    	if (!$found) {
	    		$order->status = \FLApp\Objects\Order::STATUS_NORESPONSE;
	    	}
		}

	    if (count($this->errors) == 0) {
	    	$this->parseErrors(-1, $result);
	    }

    	for ($i=0; $i<count($orderResults); $i++) {
    		$orderId = $orderResults[$i]['attributes']['OrderID'];
    		$status = $orderResults[$i]['attributes']['Status'];

    		$found = false;
	    	foreach ($this->orders as $order) {
	    		if ($order->id == $orderId) {
	    			$found = true;
	    			break;
	    		}
	    	}
	    	if (!$found) {
	    		throw new \Exception("Order in response that wasn't sent: " . $orderId, 1);	    		
	    	}
	    }

		return $this->orders;
    }
}