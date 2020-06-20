<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModel;

class ProductSeoVm extends VirtualModel
{
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