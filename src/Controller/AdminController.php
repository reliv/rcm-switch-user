<?php

namespace Rcm\SwitchUser\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class AdminController
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   Rcm\SwitchUser\AdminController
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright ${YEAR} Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
class AdminController extends AbstractActionController
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
     * isAllowed
     *
     * @param $suUser
     *
     * @return bool|mixed
     */
    protected function isAllowed($suUser)
    {
        return $this->getSwitchUserService()->isAllowed($suUser);
    }

    /**
     * indexAction
     *
     * @return \Zend\Stdlib\ResponseInterface|ViewModel
     */
    public function indexAction()
    {
        $switchUserService = $this->getSwitchUserService();
        $rcmUserService = $this->getRcmUserService();

        $view = new ViewModel();

        $adminUser = $switchUserService->getCurrentImpersonatorUser();
        $targetUser = $rcmUserService->getCurrentUser();
        $view->setVariable(
            'targetUser',
            $targetUser
        );
        $view->setVariable(
            'switchBackMethod',
            $switchUserService->getSwitchBackMethod()
        );

        if (empty($adminUser)) {
            $view->setVariable('targetUser', null);
            $adminUser = $targetUser;
        }

        if (!$this->isAllowed($adminUser)) {
            $this->getResponse()->setStatusCode(401);

            return $this->getResponse();
        }

        $view->setVariable('adminUser', $adminUser);

        return $view;
    }
}
