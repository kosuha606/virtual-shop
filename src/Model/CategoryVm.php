<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualAdmin\Domains\Multilang\MultilangTrait;
use kosuha606\VirtualAdmin\Domains\Seo\SeoModelInterface;
use kosuha606\VirtualAdmin\Domains\Seo\SeoModelTrait;
use kosuha606\VirtualAdmin\Domains\Seo\SeoUrlObserver;
use kosuha606\VirtualModel\VirtualModelEntity;
use kosuha606\VirtualModelHelppack\Traits\ObserveVMTrait;

class CategoryVm extends VirtualModelEntity implements SeoModelInterface
{
    use SeoModelTrait;

    use ObserveVMTrait;

    use MultilangTrait;

    /**
     * @return array
     */
    public static function observers()
    {
        return [
            SeoUrlObserver::class,
        ];
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'id',
            'name',
            'photo',
            'slug',
        ];
    }

    /**
     * @return string
     */
    public function buildUrl()
    {
        return '/'.$this->id.'-'.$this->slug;
    }

    /**
     * @return string
     */
    public function getPhotoSafe()
    {
        return $this->attributes['photo'] ?: 'https://via.placeholder.com/300x300';
    }

    /**
     * @return int
     */
    public function getProductsCount()
    {
        return ProductVm::count([
            'where' => [
                ['=', 'category_id', $this->id]
            ]
        ]);
    }
}
