<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModel;

class FilterProductVm extends VirtualModel
{
    public static $filter = [];

    public function attributes(): array
    {
        return [
            'id',
            'category_id',
            'product_id',
            'value',
        ];
    }

    public function isActive()
    {
        if (!self::$filter) {
            if (\Yii::$app->request->get('filter')) {
                self::$filter = \Yii::$app->request->get('filter');
            } else {
                self::$filter = ['none'];
            }
        }

        return in_array($this->getKey(), self::$filter);
    }

    public function getKey()
    {
        return $this->value.'_'.$this->category_id;
    }
}