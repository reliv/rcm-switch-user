/** RcmSwitch User Module **/
if (typeof rcm != 'undefined') {
    // RCM is undefined in unit tests
    rcm.addAngularModule('rcmSwitchUser');
}
angular.module('rcmSwitchUser', ['RcmLoading', 'RcmJsLib', 'rcmApiLib']);

/**
 * rcmSwitchUserAdmin
 */
angular.module('rcmSwitchUser').directive(
    'rcmSwitchUserSwitchToUser',
    [
        '$sce',
        '$window',
        function (
            $sce,
            $window
        ) {
            /**
             *
             * @param $scope
             * @param element
             * @param attrs
             */
            function link($scope, element, attrs) {

            }

            return {
                link: link,
                scope: {
                    propLoading: '=loading', // Bool
                    propIsSu: '=isSu', // Bool
                    propImpersonatedUser: '=impersonatedUser', // {User}
                    propSwitchBackMethod: '=switchBackMethod', // string ('auth' or 'basic')
                    propSwitchToUserName: '=switchToUserName', // string
                    propSuUserPassword: '=suUserPassword', // string
                    propMessage: '=message', // {message},
                    propOnSwitchTo: '=onSwitchTo', // function
                    propOnSwitchBack: '=onSwitchBack', // function
                },
                template: '<style type="text/css">    .switch-user-admin button,    .switch-user-admin label,    .switch-user-admin p {        margin: 5px 0px 5px 0px;    }    .switch-user-inject .alert {        padding: 3px;    }    .switch-user-inject .alert-caution {        background-color: #FFFFAA;        border-color: #FFFF00;        color: #999900;    }</style><div class="switch-user-admin container-fluid" ng-hide="propLoading">    <div class="row form-inline">        <div class="col-md-12" ng-show="propMessage">            <div class="alert alert-warning" role="alert">                {{propMessage.value}}            </div>        </div>        <div class="col-md-3" ng-show="propIsSu">            <label>Impersonating: {{propImpersonatedUser.id}} {{propImpersonatedUser.username}}</label>        </div>        <div class="col-md-4">            <form ng-submit="propOnSwitchTo()">                <input class="form-control input-sm"                       id="switchToUserName"                       placeholder="Username"                       ng-model="propSwitchToUserName"                       type="text"/>                <button class="btn btn-default btn-sm"                        type="submit">Switch to User                </button>            </form>        </div>        <div class="col-md-4" ng-show="propIsSu">            <form ng-submit="propOnSwitchBack()">                <input class="form-control input-sm"                       id="suUserPassword"                       placeholder="password"                       ng-model="propSuUserPassword"                       ng-show="switchBackMethod == \'auth\'"                       type="password"/>                <button class="btn btn-primary btn-sm"                        type="submit">End Impersonation                </button>            </form>        </div>    </div></div>'
            }
        }
    ]
);

/**
 * RcmSwitchUserService
 * @param $http
 * @param rcmLoading
 * @param rcmApiLibService
 * @param rcmEventManager
 * @constructor
 */
var RcmSwitchUserService = function ($http, rcmLoading, rcmApiLibService, rcmEventManager) {

    /**
     * self
     */
    var self = this;

    /**
     * config
     * @type {{suMessage: string}}
     */
    self.config = {
        suMessage: 'User is currently impersonating.'
    };

    /**
     * suData
     * @type {boolean}
     */
    self.suData = {
        isSu: false,
        impersonatedUser: null,
        switchBackMethod: 'auth'
    };

    /**
     * apiPaths
     * @type {{switchUser: string, switchUserBack: string}}
     */
    var apiPaths = {
        switchUser: '/api/rpc/switch-user',
        switchUserBack: '/api/rpc/switch-user-back'
    };

    /**
     * changeSu
     * @param data
     */
    var changeSu = function (data) {
        if (!data) {
            self.suData = {
                isSu: false,
                impersonatedUser: null,
                switchBackMethod: self.suData.switchBackMethod
            };
            return;
        }
        self.suData = data
    };

    /**
     * buildValidData
     * @param data
     * @returns {*}
     */
    var buildValidData = function (data) {
        if (!data) {
            data = {
                isSu: false,
                impersonatedUser: null,
                switchBackMethod: self.suData.switchBackMethod
            };
        }

        return data;
    };

    /**
     * onSuChange
     * @param data
     */
    var onSuChange = function (data) {

        data = buildValidData(data);

        changeSu(data);

        rcmEventManager.trigger(
            'rcmSwitchUserService.suChange',
            data
        );
    };

    /**
     * The suMayBeActive flag causes the SU system to only ask the server about SU info
     * if an SU has happened in this browser session.
     * Gets the cached data from the browser's "session" local storage.
     * This storage clears if the browser is closed.
     *
     * @returns {*}
     */
    function getSuMayBeActive() {
        var mayBeActive = false;
        if (typeof(sessionStorage) !== "undefined" && sessionStorage.rcmSwitchUser_suMayBeActive) {
            mayBeActive = JSON.parse(sessionStorage.rcmSwitchUser_suMayBeActive);
        }
        return mayBeActive;
    }

    /**
     * The suMayBeActive flag causes the SU system to only ask the server about SU info
     * if an SU has happened in this browser session.
     * Sets the cached data in the browser's "session" local storage
     * This storage clears if the browser is closed.
     *
     * @param data
     */
    function setSuMayBeActive(data) {
        if (typeof(sessionStorage) !== "undefined") {
            sessionStorage.rcmSwitchUser_suMayBeActive = JSON.stringify(data);
        }
    }

    /**
     * getSu
     * @param onSuccess
     * @param onError
     */
    self.getSu = function (onSuccess, onError) {

        rcmApiLibService.get(
            {
                url: apiPaths.switchUser,
                loading: function (loading) {
                    var loadingInt = Number(!loading);
                    rcmLoading.setLoading(
                        'rcmSwitchUserService.loading',
                        loadingInt
                    );
                },
                success: function (response) {
                    onSuChange(response.data);
                    onSuccess(response);
                },
                error: function (response) {
                    onSuChange(response.data);
                    onError(response);
                }
            }
        );
    };

    /**
     * switchUser
     * @param switchToUsername
     * @param onSuccess
     * @param onError
     */
    self.switchUser = function (switchToUsername, onSuccess, onError) {

        setSuMayBeActive(true);

        var data = {
            switchToUsername: switchToUsername
        };

        rcmApiLibService.post(
            {
                url: apiPaths.switchUser,
                data: data,
                loading: function (loading) {
                    var loadingInt = Number(!loading);
                    rcmLoading.setLoading(
                        'rcmSwitchUserService.loading',
                        loadingInt
                    );
                },
                success: function (response, status) {
                    onSuChange(
                        response.data
                    );
                    onSuccess(response, status);
                },
                error: function (response, status) {
                    onSuChange(response.data);
                    onError(response, status);
                }
            }
        );
    };

    /**
     * switchUserBack
     * @param suUserPassword
     * @param onSuccess
     * @param onError
     */
    self.switchUserBack = function (suUserPassword, onSuccess, onError) {

        var data = {
            suUserPassword: suUserPassword
        };

        rcmApiLibService.post(
            {
                url: apiPaths.switchUserBack,
                data: data,
                loading: function (loading) {
                    var loadingInt = Number(!loading);
                    rcmLoading.setLoading(
                        'rcmSwitchUserService.loading',
                        loadingInt
                    );
                },
                success: function (response, status) {
                    onSuChange();
                    onSuccess(response, status);
                },
                error: function (response, status) {
                    onSuChange();
                    onError(response, status);
                }
            }
        );
    };

    /**
     * init
     */
    var init = function () {
        if (!getSuMayBeActive()) {
            return;
        }

        self.getSu(
            function () {
            },
            function () {
            }
        )
    };

    init();
};

