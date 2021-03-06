<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Kernel\Plugin;

use Spryker\Yves\Kernel\AbstractPlugin;

class Pimple extends AbstractPlugin
{
    /**
     * @var \Spryker\Service\Container\Container
     */
    protected static $application;

    /**
     * @param \Spryker\Service\Container\Container $application
     *
     * @return void
     */
    public static function setApplication($application)
    {
        self::$application = $application;
    }

    /**
     * @return \Spryker\Service\Container\Container
     */
    public function getApplication()
    {
        return self::$application;
    }
}
