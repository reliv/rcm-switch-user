/**
 * RcmSwitchUserMessageInject dom loader
 *
 * @param $compile
 * @param JSON
 * @constructor
 */
var RcmSwitchUserMessageInject = function (
    $compile,
    JSON
) {
    var self = this;

    self.injectHeader = function (
        showSwitchToUserNameField,
        switchToUserName
    ) {
        // default true
        if (typeof showSwitchToUserNameField === 'undefined') {
            showSwitchToUserNameField = true;
        }

        // default null
        if (typeof switchToUserName === 'undefined') {
            switchToUserName = '';
        }

        showSwitchToUserNameField = JSON.stringify(showSwitchToUserNameField);
        switchToUserName = JSON.stringify(switchToUserName);

        var content = '' +
            '<div rcm-switch-user-message' +
            ' show-switch-to-user-name-field="' + showSwitchToUserNameField + '"' +
            ' switch-to-user-name="' + switchToUserName + '"' +
            '></div>';

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
                $compile,
                JSON
            );
        }
    ]
);

/**
 * Example usage - To inject the switch user header bar, add this code to your application
 */
angular.module('rcmSwitchUser').run(
    [
        'rcmSwitchUserMessageInject',
        function (
            rcmSwitchUserMessageInject
        ) {
            rcmSwitchUserMessageInject.injectHeader(true, null);
        }
    ]
);
