/*global jQuery, Modernizr, google */

var Pojo_Places_Admin = Pojo_Places_Admin || {};

( function( $, undefined ) {
	'use strict';

	Pojo_Places_Admin.App = {
		getLocation: function( latitude, longitube ) {
			return new google.maps.LatLng( latitude, longitube );
		},
		
		cache: {
			$document: $( document ),
			$window: $( window )
		},

		cacheElements: function() {
			this.cache.$body = $( 'body' );
			this.cache.$addressWrapper = $( '#pojo-places-address' );
			
			this.cache.fields = {
				$address: this.$( 'div.atmb-field-row.atmb-address input.atmb-field-text' ),
				$city: this.$( 'div.atmb-field-row.atmb-city input.atmb-field-text' ),
				$zipcode: this.$( 'div.atmb-field-row.atmb-zipcode input.atmb-field-text' ),
				$country: this.$( 'div.atmb-field-row.atmb-country input.atmb-field-text' ),
				$latitude: this.$( 'div.atmb-field-row.atmb-latitude input.atmb-field-text' ),
				$longitude: this.$( 'div.atmb-field-row.atmb-longitude input.atmb-field-text' )
			};
		},

		$: function( selector ) {
			return this.cache.$addressWrapper.find( selector );
		},

		buildElements: function() {
			var self = this;
			if ( 1 >= self.cache.$addressWrapper.length ) {
				self.$( 'div.atmb-wrap-fields' ).append( '<div id="pojo-google-map-wrap"></div><div class="atmb-field-row"><button class="button" id="pojo-lookup-location">Preview</button></div>' );
				
				self.cache.$map = self.$( '#pojo-google-map-wrap' );
				self.cache.$lookupLocation = self.$( '#pojo-lookup-location' );
				self.bindMapEvents();
			}
		},
		
		bindMapEvents: function() {
			var self = this;

			//TODO: Need to put this to CSS file.
			self.cache.$map.css( {
				width: '100%',
				height: '200px',
				marginTop: '10px'
			} );

			//TODO: Put this default to setting.
			var myLocation;
			if ( self.cache.fields.$latitude.val().length > 0 && self.cache.fields.$longitude.val().length > 0 ) {
				myLocation = self.getLocation(
					self.cache.fields.$latitude.val(),
					self.cache.fields.$longitude.val()
				);
			}
			else {
				myLocation = self.getLocation( 32.066157, 34.777821 );
			}

			self.geocoder = new google.maps.Geocoder();
			self.adminMap = new google.maps.Map( document.getElementById( 'pojo-google-map-wrap' ), {
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				zoom: 15,
				center: myLocation,
				streetViewControl: false,
				zoomControl: true,
				panControl: true,
				scaleControl: true
			} );

			self.marker = new google.maps.Marker( {
				position: myLocation,
				map: self.adminMap,
				draggable: true,
				animation: google.maps.Animation.DROP
			} );

			google.maps.event.addListener( self.marker, 'dragend', function() {
				self._changePosition( self.marker.getPosition() );
			} );

			self.cache.$lookupLocation.on( 'click', function( e ) {
				e.preventDefault();
				
				var address = self.cache.fields.$address.val(),
					city = self.cache.fields.$city.val(),
					zipcode = self.cache.fields.$zipcode.val(),
					country = self.cache.fields.$country.val();
				
				var addressLine = [ address, city, zipcode, country ].filter( Boolean ).join( ', ' );
				self.geocoder.geocode( { 'address': addressLine }, function( results, status ) {
					if ( status === google.maps.GeocoderStatus.OK ) {
						var LatLng = results[0].geometry.location;
						
						self.cache.fields.$latitude.val( LatLng.lat() );
						self.cache.fields.$longitude.val( LatLng.lng() );
						
						self._changePosition( LatLng, true );
					}
				} );
			} );
		},

		bindEvents: function() {
			//var self = this;
		},
		
		_changePosition: function( LatLng, change_marker ) {
			change_marker = change_marker || false;
			
			this.cache.fields.$latitude.val( LatLng.lat() );
			this.cache.fields.$longitude.val( LatLng.lng() );
			
			if ( change_marker ) {
				this.adminMap.setCenter( LatLng );
				this.marker.setPosition( LatLng );
			}
		},

		init: function() {
			this.cacheElements();
						
			this.buildElements();
			this.bindEvents();
		}
	};

	$( document ).ready( function( $ ) {
		Pojo_Places_Admin.App.init();
	} );

}( jQuery ) );