<?php

namespace Rcm\SwitchUser\Service;

use Doctrine\ORM\EntityManager;
use Rcm\SwitchUser\Entity\LogEntry;
use Rcm\SwitchUser\Model\SuProperty;
use Rcm\SwitchUser\Restriction\Restriction;
use Rcm\SwitchUser\Result;
use Rcm\SwitchUser\Switcher\Switcher;
use RcmUser\Service\RcmUserService;
use RcmUser\User\Entity\User;

/**
 * Class SwitchUserService
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
     * @return null|User
     */
    public function getUser($userName)
    {
        return $this->rcmUserService->getUserByUsername($userName);
    }

    /**
     * switchToUser
     *
     * @param User  $targetUser
     * @param array $options
     *
     * @return Result
     */
    public function switchToUser(User $targetUser, $options = [])
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

        $impersonatorUser = $this->getImpersonatorUser($targetUser);

        $result = new Result();

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
     * getCurrentImpersonatorUser Get the admin user from the current user if SUed
     *
     * @return null|User
     */
    public function getCurrentImpersonatorUser()
    {
        // Get current user
        $currentUser = $this->rcmUserService->getCurrentUser();

        if (empty($currentUser)) {
            // ERROR
            return null;
        }

        return $this->getImpersonatorUser($currentUser);
    }

    /**
     * getImpersonatorUser Get the admin user from the user if SUed
     *
     * @param User $user
     *
     * @return mixed|null
     */
    public function getImpersonatorUser(User $user)
    {
        /** @var SuProperty $suProperty */
        $suProperty = $user->getProperty(SuProperty::SU_PROPERTY);

        if (empty($suProperty)) {
            // ERROR
            return null;
        }

        $suUser = $suProperty->getUser();

        if (empty($suUser)) {
            // ERROR
            return null;
        }

        return $suUser;
    }

    /**
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
