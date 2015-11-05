<?php

namespace Rcm\SwitchUser\Switcher;

use Rcm\SwitchUser\Model\SuProperty;
use Rcm\SwitchUser\Result;
use Rcm\SwitchUser\Service\SwitchUserLogService;
use RcmUser\Service\RcmUserService;
use RcmUser\User\Entity\User;

/**
 * Class BasicSwitcher
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   Rcm\SwitchUser\Switcher
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
class BasicSwitcher implements Switcher
{
    /**
     * @var string
     */
    protected $name = 'basic';

    /**
     * @var RcmUserService
     */
    protected $rcmUserService;

    /**
     * @param RcmUserService       $rcmUserService
     * @param SwitchUserLogService $switchUserLogService
     */
    public function __construct(
        RcmUserService $rcmUserService,
        SwitchUserLogService $switchUserLogService
    ) {
        $this->rcmUserService = $rcmUserService;
        $this->switchUserLogService = $switchUserLogService;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * switchTo
     *
     * @param User $targetUser
     *
     * @return Result
     */
    public function switchTo(User $targetUser, $options = [])
    {
        $currentUser = $this->rcmUserService->getCurrentUser();

        $result = new Result();

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
     * @param User  $impersonatorUser
     * @param array $options
     *
     * @return Result
     * @throws \Exception
     */
    public function switchBack(User $impersonatorUser, $options = [])
    {
        // Get current user
        $currentUserId = $this->rcmUserService->getCurrentUser()->getId();
        $impersonatorUserId = $impersonatorUser->getId();

        $result = new Result();

        // Force login as $suUser
        $this->rcmUserService->getUserAuthService()->setIdentity($impersonatorUser);

        // log action
        $this->logAction(
            $impersonatorUserId,
            $currentUserId,
            'SU switched back',
            true
        );

        $result->setSuccess(true, 'SU switch back was successful');

        return $result;
    }

    /**
     * logAction
     *
     * @param $adminUserId
     * @param $targetUserId
     * @param $action
     * @param $actionSuccess
     *
     * @return void
     */
    protected function logAction(
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
}
