<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductDiscontinued\Persistence;

use Generated\Shared\Transfer\ProductDiscontinuedCollectionTransfer;
use Generated\Shared\Transfer\ProductDiscontinuedCriteriaFilterTransfer;
use Generated\Shared\Transfer\ProductDiscontinuedTransfer;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

/**
 * @method \Spryker\Zed\ProductDiscontinued\Persistence\ProductDiscontinuedPersistenceFactory getFactory()
 */
class ProductDiscontinuedRepository extends AbstractRepository implements ProductDiscontinuedRepositoryInterface
{
    /**
     * @uses Product
     *
     * @param \Generated\Shared\Transfer\ProductDiscontinuedTransfer $productDiscontinuedTransfer
     *
     * @return \Generated\Shared\Transfer\ProductDiscontinuedTransfer|null
     */
    public function findProductDiscontinuedByProductId(
        ProductDiscontinuedTransfer $productDiscontinuedTransfer
    ): ?ProductDiscontinuedTransfer {
        $productDiscontinuedEntity = $this->getFactory()
            ->createProductDiscontinuedQuery()
            ->leftJoinWithSpyProductDiscontinuedNote()
            ->leftJoinWithProduct()
            ->filterByFkProduct($productDiscontinuedTransfer->getFkProduct())
            ->find();

        if ($productDiscontinuedEntity->count()) {
            return $this->getFactory()
                ->createProductDiscontinuedMapper()
                ->mapProductDiscontinuedTransfer($productDiscontinuedEntity->getFirst());
        }

        return null;
    }

    /**
     * @param array $productIds
     *
     * @return bool
     */
    public function areAllConcreteProductsDiscontinued(array $productIds): bool
    {
        return ($this->getFactory()
                ->createProductDiscontinuedQuery()
                ->filterByFkProduct_In($productIds)
                ->count() === count($productIds));
    }

    /**
     * @param int[] $productIds
     *
     * @return bool
     */
    public function isAnyProductConcreteDiscontinued(array $productIds): bool
    {
        return $this->getFactory()
            ->createProductDiscontinuedQuery()
            ->filterByFkProduct_In($productIds)
            ->exists();
    }

    /**
     * @return \Generated\Shared\Transfer\ProductDiscontinuedCollectionTransfer
     */
    public function findProductsToDeactivate(): ProductDiscontinuedCollectionTransfer
    {
        $productDiscontinuedEntityCollection = $this->getFactory()
            ->createProductDiscontinuedQuery()
            ->filterByActiveUntil(['max' => time()], Criteria::LESS_THAN)
            ->find();

        if ($productDiscontinuedEntityCollection->count()) {
            return $this->getFactory()
                ->createProductDiscontinuedMapper()
                ->mapTransferCollection($productDiscontinuedEntityCollection);
        }

        return new ProductDiscontinuedCollectionTransfer();
    }

    /**
     * @uses Product
     *
     * @param \Generated\Shared\Transfer\ProductDiscontinuedCriteriaFilterTransfer $criteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\ProductDiscontinuedCollectionTransfer
     */
    public function findProductDiscontinuedCollection(
        ProductDiscontinuedCriteriaFilterTransfer $criteriaFilterTransfer
    ): ProductDiscontinuedCollectionTransfer {
        $productDiscontinuedQuery = $this->getFactory()
            ->createProductDiscontinuedQuery()
            ->leftJoinWithSpyProductDiscontinuedNote()
            ->leftJoinWithProduct();

        if ($criteriaFilterTransfer->getIds()) {
            $productDiscontinuedQuery
                ->filterByIdProductDiscontinued_In($criteriaFilterTransfer->getIds());
        }

        if ($criteriaFilterTransfer->getSkus()) {
            $productDiscontinuedQuery
                ->useProductQuery()
                    ->filterBySku_In($criteriaFilterTransfer->getSkus())
                ->endUse();
        }

        $productDiscontinuedEntityCollection = $productDiscontinuedQuery->find();

        if ($productDiscontinuedEntityCollection->count()) {
            return $this->getFactory()
                ->createProductDiscontinuedMapper()
                ->mapTransferCollection($productDiscontinuedEntityCollection);
        }

        return new ProductDiscontinuedCollectionTransfer();
    }

    /**
     * @module Product
     *
     * @param string $sku
     *
     * @return bool
     */
    public function checkIfProductDiscontinuedBySku(string $sku): bool
    {
        return $this->getFactory()
            ->createProductDiscontinuedQuery()
            ->useProductQuery()
                ->filterBySku($sku)
            ->endUse()
            ->exists();
    }

    /**
     * @module Product
     *
     * @return int[]
     */
    public function findProductAbstractIdsWithDiscontinuedConcrete(): array
    {
        return $this->getFactory()
            ->createProductDiscontinuedQuery()
            ->leftJoinProduct()
            ->useProductQuery()
                ->groupByFkProductAbstract()
            ->endUse()
            ->select([SpyProductTableMap::COL_FK_PRODUCT_ABSTRACT])
            ->find()
            ->toArray();
    }
}
