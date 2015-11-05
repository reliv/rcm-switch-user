<?php

namespace Rcm\SwitchUser\Switcher;

use Rcm\SwitchUser\Result;
use RcmUser\User\Entity\User;

/**
 * Class AuthSwitcher
 * More secure way to switch user back
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
class AuthSwitcher extends BasicSwitcher
{
    /**
     * @var string
     */
    protected $name = 'auth';

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
        if (!isset($options['suUserPassword'])) {
            throw new \Exception('suUserPassword required for AuthSwitcher');
        }
        $suUserPassword = $options['suUserPassword'];

        // Get current user
        $currentUserId = $this->rcmUserService->getCurrentUser()->getId();
        $impersonatorUserId = $impersonatorUser->getId();

        $result = new Result();

        $impersonatorUser->setPassword($suUserPassword);
        $authResult = $this->rcmUserService->authenticate($impersonatorUser);
        if (!$authResult->isValid()) {
            // ERROR
            // log action
            $this->logAction(
                $impersonatorUserId,
                $currentUserId,
                'SU attempted to switched back, provided incorrect credentials',
                true
            );

            $result->setSuccess(false, $authResult->getMessages()[0]);

            return $result;
        }

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
}
