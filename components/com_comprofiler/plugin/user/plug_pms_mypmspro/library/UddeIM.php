<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\PMS;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;
use CBLib\Input\Get;

defined('CBLIB') or die();

class UddeIM
{

	/**
	 * Checks if UddeIM is the used model and if it's not check if model hasn't been stored, but UddeIM exists
	 *
	 * @return bool
	 */
	static public function isUddeIM()
	{
		$model		=	PMSHelper::getGlobalParams()->getString( 'general_model', '' );

		if ( ( $model == 'uddeim' ) || ( ( $model === null ) && self::isInstalled() ) ) {
			return true;
		}

		return false;
	}

	/*
	 * Checks if UddeIM is even installed and optionally loads its API
	 *
	 * @param bool $load
	 */
	static public function isInstalled( $load = false )
	{
		global $_CB_framework;

		$absPath						=	$_CB_framework->getCfg( 'absolute_path' );

		if ( ! file_exists( $absPath . '/components/com_uddeim/uddeim.php' ) ) {
			return false;
		}

		if ( $load === true ) {
			static $loaded				=	0;

			if ( ! $loaded++ ) {
				self::loadLib();

				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/administrator/components/com_uddeim/admin.shared.php' );
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/components/com_uddeim/bbparser.php' );
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/components/com_uddeim/includes.php' );
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/components/com_uddeim/includes.db.php' );
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/components/com_uddeim/crypt.class.php' );
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/administrator/components/com_uddeim/config.class.php' );
			}
		}

		return true;
	}

