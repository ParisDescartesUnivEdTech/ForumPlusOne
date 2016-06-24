module.exports = function(grunt) {
    //require('load-grunt-tasks')(grunt);

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            yui: {
                files: ['yui/src/**/*.js', 'yui/src/**/*.json'],
                tasks: ['shell:yui'],
            },
            js: {
                files: ['js/**/*.js', '!js/**/jquery.js', '!js/**/*.min.js'],
                tasks: ['uglify'],
            },
            jquery: {
                files: ['jquery'],
                tasks: ['jquery'],
            },
            configFiles: {
                files: [ 'Gruntfile.js'],
                options: {
                    reload: true
                }
            }
        },
        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> - ' +
                    '<%= grunt.template.today("yyyy-mm-dd") %> */',
                sourceMap: true,
                compress: {
                    drop_console: true
                }
            },
            liveReload: {
                options: {
                    mangle: {
                        expect: ['liveReload', 'window.liveReload', 'jQuery']
                    }
                },
                files: {
                    'js/liveReload.min.js': ['js/jQueryLoader.js', 'js/jQueryColorPlugin.js', 'js/liveReload.js']
                }
            },
            collapseReplies: {
                options: {
                    mangle: {
                        expect: ['jQuery']
                    }
                },
                files: {
                    'js/collapseReplies.min.js': ['js/jQueryLoader.js', 'js/collapseReplies.js']
                }
            },
            seevoters: {
                options: {
                    mangle: {
                        expect: ['window.jQueryStrings', 'jQuery']
                    }
                },
                files: {
                    'js/seevoters.min.js': ['js/jQueryLoader.js', 'js/seevoters.js']
                }
            },
            changeState: {
                options: {
                    mangle: {
                        expect: ['jQuery']
                    }
                },
                files: {
                    'js/changeDiscussionState.min.js': ['js/jQueryLoader.js', 'js/changeDiscussionState.js']
                }
            },
            tooltip: {
                options: {
                    mangle: {
                        expect: ['jQuery']
                    }
                },
                files: {
                    'js/bootstrap-tooltip.min.js': ['js/jQueryLoader.js', 'js/bootstrap-tooltip.js']
                }
            }
        },
        batch_git_clone: {
            jquery: {
                options: {
                    configFile: ".cloneJquery.json"
                }
            },
            options: {
                overWrite: false,
                npmInstall: true,
                bowerInstall: false,
            }
        },
        shell: {
            yui: {
                command: 'for dir in $(ls yui/src) ; do cd yui/src/"$dir"; ../../../node_modules/shifter/bin/shifter; cd ../../..; done;'
            },
            jquery: {
                command: 'cd jquery ; grunt custom:-ajax/script,-ajax/jsonp,-deprecated,-wrap,-exports/amd dist:../js/'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-shell');
    grunt.loadNpmTasks('grunt-batch-git-clone');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Default task(s).
    grunt.registerTask('default', ['shell:yui', 'jquery', 'uglify', 'watch']);


    grunt.registerTask('jquery', ['batch_git_clone:jquery', 'shell:jquery']);
};
