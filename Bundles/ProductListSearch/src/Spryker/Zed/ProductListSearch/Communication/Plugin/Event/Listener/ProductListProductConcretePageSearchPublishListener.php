<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListSearch\Communication\Plugin\Event\Listener;

use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\PropelOrm\Business\Transaction\DatabaseTransactionHandlerTrait;

/**
 * @method \Spryker\Zed\ProductListSearch\Communication\ProductListSearchCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductListSearch\Business\ProductListSearchFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductListSearch\ProductListSearchConfig getConfig()
 */
class ProductListProductConcretePageSearchPublishListener extends AbstractPlugin implements EventBulkHandlerInterface
{
    use DatabaseTransactionHandlerTrait;

    /**
     * {@inheritDoc}
     * - Handles product list update event.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $eventTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventTransfers, $eventName): void
    {
        $this->preventTransaction();
        $productListIds = $this->getFactory()
            ->getEventBehaviorFacade()
            ->getEventTransferIds($eventTransfers);

        $this->getFactory()->getProductPageSearchFacade()->publishProductConcretes(
            $this->getFactory()->getProductListFacade()->getProductConcreteIdsByProductListIds($productListIds)
        );
    }
}
