<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

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
