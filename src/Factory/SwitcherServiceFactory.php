<?php

namespace Rcm\SwitchUser\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SwitcherServiceFactory
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   Rcm\SwitchUser\Factory
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
class SwitcherServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');

        $switcherMethod = $config['Rcm\\SwitchUser']['switcherMethod'];
        $switcherServiceName = $config['Rcm\\SwitchUser']['switcherServices'][$switcherMethod];
        $switcher = $serviceLocator->get($switcherServiceName);

        return $switcher;
    }
}
