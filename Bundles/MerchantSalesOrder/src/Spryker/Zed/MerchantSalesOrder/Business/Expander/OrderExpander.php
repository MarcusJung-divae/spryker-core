<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantSalesOrder\Business\Expander;

use Generated\Shared\Transfer\MerchantOrderCollectionTransfer;
use Generated\Shared\Transfer\MerchantOrderCriteriaTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\MerchantSalesOrder\Persistence\MerchantSalesOrderRepositoryInterface;

class OrderExpander implements OrderExpanderInterface
{
    /**
     * @var \Spryker\Zed\MerchantSalesOrder\Persistence\MerchantSalesOrderRepositoryInterface
     */
    protected $merchantSalesOrderRepository;

    /**
     * @param \Spryker\Zed\MerchantSalesOrder\Persistence\MerchantSalesOrderRepositoryInterface $merchantSalesOrderRepository
     */
    public function __construct(MerchantSalesOrderRepositoryInterface $merchantSalesOrderRepository)
    {
        $this->merchantSalesOrderRepository = $merchantSalesOrderRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function expandOrderWithMerchantOrderData(OrderTransfer $orderTransfer): OrderTransfer
    {
        $merchantOrderCriteriaTransfer = (new MerchantOrderCriteriaTransfer())
            ->setWithItems(true)
            ->setIdOrder($orderTransfer->getIdSalesOrder());

        $merchantOrderCollectionTransfer = $this->merchantSalesOrderRepository->getMerchantOrderCollection(
            $merchantOrderCriteriaTransfer
        );

        $orderTransfer = $this->expandOrderItemsWithMerchantOrderReference($orderTransfer, $merchantOrderCollectionTransfer);

        return $orderTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function expandOrderWithMerchantReferences(OrderTransfer $orderTransfer): OrderTransfer
    {
        $merchantReferences = $this->getMerchantReferences($orderTransfer);

        return $orderTransfer->setMerchantReferences($merchantReferences);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\MerchantOrderCollectionTransfer $merchantOrderCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function expandOrderItemsWithMerchantOrderReference(
        OrderTransfer $orderTransfer,
        MerchantOrderCollectionTransfer $merchantOrderCollectionTransfer
    ): OrderTransfer {
        $groupedByItemIdMerchantOrderTransfers = $this->groupMerchantOrderCollectionByItemId($merchantOrderCollectionTransfer);

        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if (!isset($groupedByItemIdMerchantOrderTransfers[$itemTransfer->getIdSalesOrderItem()])) {
                continue;
            }

            /** @var \Generated\Shared\Transfer\MerchantOrderTransfer $merchantOrderTransfer */
            $merchantOrderTransfer = $groupedByItemIdMerchantOrderTransfers[$itemTransfer->getIdSalesOrderItem()];
            $itemTransfer->setMerchantOrderReference($merchantOrderTransfer->getMerchantOrderReference());
        }

        return $orderTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantOrderCollectionTransfer $merchantOrderCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantOrderTransfer[]
     */
    protected function groupMerchantOrderCollectionByItemId(MerchantOrderCollectionTransfer $merchantOrderCollectionTransfer): array
    {
        $groupedByItemIdMerchantOrderTransfers = [];

        foreach ($merchantOrderCollectionTransfer->getMerchantOrders() as $merchantOrderTransfer) {
            foreach ($merchantOrderTransfer->getMerchantOrderItems() as $merchantOrderItemTransfer) {
                $groupedByItemIdMerchantOrderTransfers[$merchantOrderItemTransfer->getIdOrderItem()] = $merchantOrderTransfer;
            }
        }

        return $groupedByItemIdMerchantOrderTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return string[]
     */
    protected function getMerchantReferences(OrderTransfer $orderTransfer): array
    {
        $merchantReferences = [];

        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if (
                !$itemTransfer->getMerchantReference()
                || isset($merchantReferences[$itemTransfer->getMerchantReference()])
            ) {
                continue;
            }

            $merchantReferences[$itemTransfer->getMerchantReference()] = $itemTransfer->getMerchantReference();
        }

        return array_values($merchantReferences);
    }
}
