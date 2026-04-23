(function($) {
	var instances = [];
	var ajaxCache = {};
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbselect = $( $this ).data( 'cbselect' );

				if ( cbselect || $( $this ).hasClass( 'select2' ) ) {
					return; // cbselect or select2 is already bound; so no need to rebind below
				}

				cbselect = {};
				cbselect.type = ( $( $this ).prop( 'multiple' ) ? 'multiple' : 'single' );
				cbselect.options = ( typeof options != 'undefined' ? options : {} );
				cbselect.defaults = $.fn.cbselect.defaults;
				cbselect.settings = $.extend( true, {}, cbselect.defaults, cbselect.options );
				cbselect.element = $( $this );

				if ( cbselect.settings.useData ) {
					$.each( $.fn.cbselect.dataMap, function( key, value ) {
						const dataValue = cbselect.element.data( value );

						if ( typeof dataValue != 'undefined' ) {
							cbselect.settings[key] = dataValue;
						}
					});
				}

				cbselect.element.triggerHandler( 'cbselect.init.before', [cbselect] );

				if ( ! cbselect.settings.init ) {
					return;
				}

				if ( cbselect.settings.tags ) {
					var separators = cbselect.settings.separator;

					if ( ! separators ) {
						separators = [','];
					}

					cbselect.settings.tokenSeparators = separators;
				}

				if ( ( cbselect.type == 'multiple' ) || cbselect.settings.tags ) {
					if ( cbselect.settings.width == 'calculate' ) {
						cbselect.settings.width = 'auto'
					}

					if ( cbselect.settings.height == 'calculate' ) {
						cbselect.settings.height = 'auto'
					}
				}

				if ( cbselect.element.find( 'option:empty' ) ) {
					// Empty options don't parse correctly into select2 options so we need to be sure it's given a non-breaking space to properly display:
					cbselect.element.find( 'option:empty' ).html( '&nbsp;' );
				}

				var width = null;
				var height = null;

				if ( ( cbselect.settings.width == 'calculate' ) || ( cbselect.settings.height == 'calculate' ) ) {
					width = ( cbselect.element.outerWidth() + 50 );
					height = cbselect.element.outerHeight();

					if ( cbselect.element.is( ':hidden' ) ) {
						var cssWidth = cbselect.element.css( 'width' );
						var cssHeight = cbselect.element.css( 'height' );

						var temporary = cbselect.element.clone( false ).attr({
							id: '',
							'class': ''
						}).css({
							position: 'absolute',
							display: 'block',
							visibility: 'hidden',
							width: 'auto',
							height: 'auto',
							minWidth: ( cssWidth && ( cssWidth != '0px' ) ? cssWidth : 'auto' ),
							minHeight: ( cssHeight && ( cssHeight != '0px' ) ? cssHeight : 'auto' ),
							padding: cbselect.element.css( 'padding' ),
							border: cbselect.element.css( 'border' ),
							margin: cbselect.element.css( 'margin' ),
							fontFamily: cbselect.element.css( 'font-family' ),
							fontSize: cbselect.element.css( 'font-size' ),
							fontWeight: cbselect.element.css( 'font-weight' ),
							lineHeight: cbselect.element.css( 'line-height' ),
							boxSizing: cbselect.element.css( 'box-sizing' ),
							wordSpacing: cbselect.element.css( 'word-spacing' )
						}).appendTo( 'body' );

						width = ( temporary.outerWidth() + 50 );
						height = temporary.outerHeight();

						temporary.remove();
					}

					if ( cbselect.settings.width == 'calculate' ) {
						cbselect.settings.width = width;
					}

					if ( cbselect.settings.height == 'calculate' ) {
						cbselect.settings.height = height;
					}
				}

				if ( cbselect.settings.width == 'element' ) {
					width = cbselect.element.outerWidth();

					if ( ( ! width ) || ( width == '0px' ) ) {
						width = 'auto';
					}
				} else if ( ( cbselect.settings.width == 'copy' ) || ( cbselect.settings.width == 'resolve' ) ) {
					width = cbselect.element.css( 'width' );

					if ( ( ! width ) || ( width == '0px' ) ) {
						width = 'auto';
					}
				} else if ( cbselect.settings.width == 'off' ) {
					width = null;
				} else if ( ! width ) {
					width = cbselect.settings.width;
				}

				if ( cbselect.settings.height == 'element' ) {
					height = cbselect.element.outerHeight();

					if ( ( ! height ) || ( height == '0px' ) ) {
						height = 'auto';
					}
				} else if ( ( cbselect.settings.height == 'copy' ) || ( cbselect.settings.height == 'resolve' ) ) {
					height = cbselect.element.css( 'height' );

					if ( ( ! height ) || ( height == '0px' ) ) {
						height = 'auto';
					}
				} else if ( cbselect.settings.height == 'off' ) {
					height = null;
				} else if ( ! height ) {
					height = cbselect.settings.height;
				}

				var minWidth = null;

				if ( cbselect.type == 'multiple' ) {
					if ( ( width == 'auto' ) && cbselect.settings.placeholder ) {
						var placeholderSizer = $( '<span>' + cbselect.settings.placeholder + '</span>' ).css({
							position: 'absolute',
							display: 'block',
							visibility: 'hidden',
							padding: cbselect.element.css( 'padding' ),
							border: cbselect.element.css( 'border' ),
							margin: cbselect.element.css( 'margin' ),
							fontFamily: cbselect.element.css( 'font-family' ),
							fontSize: cbselect.element.css( 'font-size' ),
							fontWeight: cbselect.element.css( 'font-weight' ),
							lineHeight: cbselect.element.css( 'line-height' ),
							boxSizing: cbselect.element.css( 'box-sizing' ),
							wordSpacing: cbselect.element.css( 'word-spacing' )
						}).appendTo( 'body' );

						minWidth = ( placeholderSizer.outerWidth() + 5 );

						placeholderSizer.remove();
					}
				} else if ( cbselect.settings.placeholder ) {
					// This is mandatory for single select with placeholder:
					cbselect.settings.allowClear = true;
				}

				var hasTooltip = cbselect.element.is( '.cbTooltip,[data-hascbtooltip=\"true\"]' );

				if ( hasTooltip ) {
					cbselect.element.attr( 'data-cbtooltip-open-target', '~ .select2-container:first' );
					cbselect.element.attr( 'data-cbtooltip-close-target', '~ .select2-container:first' );
					cbselect.element.attr( 'data-cbtooltip-position-target', '~ .select2-container:first' );

					cbselect.element.data( 'cbtooltip-open-target', '~ .select2-container:first' );
					cbselect.element.data( 'cbtooltip-close-target', '~ .select2-container:first' );
					cbselect.element.data( 'cbtooltip-position-target', '~ .select2-container:first' );
				}

				var cssClasses = [];

				$.each( cbselect.element.attr( 'class' ).split( /\s+/ ), function( i, cssClass ) {
					if ( ( cssClass != 'cbTooltip' ) && ( cssClass != 'cbSelect' ) ) {
						cssClasses.push( cssClass );
					}
				});

				var select2Settings = $.extend( true, {}, cbselect.settings );

				delete select2Settings['separator'];
				delete select2Settings['containerCssClass'];
				delete select2Settings['dropdownCssClass'];
				delete select2Settings['selectionCssClass'];

				// Select2 doesn't support a selector for the dropdown parent so lets implement treating it as looking for closest match
				if ( select2Settings['dropdownParent'] && ( typeof select2Settings['dropdownParent'] === 'string' ) ) {
					var dropdownParent = cbselect.element.closest( select2Settings['dropdownParent'] );

					if ( dropdownParent.length ) {
						select2Settings['dropdownParent'] = dropdownParent;

						if ( ! cbselect.settings.dropdownCssClass ) {
							// Treat this like a full width parent based dropdown if no dropdown class is supplied:
							cbselect.settings.dropdownCssClass = 'select2-container--fullwidth';
						}
					} else {
						select2Settings['dropdownParent'] = null;
					}
				}

				// Style the multi-selected and tag values as badges using custom selection template (do not override existing custom template usage):
				if ( ( cbselect.settings.theme == 'bootstrap' ) && ( ( cbselect.type == 'multiple' ) || cbselect.settings.tags ) && ( ! ( 'templateSelection' in select2Settings ) ) ) {
					select2Settings['templateSelection'] = function( selection, container ) {
						container.addClass( 'badge badge-primary' );

						return $.fn.select2.defaults.defaults.templateSelection( selection, container );
					};
				}

				if ( cbselect.settings.url ) {
					cbselect.ajax_url = null;
					cbselect.ajax_restore = false;
					cbselect.ajax_term = '';
					cbselect.ajax_page = 1;

					if ( cbselect.element.children( 'optgroup,option' ).length ) {
						// The select element has initial options so we need to be sure they're still output when no search is being made:
						select2Settings['dataAdapter'] = $.fn.select2.amd.require( 'select2/data/ajax-extended' );
					}

					select2Settings['ajax'] = {
						url: cbselect.settings.url,
						data: function ( params ) {
							return {
								search: $.trim( ( params.term || '' ) ),
								page: ( params.page || 1 )
							};
						},
						dataType: 'json',
						delay: 600,
						cache: true,
						transport: function ( params, success, failure ) {
							var $request = $.ajax( params );

							if ( cbselect.ajax_restore ) {
								$request.always( function() {
									cbselect.ajax_restore = false;

									cbselect.select2.results.loading = false;
									cbselect.select2.results.lastParams.term = cbselect.ajax_term;
									cbselect.select2.results.lastParams.page = cbselect.ajax_page;

									params.term = cbselect.ajax_term;
									params.page = cbselect.ajax_page;
								});
							} else {
								$request.always( success );
							}

							return $request;
						},
						beforeSend: function( jqXHR, settings ) {
							cbselect.ajax_url = settings.url;

							if ( cbselect.ajax_url in ajaxCache ) {
								return false;
							}

							cbselect.element.triggerHandler( 'cbselect.ajax.send', [cbselect, settings] );

							return true;
						},
						processResults: function ( data, params ) {
							cbselect.element.triggerHandler( 'cbselect.ajax.data', [cbselect, data, params] );

							if ( cbselect.ajax_url in ajaxCache ) {
								cbselect.ajax_term = params.term;
								cbselect.ajax_page = params.page;

								return ajaxCache[cbselect.ajax_url];
							}

							params.page = ( params.page || 1 );

							ajaxCache[cbselect.ajax_url] = data;

							cbselect.ajax_term = params.term;
							cbselect.ajax_page = params.page;

							return data;
						}
					};
				}

				cbselect.element.select2( select2Settings );
				cbselect.select2 = cbselect.element.data( 'select2' );

				cbselect.container = cbselect.select2.$container;
				cbselect.dropdown = cbselect.select2.$dropdown;
				cbselect.selection = cbselect.select2.$selection;
				cbselect.searchable = ! ( ( cbselect.settings.minimumResultsForSearch < 0 ) || ( cbselect.element.find( 'option' ).length < cbselect.settings.minimumResultsForSearch ) );

				if ( minWidth ) {
					cbselect.selection.css( 'min-width', minWidth );
				}

				$.each( cssClasses, function( i, cssClass ) {
					cbselect.container.addClass( cssClass );
				});

				// Add the Bootstrap 4 classes so styling can be inherited from the template:
				if ( cbselect.settings.theme == 'bootstrap' ) {
					if ( ( ! cbselect.settings.tags ) && cbselect.container.hasClass( 'form-control' ) ) {
						cbselect.container.addClass( 'custom-select' );

						if ( cbselect.container.hasClass( 'form-control-sm' ) ) {
							cbselect.container.addClass( 'custom-select-sm' );
						} else if ( cbselect.container.hasClass( 'form-control-lg' ) ) {
							cbselect.container.addClass( 'custom-select-lg' );
						}
					}

					cbselect.dropdown.find( '.select2-dropdown' ).addClass( 'bg-white border' );
					cbselect.dropdown.find( '.select2-search__field' ).addClass( 'form-control' );
					cbselect.container.find( '.select2-search__field' ).addClass( 'form-control' );

					if ( cbselect.container.hasClass( 'form-control-sm' ) ) {
						cbselect.dropdown.find( '.select2-dropdown' ).addClass( 'text-small' );
						cbselect.dropdown.find( '.select2-search__field' ).addClass( 'form-control-sm' );
						cbselect.container.find( '.select2-search__field' ).addClass( 'form-control-sm' );
					} else if ( cbselect.container.hasClass( 'form-control-lg' ) ) {
						cbselect.dropdown.find( '.select2-dropdown' ).addClass( 'text-large' );
						cbselect.dropdown.find( '.select2-search__field' ).addClass( 'form-control-lg' );
						cbselect.container.find( '.select2-search__field' ).addClass( 'form-control-lg' );
					} else if ( cbselect.settings.url ) {
						cbselect.dropdown.find( '.select2-search__field' ).addClass( 'form-control-sm' );
					}
				}

				cbselect.dropdown.addClass( 'cb_template' );

				if ( cbselect.type == 'multiple' ) {
					cbselect.dropdown.addClass( 'select2-container--multiple' );
					cbselect.container.addClass( 'select2-container--multiple' );
				} else {
					cbselect.dropdown.addClass( 'select2-container--single' );
					cbselect.container.addClass( 'select2-container--single' );
				}

				if ( cbselect.settings.tags ) {
					cbselect.dropdown.addClass( 'select2-container--tags' );
					cbselect.container.addClass( 'select2-container--tags' );
				}

				cbselect.container.on( 'click.cbselect', function( e ) {
					// If clicking the container we need to be sure we toggle the dropdown since this binding is on the selection element:
					if ( e.target !== this ) {
						return;
					}

					cbselect.element.cbselect( 'toggle' );
				}).on( 'keydown.cbselect', function ( e ) {
					if ( $( this ).hasClass( 'select2-container--disabled' ) || $( this ).hasClass( 'select2-container--open' ) ) {
						// Skip keyboard navigation if we're disabled or already open since open dropdown works fine with keyboard:
						return;
					}

					if ( ( e.key == 'Down' || e.key == 'ArrowDown' ) && ( ! e.altKey ) ) { // Down (ignore if alt is held since core behavior will open dropdown)
						var downIndex = cbselect.element.find( 'option' ).index( cbselect.element.find( 'option:selected' ) );
						var downValue = cbselect.element.find( 'option' ).slice( ( downIndex + 1 ) ).filter( ':enabled' ).first().val();

						if ( typeof downValue != 'undefined' ) {
							cbselect.element.val( downValue ).trigger( 'change' );
						}

						e.preventDefault();
					} else if ( e.key == 'Up' || e.key == 'ArrowUp' ) { // Up
						var upIndex = cbselect.element.find( 'option' ).index( cbselect.element.find( 'option:selected' ) );
						var upValue = cbselect.element.find( 'option' ).slice( 0, upIndex ).filter( ':enabled' ).last().val();

						if ( typeof upValue != 'undefined' ) {
							cbselect.element.val( upValue ).trigger( 'change' );
						}

						e.preventDefault();
					}
				}).on( 'keypress.cbselect', function ( e ) {
					if ( $( this ).hasClass( 'select2-container--disabled' ) || $( this ).hasClass( 'select2-container--open' ) ) {
						// Skip auto searching if already open or disabled:
						return;
					}

					if ( ( cbselect.type != 'single' )
						|| ( ! cbselect.searchable )
						|| e.altKey
						|| e.ctrlKey
						|| e.metaKey
						|| ( e.key.length !== 1 )
						|| ( ! /[a-zA-Z]/.test( e.key ) ) // Only accept letters
					) {
						return;
					}

					cbselect.element.select2( 'open' );
					cbselect.dropdown.find( '.select2-search > input' ).val( e.key ).trigger( 'change' );
				});

				cbselect.element.on( 'select2:opening.cbselect', function( e ) {
					if ( cbselect.element.hasClass( 'disabled' )
						|| cbselect.element.is( ':disabled' )
						|| cbselect.container.hasClass( 'disabled' )
						|| cbselect.container.is( ':disabled' )
					) {
						e.preventDefault();
					}

					if ( cbselect.settings.url && cbselect.ajax_restore ) {
						cbselect.dropdown.find( '.select2-search > input' ).val( cbselect.ajax_term );
					}
				}).on( 'select2:open.cbselect', function() {
					cbselect.element.triggerHandler( 'cbselect.open', [cbselect] );
				}).on( 'select2:close.cbselect', function() {
					if ( cbselect.settings.url && ( ! cbselect.settings.tags ) ) {
						cbselect.ajax_restore = true;
					}

					cbselect.element.triggerHandler( 'cbselect.close', [cbselect] );
				}).on( 'select2:select.cbselect', function( e ) {
					if ( cbselect.settings.placeholder ) {
						// Ensure the placerholder is never selected:
						cbselect.element.cbselect( 'unset', cbselect.select2.selection.placeholder.id )
					}

					cbselect.element.triggerHandler( 'cbselect.selecting', [cbselect, e.params.data.id, e.params.data.element] );
				}).on( 'select2:unselect.cbselect', function( e ) {
					var selectedValue = cbselect.element.cbselect( 'get' );

					if ( cbselect.settings.placeholder && ( ( selectedValue === null ) || ( Array.isArray( selectedValue ) && ( ! selectedValue.length ) ) ) ) {
						// There's a placeholder and we've no value selected so we need to fallback to placeholder:
						cbselect.element.cbselect( 'set', cbselect.select2.selection.placeholder.id )
					}

					cbselect.element.triggerHandler( 'cbselect.removing', [cbselect, e.params.data.id, e.params.data.element] );
				});

				if ( cbselect.element.hasClass( 'btn' ) ) {
					// Handle button focus styling:
					cbselect.selection.on( 'focus.cbselect', function() {
						if ( $( this ).hasClass( 'select2-container--disabled' ) ) {
							return;
						}

						cbselect.container.addClass( 'focus' );
					}).on( 'blur.cbselect', function() {
						cbselect.container.removeClass( 'focus' );
					});
				}

				cbselect.container.attr( 'id', 'cbselect_' + cbselect.element.attr( 'id' ) );

				if ( height && ( height != '0px' ) ) {
					cbselect.container.css( 'height', height );
				}

				if ( cbselect.settings.containerCssClass ) {
					cbselect.container.addClass( cbselect.settings.containerCssClass );
				}

				if ( cbselect.settings.dropdownCssClass ) {
					cbselect.dropdown.addClass( cbselect.settings.dropdownCssClass );
				}

				if ( cbselect.settings.selectionCssClass ) {
					cbselect.selection.addClass( cbselect.settings.selectionCssClass );
				}

				cbselect.element.on( 'change.cbselect', function() {
					if ( typeof cbParamChange != 'undefined' ) {
						this.dispatchEvent( new Event( 'cbParamChange' ) );
					}
				});

				// Destroy the cbselect element:
				cbselect.element.on( 'remove.cbselect destroy.cbselect', function() {
					cbselect.element.cbselect( 'destroy' );
				});

				// Rebind the cbselect element to pick up any data attribute modifications:
				cbselect.element.on( 'rebind.cbselect', function() {
					cbselect.element.cbselect( 'rebind' );
				});

				// If the cbselect element is modified we need to rebuild it to ensure all our bindings are still ok:
				cbselect.element.on( 'modified.cbselect', function( e, oldId, newId, index ) {
					if ( oldId != newId ) {
						cbselect.element.cbselect( 'rebind' );
					}
				});

				// If the cbselect is cloned we need to rebind it back:
				cbselect.element.on( 'cloning.cbselect', function() {
					$( this ).cbselect( 'destroy' );

					$( this ).on( 'rebind.cbselect', function() {
						$( this ).off( 'rebind.cbselect' ); // disgard this binding as we're done with it

						cbselect.element.cbselect( cbselect.options );
					});

					$( this ).on( 'cloned.cbselect', function() {
						$( this ).off( 'cloned.cbselect' ); // disgard this binding as we're done with it

						$( this ).cbselect( cbselect.options );
					});

					return true;
				});

				cbselect.element.triggerHandler( 'cbselect.init.after', [cbselect] );

				// Bind the cbselect to the element so it's reusable and chainable:
				cbselect.element.data( 'cbselect', cbselect );

				// Add this instance to our instance array so we can keep track of our select2 instances:
				instances.push( cbselect );
			});
		},
		toggle: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			if ( cbselect.container.hasClass( 'select2-container--open' ) ) {
				cbselect.element.select2( 'close' );
			} else {
				cbselect.element.select2( 'open' );
			}

			return this;
		},
		open: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.select2( 'open' );

			return this;
		},
		close: function( value ) {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.select2( 'close' );

			return this;
		},
		get: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return null;
			}

			return cbselect.element.val();
		},
		set: function( value ) {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return null;
			}

			cbselect.element.val( value ).trigger( 'change' );

			return cbselect.element.cbselect( 'get' );
		},
		unset: function( value ) {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return null;
			}

			if ( ! $.isArray( value ) ) {
				value = [value];
			}

			var existingValue = cbselect.element.cbselect( 'get' );
			var newValue = existingValue;
			var isChanged = false;

			if ( $.isArray( existingValue ) ) {
				$.each( value, function( i, v ) {
					if ( newValue.indexOf( v ) > -1 ) {
						newValue.splice( newValue.indexOf( v ), 1 );

						isChanged = true;
					}
				});
			} else {
				$.each( value, function( i, v ) {
					if ( v === existingValue ) {
						newValue = '';

						isChanged = true;

						return false;
					}
				});
			}

			if ( isChanged ) {
				return cbselect.element.cbselect( 'set', newValue );
			} else {
				// Don't bother firing the change event and changing the value if it didn't change:
				return existingValue;
			}
		},
		enable: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.prop( 'disabled', false );

			return this;
		},
		disable: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.prop( 'disabled', true );

			return this;
		},
		container: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			return cbselect.container;
		},
		dropdown: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			return cbselect.dropdown;
		},
		rebind: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.cbselect( 'destroy' );
			cbselect.element.cbselect( cbselect.options );

			return this;
		},
		destroy: function() {
			var cbselect = $( this ).data( 'cbselect' );

			if ( ! cbselect ) {
				return this;
			}

			cbselect.element.off( '.cbselect' );
			cbselect.element.removeData( 'cbselect' );

			if ( cbselect.element.is( '.cbTooltip,[data-hascbtooltip=\"true\"]' ) ) {
				cbselect.element.removeAttr( 'data-cbtooltip-open-target' );
				cbselect.element.removeAttr( 'data-cbtooltip-close-target' );
				cbselect.element.removeAttr( 'data-cbtooltip-position-target' );

				cbselect.element.removeData( 'cbtooltip-open-target' );
				cbselect.element.removeData( 'cbtooltip-close-target' );
				cbselect.element.removeData( 'cbtooltip-position-target' );
			}

			cbselect.element.select2( 'destroy' );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbselect.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			cbselect.element.triggerHandler( 'cbselect.destroyed', [cbselect] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	$.fn.select2.amd.define( 'select2/data/ajax-extended', ['./array','./ajax', './tags', '../utils', 'module', 'jquery'], function ( ArrayAdapter, AjaxAdapter, Tags, Utils, module, $ ) {
		function ExtendedAjaxAdapter( $element, options ) {
			this.minimumInputLength = options.get( 'minimumInputLength' );
			this.paginationMore = options.get( 'paginationMore' );
			this.paginationPage = options.get( 'paginationPage' );

			ExtendedAjaxAdapter.__super__.constructor.call( this, $element, options );
		}

		Utils.Extend( ExtendedAjaxAdapter, AjaxAdapter) ;

		var arrayQuery = ArrayAdapter.prototype.query;
		var ajaxQuery = AjaxAdapter.prototype.query;

		ExtendedAjaxAdapter.prototype.query = function ( params, callback ) {
			if ( ( ( ! params.term ) || ( params.term.length < this.minimumInputLength ) ) && ( params._type && ( params._type == 'query' ) ) ) {
				var cbselect = this.$element.data( 'cbselect' );

				if ( cbselect && cbselect.ajax_restore ) {
					cbselect.ajax_restore = false;

					cbselect.select2.results.loading = false;
					cbselect.select2.results.lastParams.term = cbselect.ajax_term;
					cbselect.select2.results.lastParams.page = cbselect.ajax_page;

					params.term = cbselect.ajax_term;
					params.page = cbselect.ajax_page;

					return;
				}

				var self = this;

				var arrayCallback = function ( arrayResults ) {
					if ( cbselect ) {
						cbselect.ajax_url = null;
					}

					arrayResults['pagination'] = { more: ( self.paginationMore || false ), page: ( self.paginationPage || 1 ) };

					var processedResults = self.processResults( arrayResults, params );

					callback( processedResults );
				};

				arrayQuery.call( this, params, arrayCallback );
			} else {
				// A search was made so lets continue as normally with an ajax call:
				ajaxQuery.call( this, params, callback );
			}
		};

		if ( module.config().tags ) {
			// Tags are enabled so we need to be sure to extend the tags behavior:
			return Utils.Decorate( ExtendedAjaxAdapter, Tags );
		} else {
			return ExtendedAjaxAdapter;
		}
	});

	$.fn.cbselect = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbselect.dataMap = {
		url: 'cbselect-url',
		tags: 'cbselect-tags',
		separator: 'cbselect-separator',
		placeholder: 'cbselect-placeholder',
		closeOnSelect: 'cbselect-close-on-select',
		selectOnClose: 'cbselect-select-on-close',
		minimumInputLength: 'cbselect-minimum-input-length',
		maximumInputLength: 'cbselect-maximum-input-length',
		maximumSelectionLength: 'cbselect-maximum-selection-length',
		minimumResultsForSearch: 'cbselect-minimum-results-for-search',
		dropdownCssClass: 'cbselect-dropdown-css-class',
		containerCssClass: 'cbselect-container-css-class',
		selectionCssClass: 'cbselect-selection-css-class',
		dropdownAutoWidth: 'cbselect-dropdown-auto-width',
		theme: 'cbselect-theme',
		width: 'cbselect-width',
		height: 'cbselect-height'
	};

	$.fn.cbselect.defaults = {
		init: true,
		useData: true,
		url: null,
		tags: false,
		separator: null,
		placeholder: null,
		closeOnSelect: true,
		selectOnClose: false,
		minimumInputLength: 0,
		maximumInputLength: 0,
		maximumSelectionLength: 0,
		minimumResultsForSearch: 0,
		dropdownCssClass: null,
		containerCssClass: null,
		selectionCssClass: null,
		dropdownAutoWidth: true,
		theme: 'bootstrap',
		width: 'calculate',
		height: 'off'
	};
})(jQuery);