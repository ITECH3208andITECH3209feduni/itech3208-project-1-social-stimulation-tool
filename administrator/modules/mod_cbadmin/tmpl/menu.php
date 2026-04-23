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
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

if ( Application::MyUser()->isAuthorizedToPerformActionOnAsset( 'core.manage', 'com_comprofiler' ) ) {
	$pluginMenu							=	array();

	if ( $params->get( 'menu_plugins', 1 ) ) {
		$_PLUGINS->loadPluginGroup( 'user' );

		$_PLUGINS->trigger( 'mod_onCBAdminMenu', array( &$pluginMenu, $disabled, $params->get( 'menu_compact', 1 ) ) );
	}

	if ( $params->get( 'menu_cb', 1 ) && file_exists( $_CB_framework->getCfg( 'absolute_path' ) . '/components/com_comprofiler' ) ) {
		$prevStateBase					=	'option=com_comprofiler';

		if ( Application::Config()->get( 'installFromWeb', 1, GetterInterface::INT ) ) {
			$pluginInstall				=	array( array( 'title' => CBTxt::Th( 'Install & Update Plugins' ), 'link' => $_CB_framework->backendViewUrl( 'installcbplugin', true, array( 'tab' => 'installfrom2', 'cbprevstate' => base64_encode( $prevStateBase . '&view=showPlugins' ) ) ), 'access' => array( 'core.admin', 'root' ), 'icon' => 'cb-upload', 'taskicon' => 'fas fa-plus' ) );
		} else {
			$pluginInstall				=	array( array( 'title' => CBTxt::Th( 'Install New Plugin' ), 'link' => $_CB_framework->backendViewUrl( 'installcbplugin', true, array( 'cbprevstate' => base64_encode( $prevStateBase . '&view=showPlugins' ) ) ), 'access' => array( 'core.admin', 'root' ), 'icon' => 'cb-upload', 'taskicon' => 'fas fa-plus' ) );
		}

		$cbMenu							=	array();
		$cbMenu['component']			=	array(	'title' => CBTxt::Th( 'Community Builder' ) );
		$cbMenu['menu']					=	array(	array(	'title' => CBTxt::Th( 'Control Panel' ), 'link' => $_CB_framework->backendViewUrl( null ), 'icon' => 'cb-control_panel' ),
													array(	'title' => CBTxt::Th( 'User Management' ), 'link' => $_CB_framework->backendViewUrl( 'showusers' ), 'access' => array( 'core.manage', 'com_users' ), 'icon' => 'cb-user_management',
															'taskmenu' => array( array( 'title' => CBTxt::Th( 'Add New User' ), 'link' => $_CB_framework->backendViewUrl( 'new', true, array( 'cbprevstate' => base64_encode( $prevStateBase . '&view=showusers' ) ) ), 'access' => array( 'core.create', 'com_users' ), 'icon' => 'cb-new', 'taskicon' => 'fas fa-plus' ) )
													),
													array(	'title' => CBTxt::Th( 'Tab Management' ), 'link' => $_CB_framework->backendViewUrl( 'showTab' ), 'access' => array( 'core.manage', 'com_comprofiler.tabs' ), 'icon' => 'cb-tab_management',
															'taskmenu' => array( array( 'title' => CBTxt::Th( 'Add New Tab' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'tabsbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&view=showTab' ) ) ), 'access' => array( array( 'core.create', 'core.edit' ), 'com_comprofiler.tabs' ), 'icon' => 'cb-new', 'taskicon' => 'fas fa-plus' ) )
													),
													array(	'title' => CBTxt::Th( 'Field Management' ), 'link' => $_CB_framework->backendViewUrl( 'showField' ), 'access' => array( 'core.manage', 'com_comprofiler.fields' ), 'icon' => 'cb-field_management',
															'taskmenu' => array( array( 'title' => CBTxt::Th( 'Add New Field' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'fieldsbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&view=showField' ) ) ), 'access' => array( array( 'core.create', 'core.edit' ), 'com_comprofiler.fields' ), 'icon' => 'cb-new', 'taskicon' => 'fas fa-plus' ) )
													),
													array(	'title' => CBTxt::Th( 'List Management' ), 'link' => $_CB_framework->backendViewUrl( 'showLists' ), 'access' => array( 'core.manage', 'com_comprofiler.lists' ), 'icon' => 'cb-list_management',
															'taskmenu' => array( array( 'title' => CBTxt::Th( 'Add New List' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'listsbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&view=showLists' ) ) ), 'access' => array( array( 'core.create', 'core.edit' ), 'com_comprofiler.lists' ), 'icon' => 'cb-new', 'taskicon' => 'fas fa-plus' ) )
													),
													array(	'title' => CBTxt::Th( 'Plugin Management' ), 'link' => $_CB_framework->backendViewUrl( 'showPlugins' ), 'access' => array( 'core.manage', 'com_comprofiler.plugins' ), 'icon' => 'cb-plugin_management',
															'taskmenu' => $pluginInstall,
															'submenu' => ( $params->get( 'menu_compact', 1 ) ? $pluginMenu : array() )
													),
													array(	'title' => CBTxt::Th( 'Tools' ), 'link' => $_CB_framework->backendViewUrl( 'tools', true, array( 'cbprevstate' => base64_encode( $prevStateBase ) ) ), 'access' => array( 'core.manage', 'com_comprofiler.tools' ), 'icon' => 'cb-tools' ),
													array(	'title' => CBTxt::Th( 'Configuration' ), 'link' => $_CB_framework->backendViewUrl( 'showconfig', true, array( 'cbprevstate' => base64_encode( $prevStateBase ) ) ), 'access' => array( 'core.admin', 'com_comprofiler' ), 'icon' => 'cb-configuration' ),
													array(	'title' => CBTxt::Th( 'Credits' ), 'link' => $_CB_framework->backendViewUrl( 'credits', true, array( 'cbprevstate' => base64_encode( $prevStateBase ) ) ), 'icon' => 'cb-credits' )
												);

		$menu['cb']						=	$cbMenu;
	} elseif ( $params->get( 'menu_compact', 1 ) && $pluginMenu ) {
		$cbMenu							=	array();
		$cbMenu['component']			=	array(	'title' => CBTxt::Th( 'Community Builder' ) );
		$cbMenu['menu']					=	$pluginMenu;

		$menu['cb']						=	$cbMenu;
	}

	if ( ( ! $params->get( 'menu_compact', 1 ) ) && $pluginMenu ) {
		$menu							=	( $menu + $pluginMenu );
	}
}