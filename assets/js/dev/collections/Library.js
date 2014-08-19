var myApp = myApp || {};
myApp.collections = myApp.collections || {};

( function( $ ) {
	'use strict';

	myApp.collections.Library = Backbone.Collection.extend( {
		model: myApp.models.Book
	} );

} ( jQuery ) );