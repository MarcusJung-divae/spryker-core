<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAlternativeStorage\Persistence;

use Generated\Shared\Transfer\FilterTransfer;
use Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage;

interface ProductAlternativeStorageRepositoryInterface
{
    /**
     * @param int[] $productIds
     *
     * @return \Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductAlternativeStorage[]
     */
    public function findProductAlternativeStorageEntities(array $productIds): array;

    /**
     * @module Product
     *
     * @param int $idProduct
     *
     * @return string
     */
    public function findProductSkuById($idProduct): string;

    /**
     * @module ProductAlternative
     *
     * @param int $idProduct
     *
     * @return int[]
     */
    public function findAbstractAlternativesIdsByConcreteProductId($idProduct): array;

    /**
     * @module ProductAlternative
     *
     * @param int $idProduct
     *
     * @return int[]
     */
    public function findConcreteAlternativesIdsByConcreteProductId($idProduct): array;

    /**
     * @module Product
     *
     * @param int[] $productIds
     *
     * @return string[]
     */
    public function getIndexedProductConcreteIdToSkusByProductIds(array $productIds): array;

    /**
     * @module Product
     *
     * @param int[] $productIds
     *
     * @return string[]
     */
    public function getIndexedProductAbstractIdToSkusByProductIds(array $productIds): array;

    /**
     * @module Product
     *
     * @param int[] $productIds
     *
     * @return string[]
     */
    public function getIndexedProductConcreteIdToSkusByProductAbstractIds(array $productIds): array;

    /**
     * @param string $sku
     *
     * @return \Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage|null
     */
    public function findProductReplacementStorageEntitiesBySku(string $sku): ?SpyProductReplacementForStorage;

    /**
     * @module ProductAlternative
     *
     * @param int $idProductAbstract
     *
     * @return int[]
     */
    public function getReplacementsByAbstractProductId(int $idProductAbstract): array;

    /**
     * @module ProductAlternative
     *
     * @param int $idProductConcrete
     *
     * @return int[]
     */
    public function getReplacementsByConcreteProductId(int $idProductConcrete): array;

    /**
     * @deprecated Use `ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds()` instead.
     *
     * @see \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds()
     *
     * @return \Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductAlternativeStorage[]
     */
    public function findAllProductAlternativeStorageEntities(): array;

    /**
     * @deprecated Use `ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds()` instead.
     *
     * @see \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds()
     *
     * @param int[] $productAlternativeStorageIds
     *
     * @return \Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductAlternativeStorage[]
     */
    public function findProductAlternativeStorageEntitiesByIds(array $productAlternativeStorageIds): array;

    /**
     * @deprecated Use `ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds()` instead.
     *
     * @see \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds()
     *
     * @return \Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage[]
     */
    public function findAllProductReplacementForStorageEntities(): array;

    /**
     * @deprecated Use `ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds()` instead.
     *
     * @see \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds()
     *
     * @param int[] $productReplacementForStorageIds
     *
     * @return \Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage[]
     */
    public function findProductReplacementForStorageEntitiesByIds(array $productReplacementForStorageIds): array;

    /**
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     * @param int[] $productAlternativeStorageIds
     *
     * @return \Generated\Shared\Transfer\SynchronizationDataTransfer[]
     */
    public function getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds(
        FilterTransfer $filterTransfer,
        array $productAlternativeStorageIds = []
    ): array;

    /**
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     * @param int[] $productReplacementForStorageIds
     *
     * @return \Generated\Shared\Transfer\SynchronizationDataTransfer[]
     */
    public function getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds(
        FilterTransfer $filterTransfer,
        array $productReplacementForStorageIds = []
    ): array;
}
