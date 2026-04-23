<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Core\Field;

use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use cbFieldHandler;
use CBLib\Input\Get;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use cbSqlQueryPart;
use moscomprofilerHTML;
use stdClass;

\defined( 'CBLIB' ) or die();

class SelectField extends cbFieldHandler
{
	/**
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework;

		$value					=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				global $_CB_database;

				static $fieldValues			=	array();

				$cacheId					=	(int) $field->fieldid;

				if ( ! isset( $fieldValues[$cacheId] ) ) {
					$_CB_database->setQuery( "SELECT fieldtitle, fieldlabel FROM #__comprofiler_field_values WHERE fieldid = " . $cacheId . " AND fieldtitle != '' AND fieldgroup = 0 ORDER BY ordering" );
					$fieldValues[$cacheId]	=	$_CB_database->loadObjectList();
				}

				$allValues					=	$fieldValues[$cacheId];

				$chosen						=	$this->_explodeCBvalues( $value );

				$class						=	trim( $field->params->getCmd( 'field_display_class', '' ) );
				$displayStyle				=	$field->params->get( 'field_display_style', '' );
				$listType					=	( $displayStyle == 1 ? 'ul' : ( $displayStyle == 2 ? 'ol' : ( $displayStyle == 3 ? 'tag' : ', ' ) ) );
				$isTags						=	( $field->get( 'type', '', GetterInterface::STRING ) == 'tag' );

				foreach ( $chosen as $k => $v ) {
					foreach ( $allValues as $allValue ) {
						if ( $v != $allValue->fieldtitle ) {
							continue;
						}

						$chosen[$k]			=	( $allValue->fieldlabel == '' ? CBTxt::T( $allValue->fieldtitle ) : CBTxt::T( $allValue->fieldlabel ) );
						continue 2;
					}

					// We don't want to translate custom tag values supplied by the user; for other types lets fallback to translating encase it was a removed option:
					$chosen[$k]				=	( $isTags ? $v : CBTxt::T( $v ) );
				}

				return $this->formatFieldValueLayout( $this->_arrayToFormat( $field, $chosen, $output, $listType, $class ), $reason, $field, $user );

			/** @noinspection PhpMissingBreakStatementInspection */
			case 'htmledit':
				global $_CB_database;

				static $fieldOptions		=	array();

				$cacheId					=	(int) $field->fieldid;

				if ( ! isset( $fieldOptions[$cacheId] ) ) {
					$_CB_database->setQuery( "SELECT fieldtitle AS `value`, if ( fieldlabel != '', fieldlabel, fieldtitle ) AS `text`, `fieldgroup` AS `group` FROM #__comprofiler_field_values"		// id needed for the labels
											. "\n WHERE fieldid = " . $cacheId
											. "\n ORDER BY ordering" );
					$fieldOptions[$cacheId]	=	$_CB_database->loadObjectList();
				}

				$allValues					=	$fieldOptions[$cacheId];
/*
				if ( $reason == 'search' ) {
					array_unshift( $allValues, $this->_valueDoesntMatter( $field, $reason, ( $field->type == 'multicheckbox' ) ) );
					if ( ( $field->type == 'multicheckbox' ) && ( $value === null ) ) {
						$value	=	array( null );			// so that "None" is really not checked if not checked...
					}
				}
*/
				if ( $field->get( 'type', null, GetterInterface::STRING ) == 'tag' ) {
					static $loaded	=	0;

					if ( ! $loaded++ ) {
						$js			=	"$( '.cbSelectTag' ).cbselect({"
									.		"tags: true,"
									.		"language: {"
									.			"errorLoading: function() {"
									.				"return " . json_encode( CBTxt::T( 'The results could not be loaded.' ), JSON_HEX_TAG ) . ";"
									.			"},"
									.			"inputTooLong: function() {"
									.				"return " . json_encode( CBTxt::T( 'Search input too long.' ), JSON_HEX_TAG ) . ";"
									.			"},"
									.			"inputTooShort: function() {"
									.				"return " . json_encode( CBTxt::T( 'Search input too short.' ), JSON_HEX_TAG ) . ";"
									.			"},"
									.			"loadingMore: function() {"
									.				"return " . json_encode( CBTxt::T( 'Loading more results...' ), JSON_HEX_TAG ) . ";"
									.			"},"
									.			"maximumSelected: function() {"
									.				"return " . json_encode( CBTxt::T( 'You cannot select any more choices.' ), JSON_HEX_TAG ) . ";"
									.			"},"
									.			"noResults: function() {"
									.				"return " . json_encode( CBTxt::T( 'No results found.' ), JSON_HEX_TAG ) . ";"
									.			"},"
									.			"searching: function() {"
									.				"return " . json_encode( CBTxt::T( 'Searching...' ), JSON_HEX_TAG ) . ";"
									.			"}"
									.		"}"
									.	"});";

						$_CB_framework->outputCbJQuery( $js, 'cbselect' );
					}
				}

