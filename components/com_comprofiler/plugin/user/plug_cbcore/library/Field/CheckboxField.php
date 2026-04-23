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
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use cbSqlQueryPart;
use moscomprofilerHTML;

\defined( 'CBLIB' ) or die();

class CheckboxField extends cbFieldHandler
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
		$value			=	$user->get( $field->name );

		switch ( $output ) {
			case 'html':
			case 'rss':
				if ( $value == 1 ) {
					$yes		=	CBTxt::T( $field->params->getString( 'field_checked_label', '' ) );

					if ( $yes === '' ) {
						$yes	=	CBTxt::T( 'UE_YES', 'Yes' );
					}

					return $this->formatFieldValueLayout( $yes, $reason, $field, $user );
				} elseif ( $value == 0 ) {
					$no			=	CBTxt::T( $field->params->getString( 'field_unchecked_label', '' ) );

					if ( $no === '' ) {
						$no		=	CBTxt::T( 'UE_NO', 'No' );
					}

					return $this->formatFieldValueLayout( $no, $reason, $field, $user );
				} else {
					return $this->formatFieldValueLayout( null, $reason, $field, $user );
				}
				break;

			case 'htmledit':
				if ( $reason == 'search' ) {
					$yes		=	CBTxt::T( $field->params->getString( 'field_checked_label', '' ) );

					if ( $yes === '' ) {
						$yes	=	CBTxt::T( 'UE_YES', 'Yes' );
					}

					$no			=	CBTxt::T( $field->params->getString( 'field_unchecked_label', '' ) );

					if ( $no === '' ) {
						$no		=	CBTxt::T( 'UE_NO', 'No' );
					}

					$choices	=	array();
					$choices[]	=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'UE_NO_PREFERENCE', 'No preference' ) );
					$choices[]	=	moscomprofilerHTML::makeOption( '1', $yes );
					$choices[]	=	moscomprofilerHTML::makeOption( '0', $no );
					$html		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $value, '', $choices, true, null, false );
					$html		=	$this->_fieldSearchModeHtml( $field, $user, $html, 'singlechoice', $list_compare_types );
					return $html;
				} else {
					if ( $field->params->get( 'field_edit_style', 0, GetterInterface::INT ) ) {
						return $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'yesno', ( $value == 1 ? 1 : 0 ), '' );
					} else {
						$checked		=	'';
						if ( $value == 1 ) {
							$checked	=	' checked="checked"';
						}
						return $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'checkbox', $value, $checked );
					}
				}
				break;

			case 'json':
				return "'" . $field->name . "' : " . (int) $value;
				break;

			case 'php':
				return array( $field->name => (int) $value );
				break;

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
			$value					=	stripslashes( cbGetParam( $postdata, $col, '' ) );

			if ( $value == '' ) {
				$value				=	0;
			} elseif ( $value == '1' ) {
				$value				=	1;
			}
			$validated				=	$this->validate( $field, $user, $col, $value, $postdata, $reason );
			if ( ( $value === 0 ) || ( $value === 1 ) ) {
				if ( $validated && isset( $user->$col ) && ( ( (string) $user->$col ) !== (string) $value ) ) {
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
	function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason )
	{
		$query							=	array();
		$searchMode						=	$this->_bindSearchMode( $field, $searchVals, $postdata, 'none', $list_compare_types );
		if ( $searchMode ) {
			foreach ( $field->getTableColumns() as $col ) {
				$value						=	stripslashes( cbGetParam( $postdata, $col ) );
				if ( $value === '0' ) {
					$value				=	0;
				} elseif ( $value == '1' ) {
					$value				=	1;
				} else {
					continue;
				}
				$searchVals->$col		=	$value;
				// $this->validate( $field, $user, $col, $value, $postdata, $reason );
				$sql					=	new cbSqlQueryPart();
				$sql->tag				=	'column';
				$sql->name				=	$col;
				$sql->table				=	$field->table;
				$sql->type				=	'sql:field';
				$sql->operator			=	'=';
				$sql->value				=	$value;
				$sql->valuetype			=	'const:int';
				$sql->searchmode		=	$searchMode;
				$query[]				=	$sql;
			}
		}
		return $query;
	}
}