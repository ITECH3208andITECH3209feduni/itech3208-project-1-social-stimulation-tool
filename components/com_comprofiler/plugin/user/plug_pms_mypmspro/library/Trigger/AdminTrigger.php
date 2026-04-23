<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\PMS\Trigger;

use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class AdminTrigger extends \cbPluginHandler
{

	/**
	 * Displays backend menu items
	 *
	 * @param array $menu
	 * @param bool  $disabled
	 */
	public function adminMenu( &$menu, $disabled )
	{
		global $_CB_framework;

		if ( ! $this->params->getBool( 'general_menu', true ) ) {
			return;
		}

		$prevStateBase			=	'option=com_comprofiler&view=editPlugin&pluginid=' . $this->getPluginId();

		$items					=	array();

		$items['component']		=	array(	'title' => CBTxt::T( 'Private Messages' ) );
		$items['menu']			=	array(	array(	'title' => CBTxt::T( 'Messages' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'action' => 'showprivatemessages', 'cid' => $this->getPluginId() ) ), 'icon' => 'pms-messages',
													'submenu' => array( array( 'title' => CBTxt::Th( 'Add New Message' ), 'link' => $_CB_framework->backendViewUrl( 'editPlugin', true, array( 'cid' => $this->getPluginId(), 'table' => 'privatemessagesbrowser', 'action' => 'editrow', 'cbprevstate' => base64_encode( $prevStateBase . '&action=showprivatemessages' ) ) ), 'icon' => 'cb-new' ) )
												),
												array(	'title' => CBTxt::T( 'Configuration' ), 'link' => $_CB_framework->backendViewUrl( 'editrow', true, array( 'table' => 'pluginsbrowser', 'action' => 'editrow', 'cid' => $this->getPluginId(), 'cbprevstate' => base64_encode( 'option=com_comprofiler&view=showPlugins' ) ) ), 'icon' => 'pms-config' )
											);

		$menu['pms']			=	$items;
	}
}