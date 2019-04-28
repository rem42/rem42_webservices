<?php

class WSColissimoPickupPoint extends ColissimoPickupPoint
{
    /** @var int $id_order */
    public $id_order;

    protected $webserviceParameters = [
        'fields' => [
            'id_order' => ['type' => ObjectModel::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false],
        ],
        'objectsNodeName' => 'pickup_points',
        'objectNodeName' => 'pickup_point',
    ];

    /**
     * @param $orderId
     *
     * @return WSColissimoPickupPoint
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getWSPickupPointByIdOrder($orderId)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_pickup_point')
            ->from('colissimo_order')
            ->where('id_order = "' . pSQL($orderId).'"');
        $id = Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->getValue($dbQuery);

        $pickupPoint = new self((int) $id);
        $pickupPoint->id_order = $orderId;
        return $pickupPoint;
    }
}
