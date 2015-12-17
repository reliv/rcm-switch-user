<?php

namespace Rcm\SwitchUser\Service;

use RcmUser\Service\RcmUserService;

/**
 * Class SwitchUserAclService
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
class SwitchUserAclService
{
    /**
     * @var RcmUserService
     */
    protected $rcmUserService;

    /**
     * @var SwitchUserService
     */
    protected $switchUserService;

    /**
     * SwitchUserAclService constructor.
     *
     * @param  array            $config
     * @param RcmUserService    $rcmUserService
     * @param SwitchUserService $switchUserService
     */
    public function __construct(
        $config,
        RcmUserService $rcmUserService,
        SwitchUserService $switchUserService
    ) {
        $this->rcmUserService = $rcmUserService;
        $this->switchUserService = $switchUserService;
        $this->aclConfig = $config['Rcm\\SwitchUser']['acl'];
    }

    /**
     * isAllowed
     *
     * @param $resourceId
     * @param $privilege
     * @param $providerId
     * @param $user
     *
     * @return bool|mixed
     */
    public function isUserAllowed($resourceId, $privilege, $providerId, $user)
    {
        $suUser = $this->switchUserService->getImpersonatorUser($user);

        if (empty($suUser)) {
            return false;
        }

        return $this->rcmUserService->isUserAllowed(
            $resourceId,
            $privilege,
            $providerId,
            $suUser
        );
    }

    /**
     * currentUserIsAllowed
     *
     * @param $resourceId
     * @param $privilege
     * @param $providerId
     *
     * @return bool|mixed
     */
    public function currentUserIsAllowed($resourceId, $privilege, $providerId)
    {
        $adminUser = $this->switchUserService->getCurrentImpersonatorUser();
        $targetUser = $this->rcmUserService->getCurrentUser();

        if (empty($adminUser)) {
            $adminUser = $targetUser;
        }

        return $this->isUserAllowed(
            $resourceId,
            $privilege,
            $providerId,
            $adminUser
        );
    }

    /**
     * isSuAllowed
     *
     * this is only a basic access check,
     * the restrictions should catch and log any access attempts
     *
     * @param $suUser
     *
     * @return bool|mixed
     */
    public function isSuAllowed($suUser)
    {
        return $this->isUserAllowed(
            $this->aclConfig['resourceId'],
            $this->aclConfig['privilege'],
            $this->aclConfig['providerId'],
            $suUser
        );
    }

    /**
     * currentUserIsAllowed
     *
     * @return bool|mixed
     */
    public function currentUserIsSuAllowed()
    {
        return $this->currentUserIsAllowed(
            $this->aclConfig['resourceId'],
            $this->aclConfig['privilege'],
            $this->aclConfig['providerId']
        );
    }
}
