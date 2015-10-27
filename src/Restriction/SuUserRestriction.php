<?php

namespace Rcm\SwitchUser\Restriction;

use RcmUser\Service\RcmUserService;
use RcmUser\User\Entity\User;

/**
 * class SuUserRestriction
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
class SuUserRestriction implements Restriction
{
    /**
     * @var array
     */
    protected $aclConfig;

    /**
     * @var RcmUserService
     */
    protected $rcmUserService;

    /**
     * @param array          $config
     * @param RcmUserService $rcmUserService
     */
    public function __construct($config, RcmUserService $rcmUserService)
    {
        $this->aclConfig = $config['Rcm\\SwitchUser']['acl'];
        $this->rcmUserService = $rcmUserService;
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
        $isAllowed = $this->rcmUserService->isUserAllowed(
            $this->aclConfig['resourceId'],
            $this->aclConfig['privilege'],
            $this->aclConfig['providerId'],
            $targetUser
        );

        if ($isAllowed) {
            return new RestrictionResult(false, 'Cannot switch to this user');
        }

        return new RestrictionResult(true);
    }
}
