<?php
return [
    /* CONTROLLERS */
    'controllers' => [
        'invokables' => [
            'Rcm\SwitchUser\ApiController\RpcSuController'
            => 'Rcm\SwitchUser\ApiController\RpcSuController',
            'Rcm\SwitchUser\ApiController\RpcSwitchBackController'
            => 'Rcm\SwitchUser\ApiController\RpcSwitchBackController',
        ],
    ],
    /* Configuration */
    'Rcm\\SwitchUser' => [
        'restrictions' => [
            'Rcm\SwitchUser\Restriction\AclRestriction'
        ],
        'acl' => [
            'resourceId' => 'sites',
            'privilege' => 'admin',
            'providerId' => 'Rcm\Acl\ResourceProvider'
        ],
    ],
    /* ROUTES */
    'router' => [
        'routes' => [
            'Rcm\SwitchUser\ApiController\RpcSu' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route' => '/api/rpc/switch-user[/:id]',
                    'defaults' => [
                        'controller' => 'Rcm\SwitchUser\ApiController\RpcSuController',
                    ]
                ]
            ],
            'Rcm\SwitchUser\ApiController\RpcSwitchBack' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route' => '/api/rpc/switch-user-back[/:id]',
                    'defaults' => [
                        'controller' => 'Rcm\SwitchUser\ApiController\RpcSwitchBackController',
                    ]
                ]
            ],
        ],
    ],
    /* SERVICE MANAGER */
    'service_manager' => [
        'config_factories' => [
            'Rcm\SwitchUser\Restriction\AclRestriction' => [
                'arguments' => [
                    'config',
                    'RcmUser\Service\RcmUserService',
                ]
            ],
            'Rcm\SwitchUser\Service\SwitchUserService' => [
                'arguments' => [
                    'RcmUser\Service\RcmUserService',
                    'RcmUser\Service\RcmUserService',
                ]
            ],
        ],
        'factories' => [
            'Rcm\SwitchUser\Restriction' => 'Rcm\SwitchUser\Factory\CompositeRestrictionFactory'
        ],
    ],
];
