<?php

namespace kosuha606\VirtualShop\Model;

class CartItem
{
    /** @var int */
    public $price;

    /** @var int */
    public $productId;

    /** @var string */
    public $name;

    /** @var int */
    public $qty;

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $attr => $value) {
            $this->$attr = $value;
        }
    }

    /**
     * @return float|int
     */
    public function getTotal()
    {
        return $this->price*$this->qty;
    }
}
