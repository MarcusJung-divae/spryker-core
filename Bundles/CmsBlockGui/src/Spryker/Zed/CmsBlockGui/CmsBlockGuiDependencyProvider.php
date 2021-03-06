<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsBlockGui;

use Spryker\Zed\CmsBlockGui\Dependency\Facade\CmsBlockGuiToCmsBlockBridge;
use Spryker\Zed\CmsBlockGui\Dependency\Facade\CmsBlockGuiToLocaleBridge;
use Spryker\Zed\CmsBlockGui\Dependency\QueryContainer\CmsBlockGuiToCmsBlockQueryContainerBridge;
use Spryker\Zed\CmsBlockGui\Exception\MissingStoreRelationFormTypePluginException;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Communication\Form\FormTypeInterface;
use Spryker\Zed\Kernel\Communication\Plugin\Pimple;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\CmsBlockGui\CmsBlockGuiConfig getConfig()
 */
class CmsBlockGuiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_CMS_BLOCK = 'CMS_BLOCK_GUI:FACADE_CMS_BLOCK';
    public const FACADE_LOCALE = 'CMS_BLOCK_GUI:FACADE_LOCALE';

    public const QUERY_CONTAINER_CMS_BLOCK = 'CMS_BLOCK_GUI:QUERY_CONTAINER_CMS_BLOCK';

    public const PLUGINS_CMS_BLOCK_FORM = 'CMS_BLOCK_GUI:PLUGINS_CMS_BLOCK_FORM';
    public const PLUGINS_CMS_BLOCK_VIEW = 'CMS_BLOCK_GUI:PLUGINS_CMS_BLOCK_VIEW';
    public const PLUGIN_STORE_RELATION_FORM_TYPE = 'PLUGIN_STORE_RELATION_FORM_TYPE';

    public const TWIG_ENVIRONMENT = 'CMS_BLOCK_GUI:TWIG_ENVIRONMENT';

    public const PLUGINS_CMS_BLOCK_GLOSSARY_BEFORE_SAVE = 'PLUGINS_CMS_BLOCK_GLOSSARY_BEFORE_SAVE';
    public const PLUGINS_CMS_BLOCK_GLOSSARY_AFTER_FIND = 'PLUGINS_CMS_BLOCK_GLOSSARY_AFTER_FIND';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = parent::provideCommunicationLayerDependencies($container);
        $container = $this->addCmsBlockQueryContainer($container);
        $container = $this->addCmsBlockFacade($container);
        $container = $this->addLocaleFacade($container);
        $container = $this->addCmsBlockFormPlugins($container);
        $container = $this->addCmsBlockViewPlugins($container);
        $container = $this->addTwigEnvironment($container);
        $container = $this->addStoreRelationFormTypePlugin($container);
        $container = $this->addCmsBlockGlossaryAfterFindPlugins($container);
        $container = $this->addCmsBlockGlossaryBeforeSavePlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTwigEnvironment(Container $container)
    {
        $container[static::TWIG_ENVIRONMENT] = function (Container $container) {
            return $this->getTwigEnvironment();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsBlockQueryContainer(Container $container)
    {
        $container[static::QUERY_CONTAINER_CMS_BLOCK] = function (Container $container) {
            return new CmsBlockGuiToCmsBlockQueryContainerBridge($container->getLocator()->cmsBlock()->queryContainer());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsBlockFacade(Container $container)
    {
        $container[static::FACADE_CMS_BLOCK] = function (Container $container) {
            return new CmsBlockGuiToCmsBlockBridge($container->getLocator()->cmsBlock()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addLocaleFacade(Container $container)
    {
        $container[static::FACADE_LOCALE] = function (Container $container) {
            return new CmsBlockGuiToLocaleBridge($container->getLocator()->locale()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsBlockFormPlugins(Container $container)
    {
        $container[static::PLUGINS_CMS_BLOCK_FORM] = function (Container $container) {
            return $this->getCmsBlockFormPlugins();
        };

        return $container;
    }

    /**
     * @return array
     */
    protected function getCmsBlockFormPlugins()
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsBlockViewPlugins(Container $container)
    {
        $container[static::PLUGINS_CMS_BLOCK_VIEW] = function (Container $container) {
            return $this->getCmsBlockViewPlugins();
        };

        return $container;
    }

    /**
     * @return array
     */
    protected function getCmsBlockViewPlugins()
    {
        return [];
    }

    /**
     * @return \Twig\Environment
     */
    protected function getTwigEnvironment()
    {
        $pimplePlugin = new Pimple();

        return $pimplePlugin->getApplication()['twig'];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreRelationFormTypePlugin(Container $container)
    {
        $container[static::PLUGIN_STORE_RELATION_FORM_TYPE] = function () {
            return $this->getStoreRelationFormTypePlugin();
        };

        return $container;
    }

    /**
     * @throws \Spryker\Zed\CmsBlockGui\Exception\MissingStoreRelationFormTypePluginException
     *
     * @return \Spryker\Zed\Kernel\Communication\Form\FormTypeInterface
     */
    protected function getStoreRelationFormTypePlugin()
    {
        throw new MissingStoreRelationFormTypePluginException(
            sprintf(
                'Missing instance of %s! You need to configure StoreRelationFormType ' .
                'in your own CmsBlockGuiDependencyProvider::getStoreRelationFormTypePlugin() ' .
                'to be able to manage cms blocks.',
                FormTypeInterface::class
            )
        );
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsBlockGlossaryAfterFindPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_CMS_BLOCK_GLOSSARY_AFTER_FIND, function () {
            return $this->getCmsBlockGlossaryAfterFindPlugins();
        });

        return $container;
    }

    /**
     * @return \Spryker\Zed\CmsBlockGuiExtension\Dependency\Plugin\CmsBlockGlossaryAfterFindPluginInterface[]
     */
    protected function getCmsBlockGlossaryAfterFindPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsBlockGlossaryBeforeSavePlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_CMS_BLOCK_GLOSSARY_BEFORE_SAVE, function () {
            return $this->getCmsBlockGlossaryBeforeSavePlugins();
        });

        return $container;
    }

    /**
     * @return \Spryker\Zed\CmsBlockGuiExtension\Dependency\Plugin\CmsBlockGlossaryBeforeSavePluginInterface[]
     */
    protected function getCmsBlockGlossaryBeforeSavePlugins(): array
    {
        return [];
    }
}
