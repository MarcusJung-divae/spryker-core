<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryPageSearch\Persistence;

use Orm\Zed\Category\Persistence\SpyCategoryNodeQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface CategoryPageSearchQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @api
     *
     * @param array $localeNames
     *
     * @return \Orm\Zed\Locale\Persistence\SpyLocaleQuery
     */
    public function queryLocalesWithLocaleNames(array $localeNames);

    /**
     * @api
     *
     * @param array $categoryNodeIds
     * @param int $idLocale
     *
     * @return \Orm\Zed\Category\Persistence\SpyCategoryNodeQuery
     */
    public function queryCategoryNodeTree(array $categoryNodeIds, $idLocale);

    /**
     * @api
     *
     * @param int[] $categoryNodeIds
     * @param int $idLocale
     *
     * @return \Orm\Zed\Category\Persistence\SpyCategoryNodeQuery
     */
    public function queryWholeCategoryNodeTree(array $categoryNodeIds, int $idLocale): SpyCategoryNodeQuery;

    /**
     * @api
     *
     * @return \Orm\Zed\Category\Persistence\SpyCategoryNodeQuery
     */
    public function queryCategoryRoot();

    /**
     * @api
     *
     * @param int[] $categoryIds
     *
     * @return \Orm\Zed\Category\Persistence\SpyCategoryNodeQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function queryCategoryNodeIdsByCategoryIds(array $categoryIds);

    /**
     * @api
     *
     * @param int[] $categoryNodeIds
     *
     * @return \Orm\Zed\CategoryPageSearch\Persistence\SpyCategoryNodePageSearchQuery
     */
    public function queryCategoryNodePageSearchByIds(array $categoryNodeIds);

    /**
     * @api
     *
     * @param int[] $categoryTemplateIds
     *
     * @return \Orm\Zed\Category\Persistence\SpyCategoryNodeQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function queryCategoryNodeIdsByTemplateIds(array $categoryTemplateIds);

    /**
     * @api
     *
     * @param int[] $ids
     *
     * @return \Orm\Zed\Category\Persistence\SpyCategoryNodeQuery
     */
    public function queryCategoryNodesByIds(array $ids): SpyCategoryNodeQuery;
}
