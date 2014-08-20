/* global jQuery, google */

var pojoPlaces = pojoPlaces || {};
pojoPlaces.utils = pojoPlaces.utils || {};

( function( $ ) {
	'use strict';
	
	pojoPlaces.utils.GoogleApi = {
		getDistance: function( latitude, longitube, geo_location ) {
			return ( google.maps.geometry.spherical.computeDistanceBetween( geo_location, this.getLocation( latitude, longitube ) ) / 10 );
		},
		
		getLocation: function( latitude, longitube ) {
			return new google.maps.LatLng( latitude, longitube );
		}
	};
} ( jQuery ) );