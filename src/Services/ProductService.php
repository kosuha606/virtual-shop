<?php

namespace kosuha606\VirtualShop\Services;

use kosuha606\VirtualAdmin\Classes\Pagination;
use kosuha606\VirtualShop\Dto\LoadProductsDTO;
use kosuha606\VirtualShop\Model\FilterProductVm;
use kosuha606\VirtualShop\ServiceManager;
use kosuha606\VirtualModel\VirtualModelManager;
use kosuha606\VirtualShop\Model\ActionVm;
use kosuha606\VirtualShop\Model\ProductVm;

/**
 * @package kosuha606\VirtualShop\Services
 */
class ProductService
{
    /**
     * @var OrderService
     */
    private $orderService;
    /**
     * @var FavoriteService
     */
    private $favoriteService;

    /** @var ActionVm[] */
    private $actions = [];

    public function __construct(OrderService $orderService, FavoriteService $favoriteService)
    {
        $this->orderService = $orderService;
        $this->favoriteService = $favoriteService;
    }

    /**
     * @return ActionVm[]
     * @throws \Exception
     */
    public function findAllActions()
    {
        if (!$this->actions) {
            $this->actions = VirtualModelManager::getInstance()->getProvider()->many(ActionVm::class, [
                'where' => [
                    ['all'],
                ],
            ]);
        }

        return $this->actions;
    }

    /**
     * @return ProductVm
     * @throws \Exception
     */
    public function findProductById($productId)
    {
        /** @var ProductVm $product */
        $product = ProductVm::one([
            'where' => [
                ['=', 'id', $productId],
            ],
        ]);

        return $product;
    }

    /**
     * @param array $config
     * @return LoadProductsDTO
     * @throws \Exception
     */
    public function loadProductsWithActions(
        $filters = [],
        $page = 1,
        $itemsPerPage = 10,
        $orderBy = 'id'
    ) {
        $whereConfig = [['all']];

        if ($filters) {
            $whereConfig = [];

            foreach ($filters as $key => $value) {
                $whereConfig[] = ['=', $key, $value];
            }
        }

        $productsCount = ProductVm::count(['where' => $whereConfig]);

        $pagination = new Pagination($page, $itemsPerPage);
        $pagination->totals = $productsCount;

        $orderDirection = SORT_ASC;
        if (strpos($orderBy, '_reverse') !== false) {
            $orderBy = str_replace('_reverse', '', $orderBy);
            $orderDirection = SORT_DESC;
        }

        $products = ProductVm::many([
            'where' => $whereConfig,
            'limit' => $pagination->getLimit(),
            'offset' => $pagination->getOffset(),
            'orderBy' => $orderBy ? [$orderBy => $orderDirection] : null,
        ]);

        foreach ($products as &$product) {
            $product->actions = $this->findAllActions();
        }

        return new LoadProductsDTO($products, $pagination);
    }

    /**
     * @param ProductVm $product
     * @param $qty
     * @return bool
     * @throws \Exception
     */
    public function hasFreeRests($product, $qty)
    {
        $totalFreeQty = 0;

        if ($product->rests) {
            foreach ($product->rests as $productRest) {
                $totalFreeQty += $productRest->qty;
            }
        }

        $reservedInOrdersQty = $this->orderService->findOrderReserveQtyByProduct($product);
        $totalFreeQty -= $reservedInOrdersQty;

        return $totalFreeQty >= $qty;
    }

    /**
     * @param ProductVm $product
     * @return bool
     * @throws \Exception
     */
    public function isInFavorite(ProductVm $product)
    {
        $user = ServiceManager::getInstance()->userService->current();
        if (!$user) {
            return false;
        }

        return $this->favoriteService->hasFavorite($user, $product);
    }

    /**
     * @param ProductVm $productVm
     * @return int
     * @throws \Exception
     */
    public function maxAvailableRestAmount(ProductVm $productVm)
    {
        $reservedInOrdersQty = $this->orderService->findOrderReserveQtyByProduct($productVm);
        $maxRestAmount = $productVm->maxRestAmount();

        $amount = $maxRestAmount - $reservedInOrdersQty;

        if ($amount <= 0) {
            $amount = 0;
        }

        return $amount;
    }

    public function calculateProductSalePrice($product)
    {
        $price = $product->price;

        if ($product->actions) {
            /** @var ActionVm $action */
            foreach ($product->actions as $action) {
                if (in_array($product->id, $action->normalizeProductIds)) {
                    $price -= $price * ($action->percent/100);
                }
            }
        }

        return $price;
    }

    /**
     * @param $getFilters
     * @return mixed
     * @throws \Exception
     */
    public function processGetFilters($getFilters = [])
    {
        $filters['id'] = [];
        $filterProducts = [];

        if (!$getFilters) {
            return [];
        }

        foreach ($getFilters as $item) {
            list($value, $categoryId) = explode('_', $item);
            $nextFilterProducts = $this->getProductIds(FilterProductVm::many([
                'where' => [
                    ['=', 'value', $value],
                    ['=', 'category_id', $categoryId],
                ],
            ]));

            if (isset($filterProducts[$categoryId])) {
                $filterProducts[$categoryId] = array_merge($filterProducts[$categoryId], $nextFilterProducts);
            } else {
                $filterProducts[$categoryId] = $nextFilterProducts;
            }
        }

        foreach ($filterProducts as $filterProduct) {
            if (!$filters['id']) {
                $filters['id'] = $filterProduct;
            } else {
                $filters['id'] = array_intersect($filters['id'], $filterProduct);
            }
        }

        return $filters;
    }

    /**
     * @param $items
     * @return array
     */
    private function getProductIds($items)
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = $item->product_id;
        }

        return $result;
    }
}