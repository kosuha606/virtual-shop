<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * @property $id
 * @property $productId
 * @property $qty
 * @property $userType
 */
class ProductRestsVm extends VirtualModelEntity
{
    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'id',
            'productId',
            'qty',
            'userType',
        ];
    }
}
