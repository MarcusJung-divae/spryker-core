<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Table;

use Orm\Zed\Customer\Persistence\Map\SpyCustomerAddressTableMap;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Customer\Communication\Table\PluginExecutor\CustomerTableExpanderPluginExecutorInterface;
use Spryker\Zed\Customer\Dependency\Service\CustomerToUtilDateTimeServiceInterface;
use Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class CustomerTable extends AbstractTable
{
    public const ACTIONS = 'Actions';

    public const COL_ZIP_CODE = 'zip_code';
    public const COL_CITY = 'city';
    public const COL_FK_COUNTRY = 'country';
    public const COL_CREATED_AT = 'created_at';
    public const COL_ID_CUSTOMER = 'id_customer';
    public const COL_EMAIL = 'email';
    public const COL_FIRST_NAME = 'first_name';
    public const COL_LAST_NAME = 'last_name';

    /**
     * @var \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface
     */
    protected $customerQueryContainer;

    /**
     * @var \Spryker\Zed\Customer\Dependency\Service\CustomerToUtilDateTimeServiceInterface
     */
    protected $utilDateTimeService;

    /**
     * @var \Spryker\Zed\Customer\Communication\Table\PluginExecutor\CustomerTableExpanderPluginExecutorInterface
     */
    protected $customerTableExpanderPluginExecutor;

    /**
     * @param \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface $customerQueryContainer
     * @param \Spryker\Zed\Customer\Dependency\Service\CustomerToUtilDateTimeServiceInterface $utilDateTimeService
     * @param \Spryker\Zed\Customer\Communication\Table\PluginExecutor\CustomerTableExpanderPluginExecutorInterface $customerTableExpanderPluginExecutor
     */
    public function __construct(
        CustomerQueryContainerInterface $customerQueryContainer,
        CustomerToUtilDateTimeServiceInterface $utilDateTimeService,
        CustomerTableExpanderPluginExecutorInterface $customerTableExpanderPluginExecutor
    ) {
        $this->customerQueryContainer = $customerQueryContainer;
        $this->utilDateTimeService = $utilDateTimeService;
        $this->customerTableExpanderPluginExecutor = $customerTableExpanderPluginExecutor;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            self::COL_ID_CUSTOMER => '#',
            self::COL_CREATED_AT => 'Registration Date',
            self::COL_EMAIL => 'Email',
            self::COL_LAST_NAME => 'Last Name',
            self::COL_FIRST_NAME => 'First Name',
            self::COL_ZIP_CODE => 'Zip Code',
            self::COL_CITY => 'City',
            self::COL_FK_COUNTRY => 'Country',
            self::ACTIONS => self::ACTIONS,
        ]);

        $config->addRawColumn(self::ACTIONS);

        $config->setSortable([
            self::COL_ID_CUSTOMER,
            self::COL_CREATED_AT,
            self::COL_EMAIL,
            self::COL_LAST_NAME,
            self::COL_FIRST_NAME,
            self::COL_ZIP_CODE,
            self::COL_CITY,
        ]);

        $config->setUrl('table');

        $config->setSearchable([
            SpyCustomerTableMap::COL_ID_CUSTOMER,
            SpyCustomerTableMap::COL_EMAIL,
            SpyCustomerTableMap::COL_CREATED_AT,
            SpyCustomerTableMap::COL_FIRST_NAME,
            SpyCustomerTableMap::COL_LAST_NAME,
            SpyCustomerAddressTableMap::COL_ZIP_CODE,
            SpyCustomerAddressTableMap::COL_CITY,
        ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this->prepareQuery();

        $customersCollection = $this->runQuery($query, $config, true);

        if ($customersCollection->count() < 1) {
            return [];
        }

        return $this->formatCustomerCollection($customersCollection);
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer|null $customer
     *
     * @return string
     */
    protected function buildLinks(?SpyCustomer $customer = null)
    {
        if ($customer === null) {
            return '';
        }

        $buttons = [];
        $buttons[] = $this->generateViewButton('/customer/view?id-customer=' . $customer->getIdCustomer(), 'View');
        $buttons[] = $this->generateEditButton('/customer/edit?id-customer=' . $customer->getIdCustomer(), 'Edit');

        $buttons = $this->expandLinks($buttons, $customer);

        return implode(' ', $buttons);
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $customersCollection
     *
     * @return array
     */
    protected function formatCustomerCollection(ObjectCollection $customersCollection)
    {
        $customersList = [];

        foreach ($customersCollection as $customer) {
            $customersList[] = $this->hydrateCustomerListRow($customer);
        }

        return $customersList;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customer
     *
     * @return array
     */
    protected function hydrateCustomerListRow(SpyCustomer $customer)
    {
        $customerRow = $customer->toArray();

        $customerRow[self::COL_FK_COUNTRY] = $this->getCountryNameByCustomer($customer);
        $customerRow[self::COL_CREATED_AT] = $this->utilDateTimeService->formatDateTime($customer->getCreatedAt());
        $customerRow[self::ACTIONS] = $this->buildLinks($customer);

        return $customerRow;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customer
     *
     * @return string
     */
    protected function getCountryNameByCustomer(SpyCustomer $customer)
    {
        $countryName = '';
        if ($customer->getAddresses()->count() === 0) {
            return $countryName;
        }

        $addresses = $customer->getAddresses();
        foreach ($addresses as $address) {
            if ($address->getFkCountry() === $customer->getVirtualColumn(self::COL_FK_COUNTRY)) {
                return $address->getCountry()->getName();
            }
        }

        return $countryName;
    }

    /**
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerQuery
     */
    protected function prepareQuery()
    {
        $query = $this->customerQueryContainer
            ->queryCustomers()
            ->leftJoinBillingAddress();

        $query->withColumn(SpyCustomerAddressTableMap::COL_ZIP_CODE, self::COL_ZIP_CODE)
            ->withColumn(SpyCustomerAddressTableMap::COL_CITY, self::COL_CITY)
            ->withColumn(SpyCustomerAddressTableMap::COL_FK_COUNTRY, self::COL_FK_COUNTRY);

        return $query;
    }

    /**
     * @param string[] $buttons
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customer
     *
     * @return string[]
     */
    protected function expandLinks(array $buttons, SpyCustomer $customer): array
    {
        $expandedButtons = $this->customerTableExpanderPluginExecutor
            ->executeActionExpanderPlugins($customer->getIdCustomer());

        foreach ($expandedButtons as $button) {
            $buttons[] = $this->generateButton(
                $button->getUrl(),
                $button->getTitle(),
                $button->getDefaultOptions(),
                $button->getCustomOptions()
            );
        }

        return $buttons;
    }
}
