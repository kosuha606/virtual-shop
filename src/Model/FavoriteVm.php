<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * @property $id
 * @property $user_id
 * @property $product_id
 * @property $product
 * @property $user
 */
class FavoriteVm extends VirtualModelEntity
{
    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'id',
            'user_id',
            'product_id',
            'product',
            'user',
        ];
    }
}
