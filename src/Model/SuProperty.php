<?php

namespace Rcm\SwitchUser\Model;

use RcmUser\User\Entity\User;
use Reliv\RcmApiLib\Model\AbstractApiModel;

/**
 * Class SuProperty
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
class SuProperty extends AbstractApiModel
{
    /**
     * SU_PROPERTY
     */
    const SU_PROPERTY = 'suUser';

    /**
     * @var User
     */
    protected $suUser;

    /**
     * @param User $suUser
     */
    public function __construct(User $suUser)
    {
        $this->suUser = $suUser;
    }

    /**
     * getUserId
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->suUser;
    }

    /**
     * getUserId
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->suUser->getId();
    }

    /**
     * getUsername
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->suUser->getUsername();
    }

    /**
     * getUserProperty
     *
     * @param string $propertyId
     * @param null   $default
     *
     * @return null
     */
    public function getUserProperty($propertyId, $default = null)
    {
        return $this->suUser->getProperty($propertyId, $default);
    }

    /**
     * toArray
     *
     * @param array $ignore
     *
     * @return array
     */
    public function toArray($ignore = [])
    {
        $data = [];
        if (!in_array('username', $ignore)) {
            $data['username'] = $this->getUsername();
        }
        if (!in_array('userId', $ignore)) {
            $data['userId'] = $this->getUserId();
        }

        return $data;
    }
}
