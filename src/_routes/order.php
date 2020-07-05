<?php

use kosuha606\VirtualAdmin\Form\SecondaryFormBuilder;
use kosuha606\VirtualAdmin\Form\SecondaryFormService;
use kosuha606\VirtualAdmin\Structures\DetailComponents;
use kosuha606\VirtualAdmin\Structures\ListComponents;
use kosuha606\VirtualShop\Model\OrderReserveVm;
use kosuha606\VirtualShop\Model\OrderVm;
use kosuha606\VirtualShop\Model\ProductRestsVm;
use kosuha606\VirtualShop\Model\ProductVm;
use kosuha606\VirtualShop\Services\StringService;
use kosuha606\VirtualModel\VirtualModelEntity;
use kosuha606\VirtualModelHelppack\ServiceManager;

$baseEntity = 'order';
$stringService = ServiceManager::getInstance()->get(\kosuha606\VirtualAdmin\Services\StringService::class);
$baseEntityCamel = $stringService->transliterate($baseEntity);
$entityClass = OrderVm::class;
$listTitle = 'Заказы';
$detailTitle = 'Заказ';

return [
    'routes' => [
        $baseEntity => [
            'list' => [
                'menu' => [
                    'name' => $baseEntity.'_list',
                    'label' => $listTitle,
                    'url' => '/admin/'.$baseEntity.'/list',
                    'parent' => 'store',
                    'sort' => -1,
                ],
                'handler' => [
                    'type' => 'vue',
                    'h1' => $listTitle,
                    'entity' => $baseEntity,
                    'component' => 'list',
                    'ad_url' => '/admin/'.$baseEntity.'/detail',
                    'crud' => [
                        'model' => $entityClass,
                        'action' => 'actionList',
                        'orderBy' => [
                            'field' => 'id',
                            'direction' => 'desc',
                        ],
                    ],
                    'filter_config' => [
                        [
                            'field' => 'id',
                            'component' => DetailComponents::INPUT_FIELD,
                            'label' => 'ID',
                        ],
                    ],
                    'list_config' => [
                        [
                            'field' => 'id',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'ID'
                        ],
                        [
                            'field' => 'created_at',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'Создан',
                            'props' => [
                                'link' => 1,
                            ]
                        ],
                    ]
                ]
            ],
            'detail' => [
                'menu' => [
                    'name' => 'ad_category_detail',
                    'label' => 'Категория',
                    'url' => '/admin/ad_category/detail',
                    'visible' => false,
                ],
                'handler' => [
                    'type' => 'vue',
                    'h1' => function($model) use($detailTitle) {
                        return ($detailTitle.' '.$model->id ?: $detailTitle );
                    },
                    'entity' => $baseEntity,
                    'component' => 'detail',
                    'crud' => [
                        'model' => $entityClass,
                        'action' => 'actionView',
                    ],
                    'additional_config' => function($model) {
                        $secondaryService = ServiceManager::getInstance()->get(SecondaryFormService::class);

                        $config = $secondaryService->buildForm()
                            ->setMasterModel($model)
                            ->setMasterModelField('orderId')
                            ->setRelationType(SecondaryFormBuilder::ONE_TO_MANY)
                            ->setRelationClass(OrderReserveVm::class)
                            ->setTabName('Резервы')
                            ->setRelationEntities(OrderReserveVm::many(['where' => [['=', 'orderId', $model->id]]]))
                            ->setConfig(function ($inModel) use ($model) {
                                $stringService = ServiceManager::getInstance()->get(StringService::class);
                                /** @var OrderReserveVm $inModel */
                                return [
                                    [
                                        'field' => 'orderId',
                                        'label' => 'Заказ',
                                        'component' => DetailComponents::HIDDEN_FIELD,
                                        'value' => $model->id,
                                    ],
                                    [
                                        'field' => 'productId',
                                        'component' => DetailComponents::SELECT_FIELD,
                                        'label' => 'Продукт',
                                        'value' => $inModel->productId,
                                        'props' => [
                                            'items' => $stringService->map(VirtualModelEntity::allToArray(ProductVm::many(['where' => [['all']]])), 'id', 'name')
                                        ]
                                    ],
                                    [
                                        'field' => 'qty',
                                        'label' => 'Кол-во',
                                        'component' => DetailComponents::INPUT_FIELD,
                                        'value' => $inModel->qty,
                                    ],
                                    [
                                        'field' => 'userType',
                                        'label' => 'Тип пользователя',
                                        'component' => DetailComponents::INPUT_FIELD,
                                        'value' => $inModel->userType,
                                    ],
                                ];
                            })
                            ->getConfig()
                        ;

                        return [
                            $config
                        ];
                    },
                    'config' => function ($model) {
                        $stringService = ServiceManager::getInstance()->get(StringService::class);

                        return [
                            [
                                'field' => 'orderData',
                                'component' => DetailComponents::TEXTAREA_FIELD,
                                'label' => 'Данные заказа',
                                'value' => $model->orderData,
                            ],
                            [
                                'field' => 'total',
                                'component' => DetailComponents::INPUT_FIELD,
                                'label' => 'Итого',
                                'value' => $model->total,
                            ],
                            [
                                'field' => 'user_id',
                                'component' => DetailComponents::INPUT_FIELD,
                                'label' => 'Пользователь',
                                'value' => $model->user_id,
                            ],
                            [
                                'field' => 'created_at',
                                'component' => DetailComponents::INPUT_FIELD,
                                'label' => 'Дата создания',
                                'value' => $model->created_at,
                            ],
                        ];
                    },
                ]
            ],
        ]
    ]
];