	/**
	 * Loads the appropriate UddeIM API lib
	 *
	 * @param string $jVersion
	 */
	static public function loadLib( $jVersion = 'auto' )
	{
		global $_CB_framework;

		$absPath	=	$_CB_framework->getCfg( 'absolute_path' );

		if ( file_exists( $absPath . '/components/com_uddeim/uddeimlib.php' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once( $absPath . '/components/com_uddeim/uddeimlib.php' );
		} elseif ( ( checkJversion( '3.3+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '3.3' ) ) {
			if ( file_exists( $absPath . '/components/com_uddeim/uddeimlib33.php' ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/components/com_uddeim/uddeimlib33.php' );
			} else {
				self::loadLib( '3.2' );
			}
		} elseif ( ( checkJversion( '3.2+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '3.2' ) ) {
			if ( file_exists( $absPath . '/components/com_uddeim/uddeimlib32.php' ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/components/com_uddeim/uddeimlib32.php' );
			} else {
				self::loadLib( '3.1' );
			}
		} elseif ( ( checkJversion( '3.1+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '3.1' ) ) {
			if ( file_exists( $absPath . '/components/com_uddeim/uddeimlib31.php' ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/components/com_uddeim/uddeimlib31.php' );
			} else {
				self::loadLib( '3.0' );
			}
		} elseif ( ( checkJversion( '3.0+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '3.0' ) ) {
			if ( file_exists( $absPath . '/components/com_uddeim/uddeimlib30.php' ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/components/com_uddeim/uddeimlib30.php' );
			} else {
				self::loadLib( '2.5' );
			}
		} elseif ( ( checkJversion( '2.5+' ) && ( $jVersion == 'auto' ) ) || ( $jVersion == '2.5' ) ) {
			if ( file_exists( $absPath . '/components/com_uddeim/uddeimlib25.php' ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once( $absPath . '/components/com_uddeim/uddeimlib25.php' );
			}
		}
	}

	/**
	 * @param bool $raw
	 * @return Registry|\uddeimconfigclass
	 */
	static public function getConfig( $raw = false )
	{
		global $_CB_framework;

		static $cache			=	array();

		if ( ! isset( $cache[$raw] ) ) {
			$config				=	new Registry();

			if ( ! self::isInstalled( true ) ) {
				$cache[$raw]	=	( $raw ? new \uddeimconfigclass() : $config );

				return $cache[$raw];
			}

			$rawConfig			=	new \uddeimconfigclass();

			if ( $raw ) {
				$cache[$raw]	=	$rawConfig;

				return $rawConfig;
			}

			uddeIMloadLanguage( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_uddeim', $rawConfig );

			$config->load( $rawConfig );

			$cache[$raw]		=	$config;
		}

		return $cache[$raw];
	}

	/**
	 * Converts BBCode to HTML
	 *
	 * @param string $string
	 * @return string
	 */
	static public function bbcodeToHTML( $string )
	{
		if ( ! self::isInstalled( true ) ) {
			return $string;
		}

		// Parse BBCode:
		$string		=	uddeIMbbcode_replace( $string, self::getConfig( true ) );

		// Remove remaining BBCode:
		$string		=	uddeIMbbcode_strip( $string );

		return $string;
	}

	/**
	 * Converts HTML to BBCode
	 *
	 * @param string $string
	 * @return string
	 */
	static public function htmlToBBCode( $string )
	{
		// Bold:
		$string		=	preg_replace( '%<strong[^>]*>(.*?)</strong>%i', '[b]$1[/b]', $string );
		$string		=	preg_replace( '%<b[^>]*>(.*?)</b>%i', '[b]$1[/b]', $string );
		$string		=	preg_replace( '%<span style="font-weight: bold">(.*?)</span>%i', '[b]$1[/b]', $string );

		// Underline:
		$string		=	preg_replace( '%<u[^>]*>(.*?)</u>%i', '[u]$1[/u]', $string );
		$string		=	preg_replace( '%<span style="text-decoration: underline">(.*?)</span>%i', '[u]$1[/u]', $string );

		// Italic:
		$string		=	preg_replace( '%<i[^>]*>(.*?)</i>%i', '[i]$1[/i]', $string );
		$string		=	preg_replace( '%<span style="font-style: italic">(.*?)</span>%i', '[i]$1[/i]', $string );

		// Size:
		$string		=	preg_replace( '%<span style="font-size: ([1-7])">(.*?)</span>%i', '[size=$1]$2[/size]', $string );
		$string		=	preg_replace( '%<font size="([1-7])">(.*?)</font>%i', '[size=$1]$2[/size]', $string );

		// Color:
		$string		=	preg_replace( '%<span style="color: #(.{1,6}?)">(.*?)</span>%i', '[color=#$1]$2[/color]', $string );

		// Links:
		$string		=	preg_replace_callback( '%<a[^>]*href="(.*?)"[^>]*>(.*?)</a>%i', function( array $matches ) {
							if ( Application::Router()->isInternal( $matches[1] ) ) {
								return '[topurl=' . $matches[1] . ']' . $matches[2] . '[/topurl]';
							} else {
								return '[url=' . $matches[1] . ']' . $matches[2] . '[/url]';
							}
						}, $string );

		// Images:
		$string		=	preg_replace( '%<img[^>]*src="(.*?)"[^>]*width="([0-9]*?)"[^>]*/>%i', '[img size=$2]$1[/img]', $string );
		$string		=	preg_replace( '%<img[^>]*src="(.*?)"[^>]*/>%i', '[img]$1[/img]', $string );

		// Lists:
		$string		=	preg_replace( '%<ul[^>]*>(.*?)</ul>%i', '[ul]$1[/ul]', $string );
		$string		=	preg_replace( '%<ol[^>]*>(.*?)</ol>%i', '[ol]$1[/ol]', $string );
		$string		=	preg_replace( '%<li[^>]*>(.*?)</li>%i', '[li]$1[/li]', $string );

		// Linebreaks:
		$string		=	preg_replace( '%<br\s*/?>%i', "\n", $string );

		// Remove any remaining unsupported HTML:
		$string		=	Get::clean( $string, GetterInterface::STRING );

		return $string;
	}

	/**
	 * Sends a PMS message (HTML)
	 *
	 * @param  int     $toUserId        UserId of receiver
	 * @param  int     $fromUserId      UserId of sender
	 * @param  string  $subject         Subject of PMS message in HTML format
	 * @param  string  $message         Body of PMS message in HTML format
	 * @param  boolean $systemGenerated False: real user-to-user message; True: system-Generated by an action from user $fromid (if non-null)
	 * @param  string  $fromName        The name of the public sender
	 * @param  string  $fromEmail       The email address of the public sender
	 * @return boolean                  True: PM sent successfully; False: PM failed to send
	 */
	static public function sendUserPMS( $toUserId, $fromUserId, $subject, $message, $systemGenerated = false, $fromName = null, $fromEmail = null )
	{
		global $_PLUGINS;

		if ( ! self::isInstalled( true ) ) {
			return false;
		}

		$toUserId				=	(int) $toUserId;
		$fromUserId				=	(int) $fromUserId;

		if ( ! $toUserId ) {
			$_PLUGINS->_setErrorMSG( CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => CBTxt::T( 'To not specified!' ) ) ) );

			return false;
		}

		if ( $subject ) {
			$message			=	"[b]" . $subject . "[/b]\n\n" . $message;
		}

		$message				=	self::htmlToBBCode( $message );

		if ( ! $message ) {
			$_PLUGINS->_setErrorMSG( CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => CBTxt::T( 'Message not specified!' ) ) ) );

			return false;
		}

		$cryptMode				=	self::getConfig()->getInt( 'cryptmode', 0 );
		$cryptKey				=	self::getConfig()->getString( 'cryptkey', 'uddeIMcryptkey' );

		$pm						=	new Table( null, '#__uddeim', 'id' );

		if ( ( ! $fromUserId ) && ( ! $systemGenerated ) && ( $fromName || $fromEmail ) ) {
			if ( self::getConfig()->getBool( 'pubfrontend', false ) ) {
				$_PLUGINS->_setErrorMSG( CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => CBTxt::T( 'From not specified!' ) ) ) );

				return false;
			}

			$pm->set( 'publicname', $fromName );
			$pm->set( 'publicemail', $fromEmail );
		} elseif ( $systemGenerated || ( ! $fromUserId ) ) {
			$fromSystem			=	self::getConfig()->getString( 'sysm_username', 'System' );

			if ( $fromUserId ) {
				$fromSystem		=	uddeIMgetNameFromID( $fromUserId, self::getConfig( true ) );
			}

			$pm->set( 'disablereply', 1 );
			$pm->set( 'systemflag', 1 );
			$pm->set( 'systemmessage', $fromSystem );
		}

		$pm->set( 'fromid', (int) $fromUserId );
		$pm->set( 'toid', (int) $toUserId );
		$pm->set( 'datum', uddetime( self::getConfig()->getInt( 'timezone', 0 ) ) );

		if ( in_array( $cryptMode, array( 1, 2, 4 ) ) ) {
			$pm->set( 'message', uddeIMencrypt( $message, $cryptKey, CRYPT_MODE_BASE64 ) );
			$pm->set( 'cryptmode', 1 );
			$pm->set( 'crypthash', md5( $cryptKey ) );
		} elseif ( $cryptMode == 3 ) {
			$pm->set( 'message', uddeIMencrypt( $message, '', CRYPT_MODE_STOREBASE64 ) );
			$pm->set( 'cryptmode', 1 );
			$pm->set( 'crypthash', md5( $cryptKey ) );
		} else {
			$pm->set( 'message', $message );
		}

		if ( uddeIMgetEMNmoderated( $pm->getInt( 'fromid', 0 ) ) ) {
			$pm->set( 'delayed', 1 );
		}

		if ( ! $pm->store() ) {
			$_PLUGINS->_setErrorMSG( CBTxt::T( 'PM_FAILED_SEND_ERROR', 'Message failed to send! Error: [error]', array( '[error]' => $pm->getError() ) ) );

			return false;
		}

		$itemId					=	uddeIMgetItemid( self::getConfig( true ) );

		if ( ! uddeIMexistsEMN( $pm->getInt( 'toid', 0 ) ) ) {
			uddeIMinsertEMNdefaults( $pm->getInt( 'toid', 0 ), self::getConfig( true ) );
		}

		$emailNotify			=	self::getConfig()->getInt( 'allowemailnotify', 0 );
		$isModerated			=	uddeIMgetEMNmoderated( $pm->getInt( 'fromid', 0 ) );
		$isReply				=	stristr( $pm->getHtml( 'message', '' ), self::getConfig()->getString( 'quotedivider', '' ), '__________' );
		$isOnline				=	uddeIMisOnline( $pm->getInt( 'toid', 0 ) );

		// Strip the html and bbcode as uddeim supports neither in its notification:
		$message				=	strip_tags( uddeIMbbcode_strip( $pm->getHtml( 'message', '' ) ) );

		if ( ! $isModerated ) {
			if ( ( $emailNotify == 1 ) || ( ( $emailNotify == 2 ) && Application::User( $pm->getInt( 'toid', 0 ) )->isSuperAdmin() ) ) {
				$status			=	uddeIMgetEMNstatus( $pm->getInt( 'toid', 0 ) );

				if ( ( $status == 1 ) || ( ( $status == 2 ) && ( ! $isOnline ) ) || ( ( $status == 10 ) && ( ! $isReply ) ) || ( ( $status == 20 ) && ( ! $isOnline ) && ( ! $isReply ) ) )  {
					uddeIMdispatchEMN( $pm->getInt( 'id', 0 ), $itemId, 0, $pm->getInt( 'fromid', 0 ), $pm->getInt( 'toid', 0 ), $message, 0, self::getConfig( true ) );
				}
			}
		}

		return true;
	}

	/**
	 * returns all the parameters needed for a hyperlink or a menu entry to do a pms action
	 *
	 * @param  int     $toUserId     UserId of receiver
	 * @param  int     $fromUserId   UserId of sender
	 * @param  string  $subject      Subject of PMS message
	 * @param  string  $message      Body of PMS message
	 * @param  int     $kind         kind of link: 1: link to compose new PMS message for $toid user. 2: link to inbox of $fromid user; 3: outbox, 4: trashbox, 5: link to edit pms options, 6: archive
	 * @return array|boolean         Array of string {"caption" => menu-text ,"url" => NON-cbSef relative url-link, "tooltip" => description} or false and errorMSG
	 */
	static public function getPMSlink( $toUserId, $fromUserId, $subject, $message, $kind )
	{
		if ( ! self::isInstalled( true ) ) {
			return false;
		}

		static $itemId		=	null;

		if ( $itemId === null ) {
			$itemId			=	uddeIMgetItemid( self::getConfig( true ) );
		}

		$urlBase			=	'index.php?option=com_uddeim';
		$urlItemId			=	( $itemId ? '&amp;Itemid=' . (int) $itemId : null );

		switch( $kind ) {
			case 1: // Send PM
				return array(	'caption'	=>	CBTxt::T( 'PM_USER', 'Send Private Message' ),
								'url'		=>	$urlBase . '&amp;task=new&amp;recip=' . (int) $toUserId . $urlItemId,
								'tooltip'	=>	CBTxt::T( 'PM_USER_DESC', 'Send a Private Message to this user' )
							);
				break;
			case 2: // Inbox
				return array(	'caption'	=>	CBTxt::T( 'PM_INBOX', 'Show Private Inbox' ),
								'url'		=>	$urlBase . '&amp;task=inbox' . $urlItemId,
								'tooltip'	=>	CBTxt::T( 'PM_INBOX_DESC', 'Show Received Private Messages' )
							);
				break;
			case 3: // Outbox
				return array(	'caption'	=>	CBTxt::T( 'PM_OUTBOX', 'Show Private Outbox' ),
								'url'		=>	$urlBase . '&amp;task=outbox' . $urlItemId,
								'tooltip'	=>	CBTxt::T( 'PM_OUTBOX_DESC', 'Show Sent/Pending Private Messages' )
							);
				break;
			case 4: // Trashcan
				return array(	'caption'	=>	CBTxt::T( 'PM_TRASHBOX', 'Show Private Trashbox' ),
								'url'		=>	$urlBase . '&amp;task=trashcan' . $urlItemId,
								'tooltip'	=>	CBTxt::T( 'PM_TRASHBOX_DESC', 'Show Trashed Private Messages' )
							);
				break;
			case 5: // Options
				return array(	'caption'	=>	CBTxt::T( 'PM_OPTIONS', 'Edit PMS Options' ),
								'url'		=>	$urlBase . '&amp;task=settings' . $urlItemId,
								'tooltip'	=>	CBTxt::T( 'PM_OPTIONS_DESC', 'Edit Private Messaging System Options' )
							);
				break;
			case 6: // Archive
				return array(	'caption'	=>	CBTxt::T( 'PM_ARCHIVE', 'Show Private Archive' ),
								'url'		=>	$urlBase . '&amp;task=archive' . $urlItemId,
								'tooltip'	=>	CBTxt::T( 'PM_ARCHIVE_DESC', 'Show Archived Private Messages' )
							);
				break;
		}

		return false;
	}

	/**
	 * Returs array of PMS capabilities or false if no compatible PMS is installed
	 *
	 * @return array|bool false: no compatible PMS installed; array: { 'subject' => boolean, 'body' => boolean, 'public' => boolean }
	 */
	static public function getPMScapabilites()
	{
		if ( ! self::isInstalled( true ) ) {
			return false;
		}

		return array( 'subject' => false, 'body' => true, 'public' => self::getConfig()->getBool( 'pubfrontend', false ) );
	}

	/**
	 * Counts number of unread uddeim messages (trashed and archived also excluded) for a user
	 *
	 * @param int $userId
	 * @return int
	 */
	static public function getPMSunreadCount( $userId )
	{
		if ( ! self::isInstalled( true ) ) {
			return 0;
		}

		return uddeIMgetInboxCount( $userId, 0, 1 );
	}

	/**
	 * Called when a user is deleted to clean up their private messages
	 *
	 * @param UserTable $user
	 * @param bool      $success
	 */
	static public function deleteMessages( $user, $success )
	{
		global $_CB_database;

		if ( ! self::isInstalled( true ) ) {
			return;
		}

		if ( ! PMSHelper::getGlobalParams()->getInt( 'pmsDelete', 0 ) ) {
			return;
		}

		$sent				=	PMSHelper::getGlobalParams()->getInt( 'pmsDeleteSent', 0 );
		$received			=	PMSHelper::getGlobalParams()->getInt( 'pmsDeleteRecieved', 1 );

		if ( $sent || $received ) {
			// Private Messages:
			$query			=	"DELETE"
							.	"\n FROM " . $_CB_database->NameQuote( '#__uddeim' );
			if ( $sent && $received ) {
				$query		.=	"\n WHERE ( " . $_CB_database->NameQuote( 'fromid' ) . " = " . $user->getInt( 'id', 0 )
							.	" OR " . $_CB_database->NameQuote( 'toid' ) . " = " . $user->getInt( 'id', 0 ) . ' )';
			} elseif ( $sent ) {
				$query		.=	"\n WHERE " . $_CB_database->NameQuote( 'fromid' ) . " = " . $user->getInt( 'id', 0 );
			} elseif ( $received ) {
				$query		.=	"\n WHERE " . $_CB_database->NameQuote( 'toid' ) . " = " . $user->getInt( 'id', 0 );
			}
			$_CB_database->setQuery( $query );
			$_CB_database->query();
		}

		// Notifications:
		$query				=	"DELETE"
							.	"\n FROM " . $_CB_database->NameQuote( '#__uddeim_emn' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->getInt( 'id', 0 );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Blocks:
		$query				=	"DELETE"
							.	"\n FROM " . $_CB_database->NameQuote( '#__uddeim_blocks' )
							.	"\n WHERE ( " . $_CB_database->NameQuote( 'blocker' ) . " = " . $user->getInt( 'id', 0 )
							.	" OR " . $_CB_database->NameQuote( 'blocked' ) . " = " . $user->getInt( 'id', 0 ) . ' )';
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Userlists:
		$query				=	"DELETE"
							.	"\n FROM " . $_CB_database->NameQuote( '#__uddeim_userlists' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->getInt( 'id', 0 );
		$_CB_database->setQuery( $query );
		$_CB_database->query();

		// Spam:
		$query				=	"DELETE"
							.	"\n FROM " . $_CB_database->NameQuote( '#__uddeim_spam' )
							.	"\n WHERE ( " . $_CB_database->NameQuote( 'fromid' ) . " = " . $user->getInt( 'id', 0 )
							.	" OR " . $_CB_database->NameQuote( 'toid' ) . " = " . $user->getInt( 'id', 0 ) . ' )';
		$_CB_database->setQuery( $query );
		$_CB_database->query();
	}

	/**
	 * Migrate UddeIM messages and read states to internal model
	 *
	 * @return null|string
	 */
	static public function migrate()
	{
		global $_CB_framework, $_CB_database, $_PLUGINS;

		$batch					=	Application::Input()->getString( 'batch', '' );

		if ( ! $batch ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'UddeIM migration has started. Please do not interrupt this process. Messages will be migrated first, followed by replies, followed by read states. This operation may take awhile depending on how many messages you have.' ), 'info' );

			$js					=	"function uddeIMMigrateMessages() {" // Migrate Messages
								.		"$.ajax({"
								.			"url: '" . addslashes( $_CB_framework->backendViewUrl( 'editPlugin', false, array( 'action' => 'migrateuddeim', 'batch' => 'messages', 'cid' => $_PLUGINS->getPluginId() ), 'raw' ) ) . "',"
								.			"cache: false,"
								.			"dataType: 'json',"
								.			"beforeSend: function( jqXHR, settings ) {"
								.				"$( '.cbUddeIMMigrationMessages' ).attr( 'aria-valuenow', 50 ).css( 'width', '50%' );"
								.			"}"
								.		"}).done( function( data, textStatus, jqXHR ) {"
								.			"if ( ! data ) {"
								.				"$( '.cbUddeIMMigrationMessages' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-warning' );"
								.			"} else if ( data.status ) {"
								.				"$( '.cbUddeIMMigrationMessages' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-success' );"
								.				"uddeIMMigrateReplies();"
								.			"} else {"
								.				"$( '.cbUddeIMMigrationMessages' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-danger' );"
								.				"if ( data.message ) {"
								.					"$( '.cbUddeIMMigrationError' ).removeClass( 'hidden' ).html( data.message );"
								.				"}"
								.			"}"
								.		"}).fail( function( jqXHR, textStatus, errorThrown ) {"
								.			"$( '.cbUddeIMMigrationMessages' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-danger' );"
								.		"});"
								.	"};"
								.	"function uddeIMMigrateReplies() {" // Migrate Replies
								.		"$.ajax({"
								.			"url: '" . addslashes( $_CB_framework->backendViewUrl( 'editPlugin', false, array( 'action' => 'migrateuddeim', 'batch' => 'replies', 'cid' => $_PLUGINS->getPluginId() ), 'raw' ) ) . "',"
								.			"cache: false,"
								.			"dataType: 'json',"
								.			"beforeSend: function( jqXHR, settings ) {"
								.				"$( '.cbUddeIMMigrationReplies' ).attr( 'aria-valuenow', 25 ).css( 'width', '25%' );"
								.			"}"
								.		"}).done( function( data, textStatus, jqXHR ) {"
								.			"if ( ! data ) {"
								.				"$( '.cbUddeIMMigrationReplies' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-warning' );"
								.			"} else if ( data.status ) {"
								.				"$( '.cbUddeIMMigrationReplies' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-success' );"
								.				"uddeIMMigrateRead();"
								.			"} else {"
								.				"$( '.cbUddeIMMigrationReplies' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-danger' );"
								.				"if ( data.message ) {"
								.					"$( '.cbUddeIMMigrationError' ).removeClass( 'hidden' ).html( data.message );"
								.				"}"
								.			"}"
								.		"}).fail( function( jqXHR, textStatus, errorThrown ) {"
								.			"$( '.cbUddeIMMigrationReplies' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-danger' );"
								.		"});"
								.	"};"
								.	"function uddeIMMigrateRead() {" // Migrate Read State
								.		"$.ajax({"
								.			"url: '" . addslashes( $_CB_framework->backendViewUrl( 'editPlugin', false, array( 'action' => 'migrateuddeim', 'batch' => 'read', 'cid' => $_PLUGINS->getPluginId() ), 'raw' ) ) . "',"
								.			"cache: false,"
								.			"dataType: 'json',"
								.			"beforeSend: function( jqXHR, settings ) {"
								.				"$( '.cbUddeIMMigrationRead' ).attr( 'aria-valuenow', 25 ).css( 'width', '25%' );"
								.			"}"
								.		"}).done( function( data, textStatus, jqXHR ) {"
								.			"if ( ! data ) {"
								.				"$( '.cbUddeIMMigrationRead' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-warning' );"
								.			"} else if ( data.status ) {"
								.				"$( '.cbUddeIMMigrationRead' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-success' );"
								.				"$( '.cbUddeIMMigrationSuccess' ).removeClass( 'hidden' );"
								.			"} else {"
								.				"$( '.cbUddeIMMigrationRead' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-danger' );"
								.				"if ( data.message ) {"
								.					"$( '.cbUddeIMMigrationError' ).removeClass( 'hidden' ).html( data.message );"
								.				"}"
								.			"}"
								.		"}).fail( function( jqXHR, textStatus, errorThrown ) {"
								.			"$( '.cbUddeIMMigrationRead' ).removeClass( 'progress-bar-striped progress-bar-animated' ).addClass( 'bg-danger' );"
								.		"});"
								.	"};"
								.	"uddeIMMigrateMessages();";

			$_CB_framework->outputCbJQuery( $js );

			$return				=	'<div class="cbUddeIMMigration">'
								.		'<div class="progress">'
								.			'<div class="progress-bar progress-bar-striped progress-bar-animated cbUddeIMMigrationMessages" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>'
								.			'<div class="progress-bar progress-bar-striped progress-bar-animated cbUddeIMMigrationReplies" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>'
								.			'<div class="progress-bar progress-bar-striped progress-bar-animated cbUddeIMMigrationRead" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>'
								.		'</div>'
								.		'<div class="mt-2 text-success cbUddeIMMigrationSuccess hidden">' . CBTxt::T( 'UddeIM Migration complete! Please verify your messages have migrated correctly using the Messages button above.' ) . '</div>'
								.		'<div class="mt-2 text-danger cbUddeIMMigrationError hidden"></div>'
								.	'</div>';

			return $return;
		}

		// Check if our uddeim migration column as well as its index exists yet and if not create it; this is vital to avoid duplicate migration:
		$table					=	'#__comprofiler_plugin_messages';
		$fields					=	$_CB_database->getTableFields( $table );

		if ( ! isset( $fields[$table]['uddeim'] ) ) {
			$query				=	"ALTER TABLE " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' )
								.	" ADD " . $_CB_database->NameQuote( 'uddeim' ) . " int(11) DEFAULT NULL"
								.	", ADD UNIQUE INDEX ( " . $_CB_database->NameQuote( 'uddeim' ) . " )";
			$_CB_database->setQuery( $query );
				try {
					$_CB_database->query();

					if ( $_CB_database->getErrorMsg() ) {
						echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_MESSAGES_ERROR', 'Messages failed to migrate! Error: [error]', array( '[error]' => $_CB_database->getErrorMsg() ) ) ) );
						exit();
					}
				}
				catch ( \RuntimeException $e ) {
					echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_MESSAGES_ERROR', 'Messages failed to migrate! Error: [error]', array( '[error]' => $e->getMessage() ) ) ) );
					exit();
				}
		}

		header( 'Content-Type: application/json' );
		header( "HTTP/1.0 200 OK" );

		// Messages:
		if ( $batch == 'messages' ) {
			// We need to know how UddeIM is splitting the replies since we don't want to include the reply test interally as that will be grabbed using API on reply display:
			$replyDivider		=	self::getConfig()->getString( 'quotedivider', '__________' );

			$query				=	"INSERT INTO " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' )
								.	" ( "
								.		$_CB_database->NameQuote( 'from_user' )
								.		", " . $_CB_database->NameQuote( 'from_name' )
								.		", " . $_CB_database->NameQuote( 'from_email' )
								.		", " . $_CB_database->NameQuote( 'from_system' )
								.		", " . $_CB_database->NameQuote( 'to_user' )
								.		", " . $_CB_database->NameQuote( 'reply_to' )
								.		", " . $_CB_database->NameQuote( 'message' )
								.		", " . $_CB_database->NameQuote( 'from_user_delete' )
								.		", " . $_CB_database->NameQuote( 'to_user_delete' )
								.		", " . $_CB_database->NameQuote( 'date' )
								.		", " . $_CB_database->NameQuote( 'uddeim' )
								.	" )"
								.	"\n SELECT ud." . $_CB_database->NameQuote( 'fromid' )
								.	", ud." . $_CB_database->NameQuote( 'publicname' )
								.	", ud." . $_CB_database->NameQuote( 'publicemail' )
								.	", ud." . $_CB_database->NameQuote( 'systemflag' )
								.	", ud." . $_CB_database->NameQuote( 'toid' )
								.	", ud." . $_CB_database->NameQuote( 'replyid' );
			if ( $replyDivider ) {
				$query			.=	", SUBSTRING_INDEX( ud." . $_CB_database->NameQuote( 'message' ) . ", " . $_CB_database->Quote( $replyDivider ) . ", 1 )";
			} else {
				$query			.=	", ud." . $_CB_database->NameQuote( 'message' );
			}
			$query				.=	", ud." . $_CB_database->NameQuote( 'totrashoutbox' )
								.	", ud." . $_CB_database->NameQuote( 'totrash' )
								.	", FROM_UNIXTIME( ud." . $_CB_database->NameQuote( 'datum' ) . " )"
								.	", ud." . $_CB_database->NameQuote( 'id' )
								.	"\n FROM " . $_CB_database->NameQuote( '#__uddeim' ) . " AS ud"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' ) . " AS m"
								.	"\n ON m." . $_CB_database->NameQuote( 'uddeim' ) . " = ud." . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE m." . $_CB_database->NameQuote( 'id' ) . " IS NULL";
			$_CB_database->setQuery( $query );
			try {
				$_CB_database->query();

				if ( $_CB_database->getErrorMsg() ) {
					echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_MESSAGES_ERROR', 'Messages failed to migrate! Error: [error]', array( '[error]' => $_CB_database->getErrorMsg() ) ) ) );
					exit();
				}
			}
			catch ( \RuntimeException $e ) {
				echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_MESSAGES_ERROR', 'Messages failed to migrate! Error: [error]', array( '[error]' => $e->getMessage() ) ) ) );
				exit();
			}

			echo json_encode( array( 'status' => true, 'message' => '' ) );
			exit();
		}

		// Replies:
		if ( $batch == 'replies' ) {
			$query				=	"UPDATE " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' ) . " AS m"
								.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__uddeim' ) . " AS ud" // Find the original uddeim message
								.	"\n ON ud." . $_CB_database->NameQuote( 'id' ) . " = m." . $_CB_database->NameQuote( 'uddeim' )
								.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__uddeim' ) . " AS r" // Find the reply for the matching uddeim message
								.	"\n ON r." . $_CB_database->NameQuote( 'id' ) . " = ud." . $_CB_database->NameQuote( 'replyid' )
								.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' ) . " AS rm" // Find the internal message for the uddeim reply message
								.	"\n ON rm." . $_CB_database->NameQuote( 'uddeim' ) . " = r." . $_CB_database->NameQuote( 'id' )
								.	"\n SET m." . $_CB_database->NameQuote( 'reply_to' ) . " = rm." . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE m." . $_CB_database->NameQuote( 'reply_to' ) . " != 0";
			$_CB_database->setQuery( $query );
			try {
				$_CB_database->query();

				if ( $_CB_database->getErrorMsg() ) {
					echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_REPLIES_ERROR', 'Replies failed to migrate! Error: [error]', array( '[error]' => $_CB_database->getErrorMsg() ) ) ) );
					exit();
				}
			}
			catch ( \RuntimeException $e ) {
				echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_REPLIES_ERROR', 'Replies failed to migrate! Error: [error]', array( '[error]' => $e->getMessage() ) ) ) );
				exit();
			}

			echo json_encode( array( 'status' => true, 'message' => '' ) );
			exit();
		}

		// Read States:
		if ( $batch == 'read' ) {
			// Check if an efficient index for grabbing read messages in uddeim exists; if not add it:
			$table				=	'#__uddeim';
			$indexes			=	array();

			foreach ( $_CB_database->getTableIndex( $table ) as $index ) {
				$indexes[]		=	$index->Key_name;
			}

			if ( ! in_array( 'read_messages', $indexes ) ) {
				$query			=	"ALTER TABLE " . $_CB_database->NameQuote( '#__uddeim' ) . " ADD INDEX " . $_CB_database->NameQuote( 'read_messages' ) . " ( " . $_CB_database->NameQuote( 'toread' ) . " )";
				$_CB_database->setQuery( $query );
				try {
					$_CB_database->query();

					if ( $_CB_database->getErrorMsg() ) {
						echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_READ_ERROR', 'Read states failed to migrate! Error: [error]', array( '[error]' => $_CB_database->getErrorMsg() ) ) ) );
						exit();
					}
				}
				catch ( \RuntimeException $e ) {
					echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_READ_ERROR', 'Read states failed to migrate! Error: [error]', array( '[error]' => $e->getMessage() ) ) ) );
					exit();
				}
			}

			$query				=	"INSERT INTO " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages_read' )
								.	" ( "
								.		$_CB_database->NameQuote( 'to_user' )
								.		", " . $_CB_database->NameQuote( 'message' )
								.		", " . $_CB_database->NameQuote( 'date' )
								.	" )"
								.	"\n SELECT m." . $_CB_database->NameQuote( 'to_user' )
								.	", m." . $_CB_database->NameQuote( 'id' )
								.	", NOW()"
								.	"\n FROM " . $_CB_database->NameQuote( '#__uddeim' ) . " AS ud"
								.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages' ) . " AS m"
								.	"\n ON m." . $_CB_database->NameQuote( 'uddeim' ) . " = ud." . $_CB_database->NameQuote( 'id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages_read' ) . " AS r"
								.	"\n ON r." . $_CB_database->NameQuote( 'to_user' ) . " = m." . $_CB_database->NameQuote( 'to_user' )
								.	"\n AND r." . $_CB_database->NameQuote( 'message' ) . " = m." . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE ud." . $_CB_database->NameQuote( 'toread' ) . " = 1"
								.	"\n AND r." . $_CB_database->NameQuote( 'id' ) . " IS NULL";
			$_CB_database->setQuery( $query );
			try {
				$_CB_database->query();

				if ( $_CB_database->getErrorMsg() ) {
					echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_READ_ERROR', 'Read states failed to migrate! Error: [error]', array( '[error]' => $_CB_database->getErrorMsg() ) ) ) );
					exit();
				}
			}
			catch ( \RuntimeException $e ) {
				echo json_encode( array( 'status' => false, 'message' => CBTxt::T( 'UDDEIM_MIGRATE_READ_ERROR', 'Read states failed to migrate! Error: [error]', array( '[error]' => $e->getMessage() ) ) ) );
				exit();
			}

			echo json_encode( array( 'status' => true, 'message' => '' ) );
			exit();
		}

		return null;
	}
}
