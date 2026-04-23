function cbsaveorder( cb, n, fldName, task, subtaskName, subtaskValue ) {
    cbCheckAllRowsAndSubTask( cb, n, fldName, subtaskName, subtaskValue );
    cbsubmitform( task );
}
//needed by cbsaveorder function
function cbCheckAllRowsAndSubTask( cb, n, fldName, subtaskName, subtaskValue ) {
    if (!fldName) {
        fldName = 'cb';
    }
    f = cbParentForm( cb );
    for ( var i = 0; i < n; i++ ) {
             box = f.elements[fldName+i];
             if ( box.checked == false ) {
                     box.checked = true;
             }
    }
	if (subtaskName && subtaskValue) {
		f.elements[subtaskName].value = subtaskValue;
	}
}
/**
* Toggles the check state of a group of boxes
*
* Checkboxes must have an id attribute in the form cb0, cb1...
* @param  tgl      The id of the toggle button
* @param  n        The number of box to 'check'
* @param  fldName  An alternative field name id prefix
*/
function cbToggleAll( tgl, n, fldName ) {
	if ( ! fldName ) {
		fldName = 'cb';
	}

	var frm = tgl.form;
	var checked = 0;

	for ( i = 0; i < n; i++ ) {
		cb = eval( 'frm.' + fldName + i );

		if ( cb ) {
			cb.checked = tgl.checked;

			if ( tgl.checked == true ) {
				checked++;
			}
		}
	}

	if ( typeof( frm.boxchecked ) != 'undefined' ) {
		frm.boxchecked.value = checked;
	}

	return true;
}

function cbParentForm(cb) {
	var f;
	if ( cb == window  ) {
		f = window.event.srcElement;	// IE
	} else {
		f = cb;
	}
	while (f) {
		f = f.parentNode;
		if (f.nodeName == 'FORM') {
			break;
		}
	}
	return f;
}
/**
* Performs task/subtask on table row id
*/
function cbIsChecked(isitchecked) {
	if (isitchecked == true) {
		document.adminForm.boxchecked.value++;
	} else {
		document.adminForm.boxchecked.value--;
	}
}
/**
* Performs task/subtask on table row id
*/
function cbListItemTask( cb, task, subtaskName, subtaskValue, fldName, id ) {
    var f = cbParentForm(cb);
    if (cb && f) {
    	var cbRegexp = new RegExp('^'+fldName+'[0-9]+$');

    	for (i = 0; i < f.elements.length; i++) {
            cbx = f.elements[i];
            if ( ( cbx.type != 'checkbox' ) || ( ! cbRegexp.test(cbx.id) ) ) {
            	continue;
            }
            if ( cbx.id == fldName+id ) {
	            cbx.checked = true;
            } else {
	            cbx.checked = false;
        	}
        }
		if (subtaskName && subtaskValue) {
			f.elements[subtaskName].value = subtaskValue;
		}
        submitbutton(task);
    }
    return false;
}
/**
* Performs task/subtask on selected table rows
*/
function cbDoListTask( cb, task, subtaskName, subtaskValue, fldName ) {
    var f = document.forms['adminForm'];
    if (cb) {
    	var oneChecked = false;
        for (i = 0; true; i++) {
            cbx = f.elements[fldName+i];
            if ( ! cbx ) {
            	break;
            }
            if ( cbx.checked ) {
	            oneChecked = true;
	            break;
        	}
        }
        if ( oneChecked ) {
			if (subtaskName && subtaskValue) {
				if ( subtaskValue == 'deleterows' ) {
					if ( ! confirm('Are you sure you want to delete selected items ?') ) { 
						return false;
					}
				}
				f.elements[subtaskName].value = subtaskValue;
			}
    	    submitbutton(task);
        } else {
        	alert( "no items selected" );
        }
    }
    return false;
}
/**
* Performs task/subtask
*/
function cbDoSubTask( cb, task, subtaskName, subtaskValue ) {
	var f = document.forms['adminForm'];

	if ( cb ) {
		if (subtaskName && subtaskValue) {
			f.elements[subtaskName].value = subtaskValue;
		}

		submitbutton(task);
	}

	return false;
}

function cbhideMainMenu() {
	if ( document.adminForm.hidemainmenu ) {
		document.adminForm.hidemainmenu.value	=	1;
	}
}

function submitbutton(pressbutton) {
	cbsubmitform(pressbutton);
	return false;
}

