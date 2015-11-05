<?php

namespace Rcm\SwitchUser\Service;

use Doctrine\ORM\EntityManager;
use Rcm\SwitchUser\Entity\LogEntry;

/**
 * Class SwitchUserLogService
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   Rcm\SwitchUser\Test\Service
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
class SwitchUserLogService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * logAction
     *
     * @param string $adminUserId
     * @param string $targetUserId
     * @param string $action
     * @param bool   $actionSuccess
     *
     * @return void
     */
    public function logAction($adminUserId, $targetUserId, $action, $actionSuccess)
    {
        $entry = new LogEntry($adminUserId, $targetUserId, $action, $actionSuccess);

        $this->entityManager->persist($entry);
        $this->entityManager->flush($entry);
    }
}
