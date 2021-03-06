<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProfile\Persistence;

use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\MerchantProfileCollectionTransfer;
use Generated\Shared\Transfer\MerchantProfileCriteriaFilterTransfer;
use Generated\Shared\Transfer\MerchantProfileTransfer;
use Orm\Zed\MerchantProfile\Persistence\SpyMerchantProfileQuery;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

/**
 * @method \Spryker\Zed\MerchantProfile\Persistence\MerchantProfilePersistenceFactory getFactory()
 */
class MerchantProfileRepository extends AbstractRepository implements MerchantProfileRepositoryInterface
{
    /**
     * @param \Generated\Shared\Transfer\MerchantProfileCriteriaFilterTransfer $merchantProfileCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantProfileTransfer|null
     */
    public function findOne(MerchantProfileCriteriaFilterTransfer $merchantProfileCriteriaFilterTransfer): ?MerchantProfileTransfer
    {
        $merchantProfileEntity = $this->applyFilters(
            $this->getFactory()->createMerchantProfileQuery(),
            $merchantProfileCriteriaFilterTransfer
        )->findOne();

        if (!$merchantProfileEntity) {
            return null;
        }

        return $this->getFactory()
            ->createPropelMerchantProfileMapper()
            ->mapMerchantProfileEntityToMerchantProfileTransfer($merchantProfileEntity, new MerchantProfileTransfer());
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantProfileCriteriaFilterTransfer $merchantProfileCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantProfileCollectionTransfer
     */
    public function find(MerchantProfileCriteriaFilterTransfer $merchantProfileCriteriaFilterTransfer): MerchantProfileCollectionTransfer
    {
        $merchantProfileCollectionTransfer = new MerchantProfileCollectionTransfer();
        $merchantProfileQuery = $this->getFactory()
            ->createMerchantProfileQuery()
            ->joinWithSpyMerchant()
            ->useSpyMerchantProfileAddressQuery(null, Criteria::LEFT_JOIN)
                ->leftJoinWithSpyCountry()
            ->endUse();

        $merchantProfileQuery = $this->applyFilters($merchantProfileQuery, $merchantProfileCriteriaFilterTransfer);
        $merchantProfileQuery = $this->buildQueryFromCriteria($merchantProfileQuery, $merchantProfileCriteriaFilterTransfer->getFilter());
        $merchantProfileEntityCollection = $merchantProfileQuery->find();

        foreach ($merchantProfileEntityCollection as $merchantProfileEntity) {
            $merchantProfileTransfer = $this->getFactory()
                ->createPropelMerchantProfileMapper()
                ->mapMerchantProfileEntityToMerchantProfileTransfer($merchantProfileEntity, new MerchantProfileTransfer());

            $merchantProfileCollectionTransfer->addMerchantProfile($merchantProfileTransfer);
        }

        return $merchantProfileCollectionTransfer;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $criteria
     * @param \Generated\Shared\Transfer\FilterTransfer|null $filterTransfer
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildQueryFromCriteria(ModelCriteria $criteria, ?FilterTransfer $filterTransfer = null): ModelCriteria
    {
        $criteria = parent::buildQueryFromCriteria($criteria, $filterTransfer);
        $criteria->setFormatter(ModelCriteria::FORMAT_OBJECT);

        return $criteria;
    }

    /**
     * @param \Orm\Zed\MerchantProfile\Persistence\SpyMerchantProfileQuery $merchantProfileQuery
     * @param \Generated\Shared\Transfer\MerchantProfileCriteriaFilterTransfer $merchantProfileCriteriaFilterTransfer
     *
     * @return \Orm\Zed\MerchantProfile\Persistence\SpyMerchantProfileQuery
     */
    protected function applyFilters(
        SpyMerchantProfileQuery $merchantProfileQuery,
        MerchantProfileCriteriaFilterTransfer $merchantProfileCriteriaFilterTransfer
    ): SpyMerchantProfileQuery {
        if ($merchantProfileCriteriaFilterTransfer->getFkMerchant() !== null) {
            $merchantProfileQuery->filterByFkMerchant($merchantProfileCriteriaFilterTransfer->getFkMerchant());
        }

        if ($merchantProfileCriteriaFilterTransfer->getMerchantIds()) {
            $merchantProfileQuery->filterByFkMerchant_In($merchantProfileCriteriaFilterTransfer->getMerchantIds());
        }

        if ($merchantProfileCriteriaFilterTransfer->getMerchantProfileIds()) {
            $merchantProfileQuery->filterByIdMerchantProfile_In($merchantProfileCriteriaFilterTransfer->getMerchantProfileIds());
        }

        if ($merchantProfileCriteriaFilterTransfer->getMerchantReference()) {
            $merchantProfileQuery->useSpyMerchantQuery()
                ->filterByMerchantReference($merchantProfileCriteriaFilterTransfer->getMerchantReference())
                ->endUse();
        }

        if ($merchantProfileCriteriaFilterTransfer->getIdMerchantProfile() !== null) {
            $merchantProfileQuery->filterByIdMerchantProfile($merchantProfileCriteriaFilterTransfer->getIdMerchantProfile());
        }

        return $merchantProfileQuery;
    }
}
