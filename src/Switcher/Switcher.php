<?php

namespace Rcm\SwitchUser\Switcher;

use Rcm\SwitchUser\Result;
use RcmUser\User\Entity\User;

/**
 * Class Switcher
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
interface Switcher
{
    /**
     * getName of the switch method
     *
     * @return string
     */
    public function getName();

    /**
     * switchTo
     *
     * @param User  $targetUser
     * @param array $options
     *
     * @return Result
     */
    public function switchTo(User $targetUser, $options = []);

    /**
     * switchBack
     *
     * @param User  $impersonatorUser
     * @param array $options
     *
     * @return Result
     */
    public function switchBack(User $impersonatorUser, $options = []);
}
