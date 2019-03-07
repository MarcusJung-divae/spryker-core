<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ContentBannersRestApi;

use Spryker\Glue\ContentBannersRestApi\Dependency\Client\ContentBannersRestApiToContentStorageClientBridge;
use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;

/**
 * @method \Spryker\Glue\ContentBannersRestApi\ContentBannersRestApiConfig getConfig()
 */
class ContentBannersRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_CONTENT_STORAGE = 'CLIENT_CONTENT_STORAGE';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = $this->addContentStorageClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addContentStorageClient(Container $container): Container
    {
        $container[static::CLIENT_CONTENT_STORAGE] = function (Container $container) {
            return new ContentBannersRestApiToContentStorageClientBridge($container->getLocator()->contentStorage()->client());
        };

        return $container;
    }
}