<?php

namespace FLApp\Services;

class BaseService {
    const HOST = 'https://testinghost.apps.thefulfillmentlab.com/ws/ASPX/';
    protected $client_id;
    protected $client_key;
    private $service;

	private $post_endpoint;
	private $update_endpoint;

	protected $request;

	protected $errors;

	function __construct($operationName) {
		$this->errors = [];
		// ex: http://apps.thefulfillmentlab.com/ws/ASPX/FLUpdateOrders.aspx
		$this->endpoint = self::HOST . $operationName . '.aspx';
		$this->service = new \Sabre\Xml\Service();
	}


	public function setCreds($client_id, $client_key) {
		$this->client_id = $client_id;
		$this->client_key = $client_key;
	}

	public function makeRequest(\Sabre\Xml\XmlSerializable $object) {
		$xml = $this->service->write($object->getContainer(), $object);

		return $this->post($xml);
	}

	public function makeGetRequest(\FLApp\Requests\BaseRequest $request) {
		return $this->get($this->endpoint . $request->buildQuery());
	}

	public function post($body) {
		try {
			$response = \Httpful\Request::post($this->endpoint)
			    ->body($body)
			    ->sendsXml()
	    		->parseWith(function($body) {
			        return $body;
			    })
			    ->send();
		} catch (\Httpful\Exception\ConnectionErrorException $e) {
			$this->errors[] = ['', "Error sending request: " . $e->getMessage()];
			return [];
		}

		if (strlen($response) == 0) {
			$this->errors[] = ['', "Empty response"];
			return [];
		}

		return $this->handleResponse($response);
	}

	public function get($url) {
		try {
			$response = \Httpful\Request::get($url)
	    		->parseWith(function($body) {
			        return $body;
			    })
			    ->send();
		} catch (\Httpful\Exception\ConnectionErrorException $e) {
			$this->errors[] = ['', "Error sending request to URL " . $url . ": " . $e->getMessage()];
			return null;
		}

		if (strlen($response) == 0) {
			$this->errors[] = ['', "Empty response. URL " . $url];
			return null;
		}

		return $this->handleResponse($response);
	}

	public function handleResponse($response) {
		switch ($response->code) {
		case 200 :
			// process response
			break;
		case 404 :
			// something very wrong
			$this->errors[] = ['', "Invalid endpoint (404)"];
			return null;
		default: 
			// unknown
			$this->errors[] = ['', "Unknown response status: " . $response->code];
			return null;
		}

		return $this->processResult($response->body);
	}

	public function processResult($rawxml) {
		$service = new \Sabre\Xml\Service();
		try {
			$result = $service->parse($rawxml);
		} catch (\Sabre\Xml\LibXMLException $e) {
			// parse error
			$this->errors[] = ['', "Parse error: " . $e->getMessage()];
		} catch (Exception $e) {
			$this->errors[] = ['', "Unknown error processing response: " . $e->getMessage()];
		}
		$result = $this->request->getResult($result);

		$this->errors = array_merge($this->errors, $this->request->errors);

		return $result;
	}

	public function serviceErrors() {
		return $this->errors;
	}
}

