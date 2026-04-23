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
use CB\Plugin\PMS\PMSHelper;
use CB\Plugin\PMS\UddeIM;
use CB\Plugin\PMS\Table\MessageTable;
use CB\Plugin\PMS\Table\ReadTable;

defined('CBLIB') or die();

class UserTrigger extends \cbPluginHandler
{

	/**
	 * Called when a user is deleted to clean up their private messages
	 *
	 * @param UserTable $user
	 * @param bool      $success
	 */
	public function deleteMessages( $user, $success )
	{
		global $_CB_database;

		if ( UddeIM::isUddeIM() ) {
			UddeIM::deleteMessages( $user, $success );
			return;
		}

		if ( ! PMSHelper::getGlobalParams()->getBool( 'pmsDelete', false ) ) {
			return;
		}

		$sent				=	PMSHelper::getGlobalParams()->getBool( 'pmsDeleteSent', false );
		$received			=	PMSHelper::getGlobalParams()->getBool( 'pmsDeleteRecieved', true );

		if ( $sent || $received ) {
			$query			=	"SELECT *"
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' );
			if ( $sent && $received ) {
				$query		.=	"\n WHERE ( " . $_CB_database->NameQuote( 'from_user' ) . " = " . $user->getInt( 'id', 0 )
							.	" OR " . $_CB_database->NameQuote( 'to_user' ) . " = " . $user->getInt( 'id', 0 ) . " )";
			} elseif ( $sent ) {
				$query		.=	"\n WHERE " . $_CB_database->NameQuote( 'from_user' ) . " = " . $user->getInt( 'id', 0 );
			} elseif ( $received ) {
				$query		.=	"\n WHERE " . $_CB_database->NameQuote( 'to_user' ) . " = " . $user->getInt( 'id', 0 );
			}
			$_CB_database->setQuery( $query );
			$messages		=	$_CB_database->loadObjectList( null, '\CB\Plugin\PMS\Table\MessageTable', array( $_CB_database ) );

			/** @var MessageTable[] $messages */
			foreach ( $messages as $message ) {
				$message->delete();
			}
		}

		$query				=	"SELECT *"
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages_read' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'to_user' ) . " = " . $user->getInt( 'id', 0 );
		$_CB_database->setQuery( $query );
		$dates				=	$_CB_database->loadObjectList( null, '\CB\Plugin\PMS\Table\ReadTable', array( $_CB_database ) );

		/** @var ReadTable[] $dates */
		foreach ( $dates as $date ) {
			$date->delete();
		}
	}
}