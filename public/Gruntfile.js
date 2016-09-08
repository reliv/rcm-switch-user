var exec = require('sync-exec');

if (require('os').platform() != 'linux') {
    console.warn('You must run grunt on vagrant. Running on MacOS causes DB errors right now.');
}

/**
 * Synchronously run a CLI command that returns JSON and return the result.
 */
function executeAndGetJson(command) {
    var result = exec(command);
    if (result['stderr']) {
        console.error(result['stderr']);
    }
    return JSON.parse(result['stdout']);
}

/**
 * Get the list of source scripts from the PHP app
 */
function getSourceScripts() {
    return executeAndGetJson(
        'ENV=local php public/index.php list-html-includes-scripts'
    );
}

/**
 * Get the list of source stylesheets from the PHP app
 */
function getSourceStyleSheets() {
    return executeAndGetJson(
        'ENV=local php public/index.php list-html-includes-stylesheets'
    );
}

module.exports = function (grunt) {

    grunt.initConfig(
        {
            concat: {
                generated: {
                    files: [
                        {
                            dest: 'dist/switch-user.js',
                            src: [
                                'modules/switch-user/switch-user-module.js',
                                'modules/switch-user/switch-user-service.js',
                                'modules/switch-user/switch-user-message-inject.js',
                                'modules/switch-user/switch-user-message.js',
                                'modules/switch-user/switch-user-admin.js'
                            ]
                        }
                    ]
                }
            },
            watch: {
                src: {
                    files: [
                        'Gruntfile.js',
                        '**/*.js',
                        '**/*.css'
                    ],
                    tasks: ['concat']
                }
            }
        }
    );

    grunt.loadNpmTasks('grunt-forever');//@todo remove doesn't work
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.registerTask('default', ['concat']);

};
