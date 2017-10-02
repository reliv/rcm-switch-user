<?php

namespace Rcm\SwitchUser\Service;

use Rcm\SwitchUser\Model\SuProperty;
use Rcm\SwitchUser\Restriction\Restriction;
use Rcm\SwitchUser\Result;
use Rcm\SwitchUser\Switcher\Switcher;
use RcmUser\Service\RcmUserService;
use RcmUser\User\Entity\UserInterface;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class SwitchUserService
{
    /**
     * @var RcmUserService
     */
    protected $rcmUserService;

    /**
     * @var Restriction
     */
    protected $restriction;

    /**
     * @var array
     */
    protected $aclConfig;

    /**
     * @var Switcher
     */
    protected $switcher;

    /**
     * @var SwitchUserLogService
     */
    protected $switchUserLogService;

    /**
     * @param array                $config
     * @param RcmUserService       $rcmUserService
     * @param Restriction          $restriction
     * @param Switcher             $switcher
     * @param SwitchUserLogService $switchUserLogService
     */
    public function __construct(
        $config,
        RcmUserService $rcmUserService,
        Restriction $restriction,
        Switcher $switcher,
        SwitchUserLogService $switchUserLogService
    ) {
        $this->rcmUserService = $rcmUserService;
        $this->restriction = $restriction;
        $this->aclConfig = $config['Rcm\\SwitchUser']['acl'];
        $this->switcher = $switcher;
        $this->switchUserLogService = $switchUserLogService;
    }

    /**
     * getSwitchBackMethod
     *
     * @return string
     */
    public function getSwitchBackMethod()
    {
        return $this->switcher->getName();
    }

    /**
     * getUser
     *
     * @param $userName
     *
     * @return null|UserInterface
     */
    public function getUser($userName)
    {
        return $this->rcmUserService->getUserByUsername($userName);
    }

    /**
     * switchToUser
     *
     * @param UserInterface $targetUser
     * @param array         $options
     *
     * @return Result
     */
    public function switchToUser(UserInterface $targetUser, $options = [])
    {
        // Get current user
        $currentUser = $this->rcmUserService->getCurrentUser();

        $result = new Result();

        if (empty($currentUser)) {
            // ERROR
            $this->logAction(
                'UNKNOWN',
                $targetUser->getId(),
                'SU was attempted by user who is not logged in',
                false
            );

            $result->setSuccess(false, 'Access denied');

            return $result;
        }

        // Run restrictions
        $restictionResult = $this->restriction->allowed($currentUser, $targetUser);

        if (!$restictionResult->isAllowed()) {
            // log action
            $this->logAction(
                $currentUser->getId(),
                $targetUser->getId(),
                'SU was attempted by user without access due to restriction',
                false
            );

            $result->setSuccess(false, $restictionResult->getMessage());

            return $result;
        }

        return $this->switcher->switchTo($targetUser, $options);
    }

    /**
     * switchBack
     *
     * @param array ['suUserPassword' = null]
     *
     * @return Result
     */
    public function switchBack($options = [])
    {
        // Get current user
        $targetUser = $this->rcmUserService->getCurrentUser();

        $result = new Result();

        if (empty($targetUser)) {
            $result->setSuccess(false, 'Not logged in');

            return $result;
        }

        $impersonatorUser = $this->getImpersonatorUser($targetUser);

        if (empty($impersonatorUser)) {
            $result->setSuccess(false, 'Not in SU session');

            return $result;
        }

        return $this->switcher->switchBack($impersonatorUser, $options);
    }

    /**
     * logAction
     *
     * @param string $adminUserId
     * @param string $targetUserId
     * @param string $action
     * @param bool   $actionSuccess
     *
     * @return void
     */
    public function logAction(
        $adminUserId,
        $targetUserId,
        $action,
        $actionSuccess
    ) {
        $this->switchUserLogService->logAction(
            $adminUserId,
            $targetUserId,
            $action,
            $actionSuccess
        );
    }

    /**
     * Get the admin (SU) user from the current user if SUed
     *
     * @param null $default
     *
     * @return null|UserInterface
     */
    public function getCurrentImpersonatorUser($default = null)
    {
        // Get current user
        $currentUser = $this->rcmUserService->getCurrentUser();

        if (empty($currentUser)) {
            // ERROR
            return $default;
        }

        return $this->getImpersonatorUser($currentUser, $default);
    }

    /**
     * getImpersonatorUser Get the admin user from the user if SUed
     *
     * @param UserInterface $user
     *
     * @return mixed|null
     */
    /**
     * @param UserInterface $user
     * @param null          $default
     *
     * @return UserInterface|null
     */
    public function getImpersonatorUser(UserInterface $user, $default = null)
    {
        /** @var SuProperty $suProperty */
        $suProperty = $user->getProperty(SuProperty::SU_PROPERTY);

        if (empty($suProperty)) {
            // ERROR
            return $default;
        }

        $suUser = $suProperty->getUser();

        if (empty($suUser)) {
            // ERROR
            return $default;
        }

        return $suUser;
    }

    /**
     * @deprecated use SwitchUserAclService::isSuAllowed
     * isAllowed
     *
     * this is only a basic access check,
     * the restrictions should catch and log any access attempts
     *
     * @param $suUser
     *
     * @return bool|mixed
     */
    public function isAllowed($suUser)
    {
        if (empty($suUser)) {
            return false;
        }
        $aclConfig = $this->aclConfig;

        return $this->rcmUserService->isUserAllowed(
            $aclConfig['resourceId'],
            $aclConfig['privilege'],
            $aclConfig['providerId'],
            $suUser
        );
    }

    /**
     * @deprecated use SwitchUserAclService::currentUserIsSuAllowed
     * currentUserIsAllowed
     *
     * @return bool|mixed
     */
    public function currentUserIsAllowed()
    {
        $adminUser = $this->getCurrentImpersonatorUser();
        $targetUser = $this->rcmUserService->getCurrentUser();

        if (empty($adminUser)) {
            $adminUser = $targetUser;
        }

        return $this->isAllowed($adminUser);
    }
}
