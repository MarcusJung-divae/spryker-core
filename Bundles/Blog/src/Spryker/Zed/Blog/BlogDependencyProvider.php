<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Blog;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class BlogDependencyProvider extends AbstractBundleDependencyProvider
{
    const PLUGIN_POST_SAVE_BLOG = 'post save blog';
    const PLUGIN_PRE_SAVE_BLOG = 'pre save blog';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container)
    {
        $container[static::PLUGIN_POST_SAVE_BLOG] = function (Container $container) {
            return $this->getBlogPostSavePlugin();
        };

        $container[static::PLUGIN_PRE_SAVE_BLOG] = function (Container $container) {
            return $this->getBlogPreSavePlugin();
        };

        return $container;
    }

    /**
     * @return \Spryker\Zed\Blog\Dependency\Plugin\PreSaveBlogPluginInterface[]
     */
    public function getBlogPreSavePlugin()
    {
        return [];
    }

    /**
     * @return \Spryker\Zed\Blog\Dependency\Plugin\PostSaveBlogPluginInterface[]
     */
    public function getBlogPostSavePlugin()
    {
        return [];
    }
}
