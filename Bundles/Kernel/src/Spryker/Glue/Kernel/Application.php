<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\Kernel;

use Spryker\Shared\Kernel\Communication\Application as SharedApplication;

/**
 * @deprecated Use {@link \Spryker\Shared\Kernel\Communication\Application} instead.
 */
class Application extends SharedApplication
{
    /**
     * Instantiate a new Application.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);
        $this->unsetSilexExceptionHandler();
    }

    /**
     * @return void
     */
    private function unsetSilexExceptionHandler(): void
    {
        unset($this['exception_handler']);
    }
}
