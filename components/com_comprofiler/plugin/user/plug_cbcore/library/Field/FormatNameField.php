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
use CBLib\Registry\GetterInterface;
use cbSqlQueryPart;
use CBuser;

\defined( 'CBLIB' ) or die();

class FormatNameField extends cbFieldHandler
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
		global $_CB_framework, $ueConfig, $_PLUGINS;

		$oReturn								=	'';
		if ( isset( $user ) && $user->id ) {

		$value									=	$user->getFormattedName();

			switch ( $output ) {
				case 'html':
				case 'rss':
					$profileLink				=	$user->get( '_allowProfileLink', $field->get( '_allowProfileLink', null, GetterInterface::BOOLEAN ), GetterInterface::BOOLEAN ); // For B/C

					if ( $profileLink === null ) {
						$profileLink			=	$field->params->get( 'fieldProfileLink', true, GetterInterface::BOOLEAN );
					}

					if ( $profileLink && ( $reason != 'profile') ) {
						$profileURL				=	$_CB_framework->userProfileUrl( $user->id, false );

						if ( $field->params->get( 'fieldHoverCanvas', false, GetterInterface::BOOLEAN ) ) {
							$canvasPlugins		=	$_PLUGINS->trigger( 'onHoverCanvasDisplay', array( $field, $user, $output, $reason, $list_compare_types ) );
							$canvasContent		=	$field->params->get( 'fieldHoverCanvasContent', null, GetterInterface::HTML );
							$canvasWidth		=	$field->params->get( 'fieldHoverCanvasWidth', 300, GetterInterface::INT );

							if ( ! $canvasWidth ) {
								$canvasWidth	=	300;
							}

							$cbUser				=	CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ), false );

							$tooltip			=	'<div class="card no-overflow cbCanvasLayout cbCanvasLayoutSm">'
												.		'<div class="card-header p-0 position-relative cbCanvasLayoutTop">'
												.			'<div class="position-absolute cbCanvasLayoutBackground">'
												.				$cbUser->getField( 'canvas', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldProfileLink' => false ) ) )
												.			'</div>'
												.		'</div>'
												.		'<div class="position-relative cbCanvasLayoutBottom">'
												.			'<div class="position-absolute cbCanvasLayoutPhoto">'
												.				$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
												.			'</div>'
												.		'</div>'
												.		'<div class="card-body p-2 position-relative cbCanvasLayoutBody">';

							if ( $canvasContent ) {
								$tooltip		.=			'<div class="cbCanvasLayoutContent">'
												.				$cbUser->replaceUserVars( $canvasContent )
												.			'</div>';
							} else {
								$tooltip		.=			'<div class="text-truncate cbCanvasLayoutContent">'
												.				$cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) )
												.				' ' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) )
												.			'</div>';
							}

							$tooltip			.=			( $canvasPlugins ? '<div class="cbCanvasLayoutContent mt-1">' . implode( '', $canvasPlugins ) . '</div>' : null )
												.		'</div>'
												.	'</div>';

							$value				=	cbTooltip( null, $tooltip, null, $canvasWidth, null, $value, $profileURL, 'data-cbtooltip-close-fixed="true" data-cbtooltip-close-delay="200" data-cbtooltip-classes="qtip-canvas"' );
						} else {
							$value				=	'<a href="' . htmlspecialchars( $profileURL ) . '">' . $value . '</a>';
						}
					}

					$oReturn					=	$this->formatFieldValueLayout( $value, $reason, $field, $user );
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
					$oReturn					=	$this->_formatFieldOutput( $field->name, $value, $output );;
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
		// nothing to do, Formatted names fields don't save :-)
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