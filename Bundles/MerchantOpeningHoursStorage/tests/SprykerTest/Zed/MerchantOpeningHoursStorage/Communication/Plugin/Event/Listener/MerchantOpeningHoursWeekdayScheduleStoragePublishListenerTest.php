<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantOpeningHoursStorage\Communication\Plugin\Event\Listener;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\EventEntityTransfer;
use Spryker\Client\Kernel\Container;
use Spryker\Client\Queue\QueueDependencyProvider;
use Spryker\Zed\MerchantOpeningHours\Dependency\MerchantOpeningHoursEvents;
use Spryker\Zed\MerchantOpeningHoursStorage\Communication\Plugin\Event\Listener\MerchantOpeningHoursWeekdayScheduleStoragePublishListener;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MerchantOpeningHoursStorage
 * @group Communication
 * @group Plugin
 * @group Event
 * @group Listener
 * @group MerchantOpeningHoursWeekdayScheduleStoragePublishListenerTest
 * Add your own group annotations below this line
 */
class MerchantOpeningHoursWeekdayScheduleStoragePublishListenerTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\MerchantOpeningHoursStorage\MerchantOpeningHoursStorageCommunicationTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->tester->ensureMerchantOpeningHoursTablesIsEmpty();

        $this->tester->setDependency(QueueDependencyProvider::QUEUE_ADAPTERS, function (Container $container) {
            return [
                $container->getLocator()->rabbitMq()->client()->createQueueAdapter(),
            ];
        });
    }

    /**
     * @return void
     */
    public function testMerchantOpeningHoursWeekdayScheduleStoragePublishListenerStoresData(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();
        $merchantOpeningHoursWeekdayScheduleEntity = $this->tester->createMerchantOpeningHoursWeekdaySchedule($merchantTransfer);
        $merchantOpeningHoursWeekdayScheduleStoragePublishListener = new MerchantOpeningHoursWeekdayScheduleStoragePublishListener();
        $merchantOpeningHoursWeekdayScheduleStoragePublishListener->setFacade($this->tester->getFacade());
        $eventTransfers = [
            (new EventEntityTransfer())->setId($merchantTransfer->getIdMerchant()),
        ];

        // Act
        $merchantOpeningHoursWeekdayScheduleStoragePublishListener->handleBulk($eventTransfers, MerchantOpeningHoursEvents::ENTITY_SPY_MERCHANT_OPENING_HOURS_WEEKDAY_SCHEDULE_CREATE);

        // Assert
        $this->assertNotNull(
            $this->tester->findMerchantOpeningHoursByFkMerchant($merchantOpeningHoursWeekdayScheduleEntity->getFkMerchant())
        );
    }
}
