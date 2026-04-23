<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Core\CBLib;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_database, $_CB_framework, $_PLUGINS, $ueConfig;

if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
	echo 'CB not installed!';
	return;
}

try {
/** @noinspection PhpIncludeInspection */
include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

if ( ! defined( 'CBLIB' ) ) {
	echo 'CB version is not 2.0.0+';
	return;
}

cbimport( 'cb.html' );

outputCbTemplate();

require_once( dirname( __FILE__ ) . '/helper.php' );

$mode								=	(int) $params->get( 'mode', 1 );

if ( ( $module->position === 'menu' ) && ( $mode !== 1 ) ) {
	// Do not allow the other modes to render in the menu position or it will overflow the header making it impossible to navigate!
	echo 'CB Admin Module has the wrong mode selected for menu position';
	return;
}

$cbUser								=	CBuser::getMyInstance();
$user								=	$cbUser->getUserData();
$disabled							=	( Application::Input()->get( 'hidemainmenu', 0, GetterInterface::INT ) ? true : false );
$feedEntries						=	(int) $params->get( 'feed_entries', 5 );
$feedDuration						=	(int) $params->get( 'feed_duration', 12 );
$modalDisplay						=	(int) $params->get( 'modal_display', 1 );
$modalWidth							=	$params->get( 'modal_width', '90%' );
$modalHeight						=	$params->get( 'modal_height', '90vh' );

if ( ! $modalWidth ) {
	$modalWidth						=	'90%';
}

if ( ! $modalHeight ) {
	$modalHeight					=	'90vh';
}

$_CB_framework->document->addHeadStyleSheet( $_CB_framework->getCfg( 'live_site' ) . '/administrator/modules/mod_cbadmin/mod_cbadmin.css' );

if ( in_array( $mode, array( 3, 4 ) ) ) {
	static $JS1_loaded				=	0;

	if ( ! $JS1_loaded++ ) {
		$js							=	"$( '.cbFeedShowMore,.cbFeedShowMoreLink' ).click( function() {"
									.		"var more = $( this ).nextUntil( '.cbFeedShowMore,.cbFeedShowMoreLink' );"
									.		"more.fadeIn().removeClass( 'cbFeedItemDisabled' );"
									.		"more.next( '.cbFeedShowMore,.cbFeedShowMoreLink' ).show();"
									.		"$( this ).remove();"
									.	"});"
									.	"$( '.cbFeed' ).each( function() {"
									.		"$( this ).find( '.cbFeedShowMore,.cbFeedShowMoreLink' ).first().show();"
									.	"});";

		$_CB_framework->outputCbJQuery( $js );
	}
}

switch ( $mode ) {
	case 5:
		$messages					=	array();

		if ( ! Application::MyUser()->isAuthorizedToPerformActionOnAsset( 'core.admin', 'com_comprofiler' ) ) {
			return;
		}

		if ( Application::Config()->get( 'versionCheckInterval', '+12 HOURS', GetterInterface::STRING ) ) {
			$cbVersion				=	$_PLUGINS->getPluginVersion( 1, 2 );

			if ( $cbVersion[2] === false ) {
				modCBAdminHelper::enableUpdateSite();

				$isBuild			=	( strpos( $cbVersion[1], '+build' ) !== false );

				if ( $isBuild ) {
					$infoUrl		=	'https://www.joomlapolis.com/forge?pk_campaign=in-cb&pk_kwd=admin-module-update-button';

					if ( Application::Config()->get( 'installFromWeb', 1, GetterInterface::INT ) ) {
						$updateUrl	=	$_CB_framework->backendViewUrl( 'installcbplugin', false, array( 'tab' => 'installfrom2' ) );
					} else {
						$updateUrl	=	$cbVersion[3];
					}
				} else {
					$infoUrl		=	'https://www.joomlapolis.com/?pk_campaign=in-cb&pk_kwd=admin-module-update-button';
					$updateUrl		=	$_CB_framework->backendUrl( 'index.php?option=com_installer&view=update', false );
				}

				$latestVersion		=	'<span class="cbUpdateVersion badge badge-danger">' . $cbVersion[1] . '</span>';

				$learnButton		=	'<a href="' . htmlspecialchars( $infoUrl ) . '" target="_blank"><button class="btn btn-primary cbLearnButton">' . CBTxt::T( 'Learn More' ) . '</button></a>';

				$updateButton		=	'<a href="' . htmlspecialchars( $updateUrl ) . '"><button class="btn btn-primary cbUpdateButton">' . CBTxt::T( 'Update Now' ) . '</button></a>';

				$configUrl			=	$_CB_framework->backendViewUrl( 'showconfig', true, array( 'tab' => 'config7' ) );

				$messages[]			=	'<div class="cbUpdateNotification alert alert-danger">'
									.		CBTxt::T( 'COMMUNITY_BUILDER_VERSION_VERSION_IS_AVAILABLE_BUTTON', 'Community Builder version [version] is available: [learn_button] [update_button]', array( '[version]' => $latestVersion, '[learn_button]' => $learnButton, '[update_button]' => $updateButton ) )
									.		( $isBuild ? '<div class="mt-1 cbUpdateNotificationBuild">' . CBTxt::T( 'COMMUNITY_BUILDER_VERSION_IS_BUILD', 'The new version available is a build release. If you do not want to be notified about build releases you may disable Plugin Version and Build Release Checking in your <a href="[config_url]">Community Builder configuration</a>.', array( '[config_url]' => $configUrl ) ) . '</div>' : null )
									.	'</div>';
			}
		}

		if ( ( PHP_VERSION_ID < 50400 ) && get_magic_quotes_gpc() ) {
			$phpFunction			=	'<span class="cbDisableFunction badge badge-danger">magic_quotes_gpc</span>';

			$tutorialButton			=	'<a href="http://docs.joomla.org/How_to_turn_off_magic_quotes_gpc_for_Joomla_3" target="_blank"><button class="btn btn-primary btn-sm cbDisableFunctionButton">' . CBTxt::T( 'Please click here for instructions.' ) . '</button></a>';

			$messages[]				=	'<div class="cbDisableFunctionNotification alert alert-danger">'
									.			CBTxt::T( 'YOUR_HOST_DISABLE_FUNCTION_FOR_CB', 'Your host needs to disable [function] to run this version of Community Builder! [button]', array( '[function]' => $phpFunction, '[button]' => $tutorialButton ) )
									.	'</div>';
		}

		$versionChecks				=	explode( '|*|', Application::Config()->get( 'unsupportedVersionsCheck', 'php|*|database|*|joomla', GetterInterface::STRING ) );
		$versionMinimums			=	CBLib::supportedVersions();

		foreach ( $versionChecks as $versionCheck ) {
			$notes						=	array();

			switch ( $versionCheck ) {
				case 'php':
					$type				=	CBTxt::T( 'PHP' );
					$currentVersion		=	PHP_VERSION;

					$notes[]			=	CBTxt::T( 'This can often be changed from within your hosting panel or contact your host if you are unsure how.' );

					if ( version_compare( $currentVersion, $versionMinimums['php']['min'], '>=' ) ) {
						continue 2;
					}
					break;
				case 'database':
					$currentVersion		=	$_CB_database->getVersion();

					$notes[]			=	CBTxt::T( 'Contact your host if you are unsure how.' );

					if ( stripos( $currentVersion, 'mariadb') !== false ) {
						$type			=	CBTxt::T( 'MariaDB' );
						$versionCheck	=	'mariadb';

						if ( version_compare( preg_replace( '/^5\.5\.5-/', '', $currentVersion ), $versionMinimums['mariadb']['min'], '>=' ) )  {
							continue 2;
						}
					} else {
						$type			=	CBTxt::T( 'MySQL' );
						$versionCheck	=	'mysql';

						if ( $_CB_database->versionCompare( $versionMinimums['mysql']['min'] ) ) {
							continue 2;
						}
					}
					break;
				case 'joomla':
					$type				=	CBTxt::T( 'Joomla' );
					$currentVersion		=	Application::Cms()->getCmsVersion();

					if ( version_compare( $currentVersion, '3.0.0', '<' ) ) {
						$notes[]		=	CBTxt::T( 'CB_UNSUPPORTED_VERSION_MIGRATE', 'This will first require <a href="[url]" target="_blank">migration from Joomla 2.x to 3.x</a>.', array( '[url]' => 'https://docs.joomla.org/Joomla_2.5_to_3.x_Step_by_Step_Migration' ) );
					} else {
						$notes[]		=	CBTxt::T( 'CB_UNSUPPORTED_VERSION_JOOMLA', 'This can be done from directly within the <a href="[url]">Joomla Update component under the Components menu</a>.', array( '[url]' => $_CB_framework->backendUrl( 'index.php?option=com_joomlaupdate' ) ) );
					}

					if ( version_compare( $currentVersion, $versionMinimums['joomla']['min'], '>=' ) ) {
						continue 2;
					}
					break;
				default:
					continue 2;
			}

			$notes[]					=	CBTxt::T( 'CB_UNSUPPORTED_VERSION_SUPPORT', 'If you have any questions regarding this notice <a href="[url]" target="_blank">please do not hesitate to ask on our support forums</a>.', array( '[url]' => 'https://www.joomlapolis.com/forum' ) );
			$notes[]					=	CBTxt::T( 'CB_UNSUPPORTED_VERSION_DISABLE', 'You may disable this notice <a href="[url]">within CB &gt; Configuration &gt; Integrations</a>.', array( '[url]' => $_CB_framework->backendViewUrl( 'showconfig', true, array( 'tab' => 'config7' ) ) ) );

			if ( ( ! $versionMinimums[$versionCheck]['rec'] ) || ( $versionMinimums[$versionCheck]['min'] === $versionMinimums[$versionCheck]['rec'] ) ) {
				$messages[]				=	'<div class="alert alert-danger cbUnsupportedVersionNotice">'
										.		CBTxt::T( 'CB_UNSUPPORTED_VERSION_NOTICE', 'Community Builder will no longer be compatible with [type] [current]. The next stable release of Community Builder will be requiring a minimum of [type] [minimum]. Please consider updating to [type] [minimum]. [notes]', array( '[type]' => $type, '[current]' => $currentVersion, '[minimum]' => $versionMinimums[$versionCheck]['min'], '[notes]' => implode( ' ', $notes ) ) )
										.	'</div>';
			} else {
				$messages[]				=	'<div class="alert alert-danger cbUnsupportedVersionNotice">'
										.		CBTxt::T( 'CB_UNSUPPORTED_VERSION_NOTICE_RECOMMEND', 'Community Builder will no longer be compatible with [type] [current]. The next stable release of Community Builder will be requiring a minimum of [type] [minimum] and recommends [type] [recommended]. Please consider updating to at least [type] [minimum]. [notes]', array( '[type]' => $type, '[current]' => $currentVersion, '[minimum]' => $versionMinimums[$versionCheck]['min'], '[recommended]' => $versionMinimums[$versionCheck]['rec'], '[notes]' => implode( ' ', $notes ) ) )
										.	'</div>';
			}
		}

		$query						=	'SELECT ' . $_CB_database->NameQuote( 'extension_id' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__extensions' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . ' = ' . $_CB_database->Quote( 'plugin' )
									.	"\n AND " . $_CB_database->NameQuote( 'element' ) . ' = ' . $_CB_database->Quote( 'communitybuilder' )
									.	"\n AND " . $_CB_database->NameQuote( 'folder' ) . ' = ' . $_CB_database->Quote( 'system' )
									.	"\n AND " . $_CB_database->NameQuote( 'enabled' ) . ' = 0';
		$_CB_database->setQuery( $query );
		$systemPluginId				=	$_CB_database->loadResult();

		if ( $systemPluginId ) {
			$enableButton			=	'<a href="index.php?option=com_plugins&view=plugin&layout=edit&extension_id=' . (int) $systemPluginId . '" target="_blank"><button class="btn btn-primary btn-sm cbDisabledSystemPlgButton">' . CBTxt::T( 'Please click here to enable.' ) . '</button></a>';

			$messages[]				=	'<div class="cbDisabledSystemPlgNotification alert alert-danger">'
									.			CBTxt::T( 'CB_SYSTEM_PLUGIN_DISABLED', 'The Community Builder System plugin has been disabled! Please enable it for Community Builder to function properly. [button]', array( '[button]' => $enableButton ) )
									.	'</div>';
		}

		if ( $messages ) {
			$notification			=	'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
									.		implode( '', $messages )
									.	'</div>';

			$_CB_framework->outputCbJQuery( "$( '#system-message-container' ).append( '" . addslashes( $notification ) . "' );" );
		}
		break;
	case 4:
		static $items				=	null;

		if ( ! Application::MyUser()->isAuthorizedToPerformActionOnAsset( 'core.admin', 'com_comprofiler' ) ) {
			return;
		}

		if ( ! isset( $items ) ) {
			$items					=	array();
			$plugins				=	array();

			$query					=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'iscore' ) . " = 0";
			$_CB_database->setQuery( $query );
			$rows					=	$_CB_database->loadObjectList( 'id', '\CB\Database\Table\PluginTable', array( &$_CB_database ) );

			if ( $rows ) foreach ( $rows as $row ) {
				$rowVer				=	$_PLUGINS->getPluginVersion( $row, 2 );

				if ( $rowVer[2] === false ) {
					$items[]		=	array( $row, $rowVer, $_PLUGINS->checkPluginCompatibility( $row ), false );
					$plugins[]		=	(int) $row->id;
				}

				if ( ! in_array( $row->id, $plugins ) ) {
					if ( ! $_PLUGINS->checkPluginCompatibility( $row ) ) {
						$items[]	=	array( $row, $rowVer, false, true );
					}
				}
			}
		}

		/** @noinspection PhpIncludeInspection */
		require( JModuleHelper::getLayoutPath( 'mod_cbadmin', 'updates' ) );
		break;
	case 3:
		static $JS2_loaded			=	0;

		if ( ! $JS2_loaded++ ) {
			$js						=	"cbFeedShow = function( element, settings, event, api ) {"
									.		"$( api.elements.target ).addClass( 'cbFeedItemActive' );"
									.	"};"
									.	"cbFeedHide = function( element, settings, event, api ) {"
									.		"$( api.elements.target ).removeClass( 'cbFeedItemActive' );"
									.	"};";

			$_CB_framework->outputCbJQuery( $js );
		}

		$xml						=	modCBAdminHelper::getFeedXML( 'https://www.joomlapolis.com/news?format=feed&type=rss', 'cbnewsfeed.xml', $feedDuration );

		if ( $xml ) {
			$items					=	$xml->xpath( '//channel/item' );

			/** @noinspection PhpIncludeInspection */
			require( JModuleHelper::getLayoutPath( 'mod_cbadmin', 'news' ) );
		}
		break;
	case 2:
	case 1:
	default:
		$menu						=	array();

		/** @noinspection PhpIncludeInspection */
		require( JModuleHelper::getLayoutPath( 'mod_cbadmin', 'menu' ) );

		if ( $mode == 2 ) {
			$return					=	'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
									.		modCBAdminHelper::getTable( $menu, $disabled )
									.	'</div>';

			echo $return;
		} else {
			echo modCBAdminHelper::getMenu( $menu, $disabled );
		}
		break;
}
} catch ( Exception $e ) {
	echo '<div>'
		 . '<a href="javascript: void(0);" style="color: red;" onclick="this.nextElementSibling.style.display = ( this.nextElementSibling.style.display == \'block\' ? \'none\' : \'block\' ); return;">CB Admin Module Failed</a>'
		 . '<div style="display: none;">' . $e->getTraceAsString() . '</div>'
		 . '</div>';
	return;
} catch ( Throwable $e ) {
	echo '<div>'
		 . '<a href="javascript: void(0);" style="color: red;" onclick="this.nextElementSibling.style.display = ( this.nextElementSibling.style.display == \'block\' ? \'none\' : \'block\' ); return;">CB Admin Module Failed</a>'
		 . '<div style="display: none;">' . $e->getTraceAsString() . '</div>'
		 . '</div>';
	return;
}