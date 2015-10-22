<?php

namespace Rcm\SwitchUser\ApiController;

use Reliv\RcmApiLib\Controller\AbstractRestfulJsonController;
use Reliv\RcmApiLib\Model\ExceptionApiMessage;
use Reliv\RcmApiLib\Model\HttpStatusCodeApiMessage;

/**
 * Class RpcSuController
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
class RpcSuController extends BaseApiController
{
    /**
     * create
     *
     * @param array $data ['switchToUserId' => '{MY_ID}']
     *
     * @return \Reliv\RcmApiLib\Http\ApiResponse
     */
    public function getList()
    {
        $service = $this->getSwitchUserService();

        $suUser = $service->getCurrentSuUser();

        $data = [
            'isSu' => false,
            'user' => null,
        ];

        if (empty($suUser)) {
            return $this->getApiResponse($data, 401);
        }
        // only expose some info
        $data['isSu'] = true;
        $data['user'] = $this->getRcmUserService()->getCurrentUser();

        return $this->getApiResponse($data);
    }

    /**
     * create
     *
     * @param array $data ['switchToUserId' => '{MY_ID}']
     *
     * @return \Reliv\RcmApiLib\Http\ApiResponse
     */
    public function create($data)
    {
        $currentUser = $this->getRcmUserService()->getCurrentUser();

        if (!$this->isAllowed($currentUser)) {
            return $this->getApiResponse(null, 401);
        }

        $service = $this->getSwitchUserService();

        $user = $service->getUser($data['switchToUserId']);

        if (empty($user)) {
            return $this->getApiResponse(
                null,
                400,
                new HttpStatusCodeApiMessage(400)
            );
        }

        try {
            $result = $service->switchToUser($user);
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

        return $this->getApiResponse($user);
    }
}
