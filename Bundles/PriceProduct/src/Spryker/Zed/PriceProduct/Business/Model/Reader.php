<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProduct\Business\Model;

use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\PriceProductCriteriaTransfer;
use Generated\Shared\Transfer\PriceProductDimensionTransfer;
use Generated\Shared\Transfer\PriceProductFilterTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Spryker\Service\PriceProduct\PriceProductServiceInterface;
use Spryker\Zed\PriceProduct\Business\Model\PriceType\PriceProductTypeReaderInterface;
use Spryker\Zed\PriceProduct\Business\Model\Product\PriceProductAbstractReaderInterface;
use Spryker\Zed\PriceProduct\Business\Model\Product\PriceProductConcreteReaderInterface;
use Spryker\Zed\PriceProduct\Business\Model\Product\PriceProductMapperInterface;
use Spryker\Zed\PriceProduct\Dependency\Facade\PriceProductToProductFacadeInterface;
use Spryker\Zed\PriceProduct\PriceProductConfig;

class Reader implements ReaderInterface
{
    /**
     * @var \Spryker\Zed\PriceProduct\Dependency\Facade\PriceProductToProductFacadeInterface
     */
    protected $productFacade;

    /**
     * @var \Spryker\Zed\PriceProduct\Dependency\Facade\PriceProductToPriceFacadeInterface
     */
    protected $priceFacade;

    /**
     * @var \Spryker\Zed\PriceProduct\Business\Model\PriceType\PriceProductTypeReaderInterface
     */
    protected $priceProductTypeReader;

    /**
     * @var \Spryker\Zed\PriceProduct\Business\Model\Product\PriceProductConcreteReaderInterface
     */
    protected $priceProductConcreteReader;

    /**
     * @var \Spryker\Zed\PriceProduct\Business\Model\Product\PriceProductAbstractReaderInterface
     */
    protected $priceProductAbstractReader;

    /**
     * @var \Spryker\Zed\PriceProduct\Business\Model\PriceProductCriteriaBuilderInterface
     */
    protected $priceProductCriteriaBuilder;

    /**
     * @var \Spryker\Zed\PriceProduct\Business\Model\Product\PriceProductMapperInterface
     */
    protected $priceProductMapper;

    /**
     * @var \Spryker\Zed\PriceProduct\PriceProductConfig
     */
    protected $config;

    /**
     * @var \Spryker\Service\PriceProduct\PriceProductServiceInterface
     */
    protected $priceProductService;

    /**
     * @var string|null
     */
    protected static $priceModeIdentifierForNetType;

    /**
     * @var \Generated\Shared\Transfer\PriceProductTransfer[]
     */
    protected static $resolvedPriceProductTransferCollection = [];

    /**
     * @param \Spryker\Zed\PriceProduct\Dependency\Facade\PriceProductToProductFacadeInterface $productFacade
     * @param \Spryker\Zed\PriceProduct\Business\Model\PriceType\PriceProductTypeReaderInterface $priceProductTypeReader
     * @param \Spryker\Zed\PriceProduct\Business\Model\Product\PriceProductConcreteReaderInterface $priceProductConcreteReader
     * @param \Spryker\Zed\PriceProduct\Business\Model\Product\PriceProductAbstractReaderInterface $priceProductAbstractReader
     * @param \Spryker\Zed\PriceProduct\Business\Model\PriceProductCriteriaBuilderInterface $priceProductCriteriaBuilder
     * @param \Spryker\Zed\PriceProduct\Business\Model\Product\PriceProductMapperInterface $priceProductMapper
     * @param \Spryker\Zed\PriceProduct\PriceProductConfig $config
     * @param \Spryker\Service\PriceProduct\PriceProductServiceInterface $priceProductService
     */
    public function __construct(
        PriceProductToProductFacadeInterface $productFacade,
        PriceProductTypeReaderInterface $priceProductTypeReader,
        PriceProductConcreteReaderInterface $priceProductConcreteReader,
        PriceProductAbstractReaderInterface $priceProductAbstractReader,
        PriceProductCriteriaBuilderInterface $priceProductCriteriaBuilder,
        PriceProductMapperInterface $priceProductMapper,
        PriceProductConfig $config,
        PriceProductServiceInterface $priceProductService
    ) {
        $this->productFacade = $productFacade;
        $this->priceProductTypeReader = $priceProductTypeReader;
        $this->priceProductConcreteReader = $priceProductConcreteReader;
        $this->priceProductAbstractReader = $priceProductAbstractReader;
        $this->priceProductCriteriaBuilder = $priceProductCriteriaBuilder;
        $this->priceProductMapper = $priceProductMapper;
        $this->config = $config;
        $this->priceProductService = $priceProductService;
    }

