/**
 * rcmSwitchUserAdmin
 */
angular.module('rcmSwitchUser').directive(
    'rcmSwitchUserAdminSimple',
    [
        'rcmSwitchUserAdminService',
        function (
            rcmSwitchUserAdminService
        ) {
            return {
                link: rcmSwitchUserAdminService.link,
                scope: rcmSwitchUserAdminService.scope,
                template: '' +
                '<rcm-switch-user-switch-to-user-simple' +
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
                '</rcm-switch-user-switch-to-user-simple>'
            }
        }
    ]
);
