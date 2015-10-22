<?php

namespace Rcm\SwitchUser\Restriction;

/**
 * Class RestrictionResult
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
class RestrictionResult implements Result
{
    /**
     * @var bool
     */
    protected $allowed = true;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @param bool   $allowed
     * @param string $message
     */
    public function __construct($allowed, $message = '')
    {
        $this->setAllowed($allowed, $message);
    }

    /**
     * setAllowed
     *
     * @param bool   $allowed
     * @param string $message
     *
     * @return void
     */
    public function setAllowed($allowed, $message = '')
    {
        $this->allowed = (bool) $allowed;
        $this->message = (string) $message;
    }

    /**
     * isAllowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->allowed;
    }

    /**
     * getMessage
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
