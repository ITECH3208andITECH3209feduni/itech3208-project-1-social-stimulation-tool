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

\defined( 'CBLIB' ) or die();

class StatusField extends cbFieldHandler
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
		global $_CB_framework, $ueConfig;

		$return								=	null;

		if ( ( $ueConfig['allow_onlinestatus'] == 1 ) ) {
			$lastTime						=	$_CB_framework->userOnlineLastTime( $user->id );
			$isOnline						=	( $lastTime != null );

			switch ( $output ) {
				case 'html':
				case 'rss':
					$useLayout				=	true;

					if ( isset( $user ) && $user->id ) {
						if ( $isOnline > 0 ) {
							$value			=	CBTxt::T( 'UE_ISONLINE', 'ONLINE' );
							$icon			=	'circle';
							$class			=	'cb_online text-success';
						} else {
							$value			=	CBTxt::T( 'UE_ISOFFLINE', 'OFFLINE' );
							$icon			=	'circle-o';
							$class			=	'cb_offline text-danger';
						}

						$imgMode			=	$field->get( '_imgMode', null, GetterInterface::INT ); // For B/C

						if ( $imgMode === null ) {
							$imgMode		=	$field->params->get( ( $reason == 'list' ? 'displayModeList' : 'displayMode' ), 2, GetterInterface::INT );
						}

						switch ( $imgMode ) {
							case 0:
								$return		=	'<span class="' . $class . '">' . htmlspecialchars( $value ) . '</span>';
								break;
							case 1:
								$return		=	'<span class="' . $class . '"><span class="fa fa-' . $icon . '" title="' . htmlspecialchars( $value ) . '"></span></span>';
								$useLayout	=	false; // We don't want to use layout for icon only display as we use it externally
								break;
							case 2:
								$return		=	'<span class="' . $class . '"><span class="fa fa-' . $icon . '"></span> ' . htmlspecialchars( $value ) . '</span>';
								break;
						}
					}

					if ( $useLayout ) {
						$return				=	$this->formatFieldValueLayout( $return, $reason, $field, $user );
					}
					break;
				case 'htmledit':
//					if ( $reason == 'search' ) {
//						$choices			=	array();
//						$choices[]			=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'No preference' );
//						$choices[]			=	moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Is Online' ) );
//						$choices[]			=	moscomprofilerHTML::makeOption( '0', CBTxt::T( 'Is Offline' ) );
//
//						$col				=	$field->name;
//						$value				=	$user->$col;
//
//						$return				=	$this->_fieldSearchModeHtml( $field, $user, $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'select', $value, null, $choices ), 'singlechoice', $list_compare_types );
//					}
					break;
				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					if ( isset( $user ) && $user->id ) {
						$return				=	$this->_formatFieldOutputIntBoolFloat( $field->name, ( $isOnline > 0 ? 'true' : 'false' ), $output );
					}
					break;
			}
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
		// nothing to do, Status fields don't save :-)
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
		return array(); // Online Status doesn't currently have searching
	}
}