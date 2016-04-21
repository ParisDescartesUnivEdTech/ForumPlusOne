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
            configFiles: {
                files: [ 'Gruntfile.js'],
                options: {
                    reload: true
                }
            }
        },
        shell: {
            yui: {
                command: 'for dir in $(ls yui/src) ; do cd yui/src/"$dir"; ../../../node_modules/shifter/bin/shifter; done;'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-shell');

    // Default task(s).
    grunt.registerTask('default', ['shell:yui', 'watch']);

};
