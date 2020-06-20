<?php

use kosuha606\VirtualShop\Model\FilterProductVm;
use kosuha606\VirtualShop\Model\ProductVm;
use app\virtualModels\Model\UserVm;
use kosuha606\VirtualShop\ServiceManager;
use kosuha606\VirtualModel\Example\MemoryModelProvider;
use kosuha606\VirtualModel\VirtualModelManager;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{

    /** @var MemoryModelProvider  */
    private $provider;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->provider = new MemoryModelProvider();

        $this->provider->memoryStorage = [
            FilterProductVm::class => [
                [
                    'category_id' => 1,
                    'product_id' => 1,
                    'value' => 'БМВ',
                ],
                [
                    'category_id' => 2,
                    'product_id' => 1,
                    'value' => 'Красный',
                ],
                [
                    'category_id' => 1,
                    'product_id' => 2,
                    'value' => 'Порше',
                ],
                [
                    'category_id' => 2,
                    'product_id' => 2,
                    'value' => 'синий',
                ],
                [
                    'category_id' => 1,
                    'product_id' => 4,
                    'value' => 'Порше',
                ],
                [
                    'category_id' => 2,
                    'product_id' => 4,
                    'value' => 'Красный',
                ],
                [
                    'category_id' => 1,
                    'product_id' => 3,
                    'value' => 'Шевроле',
                ],
                [
                    'category_id' => 2,
                    'product_id' => 3,
                    'value' => 'Желтый',
                ],
            ]
        ];

        $user = UserVm::create(
            [
                'id' => 1,
                'personalDiscount' => 0,
            ]
        );
        ServiceManager::getInstance()->userService->setUser($user);

        VirtualModelManager::getInstance()->setProvider($this->provider);
    }

    public function tearDown(): void
    {
        unset($this->provider);
    }

    public function dataProvider()
    {
        return [
            [['Порше_1'], '{"id":[2,4]}'],
            [['Шевроле_1'], '{"id":[3]}'],
            [['Красный_2'], '{"id":[1,4]}'],
            [['Красный_2', 'БМВ_1'], '{"id":[1]}'],
        ];
    }

    /**
     * @throws Exception
     * @dataProvider dataProvider
     */
    public function testFilter($filterGet, $expectedString)
    {
        $productIds = ServiceManager::getInstance()->productService->processGetFilters($filterGet);
        $this->assertEquals($expectedString, json_encode($productIds));
    }
}