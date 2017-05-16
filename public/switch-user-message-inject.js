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
// angular.module('rcmSwitchUser').run(
//     [
//         'rcmSwitchUserMessageInject',
//         function (
//             rcmSwitchUserMessageInject
//         ) {
//             rcmSwitchUserMessageInject.injectHeader();
//         }
//     ]
// );
