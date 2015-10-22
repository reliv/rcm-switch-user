<?php

namespace Rcm\SwitchUser\Service;

use Doctrine\ORM\EntityManager;
use Rcm\SwitchUser\Entity\LogEntry;
use Rcm\SwitchUser\Entity\SwitchUserLog;
use Rcm\SwitchUser\Restriction\Restriction;
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
     * SU_PROPERTY
     */
    const SU_PROPERTY = 'suUser';

    /**
     * @var RcmUserService
     */
    protected $rcmUserService;

    /**
     * @var Restriction
     */
    protected $restriction;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $switchBackMethod = 'auth';

    protected $aclConfig;

    /**
     * @param array          $config
     * @param RcmUserService $rcmUserService
     * @param Restriction    $restriction
     * @param EntityManager  $entityManager
     */
    public function __construct(
        $config,
        RcmUserService $rcmUserService,
        Restriction $restriction,
        EntityManager $entityManager
    ) {
        $this->rcmUserService = $rcmUserService;
        $this->restriction = $restriction;
        $this->entityManager = $entityManager;
        $this->switchBackMethod = $config['Rcm\\SwitchUser']['switchBackMethod'];
        $this->aclConfig = $config['Rcm\\SwitchUser']['acl'];
    }

    /**
     * getUser
     *
     * @param $userId
     *
     * @return null|User
     */
    public function getUser($userId)
    {
        return $this->rcmUserService->getUserById($userId);
    }

    /**
     * switchToUser
     *
     * @param User $targetUser
     *
     * @return bool success
     * @throws \RcmUser\Exception\RcmUserException
     */
    public function switchToUser(User $targetUser)
    {
        // Get current user
        $currentUser = $this->rcmUserService->getCurrentUser();

        if (empty($currentUser)) {
            // ERROR
            $this->logAction(
                'UNKNOWN',
                $targetUser->getId(),
                'SU was attempted by user without access',
                false
            );

            return false;
        }

        // Run restrictions
        $result = $this->restriction->allowed($currentUser, $targetUser);

        if (!$result->isAllowed()) {
            // log action
            $this->logAction(
                $currentUser->getId(),
                $targetUser->getId(),
                'SU was attempted by user without access',
                false
            );

            // ERROR
            return false;
        }

        // Force login as $targetUser
        $this->rcmUserService->getUserAuthService()->setIdentity($targetUser);
        // add SU property to target user
        $targetUser->setProperty(self::SU_PROPERTY, $currentUser->getId());

        // log action
        $this->logAction(
            $currentUser->getId(),
            $targetUser->getId(),
            'SU was successful',
            true
        );

        return true;
    }

    /**
     * switchBack
     *
     * @param null $suUserPassword
     *
     * @return bool
     */
    public function switchBack($suUserPassword = null)
    {
        $method = 'switchBack'.ucfirst($this->switchBackMethod);
        return $this->$method($suUserPassword);
    }

    /**
     * switchBack Less secure way to switch user back
     *
     * @return bool
     */
    public function switchBackBasic()
    {
        // Get current user
        $targetUser = $this->rcmUserService->getCurrentUser();

        $suUser = $this->getSuUser($targetUser);

        if (empty($suUser)) {
            return false;
        }

        $suUserId = $suUser->getId();

        // Force login as $suUser
        $this->rcmUserService->getUserAuthService()->setIdentity($suUser);

        // log action
        $this->logAction(
            $suUserId,
            $targetUser->getId(),
            'SU switched back',
            true
        );

        return true;
    }

    /**
     * switchBackAuth More secure way to switch user back
     *
     * @param string $suUserPassword
     *
     * @return bool
     */
    public function switchBackAuth($suUserPassword)
    {
        // Get current user
        $targetUser = $this->rcmUserService->getCurrentUser();

        $suUser = $this->getSuUser($targetUser);

        if (empty($suUser)) {
            return false;
        }
        $suUserId = $suUser->getId();

        $suUser->setPassword($suUserPassword);
        $result = $this->rcmUserService->authenticate($suUser);
        if (!$result->isValid()) {
            // ERROR
            // log action
            $this->logAction(
                $suUserId,
                $targetUser->getId(),
                'SU attempted to switched back, provided incorrect credentials',
                true
            );

            return false;
        }

        // log action
        $this->logAction(
            $suUserId,
            $targetUser->getId(),
            'SU switched back',
            true
        );

        return true;
    }

    /**
     * currentUserIsSu
     *
     * @return string|null
     */
    public function currentUserIsSu()
    {
        // Get current user
        $currentUser = $this->rcmUserService->getCurrentUser();

        if (empty($currentUser)) {
            return false;
        }

        return $this->userIsSu($currentUser);
    }

    /**
     * userIsSu
     *
     * @param User $user
     *
     * @return string|null
     */
    public function userIsSu(User $user)
    {
        return $user->getProperty(self::SU_PROPERTY);
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
    public function logAction($adminUserId, $targetUserId, $action, $actionSuccess)
    {
        $entry = new LogEntry($adminUserId, $targetUserId, $action, $actionSuccess);

        $this->entityManager->persist($entry);
        $this->entityManager->flush($entry);
    }

    /**
     * getSuUserFromCurrent Get the admin user from the current user if SUed
     *
     * @return null|User
     */
    public function getCurrentSuUser()
    {
        // Get current user
        $currentUser = $this->rcmUserService->getCurrentUser();

        if (empty($currentUser)) {
            // ERROR
            return null;
        }

        return $this->getSuUser($currentUser);
    }

    /**
     * getSuUser Get the admin user from the user if SUed
     *
     * @return null|User
     */
    public function getSuUser(User $user)
    {
        $suUserId = $user->getProperty(self::SU_PROPERTY);

        if (empty($suUserId)) {
            // ERROR
            return null;
        }

        $suUser = $this->getUser($suUserId);

        if (empty($suUser)) {
            // ERROR
            return null;
        }

        return $suUser;
    }

    /**
     * isAllowed
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
        $adminUser = $this->getCurrentSuUser();
        $targetUser = $this->rcmUserService->getCurrentUser();

        if (empty($adminUser)) {
            $adminUser = $targetUser;
        }

        return $this->isAllowed($adminUser);
    }
}
