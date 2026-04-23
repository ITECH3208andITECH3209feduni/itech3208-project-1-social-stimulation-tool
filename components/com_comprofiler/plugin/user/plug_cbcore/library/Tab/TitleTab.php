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

class TitleTab extends cbTabHandler
{
	/**
	 * Generates the HTML to display the user profile tab
	 *
	 * @param  TabTable   $tab       the tab database entry
	 * @param  UserTable  $user      the user being displayed
	 * @param  int        $ui        1 for front-end, 2 for back-end
	 * @return mixed                 Either string HTML for tab content, or false if ErrorMSG generated
	 */
	public function getDisplayTab( $tab,$user,$ui )
	{
		$params	=	$this->params;
		$title	=	cbReplaceVars( $params->get( 'title', '_UE_PROFILE_TITLE_TEXT' ), $user );
		$name	=	$user->getFormattedName();

		$return	=	( sprintf( $title, $name ) ? '<div class="mb-3 border-bottom cb-page-header cbProfileTitle"><h3 class="m-0 p-0 mb-2 cb-page-header-title">' . sprintf( $title, $name ) . '</h3></div>' : null )
				.	$this->_writeTabDescription( $tab, $user );

		return $return;
	}
}