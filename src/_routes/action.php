<?php

use kosuha606\VirtualAdmin\Structures\DetailComponents;
use kosuha606\VirtualAdmin\Structures\ListComponents;
use kosuha606\VirtualShop\Model\ActionVm;
use kosuha606\VirtualShop\Model\OrderVm;
use kosuha606\VirtualShop\Model\ProductVm;
use kosuha606\VirtualAdmin\Domains\User\UserVm;
use kosuha606\VirtualShop\Services\StringService;
use kosuha606\VirtualModel\VirtualModelEntity;
use kosuha606\VirtualModelHelppack\ServiceManager;

$baseEntity = 'action';
$stringService = ServiceManager::getInstance()->get(\kosuha606\VirtualAdmin\Services\StringService::class);
$baseEntityCamel = $stringService->transliterate($baseEntity);
$entityClass = ActionVm::class;
$listTitle = 'Акции';
$detailTitle = 'Акция';

return [
    'routes' => [
        $baseEntity => [
            'list' => [
                'menu' => [
                    'name' => $baseEntity.'_list',
                    'label' => $listTitle,
                    'url' => '/admin/'.$baseEntity.'/list',
                    'parent' => 'store',
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
                        [
                            'field' => 'percent',
                            'component' => DetailComponents::INPUT_FIELD,
                            'label' => 'Процент',
                        ],
                    ],
                    'list_config' => [
                        [
                            'field' => 'id',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'ID'
                        ],
                        [
                            'field' => 'percent',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'Процент скидки',
                            'props' => [
                                'link' => 1,
                            ]
                        ],
                        [
                            'field' => 'created_at',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'Создан'
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
                    'config' => function ($model) {
                        $stringService = ServiceManager::getInstance()->get(StringService::class);

                        return [
                            [
                                'field' => 'productIds',
                                'component' => DetailComponents::CONFIG_BUILDER_FIELD,
                                'label' => 'Продукты',
                                'value' => $model->productIds,
                                'props' => [
                                    'inputTypes' => [
                                        [
                                            'type' => DetailComponents::SELECT_FIELD,
                                            'label' => 'Продукт',
                                            'props' => [
                                                'items' => $stringService->map(VirtualModelEntity::allToArray(ProductVm::many(['where' => [['all']]])), 'id', 'name')
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'field' => 'percent',
                                'component' => DetailComponents::INPUT_FIELD,
                                'label' => 'Процент скидки',
                                'value' => $model->percent,
                            ],
                        ];
                    },
                ]
            ],
        ]
    ]
];