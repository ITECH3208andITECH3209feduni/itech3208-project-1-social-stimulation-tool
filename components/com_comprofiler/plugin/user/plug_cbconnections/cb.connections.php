<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;

/** ensure this file is being included by a parent file */
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) {	die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;
$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'userDeleted', 'getConnectionTab' );

/**
 * Connections Tab Class for handling the Shortest Connections Path CB tab in head by default (other parts are still in core CB)
 * @package Community Builder
 * @subpackage Connections CB core module
 * @author JoomlaJoe and Beat
 */
class getConnectionPathsTab extends cbTabHandler
{
	/**
	 * Constructor
	 */
	public function __construct( )
	{
		parent::__construct();
	}

	/**
	 * Generates the HTML to display the user profile tab
	 *
	 * @param  \CB\Database\Table\TabTable   $tab       the tab database entry
	 * @param  \CB\Database\Table\UserTable  $user      the user being displayed
	 * @param  int                           $ui        1 for front-end, 2 for back-end
	 * @return string|boolean                           Either string HTML for tab content, or false if ErrorMSG generated
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_framework, $_CB_database, $ueConfig;

		$return								=	null;

		if ( ( $_CB_framework->myId() != $user->id ) && ( $_CB_framework->myId() > 0 ) && ( isset( $ueConfig['connectionPath'] ) && $ueConfig['connectionPath'] ) && $ueConfig['allowConnections'] ) {
			$myCBUser						=	CBuser::getInstance( (int) $user->id, false );
			$myName							=	$myCBUser->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );
			$myAvatar						=	$myCBUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldProfileLink' => false ) ) );

			$i								=	0;
			$cbCon							=	new cbConnection( $_CB_framework->myId() );
			$conGroups						=	$cbCon->getDegreeOfSepPath( $_CB_framework->myId(), $user->id );
			$directConDetails				=	$cbCon->getConnectionDetails( $_CB_framework->myId(), $user->id );

			$addConnURL						=	$_CB_framework->viewUrl( 'addconnection', true, array( 'connectionid' => (int) $user->id ) );
			$removeConnURL					=	$_CB_framework->viewUrl( 'removeconnection', true, array( 'connectionid' => (int) $user->id ) );
			$acceptConnURL					=	$_CB_framework->viewUrl( 'acceptconnection', true, array( 'connectionid' => (int) $user->id ) );
			$denyConnURL					=	$_CB_framework->viewUrl( 'denyconnection', true, array( 'connectionid' => (int) $user->id ) );

			if ( $ueConfig['conNotifyType'] != 0 ) {
				cbValidator::loadValidation();

				$tooltipTitle				=	sprintf( CBTxt::T( 'UE_CONNECTTO', 'Connect to %s'), $myName );

				$ooltipHTML					=	'<div class="form-group row no-gutters cb_form_line">'
											.		CBTxt::Th( 'UE_CONNECTIONINVITATIONMSG', 'Personalize your invitation to connect by adding a message that will be included with your connection.' )
											.	'</div>'
											.	'<form action="' . $addConnURL . '" method="post" id="connOverForm" name="connOverForm" class="cb_form m-0 cbValidation cbConnReqForm">'
											.		'<div class="form-group row no-gutters cbft_textarea cbtt_group cb_form_line">'
											.			'<div class="cb_field col-12">'
											.				'<textarea cols="40" rows="8" name="message" class="form-control input-block"></textarea>'
											.			'</div>'
											.		'</div>'
											.		'<div class="row no-gutters cb_form_line cbConnReqButtons">'
											.			'<div class="col-12">'
											.				'<input type="submit" class="btn btn-primary btn-sm-block cbConnReqSubmit" value="' . htmlspecialchars( CBTxt::Th( 'UE_SENDCONNECTIONREQUEST', 'Request Connection' ) ) . '"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
											.				' <input type="button" id="cbConnReqCancel" class="btn btn-secondary btn-sm-block cbConnReqCancel cbTooltipClose" value="' . htmlspecialchars( CBTxt::Th( 'UE_CANCELCONNECTIONREQUEST', 'Cancel' ) ) . '" />'
											.			'</div>'
											.		'</div>'
											.	'</form>';

				$tooltip					=	cbTooltip( $ui, $ooltipHTML, $tooltipTitle, 800, null, null, null, 'data-hascbtooltip="true" data-cbtooltip-modal="true"' );
			} else {
				$tooltip					=	null;
			}

			$connected						=	'<div class="row no-gutters alert alert-info cbConnectionPaths cbConnectionPathsConnected">'
											.		CBTxt::Th( 'CONNECTIONS_YOU_ARE_DIRECTLY_CONNECTED_WITH_USER', 'You are directly connected with [user]', array( '[user]' => $myAvatar ) )
											.	'</div>';

			$requestConnection				=	'<div class="row no-gutters alert alert-info align-items-center cbConnectionPaths cbConnectionPathsRequest">'
											.		'<div class="cbConnPathMessage col-sm-8">'
											.			CBTxt::Th( 'CONNECTIONS_YOU_HAVE_NO_CONNECTION_WITH_USER', 'You have no established connection with [user]', array( '[user]' => $myAvatar ) )
											.		'</div>'
											.		'<div class="cbConnPathActions mt-2 mt-sm-0 col-sm-4 text-right">'
											.			'<input type="button" value="' . htmlspecialchars( CBTxt::Th( 'Request Connection' ) ) . '" class="btn btn-success btn-sm-block cbConnPathAccept"' . ( $tooltip ? ' ' . $tooltip : ' onclick="location.href = \'' . addslashes( $addConnURL ) . '\';"' ) . ' />'
											.		'</div>'
											.	'</div>';

			$cancelRequest					=	'<div class="row no-gutters alert alert-info align-items-center cbConnectionPaths cbConnectionPathsCancel">'
											.		'<div class="cbConnPathMessage col-sm-8">'
											.			CBTxt::Th( 'CONNECTIONS_YOUR_CONNECTION_REQUEST_WITH_USER_IS_PENDING', 'Your connection request with [user] is pending acceptance', array( '[user]' => $myAvatar ) )
											.		'</div>'
											.		'<div class="cbConnPathActions mt-2 mt-sm-0 col-sm-4 text-right">'
											.			'<input type="button" value="' . htmlspecialchars( CBTxt::Th( 'Cancel Request' ) ) . '" class="btn btn-danger btn-sm-block cbConnPathReject" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $removeConnURL ) . '\'; })" />'
											.		'</div>'
											.	'</div>';

			$acceptDenyRequest				=	'<div class="row no-gutters alert alert-info align-items-center cbConnectionPaths cbConnectionPathsAccept">'
											.		'<div class="cbConnPathMessage col-sm-8">'
											.			CBTxt::Th( 'CONNECTIONS_THE_CONNECTION_WITH_USER_IS_PENDING_YOUR_ACCEPTANCE', 'The connection with [user] is pending your acceptance', array( '[user]' => $myAvatar ) )
											.		'</div>'
											.		'<div class="cbConnPathActions mt-2 mt-sm-0 col-sm-4 text-right">'
											.			'<input type="button" value="' . htmlspecialchars( CBTxt::Th( 'Accept Connection' ) ) . '" class="btn btn-success btn-sm-block cbConnPathAccept" onclick="window.location.href = \'' . addslashes( $acceptConnURL ) . '\';" />'
											.			' <input type="button" value="' . htmlspecialchars( CBTxt::Th( 'Reject Connection' ) ) . '" class="btn btn-secondary btn-sm-block cbConnPathReject" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $denyConnURL ) . '\'; })" />'
											.		'</div>'
											.	'</div>';

			$return							.=	$this->_writeTabDescription( $tab, $user );

			if ( is_array( $conGroups ) && ( count( $conGroups ) > 2 ) ) {
				cbArrayToInts( $conGroups );

				$query						=	"SELECT u.name, u.email, u.username, c.avatar, c.avatarapproved, u.id "
											.	"\n FROM #__comprofiler AS c"
											.	"\n LEFT JOIN #__users AS u ON c.id=u.id"
											.	"\n WHERE c.id IN (" . implode( ',', $conGroups ) . ")"
											.	"\n AND c.approved=1 AND c.confirmed=1 AND c.banned=0 AND u.block=0";
				$_CB_database->setQuery( $query );
				$connections				=	$_CB_database->loadObjectList( 'id' );

				$prevConID					=	null;
				$prevConName				=	null;

				if ( isset( $connections[$user->id] ) ) {
					$return					.=	'<div class="cbConnectionPaths cbConnectionPathsDegrees alert alert-info">'
											.		CBTxt::Th( 'CONNECTIONS_YOUR_CONNECTION_PATH_TO_USER_OF_DEGREE_IS', 'Your connection path to [user] of [degrees] degrees is ', array( '[user]' => $myAvatar, '[degrees]' => $cbCon->getDegreeOfSep() ) );

					foreach ( $conGroups as $conGroup ) {
						$cbUser				=	CBuser::getInstance( (int) $conGroup, false );

						if ( $i != 0 ) {
							$return			.=	' <span class="fa fa-chevron-right fa-sm"></span> ';
						}

						$conName			=	$cbUser->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );
						$conAvatar			=	$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldProfileLink' => false ) ) );

						if ( ( $conGroup != $_CB_framework->myId() ) && ( isset( $connections[$conGroup] ) ) ) {
							$conDetail		=	$cbCon->getConnectionDetails( $prevConID, $conGroup );

							$tipField		=	getConnectionTab::renderConnectionToolTip( $conDetail );
							$tipField		.=	'<div style="text-align: center; margin: 8px;">'
											.		$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true )
											.	'</div>';


							$tipTitle		=	$prevConName . CBTxt::T( 'UE_DETAILSABOUT', ' Details About [PERSON_NAME]', array( '[PERSON_NAME]' => htmlspecialchars( $conName ) ) );

							if ( $conGroup != $user->id ) {
								$href		=	$_CB_framework->userProfileUrl( (int) $conGroup );
							} else {
								$href		=	null;
							}

							$return			.=	cbTooltip( $ui, $tipField, $tipTitle, 300, null, $conAvatar, $href );
						} else {
							$return			.=	$conAvatar;
						}

						$i++;

						$prevConID			=	$conGroup;
						$prevConName		=	$conName;
					}

					$return					.=	'</div>';

					if ( $directConDetails !== false && $directConDetails->pending ) {
						$return				.=	$cancelRequest;
					} elseif ( ( $directConDetails !== false ) && ( ! $directConDetails->accepted ) ) {
						$return				.=	$acceptDenyRequest;
					} elseif ( $directConDetails === false ) {
						$return				.=	$requestConnection;
					}
				} else {
					$return					.=	$requestConnection;
				}
			} elseif ( is_array( $conGroups ) && ( count( $conGroups ) == 2 ) ) {
				$return						.=	$connected;
			} else {
				if ( ( $directConDetails !== false ) && $directConDetails->pending ) {
					$return					.=	$cancelRequest;
				} elseif ( ( $directConDetails !== false ) && ( ! $directConDetails->accepted ) ) {
					$return					.=	$acceptDenyRequest;
				} else {
					$return					.=	$requestConnection;
				}
			}
		}

		return $return;
	}
} // end class getConnectionPathsTab


/**
 * Connections Tab Class for handling the Connections List CB tab (other parts are still in core CB)
 * @package Community Builder
 * @subpackage Connections CB core module
 * @author JoomlaJoe and Beat
 */
class getConnectionTab extends cbTabHandler
{

