(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbtimeago = $( $this ).data( 'cbtimeago' );

				if ( cbtimeago ) {
					return; // cbtimeago is already bound; so no need to rebind below
				}

				cbtimeago = {};
				cbtimeago.options = ( typeof options != 'undefined' ? options : {} );
				cbtimeago.defaults = $.fn.cbtimeago.defaults;
				cbtimeago.settings = $.extend( true, {}, cbtimeago.defaults, cbtimeago.options );
				cbtimeago.element = $( $this );
				cbtimeago.datetime = cbtimeago.element.data( 'cbtimeago-datetime' );

				if ( ! cbtimeago.datetime ) {
					cbtimeago.datetime = cbtimeago.element.attr( 'title' );

					if ( ! cbtimeago.datetime ) {
						cbtimeago.datetime = cbtimeago.element.text();
					}
				}

				if ( cbtimeago.settings.useData ) {
					$.each( $.fn.cbtimeago.dataMap, function( key, value ) {
						const dataValue = cbtimeago.element.data( value );

						if ( typeof dataValue != 'undefined' ) {
							cbtimeago.settings[key] = dataValue;
						}
					});
				}

				cbtimeago.element.triggerHandler( 'cbtimeago.init.before', [cbtimeago] );

				if ( ! cbtimeago.settings.init ) {
					return;
				}

				if ( cbtimeago.settings.hideAgo ) {
					cbtimeago.settings.strings.future = '%s';
					cbtimeago.settings.strings.past = '%s';
				}

				var momentCache	=	null;

				if ( typeof moment != 'undefined' ) {
					momentCache = moment.locale();

					if ( cbtimeago.settings.short ) {
						moment.locale( Math.random(), {
							relativeTime: {
								future: cbtimeago.settings.strings.short.future,
								past: cbtimeago.settings.strings.short.past,
								s: cbtimeago.settings.strings.short.second,
								ss: cbtimeago.settings.strings.short.seconds,
								m: cbtimeago.settings.strings.short.minute,
								mm: cbtimeago.settings.strings.short.minutes,
								h: cbtimeago.settings.strings.short.hour,
								hh: cbtimeago.settings.strings.short.hours,
								d: cbtimeago.settings.strings.short.day,
								dd: cbtimeago.settings.strings.short.days,
								w: cbtimeago.settings.strings.short.week,
								ww: cbtimeago.settings.strings.short.weeks,
								M: cbtimeago.settings.strings.short.month,
								MM: cbtimeago.settings.strings.short.months,
								y: cbtimeago.settings.strings.short.year,
								yy: cbtimeago.settings.strings.short.years
							}
						});
					} else {
						moment.locale( Math.random(), {
							relativeTime: {
								future: cbtimeago.settings.strings.long.future,
								past: cbtimeago.settings.strings.long.past,
								s: cbtimeago.settings.strings.long.second,
								ss: cbtimeago.settings.strings.long.seconds,
								m: cbtimeago.settings.strings.long.minute,
								mm: cbtimeago.settings.strings.long.minutes,
								h: cbtimeago.settings.strings.long.hour,
								hh: cbtimeago.settings.strings.long.hours,
								d: cbtimeago.settings.strings.long.day,
								dd: cbtimeago.settings.strings.long.days,
								w: cbtimeago.settings.strings.long.week,
								ww: cbtimeago.settings.strings.long.weeks,
								M: cbtimeago.settings.strings.long.month,
								MM: cbtimeago.settings.strings.long.months,
								y: cbtimeago.settings.strings.long.year,
								yy: cbtimeago.settings.strings.long.years
							}
						});
					}
				}

				cbtimeago.livestamp = cbtimeago.element.livestamp( cbtimeago.datetime );

				if ( momentCache ) {
					moment.locale( momentCache );
				}

				// Destroy the cbtimeago element:
				cbtimeago.element.on( 'remove.cbtimeago destroy.cbtimeago', function() {
					cbtimeago.element.cbtimeago( 'destroy' );
				});

				// Rebind the cbtooltip element to pick up any data attribute modifications:
				cbtimeago.element.on( 'rebind.cbtimeago', function() {
					cbtimeago.element.cbtimeago( 'rebind' );
				});

				// If the cbtimeago element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbtimeago.element.on( 'modified.cbtimeago', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbtimeago.element.cbtimeago( 'rebind' );
					}
				});

				// If the cbtimeago is cloned we need to rebind it back:
				cbtimeago.element.on( 'cloned.cbtimeago', function() {
					$( this ).off( '.cbtimeago' );
					$( this ).removeData( 'cbtimeago' );
					$( this ).removeData( 'livestampdata' );
					$( this ).text( '' );

					if ( ( ! $( this ).data( 'cbtimeago-datetime' ) ) && ( ! $( this ).attr( 'title' ) ) ) {
						$( this ).text( cbtimeago.datetime );
					}

					$( this ).cbtimeago( cbtimeago.options );
				});

				cbtimeago.element.triggerHandler( 'cbtimeago.init.after', [cbtimeago] );

				// Bind the cbtimeago to the element so it's reusable and chainable:
				cbtimeago.element.data( 'cbtimeago', cbtimeago );

				// Add this instance to our instance array so we can keep track of our cbtimeago instances:
				instances.push( cbtimeago );
			});
		},
		rebind: function() {
			var cbtimeago = $( this ).data( 'cbtimeago' );

			if ( ! cbtimeago ) {
				return this;
			}

			cbtimeago.element.cbtimeago( 'destroy' );
			cbtimeago.element.cbtimeago( cbtimeago.options );

			return this;
		},
		destroy: function() {
			var cbtimeago = $( this ).data( 'cbtimeago' );

			if ( ! cbtimeago ) {
				return this;
			}

			cbtimeago.element.livestamp( 'destroy' );
			cbtimeago.element.off( '.cbtimeago' );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbtimeago.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			cbtimeago.element.text( '' );

			if ( ( ! cbtimeago.element.data( 'cbtimeago-datetime' ) ) && ( ! cbtimeago.element.attr( 'title' ) ) ) {
				cbtimeago.element.text( cbtimeago.datetime );
			}

			cbtimeago.element.removeData( 'cbtimeago' );
			cbtimeago.element.triggerHandler( 'cbtimeago.destroyed', [cbtimeago] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	$.fn.cbtimeago = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbtimeago.dataMap = {
		hideAgo: 'cbtimeago-hideago',
		short: 'cbtimeago-short'
	};

	$.fn.cbtimeago.defaults = {
		init: true,
		useData: true,
		hideAgo: false,
		short: false,
		strings: {
			long: {
				future: 'in %s',
				past: '%s ago',
				second: 'less than a minute',
				seconds: 'less than a minute',
				minute: 'about a minute',
				minutes: '%d minutes',
				hour: 'about an hour',
				hours: 'about %d hours',
				day: 'a day',
				days: '%d days',
				week: 'a week',
				weeks: '%d weeks',
				month: 'about a month',
				months: '%d months',
				year: 'about a year',
				years: '%d years'
			},
			short: {
				future: '%s',
				past: '%s',
				second: 'now',
				seconds: '%ds',
				minute: '1m',
				minutes: '%dm',
				hour: '1h',
				hours: '%dh',
				day: '1d',
				days: '%dd',
				week: '1w',
				weeks: '%dw',
				month: '1mo',
				months: '%dmo',
				year: '1y',
				years: '%dy'
			}
		}
	};
})(jQuery);