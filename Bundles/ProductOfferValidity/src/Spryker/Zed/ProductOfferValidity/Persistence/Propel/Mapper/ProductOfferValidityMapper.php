<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOfferValidity\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\ProductOfferValidityCollectionTransfer;
use Generated\Shared\Transfer\ProductOfferValidityTransfer;
use Orm\Zed\ProductOfferValidity\Persistence\SpyProductOfferValidity;
use Propel\Runtime\Collection\ObjectCollection;

class ProductOfferValidityMapper
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $productOfferValidityEntities
     * @param \Generated\Shared\Transfer\ProductOfferValidityCollectionTransfer $productOfferValidityCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOfferValidityCollectionTransfer
     */
    public function productOfferValidityEntitiesToProductOfferValidityCollectionTransfer(
        ObjectCollection $productOfferValidityEntities,
        ProductOfferValidityCollectionTransfer $productOfferValidityCollectionTransfer
    ): ProductOfferValidityCollectionTransfer {
        foreach ($productOfferValidityEntities as $productOfferValidityEntity) {
            $productOfferValidityCollectionTransfer->addProductOfferValidity(
                $this->productOfferValidityEntityToProductOfferValidityTransfer(
                    $productOfferValidityEntity,
                    new ProductOfferValidityTransfer()
                )
            );
        }

        return $productOfferValidityCollectionTransfer;
    }

    /**
     * @param \Orm\Zed\ProductOfferValidity\Persistence\SpyProductOfferValidity $productOfferValidityEntity
     * @param \Generated\Shared\Transfer\ProductOfferValidityTransfer $productOfferValidityTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOfferValidityTransfer
     */
    public function productOfferValidityEntityToProductOfferValidityTransfer(
        SpyProductOfferValidity $productOfferValidityEntity,
        ProductOfferValidityTransfer $productOfferValidityTransfer
    ): ProductOfferValidityTransfer {
        $productOfferValidityTransfer->fromArray($productOfferValidityEntity->toArray(), true);
        $productOfferValidityTransfer->setIdProductOffer($productOfferValidityEntity->getFkProductOffer());

        return $productOfferValidityTransfer;
    }
}
