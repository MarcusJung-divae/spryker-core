<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\Storage;

use Codeception\Test\Unit;
use Spryker\Client\Storage\StorageClient;
use Spryker\Client\StorageExtension\Dependency\Plugin\StoragePluginInterface;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Storage\StorageConstants;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group Storage
 * @group StorageClientTest
 * Add your own group annotations below this line
 */
class StorageClientTest extends Unit
{
    public const STORAGE_CACHE_STRATEGY = StorageConstants::STORAGE_CACHE_STRATEGY_REPLACE;

    /**
     * @var \Spryker\Client\Storage\StorageClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $storageClientMock;

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $storageClient = $this->createStorageClient();
        $storageClient::$service = null;
    }

    /**
     * @param string $uri
     * @param string $expectedCacheKey
     * @param array $getParameters
     *
     * @return void
     */
    protected function testStorageCacheAllowedGetParameters(
        string $uri,
        string $expectedCacheKey,
        array $getParameters = []
    ): void {
        $request = new Request();
        $request->server->set('SERVER_NAME', 'localhost');
        $request->server->set('REQUEST_URI', $uri);
        $request->query = new ParameterBag($getParameters);

        $this->storageClientMock->expects($this->once())
            ->method('updateCache')
            ->with(
                $this->equalTo(self::STORAGE_CACHE_STRATEGY),
                $this->equalTo($expectedCacheKey)
            );

        $this->storageClientMock->persistCacheForRequest(
            $request,
            static::STORAGE_CACHE_STRATEGY
        );
    }

    /**
     * @return string
     */
    protected function getStoreAndLocale(): string
    {
        return strtolower(Store::getInstance()->getStoreName()) . '.' .
            strtolower(Store::getInstance()->getCurrentLocale()) . '.';
    }

    /**
     * @return void
     */
    public function testGenerateCacheKeyWithNoGetParameter(): void
    {
        $this->markTestSkipped(
            'This test will be updated in the next major.'
        );

        $uri = '/en/cameras-&-camcorders';
        $expectedCacheKey = $this->getStoreAndLocale() . 'storage:/en/cameras-&-camcorders';

        $this->testStorageCacheAllowedGetParameters($uri, $expectedCacheKey);
    }

    /**
     * @return void
     */
    public function testGenerateCacheKeyWithOneAllowedGetParameterAndOneIsGiven(): void
    {
        $this->markTestSkipped(
            'This test will be updated in the next major.'
        );

        $uri = '/en/cameras-&-camcorders?allowedParameter1=1';
        $expectedCacheKey = $this->getStoreAndLocale() . 'storage:/en/cameras-&-camcorders?allowedParameter1=1';
        $getParameters = ['allowedParameter1' => '1'];

        $this->testStorageCacheAllowedGetParameters(
            $uri,
            $expectedCacheKey,
            $getParameters
        );
    }

    /**
     * @return void
     */
    public function testGenerateCacheKeyWithTwoAllowedGetParameterAndOneIsGiven(): void
    {
        $this->markTestSkipped(
            'This test will be updated in the next major.'
        );

        $uri = '/en/cameras-&-camcorders?allowedParameter1=1';
        $expectedCacheKey = $this->getStoreAndLocale() . 'storage:/en/cameras-&-camcorders?allowedParameter1=1';
        $getParameters = ['allowedParameter1' => '1'];

        $this->testStorageCacheAllowedGetParameters(
            $uri,
            $expectedCacheKey,
            $getParameters
        );
    }

    /**
     * @return void
     */
    public function testGenerateCacheKeyWithOneAllowedGetParameterAndTwoAreGiven(): void
    {
        $this->markTestSkipped(
            'This test will be updated in the next major.'
        );

        $uri = '/en/cameras-&-camcorders?allowedParameter1=1&allowedParameter2=2';
        $expectedCacheKey = $this->getStoreAndLocale() . 'storage:/en/cameras-&-camcorders?allowedParameter1=1';
        $getParameters = ['allowedParameter1' => '1', 'allowedParameter2' => '2'];

        $this->testStorageCacheAllowedGetParameters(
            $uri,
            $expectedCacheKey,
            $getParameters
        );
    }

    /**
     * @return void
     */
    public function testGenerateCacheKeyWithTwoAllowedGetParameterAndTwoOrderedAreGiven(): void
    {
        $this->markTestSkipped(
            'This test will be updated in the next major.'
        );

        $uri = '/en/cameras-&-camcorders?allowedParameter1=1&allowedParameter2=2';
        $expectedCacheKey = $this->getStoreAndLocale() . 'storage:/en/cameras-&-camcorders?allowedParameter1=1&allowedParameter2=2';
        $getParameters = ['allowedParameter1' => '1', 'allowedParameter2' => '2'];

        $this->testStorageCacheAllowedGetParameters(
            $uri,
            $expectedCacheKey,
            $getParameters
        );
    }

    /**
     * @return void
     */
    public function testGenerateCacheKeyWithTwoAllowedGetParameterAndTwoNotOrderedAreGiven(): void
    {
        $this->markTestSkipped(
            'This test will be updated in the next major.'
        );

        $uri = '/en/cameras-&-camcorders?allowedParameter2=2&allowedParameter1=1';
        $expectedCacheKey = $this->getStoreAndLocale() . 'storage:/en/cameras-&-camcorders?allowedParameter1=1&allowedParameter2=2';
        $getParameters = ['allowedParameter1' => '1', 'allowedParameter2' => '2'];

        $this->testStorageCacheAllowedGetParameters(
            $uri,
            $expectedCacheKey,
            $getParameters
        );
    }

    /**
     * @return void
     */
    public function testInvalidStorageScanPluginInterfaceExceptionThrown(): void
    {
        $this->expectException('Spryker\Client\Storage\Exception\InvalidStorageScanPluginInterfaceException');
        /** @var \Spryker\Client\StorageExtension\Dependency\Plugin\StoragePluginInterface|\PHPUnit\Framework\MockObject\MockObject $storagePluginMock */
        $storagePluginMock = $this->getMockBuilder(StoragePluginInterface::class)->getMock();

        $storageClient = $this->createStorageClient();
        $storageClient::$service = $storagePluginMock;

        $storageClient->scanKeys('*', 100);
    }

    /**
     * @return \Spryker\Client\Storage\StorageClient
     */
    protected function createStorageClient(): StorageClient
    {
        return new StorageClient();
    }
}
