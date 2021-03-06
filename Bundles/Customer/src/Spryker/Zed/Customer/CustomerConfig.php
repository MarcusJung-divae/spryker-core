<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer;

use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Shared\Customer\CustomerConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\SequenceNumber\SequenceNumberConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class CustomerConfig extends AbstractBundleConfig
{
    public const ERROR_CODE_CUSTOMER_ALREADY_REGISTERED = 4001;
    public const ERROR_CODE_CUSTOMER_INVALID_EMAIL = 4002;

    protected const MIN_LENGTH_CUSTOMER_PASSWORD = 1;

    /**
     * @uses \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder::MAX_PASSWORD_LENGTH
     *
     * @var int
     */
    protected const MAX_LENGTH_CUSTOMER_PASSWORD = 72;

    /**
     * @api
     *
     * @return string
     */
    public function getHostYves()
    {
        return $this->get(CustomerConstants::BASE_URL_YVES);
    }

    /**
     * @api
     *
     * @param string $token
     *
     * @return string
     */
    public function getCustomerPasswordRestoreTokenUrl($token)
    {
        return $this->getHostYves() . '/password/restore?token=' . $token;
    }

    /**
     * @api
     *
     * @param string $token
     *
     * @return string
     */
    public function getRegisterConfirmTokenUrl($token)
    {
        return $this->getHostYves() . '/register/confirm?token=' . $token;
    }

    /**
     * @api
     *
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    public function getCustomerReferenceDefaults()
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $sequenceNumberSettingsTransfer->setName(CustomerConstants::NAME_CUSTOMER_REFERENCE);

        $sequenceNumberPrefixParts = [];
        $sequenceNumberPrefixParts[] = Store::getInstance()->getStoreName();
        $sequenceNumberPrefixParts[] = $this->get(SequenceNumberConstants::ENVIRONMENT_PREFIX);
        $prefix = implode($this->getUniqueIdentifierSeparator(), $sequenceNumberPrefixParts) . $this->getUniqueIdentifierSeparator();
        $sequenceNumberSettingsTransfer->setPrefix($prefix);

        return $sequenceNumberSettingsTransfer;
    }

    /**
     * This method provides list of URLs to render blocks inside customer detail page.
     * URL defines path to external bundle controller. For example: /sales/customer/customer-orders would call sales bundle, customer controller, customerOrders action.
     *
     * example:
     * [
     *    'sales' => '/sales/customer/customer-orders',
     * ]
     *
     * @api
     *
     * @return string[]
     */
    public function getCustomerDetailExternalBlocksUrls()
    {
        return [];
    }

    /**
     * @api
     *
     * @return int
     */
    public function getCustomerPasswordMinLength(): int
    {
        return static::MIN_LENGTH_CUSTOMER_PASSWORD;
    }

    /**
     * @api
     *
     * @return int
     */
    public function getCustomerPasswordMaxLength(): int
    {
        return static::MAX_LENGTH_CUSTOMER_PASSWORD;
    }

    /**
     * @return string
     */
    protected function getUniqueIdentifierSeparator()
    {
        return '-';
    }
}
