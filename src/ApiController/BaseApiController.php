<?php

namespace Rcm\SwitchUser\ApiController;

use Reliv\RcmApiLib\Controller\AbstractRestfulJsonController;
use Reliv\RcmApiLib\Model\ExceptionApiMessage;
use Reliv\RcmApiLib\Model\HttpStatusCodeApiMessage;

/**
 * Class RpcController
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   Reliv\Conference\ApiController
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
class BaseApiController extends AbstractRestfulJsonController
{
    /**
     * getRcmUserService
     *
     * @return \RcmUser\Service\RcmUserService
     */
    protected function getRcmUserService()
    {
        return $this->getServiceLocator()->get(
            'RcmUser\Service\RcmUserService'
        );
    }

    /**
     * getSwitchUserService
     *
     * @return \Rcm\SwitchUser\Service\SwitchUserService
     */
    protected function getSwitchUserService()
    {
        return $this->getServiceLocator()->get(
            'Rcm\SwitchUser\Service\SwitchUserService'
        );
    }

    /**
     * getSwitchUserAclService
     *
     * @return \Rcm\SwitchUser\Service\SwitchUserAclService
     */
    protected function getSwitchUserAclService()
    {
        return $this->getServiceLocator()->get(
            'Rcm\SwitchUser\Service\SwitchUserAclService'
        );
    }

    /**
     * isAllowed
     *
     * @param $suUser
     *
     * @return bool|mixed
     */
    protected function isAllowed($suUser)
    {
        return $this->getSwitchUserAclService()->isSuAllowed($suUser);
    }
}
