<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModel;

class FilterCategoryVm extends VirtualModel
{
    public function attributes(): array
    {
        return [
            'id',
            'name',
        ];
    }
}