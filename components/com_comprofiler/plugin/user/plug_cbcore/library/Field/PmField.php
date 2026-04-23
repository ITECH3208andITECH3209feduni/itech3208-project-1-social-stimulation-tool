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

class PmField extends cbFieldHandler
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
		global $_CB_framework, $_CB_PMS;

		$oReturn								=	null;

		if ( ! $_CB_PMS ) {
			return $oReturn;
		}

		$pmLinks								=	$_CB_PMS->getPMSlinks( $user->id, $_CB_framework->myId(), null, null, 1 ) ;

		if ( count( $pmLinks ) > 0 ) {
			switch ( $output ) {
				case 'html':
				case 'rss':
					$imgMode					=	$field->get( '_imgMode', null, GetterInterface::INT ); // For B/C

					if ( $imgMode === null ) {
						$imgMode				=	$field->params->get( ( $reason == 'list' ? 'displayModeList' : 'displayMode' ), 0, GetterInterface::INT );
					}

					$pmIMG						=	'<span class="fa fa-comment" title="' . htmlspecialchars( CBTxt::T( '_UE_PM_USER', 'Send Private Message' ) ) . '"></span>';
					$useLayout					=	true;

					foreach ( $pmLinks as $pmLink ) {
					 	if ( is_array( $pmLink ) ) {
							switch ( $imgMode ) {
								default:
								case 0:
									$linkItem	=	$pmLink['caption'];		// Already translated in PMS plugin
									break;
								case 1:
									$useLayout	=	false; // We don't want to use layout for icon only display as we use it externally
									$linkItem	=	$pmIMG;
									break;
								case 2:
									$linkItem	=	$pmIMG . ' ' . $pmLink['caption'];
									break;
							}

							$oReturn			.=	'<a href="' . cbSef( $pmLink['url'] ) . '" title="' . htmlspecialchars( $pmLink['tooltip'] ) . '">' . $linkItem . '</a>';
					 	}
					}

					if ( $useLayout ) {
						$oReturn				=	$this->formatFieldValueLayout( $oReturn, $reason, $field, $user );
					}
					break;
				case 'htmledit':
					$oReturn					=	null;
					break;
				case 'json':
				case 'php':
				case 'xml':
				case 'csvheader':
				case 'fieldslist':
				case 'csv':
				default:
					$retArray					=	array();

					foreach ( $pmLinks as $pmLink ) {
					 	if ( is_array( $pmLink ) ) {
							$title				=	cbReplaceVars( $pmLink['caption'], $user );
							$url				=	cbSef( $pmLink['url'] );
							$description		=	cbReplaceVars( $pmLink['tooltip'], $user );

	 						$retArray[]			=	array( 'title' => $title, 'url' => $url, 'tooltip' => $description );
					 	}
					}

					$oReturn					=	$this->_linksArrayToFormat( $field, $retArray, $output );
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
		// on purpose don't log field update
		// nothing to do, PM fields don't save :-)
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