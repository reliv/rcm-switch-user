<?php
/**
 * Module Config For ZF2
 */

namespace Rcm\SwitchUser;

/**
 * Class Module
 */
class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
