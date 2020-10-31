<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

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
