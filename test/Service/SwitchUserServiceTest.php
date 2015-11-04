<?php

namespace Rcm\SwitchUser\Test\Service;

require_once(__DIR__ . '/../autoload.php');

use Rcm\SwitchUser\Restriction\Result;
use Rcm\SwitchUser\Service\SwitchUserService;
use RcmUser\User\Entity\User;

class SwitchUserServiceTest extends \PHPUnit_Framework_TestCase
{
    public function getUnit()
    {
        $this->configMock = [
            'Rcm\\SwitchUser' => [
                'switchBackMethod' => 'auth',
                'acl' => [
                    'resourceId' => 'switchuser',
                    'privilege' => 'execute',
                    'providerId' => 'Rcm\SwitchUser\Acl\ResourceProvider'
                ],
            ],
        ];

        /** @var \RcmUser\User\Entity\User $rcmUserMock */
        $this->rcmUserMock = $this->getMockBuilder('RcmUser\User\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->rcmUSerAuthenticationServiceMock = $this->getMockBuilder('RcmUser\Authentication\Service\UserAuthenticationService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->rcmUSerAuthenticationServiceMock->method('setIdentity')
        ->will($this->returnValue(null));

        /** @var \RcmUser\Service\RcmUserService $rcmUserServiceMock */
        $this->rcmUserServiceMock = $this->getMockBuilder('RcmUser\Service\RcmUserService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->rcmUserServiceMock->method('getCurrentUser')
            ->will($this->returnValue($this->rcmUserMock));

        $this->rcmUserServiceMock->method('getUserByUsername')
            ->will($this->returnValue($this->rcmUserMock));

        $this->rcmUserServiceMock->method('getUserAuthService')
            ->will($this->returnValue($this->rcmUSerAuthenticationServiceMock));

        /** @var Result $this->restrictionResultMock */
        $this->restrictionResultMock = $this->getMockBuilder(
            'Rcm\SwitchUser\Restriction\Result'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->restrictionResultMock->method('allowed')
            ->will($this->returnValue($this->restrictionResultMock));

        /** @var \Rcm\SwitchUser\Restriction\Restriction $this->restrictionMock */
        $this->restrictionMock = $this->getMockBuilder(
            'Rcm\SwitchUser\Restriction\Restriction'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->restrictionMock->method('isAllowed')
            ->will($this->returnValue(true));

        $this->restrictionMock->method('getMessage')
            ->will($this->returnValue(''));

        /** @var \Doctrine\ORM\EntityManager $entityMangerMock */
        $this->entityMangerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $unit = new SwitchUserService(
            $this->configMock,
            $this->rcmUserServiceMock,
            $this->restrictionMock,
            $this->entityMangerMock
        );

        return $unit;
    }

   public function testGetSwitchBackMethod()
    {
        $unit = $this->getUnit();

        $result = $unit->getSwitchBackMethod();

        $this->assertEquals('auth', $result);
    }

    public function testGetUser()
    {
        $unit = $this->getUnit();

        $result = $unit->getUser('something');

        $this->assertInstanceOf('\RcmUser\User\Entity\User', $result);
    }

    public function switchToUser()
    {
        /* HAPPY PATH */
        $unit = $this->getUnit();

        $targetUser = new User('123');

        $result = $unit->switchToUser($targetUser);

        $this->assertInstanceOf('\Rcm\SwitchUser\Result', $result);
        $this->assertTrue($result->isSuccess());

        /* NO USER */
        $unit = $this->getUnit();
        $this->rcmUserServiceMock->method('getCurrentUser')
            ->will($this->returnValue(null));

        $result = $unit->switchToUser($nulletUser);

        $this->assertInstanceOf('\Rcm\SwitchUser\Result', $result);
        $this->assertTrue($result->isSuccess());

        /* RESTRICTION */
        $unit = $this->getUnit();
        $this->rcmUserServiceMock->method('getCurrentUser')
            ->will($this->returnValue(null));

        $result = $unit->switchToUser($nulletUser);

        $this->assertInstanceOf('\Rcm\SwitchUser\Result', $result);
        $this->assertTrue($result->isSuccess());

    }
}
