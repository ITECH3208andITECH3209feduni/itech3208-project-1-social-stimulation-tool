<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CB\Database\Table\ListTable;
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_comprofiler {

	static function emailUser( /** @noinspection PhpUnusedParameterInspection */ $option, $rowFrom, $rowTo, $allowPublic = 0, $name = '', $email = '', $subject = '', $message = '' ) {
		global $_CB_framework, $_PLUGINS;

		$beforeResults		=	implode( '', $_PLUGINS->trigger( 'onBeforeEmailUserForm', array( &$rowFrom, &$rowTo, 1, &$allowPublic, &$name, &$email, &$subject, &$message ) ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".  $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $allowPublic && ( ! $rowFrom->id ) ) {
			$warning		=	CBTxt::T( 'IMPORTANT:<ol class="m-0"><li>Please be aware that emails may not be received by the intended users due to their email settings and spam filter.</li></ol>' );
		} else {
			$warning		=	CBTxt::Th( 'UE_EMAILFORMWARNING', 'IMPORTANT:<ol class="m-0"><li>Your email address on your profile is: <strong>%s</strong>.</li><li>Make sure that it is accurate and check your spam filter before sending, because the receiver will use it for his reply.</li><li>Please be aware that emails may not be received by the intended users due to their email settings and spam filter.</li></ol>' );
		}

		$pageTitle			=	CBTxt::T( 'SEND_MESSAGE_TO_NAME', 'Send message to [name]', array( '[name]' => $rowTo->getFormattedName() ) );

		if ( $pageTitle ) {
			$_CB_framework->setPageTitle( $pageTitle );
			$_CB_framework->appendPathWay( $pageTitle );
		}

		$afterResults		=	implode( '', $_PLUGINS->trigger( 'onAfterEmailUserForm', array( &$rowFrom, &$rowTo, &$warning, 1, &$allowPublic, &$name, &$email, &$subject, &$message ) ) );

		outputCbTemplate( 1 );
		cbValidator::loadValidation();

		$pageClass			=	$_CB_framework->getMenuPageClass();

		$return				=	'<div class="cbEmailUser cb_template cb_template_' . selectTemplate( 'dir' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">';

		if ( $rowFrom->id == $rowTo->id ) {
			$return			.=		'<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . CBTxt::Th( 'UE_NOSELFEMAIL', 'You are not allowed to send an email to yourself!' ) . '</h3></div>';
		} else {
			$salt			=	cbMakeRandomString( 16 );
			$key			=	'cbmv1_' . md5( $salt . $rowTo->id . $rowTo->password . $rowTo->lastvisitDate . $rowFrom->password . $rowFrom->lastvisitDate ) . '_' . $salt;

			$toUser			=	CBuser::getInstance( (int) $rowTo->id, false );

			$return			.=		( CBTxt::Th( 'UE_EMAILFORMTITLE', 'Send a message via email to %s' ) ? '<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . sprintf( CBTxt::Th( 'UE_EMAILFORMTITLE', 'Send a message via email to %s' ), $toUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) ) . '</h3></div>' : null )
							.		'<form action="' . $_CB_framework->viewUrl( 'senduseremail' ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form cbValidation">';

			if ( $beforeResults ) {
				$return		.=			$beforeResults;
			}

			if ( $allowPublic && ( ! $rowFrom->id ) ) {
				$return		.=			'<div class="form-group row no-gutters cb_form_line">'
							.				'<label for="emailName" class="col-form-label col-sm-3 pr-sm-2">' . CBTxt::T( 'Name' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					'<input type="text" name="emailName" id="emailName" class="form-control required" size="50" maxlength="255" value="' . htmlspecialchars( $name ) . '" />'
							.					getFieldIcons( 1, 1, null )
							.				'</div>'
							.			'</div>'
							.			'<div class="form-group row no-gutters cb_form_line">'
							.				'<label for="emailAddress" class="col-form-label col-sm-3 pr-sm-2">' . CBTxt::T( 'Email Address' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					'<input type="text" name="emailAddress" id="emailAddress" class="form-control required" size="50" maxlength="255" value="' . htmlspecialchars( $email ) . '" />'
							.					getFieldIcons( 1, 1, null )
							.				'</div>'
							.			'</div>';
			}

			$return			.=			'<div class="form-group row no-gutters cb_form_line">'
							.				'<label for="emailSubject" class="col-form-label col-sm-3 pr-sm-2">' . CBTxt::T( 'Subject' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					'<input type="text" name="emailSubject" id="emailSubject" class="form-control required" size="50" maxlength="255" value="' . htmlspecialchars( $subject ) . '" />'
							.					getFieldIcons( 1, 1, null )
							.				'</div>'
							.			'</div>'
							.			'<div class="form-group row no-gutters cb_form_line">'
							.				'<label for="checkemail" class="col-form-label col-sm-3 pr-sm-2">' . CBTxt::T( 'Message' ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					'<textarea name="emailBody" id="emailBody" class="form-control required" cols="50" rows="15">' . htmlspecialchars( $message ) . '</textarea>'
							.					getFieldIcons( 1, 1, null )
							.				'</div>'
							.			'</div>';

			if ( $afterResults ) {
				$return		.=			'<div class="form-group row no-gutters cb_form_line">'
							.				'<div class="offset-sm-3 col-sm-9">'
							.					$afterResults
							.				'</div>'
							.			'</div>';
			}

			$return			.=			'<div class="form-group row no-gutters cb_form_line">'
							.				'<div class="offset-sm-3 col-sm-9">'
							.					sprintf( $warning, $rowFrom->email )
							.				'</div>'
							.			'</div>'
							.			'<div class="form-group row no-gutters cb_form_line">'
							.				'<div class="offset-sm-3 col-sm-9">'
							.					'<input type="submit" class="btn btn-primary cbEmailUserSubmit" value="' . htmlspecialchars( CBTxt::T( 'UE_SENDEMAIL', 'Send Email' ) ) . '"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
							.					' <input type="button" class="btn btn-secondary cbEmailUserCancel" value="' . htmlspecialchars( CBTxt::T( 'UE_CANCEL', 'Cancel' ) ) . '" onclick="window.location=\'' . $_CB_framework->userProfileUrl( (int) $rowTo->id ) . '\'; return false;" />'
							.				'</div>'
							.			'</div>'
							.			'<input type="hidden" name="fromID" value="' . (int) $rowFrom->id . '" />'
							.			'<input type="hidden" name="toID" value="' . (int) $rowTo->id . '" />'
							.			'<input type="hidden" name="protect" value="' . $key . '" />'
							.			Application::Session()->getFormTokenInput()
							.		'</form>'
							.	'</div>';
		}

		echo $return;

		$_CB_framework->setMenuMeta();
	}

/******************************
Profile Functions
******************************/

	/**
	 * @param UserTable   $user
	 * @param string      $option
	 * @param string      $submitvalue
	 * @param null|string $regErrorMSG
	 * @return void
	 */
	static function userEdit( $user, $option = '', $submitvalue = '', $regErrorMSG = null ): void
	{
		global $_CB_framework, $_PLUGINS;

		$integrations				=	implode( '', $_PLUGINS->trigger( 'onBeforeUserProfileEditDisplay', [ &$user, 1 ] ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".$_PLUGINS->getErrorMSG()."\"); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $regErrorMSG ) {
			$_CB_framework->enqueueMessage( $regErrorMSG, 'error' );
		}

		if ( $user->getInt( 'id', 0 ) !== Application::MyUser()->getUserId() ) {
			$_CB_framework->enqueueMessage( sprintf( CBTxt::T( 'UE_WARNING_EDIT_OTHER_USER_PROFILE', 'WARNING: This is not your profile. As a moderator, you are editing the profile of user: %s.' ), $user->getFormattedName() ) );
		}

		$_CB_framework->setDisplayedUser( $user->getInt( 'id', 0 ) );

		$cbTemplate					=	HTML_comprofiler::_cbTemplateLoad();
		$userEditTemplate			=	$_PLUGINS->getLoadedPlugin( 'templates', $cbTemplate );
		$userEditTitle				=	cbSetTitlePath( $user, CBTxt::T( 'UE_EDIT_TITLE', 'Edit Your Details' ), CBTxt::T( 'UE_EDIT_OTHER_USER_TITLE', 'Edit %s\'s Details' ) );

		// Fallback defaults encase template fails to load
		$userEditLayout				=	Application::Config()->getString( 'profile_edit_layout', 'tabbed' );
		$userEditCustom				=	'';
		$userEditScroll				=	false;
		$userEditLegend				=	Application::Config()->getInt( 'profile_edit_show_icons_explain', 0 );
		$userNavCollect				=	false;

		if ( $userEditTemplate ) {
			// Load in template parameters and set the layout
			if ( ! ( $userEditTemplate->getRaw( 'params' ) instanceof ParamsInterface ) ) {
				$userEditTemplate->set( 'params', new Registry( $userEditTemplate->getRaw( 'params' ) ) );
			}

			$userEditLayout			=	$userEditTemplate->params->getString( 'profile_edit_layout', $userEditLayout );
			$userEditCustom			=	$userEditTemplate->params->getRaw( 'profile_edit_layout_custom', $userEditCustom );
			$userEditScroll			=	$userEditTemplate->params->getBool( 'profile_edit_layout_scroll', $userEditScroll );
			$userEditLegend			=	$userEditTemplate->params->getInt( 'profile_edit_layout_legend', $userEditLegend );
			$userNavCollect			=	$userEditTemplate->params->getBool( 'profile_edit_layout_nav', $userNavCollect );
		}

		outputCbTemplate();
		initToolTip();

		$tabs						=	new cbTabs( true, 1, null, ( $userEditLayout !== 'flat' ) );

		if ( $userNavCollect ) {
			$tabs->collectNavigation( [ 'CB', 'CBVertical', 'CBStepped', 'CBMenu' ] );
		}

		$tabContent					=	$tabs->getEditTabs( $user, null, 'htmledit', 'divs', 'edit', $userEditLayout );
		$tabNav						=	$tabs->getViewTabsNav();

		$topIcons					=	'';
		$bottomIcons				=	'';

		if ( $userEditLegend ) {
			$icons					=	getFieldIcons( 1, true, true, '', '', true );

			if ( in_array( $userEditLegend, [ 1, 3 ], true ) ) {
				$topIcons			=	$icons;
			}

			if ( in_array( $userEditLegend, [ 2, 3 ], true ) ) {
				$bottomIcons		=	$icons;
			}
		}

		$layoutData					=	[	&$user,
											$userEditTitle,
											&$tabContent,
											&$tabNav,
											$userEditLayout,
											$userEditCustom,
											$userEditScroll,
											$submitvalue,
											CBTxt::T( 'UE_CANCEL', 'Cancel' ),
											$topIcons,
											$bottomIcons,
											$integrations,
										];

		$return						=	$_PLUGINS->callTemplate( $cbTemplate, 'Edit', 'drawLayout', $layoutData, 'htmledit' )
									.	cbPoweredBy();

		$_CB_framework->setMenuMeta();

		$_PLUGINS->trigger( 'onAfterUserProfileEditDisplay', [ $user, $tabContent, &$return ] );

		echo $return;
	}

	/**
	 * @param UserTable $user
	 * @param string    $option
	 * @param string    $submitvalue
	 * @return void
	 */
	static function userProfile( $user, $option = '', $submitvalue = '' ): void
	{
		global $_CB_framework, $_POST, $_PLUGINS;

		$_PLUGINS->loadPluginGroup( 'user' );

		$_PLUGINS->trigger( 'onBeforeUserProfileRequest', [ &$user, 1 ] );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".$_PLUGINS->getErrorMSG()."\"); window.history.go(-1); </script>\n";
			exit();
		}

		$cbMyIsModerator		=	Application::MyUser()->isModeratorFor( Application::User( (int) $user->id ) );
		$cbUserIsModerator		=	Application::User( (int) $user->id )->isGlobalModerator();

		if ( ( $user->getInt( 'banned', 0 ) !== 0 ) || ( ( $user->getInt( 'block', 0 ) === 1 ) && $user->getInt( 'confirmed', 0 ) && $user->getInt( 'approved', 0 ) ) ) {
			if ( $user->getInt( 'banned', 0 ) !== 0 ) {
				if ( Application::MyUser()->getUserId() !== $user->getInt( 'id', 0 ) ) {
					$_CB_framework->enqueueMessage( CBTxt::T( 'UE_USERPROFILEBANNED', 'This profile has been banned by a moderator.' ) . ( $user->getString( 'bannedreason', '' ) && $cbMyIsModerator ? '<p>' . nl2br( $user->getString( 'bannedreason', '' ) ) . '</p>' : null ), 'error' );
				} else {
					$_CB_framework->enqueueMessage( CBTxt::T( 'UE_BANNED_CHANGE_PROFILE', 'Your Profile is banned. Only you and moderators can see it.<br />Please follow the request of the moderator, then choose moderation / unban to submit a request for unbanning your profile.' ) . ( $user->getString( 'bannedreason', '' ) ? '<p>' . nl2br( $user->getString( 'bannedreason', '' ) ) . '</p>' : null ), 'error' );
				}
			}

			if ( $user->getInt( 'block', 0 ) === 1 ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'UE_USERPROFILEBLOCKED', 'This profile is no longer available.' ), 'error' );
			}

			if ( ( Application::MyUser()->getUserId() !== $user->getInt( 'id', 0 ) ) && ( ! $cbMyIsModerator ) ) {
				return;
			}
		}

		if ( ! $user->getInt( 'confirmed', 0 ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'UE_USER_NOT_CONFIRMED', 'This user has not yet confirmed his email address and account!' ), ( $cbMyIsModerator ? 'warning' : 'error' ) );
		}

		if ( ! $user->getInt( 'approved', 0 ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'UE_USER_NOT_APPROVED', 'This user has not yet been approved by a moderator!' ), ( $cbMyIsModerator ? 'warning' : 'error' ) );
		}

		if ( ( ( ! $user->getInt( 'confirmed', 0 ) ) || ( ! $user->getInt( 'approved', 0 ) ) ) && ( ! $cbMyIsModerator ) ) {
			return;
		}

		$integrations			=	implode( '', $_PLUGINS->trigger( 'onBeforeUserProfileDisplay', [ &$user, 1, $cbUserIsModerator, $cbMyIsModerator ] ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".$_PLUGINS->getErrorMSG()."\"); window.history.go(-1); </script>\n";
			exit();
		}

		$_CB_framework->setDisplayedUser( $user->getInt( 'id', 0 ) );

		$cbTemplate				=	HTML_comprofiler::_cbTemplateLoad();
		$cbUser					=&	CBuser::getInstance( $user->getInt( 'id', 0 ), false );
		$template				=	$_PLUGINS->getLoadedPlugin( 'templates', $cbTemplate );

		// Fallback defaults encase template fails to load
		$userViewLayout			=	'canvas_other';
		$userViewCustom			=	'';
		$userViewScroll			=	false;
		$userMainLeft			=	Application::Config()->get( 'mainLayoutLeftSize', 3, GetterInterface::INT );
		$userMainMiddle			=	Application::Config()->get( 'mainLayoutMiddleSize', 0, GetterInterface::INT );
		$userMainRight			=	Application::Config()->get( 'mainLayoutRightSize', 3, GetterInterface::INT );

		if ( $template ) {
			if ( ! ( $template->getRaw( 'params' ) instanceof ParamsInterface ) ) {
				$template->set( 'params', new Registry( $template->getRaw( 'params' ) ) );
			}

			if ( ( $user->getInt( 'id', 0 ) === Application::MyUser()->getUserId() ) && ( Application::Input()->getString( 'get/viewas', '' ) !== 'other' ) ) {
				$userViewLayout	=	$template->params->getString( 'profile_view_layout_home', $userViewLayout );
				$userViewCustom	=	$template->params->getRaw( 'profile_view_layout_home_custom', $userViewCustom );
				$userViewScroll	=	$template->params->getBool( 'profile_view_layout_home_scroll', $userViewScroll );
				$userNavCollect	=	$template->params->getString( 'profile_view_layout_home_nav', '' );
				$userMainLeft	=	$template->params->getInt( 'profile_view_layout_home_main_left', $userMainLeft );
				$userMainMiddle	=	$template->params->getInt( 'profile_view_layout_home_main_middle', $userMainMiddle );
				$userMainRight	=	$template->params->getInt( 'profile_view_layout_home_main_right', $userMainRight );
			} else {
				$userViewLayout	=	$template->params->getString( 'profile_view_layout_other', $userViewLayout );
				$userViewCustom	=	$template->params->getRaw( 'profile_view_layout_other_custom', $userViewCustom );
				$userViewScroll	=	$template->params->getBool( 'profile_view_layout_other_scroll', $userViewScroll );
				$userNavCollect	=	$template->params->getString( 'profile_view_layout_other_nav', '' );
				$userMainLeft	=	$template->params->getInt( 'profile_view_layout_other_main_left', $userMainLeft );
				$userMainMiddle	=	$template->params->getInt( 'profile_view_layout_other_main_middle', $userMainMiddle );
				$userMainRight	=	$template->params->getInt( 'profile_view_layout_other_main_right', $userMainRight );
			}

			$cbUser->_getCbTabs()->collectNavigation( explode( '|*|', $userNavCollect ) );
		}

		$userViewTabs			=	$cbUser->getProfileView();
		$userViewTabsNav		=	$cbUser->getProfileViewNav();

		$_CB_framework->setPageTitle( cbUnHtmlspecialchars( $user->getFormattedName() ) );
		$_CB_framework->appendPathWay( $user->getFormattedName() );

		outputCbTemplate( 1 );
		initToolTip( 1 );

		$layoutData				=	[	&$user,
										&$userViewTabs,
										&$userViewTabsNav,
										$userViewLayout,
										$userViewCustom,
										$userViewScroll,
										[ 'left' => $userMainLeft, 'middle' => $userMainMiddle, 'right' => $userMainRight ],
										$integrations,
									];

		$return					=	$_PLUGINS->callTemplate( $cbTemplate, 'Profile', 'drawLayout', $layoutData, 'html' )
								.	cbPoweredBy();

		if ( Application::MyUser()->getUserId() !== $user->getInt( 'id', 0 ) ) {
			recordViewHit( Application::MyUser()->getUserId(), $user->getInt( 'id', 0 ), Application::Input()->getRequestIP() );
		}

		$_CB_framework->setMenuMeta();

		$_PLUGINS->trigger( 'onAfterUserProfileDisplay', [ $user, true, &$return ] );

		echo $return;
	}

	/**
	 * Loads CB template rendering engine...
	 *
	 */
	static function _cbTemplateLoad() {
		global $_PLUGINS;

		static $loaded			=	array();

		$element				=	selectTemplate( 'dir' );
		$templatePhpFile		=	selectTemplate( 'absolute_path' ) . '/' . $element . '.php';
		if ( ! is_readable( $templatePhpFile ) ) {
			$element			=	'default';
		}

		if ( ! isset( $loaded[$element] ) ) {
			$_PLUGINS->loadPluginGroup( 'templates', $element );
			$loaded[$element]	=	true;
		}
		return $element;
	}
	/**
	 * Invokes CB template rendering engine...
	 *
	 * @param  mixed      $cbTemplate
	 * @param  UserTable  $user
	 * @param  string     $view
	 * @param  string     $method
	 * @param  array      $paramsArray
	 * @param  string     $output       'html'
	 * @return string
	 */
	static function _cbTemplateRender( $cbTemplate, /** @noinspection PhpUnusedParameterInspection */ $user, $view, $method, $paramsArray, $output = 'html' ) {
		global $_PLUGINS;

		$element				=	$cbTemplate;		// for now as this...
		if ( ( $output == 'html' ) || ( $output == 'htmledit' ) ) {
			return '<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">' . $_PLUGINS->callTemplate( $element, $view, $method, $paramsArray, $output ) . '</div>';
		} else {
			return $_PLUGINS->callTemplate( $element, $view, $method, $paramsArray, $output );
		}
	}



/******************************
List Functions
******************************/

	/**
	 * @param ListTable     $row
	 * @param UserTable[]   $users
	 * @param array         $columns
	 * @param FieldTable[]  $fields
	 * @param array         $input
	 * @param string|null   $search
	 * @param int           $searchmode
	 * @param cbPageNav     $pageNav
	 * @param UserTable     $myUser
	 * @param FieldTable[]  $searchableFields
	 * @param stdClass      $searchValues
	 * @param cbTabs        $tabs
	 * @param string|null   $errorMsg
	 * @param bool          $listAll
	 * @param int           $random
	 */
	static function usersList( &$row, &$users, &$columns, &$fields, &$input, $search, $searchmode, $pageNav, &$myUser, &$searchableFields, &$searchValues, &$tabs, $errorMsg, $listAll = true, $random = 0 ) {
		global $_CB_framework, $_PLUGINS, $_POST, $_GET, $_REQUEST;

		$params							=	new Registry( $row->params );

		// The Itemid for this userlist; kept for trigger B/C:
		$Itemid							=	getCBprofileItemid( null, 'userslist', '&listid=' . (int) $row->listid );

		$results						=	$_PLUGINS->trigger( 'onBeforeDisplayUsersList', array( &$row, &$users, &$columns, &$fields, &$input, $row->listid, &$search, &$Itemid, 1 ) );	// $uid = 1

		// Plugin content divided by location:
		$pluginAdditions				=	array( 'search', 'header', 'footer' );
		$pluginAdditions['search']		=	array();
		$pluginAdditions['header']		=	array();
		$pluginAdditions['footer']		=	array();

		if ( is_array( $results ) && ( count( $results ) > 0 ) ) foreach ( $results as $res ) {
			if ( is_array( $res ) ) foreach ( $res as $k => $v ) {
				$pluginAdditions[$k][]	=	$v;
			}
		}

		outputCbTemplate( 1 );
		outputCbJs();
		cbValidator::loadValidation();

		$cbTemplate						=	HTML_comprofiler::_cbTemplateLoad();

		if ( $errorMsg ) {
			$_CB_framework->enqueueMessage( $errorMsg, 'error' );
		}

		// Page title and pathway:
		$listTitleHtml					=	cbReplaceVars( $row->title, $myUser );
		$listTitleNoHtml				=	strip_tags( cbReplaceVars( $row->title, $myUser, false, false ) );
		$listDescription				=	cbReplaceVars( $row->description, $myUser );

		$_CB_framework->setPageTitle( $listTitleNoHtml );
		$_CB_framework->appendPathWay( $listTitleHtml );

		// Add row click JS:
		if ( $params->get( 'allow_profilelink', 1 ) ) {
			$allowProfileLink			=	true;
		} else {
			$allowProfileLink			=	false;
		}

		if ( $params->get( 'profilelink_target', 0 ) ) {
			$profileLinkTarget			=	'_blank';
		} else {
			$profileLinkTarget			=	null;
		}

		$js								=	"$( '.cbUserListSelector' ).on( 'change', function( e ) {"
										.		"var url = $( this ).find( 'option[value=\"' + $( this ).val() + '\"]' ).data( 'url' );"
										.		"if ( url ) {"
										.			"window.location = url;"
										.		"} else {"
										.			"this.form.submit();"
										.		"}"
										.	"});";

		if ( $users && is_array( $users ) && $allowProfileLink ) {
			$js							.=	"var cbUserURLs = [];";

			foreach( $users as $user ) {
				$js						.=	"cbUserURLs[" . (int) $user->id . "] = '" . addslashes( $_CB_framework->userProfileUrl( (int) $user->id, false ) ) . "';";
			}

			$js							.=	"$( '.cbUserListRow' ).on( 'click', function( e ) {"
										.		"if ( ! ( $( e.target ).is( 'a' ) || $( e.target ).closest( 'a' ).length || $( e.target ).hasClass( 'cbClicksInside' ) || $( e.target ).closest( '.cbClicksInside' ).length || ( $( this ).attr( 'id' ) == '' ) ) ) {"
										.			"var index = $( this ).data( 'id' );"
										.			"if ( $( this ).data( 'target' ) ) {"
										.				"window.open( cbUserURLs[index] );"
										.			"} else {"
										.				"window.location = cbUserURLs[index];"
										.			"}"
										.			"return false;"
										.		"}"
										.	"});";
		}

		$_CB_framework->outputCbJQuery( $js );

		// Search JS:
		$isSearching					=	( $search !== null );
		$searchValuesCount				=	count( get_object_vars( $searchValues ) );

		if ( $searchValuesCount && $users && $params->get( 'list_search_collapse', 0 ) ) {
			$isCollapsed				=	true;
		} elseif ( $searchmode == 0 ) {
			$isCollapsed				=	true;
		} else {
			$isCollapsed				=	false;
		}

		if ( count( $searchableFields ) > 0 ) {
			cbUsersList::outputAdvancedSearchJs( ( $isCollapsed ? null : $search ) );
		}

		// Base form URL:
		$baseUrl						=	$_CB_framework->viewUrl( array( 'userslist', 'listid' => (int) $row->listid ), true, array( 'searchmode' => 0 ) );

		// Searching attributes:
		$showAll						=	( $search === null );
		$criteriaTitle					=	cbReplaceVars( CBTxt::Th( 'UE_SEARCH_CRITERIA', 'Search criteria' ), $myUser );

		if ( ( $searchmode == 0 ) || ( ( $searchmode == 1 ) && $searchValuesCount ) || ( $searchmode == 2 ) ) {
			$resultsTitle				=	cbReplaceVars( CBTxt::Th( 'UE_SEARCH_RESULTS', 'Search results' ), $myUser );
		} else {
			$resultsTitle				=	null;
		}

		// Search content:
		$searchTabContent				=	$tabs->getSearchableContents( $searchableFields, $myUser, $searchValues, $params->get( 'list_compare_types', 0 ) );

		if ( count( $pluginAdditions['search'] ) ) {
			$searchTabContent			.=	'<div class="cbUserListSearchPlugins">'
										.		'<div>' . implode( '</div><div>', $pluginAdditions['search'] ) . '</div>'
										.	'</div>';
		}

		// Prepare SEO URL for paging:
		$pageNav->setStaticLimit( true );

		$canPage						=	( $params->get( 'list_paging', 1 ) && ( $pageNav->total > $pageNav->limit ) );
		$urlParts						=	array();

		// Add search mode; defaults to 0 so don't bother adding if we don't have to:
		if ( $searchmode ) {
			$urlParts['searchmode']		=	(int) $searchmode;
		}

		foreach ( get_object_vars( $searchValues ) as $k => $v ) {
			if ( is_array( $v ) ) {
				foreach ( $v as $kk => $vv ) {
					$kk					=	$k . '[' . $kk . ']';

					$urlParts[$kk]		=	urlencode( $vv ); // double encode (will be encoded again in viewUrl) to workaround Joomla SEF decoding
				}
			} else {
				$urlParts[$k]			=	urlencode( $v ); // double encode (will be encoded again in viewUrl) to workaround Joomla SEF decoding
			}
		}

		if ( $random ) {
			$urlParts['rand']			=	(int) $random;
		}

		$pagingUrl						=	$_CB_framework->viewUrl( array( 'userslist', 'listid' => (int) $row->listid ), false, $urlParts );

		// Ensure the URL isn't too long for browsers to prevent search criteria from being lost:
		if ( strlen( $pagingUrl ) < 2083 ) {
			$pageNav->setBaseURL( $pagingUrl );

			// Check if we need to redirect for SEO friendly URLs (only if searching and only if from POST):
			if ( $isSearching && Application::Input()->get( 'post/listid', 0, GetterInterface::INT ) && $urlParts ) {
				cbRedirect( $pagingUrl );
			}
		}

		// User row content:
		$tableContent					=&	HTML_comprofiler::_getListTableContent( $users, $columns, $fields );

		if ( $params->get( 'list_grid_layout', 0 ) ) {
			$layout						=	'grid';
		} else {
			$layout						=	'list';
		}

		if ( $params->get( 'list_show_selector', 1 ) ) {
			$listSelector				=	true;
		} else {
			$listSelector				=	false;
		}

		$pageClass						=	$_CB_framework->getMenuPageClass();

		$return							=	'<div class="cbUsersList cb_template cb_template_' . selectTemplate( 'dir' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">'
										.		'<form action="' . $_CB_framework->viewUrl( array( 'userslist', 'listid' => (int) $row->listid ), true, array( 'searchmode' => (int) $searchmode ) ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form cbValidation">'
										.			( ( ! $listSelector ) || ( ! isset( $input['plists'] ) ) ? '<input type="hidden" name="listid" value="' . (int) $row->listid . '" />' : null )
										.			'<input type="hidden" name="limitstart" value="0" />'
										.			'<input type="hidden" name="search" value="" />'
										.			( $random ? '<input type="hidden" name="rand" value="' . (int) $random . '" />' : null )
										.			Application::Session()->getFormTokenInput()
										.			$_PLUGINS->callTemplate( $cbTemplate, 'List', 'drawListHead', array( &$input, $row->listid, $pageNav->total, $showAll, $searchTabContent, $isSearching, $baseUrl, $listTitleHtml, $listDescription, $criteriaTitle, $resultsTitle, $listAll, $listSelector, $isCollapsed, $searchmode ), 'html' );

		if ( ( $searchmode == 0 ) || ( ( $searchmode == 1 ) && $searchValuesCount ) || ( $searchmode == 2 ) ) {
			if ( count( $pluginAdditions['header'] ) ) {
				$return					.=			'<div class="cbUserListHeader">'
										.				'<div>' . implode( '</div><div>', $pluginAdditions['header'] ) . '</div>'
										.			'</div>';
			}

			$return						.=			$_PLUGINS->callTemplate( $cbTemplate, 'List', 'drawListBody', array( &$users, &$columns, &$tableContent, $row->listid, $allowProfileLink, $profileLinkTarget, $layout, $params->getInt( 'list_grid_layout_size' ), $searchmode ), 'html' );

			if ( $canPage ) {
				$return					.=			'<div class="mt-3 cbUserListPagination cbUserListPaginationBottom">'
										.				$pageNav->getListLinks()
										.			'</div>';
			}

			if ( count( $pluginAdditions['footer'] ) ) {
				$return					.=			'<div class="cbUserListFooter">'
										.				'<div>' . implode( '</div><div>', $pluginAdditions['footer'] ) . '</div>'
										.			'</div>';
			}
		}

		$return							.=		'</form>'
										.	'</div>'
										.	cbPoweredBy();

		echo $return;

		$_CB_framework->setMenuMeta();
	}	// end function usersList

	static function & _getListTableContent( &$users, &$columns, &$fields ) {
		global $_CB_framework, $_PLUGINS;

		$displayedUser									=	$_CB_framework->getDisplayedUser();
		$tableContent									=	array();

		if ( is_array( $users ) && ( count( $users ) > 0 ) ) {
			foreach( $users as $userIdx => $user ) {
				$_CB_framework->setDisplayedUser( $user->getInt( 'id', 0 ) );

				$tableContent[$userIdx]					=	array();

				foreach ( $columns as $colIdx => $column ) {
					$tableContent[$userIdx][$colIdx]	=	array();

					foreach ( $column->fields as $fieldIdx => $colField ) {
						$fieldId						=	( isset( $colField['fieldid'] ) ? $colField['fieldid'] : null );

						if ( $fieldId && isset( $fields[$fieldId] ) ) {
							$field						=	$fields[$fieldId];

							$tableContent[$userIdx][$colIdx][$fieldIdx]		=	new stdClass();

							$fieldView					=&	$tableContent[$userIdx][$colIdx][$fieldIdx];
							$fieldView->name			=	$field->name;
							$fieldView->value			=	$_PLUGINS->callField( $field->type, 'getFieldRow', array( &$field, &$user, 'html', 'none', 'list', 0 ), $field );

							if ( is_string( $fieldView->value ) && ( trim( $fieldView->value ) == '' ) ) {
								$fieldView->value		=	null;
							}

							$fieldView->title			=	$_PLUGINS->callField( $field->type, 'getFieldTitle', array( &$field, &$user, 'html', 'list' ), $field );

							if ( is_string( $fieldView->title ) && ( trim( $fieldView->title ) == '' ) ) {
								$fieldView->title		=	null;
							}

							$fieldView->display			=	( isset( $colField['display'] ) ? $colField['display'] : 4 );
						}
					}
				}
			}
		}

		$_CB_framework->setDisplayedUser( $displayedUser );

		return $tableContent;
	}

/******************************
Registration Functions
******************************/

	static function confirmation() {
		outputCbTemplate( 1 );

		$htmlSuccess	=	CBTxt::Th( 'UE_SUBMIT_SUCCESS', 'Submission Success!' );

		$return		=	'<div class="cbRegConfirmation cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
					.		( $htmlSuccess ? '<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . $htmlSuccess . '</h3></div>' : null )
					.		'<div>' . CBTxt::Th( 'UE_SUBMIT_SUCCESS_DESC', 'Your item has been successfully submitted to our administrators. It will be reviewed before being published on this site.' ) . '</div>'
					.	'</div>';

		echo $return;
	}

	static function lostPassForm( /** @noinspection PhpUnusedParameterInspection */ $option ) {
		global $_CB_framework, $ueConfig, $_PLUGINS;

		$results				=	$_PLUGINS->trigger( 'onLostPassForm', array( 1 ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"" . $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
			exit();
		}

		$usernameExists			=	( ( isset( $ueConfig['login_type'] ) ) && ( $ueConfig['login_type'] != 2 ) );
		$pageTitle				=	( $usernameExists ? CBTxt::Th( 'UE_LOST_USERNAME_OR_PASSWORD', 'Lost your Username or your Password ?' ) : CBTxt::T( 'UE_LOST_YOUR_PASSWORD', 'Lost your Password ?' ) );

		outputCbTemplate( 1 );
		cbValidator::loadValidation();
		initToolTip( 1 );

		$js						=	"$( '#checkusername,#checkemail' ).keyup( function() {"
								.		"$( this ).next( '.cb_result_container' ).remove();"
								.		"if ( $.trim( $( '#checkusername' ).val() ) != '' ) {"
								.			"if ( $.trim( $( '#checkemail' ).val() ) == '' ) {"
								.				"$( '.cbLostPassSend' ).prop( 'disabled', true );"
								.			"} else {"
								.				"$( '.cbLostPassSend' ).prop( 'disabled', false );"
								.			"}"
								.		"} else {"
								.			"if ( $.trim( $( '#checkemail' ).val() ) == '' ) {"
								.				"$( '.cbLostPassSend' ).prop( 'disabled', true );"
								.			"} else {"
								.				"$( '.cbLostPassSend' ).prop( 'disabled', false );"
								.			"}"
								.		"}"
								.	"});";

		if ( $usernameExists ) {
			$js					.=	"$( '#reminderUsername,#reminderPassword' ).click( function() {"
								.		"$( '#checkusername,#checkemail' ).next( '.cb_result_container' ).remove();"
								.		"$( '#checkusername,#checkemail' ).val( '' );"
								.		"$( '.cbLostPassSend' ).prop( 'disabled', true );"
								.		"$( '.cb_forgot_line,.cb_forgot_button' ).show();"
								.		"if ( $( '#reminderUsername' ).prop( 'checked' ) ) {"
								.			"if ( $( '#reminderPassword' ).prop( 'checked' ) ) {"
								.				"$( '.cbLostPassSend' ).val( '" . addslashes( CBTxt::Th( 'UE_BUTTON_SEND_USERNAME_PASS', 'Send Username/Password' ) ) . "' );"
								.				"$( '#lostusernamedesc,#lostpassdesc' ).hide();"
								.				"$( '#lostusernamepassdesc' ).show();"
								.			"} else {"
								.				"$( '.cbLostPassSend' ).val( '" . addslashes( CBTxt::Th( 'UE_BUTTON_SEND_USERNAME', 'Send Username' ) ) . "' );"
								.				"$( '#lostusernamepassdesc,#lostpassdesc' ).hide();"
								.				"$( '#lostusernamedesc' ).show();"
								.			"}"
								.			"$( '#lostpassusername' ).hide();"
								.			"$( '#lostpassemail' ).show();"
								.		"} else {"
								.			"if ( $( '#reminderPassword' ).prop( 'checked' ) ) {"
								.				"$( '.cbLostPassSend' ).val( '" . addslashes( CBTxt::Th( 'UE_BUTTON_SEND_PASS', 'Send Password' ) ) . "' );"
								.				"$( '#lostusernamepassdesc,#lostusernamedesc' ).hide();"
								.				"$( '#lostpassusername,#lostpassemail,#lostpassdesc' ).show();"
								.			"} else {"
								.				"$( '.cb_forgot_line,.cb_forgot_button,#lostusernamepassdesc,#lostusernamedesc,#lostpassdesc' ).hide();"
								.			"}"
								.		"}"
								.	"});"
								.	"$( '.cb_forgot_line,.cb_forgot_button,#lostusernamepassdesc,#lostusernamedesc,#lostpassdesc' ).hide();";
		}

		$_CB_framework->outputCbJQuery( $js );

		$pageClass				=	$_CB_framework->getMenuPageClass();

		$return					=	'<div class="cbLostPassForm cb_template cb_template_' . selectTemplate( 'dir' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">'
								.		( $pageTitle ? '<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . $pageTitle . '</h3></div>' : null )
								.		'<form action="' . $_CB_framework->viewUrl( 'sendNewPass', true, null, 'html', ( checkCBPostIsHTTPS( true ) ? 1 : 0 ) ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form cbValidation">';

		if ( $usernameExists ) {
			$return				.=			'<div class="form-group row no-gutters cb_form_line" id="lostpassreminder">'
								.				'<label for="typeofloose" class="col-form-label col-sm-3 pr-sm-2 pt-0 pb-0">' . CBTxt::Th( 'UE_REMINDER_NEEDED_FOR', 'Reminder needed for' ) . '</label>'
								.				'<div class="cb_field col-sm-9">'
								.					'<div class="cbSnglCtrlLbl form-check form-check-inline">'
								.						'<input type="checkbox" id="reminderUsername" name="typeofloose[]" value="username" class="form-check-input" />'
								.						'<label for="reminderUsername" class="form-check-label">'
								.							CBTxt::Th( 'UE_LOST__USERNAME', 'Lost Username' )
								.						'</label>'
								.					'</div>'
								.					'<div class="cbSnglCtrlLbl form-check form-check-inline">'
								.						'<input type="checkbox" id="reminderPassword" name="typeofloose[]" value="password" class="form-check-input" />'
								.						'<label for="reminderPassword" class="form-check-label">'
								.							CBTxt::Th( 'UE_LOST__PASSWORD', 'Lost Password' )
								.						'</label>'
								.					'</div>'
								.				'</div>'
								.			'</div>'
								.			'<div class="form-group row no-gutters cb_form_line" id="lostusernamedesc">'
								.				'<div class="cb_field offset-sm-3 col-sm-9">'
								.					CBTxt::Th( 'UE_LOST_USERNAME_ONLY_DESC', 'If you <strong>lost your username</strong>, please enter your E-mail Address, then click the Send Username button, and your username will be sent to your email address.' )
								.				'</div>'
								.			'</div>'
								.			'<div class="form-group row no-gutters cb_form_line" id="lostusernamepassdesc">'
								.				'<div class="cb_field offset-sm-3 col-sm-9">'
								.					CBTxt::Th( 'UE_LOST_USERNAME_PASSWORD_DESC', 'If you <strong>forgot both your username and your password</strong>, please recover the username first, then the password. To recover your username, please enter your E-mail Address, leaving Username field empty, then click the Send Username button, and your username will be sent to your email address. From there you can use this same form to recover your password.' )
								.				'</div>'
								.			'</div>';
		}

		$return					.=			'<div class="form-group row no-gutters cb_form_line" id="lostpassdesc">'
								.				'<div class="cb_field offset-sm-3 col-sm-9">';

		if ( $usernameExists ) {
			$return				.=					CBTxt::Th( 'UE_LOST_PASSWORD_DESC', 'If you <strong>lost your password</strong> but know your username, please enter your Username and your E-mail Address, press the Send Password button, and you will receive a new password shortly. Use this new password to access the site.' );
		} else {
			$return				.=					CBTxt::Th( 'UE_LOST_PASSWORD_EMAIL_ONLY_DESC', 'If you <strong>lost your password</strong>, please enter your E-mail Address, then click the Send Password button, and you will receive a new password shortly. Use this new password to access the site.' );
		}

		$return					.=				'</div>'
								.			'</div>';

		if ( $usernameExists ) {
			if ( Application::Config()->get( 'reg_username_checker', 0 ) ) {
				$usernameValidation		=	cbValidator::getRuleHtmlAttributes( 'cbfield', array( 'user' => 0, 'field' => 'username', 'reason' => 'register', 'function' => 'testexists' ) );
			} else {
				$usernameValidation		=	null;
			}

			$return				.=			'<div class="cb_forgot_line form-group row no-gutters cb_form_line" id="lostpassusername">'
								.				'<label for="checkusername" class="col-form-label col-sm-3 pr-sm-2">' . CBTxt::Th( 'PROMPT_UNAME', 'Username:' ) . '</label>'
								.				'<div class="cb_field col-sm-9">'
								.					'<input type="text" name="checkusername" id="checkusername" class="form-control" size="30" maxlength="255"' . $usernameValidation . ' />'
								.				'</div>'
								.			'</div>';
		}

		$emailField				=	new FieldTable();

		$emailField->load( array( 'name' => 'email' ) );

		$emailParams			=	new Registry( $emailField->get( 'params' ) );

		if ( in_array( $emailParams->getInt( 'field_check_email', 0 ), [ 2, 3 ], true ) ) {
			$emailValidation	=	cbValidator::getRuleHtmlAttributes( 'cbfield', array( 'user' => 0, 'field' => 'email', 'reason' => 'register', 'function' => 'testexists' ) );
		} else {
			$emailValidation	=	null;
		}


		$return					.=			'<div class="cb_forgot_line form-group row no-gutters cb_form_line" id="lostpassemail">'
								.				'<label for="checkemail" class="col-form-label col-sm-3 pr-sm-2">' . CBTxt::Th( 'PROMPT_EMAIL', 'E-mail Address:' ) . '</label>'
								.				'<div class="cb_field col-sm-9">'
								.					'<input type="text" name="checkemail" id="checkemail" class="form-control" size="30" maxlength="255"' . $emailValidation . ' />'
								.				'</div>'
								.			'</div>';

		if ( is_array( $results ) ) foreach ( $results as $result ) {
			if ( $result ) {
				$return			.=			'<div class="cb_forgot_line form-group row no-gutters cb_form_line">'
								.				'<label' . ( isset( $result[2] ) ? ' for="' . htmlspecialchars( $result[2] ) . '"' : null ) . ' class="col-form-label col-sm-3 pr-sm-2">' . ( isset( $result[0] ) ? $result[0] : null ) . '</label>'
								.				'<div class="cb_field col-sm-9">'
								.					( isset( $result[1] ) ? $result[1] : null )
								.				'</div>'
								.			'</div>';
			}
		}

		$return					.=			'<div class="cb_forgot_button form-group row no-gutters cb_form_line">'
								.				'<div class="offset-sm-3 col-sm-9">'
								.					'<input type="submit" class="btn btn-primary cbLostPassSend" value="'
								.						htmlspecialchars(
															$usernameExists ?
															CBTxt::Th( 'UE_BUTTON_SEND_USERNAME_PASS', 'Send Username/Password' )
															: CBTxt::Th( 'UE_BUTTON_SEND_PASS', 'Send Password' )
														)
								.					'" disabled="disabled"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
								.				'</div>'
								.			'</div>';

		if ( ! $usernameExists ) {
			$return				.= 			'<input type="hidden" name="typeofloose[]" value="password" />';
		}

		$return					.=			Application::Session()->getFormTokenInput()
								.		'</form>'
								.	'</div>'
								.	cbPoweredBy();

		echo $return;

		$_CB_framework->setMenuMeta();
	}

	static function loginForm( /** @noinspection PhpUnusedParameterInspection */ $option, &$postvars, $regErrorMSG = null, $messagesToUser = null, $alertmessages = null, $returnUrl = null ) {
		global $_CB_framework, $_CB_database, $_PLUGINS;

		$results					=	implode( '', $_PLUGINS->trigger( 'onBeforeLoginFormDisplay', array( &$postvars, &$regErrorMSG, &$messagesToUser, &$alertmessages ) ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"" . $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $regErrorMSG ) {
			$_CB_framework->enqueueMessage( $regErrorMSG, 'error' );
		}

		outputCbTemplate( 1 );
		outputCbJs();
		initToolTip( 1 );

		$params						=	null;
		$moduleFile					=	$_CB_framework->getCfg( 'absolute_path' ) . '/modules/mod_cblogin/mod_cblogin.php';

		if ( file_exists( $moduleFile ) ) {
			$language				=	CBuser::getMyUserDataInstance()->getUserLanguage();

			if ( ! $language ) {
				$language			=	Application::Cms()->getLanguageTag();
			}

			define( '_UE_LOGIN_FROM', 'loginform' );

			$query					=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__modules' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'module' ) . " = " . $_CB_database->Quote( 'mod_cblogin' )
									.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n AND " . $_CB_database->NameQuote( 'access' ) . " IN " . $_CB_database->safeArrayOfIntegers( Application::MyUser()->getAuthorisedViewLevels() )
									.	"\n AND " . $_CB_database->NameQuote( 'language' ) . " IN ( " . $_CB_database->Quote( $language ) . ", " . $_CB_database->Quote( '*' ) . ", " . $_CB_database->Quote( '' ) . " )"
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'position' ) . ", " . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query, 0, 1 );
			$module					=	null;
			$_CB_database->loadObject( $module );

			if ( $module ) {
				$moduleContent		=	JModuleHelper::renderModule( $module, array( 'style' => 'xhtml', 'return_url' => $returnUrl ) );
			} else {
				$moduleContent		=	CBTxt::T( 'Error: CB Login module not created (required).' );
			}
		} else {
			$moduleContent			=	CBTxt::T( 'Error: CB Login module not installed (required).' );
		}

		$return						=	null;

		if ( ( is_array( $messagesToUser ) && $messagesToUser ) || $results ) {
			$pageClass				=	$_CB_framework->getMenuPageClass();

			$return					.=	'<div class="cbLoginPage cb_template cb_template_' . selectTemplate( 'dir' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">'
									.		( is_array( $messagesToUser ) && $messagesToUser ? '<div>' . implode( '</div><div>', $messagesToUser ) . '</div>' : null )
									.		( $results ? $results : null )
									.	'</div>';
		}

		$return						.=	$moduleContent;

		echo $return;

		$_CB_framework->setMenuMeta();
	}

	/**
	 * @param string          $option
	 * @param string|int|bool $emailpass
	 * @param UserTable       $user
	 * @param array           $postvars
	 * @param null|string     $regErrorMSG
	 * @param bool            $stillDisplayLoginModule
	 * @param string          $regErrorLevel
	 * @param bool            $isInline
	 * @param string          $customLayout
	 * @return void
	 */
	static function registerForm( $option, $emailpass, $user, $postvars = [], $regErrorMSG = null, $stillDisplayLoginModule = false, $regErrorLevel = 'error', bool $isInline = false, string $customLayout = '', ?bool $forceHTTPS = null ): void
	{
		global $_CB_framework, $_CB_database, $_PLUGINS;

		$integrations				=	implode( '', $_PLUGINS->trigger( 'onBeforeRegisterFormDisplay', array( &$user, $regErrorMSG ) ) );

		if ( $_PLUGINS->is_errors() ) {
			if ( $isInline ) {
				return;
			}

			echo "<script type=\"text/javascript\">alert(\"".$_PLUGINS->getErrorMSG()."\"); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $regErrorMSG ) {
			$_CB_framework->enqueueMessage( $regErrorMSG, $regErrorLevel );
		}

		if ( $forceHTTPS === null ) {
			$forceHTTPS				=	checkCBPostIsHTTPS( true );
		}

		$cbTemplate					=	HTML_comprofiler::_cbTemplateLoad();
		$template					=	$_PLUGINS->getLoadedPlugin( 'templates', $cbTemplate );
		$userRegLayout				=	Application::Config()->getString( 'reg_layout', 'flat' );
		$userRegCustom				=	'';
		$userRegScroll				=	false;
		$userRegLegend				=	Application::Config()->getInt( 'reg_show_icons_explain', 0 );
		$userNavCollect				=	false;
		$titleCanvas				=	Application::Config()->getString( 'reg_title_img', 'none' );

		// CBTxt::T( 'REGISTRATION_GREETING', 'Welcome to our community - tell us about yourself and sign up' )
		$headerMessage				=	CBTxt::T( Application::Config()->getHtml( 'reg_intro_msg', '' ) );
		// CBTxt::T( 'REGISTRATION_CONCLUSION', 'Thanks for visiting our site! Hope you enjoy your stay!' )
		$footerMessage				=	CBTxt::T( Application::Config()->getHtml( 'reg_conclusion_msg', '' ) );

		$showLogin					=	Application::Config()->getBool( 'reg_show_login_on_page', false );

		if ( $template ) {
			if ( ! ( $template->getRaw( 'params' ) instanceof ParamsInterface ) ) {
				$template->set( 'params', new Registry( $template->getRaw( 'params' ) ) );
			}

			$userRegLayout			=	$template->params->getString( 'registration_layout', $userRegLayout );
			$userRegCustom			=	$template->params->getRaw( 'registration_layout_custom', $userRegCustom );
			$userRegScroll			=	$template->params->getBool( 'registration_layout_scroll', $userRegScroll );
			$userRegLegend			=	$template->params->getInt( 'registration_layout_legend', $userRegLegend );
			$userNavCollect			=	$template->params->getBool( 'registration_layout_nav', $userNavCollect );
			$showLogin				=	$template->params->getBool( 'registration_layout_login', $showLogin );
			$headerMessage			=	CBTxt::T( $template->params->getHtml( 'registration_layout_header', $headerMessage ) );
			$footerMessage			=	CBTxt::T( $template->params->getHtml( 'registration_layout_footer', $footerMessage ) );
			$titleCanvas			=	$template->params->getString( 'registration_layout_canvas', $titleCanvas );
		}

		if ( $customLayout ) {
			$userRegLayout			=	$customLayout;
		}

		outputCbTemplate();
		outputCbJs();
		initToolTip();

		$translatedRegistrationTitle	=	CBTxt::T( 'UE_REGISTRATION', 'Sign up' );

		if ( $translatedRegistrationTitle && ( ! $isInline ) ) {
			$_CB_framework->setPageTitle( $translatedRegistrationTitle );
			$_CB_framework->appendPathWay( $translatedRegistrationTitle );
		}

		$tabs							=	new cbTabs( ( $userRegLayout !== 'stepped' ), 1, null, ( $userRegLayout !== 'flat' ) );

		if ( $userNavCollect ) {
			$tabs->collectNavigation( [ 'CB', 'CBVertical', 'CBStepped', 'CBMenu' ] );
		}

		$tabContent						=	$tabs->getEditTabs( $user, null, 'htmledit', 'divs', 'register', $userRegLayout );
		$tabNav							=	$tabs->getViewTabsNav();

		$topIcons						=	'';
		$bottomIcons					=	'';

		if ( $userRegLegend ) {
			$icons						=	getFieldIcons( 1, true, true, '', '', true );

			if ( in_array( $userRegLegend, [ 1, 3 ], true ) ) {
				$topIcons				=	$icons;
			}

			if ( in_array( $userRegLegend, [ 2, 3 ], true ) ) {
				$bottomIcons			=	$icons;
			}
		}

		$moduleContent					=	'';

		if ( $showLogin && ( $stillDisplayLoginModule || ( ! $regErrorMSG ) ) ) {
			$moduleFile					=	$_CB_framework->getCfg( 'absolute_path' ) . '/modules/mod_cblogin/mod_cblogin.php';

			if ( file_exists( $moduleFile ) ) {
				define( '_UE_LOGIN_FROM', 'loginform' );

				$query					=	'SELECT *'
										.	"\n FROM " . $_CB_database->NameQuote( '#__modules' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'module' ) . " = " . $_CB_database->Quote( 'mod_cblogin' )
										.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
										.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' );
				$_CB_database->setQuery( $query, 0, 1 );
				$module					=	null;
				$_CB_database->loadObject( $module );

				if ( $module ) {
					$moduleContent		=	JModuleHelper::renderModule( $module, [ 'style' => 'xhtml' ] );
				} else {
					$moduleContent		=	CBTxt::T( 'Error: CB Login module not created (required).' );
				}
			} else {
				$moduleContent			=	CBTxt::T( 'Error: CB Login module not installed (required).' );
			}
		}

		if ( $titleCanvas !== 'none' ) {
			if ( $titleCanvas === 'general' ) {
				$titleCanvas			=	selectTemplate() . 'images/title/general.jpg';
			} else {
				$titleCanvas			=	$_CB_framework->getCfg( 'live_site' ) . '/images/' . $titleCanvas;
			}
		} else {
			$titleCanvas				=	'';
		}

		$layoutData						=	[	&$user,
												CBTxt::Th( 'REGISTER_TITLE', 'Join us!' ),
												&$tabContent,
												&$tabNav,
												$userRegLayout,
												$userRegCustom,
												$userRegScroll,
												$headerMessage,
												$footerMessage,
												CBTxt::Th( 'UE_REGISTER', 'Sign up' ),
												$moduleContent,
												$topIcons,
												$bottomIcons,
												(bool) $emailpass,
												$titleCanvas,
												$integrations,
												$forceHTTPS,
											];

		$return							=	$_PLUGINS->callTemplate( $cbTemplate, 'Registration', 'drawLayout', $layoutData, 'htmledit' )
										.	cbPoweredBy();

		if ( ! $isInline ) {
			$_CB_framework->setMenuMeta();
		}

		$_PLUGINS->trigger( 'onAfterRegisterFormDisplay', [ $user, $tabContent, &$return ] );

		echo $return;
	}


/******************************
Moderation Functions
******************************/

	static function reportUserForm( /** @noinspection PhpUnusedParameterInspection */ $option, $uid, $reportedByUser, $reportedUser ) {
		global $_CB_framework, $ueConfig, $_PLUGINS;

		$results			=	implode( '', $_PLUGINS->trigger( 'onBeforeReportUserFormDisplay', array( $uid, &$reportedByUser, &$reportedUser ) ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".  $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $ueConfig['allowUserReports'] == 0 ) {
				echo CBTxt::Th( 'UE_FUNCTIONALITY_DISABLED', 'This functionality is currently disabled.' );
				return;
		}

		outputCbTemplate( 1 );
		cbValidator::loadValidation();

		$return				=	'<div class="cbReportUserForm cb_template cb_template_' . selectTemplate( 'dir' ) . '">';

		if ( $results ) {
			$return			.=		$results;
		}

		$return				.=		'<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . CBTxt::Th( 'UE_REPORTUSER_TITLE', 'Report User' ) . '</h3></div>'
							.		'<form action="' . $_CB_framework->viewUrl( 'reportuser' ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form cbValidation">'
							.			'<div class="form-group row no-gutters cb_form_line">'
							.				'<label for="reportexplaination" class="col-form-label col-sm-3 pr-sm-2">' . CBTxt::Th( 'UE_REPORTUSERSACTIVITY', 'Describe User Activity' ) . '</label>'
							.				'<div class="col-sm-9 cb_field">'
							.					'<textarea name="reportexplaination" cols="50" rows="8" maxlength="4000" class="form-control required"></textarea>'
							.				'</div>'
							.			'</div>'
							.			'<div class="row no-gutters cbReportUsrButtons">'
							.				'<div class="offset-sm-3 col-sm-9">'
							.					'<input type="submit" class="btn btn-primary cbReportUsrSubmit" value="' . htmlspecialchars( CBTxt::Th( 'UE_SUBMITFORM', 'Submit' ) ) . '"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
							.					' <input type="button" class="btn btn-secondary cbReportUsrCancel" value="' . htmlspecialchars( CBTxt::Th( 'UE_CANCEL', 'Cancel' ) ) . '" onclick="window.location=\'' . $_CB_framework->userProfileUrl( $uid ) . '\'; return false;" />'
							.				'</div>'
							.			'</div>'
							.			'<input type="hidden" name="reportedbyuser" value="' . (int) $_CB_framework->myId() . '" />'
							.			'<input type="hidden" name="reporteduser" value="' . (int) $uid . '" />'
							.			'<input type="hidden" name="reportform" value="0" />'
							.			Application::Session()->getFormTokenInput()
							.		'</form>'
							.	'</div>';

		echo $return;
	}

	static function banUserForm( /** @noinspection PhpUnusedParameterInspection */ $option, $uid, $act, $orgBannedReason, $bannedByUser, $bannedUser ) {
		global $_CB_framework, $ueConfig, $_PLUGINS;

		$results			=	implode( '', $_PLUGINS->trigger( 'onBeforeBanUserFormDisplay', array( $uid, &$orgBannedReason, &$bannedByUser, &$bannedUser ) ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".  $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
			exit();
		}

		if ( $ueConfig['allowUserBanning'] == 0 ) {
				echo CBTxt::Th( 'UE_FUNCTIONALITY_DISABLED', 'This functionality is currently disabled.' );
				return;
		}

		outputCbTemplate( 1 );
		cbValidator::loadValidation();

		$return				=	'<div class="cbBanUserForm cb_template cb_template_' . selectTemplate( 'dir' ) . '">';

		if ( $results ) {
			$return			.=		$results;
		}

		$pageTitle			=	( $_CB_framework->myId() != $uid ? CBTxt::Th( 'UE_REPORTBAN_TITLE', 'Ban Report' ) : CBTxt::T( 'UE_REPORTUNBAN_TITLE', 'Unbanning Report' ) );

		$return				.=		( $pageTitle ? '<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . $pageTitle . '</h3></div>' : null )
							.		'<form action="' . $_CB_framework->viewUrl( 'banProfile', true, array( 'act' => ( ( $_CB_framework->myId() != $uid ) ? 1 : 2 ), 'user' => (int) $uid ) ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form cbValidation">'
							.			'<div class="form-group row no-gutters cb_form_line">'
							.				'<label for="bannedreason" class="col-form-label col-sm-3 pr-sm-2">' . ( $_CB_framework->myId() != $uid ? CBTxt::Th( 'UE_BANREASON', 'Reason for Ban' ) : CBTxt::Th( 'UE_UNBANREQUEST', 'Unban Profile Request' ) ) . '</label>'
							.				'<div class="cb_field col-sm-9">'
							.					'<textarea name="bannedreason" cols="50" rows="8" maxlength="4000" class="form-control required"></textarea>'
							.				'</div>'
							.			'</div>'
							.			'<div class="row no-gutters cbBanUsrButtons">'
							.				'<div class="offset-sm-3 col-sm-9">'
							.					'<input type="submit" class="btn btn-primary cbBanUsrSubmit" value="' . htmlspecialchars( CBTxt::Th( 'UE_SUBMITFORM', 'Submit' ) ) . '"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
							.					' <input type="button" class="btn btn-secondary cbBanUsrCancel" value="' . htmlspecialchars( CBTxt::Th( 'UE_CANCEL', 'Cancel' ) ) . '" onclick="window.location=\'' . $_CB_framework->userProfileUrl( $uid ) . '\'; return false;" />'
							.				'</div>'
							.			'</div>'
							.			'<input type="hidden" name="bannedby" value="' . (int) $_CB_framework->myId() . '" />'
							.			'<input type="hidden" name="uid" value="' . (int) $uid . '" />'
							.			'<input type="hidden" name="orgbannedreason" value="' . htmlspecialchars( $orgBannedReason ) . '" />'
							.			'<input type="hidden" name="reportform" value="0" />'
							.			Application::Session()->getFormTokenInput()
							.		'</form>'
							.	'</div>';

		echo $return;
	}

static function pendingApprovalUsers( /** @noinspection PhpUnusedParameterInspection */ $option, $users ) {
	global $_CB_framework, $_PLUGINS;

	$results					=	implode( '', $_PLUGINS->trigger( 'onBeforePendingApprovalUsersFormDisplay', array( &$users ) ) );

	if ( $_PLUGINS->is_errors() ) {
		echo "<script type=\"text/javascript\">alert(\"".  $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
		exit();
	}

	outputCbJs();
	outputCbTemplate();

	$pageClass					=	$_CB_framework->getMenuPageClass();

	$return						=	'<div class="cbPendingApprovalUsers cb_template cb_template_' . selectTemplate( 'dir' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">';

	if ( $results ) {
		$return					.=		$results;
	}

	$return						.=		'<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . CBTxt::Th( 'UE_USERAPPROVAL_MODERATE', 'User Approval/Rejection' ) . '</h3></div>';

	if ( count( $users ) < 1 ) {
		$return					.=		CBTxt::Th( 'UE_NOUSERSPENDING', 'No Users Pending Approval' );
	} else {
		$toggleJs				=	"cbToggleAll( this, " . count( $users ) . ", 'uids' );";

		$return					.=		'<form action="' . $_CB_framework->viewUrl( 'pendingapprovaluser' ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form">'
								.			'<div class="table-responsive mb-3">'
								.				'<table class="table table-hover mb-0">'
								.					'<thead>'
								.						'<tr>'
								.							'<th style="width: 1%;" class="text-center"><input type="checkbox" name="toggle" value="" onclick="' . $toggleJs . '" /></th>'
								.							'<th class="text-left">' . CBTxt::Th( 'UE_USER', 'User' ) . '</th>'
								.							'<th style="width: 24%;" class="text-left xs-hidden">' . CBTxt::Th( 'UE_REGISTERDATE', 'Date Registered' ) . '</th>'
								.							'<th style="width: 24%;" class="text-left">' . CBTxt::Th( 'UE_COMMENT', 'Reject Comment' ) . '</th>'
								.						'</tr>'
								.					'</thead>'
								.					'<tbody>';

		for ( $i = 0; $i < count( $users ); $i++ ) {
			$user				=	$users[$i];

			$return				.=						'<tr>'
								.							'<td style="width: 1%;" class="text-center"><input type="checkbox" id="uids' . $i . '" name="uids[]" value="' . (int) $user->id . '" /></td>'
								.							'<td class="text-left">' . CBuser::getInstance( (int) $user->id, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '<br />' . $user->email . '</td>'
								.							'<td style="width: 24%;" class="text-left xs-hidden">' . cbFormatDate( $user->registerDate ) . '</td>'
								.							'<td style="width: 25%;" class="text-left"><textarea name="comment' . (int) $user->id . '" cols="20" rows="3" class="form-control w-100"></textarea></td>'
								.						'</tr>';
		}

		$return					.=					'</tbody>'
								.				'</table>'
								.			'</div>'
								.			'<div class="cbPendUserButtons">'
								.				'<input type="button" class="btn btn-success cbPendUserApprove" value="' . htmlspecialchars( CBTxt::Th( 'UE_APPROVE', 'Approve' ) ) . '" onclick="this.form.view.value=\'approveuser\'; this.form.submit();" />'
								.				' <input type="button" class="btn btn-danger cbPendUserReject" value="' . htmlspecialchars( CBTxt::Th( 'UE_REJECT', 'Reject' ) ) . '" onclick="this.form.view.value=\'rejectuser\'; this.form.submit();" />'
								.			'</div>'
								.			'<input type="hidden" name="view" value="" />'
								.			Application::Session()->getFormTokenInput()
								.		'</form>';
	}

	$return						.=	'</div>';

	echo $return;

	$_CB_framework->setMenuMeta();
}

/**
 * @param  array       $connections
 * @param  array       $actions
 * @param  int         $total
 * @param  cbTabs      $connMgmtTabs
 * @param  array       $pagingParams
 * @param  int         $perpage
 * @param  array|null  $connecteds
 */
static function manageConnections( $connections, $actions, $total, &$connMgmtTabs, &$pagingParams, $perpage, $connecteds = null ) {
	global $_CB_framework, $ueConfig, $_PLUGINS, $_REQUEST;

	$results						=	implode( '', $_PLUGINS->trigger( 'onBeforeManageConnectionsFormDisplay', array( &$connections, &$actions, &$total, &$connMgmtTabs, &$pagingParams, &$perpage, &$connecteds ) ) );

	if ( $_PLUGINS->is_errors() ) {
		echo "<script type=\"text/javascript\">alert(\"".  $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
		exit();
	}

	outputCbTemplate();
	initToolTip();
	cbValidator::loadValidation();

	$connectionCategories			=	explode( "\n", $ueConfig['connection_categories'] );
	$connectionTypes				=	array();

	if ( $connectionCategories ) foreach ( $connectionCategories as $connectionCategory ) {
		if ( ( trim( $connectionCategory ) != null ) && ( trim( $connectionCategory ) != "" ) ) {
			$connectionTypes[]		=	moscomprofilerHTML::makeOption( trim( $connectionCategory ) , CBTxt::T( trim( $connectionCategory ) ) );
		}
	}

	$tabs							=	new cbTabs( 1, 1 );
	$tabCount						=	0;

	if ( $actions ) {
		$tabCount++;
	}

	if ( $connections ) {
		$tabCount++;
	}

	if ( ( $ueConfig['autoAddConnections'] == 0 ) && $connecteds ) {
		$tabCount++;
	}

	$pageTitle						=	CBTxt::T( 'UE_MANAGECONNECTIONS', 'Manage Connections' );

	if ( $pageTitle ) {
		$_CB_framework->setPageTitle( $pageTitle );
	}

	$pageClass						=	$_CB_framework->getMenuPageClass();

	$return							=	'<div class="cbManageConnections cb_template cb_template_' . selectTemplate( 'dir' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">'
									.		( $results ? $results : null )
									.		( $pageTitle ? '<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . $pageTitle . '</h3></div>' : null );

	if ( $actions || $connections || ( ( $ueConfig['autoAddConnections'] == 0 ) && $connecteds ) ) {
		$content					=		null;

		if ( $actions ) {
			$description			=	CBTxt::Th( 'UE_CONNECT_ACTIONREQUIRED', 'Below you see users proposing to connect with you. You have the choice to accept or decline their request.' );

			$content				.=			( $tabCount > 1 ? $tabs->startTab( 'myCon', CBTxt::Th( 'UE_MANAGEACTIONS', 'Manage Actions' ) . ' <span class="badge badge-pill badge-light border text-muted">' . count( $actions ) . '</span>', 'actions' ) : null )
									.				'<form action="' . $_CB_framework->viewUrl( 'processconnectionactions' ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form cbValidation">'
									.					( $description ? '<div class="mb-3 tab_description">' . $description . '</div>' : null )
									.					'<div class="ml-n2 mr-n2 mb-n3 row no-gutters">';

			foreach( $actions as $action ) {
				$cbUser				=	CBuser::getInstance( (int) $action->id, false );

				$tooltip			=	CBTxt::Th( 'CONNECTION_TIP_CONNECTION_REQUESTED_ON', 'Connected Requested on [CONNECTION_DATE]', array( '[CONNECTION_DATE]' => cbFormatDate( $action->membersince, true, false ) ) );

				if ( $action->reason != null ) {
					$tooltip		.=	'<br />' . CBTxt::Th( 'CONNECTION_TIP_CONNECTION_REASON', 'Message: [CONNECTION_REASON]', array( '[CONNECTION_REASON]' => htmlspecialchars( $action->reason ) ) );
				}

				$actionList			=	array();
				$actionList[]		=	moscomprofilerHTML::makeOption( 'a', CBTxt::T( 'Accept' ), 'value', 'text', null, 'flex-grow-1 btn-sm btn-success' );
				$actionList[]		=	moscomprofilerHTML::makeOption( 'd', CBTxt::Th( 'Reject' ), 'value', 'text', null, 'flex-grow-1 btn-sm btn-danger' );

				$content			.=						'<div class="col-12 col-md-6 col-lg-4 pb-3 pl-2 pr-2">'
									.							'<div class="h-100 card no-overflow cbCanvasLayout cbCanvasLayoutSm">'
									.								'<div class="card-header p-0 position-relative cbCanvasLayoutTop">'
									.									'<div class="position-absolute cbCanvasLayoutBackground">'
									.										$cbUser->getField( 'canvas', null, 'html', 'none', 'list', 0, true )
									.									'</div>'
									.								'</div>'
									.								'<div class="position-relative cbCanvasLayoutBottom">'
									.									'<div class="position-absolute cbCanvasLayoutPhoto">'
									.										cbTooltip( 1, $tooltip, CBTxt::T( 'UE_CONNECTIONREQUESTDETAIL', 'Connection Request Details' ), 300, null, $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true ), null, 'class="d-inline-block"' )
									.									'</div>'
									.								'</div>'
									.								'<div class="card-body p-2 position-relative cbCanvasLayoutBody">'
									.									'<div class="text-truncate cbCanvasLayoutContent">'
									.										$cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) )
									.										' <span class="text-large">' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) ) . '</span>'
									.									'</div>'
									.									'<div class="mt-1 cbCanvasLayoutContent">'
									.										moscomprofilerHTML::radioListButtons( $actionList, (int) $action->id . 'action', '', 'value', 'text', 'a', 0, array( 'w-100' ), null, false )
									.										'<input type="hidden" name="uid[]" value="' . (int) $action->id . '" />'
									.									'</div>'
									.								'</div>'
									.							'</div>'
									.						'</div>';
			}

			$content				.=					'</div>'
									.					'<div class="mt-3 cbMngConnButtons">'
									.						'<input type="submit" class="btn btn-primary btn-sm-block cbMngConnSubmit" value="' . htmlspecialchars( CBTxt::Th( 'UE_UPDATE', 'Update' ) ) . '"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.						' <input type="button" class="btn btn-secondary btn-sm-block cbMngConnCancel" value="' . htmlspecialchars( CBTxt::Th( 'UE_CANCEL', 'Cancel' ) ) . '" onclick="window.location=\'' . $_CB_framework->userProfileUrl() . '\'; return false;" />'
									.					'</div>'
									.					Application::Session()->getFormTokenInput()
									.				'</form>'
									.			( $tabCount > 1 ? $tabs->endTab() : null );
		}

		if ( $connections ) {
			$_CB_framework->outputCbJQuery( "$( '.cbSelect' ).cbselect();", 'cbselect' );

			$description			=	CBTxt::Th( 'UE_CONNECT_MANAGECONNECTIONS', 'Below you see users to whom you are connected directly. ' );

			$content				.=			( $tabCount > 1 ? $tabs->startTab( 'myCon', CBTxt::Th( 'UE_MANAGECONNECTIONS', 'Manage Connections' ), 'connections' ) : null )
									.				'<form action="' . $_CB_framework->viewUrl( 'saveconnections' ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form cbValidation">'
									.					( $description ? '<div class="mb-3 tab_description">' . $description . '</div>' : null )
									.					'<div class="ml-n2 mr-n2 mb-n3 row no-gutters">';

			foreach( $connections as $connection ) {
				$cbUser				=	CBuser::getInstance( (int) $connection->id, false );

				$tooltip			=	CBTxt::Th( 'CONNECTION_TIP_CONNECTED_SINCE_CONNECTION_DATE', 'Connected Since [CONNECTION_DATE]', array( '[CONNECTION_DATE]' => cbFormatDate( $connection->membersince, true, false ) ) );

				if ( $connection->type != null ) {
					$tooltip		.=	'<br />' . CBTxt::Th( 'CONNECTION_TIP_TYPES_LIST', '{1} Type: [CONNECTIONS_TYPES]|]1,Inf] Types: [CONNECTIONS_TYPES]|%%COUNT%%', array( '%%COUNT%%' => count( explode( "|*|", $connection->type ) ), '[CONNECTIONS_TYPES]' => getConnectionTypes( $connection->type ) ) );
				}

				if ( $connection->description != null ) {
					$tooltip		.=	'<br />' . CBTxt::Th( 'CONNECTION_TIP_CONNECTION_COMMENT', 'Comment: [CONNECTION_DESCRIPTION]', array( '[CONNECTION_DESCRIPTION]' => htmlspecialchars( $connection->description ) ) );
				}

				$buttons			=	array();

				if ( $connection->pending ) {
					$buttons[]		=	'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'removeconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connection->memberid ) ) ) . '\'; })" class="h-100 btn btn-sm btn-light border btn-block">' . CBTxt::Th( 'Cancel Connection Request' ) . '</button>';
				} elseif ( ! $connection->accepted  ) {
					$buttons[]		=	'<a href="' . $_CB_framework->viewUrl( 'acceptconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connection->memberid ) ) . '" class="h-100 btn btn-sm btn-success btn-block">' . CBTxt::T( 'Accept Connection' ) . '</a>';
					$buttons[]		=	'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'denyconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connection->memberid ) ) ) . '\'; })" class="h-100 btn btn-sm btn-light border btn-block">' . CBTxt::Th( 'Reject Connection' ) . '</button>';
				}

				$content			.=						'<div class="col-12 col-md-6 col-lg-4 pb-3 pl-2 pr-2">'
									.							'<div class="h-100 card no-overflow cbCanvasLayout cbCanvasLayoutSm">'
									.								'<div class="card-header p-0 position-relative cbCanvasLayoutTop">'
									.									'<div class="position-absolute cbCanvasLayoutBackground">'
									.										$cbUser->getField( 'canvas', null, 'html', 'none', 'list', 0, true )
									.									'</div>';

				if ( $connection->accepted && ( ! $connection->pending ) ) {
					$menuItems		=	'<ul class="list-unstyled dropdown-menu cbCanvasLayoutMenuItems" style="display: block; position: relative; margin: 0;">'
									.		'<li class="cbCanvasLayoutMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'removeconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connection->memberid ) ) ) . '\'; })" class="dropdown-item"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'UE_REMOVECONNECTION', 'Remove Connection' ) . '</a></li>'
									.	'</ul>';

					$menuAttr		=	cbTooltip( null, $menuItems, null, 'auto', null, null, null, 'class="border btn btn-light btn-sm cbCanvasLayoutMenu" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle" data-cbtooltip-open-classes="active"' );

					$content		.=									'<div class="position-absolute text-right p-1 cbCanvasLayoutActions">'
									.										'<button type="button" ' . trim( $menuAttr ) . '><span class="pl-2 pr-2 align-bottom text-large fa fa-ellipsis-v"></span></button>'
									.									'</div>';
				}

				$content			.=								'</div>'
									.								'<div class="position-relative cbCanvasLayoutBottom">'
									.									'<div class="position-absolute cbCanvasLayoutPhoto">'
									.										cbTooltip( 1, $tooltip, CBTxt::T( 'UE_CONNECTEDDETAIL', 'Connection Details' ), 300, null, $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true ), null, 'class="d-inline-block"' )
									.									'</div>'
									.								'</div>'
									.								'<div class="d-flex flex-column card-body p-0 position-relative cbCanvasLayoutBody">'
									.									'<div class="m-2 text-truncate cbCanvasLayoutContent">'
									.										$cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) )
									.										' <span class="text-large">' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) ) . '</span>'
									.									'</div>';

				if ( $connectionTypes ) {
					$content		.=									'<div class="ml-2 mr-2 mb-2 cbCanvasLayoutContent">'
									.										moscomprofilerHTML::selectList( $connectionTypes, $connection->id . 'connectiontype[]', 'class="w-100 form-control cbSelect" multiple="multiple" data-cbselect-placeholder="' . htmlspecialchars( CBTxt::T( 'UE_CONNECTIONTYPE', 'Type' ) ) . '"', 'value', 'text', explode( '|*|', trim( (string) $connection->type ) ), 0 )
									.									'</div>';
				}

				$content			.=									'<div class="ml-2 mr-2 mb-2 flex-grow-1 cbCanvasLayoutContent">'
									.										'<textarea cols="25" class="h-100 w-100 form-control" rows="4" name="' . (int) $connection->id . 'description" placeholder="' . htmlspecialchars( CBTxt::T( 'UE_CONNECTIONCOMMENT', 'Comment' ) ) . '">' . htmlspecialchars( (string) $connection->description ) . '</textarea>'
									.										'<input type="hidden" name="uid[]" value="' . (int) $connection->id . '" />'
									.									'</div>';

				if ( $buttons ) {
					$content		.=									'<div class="ml-1 mr-1 mb-1 mt-n1 row no-gutters cbCanvasLayoutContent">'
									.										'<div class="p-1 mw-100 col-md-6 flex-grow-1 cbCanvasLayoutContent">' . implode( '</div><div class="p-1 mw-100 col-md-6 flex-grow-1 cbCanvasLayoutContent">', $buttons ) . '</div>'
									.									'</div>';
				}

				$content			.=								'</div>'
									.							'</div>'
									.						'</div>';
			}

			$content				.=					'</div>';

			if ( $perpage < $total ) {
				$content			.=					'<div class="mt-3 text-center">'
									.						$connMgmtTabs->_writePaging( $pagingParams, 'connections_', $perpage, $total, 'manageconnections' )
									.					'</div>';
			}

			$content				.=					'<div class="mt-3 cbMngConnButtons">'
									.						'<input type="submit" class="btn btn-primary btn-sm-block cbMngConnSubmit" value="' . htmlspecialchars( CBTxt::Th( 'UE_UPDATE', 'Update' ) ) . '"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.						' <input type="button" class="btn btn-secondary btn-sm-block cbMngConnCancel" value="' . htmlspecialchars( CBTxt::Th( 'UE_CANCEL', 'Cancel' ) ) . '" onclick="window.location=\'' . $_CB_framework->userProfileUrl() . '\'; return false;" />'
									.					'</div>'
									.					Application::Session()->getFormTokenInput()
									.				'</form>'
									.			( $tabCount > 1 ? $tabs->endTab() : null );
		}

		if ( ( $ueConfig['autoAddConnections'] == 0 ) && $connecteds ) {
			$description			=	CBTxt::Th( 'UE_CONNECT_CONNECTEDWITH', '' );

			$content				.=			( $tabCount > 1 ? $tabs->startTab( 'myCon', CBTxt::Th( 'UE_CONNECTEDWITH', 'Manage Connections With Me' ), 'connected' ) : null )
									.				( $description ? '<div class="mb-3 tab_description">' . $description . '</div>' : null )
									.				'<div class="ml-n2 mr-n2 mb-n3 row no-gutters">';

			foreach( $connecteds as $connected ) {
				$cbUser				=	CBuser::getInstance( (int) $connected->id, false );

				$tooltip			=	CBTxt::Th( 'CONNECTION_TIP_CONNECTED_SINCE_CONNECTION_DATE', 'Connected Since [CONNECTION_DATE]', array( '[CONNECTION_DATE]' => cbFormatDate( $connected->membersince, true, false ) ) );

				if ( $connected->type != null ) {
					$tooltip		.=	'<br />' . CBTxt::Th( 'CONNECTION_TIP_TYPES_LIST', '{1} Type: [CONNECTIONS_TYPES]|]1,Inf] Types: [CONNECTIONS_TYPES]|%%COUNT%%', array( '%%COUNT%%' => count( explode( "|*|", $connected->type ) ), '[CONNECTIONS_TYPES]' => getConnectionTypes( $connected->type ) ) );
				}

				if ( $connected->description != null ) {
					$tooltip		.=	'<br />' . CBTxt::Th( 'CONNECTION_TIP_CONNECTION_COMMENT', 'Comment: [CONNECTION_DESCRIPTION]', array( '[CONNECTION_DESCRIPTION]' => htmlspecialchars( $connected->description ) ) );
				}

				$buttons			=	array();

				if ( $connected->pending ) {
					$buttons[]		=	'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'removeconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connected->memberid ) ) ) . '\'; })" class="h-100 btn btn-sm btn-light border btn-block">' . CBTxt::Th( 'Cancel Connection Request' ) . '</button>';
				} elseif ( ! $connected->accepted  ) {
					$buttons[]		=	'<a href="' . $_CB_framework->viewUrl( 'acceptconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connected->memberid ) ) . '" class="h-100 btn btn-sm btn-success btn-block">' . CBTxt::Th( 'Accept Connection' ) . '</a>';
					$buttons[]		=	'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'denyconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connected->memberid ) ) ) . '\'; })" class="h-100 btn btn-sm btn-light border btn-block">' . CBTxt::Th( 'Reject Connection' ) . '</button>';
				} else {
					$buttons[]		=	'<button type="button" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'UE_CONFIRMREMOVECONNECTION', 'Are you sure you want to remove this connection?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->viewUrl( 'removeconnection', true, array( 'act' => 'connections', 'connectionid' => (int) $connected->memberid ) ) ) . '\'; })" class="h-100 btn btn-sm btn-light border btn-block">' . CBTxt::Th( 'UE_REMOVECONNECTION', 'Remove Connection' ) . '</button>';
				}

				$content			.=					'<div class="col-12 col-md-6 col-lg-4 pb-3 pl-2 pr-2">'
									.						'<div class="h-100 card no-overflow cbCanvasLayout cbCanvasLayoutSm">'
									.							'<div class="card-header p-0 position-relative cbCanvasLayoutTop">'
									.								'<div class="position-absolute cbCanvasLayoutBackground">'
									.									$cbUser->getField( 'canvas', null, 'html', 'none', 'list', 0, true )
									.								'</div>'
									.							'</div>'
									.							'<div class="position-relative cbCanvasLayoutBottom">'
									.								'<div class="position-absolute cbCanvasLayoutPhoto">'
									.									cbTooltip( 1, $tooltip, CBTxt::T( 'UE_CONNECTEDDETAIL', 'Connection Details' ), 300, null, $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true ), null, 'class="d-inline-block"' )
									.								'</div>'
									.							'</div>'
									.							'<div class="d-flex flex-column card-body p-0 position-relative cbCanvasLayoutBody">'
									.								'<div class="m-2 text-truncate cbCanvasLayoutContent">'
									.									$cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) )
									.									' <span class="text-large">' . $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) ) . '</span>'
									.								'</div>';

				if ( $buttons ) {
					$content		.=								'<div class="ml-1 mr-1 mb-1 mt-n1 row no-gutters cbCanvasLayoutContent">'
									.									'<div class="p-1 mw-100 col-md-6 flex-grow-1 cbCanvasLayoutContent">' . implode( '</div><div class="p-1 mw-100 col-md-6 flex-grow-1 cbCanvasLayoutContent">', $buttons ) . '</div>'
									.								'</div>';
				}

				$content			.=							'</div>'
									.						'</div>'
									.					'</div>';
			}

			$content				.=				'</div>'
									.				'<div class="mt-3 cbMngConnButtons">'
									.					'<input type="button" class="btn btn-secondary btn-sm-block cbMngConnCancel" value="' . htmlspecialchars( CBTxt::Th( 'UE_CANCEL', 'Cancel' ) ) . '" onclick="window.location=\'' . $_CB_framework->userProfileUrl() . '\'; return false;" />'
									.				'</div>'
									.			( $tabCount > 1 ? $tabs->endTab() : null );
		}

		$return						.=		( $tabCount > 1 ? $tabs->startPane( 'myCon' ) . $content . $tabs->endPane() : $content );
	} else {
		$return						.=		'<div class="mb-3">'
									.			CBTxt::T( 'UE_NOCONNECTEDWITH', 'There are currently no users connected with you.' )
									.		'</div>'
									.		'<div class="cbMngConnButtons">'
									.			'<input type="button" class="btn btn-secondary cbMngConnCancel" value="' . htmlspecialchars( CBTxt::Th( 'UE_CANCEL', 'Cancel' ) ) . '" onclick="window.location=\'' . $_CB_framework->userProfileUrl() . '\'; return false;" />'
									.		'</div>';
	}

	$return							.=	'</div>'
									.	cbPoweredBy();

	echo $return;

	$_CB_framework->setMenuMeta();
}

}	// end class HTML_comprofiler

	function moderateBans( /** @noinspection PhpUnusedParameterInspection */ $option, $act, $uid ) {
		global $_CB_framework, $_CB_database, $_PLUGINS, $_REQUEST;

		$_PLUGINS->loadPluginGroup( 'user' );

		$results					=	implode( '', $_PLUGINS->trigger( 'onBeforeModerateBansFormDisplay', array( $uid, $act ) ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".  $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
			exit();
		}

		$isModerator				=	Application::MyUser()->isGlobalModerator();

		if ( ( ! $isModerator ) || ( ( $act == 2 ) && ( $uid == $_CB_framework->myId() ) ) ) {
			cbNotAuth();
			return;
		}

		$query						=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' );
		if ( $act == 2 ) {
			$query					.=	"\n WHERE NOT( ISNULL( " . $_CB_database->NameQuote( 'banned' ) . " ) )"
									.	"\n AND " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $uid;
		} else {
			$query					.=	"\n WHERE " . $_CB_database->NameQuote( 'banned' ) . " = 2"
									.	"\n AND " . $_CB_database->NameQuote( 'id' ) . " != " . (int) $_CB_framework->myId();
		}
		$query						.=	"\n AND " . $_CB_database->NameQuote( 'approved' ) . " = 1"
									.	"\n AND " . $_CB_database->NameQuote( 'confirmed' ) . " = 1";
		$_CB_database->setQuery( $query );
		$total						=	$_CB_database->loadResult();

		$limitstart					=	(int) getPagesLimitStart( $_REQUEST );
		$limit						=	20;

		if ( $limit > $total ) {
			$limitstart				=	0;
		}

		$query						=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' );
		if ( $act == 2 ) {
			$query					.=	"\n WHERE NOT( ISNULL( " . $_CB_database->NameQuote( 'banned' ) . " ) )"
									.	"\n AND " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $uid;
		} else {
			$query					.=	"\n WHERE " . $_CB_database->NameQuote( 'banned' ) . " = 2"
									.	"\n AND " . $_CB_database->NameQuote( 'id' ) . " != " . (int) $_CB_framework->myId();
		}
		$query						.=	"\n AND " . $_CB_database->NameQuote( 'approved' ) . " = 1"
									.	"\n AND " . $_CB_database->NameQuote( 'confirmed' ) . " = 1";
		$_CB_database->setQuery( $query, $limitstart, $limit );
		$rows						=	$_CB_database->loadObjectList();

		outputCbTemplate( 1 );

		$pageClass					=	$_CB_framework->getMenuPageClass();

		$return						=	'<div class="cbModerateBans cb_template cb_template_' . selectTemplate( 'dir' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">';

		if ( $results ) {
			$return					.=		$results;
		}

		$return						.=		'<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . CBTxt::T( 'UE_UNBAN_MODERATE', 'Unban Profile Requests' ) . '</h3></div>';

		if ( $total < 1 ) {
			$return					.=		CBTxt::T( 'UE_NOUNBANREQUESTS', 'No Unban Requests to Process' );
		} else {
			$return					.=		'<div class="mb-3 tab_description">' . CBTxt::T( 'UE_UNBAN_MODERATE_DESC', 'Click on the Banned Username to view the corresponding user profile.' ) . '</div>'
									.		'<div class="table-responsive">'
									.			'<table class="table table-hover mb-0">'
									.				'<thead>'
									.					'<tr>'
									.						'<th class="text-left">' . CBTxt::Th( 'UE_BANNEDUSER', 'Banned User' ) . '</th>'
									.						'<th class="text-left">' . CBTxt::Th( 'UE_BANNEDREASON', 'Banned Reason' ) . '</th>'
									.						'<th class="text-left xs-hidden">' . CBTxt::Th( 'UE_BANNEDON', 'Banned Date' ) . '</th>'
									.						'<th class="text-left xs-hidden">' . CBTxt::Th( 'UE_BANNEDBY', 'Banned By' ) . '</th>'
									.						'<th class="text-left xs-hidden">' . CBTxt::Th( 'UE_UNBANNEDON', 'Unbanned Date' ) . '</th>'
									.						'<th class="text-left xs-hidden">' . CBTxt::Th( 'UE_UNBANNEDBY', 'Unbanned By' ) . '</th>'
									.						'<th class="text-left">' . CBTxt::Th( 'UE_BANSTATUS', 'Ban status' ) . '</th>'
									.					'</tr>'
									.				'</thead>'
									.				'<tbody>';

			for ( $i = 0; $i < count( $rows ); $i++ ) {
				$row				=	$rows[$i];

				$return				.=					'<tr>'
									.						'<td class="text-left">' . CBuser::getInstance( (int) $row->id, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</td>'
									.						'<td class="text-left">' . $row->bannedreason . '</td>'
									.						'<td class="text-left xs-hidden">' . cbFormatDate( $row->banneddate ) . '</td>'
									.						'<td class="text-left xs-hidden">' . CBuser::getInstance( (int) $row->bannedby, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</td>'
									.						'<td class="text-left xs-hidden">' . cbFormatDate( $row->unbanneddate ) . '</td>'
									.						'<td class="text-left xs-hidden">' . CBuser::getInstance( (int) $row->unbannedby, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</td>'
									.						'<td class="text-left ' . ( $row->banned == 1 ? 'text-danger' : ( $row->banned == 2 ? 'text-warning' : 'text-success' ) ) . '">'
									.							( $row->banned == 1 ?
																	CBTxt::Th( 'UE_BANSTATUS_BANNED', 'Banned' )
																	: ( $row->banned == 2 ?
																		CBTxt::Th( 'UE_BANSTATUS_UNBAN_REQUEST_PENDING', 'Unban request pending' )
																		: CBTxt::Th( 'UE_BANSTATUS_PROCESSED', 'Processed' )
																	  )
																)
									.						'</td>'
									.					'</tr>';
			}

			$return					.=				'</tbody>'
									.			'</table>'
									.		'</div>';

			if ( $total > $limit ) {
				$return				.=		'<div class="mt-3 text-center">'
									.			writePagesLinks( $limitstart, $limit, $total, $_CB_framework->viewUrl( 'moderatebans' ) )
									.		'</div>';
			}
		}

		$return						.=	'</div>';

		echo $return;

		$_CB_framework->setMenuMeta();
	}

	function moderateReports( /** @noinspection PhpUnusedParameterInspection */ $option ) {
		global $_CB_framework, $_CB_database, $_PLUGINS, $_REQUEST;

		$_PLUGINS->loadPluginGroup( 'user' );

		$results					=	implode( '', $_PLUGINS->trigger( 'onBeforeModerateReportsFormDisplay', array() ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".  $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
			exit();
		}

		$isModerator				=	Application::MyUser()->isGlobalModerator();

		if ( ! $isModerator ) {
			cbNotAuth();
			return;
		}

		$query						=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_userreports' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'reportedstatus' ) . " = 0";
		$_CB_database->setQuery( $query );
		$total						=	$_CB_database->loadResult();

		$limitstart					=	(int) getPagesLimitStart( $_REQUEST );
		$limit						=	20;

		if ( $limit > $total ) {
			$limitstart				=	0;
		}

		$query						=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_userreports' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'reportedstatus' ) . " = 0"
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'reporteduser' ) . ", " . $_CB_database->NameQuote( 'reportedondate' );
		$_CB_database->setQuery( $query, $limitstart, $limit );
		$rows						=	$_CB_database->loadObjectList();

		outputCbJs();
		outputCbTemplate();
		cbValidator::loadValidation();

		$pageClass					=	$_CB_framework->getMenuPageClass();

		$return						=	'<div class="cbModerateReports cb_template cb_template_' . selectTemplate( 'dir' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">';

		if ( $results ) {
			$return					.=		$results;
		}

		$return						.=		'<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . CBTxt::Th( 'UE_USERREPORT_MODERATE', 'Moderate User Reports' ) . '</h3></div>';

		if ( $total < 1 ) {
			$return					.=		CBTxt::Th( 'UE_NOREPORTSTOPROCESS', 'No User Reports to Process' );
		} else {
			$toggleJs				=	"cbToggleAll( this, " . count( $rows ) . ", 'reports' );";

			$return					.=		'<form action="' . $_CB_framework->viewUrl( 'processreports' ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form cbValidation">'
									.			'<div class="table-responsive mb-3">'
									.				'<table class="table table-hover mb-0">'
									.					'<thead>'
									.						'<tr>'
									.							'<th style="width: 1%;" class="text-center"><input type="checkbox" name="toggle" value="" onclick="' . $toggleJs . '" /></th>'
									.							'<th style="width: 25%;" class="text-left">' . CBTxt::Th( 'UE_REPORTEDUSER', 'Reported User' ) . '</th>'
									.							'<th style="width: 25%;" class="text-left">' . CBTxt::Th( 'UE_REPORT', 'Report' ) . '</th>'
									.							'<th style="width: 24%;" class="text-left xs-hidden">' . CBTxt::Th( 'UE_REPORTEDONDATE', 'Report Date' ) . '</th>'
									.							'<th style="width: 25%;" class="text-left xs-hidden">' . CBTxt::Th( 'UE_REPORTEDBY', 'Reported By' ) . '</th>'
									.						'</tr>'
									.					'</thead>'
									.					'<tbody>';

			for ( $i = 0; $i < count( $rows ); $i++ ) {
				$row				=	$rows[$i];

				$return				.=						'<tr>'
									.							'<td style="width: 1%;" class="text-center"><input type="checkbox" id="reports' . $i . '" name="reports[]" value="' . (int) $row->reportid . '" /></td>'
									.							'<td style="width: 25%;" class="text-left">' . CBuser::getInstance( (int) $row->reporteduser, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</td>'
									.							'<td style="width: 25%;" class="text-left">' . $row->reportexplaination . '</td>'
									.							'<td style="width: 24%;" class="text-left xs-hidden">' . cbFormatDate( $row->reportedondate ) . '</td>'
									.							'<td style="width: 25%;" class="text-left xs-hidden">' . CBuser::getInstance( (int) $row->reportedbyuser, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</td>'
									.						'</tr>';
			}

			$return					.=					'</tbody>'
									.				'</table>'
									.			'</div>'
									.			'<div class="cbModReportsButtons">'
									.				'<input type="submit" class="btn btn-primary cbModReportsProcess" value="' . htmlspecialchars( CBTxt::Th( 'UE_PROCESSUSERREPORT', 'Process' ) ) . '"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />'
									.			'</div>'
									.			Application::Session()->getFormTokenInput()
									.		'</form>';

			if ( $total > $limit ) {
				$return				.=		'<div class="mt-3 text-center">'
									.			writePagesLinks( $limitstart, $limit, $total, $_CB_framework->viewUrl( 'moderatereports' ) )
									.		'</div>';
			}
		}

		$return						.=	'</div>';

		echo $return;

		$_CB_framework->setMenuMeta();
    }

	function moderateImages( /** @noinspection PhpUnusedParameterInspection */ $option ) {
		global $_CB_framework, $_CB_database, $_PLUGINS, $_REQUEST;

		$_PLUGINS->loadPluginGroup( 'user' );

		$results					=	implode( '', $_PLUGINS->trigger( 'onBeforeModerateImagesFormDisplay', array() ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".  $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
			exit();
		}

		$isModerator				=	Application::MyUser()->isGlobalModerator();

		if ( ! $isModerator ) {
			cbNotAuth();
			return;
		}

		$avatarPath					=	$_CB_framework->getCfg( 'live_site' ) . '/images/comprofiler/';

		$query						=	'SELECT ' . $_CB_database->NameQuote( 'name' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'image' );
		$_CB_database->setQuery( $query );
		$imageFields				=	$_CB_database->loadResultArray();

		$approvedColumns			=	array();

		if ( $imageFields ) foreach ( $imageFields as $imageField ) {
			$approvedColumns[]		=	"( c." . $_CB_database->NameQuote( $imageField ) . " != '' AND c." . $_CB_database->NameQuote( $imageField ) . " IS NOT NULL AND c." . $_CB_database->NameQuote( $imageField . 'approved' ) . " = 0 )";
		}

		$query						=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
									.	"\n WHERE ( " . implode( ' OR ', $approvedColumns ) . " )"
									.	"\n AND c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
									.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
									.	"\n AND c." . $_CB_database->NameQuote( 'banned' ) . " = 0";
		$_CB_database->setQuery( $query );
		$total						=	$_CB_database->loadResult();

		$limitstart					=	(int) getPagesLimitStart( $_REQUEST );
		$limit						=	20;

		if ( $limit > $total ) {
			$limitstart				=	0;
		}

		$query						=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
									.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS u"
									.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
									.	"\n WHERE ( " . implode( ' OR ', $approvedColumns ) . " )"
									.	"\n AND c." . $_CB_database->NameQuote( 'approved' ) . " = 1"
									.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
									.	"\n AND c." . $_CB_database->NameQuote( 'banned' ) . " = 0";
		$_CB_database->setQuery( $query, $limitstart, $limit );
		$rows						=	$_CB_database->loadObjectList();

		outputCbTemplate( 1 );

		$pageClass					=	$_CB_framework->getMenuPageClass();

		$return						=	'<div class="cbModerateImages cb_template cb_template_' . selectTemplate( 'dir' ) . ( $pageClass ? ' ' . htmlspecialchars( $pageClass ) : null ) . '">';

		if ( $results ) {
			$return					.=		$results;
		}

		$return						.=		'<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . CBTxt::Th( 'UE_IMAGE_MODERATE', 'Moderate Images' ) . '</h3></div>';

		if ( $total < 1 ) {
			$return					.=		CBTxt::Th( 'UE_NOIMAGESTOAPPROVE', 'No Images to Process' );
		} else {
			$return					.=		'<form action="' . $_CB_framework->viewUrl( 'approveimage' ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form">'
									.			'<div class="row no-gutters mb-2 cbModerateImgs">';

			$f						=	0;

			for ( $i = 0; $i < count( $rows ); $i++ ) {
				$row				=	$rows[$i];

				$name				=	CBuser::getInstance( (int) $row->id, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true );

				if ( $imageFields ) foreach ( $imageFields as $imageField ) {
					$approvedColumn	=	$imageField . 'approved';

					if ( $row->$approvedColumn == 0 ) {
						$f++;

						$image		=	$avatarPath . $row->$imageField;
						$imageTn	=	$avatarPath . 'tn' . $row->$imageField;

						$return		.=				'<div class="pb-2 col-12 col-sm-6 col-md-4' . ( $f % 2 == 0 ? ' pl-sm-1' : ' pr-sm-1' ) . ( $f % 3 == 0 ? ' pr-md-0 pl-md-1' : ( $f % 3 == 2 ? ' pl-md-1 pr-md-1' : ' pl-md-0 pr-md-1' ) ) . '">'
									.					'<div class="card cbModerateImg">'
									.						'<div class="card-header p-2 text-center">'
									.							'<div class="form-check form-check-inline m-0 mw-100 text-overflow">'
									.								'<input id="img' . (int) $row->id . '" type="checkbox" checked="checked" name="images[' . (int) $row->id . '][]" value="' . htmlspecialchars( $imageField ) . '" class="form-check-input" />'
									.								' ' . $name
									.							'</div>'
									.						'</div>'
									.						'<div class="card-body p-2 text-center">'
									.							cbTooltip( null, '<div class="d-flex w-100 h-100 align-items-center justify-content-center"><div><img src="' . htmlspecialchars( $image ) . '" class="cbImgPict mw-100 mh-100" /></div></div>', null, array( '90%', '90%' ), null, '<img src="' . htmlspecialchars( $imageTn ) . '" class="cbThumbPict img-thumbnail" />', 'javascript: void(0);', 'data-hascbtooltip="true" data-cbtooltip-modal="true"' )
									.						'</div>'
									.						'<div class="card-footer p-2 text-center">'
									.							'<a href="' . $_CB_framework->viewUrl( 'approveimage', true, array( 'flag' => 1, 'images[' . (int) $row->id . '][]' => $imageField ) ) . '">'
									.								'<span class="fa fa-check-circle-o" title="' . htmlspecialchars( CBTxt::T( 'UE_APPROVE_IMAGE', 'Approve Image' ) ) . '"></span>'
									.							'</a>'
									.							' <a href="' . $_CB_framework->viewUrl( 'approveimage', true, array( 'flag' => 0, 'images[' . (int) $row->id . '][]' => $imageField ) ) . '">'
									.								'<span class="fa fa-times-circle-o" title="' . htmlspecialchars( CBTxt::T( 'UE_REJECT_IMAGE', 'Reject Image' ) ) . '"></span>'
									.							'</a>'
									.							' <a href="' . $_CB_framework->userProfileUrl( (int) $row->id ) . '">'
									.								'<span class="fa fa-user" title="' . htmlspecialchars( CBTxt::T( 'UE_VIEWPROFILE', 'View Profile' ) ) . '"></span>'
									.							'</a>'
									.						'</div>'
									.					'</div>'
									.				'</div>';
					}
				}
			}

			$return					.=			'</div>'
									.			'<div class="cbModImgButtons">'
									.				'<input type="button" class="btn btn-success cbModImgApprove" value="' . htmlspecialchars( CBTxt::Th( 'UE_APPROVE', 'Approve' ) ) . '" onclick="this.form.act.value=\'1\'; this.form.submit();" />'
									.				' <input type="button" class="btn btn-danger cbModImgReject" value="' . htmlspecialchars( CBTxt::Th( 'UE_REJECT', 'Reject' ) ) . '" onclick="this.form.act.value=\'0\'; this.form.submit();" />'
									.			'</div>'
									.			'<input type="hidden" name="act" value="" />'
									.			Application::Session()->getFormTokenInput()
									.		'</form>';

			if ( $total > $limit ) {
				$return				.=		'<div class="mt-3 text-center">'
									.			writePagesLinks( $limitstart, $limit, $total, $_CB_framework->viewUrl( 'moderateimages' ) )
									.		'</div>';
			}
		}

		$return						.=	'</div>';

		echo $return;

		$_CB_framework->setMenuMeta();
	}

	function viewReports( /** @noinspection PhpUnusedParameterInspection */ $option, $uid, $act ) {
		global $_CB_framework, $_CB_database, $_PLUGINS, $_REQUEST;

		$_PLUGINS->loadPluginGroup( 'user' );

		$results					=	implode( '', $_PLUGINS->trigger( 'onBeforeViewReportsFormDisplay', array( $uid, $act ) ) );

		if ( $_PLUGINS->is_errors() ) {
			echo "<script type=\"text/javascript\">alert(\"".  $_PLUGINS->getErrorMSG() . "\"); window.history.go(-1); </script>\n";
			exit();
		}

		$isModerator				=	Application::MyUser()->isGlobalModerator();

		if ( ! $isModerator ) {
			cbNotAuth();
			return;
		}

		$query						=	'SELECT COUNT(*)'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_userreports' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'reporteduser' ) . " = " . (int) $uid
									.	( $act == 1 ? "\n AND " . $_CB_database->NameQuote( 'reportedstatus' ) . " = 0" : null );
		$_CB_database->setQuery( $query );
		$total						=	$_CB_database->loadResult();

		$limitstart					=	(int) getPagesLimitStart( $_REQUEST );
		$limit						=	20;

		if ( $limit > $total ) {
			$limitstart				=	0;
		}

		$query						=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_userreports' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'reporteduser' ) . " = " . (int) $uid
									.	( $act == 1 ? "\n AND " . $_CB_database->NameQuote( 'reportedstatus' ) . " = 0" : null )
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'reporteduser' ) . ", " . $_CB_database->NameQuote( 'reportedondate' );
		$_CB_database->setQuery( $query, $limitstart, $limit );
		$rows						=	$_CB_database->loadObjectList();

		outputCbTemplate( 1 );

		$return						=	'<div class="cbViewReports cb_template cb_template_' . selectTemplate( 'dir' ) . '">';

		if ( $results ) {
			$return					.=		$results;
		}

		$return						.=		'<div class="mb-3 border-bottom cb-page-header"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . CBTxt::Th( 'UE_USERREPORT', 'User Report' ) . '</h3></div>';

		if ( $total < 1 ) {
			$return					.=		CBTxt::Th( 'UE_NOREPORTSTOPROCESS', 'No User Reports to Process' );
		} else {
			$return					.=		'<form action="' . $_CB_framework->viewUrl( 'moderatereports' ) . '" method="post" id="adminForm" name="adminForm" class="form-auto m-0 cb_form">'
									.			'<div class="table-responsive mb-3">'
									.				'<table class="table table-hover mb-0">'
									.					'<thead>'
									.						'<tr>'
									.							'<th style="width: 20%;" class="text-left">' . CBTxt::Th( 'UE_REPORTEDUSER', 'Reported User' ) . '</th>'
									.							'<th style="width: 20%;" class="text-left">' . CBTxt::Th( 'UE_REPORT', 'Report' ) . '</th>'
									.							'<th style="width: 20%;" class="text-left xs-hidden">' . CBTxt::Th( 'UE_REPORTEDONDATE', 'Report Date' ) . '</th>'
									.							'<th style="width: 20%;" class="text-left xs-hidden">' . CBTxt::Th( 'UE_REPORTEDBY', 'Reported By' ) . '</th>'
									.							'<th style="width: 20%;" class="text-left">' . CBTxt::Th( 'UE_REPORTSTATUS', 'Report status' ) . '</th>'
									.						'</tr>'
									.					'</thead>'
									.					'<tbody>';

			for ( $i = 0; $i < count( $rows ); $i++ ) {
				$row				=	$rows[$i];

				$return				.=						'<tr>'
									.							'<td style="width: 20%;" class="text-left">' . CBuser::getInstance( (int) $row->reporteduser, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</td>'
									.							'<td style="width: 20%;" class="text-left">' . $row->reportexplaination . '</td>'
									.							'<td style="width: 20%;" class="text-left xs-hidden">' . cbFormatDate( $row->reportedondate ) . '</td>'
									.							'<td style="width: 20%;" class="text-left xs-hidden">' . CBuser::getInstance( (int) $row->reportedbyuser, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) . '</td>'
									.							'<td style="width: 20%;" class="text-left ' . ( $row->reportedstatus ? 'text-success' : 'text-danger' ) . '">' . ( $row->reportedstatus ? CBTxt::Th( 'UE_REPORTSTATUS_PROCESSED', 'Processed' ) : CBTxt::Th( 'UE_REPORTSTATUS_OPEN', 'Open' ) ) . '</td>'
									.						'</tr>';
			}

			$return					.=					'</tbody>'
									.				'</table>'
									.			'</div>'
									.			'<div class="cbViewReportsButtons">'
									.				'<input type="submit" class="btn btn-primary cbViewReportsMod" value="' . htmlspecialchars( CBTxt::Th( 'UE_USERREPORT_MODERATE', 'Moderate User Reports' ) ) . '" />'
									.			'</div>'
									.		'</form>';

			if ( $total > $limit ) {
				$return				.=		'<div class="mt-3 text-center">'
									.			writePagesLinks( $limitstart, $limit, $total, $_CB_framework->viewUrl( 'viewreports' ) )
									.		'</div>';
			}
		}

		$return						.=	'</div>';

		echo $return;
}
