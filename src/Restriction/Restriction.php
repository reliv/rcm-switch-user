<?php

namespace Rcm\SwitchUser\Restriction;

use RcmUser\User\Entity\User;

/**
 * interface Restriction
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
interface Restriction
{
    /**
     * allowed
     *
     * @param User $adminUser
     * @param User $targetUser
     *
     * @return Result
     */
    public function allowed(User $adminUser, User $targetUser);
}
