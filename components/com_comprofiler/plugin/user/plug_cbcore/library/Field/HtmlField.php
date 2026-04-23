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
use CBLib\Application\Application;
use cbSqlQueryPart;

\defined( 'CBLIB' ) or die();

class HtmlField extends cbFieldHandler
{
	/**
	 * Returns a DELIMITER field in specified format
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
		$value			=	cbUnHtmlspecialchars( $field->description ); //TBD: unhtml is kept for backwards database compatibility until CB 2.0

		if ( $field->params->get( 'field_content_plugins', 0 ) ) {
			$value		=	Application::Cms()->prepareHtmlContentPlugins( $value, 'field.custom', $user->id );
		}

		$return			=	$this->_formatFieldOutput( $field->name, cbReplaceVars( $value, $user, true, true, array( 'reason' => $reason ) ), $output, false );

		if ( $output == 'htmledit' ) {
			$return		.=	$this->_fieldIconsHtml( $field, $user, $output, $reason, null, null, $value, null, null, false, false );
		}

		return $return;
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
		// nothing to do, Delimiter fields don't save :-)
	}

	/**
	 * Finder:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @param  string      $reason    'edit' for save profile edit, 'register' for registration, 'search' for searches
	 * @return cbSqlQueryPart[]
	 */
	function bindSearchCriteria( &$field, &$user, &$postdata, $list_compare_types, $reason )
	{
		return array();
	}
}