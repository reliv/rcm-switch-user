<?php

namespace Rcm\SwitchUser\ApiController;

use Reliv\RcmApiLib\Controller\AbstractRestfulJsonController;

/**
 * @author James Jervis - https://github.com/jerv13
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
            \RcmUser\Service\RcmUserService::class
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
            \Rcm\SwitchUser\Service\SwitchUserService::class
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
            \Rcm\SwitchUser\Service\SwitchUserAclService::class
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
