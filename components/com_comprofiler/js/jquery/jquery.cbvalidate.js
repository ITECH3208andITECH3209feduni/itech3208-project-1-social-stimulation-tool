(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbvalidate = $( $this ).data( 'cbvalidate' );

				if ( cbvalidate ) {
					return; // cbvalidate is already bound; so no need to rebind below
				}

				cbvalidate = {};
				cbvalidate.options = ( typeof options != 'undefined' ? options : {} );
				cbvalidate.defaults = $.fn.cbvalidate.defaults;
				cbvalidate.settings = $.extend( true, {}, cbvalidate.defaults, cbvalidate.options );
				cbvalidate.element = $( $this );

				if ( cbvalidate.settings.useData ) {
					$.each( $.fn.cbvalidate.dataMap, function( key, value ) {
						const dataValue = cbvalidate.element.data( value );

						if ( typeof dataValue != 'undefined' ) {
							cbvalidate.settings[key] = dataValue;
						}
					});
				}

				cbvalidate.element.triggerHandler( 'cbvalidate.init.before', [cbvalidate] );

				if ( ! cbvalidate.settings.init ) {
					return;
				}

				// Add the translated messages to jQuery Validator:
				if ( cbvalidate.settings.messages ) {
					$.extend( $.validator.messages, cbvalidate.settings.messages );
				}

				// Add the password unmasking functionality:
				cbvalidate.element.find( '.cbPasswordUnmask' ).each( function() {
					var passwordInput = $( this ).find( 'input' );
					var showPassword = $( this ).find( '.cbPasswordUnmaskShow' );
					var hidePassword = $( this ).find( '.cbPasswordUnmaskHide' );

					showPassword.on( 'click', function() {
						passwordInput.attr( 'type', 'text' );
						showPassword.addClass( 'hidden' );
						hidePassword.removeClass( 'hidden' );
						passwordInput.focus();
					});

					hidePassword.on( 'click', function() {
						passwordInput.attr( 'type', 'password' );
						showPassword.removeClass( 'hidden' );
						hidePassword.addClass( 'hidden' );
						passwordInput.focus();
					});

					passwordInput.data( 'passwordunmask', true );
				});

				// Bind to the forms submit handling so we can set submit variable state:
				cbvalidate.element.on( 'submit', function( event ) {
					var buttons = $( this ).find( 'button[type="submit"][data-submit-text],input[type="submit"][data-submit-text]' );

					buttons.each( function() {
						var submitText = $( this ).data( 'submit-text' );
						var buttonText = null;

						$( this ).prop( 'disabled', true );

						if ( $( this ).is( 'input' ) ) {
							buttonText = $( this ).val();

							$( this ).val( submitText );
						} else {
							buttonText = $( this ).html();

							$( this ).html( submitText );
						}

						$( this ).data( 'button-text', buttonText );
					});

					// Revert password unmasking before submit:
					$( this ).find( '.cbPasswordUnmask' ).each( function() {
						if ( $( this ).find( 'input[type="password"]' ).length ) {
							return;
						}

						$( this ).find( 'input' ).attr( 'type', 'password' );
						$( this ).find( '.cbPasswordUnmaskShow' ).removeClass( 'hidden' );
						$( this ).find( '.cbPasswordUnmaskHide' ).addClass( 'hidden' );
					});

					var validate = $( this ).validate();

					validate.cbIsFormSubmitting = true;

					var valid = validate.form();

					validate.cbIsFormSubmitting = false;

					if ( ! valid ) {
						buttons.each( function() {
							var buttonText = $( this ).data( 'button-text' );

							$( this ).prop( 'disabled', false );

							if ( $( this ).is( 'input' ) ) {
								$( this ).val( buttonText );
							} else {
								$( this ).html( buttonText );
							}

							$( this ).removeData( 'button-text' );
						});

						var errors = cbvalidate.validate.errorList;

						if ( errors.length ) {
							var errorElement = $( errors[0].element );
							var tabPane = errorElement.closest( '.cbTabPane' );

							if ( tabPane.length ) {
								var tab = tabPane.closest( '.cbTabs' );

								if ( tab.length ) {
									var cbtabs = tab.data( 'cbtabs' );

									if ( cbtabs ) {
										cbtabs.element.cbtabs( 'select', tabPane.attr( 'id' ) );
									}
								}
							}

							$.scrollTo( errorElement, 0, { axis: 'y', offset: { top: - ( ( $( window ).outerHeight() - errorElement.outerHeight() ) / 2 ), left: 0 } } );
						}

						cbvalidate.element.find( '.cbValidationHasImageData' ).prop( 'disabled', false );

						validate.focusInvalid();
						event.preventDefault();
					} else {
						cbvalidate.element.find( '.cbValidationHasImageData' ).prop( 'disabled', true );
					}

					cbvalidate.element.triggerHandler( 'cbvalidate.validate', [cbvalidate, valid] );
				});

				// Bind customized jQuery Validate usage to the form:
				cbvalidate.validate = cbvalidate.element.validate({
					onsubmit: false,
					ignoreTitle: true,
					errorClass: 'cbValidationMessage',
					validClass: 'cbValidationMessage',
					ignore: '.cbValidationDisabled,.hidden,.disabled,.ignore,:disabled,[readonly],[type="hidden"]:not(.cbValidationAllowed)',
					cbIsFormSubmitting: false,
					cbIsOnFocusIn: false,
					cbIsOnFocusOut: false,
					cbIsOnKeyUp: false,
					cbIsOnClick: false,
					cbIsOnChange: false,
					success: function( label, element ) {
						var labels = cbvalidate.validate.errorsFor( element );

						// Duplicate validation labels exist for this element.. remove them:
						if ( labels.length > 1 ) {
							labels.not( label ).remove();
						}

						var message = $( element ).data( 'remote-response' );

						if ( ! message ) {
							message = $( element ).data( 'msg-success' );
						}

						if ( message ) {
							label.find( '.cbValidationIcon' ).remove();
							label.removeClass( 'invalid-feedback' );

							label.html( message );

							if ( message.toString().charAt( 0 ) != '<' ) {
								label.prepend( '<span class="cbValidationIcon fa fa-check mr-1"></span>' );
								label.addClass( 'valid-feedback' );
							}
						} else {
							label.remove(); // Remove the validate label if there's nothing to show
						}

						cbvalidate.element.triggerHandler( 'cbvalidate.success', [cbvalidate, label, element] );
					},
					showErrors: function( errorMap, errorList ) {
						var validator = this;

						validator.defaultShowErrors();

						$.each( errorList, function( i, error ) {
							if ( $( error.element ).hasClass( 'cbValidationHasImageData' ) ) {
								var imageData = $( error.element ).siblings( '.cbValidationImageData,.cbValidationImageDataCrop' );

								if ( imageData.length ) {
									imageData.remove();
								}

								$( error.element ).removeClass( 'cbValidationHasImageData' );
							}

							var label = validator.errorsFor( error.element );

							// Duplicate validation labels exist for this element.. remove them:
							if ( label.length > 1 ) {
								label.not( ':last' ).remove();
							}

							label.find( '.cbValidationIcon' ).remove();
							label.removeClass( 'valid-feedback' );

							if ( error.message.toString().charAt( 0 ) != '<' ) {
								label.prepend( '<span class="cbValidationIcon fa fa-times mr-1"></span>' );
								label.addClass( 'invalid-feedback' );
							}
						});

						cbvalidate.element.triggerHandler( 'cbvalidate.showerrors', [cbvalidate, errorMap, errorList] );
					},
					highlight: function( element, errorClass, validClass ) {
						if ( $( element ).is( ':checkbox' ) || $( element ).is( ':radio' ) ) {
							$( element ).closest( '.cb_form_line,tr' ).find( ':checkbox,:radio' ).addClass( 'cbValidationError is-invalid' ); // input
						} else {
							$( element ).addClass( 'cbValidationError is-invalid' ); // input

							if ( $( element ).hasClass( 'cbDatePicker' ) ) {
								// Also highlight the secondary inputs of a datepicker
								$( element ).siblings( '.cbDatePickerSelector' ).addClass( 'cbValidationError is-invalid' );
								$( element ).siblings( '.combodate' ).find( 'select' ).addClass( 'cbValidationError is-invalid' );
							}
						}

						$( element ).closest( '.cb_form_line,tr' ).find( '.col-form-label' ).addClass( 'cbValidationError text-danger' ); // label
						$( element ).closest( '.cb_form_line' ).addClass( 'cbValidationError' ); // divs
						$( element ).closest( 'tr' ).addClass( 'cbValidationError' ); // tables

						var tabs = cbvalidate.element.find( '.cbTabs' ); // tabs

						if ( tabs.length ) {
							tabs.each( function() {
								var cbtabs = $( this ).data( 'cbtabs' );

								if ( cbtabs ) {
									$.each( cbtabs.tabs, function( i, tab ) {
										var hasErrors = tab.tabPane.find( 'input.cbValidationError,select.cbValidationError,textarea.cbValidationError' ).not( ':submit,:reset,:image,[disabled],[readonly]' ).not( cbvalidate.validate.settings.ignore );

										if ( hasErrors.length ) {
											tab.tabNav.addClass( 'cbValidationError' );

											if ( cbtabs.tabsNav.hasClass( 'nav-pills' ) ) {
												tab.tabNavLink.addClass( 'text-white bg-danger' );
											} else {
												tab.tabNavLink.addClass( 'text-danger border-danger' );
											}
										}
									});
								}
							});
						}

						cbvalidate.element.triggerHandler( 'cbvalidate.highlight', [cbvalidate, element, errorClass, validClass] );
					},
					unhighlight: function( element, errorClass, validClass ) {
						if ( $( element ).is( ':checkbox' ) || $( element ).is( ':radio' ) ) {
							$( element ).closest( '.cb_form_line,tr' ).find( ':checkbox,:radio' ).removeClass( 'cbValidationError is-invalid' ); // input
						} else {
							$( element ).removeClass( 'cbValidationError is-invalid' ); // input

							if ( $( element ).hasClass( 'cbDatePicker' ) ) {
								// Also unhighlight the secondary inputs of a datepicker
								$( element ).siblings( '.cbDatePickerSelector' ).removeClass( 'cbValidationError is-invalid' );
								$( element ).siblings( '.combodate' ).find( 'select' ).removeClass( 'cbValidationError is-invalid' );
							}
						}

						$( element ).closest( '.cb_form_line,tr' ).find( '.col-form-label' ).removeClass( 'cbValidationError text-danger' ); // label
						$( element ).closest( '.cb_form_line' ).removeClass( 'cbValidationError' ); // divs
						$( element ).closest( 'tr' ).removeClass( 'cbValidationError' ); // tables

						var tabs = cbvalidate.element.find( '.cbTabs' ); // tabs

						if ( tabs.length ) {
							tabs.each( function() {
								var cbtabs = $( this ).data( 'cbtabs' );

								if ( cbtabs ) {
									$.each( cbtabs.tabs, function( i, tab ) {
										var hasErrors = tab.tabPane.find( 'input.cbValidationError,select.cbValidationError,textarea.cbValidationError' ).not( ':submit,:reset,:image,[disabled],[readonly]' ).not( cbvalidate.validate.settings.ignore );

										if ( ! hasErrors.length ) {
											tab.tabNav.removeClass( 'cbValidationError' );

											if ( cbtabs.tabsNav.hasClass( 'nav-pills' ) ) {
												tab.tabNavLink.removeClass( 'text-white bg-danger' );
											} else {
												tab.tabNavLink.removeClass( 'text-danger border-danger' );
											}
										}
									});
								}
							});
						}

						cbvalidate.element.triggerHandler( 'cbvalidate.unhighlight', [cbvalidate, element, errorClass, validClass] );
					},
					errorElement: 'div',
					errorPlacement: function( error, element ) {
						if ( $( element ).is( ':checkbox' ) || $( element ).is( ':radio' ) || $( element ).parent().hasClass( 'input-group' ) ) {
							element.parent().siblings().last().after( error );
						} else {
							element.parent().append( error );
						}

						cbvalidate.element.triggerHandler( 'cbvalidate.errorplacement', [cbvalidate, error, element] );
					},
					onfocusin: function( element ) { // Validate on focus
						this.cbIsOnFocusIn = true;

						$.validator.defaults.onfocusin.call( this, element );

						this.cbIsOnFocusIn = false;

						cbvalidate.element.triggerHandler( 'cbvalidate.focusin', [cbvalidate, element] );
					},
					onfocusout: function( element, event ) { // Validate on blur
						// Revert an unmasked password input if leaving the input:
						if ( ( $( element ).data( 'passwordunmask' ) === true )
							&& ( ! $( element ).is( 'input[type="password"]' ) )
							&& ( ! $( event.relatedTarget ).is( '.cbPasswordUnmaskShow' ) )
							&& ( ! $( event.relatedTarget ).is( '.cbPasswordUnmaskHide' ) )
						) {
							var passwordMask = $( element ).closest( '.cbPasswordUnmask' );

							$( element ).attr( 'type', 'password' );
							passwordMask.find( '.cbPasswordUnmaskShow' ).removeClass( 'hidden' );
							passwordMask.find( '.cbPasswordUnmaskHide' ).addClass( 'hidden' );
						}

						this.cbIsOnFocusOut = true;

						// $.validator.defaults.onfocusout.call( this, element );
						// WORKAROUND FOR: https://github.com/jquery-validation/jquery-validation/issues/1975
						if ( ( ! this.checkable( element ) ) && ( element.name in this.submitted || ( this.optional( element ) !== true ) ) ) {
							this.element( element );
						}

						this.cbIsOnFocusOut = false;

						cbvalidate.element.triggerHandler( 'cbvalidate.focusout', [cbvalidate, element] );
					},
					onkeyup: function( element, event ) { // Validate on keyup
						this.cbIsOnKeyUp = true;

						$.validator.defaults.onkeyup.call( this, element, event );

						this.cbIsOnKeyUp = false;

						cbvalidate.element.triggerHandler( 'cbvalidate.keyup', [cbvalidate, element, event] );
					},
					onclick: function( element ) { // Validate on click
						this.cbIsOnClick = true;

						$.validator.defaults.onclick.call( this, element );

						this.cbIsOnClick = false;

						cbvalidate.element.triggerHandler( 'cbvalidate.click', [cbvalidate, element] );
					},
					onchange: function( element ) { // Validate on change
						this.cbIsOnChange = true;

						// if ( element.name in this.submitted || ( ! this.optional( element ) ) ) {
						// WORKAROUND FOR: https://github.com/jquery-validation/jquery-validation/issues/1975
						if ( element.name in this.submitted || ( this.optional( element ) !== true ) ) {
							this.element( element );
						}

						this.cbIsOnChange = false;

						cbvalidate.element.triggerHandler( 'cbvalidate.change', [cbvalidate, element] );
					}
				});

				// Bind to the change event so we can validate on change
				cbvalidate.element.on( 'change.validate', 'input,select,textarea', function( event ) {
					var validator = $.data( this.form, 'validator' );
					var eventType = 'on' + event.type.replace( /^validate/, "" );
					var settings = validator.settings;

					if ( settings[eventType] && ( ! $( this ).is( settings.ignore ) ) ) {
						settings[eventType].call( validator, this, event );
					}
				}).find( 'input,select,textarea' ).on( 'cloned.validate', function() {
					if ( $( this ).is( cbvalidate.validate.settings.ignore ) ) {
						return;
					}

					if ( $( this ).is( ':checkbox' ) || $( this ).is( ':radio' ) ) {
						$( this ).closest( '.cb_form_line,tr' ).find( ':checkbox,:radio' ).removeClass( 'cbValidationError is-invalid' ); // input
					} else {
						$( this ).removeClass( 'cbValidationError is-invalid' ); // input

						if ( $( this ).hasClass( 'cbDatePicker' ) ) {
							// Also unhighlight the secondary inputs of a datepicker
							$( this ).siblings( '.cbDatePickerSelector' ).removeClass( 'cbValidationError is-invalid' );
							$( this ).siblings( '.combodate' ).find( 'select' ).removeClass( 'cbValidationError is-invalid' );
						}
					}

					$( this ).closest( '.cb_form_line,tr' ).find( '.col-form-label' ).removeClass( 'cbValidationError text-danger' ); // label
					$( this ).closest( '.cb_form_line' ).removeClass( 'cbValidationError' ); // divs
					$( this ).closest( 'tr' ).removeClass( 'cbValidationError' ); // tables
					$( this ).closest( '.cb_form_line,tr' ).find( '.cbValidationMessage' ).remove(); // message

					$( this ).removeData( 'previousValue' );
					$( this ).removeData( 'remote-response' );
				});

				cbvalidate.element.find( '.cbRepeat' ).on( 'cbrepeat.add', function( e, cbrepeat, row ) {
					// If a new row was added to a repeat usage then validate the previous row so users can be informed of any validation mistakes:
					cbvalidate.element.cbvalidate( 'validate', row, true );
				});

				// Pass the cbvalidator options to validator plugin object so new validate methods can access the options:
				cbvalidate.validate.options = cbvalidate.settings;

				// Destroy the cbvalidate element:
				cbvalidate.element.on( 'remove.cbvalidate destroy.cbvalidate', function() {
					cbvalidate.element.cbvalidate( 'destroy' );
				});

				cbvalidate.element.triggerHandler( 'cbvalidate.init.after', [cbvalidate] );

				// Bind the cbvalidate to the element so it's reusable and chainable:
				cbvalidate.element.data( 'cbvalidate', cbvalidate );

				// Add this instance to our instance array so we can keep track of our cbvalidator instances:
				instances.push( cbvalidate );
			});
		},
		validate: function( element, children ) {
			var cbvalidate = $( this ).data( 'cbvalidate' );

			if ( ! cbvalidate ) {
				return true;
			}

			if ( typeof element == 'undefined' ) {
				element = null;
			}

			if ( typeof children == 'undefined' ) {
				children = false;
			}

			cbvalidate.validate.cbIsFormSubmitting = true;

			var valid = true;

			if ( element ) {
				if ( children ) {
					var invalid = 0;

					cbvalidate.validate.elements().filter( function() {
						return ( $( this ).closest( element ).length || $( this ).is( element ) );
					}).each( function() {
						if ( ! cbvalidate.validate.element( this ) ) {
							invalid++;
						}
					});

					valid = ( ! invalid );
				} else {
					valid = cbvalidate.validate.element( element );
				}
			} else {
				valid = cbvalidate.validate.form();
			}

			cbvalidate.validate.cbIsFormSubmitting = false;

			if ( ! valid ) {
				cbvalidate.validate.focusInvalid();
			}

			cbvalidate.element.triggerHandler( 'cbvalidate.validate', [cbvalidate, valid, element] );

			return valid;
		},
		reset: function() {
			var cbvalidate = $( this ).data( 'cbvalidate' );

			if ( ! cbvalidate ) {
				return false;
			}

			cbvalidate.validate.resetForm();

			cbvalidate.element.triggerHandler( 'cbvalidate.reset', [cbvalidate] );

			return true;
		},
		invalid: function() {
			var cbvalidate = $( this ).data( 'cbvalidate' );

			if ( ! cbvalidate ) {
				return 0;
			}

			var invalid = cbvalidate.validate.numberOfInvalids();

			cbvalidate.element.triggerHandler( 'cbvalidate.invalid', [cbvalidate, invalid] );

			return invalid;
		},
		focus: function() {
			var cbvalidate = $( this ).data( 'cbvalidate' );

			if ( ! cbvalidate ) {
				return false;
			}

			cbvalidate.validate.focusInvalid();

			cbvalidate.element.triggerHandler( 'cbvalidate.focus', [cbvalidate] );

			return true;
		},
		valid: function() {
			var cbvalidate = $( this ).data( 'cbvalidate' );

			if ( ! cbvalidate ) {
				return true;
			}

			var valid = cbvalidate.validate.valid();

			cbvalidate.element.triggerHandler( 'cbvalidate.valid', [cbvalidate, valid] );

			return valid;
		},
		elements: function( type ) {
			var cbvalidate = $( this ).data( 'cbvalidate' );

			if ( ! cbvalidate ) {
				return [];
			}

			if ( typeof type == 'undefined' ) {
				type = null;
			}

			var elements = [];

			if ( type == 'invalid' ) {
				elements = cbvalidate.validate.invalidElements();
			} else if ( type == 'valid' ) {
				elements = cbvalidate.validate.validElements();
			} else {
				elements = cbvalidate.validate.elements();
			}

			cbvalidate.element.triggerHandler( 'cbvalidate.elements', [cbvalidate, elements] );

			return elements;
		},
		errors: function() {
			var cbvalidate = $( this ).data( 'cbvalidate' );

			if ( ! cbvalidate ) {
				return [];
			}

			var errors = cbvalidate.validate.errors();

			cbvalidate.element.triggerHandler( 'cbvalidate.errors', [cbvalidate, errors] );

			return errors;
		},
		destroy: function() {
			var cbvalidate = $( this ).data( 'cbvalidate' );

			if ( ! cbvalidate ) {
				return this;
			}

			cbvalidate.element.off( 'destroy.cbvalidate' );
			cbvalidate.validate.destroy();

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	// http://jqueryvalidation.org/creditcard-method/
	// based on http://en.wikipedia.org/wiki/Luhn_algorithm
	$.validator.addMethod( 'creditcard', function( value, element ) {
		if ( this.optional( element ) ) {
			return "dependency-mismatch";
		}

		// Accept only spaces, digits and dashes
		if ( /[^0-9 \-]+/.test( value ) ) {
			return false;
		}

		var nCheck = 0,
			nDigit = 0,
			bEven = false,
			n, cDigit;

		value = value.replace( /\D/g, "" );

		// Basing min and max length on
		// http://developer.ean.com/general_info/Valid_Credit_Card_Types
		if ( value.length < 13 || value.length > 19 ) {
			return false;
		}

		for ( n = value.length - 1; n >= 0; n-- ) {
			cDigit = value.charAt( n );
			nDigit = parseInt( cDigit, 10 );
			if ( bEven ) {
				if ( ( nDigit *= 2 ) > 9 ) {
					nDigit -= 9;
				}
			}

			nCheck += nDigit;
			bEven = !bEven;
		}

		return ( nCheck % 10 ) === 0;
	}, 'Please enter a valid credit card number.' );

	// maximum age
	$.validator.addMethod( 'maxage', function( value, element, params ) {
		if ( this.optional( element ) ) {
			return true;
		}

		var today = new Date();
		var birthDate = new Date( value );
		var age = ( today.getFullYear() - birthDate.getFullYear() );

		if ( age < ( params + 1 ) ) {
			return true;
		}

		var m = ( today.getMonth() - birthDate.getMonth() );

		if ( ( m < 0 ) || ( ( m === 0 ) && ( today.getDate() < birthDate.getDate() ) ) ) {
			age--;
		}

		return ( age <= params );
	}, $.validator.format( 'You must be no more than {0} years old.' ) );

	// minimum age
	$.validator.addMethod( 'minage', function( value, element, params ) {
		if ( this.optional( element ) ) {
			return true;
		}

		var today = new Date();
		var birthDate = new Date( value );
		var age = ( today.getFullYear() - birthDate.getFullYear() );

		if ( age > ( params + 1 ) ) {
			return true;
		}

		var m = ( today.getMonth() - birthDate.getMonth() );

		if ( ( m < 0 ) || ( ( m === 0 ) && ( today.getDate() < birthDate.getDate() ) ) ) {
			age--;
		}

		return ( age >= params );
	}, $.validator.format( 'You must be at least {0} years old.' ) );

	// range of age
	$.validator.addMethod( 'rangeage', function( value, element, params ) {
		if ( this.optional( element ) ) {
			return true;
		}

		var today = new Date();
		var birthDate = new Date( value );
		var age = ( today.getFullYear() - birthDate.getFullYear() );

		if ( ( age > ( params[0] + 1 ) ) && ( age < ( params[1] + 1 ) ) ) {
			return true;
		}

		var m = ( today.getMonth() - birthDate.getMonth() );

		if ( ( m < 0 ) || ( ( m === 0 ) && ( today.getDate() < birthDate.getDate() ) ) ) {
			age--;
		}

		return ( ( age >= params[0] ) && ( age <= params[1] ) );
	}, $.validator.format( 'You must be at least {0} years old, but not older than {1}.' ) );

	// maximum word count
	$.validator.addMethod( 'maxWords', function( value, element, params ) {
		return this.optional( element ) || ( $( value ).text().match( /\b\w+\b/g ).length <= params );
	}, $.validator.format( 'Please enter {0} words or less.' ) );

	// minimum word count
	$.validator.addMethod( 'minWords', function( value, element, params ) {
		return this.optional( element ) || ( $( value ).text().match( /\b\w+\b/g ).length >= params );
	}, $.validator.format( 'Please enter at least {0} words.' ) );

	// range of words count
	$.validator.addMethod( 'rangeWords', function( value, element, params ) {
		var valueStripped = $( value ).text();
		var regex = /\b\w+\b/g;

		return this.optional( element ) || ( valueStripped.match( regex ).length >= params[0] ) && ( valueStripped.match( regex ).length <= params[1] );
	}, $.validator.format( 'Please enter between {0} and {1} words.' ) );

	// regex validation
	$.validator.addMethod( 'pattern', function( value, element, params ) {
		if ( this.optional( element ) ) {
			return true;
		}

		if ( typeof params === 'string' ) {
			params = decodeURIComponent( params );

			var delimiter = params.substr( 0, 1 );
			var end = params.lastIndexOf( delimiter );
			var pattern = params.slice( 1, end );
			var modifiers = params.substr( ( end + 1 ) );

			params = new RegExp( pattern, modifiers );
		}

		return params.test( value );
	}, 'Invalid format.' );

	// is regex validation
	$.validator.addMethod( 'isPattern', function( value, element, params ) {
		if ( this.optional( element ) ) {
			return true;
		}

		try {
			if ( typeof value === 'string' ) {
				var delimiter = value.substr( 0, 1 );
				var end = value.lastIndexOf( delimiter );
				var pattern = value.slice( 1, end );
				var modifiers = value.substr( ( end + 1 ) );

				value = new RegExp( pattern, modifiers );
			}

			value.test( '' );
		} catch( e ) {
			return false;
		}

		return true;
	}, 'Invalid regular expression.' );

	// same as url, but Protocol is optional and specific to CB url usages
	$.validator.addMethod( 'cburl', function( value, element ) {
		return this.optional( element ) || /^(?:(https?|ftp):\/\/)?(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test( value );
	}, 'Please enter a valid URL.' );

	// same as pattern, but tests specifically for a valid Joomla/CB username
	$.validator.addMethod( 'cbusername', function( value, element ) {
		return this.optional( element ) || ( ! /^\s+|[<>"'%;()&\\]|\.\.\/|\s+$/i.test( value ) );
	}, 'Please enter a valid username with no space at beginning or end and must not contain the following characters: < > \\ " \' % ; ( ) &' );

	// tests password strength; result is always true and is for display purposes only
	$.validator.addMethod( 'passwordstrength', function( value, element ) {
		if ( ! value ) {
			$( element ).data( 'msg-success', '' );

			return true;
		}

		var strengths	=	[/.{8,}/, /[A-Z]+/, /[a-z]+/, /\d+/, /[\-\]\\`~!@#$%^&*()_=+[{}|;:'",<.>\/?]+/, /.{12,}/, /[A-Z]{2,}/, /[a-z]{2,}/, /\d{2,}/, /[\-\]\\`~!@#$%^&*()_=+[{}|;:'",<.>\/?]{2,}/];
		var strength	=	0;

		$.each( strengths, function( i, regexp ) {
			if ( value.match( regexp ) ) {
				strength++;
			}
		});

		if ( strength > strengths.length ) {
			strength	=	strengths.length;
		} else if ( strength < 0 ) {
			strength	=	0;
		}

		var normalized	=	( strength * ( 100 / strengths.length ) );
		var quality = 'cbPasswordStrengthStrong bg-success';

		if ( normalized <= 30 ) {
			quality = 'cbPasswordStrengthVeryWeak bg-danger';
		} else if ( normalized <= 50 ) {
			quality = 'cbPasswordStrengthWeak bg-warning';
		} else if ( normalized <= 70 ) {
			quality = 'cbPasswordStrengthOk bg-info';
		}

		var width = $( element ).outerWidth();

		if ( $( element ).is( ':hidden' ) ) {
			var temporary = $( element ).clone( false ).attr({
				id: '',
				'class': ''
			}).css({
				position: 'absolute',
				display: 'block',
				width: 'auto',
				visibility: 'hidden',
				padding: $( element ).css( 'padding' ),
				border: $( element ).css( 'border' ),
				margin: $( element ).css( 'margin' ),
				fontFamily: $( element ).css( 'font-family' ),
				fontSize: $( element ).css( 'font-size' ),
				fontWeight: $( element ).css( 'font-weight' ),
				boxSizing: $( element ).css( 'box-sizing' )
			}).appendTo( 'body' );

			width = temporary.outerWidth();

			temporary.remove();
		}

		$( element ).data( 'msg-success', '<div class="cbPasswordStrength progress m-0 mt-2" style="width: ' + width + 'px;"><div class="cbPasswordStrengthBar progress-bar ' + quality + '" style="width: ' + normalized + '%"></div></div>' );

		return true;
	}, '' );

	// resizes an image to specified height width using html5
	$.validator.addMethod( 'resize', function( value, element, params ) {
		if ( this.optional( element ) || this.cbIsFormSubmitting ) {
			return true;
		}

		var imageData = $( element ).siblings( '.cbValidationImageData,.cbValidationImageDataCrop' );

		if ( ! window.FileReader ) {
			if ( imageData.length ) {
				imageData.remove();
			}

			$( element ).removeClass( 'cbValidationHasImageData' );

			// FileReader is mandatory and if not available then skip resizing:
			return true;
		}

		var previous = this.previousValue( element );
		var validator = this;

		if ( previous.old === value ) {
			return previous.valid;
		}

		previous.old = value;

		if ( imageData.length ) {
			imageData.remove();
		}

		$( element ).removeClass( 'cbValidationHasImageData' );

		var maxWidth = ( typeof params[0] != 'undefined' ? params[0] : 0 );
		var maxHeight = ( typeof params[1] != 'undefined' ? params[1] : 0 );

		if ( ( maxWidth || maxHeight ) && ( $( element ).attr( 'type' ) === 'file' ) ) {
			// Check if the element has a FileList before checking each file
			if ( element.files && element.files.length ) {
				for ( var i = 0; i < element.files.length; i++ ) {
					var file = element.files[i];

					if ( ( ! /image/.test( file.type ) ) || ( ! /\.(jpg|jpeg|png|webp|avif)$/i.test( file.name ) ) ) {
						continue;
					}

					resizeImageOrientation.call( element, validator, file, previous, params );
				}

				return 'pending';
			}
		}

		return true;
	});

	// file mimetype validation
	$.validator.addMethod( 'accept', function( value, element, params ) {
		// Split mime on commas in case we have multiple types we can accept
		var typeParam = ( typeof params === 'string' ? params.replace( /\s/g, '' ).replace( /,/g, '|' ) : 'image/*' );
		var optionalValue = this.optional( element );

		// Element is optional
		if ( optionalValue ) {
			return optionalValue;
		}

		if ( $( element ).attr( 'type' ) === 'file' ) {
			// Escape string to be used in the regex
			// see: http://stackoverflow.com/questions/3446170/escape-string-for-use-in-javascript-regex
			// Escape also "/*" as "/.*" as a wildcard
			typeParam = typeParam.replace( /[\-\[\]\/\{\}\(\)\+\?\.\\\^\$\|]/g, "\\$&" ).replace( /,/g, "|" ).replace( /\/\*/g, "/.*" );

			if ( $( element ).hasClass( 'cbValidationHasImageData' ) ) {
				// File was resized using client side resizing so try to extract the mime type from the data that will actually be sent to the server:
				var imageData = $( element ).siblings( '.cbValidationImageData' ).val();
				var imageType = imageData.substring( ( imageData.indexOf( ':' ) + 1 ), imageData.indexOf( ';' ) );

				if ( imageType && ( ! imageType.match( new RegExp( '.?(' + typeParam + ')$', 'i' ) ) ) ) {
					return false;
				}
			} else {
				// Check if the element has a FileList before checking each file
				if ( element.files && element.files.length ) {
					for ( var i = 0; i < element.files.length; i++ ) {
						var file = element.files[i];

						// Grab the mimetype from the loaded file, verify it matches
						if ( file.type && ( ! file.type.replace( /mp3/, 'mpeg' ).replace( /m4a/, 'mp4' ).match( new RegExp( '.?(' + typeParam + ')$', 'i' ) ) ) ) {
							return false;
						}
					}
				}
			}
		}

		// Either return true because we've validated each file, or because the
		// browser does not support element.files and the FileList feature
		return true;
	}, $.validator.format( 'Please enter a value with a valid extension.' ) );

	// file extension validation
	$.validator.addMethod( 'extension', function( value, element, params ) {
		params = ( typeof params === 'string' ? params.replace( /,/g, '|' ) : 'png|jpe?g|gif' );

		if ( $( element ).hasClass( 'cbValidationHasImageData' ) ) {
			// File was resized using client side resizing so try to extract the extension from the data that will actually be sent to the server:
			var imageData = $( element ).siblings( '.cbValidationImageData' ).val();
			var imageType = imageData.substring( ( imageData.indexOf( ':' ) + 1 ), imageData.indexOf( ';' ) );

			if ( imageType ) {
				return this.optional( element ) || imageType.substring( ( imageType.indexOf( '/' ) + 1 ) ).match( new RegExp( '^' + params + '$', 'i' ) );
			}
		}

		return this.optional( element ) || value.match( new RegExp( '\\.(' + params + ')$', 'i' ) );
	}, $.validator.format( 'Please enter a value with a valid extension.' ) );

	// minimum and maximum file size
	$.validator.addMethod( 'filesize', function( value, element, params ) {
		var optionalValue = this.optional( element );

		// Element is optional
		if ( optionalValue ) {
			return optionalValue;
		}

		var exceedsMin = false;
		var exceedsMax = false;

		if ( ( params[0] || params[1] ) && ( $( element ).attr( 'type' ) === 'file' ) ) {
			if ( $( element ).hasClass( 'cbValidationHasImageData' ) ) {
				// File was resized using client side resizing so lets try to estimate the size of the image based off its data length:
				var imageData = $( element ).siblings( '.cbValidationImageData' ).val();
				var imageDataLength = ( imageData.length - ( imageData.indexOf( ',' ) + 1 ) );
				var imageDataPadding = ( ( imageData.charAt( imageData.length - 2 ) === '=' ) ? 2 : ( ( imageData.charAt( imageData.length - 1 ) === '=' ) ? 1 : 0 ) );
				var imageDataSize = bytesToType( ( imageDataLength * 0.75 - imageDataPadding ), params[2] );

				if ( params[0] && ( imageDataSize < params[0] ) ) {
					exceedsMin = true;
				}

				if ( params[1] && ( imageDataSize > params[1] ) ) {
					exceedsMax = true;
				}
			} else if ( element.files && element.files.length ) {
				// Check if the element has a FileList before checking each file
				for ( var i = 0; i < element.files.length; i++ ) {
					var file = element.files[i];
					var size = bytesToType( file.size, params[2] );

					if ( params[0] && ( size < params[0] ) ) {
						exceedsMin = true;
					}

					if ( params[1] && ( size > params[1] ) ) {
						exceedsMax = true;
					}
				}
			}
		}

		if ( exceedsMin || exceedsMax ) {
			if ( ! this.settings.messages[ element.name ] ) {
				this.settings.messages[element.name] = {};
				this.settings.messages[element.name].min = this.defaultMessage( element, 'filesizemin' );
				this.settings.messages[element.name].max = this.defaultMessage( element, 'filesizemax' );
				this.settings.messages[element.name].both = this.defaultMessage( element, 'filesize' );
			}

			if ( exceedsMin && ( ! exceedsMax ) ) {
				this.settings.messages[element.name].filesize = this.settings.messages[element.name].min;
			} else if ( exceedsMax && ( ! exceedsMin ) ) {
				this.settings.messages[element.name].filesize = this.settings.messages[element.name].max;
			} else {
				this.settings.messages[element.name].filesize = this.settings.messages[element.name].both;
			}

			return false;
		}

		// Either return true because we've validated each file, or because the
		// browser does not support element.files and the FileList feature
		return true;
	}, $.validator.format( 'File size must exceed the minimum of {0} {2}s, but not the maximum of {1} {2}s.' ) );

	// ensure 1 value doesn't match another (opposite of equal to validator)
	$.validator.addMethod( 'notEqualTo', function( value, element, param ) {
		return this.optional( element ) || ( ! $.validator.methods.equalTo.call( this, value, element, param ) );
	}, 'Please enter a different value, values must not be the same.' );

	// ensure 1 value doesn't match another (opposite of equal to validator)
	$.validator.addMethod( 'maxselect', function( value, element, param ) {
		return $.validator.methods.maxlength.call( this, value, element, param );
	}, $.validator.format( 'Please select no more than {0} options.' ) );

	// ensure 1 value doesn't match another (opposite of equal to validator)
	$.validator.addMethod( 'minselect', function( value, element, param ) {
		return $.validator.methods.minlength.call( this, value, element, param );
	}, $.validator.format( 'Please select at least {0} options.' ) );

	// validate a field as required if another field has a value
	$.validator.addMethod( 'requiredIf', function( value, element, param ) {
		var target = $( param );

		if ( this.settings.onfocusout ) {
			target.off( '.validate-requiredIf' ).on( 'blur.validate-requiredIf', function() {
				$( element ).valid();
			});
		}

		if ( target[0].nodeName.toLowerCase() === 'select' ) {
			var val = target.val();

			if ( ! ( val && ( val.length > 0 ) ) ) {
				return true;
			}
		}

		var targetValue = this.elementValue( target[0] );

		if ( this.checkable( target[0] ) ) {
			if ( this.getLength( targetValue, target[0] ) <= 0 ) {
				return true;
			}
		}

		if ( targetValue.length <= 0 ) {
			return true;
		}

		return $.validator.methods.required.call( this, value, element, null );
	}, 'This field is required.' );

	// forbidden words validation
	$.validator.addMethod( 'forbiddenWords', function( value, element, params ) {
		if ( this.optional( element ) ) {
			return true;
		}

		if ( $.isArray( params ) ) {
			var valid = true;

			$.each( params, function( i, v ) {
				if ( ( v !== '' ) && ( value.indexOf( v ) != -1 ) ) {
					valid = false;

					return false;
				}
			});

			return valid;
		} else {
			if ( ( params !== '' ) && ( value.indexOf( params ) != -1 ) ) {
				return false;
			}
		}

		return true;
	}, 'Invalid format.' );

	// custom extension of cbremote for cbfield fieldclass usage
	$.validator.addMethod( 'cbfield', function( value, element, params ) {
		var options = this.options;

		if ( ! params.method ) {
			params.method = 'cbfield';
		}

		if ( ! params['function'] ) {
			params['function'] = 'checkvalue';
		}

		if ( this.checkable( element ) ) {
			var values = [];

			this.findByName( element.name ).filter( ':checked' ).each( function() {
				values.push( $( this ).val() );
			});

			if ( element.name.indexOf( '[]' ) !== -1 ) {
				value = values;
			} else {
				value = values.join( '|*|' );
			}
		}

		if ( ! params.data ) {
			params.data = {};
		}

		params.data.user = params.user;
		params.data.field = params.field;
		params.data.reason = params.reason;
		params.data.value = value;

		if ( options.settings.cbfield.url ) {
			params.url = options.settings.cbfield.url;
		}

		return $.validator.methods.cbremote.call( this, value, element, params );
	}, 'Please fix this field.' );

	// same as remote, but specifically for cb
	$.validator.addMethod( 'cbremote', function( value, element, params ) {
		$( element ).data( 'remote-response', null );

		if ( this.optional( element ) || ( ! params.url ) || $( element ).hasClass( 'cbValidationRemoteSkip' ) ) {
			return true;
		}

		$( element ).addClass( 'cbValidationRemoteSkip' );

		if ( ! $( element ).valid() ) {
			$( element ).removeClass( 'cbValidationRemoteSkip' );

			// Don't bother ajaxing if the input fails validation:
			return true;
		} else {
			$( element ).removeClass( 'cbValidationRemoteSkip' );
		}

		// substitute params into the url
		$.each( params, function( k, v ) {
			if ( ( typeof v != 'object' ) && ( typeof v != 'array' ) && ( k != 'url' ) && ( k != 'data' ) ) { // Be sure to ignore objects, arrays, data, and self (url)
				params.url = params.url.replace( '[' + k + ']', v );
			}
		});

		var previous = this.previousValue( element );
		var validator = this;

		if ( ! this.settings.messages[element.name] ) {
			this.settings.messages[element.name] = {};
		}

		if ( ! params.headers ) {
			params.headers = {};
		}

		if ( this.options.settings.cbremote.csrf ) {
			params.headers['X-CSRF-Token'] = this.options.settings.cbremote.csrf;
		}

		if ( ! params.data ) {
			params.data = {};
		}

		if ( ! params.method ) {
			params.method = 'cbremote';
		}

		previous.originalMessage = this.settings.messages[element.name][params.method];
		this.settings.messages[element.name][params.method] = previous.message;

		if ( previous.old === value ) {
			$( element ).data( 'remote-response', $( previous ).data( 'remote-response' ) );

			return previous.valid;
		} else if ( this.cbIsOnKeyUp || this.cbIsFormSubmitting ) {
			return true;
		}

		previous.old = value;
		$( previous ).data( 'remote-response', null );

		this.startRequest( element );

		$.ajax({
			url: params.url,
			type: 'POST',
			mode: 'abort',
			port: 'validate' + element.name,
			dataType: 'json',
			headers: params.headers,
			data: params.data,
			converters: {
				'text json': function( result ) {
					try {
						return $.parseJSON( result );
					} catch( e ) {
						return { "valid": true, "message": result };
					}
				}
			},
			beforeSend: function() {
				var spinner = $( '<span class="cbFieldIcons cbValidateSpinner"><div class="cbSpinner spinner-border spinner-border-sm text-secondary"></div></span>' );

				if ( $( element ).parent( '.form-check' ).length ) {
					spinner.insertAfter( $( element ).parent().siblings( '.form-check' ).last() );
				} else if ( $( element ).parent( '.btn-group-list' ).length ) {
					spinner.insertAfter( $( element ).parent() );
				} else if ( $( element ).siblings( '.cbFieldIcons' ).length ) {
					spinner.insertBefore( $( element ).siblings( '.cbFieldIcons' ) );
				} else {
					spinner.insertAfter( $( element ) );
				}
			}
		}).done( function( response ) {
			var valid = ( ( response.valid === true ) || ( response.valid === 'true' ) );
			var errors = {};
			var message = null;
			var submitted = null;

			validator.settings.messages[element.name][params.method] = previous.originalMessage;

			if ( valid ) {
				$( element ).data( 'remote-response', response.message );
				$( previous ).data( 'remote-response', response.message );

				submitted = validator.formSubmitted;
				validator.resetInternals();
				validator.toHide = validator.errorsFor( element );
				validator.prepareElement( element );
				validator.formSubmitted = submitted;
				validator.successList.push( element );
				delete validator.invalid[element.name];
				validator.showErrors();
			} else {
				message = ( response.message || validator.defaultMessage( element, params.method ) );
				errors[element.name] = previous.message = ( $.isFunction( message ) ? message( value ) : message );
				validator.invalid[element.name] = true;
				validator.showErrors( errors );
			}

			previous.valid = valid;

			validator.stopRequest( element, valid );
		}).always( function() {
			if ( $( element ).parent( '.form-check,.btn-group-list' ).length ) {
				$( element ).parent().siblings( '.cbValidateSpinner' ).remove();
			} else {
				$( element ).siblings( '.cbValidateSpinner' ).remove();
			}
		});

		return 'pending';
	}, 'Please fix this field.' );

	function resizeImageOrientation( validator, file, previous, params ) {
		var $that = this;

		if ( /\.(jpg|jpeg)$/i.test( file.name ) ) {
			var reader = new FileReader();

			reader.mimeType = file.type;

			reader.onload = function( readerEvent ) {
				var orientation = null;

				if ( window.DataView && ( ! CSS.supports( 'image-orientation', 'from-image' ) ) ) {
					var view = new DataView( readerEvent.target.result );

					if ( view.getUint16( 0, false ) == 0xFFD8 ) {
						var length = view.byteLength;
						var offset = 2;

						while ( offset < length ) {
							var marker = view.getUint16( offset, false );

							offset += 2;

							if ( marker == 0xFFE1 ) {
								if ( view.getUint32( offset += 2, false ) == 0x45786966 ) {
									var little = view.getUint16( offset += 6, false ) == 0x4949;

									offset += view.getUint32( offset + 4, little );

									var tags = view.getUint16( offset, little );

									offset += 2;

									for ( var i = 0; i < tags; i++ ) {
										if ( view.getUint16( offset + ( i * 12 ), little ) == 0x0112 ) {
											orientation = view.getUint16( offset + ( i * 12 ) + 8, little );
											break;
										}
									}

									if ( orientation ) {
										break;
									}
								} else {
									break;
								}
							} else if ( ( marker & 0xFF00 ) != 0xFF00 ) {
								break;
							} else {
								offset += view.getUint16( offset, false );
							}
						}
					}
				}

				file.orientation = orientation;

				resizeImage.call( $that, validator, file, previous, params );
			};

			reader.onerror = function() {
				validator.stopRequest( $that, true );
			};

			reader.readAsArrayBuffer( file );
		} else {
			file.orientation = null;

			resizeImage.call( $that, validator, file, previous, params );
		}
	}

	function resizeImage( validator, file, previous, params ) {
		const $that = this;
		let maxWidth = ( typeof params[0] != 'undefined' ? parseInt( params[0] ) : 0 );
		let maxHeight = ( typeof params[1] != 'undefined' ? parseInt( params[1] ) : 0 );
		let aspectRatio = ( typeof params[2] != 'undefined' ? parseInt( params[2] ) : 1 );
		let alwaysResample = ( typeof params[3] != 'undefined' ? parseInt( params[3] ) : 1 );
		const messageWidth = $.validator.messages.cropwidth;
		const messageHeight = $.validator.messages.cropheight;
		const imgReader = new FileReader();

		imgReader.mimeType = file.type;

		// Always process WEBP and AVIF as we need to convert it to PNG:
		if ( ( file.type === 'image/webp' ) || ( file.type === 'image/avif' ) ) {
			alwaysResample = true;
		}

		imgReader.onload = function( imgReaderEvent ) {
			const image = new Image();

			image.mimeType = this.mimeType;

			image.onload = function( imageEvent ) {
				let originalImage = image;
				const originalWidth = image.width;
				const originalHeight = image.height;

				let mimeType = image.mimeType;
				let width = originalWidth;
				let height = originalHeight;

				// Lets correctly rotate the image before we do any form of cropping and resizing:
				if ( file.orientation ) {
					const orientationCanvas = document.createElement( 'canvas' );

					if ( ( file.orientation > 4 ) && ( file.orientation < 9 ) ) {
						width = originalHeight;
						maxWidth = ( typeof params[1] != 'undefined' ? params[1] : 0 );
						height = originalWidth;
						maxHeight = ( typeof params[0] != 'undefined' ? params[0] : 0 );

						orientationCanvas.width = originalHeight;
						orientationCanvas.height = originalWidth;
					} else {
						orientationCanvas.width = originalWidth;
						orientationCanvas.height = originalHeight;
					}

					const orientationContext = orientationCanvas.getContext( '2d' );

					orientationContext.mozImageSmoothingEnabled = false;
					orientationContext.webkitImageSmoothingEnabled = false;
					orientationContext.msImageSmoothingEnabled = false;
					orientationContext.imageSmoothingEnabled = false;
					orientationContext.imageSmoothingQuality = 'high';

					switch ( file.orientation ) {
						case 2:
							orientationContext.transform( -1, 0, 0, 1, originalWidth, 0 );
							break;
						case 3:
							orientationContext.transform( -1, 0, 0, -1, originalWidth, originalHeight );
							break;
						case 4:
							orientationContext.transform( 1, 0, 0, -1, 0, originalHeight );
							break;
						case 5:
							orientationContext.transform( 0, 1, 1, 0, 0, 0 );
							break;
						case 6:
							orientationContext.transform( 0, 1, -1, 0, originalHeight, 0 );
							break;
						case 7:
							orientationContext.transform( 0, -1, -1, 0, originalHeight, originalWidth );
							break;
						case 8:
							orientationContext.transform( 0, -1, 1, 0, 0, originalWidth );
							break;
					}

					orientationContext.drawImage( originalImage, 0, 0 );

					// Replace the image source with our rotated one:
					originalImage = orientationCanvas;
				}

				if ( ! maxWidth ) {
					maxWidth = width;
				}

				if ( ! maxHeight ) {
					maxHeight = height;
				}

				let dx = 0;
				let dy = 0;

				if ( ! aspectRatio ) {
					width = maxWidth;
					height = maxHeight;
				} else if ( aspectRatio === 1 ) {
					if ( width > maxWidth ) {
						height = Math.floor( ( height * ( maxWidth / width ) ) );
						width = maxWidth;
					}

					if ( height > maxHeight ) {
						width = Math.floor( ( width * ( maxHeight / height ) ) );
						height = maxHeight;
					}
				} else if ( ( aspectRatio === 2 ) || ( aspectRatio === 3 ) ) {
					if ( ( maxWidth > maxHeight ) || ( ( maxHeight === maxWidth ) && ( height > width ) ) ) {
						height = Math.floor( ( height * ( maxWidth / width ) ) );
						width = maxWidth;

						if ( aspectRatio === 2 ) {
							dy = Math.floor( ( ( maxHeight / 2 ) - ( height / 2 ) ) );
						}
					} else if ( ( maxHeight > maxWidth ) || ( ( maxHeight === maxWidth ) && ( width > height ) ) ) {
						width = Math.floor( ( width * ( maxHeight / height ) ) );
						height = maxHeight;

						if ( aspectRatio === 2 ) {
							dx = Math.floor( ( ( maxWidth / 2 ) - ( width / 2 ) ) );
						}
					} else if ( width === height ) {
						width = maxWidth;
						height = maxHeight;
					}
				}

				let cropOrientation = '';

				if ( height > width ) {
					cropOrientation = 'portrait';

					if ( height <= maxHeight ) {
						cropOrientation = 'landscape';
					}
				} else if ( width > height ) {
					cropOrientation = 'landscape';

					if ( width <= maxWidth ) {
						cropOrientation = 'portrait';
					}
				} else {
					cropOrientation = 'square';
				}

				if ( ( ! alwaysResample ) && ( originalWidth === width ) && ( originalHeight === height ) && ( aspectRatio !== 3 ) ) {
					// No change in size so lets abort resizing:
					validator.stopRequest( $that, true );
				} else {
					const canvas = document.createElement( 'canvas' );

					if ( aspectRatio === 2 ) {
						canvas.width = maxWidth;
						canvas.height = maxHeight;

						if ( cropOrientation === 'square' ) {
							dx = 0;
							dy = 0;
						}
					} else {
						canvas.width = width;
						canvas.height = height;
					}

					const context = canvas.getContext( '2d' );

					context.mozImageSmoothingEnabled = false;
					context.webkitImageSmoothingEnabled = false;
					context.msImageSmoothingEnabled = false;
					context.imageSmoothingEnabled = false;
					context.imageSmoothingQuality = 'high';

					context.drawImage( originalImage, dx, dy, width, height );

					// If this is WEBP or AVIF change it to PNG:
					if ( ( mimeType === 'image/webp' ) || ( mimeType === 'image/avif' ) ) {
						mimeType = 'image/png';
					}

					const name = $( $that ).attr( 'name' );

					$( $that ).addClass( 'cbValidationHasImageData' );
					$( '<input type="hidden"' + ( name.length ? ' name="' + name + '_image_data"' : '' ) + ' value="' + canvas.toDataURL( mimeType ) + '" class="cbValidationImageData" />' ).insertAfter( $( $that ) );

					// if ( ( aspectRatio == 3 ) && ( ( width > maxWidth ) || ( height > maxHeight ) ) ) {
					if ( ( aspectRatio === 3 ) && ( cropOrientation !== 'square' ) ) {
						$( `
							<div class="cbValidationImageDataCrop">
								<div class="cbValidationImageDataCropMsg">${( cropOrientation === 'landscape' ? messageWidth : ( cropOrientation === 'portrait' ? messageHeight : '' ) )}</div>
								<div class="cbValidationImageDataCropArea">
									<div class="cbValidationImageDataCropSelect"></div>
									<img src="${canvas.toDataURL( mimeType )}" class="cbValidationImageDataCropImage" />
								</div>
							</div>
						` ).insertAfter( $( $that ).siblings( '.cbValidationImageData' ) );

						const dragCrop = function( force ) {
							let cropDx = 0;
							let cropDy = 0;

							if ( force === true ) {
								if ( cropOrientation === 'landscape' ) {
									cropDx = ( ( maxWidth / 2 ) - ( width / 2 ) );
								} else if ( cropOrientation === 'portrait' ) {
									cropDy = ( ( maxHeight / 2 ) - ( height / 2 ) );
								}
							} else {
								const position = $( this ).position();

								if ( cropOrientation === 'landscape' ) {
									if ( $( this ).parent().width() ) {
										cropDx = - ( width / ( $( this ).parent().width() / position.left ) );
									}
								} else if ( cropOrientation === 'portrait' ) {
									if ( $( this ).parent().height() ) {
										cropDy = - ( height / ( $( this ).parent().height() / position.top ) );
									}
								}
							}

							const cropped = document.createElement( 'canvas' );

							cropped.width = maxWidth;
							cropped.height = maxHeight;

							const croppedContect = cropped.getContext( '2d' );

							croppedContect.mozImageSmoothingEnabled = false;
							croppedContect.webkitImageSmoothingEnabled = false;
							croppedContect.msImageSmoothingEnabled = false;
							croppedContect.imageSmoothingEnabled = false;
							croppedContect.imageSmoothingQuality = 'high';

							croppedContect.drawImage( originalImage, cropDx, cropDy, width, height );

							if ( force === true ) {
								$( this ).siblings( '.cbValidationImageData' ).val( cropped.toDataURL( mimeType ) );
							} else {
								$( this ).closest( '.cbValidationImageDataCrop' ).siblings( '.cbValidationImageData' ).val( cropped.toDataURL( mimeType ) );
							}
						};

						dragCrop.call( $that, true );

						$( $that ).siblings( '.cbValidationImageDataCrop' ).find( '.cbValidationImageDataCropImage' ).on( 'load', function() {
							$( this ).siblings( '.cbValidationImageDataCropSelect' ).draggable({
								containment: 'parent',
								scroll: false,
								axes: ( cropOrientation === 'landscape' ? 'x' : ( cropOrientation === 'portrait' ? 'y' : false ) ),
								create: function() {
									if ( cropOrientation === 'landscape' ) {
										$( this ).css({
											height: '100%',
											width: ( ( maxWidth / width ) * 100 ) + '%'
										}).css( 'left', ( ( $( this ).parent().width() / 2 ) - ( $( this ).width() / 2 ) ) + 'px' );
									} else if ( cropOrientation === 'portrait' ) {
										$( this ).css({
											height: ( ( maxHeight / height ) * 100 ) + '%',
											width: '100%'
										}).css( 'top', ( ( $( this ).parent().height() / 2 ) - ( $( this ).height() / 2 ) ) + 'px' );
									} else {
										$( this ).css({
											height: '100%',
											width: '100%'
										});
									}

									dragCrop.call( this );
								},
								stop: function( e, ui ) {
									dragCrop.call( this );
								}
							});
						});
					}

					previous.valid = true;

					validator.stopRequest( $that, true );
				}
			};

			image.onerror = function() {
				validator.stopRequest( $that, true );
			};

			image.src = imgReaderEvent.target.result;
		};

		imgReader.onerror = function() {
			validator.stopRequest( $that, true );
		};

		imgReader.readAsDataURL( file );
	}

	function bytesToType( bytes, type ) {
		if ( type == 'TB' ) {
			bytes = ( ( ( ( bytes / 1024 ) / 1024 ) / 1024 ) / 1024 );
		} else if ( type == 'GB' ) {
			bytes = ( ( ( bytes / 1024 ) / 1024 ) / 1024 );
		} else if ( type == 'MB' ) {
			bytes = ( ( bytes / 1024 ) / 1024 );
		} else if ( type == 'KB' ) {
			bytes = ( bytes / 1024 );
		}

		return bytes;
	}

	$.fn.cbvalidate = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbvalidate.dataMap = {
		messages: 'cbvalidate-messages',
		settings: 'cbvalidate-settings'
	};

	$.fn.cbvalidate.defaults = {
		init: true,
		useData: false,
		messages: null,
		settings: null
	};
})(jQuery);