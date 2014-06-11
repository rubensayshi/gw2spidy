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
      html: 'templates/wrapper.html.twig'
    }
  });
  
  grunt.registerTask('build', [
    'useminPrepare',
    'concat',
    'cssmin',
    'uglify',
    'usemin'
  ]);
};
