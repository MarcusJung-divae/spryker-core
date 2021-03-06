<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Dataset\Communication\Controller;

use SprykerTest\Zed\Dataset\DatasetCommunicationTester;
use SprykerTest\Zed\Dataset\DatasetObject\DatasetListPage;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Dataset
 * @group Communication
 * @group Controller
 * @group DatasetListCest
 * Add your own group annotations below this line
 */
class DatasetListCest
{
    /**
     * @param \SprykerTest\Zed\Dataset\DatasetCommunicationTester $i
     *
     * @return void
     */
    public function breadcrumbIsVisible(DatasetCommunicationTester $i): void
    {
        $i->amOnPage(DatasetListPage::URL);
        $i->seeBreadcrumbNavigation('Dataset');
    }
}
