<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CB\Plugin\PMS\PMSHelper;
use CB\Plugin\PMS\Table\MessageTable;
use CB\Plugin\PMS\Table\ReadTable;
use CB\Plugin\PMS\UddeIM;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBplug_pmsmypmspro extends cbPluginHandler
{

	/**
	 * @param null           $tab
	 * @param null|UserTable $user
	 * @param int            $ui
	 * @param array          $postdata
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_CB_PMS;

		$raw						=	( $this->getInput()->getString( 'format', '' ) === 'raw' );
		$action						=	$this->getInput()->getString( 'action', '' );
		$function					=	$this->getInput()->getString( 'func', '' );
		$id							=	$this->getInput()->getInt( 'id', 0 );
		$user						=	CBuser::getMyUserDataInstance();

		if ( UddeIM::isUddeIM() ) {
			$link					=	$_CB_PMS->getPMSlinks( null, $user->getInt( 'id', 0 ), null, null, 2 );

			if ( isset( $link[0]['url'] ) ) {
				$inboxURL			=	$link[0]['url'];
			} else {
				$inboxURL			=	'index.php?option=com_uddeim';
			}

			$link					=	$_CB_PMS->getPMSlinks( $this->getInput()->getInt( 'to', 0 ), $user->getInt( 'id', 0 ), null, null, 1 );

			if ( isset( $link[0]['url'] ) ) {
				$pmURL				=	$link[0]['url'];
			} else {
				$pmURL				=	$inboxURL;
			}

			if ( $action == 'message' ) {
				if ( $function == 'new' ) {
					cbRedirect( $pmURL );
				} elseif ( $function != 'quick'  ) {
					cbRedirect( $inboxURL );
				}
			} else {
				cbRedirect( $inboxURL );
			}
		}

		if ( ! $raw ) {
			outputCbJs();
			outputCbTemplate();

			ob_start();
		}

		switch ( $action ) {
			case 'message':
				switch ( $function ) {
					case 'quick':
						if ( ! Application::Session()->checkFormToken() ) {
							return;
						}

						$this->saveQuickMessage( $user );
						break;
					case 'new':
						$this->showMessageEdit( null, $user );
						break;
					case 'edit':
						$this->showMessageEdit( $id, $user );
						break;
					case 'save':
						if ( ! Application::Session()->checkFormToken() ) {
							return;
						}

						$this->saveMessageEdit( $id, $user );
						break;
					case 'read':
						$this->stateMessage( 1, $id, $user );
						break;
					case 'unread':
						$this->stateMessage( 0, $id, $user );
						break;
					case 'delete':
						if ( ! Application::Session()->checkFormToken( 'get' ) ) {
							return;
						}

						$this->deleteMessage( $id, $user );
						break;
					case 'report':
						if ( ! Application::Session()->checkFormToken( 'get' ) ) {
							return;
						}

						$this->reportMessage( $id, $user );
						break;
					case 'show':
					default:
						$this->showMessage( $id, $user );
						break;
				}
				break;
			case 'messages':
			default:
				switch ( $function ) {
					case 'new':
						$this->showMessageEdit( null, $user );
						break;
					case 'received':
					case 'sent':
					case 'modal':
						$this->showMessages( $user, $function );
						break;
					case 'show':
					default:
						$this->showMessages( $user );
						break;
				}
				break;
		}

		if ( ! $raw ) {
			$html					=	ob_get_contents();
			ob_end_clean();

			$class					=	$this->params->getString( 'general_class', '' );

			$return					=	'<div class="cbPMS' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
									.		$html
									.	'</div>';

			echo $return;
		}
	}

	/**
	 * @param UserTable   $user
	 * @param null|string $type
	 */
	public function showMessages( $user, $type = null )
	{
		global $_CB_framework, $_CB_database, $_CB_PMS;

		if ( ! $user->getInt( 'id', 0 ) ) {
			if ( $type == 'modal' ) {
				return;
			} else {
				PMSHelper::returnRedirect( 'index.php', CBTxt::T( 'You do not have permission to view messages.' ), 'error' );
			}
		}

		$limit					=	$this->params->getInt( 'messages_limit', 15 );
		$limitstart				=	(int) $_CB_framework->getUserStateFromRequest( 'pmlimitstart{com_comprofiler}', 'pmlimitstart', 0 );
		$search					=	$_CB_framework->getUserStateFromRequest( 'pmsearch{com_comprofiler}', 'pmsearch', '' );
		$allowTypeFilter		=	false;

		if ( $type == 'modal' ) {
			// Reset search and paging for modal output as we only want to show the first unfiltered page:
			$limitstart			=	0;
			$search				=	null;
		} elseif ( ! $type ) {
			$type				=	$_CB_framework->getUserStateFromRequest( 'pmtype{com_comprofiler}', 'pmtype', '' );
			$allowTypeFilter	=	true;
		}

		$where					=	null;

		if ( $search && $this->params->getBool( 'messages_search', true ) ) {
			$where				.=	"\n AND ( m." . $_CB_database->NameQuote( 'message' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	( $type != 'sent' ? " OR m." . $_CB_database->NameQuote( 'from_name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) : null )
								.	" OR u." . $_CB_database->NameQuote( 'username' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR u." . $_CB_database->NameQuote( 'name' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( $where ? true : false );

		$query					=	"SELECT COUNT(*)"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' ) . " AS m";
		if ( $type == 'sent' ) {
			if ( $searching ) {
				$query			.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
								.	"\n ON u." . $_CB_database->NameQuote( 'id' ) . " = m." . $_CB_database->NameQuote( 'to_user' );
			}
			$query				.=	"\n WHERE ( m." . $_CB_database->NameQuote( 'from_user' ) . " = " . $user->getInt( 'id', 0 )
								.	" AND m." . $_CB_database->NameQuote( 'from_user_delete' ) . " = 0 )";
		} else {
			if ( $searching ) {
				$query			.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
								.	"\n ON u." . $_CB_database->NameQuote( 'id' ) . " = m." . $_CB_database->NameQuote( 'from_user' );
			}
			$query				.=	"\n WHERE ( ( m." . $_CB_database->NameQuote( 'from_user' ) . " != " . $user->getInt( 'id', 0 )
								.	" AND m." . $_CB_database->NameQuote( 'to_user' ) . " = 0 )"
								.	" OR ( m." . $_CB_database->NameQuote( 'to_user' ) . " = " . $user->getInt( 'id', 0 )
								.	" AND m." . $_CB_database->NameQuote( 'to_user_delete' ) . " = 0 ) )";
		}
		$query					.=	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		$pageNav				=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( 'pm' );
		$pageNav->setStaticLimit( true );
		$pageNav->setBaseURL( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'messages', 'func' => ( ( ! $allowTypeFilter ) && ( $type != 'modal' ) ? $type : null ), 'pmsearch' => ( $searching ? $search : null ), 'pmtype' => ( $allowTypeFilter && ( $type != 'modal' ) ? $type : null ) ) ) );

		switch( $this->params->getInt( 'messages_orderby', 2 ) ) {
			case 1:
				$orderBy		=	'm.' . $_CB_database->NameQuote( 'date' ) . ' ASC';
				break;
			case 3:
				$orderBy		=	'm.' . $_CB_database->NameQuote( 'message' ) . ' ASC';
				break;
			case 4:
				$orderBy		=	'm.' . $_CB_database->NameQuote( 'message' ) . ' DESC';
				break;
			case 2:
			default:
				$orderBy		=	'm.' . $_CB_database->NameQuote( 'date' ) . ' DESC';
				break;
		}

		$query					=	"SELECT m.*"
								.	", r." . $_CB_database->NameQuote( 'date' ) . " AS _read"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' ) . " AS m";
		if ( $type == 'sent' ) {
			$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages_read' ) . " AS r"
								.	"\n ON r." . $_CB_database->NameQuote( 'message' ) . " = m." . $_CB_database->NameQuote( 'id' );
			if ( $searching ) {
				$query			.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
								.	"\n ON u." . $_CB_database->NameQuote( 'id' ) . " = m." . $_CB_database->NameQuote( 'to_user' );
			}
			$query				.=	"\n WHERE ( m." . $_CB_database->NameQuote( 'from_user' ) . " = " . $user->getInt( 'id', 0 )
								.	" AND m." . $_CB_database->NameQuote( 'from_user_delete' ) . " = 0 )";
		} else {
			$query				.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages_read' ) . " AS r"
								.	"\n ON r." . $_CB_database->NameQuote( 'to_user' ) . " = " . $user->getInt( 'id', 0 )
								.	"\n AND r." . $_CB_database->NameQuote( 'message' ) . " = m." . $_CB_database->NameQuote( 'id' );
			if ( $searching ) {
				$query			.=	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
								.	"\n ON u." . $_CB_database->NameQuote( 'id' ) . " = m." . $_CB_database->NameQuote( 'from_user' );
			}
			$query				.=	"\n WHERE ( ( m." . $_CB_database->NameQuote( 'from_user' ) . " != " . $user->getInt( 'id', 0 )
								.	" AND m." . $_CB_database->NameQuote( 'to_user' ) . " = 0 )"
								.	" OR ( m." . $_CB_database->NameQuote( 'to_user' ) . " = " . $user->getInt( 'id', 0 )
								.	" AND m." . $_CB_database->NameQuote( 'to_user_delete' ) . " = 0 ) )";
		}
		$query					.=	$where
								.	"\n ORDER BY " . $orderBy;
		if ( $this->params->getBool( 'messages_paging', true ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\PMS\Table\MessageTable', array( $_CB_database ) );

		$users					=	array();

		/** @var MessageTable[] $rows */
		foreach ( $rows as $row ) {
			$userId				=	$row->getInt( 'from_user', 0 );

			if ( $userId && ( ! in_array( $userId, $users ) ) ) {
				$users[]		=	$userId;
			}

			$userId				=	$row->getInt( 'to_user', 0 );

			if ( $userId && ( ! in_array( $userId, $users ) ) ) {
				$users[]		=	$userId;
			}
		}

		if ( $users ) {
			\CBuser::advanceNoticeOfUsersNeeded( $users );
		}

		$unread					=	$_CB_PMS->getPMSunreadCount( $user->getInt( 'id', 0 ) );

		if ( isset( $unread[0] ) ) {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$unread				=	$unread[0];
		} else {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$unread				=	0;
		}

		if ( $type != 'modal' ) {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$returnUrl			=	PMSHelper::getReturn();

			$js					=	"$( '.pmMessagesRow' ).on( 'click', function( e ) {"
								.		"if ( ! ( $( e.target ).is( 'a' ) || $( e.target ).closest( 'a' ).length || $( e.target ).is( '.btn' ) || $( e.target ).closest( '.btn' ).length ) ) {"
								.			"var url = $( this ).data( 'pm-url' );"
								.			"if ( url ) {"
								.				"window.location = url;"
								.			"}"
								.		"}"
								.	"});"
								.	"$( '.pmSearchType' ).cbselect({"
								.		"width: 'auto',"
								.		"height: '100%',"
								.		"minimumResultsForSearch: Infinity"
								.	"});";

			$_CB_framework->outputCbJQuery( $js, 'cbselect' );

			initToolTip();
		} else {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$returnUrl			=	base64_encode( $_CB_framework->userProfileUrl( $user->getInt( 'id', 0 ), false ) );
		}

		$input					=	array();
		$input['search']		=	null;

		if ( ( $type != 'modal' ) && $this->params->getBool( 'messages_search', true ) && ( $searching || $pageNav->total ) ) {
			$input['search']	=	'<input type="text" name="pmsearch" value="' . htmlspecialchars( (string) $search ) . '" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Messages...' ) ) . '" class="form-control pmSearch" role="combobox" />';
		}

		$input['type']			=	null;

		if ( $allowTypeFilter ) {
			$types				=	array();
			$types[]			=	moscomprofilerHTML::makeOption( '', CBTxt::T( 'Received' ) );
			$types[]			=	moscomprofilerHTML::makeOption( 'sent', CBTxt::T( 'Sent' ) );

			$input['type']		=	moscomprofilerHTML::selectList( $types, 'pmtype', 'class="form-control flex-grow-0 pmSearchType" onchange="document.pmMessagesForm.submit();"', 'value', 'text', $type, 0, false, false );
		}

		require PMSHelper::getTemplate( null, 'messages' );
	}

	/**
	 * @param null|int    $id
	 * @param UserTable   $user
	 */
	public function showMessage( $id, $user )
	{
		global $_CB_framework, $_PLUGINS;

		$row							=	new MessageTable();

		$row->load( (int) $id );

		$returnUrl						=	PMSHelper::getReturn( true, true );

		if ( ! $returnUrl ) {
			$returnUrl					=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'messages' ) );
		}

		if ( ! $row->getInt( 'id', 0 ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message does not exist.' ), 'error' );
		} elseif ( ( ! $user->getInt( 'id', 0 ) )
				   || ( ( $user->getInt( 'id', 0 ) != $row->getInt( 'from_user', 0 ) )
				   && ( $user->getInt( 'id', 0 ) != $row->getInt( 'to_user', 0 ) )
				   && ( $row->getInt( 'to_user', 0 ) != 0 ) ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to view this message.' ), 'error' );
		} elseif ( $row->isDeleted( $user ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message does not exist.' ), 'error' );
		}

		$messageLimit					=	( Application::MyUser()->isGlobalModerator() ? 0 : $this->params->getInt( 'messages_characters', 2500 ) );
		$messageEditor					=	$this->params->getInt( 'messages_editor', 2 );

		if ( ( $messageEditor == 3 ) && ( ! Application::MyUser()->isGlobalModerator() ) ) {
			$messageEditor				=	1;
		}

		$input							=	array();

		if ( $messageEditor >= 2 ) {
			$input['message']			=	cbTooltip( null, CBTxt::T( 'Input your reply.' ), null, null, null, Application::Cms()->displayCmsEditor( 'message', $this->getInput()->getHtml( 'post/message', '' ), '100%', 175, 35, 6 ), null, 'class="d-block clearfix"' );
		} else {
			$messageTooltip				=	cbTooltip( null, CBTxt::T( 'Input your reply.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['message']			=	'<textarea id="message" name="message" class="w-100 form-control" cols="35" rows="6"' . $messageTooltip . ( $messageLimit ? cbValidator::getRuleHtmlAttributes( 'maxlength', $messageLimit ) : null ) . '>' . htmlspecialchars( $this->getInput()->getString( 'post/message', '' ) ) . '</textarea>';
		}

		$input['message_limit']			=	null;

		if ( $messageLimit ) {
			$js							=	"$( '.pmMessageEditMessage textarea' ).on( 'change keyup', function() {"
										.		"$( '.pmMessageEditLimit' ).removeClass( 'hidden' );"
										.		"var inputLength = $( this ).val().length;"
										.		"if ( inputLength > $messageLimit ) {"
										.			"$( this ).val( $( this ).val().substr( 0, $messageLimit ) );"
										.			"$( '.pmMessageEditLimitCurrent' ).html( $messageLimit );"
										.		"} else {"
										.			"$( '.pmMessageEditLimitCurrent' ).html( $( this ).val().length );"
										.		"}"
										.	"});";

			if ( $messageEditor >= 2 ) {
				// Before attempting to bind to an editors events make absolutely sure it exists and its used functions eixst; otherwise hide the message limit and just trim on save:
				$js						.=	"if ( ( typeof Joomla != 'undefined' )"
										.		" && ( typeof Joomla.editors != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'] != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'].getValue != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'].setValue != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'].instance != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'].instance.on != 'undefined' ) ) {"
										.		"var messageEditor = Joomla.editors.instances['message'];"
										.		"messageEditor.instance.on( 'change keyup', function() {"
										.			"var inputValue = messageEditor.getValue();"
										.			"var inputLength = inputValue.length;"
										.			"if ( inputLength > $messageLimit ) {"
										.				"messageEditor.setValue( inputValue.substr( 0, $messageLimit ) );"
										.				"$( '.pmMessageEditLimitCurrent' ).html( $messageLimit );"
										.			"} else {"
										.				"$( '.pmMessageEditLimitCurrent' ).html( inputValue.length );"
										.			"}"
										.		"});"
										.	"} else {"
										.		"$( '.pmMessageEditLimit' ).addClass( 'hidden' );"
										.	"}";
			}

			$_CB_framework->outputCbJQuery( $js );

			$input['message_limit']		=	'<div class="badge badge-secondary font-weight-normal align-bottom pmMessageEditLimit">'
										.		'<span class="pmMessageEditLimitCurrent">0</span> / <span class="pmMessageEditLimitMax">' . $messageLimit . '</span>'
										.	'</div>';
		}

		$input['captcha']				=	null;

		$showCaptcha					=	$this->params->getInt( 'messages_captcha', 1 );

		if ( Application::MyUser()->isGlobalModerator() || ( ( $showCaptcha == 2 ) && $user->getInt( 'id', 0 ) ) ) {
			$showCaptcha				=	0;
		}

		if ( $showCaptcha ) {
			$input['captcha']			=	implode( '', $_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( true ) ) );
		}

		if ( ( $user->getInt( 'id', 0 ) == $row->getInt( 'to_user', 0 ) ) || ( ! $row->getInt( 'to_user', 0 ) ) ) {
			$row->setRead( $user->getInt( 'id', 0 ), 1 );
		}

		cbValidator::loadValidation();
		initToolTip();

		require PMSHelper::getTemplate( null, 'message' );
	}

	/**
	 * @param null|int    $id
	 * @param UserTable   $user
	 */
	public function showMessageEdit( $id, $user )
	{
		global $_CB_framework, $_PLUGINS;

		$row							=	new MessageTable();

		$row->load( (int) $id );

		$returnUrl						=	PMSHelper::getReturn( true, true );

		if ( ! $returnUrl ) {
			if ( ! $user->getInt( 'id', 0 ) ) {
				// Public users can't access messages or message endpoint so just send them home if they have no return url:
				$returnUrl				=	'index.php';
			} elseif ( $row->getInt( 'id', 0 ) ) {
				$returnUrl				=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'message', 'id' => $row->getInt( 'id', 0 ) ) );
			} else {
				$returnUrl				=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'messages' ) );
			}
		}

		if ( ! $row->getInt( 'id', 0 ) ) {
			if ( ! PMSHelper::canMessage( $user->getInt( 'id', 0 ), false ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to send messages.' ), 'error' );
			}
		} elseif ( ( $user->getInt( 'id', 0 ) != $row->getInt( 'from_user', 0 ) ) || ( ! $user->getInt( 'id', 0 ) ) || $row->getRead() ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to edit this message.' ), 'error' );
		} elseif ( $row->isDeleted( $user ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message does not exist.' ), 'error' );
		}

		$toMultiple						=	$this->params->getBool( 'messages_multiple', false );
		$toLimit						=	$this->params->getInt( 'messages_multiple_limit', 5 );

		if ( ! $toMultiple ) {
			$toLimit					=	1;
		}

		$messageLimit					=	( Application::MyUser()->isGlobalModerator() ? 0 : $this->params->getInt( 'messages_characters', 2500 ) );

		$js								=	"$( '.pmMessageEditConn' ).on( 'change', function() {"
										.		"var selected = $( this ).val();"
										.		"$( this ).val( '' );";

		if ( $this->params->getBool( 'messages_multiple', false ) ) {
			$js							.=		"var existing = $( '.pmMessageEditTo' ).val().split( ',' ).filter( function( v ) { return v; } );"
										.		"if ( existing.indexOf( selected ) === -1 ) {"
										.			"existing.push( selected );"
										.			"$( '.pmMessageEditTo' ).val( existing.join( ',' ) ).trigger( 'change' );"
										.		"}";
		} else {
			$js							.=		"$( '.pmMessageEditTo' ).val( selected ).trigger( 'change' );";
		}

		$js								.=	"}).cbselect({"
										.		"width: '100%',"
										.		"dropdownParent: '.pmMessageEditToGroup'"
										.	"});"
										.	"$( '.pmMessageEditGlobal input' ).on( 'change', function() {"
										.		"if ( $( this ).is( ':checked' ) ) {"
										.			"$( '.pmMessageEditTo' ).parent().siblings( '.cbValidationMessage' ).remove();"
										.			"$( '.pmMessageEditTo' ).removeClass( 'cbValidationError is-invalid' );"
										.			"$( '.pmMessageEditTo,.pmMessageEditConn' ).addClass( 'disabled' ).prop( 'disabled', true );"
										.			"$( '.pmMessageEditConn' ).cbtooltip( 'disable' );"
										.		"} else {"
										.			"$( '.pmMessageEditTo,.pmMessageEditConn' ).removeClass( 'disabled' ).prop( 'disabled', false );"
										.			"$( '.pmMessageEditConn' ).cbtooltip( 'enable' );"
										.		"}"
										.	"}).trigger( 'change' );";

		$messageEditor					=	$this->params->getInt( 'messages_editor', 2 );

		if ( ( $messageEditor == 3 ) && ( ! Application::MyUser()->isGlobalModerator() ) ) {
			$messageEditor				=	1;
		}

		$input							=	array();

		$input['from_name']				=	null;
		$input['from_email']			=	null;

		if ( ( ! $user->getInt( 'id', 0 ) ) && $this->params->getInt( 'messages_public', 0 ) ) {
			$nameTooltip				=	cbTooltip( null, CBTxt::T( 'Input your name to be sent with your message.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['from_name']			=	'<input type="text" id="from_name" name="from_name" value="' . htmlspecialchars( $this->getInput()->getString( 'post/from_name', $row->getString( 'from_name', '' ) ) ) . '" class="form-control required" size="40"' . $nameTooltip . cbValidator::getRuleHtmlAttributes( 'maxlength', 100 ) . ' />';

			$emailTooltip				=	cbTooltip( null, CBTxt::T( 'Input your email address to be sent with your message. Note the user you are messaging will see your email address and replies to your message will be emailed to you.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['from_email']		=	'<input type="text" id="from_email" name="from_email" value="' . htmlspecialchars( $this->getInput()->getString( 'post/from_email', $row->getString( 'from_email', '' ) ) ) . '" class="form-control required" size="40"' . $emailTooltip . cbValidator::getRuleHtmlAttributes( 'email' ) . cbValidator::getRuleHtmlAttributes( 'maxlength', 100 ) . ' />';
		}

		$input['global']				=	null;
		$input['system']				=	null;

		if ( Application::MyUser()->isGlobalModerator() ) {
			if ( $this->params->getBool( 'messages_global', true ) ) {
				$globalTooltip			=	cbTooltip( null, CBTxt::T( 'Select if this message should be sent to all users.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

				$input['global']		=	moscomprofilerHTML::checkboxListButtons( array( moscomprofilerHTML::makeOption( '1', '<span class="fa fa-globe"></span>', 'value', 'text', null, 'rounded-left-0' ) ), 'global', 'data-cbtooltip-simple="true"' . $globalTooltip, 'value', 'text', $this->getInput()->getInt( 'post/global', ( $row->getInt( 'id', 0 ) && ( ! $row->getInt( 'to_user', 0 ) ) ? 1 : 0 ) ), 0, array( 'pmMessageEditGlobal' ), null, false );
			}

			if ( $this->params->getBool( 'messages_system', true ) ) {
				$systemTooltip			=	cbTooltip( null, CBTxt::T( 'Select if this message should be sent from the system. It will not link back to you personally, but the message will still belong to you.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

				$input['system']		=	moscomprofilerHTML::yesnoButtonList( 'system', $systemTooltip, $this->getInput()->getInt( 'post/system', $row->getInt( 'from_system', 0 ) ) );
			}
		}

		$input['to']					=	null;
		$input['user']					=	null;

		if ( ! $row->getInt( 'id', 0 ) ) {
			$to							=	$this->getInput()->getString( 'get/to', '' );

			if ( $to ) {
				$toUser					=	new UserTable();

				if ( is_numeric( $to ) ) {
					$toUser->load( (int) $to );
				} else {
					$toUser->loadByUsername( trim( $to ) );
				}

				if ( $toUser->getInt( 'id', 0 ) ) {
					$to					=	$toUser->getString( 'username', '' );

					if ( ! PMSHelper::canMessage( $user->getInt( 'id', 0 ), $toUser->getInt( 'id', 0 ) ) ) {
						PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to message this user.' ), 'error' );
					}
				}
			}

			$toTooltip					=	cbTooltip( null, ( $toMultiple ? ( $toLimit ? CBTxt::T( 'PM_MESSAGE_TO_LIMIT', 'Input the username of the user you want to send a message to. Separate multiple usernames with a comma. You may send this message up to a maximum of [limit] users.', array( '[limit]' => $toLimit ) ) : CBTxt::T( 'Input the username of the user you want to send a message to. Separate multiple usernames with a comma.' ) ) : CBTxt::T( 'Input the username of the user you want to send a message to.' ) ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['to']				=	'<input type="text" id="to" name="to" value="' . htmlspecialchars( $this->getInput()->getString( 'post/to', (string) $to ) ) . '" class="required form-control pmMessageEditTo"' . $toTooltip . ' />';
		} else {
			$to							=	$row->getInt( 'to_user', 0 );

			if ( $to ) {
				$cbUser					=	CBuser::getInstance( $to, false );

				if ( ! $cbUser->getUserData()->getInt( 'id', 0 ) ) {
					$name				=	CBTxt::T( 'Deleted' );
				} else {
					$name				=	$cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) );
				}
			} else {
				$name					=	CBTxt::T( 'All Users' );
			}

			$input['user']				=	$name;
		}

		$listConnections				=	array();

		if ( Application::Config()->getBool( 'allowConnections', true ) && $this->params->getBool( 'messages_connections', true ) && $user->getInt( 'id', 0 ) ) {
			$cbConnection				=	new cbConnection( $user->getInt( 'id', 0 ) );

			foreach( $cbConnection->getConnectedToMe( $user->getInt( 'id', 0 ) ) as $connection ) {
				$listConnections[]		=	moscomprofilerHTML::makeOption( (string) $connection->username, getNameFormat( $connection->name, $connection->username, Application::Config()->getInt( 'name_format', 3 ) ) );
			}
		}

		if ( $listConnections ) {
			array_unshift( $listConnections, moscomprofilerHTML::makeOption( '', CBTxt::T( '- Select Connection -' ) ) );

			$listTooltip				=	cbTooltip( null, CBTxt::T( 'Select a connection to send a message to.' ), null, null, null, null, null, 'data-hascbtooltip="true" data-cbtooltip-simple="true"' );

			$input['conn']				=	moscomprofilerHTML::selectList( $listConnections, 'connection', 'class="btn btn-light border fa-before fa-users pmMessageEditConn" data-cbselect-selectionCssClass="hidden"' . $listTooltip, 'value', 'text', 0, 1, false, false );
		} else {
			$input['conn']				=	null;
		}

		if ( $messageEditor >= 2 ) {
			$input['message']			=	cbTooltip( null, CBTxt::T( 'Input your private message.' ), null, null, null, Application::Cms()->displayCmsEditor( 'message', $this->getInput()->getHtml( 'post/message', $row->getHtml( 'message', '' ) ), '100%', 175, 35, 6 ), null, 'class="d-block clearfix"' );
		} else {
			$messageTooltip				=	cbTooltip( null, CBTxt::T( 'Input your private message.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

			$input['message']			=	'<textarea id="message" name="message" class="w-100 form-control" cols="35" rows="6"' . $messageTooltip . ( $messageLimit ? cbValidator::getRuleHtmlAttributes( 'maxlength', $messageLimit ) : null ) . '>' . htmlspecialchars( $this->getInput()->getString( 'post/message', $row->getString( 'message', '' ) ) ) . '</textarea>';
		}

		$input['message_limit']			=	null;

		if ( $messageLimit ) {
			$js							.=	"$( '.pmMessageEditMessage textarea' ).on( 'change keyup', function() {"
										.		"$( '.pmMessageEditLimit' ).removeClass( 'hidden' );"
										.		"var inputLength = $( this ).val().length;"
										.		"if ( inputLength > $messageLimit ) {"
										.			"$( this ).val( $( this ).val().substr( 0, $messageLimit ) );"
										.			"$( '.pmMessageEditLimitCurrent' ).html( $messageLimit );"
										.		"} else {"
										.			"$( '.pmMessageEditLimitCurrent' ).html( $( this ).val().length );"
										.		"}"
										.	"});";

			if ( $messageEditor >= 2 ) {
				// Before attempting to bind to an editors events make absolutely sure it exists and its used functions eixst; otherwise hide the message limit and just trim on save:
				$js						.=	"if ( ( typeof Joomla != 'undefined' )"
										.		" && ( typeof Joomla.editors != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'] != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'].getValue != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'].setValue != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'].instance != 'undefined' )"
										.		" && ( typeof Joomla.editors.instances['message'].instance.on != 'undefined' ) ) {"
										.		"var messageEditor = Joomla.editors.instances['message'];"
										.		"messageEditor.instance.on( 'change keyup', function() {"
										.			"var inputValue = messageEditor.getValue();"
										.			"var inputLength = inputValue.length;"
										.			"if ( inputLength > $messageLimit ) {"
										.				"messageEditor.setValue( inputValue.substr( 0, $messageLimit ) );"
										.				"$( '.pmMessageEditLimitCurrent' ).html( $messageLimit );"
										.			"} else {"
										.				"$( '.pmMessageEditLimitCurrent' ).html( inputValue.length );"
										.			"}"
										.		"});"
										.	"} else {"
										.		"$( '.pmMessageEditLimit' ).addClass( 'hidden' );"
										.	"}";
			}

			$input['message_limit']		=	'<div class="badge badge-secondary font-weight-normal align-bottom pmMessageEditLimit">'
										.		'<span class="pmMessageEditLimitCurrent">0</span> / <span class="pmMessageEditLimitMax">' . $messageLimit . '</span>'
										.	'</div>';
		}

		$input['captcha']				=	null;

		$showCaptcha					=	$this->params->getInt( 'messages_captcha', 1 );

		if ( Application::MyUser()->isGlobalModerator() || ( ( $showCaptcha == 2 ) && $user->getInt( 'id', 0 ) ) || $row->getInt( 'id', 0 ) ) {
			$showCaptcha				=	0;
		}

		if ( $showCaptcha ) {
			$input['captcha']			=	implode( '', $_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( true ) ) );
		}

		$_CB_framework->outputCbJQuery( $js, 'cbselect' );

		cbValidator::loadValidation();
		initToolTip();

		require PMSHelper::getTemplate( null, 'message_edit' );
	}

	/**
	 * @param null|int  $id
	 * @param UserTable $user
	 */
	private function saveMessageEdit( $id, $user )
	{
		global $_CB_framework, $_PLUGINS;

		$row							=	new MessageTable();

		$row->load( (int) $id );

		$reply							=	$this->getInput()->getInt( 'post/reply', 0 );

		if ( ! $user->getInt( 'id', 0 ) ) {
			// Public users can't access messages or message endpoint so just send them home if they have no return url:
			$returnUrl					=	'index.php';
		} elseif ( $reply ) {
			$returnUrl					=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'message', 'id' => $reply ) );
		} else {
			if ( $row->getInt( 'id', 0 ) ) {
				$returnUrl				=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'message', 'id' => $row->getInt( 'id', 0 ) ) );
			} else {
				$returnUrl				=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'messages' ) );
			}
		}

		if ( ! $row->getInt( 'id', 0 ) ) {
			if ( $reply && ( ! PMSHelper::canReply( $user->getInt( 'id', 0 ), false ) ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to send replies.' ), 'error' );
			} elseif ( ! PMSHelper::canMessage( $user->getInt( 'id', 0 ), false ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to send messages.' ), 'error' );
			}
		} elseif ( ( $user->getInt( 'id', 0 ) != $row->getInt( 'from_user', 0 ) ) || ( ! $user->getInt( 'id', 0 ) ) || $row->getRead() ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to edit this message.' ), 'error' );
		} elseif ( $row->isDeleted( $user ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message does not exist.' ), 'error' );
		}

		$messageLimit					=	( Application::MyUser()->isGlobalModerator() ? 0 : $this->params->getInt( 'messages_characters', 2500 ) );
		$messageEditor					=	$this->params->getInt( 'messages_editor', 2 );

		if ( ( $messageEditor == 3 ) && ( ! Application::MyUser()->isGlobalModerator() ) ) {
			$messageEditor				=	1;
		}

		if ( $messageEditor >= 2 ) {
			$message					=	trim( $this->getInput()->getHtml( 'post/message', $row->getHtml( 'message', '' ) ) );
		} else {
			$message					=	trim( $this->getInput()->getString( 'post/message', $row->getString( 'message', '' ) ) );
		}

		$message						=	PMSHelper::removeDuplicateSpacing( $message );

		if ( $messageLimit && ( cbutf8_strlen( $message ) > $messageLimit ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'MESSAGE_TOO_LONG', 'Message is too long! Please provide a message no longer than [limit] characters.', array( '[limit]' => $messageLimit ) ), 'error' );

			if ( $reply ) {
				$this->showMessage( $reply, $user );
				return;
			}

			$this->showMessageEdit( $id, $user );
			return;
		}

		if ( ! $row->getInt( 'id', 0 ) ) {
			$toArray					=	explode( ',', $this->getInput()->getString( 'post/to', '' ) );
			$toLimit					=	$this->params->getInt( 'messages_multiple_limit', 5 );

			if ( ! $this->params->getBool( 'messages_multiple', false ) ) {
				$toLimit				=	1;
			}

			$global						=	false;

			if ( Application::MyUser()->isGlobalModerator() && $this->params->getBool( 'messages_system', true ) && $this->getInput()->getInt( 'post/global', 0 ) ) {
				$global					=	true;
				$toArray				=	array( 0 );
			}

			$replyTo					=	new MessageTable();

			if ( $reply ) {
				$replyTo->load( (int) $reply );

				if ( ! $replyTo->getInt( 'id', 0 ) ) {
					PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message does not exist.' ), 'error' );
				} elseif ( ( ! PMSHelper::canReply( $user->getInt( 'id', 0 ), $replyTo->getInt( 'from_user', 0 ) ) )
						   || $replyTo->getBool( 'from_system', false )
						   || ( $user->getInt( 'id', 0 ) != $replyTo->getInt( 'to_user', 0 ) )
						   || ( ( ! $replyTo->getInt( 'from_user', 0 ) ) && ( ! $replyTo->getString( 'from_email', '' ) ) )
				) {
					PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to reply to this message.' ), 'error' );
				}

				$toArray				=	array( $replyTo->getInt( 'from_user', 0 ) );
			}

			if ( ( ! $this->params->getBool( 'messages_multiple', false ) ) && ( count( $toArray ) > 1 ) ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'Sending messages to multiple users is not supported! Please specify a single user.' ), 'error' );

				if ( $reply ) {
					$this->showMessage( $reply, $user );
					return;
				}

				$this->showMessageEdit( $id, $user );
				return;
			}

			if ( ! $toArray ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'User not specified.' ), 'error' );

				if ( $reply ) {
					$this->showMessage( $reply, $user );
					return;
				}

				$this->showMessageEdit( $id, $user );
				return;
			}

			$sent						=	array();

			foreach ( $toArray as $k => $to ) {
				if ( $toLimit && ( $k >= $toLimit ) ) {
					break;
				}

				if ( in_array( $to, $sent ) ) {
					continue;
				}

				$row					=	new MessageTable();

				if ( ( ! $user->getInt( 'id', 0 ) ) && $this->params->getInt( 'messages_public', 0 ) ) {
					$row->set( 'from_user', 0 );
					$row->set( 'from_name', $this->getInput()->getString( 'post/from_name', $row->getString( 'from_name', '' ) ) );
					$row->set( 'from_email', $this->getInput()->getString( 'post/from_email', $row->getString( 'from_email', '' ) ) );
				} else {
					$row->set( 'from_user', $user->getInt( 'id', 0 ) );
				}

				if ( $global ) {
					$row->set( 'to_user', 0 );
				} else {
					$toUser				=	new UserTable();

					if ( is_int( $to ) ) {
						$toUser->load( $to );
					} else {
						$toUser->loadByUsername( trim( $to ) );
					}

					if ( ! $toUser->getInt( 'id', 0 ) ) {
						if ( count( $toArray ) > 1 ) {
							// Multiple recipients were supplied so lets make sure the error is clear on which failed:
							$_CB_framework->enqueueMessage( CBTxt::T( 'USER_TO_NOT_EXIST', 'User "[to]" does not exist.', array( '[to]' => htmlspecialchars( trim( $to ) ) ) ), 'error' );
						} else {
							$_CB_framework->enqueueMessage( CBTxt::T( 'User does not exist.' ), 'error' );
						}

						if ( $reply ) {
							$this->showMessage( $reply, $user );
							return;
						}

						$this->showMessageEdit( $id, $user );
						return;
					} elseif ( $toUser->getInt( 'id', 0 ) == $user->getInt( 'id', 0 ) ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'You can not message yourself!' ), 'error' );

						if ( $reply ) {
							$this->showMessage( $reply, $user );
							return;
						}

						$this->showMessageEdit( $id, $user );
						return;
					}

					$row->set( 'to_user', $toUser->getInt( 'id', 0 ) );
				}

				if ( ( ! $reply ) && ( ! PMSHelper::canMessage( $row->getInt( 'from_user', 0 ), $row->getInt( 'to_user', 0 ) ) ) ) {
					if ( count( $toArray ) > 1 ) {
						// Multiple recipients were supplied so lets make sure the error is clear on which failed:
						$_CB_framework->enqueueMessage( CBTxt::T( 'NO_PERMISSION_MESSAGE_TO_USER', 'You do not have permission to message "[to]".', array( '[to]' => htmlspecialchars( trim( $to ) ) ) ), 'error' );
					} else {
						$_CB_framework->enqueueMessage( CBTxt::T( 'You do not have permission to message this user.' ), 'error' );
					}

					$this->showMessageEdit( $id, $user );
					return;
				}

				$row->set( 'reply_to', $reply );
				$row->set( 'message', $message );

				if ( Application::MyUser()->isGlobalModerator() && $this->params->getBool( 'messages_system', true ) ) {
					$row->set( 'from_system', $this->getInput()->getInt( 'post/system', $row->getInt( 'from_system', 0 ) ) );
				}

				$checkCaptcha			=	$this->params->getInt( 'messages_captcha', 1 );

				if ( Application::MyUser()->isGlobalModerator() || ( ( $checkCaptcha == 2 ) && $user->getInt( 'id', 0 ) ) || ( $k != 0 ) ) {
					$checkCaptcha		=	0;
				}

				if ( $checkCaptcha ) {
					$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

					if ( $_PLUGINS->is_errors() ) {
						$row->setError( CBTxt::T( $_PLUGINS->getErrorMSG() ) );
					}
				}

				if ( $row->getError() || ( ! $row->check() ) ) {
					if ( count( $toArray ) > 1 ) {
						// Multiple recipients were supplied so lets make sure the error is clear on which failed:
						$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SEND_TO_ERROR', 'Message failed to send to "[to]"! Error: [error]', array( '[to]' => htmlspecialchars( trim( $to ) ), '[error]' => $row->getError() ) ), 'error' );
					} else {
						$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
					}

					if ( $reply ) {
						$this->showMessage( $reply, $user );
						return;
					}

					$this->showMessageEdit( $id, $user );
					return;
				}

				if ( $reply && ( ! $replyTo->getInt( 'from_user', 0 ) ) ) {
					$cbNotification		=	new cbNotification();

					$toUser				=	new UserTable();
					$toUser->name		=	$replyTo->getString( 'from_name', '' );
					$toUser->username	=	$replyTo->getString( 'from_name', '' );
					$toUser->email		=	$replyTo->getString( 'from_email', '' );

					if ( ! cbIsValidEmail( $toUser->email ) ) {
						if ( count( $toArray ) > 1 ) {
							// Multiple recipients were supplied so lets make sure the error is clear on which failed:
							$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SEND_TO_ERROR', 'Message failed to send to "[to]"! Error: [error]', array( '[to]' => htmlspecialchars( trim( $to ) ), '[error]' => CBTxt::T( 'Public users email address is not valid!' ) ) ), 'error' );
						} else {
							$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => CBTxt::T( 'Public users email address is not valid!' ) ) ), 'error' );
						}

						$this->showMessage( $reply, $user );
						return;
					}

					$replyToName		=	$user->getFormattedName();
					$replyToEmail		=	$user->getString( 'email', '' );

					$subject			=	CBTxt::T( 'You have a new private message reply' );
					$message			=	CBTxt::T( 'FROM_HAS_REPLIED_GUEST_MESSAGE', '[from] has replied to your private message.<br /><br />[message]', array( '[from]' => $row->getFrom( 'profile_direct' ), '[message]' => $row->getMessage() ) );

					if ( ! $cbNotification->sendFromSystem( $toUser, $subject, $message, false, 1, null, null, null, array(), true, CBTxt::T( $this->params->getString( 'messages_notify_from_name', '' ) ), $this->params->getString( 'messages_notify_from_email', '' ), $replyToName, $replyToEmail ) ) {
						if ( count( $toArray ) > 1 ) {
							// Multiple recipients were supplied so lets make sure the error is clear on which failed:
							$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SEND_TO_ERROR', 'Message failed to send to "[to]"! Error: [error]', array( '[to]' => htmlspecialchars( trim( $to ) ), '[error]' => $cbNotification->errorMSG ) ), 'error' );
						} else {
							$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => $cbNotification->errorMSG ) ), 'error' );
						}

						$this->showMessage( $reply, $user );
						return;
					}
				} else {
					if ( $row->getError() || ( ! $row->store() ) ) {
						if ( count( $toArray ) > 1 ) {
							// Multiple recipients were supplied so lets make sure the error is clear on which failed:
							$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SEND_TO_ERROR', 'Message failed to send to "[to]"! Error: [error]', array( '[to]' => htmlspecialchars( trim( $to ) ), '[error]' => $row->getError() ) ), 'error' );
						} else {
							$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
						}

						if ( $reply ) {
							$this->showMessage( $reply, $user );
							return;
						}

						$this->showMessageEdit( $id, $user );
						return;
					}
				}

				if ( $reply ) {
					$returnUrl			=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'message', 'id' => $row->getInt( 'id', 0 ) ) );
				}

				$sent[]					=	$to;
			}

			if ( ! $sent ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => CBTxt::T( 'Nothing to send!' ) ) ), 'error' );

				if ( $reply ) {
					$this->showMessage( $reply, $user );
					return;
				}

				$this->showMessageEdit( $id, $user );
				return;
			}

			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message sent successfully!' ) );
		} else {
			$row->set( 'message', $message );

			if ( Application::MyUser()->isGlobalModerator() && $this->params->getBool( 'messages_system', true ) ) {
				$row->set( 'from_system', $this->getInput()->getInt( 'post/system', $row->getInt( 'from_system', 0 ) ) );
			}

			if ( $row->getError() || ( ! $row->check() ) ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SAVE_ERROR', 'Message failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

				$this->showMessageEdit( $id, $user );
				return;
			}

			if ( $row->getError() || ( ! $row->store() ) ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'PM_FAILED_SAVE_ERROR', 'Message failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

				$this->showMessageEdit( $id, $user );
				return;
			}

			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message saved successfully!' ) );
		}
	}

	/**
	 * @param UserTable $user
	 */
	private function saveQuickMessage( $user )
	{
		global $_CB_framework, $_PLUGINS, $_CB_PMS;

		if ( ! Application::Session()->checkFormToken() ) {
			return;
		}

		$to						=	CBuser::getUserDataInstance( $this->getInput()->getInt( 'to', 0 ) );
		$returnUrl				=	$_CB_framework->userProfileUrl( $to->getInt( 'id', 0 ), false, 'getmypmsproTab' );

		if ( ! $to->getInt( 'id', 0 ) ) {
			$returnUrl			=	$_CB_framework->userProfileUrl( $user->getInt( 'id', 0 ), false, 'getmypmsproTab' );

			if ( ! $user->getInt( 'id', 0 ) ) {
				$returnUrl		=	'index.php';
			}
		}

		$features				=	$_CB_PMS->getPMScapabilites();

		if ( UddeIM::isUddeIM() ) {
			if ( ( ! $user->getInt( 'id', 0 ) ) && ( ! ( isset( $features[0]['public'] ) && $features[0]['public'] ) ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to message this user.' ), 'error' );
			}
		} elseif ( ! PMSHelper::canMessage( $user->getInt( 'id', 0 ), $to->getInt( 'id', 0 ) ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to message this user.' ), 'error' );
		}

		if ( ! $to->getInt( 'id', 0 ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => CBTxt::T( 'To not specified!' ) ) ), 'error' );
		}

		if ( $to->getInt( 'id', 0 ) == $user->getInt( 'id', 0 ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => CBTxt::T( 'You can not message yourself!' ) ) ), 'error' );
		}

		$tab					=	new TabTable();

		$tab->load( array( 'pluginclass' => 'getmypmsproTab' ) );

		if ( ! ( $tab->getInt( 'enabled', 1 ) && Application::MyUser()->canViewAccessLevel( $tab->getInt( 'viewaccesslevel', 1 ) ) ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to send messages.' ), 'error' );
		}

		$checkCaptcha			=	$this->params->getInt( 'messages_captcha', 1 );

		if ( Application::MyUser()->isGlobalModerator() || ( ( $checkCaptcha == 2 ) && $user->getInt( 'id', 0 ) ) ) {
			$checkCaptcha		=	0;
		}

		if ( $checkCaptcha ) {
			$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

			if ( $_PLUGINS->is_errors() ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( $_PLUGINS->getErrorMSG() ), 'error' );
			}
		}

		$fromName				=	null;
		$fromEmail				=	null;

		if ( ( ! $user->getInt( 'id', 0 ) ) && isset( $features[0]['public'] ) && $features[0]['public'] ) {
			$fromName			=	$this->getInput()->getString( 'post/from_name', '' );
			$fromEmail			=	$this->getInput()->getString( 'post/from_email', '' );
		}

		if ( UddeIM::isUddeIM() ) {
			$message			=	$this->getInput()->getString( 'post/message', '' );
		} else {
			$messageLimit		=	( Application::MyUser()->isGlobalModerator() ? 0 : $this->params->getInt( 'messages_characters', 2500 ) );
			$messageEditor		=	$this->params->getInt( 'messages_editor', 2 );

			if ( ( $messageEditor == 3 ) && ( ! Application::MyUser()->isGlobalModerator() ) ) {
				$messageEditor	=	1;
			}

			if ( $messageEditor >= 2 ) {
				$message		=	$this->getInput()->getHtml( 'post/message', '' );
			} else {
				$message		=	$this->getInput()->getString( 'post/message', '' );
			}

			$message			=	PMSHelper::removeDuplicateSpacing( $message );

			if ( $messageLimit && ( cbutf8_strlen( $message ) > $messageLimit ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'MESSAGE_TOO_LONG', 'Message is too long! Please provide a message no longer than [limit] characters.', array( '[limit]' => $messageLimit ) ), 'error' );
			}
		}

		$send					=	$_CB_PMS->sendPMSMSG( $to->getInt( 'id', 0 ), $user->getInt( 'id', 0 ), null, $message, false, $fromName, $fromEmail );

		if ( is_array( $send ) && ( count( $send ) > 0 ) ) {
			$result				=	$send[0];
		} else {
			$result				=	false;
		}

		if ( ! $result ) {
			PMSHelper::returnRedirect( $returnUrl, $_PLUGINS->getErrorMSG(), 'error' );
		}

		PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message sent successfully!' ) );
	}

	/**
	 * Toggles the read state for a message or all messages
	 *
	 * @param int       $state
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function stateMessage( $state, $id, $user )
	{
		global $_CB_database, $_CB_framework;

		$returnUrl				=	PMSHelper::getReturn( true, true );

		if ( ! $id ) {
			// Mark all read or unread; note this is limited to batches of 100 messages:
			if ( ! $returnUrl ) {
				$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'messages' ) );
			}

			if ( $state ) {
				$query			=	"SELECT m.*"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' ) . " AS m"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages_read' ) . " AS r"
								.	" ON r." . $_CB_database->NameQuote( 'to_user' ) . " = " . $user->getInt( 'id', 0 )
								.	" AND r." . $_CB_database->NameQuote( 'message' ) . " = m." . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE ( ( m." . $_CB_database->NameQuote( 'from_user' ) . " != " . $user->getInt( 'id', 0 )
								.	" AND m." . $_CB_database->NameQuote( 'to_user' ) . " = 0 )"
								.	" OR ( m." . $_CB_database->NameQuote( 'to_user' ) . " = " . $user->getInt( 'id', 0 )
								.	" AND m." . $_CB_database->NameQuote( 'to_user_delete' ) . " = 0 ) )"
								.	"\n AND r." . $_CB_database->NameQuote( 'id' ) . " IS NULL";
				$_CB_database->setQuery( $query, 0, 100 );
				$rows			=	$_CB_database->loadObjectList( null, '\CB\Plugin\PMS\Table\MessageTable', array( $_CB_database ) );

				/** @var MessageTable[] $rows */
				foreach ( $rows as $row ) {
					$read		=	new ReadTable();

					$read->set( 'to_user', $user->getInt( 'id', 0 ) );
					$read->set( 'message', $row->getInt( 'id', 0 ) );

					if ( $read->getError() || ( ! $read->check() ) ) {
						PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_READ_ERROR', 'Message failed to mark read! Error: [error]', array( '[error]' => $read->getError() ) ), 'error' );
					}

					if ( $read->getError() || ( ! $read->store() ) ) {
						PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_READ_ERROR', 'Message failed to mark read! Error: [error]', array( '[error]' => $read->getError() ) ), 'error' );
					}
				}

				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Messages marked read successfully!' ) );
			} else {
				$query			=	"SELECT *"
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages_read' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'to_user' ) . " = " . $user->getInt( 'id', 0 );
				$_CB_database->setQuery( $query, 0, 100 );
				$rows			=	$_CB_database->loadObjectList( null, '\CB\Plugin\PMS\Table\ReadTable', array( $_CB_database ) );

				/** @var MessageTable[] $rows */
				foreach ( $rows as $row ) {
					if ( $row->getError() || ( ! $row->canDelete() ) ) {
						PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_UNREAD_ERROR', 'Message failed to mark unread! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
					}

					if ( $row->getError() || ( ! $row->delete() ) ) {
						PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_UNREAD_ERROR', 'Message failed to mark unread! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
					}
				}

				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Messages marked unread successfully!' ) );
			}
		}

		$row					=	new MessageTable();

		$row->load( (int) $id );

		if ( ! $returnUrl ) {
			if ( $row->getInt( 'id', 0 ) ) {
				$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'message', 'id' => $row->getInt( 'id', 0 ) ) );
			} else {
				$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'messages' ) );
			}
		}

		if ( ( ! $row->getInt( 'id', 0 ) )
			 || ( $user->getInt( 'id', 0 ) == $row->getInt( 'from_user', 0 ) )
			 || ( ! $user->getInt( 'id', 0 ) )
		) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to mark this message.' ), 'error' );
		} elseif ( $row->isDeleted( $user ) ) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message does not exist.' ), 'error' );
		}

		$read					=	new ReadTable();

		$read->load( array( 'to_user' => $user->getInt( 'id', 0 ), 'message' => $row->getInt( 'id', 0 ) ) );

		if ( $state ) {
			if ( ! $read->getInt( 'id', 0 ) ) {
				$read->set( 'to_user', $user->getInt( 'id', 0 ) );
				$read->set( 'message', $row->getInt( 'id', 0 ) );

				if ( $read->getError() || ( ! $read->check() ) ) {
					PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_READ_ERROR', 'Message failed to mark read! Error: [error]', array( '[error]' => $read->getError() ) ), 'error' );
				}

				if ( $read->getError() || ( ! $read->store() ) ) {
					PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_READ_ERROR', 'Message failed to mark read! Error: [error]', array( '[error]' => $read->getError() ) ), 'error' );
				}
			}

			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message marked read successfully!' ) );
		} elseif ( $read->getInt( 'id', 0 ) ) {
			if ( $read->getError() || ( ! $read->canDelete() ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_UNREAD_ERROR', 'Message failed to mark unread! Error: [error]', array( '[error]' => $read->getError() ) ), 'error' );
			}

			if ( $read->getError() || ( ! $read->delete() ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_UNREAD_ERROR', 'Message failed to mark unread! Error: [error]', array( '[error]' => $read->getError() ) ), 'error' );
			}

			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message marked unread successfully!' ) );
		}

		PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to mark this message.' ), 'error' );
	}

	/**
	 * @param int       $id
	 * @param UserTable $user
	 * @param bool      $isReport
	 */
	private function deleteMessage( $id, $user, $isReport = false )
	{
		global $_CB_framework;

		$row			=	new MessageTable();

		$row->load( (int) $id );

		$returnUrl		=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'messages' ) );

		if ( ( ! $row->getInt( 'id', 0 ) )
			 || ( ( $user->getInt( 'id', 0 ) != $row->getInt( 'from_user', 0 ) ) && ( $user->getInt( 'id', 0 ) != $row->getInt( 'to_user', 0 ) ) && ( ! Application::MyUser()->isGlobalModerator() ) )
			 || ( ! $user->getInt( 'id', 0 ) )
		) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to delete this message.' ), 'error' );
		}

		$delete			=	false;

		if ( ( ! $row->getInt( 'from_user', 0 ) ) || ( ! $row->getInt( 'to_user', 0 ) ) ) {
			$delete		=	true;
		} elseif ( $user->getInt( 'id', 0 ) == $row->getInt( 'from_user', 0 ) ) {
			if ( $row->getInt( 'to_user_delete', 0 ) || ( ! $row->getRead() ) || ( $user->getInt( 'id', 0 ) == $row->getInt( 'to_user', 0 ) ) ) {
				$delete	=	true;
			} else {
				$row->set( 'from_user_delete', 1 );
			}
		} elseif ( $user->getInt( 'id', 0 ) == $row->getInt( 'to_user', 0 ) ) {
			if ( $row->getInt( 'from_user_delete', 0 ) || ( $user->getInt( 'id', 0 ) == $row->getInt( 'from_user', 0 ) ) ) {
				$delete	=	true;
			} else {
				$row->set( 'to_user_delete', 1 );
			}
		}

		if ( $delete ) {
			if ( $row->getError() || ( ! $row->canDelete() ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_DELETE_ERROR', 'Message failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			if ( $row->getError() || ( ! $row->delete() ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_DELETE_ERROR', 'Message failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}
		} else {
			if ( $row->getError() || ( ! $row->check() ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_DELETE_ERROR', 'Message failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}

			if ( $row->getError() || ( ! $row->store() ) ) {
				PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'PM_FAILED_DELETE_ERROR', 'Message failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
			}
		}

		if ( $isReport ) {
			return;
		}

		PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'Message deleted successfully!' ) );
	}

	/**
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function reportMessage( $id, $user )
	{
		global $_CB_framework;

		$row				=	new MessageTable();

		$row->load( (int) $id );

		$returnUrl			=	$_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'messages' ) );

		if ( ( ! $row->getInt( 'id', 0 ) )
			 || ( ! $user->getInt( 'id', 0 ) )
			 || ( $user->getInt( 'id', 0 ) !== $row->getInt( 'to_user', 0 ) )
			 || $row->getBool( 'from_system', false )
		) {
			PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'You do not have permission to report this message.' ), 'error' );
		}

		$cbNotification		=	new cbNotification();
		$subject			=	CBTxt::T( 'A private message has been reported' );
		$message			=	CBTxt::T( 'USER_REPORTED_MESSAGE', '[to] has reported the following message.<br /><br />From: [from]<br />ID: [message_id]<br />Date: [message_date]<br />Message:<br /><br />[message]', array( '[to]' => $row->getTo( 'profile_direct' ), '[from]' => $row->getFrom( 'profile_direct' ), '[message_id]' => $row->getInt( 'id', 0 ), '[message]' => $row->getMessage(), '[message_date]' => cbFormatDate( $row->getString( 'date' ) ) ) );

		$cbNotification->sendToModerators( $subject, $message, false, 1 );

		PMSHelper::returnRedirect( $returnUrl, CBTxt::T( 'MESSAGE_REPORTED_SUCCESS', 'Message reported successfully! <a href="[delete_url]">Click here if you would also like to delete this message.</a>', array( '[delete_url]' => $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'message', 'func' => 'delete', 'id' => $row->getInt( 'id', 0 ), Application::Session()->getFormTokenName() => Application::Session()->getFormTokenValue() ) ) ) ) );
	}
}
