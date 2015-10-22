<?php
return [
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
    /* Configuration */
    'Rcm\\SwitchUser' => [
        'restrictions' => [
            'Rcm\SwitchUser\Restriction\AclRestriction',
            'Rcm\SwitchUser\Restriction\SuUserRestriction',
        ],
        'acl' => [
            'resourceId' => 'sites',
            'privilege' => 'admin',
            'providerId' => 'Rcm\Acl\ResourceProvider'
        ],
        /*
         * 'basic' = no auth required
         * 'auth'  = password auth required to switch back to admin
         */
        'switchBackMethod' => 'auth',
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
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
