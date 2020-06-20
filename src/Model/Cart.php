<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualShop\ServiceManager;
use kosuha606\VirtualShop\Services\CartService;

/**
 * Корзина
 * @NOTICE Корзина должна каждый раз быть построена при каждой загрузке приложения
 * @NOTICE Нужен враппер который будет заниматься построением корзины
 * @package kosuha606\Model\iteration2\model
 */
class Cart
{
    /**
     * @var CartItem[]
     */
    public $items = [];

    /**
     * @var PromocodeVm
     */
    public $promocode;

    /**
     * @var DeliveryVm
     */
    public $delivery;

    /**
     * @var PaymentVm
     */
    public $payment;

    /**
     * @var CartService
     */
    public $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @return float|int
     * @throws \Exception
     */
    public function complete()
    {
        return $this->cartService->calculateTotals($this);
    }

    public function applyPromocode(PromocodeVm $promocode)
    {
        $this->promocode = $promocode;
    }

    /**
     * @return float|int
     * @throws \Exception
     */
    public function getTotals()
    {
        return $this->cartService->calculateTotals($this);
    }

    public function getProductsTotal()
    {
        $price = 0;

        foreach ($this->items as $item) {
            $price += $item->getTotal();
        }

        return $price;
    }

    /**
     * @param ProductVm $product
     * @param int $qty
     * @throws \Exception
     */
    public function addProduct(ProductVm $product, $qty = 1)
    {
        if ($qty <= 0) {
            // Не разрешаем никому ставить кол-во меньше 0
            $qty = 1;
        }

        if ($product->hasFreeRests($qty)) {
            $this->items[] = new CartItem([
                'price' => $product->sale_price,
                'productId' => $product->id,
                'name' => $product->name,
                'qty' => $qty
            ]);
        } else {
            throw new \Exception('Нет доступных остатков по продукту');
        }
    }

    public function deleteProduct(ProductVm $product)
    {
        $newItems = [];
        $productFound = false;

        foreach ($this->items as $item) {
            if ($item->productId != $product->id) {
                $newItems[] = $item;
            } else {
                $productFound = true;
            }
        }

        if (!$productFound) {
            throw new \Exception("Продукт {$product->id} не найден в корзине");
        }

        $this->items = $newItems;
    }

    /**
     * @param DeliveryVm $delivery
     */
    public function setDelivery(DeliveryVm $delivery): void
    {
        $this->delivery = $delivery;
    }

    /**
     * @param PaymentVm $payment
     */
    public function setPayment(PaymentVm $payment): void
    {
        $this->payment = $payment;
    }

    public function getCountProducts()
    {
        return count($this->items);
    }

    public function getAmount()
    {
        $amount = 0;

        foreach ($this->items as $item) {
            $amount += $item->qty;
        }

        return $amount;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function hasItems()
    {
        return count($this->items) > 0;
    }
}