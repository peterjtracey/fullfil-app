<?php
/*
FullfilApp - LGPL license
https://github.com/peterjtracey/fullfil-app
*/
namespace FLApp;

require_once 'objects/Order.php';
require_once 'objects/OrderItem.php';
require_once 'objects/Search.php';
require_once 'objects/OrderResponse.php';
require_once 'services/BaseService.php';
require_once 'requests/BaseRequest.php';
require_once 'requests/PostOrderRequest.php';
require_once 'requests/CancelOrderRequest.php';
require_once 'requests/SearchOrdersRequest.php';
require_once 'requests/SearchReturnedOrdersRequest.php';
require_once 'services/PostOrders.php';
require_once 'services/UpdateOrders.php';
require_once 'services/CancelOrders.php';
require_once 'services/SearchOrders.php';
require_once 'services/SearchReturnedOrders.php';
require_once 'services/InventoryService.php';


class FullfilApp {
	private $client_id;
	private $client_key;
	private $service;

	function __construct($id, $key, $debug_orderid = null) {
		$this->client_id = $id;
		$this->client_key = $key;

		if (!is_null($debug_orderid)) {
			define('TFLAPP_DEBUG_ORDER', $debug_orderid);
		}
	}	

    /**
     * Get last request errors
     *
     * @return  array
     */
	public function getErrors() {
		return $this->service->serviceErrors();
	}

    /**
     * Send orders to be shipped
     *
     * results will have status populated
     *
     * @param   array of FLApp\Objects\Order
     * @return  array of FLApp\Objects\Order
     */
	public function PostOrders($orders) {
		$this->service = new \FLApp\Services\PostOrders();
		$this->service->setCreds($this->client_id, $this->client_key);

		return $this->service->send($orders);
	}

    /**
     * Edit orders
     *
     * results will have status populated
     *
     * @param   array of FLApp\Objects\Order
     * @return  array of FLApp\Objects\Order
     */
	public function UpdateOrders($orders) {
		$this->service = new \FLApp\Services\UpdateOrders();
		$this->service->setCreds($this->client_id, $this->client_key);

		return $this->service->send($orders);
	}

    /**
     * Cancel orders
     *
     * results will have status populated
     *
     * @param   array of FLApp\Objects\Order
     * @return  array of FLApp\Objects\Order
     */
	public function CancelOrders($orders) {
		$this->service = new \FLApp\Services\CancelOrders();
		$this->service->setCreds($this->client_id, $this->client_key);

		return $this->service->send($orders);
	}

    /**
     * Search orders by start and end dates
     *
     * For search by id use LoadOrder
     *
     * see Order object for returned properties
     *
     * @param   FLApp\Objects\Search
     * @return  FLApp\Objects\OrderResponse
     */
	public function SearchOrders($search) {
		$this->service = new \FLApp\Services\SearchOrders();
		$this->service->setCreds($this->client_id, $this->client_key);

		$results = $this->service->send($search);

		if (!isset($search->id)) {
			return $results;
		} else {
	        foreach ($results as $order) {
	          if ($order->id == $search->id) {
	          	return $order;
	          }
	        }
		}

		return [];
	}

    /**
     * Search orders by start and end dates
     *
     * For search by id use LoadOrder
     *
     * see Order object for returned properties
     *
     * @param   FLApp\Objects\Search
     * @return  FLApp\Objects\OrderResponse
     */
	public function SearchReturnedOrders($search) {
		$this->service = new \FLApp\Services\SearchReturnedOrders();
		$this->service->setCreds($this->client_id, $this->client_key);

		$results = $this->service->send($search);

		if (!isset($search->id)) {
			return $results;
		} else {
            foreach ($results->orders as $order) {
              if ($order->id == $search->id) {
              	return $order;
              }
            }
		}

		return [];
	}

    /**
     * Find order by id
     *
     * see Order object for returned properties
     *
     * @param   FLApp\Objects\Search
     * @return  FLApp\Objects\Order
     */
	public function LoadOrder($id) {
		$search = new \FLApp\Objects\Search();
		$search->id = $id;

		$this->service = new \FLApp\Services\SearchOrders();
		$this->service->setCreds($this->client_id, $this->client_key);

		$result = $this->service->send($search);

		if (count($result->orders) > 0 && $result->orders[0]->id == $id) {
			return $result->orders[0];
		} else {
			return null;
		}
	}

    /**
     * Format date to expected format of api
     *
     * no argument will use current date
     *
     * @param   int|string timestamp or string date value
     * @return  string
     */
	public static function apiDate($date = null) {
		if ($date == null) {
			return date('m/d/Y');			
		} else {
			if (is_numeric($date)) {
				$time = $date;
			} else {
		        $time = strtotime($date);
		    }

	        return date('m/d/Y', $time);
       	}
	}

    /**
     * Process inventory snapshot and update inventory 
     * accordingly
     *
     * @param   string $host
     * @param   string $user
     * @param   string $pass
     * @return  bool
     */
	public static function ScanInventory($host, $user, $pass, $temp_folder) {
		if (\FLApp\Services\InventoryService::findSnapshot($host, $user, $pass, $temp_folder)) {
			return \FLApp\Services\InventoryService::processFile($temp_folder);
		}

		return false;
	}
}
