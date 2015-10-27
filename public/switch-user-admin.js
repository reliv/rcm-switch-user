/**
 * rcmSwitchUserAdmin
 */
angular.module('rcmSwitchUser').directive(
    'rcmSwitchUserAdmin',
    [
        '$sce',
        'rcmSwitchUserService',
        'rcmEventManager',
        function (
            $sce,
            rcmSwitchUserService,
            rcmEventManager
        ) {

            /**
             *
             * @param $scope
             * @param element
             * @param attrs
             */
            function link($scope, element, attrs) {



                $scope.loading = true;
                $scope.isSu = false;
                $scope.impersonatedUser = null;
                $scope.switchBackMethod = 'auth';

                $scope.switchToUser = null;
                $scope.suUserPassword = null;

                var onSwitchToSuccess = function (response) {
                    //$scope.$apply();
                };

                var onSwitchToError = function (response) {
                    //$scope.$apply();
                };

                var onSwitchBackAndToSuccess = function (response) {
                    onSwitchBackSuccess(response);
                    switchTo();
                };

                var onSwitchBackSuccess = function (response) {
                    $scope.suUserPassword = null;
                    $scope.$apply();
                };

                var onSwitchBackError = function (response) {
                    $scope.suUserPassword = null;
                    $scope.$apply();
                };

                var switchTo = function() {
                    rcmSwitchUserService.switchUser(
                        $scope.switchToUser,
                        onSwitchToSuccess,
                        onSwitchToError
                    );
                };

                $scope.switchTo = function () {
                    if($scope.isSu) {
                        rcmSwitchUserService.switchUserBack(
                            $scope.switchToUser,
                            onSwitchBackAndToSuccess,
                            onSwitchBackError
                        );
                        return;
                    }

                    switchTo();
                };

                $scope.switchBack = function () {
                    rcmSwitchUserService.switchUserBack(
                        $scope.suUserPassword,
                        onSwitchBackSuccess,
                        onSwitchBackError
                    );
                };

                rcmEventManager.on(
                    'rcmSwitchUserService.suChange',
                    function (data) {
                        $scope.isSu = data.isSu;
                        $scope.impersonatedUser = data.impersonatedUser;
                        $scope.switchBackMethod = data.switchBackMethod;
                        $scope.loading = false;
                        //$scope.$apply();
                    }
                );
            }

            return {
                link: link,
                scope: {},
                templateUrl: '/modules/switch-user/switch-user-admin.html'
            }
        }
    ]
);
