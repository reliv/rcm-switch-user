/**
 * rcmSwitchUserAdmin
 */
angular.module('rcmSwitchUser').directive(
    'rcmSwitchUserAdmin',
    [
        'rcmSwitchUserAdminService',
        function (
            rcmSwitchUserAdminService
        ) {
            return {
                link: rcmSwitchUserAdminService.link,
                scope: {
                    propShowSwitchToUserNameField: '=showSwitchToUserNameField', // bool
                    propSwitchToUserName: '=switchToUserName', // string
                    propSwitchToUserNameLabel: '=switchToUserNameLabel', // string
                    propSwitchBackLabel: '=switchBackLabel' // string
                },
                template: '' +
                '<rcm-switch-user-switch-to-user' +
                ' loading="loading"' +
                ' is-su="isSu"' +
                ' impersonated-user="impersonatedUser"' +
                ' switch-back-method="switchBackMethod"' +
                ' show-switch-to-user-name-field="propShowSwitchToUserNameField"' +
                ' switch-to-user-name="propSwitchToUserName"' +
                ' switch-to-user-name-label="propSwitchToUserNameLabel"' +
                ' switch-back-label="propSwitchBackLabel"' +
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
