<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

class PromocodeVm extends VirtualModelEntity
{
    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'id',
            'amount',
            'code',
            'userType',
        ];
    }
}
