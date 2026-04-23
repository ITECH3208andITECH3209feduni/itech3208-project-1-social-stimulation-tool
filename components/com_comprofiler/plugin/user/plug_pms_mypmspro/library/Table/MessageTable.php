<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\PMS\Table;

use ActionlogsModelActionlog;
use CB\Database\Table\UserTable;
use CB\Plugin\PMS\PMSHelper;
use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Input\Get;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CBuser;

defined('CBLIB') or die();

class MessageTable extends Table
{
	/** @var int  */
	public $id					=	null;
	/** @var int  */
	public $from_user			=	null;
	/** @var string  */
	public $from_name			=	null;
	/** @var string  */
	public $from_email			=	null;
	/** @var int  */
	public $from_system			=	null;
	/** @var int  */
	public $to_user				=	null;
	/** @var int  */
	public $reply_to			=	null;
	/** @var string  */
	public $message				=	null;
	/** @var int  */
	public $from_user_delete	=	null;
	/** @var int  */
	public $to_user_delete		=	null;
	/** @var string  */
	public $date				=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_messages';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( ( ! $this->getInt( 'from_user', 0 ) ) && ( ! $this->getBool( 'from_system', false ) ) ) {
			if ( $this->getString( 'from_name', '' ) == '' ) {
				$this->setError( CBTxt::T( 'Name not specified!' ) );

				return false;
			}

