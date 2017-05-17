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
                '<div class="switch-user-inject" ng-if="isSu">' +
                ' <div class="alert alert-caution" role="alert"> ' +
                '  <div rcm-switch-user-admin ' +
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
