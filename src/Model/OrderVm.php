<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModel;

/**
 * @property $orderData
 * @property $total
 * @property $userType
 * @property $reserve
 */
class OrderVm extends VirtualModel
{
    public function attributes(): array
    {
        return [
            'id',
            'orderData',
            'user_id',
            'total',
            'userType',
            'reserve',
        ];
    }

    public function getOrderData()
    {
        if (!is_array($this->attributes['orderData'])) {
            $attributes['orderData'] = json_decode($this->attributes['orderData'], true);
        }

        return $this->attributes['orderData'];
    }

    public function setAttributes($attributes)
    {
        if (!is_array($attributes['orderData'])) {
            $attributes['orderData'] = json_decode($attributes['orderData'], true);
        }

        return parent::setAttributes($attributes);
    }

    public function save($config = [])
    {
        $this->attributes['orderData'] = json_encode($this->attributes['orderData'], JSON_UNESCAPED_UNICODE);
        return parent::save($config);
    }
}