<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountPromotion;

use Spryker\Zed\DiscountPromotion\Dependency\Facade\DiscountPromotionToAvailabilityBridge;
use Spryker\Zed\DiscountPromotion\Dependency\Facade\DiscountPromotionToProductBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\DiscountPromotion\DiscountPromotionConfig getConfig()
 */
class DiscountPromotionDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';
    public const FACADE_AVAILABILITY = 'FACADE_AVAILABILITY';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addProductFacade($container);
        $container = $this->addAvailabilityFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductFacade(Container $container)
    {
        $container[static::FACADE_PRODUCT] = function (Container $container) {
            return new DiscountPromotionToProductBridge($container->getLocator()->product()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAvailabilityFacade(Container $container)
    {
        $container[static::FACADE_AVAILABILITY] = function (Container $container) {
            return new DiscountPromotionToAvailabilityBridge($container->getLocator()->availability()->facade());
        };

        return $container;
    }
}
