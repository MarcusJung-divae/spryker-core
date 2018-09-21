<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SprykGui\Business\Finder\Module;

interface ModuleFinderInterface
{
    /**
     * @return \Generated\Shared\Transfer\ModuleTransfer[]
     */
    public function findModules(): array;
}
