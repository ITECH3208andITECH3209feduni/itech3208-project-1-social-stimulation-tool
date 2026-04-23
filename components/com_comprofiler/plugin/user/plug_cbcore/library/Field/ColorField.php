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

\defined( 'CBLIB' ) or die();

class ColorField extends cbFieldHandler
{
	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		if ( ( $output == 'htmledit' ) && ( $reason == 'search' ) ) {
			// Searching a color field isn't very practical so for now just disable searching:
			return null;
		}

		if ( $output == 'html' ) {
			$valuesArray		=	array();

			foreach ( $field->getTableColumns() as $col ) {
				$valuesArray[]	=	$user->get( $col, null, GetterInterface::STRING );
			}

			$value				=	strtoupper( implode( ', ', $valuesArray ) );

			if ( $value && preg_match( '/^#[0-9A-Fa-f]{3,6}$/i', $value ) ) {
				$value			=	'<span class="d-inline-block border cbColorField">'
								.		'<div class="pl-5 pr-5 pt-3 pb-3 cbColorFieldSample" style="background-color: ' . htmlspecialchars( $value ) . ';"></div>'
								.		'<div class="border-top text-center user-select-all cbColorFieldColor">' . htmlspecialchars( $value ) . '</div>'
								.	'</span>';
			}

			return $this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $value, $output, false ), $reason, $field, $user );
		}

		return parent::getField( $field, $user, $output, $reason, $list_compare_types );
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value						=	Get::get( $postdata, $col, null, GetterInterface::STRING );

			if ( $value !== null ) {
				$value					=	strtoupper( $value );

				if ( $this->validate( $field, $user, $col, $value, $postdata, $reason ) ) {
					if ( $user->get( $col, '', GetterInterface::STRING ) !== $value ) {
						$this->_logFieldUpdate( $field, $user, $reason, $user->get( $col, '', GetterInterface::STRING ), $value );
					}
				}

				$user->set( $col, $value );
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

		if ( ( $value !== '' ) && ( $value !== null ) && ( ! preg_match( '/^#[0-9A-Fa-f]{3,6}$/i', $value ) ) ) {
			$this->_setValidationError( $field, $user, $reason, CBTxt::T( 'Not a color' ) );

			return false;
		}

		return true;
	}

	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $searchVals          RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata            Typically $_POST (but not necessarily), filtering required.
	 * @param  int         $list_compare_types  IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string      $reason              'edit' for save user edit, 'register' for save registration
	 * @return cbSqlQueryPart[]
	 */
	public function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, /** @noinspection PhpUnusedParameterInspection */ $reason )
	{
		// Searching a color field isn't very practical so for now just disable searching:
		return array();
	}
}