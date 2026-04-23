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
use cbSqlQueryPart;
use moscomprofilerHTML;

\defined( 'CBLIB' ) or die();

class CounterField extends cbFieldHandler
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
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		$oReturn							=	'';

		if ( is_object( $user ) ) {
			$values							=	array();
			foreach ( $field->getTableColumns() as $col ) {
				$values[]					=	(int) $user->$col;
			}
			$value							=	implode( ', ', $values );

			switch ( $output ) {
				case 'html':
				case 'rss':
					$oReturn				=	$this->formatFieldValueLayout( $value, $reason, $field, $user );
					break;

				case 'htmledit':
					$oReturn				=	null;

					if ( $reason == 'search' ) {
						$minNam				=	$field->name . '__minval';
						$maxNam				=	$field->name . '__maxval';

						$minVal				=	$user->get( $minNam );
						$maxVal				=	$user->get( $maxNam );

						if ( $maxVal === null ) {
							$maxVal			=	99999;
						}

						$choices			=	array();

						for ( $i = 0 ; $i <= 10000 ; ( $i < 5 ? $i += 1 : ( $i < 30 ? $i += 5 : ( $i < 100 ? $i += 10 : ( $i < 1000 ? $i += 100 : $i += 1000 ) ) ) ) ) {
							$choices[]		=	moscomprofilerHTML::makeOption( ( $i == 0 ? 0 : (string) $i ), $i );
						}

						$fieldNameSave		=	$field->name;
						$field->name		=	$minNam;

						$minHtml			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $minVal, null, $choices );

						$field->name		=	$maxNam;

						$choices[]			=	moscomprofilerHTML::makeOption( '99999', 'UE_ANY' ); // CBTxt::T( 'UE_ANY', 'Any' )

						$maxHtml			=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $maxVal, null, $choices );

						$field->name		=	$fieldNameSave;

						$oReturn			=	$this->_fieldSearchRangeModeHtml( $field, $user, $output, $reason, $value, $minHtml, $maxHtml, $list_compare_types );
					}
					break;

				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					$oReturn				=	$this->_formatFieldOutputIntBoolFloat( $field->name, $value, $output );;
					break;
			}
		}
		return $oReturn;
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
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );
		// nothing to do, counter Status fields don't save :-)
	}

	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
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

		$col							=	$field->name;
		$minNam							=	$col . '__minval';
		$maxNam							=	$col . '__maxval';

		$searchMode						=	$this->_bindSearchRangeMode( $field, $searchVals, $postdata, $minNam, $maxNam, $list_compare_types );

		if ( $searchMode ) {
			$minVal						=	(int) cbGetParam( $postdata, $minNam, 0 );
			$maxVal						=	(int) cbGetParam( $postdata, $maxNam, 0 );

			if ( $minVal && ( $minVal != 0 ) ) {
				$searchVals->$minNam	=	$minVal;

				$operator				=	( $searchMode == 'isnot' ? ( $minVal == $maxVal ? '<' : '<=' ) : '>=' );

				$min					=	$this->_intToSql( $field, $col, $minVal, $operator, $searchMode );
			} else {
				$min					=	null;
			}

			if ( $maxVal && ( $maxVal != 99999 ) ) {
				$searchVals->$maxNam	=	$maxVal;

				$operator				=   ( $searchMode == 'isnot' ? ( $maxVal == $minVal ? '>' : '>=' ) : '<=' );

				$max					=	$this->_intToSql( $field, $col, $maxVal, $operator, $searchMode );
			} else {
				$max					=	null;
			}

			if ( $min && $max ) {
				$sql					=	new cbSqlQueryPart();
				$sql->tag				=	'column';
				$sql->name				=	$col;
				$sql->table				=	$field->table;
				$sql->type				=	'sql:operator';
				$sql->operator			=	( $searchMode == 'isnot' ? 'OR' : 'AND' );
				$sql->searchmode		=	$searchMode;

				$sql->addChildren( array( $min, $max ) );

				$query[]				=	$sql;
			} elseif ( $min ) {
				$query[]				=	$min;
			} elseif ( $max ) {
				$query[]				=	$max;
			}
		}

		return $query;
	}

	/**
	 * Internal function to build SQL request
	 * @access private
	 *
	 * @param  FieldTable      $field
	 * @param  string          $col
	 * @param  int             $value
	 * @param  string          $operator
	 * @param  string          $searchMode
	 * @return cbSqlQueryPart
	 */
	function _intToSql( &$field, $col, $value, $operator, $searchMode )
	{
		$value							=	(int) $value;
		// $this->validate( $field, $user, $col, $value, $postdata, $reason );
		$sql							=	new cbSqlQueryPart();
		$sql->tag						=	'column';
		$sql->name						=	$col;
		$sql->table						=	$field->table;
		$sql->type						=	'sql:field';
		$sql->operator					=	$operator;
		$sql->value						=	$value;
		$sql->valuetype					=	'const:int';
		$sql->searchmode				=	$searchMode;
		return $sql;
	}
}