				if ( $reason == 'search' ) {
//					$html			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'multicheckbox', $value, '', $allValues );
					$displayType	=	$field->type;
					if ( ( $field->type == 'radio' ) && ( ( $list_compare_types == 2 ) || ( is_array( $value ) && ( count( $value ) > 1 ) ) ) ) {
						$displayType	=	'multicheckbox';
					}
					if ( ( $field->type == 'select' ) && ( ( $list_compare_types == 1 ) || ( is_array( $value ) && ( count( $value ) > 1 ) ) ) ) {
						$displayType	=	'multiselect';
					}
					if ( in_array( $list_compare_types, array( 0, 2 ) ) && ( ! in_array( $displayType, array( 'multicheckbox', 'tag' ) ) ) ) {
						if ( $allValues && ( $allValues[0]->value == '' ) ) {
							// About to add 'No preference' so remove custom blank
							unset( $allValues[0] );
						}

						array_unshift( $allValues, moscomprofilerHTML::makeOption( '', 'UE_NO_PREFERENCE' ) ); // CBTxt::T( 'UE_NO_PREFERENCE', 'No preference' )
					}
					$html			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', $displayType, $value, '', $allValues );
					$html			=	$this->_fieldSearchModeHtml( $field, $user, $html, ( ( ( strpos( $displayType, 'multi' ) === 0 ) && ( ! in_array( $field->type, array( 'radio', 'select' ) ) ) ) || ( $displayType == 'tag' ) ? 'multiplechoice' : 'singlechoice' ), $list_compare_types );
				} else {
					if ( $field->get( 'type', null, GetterInterface::STRING ) == 'tag' ) {
						// Since we're a tag usage we can have custom values so lets see if any exist to be added to available options:
						$chosen		=	$this->_explodeCBvalues( $value );

						foreach ( $chosen as $k => $v ) {
							foreach ( $allValues as $allValue ) {
								if ( $v != $allValue->value ) {
									// Custom values we'll add further below:
									continue;
								}

								// Skip values that actually exist:
								continue 2;
							}

							// Add custom tags to the available values list:
							$customValue				=	new stdClass();
							$customValue->value			=	$v;
							$customValue->text			=	$v;
							$customValue->group			=	0;

							$allValues[]				=	$customValue;
						}
					}

					if ( in_array( $field->type, array( 'multicheckbox', 'radio' ) ) && $field->params->get( 'field_edit_style', 0, GetterInterface::INT ) ) {
						$html		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', $field->type . 'buttons', $value, ( in_array( $field->type, array( 'multicheckbox', 'multiselect', 'tag' ) ) ? $this->getDataAttributes( $field, $user, $output, $reason ) : '' ), $allValues );
					} else {
						$html		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', $field->type, $value, ( in_array( $field->type, array( 'multicheckbox', 'multiselect', 'tag' ) ) ? $this->getDataAttributes( $field, $user, $output, $reason ) : '' ), $allValues );
					}
				}

				return $html;

