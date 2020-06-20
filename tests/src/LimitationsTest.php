<?php

use kosuha606\VirtualShop\Model\DeliveryVm;
use kosuha606\VirtualShop\Model\PaymentVm;
use kosuha606\VirtualShop\Model\ProductVm;
use kosuha606\VirtualShop\Model\PromocodeVm;
use kosuha606\VirtualShop\ServiceManager;
use kosuha606\VirtualModel\Example\MemoryModelProvider;
use kosuha606\VirtualModel\VirtualModelManager;
use PHPUnit\Framework\TestCase;

class LimitationsTest extends TestCase
{
    /** @var MemoryModelProvider  */
    private $provider;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        ServiceManager::getInstance()->cartBuilder->clear();
        $this->provider = new MemoryModelProvider();

        $this->provider->memoryStorage = [
            ProductVm::class => [
                [
                    'id' => 1,
                    'name' => 'First',
                    'price' => 100,
                    'price2B' => 200,
                    'actions' => [],
                    'rests' => [
                        [
                            'productId' => 1,
                            'qty' => 10,
                            'userType' => 'b2c',
                        ]
                    ],
                ],
                [
                    'id' => 2,
                    'name' => 'Second',
                    'price' => 200,
                    'price2B' => 400,
                    'actions' => [],
                    'rests' => [
                        [
                            'productId' => 2,
                            'qty' => 10,
                            'userType' => 'b2c',
                        ]
                    ],
                ],
            ],
            PaymentVm::class => [
                [
                    'id' => 1,
                    'comission' => 10,
                    'description' => 'Yandex',
                    'userType' => 'b2c',
                ]
            ],
            DeliveryVm::class => [
                [
                    'id' => 2,
                    'price' => 100,
                    'description' => 'До двери',
                    'userType' => 'b2c',
                ]
            ],
            PromocodeVm::class => [
                [
                    'id' => 1,
                    'amount' => 2,
                    'code' => 'test',
                    'userType' => 'b2c',
                ],
            ]
        ];

        VirtualModelManager::getInstance()->setProvider($this->provider);
    }

    public function tearDown(): void
    {
        ServiceManager::getInstance()->cartBuilder->clear();
        unset($this->provider);
    }

    /**
     * При отрицательном кол-ве корзина ставит по умолчанию 1
     * @throws Exception
     */
    public function testNegativeQty()
    {
        $cart = ServiceManager::getInstance()->cartBuilder->unserialize([
            'products' => [
                1 => -2,
                2 => -3,
            ],
            'payment' => 1,
            'delivery' => 2,
        ]);
        $totals = $cart->getTotals();

        $this->assertEquals(410, $totals);
    }

}