    /**
     * @param string $sku
     * @param string|null $priceTypeName
     *
     * @return int|null
     */
    public function findPriceBySku($sku, $priceTypeName = null)
    {
        $priceProductCriteriaTransfer = $this->priceProductCriteriaBuilder->buildCriteriaWithDefaultValues($priceTypeName);

        $priceProductTransfer = $this->findProductPrice($sku, $priceProductCriteriaTransfer);

        if ($priceProductTransfer === null) {
            return null;
        }

        return $this->getPriceByPriceMode($priceProductTransfer->getMoneyValue(), $priceProductCriteriaTransfer->getPriceMode());
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer $priceProductFilterTransfer
     *
     * @return int|null
     */
    public function findPriceFor(PriceProductFilterTransfer $priceProductFilterTransfer)
    {
        $priceProductTransfer = $this->findPriceProductFor($priceProductFilterTransfer);

        if ($priceProductTransfer === null) {
            return null;
        }

        $priceProductCriteriaTransfer = $this->priceProductCriteriaBuilder->buildCriteriaFromFilter($priceProductFilterTransfer);

        return $this->getPriceByPriceMode($priceProductTransfer->getMoneyValue(), $priceProductCriteriaTransfer->getPriceMode());
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer $priceProductFilterTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer|null
     */
    public function findPriceProductFor(PriceProductFilterTransfer $priceProductFilterTransfer): ?PriceProductTransfer
    {
        $priceProductFilterTransfer->requireSku();

        $priceProductCriteriaTransfer = $this->priceProductCriteriaBuilder->buildCriteriaFromFilter($priceProductFilterTransfer);

        return $this->findProductPrice($priceProductFilterTransfer->getSku(), $priceProductCriteriaTransfer);
    }

    /**
     * @param int $idProductConcrete
     * @param int $idProductAbstract
     * @param \Generated\Shared\Transfer\PriceProductCriteriaTransfer|null $priceProductCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]
     */
    public function findProductConcretePrices(
        int $idProductConcrete,
        int $idProductAbstract,
        ?PriceProductCriteriaTransfer $priceProductCriteriaTransfer = null
    ): array {
        $abstractPriceProductTransfers = $this->priceProductAbstractReader
            ->findProductAbstractPricesById($idProductAbstract, $priceProductCriteriaTransfer);

        $concretePriceProductTransfers = $this->priceProductConcreteReader
            ->findProductConcretePricesById($idProductConcrete, $priceProductCriteriaTransfer);

        return $this->mergeConcreteAndAbstractPrices($abstractPriceProductTransfers, $concretePriceProductTransfers);
    }

    /**
     * @param string $sku
     * @param string|null $priceTypeName
     *
     * @return bool
     */
    public function hasValidPrice($sku, $priceTypeName = null)
    {
        $priceTypeName = $this->priceProductTypeReader->handleDefaultPriceType($priceTypeName);

        if (!$this->priceProductTypeReader->hasPriceType($priceTypeName)) {
            return false;
        }

        $priceProductCriteriaTransfer = $this->priceProductCriteriaBuilder->buildCriteriaWithDefaultValues();

        return $this->isValidPrice($sku, $priceProductCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer $priceProductFilterTransfer
     *
     * @return bool
     */
    public function hasValidPriceFor(PriceProductFilterTransfer $priceProductFilterTransfer)
    {
        $priceProductFilterTransfer->requireSku();

        $priceTypeName = $this->priceProductTypeReader->handleDefaultPriceType(
            $priceProductFilterTransfer->getPriceTypeName()
        );

        if (!$this->priceProductTypeReader->hasPriceType($priceTypeName)) {
            return false;
        }

        $priceProductCriteriaTransfer = $this->priceProductCriteriaBuilder->buildCriteriaFromFilter($priceProductFilterTransfer);

        return $this->isValidPrice($priceProductFilterTransfer->getSku(), $priceProductCriteriaTransfer);
    }

    /**
     * @param string $sku
     * @param string $priceTypeName
     * @param string $currencyIsoCode
     *
     * @return int
     */
    public function getProductPriceIdBySku($sku, $priceTypeName, $currencyIsoCode)
    {
        $priceProductCriteriaTransfer = $this->priceProductCriteriaBuilder->buildCriteriaWithDefaultValues();

        if ($this->priceProductConcreteReader->hasPriceForProductConcrete($sku, $priceProductCriteriaTransfer)) {
            return $this->priceProductConcreteReader->findPriceProductId($sku, $priceProductCriteriaTransfer);
        }

        if (!$this->priceProductAbstractReader->hasPriceForProductAbstract($sku, $priceProductCriteriaTransfer)) {
            $sku = $this->productFacade->getAbstractSkuFromProductConcrete($sku);
        }

        return $this->priceProductAbstractReader->findPriceProductId($sku, $priceProductCriteriaTransfer);
    }

    /**
     * @param string $sku
     * @param \Generated\Shared\Transfer\PriceProductDimensionTransfer|null $priceProductDimensionTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]
     */
    public function findPricesBySkuForCurrentStore(
        string $sku,
        ?PriceProductDimensionTransfer $priceProductDimensionTransfer = null
    ): array {
        $priceProductDimensionTransfer = $this->getPriceProductDimensionTransfer($priceProductDimensionTransfer);

        $abstractPriceProductTransfers = $this->priceProductAbstractReader
            ->findProductAbstractPricesBySkuForCurrentStore($sku, $priceProductDimensionTransfer);

        $concretePriceProductTransfers = $this->priceProductConcreteReader
            ->findProductConcretePricesBySkuForCurrentStore($sku, $priceProductDimensionTransfer);

        if (!$concretePriceProductTransfers) {
            return $abstractPriceProductTransfers;
        }

        if (!$abstractPriceProductTransfers) {
            return $concretePriceProductTransfers;
        }

        return $this->mergeConcreteAndAbstractPrices($abstractPriceProductTransfers, $concretePriceProductTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer[] $abstractPriceProductTransfers
     * @param \Generated\Shared\Transfer\PriceProductTransfer[] $concretePriceProductTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]
     */
    protected function mergeConcreteAndAbstractPrices(
        array $abstractPriceProductTransfers,
        array $concretePriceProductTransfers
    ) {
        $priceProductTransfers = [];
        foreach ($abstractPriceProductTransfers as $priceProductAbstractTransfer) {
            $abstractKey = $this->buildPriceProductIdentifier($priceProductAbstractTransfer);

            $priceProductTransfers = $this->mergeConcreteProduct(
                $concretePriceProductTransfers,
                $abstractKey,
                $priceProductAbstractTransfer,
                $priceProductTransfers
            );

            if (!isset($priceProductTransfers[$abstractKey])) {
                $priceProductTransfers[$abstractKey] = $priceProductAbstractTransfer;
            }
        }

        return $this->addConcreteNotMergedPrices($concretePriceProductTransfers, $priceProductTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer[] $concretePriceProductTransfers
     * @param \Generated\Shared\Transfer\PriceProductTransfer[] $priceProductTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]
     */
    protected function addConcreteNotMergedPrices(array $concretePriceProductTransfers, array $priceProductTransfers)
    {
        foreach ($concretePriceProductTransfers as $priceProductConcreteTransfer) {
            $concreteKey = $this->buildPriceProductIdentifier($priceProductConcreteTransfer);

            if (isset($priceProductTransfers[$concreteKey])) {
                continue;
            }

            $priceProductTransfers[$concreteKey] = $priceProductConcreteTransfer;
        }

        return $priceProductTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer[] $concretePriceProductTransfers
     * @param string $abstractKey
     * @param \Generated\Shared\Transfer\PriceProductTransfer $priceProductAbstractTransfer
     * @param \Generated\Shared\Transfer\PriceProductTransfer[] $priceProductTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]
     */
    protected function mergeConcreteProduct(
        array $concretePriceProductTransfers,
        $abstractKey,
        PriceProductTransfer $priceProductAbstractTransfer,
        array $priceProductTransfers
    ) {
        foreach ($concretePriceProductTransfers as $priceProductConcreteTransfer) {
            $concreteKey = $this->buildPriceProductIdentifier($priceProductConcreteTransfer);

            if ($abstractKey !== $concreteKey) {
                continue;
            }

            $priceProductTransfers[$concreteKey] = $this->resolveConcreteProductPrice(
                $priceProductAbstractTransfer,
                $priceProductConcreteTransfer
            );
        }

        if (!isset($priceProductTransfers[$abstractKey])) {
            $priceProductAbstractTransfer->getPriceDimension()->setIdPriceProductDefault(null);
            $priceProductAbstractTransfer->getMoneyValue()->setIdEntity(null);
            $priceProductTransfers[$abstractKey] = $priceProductAbstractTransfer;
        }

        return $priceProductTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer $priceProductAbstractTransfer
     * @param \Generated\Shared\Transfer\PriceProductTransfer $priceProductConcreteTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer
     */
    protected function resolveConcreteProductPrice(
        PriceProductTransfer $priceProductAbstractTransfer,
        PriceProductTransfer $priceProductConcreteTransfer
    ) {

        $abstractMoneyValueTransfer = $priceProductAbstractTransfer->getMoneyValue();
        $concreteMoneyValueTransfer = $priceProductConcreteTransfer->getMoneyValue();

        if ($concreteMoneyValueTransfer->getGrossAmount() === null) {
            $concreteMoneyValueTransfer->setGrossAmount($abstractMoneyValueTransfer->getGrossAmount());
        }

        if ($concreteMoneyValueTransfer->getNetAmount() === null) {
            $concreteMoneyValueTransfer->setNetAmount($abstractMoneyValueTransfer->getNetAmount());
        }

        return $priceProductConcreteTransfer;
    }

    /**
     * @param string $sku
     * @param \Generated\Shared\Transfer\PriceProductCriteriaTransfer $priceProductCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer|null
     */
    protected function findProductPrice(string $sku, PriceProductCriteriaTransfer $priceProductCriteriaTransfer): ?PriceProductTransfer
    {
        $priceProductConcreteTransfers = $this->priceProductConcreteReader
            ->findProductConcretePricesBySkuAndCriteria($sku, $priceProductCriteriaTransfer);

        if ($this->productFacade->hasProductConcrete($sku)) {
            $sku = $this->productFacade->getAbstractSkuFromProductConcrete($sku);
        }

        $priceProductAbstractTransfers = $this->priceProductAbstractReader
            ->findProductAbstractPricesBySkuAndCriteria($sku, $priceProductCriteriaTransfer);

        if (!$priceProductConcreteTransfers) {
            return $this->priceProductService
                ->resolveProductPriceByPriceProductCriteria($priceProductAbstractTransfers, $priceProductCriteriaTransfer);
        }

        $priceProductTransfers = $this->priceProductService
            ->mergeConcreteAndAbstractPrices($priceProductAbstractTransfers, $priceProductConcreteTransfers);

        return $this->priceProductService
            ->resolveProductPriceByPriceProductCriteria($priceProductTransfers, $priceProductCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MoneyValueTransfer $moneyValueTransfer
     * @param string $priceMode
     *
     * @return int|null
     */
    protected function getPriceByPriceMode(MoneyValueTransfer $moneyValueTransfer, string $priceMode): ?int
    {
        if ($priceMode === $this->priceProductMapper->getNetPriceModeIdentifier()) {
            return $moneyValueTransfer->getNetAmount();
        }

        return $moneyValueTransfer->getGrossAmount();
    }

    /**
     * @param string $sku
     * @param \Generated\Shared\Transfer\PriceProductCriteriaTransfer $priceProductCriteriaTransfer
     *
     * @return bool
     */
    protected function isValidPrice($sku, PriceProductCriteriaTransfer $priceProductCriteriaTransfer)
    {
        if ($this->priceProductConcreteReader->hasPriceForProductConcrete($sku, $priceProductCriteriaTransfer)) {
            return true;
        }

        $abstractSku = $this->productFacade->getAbstractSkuFromProductConcrete($sku);
        if ($this->priceProductAbstractReader->hasPriceForProductAbstract($abstractSku, $priceProductCriteriaTransfer)) {
            return true;
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductDimensionTransfer|null $priceProductDimensionTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductDimensionTransfer
     */
    protected function getPriceProductDimensionTransfer(?PriceProductDimensionTransfer $priceProductDimensionTransfer = null): PriceProductDimensionTransfer
    {
        if (!$priceProductDimensionTransfer) {
            $priceProductDimensionTransfer = (new PriceProductDimensionTransfer())
                ->setType($this->config->getPriceDimensionDefault());
        }

        return $priceProductDimensionTransfer;
    }

    /**
     * @param int $idProductConcrete
     * @param int $idProductAbstract
     * @param \Generated\Shared\Transfer\PriceProductCriteriaTransfer|null $priceProductCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]
     */
    public function findProductConcretePricesWithoutPriceExtraction(
        int $idProductConcrete,
        int $idProductAbstract,
        ?PriceProductCriteriaTransfer $priceProductCriteriaTransfer = null
    ): array {
        $abstractPriceProductTransfers = $this->priceProductAbstractReader
            ->findProductAbstractPricesWithoutPriceExtraction($idProductAbstract, $priceProductCriteriaTransfer);

        $concretePriceProductTransfers = $this->priceProductConcreteReader
            ->findProductConcretePricesWithoutPriceExtraction($idProductConcrete, $priceProductCriteriaTransfer);

        return $this->mergeConcreteAndAbstractPrices($abstractPriceProductTransfers, $concretePriceProductTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer $priceProductTransfer
     *
     * @return string
     */
    protected function buildPriceProductIdentifier(PriceProductTransfer $priceProductTransfer): string
    {
        $moneyValueTransfer = $priceProductTransfer->requireMoneyValue()->getMoneyValue();
        $priceTypeTransfer = $priceProductTransfer->requirePriceType()->getPriceType();

        return implode(
            '-',
            [
                $moneyValueTransfer->getFkStore(),
                $moneyValueTransfer->getFkCurrency(),
                $priceTypeTransfer->getName(),
                $priceTypeTransfer->getPriceModeConfiguration(),
            ]
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]
     */
    public function getValidPrices(array $priceProductFilterTransfers): array
    {
        if (count($priceProductFilterTransfers) === 0) {
            return [];
        }
        $priceProductFilterTransfers = $this->filterPriceProductFilterTransfersWithoutExistingPriceType($priceProductFilterTransfers);
        if (!$priceProductFilterTransfers) {
            return [];
        }

        $concretePricesBySku = $this->findPricesForConcreteProducts($priceProductFilterTransfers);
        $abstractPricesBySku = $this->findPricesForAbstractProducts(
            $this->getProductConcreteSkus($priceProductFilterTransfers),
            $priceProductFilterTransfers
        );

        return $this->resolveProductPrices(
            $this->mergeIndexedPriceProductTransfers($abstractPricesBySku, $concretePricesBySku),
            $priceProductFilterTransfers
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer[][] $indexedAbstractPriceProductTransfers
     * @param \Generated\Shared\Transfer\PriceProductTransfer[][] $indexedConcretePriceProductTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[][]
     */
    protected function mergeIndexedPriceProductTransfers(array $indexedAbstractPriceProductTransfers, array $indexedConcretePriceProductTransfers): array
    {
        $mergedPriceProductTransfers = [];

        foreach ($indexedAbstractPriceProductTransfers as $sku => $abstractPriceProductTransfers) {
            $mergedPriceProductTransfers[$sku] = $this->priceProductService->mergeConcreteAndAbstractPrices(
                $abstractPriceProductTransfers,
                $indexedConcretePriceProductTransfers[$sku] ?? []
            );
        }

        foreach ($indexedConcretePriceProductTransfers as $sku => $concretePriceProductTransfers) {
            $mergedPriceProductTransfers[$sku] = $this->priceProductService->mergeConcreteAndAbstractPrices(
                $indexedAbstractPriceProductTransfers[$sku] ?? [],
                $concretePriceProductTransfers
            );
        }

        return $mergedPriceProductTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer[][] $priceProductTransfers
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[]
     */
    protected function resolveProductPrices(array $priceProductTransfers, array $priceProductFilterTransfers): array
    {
        $priceProductCriteriaTransfers = $this
            ->priceProductCriteriaBuilder
            ->buildCriteriaTransfersFromFilterTransfers($priceProductFilterTransfers);

        $resolvedPriceProductTransfers = [];
        foreach ($priceProductCriteriaTransfers as $index => $priceProductCriteriaTransfer) {
            $priceProductCriteriaIdentifier = spl_object_hash($priceProductFilterTransfers[$index]);
            $resolvedItemPrice = $this->resolveProductPriceByPriceProductCriteria(
                $priceProductCriteriaIdentifier,
                $priceProductTransfers,
                $priceProductCriteriaTransfer
            );

            if ($this->isPriceProductHasValidMoneyValue($resolvedItemPrice, $priceProductFilterTransfers)) {
                $resolvedPriceProductTransfers[] = $resolvedItemPrice;
            }
        }

        return $resolvedPriceProductTransfers;
    }

    /**
     * @param string $priceProductCriteriaIdentifier
     * @param array $priceProductTransfers
     * @param \Generated\Shared\Transfer\PriceProductCriteriaTransfer $priceProductCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer|null
     */
    protected function resolveProductPriceByPriceProductCriteria(
        string $priceProductCriteriaIdentifier,
        array $priceProductTransfers,
        PriceProductCriteriaTransfer $priceProductCriteriaTransfer
    ): ?PriceProductTransfer {
        if (!isset(static::$resolvedPriceProductTransferCollection[$priceProductCriteriaIdentifier])) {
            static::$resolvedPriceProductTransferCollection[$priceProductCriteriaIdentifier] = $this->priceProductService->resolveProductPriceByPriceProductCriteria(
                $priceProductTransfers[$priceProductCriteriaIdentifier],
                $priceProductCriteriaTransfer
            );
        }

        return static::$resolvedPriceProductTransferCollection[$priceProductCriteriaIdentifier];
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer|null $priceProductTransfer
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     *
     * @return bool
     */
    protected function isPriceProductHasValidMoneyValue(
        ?PriceProductTransfer $priceProductTransfer,
        array $priceProductFilterTransfers
    ): bool {
        if ($priceProductTransfer === null) {
            return false;
        }

        $priceModeIdentifierForNetType = $this->getPriceModeIdentifierForNetType();

        foreach ($priceProductFilterTransfers as $priceProductFilterTransfer) {
            $moneyValueTransfer = $priceProductTransfer->getMoneyValue();

            if ($priceProductFilterTransfer->getPriceMode() === $priceModeIdentifierForNetType) {
                if ($moneyValueTransfer->getNetAmount() === null) {
                    return false;
                }

                continue;
            }

            if ($moneyValueTransfer->getGrossAmount() === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getPriceModeIdentifierForNetType(): string
    {
        if (static::$priceModeIdentifierForNetType === null) {
            static::$priceModeIdentifierForNetType = $this->config->getPriceModeIdentifierForNetType();
        }

        return static::$priceModeIdentifierForNetType;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[][]
     */
    protected function findPricesForConcreteProducts(array $priceProductFilterTransfers): array
    {
        $priceProductFilterTransfer = $this->getCommonPriceProductFilterTransfer($priceProductFilterTransfers);
        $priceProductCriteriaTransfer = $this->priceProductCriteriaBuilder->buildCriteriaFromFilter($priceProductFilterTransfer);
        $productConcreteSkus = array_map(function (PriceProductFilterTransfer $priceProductFilterTransfer) {
            return $priceProductFilterTransfer->getSku();
        }, $priceProductFilterTransfers);

        $concretePriceProductTransfers = $this->priceProductConcreteReader->getProductConcretePricesByConcreteSkusAndCriteria(
            $productConcreteSkus,
            $priceProductCriteriaTransfer
        );

        return $this->groupPriceProductTransfersByFilter($priceProductFilterTransfers, $concretePriceProductTransfers);
    }

    /**
     * @param string[] $productConcreteSkus
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[][]
     */
    protected function findPricesForAbstractProducts(array $productConcreteSkus, array $priceProductFilterTransfers): array
    {
        $priceProductFilterTransfer = $this->getCommonPriceProductFilterTransfer($priceProductFilterTransfers);
        $priceProductCriteriaTransfer = $this->priceProductCriteriaBuilder->buildCriteriaFromFilter($priceProductFilterTransfer);

        $priceProductTransfers = $this->priceProductAbstractReader->getProductAbstractPricesByConcreteSkusAndCriteria(
            $productConcreteSkus,
            $priceProductCriteriaTransfer
        );

        return $this->groupPriceProductTransfersByFilter($priceProductFilterTransfers, $priceProductTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     * @param \Generated\Shared\Transfer\PriceProductTransfer[] $priceProductTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer[][]
     */
    protected function groupPriceProductTransfersByFilter(array $priceProductFilterTransfers, array $priceProductTransfers): array
    {
        $priceProductTransfersGroupedByFilterIdentifier = [];

        foreach ($priceProductFilterTransfers as $priceProductFilterTransfer) {
            $priceProductFilterIdentifier = spl_object_hash($priceProductFilterTransfer);
            $priceProductTransfersGroupedByFilterIdentifier[$priceProductFilterIdentifier] = $this->priceProductService->resolveProductPricesByPriceProductFilter(
                $priceProductTransfers,
                $priceProductFilterTransfer
            );
        }

        return $priceProductTransfersGroupedByFilterIdentifier;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     *
     * @return string[]
     */
    protected function getProductConcreteSkus(array $priceProductFilterTransfers): array
    {
        $productConcreteSkus = [];

        foreach ($priceProductFilterTransfers as $priceProductFilterTransfer) {
            $productConcreteSkus[] = $priceProductFilterTransfer->getSku();
        }

        return $productConcreteSkus;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductFilterTransfer[]
     */
    protected function getPriceProductFilterTransfersBySku(array $priceProductFilterTransfers): array
    {
        $priceProductFilterTransfersBySku = [];
        foreach ($priceProductFilterTransfers as $priceProductFilterTransfer) {
            $priceProductFilterTransfersBySku[$priceProductFilterTransfer->getSku()] = $priceProductFilterTransfer;
        }

        return $priceProductFilterTransfersBySku;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductFilterTransfer
     */
    protected function getCommonPriceProductFilterTransfer(array $priceProductFilterTransfers): PriceProductFilterTransfer
    {
        return array_shift($priceProductFilterTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer[] $priceProductFilterTransfers
     *
     * @return \Generated\Shared\Transfer\PriceProductFilterTransfer[]
     */
    protected function filterPriceProductFilterTransfersWithoutExistingPriceType(array $priceProductFilterTransfers): array
    {
        $filteredPriceProductFilterTransfers = [];
        foreach ($priceProductFilterTransfers as $priceProductFilterTransfer) {
            $priceTypeName = $this->priceProductTypeReader->handleDefaultPriceType(
                $priceProductFilterTransfer->getPriceTypeName()
            );
            if ($this->priceProductTypeReader->hasPriceType($priceTypeName)) {
                $filteredPriceProductFilterTransfers[] = $priceProductFilterTransfer;
            }
        }

        return $filteredPriceProductFilterTransfers;
    }
}
