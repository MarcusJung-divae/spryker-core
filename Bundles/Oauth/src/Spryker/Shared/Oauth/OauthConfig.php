<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Oauth;

use Spryker\Shared\Config\Config;
use Spryker\Shared\Kernel\AbstractSharedConfig;

class OauthConfig extends AbstractSharedConfig
{
    /**
     * Path to public key location
     *
     * @api
     *
     * @see https://oauth2.thephpleague.com/installation/
     *
     * @return string
     */
    public function getPublicKeyPath(): string
    {
        return Config::getInstance()->get(OauthConstants::PUBLIC_KEY_PATH);
    }

    /**
     * Path to private key location
     *
     * @api
     *
     * @see https://oauth2.thephpleague.com/installation/
     *
     * @return string
     */
    public function getPrivateKeyPath(): string
    {
        return Config::getInstance()->get(OauthConstants::PRIVATE_KEY_PATH);
    }

    /**
     * Encryption key used to encrypt data
     *
     * @api
     *
     * @return string
     */
    public function getEncryptionKey(): string
    {
        return Config::getInstance()->get(OauthConstants::ENCRYPTION_KEY);
    }

    /**
     * Interval for how long is the refresh token is valid, this will be feed to \DateTime object
     *
     * @api
     *
     * @return string
     */
    public function getRefreshTokenTTL(): string
    {
        return 'P1M';
    }

    /**
     *  Interval for how long is the access token is valid, this will be feed to \DateTime object
     *
     * @api
     *
     * @return string
     */
    public function getAccessTokenTTL(): string
    {
        return 'PT8H';
    }

    /**
     * Specification:
     *  - Interval for how long refresh tokens will be stored in the system after they expire, this will be feed to \DateTime object.
     *
     * @api
     *
     * @return string
     */
    public function getRefreshTokenRetentionInterval(): string
    {
        return 'P100Y';
    }
}
