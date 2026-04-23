<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Core\Tab;

use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use cbTabHandler;

\defined( 'CBLIB' ) or die();

class AvatarTab extends cbTabHandler
{
	/**
	 * Generates the HTML to display the user profile tab
	 *
	 * @param  TabTable   $tab       the tab database entry
	 * @param  UserTable  $user      the user being displayed
	 * @param  int        $ui        1 for front-end, 2 for back-end
	 * @return mixed                 Either string HTML for tab content, or false if ErrorMSG generated
	 */
	public function getEditTab( $tab, $user, $ui )
	{
		return $this->_writeTabDescription( $tab, $user, null, 'edit' );
	}
}