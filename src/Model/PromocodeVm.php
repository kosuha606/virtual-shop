<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModel;

/**
 * Промокод для корзины
 * @package kosuha606\Model\iteration2\model
 * @property $code
 */
class PromocodeVm extends VirtualModel
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