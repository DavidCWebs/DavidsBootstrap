module.exports = function(grunt) {
  grunt.initConfig({
  		pkg: grunt.file.readJSON('package.json'),
        
      less: {
	      style: {
	        files: {
	          "css/bootstrap.css": "less/bootstrap.less"
	        }
	      }
	    },
	    
	    cssmin: {
            css:{
                src: "css/bootstrap.css",
                dest: "css/bootstrap.min.css"
            }
        },
        
       watch: {
            styles: {
               options: {
                    spawn: false,
                    event: ["added", "deleted", "changed"]
                },
                files: [ "css/*.css", "less/*.less"],
                tasks: [ "less", "cssmin" ]
            }
        }
    });
    

    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-contrib-cssmin");

    // the default task can be run just by typing "grunt" on the command line
    grunt.registerTask("default", ["watch", "cssmin:css"]);
};