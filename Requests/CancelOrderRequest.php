<?php

namespace FLApp\Requests;

use Sabre\Xml\XmlSerializable;

class CancelOrderRequest extends BaseRequest 
	implements \Sabre\Xml\XmlSerializable {
/*
outputs 
<Order OrderID="9239923">
<CancelDate>07/28/2012</CancelDate>
<CancelReason>Fraud</ CancelReason>
</Order>*/


	public $containerName = 'Orders';

	function __construct($orders) {
		$this->orders = $orders;
	}

    function xmlSerialize(\Sabre\Xml\Writer $writer) {
    	$elements = $this->requestStart();

    	foreach ($this->orders as $order) {
    		if (!isset($order->cancel_date)) {
    			$order->cancel_date = \FulfillApp::apiDate();
    		}
    		$elements[] = [[
					        'name' => self::NS . 'Order',
					        'attributes' => [
					            'OrderID' => $order->id,
				        ],
			        'value' => [
		            self::NS . 'CancelDate' => $order->cancel_date,
		            self::NS . 'CancelReason' => $order->reason
	        		]
	            ]
	        ];
	    }

	    $writer->write($elements);
    }


    function getResult($result) {
    	$orderResults = $result[0]['value'];
    	for ($i=0; $i<count($orderResults); $i++) {
    		$orderId = $orderResults[$i]['attributes']['OrderID'];
    		$status = $orderResults[$i]['attributes']['Status'];

    		$found = true;
	    	foreach ($this->orders as $order) {
	    		if ($order->id == $orderId) {
	    			$order->status = strtoupper($status);
	    			if ($order->status == 'ERROR') {
	    				$order->errors = $this->parseErrors($orderId, $orderResults);
	    			}
	    		}
	    	}
    	}

		return $this->orders;
    }
}