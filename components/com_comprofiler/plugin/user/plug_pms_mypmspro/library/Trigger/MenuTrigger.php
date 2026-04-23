<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\PMS\Trigger;

use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CB\Plugin\PMS\PMSHelper;
use CBLib\Language\CBTxt;
use CB\Plugin\PMS\UddeIM;

defined('CBLIB') or die();

class MenuTrigger extends \cbPluginHandler
{

	/**
	 * Displays frontend messages icon on cb menu bar
	 *
	 * @param UserTable $user
	 * @return null|string
	 */
	public function getMessages( $user )
	{
		global $_CB_framework, $_CB_PMS;

		if ( ( ! $this->params->getBool( 'messages_icon', true ) )
			 || ( Application::MyUser()->getUserId() != $user->getInt( 'id', 0 ) ) ) {
			return null;
		}

		$unread					=	$_CB_PMS->getPMSunreadCount( $user->getInt( 'id', 0 ) );

		if ( isset( $unread[0] ) ) {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$unread				=	$unread[0];
		} else {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$unread				=	0;
		}

		$total					=	$unread;

		if ( $total >= 1000 ) {
			$total				=	round( $total / 1000, 1 );

			/** @noinspection PhpUnusedLocalVariableInspection */
			$total				=	CBTxt::T( 'TOTAL_FORMATTED_SHORT', '[total]K', array( '[total]' => number_format( $total, ( floor( $total ) != $total ? 1 : 0 ) ) ) );
		} else {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$total				=	number_format( $total );
		}

		$inbox					=	null;

		if ( ! UddeIM::isUddeIM() ) {
			$loading			=	'<div class="text-center m-3 pmMessagesLoading"><div class="spinner-border spinner-border-sm text-secondary"></div></div>';

			$js					=	"$( '.cbPMSMessages' ).on( 'cbtooltip.render', function( e, cbtooltip ) {"
								.		"e.preventDefault();"
								.		"var element = $( this );"
								.		"if ( element.hasClass( 'messagesRequesting' ) ) {"
								.			"return false;"
								.		"}"
								.		"element.addClass( 'messagesRequesting' );"
								.		"var ajax = null;"
								.		"cbtooltip.tooltip.qtip( 'api' ).set( 'content.text', function( e, api ) {"
								.			"if ( ajax == null ) {"
								.				"ajax = $.ajax({"
								.							"url: '" . addslashes( $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'messages', 'func' => 'modal' ), 'raw', 0, true ) ) . "',"
								.							"type: 'GET',"
								.							"dataType: 'html',"
								.						"}).fail( function( jqXHR, textStatus, errorThrown ) {"
								.							"element.removeClass( 'messagesRequesting' );"
								.							"if ( ! api.destroyed ) {"
								.								"api.hide();"
								.							"}"
								.						"}).done( function( data, textStatus, jqXHR ) {"
								.							"element.removeClass( 'messagesRequesting' );"
								.							"if ( api.destroyed ) {"
								.								"return;"
								.							"}"
								.							"api.elements.tooltip.removeClass( 'pmMessagesModalLoad pmMessagesModalLoading' );"
								.							"if ( data ) {"
								.								"api.set( 'content.text', $( data ) );"
								.								"api.elements.content.find( '.cbTooltip,[data-hascbtooltip=\"true\"]' ).cbtooltip();"
								.								"api.elements.content.find( '.pmMessagesRow' ).on( 'click', function( e ) {"
								.									"if ( ! ( $( e.target ).is( 'a' ) || $( e.target ).closest( 'a' ).length || $( e.target ).is( '.btn' ) || $( e.target ).closest( '.btn' ).length ) ) {"
								.										"var url = $( this ).data( 'pm-url' );"
								.										"if ( url ) {"
								.											"window.location = url;"
								.										"}"
								.									"}"
								.								"});"
								.							"} else {"
								.								"api.hide();"
								.							"}"
								.						"});"
								.			"}"
								.			"return '" . addslashes( $loading ) . "';"
								.		"});"
								.		"return ajax;"
								.	"});";

			$_CB_framework->outputCbJQuery( $js );

			initToolTip();
		} else {
			$link				=	$_CB_PMS->getPMSlinks( null, $user->getInt( 'id', 0 ), null, null, 2 );

			if ( isset( $link[0]['url'] ) ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$inbox			=	$link[0]['url'];
			} else {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$inbox			=	'index.php?option=com_uddeim';
			}
		}

		ob_start();
		require PMSHelper::getTemplate( null, 'messages_icon' );
		return ob_get_clean();
	}
}