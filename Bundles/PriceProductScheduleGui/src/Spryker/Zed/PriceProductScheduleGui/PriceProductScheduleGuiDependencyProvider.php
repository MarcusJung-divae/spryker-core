<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductScheduleGui;

use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleListQuery;
use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\PriceProductScheduleGui\Dependency\Facade\PriceProductScheduleGuiToCurrencyFacadeBridge;
use Spryker\Zed\PriceProductScheduleGui\Dependency\Facade\PriceProductScheduleGuiToMoneyFacadeBridge;
use Spryker\Zed\PriceProductScheduleGui\Dependency\Facade\PriceProductScheduleGuiToPriceProductFacadeBridge;
use Spryker\Zed\PriceProductScheduleGui\Dependency\Facade\PriceProductScheduleGuiToPriceProductScheduleFacadeBridge;
use Spryker\Zed\PriceProductScheduleGui\Dependency\Facade\PriceProductScheduleGuiToProductFacadeBridge;
use Spryker\Zed\PriceProductScheduleGui\Dependency\Facade\PriceProductScheduleGuiToStoreFacadeBridge;
use Spryker\Zed\PriceProductScheduleGui\Dependency\Facade\PriceProductScheduleGuiToTranslatorFacadeBridge;
use Spryker\Zed\PriceProductScheduleGui\Dependency\Service\PriceProductScheduleGuiToUtilCsvServiceBridge;

/**
 * @method \Spryker\Zed\PriceProductScheduleGui\PriceProductScheduleGuiConfig getConfig()
 */
class PriceProductScheduleGuiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_MONEY = 'FACADE_MONEY';
    public const FACADE_PRICE_PRODUCT = 'FACADE_PRICE_PRODUCT';
    public const FACADE_STORE = 'FACADE_STORE';
    public const FACADE_TRANSLATOR = 'FACADE_TRANSLATOR';
    public const FACADE_PRICE_PRODUCT_SCHEDULE = 'FACADE_PRICE_PRODUCT_SCHEDULE';
    public const FACADE_CURRENCY = 'FACADE_CURRENCY';
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';

    public const PROPEL_QUERY_PRICE_PRODUCT_SCHEDULE = 'PROPEL_QUERY_PRICE_PRODUCT_SCHEDULE';
    public const PROPEL_QUERY_PRICE_PRODUCT_SCHEDULE_LIST = 'PROPEL_QUERY_PRICE_PRODUCT_SCHEDULE_LIST';

    public const SERVICE_UTIL_CSV = 'SERVICE_UTIL_CSV';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = $this->addPriceProductFacade($container);
        $container = $this->addStoreFacade($container);
        $container = $this->addTranslatorFacade($container);
        $container = $this->addMoneyFacade($container);
        $container = $this->addPriceProductScheduleFacade($container);
        $container = $this->addPriceProductScheduleQuery($container);
        $container = $this->addPriceProductScheduleListQuery($container);
        $container = $this->addUtilCsvService($container);
        $container = $this->addCurrencyFacade($container);
        $container = $this->addProductFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilCsvService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_CSV, function (Container $container) {
            return new PriceProductScheduleGuiToUtilCsvServiceBridge(
                $container->getLocator()->utilCsv()->service()
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductFacade(Container $container): Container
    {
        $container->set(static::FACADE_PRODUCT, function (Container $container) {
            return new PriceProductScheduleGuiToProductFacadeBridge(
                $container->getLocator()->product()->facade()
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCurrencyFacade(Container $container): Container
    {
        $container->set(static::FACADE_CURRENCY, function (Container $container) {
            return new PriceProductScheduleGuiToCurrencyFacadeBridge(
                $container->getLocator()->currency()->facade()
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPriceProductFacade(Container $container): Container
    {
        $container->set(static::FACADE_PRICE_PRODUCT, function (Container $container) {
            return new PriceProductScheduleGuiToPriceProductFacadeBridge(
                $container->getLocator()->priceProduct()->facade()
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new PriceProductScheduleGuiToStoreFacadeBridge(
                $container->getLocator()->store()->facade()
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTranslatorFacade(Container $container): Container
    {
        $container->set(static::FACADE_TRANSLATOR, function (Container $container) {
            return new PriceProductScheduleGuiToTranslatorFacadeBridge(
                $container->getLocator()->translator()->facade()
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyFacade(Container $container): Container
    {
        $container->set(static::FACADE_MONEY, function (Container $container) {
            return new PriceProductScheduleGuiToMoneyFacadeBridge(
                $container->getLocator()->money()->facade()
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPriceProductScheduleFacade(Container $container): Container
    {
        $container->set(static::FACADE_PRICE_PRODUCT_SCHEDULE, function (Container $container) {
            return new PriceProductScheduleGuiToPriceProductScheduleFacadeBridge(
                $container->getLocator()->priceProductSchedule()->facade()
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPriceProductScheduleQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_PRICE_PRODUCT_SCHEDULE, function () {
            return SpyPriceProductScheduleQuery::create();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPriceProductScheduleListQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_PRICE_PRODUCT_SCHEDULE_LIST, function () {
            return SpyPriceProductScheduleListQuery::create();
        });

        return $container;
    }
}
