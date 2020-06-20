<?php

namespace kosuha606\VirtualShop\Cart;


use kosuha606\VirtualShop\Model\Cart;
use kosuha606\VirtualShop\Model\DeliveryVm;
use kosuha606\VirtualShop\Model\PaymentVm;
use kosuha606\VirtualShop\Model\PromocodeVm;
use kosuha606\VirtualShop\Services\CartService;
use kosuha606\VirtualShop\Services\ProductService;

/**
 * Построитель корзины, основная идея в том, чтобы
 * используя простые типы данных создавать корзину
 * с помощью простого интерфейса
 *
 * @package kosuha606\Model\iteration2\cart
 */
class CartBuilder
{
    /** @var Cart */
    private $cart;

    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(
        CartService $cartService,
        ProductService $productService
    ) {
        $this->cart = new Cart($cartService);
        $this->cartService = $cartService;
        $this->productService = $productService;
    }

    public function serialize(): array
    {
        $result = [];

        foreach ($this->cart->items as $item) {
            $result['products'][$item->productId] = $item->qty;
        }

        if ($this->cart->payment) {
            $result['payment'] = $this->cart->payment->id;
        }

        if ($this->cart->delivery) {
            $result['delivery'] = $this->cart->delivery->id;
        }

        if ($this->cart->promocode) {
            $result['promocode'] = $this->cart->promocode->id;
        }

        return $result;
    }

    /**
     * @param array $cartArray
     * @return Cart
     * @throws \Exception
     */
    public function unserialize($cartArray = []): Cart
    {
        if (isset($cartArray['products'])) {
            foreach ($cartArray['products'] as $productId => $qty) {
                $this->addProductById($productId, $qty);
            }
        }

        if (isset($cartArray['promocode'])) {
            $this->setPromocodeById($cartArray['promocode']);
        }

        if (isset($cartArray['payment'])) {
            $this->setPaymentById($cartArray['payment']);
        }

        if (isset($cartArray['delivery'])) {
            $this->setDeliveryById($cartArray['delivery']);
        }

        return $this->cart;
    }

    /**
     * @param $productId
     * @param $qty
     * @throws \Exception
     */
    public function addProductById($productId, $qty)
    {
        $product = $this->productService->findProductById($productId);
        $product->actions = $this->productService->findAllActions();
        $this->cart->addProduct($product, $qty);
    }

    /**
     * @param $productId
     * @throws \Exception
     */
    public function deleteProductById($productId)
    {
        $product = $this->productService->findProductById($productId);
        $this->cart->deleteProduct($product);
    }

    /**
     * @param $promocodeId
     */
    public function setPromocodeById($promocodeId)
    {
        $promocode = $this->cartService->getPromocodeById($promocodeId);
        $this->cart->applyPromocode($promocode);
    }

    public function clear()
    {
        $this->cart->items = [];
        $this->cart->promocode = null;
        $this->cart->payment = null;
        $this->cart->delivery = null;
    }

    /**
     * @param $code
     * @throws \Exception
     */
    public function setPromocodeByCode($code)
    {
        $promocode = PromocodeVm::one([
            'where' => [
                ['=', 'code', $code]
            ]
        ]);

        if (!$promocode) {
            throw new \Exception("Нет промокода $code");
        }

        $this->cart->applyPromocode($promocode);
    }

    public function setPaymentById($paymentId)
    {
        /** @var PaymentVm $payment */
        $payment = $this->cartService->getPaymentById($paymentId);
        $this->cart->setPayment($payment);
    }

    public function setDeliveryById($deliveryId)
    {
        /** @var DeliveryVm $delivery */
        $delivery = $this->cartService->getDeliveryById($deliveryId);
        $this->cart->setDelivery($delivery);
    }

    public function getCart()
    {
        return $this->cart;
    }
}