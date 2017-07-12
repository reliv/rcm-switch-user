<?php

namespace Rcm\SwitchUser\ApiController;

use Reliv\RcmApiLib\Model\ApiMessage;
use Reliv\RcmApiLib\Model\ExceptionApiMessage;

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
class RpcSwitchBackController extends BaseApiController
{
    /**
     * create
     *
     * @param array $data ['suUserPassword' => '{validPassword}']
     *
     * @return \Reliv\RcmApiLib\Http\ApiResponse
     */
    public function create($data)
    {
        $service = $this->getSwitchUserService();

        $currentUser = $this->getRcmUserService()->getCurrentUser();

        if (empty($currentUser)) {
            return $this->getApiResponse(null, 401);
        }

        $suUser = $service->getImpersonatorUser($currentUser, $currentUser);

        if (!$this->isAllowed($suUser)) {
            return $this->getApiResponse(null, 401);
        }

        // The switch back method used will determine what the API requires
        $options = $data;

        try {
            $result = $service->switchBack($options);
        } catch (\Exception $exception) {
            return $this->getApiResponse(
                null,
                500,
                new ExceptionApiMessage($exception)
            );
        }

        if (!$result->isSuccess()) {
            return $this->getApiResponse(
                null,
                406,
                new ApiMessage('failure', $result->getMessage(), 'rpcSwitchBack', 'invalid')
            );
        }

        $data = [
            'userId' => $suUser->getId(),
            'username' => $suUser->getUsername(),
        ];

        return $this->getApiResponse($data);
    }
}
