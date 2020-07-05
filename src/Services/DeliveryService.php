<?php

namespace kosuha606\VirtualShop\Services;

use kosuha606\VirtualModel\VirtualModelManager;
use kosuha606\VirtualShop\Model\DeliveryVm;

/**
 * @package kosuha606\VirtualShop\Services
 */
class DeliveryService
{
    /**
     * @param $id
     * @return \kosuha606\VirtualModel\VirtualModelEntity
     * @throws \Exception
     */
    public function findDeliveryById($id)
    {
        $delivery = VirtualModelManager::getInstance()->getProvider()->one(DeliveryVm::class, [
            'where' => [
                ['=', 'id', $id]
            ]
        ]);

        return $delivery;
    }
}