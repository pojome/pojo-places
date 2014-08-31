/*global jQuery, Modernizr, google */

var Pojo_Places = Pojo_Places || {};

( function( $, undefined ) {
	'use strict';

	Pojo_Places.App = {
		cache: {
			$document: $( document ),
			$window: $( window )
		},

		cacheElements: function() {
			this.cache.$body = $( 'body' );
			this.cache.$placesWrap = $( '#pojo-places' );

			this.cache.$places_ul = this.cache.$placesWrap.find( 'ul.places' );
			this.cache.$places = this.cache.$places_ul.find( 'li.place' );

			this.cache.$loading = this.cache.$placesWrap.find( 'div.loading' );

			this.cache.$search_wrap = this.cache.$placesWrap.find( 'div.search-wrap' );
			this.cache.$search_box = this.cache.$search_wrap.find( 'input.search-box' );
		},

		buildElements: function() {},

		bindEvents: function() {
			var self = this;

			self.userLocation = self.google_api.getLocation( 32.07, 34.77 );
			self._googleListener();
			
			self.cache.$placesWrap.find( 'button.get-geolocation-position' ).on( 'click', self._getLocationGetPosition );
		},

		_getLocationGetPosition: function() {
			var self = Pojo_Places.App;

			navigator.geolocation.getCurrentPosition( function(position) {
				self.cache.$loading.show();

				self.userLocation = self.google_api.getLocation(
					position.coords.latitude,
					position.coords.longitude
				);

				self.geocoder.geocode( { 'latLng': self.userLocation }, function( results, status ) {
					if ( status === google.maps.GeocoderStatus.OK ) {
						if ( results[0] ) {
							self.cache.$search_box.val( results[0].formatted_address );
						}
					}
					self.cache.$loading.hide();
				} );

				self.renderPanel();
			} );
		},

		_googleListener: function() {
			var self = this;
			var autocomplete = new google.maps.places.Autocomplete( self.cache.$search_box[0], { types: [ 'geocode' ] } );

			google.maps.event.addListener( autocomplete, 'place_changed', function() {
				var geometry = autocomplete.getPlace().geometry;
				if ( undefined !== geometry ) {
					self.userLocation = geometry.location;
					self.renderPanel();
				}
			} );

			if ( Modernizr.geolocation ) {
				self.cache.$placesWrap.find( 'button.get-geolocation-position' ).show();
			}
		},
		
		renderPanel: function() {
			var self = this;

			self.cache.$places.each( function() {
				var distance = Math.round(
					self.google_api.getDistance(
						Number( $( this ).data( 'latitude' ) ),
						Number( $( this ).data( 'longitude' ) ),
						self.userLocation
					)
				);

				$( this ).data( 'distance', distance )
					.find( '.dist-debug' ).html( 'dist: ' + distance );
			} );

			self.cache.$places.sort( function( a, b ) {
				var a_distance = parseInt( $( a ).data( 'distance' ) );
				var b_distance = parseInt( $( b ).data( 'distance' ) );
				return ( a_distance < b_distance ) ? -1 : ( a_distance > b_distance ) ? 1 : 0;
			} );

			self.cache.$places_ul.append( self.cache.$places );
		},

		init: function() {
			this.google_api = Pojo_Places.GoogleApi;
			this.geocoder = new google.maps.Geocoder();

			this.cacheElements();
			this.buildElements();
			this.bindEvents();
			this.renderPanel();
		}
	};

	$( document ).ready( function( $ ) {
		Pojo_Places.App.init();
	} );

}( jQuery ) );