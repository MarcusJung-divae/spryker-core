<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CompanyUserStorage\Processor\CompanyUser\Provider;

use Generated\Shared\Transfer\CompanyUserTransfer;

interface CompanyUserStorageProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserTransfer
     */
    public function provideCompanyUserFromStorage(CompanyUserTransfer $companyUserTransfer): CompanyUserTransfer;
}
