<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOfferStock\Business;

use Generated\Shared\Transfer\ProductOfferStockRequestTransfer;
use Generated\Shared\Transfer\ProductOfferStockTransfer;

interface ProductOfferStockFacadeInterface
{
    /**
     * Specification:
     * - Retrieves product offer stock from database for provided store.
     * - Expects ProductOfferStockRequestTransfer.store to be provided.
     * - Expects ProductOfferStockRequestTransfer.productOfferReference to be provided.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductOfferStockRequestTransfer $productOfferStockRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOfferStockTransfer
     */
    public function getProductOfferStock(ProductOfferStockRequestTransfer $productOfferStockRequestTransfer): ProductOfferStockTransfer;
}
