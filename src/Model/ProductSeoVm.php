<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

/**
 * @property $id
 * @property $product_id
 * @property $meta_title
 * @property $meta_keywords
 * @property $meta_description
 */
class ProductSeoVm extends VirtualModelEntity
{
    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'id',
            'product_id',
            'meta_title',
            'meta_keywords',
            'meta_description',
        ];
    }
}
