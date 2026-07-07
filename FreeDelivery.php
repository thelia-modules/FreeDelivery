<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

declare(strict_types=1);

namespace FreeDelivery;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Core\Install\Database;
use Thelia\Module\BaseModule;

class FreeDelivery extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'freedelivery';

    public function preActivation(?ConnectionInterface $con = null): bool
    {
        if (!self::getConfigValue('is_initialized')) {
            $database = new Database($con);

            $database->insertSql(null, [__DIR__.'/Config/thelia.sql']);

            self::setConfigValue('is_initialized', '1');
        }

        return true;
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([__DIR__.'/I18n/*'])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
