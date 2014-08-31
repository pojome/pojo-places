/* global jQuery, google */

var Pojo_Places = Pojo_Places || {};

( function( $ ) {
	'use strict';

	Pojo_Places.GoogleApi = {
		getDistance: function( latitude, longitube, geo_location ) {
			return ( google.maps.geometry.spherical.computeDistanceBetween( geo_location, this.getLocation( latitude, longitube ) ) / 10 );
		},
		
		getLocation: function( latitude, longitube ) {
			return new google.maps.LatLng( latitude, longitube );
		}
	};
} ( jQuery ) );