/**
 * rcmSwitchUserService
 */
angular.module('rcmSwitchUser').service(
    'rcmSwitchUserService',
    [
        '$http',
        'rcmLoading',
        'rcmApiLibService',
        'rcmEventManager',
        function ($http,
                  rcmLoading,
                  rcmApiLibService,
                  rcmEventManager) {
            return new RcmSwitchUserService(
                $http,
                rcmLoading,
                rcmApiLibService,
                rcmEventManager
            );
        }
    ]
);

/**
 * RcmSwitchUserMessageInject dom loader
 * @param $compile
 */
var RcmSwitchUserMessageInject = function (
    $compile
) {
    var self = this;

    self.injectHeader = function () {

        var content = '<div rcm-switch-user-message></div>';
        var element = jQuery(content);
        element.prependTo('body');

        var contents = element.contents();
        var aemlement = angular.element(element);
        var scope = aemlement.scope;

        $compile(contents)(scope);
    }
};

/**
 * rcmSwitchUserService
 */
angular.module('rcmSwitchUser').service(
    'rcmSwitchUserMessageInject',
    [
        '$compile',
        function (
            $compile
        ) {
            return new RcmSwitchUserMessageInject(
                $compile
            );
        }
    ]
);

/**
 * Example usage - To inject the switch user, add this code to your application
 */
angular.module('rcmSwitchUser').run(
    [
        'rcmSwitchUserMessageInject',
        function (
            rcmSwitchUserMessageInject
        ) {
            rcmSwitchUserMessageInject.injectHeader();
        }
    ]
);

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
                scope: {},
                template: '' +
                '<div class="switch-user-inject" ng-if="isSu">' +
                ' <div class="alert alert-caution" role="alert"> ' +
                '  <div rcm-switch-user-admin switch-to-user-name="null"></div> ' +
                ' </div> ' +
                '</div>'
            }
        }
    ]
);

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
                $scope.suUserPassword = null;
                $scope.message = null;

                var setLoading = function (isLoading) {
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
                var apiInit = function () {
                    setLoading(true);
                    $scope.message = null;
                };

                /**
                 *handleMessages
                 * @param messages
                 */
                var handleMessages = function (messages) {
                    $scope.message = null;
                    rcmApiLibMessageService.getPrimaryMessage(
                        messages,
                        function (message) {
                            if (message) {
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
                var switchTo = function () {
                    apiInit();
                    rcmSwitchUserService.switchUser(
                        $scope.propSwitchToUserName,
                        onSwitchToSuccess,
                        onSwitchToError
                    );
                };

                /**
                 * switchTo
                 */
                $scope.switchTo = function () {
                    if ($scope.isSu) {
                        apiInit();
                        rcmSwitchUserService.switchUserBack(
                            $scope.propSwitchToUserName,
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
                scope: {
                    propSwitchToUserName: '=switchToUserName'
                },
                template: '' +
                '<rcm-switch-user-switch-to-user' +
                ' loading="loading"' +
                ' is-su="isSu"' +
                ' impersonated-user="impersonatedUser"' +
                ' switch-back-method="switchBackMethod"' +
                ' switch-to-user-name="propSwitchToUserName"' +
                ' su-user-password="suUserPassword"' +
                ' message="message"' +
                ' on-switch-to="switchTo"' +
                ' on-switch-back="switchBack"' +
                '>' +
                '</rcm-switch-user-switch-to-user>'
            }
        }
    ]
);
