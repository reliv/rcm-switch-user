<?php
return [
    /* ASSET MANAGER */
    'asset_manager' => [
        'resolver_configs' => [
            'aliases' => [
                'modules/switch-user/' => __DIR__ . '/../public/',
            ],
            'collections' => [
                'modules/switch-user/switch-user.js' => [
                    'modules/switch-user/switch-user-module.js',
                    'modules/switch-user/switch-user-service.js',
                    'modules/switch-user/switch-user-message-inject.js',
                    'modules/switch-user/switch-user-message.js',
                    'modules/switch-user/switch-user-admin.js',
                ],
                'modules/rcm/modules.js' => [
                    'modules/switch-user/switch-user.js',
                ]
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
        'switchBackMethod' => 'auth',
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
                    'route' => '/switch-user',
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
            'Rcm\SwitchUser\Service\SwitchUserService' => [
                'arguments' => [
                    'config',
                    'RcmUser\Service\RcmUserService',
                    'Rcm\SwitchUser\Restriction',
                    'Doctrine\ORM\EntityManager',
                ]
            ],
        ],
        'factories' => [
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
