<?php

namespace FLApp\Services;

use FLApp\Requests\PostOrdersRequest;

class CancelOrders extends BaseService {
	function __construct() {

		parent::__construct('FLCancelOrders');
	}

	public function send($orders) {
		$this->request = new \FLApp\Requests\CancelOrderRequest($orders);

		$this->request->setCreds($this->client_id, $this->client_key);

		return $this->makeRequest($this->request);
	}

}