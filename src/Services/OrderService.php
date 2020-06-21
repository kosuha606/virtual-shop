<?php

namespace kosuha606\VirtualShop\Services;

use kosuha606\VirtualShop\Model\Cart;
use kosuha606\VirtualShop\Model\OrderVm;
use app\virtualModels\Model\UserVm;
use kosuha606\VirtualModel\VirtualModel;
use kosuha606\VirtualModel\VirtualModelManager;
use kosuha606\VirtualShop\Model\OrderReserveVm;
use kosuha606\VirtualShop\Model\ProductVm;

/**
 * @package kosuha606\VirtualShop\Services
 */
class OrderService
{
    /** @var UserService */
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Cart $cart
     * @param UserVm $user
     * @throws \Exception
     */
    public function buildOrder(Cart $cart, UserVm $user)
    {
        $data = [
            'items' => [],
            'payment' => $cart->payment->toArray(),
            'delivery' => $cart->delivery->toArray(),
        ];

        if ($cart->promocode) {
            $data['promocode'] = $cart->promocode->toArray();
        }

        foreach ($cart->items as $item) {
            $data['items'][] = [
                'product_id' => $item->productId,
                'price' => $item->price,
                'qty' => $item->qty,
                'name' => $item->name,
            ];
        }

        $order = OrderVm::create([
            'user_id' => $user->id,
            'orderData' => $data,
            'total' => $cart->getTotals(),
            'userType' => 'b2c',
        ]);
        $ids = $order->save();

        foreach ($ids as $id) {
            foreach ($cart->items as $item) {
                $orderReserve = OrderReserveVm::create([
                    'orderId' => $id,
                    'productId' => $item->productId,
                    'qty' => $item->qty,
                    'userType' => 'b2c',
                ]);
                $orderReserve->save();
            }
        }
    }

    /**
     * @param ProductVm $product
     * @return int
     * @throws \Exception
     */
    public function findOrderReserveQtyByProduct(ProductVm $product)
    {
        $reservedQty = 0;

        foreach ($this->getOrderReserve() as $item) {
            if ($item->productId === $product->id) {
                $reservedQty += $item->qty;
            }
        }

        return $reservedQty;
    }

    public function currentUser()
    {
        return $this->userService->current();
    }

    /**
     * @return OrderReserveVm[]
     * @throws \Exception
     */
    public function getOrderReserve()
    {
        $items = VirtualModelManager::getInstance()->getProvider()->many(OrderReserveVm::class, [
            'where' => [
                ['all']
            ]
        ]);

        return $items;
    }

    public function buildOrdersStatistic($range)
    {
        $ordersDynamic = ['dates' => [], 'values' => []];

        foreach ($range as $item) {
            $ordersDynamic['dates'][] = $item[0];
            $ordersDynamic['values'][] = OrderVm::count([
                'where' => [
                    ['>=', 'created_at', $item[0]],
                    ['<=', 'created_at', $item[1]],
                ]
            ]);
        }

        return $ordersDynamic;
    }
}