<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModel;

/**
 * Вариант доставки
 * @package kosuha606\Model\iteration2\model
 * @property $description
 */
class DeliveryVm extends VirtualModel
{
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