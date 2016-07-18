/**
 * rcmSwitchUserMessage
 */
angular.module('rcmSwitchUser').directive('rcmSwitchUserMessage', [
    '$sce',
    'rcmSwitchUserService',
    'rcmEventManager',
    function ($sce, rcmSwitchUserService, rcmEventManager) {
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
            scope: {},
            template: '<div class="rcm-switch-user-inject" ng-if="isSu">' +
            '<div class="alert alert-caution" role="alert"> ' +
            '<div rcm-switch-user-admin></div> ' +
            '</div> ' +
            '</div>'
        }
    }
]);
