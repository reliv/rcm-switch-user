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

    self.defaults = {
        showSwitchToUserNameField: true,
        switchToUserName: '',
        switchToUserNameLabel: 'Switch to User',
        switchBackLabel: 'End Impersonation'
    };

    /**
     *
     * @param {boolean} showSwitchToUserNameField
     * @param {string} switchToUserName
     * @param {string} switchToUserNameLabel
     * @param {string} switchBackLabel
     */
    self.injectHeader = function (
        showSwitchToUserNameField,
        switchToUserName,
        switchToUserNameLabel,
        switchBackLabel
    ) {
        // default true
        if (typeof showSwitchToUserNameField === 'undefined') {
            showSwitchToUserNameField = self.defaults.showSwitchToUserNameField;
        }

        // default null
        if (typeof switchToUserName === 'undefined') {
            switchToUserName = self.defaults.switchToUserName;
        }

        // default null
        if (typeof switchToUserNameLabel === 'undefined') {
            switchToUserNameLabel = self.defaults.switchToUserNameLabel;
        }

        // default null
        if (typeof switchBackLabel === 'undefined') {
            switchBackLabel = self.defaults.switchBackLabel;
        }

        showSwitchToUserNameField = Boolean(showSwitchToUserNameField);
        showSwitchToUserNameField = JSON.stringify(showSwitchToUserNameField);
        switchToUserName = String(switchToUserName);
        switchToUserNameLabel = String(switchToUserNameLabel);
        switchBackLabel = String(switchBackLabel);

        var content = '' +
            '<div rcm-switch-user-message' +
            ' show-switch-to-user-name-field="' + showSwitchToUserNameField + '"' +
            ' switch-to-user-name="\'' + switchToUserName + '\'"' +
            ' switch-to-user-name-label="\'' + switchToUserNameLabel + '\'"' +
            ' switch-back-label="\'' + switchBackLabel + '\'"' +
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
            rcmSwitchUserMessageInject.injectHeader(
                rcmSwitchUserMessageInject.defaults.showSwitchToUserNameField,
                rcmSwitchUserMessageInject.defaults.switchToUserName,
                rcmSwitchUserMessageInject.defaults.switchToUserNameLabel,
                rcmSwitchUserMessageInject.defaults.switchBackLabel
            );
        }
    ]
);
