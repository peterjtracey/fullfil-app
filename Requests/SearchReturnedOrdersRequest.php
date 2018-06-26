<?php

namespace FLApp\Requests;

use Sabre\Xml\XmlSerializable;
use FLApp\Objects\Order;

class SearchReturnedOrdersRequest extends BaseRequest {
	public $search;

	public $containerName = 'Orders';

	function __construct($search) {
		$this->search = $search;
	}

	public function buildQuery() {
		$queryString = $this->queryStart();

		// just in case api actually does support id search
		//if (isset($this->search->id)) {
		//	$queryString .= "&OrderID=" . $this->search->id;
		//} else {
			$queryString .= "&startOrderDate=" . $this->search->start . "&endOrderDate=" . $this->search->end;
		//}

		return $queryString;
	}

}