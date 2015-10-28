/**
 * rcmSwitchUserAdmin
 */
angular.module('rcmSwitchUser').directive(
    'rcmSwitchUserAdmin',
    [
        '$sce',
        'rcmSwitchUserService',
        'rcmEventManager',
        'rcmApiLibMessageService',
        function (
            $sce,
            rcmSwitchUserService,
            rcmEventManager,
            rcmApiLibMessageService
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

                $scope.message = null;

                var apiInit = function() {
                    $scope.loading = true;
                    $scope.message = null;
                };

                var handleMessages = function(messages) {
                    $scope.message = null;
                    rcmApiLibMessageService.getPrimaryMessage(
                        messages,
                        function(message) {
                            if(message) {
                                $scope.message = message;
                            }
                        }
                    );
                };

                var onSwitchToSuccess = function (response) {
                    //$scope.$apply();
                };

                var onSwitchToError = function (response) {
                    //$scope.$apply();
                    handleMessages(response.messages);
                };

                var onSwitchBackAndToSuccess = function (response) {
                    onSwitchBackSuccess(response);
                    switchTo();
                };

                var onSwitchBackSuccess = function (response) {
                    $scope.suUserPassword = null;
                    handleMessages(response.messages);
                    //$scope.$apply();
                };

                var onSwitchBackError = function (response) {
                    $scope.suUserPassword = null;
                    handleMessages(response.messages);
                    //$scope.$apply();
                };

                var switchTo = function() {
                    apiInit();
                    rcmSwitchUserService.switchUser(
                        $scope.switchToUser,
                        onSwitchToSuccess,
                        onSwitchToError
                    );
                };

                $scope.switchTo = function () {
                    if($scope.isSu) {
                        apiInit();
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
                    apiInit();
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
