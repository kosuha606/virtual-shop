<?php

namespace kosuha606\VirtualShop;

use kosuha606\VirtualAdmin\Interfaces\AdminRoutesLoaderInterface;
use kosuha606\VirtualAdmin\Services\AdminConfigService;
use kosuha606\VirtualModelHelppack\ServiceManager;

/**
 * @package kosuha606\VirtualShop
 */
class ShopRoutesLoader implements AdminRoutesLoaderInterface
{
    public function routesData(): array
    {
        $adminConfigService = ServiceManager::getInstance()->get(AdminConfigService::class);

        return $adminConfigService->loadConfigs(__DIR__.'/_routes/');
    }
}