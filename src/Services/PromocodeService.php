<?php

namespace kosuha606\VirtualShop\Services;

use kosuha606\VirtualModel\VirtualModelManager;
use kosuha606\VirtualShop\Model\PromocodeVm;

class PromocodeService
{
    /**
     * @param $id
     * @return PromocodeVm
     * @throws \Exception
     */
    public function findPromocodeById($id)
    {
        /** @var PromocodeVm $promocode */
        $promocode = VirtualModelManager::getInstance()->getProvider()->one(PromocodeVm::class, [
            'where' => [
                ['=', 'id', $id]
            ]
        ]);

        return $promocode;
    }
}
