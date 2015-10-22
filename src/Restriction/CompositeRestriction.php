<?php

namespace Rcm\SwitchUser\Restriction;

use RcmUser\Service\RcmUserService;
use RcmUser\User\Entity\User;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * class CompositeRestriction
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   moduleNameHere
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
class CompositeRestriction implements Restriction
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
    /**
     * @var array
     */
    protected $restrictions = [];

    /**
     * @param array                   $config
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct($config, ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->buildRestrictions($config['Rcm\\SwitchUser']['restrictions']);
    }

    /**
     * buildRestrictions
     *
     * @param array $restrictionConfig
     *
     * @return void
     */
    protected function buildRestrictions($restrictionConfig)
    {
        foreach ($restrictionConfig as $serviceName) {
            /** @var Restriction $service */
            $service = $this->serviceLocator->get($serviceName);
            $this->add($service);
        }
    }

    /**
     * add
     *
     * @param Restriction $restriction
     *
     * @return void
     */
    public function add(Restriction $restriction)
    {
        $this->restrictions[] = $restriction;
    }

    /**
     * allowed
     *
     * @param User $adminUser
     * @param User $targetUser
     *
     * @return bool
     */
    public function allowed(User $adminUser, User $targetUser)
    {
        /** @var Restriction $restriction */
        foreach ($this->restrictions as $restriction) {
            $result = $restriction->allowed($adminUser, $targetUser);
            if (!$result->isAllowed()) {
                return $result;
            }
        }

        return new RestrictionResult(true);
    }
}
