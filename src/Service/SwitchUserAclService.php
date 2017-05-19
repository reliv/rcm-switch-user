<?php

namespace Rcm\SwitchUser\Service;

use RcmUser\Service\RcmUserService;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class SwitchUserAclService
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
        $this->aclConfig = $config['Rcm\\SwitchUser']['acl'];
        $this->rcmUserService = $rcmUserService;
        $this->switchUserService = $switchUserService;
    }

    /**
     * getAclUser
     *
     * @param $user
     *
     * @return mixed|null
     */
    public function getAclUser($user)
    {
        if (empty($user)) {
            return null;
        }

        $adminUser = $this->switchUserService->getImpersonatorUser($user);
        $targetUser = $user;

        if (empty($adminUser)) {
            $adminUser = $targetUser;
        }

        return $adminUser;
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
    public function isUserAllowed($resourceId, $privilege, $providerId = null, $user)
    {
        $suUser = $this->getAclUser($user);

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
    public function currentUserIsAllowed($resourceId, $privilege, $providerId = null)
    {
        $user = $this->rcmUserService->getCurrentUser();

        $adminUser = $this->getAclUser($user);

        return $this->isUserAllowed(
            $resourceId,
            $privilege,
            $providerId,
            $adminUser
        );
    }

    /**
     * isImpersonatorUserAllowed
     *
     * @param $resourceId
     * @param $privilege
     * @param $providerId
     * @param $user
     *
     * @return bool|mixed
     */
    public function isImpersonatorUserAllowed(
        $resourceId,
        $privilege,
        $providerId = null,
        $user
    ) {
        $user = $this->switchUserService->getImpersonatorUser($user);

        if (empty($user)) {
            return false;
        }

        return $this->rcmUserService->isUserAllowed(
            $resourceId,
            $privilege,
            $providerId = null,
            $user
        );
    }

    /**
     * isCurrentImpersonatorUserAllowed
     *
     * @param $resourceId
     * @param $privilege
     * @param $providerId
     *
     * @return bool|mixed
     */
    public function isCurrentImpersonatorUserAllowed(
        $resourceId,
        $privilege,
        $providerId = null
    ) {
        $user = $this->rcmUserService->getCurrentUser();

        return $this->isImpersonatorUserAllowed(
            $resourceId,
            $privilege,
            $providerId,
            $user
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
