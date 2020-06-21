<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualAdmin\Domains\Cache\CacheAimInterface;
use kosuha606\VirtualAdmin\Domains\Cache\CacheAimObserver;
use kosuha606\VirtualAdmin\Domains\Cache\CacheEntityDto;
use kosuha606\VirtualAdmin\Domains\Comment\CommentVm;
use kosuha606\VirtualAdmin\Domains\Multilang\MultilangTrait;
use kosuha606\VirtualAdmin\Domains\Search\SearchableInterface;
use kosuha606\VirtualAdmin\Domains\Search\SearchIndexDto;
use kosuha606\VirtualAdmin\Domains\Search\SearchObserver;
use kosuha606\VirtualAdmin\Domains\Seo\SeoModelInterface;
use kosuha606\VirtualAdmin\Domains\Seo\SeoModelTrait;
use kosuha606\VirtualAdmin\Domains\Seo\SeoUrlObserver;
use kosuha606\VirtualModel\VirtualModel;
use kosuha606\VirtualShop\ServiceManager;
use kosuha606\VirtualShop\Services\ProductService;
use kosuha606\VirtualModelHelppack\Traits\ObserveVMTrait;

/**
 * Продукт
 * @property $rests
 *
 * @property $id
 * @property $name
 * @property $price
 * @property $slug
 * @property $price2B
 * @property $actions
 * @property $photo
 * @property $category_id
 *
 */
class ProductVm extends VirtualModel
    implements
    CacheAimInterface,
    SearchableInterface,
    SeoModelInterface
{
    use ObserveVMTrait;

    use MultilangTrait;

    use SeoModelTrait;

    /** @var ProductService */
    private $productService;

    public $hasDiscount = false;

    /**
     * Виртуальный атритут за который действительно продается товар
     * @var int
     */
    private $sale_price;

    public function attributes(): array
    {
        return [
            'id',
            'name',
            'price',
            'slug',
            'price2B',
            'actions',
            'rests',
            'photo',
            'category_id',
        ];
    }

    public function buildUrl()
    {
        $category = $this->getCategory();
        $result = '/'.$this->id.'-'.$this->slug;

        if ($category->id) {
            $result = $category->getUrl().$result;
        }

        return $result;
    }

    public function buildIndex(): SearchIndexDto
    {
        return new SearchIndexDto(1, [
            [
                'field' => 'title',
                'value' => $this->name,
                'type' => 'text',
            ],
            [
                'field' => 'url',
                'value' => $this->getUrl(),
                'type' => 'keyword',
            ],
            [
                'field' => 'content',
                'value' => $this->price,
                'type' => 'text',
            ],
        ]);
    }

    public static function observers()
    {
        return [
            CacheAimObserver::class,
            SearchObserver::class,
            SeoUrlObserver::class,
        ];
    }

    public function cacheItems(): array
    {
        $rests = VirtualModel::allToArray(ProductRestsVm::many(['where' => [
            ['=', 'productId', $this->id]
        ]]));
        $cacheData = $this->toArray();
        $restsArr = array_column($rests, 'qty');
        $cacheData['rests'] = array_sum($restsArr);
        $comments = CommentVm::many(['where' => [
            ['=', 'model_id', $this->id],
            ['=', 'model_class', ProductVm::class]
        ]]);
        $cacheData['comments_qty'] = count($comments);

        return [
            new CacheEntityDto($this->id,  'id', 'product', $cacheData),
        ];
    }

    public function __construct($environment = 'db')
    {
        $this->productService = ServiceManager::getInstance()->productService;
        parent::__construct($environment);
    }

    public function setAttribute($name, $value)
    {
        $result = parent::setAttribute($name, $value);

        if ($name === 'actions') {
            if ($this->price != $this->getSalePrice()) {
                $this->hasDiscount = true;
            }
        }

        return $result;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getRests()
    {
        if (!$this->attributes['rests']) {
            $rests = ProductRestsVm::many([
                'where' => [
                    ['=', 'productId', $this->id],
                ],
            ]);
            $this->setAttribute('rests', $rests);
        } elseif (isset($this->attributes['rests'][0])
            && is_array($this->attributes['rests'][0])
        ) {
            $rests = ProductRestsVm::createMany($this->attributes['rests']);
            $this->setAttribute('rests', $rests);
        }

        return $this->attributes['rests'];
    }

    public function getPhotoSafe()
    {
        return $this->attributes['photo'] ? '/'.$this->attributes['photo'] : 'https://via.placeholder.com/300x300';
    }

    /**
     * Проверяет имеются ли свободные остатки по
     * продукту
     * @param $qty
     * @return bool
     * @NOTICE Переделал, теперь происходит делегирование логики к дружественному классу-сервису
     * @throws \Exception
     */
    public function hasFreeRests($qty)
    {
        return $this->productService->hasFreeRests($this, $qty);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function maxAvailableRestAmount()
    {
        return $this->productService->maxAvailableRestAmount($this);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isInFavorite()
    {
        return $this->productService->isInFavorite($this);
    }

    /**
     *
     */
    public function maxRestAmount()
    {
        $rests = $this->rests;
        $amount = 0;
        /** @var ProductRestsVm $rest */
        foreach ($rests as $rest) {
            $amount += $rest->qty;
        }

        return $amount;
    }

    /**
     * Получить цену за которую нужно продать товар
     * @return float|int
     * @throws \Exception
     */
    public function getSalePrice()
    {
        if (!$this->actions) {
            $this->actions = ActionVm::many(['where' => [['all']]]);
        }

        return $this->productService->calculateProductSalePrice($this);
    }

    public function getCategory()
    {
        return CategoryVm::one([
            'where' => [
                ['=', 'id', $this->category_id]
            ]
        ]);
    }
}