	/**
	 * Generates the HTML to display the user profile tab
	 *
	 * @param  \CB\Database\Table\TabTable   $tab       the tab database entry
	 * @param  \CB\Database\Table\UserTable  $user      the user being displayed
	 * @param  int                           $ui        1 for front-end, 2 for back-end
	 * @return string|boolean                           Either string HTML for tab content, or false if ErrorMSG generated
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_framework, $_CB_database, $_CB_PMS;

		$owner										=	( Application::MyUser()->getUserId() == $user->get( 'id', 0, GetterInterface::INT ) );

		if ( ( ! Application::Config()->get( 'allowConnections', true, GetterInterface::BOOLEAN ) ) || ( ( Application::Config()->get( 'connectionDisplay', 0, GetterInterface::INT ) == 1 ) && ( ! $owner ) ) ) {
			return null;
		}

		$conShowSummary								=	$this->params->get( 'con_ShowSummary', 0, GetterInterface::INT );
		$conSummaryEntries							=	$this->params->get( 'con_SummaryEntries', 4, GetterInterface::INT );
		$conPagingEnabled							=	$this->params->get( 'con_PagingEnabled', 1, GetterInterface::INT );
		$conEntriesPerPage							=	$this->params->get( 'con_EntriesPerPage', 10, GetterInterface::INT );

		$pagingParams								=	$this->_getPaging( array(), array( 'connshow_' ) );
		$showAll									=	$this->_getReqParam( 'showall', false );

		if ( $conShowSummary && ( ! $showAll ) && ( $pagingParams['connshow_limitstart'] === null ) ) {
			$summaryMode							=	true;
			$showPaging								=	false;
			$conEntriesPerPage						=	$conSummaryEntries;
		} else {
			$summaryMode							=	false;
			$showPaging								=	$conPagingEnabled;
		}

		$isVisitor									=	null;

		if ( ! $owner ) {
			$isVisitor								=	"\n AND m." . $_CB_database->NameQuote( 'pending' ) . " = 0"
													.	"\n AND m." . $_CB_database->NameQuote( 'accepted' ) . " = 1";
		}

		//select a count of all applicable entries for pagination
		$query										=	'SELECT COUNT(*)'
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_members' ) . " AS m"
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
													.	' ON m.' . $_CB_database->NameQuote( 'memberid' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
													.	' ON m.' . $_CB_database->NameQuote( 'memberid' ) . ' = u.' . $_CB_database->NameQuote( 'id' )
													.	"\n WHERE m." . $_CB_database->NameQuote( 'referenceid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
													.	$isVisitor
													.	"\n AND c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
													.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
													.	"\n AND c." . $_CB_database->NameQuote( 'banned' ) . " = 0"
													.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0";
		$_CB_database->setQuery( $query );
		$total										=	(int) $_CB_database->loadResult();

		if ( ! $total ) {
			if ( ! Application::Config()->get( 'showEmptyTabs', true, GetterInterface::BOOLEAN ) ) {
				return null;
			} else {
				return CBTxt::Th( 'UE_NOCONNECTIONS', 'This user has no current connections.' );
			}
		}

		if ( ( ! $showPaging ) || ( $pagingParams['connshow_limitstart'] === null ) || ( $conEntriesPerPage > $total ) ) {
			$pagingParams['connshow_limitstart']	=	0;
		}

		$query										=	'SELECT m.*'
													.	', u.' . $_CB_database->NameQuote( 'name' )
													.	', u.' . $_CB_database->NameQuote( 'email' )
													.	', u.' . $_CB_database->NameQuote( 'username' )
													.	', c.' . $_CB_database->NameQuote( 'avatar' )
													.	', c.' . $_CB_database->NameQuote( 'avatarapproved' )
													.	', u.' . $_CB_database->NameQuote( 'id' )
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_members' ) . " AS m"
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
													.	' ON m.' . $_CB_database->NameQuote( 'memberid' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
													.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
													.	' ON m.' . $_CB_database->NameQuote( 'memberid' ) . ' = u.' . $_CB_database->NameQuote( 'id' )
													.	"\n WHERE m." . $_CB_database->NameQuote( 'referenceid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
													.	$isVisitor
													.	"\n AND c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
													.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
													.	"\n AND c." . $_CB_database->NameQuote( 'banned' ) . " = 0"
													.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
													.	"\n ORDER BY m." . $_CB_database->NameQuote( 'membersince' ) . " DESC, m." . $_CB_database->NameQuote( 'memberid' ) . " ASC";
		$_CB_database->setQuery( $query, ( $pagingParams['connshow_limitstart'] ? (int) $pagingParams['connshow_limitstart'] : 0 ), $conEntriesPerPage );
		$connections								=	$_CB_database->loadObjectList();

		$return										=	$this->_writeTabDescription( $tab, $user )
													.	'<div class="ml-n2 mr-n2 mb-n3 row no-gutters">';

		foreach ( $connections as $connection ) {
			$cbUser									=	CBuser::getInstance( (int) $connection->id, false );
			$avatar									=	cbTooltip( 1, getConnectionTab::renderConnectionToolTip( $connection ), CBTxt::T( 'UE_CONNECTEDDETAIL', 'Connection Details' ), 300, null, $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true ), null, 'class="d-inline-block"' );
			$buttons								=	array();

			if ( $owner ) {
				if ( $connection->pending ) {
					$buttons[]						=	'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'removeconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connection->memberid ) ) ) . '\'; })" class="h-100 btn btn-sm btn-light border btn-block">' . CBTxt::Th( 'Cancel Connection Request' ) . '</button>';
				} elseif ( ! $connection->accepted  ) {
					$buttons[]						=	'<a href="' . $_CB_framework->viewUrl( 'acceptconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connection->memberid ) ) . '" class="h-100 btn btn-sm btn-success btn-block">' . CBTxt::T( 'Accept Connection' ) . '</a>';
					$buttons[]						=	'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'denyconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connection->memberid ) ) ) . '\'; })" class="h-100 btn btn-sm btn-light border btn-block">' . CBTxt::Th( 'Reject Connection' ) . '</button>';
				} else {
					switch ( Application::Config()->get( 'allow_email_display', 3, GetterInterface::INT ) ) {
						case 1:
						case 2:
							$buttons[]				=	moscomprofilerHTML::emailCloaking( htmlspecialchars( $cbUser->getUserData()->get( 'email', null, GetterInterface::STRING ) ), 1, CBTxt::T( 'UE_SENDEMAIL', 'Send Email' ), 0, false, 'h-100 btn btn-sm btn-light border btn-block' );
							break;
						case 3:
							$buttons[]				=	'<a href="' . $_CB_framework->viewUrl( array( 'emailuser', 'uid' => (int) $connection->id ) ) . '" class="h-100 btn btn-sm btn-light border btn-block">' . CBTxt::T( 'UE_SENDEMAIL', 'Send Email' ) . '</a>';
							break;
					}

					foreach ( $_CB_PMS->getPMSlinks( (int) $connection->id, $user->get( 'id', 0, GetterInterface::INT ), null, null, 1 ) as $pmLink ) {
						if ( ! is_array( $pmLink ) ) {
							continue;
						}

						$buttons[]					=	'<a href="' . cbSef( $pmLink['url'] ) . '" class="h-100 btn btn-sm btn-light border btn-block">' . CBTxt::T( 'Send Message' ) . '</a>';
						break;
					}
				}
			}

			$return									.=		'<div class="col-12 col-md-6 col-lg-4 pb-3 pl-2 pr-2">'
													.			'<div class="h-100 card no-overflow cbCanvasLayout cbCanvasLayoutSm">'
													.				'<div class="card-header p-0 position-relative cbCanvasLayoutTop">'
													.					'<div class="position-absolute cbCanvasLayoutBackground">'
													.						$cbUser->getField( 'canvas', null, 'html', 'none', 'list', 0, true )
													.					'</div>'
													.				'</div>'
													.				'<div class="position-relative cbCanvasLayoutBottom">'
													.					'<div class="position-absolute cbCanvasLayoutPhoto">'
													.						$avatar
													.					'</div>'
													.				'</div>'
													.				'<div class="d-flex flex-column card-body p-0 position-relative cbCanvasLayoutBody">'
													.					'<div class="m-2 text-truncate cbCanvasLayoutContent">'
													.						$cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) )
													.						' <span class="text-large">' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) ) . '</span>'
													.					'</div>';

			if ( $buttons ) {
				$return								.=					'<div class="flex-grow-1 ml-1 mr-1 mb-1 mt-n1 row no-gutters cbCanvasLayoutContent">'
													.						'<div class="p-1 mw-100 col-md-6 flex-grow-1 cbCanvasLayoutContent">' . implode( '</div><div class="p-1 mw-100 col-md-6 flex-grow-1 cbCanvasLayoutContent">', $buttons ) . '</div>'
													.					'</div>';
			}

			$return									.=				'</div>'
													.			'</div>'
													.		'</div>';
		}

		$return										.=	'</div>';

		// Add paging control at end of list if paging enabled
		if ( $showPaging && ( $conEntriesPerPage < $total ) ) {
			$return									.=	'<div class="mt-3 text-center">'
													.		$this->_writePaging( $pagingParams, 'connshow_', $conEntriesPerPage, $total )
													.	'</div>';
		}

		if ( ( $conShowSummary && $owner ) || ( $summaryMode && ( $conEntriesPerPage < $total ) ) ) {
			$return									.=	'<div class="row no-gutters mt-3 connSummaryFooter">';

			if ( $owner ) {
				// Manage connections link:
				$return								.=		'<div class="col-6 connSummaryFooterManage">'
													.			'<a href="' . $_CB_framework->viewUrl( 'manageconnections' ) . '" >[' . CBTxt::Th( 'UE_MANAGECONNECTIONS', 'Manage Connections' ) . ']</a>'
													.		'</div>';
			}

			if ( $summaryMode && ( $conEntriesPerPage < $total ) ) {
				// See all of user's ## connections
				$return								.=		'<div class="col-6 text-right connSummaryFooterSeeConnections">'
													.			'<a href="' . $this->_getAbsURLwithParam( array( 'showall' => 1 ) ) . '">';

				if ( $owner ) {
					$return							.=				sprintf( CBTxt::Th( 'UE_SEEALLNCONNECTIONS', 'See all %s connections' ), $total );
				} else {
					$return							.=				sprintf( CBTxt::Th( 'UE_SEEALLOFUSERSNCONNECTIONS', 'See all of %s\'s %s connections' ), $user->getFormattedName(), "<strong>" . $total . "</strong>" );
				}

				$return								.=			'</a>'
													.		'</div>';
			}

			$return									.=	'</div>';
		}

		return $return;
	}

	/**
	 * Renders the tooltip for a connection
	 *
	 * @param  \CB\Database\Table\MemberTable  $connection  Connection to render field tip for
	 * @return string                                       HTML for the description of the connection
	 */
	public static function renderConnectionToolTip( $connection )
	{
		$tipField		=	CBTxt::Th( 'CONNECTION_TIP_CONNECTED_SINCE_CONNECTION_DATE', 'Connected Since [CONNECTION_DATE]', array( '[CONNECTION_DATE]' => cbFormatDate( $connection->membersince, true, false ) ) );

		if ( $connection->type != null ) {
			$tipField	.=	'<br />' . CBTxt::Th( 'CONNECTION_TIP_TYPES_LIST', '{1} Type: [CONNECTIONS_TYPES]|]1,Inf] Types: [CONNECTIONS_TYPES]|%%COUNT%%', array( '%%COUNT%%' => count( explode( "|*|", $connection->type ) ), '[CONNECTIONS_TYPES]' => getConnectionTypes( $connection->type ) ) );
		}

		if ( $connection->description != null ) {
			$tipField	.=	'<br />' . CBTxt::Th( 'CONNECTION_TIP_CONNECTION_COMMENT', 'Comment: [CONNECTION_DESCRIPTION]', array( '[CONNECTION_DESCRIPTION]' => htmlspecialchars( $connection->description ) ) );
		}
		return $tipField;
	}

