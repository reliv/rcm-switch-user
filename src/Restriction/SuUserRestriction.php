<?php

namespace Rcm\SwitchUser\Restriction;

use RcmUser\Service\RcmUserService;
use RcmUser\User\Entity\UserInterface;

/**
 * @author James Jervis - https://github.com/jerv13
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
     * @param UserInterface $adminUser
     * @param UserInterface $targetUser
     *
     * @return RestrictionResult
     */
    public function allowed(UserInterface $adminUser, UserInterface $targetUser)
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
