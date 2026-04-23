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
use cbConnection;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;

\defined( 'CBLIB' ) or die();

class PointsField extends IntegerField
{
	/**
	 * Checks if user has increment access to this field
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @return boolean
	 */
	private function getIncrementAccess( &$field, &$user )
	{
		global $_CB_framework, $_CB_database;

		static $cache					=	array();

		$myId							=	(int) $_CB_framework->myId();
		$userId							=	(int) $user->get( 'id' );
		$fieldId						=	(int) $field->get( 'fieldid' );
		$ipAddress						=	$this->getInput()->getRequestIP();

		$incrementDelay					=	$field->params->get( 'points_inc_delay', null );
		$customDelay					=	$field->params->get( 'points_inc_delay_custom', null );

		$cacheId						=	$myId . $userId . $fieldId;

		if ( ! isset( $cache[$cacheId] ) ) {
			$ratingAccess				=	(int) $field->params->get( 'points_access', 1 );
			$excludeSelf				=	(int) $field->params->get( 'points_access_exclude', 0 );
			$includeSelf				=	(int) $field->params->get( 'points_access_include', 0 );
			$viewAccessLevel			=	(int) $field->params->get( 'points_access_custom', 1 );
			$access						=	false;

			switch ( $ratingAccess ) {
				case 8:
					if ( Application::MyUser()->canViewAccessLevel( $viewAccessLevel ) && ( ( ( $userId == $myId ) && ( ! $excludeSelf ) ) || ( $userId != $myId ) ) ) {
						$access			=	true;
					}
					break;
				case 7:
					if ( Application::MyUser()->isModeratorFor( Application::User( (int) $userId ) ) && ( ( ( $userId == $myId ) && ( ! $excludeSelf ) ) || ( $userId != $myId ) ) ) {
						$access			=	true;
					}
					break;
				case 6:
					if ( $userId != $myId ) {
						$cbConnection	=	new cbConnection( $userId );

						if ( $cbConnection->getConnectionDetails( $userId, $myId ) !== false ) {
							$access		=	true;
						}
					} else if ( ( $userId == $myId ) && $includeSelf ) {
						$access			=	true;
					}
					break;
				case 5:
					if ( ( $myId == 0 ) && ( $userId != $myId ) || ( ( $userId == $myId ) && $includeSelf ) ) {
						$access			=	true;
					}
					break;
				case 4:
					if ( ( $myId > 0 ) && ( ( ( $userId == $myId ) && ( ! $excludeSelf ) ) || ( $userId != $myId ) ) ) {
						$access			=	true;
					}
					break;
				case 3:
					if ( $userId != $myId ) {
						$access			=	true;
					}
					break;
				case 2:
					if ( $userId == $myId ) {
						$access			=	true;
					}
					break;
				case 1:
				default:
					if ( ( ( $userId == $myId ) && ( ! $excludeSelf ) ) || ( $userId != $myId ) ) {
						$access			=	true;
					}
					break;
			}

			$cache[$cacheId]			=	$access;
		}

		$canAccess						=	$cache[$cacheId];

		if ( $canAccess && $incrementDelay ) {
			$query						=	'SELECT ' . $_CB_database->NameQuote( 'date' )
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_ratings' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'field' )
										.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " = " . $fieldId
										.	"\n AND " . $_CB_database->NameQuote( 'target' ) . " = " . $userId
										.	"\n AND " . $_CB_database->NameQuote( 'user_id' ) . " = " . $myId;
			if ( $myId == 0 ) {
				$query					.=	"\n AND " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
			}
			$query						.=	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
			$_CB_database->setQuery( $query, 0, 1 );
			$incrementDate				=	$_CB_database->loadResult();

			if ( $incrementDate ) {
				if ( $incrementDelay == 'FOREVER' ) {
					$canAccess			=	false;
				} elseif ( $incrementDelay == 'CUSTOM' ) {
					if ( $customDelay && ( $_CB_framework->getUTCTimestamp( strtoupper( $customDelay ), $_CB_framework->getUTCTimestamp( $incrementDate ) ) >= $_CB_framework->getUTCNow() ) ) {
						$canAccess		=	false;
					}
				} elseif ( $_CB_framework->getUTCTimestamp( $incrementDelay, $_CB_framework->getUTCTimestamp( $incrementDate ) ) >= $_CB_framework->getUTCNow() ) {
					$canAccess			=	false;
				}
			}
		}

		return $canAccess;
	}

