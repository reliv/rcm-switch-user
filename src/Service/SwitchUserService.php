<?php

namespace Rcm\SwitchUser\Service;

use Doctrine\ORM\EntityManager;
use Rcm\SwitchUser\Entity\LogEntry;
use Rcm\SwitchUser\Model\SuProperty;
use Rcm\SwitchUser\Restriction\Restriction;
use Rcm\SwitchUser\Result;
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
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $switchBackMethod = 'auth';

    /**
     * @var array
     */
    protected $aclConfig;

    /**
     * @param array $config
     * @param RcmUserService $rcmUserService
     * @param Restriction $restriction
     * @param EntityManager $entityManager
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
     * getSwitchBackMethod
     *
     * @return string
     */
    public function getSwitchBackMethod()
    {
        return $this->switchBackMethod;
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
     * @param User $targetUser
     *
     * @return Result
     * @throws \RcmUser\Exception\RcmUserException
     */
    public function switchToUser(User $targetUser)
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

        // Force login as $targetUser
        $this->rcmUserService->getUserAuthService()->setIdentity($targetUser);
        // add SU property to target user
        $targetUser->setProperty(
            SuProperty::SU_PROPERTY,
            new SuProperty($currentUser)
        );

        // log action
        $this->logAction(
            $currentUser->getId(),
            $targetUser->getId(),
            'SU was successful',
            true
        );

        $result->setSuccess(true, 'SU was successful');

        return $result;
    }

    /**
     * switchBack
     *
     * @param null $suUserPassword
     *
     * @return Result
     */
    public function switchBack($suUserPassword = null)
    {
        $method = 'switchBack' . ucfirst($this->getSwitchBackMethod());

        return $this->$method($suUserPassword);
    }

    /**
     * switchBack Less secure way to switch user back
     *
     * @return Result
     */
    public function switchBackBasic()
    {
        // Get current user
        $targetUser = $this->rcmUserService->getCurrentUser();

        $suUser = $this->getSuUser($targetUser);

        $result = new Result();

        if (empty($suUser)) {
            $result->setSuccess(false, 'Not in SU session');

            return $result;
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

        $result->setSuccess(true, 'SU switch back was successful');

        return $result;
    }

    /**
     * switchBackAuth More secure way to switch user back
     *
     * @param string $suUserPassword
     *
     * @return Result
     */
    public function switchBackAuth($suUserPassword)
    {
        // Get current user
        $targetUser = $this->rcmUserService->getCurrentUser();

        $suUser = $this->getSuUser($targetUser);

        $result = new Result();

        if (empty($suUser)) {
            $result->setSuccess(false, 'Not in SU session');

            return $result;
        }

        $suUserId = $suUser->getId();

        $suUser->setPassword($suUserPassword);
        $authResult = $this->rcmUserService->authenticate($suUser);
        if (!$authResult->isValid()) {
            // ERROR
            // log action
            $this->logAction(
                $suUserId,
                $targetUser->getId(),
                'SU attempted to switched back, provided incorrect credentials',
                true
            );

            $result->setSuccess(false, $authResult->getMessages()[0]);

            return $result;
        }

        // log action
        $this->logAction(
            $suUserId,
            $targetUser->getId(),
            'SU switched back',
            true
        );

        $result->setSuccess(true, 'SU switch back was successful');

        return $result;
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
        /** @var SuProperty $suProperty */
        $suProperty = $user->getProperty(SuProperty::SU_PROPERTY);
        if ($suProperty === null) {
            return null;
        }

        return $suProperty->getUserId();
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
        /** @var SuProperty $suProperty */
        $suProperty = $user->getProperty(SuProperty::SU_PROPERTY);

        if (empty($suProperty)) {
            // ERROR
            return null;
        }

        $suUserId = $suProperty->getUserId();

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
