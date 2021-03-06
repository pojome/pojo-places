/**
 * Pojo Places Makefile
 */
'use strict';

module.exports = function( grunt ) {

	require( 'matchdep' ).filterDev( 'grunt-*' ).forEach( grunt.loadNpmTasks );

	// Project configuration.
	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),

		banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
			'<%= grunt.template.today("dd-mm-yyyy") %> */',
		
		checktextdomain: {
			standard: {
				options:{
					text_domain: 'pojo-places',
					correct_domain: true,
					keywords: [
						// WordPress keywords
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d',
						'esc_attr_e:1,2d',
						'esc_attr_x:1,2c,3d',
						'_ex:1,2c,3d',
						'_n:1,2,4d',
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d'
					]
				},
				files: [ {
					src: [
						'**/*.php',
						'!node_modules/**',
						'!build/**',
						'!tests/**',
						'!vendor/**',
						'!*~'
					],
					expand: true
				} ]
			}
		},

		pot: {
			options:{
				text_domain: 'pojo-places',
				dest: 'languages/',
				keywords: [
					// WordPress keywords
					'__:1',
					'_e:1',
					'_x:1,2c',
					'esc_html__:1',
					'esc_html_e:1',
					'esc_html_x:1,2c',
					'esc_attr__:1',
					'esc_attr_e:1',
					'esc_attr_x:1,2c',
					'_ex:1,2c',
					'_n:1,2',
					'_nx:1,2,4c',
					'_n_noop:1,2',
					'_nx_noop:1,2,3c'
				]
			},
			files:{
				src: [
					'**/*.php',
					'!node_modules/**',
					'!build/**',
					'!tests/**',
					'!vendor/**',
					'!*~'
				],
				expand: true
			}
		},

		usebanner: {
			dist: {
				options: {
					banner: '<%= banner %>'
				},
				files: {
					src: [
						'assets/css/style.css',
						'assets/admin/js/app.min.js',
						'assets/js/app.min.js'
					]
				}
			}
		},

		less: {
			dist: {
				options: {
					cleancss: true
				},
				files: {
					'assets/css/style.css': 'assets/less/style.less'
				}
			}
		},

		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'assets/js/dev/utils/google-api.js',
				'assets/js/dev/app.js',
				'assets/admin/js/dev/app.js'
			]
		},

		uglify: {
			pkg: grunt.file.readJSON( 'package.json' ),
			options: {},
			dist: {
				files: {
					'assets/js/app.min.js': [
						'assets/js/dev/utils/google-api.js',
						'assets/js/dev/app.js'
					],
					'assets/admin/js/app.min.js': [
						'assets/admin/js/dev/app.js'
					]
				}
			}
		},

		watch: {
			js: {
				files: [
					'**/*.js',
					'!**/*.min.js'
				],
				tasks: [
					'jshint',
					'uglify'
				],
				options: {}
			},

			less: {
				files: [
					'**/*.less'
				],
				tasks: [
					'less'
				],
				options: {}
			}
		},

		bumpup: {
			options: {
				updateProps: {
					pkg: 'package.json'
				}
			},
			file: 'package.json'
		},

		replace: {
			plugin_main: {
				src: [ 'pojo-places.php' ],
				overwrite: true,
				replacements: [
					{
						from: /Version: \d{1,1}\.\d{1,2}\.\d{1,2}/g,
						to: 'Version: <%= pkg.version %>'
					}
				]
			}
		},

		shell: {
			git_add_all : {
				command: [
					'git add --all',
					'git commit -m "Bump to <%= pkg.version %>"'
				].join( '&&' )
			}
		},

		release: {
			options: {
				bump: false,
				npm: false,
				commit: false,
				tagName: 'v<%= version %>',
				commitMessage: 'released v<%= version %>',
				tagMessage: 'Tagged as v<%= version %>'
			}
		}
		
	} );

	// Default task(s).
	grunt.registerTask( 'default', [
		'checktextdomain',
		'pot',
		'less',
		'uglify',
		'usebanner'
	] );

	grunt.registerTask( 'publish', [
		'default',
		'bumpup',
		'replace',
		'shell:git_add_all',
		'release'
	] );
};