			if ( $this->getString( 'from_email', '' ) == '' ) {
				$this->setError( CBTxt::T( 'Email Address not specified!' ) );

				return false;
			} elseif ( ! cbIsValidEmail( $this->getString( 'from_email', '' ) ) ) {
				$this->setError( CBTxt::T( 'Email Address is not valid!' ) );

				return false;
			}
		}

		if ( $this->getString( 'message', '' ) == '' ) {
			$this->setError( CBTxt::T( 'Message not specified!' ) );

			return false;
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_CB_framework, $_PLUGINS;

		$new								=	( $this->getInt( 'id', 0 ) ? false : true );
		$old								=	new self();

		$this->set( 'date', $this->getString( 'date', Application::Database()->getUtcDateTime() ) );

		if ( ! $new ) {
			$old->load( $this->getInt( 'id', 0 ) );

			$integrations					=	$_PLUGINS->trigger( 'pm_onBeforeUpdateMessage', array( &$this, $old ) );
		} else {
			$integrations					=	$_PLUGINS->trigger( 'pm_onBeforeCreateMessage', array( &$this ) );
		}

		if ( in_array( false, $integrations, true ) ) {
			return false;
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'pm_onAfterUpdateMessage', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'pm_onAfterCreateMessage', array( $this ) );

			// Send Notification:
			$notify							=	PMSHelper::getGlobalParams()->getInt( 'messages_notify', 0 );

			if ( $notify && $this->getInt( 'to_user', 0 ) ) {
				if ( PMSHelper::getGlobalParams()->getBool( 'messages_notify_offline', false ) && ( $_CB_framework->userOnlineLastTime( $this->getInt( 'to_user', 0 ) ) != null ) ) {
					// Notifications are set to only be sent to offline users, but the user is online so disable notifications:
					$notify					=	0;
				} elseif ( $notify == 4 ) {
					$notifyField			=	PMSHelper::getGlobalParams()->getInt( 'messages_notify_field', 0 );

					// Default notifications off encase no field was selected or the field couldn't be found:
					$notify					=	0;

					if ( $notifyField ) {
						$cbUser				=	\CBuser::getInstance( $this->getInt( 'to_user', 0 ), false );
						$notifyFieldValue	=	$cbUser->getField( $notifyField, null, 'php', 'none', 'profile', 0, true );

						if ( is_array( $notifyFieldValue ) ) {
							$notify			=	array_shift( $notifyFieldValue );

							if ( is_array( $notify ) ) {
								$notify		=	implode( '|*|', $notify );
							}

							$notify			=	(int) $notify;
						}
					}
				}

				if ( ( $notify == 1 ) || ( ( $notify == 2 ) && ( ! $this->getInt( 'reply_to', 0 ) ) ) || ( ( $notify == 3 ) && $this->getInt( 'reply_to', 0 ) ) ) {
					$cbNotification			=	new \cbNotification();

					$savedLanguage			=	CBTxt::setLanguage( \CBuser::getUserDataInstance( $this->getInt( 'to_user', 0 ) )->getUserLanguage() );
					$messageUrl				=	$_CB_framework->pluginClassUrl( 'pms.mypmspro', true, [ 'action' => 'message', 'func' => 'show', 'id' => $this->getInt( 'id', 0 ) ] );

					if ( $this->getInt( 'reply_to', 0 ) ) {
						$subject			=	CBTxt::T( 'You have a new private message reply' );

						if ( PMSHelper::getGlobalParams()->getBool( 'messages_notify_message', false ) ) {
							$message		=	CBTxt::T( 'FROM_HAS_REPLIED_MESSAGE_WITH', '[from] has <a href="[message_url]">replied to your private message</a>.<br /><br />[message]', array( '[from]' => $this->getFrom( 'profile_direct' ), '[message]' => $this->getMessage(), '[message_url]' => $messageUrl ) );
						} else {
							$message		=	CBTxt::T( 'FROM_HAS_REPLIED_MESSAGE', '[from] has <a href="[message_url]">replied to your private message</a>.', array( '[from]' => $this->getFrom( 'profile_direct' ), '[message_url]' => $messageUrl ) );
						}
					} else {
						$subject			=	CBTxt::T( 'You have a new private message' );

						if ( PMSHelper::getGlobalParams()->getBool( 'messages_notify_message', false ) ) {
							$message		=	CBTxt::T( 'FROM_HAS_SENT_MESSAGE_WITH', '[from] has <a href="[message_url]">sent you a new private message</a>.<br /><br />[message]', array( '[from]' => $this->getFrom( 'profile_direct' ), '[message]' => $this->getMessage(), '[message_url]' => $messageUrl ) );
						} else {
							$message		=	CBTxt::T( 'FROM_HAS_SENT_MESSAGE', '[from] has <a href="[message_url]">sent you a new private message</a>.', array( '[from]' => $this->getFrom( 'profile_direct' ), '[message_url]' => $messageUrl ) );
						}
					}

					$cbNotification->sendFromSystem( $this->getInt( 'to_user', 0 ), $subject, $message, false, 1, null, null, null, array(), true, CBTxt::T( PMSHelper::getGlobalParams()->getString( 'messages_notify_from_name', '' ) ), PMSHelper::getGlobalParams()->getString( 'messages_notify_from_email', '' ) );

					CBTxt::setLanguage( $savedLanguage );
				}
			}
		}

		return true;
	}

	/**
	 * Generic check for whether dependencies exist for this object in the db schema
	 * Should be overridden if checks need to be done before delete()
	 *
	 * @param  int  $oid  key index (only int supported here)
	 * @return boolean
	 */
	public function canDelete( $oid = null )
	{
		if ( Application::Application()->isClient( 'administrator' ) ) {
			if ( $this->getBool( 'from_system', false ) ) {
				return true;
			}

			$from	=	$this->getInt( 'to_user', 0 );
			$userId	=	Application::MyUser()->getUserId();

			if ( ( ! $from ) || ( $from === $userId ) ) {
				return true;
			}

			$to		=	$this->getInt( 'from_user', 0 );

			if ( ( ! $to ) || ( $to === $userId ) ) {
				return true;
			}

			return PMSHelper::getGlobalParams()->getBool( 'manage_user_messages', false )
				   && Application::MyUser()->isAuthorizedToPerformActionOnAsset( 'pms.usermessages', 'com_comprofiler.plugin.pms' );
		}

		// Frontend checks done in the component controller
		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$integrations	=	$_PLUGINS->trigger( 'pm_onBeforeDeleteMessage', array( &$this ) );

		if ( in_array( false, $integrations, true ) ) {
			return false;
		}

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Cleans up read states for this message:
		$query			=	"SELECT *"
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_messages_read' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'message' ) . " = " . $this->getInt( 'id', 0 );
		$this->getDbo()->setQuery( $query );
		$dates			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\PMS\Table\ReadTable', array( $this->getDbo() ) );

		/** @var ReadTable[] $dates */
		foreach ( $dates as $date ) {
			$date->delete();
		}

		$_PLUGINS->trigger( 'pm_onAfterDeleteMessage', array( $this ) );

		return true;
	}

	/**
	 * @param string $field
	 * @return string|array
	 */
	public function getFrom( $field = 'name' )
	{
		global $_CB_framework;

		static $cache				=	array();

		$userId						=	Application::MyUser()->getUserId();
		$id							=	$this->getInt( 'id', 0 );

		if ( ! isset( $cache[$userId][$id] ) ) {
			$profileDirect			=	null;
			$status					=	null;

			if ( $this->getBool( 'from_system', false ) ) {
				$cbUser				=	\CBuser::getMyInstance();
				$name				=	CBTxt::T( PMSHelper::getGlobalParams()->getHtml( 'messages_system_name', 'System' ) );

				if ( ! $name ) {
					$name			=	CBTxt::T( 'System' );
				}

				$name				=	$cbUser->replaceUserVars( $name, true, false, null, false );
				$avatar				=	CBTxt::T( PMSHelper::getGlobalParams()->getString( 'messages_system_avatar', '' ) );

				if ( $avatar ) {
					if ( $avatar[0] == '/' ) {
						$avatar		=	$_CB_framework->getCfg( 'live_site' ) . $avatar;
					}

					switch ( PMSHelper::getGlobalParams()->getString( 'messages_system_avatar_style', 'roundedbordered' ) ) {
						case 'rounded':
							$style	=	' rounded';
							break;
						case 'roundedbordered':
							$style	=	' img-thumbnail';
							break;
						case 'circle':
							$style	=	' rounded-circle';
							break;
						case 'circlebordered':
							$style	=	' img-thumbnail rounded-circle';
							break;
						default:
							$style	=	null;
							break;
					}

					$avatar			=	'<img src="' . htmlspecialchars( $avatar ) . '" class="cbImgPict cbThumbPict' . htmlspecialchars( $style ) . '" />';
				} else {
					$avatar			=	PMSHelper::getAnonAvatar( $name );
				}

				$link				=	$cbUser->replaceUserVars( PMSHelper::getGlobalParams()->getString( 'messages_system_url', '' ), false, false, null, false );
				$profile			=	$name;

				if ( $link ) {
					$avatar			=	'<a href="' . htmlspecialchars( $link ) . '">' . $avatar . '</a>';
					$profile		=	'<a href="' . htmlspecialchars( $link ) . '">' . $profile . '</a>';
				}
			} elseif ( ! $this->getInt( 'from_user', 0 ) ) {
				$email				=	$this->getString( 'from_email', '' );

				if ( ! cbIsValidEmail( $email ) ) {
					$email			=	null;
				}

				$name				=	htmlspecialchars( $this->getString( 'from_name', '' ) );

				if ( ! $name ) {
					$name			=	CBTxt::T( 'Guest' );
				}

				$avatar				=	PMSHelper::getAnonAvatar( $name );
				$profile			=	$name;

				if ( $email ) {
					$avatar			=	'<a href="mailto:' . htmlspecialchars( $email ) . '">' . $avatar . '</a>';
					$profile		=	'<a href="mailto:' . htmlspecialchars( $email ) . '">' . $profile . '</a>';
				}
			} else {
				$cbUser				=	\CBuser::getInstance( $this->getInt( 'from_user', 0 ), false );
				$avatar				=	$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true );

				if ( ! $cbUser->getUserData()->getInt( 'id', 0 ) ) {
					$name			=	CBTxt::T( 'Deleted' );
					$avatar			=	PMSHelper::getAnonAvatar( $name );
					$profile		=	$name;
				} else {
					$name			=	$cbUser->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );
					$profile		=	$cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) );
					$profileDirect	=	'<a href="' . $_CB_framework->viewUrl( 'userprofile', true, array( 'user' => $this->getInt( 'from_user', 0 ) ) ) . '">' . $name . '</a>';
					$status			=	$cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) );
				}
			}

			$cache[$userId][$id]	=	array( 'avatar' => $avatar, 'name' => $name, 'profile' => $profile, 'profile_direct' => ( ! $profileDirect ? $profile : $profileDirect ), 'status' => $status );
		}

		if ( in_array( $field, array( 'avatar', 'name', 'profile', 'profile_direct', 'status' ) ) ) {
			return $cache[$userId][$id][$field];
		}

		return $cache[$userId][$id];
	}

	/**
	 * @param string $field
	 * @return string|array
	 */
	public function getTo( $field = 'name' )
	{
		global $_CB_framework;

		static $cache				=	array();

		$userId						=	Application::MyUser()->getUserId();
		$id							=	$this->getInt( 'id', 0 );

		if ( ! isset( $cache[$userId][$id] ) ) {
			$profileDirect			=	null;
			$status					=	null;

			if ( ! $this->getInt( 'to_user', 0 ) ) {
				$avatar				=	\CBuser::getInstance( 0, false )->getField( 'avatar', null, 'html', 'none', 'list', 0, true, array( '_allowProfileLink' => false ) );
				$name				=	CBTxt::T( 'All Users' );
				$profile			=	$name;
			} else {
				$cbUser				=	\CBuser::getInstance( $this->getInt( 'to_user', 0 ), false );
				$avatar				=	$cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true );

				if ( ! $cbUser->getUserData()->getInt( 'id', 0 ) ) {
					$name			=	CBTxt::T( 'Deleted' );
					$profile		=	$name;
				} else {
					$name			=	$cbUser->getField( 'formatname', null, 'html', 'none', 'profile', 0, true );
					$profile		=	$cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) );
					$profileDirect	=	'<a href="' . $_CB_framework->viewUrl( 'userprofile', true, array( 'user' => $this->getInt( 'to_user', 0 ) ) ) . '">' . $name . '</a>';
					$status			=	$cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) );
				}
			}

			$cache[$userId][$id]	=	array( 'avatar' => $avatar, 'name' => $name, 'profile' => $profile, 'profile_direct' => ( ! $profileDirect ? $profile : $profileDirect ), 'status' => $status );
		}

		if ( in_array( $field, array( 'avatar', 'name', 'profile', 'profile_direct', 'status' ) ) ) {
			return $cache[$userId][$id][$field];
		}

		return $cache[$userId][$id];
	}

	/**
	 * @param int $length
	 * @return string
	 */
	public function getMessage( $length = 0 )
	{
		$editor				=	PMSHelper::getGlobalParams()->getInt( 'messages_editor', 2 );

		if ( ( $editor == 3 ) && ( ! Application::User( $this->getInt( 'from_user', 0 ) )->isGlobalModerator() ) ) {
			$editor			=	1;
		}

		if ( ( $editor >= 2 ) || $this->getBool( 'from_system', false ) ) {
			$message		=	$this->getHtml( 'message', '' );
		} else {
			$message		=	htmlspecialchars( $this->getString( 'message', '' ) );
		}

		// BBCode:
		$bbCode				=	PMSHelper::getGlobalParams()->getInt( 'messages_bbcode', 1 );

		if ( ( $bbCode == 1 ) || ( ( $bbCode == 2 ) && $this->getBool( 'from_system', false ) ) ) {
			$message		=	PMSHelper::bbcodeToHTML( $message );
		}

		// Remove duplicate spaces, tabs, and linebreaks:
		$message			=	PMSHelper::removeDuplicateSpacing( $message );

		if ( $length ) {
			// We just want a snippet so remove any html:
			$message		=	Get::clean( $message, GetterInterface::STRING );

			if ( $editor >= 2 ) {
				// And escape what's left encase we allowed HTML in the message:
				$message	=	htmlspecialchars( $message );
			}

			if ( cbutf8_strlen( $message ) > $length ) {
				$message	=	cbutf8_substr( $message, 0, $length ) . '...';
			}
		} elseif ( $editor === 1 ) {
			// Linebreaks:
			$message		=	str_replace( array( "\r\n", "\r", "\n" ), '<br />', $message );
		}

		return $message;
	}

	/**
	 * Returns if message was read by a specific user or anyone
	 *
	 * @param int $userId
	 * @return string
	 */
	public function getRead( $userId = 0 )
	{
		global $_CB_database;

		if ( $userId && ( $userId == $this->getInt( 'from_user', 0 ) ) ) {
			return true;
		}

		$readDate					=	$this->getString( '_read', '' );

		if ( $readDate !== null ) {
			// We know for sure from existing query that the message was read or not so use that instead of querying any further:
			return $readDate;
		}

		static $cache				=	array();

		$id							=	$this->getInt( 'id', 0 );

		if ( ! isset( $cache[$id][$userId] ) ) {
			$query					=	"SELECT " . $_CB_database->NameQuote( 'date' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_messages_read' );
			if ( $userId ) {
				$query				.=	"\n WHERE " . $_CB_database->NameQuote( 'to_user' ) . " = " . (int) $userId
									.	"\n AND " . $_CB_database->NameQuote( 'message' ) . " = " . $id;
			} else {
				$query				.=	"\n WHERE " . $_CB_database->NameQuote( 'message' ) . " = " . $id;
			}
			$_CB_database->setQuery( $query );
			$readDate				=	$_CB_database->loadResult();

			$cache[$id][$userId]	=	( $readDate ? $readDate : '' );
		}

		return $cache[$id][$userId];
	}

	/**
	 * Sets the read state for this message for the supplied user id
	 *
	 * @param int $userId
	 * @param int $state
	 * @return bool
	 */
	public function setRead( $userId = 0, $state = 1 )
	{
		if ( ! $userId ) {
			return false;
		}

		$read	=	new ReadTable();

		$read->load( array( 'to_user' => (int) $userId, 'message' => $this->getInt( 'id', 0 ) ) );

		if ( $state ) {
			if ( ! $read->getInt( 'id', 0 ) ) {
				$read->set( 'to_user', (int) $userId );
				$read->set( 'message', $this->getInt( 'id', 0 ) );

				$read->store();
			}

			$this->set( '_read', 1 );
		} else {
			if ( $read->getInt( 'id', 0 ) ) {
				$read->delete();
			}

			$this->set( '_read', 0 );
		}

		return true;
	}

	/**
	 * Returns the message this message is replying to
	 *
	 * @return bool|MessageTable
	 */
	public function getReplyTo()
	{
		static $cache				=	array();

		$reply						=	$this->getInt( 'reply_to', 0 );

		if ( ! $reply ) {
			return false;
		}

		if ( ! isset( $cache[$reply] ) ) {
			$row					=	new MessageTable();

			$row->load( $reply );

			if ( ! $row->getInt( 'id', 0 ) ) {
				$cache[$reply]		=	false;
			} else {
				$cache[$reply]		=	$row;
			}
		}

		return $cache[$reply];
	}

	/**
	 * Logs Joomla user action entry for accessing a users private message
	 */
	public function logAccessed()
	{
		if ( $this->getBool( 'from_system', false ) ) {
			return;
		}

		$from	=	$this->getInt( 'to_user', 0 );
		$userId	=	Application::MyUser()->getUserId();

		if ( ( ! $from ) || ( $from === $userId ) ) {
			return;
		}

		$to		=	$this->getInt( 'from_user', 0 );

		if ( ( ! $to ) || ( $to === $userId ) ) {
			return;
		}

		Application::Cms()->logUserAction(
			CBTxt::T( 'User {username} read private message {id} from user {from_username} to user {to_username}' ),
			array(	'action'		=>	'message-read',
					'username'		=>	CBuser::getMyUserDataInstance()->getString( 'username' ),
					'user_id'		=>	$userId,
					'from_username'	=>	CBuser::getUserDataInstance( $from )->getString( 'username' ),
					'to_username'	=>	CBuser::getUserDataInstance( $to )->getString( 'username' ),
					'from_user'		=>	$from,
					'to_user'		=>	$to,
					'id'			=>	$this->getInt( 'id', 0 )
			),
			'plugin.pms',
			$userId
		);
	}

	/**
	 * @param null|UserTable $user
	 * @return bool
	 */
	public function isDeleted( $user = null )
	{
		if ( $user === null ) {
			$user	=	CBuser::getMyUserDataInstance();
		}

		if ( ( $user->getInt( 'id', 0 ) === $this->getInt( 'from_user', 0 ) ) && ( $user->getInt( 'id', 0 ) === $this->getInt( 'to_user', 0 ) ) ) {
			// User is the sender and the recipient so ignore the deleted flags and let the subsequent delete trigger the full removal of the message
			return false;
		}

		if ( ( $user->getInt( 'id', 0 ) === $this->getInt( 'from_user', 0 ) ) && $this->getInt( 'from_user_delete', 0 ) ) {
			// User is the sender and has flagged this message for deletion
			return true;
		}

		if ( ( $user->getInt( 'id', 0 ) === $this->getInt( 'to_user', 0 ) ) && $this->getInt( 'to_user_delete', 0 ) ) {
			// User is the recipient and has flagged this message for deletion
			return true;
		}

		return false;
	}
}