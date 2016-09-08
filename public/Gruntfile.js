module.exports = function (grunt) {

    grunt.initConfig(
        {
            concat: {
                generated: {
                    files: [
                        {
                            dest: 'dist/switch-user.js',
                            src: [
                                'switch-user-module.js',
                                'switch-user-service.js',
                                'switch-user-message-inject.js',
                                'switch-user-message.js',
                                'switch-user-admin.js'
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
