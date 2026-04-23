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
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class MassMessageTable extends MessageTable
{

	/**
	 * Copy the named array or object content into this object as vars
	 * only existing vars of object are filled.
	 * When undefined in array, object variables are kept.
	 *
	 * WARNING: DOES addslashes / escape BY DEFAULT
	 *
	 * Can be overridden or overloaded.
	 *
	 * @param  array|object  $array         The input array or object
	 * @param  string        $ignore        Fields to ignore
	 * @param  string        $prefix        Prefix for the array keys
	 * @return boolean                      TRUE: ok, FALSE: error on array binding
	 */
	public function bind( $array, $ignore = '', $prefix = null )
	{
		$bind				=	parent::bind( $array, $ignore, $prefix );

		// Bind the selected users ids as the recipients:
		$input				=	Application::Input()->subTree( 'usersbrowser' );
		$users				=	array();

		foreach ( $input->subTree( 'idcid' ) as $id ) {
			if ( $id ) {
				$users[]	=	(int) $id;
			}
		}

		if ( $users ) {
			$this->set( 'to_user', implode( ',', $users ) );
		}

		return $bind;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		$recipients		=	cbToArrayOfInt( explode( ',', $this->getString( 'to_user', '' ) ) );

		foreach ( $recipients as $recipient ) {
			$message	=	new parent();

			$message->bind( $this );

			$message->set( 'to_user', $recipient );

			$message->store();
		}

		cbRedirect( 'index.php?option=com_comprofiler&view=showusers', CBTxt::T( 'Private messages sent successfully!' ) );

		return true;
	}
}