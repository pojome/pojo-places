var pojoPlaces = pojoPlaces || {};
pojoPlaces.collections = pojoPlaces.collections || {};

( function( $ ) {
	'use strict';

	pojoPlaces.collections.Library = Backbone.Collection.extend( {
		model: pojoPlaces.models.Book
	} );

} ( jQuery ) );