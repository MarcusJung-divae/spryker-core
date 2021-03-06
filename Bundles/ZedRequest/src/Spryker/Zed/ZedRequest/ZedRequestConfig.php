<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ZedRequest;

use Spryker\Shared\Kernel\Store;
use Spryker\Shared\ZedRequest\ZedRequestConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class ZedRequestConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * @param string $fileName
     *
     * @return string
     */
    public function getPathToYvesRequestRepeatData($fileName)
    {
        $path = $this->get(ZedRequestConstants::YVES_REQUEST_REPEAT_DATA_PATH, $this->getBcYvesRepeatDataPath());
        $pathTofFile = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . $fileName;

        return $pathTofFile;
    }

    /**
     * Path is used for BC. When this config option is not set, this will be used.
     *
     * @return string
     */
    private function getBcYvesRepeatDataPath()
    {
        return APPLICATION_ROOT_DIR . '/data/' . Store::getInstance()->getStoreName() . '/logs/ZED/';
    }

    /**
     * @api
     *
     * @param string|null $bundleControllerAction
     *
     * @return string
     */
    public function getYvesRequestRepeatDataFileName($bundleControllerAction = null)
    {
        $fileName = 'last_yves_request';
        if ($bundleControllerAction) {
            $fileName .= '_' . $bundleControllerAction;
        }

        return basename($fileName . '.log');
    }

    /**
     * @api
     *
     * @return int
     */
    public function getPermissionMode(): int
    {
        return $this->get(ZedRequestConstants::DIRECTORY_PERMISSION, 0777);
    }

    /**
     * @api
     *
     * @return bool
     */
    public function isRepeatEnabled(): bool
    {
        return $this->get(ZedRequestConstants::ENABLE_REPEAT, $this->getEnableRepeatDefaultValue());
    }

    /**
     * @deprecated Method will be removed without replacement.
     *
     * @return bool
     */
    protected function getEnableRepeatDefaultValue(): bool
    {
        return APPLICATION_ENV === 'development';
    }
}
