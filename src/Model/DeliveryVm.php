<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

class DeliveryVm extends VirtualModelEntity
{
    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'id',
            'price',
            'description',
            'userType',
        ];
    }
}