	/**
	 * UserBot Called when a user is deleted from backend (prepare future unregistration)
	 * @param  UserTable  $user     reflecting the user being deleted
	 * @param  int        $success  1 for successful deleting
	 * @return boolean              true if all is ok, or false if ErrorMSG generated
	 */
	public function userDeleted( $user, /** @noinspection PhpUnusedParameterInspection */ $success )
	{
		global $_CB_database, $ueConfig;
		$sql		=	"DELETE FROM #__comprofiler_members WHERE referenceid = " . (int) $user->id;
		$_CB_database->SetQuery( $sql );

		try {
			$_CB_database->query();
		}
		catch ( RuntimeException $e ) {
			$this->_setErrorMSG( "SQL error cb.connections:userDelted-1" . $e->getMessage() );
			return false;
		}
		
		if ( $ueConfig['autoAddConnections'] ) {
			$sql	=	"DELETE FROM #__comprofiler_members WHERE memberid = " . (int) $user->id;
			$_CB_database->SetQuery( $sql );

			try {
				$_CB_database->query();
			}
			catch ( RuntimeException $e ) {
				$this->_setErrorMSG( "SQL error cb.connections:userDelted-1" . $e->getMessage() );
				return false;
			}

		}
		return true;
	}
} // end class getConnectionTab.
