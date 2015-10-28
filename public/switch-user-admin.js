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
        '$window',
        function (
            $sce,
            rcmSwitchUserService,
            rcmEventManager,
            rcmApiLibMessageService,
            $window
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

                /**
                 *
                 */
                var apiInit = function() {
                    $scope.loading = true;
                    $scope.message = null;
                };

                /**
                 *
                 * @param messages
                 */
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

                /**
                 *
                 * @param response
                 */
                var onSwitchToSuccess = function (response) {
                };

                /**
                 *
                 * @param response
                 */
                var onSwitchToError = function (response) {
                    handleMessages(response.messages);
                };

                /**
                 *
                 * @param response
                 */
                var onSwitchBackAndToSuccess = function (response) {
                    onSwitchBackSuccess(response);
                    switchTo();
                };

                /**
                 *
                 * @param response
                 */
                var onSwitchBackSuccess = function (response) {
                    $scope.suUserPassword = null;
                    handleMessages(response.messages);
                };

                /**
                 *
                 * @param response
                 */
                var onSwitchBackError = function (response) {
                    $scope.suUserPassword = null;
                    handleMessages(response.messages);
                };

                /**
                 *
                 */
                var switchTo = function() {
                    apiInit();
                    rcmSwitchUserService.switchUser(
                        $scope.switchToUser,
                        onSwitchToSuccess,
                        onSwitchToError
                    );
                };

                /**
                 *
                 */
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

                /**
                 *
                 */
                $scope.switchBack = function () {
                    apiInit();
                    rcmSwitchUserService.switchUserBack(
                        $scope.suUserPassword,
                        onSwitchBackSuccess,
                        onSwitchBackError
                    );
                };

                /**
                 *
                 */
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
