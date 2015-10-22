<?php

namespace Rcm\SwitchUser\Restriction;

/**
 * interface Result
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   Rcm\SwitchUser\Restriction
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
interface Result
{
    /**
     * setAllowed
     *
     * @param bool   $allowed
     * @param string $message
     *
     * @return void
     */
    public function setAllowed($allowed, $message = '');

    /**
     * isAllowed
     *
     * @return bool
     */
    public function isAllowed();

    /**
     * getMessage
     *
     * @return string
     */
    public function getMessage();
}
