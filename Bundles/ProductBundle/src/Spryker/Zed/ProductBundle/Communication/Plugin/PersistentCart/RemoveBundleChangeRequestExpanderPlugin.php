<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductBundle\Communication\Plugin\PersistentCart;

use Generated\Shared\Transfer\CartChangeTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\PersistentCartExtension\Dependency\Plugin\CartChangeRequestExpandPluginInterface;

/**
 * @method \Spryker\Zed\ProductBundle\Business\ProductBundleFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductBundle\Communication\ProductBundleCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductBundle\ProductBundleConfig getConfig()
 * @method \Spryker\Zed\ProductBundle\Persistence\ProductBundleQueryContainerInterface getQueryContainer()
 */
class RemoveBundleChangeRequestExpanderPlugin extends AbstractPlugin implements CartChangeRequestExpandPluginInterface
{
    /**
     * {@inheritDoc}
     * Specification:
     * - Replace items with bundle items if it exist
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expand(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        return $this->getFacade()->replaceItemsWithBundleItems($cartChangeTransfer);
    }
}
