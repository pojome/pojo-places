/* global Backbone, jQuery */

var pojoPlaces = pojoPlaces || {};
pojoPlaces.views = pojoPlaces.views || {};

( function( $ ) {
	'use strict';
	
	pojoPlaces.views.LibraryView = Backbone.View.extend( {
		el: '#my-app',
		
		template: '',
		
		events: {
			'click a.btn-remove': 'removeView'
		},
		
		initialize: function() {
			var self = this;
			
			self.$list = self.$( 'ul.list-view' );
			
			self.template = _.template( $( '#tmpl-book-row' ).html() );
			
			self.collection = new pojoPlaces.collections.Library();
			self.listenTo( self.collection, 'add', self.addBook );
			
			self.collection.add( {
				author: 'Yakir Sitbon',
				name: 'First item here'
			} );
			
			self.collection.add( {
				author: 'Aba Momo',
				name: 'Nice book forever',
				year: 2012
			} );
			
			self.collection.add(
				self.collection.get( 'c3' ).clone()
			);
			
			self.render();
		},

		addBook: function( model, models, options ) {
			var view = new pojoPlaces.views.BookView( { model: model } );
			
			this.$list.append(view.render().el);
		},

		// Add all items in the **Todos** collection at once.
		addAll: function () {
			this.$list.html( '' );
			this.collection.each( this.addBook, this );
		},
		
		render: function() {
			return this;
		}
		
	} );

} ( jQuery ) );