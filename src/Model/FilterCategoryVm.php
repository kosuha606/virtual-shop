<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualModel\VirtualModelEntity;

class FilterCategoryVm extends VirtualModelEntity
{
    public function attributes(): array
    {
        return [
            'id',
            'name',
        ];
    }
}