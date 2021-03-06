<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerTest\Zed\CompanyDataImport;

use Codeception\Actor;
use Orm\Zed\Company\Persistence\SpyCompanyQuery;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class CompanyDataImportCommunicationTester extends Actor
{
    use _generated\CompanyDataImportCommunicationTesterActions;

   /**
    * Define custom actions here
    */

    /**
     * @return void
     */
    public function truncateCompanyRelations(): void
    {
        $this->truncateTableRelations($this->getCompanyQuery());
    }

    /**
     * @return \Orm\Zed\Company\Persistence\SpyCompanyQuery
     */
    protected function getCompanyQuery(): SpyCompanyQuery
    {
        return SpyCompanyQuery::create();
    }
}
