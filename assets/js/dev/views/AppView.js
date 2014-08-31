/* global Backbone, Modernizr, jQuery, google */

var pojoPlaces = pojoPlaces || {};
pojoPlaces.views = pojoPlaces.views || {};

( function( $, undefined ) {
	'use strict';

	pojoPlaces.views.AppView = Backbone.View.extend( {
		el: '#pojo-places',
		
		events: {
			'click button.get-geolocation-position': '_getLocationGetPosition'
		},

		initialize: function() {
			var self = this;
			self.google_api = pojoPlaces.utils.GoogleApi;
			self.geocoder = new google.maps.Geocoder();
			
			self.$places_ul = self.$( 'ul.places' );
			self.$places = self.$places_ul.find( 'li.place' );
			
			self.$loading = self.$( 'div.loading' );
			
			self.$search_wrap = self.$( 'div.search-wrap' );
			self.$search_box = self.$search_wrap.find( 'input.search-box' );
			
			self.userLocation = self.google_api.getLocation( 32.07, 34.77 );
			
			self._googleListener();
			
			self.render();
		},
		
		_getLocationGetPosition: function() {
			var self = pojoPlaces.App;
			
			navigator.geolocation.getCurrentPosition( function(position) {
				self.$loading.show();
				
				self.userLocation = self.google_api.getLocation(
					position.coords.latitude,
					position.coords.longitude
				);

				self.geocoder.geocode( { 'latLng': self.userLocation }, function( results, status ) {
					if ( status == google.maps.GeocoderStatus.OK ) {
						if ( results[0] ) {
							self.$search_box.val( results[0].formatted_address );
						}
						else {
							//alert( 'Google did not return any results.' );
						}
					}
					self.$loading.hide();
				} );

				self.render();
			} );
		},
		
		_googleListener: function() {
			var self = this;
			var autocomplete = new google.maps.places.Autocomplete( self.$search_box[0], { types: [ 'geocode' ] } );

			google.maps.event.addListener( autocomplete, 'place_changed', function() {
				var geometry = autocomplete.getPlace().geometry;
				if ( undefined !== geometry ) {
					self.userLocation = geometry.location;
					self.render();
				}
			} );

			if ( Modernizr.geolocation ) {
				self.$( 'button.get-geolocation-position' ).show();
			}
		},
		
		render: function() {
			var self = this;
			
			self.$places.each( function() {
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

			self.$places.sort( function( a, b ) {
				var a_distance = parseInt( $( a ).data( 'distance' ) );
				var b_distance = parseInt( $( b ).data( 'distance' ) );
				return ( a_distance < b_distance ) ? -1 : ( a_distance > b_distance ) ? 1 : 0;
			} );

			self.$places_ul.append( self.$places );
		}
	} );

} ( jQuery ) );