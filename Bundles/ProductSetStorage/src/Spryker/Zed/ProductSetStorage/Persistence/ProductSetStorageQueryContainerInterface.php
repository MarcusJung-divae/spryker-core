<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetStorage\Persistence;

use Orm\Zed\ProductSet\Persistence\SpyProductSetDataQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface ProductSetStorageQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @api
     *
     * @deprecated Use {@link queryProductSetDataByProductSetIds()} instead.
     *
     * @param array $productSetIds
     *
     * @return \Orm\Zed\ProductSet\Persistence\SpyProductSetDataQuery
     */
    public function queryProductSetDataByIds(array $productSetIds);

    /**
     * @api
     *
     * @param int[] $productSetIds
     *
     * @return \Orm\Zed\ProductSet\Persistence\SpyProductSetDataQuery
     */
    public function queryProductSetDataByProductSetIds(array $productSetIds): SpyProductSetDataQuery;

    /**
     * @api
     *
     * @param array $productSetIds
     *
     * @return \Orm\Zed\ProductSetStorage\Persistence\SpyProductSetStorageQuery
     */
    public function queryProductSetStorageByIds(array $productSetIds);

    /**
     * @api
     *
     * @param array $productImageIds
     *
     * @return \Orm\Zed\ProductImage\Persistence\SpyProductImageSetToProductImageQuery
     */
    public function queryProductSetIdsByProductImageIds(array $productImageIds);

    /**
     * @api
     *
     * @param array $productImageSetToProductImageIds
     *
     * @return \Orm\Zed\ProductImage\Persistence\SpyProductImageSetToProductImageQuery
     */
    public function queryProductSetIdsByProductImageSetToProductImageIds(array $productImageSetToProductImageIds);

    /**
     * @api
     *
     * @param int[] $productSetIds
     *
     * @return \Orm\Zed\ProductSet\Persistence\SpyProductSetQuery
     */
    public function queryProductSetByIds($productSetIds);
}
