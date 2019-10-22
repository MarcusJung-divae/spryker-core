<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProfileGui\Dependency\Facade;

use Generated\Shared\Transfer\CountryCollectionTransfer;

interface MerchantProfileGuiToCountryFacadeInterface
{
    /**
     * @return \Generated\Shared\Transfer\CountryCollectionTransfer
     */
    public function getAvailableCountries(): CountryCollectionTransfer;
}
