/**
 * RcmSwitchUserMessageInject dom loader
 * @param $compile
 */
var rcmSwitchUserMessageInject = function (
    $compile
) {
    var content = '<div rcm-switch-user-message></div>';
    var element = jQuery(content);
    element.prependTo('body');

    var contents = element.contents();
    var aemlement = angular.element(element);
    var scope = aemlement.scope;

    $compile(contents)(scope);
};

/**
 * run
 */
angular.module('rcmSwitchUser').run(
    [
        '$compile',
        function (
            $compile
        ) {
            rcmSwitchUserMessageInject(
                $compile
            );
        }
    ]
);
