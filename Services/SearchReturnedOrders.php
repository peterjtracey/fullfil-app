<?php

namespace FLApp\Services;

use FLApp\Requests\PostOrdersRequest;
use FLApp\Objects\OrderResponse;
use FLApp\Objects\Order;

class SearchReturnedOrders extends BaseService {
	function __construct() {
		parent::__construct('FLSearchReturns');
	}

	public function send($search) {
		$this->request = new \FLApp\Requests\SearchReturnedOrdersRequest($search);

		$this->request->setCreds($this->client_id, $this->client_key);

		return $this->makeGetRequest($this->request);
	}

	public function processResult($rawxml) {
		$service = new \Sabre\Xml\Service();
		$service->elementMap = [
		    '{}Orders' => 'FLApp\Objects\OrderResponse',
		    '{}Order' => 'FLApp\Objects\Order',
		];

		return $service->parse($rawxml);
	}
}