/**
* Submit the admin form
*/
function cbsubmitform(pressbutton){
	if (pressbutton.indexOf('=') == -1) {
		if ( document.forms['adminForm'].elements['task'] ) {
			document.forms['adminForm'].elements['task'].value = pressbutton;
		} else if ( document.forms['adminForm'].elements['view'] ) {
			document.forms['adminForm'].elements['view'].value = pressbutton;
		}
	} else {
		var formchanges = pressbutton.split('&');
		for (var i = 0; i < formchanges.length; i++) {
			var nv = formchanges[i].split('=');
			if ( ( typeof( document.forms['adminForm'].elements[nv[0]] ) == 'undefined' ) && ( typeof( cbjQuery ) != 'undefined' ) ) {
				cbjQuery('<input type="hidden" />').attr('name', nv[0]).attr('value', nv[1]).appendTo(cbjQuery(document.forms['adminForm']));
			}
			document.forms['adminForm'].elements[nv[0]].value = nv[1];
		}
	}
	if ( typeof( cbjQuery ) != 'undefined' ) {
		cbjQuery( document.forms['adminForm'] ).submit();
	} else {
		if ( typeof(document.forms['adminForm']) != 'undefined' ) {
			try {
				document.forms['adminForm'].onsubmit();
				}
			catch(e){}
		}
		document.forms['adminForm'].submit();
	}
}

/**
* general cb DOM events handler
*/

