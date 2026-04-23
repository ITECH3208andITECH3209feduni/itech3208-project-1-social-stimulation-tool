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
use cbFieldHandler;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use cbSqlQueryPart;

\defined( 'CBLIB' ) or die();

class RatingField extends cbFieldHandler
{
	/**
	 * Checks if user has vote access to this field
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  null|int    $myId
	 * @return boolean
	 */
	private function getVoteAccess( &$field, &$user, $myId = null )
	{
		global $_CB_framework;

		static $cache					=	array();

		if ( $myId === null ) {
			$myId						=	(int) $_CB_framework->myId();
		} else {
			$myId						=	(int) $myId;
		}

		$userId							=	(int) $user->get( 'id' );
		$fieldId						=	(int) $field->get( 'fieldid' );

		$cacheId						=	$myId . $userId . $fieldId;

		if ( ! isset( $cache[$cacheId] ) ) {
			$ratingAccess				=	(int) $field->params->get( 'rating_access', 1 );
			$excludeSelf				=	(int) $field->params->get( 'rating_access_exclude', 0 );
			$includeSelf				=	(int) $field->params->get( 'rating_access_include', 0 );
			$viewAccessLevel			=	(int) $field->params->get( 'rating_access_custom', 1 );
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
					} elseif ( ( $userId == $myId ) && $includeSelf ) {
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

		return $cache[$cacheId];
	}

	/**
	 * Get viewing users current vote
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  null|int    $myId
	 * @return int
	 */
	private function getCurrentVote( &$field, &$user, $myId = null )
	{
		global $_CB_database, $_CB_framework;

		static $cache				=	array();

		if ( $myId === null ) {
			$myId					=	(int) $_CB_framework->myId();
		} else {
			$myId					=	(int) $myId;
		}

		$userId						=	(int) $user->get( 'id' );
		$fieldId					=	(int) $field->get( 'fieldid' );
		$ipAddress					=	$this->getInput()->getRequestIP();

		$cacheId					=	md5( ( $myId == 0 ? $ipAddress : $myId ) . $userId . $fieldId );

		if ( ! isset( $cache[$cacheId] ) ) {
			$query					=	'SELECT ' . $_CB_database->NameQuote( 'rating' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_ratings' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'field' )
									.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " = " . $fieldId
									.	"\n AND " . $_CB_database->NameQuote( 'target' ) . " = " . $userId
									.	"\n AND " . $_CB_database->NameQuote( 'user_id' ) . " = " . $myId;
			if ( $myId == 0 ) {
				$query				.=	"\n AND " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
			}
			$_CB_database->setQuery( $query );
			$cache[$cacheId]		=	$_CB_database->loadResult();
		}

		return $cache[$cacheId];
	}

	/**
	 * Inserts a new vote into the database
	 *
	 * @param  float       $value
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  null|int    $myId
	 * @return float
	 */
	private function insertVote( $value, &$field, &$user, $myId = null )
	{
		global $_CB_database, $_CB_framework;

		if ( $myId === null ) {
			$myId			=	(int) $_CB_framework->myId();
		} else {
			$myId			=	(int) $myId;
		}

		$userId				=	(int) $user->get( 'id' );
		$fieldId			=	(int) $field->get( 'fieldid' );
		$ipAddress			=	$this->getInput()->getRequestIP();

		if ( ! $value ) {
			$query			=	'DELETE'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_ratings' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'field' )
							.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " = " . $fieldId
							.	"\n AND " . $_CB_database->NameQuote( 'target' ) . " = " . $userId
							.	"\n AND " . $_CB_database->NameQuote( 'user_id' ) . " = " . $myId;
			if ( $myId == 0 ) {
				$query		.=	"\n AND " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
			}
			$_CB_database->setQuery( $query );
			$_CB_database->query();
		} else {
			$query			=	'SELECT ' . $_CB_database->NameQuote( 'id' )
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_ratings' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'field' )
							.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " = " . $fieldId
							.	"\n AND " . $_CB_database->NameQuote( 'target' ) . " = " . $userId
							.	"\n AND " . $_CB_database->NameQuote( 'user_id' ) . " = " . $myId;
			if ( $myId == 0 ) {
				$query		.=	"\n AND " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
			}
			$_CB_database->setQuery( $query );
			$ratingId		=	$_CB_database->loadResult();

			if ( $ratingId ) {
				$query		=	'UPDATE ' . $_CB_database->NameQuote( '#__comprofiler_ratings' )
							.	"\n SET " . $_CB_database->NameQuote( 'rating' ) . " = " . (float) $value
							.	', ' . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress )
							.	', ' . $_CB_database->NameQuote( 'date' ) . ' = ' . $_CB_database->Quote( $_CB_framework->getUTCDate() )
							.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $ratingId;
				$_CB_database->setQuery( $query );
				$_CB_database->query();
			} else {
				$query		=	'INSERT INTO ' . $_CB_database->NameQuote( '#__comprofiler_ratings' )
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
							.		', ' . (float) $value
							.		', ' . $_CB_database->Quote( $ipAddress )
							.		', ' . $_CB_database->Quote( $_CB_framework->getUTCDate() )
							.	')';
				$_CB_database->setQuery( $query );
				$_CB_database->query();
			}
		}

		$query				=	'SELECT ROUND( AVG( ' . $_CB_database->NameQuote( 'rating' ) . ' ), 1 )'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_ratings' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'field' )
							.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " = " . $fieldId
							.	"\n AND " . $_CB_database->NameQuote( 'target' ) . " = " . $userId;
		$_CB_database->setQuery( $query );

		return $_CB_database->loadResult();
	}

	/**
	 * Get the number of a fields votes
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @return mixed
	 */
	private function getVoteCount( &$field, &$user )
	{
		global $_CB_database;

		static $cache				=	array();

		$userId						=	(int) $user->get( 'id' );
		$fieldId					=	(int) $field->get( 'fieldid' );

		$cacheId					=	$userId . $fieldId;

		if ( ! isset( $cache[$cacheId] ) ) {
			$query					=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_ratings' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'field' )
									.	"\n AND " . $_CB_database->NameQuote( 'item' ) . " = " . $fieldId
									.	"\n AND " . $_CB_database->NameQuote( 'target' ) . " = " . $userId;
			$_CB_database->setQuery( $query );
			$cache[$cacheId]		=	(int) $_CB_database->loadResult();
		}

		return $cache[$cacheId];
	}

	/**
	 * output rating field html display
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $reason
	 * @return string
	 */
	private function getRatingHTML( &$field, &$user, $reason )
	{
		global $_CB_framework;

		static $JS_loaded			=	0;

		$userId						=	(int) $user->get( 'id' );
		$fieldName					=	$field->get( 'name' );

		if ( in_array( $reason, array( 'edit', 'register' ) ) && ( (int) $_CB_framework->myId() != $userId ) ) {
			$myId					=	$userId;
		} else {
			$myId					=	null;
		}

		$value						=	(float) $user->get( $fieldName );

		$readOnly					=	$this->_isReadOnly( $field, $user, $reason );
		$required					=	$this->_isRequired( $field, $user, $reason );

		$maxRating					=	(int) $field->params->get( 'rating_number', 5 );
		$voteCount					=	(int) $field->params->get( 'rating_votes', 0 );
		$voteNumerical				=	(int) $field->params->get( 'rating_numerical', 0 );
		$ratingStep					=	(float) number_format( $field->params->get( 'rating_step', '1.0' ), 1, '.', '' );
		$forceWhole					=	(int) $field->params->get( 'rating_whole', 0 );
		$userlistVote				=	(int) $field->params->get( 'rating_list', 0 );
		$userlistAccess				=	false;

		if ( ! $ratingStep ) {
			$ratingStep				=	(float) '1.0';
		}

		if ( $reason == 'list' ) {
			$fieldName				=	$fieldName . $userId;

			if ( $userlistVote ) {
				$userlistAccess		=	true;
			}
		}

		$canVote					=	( ( ! $readOnly ) && $this->getVoteAccess( $field, $user, $myId ) && ( ( ( $reason == 'list' ) && $userlistAccess ) || ( $reason != 'list' ) ) );

		if ( $forceWhole ) {
			$value					=	(float) round( $value );
		}

		if ( $value > $maxRating ) {
			$value					=	(float) $maxRating;
		} elseif ( $value < 0 ) {
			$value					=	(float) '0';
		}

		$return						=	null;

		if ( ( ! in_array( $reason, array( 'edit', 'register' ) ) ) && ( $value || ( ( ! $value ) && ( ! $canVote ) ) ) ) {
			$return					.=		'<div id="' . $fieldName . 'Total" class="cbRatingFieldTotal">'
									.			'<div class="rateit" data-rateit-value="' . $value . '" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-min="0" data-rateit-max="' . $maxRating . '" data-rateit-mode="font"></div>';

			if ( $voteNumerical && $value ) {
				$return				.=			' <span class="cbRatingFieldNumerical" title="' . htmlspecialchars( CBTxt::T( 'Rating' ) ) . '"><small>(' . $value . ')</small></span>';
			}

			if ( $voteCount ) {
				$count				=	$this->getVoteCount( $field, $user );

				if ( $count ) {
					$return			.=			' <span class="cbRatingFieldCount" title="' . htmlspecialchars( CBTxt::T( 'Number of Votes' ) ) . '"><small>(' . $count . ')</small></span>';
				}
			}

			$return					.=		'</div>';
		}

		if ( in_array( $reason, array( 'edit', 'register' ) ) && ( (int) $_CB_framework->myId() != $userId ) ) {
			$myId					=	$userId;
		} else {
			$myId					=	null;
		}

		if ( $canVote ) {
			$rating					=	(float) $this->getCurrentVote( $field, $user, $myId );

			if ( $rating > $maxRating ) {
				$rating				=	(float) $maxRating;
			} elseif ( $rating < 0 ) {
				$rating				=	(float) '0';
			}

			$return					.=		'<div id="' . $fieldName . 'Rating" class="cbRatingFieldRating">'
									.			'<input type="hidden" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $rating . '" />'
									.			'<div class="rateit" data-field="' . $field->get( 'name' ) . '" data-target="' . $userId . '" data-rateit-backingfld="#' . $fieldName . '" data-rateit-step="' . $ratingStep . '" data-rateit-value="' . $rating . '" data-rateit-ispreset="true" data-rateit-resetable="' . ( $required ? 'false' : 'true' ) . '" data-rateit-min="0" data-rateit-max="' . $maxRating . '" data-rateit-mode="font"></div>'
									.		'</div>';
		}

		if ( $return ) {
			$return					=	'<div id="' . $fieldName . 'Container" class="cbRatingField' . ( ! in_array( $reason, array( 'edit', 'register' ) ) ? ' cbRatingFieldToggle' : null ) . ( $userlistAccess ? ' cbClicksInside' : null ) . '">'
									.		$return
									.	'</div>';
		}

		$js							=	null;

		if ( ! in_array( $reason, array( 'edit', 'register' ) ) ) {
			if ( ! $JS_loaded++ ) {
				$js					=	"$( '.cbRatingFieldToggle' ).on( 'rated reset', '.rateit', function ( e ) {"
									.		"var rating = $( this ).parents( '.cbRatingField' );"
									.		"var vote = $( this ).rateit( 'value' );"
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
									.				"value: vote"
									.			"}"
									.		"}).done( function( data, textStatus, jqXHR ) {"
									.			"rating.find( '.cbRatingFieldTotal,.alert' ).remove();"
									.			"rating.prepend( data );"
									.			"rating.find( '.cbRatingFieldTotal .rateit' ).rateit();"
									.		"});"
									.	"});";
			}
		}

		// Still need the plugin loaded so the rating stars get styled:
		$_CB_framework->outputCbJQuery( $js, 'rateit' );

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
		parent::fieldClass( $field, $user, $postdata, $reason ); // Performs spoof check

		if ( ! $user ) {
			return null;
		}

		$userId							=	(int) $user->get( 'id' );

		if ( ( ! in_array( $reason, array( 'profile', 'list' ) ) ) || ( cbGetParam( $_GET, 'function', '' ) != 'savevalue' ) || ( ! $userId ) || $this->_isReadOnly( $field, $user, $reason ) || ( ! $this->getVoteAccess( $field, $user ) ) ) {
			return null; // wrong reason, wrong function, user doesn't exist, field is read only, or user has no vote access; do nothing
		}

		$fieldName						=	$field->get( 'name' );
		$maxRating						=	(int) $field->params->get( 'rating_number', 5 );
		$voteCount						=	(int) $field->params->get( 'rating_votes', 0 );
		$voteNumerical					=	(int) $field->params->get( 'rating_numerical', 0 );
		$forceWhole						=	(int) $field->params->get( 'rating_whole', 0 );
		$value							=	(float) stripslashes( cbGetParam( $postdata, 'value' ) );

		if ( $value > $maxRating ) {
			$value						=	(float) $maxRating;
		} elseif ( $value < 0 ) {
			$value						=	(float) '0';
		}

		$postdata[$fieldName]			=	$value;

		if ( $this->validate( $field, $user, $fieldName, $value, $postdata, $reason ) && ( (float) $this->getCurrentVote( $field, $user ) !== (float) $value ) ) {
			$value						=	(float) $this->insertVote( $value, $field, $user );

			if ( $user->storeDatabaseValue( $fieldName, $value ) ) {
				$this->_logFieldUpdate( $field, $user, $reason, (float) $user->get( $fieldName ), $value );

				$user->set( $fieldName, $value );
			}
		}

		$value							=	(float) $user->get( $fieldName );

		if ( $reason == 'list' ) {
			$fieldName					=	$fieldName . $userId;
		}

		if ( $forceWhole ) {
			$value						=	(float) round( $value );
		}

		if ( $value > $maxRating ) {
			$value						=	(float) $maxRating;
		} elseif ( $value < 0 ) {
			$value						=	(float) '0';
		}

		$return							=	null;

		if ( $value ) {
			$return						.=	'<div id="' . $fieldName . 'Total" class="cbRatingFieldTotal">'
										.		'<div class="rateit" data-rateit-value="' . $value . '" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-min="0" data-rateit-max="' . $maxRating . '" data-rateit-mode="font"></div>';

			if ( $voteNumerical && $value ) {
				$return					.=		' <span class="cbRatingFieldNumerical" title="' . htmlspecialchars( CBTxt::T( 'Rating' ) ) . '"><small>(' . $value . ')</small></span>';
			}

			if ( $voteCount ) {
				$count					=	$this->getVoteCount( $field, $user );

				if ( $count ) {
					$return				.=		' <span class="cbRatingFieldCount" title="' . htmlspecialchars( CBTxt::T( 'Number of Votes' ) ) . '"><small>(' . $count . ')</small></span>';
				}
			}

			$return						.=	'</div>';
		}

		return $return;
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
		$return						=	null;

		switch ( $output ) {
			case 'html':
			case 'htmledit':
				if ( $reason == 'search' ) {
					$fieldName		=	$field->get( 'name' );
					$minNam			=	$fieldName . '__minval';
					$maxNam			=	$fieldName . '__maxval';

					$minVal			=	$user->get( $minNam );
					$maxVal			=	$user->get( $maxNam );

					$field->set( 'name', $minNam );

					$minHtml		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $minVal, null );

					$field->set( 'name', $maxNam );

					$maxHtml		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $maxVal, null );

					$field->set( 'name', $fieldName );

					$return			=	$this->_fieldSearchRangeModeHtml( $field, $user, $output, $reason, null, $minHtml, $maxHtml, $list_compare_types );
				} else {
					$return			=	$this->formatFieldValueLayout( $this->getRatingHTML( $field, $user, $reason ), $reason, $field, $user );
				}
				break;
			default:
				$return				=	parent::getField( $field, $user, $output, $reason, $list_compare_types );
				break;
		}

