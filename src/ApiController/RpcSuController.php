<?php

namespace Rcm\SwitchUser\ApiController;

use RcmUser\User\Entity\User;
use Reliv\RcmApiLib\Model\ApiMessage;
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
     * @param array $data ['switchToUsername' => '{MY_ID}']
     *
     * @return \Reliv\RcmApiLib\Http\ApiResponse
     */
    public function getList()
    {
        $service = $this->getSwitchUserService();

        $suUser = $service->getCurrentImpersonatorUser();

        $resultData = $this->buildResult(false, null);

        if (empty($suUser)) {
            return $this->getApiResponse($resultData);
        }

        $resultData = $this->buildResult(
            true,
            $this->getRcmUserService()->getCurrentUser()
        );

        return $this->getApiResponse($resultData);
    }

    /**
     * create
     *
     * @param array $data ['switchToUsername' => '{MY_ID}']
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

        $user = $service->getUser($data['switchToUsername']);

        $resultData = $this->buildResult(false, null);

        if (empty($user)) {
            return $this->getApiResponse(
                $resultData,
                400,
                new ApiMessage('httpStatus', 'Switch user request is not valid', 'rpcSu', '400', true)
            );
        }

        try {
            $result = $service->switchToUser($user);
        } catch (\Exception $exception) {
            return $this->getApiResponse(
                $resultData,
                500,
                new ExceptionApiMessage($exception)
            );
        }

        if (!$result->isSuccess()) {
            return $this->getApiResponse(
                $resultData,
                406,
                new ApiMessage('failure', $result->getMessage(), 'rpcSu', 'invalid')
            );
        }

        $resultData = $this->buildResult(true, $user);

        return $this->getApiResponse($resultData);
    }

    /**
     * buildResult
     *
     * @param bool      $isSu
     * @param User|null $impersonatedUser
     *
     * @return mixed
     */
    protected function buildResult($isSu, $impersonatedUser)
    {
        $data = [];
        $data['isSu'] = $isSu;
        $data['impersonatedUser'] = $impersonatedUser;
        $data['switchBackMethod'] = $this->getSwitchUserService()
            ->getSwitchBackMethod();

        return $data;
    }
}
