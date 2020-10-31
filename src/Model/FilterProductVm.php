<?php

namespace kosuha606\VirtualShop\Model;

use kosuha606\VirtualAdmin\Services\RequestService;
use kosuha606\VirtualModel\VirtualModelEntity;
use kosuha606\VirtualModelHelppack\ServiceManager;

class FilterProductVm extends VirtualModelEntity
{
    /** @var array  */
    public static $filter = [];

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'id',
            'category_id',
            'product_id',
            'value',
        ];
    }

    /**
     * @return bool
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function isActive()
    {
        if (!self::$filter) {
            $requestService = ServiceManager::getInstance()->get(RequestService::class);
            if (isset($requestService->request()->get['filter'])) {
                self::$filter = $requestService->request()->get['filter'];
            } else {
                self::$filter = ['none'];
            }
        }

        return in_array($this->getKey(), self::$filter);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->value.'_'.$this->category_id;
    }
}
