<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductPackagingUnitGui;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductPackagingUnitGuiConfig extends AbstractBundleConfig
{
    public const REQUEST_PARAM_ID_PRODUCT_PACKAGING_UNIT_TYPE = 'id-product-packaging-unit-type';
    public const REQUEST_PARAM_REDIRECT_URL = 'redirect-url';

    public const URL_PRODUCT_PACKAGING_UNIT_TYPE_LIST = '/product-packaging-unit-gui/';
    public const URL_PRODUCT_PACKAGING_UNIT_TYPE_EDIT = '/product-packaging-unit-gui/edit/';
    public const URL_PRODUCT_PACKAGING_UNIT_TYPE_DELETE = '/product-packaging-unit-gui/delete/';
}
