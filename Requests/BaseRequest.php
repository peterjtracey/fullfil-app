<?php

namespace FLApp\Requests;

class BaseRequest {
	const NS = '';

	public $client_id;
	public $client_key;

	public $containerName;

	public $orders;

	public $errors = [];

	public function getContainer() {
		return self::NS . $this->containerName;
	}


	public function setCreds($client_id, $client_key) {
		$this->client_id = $client_id;
		$this->client_key = $client_key;
	}

	public function requestStart() {
		return [
			self::NS . 'ClientID' => $this->client_id,
			self::NS . 'ClientKey' => $this->client_key
			];
	}

	public function queryStart() {
		return "?ClientID=" . $this->client_id . "&ClientKey=" . $this->client_key;
	}

	public function buildQuery() {
		
	}

	public function parseErrors($errorOrderID, $result) {
    	$errors = $result[1]['value'];
    	$orderErrors = [];
    	foreach ($errors as $errorElem) {
    		$id = preg_replace('/[^0-9]/', "", $errorElem['value'][0]['value']);
    		if (intval($id) == intval($errorOrderID)) {
    			$orderErrors[] = $errorElem['value'][0]['value'] . ':' . $errorElem['value'][1]['value'];
    		} else {
    			$this->errors = [$errorElem['value'][0]['value'], $errorElem['value'][1]['value']];
    		}
    	}

		return $orderErrors;
	}
}