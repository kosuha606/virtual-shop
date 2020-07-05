<?php

namespace kosuha606\VirtualShop\Services;

use kosuha606\VirtualShop\Model\Cart;

/**
 * @package kosuha606\VirtualShop\Services
 */
class CartService
{
    /** @var OrderService */
    private $orderService;

    /** @var PaymentService */
    private $paymentService;

    /** @var DeliveryService */
    private $deliveryService;

    private $promocodeService;

    public function __construct(
        OrderService $orderService,
        PaymentService $paymentService,
        DeliveryService $deliveryService,
        PromocodeService $promocodeService
    ) {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
        $this->deliveryService = $deliveryService;
        $this->promocodeService = $promocodeService;
    }

    /**
     * @param Cart $cart
     * @return float|int|mixed
     * @throws \Exception
     */
    public function calculateTotals(Cart $cart)
    {
        $price = 0;

        foreach ($cart->items as $item) {
            $price += $item->getTotal();
        }

        $user = $this->orderService->currentUser();

        if ($cart->payment) {
            $price += $cart->payment->comission;
        }

        if ($cart->delivery) {
            $price += $cart->delivery->price;
        }

        if ($user && $user->personalDiscount) {
            $price -= $price*($user->personalDiscount/100);
        }

        if ($cart->promocode) {
            if ($price < $cart->promocode->amount) {
                throw new \Exception('Сумма промокода больше суммы корзины');
            }

            $price -= $cart->promocode->amount;
        }

        return $price;
    }

    /**
     * @param $promocodeId
     * @return \kosuha606\VirtualShop\Model\PromocodeVm
     * @throws \Exception
     */
    public function getPromocodeById($promocodeId)
    {
        return $this->promocodeService->findPromocodeById($promocodeId);
    }

    /**
     * @param $paymentId
     * @return \kosuha606\VirtualShop\Model\PaymentVm
     * @throws \Exception
     */
    public function getPaymentById($paymentId)
    {
        return $this->paymentService->findPaymentById($paymentId);
    }

    /**
     * @param $deliveryId
     * @return \kosuha606\VirtualModel\VirtualModelEntity
     * @throws \Exception
     */
    public function getDeliveryById($deliveryId)
    {
        return $this->deliveryService->findDeliveryById($deliveryId);
    }
}