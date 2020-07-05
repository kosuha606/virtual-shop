<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualAdmin\Helpers\ConstructorHelper;
use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * Акция для продукта
 * @package kosuha606\Model\iteration2\model
 * @property $normalizeProductIds
 */
class ActionVm extends VirtualModelEntity
{
    public function attributes(): array
    {
        return [
            'id',
            'normalizeProductIds',
            'productIds',
            'percent',
            'userType',
        ];
    }

    public function getProductIds()
    {
        if (is_array($this->attributes['productIds'])) {
            $result = $this->attributes['productIds'];
        } else {
            $result = json_decode($this->attributes['productIds'], JSON_UNESCAPED_UNICODE);
        }

        return $result;
    }

    public function getNormalizeProductIds()
    {
        $result = $this->getProductIds();
        $result = ConstructorHelper::normalizeConfig($result);

        return is_array($result) ? $result : [$result];
    }
}