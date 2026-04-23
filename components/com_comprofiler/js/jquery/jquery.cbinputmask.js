(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbinputmask = $( $this ).data( 'cbinputmask' );

				if ( cbinputmask ) {
					return; // cbinputmask is already bound; so no need to rebind below
				}

				cbinputmask = {};
				cbinputmask.options = ( typeof options != 'undefined' ? options : {} );

				if ( $( $this ).data( 'cbinputmask-options' ) ) {
					cbinputmask.options = $.extend( true, {}, cbinputmask.options, $( $this ).data( 'cbinputmask-options' ) );
				}

				cbinputmask.defaults = $.fn.cbinputmask.defaults;
				cbinputmask.settings = $.extend( true, {}, cbinputmask.defaults, cbinputmask.options );
				cbinputmask.element = $( $this );

				if ( cbinputmask.settings.useData ) {
					$.each( $.fn.cbinputmask.dataMap, function( key, value ) {
						const dataValue = cbinputmask.element.data( value );

						if ( typeof dataValue != 'undefined' ) {
							cbinputmask.settings[key] = dataValue;
						}
					});
				}

				cbinputmask.element.triggerHandler( 'cbinputmask.init.before', [cbinputmask] );

				if ( ! cbinputmask.settings.init ) {
					return;
				}

				if ( cbinputmask.settings.regex ) {
					cbinputmask.settings.alias = 'regexp';
				}

				if ( cbinputmask.settings.direction == 'r2l' ) {
					cbinputmask.settings.numericInput = true;
				}

				cbinputmask.element.inputmask( cbinputmask.settings );

				// Destroy the cbinputmask element:
				cbinputmask.element.on( 'remove.cbinputmask destroy.cbinputmask', function() {
					cbinputmask.element.cbinputmask( 'destroy' );
				});

				// Rebind the cbinputmask element to pick up any data attribute modifications:
				cbinputmask.element.on( 'rebind.cbinputmask', function() {
					cbinputmask.element.cbinputmask( 'rebind' );
				});

				// If the cbinputmask element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbinputmask.element.on( 'modified.cbinputmask', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbinputmask.element.cbinputmask( 'rebind' );
					}
				});

				// If the cbinputmask is cloned we need to rebind it back:
				cbinputmask.element.on( 'cloning.cbinputmask', function() {
					$( this ).cbinputmask( 'destroy' );

					$( this ).on( 'rebind.cbinputmask', function() {
						$( this ).off( 'rebind.cbinputmask' ); // disgard this binding as we're done with it

						cbinputmask.element.cbinputmask( cbinputmask.options );
					});

					$( this ).on( 'cloned.cbinputmask', function() {
						$( this ).off( 'cloned.cbinputmask' ); // disgard this binding as we're done with it

						$( this ).cbinputmask( cbinputmask.options );
					});

					return true;
				});

				cbinputmask.element.triggerHandler( 'cbinputmask.init.after', [cbinputmask] );

				// Bind the cbinputmask to the element so it's reusable and chainable:
				cbinputmask.element.data( 'cbinputmask', cbinputmask );

				// Add this instance to our instance array so we can keep track of our cbinputmask instances:
				instances.push( cbinputmask );
			});
		},
		rebind: function() {
			var cbinputmask = $( this ).data( 'cbinputmask' );

			if ( ! cbinputmask ) {
				return this;
			}

			cbinputmask.element.cbinputmask( 'destroy' );
			cbinputmask.element.cbinputmask( cbinputmask.options );

			return this;
		},
		destroy: function() {
			var cbinputmask = $( this ).data( 'cbinputmask' );

			if ( ! cbinputmask ) {
				return this;
			}

			cbinputmask.element.inputmask( 'remove' );
			cbinputmask.element.removeData( '_inputmask_opts' );

			cbinputmask.element.off( '.cbinputmask' );
			cbinputmask.element.removeData( 'cbinputmask' );
			cbinputmask.element.triggerHandler( 'cbinputmask.destroyed', [cbinputmask] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	Inputmask.extendDefinitions({
		1: {
			validator: '[1-9\uff11-\uff19]'
		}
	});

	Inputmask.extendAliases({
		regexp: {
			alias: 'regex',
			mask: function mask( opts ) {
				var regexp = decodeURIComponent( opts.regex );
				var delimiter = regexp.substr( 0, 1 );
				var end = regexp.lastIndexOf( delimiter );
				var pattern = regexp.slice( 1, end );
				var modifiers = regexp.substr( ( end + 1 ) );
				opts.regex = pattern;
				opts.casing = ( modifiers.length && ( modifiers.indexOf( 'i' ) != -1 ) );
				return null;
			}
		}
	});

	$.fn.cbinputmask = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbinputmask.dataMap = {
		alias: 'cbinputmask-alias',
		mask: 'cbinputmask-mask',
		regex: 'cbinputmask-regex',
		direction: 'cbinputmask-direction'
	};

	$.fn.cbinputmask.defaults = {
		init: true,
		useData: true,
		alias: null,
		mask: null,
		regex: null,
		direction: null
	};
})(jQuery);