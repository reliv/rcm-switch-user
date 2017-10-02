<?php

namespace Rcm\SwitchUser\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * @author James Jervis - https://github.com/jerv13
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
