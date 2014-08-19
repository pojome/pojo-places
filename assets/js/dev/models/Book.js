var myApp = myApp || {};
myApp.models = myApp.models || {};

( function( $ ) {
	'use strict';

	myApp.models.Book = Backbone.Model.extend( {
		defaults: {
			author: '',
			name: '',
			year: 2014,
			published: true
		}
	} );

	// Set up this model as a "no URL model" where data is not synced with the server
	myApp.models.Book.prototype.sync = function() { return null; };
	myApp.models.Book.prototype.fetch = function() { return null; };
	myApp.models.Book.prototype.save = function() { return null; };
	
} ( jQuery ) );