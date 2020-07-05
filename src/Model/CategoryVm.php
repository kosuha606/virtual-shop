<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualAdmin\Domains\Multilang\MultilangTrait;
use kosuha606\VirtualAdmin\Domains\Seo\SeoModelInterface;
use kosuha606\VirtualAdmin\Domains\Seo\SeoModelTrait;
use kosuha606\VirtualAdmin\Domains\Seo\SeoUrlObserver;
use kosuha606\VirtualModel\VirtualModelEntity;
use kosuha606\VirtualModelHelppack\Traits\ObserveVMTrait;

/**
 *
 * @property $id
 * @property $name
 * @property $photo
 * @property $slug
 *
 */
class CategoryVm extends VirtualModelEntity implements SeoModelInterface
{
    use SeoModelTrait;

    use ObserveVMTrait;

    use MultilangTrait;

    public static function observers()
    {
        return [
            SeoUrlObserver::class,
        ];
    }

    public function attributes(): array
    {
        return [
            'id',
            'name',
            'photo',
            'slug',
        ];
    }

    public function buildUrl()
    {
        return '/'.$this->id.'-'.$this->slug;
    }

    public function getPhotoSafe()
    {
        return $this->attributes['photo'] ?: 'https://via.placeholder.com/300x300';
    }

    /**
     * @return mixed
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