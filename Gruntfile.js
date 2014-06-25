module.exports = function (grunt) {
    grunt.initConfig({
        /*
         * Javascript concatenation
         */
        concat : {
            vendor: {
                src : [

                    'webroot/assets/js/vendor/jquery.min.js',
                    'webroot/assets/js/vendor/highstock/js/highstock.js',

                    'webroot/assets/js/tooltip.js',
                    'webroot/assets/js/vendor/bootstrap/bootstrap-transition.js',
                    'webroot/assets/js/vendor/bootstrap/bootstrap-alert.js',
                    'webroot/assets/js/vendor/bootstrap/bootstrap-dropdown.js',
                    'webroot/assets/js/vendor/bootstrap/bootstrap-tooltip.js',
                    'webroot/assets/js/vendor/bootstrap/bootstrap-tab.js',
                    'webroot/assets/js/vendor/bootstrap/bootstrap-button.js'
                ],
                dest : 'webroot/assets/compiled/vendor.js'
            },
            scripts: {
                src : [
                    'webroot/assets/js/chart.js',
                    'webroot/assets/js/gw2money.js',
                    'webroot/assets/js/item-history.js',
                    'webroot/assets/js/js-flash-messages.js',
                    'webroot/assets/js/crafting.js',
                    'webroot/assets/js/watchlist.js'
                ],
                dest : 'webroot/assets/compiled/scripts.js'
            }
        },

        /*
         * CSS concatenation
         */
        concat_css : {
            bundle: {
                src : [
                    'webroot/assets/css/bootstrap.css',
                    'webroot/assets/css/bootstrap-responsive.css',
                    'webroot/assets/css/font-awesome.css',
                    'webroot/assets/css/style.css',
                    'webroot/assets/css/tooltip.css'
                ],
                dest : 'webroot/assets/compiled/bundle.css'
            }
        },

        /*
         * Javascript uglifying
         */
        uglify : {
            dist : {
                files : {
                    'webroot/assets/compiled/vendor.min.js'  : ['<%= concat.vendor.dest %>'],
                    'webroot/assets/compiled/scripts.min.js' : ['<%= concat.scripts.dest %>']
                }
            }
        },

        /*
         * Watch
         */
        watch : {
            options : {},
            css: {
                files : ['webroot/assets/css/*', 'webroot/static/less/*.less', 'webroot/static/less/**/*.less'],
                tasks : ['concat_css']
            },
            js: {
                files : ['webroot/assets/js/*.js', 'webroot/assets/js/**/*.js'],
                tasks : ['concat']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-concat-css');
    grunt.loadNpmTasks('grunt-notify');

    grunt.registerTask('default', ['concat', 'uglify', 'concat_css']);
};
