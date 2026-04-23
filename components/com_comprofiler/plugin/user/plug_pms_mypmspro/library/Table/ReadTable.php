<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\PMS\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class ReadTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $to_user			=	null;
	/** @var int  */
	public $message			=	null;
	/** @var string  */
	public $date			=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_messages_read';

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
		if ( $this->getString( 'to_user', '' ) == '' ) {
			$this->setError( CBTxt::T( 'User not specified!' ) );

			return false;
		} elseif ( $this->getString( 'message', '' ) == '' ) {
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
		global $_PLUGINS;

		$new				=	( $this->getInt( 'id', 0 ) ? false : true );
		$old				=	new self();

		$this->set( 'date', $this->getString( 'date', Application::Database()->getUtcDateTime() ) );

		if ( ! $new ) {
			$old->load( array( 'to_user' => $this->getInt( 'to_user', 0 ), 'message' => $this->getInt( 'message', 0 ) ) );

			$integrations	=	$_PLUGINS->trigger( 'pm_onBeforeUpdateMessageRead', array( &$this, $old ) );
		} else {
			$integrations	=	$_PLUGINS->trigger( 'pm_onBeforeCreateMessageRead', array( &$this ) );
		}

		if ( in_array( false, $integrations, true ) ) {
			return false;
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'pm_onAfterUpdateMessageRead', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'pm_onAfterCreateMessageRead', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$integrations	=	$_PLUGINS->trigger( 'pm_onBeforeDeleteMessageRead', array( &$this ) );

		if ( in_array( false, $integrations, true ) ) {
			return false;
		}

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'pm_onAfterDeleteMessageRead', array( $this ) );

		return true;
	}

	/**
	 * @return MessageTable
	 */
	public function getMessage()
	{
		$id				=	$this->getInt( 'message', 0 );

		if ( ! $id ) {
			return new MessageTable();
		}

		static $cache	=	array();

		if ( ! isset( $cache[$id] ) ) {
			$message	=	new MessageTable();

			$message->load( $id );

			$cache[$id]	=	$message;
		}

		return $cache[$id];
	}
}