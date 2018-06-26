<?php

namespace FLApp\Objects;

use Sabre\Xml\XmlDeserializable;

class OrderResponse implements XmlDeserializable {
	public $orders = [];

    static function xmlDeserialize(\Sabre\Xml\Reader $reader) {
        $response = new self();

        $children = $reader->parseInnerTree();
        foreach ($children as $child) {
            if ($child['value'] instanceof Order) {
            	$child['value']->id = $child['attributes']['OrderID'];
                $response->orders[] = $child['value'];
            }
        }
        
        return $response;
    }
}