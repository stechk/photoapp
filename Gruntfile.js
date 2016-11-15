module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({

            pkg: grunt.file.readJSON('node_modules/grunt-contrib-uglify/package.json'),
            uglify: {
                options: {
                   // banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                    beautify: true,
                    mangle: true
                },
                my_target: {
                    files: [
                        {
                            expand: true,
                            cwd: 'src/js/',
                            src: ['*.js','akce/*.js'],
                            dest: 'src/js/tmp/'
                        }
                    ]},
                combine: {

                    files: {
                        'htdocs/www/js/main.js': [
                            'src/js/*.js',
                        ]
                    }
                }
            },
            clean: {
                js: ["src/js/tmp/*.js","src/js/tmp/akce/*.js" ],
                temp: ["htdocs/temp/cache/**/*.*"]
            },

            //ADMIN SASS
        sass: {
            dist: {
                files: {
                    "htdocs/www/css/main.css": "src/sass/main.scss",
                }
            }
        }
        }

    )
    ;

// Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-sass');

// Default task(s).

}
;