		return $return;
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
		$maxRating				=	(int) $field->params->get( 'rating_number', 5 );

		$this->_prepareFieldMetaSave( $field, $user, $postdata, $reason );

		foreach ( $field->getTableColumns() as $col ) {
			$value				=	cbGetParam( $postdata, $col );

			if ( ( $value !== null ) && ( ! is_array( $value ) ) ) {
				$value			=	(float) stripslashes( $value );

				if ( $value > $maxRating ) {
					$value		=	(float) $maxRating;
				} elseif ( $value < 0 ) {
					$value		=	(float) '0';
				}

				$this->validate( $field, $user, $col, $value, $postdata, $reason );
			}
		}
	}

	/**
	 * Mutator:
	 * Prepares field data commit
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function commitFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_framework;

		$maxRating				=	(int) $field->params->get( 'rating_number', 5 );

		foreach ( $field->getTableColumns() as $col ) {
			$value				=	cbGetParam( $postdata, $col );

			if ( ( $value !== null ) && ( ! is_array( $value ) ) ) {
				$value			=	(float) stripslashes( $value );

				if ( $value > $maxRating ) {
					$value		=	(float) $maxRating;
				} elseif ( $value < 0 ) {
					$value		=	(float) '0';
				}

				if ( $this->validate( $field, $user, $col, $value, $postdata, $reason ) && ( (float) $this->getCurrentVote( $field, $user ) !== (float) $value ) ) {
					$userId		=	(int) $user->get( 'id' );

					if ( in_array( $reason, array( 'edit', 'register' ) ) && ( (int) $_CB_framework->myId() != $userId ) ) {
						$myId	=	$userId;
					} else {
						$myId	=	null;
					}

					$rating		=	(float) $this->insertVote( $value, $field, $user, $myId );

					$this->_logFieldUpdate( $field, $user, $reason, $user->get( $col ), $rating );

					$user->set( $col, $rating );
				}
			}
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
	public function bindSearchCriteria( &$field, &$searchVals, &$postdata, $list_compare_types, $reason )
	{
		$query								=	array();

		foreach ( $field->getTableColumns() as $col ) {
			$minNam							=	$col . '__minval';
			$maxNam							=	$col . '__maxval';
			$searchMode						=	$this->_bindSearchRangeMode( $field, $searchVals, $postdata, $minNam, $maxNam, $list_compare_types );

			if ( $searchMode ) {
				$minVal						=	(float) cbGetParam( $postdata, $minNam, 0 );
				$maxVal						=	(float) cbGetParam( $postdata, $maxNam, 0 );

				if ( $minVal && ( cbGetParam( $postdata, $minNam, '' ) !== '' ) ) {
					$searchVals->$minNam	=	$minVal;
					$operator				=	( $searchMode == 'isnot' ? ( $minVal == $maxVal ? '<' : '<=' ) : '>=' );
					$min					=	$this->_floatToSql( $field, $col, $minVal, $operator, $searchMode );
				} else {
					$min					=	null;
				}

				if ( $maxVal && ( cbGetParam( $postdata, $maxNam, '' ) !== '' ) ) {
					$searchVals->$maxNam	=	$maxVal;
					$operator				=	( $searchMode == 'isnot' ? ( $maxVal == $minVal ? '>' : '>=' ) : '<=' );
					$max					=	$this->_floatToSql( $field, $col, $maxVal, $operator, $searchMode );
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
		}

		return $query;
	}

	/**
	 * Internal function to create an SQL query part based on a comparison operator
	 *
	 * @param  FieldTable      $field
	 * @param  string          $col
	 * @param  int             $value
	 * @param  string          $operator
	 * @param  string          $searchMode
	 * @return cbSqlQueryPart
	 */
	protected function _floatToSql( &$field, $col, $value, $operator, $searchMode )
	{
		$value				=	(float) $value;

		$sql				=	new cbSqlQueryPart();
		$sql->tag			=	'column';
		$sql->name			=	$col;
		$sql->table			=	$field->table;
		$sql->type			=	'sql:field';
		$sql->operator		=	$operator;
		$sql->value			=	$value;
		$sql->valuetype		=	'const:float';
		$sql->searchmode	=	$searchMode;

		return $sql;
	}
}