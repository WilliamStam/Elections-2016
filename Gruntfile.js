var jsfile = [
	'vendor/components/jquery/jquery.js',
	'vendor/components/bootstrap/js/bootstrap.js' ,
	'vendor/components/jquery-hotkeys/jquery.hotkeys.js',
	'vendor/components/jquery-mousewheel/jquery.mousewheel.js',
	'vendor/components/TouchSwipe-Jquery-Plugin/jquery.touchSwipe.js',
	'vendor/components/jQote2/jquery.jqote2.js',
	'vendor/timrwood/moment/moment.js',
	'vendor/components/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
	'vendor/components/daterangepicker/daterangepicker.js',
	'vendor/components/hideseek/jquery.hideseek.min.js',
	'vendor/moxiecode/plupload/js/plupload.full.min.js',
	'vendor/moxiecode/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js',
	'vendor/components/toastr/toastr.js',
	'vendor/drmonty/leaflet/js/leaflet.min.js',
	
	'vendor/ivaynberg/select2/dist/js/select2.full.min.js',
	'app/_js/plugins/jquery.getData.js',
	'app/_js/plugins/jquery.ba-dotimeout.min.js',
	'app/_js/plugins/jquery.ba-bbq.js',
	'app/_js/plugins/jquery.ui.touch-punch.min.js',
	'app/_js/plugins/jquery.keepalive.js',
	'app/_js/plugins/jquery.plupload.js',
];

module.exports = function (grunt) {
	require('time-grunt')(grunt);
	require('jit-grunt')(grunt);
	
	
	grunt.initConfig({
		
		concat: {
			js: {
				options: {
					separator: ';',
					stripBanners: true,
					sourceMap :true,
					sourceMapName : 'app/javascript.js.map'
				},
				src: jsfile,
				dest: 'app/javascript.js',
				nonull: true
			},
			js_quick: {
				options: {
					separator: ';',
					stripBanners: true
				},
				src: jsfile,
				dest: 'app/javascript.js',
				nonull: true
			}
		},
		clean: {
			map: ["app/javascript.js.map"],
		},
		
		uglify: {
			js: {
				
				files: {
					'app/javascript.js': ['app/javascript.js']
				}
			}
		},
		less: {
			style: {
				files: {
					"app/style.css": "app/less/style.less",
				}
			}
		},
		cssmin: {
			options: {
				report: "min",
				keepSpecialComments: 0,
				shorthandCompacting: true
			},
			target: {
				files: {
					'app/style.css': 'app/style.css',
				}
			}
		},
		watch: {
			js: {
				files: ['js/*.js'],
				tasks: ['concat:js'],
				options: {
					spawn: false,
					livereload: true
				}
			},
			css: {
				files: ['less/*.less'],
				tasks: ['less:style'],
				options: {
					spawn: false,
					livereload: true
				}
			}
		}
		
	});
	
	
	
	
	
	
	grunt.registerTask('jsmin', ['uglify:js']);
	grunt.registerTask('js', ['concat:js_quick','clean:map']);
	grunt.registerTask('jsmap', ['concat:js']);
	grunt.registerTask('css', ['less:style']);
	grunt.registerTask('build', ['concat:js','less:style', 'uglify:js','cssmin','clean:map']);
	grunt.registerTask('default', ['watch']);

};