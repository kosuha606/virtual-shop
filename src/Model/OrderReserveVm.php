<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * @property $orderId
 * @property $productId
 * @property $qty
 * @property $userType
 */
class OrderReserveVm extends VirtualModelEntity
{
    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'orderId',
            'productId',
            'qty',
            'userType',
        ];
    }
}
