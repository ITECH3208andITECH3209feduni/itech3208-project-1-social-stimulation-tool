(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbdatepicker = $( $this ).data( 'cbdatepicker' );

				if ( cbdatepicker ) {
					return; // cbdatepicker is already bound; so no need to rebind below
				}

				cbdatepicker = {};
				cbdatepicker.options = ( typeof options != 'undefined' ? options : {} );
				cbdatepicker.defaults = $.fn.cbdatepicker.defaults;
				cbdatepicker.settings = $.extend( true, {}, cbdatepicker.defaults, cbdatepicker.options );
				cbdatepicker.strings = $.extend( true, {}, $.datepicker.regional[''], cbdatepicker.settings.strings );
				cbdatepicker.element = $( $this );
				cbdatepicker.selector = $( $this );

				if ( cbdatepicker.element.is( 'input[type="hidden"]' ) ) {
					cbdatepicker.selector = cbdatepicker.element.siblings( '.cbDatePickerSelector' );
				}

				if ( cbdatepicker.settings.useData ) {
					$.each( $.fn.cbdatepicker.dataMap, function( key, value ) {
						const dataValue = cbdatepicker.element.data( value );

						if ( typeof dataValue != 'undefined' ) {
							cbdatepicker.settings[key] = dataValue;
						}
					});
				}

				cbdatepicker.element.triggerHandler( 'cbdatepicker.init.before', [cbdatepicker] );

				if ( ! cbdatepicker.settings.init ) {
					return;
				}

				var currentYear = new Date().getFullYear();

				if ( cbdatepicker.settings.minYear ) {
					if ( typeof cbdatepicker.settings.minYear == 'string' ) {
						var minYearRelative = cbdatepicker.settings.minYear.substr( 0, 1 );
						var minYearAdjust = cbdatepicker.settings.minYear.substr( 1 );

						if ( minYearRelative == '+' ) {
							cbdatepicker.settings.minYear = ( currentYear + parseInt( minYearAdjust ) );
						} else if ( minYearRelative == '-' ) {
							cbdatepicker.settings.minYear = ( currentYear - parseInt( minYearAdjust ) );
						}
					}
				} else {
					cbdatepicker.settings.minYear = ( currentYear - 99 );
				}

				if ( cbdatepicker.settings.maxYear ) {
					if ( typeof cbdatepicker.settings.maxYear == 'string' ) {
						var maxYearRelative = cbdatepicker.settings.maxYear.substr( 0, 1 );
						var maxYearAdjust = cbdatepicker.settings.maxYear.substr( 1 );

						if ( maxYearRelative == '+' ) {
							cbdatepicker.settings.maxYear = ( currentYear + parseInt( maxYearAdjust ) );
						} else if ( maxYearRelative == '-' ) {
							cbdatepicker.settings.maxYear = ( currentYear - parseInt( maxYearAdjust ) );
						}
					}
				} else {
					cbdatepicker.settings.maxYear = ( currentYear + 99 );
				}

				if ( ( cbdatepicker.settings.calendarType == 2 ) || ( cbdatepicker.settings.calendarType == 3 ) ) {
					var momentCache	=	null;

					if ( typeof moment != 'undefined' ) {
						momentCache = moment.locale();

						moment.locale( Math.random(), {
							months: cbdatepicker.strings.monthNames,
							monthsShort: cbdatepicker.strings.monthNamesShort,
							weekdays: cbdatepicker.strings.dayNames,
							weekdaysShort: cbdatepicker.strings.dayNamesShort,
							weekdaysMin: cbdatepicker.strings.dayNamesMin
						});
					}

					cbdatepicker.combodate = cbdatepicker.selector.combodate({
						format: cbdatepicker.settings.format,
						template: cbdatepicker.settings.template,
						minYear: cbdatepicker.settings.minYear,
						maxYear: cbdatepicker.settings.maxYear,
						firstItem: cbdatepicker.settings.firstItem,
						smartDays: true,
						yearDescending: cbdatepicker.settings.yearDescending,
						minuteStep: 1,
						secondStep: 1,
						customClass: cbdatepicker.settings.customClass
					});

					if ( momentCache ) {
						moment.locale( momentCache );
					}

					cbdatepicker.selector.siblings( '.combodate' ).children().on( 'change', function( event ) {
						var selected = $( this ).val();

						if ( selected !== '' ) {
							$( this ).siblings().each( function() {
								if ( ! $( this ).val() ) {
									var option = null;

									if ( $( this ).hasClass( 'year' ) ) {
										option = $( this ).children( 'option[value="' + currentYear + '"]' ).val();
									}

									if ( ! option ) {
										option = $( this ).children( 'option[value!=""]:first' ).val();
									}

									if ( option ) {
										$( this ).val( option );
									}
								}
							});
						} else{
							$( this ).siblings().each( function() {
								$( this ).val( '' );
							});
						}

						cbdatepicker.element.triggerHandler( 'cbdatepicker.select', [cbdatepicker, cbdatepicker.selector.combodate( 'getValue' )] );
					});
				}

				if ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) || cbdatepicker.settings.addPopup ) {
					if ( cbdatepicker.settings.showTime ) {
						cbdatepicker.datetimepicker = cbdatepicker.selector.datetimepicker({
							showAnim: '',
							showHour: ( cbdatepicker.settings.timeTemplate.toLowerCase().indexOf( 'h' ) !== -1 ),
							showMinute: ( cbdatepicker.settings.timeTemplate.toLowerCase().indexOf( 'm' ) !== -1 ),
							showSecond: ( cbdatepicker.settings.timeTemplate.toLowerCase().indexOf( 's' ) !== -1 ),
							showMillisec: ( cbdatepicker.settings.timeTemplate.toLowerCase().indexOf( 'l' ) !== -1 ),
							showMicrosec: ( cbdatepicker.settings.timeTemplate.toLowerCase().indexOf( 'c' ) !== -1 ),
							stepMinute: 1,
							stepSecond: 1,
							altFieldTimeOnly: false,
							altTimeFormat: ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ? cbdatepicker.settings.timeFormat : null ),
							timeFormat: ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ? cbdatepicker.settings.timeTemplate : cbdatepicker.settings.timeFormat ),
							pickerTimeFormat: cbdatepicker.settings.timeTemplate,
							amNames: cbdatepicker.strings.amNames,
							pmNames: cbdatepicker.strings.pmNames,
							timeOnly: cbdatepicker.settings.timeOnly,
							timeOnlyTitle: cbdatepicker.strings.timeOnlyTitle,
							timeText: cbdatepicker.strings.timeText,
							hourText: cbdatepicker.strings.hourText,
							minuteText: cbdatepicker.strings.minuteText,
							secondText: cbdatepicker.strings.secondText,
							millisecText: cbdatepicker.strings.millisecText,
							microsecText: cbdatepicker.strings.microsecText,
							timezoneText: cbdatepicker.strings.timezoneText,
							onSelect: function( selected ) {
								if ( cbdatepicker.settings.addPopup ) {
									cbdatepicker.selector.combodate( 'setValue', selected );
								} else {
									cbdatepicker.selector.change();
								}

								if ( cbdatepicker.settings.calendarType == 4 ) {
									cbdatepicker.selector.siblings( '.cbDatePickerSelected' ).html( selected );
								}

								cbdatepicker.element.triggerHandler( 'cbdatepicker.select', [cbdatepicker, selected] );
							},
							yearRange: cbdatepicker.settings.minYear + ':' + cbdatepicker.settings.maxYear,
							isRTL: cbdatepicker.settings.isRTL,
							firstDay: cbdatepicker.settings.firstDay,
							changeMonth: true,
							changeYear: true,
							showButtonPanel: false,
							altField: ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ? findTarget( cbdatepicker, cbdatepicker.settings.target ) : null ),
							altFormat: ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ? cbdatepicker.settings.dateFormat : null ),
							dateFormat: ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ? cbdatepicker.settings.dateTemplate : cbdatepicker.settings.dateFormat ),
							dayNames: cbdatepicker.strings.dayNames,
							dayNamesMin: cbdatepicker.strings.dayNamesMin,
							dayNamesShort: cbdatepicker.strings.dayNamesShort,
							monthNames: cbdatepicker.strings.monthNames,
							monthNamesShort: cbdatepicker.strings.monthNamesShort,
							prevText: cbdatepicker.strings.prevText,
							nextText: cbdatepicker.strings.nextText,
							currentText: cbdatepicker.strings.currentText,
							closeText: cbdatepicker.strings.closeText
						});
					} else {
						cbdatepicker.datepicker = cbdatepicker.selector.datepicker({
							onSelect: function( selected ) {
								if ( cbdatepicker.settings.addPopup ) {
									cbdatepicker.selector.combodate( 'setValue', selected );
								} else {
									cbdatepicker.selector.change();
								}

								if ( cbdatepicker.settings.calendarType == 4 ) {
									cbdatepicker.selector.siblings( '.cbDatePickerSelected' ).html( selected );
								}

								cbdatepicker.element.triggerHandler( 'cbdatepicker.select', [cbdatepicker, selected] );
							},
							showAnim: '',
							yearRange: cbdatepicker.settings.minYear + ':' + cbdatepicker.settings.maxYear,
							isRTL: cbdatepicker.settings.isRTL,
							firstDay: cbdatepicker.settings.firstDay,
							changeMonth: true,
							changeYear: true,
							showButtonPanel: false,
							altField: ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ? findTarget( cbdatepicker, cbdatepicker.settings.target ) : null ),
							altFormat: ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ? cbdatepicker.settings.dateFormat : null ),
							dateFormat: ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ? cbdatepicker.settings.dateTemplate : cbdatepicker.settings.dateFormat ),
							dayNames: cbdatepicker.strings.dayNames,
							dayNamesMin: cbdatepicker.strings.dayNamesMin,
							dayNamesShort: cbdatepicker.strings.dayNamesShort,
							monthNames: cbdatepicker.strings.monthNames,
							monthNamesShort: cbdatepicker.strings.monthNamesShort,
							prevText: cbdatepicker.strings.prevText,
							nextText: cbdatepicker.strings.nextText,
							currentText: cbdatepicker.strings.currentText,
							closeText: cbdatepicker.strings.closeText
						});
					}
				}

				cbdatepicker.calendarHandler = function( e ) {
					e.preventDefault();
					e.stopPropagation();

					var widget = cbdatepicker.selector.datepicker( 'widget' );

					if ( widget.not( ':visible' ).length ) {
						cbdatepicker.selector.datepicker( 'show' );

						widget.position({
							my: 'left top+20',
							at: 'left top',
							of: $( this )
						});

						if ( widget.css( 'z-index' ) < 100 ) {
							widget.css( 'z-index', 100 );
						}
					}

					cbdatepicker.element.triggerHandler( 'cbdatepicker.calendar', [cbdatepicker] );
				};

				cbdatepicker.selector.siblings( '.cbDatePickerCalendar' ).on( 'click', cbdatepicker.calendarHandler );

				// If the value has changed and we have a target we need to adjust the target with the utc offset applied as we always store in utc:
				cbdatepicker.changeHandler = function( e ) {
					findTarget( cbdatepicker, cbdatepicker.settings.target ).val( methods.get.call( $this ) ).trigger( 'change' );
				};

				cbdatepicker.selector.on( 'change', cbdatepicker.changeHandler );

				// Destroy the cbdatepicker element:
				cbdatepicker.element.on( 'remove.cbdatepicker destroy.cbdatepicker', function() {
					cbdatepicker.element.cbdatepicker( 'destroy' );
				});

				// Rebind the cbdatepicker element to pick up any data attribute modifications:
				cbdatepicker.element.on( 'rebind.cbdatepicker', function() {
					cbdatepicker.element.cbdatepicker( 'rebind' );
				});

				// If the cbdatepicker element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbdatepicker.element.on( 'modified.cbdatepicker', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						var targetAttr = cbdatepicker.element.attr( 'data-cbdatepicker-target' );

						if ( typeof targetAttr != 'undefined' ) {
							cbdatepicker.element.attr( 'data-cbdatepicker-target', targetAttr.replace( oldId, newId ) );
						}

						var targetData = cbdatepicker.element.data( 'cbdatepicker-target' );

						if ( typeof targetData != 'undefined' ) {
							cbdatepicker.element.data( 'cbdatepicker-target', targetData.replace( oldId, newId ) );
						}

						cbdatepicker.element.cbdatepicker( 'rebind' );
					}
				});

				// If the cbdatepicker is cloned we need to rebind it back:
				cbdatepicker.element.on( 'cloned.cbdatepicker', function() {
					var selector = $( this );

					if ( $( this ).is( 'input[type="hidden"]' ) ) {
						selector = $( this ).siblings( '.cbDatePickerSelector' );
					}

					$( this ).off( '.cbdatepicker' );
					$( this ).removeData( 'cbdatepicker' );

					selector.removeData( 'combodate' );
					selector.removeData( 'datepicker' );
					selector.siblings( '.combodate' ).remove();
					selector.removeClass( 'hasDatepicker' );
					selector.siblings( '.cbDatePickerCalendar' ).off( 'click', cbdatepicker.calendarHandler );
					selector.off( 'change', cbdatepicker.changeHandler );

					$( this ).cbdatepicker( cbdatepicker.options );
				});

				cbdatepicker.element.triggerHandler( 'cbdatepicker.init.after', [cbdatepicker] );

				// Bind the cbdatepicker to the element so it's reusable and chainable:
				cbdatepicker.element.data( 'cbdatepicker', cbdatepicker );

				// Add this instance to our instance array so we can keep track of our cbdatepicker instances:
				instances.push( cbdatepicker );
			});
		},
		get: function() {
			var cbdatepicker = $( this ).data( 'cbdatepicker' );

			if ( ! cbdatepicker ) {
				return '';
			}

			var value = '';

			if ( ( cbdatepicker.settings.calendarType == 2 ) || ( cbdatepicker.settings.calendarType == 3 ) ) {
				if ( cbdatepicker.settings.showTime ) {
					value = cbdatepicker.selector.combodate( 'getValue', 'YYYY-MM-DD HH:mm:ss' );
				} else {
					value = cbdatepicker.selector.combodate( 'getValue', 'YYYY-MM-DD' );
				}
			} else if ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ) {
				if ( cbdatepicker.settings.showTime ) {
					value = cbdatepicker.selector.datetimepicker( 'getDate' );

					if ( value ) {
						value = new Date( value ).toSQLDateTimeString();
					}
				} else {
					value = cbdatepicker.selector.datepicker( 'getDate' );

					if ( value ) {
						value = new Date( value ).toSQLDateString();
					}
				}
			}

			if ( value && ( typeof moment != 'undefined' ) ) {
				var isOffset = false;

				if ( cbdatepicker.settings.showTime && cbdatepicker.settings.offset ) {
					value = value + cbdatepicker.settings.offset;
					isOffset = true;
				}

				var date = moment( value );

				if ( ( ! isOffset ) && cbdatepicker.settings.showTime && cbdatepicker.settings.timezone && ( cbdatepicker.settings.timezone != 'UTC' ) && ( typeof moment.tz != 'undefined' ) ) {
					// Reconstruct using the supplied timezone to avoid being offset by the browser:
					date = moment.tz( value, cbdatepicker.settings.timezone );

					isOffset = true;
				}

				if ( date.isValid() ) {
					if ( isOffset ) {
						date.utc();
					}

					value = date.format( cbdatepicker.settings.format );
				} else {
					value = '';
				}
			} else {
				// We don't want null; we want empty string if this is the case:
				value = '';
			}

			return value;
		},
		set: function( value ) {
			var cbdatepicker = $( this ).data( 'cbdatepicker' );

			if ( ! cbdatepicker ) {
				return this;
			}

			if ( ( cbdatepicker.settings.calendarType == 2 ) || ( cbdatepicker.settings.calendarType == 3 ) ) {
				cbdatepicker.selector.combodate( 'setValue', value );
			} else if ( ( cbdatepicker.settings.calendarType == 1 ) || ( cbdatepicker.settings.calendarType == 4 ) ) {
				if ( cbdatepicker.settings.showTime ) {
					cbdatepicker.selector.datetimepicker( 'setDate', value );
				} else {
					cbdatepicker.selector.datepicker( 'setDate', value );
				}
			}

			return this;
		},
		rebind: function() {
			var cbdatepicker = $( this ).data( 'cbdatepicker' );

			if ( ! cbdatepicker ) {
				return this;
			}

			cbdatepicker.element.cbdatepicker( 'destroy' );
			cbdatepicker.element.cbdatepicker( cbdatepicker.options );

			return this;
		},
		destroy: function() {
			var cbdatepicker = $( this ).data( 'cbdatepicker' );

			if ( ! cbdatepicker ) {
				return this;
			}

			if ( cbdatepicker.combodate ) {
				cbdatepicker.selector.combodate( 'destroy' );
			}

			if ( cbdatepicker.datetimepicker ) {
				cbdatepicker.selector.datetimepicker( 'destroy' );
			}

			if ( cbdatepicker.datepicker ) {
				cbdatepicker.selector.datepicker( 'destroy' );
			}

			cbdatepicker.selector.siblings( '.cbDatePickerCalendar' ).off( 'click', cbdatepicker.calendarHandler );
			cbdatepicker.selector.off( 'change', cbdatepicker.changeHandler );
			cbdatepicker.element.off( '.cbdatepicker' );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbdatepicker.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			cbdatepicker.element.removeData( 'cbdatepicker' );
			cbdatepicker.element.triggerHandler( 'cbdatepicker.destroyed', [cbdatepicker] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	function findTarget( cbdatepicker, target ) {
		if ( ! target ) {
			return cbdatepicker.element;
		}

		if ( ( target.lastIndexOf( '~ ', 0 ) === 0 ) || ( target.lastIndexOf( '+ ', 0 ) === 0 ) || ( target.lastIndexOf( '> ', 0 ) === 0 ) || ( target.lastIndexOf( ' ', 0 ) === 0 ) ) {
			return cbdatepicker.element.find( target.trim() );
		}

		return $( target );
	}

	function padNumber( number ) {
		if ( number < 10 ) {
			return '0' + number;
		}

		return number;
	}

	if ( ! Date.prototype.toSQLDateString ) {
		Date.prototype.toSQLDateString = function() {
			return this.getFullYear() +
			'-' + padNumber( this.getMonth() + 1 ) +
			'-' + padNumber( this.getDate() );
		};
	}

	if ( ! Date.prototype.toSQLDateTimeString ) {
		Date.prototype.toSQLDateTimeString = function() {
			return this.getFullYear() +
			'-' + padNumber( this.getMonth() + 1 ) +
			'-' + padNumber( this.getDate() ) +
			' ' + padNumber( this.getHours() ) +
			':' + padNumber( this.getMinutes() ) +
			':' + padNumber( this.getSeconds() );
		};
	}

	$.fn.cbdatepicker = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbdatepicker.dataMap = {
		calendarType: 'cbdatepicker-calendartype',
		template: 'cbdatepicker-template',
		format: 'cbdatepicker-format',
		timeTemplate: 'cbdatepicker-timetemplate',
		dateTemplate: 'cbdatepicker-datetemplate',
		timeFormat: 'cbdatepicker-timeformat',
		dateFormat: 'cbdatepicker-dateformat',
		minYear: 'cbdatepicker-minyear',
		maxYear: 'cbdatepicker-maxyear',
		yearDescending: 'cbdatepicker-yeardescending',
		firstDay: 'cbdatepicker-firstday',
		timeOnly: 'cbdatepicker-timeonly',
		showTime: 'cbdatepicker-showtime',
		addPopup: 'cbdatepicker-addpopup',
		isRTL: 'cbdatepicker-isrtl',
		customClass: 'cbdatepicker-customclass',
		firstItem: 'cbdatepicker-firstitem',
		timezone: 'cbdatepicker-timezone',
		offset: 'cbdatepicker-offset',
		target: 'cbdatepicker-target'
	};

	$.fn.cbdatepicker.defaults = {
		init: true,
		useData: true,
		calendarType: 2,
		template: 'MM / DD / YYYY HH : mm : ss',
		format: 'YYYY-MM-DD HH:mm:ss',
		timeTemplate: 'HH:mm:ss',
		dateTemplate: 'yy-mm-dd',
		timeFormat: 'HH:mm:ss',
		dateFormat: 'yy-mm-dd',
		minYear: '-99',
		maxYear: '+99',
		yearDescending: false,
		firstDay: 0,
		timeOnly: false,
		showTime: false,
		addPopup: false,
		isRTL: false,
		customClass: null,
		firstItem: 'empty',
		timezone: null,
		offset: null,
		target: null
	};
})(jQuery);