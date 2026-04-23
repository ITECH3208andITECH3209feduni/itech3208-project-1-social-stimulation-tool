(function($) {
	var instances = [];
	var methods = {
		init: function( options ) {
			return this.each( function () {
				var $this = this;
				var cbrepeat = $( $this ).data( 'cbrepeat' );

				if ( cbrepeat ) {
					return; // cbtabs is already bound; so no need to rebind below
				}

				cbrepeat = {};
				cbrepeat.options = ( typeof options != 'undefined' ? options : {} );
				cbrepeat.defaults = $.fn.cbrepeat.defaults;
				cbrepeat.settings = $.extend( true, {}, cbrepeat.defaults, cbrepeat.options );
				cbrepeat.element = $( $this );
				cbrepeat.rowIndex = 0;

				if ( cbrepeat.settings.useData ) {
					$.each( $.fn.cbrepeat.dataMap, function( key, value ) {
						const dataValue = cbrepeat.element.data( value );

						if ( typeof dataValue != 'undefined' ) {
							cbrepeat.settings[key] = dataValue;
						}
					});
				}

				if ( ! cbrepeat.settings.min ) {
					cbrepeat.settings.min = 1;
				}

				cbrepeat.element.triggerHandler( 'cbrepeat.init.before', [cbrepeat] );

				if ( ! cbrepeat.settings.init ) {
					return;
				}

				cbrepeat.rowIndex = findIndex( cbrepeat );

				if ( cbrepeat.settings.sortable ) {
					var first = cbrepeat.element.find( '.cbRepeatRow' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
					}).first();

					cbrepeat.element.sortable({
						placeholder: first.attr( 'class' ) + ' cbRepeatRowPlaceholder',
						forcePlaceholderSize: true,
						cursor: 'move',
						items: '.cbRepeatRow',
						containment: 'parent',
						animated: true,
						stop: function( event, ui ) {
							var checked = [];

							cbrepeat.element.find( '.cbRepeatRow' ).filter( function() {
								return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
							}).find( 'input:checked' ).each( function() {
								checked.push( $( this ) );
							});

							updateRepeat( cbrepeat );

							$.each( checked, function( checkedElementId, checkedElement ) {
								checkedElement.prop( 'checked', true );
							});

							cbrepeat.element.triggerHandler( 'cbrepeat.move', [cbrepeat, event, ui] );
						},
						tolerance: 'pointer',
						handle: '.cbRepeatRowMove',
						opacity: 0.5
					});
				}

				cbrepeat.addHandler = function( e ) {
					e.preventDefault();
					e.stopPropagation();

					var add = cbrepeat.element.find( '.cbRepeatRowAddCount' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
					});
					var count = 1;

					if ( add.length && ( add.val() > 0 ) ) {
						count = add.val();
					}

					if ( cbrepeat.settings.limit && ( count > cbrepeat.settings.limit ) ) {
						count = cbrepeat.settings.limit;

						if ( add.length ) {
							add.val( count );
						}
					}

					addRow.call( $this, count );
				};

				if ( cbrepeat.settings.add ) {
					cbrepeat.element.find( '.cbRepeatRowAdd' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
					}).on( 'click.cbrepeat', cbrepeat.addHandler );
				}

				cbrepeat.removeHandler = function( e ) {
					e.preventDefault();
					e.stopPropagation();

					var row = $( this ).closest( '.cbRepeatRow' );

					removeRow.call( $this, row );
				};

				if ( cbrepeat.settings.remove ) {
					cbrepeat.element.find( '.cbRepeatRowRemove' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
					}).on( 'click.cbrepeat', cbrepeat.removeHandler );
				}

				updateRepeat( cbrepeat );

				cbrepeat.element.on( 'remove.cbrepeat destroy.cbrepeat', function() {
					cbrepeat.element.cbrepeat( 'destroy' );
				});

				cbrepeat.element.on( 'rebind.cbrepeat', function() {
					cbrepeat.element.cbrepeat( 'rebind' );
				});

				cbrepeat.element.on( 'modified.cbrepeat', function( e, orgId, oldId, newId ) {
					if ( oldId != newId ) {
						cbrepeat.element.cbrepeat( 'rebind' );
					}
				});

				cbrepeat.element.on( 'cloned.cbrepeat', function() {
					$( this ).off( '.cbrepeat' );
					$( this ).removeData( 'cbrepeat' );
					$( this ).removeData( 'uiSortable' );
					$( this ).removeData( 'ui-sortable' );

					var container = $( this ).find( '.cbRepeatRow' ).first().parent();

					$( this ).find( '.cbRepeatRowAdd' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( container );
					}).off( 'click.cbrepeat', cbrepeat.addHandler );

					$( this ).find( '.cbRepeatRowRemove' ).filter( function() {
						return $( this ).closest( '.cbRepeat' ).is( container );
					}).off( 'click.cbrepeat', cbrepeat.removeHandler );

					$( this ).cbrepeat( cbrepeat.options );
				});

				cbrepeat.element.triggerHandler( 'cbrepeat.init.after', [cbrepeat] );

				// Bind the cbrepeat to the element so it's reusable and chainable:
				cbrepeat.element.data( 'cbrepeat', cbrepeat );

				// Add this instance to our instance array so we can keep track of our repeat instances:
				instances.push( cbrepeat );
			});
		},
		add: function( count ) {
			if ( ! count ) {
				count = 1;
			}

			addRow.call( this, count );

			return this;
		},
		remove: function( row ) {
			removeRow.call( this, row );

			return this;
		},
		update: function() {
			var cbrepeat = $( this ).data( 'cbrepeat' );

			if ( ! cbrepeat ) {
				return this;
			}

			updateRepeat( cbrepeat );

			return this;
		},
		reset: function() {
			var cbrepeat = $( this ).data( 'cbrepeat' );

			if ( ! cbrepeat ) {
				return this;
			}

			var row = cbrepeat.element.find( '.cbRepeatRow:not(:first)' ).filter( function() {
				return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
			});

			row.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).off( 'remove removeqtip' );
			row.remove();

			updateRepeat( cbrepeat );

			return this;
		},
		rebind: function() {
			var cbrepeat = $( this ).data( 'cbrepeat' );

			if ( ! cbrepeat ) {
				return this;
			}

			cbrepeat.element.cbrepeat( 'cbrepeat' );
			cbrepeat.element.cbrepeat( cbrepeat.options );

			return this;
		},
		destroy: function() {
			var cbrepeat = $( this ).data( 'cbrepeat' );

			if ( ! cbrepeat ) {
				return this;
			}

			if ( cbrepeat.settings.sortable ) {
				cbrepeat.element.sortable( 'destroy' );
				cbrepeat.element.removeData( 'uiSortable' );
				cbrepeat.element.removeData( 'ui-sortable' );
			}

			cbrepeat.element.find( '.cbRepeatRowAdd' ).filter( function() {
				return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
			}).off( 'click.cbrepeat', cbrepeat.addHandler );

			cbrepeat.element.find( '.cbRepeatRowRemove' ).filter( function() {
				return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
			}).off( 'click.cbrepeat', cbrepeat.removeHandler );

			cbrepeat.element.off( '.cbrepeat' );

			$.each( instances, function( i, instance ) {
				if ( instance.element == cbrepeat.element ) {
					instances.splice( i, 1 );

					return false;
				}

				return true;
			});

			cbrepeat.element.removeData( 'cbrepeat' );
			cbrepeat.element.triggerHandler( 'cbrepeat.destroyed', [cbrepeat] );

			return this;
		},
		instances: function() {
			return instances;
		}
	};

	function addRow( count ) {
		const cbrepeat = $( this ).data( 'cbrepeat' );

		if ( ! cbrepeat ) {
			return false;
		}

		const rows = cbrepeat.element.find( '.cbRepeatRow' ).filter( function() {
			return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
		});

		if ( cbrepeat.settings.max && ( rows.length >= cbrepeat.settings.max ) ) {
			return false;
		}

		const row = rows.last();
		const checked = [];

		row.find( 'input:checked' ).each( function() {
			checked.push( $( this ) );
		});

		const cloning = [];
		const cloned = [];
		const clones = [];
		let last = row;

		for ( let i = 0; i < count; ++i ) {
			let items = row.find( '*' );

			if ( cbrepeat.settings.ignore ) {
				items = items.not( cbrepeat.settings.ignore );
			}

			// Lets notify the elements they are about to be cloned so they can perform any necessary clean up or caching:
			items.each( function() {
				if ( $( this ).triggerHandler( 'cloning' ) ) {
					// Only cache those that notify needing to rebind (cloning should return true for this behavior):
					cloning.push( $( this ) );
				}
			});

			const clone = row.clone( true );

			if ( count > 1 ) {
				// We want the new row to always be last and since we're inserting multiple at once we need to insert after that last entry so lets find it:
				last = cbrepeat.element.find( '.cbRepeatRow' ).filter( function() {
					return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
				}).last()
			}

			clone.insertAfter( last );

			// Reset nested CBRepeat usages to single row (improves parsing performance); this needs be done before we scan every node for value reset:
			let nested = clone.find( '.cbRepeat' );

			if ( cbrepeat.settings.ignore ) {
				nested = nested.not( cbrepeat.settings.ignore );
			}

			nested.each( function() {
				const $this = this;
				const repeat = $( this ).find( '.cbRepeatRow:not(:first)' ).filter( function() {
					return $( this ).closest( '.cbRepeat' ).is( $( $this ) );
				});

				repeat.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).off( 'remove removeqtip' );
				repeat.remove();
			});

			// Reset the values of every node and trigger their cloned event:
			items = clone.find( '*' );

			if ( cbrepeat.settings.ignore ) {
				items = items.not( cbrepeat.settings.ignore );
			}

			items.each( function() {
				if ( $( this ).is( 'input,select,textarea' ) && ( ! $( this ).hasClass( 'cbRepeatNoReset' ) ) && ( ! $( this ).closest( '.cbRepeatNoReset' ).length ) ) {
					const type = $( this ).attr( 'type' );
					let defaultValue = $( this ).data( 'cbrepeat-default' );

					if ( typeof defaultValue != 'undefined' ) {
						defaultValue = $.trim( defaultValue );
					} else {
						defaultValue = null;
					}

					if ( ( type === 'checkbox' ) || ( type === 'radio' ) ) {
						if ( defaultValue ) {
							if ( type === 'checkbox' && ( defaultValue.indexOf( '|*|' ) !== -1 ) ) {
								if ( defaultValue.split( '|*|' ).indexOf( $( this ).val() ) !== -1 ) {
									$( this ).prop( 'checked', true );
								} else {
									$( this ).prop( 'checked', false );
								}
							} else {
								if ( $( this ).val() === defaultValue ) {
									$( this ).prop( 'checked', true );
								} else {
									$( this ).prop( 'checked', false );
								}
							}
						} else {
							if ( ( type === 'radio' ) && ( ( $( this ).siblings( 'input[type="radio"]' ).length + 1 ) === 2 ) && ( $( this ).val() === 0 ) ) {
								$( this ).prop( 'checked', true );
							} else {
								$( this ).prop( 'checked', false );
							}
						}

						// Workaround fixes for Joomla conflicting with our yesno usage:
						if ( ( type === 'radio' )
							&& ( ( $( this ).siblings( 'input[type="radio"]' ).length + 1 ) === 2 )
							&& $( this ).closest( '.cbRadioButtonsYesNo' ).length
						) {
							if ( $( this ).val() === 0 ) {
								$( this ).next( 'label' ).addClass( 'btn-danger' );
							} else {
								$( this ).next( 'label' ).addClass( 'btn-success' );
							}
						}

						if ( type === 'radio' ) {
							$( this ).next( 'label' ).removeClass( 'active' );
						}
					} else {
						if ( defaultValue ) {
							if ( $( this ).is( 'select[multiple]' ) ) {
								defaultValue = defaultValue.split( '|*|' );
							}

							$( this ).val( defaultValue );
						} else {
							$( this ).val( '' );

							if ( $( this ).is( 'select' ) ) {
								if ( ! $( this ).children( 'option[value=""]:first' ).length ) {
									$( this ).val( $( this ).children( 'option[value!=""]:first' ).val() );
								}
							}
						}
					}
				}

				cloned.push( $( this ) );
			});

			clones.push( clone );

			updateRow.call( clone, cbrepeat );
		}

		updateRepeat( cbrepeat );

		$.each( checked, function( checkedIndex, checkedElement ) {
			checkedElement.prop( 'checked', true );
		});

		// Allow the original elements that are being cloned to rebind if they had to destroy before cloning:
		$.each( cloning, function( cloningIndex, cloningElement ) {
			cloningElement.triggerHandler( 'rebind' );
		});

		// We want the cloned event to be after id, name, and for attributes have been updated:
		$.each( cloned, function( clonedIndex, clonedElement ) {
			clonedElement.triggerHandler( 'cloned' );
		});

		cbrepeat.element.triggerHandler( 'cbrepeat.add', [cbrepeat, row, count, clones, cloning, cloned] );

		return true;
	}

	function findIndex( cbrepeat ) {
		const indexes = [];

		const rows = cbrepeat.element.find( '.cbRepeatRow' ).filter( function() {
			return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
		})

		let index = ( rows.length - 1 );

		rows.each( function() {
			const row = $( this );
			const dataIndex = row.data( 'cbrepeat-index' );

			if ( dataIndex ) {
				indexes.push( dataIndex );
				return false;
			}

			row.find( '.cbRepeatRowIndex,*[id],*[for],*[name],*[data-cbrepeat-fallback-id],*[data-cbrepeat-fallback-for],*[data-cbrepeat-fallback-name]' ).filter( function() {
				return $( this ).closest( '.cbRepeatRow' ).is( row );
			}).each( function() {
				const item = $( this );

				if ( item.hasClass( 'cbRepeatRowIndex' ) ) {
					let indexValue;

					if ( item.is( 'input' ) ) {
						indexValue = parseInt( item.val() );
					} else {
						indexValue = parseInt( item.text() );
					}

					if ( indexValue >= 0 ) {
						indexes.push( indexValue );
						return false;
					}
				}

				if ( item.attr( 'id' ) || item.attr( 'data-cbrepeat-fallback-id' ) ) {
					let idAttribute = 'id';

					if ( ! item.attr( 'id' ) ) {
						idAttribute = 'data-cbrepeat-fallback-id';
					}

					const idValue = item.attr( idAttribute );

					if ( /^(.*_{2,})(\d+)(_{2,}\w+)$/g.test( idValue ) ) {
						const idIndex = parseInt( idValue.replace( /^(.*_{2,})(\d+)(_{2,}\w+)$/g, '$2' ) );

						if ( idIndex >= 0 ) {
							indexes.push( idIndex );
							return false;
						}
					}
				}

				if ( item.attr( 'for' ) || item.attr( 'data-cbrepeat-fallback-for' ) ) {
					let forAttribute = 'for';

					if ( ! item.attr( 'for' ) ) {
						forAttribute = 'data-cbrepeat-fallback-for';
					}

					const forValue = item.attr( forAttribute );

					if ( /^(.*_{2,})(\d+)(_{2,}\w+)$/g.test( forValue ) ) {
						const forIndex = parseInt( forValue.replace( /^(.*_{2,})(\d+)(_{2,}\w+)$/g, '$2' ) );

						if ( forIndex >= 0 ) {
							indexes.push( forIndex );
							return false;
						}
					}
				}

				if ( item.attr( 'name' ) || item.attr( 'data-cbrepeat-fallback-name' ) ) {
					let nameAttribute = 'name';

					if ( ! item.attr( 'name' ) ) {
						nameAttribute = 'data-cbrepeat-fallback-name';
					}

					const nameValue = item.attr( nameAttribute );

					if ( /^(.*_{2,})(\d+)(_{2,}\w+)$/g.test( nameValue ) ) {
						const nameIndex = parseInt( nameValue.replace( /^(.*_{2,})(\d+)(_{2,}\w+)$/g, '$2' ) );

						if ( nameIndex >= 0 ) {
							indexes.push( nameIndex );
							return false;
						}
					}
				}
			});
		});

		if ( indexes.length ) {
			index = Math.max( ...indexes );
		}

		return index;
	}

	function removeRow( row ) {
		var cbrepeat = $( this ).data( 'cbrepeat' );

		if ( ! cbrepeat ) {
			return false;
		}

		var rows = cbrepeat.element.find( '.cbRepeatRow' ).filter( function() {
			return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
		});

		if ( cbrepeat.settings.min && ( rows.length <= cbrepeat.settings.min ) ) {
			return false;
		}

		if ( ! row ) {
			row = rows.last();
		}

		row.find( '.cbTabs,.cbTooltip,[data-hascbtooltip=\"true\"]' ).off( 'remove removeqtip' );
		row.find( '.cbRepeat' ).off( 'remove.cbrepeat' );

		row.remove();

		updateRepeat( cbrepeat );

		cbrepeat.element.triggerHandler( 'cbrepeat.remove', [cbrepeat, row] );

		return true;
	}

	function updateRow( cbrepeat ) {
		cbrepeat.rowIndex = ( cbrepeat.rowIndex + 1 );

		const row = $( this );
		const newConditions = {};

		let items = $( this ).find( '*[id],*[for],*[name],*[data-cbrepeat-fallback-id],*[data-cbrepeat-fallback-for],*[data-cbrepeat-fallback-name]' ).filter( function() {
			return $( this ).closest( '.cbRepeatRow' ).is( row );
		});

		if ( cbrepeat.settings.ignore ) {
			items = items.not( cbrepeat.settings.ignore );
		}

		const counter = $( this ).find( '.cbRepeatRowIndex' ).filter( function() {
			return $( this ).closest( '.cbRepeatRow' ).is( row );
		});

		if ( counter.length ) {
			counter.each( function() {
				if ( $( this ).is( 'input' ) ) {
					$( this ).val( cbrepeat.rowIndex );
				} else {
					$( this ).html( cbrepeat.rowIndex );
				}
			});
		}

		items.each( function() {
			if ( $( this ).attr( 'id' ) || $( this ).attr( 'data-cbrepeat-fallback-id' ) ) {
				let idAttribute = 'id';

				if ( ! $( this ).attr( 'id' ) ) {
					idAttribute = 'data-cbrepeat-fallback-id';
				}

				const oldId = $( this ).attr( idAttribute );
				const newId = oldId.replace( /^(.*_{2,})(\d+)(_{2,}\w+)$/g, '$1' + cbrepeat.rowIndex + '$3' );
				const oldIdNormalized = oldId.replace( 'cbfr_', '' ).replace( 'cbfv_', '' ).replace( /^(.*_{2,}\d+)_{2,}\w+/g, '$1' );
				const newIdNormalized = newId.replace( 'cbfr_', '' ).replace( 'cbfv_', '' ).replace( /^(.*_{2,}\d+)_{2,}\w+/g, '$1' );

				if ( oldIdNormalized !== newIdNormalized ) {
					if ( ! $( this ).data( 'orgId' ) ) {
						$( this ).data( 'orgId', oldId );
					}

					$( this ).attr( idAttribute, newId );
					$( this ).triggerHandler( 'modified', [ oldId, newId, cbrepeat.rowIndex ] );

					let idItems = $( this ).closest( '.cbRepeatRow' ).find( '*[id*="' + oldIdNormalized + '"],*[id*="' + oldIdNormalized.replace( /_{2,}/g, '__' ) + '"],*[data-cbrepeat-fallback-id*="' + oldIdNormalized + '"],*[id*="' + oldIdNormalized.replace( /_{2,}/g, '__' ) + '"]' );

					if ( cbrepeat.settings.ignore ) {
						idItems = idItems.not( cbrepeat.settings.ignore );
					}

					idItems.each( function() {
						if ( $( this ).attr( 'data-cbrepeat-fallback-id' ) ) {
							const itemOldFallbackId = $( this ).attr( 'data-cbrepeat-fallback-id' );
							const itemNewFallbackId = itemOldFallbackId.replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) );

							if ( itemOldFallbackId !== itemNewFallbackId ) {
								$( this ).attr( 'data-cbrepeat-fallback-id', itemNewFallbackId );
							}
						}

						if ( $( this ).attr( 'id' ) ) {
							const itemOldId = $( this ).attr( 'id' );
							const itemNewId = itemOldId.replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) );

							if ( itemOldId === itemNewId )  {
								return;
							}

							if ( ! $( this ).data( 'orgId' ) ) {
								$( this ).data( 'orgId', itemOldId );
							}

							$( this ).attr( 'id', itemNewId );

							copyConditions.call( newConditions, itemOldId, itemNewId, oldIdNormalized, newIdNormalized );

							$( this ).triggerHandler( 'modified', [ itemOldId, itemNewId, cbrepeat.rowIndex ] );
						}
					});

					copyConditions.call( newConditions, oldId, newId, oldIdNormalized, newIdNormalized );
				}
			}

			if ( $( this ).attr( 'for' ) || $( this ).attr( 'data-cbrepeat-fallback-for' ) ) {
				let forAttribute = 'for';

				if ( ! $( this ).attr( 'for' ) ) {
					forAttribute = 'data-cbrepeat-fallback-for';
				}

				const oldFor = $( this ).attr( forAttribute );
				const newFor = oldFor.replace( /^(.*)(\[\d+])(\[\w+])$/g, '$1[' + cbrepeat.rowIndex + ']$3' ).replace( /^(.*_{2,})(\d+)(_{2,}\w+)$/g, '$1' + cbrepeat.rowIndex + '$3' );
				const oldForNormalized = oldFor.replace( /\[[a-zA-Z0-9]+]$/g, '' ).replace( /^(.*_{2,}\d+)_{2,}\w+/g, '$1' );
				const newForNormalized = newFor.replace( /\[[a-zA-Z0-9]+]$/g, '' ).replace( /^(.*_{2,}\d+)_{2,}\w+/g, '$1' );

				if ( oldForNormalized !== newForNormalized ) {
					if ( ! $( this ).data( 'orgFor' ) ) {
						$( this ).data( 'orgFor', oldFor );
					}

					$( this ).attr( forAttribute, newFor );
					$( this ).triggerHandler( 'modified-for', [ oldFor, newFor, cbrepeat.rowIndex ] );

					let forItems = $( this ).closest( '.cbRepeatRow' ).find( '*[for*="' + oldForNormalized + '"],*[data-cbrepeat-fallback-for*="' + oldForNormalized + '"]' );

					if ( cbrepeat.settings.ignore ) {
						forItems = forItems.not( cbrepeat.settings.ignore );
					}

					forItems.each( function() {
						if ( $( this ).attr( 'data-cbrepeat-fallback-for' ) ) {
							const itemOldFallbackFor = $( this ).attr( 'data-cbrepeat-fallback-for' );
							const itemNewFallbackFor = itemOldFallbackFor.replace( oldForNormalized, newForNormalized );

							if ( itemOldFallbackFor !== itemNewFallbackFor ) {
								$( this ).attr( 'data-cbrepeat-fallback-for', itemNewFallbackFor );
							}
						}

						if ( $( this ).attr( 'for' ) ) {
							const itemOldFor = $( this ).attr( 'for' );

							if ( ! $( this ).data( 'orgFor' ) ) {
								$( this ).data( 'orgFor', $( this ).attr( 'for' ) );
							}

							$( this ).attr( 'for', $( this ).attr( 'for' ).replace( oldForNormalized, newForNormalized ) );

							const itemNewFor = $( this ).attr( 'for' );

							if ( itemOldFor === itemNewFor )  {
								return;
							}

							$( this ).triggerHandler( 'modified-for', [ itemOldFor, itemNewFor, cbrepeat.rowIndex ] );
						}
					});
				}
			}

			if ( $( this ).attr( 'name' ) || $( this ).attr( 'data-cbrepeat-fallback-name' ) ) {
				let nameAttribute = 'name';

				if ( ! $( this ).attr( 'name' ) ) {
					nameAttribute = 'data-cbrepeat-fallback-name';
				}

				const oldName = $( this ).attr( nameAttribute );
				const newName = oldName.replace( /^(.*)(\[\d+])(\[\w+])$/g, '$1[' + cbrepeat.rowIndex + ']$3' ).replace( /^(.*_{2,})(\d+)(_{2,}\w+)$/g, '$1' + cbrepeat.rowIndex + '$3' );
				const oldNameNormalized = oldName.replace( /\[[a-zA-Z0-9]+]$/g, '' ).replace( /^(.*_{2,}\d+)_{2,}\w+/g, '$1' );
				const newNameNormalized = newName.replace( /\[[a-zA-Z0-9]+]$/g, '' ).replace( /^(.*_{2,}\d+)_{2,}\w+/g, '$1' );

				if ( oldNameNormalized !== newNameNormalized ) {
					if ( ! $( this ).data( 'orgName' ) ) {
						$( this ).data( 'orgName', $( this ).attr( nameAttribute ) );
					}

					$( this ).attr( nameAttribute, newName );
					$( this ).triggerHandler( 'modified-name', [ oldName, newName, cbrepeat.rowIndex ] );

					let nameItems = $( this ).closest( '.cbRepeatRow' ).find( '*[name*="' + oldNameNormalized + '"],*[data-cbrepeat-fallback-name*="' + oldNameNormalized + '"]' );

					if ( cbrepeat.settings.ignore ) {
						nameItems = nameItems.not( cbrepeat.settings.ignore );
					}

					nameItems.each( function() {
						if ( $( this ).attr( 'data-cbrepeat-fallback-name' ) ) {
							const itemOldFallbackName = $( this ).attr( 'data-cbrepeat-fallback-name' );
							const itemNewFallbackName = itemOldFallbackName.replace( oldNameNormalized, newNameNormalized );

							if ( itemOldFallbackName !== itemNewFallbackName ) {
								$( this ).attr( 'data-cbrepeat-fallback-name', itemNewFallbackName );
							}
						}

						if ( $( this ).attr( 'name' ) ) {
							const itemOldName = $( this ).attr( 'name' );
							const itemNewName = $( this ).attr( 'name' ).replace( oldNameNormalized, newNameNormalized );

							if ( itemOldName === itemNewName )  {
								return;
							}

							if ( ! $( this ).data( 'orgName' ) ) {
								$( this ).data( 'orgName', itemOldName );
							}

							$( this ).attr( 'name', itemNewName );
							$( this ).triggerHandler( 'modified-name', [ itemOldName, itemNewName, cbrepeat.rowIndex ] );
						}
					});
				}
			}
		});

		if ( newConditions && ( typeof cbHideFields !== 'undefined' ) ) {
			Object.assign( cbHideFields, newConditions );

			cbInitFields( newConditions );
		}

		cbrepeat.element.triggerHandler( 'cbrepeat.updated', [cbrepeat, row] );
	}

	function updateRepeat( cbrepeat ) {
		const rowCount = cbrepeat.element.find( '.cbRepeatRow' ).filter( function() {
			return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
		}).length;

		if ( rowCount > cbrepeat.settings.min ) {
			cbrepeat.element.find( '.cbRepeatRowRemove' ).filter( function() {
				return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
			}).each( function() {
				$( this ).removeClass( 'hidden' );

				const btnContainer = $( this ).closest( '.cbRepeatRowIncrement' );

				if ( btnContainer.length ) {
					btnContainer.removeClass( 'hidden' );
				}
			});

			if ( cbrepeat.settings.sortable ) {
				cbrepeat.element.sortable( 'enable' );
			}
		} else {
			cbrepeat.element.find( '.cbRepeatRowRemove' ).filter( function() {
				return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
			}).each( function() {
				$( this ).addClass( 'hidden' );

				const btnContainer = $( this ).closest( '.cbRepeatRowIncrement' );

				if ( btnContainer.length ) {
					btnContainer.addClass( 'hidden' );
				}
			});

			if ( cbrepeat.settings.sortable ) {
				cbrepeat.element.sortable( 'disable' );
			}
		}

		if ( cbrepeat.settings.max ) {
			if ( rowCount >= cbrepeat.settings.max ) {
				cbrepeat.element.find( '.cbRepeatRowAdd' ).filter( function() {
					return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
				}).each( function() {
					$( this ).addClass( 'hidden' );

					const btnContainer = $( this ).closest( '.cbRepeatRowIncrement' );

					if ( btnContainer.length ) {
						btnContainer.addClass( 'hidden' );
					}
				});
			} else {
				cbrepeat.element.find( '.cbRepeatRowAdd' ).filter( function() {
					return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
				}).each( function() {
					$( this ).removeClass( 'hidden' );

					const btnContainer = $( this ).closest( '.cbRepeatRowIncrement' );

					if ( btnContainer.length ) {
						btnContainer.removeClass( 'hidden' );
					}
				});
			}
		}

		if ( ( ! cbrepeat.settings.sortable ) || ( rowCount <= 1 ) ) {
			cbrepeat.element.find( '.cbRepeatRowSort' ).filter( function() {
				return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
			}).addClass( 'hidden' );
		} else if ( rowCount > 1 ) {
			cbrepeat.element.find( '.cbRepeatRowSort' ).filter( function() {
				return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
			}).removeClass( 'hidden' );
		}

		cbrepeat.element.find( '.cbRepeatCount' ).filter( function() {
			return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
		}).each( function() {
			if ( $( this ).is( 'input' ) ) {
				$( this ).val( rowCount );
			} else {
				$( this ).html( rowCount );
			}
		});

		cbrepeat.element.find( '.cbRepeatRowNumber' ).filter( function() {
			return $( this ).closest( '.cbRepeat' ).is( cbrepeat.element );
		}).each( function( i ) {
			if ( $( this ).is( 'input' ) ) {
				$( this ).val( ( i + 1 ) );
			} else {
				$( this ).html( ( i + 1 ) );
			}
		});

		if ( rowCount === cbrepeat.settings.min ) {
			cbrepeat.element.addClass( 'cbRepeatMin' );
		} else if ( rowCount === cbrepeat.settings.max ) {
			cbrepeat.element.addClass( 'cbRepeatMax' );
		} else {
			cbrepeat.element.removeClass( 'cbRepeatMin cbRepeatMax' );
		}
	}

	function copyConditions( oldId, newId, oldIdNormalized, newIdNormalized ) {
		if ( ( typeof cbHideFields === 'undefined' ) || ( typeof cbHideFields[oldId] === 'undefined' ) ) {
			return;
		}

		this[newId] = {};
		this[newId].element = cbHideFields[oldId].element.replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) );
		this[newId].conditions = [];

		cbHideFields[oldId].conditions.forEach( ( condition, i ) => {
			this[newId].conditions[i] = { operator: condition.operator, value: condition.value, show: [], set: [] };

			condition.show.forEach( ( field, s ) => {
				if ( field ) {
					this[newId].conditions[i].show[s] = field.replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) );
				}
			});

			condition.set.forEach( ( field, s ) => {
				if ( field ) {
					this[newId].conditions[i].set[s] = field.replace( oldIdNormalized, newIdNormalized ).replace( oldIdNormalized.replace( /_{2,}/g, '__' ), newIdNormalized.replace( /_{2,}/g, '__' ) );
				}
			});
		});
	}

	$.fn.cbrepeat = function( options ) {
		if ( methods[options] ) {
			return methods[ options ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( ( typeof options === 'object' ) || ( ! options ) ) {
			return methods.init.apply( this, arguments );
		}

		return this;
	};

	$.fn.cbrepeat.dataMap = {
		sortable: 'cbrepeat-sortable',
		ignore: 'cbrepeat-ignore',
		add: 'cbrepeat-add',
		remove: 'cbrepeat-remove',
		min: 'cbrepeat-min',
		max: 'cbrepeat-max',
		limit: 'cbrepeat-limit'
	};

	$.fn.cbrepeat.defaults = {
		init: true,
		useData: true,
		sortable: true,
		ignore: null,
		add: true,
		remove: true,
		min: 1,
		max: 0,
		limit: 25
	};
})(jQuery);