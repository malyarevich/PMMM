/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig({

		// Setting folder templates.
		dirs: {
			css: 'assets/css',
			theme_css: 'themes/assets/css',
			fonts: 'assets/fonts',
			images: 'assets/images',
			js: 'assets/js'
		},

		// JavaScript linting with JSHint.
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/admin/*.js',
				'!<%= dirs.js %>/admin/*.min.js',
				'<%= dirs.js %>/*.js',
				'!<%= dirs.js %>/*.min.js',
			]
		},

		// Minify .js files.
		uglify: {
			options: {
				preserveComments: 'some'
			},
			admin: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/admin/',
					src: [
						'*.js',
						'!*.min.js',
						'!Gruntfile.js',
					],
					dest: '<%= dirs.js %>/admin/',
					ext: '.min.js'
				}]
			},
			frontend: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.js %>/',
					ext: '.min.js'
				}]
			},
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				files: [{
			      	expand: true,
					cwd: '<%= dirs.css %>/',
					src: ['*.css', '!*.min.css'],
					dest: '<%= dirs.css %>/',
					ext: '.min.css'
			    }, {
			    	expand: true,
					cwd: '<%= dirs.theme_css %>/',
					src: ['*.css', '!*.min.css'],
					dest: '<%= dirs.theme_css %>/',
					ext: '.min.css'
			    }, {
			    	expand: true,
					cwd: '<%= dirs.css %>/admin/',
					src: ['*.css', '!*.min.css'],
					dest: '<%= dirs.css %>/admin/',
					ext: '.min.css'
			    }]
			}
		},

		// Watch changes for assets.
		watch: {
			css: {
				files: [ 
					'<%= dirs.css %>/*.css', 
					'<%= dirs.css %>/admin/*.css',
					'<%= dirs.theme_css %>/*.css'
				],
				tasks: ['cssmin']
			},
			js: {
				files: [
					'<%= dirs.js %>/admin/*js',
					'<%= dirs.js %>/*js',
					'!<%= dirs.js %>/admin/*.min.js',
					'!<%= dirs.js %>/*.min.js'
				],
				tasks: ['uglify']
			}
		},

	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-shell' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );

	// Register tasks
	grunt.registerTask( 'default', [
		'css',
		'uglify'
	]);

	grunt.registerTask( 'css', [
		'cssmin'
	]);

};