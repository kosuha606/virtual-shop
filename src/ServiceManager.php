<?php

namespace kosuha606\VirtualShop;

use kosuha606\VirtualAdmin\Domains\User\UserService;
use kosuha606\VirtualShop\Cart\CartBuilder;
use kosuha606\VirtualShop\Services\CartService;
use kosuha606\VirtualShop\Services\DeliveryService;
use kosuha606\VirtualShop\Services\FavoriteService;
use kosuha606\VirtualShop\Services\OrderService;
use kosuha606\VirtualShop\Services\PaymentService;
use kosuha606\VirtualShop\Services\ProductService;
use kosuha606\VirtualShop\Services\PromocodeService;

/**
 * @property UserService $userService
 * @property ProductService $productService
 * @property CartService $cartService
 * @property PaymentService $paymentService
 * @property DeliveryService $deliveryService
 * @property PromocodeService $promocodeService
 * @property CartBuilder $cartBuilder
 * @property OrderService $orderService
 * @property FavoriteService $favoriteService
 * @deprecated Use container instead
 */
class ServiceManager
{
    private static $instance;

    private $services = [];

    private $type;

    /**
     * ServiceManager constructor.
     * @param $type
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function __construct($type)
    {
        $this->services = [
            'userService' => \kosuha606\VirtualModelHelppack\ServiceManager::getInstance()->get(UserService::class),
        ];

        $this->services['paymentService'] = new PaymentService();
        $this->services['deliveryService'] = new DeliveryService();
        $this->services['promocodeService'] = new PromocodeService();
        $this->services['favoriteService'] = new FavoriteService();
        $this->services['orderService'] = new OrderService($this->services['userService']);
        $this->services['productService'] = new ProductService(
            $this->services['orderService'],
            $this->services['favoriteService']
        );
        $this->services['cartService'] = new CartService(
            $this->services['orderService'],
            $this->services['paymentService'],
            $this->services['deliveryService'],
            $this->services['promocodeService']
        );
        $this->services['cartBuilder'] = new CartBuilder(
            $this->services['cartService'],
            $this->services['productService']
        );

        $this->type = $type;
    }

    public function __get($name)
    {
        return $this->services[$name];
    }

    public static function getInstance($type = 'bad')
    {
        if (!self::$instance) {
            self::$instance = new self($type);
        }
        self::$instance->type = $type;

        return self::$instance;
    }
}