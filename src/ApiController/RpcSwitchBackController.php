<?php

namespace Rcm\SwitchUser\ApiController;

use Reliv\RcmApiLib\Controller\AbstractRestfulJsonController;
use Reliv\RcmApiLib\Model\ExceptionApiMessage;
use Reliv\RcmApiLib\Model\HttpStatusCodeApiMessage;

/**
 * Class RpcSwitchBackController
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
class RpcSwitchBackController extends AbstractRestfulJsonController
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
     * getAclConfig
     *
     * @return array
     */
    protected function getAclConfig()
    {
        $config = $this->getServiceLocator()->get(
            'config'
        );

        return $config['Rcm\\SwitchUser']['acl'];
    }

    /**
     * isAllowed
     *
     * @return bool
     */
    protected function isAllowed($suUser)
    {
        if (empty($suUser)) {
            return false;
        }
        $aclConfig = $this->getAclConfig();

        return $this->getRcmUserService()->isUserAllowed(
            $aclConfig['resourceId'],
            $aclConfig['admin'],
            $aclConfig['providerId'],
            $suUser
        );
    }

    /**
     * update
     *
     * @param array $data ['switchToUserId' => '{MY_ID}']
     *
     * @return \Reliv\RcmApiLib\Http\ApiResponse
     */
    public function update($data)
    {
        $service = $this->getSwitchUserService();

        $suUser = $service->getCurrentSuUser();

        if (!$this->isAllowed($suUser)) {
            return $this->getApiResponse(null, 401);
        }

        try {
            $result = $service->switchBack();
        } catch (\Exception $exception) {
            return $this->getApiResponse(
                null,
                500,
                new ExceptionApiMessage($exception)
            );
        }

        if (empty($result)) {
            return $this->getApiResponse(
                null,
                406,
                new HttpStatusCodeApiMessage(406)
            );
        }

        return $this->getApiResponse($result);
    }
}
