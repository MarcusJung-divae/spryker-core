<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\SearchElasticsearch\Index;

use Spryker\Shared\SearchElasticsearch\Dependency\Client\SearchElasticsearchToStoreClientInterface;

class IndexNameResolver implements IndexNameResolverInterface
{
    /**
     * @var \Spryker\Shared\SearchElasticsearch\Dependency\Client\SearchElasticsearchToStoreClientInterface
     */
    protected $storeClient;

    /**
     * @var string|null
     */
    protected static $storeName;

    /**
     * @param \Spryker\Shared\SearchElasticsearch\Dependency\Client\SearchElasticsearchToStoreClientInterface $storeClient
     */
    public function __construct(SearchElasticsearchToStoreClientInterface $storeClient)
    {
        $this->storeClient = $storeClient;
    }

    /**
     * @param string $sourceIdentifier
     *
     * @return string
     */
    public function resolve(string $sourceIdentifier): string
    {
        $indexName = sprintf(
            '%s_%s',
            $this->getStoreName(),
            $sourceIdentifier
        );

        return mb_strtolower($indexName);
    }

    /**
     * @return string
     */
    protected function getStoreName(): string
    {
        if (static::$storeName === null) {
            $storeTransfer = $this->storeClient->getCurrentStore();

            static::$storeName = $storeTransfer->requireName()->getName();
        }

        return static::$storeName;
    }
}
