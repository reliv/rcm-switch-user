module.exports = function (grunt) {

    var files = [
        'switch-user-module.js',
        'switch-to-user-directive.js',
        'switch-user-service.js',
        'switch-user-message-inject.js',
        'switch-user-message.js',
        'switch-user-admin.js'
    ];

    grunt.initConfig(
        {
            pkg: grunt.file.readJSON('package.json'),
            concat: {
                options: {},
                switchUser: {
                    files: {
                        'dist/switch-user.js': files
                    }
                },
            },
            inlineTemplate: {
                options: {},
                dist: {
                    src: ['dist/switch-user.js'],
                    dest: 'dist/switch-user.js'
                }
            },
            uglify: {
                switchUser: {
                    options: {
                        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                        mangle: false,
                        sourceMap: true
                    },
                    files: {
                        'dist/switch-user.min.js': ['dist/switch-user.js']
                    }
                },
            },

            watch: {
                src: {
                    files: [
                        'Gruntfile.js',
                        '**/*.js',
                        '**/*.css'
                    ],
                    tasks: ['concat', 'inlineTemplate', 'uglify']
                }
            }
        }
    );

    grunt.loadNpmTasks('grunt-forever');//@todo remove doesn't work
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-inline-template');
    grunt.registerTask('default', ['concat', 'inlineTemplate', 'uglify']);

};
