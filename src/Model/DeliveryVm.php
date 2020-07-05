<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * Вариант доставки
 * @package kosuha606\Model\iteration2\model
 * @property $description
 */
class DeliveryVm extends VirtualModelEntity
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