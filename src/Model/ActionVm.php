<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualAdmin\Helpers\ConstructorHelper;
use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * @property $id
 * @property $normalizeProductIds
 * @property $productIds
 * @property $percent
 * @property $userType
 */
class ActionVm extends VirtualModelEntity
{
    /**
     * @return array
     */
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

    /**
     * @return mixed
     */
    public function getProductIds()
    {
        if (is_array($this->attributes['productIds'])) {
            $result = $this->attributes['productIds'];
        } else {
            $result = json_decode($this->attributes['productIds'], JSON_UNESCAPED_UNICODE);
        }

        return $result;
    }

    /**
     * @return array|mixed
     */
    public function getNormalizeProductIds()
    {
        $result = $this->getProductIds();
        $result = ConstructorHelper::normalizeConfig($result);

        return is_array($result) ? $result : [$result];
    }
}
