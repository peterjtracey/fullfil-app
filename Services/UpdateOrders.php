<?php

namespace FLApp\Services;

use FLApp\Requests\PostOrdersRequest;

class UpdateOrders extends BaseService {
	function __construct() {

		parent::__construct('FLUpdateOrders');
	}

	public function send($orders) {
		$this->request = new \FLApp\Requests\PostOrdersRequest($orders);

		$this->request->setCreds($this->client_id, $this->client_key);

		return $this->makeRequest($this->request);
	}

}