<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * Вариант оплаты
 * @package kosuha606\Model\iteration2\model
 * @property $description
 * @property $comission
 */
class PaymentVm extends VirtualModelEntity
{
    public function attributes(): array
    {
        return [
            'id',
            'comission',
            'description',
            'userType',
        ];
    }
}