module.exports = function (grunt) {
  // Load all required grunt tasks.
  require('load-grunt-tasks')(grunt);

  grunt.initConfig({
    useminPrepare: {
      html: 'templates/wrapper.html.twig',
      options: {
        root: 'webroot',
        dest: 'webroot'
      }
    },
    usemin: {
      html: 'templates/dist/wrapper.html.twig'
    },
    clean: {
      distTemplates: ['templates/dist'],
    },
    copy: {
      templates: {
        files: [
          {
            expand: true,
            cwd: 'templates',
            src: ['**/*'],
            dest: 'templates/dist'
          }
        ]
      }
    },
    sed: {
      twigPathDist: {
        path: 'webroot/index.php',
        pattern: "\'/../templates\'",
        replacement: "\'/../templates/dist\'"
      },
      twigPathDev: {
        path: 'webroot/index.php',
        pattern: "\'/../templates/dist\'",
        replacement: "\'/../templates\'"
      }
    },
    surround: {
      dist: {
        options: {
          prepend: '{% spaceless %}',
          append: '{% endspaceless %}',
          overwrite: true,
          ignoreRepetition: true
        },
        files: [{
          src: ['templates/dist/wrapper.html.twig']
        }]
      }
    }
  });

  grunt.registerTask('unbuild', [
    'clean:distTemplates',
    'sed:twigPathDev' // Replace twig template path to dev.
  ]);

  grunt.registerTask('build', [
    'clean:distTemplates',
    'copy:templates', // Copy dev templates to dist folder.
    'useminPrepare',
    'concat',
    'cssmin',
    'uglify',
    'usemin',
    'sed:twigPathDist', // Replace twig template path to dist.
    'surround:dist' // Prepend and append spaceless twig tags.
  ]);
};