			case 'xml':
			case 'json':
			/** @noinspection PhpMissingBreakStatementInspection */
			case 'php':
				if ( substr( $reason, -11 ) == ':translated' ) {
					// Translated version in case reason finishes by :translated: (will be used later):
					if ( in_array( $field->type, array( 'radio', 'select' ) ) ) {
						$chosen			=	CBTxt::T( $value );

						return $this->_formatFieldOutput( $field->name, $chosen, $output, ( $output != 'xml' ) );
					}

					// multiselect, multicheckbox, tag:
					$chosen			=	$this->_explodeCBvalues( $value );
					for( $i = 0, $n = count( $chosen ); $i < $n; $i++ ) {
						$chosen[$i]	=	CBTxt::T( $chosen[$i] );
					}

					return $this->_arrayToFormat( $field, $chosen, $output );
				}
				// else: fall-through on purpose here (fixes bug #2960):

			case 'csv':
				if ( in_array( $field->type, array( 'radio', 'select' ) ) ) {
					return $this->_formatFieldOutput( $field->name, $value, $output, ( $output != 'xml' ) );
				}

				// multiselect, multicheckbox, tag:
				$chosen			=	$this->_explodeCBvalues( $value );
				return $this->_arrayToFormat( $field, $chosen, $output );

			case 'csvheader':
			case 'fieldslist':
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
		}
	}
	/**
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason ) {
		global $_CB_database;

		$isTags							=	( $field->get( 'type', null, GetterInterface::STRING ) == 'tag' );

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value						=	cbGetParam( $postdata, $col, null, _CB_ALLOWRAW );
//			if ( $value === null ) {
//				$value				=	array();
//			} elseif ( $field->type == 'radio' ) {
//				$value				=	array( $value );
//			}

			if ( is_array( $value ) ) {
				if ( count( $value ) > 0 ) {

					$_CB_database->setQuery( 'SELECT fieldtitle AS id FROM #__comprofiler_field_values'
											. "\n WHERE fieldid = " . (int) $field->fieldid
											. "\n AND fieldtitle != ''"
											. "\n AND fieldgroup = 0"
											. "\n ORDER BY ordering" );
					$authorizedValues	=	$_CB_database->loadResultArray();

					$okVals				=	array();
					foreach ( $value as $k => $v ) {
						// revert escaping of cbGetParam:
						$v				=	stripslashes( $v );
						// check for duplicates:
						if ( in_array( $v, $okVals, true ) )  {
							continue;
						}
						// check authorized values:
						if ( in_array( $v, $authorizedValues, true ) ) {
							$okVals[$k]	=	$v;
						} elseif ( $isTags ) {
							// Allow unauthorized values for tags, but clean them to strings:
							$okVals[$k]	=	Get::clean( $v, GetterInterface::STRING );
						}
					}
					$value				=	$this->_implodeCBvalues( $okVals );
				} else {
					$value				=	'';
				}
			} elseif ( ( $value === null ) || ( $value === '' ) ) {
				$value					=	'';
			} else {
				$value					=	stripslashes( $value );	// compensate for cbGetParam.
				$_CB_database->setQuery( 'SELECT fieldtitle AS id FROM #__comprofiler_field_values'
											. "\n WHERE fieldid = " . (int) $field->fieldid
											. "\n AND fieldtitle = " . $_CB_database->Quote( $value )
											. "\n AND fieldgroup = 0" );
				$authorizedValues		=	$_CB_database->loadResultArray();

				if ( ! in_array( $value, $authorizedValues, true ) ) {
					if ( $isTags ) {
						// Allow unauthorized value for tags, but clean it to string:
						$value			=	Get::clean( $value, GetterInterface::STRING );
					} else {
						$value			=	null;
					}
				}
			}
			if ( $this->validate( $field, $user, $col, $value, $postdata, $reason ) ) {
				if ( isset( $user->$col ) && ( (string) $user->$col ) !== (string) $value ) {
					$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
				}
			}
			$user->$col				=	$value;
		}
	}
	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $searchVals  RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string      $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return cbSqlQueryPart[]
	 */
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason ) {
		global $_CB_database;

		$displayType						=	$field->type;
		if ( ( $field->type == 'radio' ) && ( $list_compare_types == 2 ) ) {
			$displayType	=	'multicheckbox';
		}

		$query								=	array();
		$searchMode							=	$this->_bindSearchMode( $field, $searchVals, $postdata, ( ( strpos( $displayType, 'multi' ) === 0 ) || ( $displayType == 'tag' ) ? 'multiplechoice' : 'singlechoice' ), $list_compare_types );
		if ( $searchMode ) {
			foreach ( $field->getTableColumns() as $col ) {
				$value						=	cbGetParam( $postdata, $col );
				if ( is_array( $value ) ) {
					if ( count( $value ) > 0 ) {
						$_CB_database->setQuery( 'SELECT fieldtitle AS id FROM #__comprofiler_field_values'
												. "\n WHERE fieldid = " . (int) $field->fieldid
												. "\n AND fieldtitle != ''"
												. "\n AND fieldgroup = 0"
												. "\n ORDER BY ordering" );
						$authorizedValues	=	$_CB_database->loadResultArray();

						foreach ( $value as $k => $v ) {
							if ( ( count( $value ) == 1 ) && ( $v === '' ) ) {
								if ( $list_compare_types == 1 ) {
									$value		=	'';		// Advanced search: "None": checked: search for nothing selected
								} else {
									$value		=	null;	// Type 0 and 2 : Simple search: "Do not care" checked: do not search
								}
								break;
							}
							// revert escaping of cbGetParam:
							$v				=	stripslashes( $v );
							// check authorized values:
							if ( in_array( $v, $authorizedValues ) ) {
								$value[$k]	=	$v;
							} elseif ( $displayType == 'tag' ) {
								// Allow unauthorized values for tags, but clean them to strings:
								$value[$k]	=	Get::clean( $v, GetterInterface::STRING );
							} else {
								unset( $value[$k] );
							}
						}

					} else {
						$value				=	null;
					}
					if ( ( $value !== null ) && ( $value !== '' ) && in_array( $searchMode, array( 'is', 'isnot' ) ) ) {		// keep $value array if search is not strict
						$value				=	stripslashes( $this->_implodeCBvalues( $value ) );	// compensate for cbGetParam.
					}
				} else {
					if ( ( $value !== null ) && ( $value !== '' ) ) {
						$value					=	stripslashes( $value );	// compensate for cbGetParam.
						$_CB_database->setQuery( 'SELECT fieldtitle AS id FROM #__comprofiler_field_values'
													. "\n WHERE fieldid = " . (int) $field->fieldid
													. "\n AND fieldtitle = " . $_CB_database->Quote( $value )
													. "\n AND fieldgroup = 0" );
						$authorizedValues	=	$_CB_database->loadResultArray();
						if ( ! in_array( $value, $authorizedValues ) ) {
							if ( $displayType == 'tag' ) {
								// Allow unauthorized value for tags, but clean it to string:
								$value			=	Get::clean( $value, GetterInterface::STRING );
							} else {
								$value			=	null;
							}
						}
					} else {
						if ( ( $list_compare_types == 1 ) && in_array( $searchMode, array( 'is', 'isnot' ) ) ) {
							$value			=	'';
						} else {
	//					if ( ( $field->type == 'multicheckbox' ) && ( $value === null ) ) {
							$value			=	null;				// 'none' is not checked and no other is checked: search for DON'T CARE
						}
					}
				}
				if ( $value !== null ) {
					$searchVals->$col		=	$value;
					// $this->validate( $field, $user, $col, $value, $postdata, $reason );
					$sql					=	new cbSqlQueryPart();
					$sql->tag				=	'column';
					$sql->name				=	$col;
					$sql->table				=	$field->table;
					$sql->type				=	'sql:field';
					$sql->operator			=	'=';
					$sql->value				=	$value;
					$sql->valuetype			=	'const:string';
					$sql->searchmode		=	$searchMode;
					$query[]				=	$sql;
				}
			}
		}
		return $query;
	}
}