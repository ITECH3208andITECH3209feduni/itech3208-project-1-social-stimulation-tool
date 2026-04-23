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
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use cbSqlQueryPart;

\defined( 'CBLIB' ) or die();

class IntegerField extends TextField
{
	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output  'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason  'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'list' for user-lists
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		$value						=	$user->get( $field->name );

		switch ( $output ) {
			case 'htmledit':
				if ( $reason == 'search' ) {
					$minNam			=	$field->name . '__minval';
					$maxNam			=	$field->name . '__maxval';

					$minVal			=	$user->get( $minNam );
					$maxVal			=	$user->get( $maxNam );

					$fieldNameSave	=	$field->name;
					$field->name	=	$minNam;
					$minHtml		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'number', $minVal, '' );
					$field->name	=	$maxNam;
					$maxHtml		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'number', $maxVal, '' );
					$field->name	=	$fieldNameSave;

					return $this->_fieldSearchRangeModeHtml( $field, $user, $output, $reason, $value, $minHtml, $maxHtml, $list_compare_types );
				} else {
					if ( $field->get( 'type', null, GetterInterface::STRING ) == 'float' ) {
						$min		=	$field->params->get( 'integer_minimum', 0, GetterInterface::FLOAT );
						$value		=	(float) $value;
						$type		=	'float';
					} else {
						$min		=	$field->params->get( 'integer_minimum', 0, GetterInterface::INT );
						$value		=	(int) $value;
						$type		=	'integer';
					}

					if ( $field->params->getInt( 'field_edit_style', 0 ) === 1 ) {
						$type		=	'range';
					}

					if ( ( $min > 0 ) && ( (string) $value === '0' ) ) {
						// If the minimum does not allow for 0 and the value is 0 then treat it as null to allow range validation for non-required usage:
						$value		=	null;
					}

					return $this->_fieldEditToHtml( $field, $user, $reason, 'input', $type, $value, $this->getDataAttributes( $field, $user, $output, $reason ) );
				}
				break;
			case 'html':
			case 'rss':
				$thousandsSep		=	CBTxt::T( $field->params->get( 'fieldThousandsSeparator', '', GetterInterface::STRING ) );
				$decimalPoint		=	'';
				$decimals			=	0;

				if ( $field->get( 'type', null, GetterInterface::STRING ) == 'float' ) {
					$decimalPoint	=	CBTxt::T( $field->params->get( 'fieldDecimalsSeparator', '', GetterInterface::STRING ) );
					$decimals		=	strlen( substr( strrchr( (string) $field->params->get( 'integer_step', 0.01, GetterInterface::FLOAT ), '.' ), 1 ) );

					$value			=	(float) $value;
				} else {
					$value			=	(int) $value;
				}

				if ( $thousandsSep || $decimalPoint ) {
					$value			=	number_format( $value, $decimals, $decimalPoint, $thousandsSep );
				}

				return $this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $value, $output, true ), $reason, $field, $user );
				break;
			case 'json':
			case 'php':
			case 'xml':
			case 'csvheader':
			case 'fieldslist':
			case 'csv':
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value					=	cbGetParam( $postdata, $col );

			if ( ! is_array( $value ) ) {
				$value				=	stripslashes( $value );
				$validated			=	$this->validate( $field, $user, $col, $value, $postdata, $reason );

				if ( $value !== null ) {
					if ( $field->get( 'type', null, GetterInterface::STRING ) == 'float' ) {
						$value		=	(float) $value;
					} else {
						$value		=	(int) $value;
					}

					if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->$col, $value );
					}

					$user->$col		=	$value;
				}
			}
		}
	}

	/**
	 * Validator:
	 * Validates $value for $field->required and other rules
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user        RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  string      $columnName  Column to validate
	 * @param  string      $value       (RETURNED:) Value to validate, Returned Modified if needed !
	 * @param  array       $postdata    Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason      'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return boolean                  True if validate, $this->_setErrorMSG if False
	 */
	public function validate( &$field, &$user, $columnName, &$value, &$postdata, $reason )
	{
		if ( ! parent::validate( $field, $user, $columnName, $value, $postdata, $reason ) ) {
			return false;
		}

		if ( ( $value !== '' ) && ( $value !== null ) ) {		// empty values (e.g. non-mandatory) are treated in the parent validation.
			if ( $field->get( 'type', null, GetterInterface::STRING ) == 'float' ) {
				if ( ! preg_match( '/^[-0-9.]*$/', $value ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Not a number' ) );

					return false;
				}

				$min			=	$field->params->get( 'integer_minimum', 0, GetterInterface::FLOAT );
				$max			=	$field->params->get( 'integer_maximum', 1000000, GetterInterface::FLOAT );
				$step			=	$field->params->get( 'integer_step', 0.01, GetterInterface::FLOAT );
				$value			=	(float) $value;
			} else {
				if ( ! preg_match( '/^[-0-9]*$/', $value ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Not an integer' ) );

					return false;
				}

				$min			=	$field->params->get( 'integer_minimum', 0, GetterInterface::INT );
				$max			=	$field->params->get( 'integer_maximum', 1000000, GetterInterface::INT );
				$step			=	$field->params->get( 'integer_step', 1, GetterInterface::INT );
				$value			=	(int) $value;
			}

			// Validate Min/Max Range:
			if ( $max < $min ) {
				$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Min setting > Max setting !' ) );

				return false;
			}

			if ( $min && $max && ( ( $value < $min ) || ( $value > $max ) ) ) {
				$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'VALIDATION_ERROR_FIELD_BETWEEN_AND_NUMBER', 'Please enter a value between {0} and {1}.', array( '{0}' => $min, '{1}' => $max ) ) );

				return false;
			} elseif ( $min && ( $value < $min )  ) {
				$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'VALIDATION_ERROR_FIELD_GREATER_OR_EQUAL_TO', 'Please enter a value greater than or equal to {0}.', array( '{0}' => $min ) ) );

				return false;
			} elseif ( $max && ( $value > $max ) ) {
				$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'VALIDATION_ERROR_FIELD_LESS_OR_EQUAL_TO', 'Please enter a value less than or equal to {0}.', array( '{0}' => $max ) ) );

				return false;
			}

			// Validate Divisable by Step:
			if ( $value && $step ) {
				if ( $field->get( 'type', null, GetterInterface::STRING ) == 'float' ) {
					if ( ! ( abs( ( $value / $step ) - round( $value / $step ) ) < 0.0000001 ) ) {
						$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'VALIDATION_ERROR_FIELD_STEP', 'Please enter a multiple of {0}.', array( '{0}' => $step ) ) );

						return false;
					}
				} elseif ( ( abs( $value ) % $step ) != 0 ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'VALIDATION_ERROR_FIELD_STEP', 'Please enter a multiple of {0}.', array( '{0}' => $step ) ) );

					return false;
				}
			}

			// Validate Forbidden Words:
			$forbiddenContent		=	$field->params->get( 'fieldValidateForbiddenList_' . $reason, '' );

			if ( $forbiddenContent != '' ) {
				$forbiddenContent	=	explode( ',', $forbiddenContent );

				if ( in_array( (string) $value, $forbiddenContent ) ) {
					$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'UE_INPUT_VALUE_NOT_ALLOWED', 'This input value is not authorized.' ) );

					return false;
				}
			}
		}

		return true;
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
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason )
	{
		$query							=	array();

		foreach ( $field->getTableColumns() as $col ) {
			$minNam						=	$col . '__minval';
			$maxNam						=	$col . '__maxval';
			$searchMode					=	$this->_bindSearchRangeMode( $field, $searchVals, $postdata, $minNam, $maxNam, $list_compare_types );

			if ( $searchMode ) {
				if ( $field->get( 'type', null, GetterInterface::STRING ) == 'float' ) {
					$minVal				=	(float) cbGetParam( $postdata, $minNam, 0 );
					$maxVal				=	(float) cbGetParam( $postdata, $maxNam, 0 );
				} else {
					$minVal				=	(int) cbGetParam( $postdata, $minNam, 0 );
					$maxVal				=	(int) cbGetParam( $postdata, $maxNam, 0 );
				}

				if ( $minVal && ( cbGetParam( $postdata, $minNam, '' ) !== '' ) ) {
					$searchVals->$minNam =	$minVal;

					if ( $field->get( 'type', null, GetterInterface::STRING ) == 'float' ) {
						$operator		=	( $searchMode == 'isnot' ? ( abs( $minVal - $maxVal ) < 0.0000001 ? '<' : '<=' ) : '>=' );
					} else {
						$operator		=	( $searchMode == 'isnot' ? ( $minVal == $maxVal ? '<' : '<=' ) : '>=' );
					}

					$min				=	$this->_intToSql( $field, $col, $minVal, $operator, $searchMode );
				} else {
					$min				=	null;
				}

				if ( $maxVal && ( cbGetParam( $postdata, $maxNam, '' ) !== '' ) ) {
					$searchVals->$maxNam =	$maxVal;

					if ( $field->get( 'type', null, GetterInterface::STRING ) == 'float' ) {
						$operator		=	( $searchMode == 'isnot' ? ( ( abs( $maxVal - $minVal ) < 0.0000001 ? '<' : '<=' ) ? '>' : '>=' ) : '<=' );
					} else {
						$operator		=	( $searchMode == 'isnot' ? ( $maxVal == $minVal ? '>' : '>=' ) : '<=' );
					}

					$max				=	$this->_intToSql( $field, $col, $maxVal, $operator, $searchMode );
				} else {
					$max				=	null;
				}

				if ( $min && $max ) {
					$sql				=	new cbSqlQueryPart();
					$sql->tag			=	'column';
					$sql->name			=	$col;
					$sql->table			=	$field->table;
					$sql->type			=	'sql:operator';
					$sql->operator		=	( $searchMode == 'isnot' ? 'OR' : 'AND' );
					$sql->searchmode	=	$searchMode;

					$sql->addChildren( array( $min, $max ) );

					$query[]			=	$sql;
				} elseif ( $min ) {
					$query[]			=	$min;
				} elseif ( $max ) {
					$query[]			=	$max;
				}
			}
		}

		return $query;
	}

	/**
	 * Internal function to create an SQL query part based on a comparison operator
	 *
	 * @param  FieldTable  $field
	 * @param  string      $col
	 * @param  int         $value
	 * @param  string      $operator
	 * @param  string      $searchMode
	 * @return cbSqlQueryPart
	 */
	protected function _intToSql( &$field, $col, $value, $operator, $searchMode )
	{
		if ( $field->get( 'type', null, GetterInterface::STRING ) == 'float' ) {
			$value						=	(float) $value;
			$valueType					=	'const:float';
		} else {
			$value						=	(int) $value;
			$valueType					=	'const:int';
		}

		$sql							=	new cbSqlQueryPart();
		$sql->tag						=	'column';
		$sql->name						=	$col;
		$sql->table						=	$field->table;
		$sql->type						=	'sql:field';
		$sql->operator					=	$operator;
		$sql->value						=	$value;
		$sql->valuetype					=	$valueType;
		$sql->searchmode				=	$searchMode;

		return $sql;
	}
}