	/**
	 * output points field html display
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $reason
	 * @param  boolean     $ajax
	 * @return string
	 */
	private function getPointsHTML( &$field, &$user, $reason, $ajax = false )
	{
		global $_CB_framework;

		static $JS_loaded				=	0;

		$userId							=	(int) $user->get( 'id' );
		$fieldName						=	$field->get( 'name' );
		$value							=	(int) $user->get( $fieldName );

		$readOnly						=	$this->_isReadOnly( $field, $user, $reason );

		$maxPoints						=	(int) $field->params->get( 'integer_maximum', '1000000' );
		$pointsLayout					=	$field->params->get( 'points_layout', '' );
		$userlistIncrement				=	(int) $field->params->get( 'points_list', 0 );
		$userlistAccess					=	false;

		if ( $reason == 'list' ) {
			$fieldName					=	$fieldName . $userId;

			if ( $userlistIncrement ) {
				$userlistAccess			=	true;
			}
		}


		$canIncrement					=	( ( ! $readOnly ) && $this->getIncrementAccess( $field, $user ) && ( ( ( $reason == 'list' ) && $userlistAccess ) || ( $reason != 'list' ) ) );

		if ( $canIncrement ) {
			$plusCSS					=	$field->params->get( 'points_plus_class', '' );
			$minusCSS					=	$field->params->get( 'points_minus_class', '' );

			$plusIcon					=	'<span class="' . ( $plusCSS ? htmlspecialchars( $plusCSS ) : 'fa fa-plus-circle fa-lg' ) . '"></span>';
			$minusIcon					=	'<span class="' . ( $minusCSS ? htmlspecialchars( $minusCSS ) : 'fa fa-minus-circle fa-lg' ) . '"></span>';

			$replace					=	array(	'[plus]' => ( $value < $maxPoints ? '<span class="cbPointsFieldIncrement cbPointsFieldIncrementPlus" data-value="plus" data-field="' . $field->get( 'name' ) . '" data-target="' . $userId . '">' . $plusIcon . '</span>' : null ),
													'[minus]' => ( $value > 0 ? '<span class="cbPointsFieldIncrement cbPointsFieldIncrementMinus" data-value="minus" data-field="' . $field->get( 'name' ) . '" data-target="' . $userId . '">' . $minusIcon . '</span>' : null ),
													'[value]' => '<span class="cbPointsFieldValue">' . $value . '</span>',
												);

			if ( $pointsLayout ) {
				$pointsLayout			=	CBTxt::Th( $pointsLayout, null, $replace );
			} else {
				$pointsLayout			=	CBTxt::Th( 'POINTS_FIELD_LAYOUT_VALUE_PLUS_MINUS', '[value] [plus] [minus]', $replace );
			}

			if ( $ajax ) {
				$return					=	$pointsLayout;
			} else {
				$return					=	'<span id="' . $fieldName . 'Container" class="cbPointsField' . ( ! in_array( $reason, array( 'edit', 'register' ) ) ? ' cbPointsFieldToggle' : null ) . ( $userlistAccess ? ' cbClicksInside' : null ) . '">'
										.		$pointsLayout
										.	'</span>';

				if ( ! in_array( $reason, array( 'edit', 'register' ) ) ) {
					if ( ! $JS_loaded++ ) {
							$js				=	"$( '.cbPointsFieldToggle' ).on( 'click', '.cbPointsFieldIncrement', function ( e ) {"
											.		"var points = $( this ).parents( '.cbPointsField' );"
											.		"var increment = $( this ).data( 'value' );"
											.		"var field = $( this ).data( 'field' );"
											.		"var target = $( this ).data( 'target' );"
											.		"$.ajax({"
											.			"type: 'POST',"
											.			"url: '" . addslashes( cbSef( 'index.php?option=com_comprofiler&view=fieldclass&function=savevalue&reason=' . urlencode( $reason ), false, 'raw' ) ) . "',"
											.			"headers: {"
											.				"'X-CSRF-Token': " . json_encode( Application::Session()->getFormTokenName() )
											.			"},"
											.			"data: {"
											.				"field: field,"
											.				"user: target,"
											.				"value: increment"
											.			"}"
											.		"}).done( function( data, textStatus, jqXHR ) {"
											.			"points.html( data );"
											.		"});"
											.	"});";

						$_CB_framework->outputCbJQuery( $js );
					}
				}
			}
		} else {
			$return						=	parent::getField( $field, $user, 'html', $reason, 0 );
		}

		return $return;
	}

	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check well for the $reason ...
	 *
	 * @param FieldTable     $field
	 * @param null|UserTable $user
	 * @param array          $postdata
	 * @param string         $reason 'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches (always public!)
	 * @return string                  Expected output.
	 */
	public function fieldClass( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_framework, $_CB_database;

		parent::fieldClass( $field, $user, $postdata, $reason ); // Performs spoof check

		if ( ! $user ) {
			return null;
		}

		$userId							=	(int) $user->get( 'id' );

		if ( ( ! in_array( $reason, array( 'profile', 'list' ) ) ) || ( cbGetParam( $_GET, 'function', '' ) != 'savevalue' ) || ( ! $userId ) || $this->_isReadOnly( $field, $user, $reason ) || ( ! $this->getIncrementAccess( $field, $user ) ) ) {
			return null; // wrong reason, wrong function, user doesn't exist, field is read only, or user has no increment access; do nothing
		}

		$myId							=	(int) $_CB_framework->myId();
		$fieldId						=	(int) $field->get( 'fieldid' );
		$ipAddress						=	$this->getInput()->getRequestIP();
		$fieldName						=	$field->get( 'name' );

		$direction						=	stripslashes( cbGetParam( $postdata, 'value' ) );
		$value							=	(int) $user->get( $fieldName );

		if ( $direction == 'plus' ) {
			$increment					=	(int) $field->params->get( 'points_inc_plus', 1 );
			$value						+=	( $increment && ( $increment > 0 ) ? $increment : 0 );
		} elseif ( $direction == 'minus' ) {
			$increment					=	(int) $field->params->get( 'points_inc_minus', 1 );
			$value						-=	( $increment && ( $increment > 0 ) ? $increment : 0 );
			$increment					=	( $increment ? -$increment : 0 );
		} else {
			$increment					=	0;
		}

		$postdata[$fieldName]			=	$value;

		if ( $this->validate( $field, $user, $fieldName, $value, $postdata, $reason ) && $increment && ( (int) $user->get( $fieldName ) != $value ) ) {
			$query						=	'INSERT INTO ' . $_CB_database->NameQuote( '#__comprofiler_ratings' )
										.	"\n ("
										.		$_CB_database->NameQuote( 'user_id' )
										.		', ' . $_CB_database->NameQuote( 'type' )
										.		', ' . $_CB_database->NameQuote( 'item' )
										.		', ' . $_CB_database->NameQuote( 'target' )
										.		', ' . $_CB_database->NameQuote( 'rating' )
										.		', ' . $_CB_database->NameQuote( 'ip_address' )
										.		', ' . $_CB_database->NameQuote( 'date' )
										.	')'
										.	"\n VALUES ("
										.		$myId
										.		', ' . $_CB_database->Quote( 'field' )
										.		', ' . $fieldId
										.		', ' . $userId
										.		', ' . (float) $increment
										.		', ' . $_CB_database->Quote( $ipAddress )
										.		', ' . $_CB_database->Quote( $_CB_framework->getUTCDate() )
										.	')';
			$_CB_database->setQuery( $query );
			$_CB_database->query();

			if ( $user->storeDatabaseValue( $fieldName, (int) $value ) ) {
				$this->_logFieldUpdate( $field, $user, $reason, (int) $user->get( $fieldName ), (int) $value );

				$user->set( $fieldName, (int) $value );
			}
		}

		return $this->getPointsHTML( $field, $user, $reason, true );
	}

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
		$return					=	null;

		switch ( $output ) {
			case 'html':
				$return			=	$this->formatFieldValueLayout( $this->getPointsHTML( $field, $user, $reason ), $reason, $field, $user );
				break;
			case 'htmledit':
				if ( ( $reason == 'search' ) || $this->getIncrementAccess( $field, $user ) ) {
					$return		=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				}
				break;
			default:
				$return			=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}

		return $return;
	}
}