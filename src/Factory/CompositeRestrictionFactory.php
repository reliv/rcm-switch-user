<?php

namespace Rcm\SwitchUser\Factory;

use Interop\Container\ContainerInterface;
use Rcm\SwitchUser\Restriction\CompositeRestriction;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class CompositeRestrictionFactory
{
    /**
     * @param ContainerInterface|ServiceLocatorInterface $container
     *
     * @return CompositeRestriction
     */
    public function __invoke($container)
    {
        $config = $container->get('config');

        return new CompositeRestriction(
            $config,
            $container
        );
    }
}
