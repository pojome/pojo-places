var pojoPlaces = pojoPlaces || {};
pojoPlaces.views = pojoPlaces.views || {};

( function( $ ) {
	'use strict';

	pojoPlaces.views.BookView = Backbone.View.extend( {
		tagName: 'li',

		template: '',

		events: {
			
		},

		initialize: function() {
			var self = this;

			self.template = _.template( $( '#tmpl-book-row' ).html() );

			self.listenTo( self.model, 'destroy', self.remove );

			self.render();
		},

		addBook: function( model, models, options ) {
			this.$el.append(
				this.template( {    
					book: model.toJSON(),
					cid: model.cid
				} )
			);
		},

		removeBook: function( e ) {
			e.preventDefault();
			this.model.destroy();
		},
		
		cloneBook: function( e ) {
			e.preventDefault();
			pojoPlaces.main.collection.add( this.model.clone() );
			
		},

		editBook: function() {
			
		},
		
		render: function() {
			var self = this;
			
			self.$el.html( self.template( {
				book: self.model.toJSON(),
				cid: self.model.cid
			} ) );
			return self;
		}

	} );

} ( jQuery ) );