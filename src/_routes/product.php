<?php

use kosuha606\VirtualAdmin\Domains\User\UserVm;
use kosuha606\VirtualAdmin\Domains\Comment\CommentVm;
use kosuha606\VirtualAdmin\Form\SecondaryFormBuilder;
use kosuha606\VirtualAdmin\Form\SecondaryFormService;
use kosuha606\VirtualAdmin\Structures\DetailComponents;
use kosuha606\VirtualAdmin\Structures\ListComponents;
use kosuha606\VirtualAdmin\Structures\SecondaryForms;
use kosuha606\VirtualShop\Model\CategoryVm;
use kosuha606\VirtualShop\Model\FilterCategoryVm;
use kosuha606\VirtualShop\Model\FilterProductVm;
use kosuha606\VirtualShop\Model\OrderReserveVm;
use kosuha606\VirtualShop\Model\OrderVm;
use kosuha606\VirtualShop\Model\ProductRestsVm;
use kosuha606\VirtualShop\Model\ProductSeoVm;
use kosuha606\VirtualShop\Model\ProductVm;
use kosuha606\VirtualShop\Services\StringService;
use kosuha606\VirtualModel\VirtualModel;
use kosuha606\VirtualModelHelppack\ServiceManager;

$baseEntity = 'product';
$stringService = ServiceManager::getInstance()->get(\kosuha606\VirtualAdmin\Services\StringService::class);
$baseEntityCamel = $stringService->transliterate($baseEntity);
$entityClass = ProductVm::class;
$listTitle = 'Продукты';
$detailTitle = 'Продукт';

