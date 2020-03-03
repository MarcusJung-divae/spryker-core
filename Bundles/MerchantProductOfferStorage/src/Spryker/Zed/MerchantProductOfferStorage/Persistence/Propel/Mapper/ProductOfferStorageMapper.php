<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProductOfferStorage\Persistence\Propel\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\ProductOfferStorageTransfer;
use Generated\Shared\Transfer\ProductOfferTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Orm\Zed\ProductOffer\Persistence\SpyProductOffer;

class ProductOfferStorageMapper
{
    /**
     * @param \Generated\Shared\Transfer\ProductOfferTransfer $productOfferTransfer
     * @param \Generated\Shared\Transfer\ProductOfferStorageTransfer $productOfferStorageTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOfferStorageTransfer
     */
    public function mapProductOfferTransferToProductOfferStorageTransfer(
        ProductOfferTransfer $productOfferTransfer,
        ProductOfferStorageTransfer $productOfferStorageTransfer
    ): ProductOfferStorageTransfer {
        $productOfferStorageTransfer->setIdProductOffer($productOfferTransfer->getIdProductOffer());
        $productOfferStorageTransfer->setIdMerchant($productOfferTransfer->getFkMerchant());
        $productOfferStorageTransfer->setProductOfferReference($productOfferTransfer->getProductOfferReference());
        $productOfferStorageTransfer->setMerchantSku($productOfferTransfer->getMerchantSku());
        $productOfferStorageTransfer->setMerchantReference($productOfferTransfer->getMerchantReference());

        return $productOfferStorageTransfer;
    }

    /**
     * @param \Orm\Zed\ProductOffer\Persistence\SpyProductOffer $productOfferEntity
     * @param \Generated\Shared\Transfer\ProductOfferTransfer $productOfferTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOfferTransfer
     */
    public function mapProductOfferEntityToProductOfferTransfer(
        SpyProductOffer $productOfferEntity,
        ProductOfferTransfer $productOfferTransfer
    ): ProductOfferTransfer {
        $productOfferTransfer = $productOfferTransfer->fromArray(
            $productOfferEntity->toArray(),
            true
        );

        $productOfferTransfer = $productOfferTransfer->setMerchantReference($productOfferEntity->getSpyMerchant()->getMerchantReference());
        $productOfferTransfer->setStores($this->getStoresByProductOfferEntity($productOfferEntity));

        return $productOfferTransfer;
    }

    /**
     * @param \Orm\Zed\ProductOffer\Persistence\SpyProductOffer $productOfferEntity
     *
     * @return \Generated\Shared\Transfer\StoreTransfer[]|\ArrayObject
     */
    protected function getStoresByProductOfferEntity(SpyProductOffer $productOfferEntity): ArrayObject
    {
        $storeTransfers = [];
        foreach ($productOfferEntity->getSpyStores() as $storeEntity) {
            $storeTransfers[] = (new StoreTransfer())->fromArray($storeEntity->toArray(), true);
        }

        return new ArrayObject($storeTransfers);
    }
}
