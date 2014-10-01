/* global jQuery, Modernizr, google, Pojo, Pojo_Places */

;(function( $, window, document, undefined ) {
	'use strict';
	
	var pluginName = "pojoPlaces",
		defaults = {};

	function Plugin( element, options ) {
		this.element = element;
		this.settings = $.extend( {}, defaults, options );

		this._defaults = defaults;
		this._name = pluginName;

		this.cache = {};

		this.init();
	}
	
	$.extend( Plugin.prototype, {
		cacheElements: function() {
			this.cache.$placesWrap = $( this.element );

			this.cache.$places_ul = this.cache.$placesWrap.find( 'ul.places-list' );
			this.cache.$places = this.cache.$places_ul.find( 'li.place-item' );

			this.cache.$loading = this.cache.$placesWrap.find( 'div.places-loading' );

			this.cache.$search_wrap = this.cache.$placesWrap.find( 'div.search-wrap' );
			this.cache.$search_box = this.cache.$search_wrap.find( 'input.search-box' );
			
			this.cache.hasFilterTags = 1 <= $( '.places-filter-tags', this.cache.$search_wrap ).length;
			this.cache.hasFilterCategory = 1 <= $( '.places-filter-category', this.cache.$search_wrap ).length;
		},

		buildElements: function() {},

		bindEvents: function() {
			var self = this;

			self.userLocation = self.google_api.getLocation( Pojo.places.lat, Pojo.places.lng );
			
			self.cache.$placesWrap.find( 'button.get-geolocation-position' ).on( 'click', function() {
				navigator.geolocation.getCurrentPosition( function( position ) {
					self.loading.show( self );

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
						self.loading.hide( self );
					} );

					self.renderPanel( self );
				} );
			} );
			
			$( '.places-input-filter, .places-filter-select', self.cache.$search_wrap ).on( 'change', function() {
				self.loading.show( self );
				var $filters = $( '.places-input-filter, .places-filter-select', self.cache.$search_wrap ),
					form_data = self.cache.$search_wrap.find( ':input' ).serialize();

				// Maybe: http://www.joezimjs.com/javascript/3-ways-to-parse-a-query-string-in-a-url/
				//location.hash = form_data;
				
				$filters.prop( 'disabled', 'disabled' );
				
				self.cache.$places
					.addClass( 'hide' )
					.removeClass( 'category-filtered' )
					.removeClass( 'tag-filtered' );
				
					setTimeout( function() {
						var terms = [];
						self.cache.$search_wrap
							.find( '.places-input-filter:checked, .places-filter-select' )
							.each( function() {
								var $thisElement = $( this );

								if ( $thisElement.hasClass( 'places-filter-select' ) && '' === $thisElement.val() ) {
									$thisElement.find( 'option' ).each( function() {
										if ( '' !== $( this ).val() ) {
											terms.push( $( this ).val() );
										}
									} );
								} else {
									terms.push( $thisElement.val() );
								}
							} );

						$.each( terms, function( index, value ) {
							$( 'li[data-category*=";' + value + ';"]', self.cache.$places_ul ).addClass( 'category-filtered' );
							$( 'li[data-tags*=";' + value + ';"]', self.cache.$places_ul ).addClass( 'tag-filtered' );
						} );

						var itemSelector = 'li';
						if ( self.cache.hasFilterCategory ) {
							itemSelector += '.category-filtered';
						}

						if ( self.cache.hasFilterTags ) {
							itemSelector += '.tag-filtered';
						}

						$( itemSelector, self.cache.$places_ul ).removeClass( 'hide' );

						self.loading.hide( self );
						$filters.removeProp( 'disabled' );
					}, 500 );
			} );

			self._googleListener();
		},
		
		loading: {
			show: function( self ) {
				self.cache.$loading.show();
			},
			
			hide: function( self ) {
				self.cache.$loading.hide();
			}
		},

		_googleListener: function() {
			var self = this;

			if ( 1 <= self.cache.$search_box.length ) {
				var autocomplete = new google.maps.places.Autocomplete( self.cache.$search_box[0], { types: [ 'geocode' ] } );

				google.maps.event.addListener( autocomplete, 'place_changed', function() {
					var geometry = autocomplete.getPlace().geometry;
					if ( undefined !== geometry ) {
						self.userLocation = geometry.location;
						self.renderPanel( self );
					}
				} );

				if ( Modernizr.geolocation ) {
					var $button = self.cache.$placesWrap.find( 'button.get-geolocation-position' );
					$button.show();
					
					if ( 'yes' === self.cache.$placesWrap.data( 'load_geolocation' ) ) {
						$button.trigger( 'click' );
					}
				}
			}
		},

		renderPanel: function( self ) {
			self.loading.show( self );
			
			setTimeout( function() {
				self.cache.$places.each( function() {
					var distance = Math.round(
						self.google_api.getDistance(
							Number( $( this ).data( 'latitude' ) ),
							Number( $( this ).data( 'longitude' ) ),
							self.userLocation
						)
					);

					$( this ).data( 'distance', distance );
				} );

				self.cache.$places.sort( function( a, b ) {
					var a_distance = parseInt( $( a ).data( 'distance' ) );
					var b_distance = parseInt( $( b ).data( 'distance' ) );
					return ( a_distance < b_distance ) ? -1 : ( a_distance > b_distance ) ? 1 : 0;
				} );

				self.cache.$places_ul.append( self.cache.$places );
				self.loading.hide( self );
			}, 800 );
		},
		
		init: function() {
			this.google_api = Pojo_Places.GoogleApi;
			this.geocoder = new google.maps.Geocoder();

			this.cacheElements();
			this.buildElements();
			this.bindEvents();
			this.renderPanel( this );
		}
	} );

	$.fn[ pluginName ] = function( options ) {
		this.each( function() {
			if ( ! $.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
			}
		} );
		return this;
	};
})( jQuery, window, document );

jQuery( document ).ready( function( $ ) {
	'use strict';
	$( 'div.pojo-places' ).pojoPlaces();
} );