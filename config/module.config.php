<?php
return [
    /* Inject script onto RCM pages */
    'Rcm' => [
        'HtmlIncludes' => [
            'scripts' => [
                'modules' => [
                    '/modules/switch-user/dist/switch-user.js' => []
                ],
            ]
        ]
    ],
    /* ASSET MANAGER */
    'asset_manager' => [
        'resolver_configs' => [
            'aliases' => [
                'modules/switch-user/' => __DIR__ . '/../public/',
            ],
        ],
    ],
    /* CONTROLLERS */
    'controllers' => [
        'invokables' => [
            'Rcm\SwitchUser\ApiController\RpcSuController'
            => 'Rcm\SwitchUser\ApiController\RpcSuController',
            'Rcm\SwitchUser\ApiController\RpcSwitchBackController'
            => 'Rcm\SwitchUser\ApiController\RpcSwitchBackController',
            'Rcm\SwitchUser\Controller\AdminController'
            => 'Rcm\SwitchUser\Controller\AdminController',
        ],
    ],
    /* DOCTRINE */
    'doctrine' => [
        'driver' => [
            'Rcm\SwitchUser' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity'
                ]
            ],
            'orm_default' => [
                'drivers' => [
                    'Rcm\SwitchUser' => 'Rcm\SwitchUser'
                ]
            ]
        ]
    ],
    /* Rcm\SwitchUser Configuration */
    'Rcm\\SwitchUser' => [
        'restrictions' => [
            'Rcm\SwitchUser\Restriction\AclRestriction',
            'Rcm\SwitchUser\Restriction\SuUserRestriction',
        ],
        'acl' => [
            'resourceId' => 'switchuser',
            'privilege' => 'execute',
            'providerId' => 'Rcm\SwitchUser\Acl\ResourceProvider'
        ],
        /*
         * 'basic' = no auth required
         * 'auth'  = password auth required to switch back to admin
         */
        'switcherMethod' => 'auth',
        /**
         * register switchers
         * ['{switcherMethod}' => '{ServiceName}']
         */
        'switcherServices' => [
            'basic' => 'Rcm\SwitchUser\Switcher\BasicSwitcher',
            'auth' => 'Rcm\SwitchUser\Switcher\AuthSwitcher',
        ]
    ],
    /* Plugin Config */
    'rcmPlugin' => [
        'RcmSwitchUser' => [
            'type' => 'Admin',
            'display' => 'Switch User',
            'tooltip' => 'Switch User Admin options',
            'icon' => '',
            'defaultInstanceConfig' => [],
            'canCache' => false
        ],
    ],
    /* RcmUser Config */
    'RcmUser' => [
        'Acl\Config' => [
            'ResourceProviders' => [
                'Rcm\SwitchUser\Acl\ResourceProvider' => [
                    'switchuser' => [
                        'resourceId' => 'switchuser',
                        'parentResourceId' => null,
                        'privileges' => [
                            'execute',
                        ],
                        'name' => 'RCM Switch User.',
                        'description' => 'Switch user ACL resource.',
                    ],
                ],
            ],
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
            'Rcm\SwitchUser\Controller\Admin' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route' => '/admin/switch-user',
                    'defaults' => [
                        'controller' => 'Rcm\SwitchUser\Controller\AdminController',
                        'action' => 'index',
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
            'Rcm\SwitchUser\Restriction\SuUserRestriction' => [
                'arguments' => [
                    'config',
                    'RcmUser\Service\RcmUserService',
                ]
            ],
            /* Services */
            'Rcm\SwitchUser\Service\SwitchUserAclService' => [
                'arguments' => [
                    'config',
                    'RcmUser\Service\RcmUserService',
                    'Rcm\SwitchUser\Service\SwitchUserService',
                ]
            ],
            'Rcm\SwitchUser\Service\SwitchUserLogService' => [
                'arguments' => [
                    'Doctrine\ORM\EntityManager',
                ]
            ],
            'Rcm\SwitchUser\Service\SwitchUserService' => [
                'arguments' => [
                    'config',
                    'RcmUser\Service\RcmUserService',
                    'Rcm\SwitchUser\Restriction',
                    'Rcm\SwitchUser\Switcher',
                    'Rcm\SwitchUser\Service\SwitchUserLogService',
                ]
            ],
            /* Switchers */
            'Rcm\SwitchUser\Switcher\BasicSwitcher' => [
                'arguments' => [
                    'RcmUser\Service\RcmUserService',
                    'Rcm\SwitchUser\Service\SwitchUserLogService',
                ]
            ],
            'Rcm\SwitchUser\Switcher\AuthSwitcher' => [
                'arguments' => [
                    'RcmUser\Service\RcmUserService',
                    'Rcm\SwitchUser\Service\SwitchUserLogService',
                ]
            ],
        ],
        'factories' => [
            'Rcm\SwitchUser\Switcher' => 'Rcm\SwitchUser\Factory\SwitcherServiceFactory',
            'Rcm\SwitchUser\Restriction' => 'Rcm\SwitchUser\Factory\CompositeRestrictionFactory'
        ],
    ],
    /* VIEW MANAGER*/
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
