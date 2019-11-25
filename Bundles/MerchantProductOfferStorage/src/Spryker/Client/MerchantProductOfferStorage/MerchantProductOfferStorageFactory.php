<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Client\MerchantProductOfferStorage;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\MerchantProductOfferStorage\Dependency\Client\MerchantProductOfferStorageToStorageClientInterface;
use Spryker\Client\MerchantProductOfferStorage\Dependency\Service\MerchantProductOfferStorageToSynchronizationServiceInterface;
use Spryker\Client\MerchantProductOfferStorage\Mapper\MerchantProductOfferMapper;
use Spryker\Client\MerchantProductOfferStorage\Mapper\MerchantProductOfferMapperInterface;
use Spryker\Client\MerchantProductOfferStorage\Plugin\ProductOfferProviderPluginInterface;
use Spryker\Client\MerchantProductOfferStorage\ProductConcreteDefaultProductOffer\ProductConcreteDefaultProductOffer;
use Spryker\Client\MerchantProductOfferStorage\ProductConcreteDefaultProductOffer\ProductConcreteDefaultProductOfferInterface;
use Spryker\Client\MerchantProductOfferStorage\Storage\ProductOfferStorageReader;
use Spryker\Client\MerchantProductOfferStorage\Storage\ProductOfferStorageReaderInterface;

class MerchantProductOfferStorageFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Client\MerchantProductOfferStorage\Storage\ProductOfferStorageReaderInterface
     */
    public function createProductOfferStorageReader(): ProductOfferStorageReaderInterface
    {
        return new ProductOfferStorageReader(
            $this->getStorageClient(),
            $this->getSynchronizationService(),
            $this->createMerchantProductOfferMapper()
        );
    }

    /**
     * @return \Spryker\Client\MerchantProductOfferStorage\ProductConcreteDefaultProductOffer\ProductConcreteDefaultProductOfferInterface
     */
    public function createProductConcreteDefaultProductOffer(): ProductConcreteDefaultProductOfferInterface
    {
        return new ProductConcreteDefaultProductOffer(
            $this->createProductOfferStorageReader(),
            $this->getDefaultProductOfferPlugin()
        );
    }

    /**
     * @return \Spryker\Client\MerchantProductOfferStorage\Mapper\MerchantProductOfferMapperInterface
     */
    public function createMerchantProductOfferMapper(): MerchantProductOfferMapperInterface
    {
        return new MerchantProductOfferMapper();
    }

    /**
     * @return \Spryker\Client\MerchantProductOfferStorage\Dependency\Client\MerchantProductOfferStorageToStorageClientInterface
     */
    public function getStorageClient(): MerchantProductOfferStorageToStorageClientInterface
    {
        return $this->getProvidedDependency(MerchantProductOfferStorageDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Spryker\Client\MerchantProductOfferStorage\Dependency\Service\MerchantProductOfferStorageToSynchronizationServiceInterface
     */
    public function getSynchronizationService(): MerchantProductOfferStorageToSynchronizationServiceInterface
    {
        return $this->getProvidedDependency(MerchantProductOfferStorageDependencyProvider::SERVICE_SYNCHRONIZATION);
    }

    /**
     * @return \Spryker\Client\MerchantProductOfferStorage\Plugin\ProductOfferProviderPluginInterface
     */
    public function getDefaultProductOfferPlugin(): ProductOfferProviderPluginInterface
    {
        return $this->getProvidedDependency(MerchantProductOfferStorageDependencyProvider::PLUGIN_PRODUCT_OFFER_PLUGIN);
    }
}