function cbAddEvent(obj, evType, fn){
	if (obj.addEventListener){
		obj.addEventListener(evType, fn, true);
		return true;
	} else if (obj.attachEvent){
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

/**
* CB hide and set fields depending on other fields:
*/

const cbHideFields = {};
const cbHideFieldsMap = {};

/**
 * Conditions a field collecting the show/hide/set
 * Returns true if an evaluate is triggering a form submit otherwise false
 *
 * @param {Element} field
 * @param {HTMLInputElement|HTMLSelectElement} input
 * @param {array} conditions
 * @param {array} show
 * @param {array} hide
 * @param {object} set
 * @param {string} state
 * @param {object} cache
 * @returns {boolean}
 */
function cbParamCondition( field, input, conditions, show = [], hide = [], set = {}, state = null, cache = {} ) {
	let inputValue;

	if ( ( input.type === 'radio' ) || ( input.type === 'checkbox' ) ) {
		const checked = [];

		field.querySelectorAll( 'input[type="' + input.type + '"]' ).forEach( ( checkbox ) => {
			if ( checkbox.checked ) {
				checked.push( checkbox.value );
			}
		});

		inputValue = checked.join( '|*|' );
	} else if ( input.type === 'select-multiple' ) {
		const selected = [];

		for ( const option of input.options ) {
			if ( option.selected ) {
				selected.push( option.value );
			}
		}

		inputValue = selected.join( '|*|' );
	} else {
		inputValue = input.value;
	}

	let submit = false;

	/**
	 * @property {object} condition
	 * @property {string} condition.operator
	 * @property {?string} condition.value
	 * @property {array} condition.show
	 * @property {array} condition.hide
	 * @property {array} condition.set
	 */
	for ( const condition of conditions ) {
		const operator = condition.operator;
		const value = condition.value;

		let match = false;

		switch ( operator ) {
			case '=':
			case '==':
				// still allow loose comparisons as there's too many B/C issues with forcing strict comparisons
				// noinspection EqualityComparisonWithCoercionJS
				match = ( inputValue == value );
				break;
			case '===':
				match = ( inputValue === value );
				break;
			case '<>':
			case '!=':
				// still allow loose comparisons as there's too many B/C issues with forcing strict comparisons
				// noinspection EqualityComparisonWithCoercionJS
				match = ( inputValue != value );
				break;
			case '!==':
				match = ( inputValue !== value );
				break;
			case '>=':
				match = ( inputValue >= value );
				break;
			case '<=':
				match = ( inputValue <= value );
				break;
			case '>':
				match = ( inputValue > value );
				break;
			case '<':
				match = ( inputValue < value );
				break;
			case 'empty':
				match = ( ! inputValue.length );
				break;
			case '!empty':
				match = inputValue.length;
				break;
			case 'contains':
				if ( inputValue === '' ) {
					match = ( inputValue === value );
				} else if ( inputValue.includes( '|*|' ) ) {
					match = inputValue.split( '|*|' ).includes( value );
				} else {
					match = inputValue.includes( value );
				}
				break;
			case '!contains':
				if ( inputValue === '' ) {
					match = ( inputValue !== value );
				} else if ( inputValue.includes( '|*|' ) ) {
					match = ( ! inputValue.split( '|*|' ).includes( value ) );
				} else {
					match = ( ! inputValue.includes( value ) );
				}
				break;
			case 'in':
				if ( value === '' ) {
					match = ( value !== inputValue );
				} else if ( value.includes( '|*|' ) ) {
					match = value.split( '|*|' ).includes( inputValue );
				} else {
					match = value.includes( inputValue );
				}
				break;
			case '!in':
				if ( value === '' ) {
					match = ( value !== inputValue );
				} else if ( value.includes( '|*|' ) ) {
					match = ( ! value.split( '|*|' ).includes( inputValue ) );
				} else {
					match = ( ! value.includes( inputValue ) );
				}
				break;
			case 'regexp':
				if ( value === '' ) {
					match = ( value === inputValue );
				} else {
					try {
						match = new RegExp( value ).test( inputValue );
					} catch( e ) {}
				}
				break;
			case '!regexp':
				if ( value === '' ) {
					match = ( value !== inputValue );
				} else {
					try {
						match = ( ! new RegExp( value ).test( inputValue ) );
					} catch( e ) {}
				}
				break;
			case 'evaluate':
				if ( ( 'cbPreviousValue' in input ) && ( input.cbPreviousValue !== inputValue ) ) {
					submit = true;
				}

				input.cbPreviousValue = inputValue;
				break;
			default:
				console.warn( 'xml if:showhide unknown ' + operator + ' operator.' );
				break;
		}

		if ( match ) {
			conditionLoop:
			for ( const conditionFieldId of condition.show ) {
				// Do not show something that is already hidden
				if ( hide.includes( conditionFieldId ) ) {
					continue;
				}

				// Utilize this conditions caching to check for child or related conditions
				if ( state !== 'init' ) {
					if ( ( state !== 'or' ) && ( conditionFieldId in cbHideFields ) ) {
						// Check if the field being shown also has any conditions and collect their show/hide/set as well
						if ( ! ( conditionFieldId in cache ) ) {
							const conditionField = document.getElementById( conditionFieldId );

							if ( ! conditionField ) {
								continue;
							}

							const conditionInput = document.getElementById( cbHideFields[conditionFieldId].element );

							if ( conditionInput !== null ) {
								cbParamCondition( conditionField, conditionInput, cbHideFields[conditionFieldId].conditions, show, hide, set, 'nested', cache );
							} else {
								cbParamInputs( conditionField ).forEach( ( conditionChildInput ) => {
									cbParamCondition( conditionField, conditionChildInput, cbHideFields[conditionFieldId].conditions, show, hide, set, 'nested', cache );
								});
							}

							cache[conditionFieldId] = { show: show, hide: hide, set: set };
						}

						if ( cache[conditionFieldId].hide.includes( conditionFieldId ) ) {
							continue;
						}
					}

					if ( ( state === 'change' ) && ( conditionFieldId in cbHideFieldsMap ) && ( cbHideFieldsMap[conditionFieldId].length > 1 ) ) {
						// Check if this field has been conditioned multiple times and see if one of those other conditions is hiding it (multiple XML conditions are always AND cases)
						for ( const otherConditionFieldId of cbHideFieldsMap[conditionFieldId] ) {
							if ( ( otherConditionFieldId === field.id ) || ( ! ( otherConditionFieldId in cbHideFields ) ) ) {
								continue;
							}

							if ( ! ( otherConditionFieldId in cache ) ) {
								const otherConditionField = document.getElementById( otherConditionFieldId );

								if ( ! otherConditionField ) {
									continue;
								}

								const otherConditionInput = document.getElementById( cbHideFields[otherConditionFieldId].element );
								const otherShow = [];
								const otherHide = [];
								const otherSet = [];

								if ( otherConditionInput !== null ) {
									cbParamCondition( otherConditionField, otherConditionInput, cbHideFields[otherConditionFieldId].conditions, otherShow, otherHide, otherSet, 'or', cache );
								} else {
									cbParamInputs( otherConditionField ).forEach( ( otherConditionChildInput ) => {
										cbParamCondition( otherConditionField, otherConditionChildInput, cbHideFields[otherConditionFieldId].conditions, otherShow, otherHide, otherSet, 'or', cache );
									});
								}

								cache[otherConditionFieldId] = { show: otherShow, hide: otherHide, set: otherSet };
							}

							if ( cache[otherConditionFieldId].hide.includes( conditionFieldId ) ) {
								continue conditionLoop;
							}
						}
					}
				}

				if ( show.includes( conditionFieldId ) ) {
					continue;
				}

				show.push( conditionFieldId );
			}
		} else {
			for ( const conditionFieldId of condition.show ) {
				if ( hide.includes( conditionFieldId ) ) {
					continue;
				}

				hide.push( conditionFieldId );
			}

			for ( const conditionFieldSet of condition.set ) {
				set[conditionFieldSet.element] = conditionFieldSet.value;
			}
		}
	}

	return submit;
}

/**
 * Finalizes a fields conditioned state
 *
 * @param {array} show
 * @param {array} hide
 * @param {object} set
 */
function cbParamShowHide( show = [], hide = [], set = {} ) {
	for ( const conditionFieldId of show ) {
		if ( hide.includes( conditionFieldId ) ) {
			continue;
		}

		const conditionField = document.getElementById( conditionFieldId );

		if ( ! conditionField ) {
			continue;
		}

		if ( ( conditionField.type === 'radio' ) || ( conditionField.type === 'checkbox' ) ) {
			conditionField.parentElement.classList.remove( 'hidden' );
		} else {
			conditionField.classList.remove( 'hidden' );

			if ( conditionField.tagName.toLowerCase() === 'option' ) {
				conditionField.disabled = false;
			}
		}

		conditionField.classList.remove( 'cbDisplayDisabled' );

		conditionField.querySelectorAll( 'input,select,textarea' ).forEach( ( conditionFieldChild ) => {
			conditionFieldChild.classList.remove( 'cbValidationDisabled' );

			if ( conditionFieldChild.classList.contains( 'cbInputDisabled' ) ) {
				conditionFieldChild.classList.remove( 'cbInputDisabled' );
				conditionFieldChild.disabled = false;
			}
		});
	}

	for ( const conditionFieldId of hide ) {
		const conditionField = document.getElementById( conditionFieldId );

		if ( ! conditionField ) {
			continue;
		}

		if ( ( conditionField.type === 'radio' ) || ( conditionField.type === 'checkbox' ) ) {
			conditionField.parentElement.classList.add( 'hidden' );

			conditionField.checked = false;
		} else {
			conditionField.classList.add( 'hidden' );

			if ( conditionField.tagName.toLowerCase() === 'option' ) {
				conditionField.selected = false;
				conditionField.disabled = true;
			}
		}

		conditionField.classList.add( 'cbDisplayDisabled' );

		conditionField.querySelectorAll( 'input,select,textarea' ).forEach( ( conditionFieldChild ) => {
			conditionFieldChild.classList.add( 'cbValidationDisabled' );

			if ( ! Object.keys( set ).length ) {
				if ( ! conditionFieldChild.disabled ) {
					conditionFieldChild.classList.add( 'cbInputDisabled' );
					conditionFieldChild.disabled = true;
				}

				return;
			}

			let childId = conditionFieldChild.id;

			// Handle CBs input IDs for checkbox and multi-select which converts [ and ] to __
			if ( ( conditionFieldChild.type === 'radio' ) || ( conditionFieldChild.type === 'checkbox' ) ) {
				childId = childId.replace( /_+cbf[0-9]+$/g, '' );
			} else if ( conditionFieldChild.type === 'select-multiple' ) {
				childId = childId.replace( /_+$/g, '' );
			}

			if ( ! ( childId in set ) ) {
				if ( ! conditionFieldChild.disabled ) {
					conditionFieldChild.classList.add( 'cbInputDisabled' );
					conditionFieldChild.disabled = true;
				}

				return;
			}

			// Inputs value is being set so lets be sure it isn't disabled when we do this or it won't submit
			if ( conditionFieldChild.disabled ) {
				conditionFieldChild.classList.remove( 'cbInputDisabled' );
				conditionFieldChild.disabled = false;
			}

			const newValue = set[childId];

			if ( ( conditionFieldChild.type === 'radio' ) || ( conditionFieldChild.type === 'checkbox' ) ) {
				conditionFieldChild.checked = newValue.split( '|*|' ).includes( conditionFieldChild.value );
			} else if ( conditionFieldChild.type === 'select-multiple' ) {
				const newValues = newValue.split( '|*|' );

				for ( const childOption of conditionFieldChild.options ) {
					childOption.selected = newValues.includes( childOption.value );
					childOption.disabled = false;
				}
			} else {
				conditionFieldChild.value = newValue;
			}

			conditionFieldChild.dispatchEvent( new Event( 'change' ) );
		});
	}
}

/**
 * Conditions a field based off the value of input
 *
 * @param {Element} field
 * @param {HTMLInputElement|HTMLSelectElement} input
 * @param {array} conditions
 */
function cbParamChange( field, input, conditions ) {
	const show = [];
	const hide = [];
	const set = {};
	const submit = cbParamCondition( field, input, conditions, show, hide, set, 'change' );

	cbParamShowHide( show, hide, set );

	if ( submit ) {
		const form = field.closest( 'form' );

		if ( form ) {
			form.submit();
		}
	}
}

/**
 * Returns the top most inputs relevant to a param
 *
 * @param {Element} field
 */
function cbParamInputs( field ) {
	// Search for select inputs first
	let inputs = field.querySelectorAll( 'select' );

	// Now search for textarea inputs
	if ( ! inputs.length ) {
		inputs = field.querySelectorAll( 'textarea' );
	}

	// Lets fallback to regular inputs
	if ( ! inputs.length ) {
		inputs = field.querySelectorAll( 'input' );
	}

	// The above is done to prevent mixing types (e.g. select2 has a select element AND an input element, but ONLY the select element is relevant)
	return inputs;
}

/**
 * Binds conditions to their fields and initializes them
 *
 * @param {object} fields
 */
function cbInitFields( fields ) {
	const show = [];
	const hide = [];
	const set = {};

	for ( const fieldId in fields ) {
		const field = document.getElementById( fieldId );

		if ( field === null ) {
			console.warn( 'xml if:showhide ' + fieldId + ' is undefined.' );
			continue;
		}

		// Collect a map of what fields are being conditioned by
		for ( const condition of fields[fieldId].conditions ) {
			for ( const conditionFieldId of condition.show ) {
				if ( ! ( conditionFieldId in cbHideFieldsMap ) ) {
					cbHideFieldsMap[conditionFieldId] = [];
				}

				if ( cbHideFieldsMap[conditionFieldId].includes( fieldId ) ) {
					continue;
				}

				cbHideFieldsMap[conditionFieldId].push( fieldId );
			}
		}

		const bindCondition = ( input ) => {
			if ( input.type === 'hidden' ) {
				return;
			}

			const triggerCondition = () => {
				cbParamChange( field, input, fields[fieldId].conditions );
			};

			input.addEventListener( 'cbParamChange', triggerCondition );

			if ( ( input.type === 'text' )
				|| ( input.type === 'email' )
				|| ( input.type === 'number' )
				|| ( input.type === 'password' )
				|| ( input.type === 'search' )
				|| ( input.type === 'tel' )
				|| ( input.type === 'url' )
				|| ( input.tagName.toLowerCase() === 'textarea' )
			) {
				input.addEventListener( 'keyup', triggerCondition );
			}

			input.addEventListener( 'change', triggerCondition );

			cbParamCondition( field, input, fields[fieldId].conditions, show, hide, set, 'init' );
		};

		const input = document.getElementById( fields[fieldId].element );

		if ( input !== null ) {
			bindCondition( input );
		} else {
			cbParamInputs( field ).forEach( ( input ) => {
				bindCondition( input );
			});
		}
	}

	cbParamShowHide( show, hide, set );
}

window.addEventListener( 'load', () => {
	cbInitFields( cbHideFields );
});


/**
* CB basic ajax library (experimental): OBSOLETED IN CB 1.2: USE JQUERY !
*/


function CBgetHttpRequestInstance() {
	var http_request = false;

	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	return http_request;
}

function CBmakeHttpRequest(url, id, errorText, postsVars, http_request) {
	if ((arguments.length < 5) || (http_request==null) ) {
		http_request = CBgetHttpRequestInstance();
	}
	if (!http_request) {
		// alert('Giving up: Cannot create an XMLHTTP instance');
		return false;
	}
	http_request.onreadystatechange = function() { CBalertContents(http_request); };
	if (postsVars == null) {
		http_request.open('GET', url, true);
		http_request.send(null);
	} else {
		http_request.open('POST', url, true);
		http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http_request.setRequestHeader("Content-length", postsVars.length);
		http_request.send(postsVars);
	}

	function CBalertContents(http_request) {
		if (http_request.readyState == 4) {
			if ((http_request.status == 200) && (http_request.responseText.length > 0) && (http_request.responseText.length < 1025)) {
				document.getElementById(id).innerHTML = http_request.responseText;
			} else if (errorText.length > 0) {
				document.getElementById(id).innerHTML = errorText;
			} else {
				document.getElementById(id).innerHTML = '';
			}
			http_request = null;
		}
	}

	return true;
}
