<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Publisher\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Publisher\Business\Collator\PublisherEventCollator;
use Spryker\Zed\Publisher\Business\Collator\PublisherEventCollatorInterface;
use Spryker\Zed\Publisher\PublisherDependencyProvider;

/**
 * @method \Spryker\Zed\Publisher\PublisherConfig getConfig()
 */
class PublisherBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\Publisher\Business\Collator\PublisherEventCollatorInterface
     */
    public function createPublisherEventCollator(): PublisherEventCollatorInterface
    {
        return new PublisherEventCollator(
            $this->getPublisherPlugins()
        );
    }

    /**
     * @return \Spryker\Zed\PublisherExtension\Dependency\Plugin\PublisherPluginInterface[]
     */
    public function getPublisherPlugins(): array
    {
        return $this->getProvidedDependency(PublisherDependencyProvider::PLUGINS_PUBLISHER);
    }
}
