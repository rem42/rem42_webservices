<?php

class PrestashopStats
{
    public $id = 42;

    public $product_count;

    public $order_count;

    static $webserviceParameters = [
        'retrieveData' => [
            'className' => 'PrestashopStats',
            'retrieveMethod' => 'loadAll',
            'params' => [],
        ],
        'fields' => [
            'product_count' => ['type' => ObjectModel::TYPE_INT],
            'order_count' => ['type' => ObjectModel::TYPE_INT],
        ],
        'objectsNodeName' => 'stats',
        'objectNodeName' => 'stat',
    ];

    public function getWebserviceParameters()
    {
        $webserviceParameters = self::$webserviceParameters;
        foreach ($webserviceParameters['fields'] as $field => $value) {
            $webserviceParameters['fields'][$field]['sqlId'] = $field;
        }

        return $webserviceParameters;
    }

    public function loadAll()
    {
        $this->nbProducts();
        $this->nbOrders();
    }

    public function nbProducts()
    {
        $table = 'product';
        $productDefinition = Product::$definition;

        if(isset($productDefinition['table'])) {
            $table = $productDefinition['table'];
        }

        $return = Db::getInstance()->getRow('SELECT COUNT(*) AS NBPRODUCT FROM ' . _DB_PREFIX_ . $table);

        $this->product_count = $return['NBPRODUCT'];
    }

    public function nbOrders()
    {
        $table = 'orders';
        $productDefinition = Order::$definition;

        if(isset($productDefinition['table'])) {
            $table = $productDefinition['table'];
        }

        $return = Db::getInstance()->getRow('SELECT COUNT(*) AS NBORDER FROM ' . _DB_PREFIX_ . $table);

        $this->order_count = $return['NBORDER'];
    }
}
