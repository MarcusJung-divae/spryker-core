<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DataImport\Business;

use Generated\Shared\Transfer\DataImportConfigurationActionTransfer;
use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReportTransfer;

/**
 * @method \Spryker\Zed\DataImport\Business\DataImportBusinessFactory getFactory()
 */
interface DataImportFacadeInterface
{
    /**
     * Specification:
     * - Creates importer.
     * - Runs `DataImporterInterface::import()`.
     * - Returns DataImportReportTransfer.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\DataImport\Business\DataImportFacadeInterface::importByAction()} instead.
     *
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfiguration
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function import(?DataImporterConfigurationTransfer $dataImporterConfiguration = null);

    /**
     * Specification:
     * - Creates importer by DataImportConfigurationActionTransfer.
     * - Runs `DataImporterInterface::import()`.
     * - Returns DataImportReportTransfer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\DataImportConfigurationActionTransfer $dataImportConfigurationActionTransfer
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfiguration
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function importByAction(
        DataImportConfigurationActionTransfer $dataImportConfigurationActionTransfer,
        ?DataImporterConfigurationTransfer $dataImporterConfiguration = null
    ): DataImporterReportTransfer;

    /**
     * Specification:
     * - Returns a list of all applied `DataImportPluginInterfaces` and `DataImportInterfaces`.
     *
     * @api
     *
     * @return string[]
     */
    public function listImporters(): array;

    /**
     * Specification:
     * - Triggers publish event for all imported entities.
     *
     * @api
     *
     * @return void
     */
    public function publish(): void;

    /**
     * Specification:
     * - Writes JSON encoded data of DataSetItemTransfers into a queue.
     *
     * @api
     *
     * @param string $queueName
     * @param \Generated\Shared\Transfer\DataSetItemTransfer[] $dataSetItems
     *
     * @return void
     */
    public function writeDataSetItemsToQueue(string $queueName, array $dataSetItems): void;
}
