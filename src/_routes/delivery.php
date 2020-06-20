<?php

use kosuha606\VirtualAdmin\Structures\DetailComponents;
use kosuha606\VirtualAdmin\Structures\ListComponents;
use kosuha606\VirtualShop\Model\ActionVm;
use kosuha606\VirtualShop\Model\DeliveryVm;
use kosuha606\VirtualShop\Services\StringService;
use kosuha606\VirtualModel\VirtualModel;
use kosuha606\VirtualModelHelppack\ServiceManager;

$baseEntity = 'delivery';
$stringService = ServiceManager::getInstance()->get(\kosuha606\VirtualAdmin\Services\StringService::class);
$baseEntityCamel = $stringService->transliterate($baseEntity);
$entityClass = DeliveryVm::class;
$listTitle = 'Доставка';
$detailTitle = 'Доставка';

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
                    'filter' => function($filterKey) {
                        $function = '=';
                        switch ($filterKey) {
                            case 'description':
                                $function = 'like';
                                break;
                        }

                        return $function;
                    },
                    'filter_config' => [
                        [
                            'field' => 'id',
                            'component' => DetailComponents::INPUT_FIELD,
                            'label' => 'ID',
                        ],
                        [
                            'field' => 'description',
                            'component' => DetailComponents::INPUT_FIELD,
                            'label' => 'Описание',
                        ],
                    ],
                    'list_config' => [
                        [
                            'field' => 'id',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'ID'
                        ],
                        [
                            'field' => 'description',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'Описание',
                            'props' => [
                                'link' => 1,
                            ]
                        ],
                        [
                            'field' => 'price',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'Стоимость'
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
                        return ($detailTitle.' '.$model->description ?: $detailTitle );
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
                                'field' => 'price',
                                'component' => DetailComponents::INPUT_FIELD,
                                'label' => 'Стоимость',
                                'value' => $model->price,
                            ],
                            [
                                'field' => 'description',
                                'component' => DetailComponents::INPUT_FIELD,
                                'label' => 'Описание',
                                'value' => $model->description,
                            ],
                        ];
                    },
                ]
            ],
        ]
    ]
];