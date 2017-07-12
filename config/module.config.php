<?php
return [
    /* Inject script onto RCM pages */
    'Rcm' => [
        'HtmlIncludes' => [
            'scripts' => [
                'modules' => [
                    '/modules/switch-user/dist/switch-user.min.js' => []
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
            \Rcm\SwitchUser\ApiController\RpcSuController::class
            => \Rcm\SwitchUser\ApiController\RpcSuController::class,
            \Rcm\SwitchUser\ApiController\RpcSwitchBackController::class
            => \Rcm\SwitchUser\ApiController\RpcSwitchBackController::class,
            \Rcm\SwitchUser\Controller\AdminController::class
            => \Rcm\SwitchUser\Controller\AdminController::class,
        ],
    ],
    /* DOCTRINE */
    'doctrine' => [
        'driver' => [
            'Rcm\SwitchUser' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
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
            \Rcm\SwitchUser\Restriction\AclRestriction::class,
            \Rcm\SwitchUser\Restriction\SuUserRestriction::class,
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
            'basic' => \Rcm\SwitchUser\Switcher\BasicSwitcher::class,
            'auth' => \Rcm\SwitchUser\Switcher\AuthSwitcher::class,
        ]
    ],
    /* Plugin Config */
    'rcmPlugin' => [
        'RcmSwitchUser' => [
            'type' => 'Admin',
            'display' => 'Switch User',
            'tooltip' => 'Switch User Admin options',
            'icon' => '',
            'defaultInstanceConfig' => [
                'showSwitchToUserNameField' => 'true',
                'switchToUserNamePlaceholder' => 'Username',
                'switchToUserNameButtonLabel' => 'Switch to User',
                'switchBackButtonLabel' => 'End Impersonation',
                'switchUserInfoContentPrefix' => 'Impersonating:'
            ],
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
                        'controller' => \Rcm\SwitchUser\ApiController\RpcSuController::class,
                    ]
                ]
            ],
            'Rcm\SwitchUser\ApiController\RpcSwitchBack' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route' => '/api/rpc/switch-user-back[/:id]',
                    'defaults' => [
                        'controller' => \Rcm\SwitchUser\ApiController\RpcSwitchBackController::class,
                    ]
                ]
            ],
            'Rcm\SwitchUser\Controller\Admin' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route' => '/admin/switch-user',
                    'defaults' => [
                        'controller' => \Rcm\SwitchUser\Controller\AdminController::class,
                        'action' => 'index',
                    ]
                ]
            ],
        ],
    ],
    /* SERVICE MANAGER */
    'service_manager' => [
        'config_factories' => [
            \Rcm\SwitchUser\Middleware\RcmSwitchUserAcl::class => [
                'arguments' => [
                    \Rcm\SwitchUser\Service\SwitchUserAclService::class,
                ]
            ],
            \Rcm\SwitchUser\Restriction\AclRestriction::class => [
                'arguments' => [
                    'config',
                    \RcmUser\Service\RcmUserService::class,
                ]
            ],
            \Rcm\SwitchUser\Restriction\SuUserRestriction::class => [
                'arguments' => [
                    'config',
                    \RcmUser\Service\RcmUserService::class,
                ]
            ],
            /* Services */
            \Rcm\SwitchUser\Service\SwitchUserAclService::class => [
                'arguments' => [
                    'config',
                    \RcmUser\Service\RcmUserService::class,
                    \Rcm\SwitchUser\Service\SwitchUserService::class,
                ]
            ],
            \Rcm\SwitchUser\Service\SwitchUserLogService::class => [
                'arguments' => [
                    'Doctrine\ORM\EntityManager',
                ]
            ],
            \Rcm\SwitchUser\Service\SwitchUserService::class => [
                'arguments' => [
                    'config',
                    \RcmUser\Service\RcmUserService::class,
                    \Rcm\SwitchUser\Restriction\Restriction::class,
                    \Rcm\SwitchUser\Switcher\Switcher::class,
                    \Rcm\SwitchUser\Service\SwitchUserLogService::class,
                ]
            ],
            /* Switchers */
            \Rcm\SwitchUser\Switcher\BasicSwitcher::class => [
                'arguments' => [
                    \RcmUser\Service\RcmUserService::class,
                    \Rcm\SwitchUser\Service\SwitchUserLogService::class,
                ]
            ],
            \Rcm\SwitchUser\Switcher\AuthSwitcher::class => [
                'arguments' => [
                    \RcmUser\Service\RcmUserService::class,
                    \Rcm\SwitchUser\Service\SwitchUserLogService::class,
                ]
            ],
        ],
        'factories' => [
            /* DEFAULT Switcher*/
            \Rcm\SwitchUser\Switcher\Switcher::class => \Rcm\SwitchUser\Factory\SwitcherServiceFactory::class,
            \Rcm\SwitchUser\Restriction\Restriction::class => \Rcm\SwitchUser\Factory\CompositeRestrictionFactory::class
        ],
    ],
    /* VIEW MANAGER*/
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
