<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductOffer\Persistence;

use Orm\Zed\PriceProductOffer\Persistence\SpyPriceProductOfferQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Spryker\Zed\PriceProductOffer\Persistence\PriceProductOfferRepositoryInterface getRepository()
 * @method \Spryker\Zed\PriceProductOffer\PriceProductOfferConfig getConfig()
 */
class PriceProductOfferPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\PriceProductOffer\Persistence\SpyPriceProductOfferQuery
     */
    public function getPriceProductOfferPropelQuery(): SpyPriceProductOfferQuery
    {
        return SpyPriceProductOfferQuery::create();
    }
}
