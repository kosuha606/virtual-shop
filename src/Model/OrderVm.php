<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * @property $id
 * @property $orderData
 * @property $user_id
 * @property $total
 * @property $userType
 * @property $reserve
 */
class OrderVm extends VirtualModelEntity
{
    /**
     * @return array
     */
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

    /**
     * @return mixed
     */
    public function getOrderData()
    {
        if (!is_array($this->attributes['orderData'])) {
            $attributes['orderData'] = json_decode($this->attributes['orderData'], true);
        }

        return $this->attributes['orderData'];
    }

    /**
     * @param array $attributes
     * @return void
     */
    public function setAttributes($attributes)
    {
        if (!is_array($attributes['orderData'])) {
            $attributes['orderData'] = json_decode($attributes['orderData'], true);
        }

        parent::setAttributes($attributes);
    }

    /**
     * @param array $config
     * @return mixed|null
     * @throws \Exception
     */
    public function save($config = [])
    {
        $this->attributes['orderData'] = json_encode($this->attributes['orderData'], JSON_UNESCAPED_UNICODE);

        return parent::save($config);
    }
}
