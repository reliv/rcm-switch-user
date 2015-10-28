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
        'rcmLoading',
        function (
            $sce,
            rcmSwitchUserService,
            rcmEventManager,
            rcmApiLibMessageService,
            $window,
            rcmLoading
        ) {

            /**
             *
             * @param $scope
             * @param element
             * @param attrs
             */
            function link($scope, element, attrs) {

                $scope.loading = false;
                $scope.isSu = false;
                $scope.impersonatedUser = null;
                $scope.switchBackMethod = 'auth';
                $scope.switchToUser = null;
                $scope.suUserPassword = null;
                $scope.message = null;

                var setLoading = function(isLoading) {
                    $scope.loading = isLoading;
                    var loadingInt = Number(!isLoading);
                    rcmLoading.setLoading(
                        'rcmSwitchUserAdmin.loading',
                        loadingInt
                    );
                };

                /**
                 * apiInit
                 */
                var apiInit = function() {
                    setLoading(true);
                    $scope.message = null;
                };

                /**
                 *handleMessages
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
                 * onSwitchToSuccess
                 * @param response
                 */
                var onSwitchToSuccess = function (response) {
                    $window.location.reload();
                };

                /**
                 * onSwitchToError
                 * @param response
                 */
                var onSwitchToError = function (response) {
                    handleMessages(response.messages);
                    setLoading(false);
                };

                /**
                 * onSwitchBackAndToSuccess
                 * @param response
                 */
                var onSwitchBackAndToSuccess = function (response) {
                    $scope.suUserPassword = null;
                    switchTo();
                };

                /**
                 * onSwitchBackSuccess
                 * @param response
                 */
                var onSwitchBackSuccess = function (response) {
                    $scope.suUserPassword = null;
                    $window.location.reload();
                };

                /**
                 * onSwitchBackError
                 * @param response
                 */
                var onSwitchBackError = function (response) {
                    $scope.suUserPassword = null;
                    handleMessages(response.messages);
                    setLoading(false);
                };

                /**
                 * switchTo
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
                 * switchTo
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
                 * switchBack
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
                 * rcmEventManager.on
                 */
                rcmEventManager.on(
                    'rcmSwitchUserService.suChange',
                    function (data) {
                        $scope.isSu = data.isSu;
                        $scope.impersonatedUser = data.impersonatedUser;
                        $scope.switchBackMethod = data.switchBackMethod;
                        //$scope.loading = false;
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
