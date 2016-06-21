<?php

namespace AntonOrlov\Lasership;
use Curl\Curl;

class Api
{
    private $apiId;
    private $apiKey;
    private $lastResponse = '';
    private $curl;

    public function __construct($apiId, $apiKey)
    {
        $this->apiId = $apiId;
        $this->apiKey = $apiKey;
        $this->init();
    }

    private function init() {
        $this->curl = new Curl();
        $this->curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
        $this->curl->setopt(CURLOPT_SSL_VERIFYPEER, FALSE);
    }

    public function addOrder($branch, $origin, $order, $test = 0) {
        $_order = [
            'CustomerBranch' => $branch,
            'CustomerOrderNumber' => $order['id'],
            'OrderedFor' => '',
            'OrderedBy' => [
                'Name' => $order['to']['name'],
                'Phone' => '',
                'Email' => ''
            ],
            'Reference1' => $order['client'],
            'Reference2' => '',
            'ServiceCode' => 'SD',
            'PickupType' => 'None',
            'Origin' => [
                'LocationType' => 'Business',
                'CustomerClientID' => '',
                'Contact' => $origin['contact'],
                'Organization' => $origin['org'],
                'Address' => $origin['address'],
                'Address2' => '',
                'PostalCode' => $origin['code'],
                'City' => $origin['city'],
                'State' => $origin['state'],
                'Country' => $origin['country'],
                'Phone' => '',
                'PhoneExtension' => '',
                'Email' => $origin['email'],
                'Payor' => '',
                'Instruction' => '',
                'UTCExpectedReadyForPickupBy' => '',
                'UTCExpectedDeparture' => '',
                'CustomerRoute' => '',
                'CustomerSequence' => ''
            ],
            'Destination' => [
                'LocationType' => 'Residence',
                'CustomerClientID' => '',
                'Contact' =>  $order['to']['name'],
                'Organization' => '',
                'Address' => $order['to']['address'],
                'Address2' => '',
                'PostalCode' => $order['to']['code'],
                'City' => $order['to']['city'],
                'State' => $order['to']['state'],
                'Country' => 'US',
                'Phone' => '',
                'PhoneExtension' => '',
                'Email' => $order['to']['email'],
                'Payor' => '',
                'Instruction' => '',
                'UTCExpectedDeliveryBy' => $order['to']['deliveryDate'],
                'CustomerRoute' => '',
                'CustomerSequence' => ''
            ],
            'Pieces' => []
        ];
        foreach ($order['items'] as $item) {
            array_push($_order['Pieces'], [
                'ContainerType' => 'Box',
                'CustomerBarcode' => '',
                'CustomerPalletBarcode' => '',
                'Weight' => $item['weight'],
                'WeightUnit' => 'lbs',
                'Width' => $item['width'],
                'Length' => $item['length'],
                'Height' => $item['height'],
                'DimensionUnit' => 'in',
                'Description' => $item['name'],
                'Reference' => '',
                'DeclaredValue' => $item['price'],
                'DeclaredValueCurrency' => 'USD',
                'SignatureType' => $item['sign'],
                'Attributes' => []
            ]);
        }
        $jsonOrder = json_encode($_order);
        $url = 'https://api.lasership.com/Method/PlaceOrder/json/' . $this->apiId . '/' . $this->apiKey  . '/' . $test . '/1/DN4x6';
        $this->curl->post($url, array('Order' => $jsonOrder));
        $this->lastResponse = $this->curl->response;
        return json_decode($this->lastResponse, true);
    }

    public function getEvents() {
        $url = 'https://api.lasership.com/Method/GetEvents/json/' . $this->apiId . '/' . $this->apiKey;
        $this->curl->get($url);
        $this->lastResponse = $this->curl->response;
        return json_decode($this->lastResponse, true);
    }

    public function getLastResponse() {
        return $this->lastResponse;
    }
}
