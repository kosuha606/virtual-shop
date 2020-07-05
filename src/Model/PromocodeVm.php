<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * Промокод для корзины
 * @package kosuha606\Model\iteration2\model
 * @property $code
 * @method static one(array $array)
 */
class PromocodeVm extends VirtualModelEntity
{
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