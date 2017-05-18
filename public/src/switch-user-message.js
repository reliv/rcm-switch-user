/**
 * rcmSwitchUserMessage
 */
angular.module('rcmSwitchUser').directive(
    'rcmSwitchUserMessage', [
        '$sce',
        'rcmSwitchUserService',
        'rcmEventManager',
        function (
            $sce,
            rcmSwitchUserService,
            rcmEventManager
        ) {
            /**
             * Link function
             *
             * @param $scope
             * @param element
             * @param attrs
             */
            function link($scope, element, attrs) {

                $scope.loading = true;

                $scope.isSu = false;

                $scope.impersonatedUser = null;

                rcmEventManager.on(
                    'rcmSwitchUserService.suChange',
                    function (data) {
                        $scope.isSu = data.isSu;
                        $scope.impersonatedUser = data.impersonatedUser;
                        $scope.loading = false;
                    }
                );
            }

            return {
                link: link,
                scope: {
                    propShowSwitchToUserNameField: '=showSwitchToUserNameField', // bool
                    propSwitchToUserName: '=switchToUserName', // string
                    propSwitchToUserNameLabel: '=switchToUserNameLabel', // string
                    propSwitchBackLabel: '=switchBackLabel' // string
                },
                template: '' +
                '<style type="text/css">' +
                '    .switch-user-message .alert {' +
                '        padding: 3px;' +
                '    }' +
                '    .switch-user-message .alert-caution {' +
                '       background-color: #FFFFAA;' +
                '       border-color: #FFFF00;' +
                '       color: #999900;' +
                '   }' +
                '</style>' +
                '<div class="switch-user-message" ng-if="isSu">' +
                ' <div class="alert alert-caution" role="alert"> ' +
                '  <div rcm-switch-user-admin-horizontal ' +
                '       show-switch-to-user-name-field="propShowSwitchToUserNameField"' +
                '       switch-to-user-name="propSwitchToUserName"' +
                '       switch-to-user-name-label="propSwitchToUserNameLabel"' +
                '       switch-back-label="propSwitchBackLabel"' +
                '  ></div> ' +
                ' </div> ' +
                '</div>'
            }
        }
    ]
);