return [
    'routes' => [
        $baseEntity => [
            'list' => [
                'menu' => [
                    'name' => $baseEntity.'_list',
                    'label' => $listTitle,
                    'url' => '/admin/'.$baseEntity.'/list',
                    'parent' => 'store',
                    'sort' => -2,
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
                            case 'name':
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
                            'field' => 'name',
                            'component' => DetailComponents::INPUT_FIELD,
                            'label' => 'Название',
                        ],
                    ],
                    'list_config' => [
                        [
                            'field' => 'id',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'ID'
                        ],
                        [
                            'field' => 'name',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'Название',
                            'props' => [
                                'link' => 1,
                            ]
                        ],
                        [
                            'field' => 'category',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'Категория',
                            'value' => function($model) {
                                $product = ProductVm::one(['where' => [['=', 'id', $model['id']]]]);
                                /** @var CategoryVm $category */
                                $category = $product->getCategory();

                                return $category->name ?? '--';
                            }
                        ],
                        [
                            'field' => 'price',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'Цена'
                        ],
                        [
                            'field' => 'created_at',
                            'component' => ListComponents::STRING_CELL,
                            'label' => 'Создан',
                            'value' => function($model) {
                                return $model['created_at'] ? date('d.m.Y H:i', strtotime($model['created_at'])) : '--';
                            }
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
                        return ($model->name ?: $detailTitle );
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
                            ->setMasterModelField('productId')
                            ->setRelationType(SecondaryFormBuilder::ONE_TO_MANY)
                            ->setRelationClass(ProductRestsVm::class)
                            ->setTabName('Остатки')
                            ->setRelationEntities(ProductRestsVm::many(['where' => [['=', 'productId', $model->id]]]))
                            ->setConfig(function ($inModel) use ($model) {
                                return [
                                    [
                                        'field' => 'productId',
                                        'label' => 'Продукт',
                                        'component' => DetailComponents::HIDDEN_FIELD,
                                        'value' => $model->id,
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


                        $configProduct = $secondaryService
                            ->buildForm()
                            ->setMasterModel($model)
                            ->setMasterModelField('product_id')
                            ->setRelationType(SecondaryFormBuilder::ONE_TO_MANY)
                            ->setRelationClass(FilterProductVm::class)
                            ->setTabName('Фильтры')
                            ->setRelationEntities(FilterProductVm::many(['where' => [['=', 'product_id', $model->id]]]))
                            ->setConfig(function ($inModel) use ($model) {
                                $stringService = ServiceManager::getInstance()->get(StringService::class);

                                return [
                                    [
                                        'field' => 'product_id',
                                        'label' => 'Продукт',
                                        'component' => DetailComponents::HIDDEN_FIELD,
                                        'value' => $model->id,
                                    ],
                                    [
                                        'field' => 'category_id',
                                        'label' => 'Фильтр',
                                        'component' => DetailComponents::SELECT_FIELD,
                                        'value' => $inModel->category_id,
                                        'props' => [
                                            'items' => $stringService->map(VirtualModel::allToArray(FilterCategoryVm::many(['where' => [['all']]])), 'id', 'name')
                                        ]
                                    ],
                                    [
                                        'field' => 'value',
                                        'label' => 'Значение',
                                        'component' => DetailComponents::INPUT_FIELD,
                                        'value' => $inModel->value,
                                    ],
                                ];
                            })
                            ->getConfig()
                        ;

                        $configComment = $secondaryService
                            ->buildForm()
                            ->setMasterModel($model)
                            ->setMasterModelId($model->id.','.get_class($model))
                            ->setMasterModelField('model_id,model_class')
                            ->setRelationType(SecondaryFormBuilder::ONE_TO_MANY)
                            ->setRelationClass(CommentVm::class)
                            ->setTabName('Комментарии')
                            ->setRelationEntities(CommentVm::many(['where' => [
                                ['=', 'model_id', $model->id],
                                ['=', 'model_class', ProductVm::class],
                            ]]))
                            ->setConfig(function ($inModel) use ($model) {
                                $stringService = ServiceManager::getInstance()->get(StringService::class);

                                return [
                                    [
                                        'field' => 'model_id',
                                        'label' => 'Продукт',
                                        'component' => DetailComponents::HIDDEN_FIELD,
                                        'value' => $model->id,
                                    ],
                                    [
                                        'field' => 'model_class',
                                        'label' => 'Продукт',
                                        'component' => DetailComponents::HIDDEN_FIELD,
                                        'value' => get_class($model),
                                    ],
                                    [
                                        'field' => 'user_id',
                                        'label' => 'Пользователь',
                                        'component' => DetailComponents::SELECT_FIELD,
                                        'value' => $inModel->user_id,
                                        'props' => [
                                            'items' => $stringService->map(VirtualModel::allToArray(UserVm::many(['where' => [['all']]])), 'id', 'email')
                                        ]
                                    ],
                                    [
                                        'field' => 'content',
                                        'label' => 'Сообщение',
                                        'component' => DetailComponents::TEXTAREA_FIELD,
                                        'value' => $inModel->content,
                                    ],
                                ];
                            })
                            ->getConfig()
                        ;

                        return [
                            $config,
                            SecondaryForms::SEO_PAGE($model),
                            $configProduct,
                            $configComment,
                        ];
                    },
                    'config' => function ($model) {
                        $stringService = ServiceManager::getInstance()->get(StringService::class);

                        return [
                            DetailComponents::MULTILANG_FIELD(
                                DetailComponents::INPUT_FIELD,
                                'name',
                                'Название',
                                $model->name,
                                $model
                            ),
                            [
                                'field' => 'slug',
                                'component' => DetailComponents::INPUT_FIELD,
                                'label' => 'Ссылка',
                                'value' => $model->slug,
                            ],
                            [
                                'field' => 'category_id',
                                'label' => 'Категория',
                                'component' => DetailComponents::SELECT_FIELD,
                                'value' => $model->category_id,
                                'props' => [
                                    'items' => $stringService->map(VirtualModel::allToArray(CategoryVm::many(['where' => [['all']]])), 'id', 'name')
                                ]
                            ],
                            [
                                'field' => 'photo',
                                'component' => DetailComponents::IMAGE_FIELD,
                                'label' => 'Фотография',
                                'value' => $model->photo,
                            ],
                            [
                                'field' => 'price',
                                'component' => DetailComponents::INPUT_FIELD,
                                'label' => 'Цена',
                                'value' => $model->price,
                            ],
                            [
                                'field' => 'price2B',
                                'component' => DetailComponents::INPUT_FIELD,
                                'label' => 'Цена оптом',
                                'value' => $model->price2B,
                            ],
                        ];
                    },
                ]
            ],
        ]
    ]
];