<?php

namespace kosuha606\VirtualShop\Dto;

use kosuha606\VirtualShop\Model\ProductVm;
use kosuha606\VirtualAdmin\Classes\Pagination;

class LoadProductsDTO
{
    /** @var ProductVm[] */
    public $products;

    /** @var Pagination */
    public $pagination;

    /**
     * @param $products
     * @param $pagination
     */
    public function __construct(
        $products,
        $pagination
    ) {
        $this->products = $products;
        $this->pagination = $pagination;
    }
}
