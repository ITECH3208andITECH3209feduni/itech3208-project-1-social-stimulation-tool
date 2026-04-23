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

\defined( 'CBLIB' ) or die();

class TextareaField extends TextField
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
		switch ( $output ) {
			case 'html':
			case 'rss':
				return str_replace( "\n", '<br />', parent::getField( $field, $user, $output, $reason, $list_compare_types ) );
			default:
				return parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}
	}

	/**
	 * converts to HTML
	 * Override to change the field type from textarea to text in case of searches.
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $reason      'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  string      $tag         <tag
	 * @param  string      $type        type="$type"
	 * @param  string      $value       value="$value"
	 * @param  string      $additional  'xxxx="xxx" yy="y"'  WARNING: No classes in here, use $classes
	 * @param  string      $allValues
	 * @param  boolean     $displayFieldIcons
	 * @param  array       $classes     CSS classes
	 * @param  boolean     $translate          specify if $allValues should be translated or not
	 * @return string                   HTML: <tag type="$type" value="$value" xxxx="xxx" yy="y" />
	 */
	protected function _fieldEditToHtml( &$field, &$user, $reason, $tag, $type, $value, $additional, $allValues = null, $displayFieldIcons = true, $classes = null, $translate = true )
	{
		$rows					=	$field->rows;

		if ( $reason == 'search' ) {
			if ( $rows > 5 ) {
				$field->rows	=	5;
			}
		}

		$return					=	 parent::_fieldEditToHtml( $field, $user, $reason, $tag, $type, $value, $additional, $allValues, $displayFieldIcons, $classes );

		if ( $reason == 'search' ) {
			$field->rows		=	$rows;
		}

		return $return;
	}
}