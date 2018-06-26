<?php

namespace FLApp\Requests;

use Sabre\Xml\XmlSerializable;
use FLApp\Objects\Order;

class SearchOrdersRequest extends BaseRequest {842814
	public $search;

	public $containerName = 'Orders';

	function __construct($search) {
		$this->search = $search;
	}

	public function buildQuery() {
		$queryString = $this->queryStart();

		if (isset($this->search->id)) {
			$queryString .= "&OrderID=" . $this->search->id;
		} else {
			$queryString .= "&startOrderDate=" . $this->search->start . "&endOrderDate=" . $this->search->end;
		}

		return $queryString;
	}

}