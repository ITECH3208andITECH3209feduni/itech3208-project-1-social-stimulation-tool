<?php
 /**
 * @package Spider Video Player Lite
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( JApplicationHelper::getPath( 'toolbar_html' ) );
switch ( $task )
{
	case 'add_video'  :
	case 'edit_video'  :	
		TOOLBAR_spidervideoplayer_video::_NEW();
		break;
		
	case 'add_playlist'  :
	case 'edit_playlist'  :	
		TOOLBAR_spidervideoplayer_playlist::_NEW();
		break;

	case 'add_theme'  :
	case 'edit_theme'  :	
		TOOLBAR_spidervideoplayer_theme::_NEW();
		break;
	case 'add_tag'  :
	case 'edit_tag'  :	
		TOOLBAR_spidervideoplayer_tag::_NEW();
		break;

	case 'video'  :	
		TOOLBAR_spidervideoplayer_video::_DEFAULT();
		break;

	case 'playlist'  :	
		TOOLBAR_spidervideoplayer_playlist::_DEFAULT();
		break;

	case 'theme'  :	
		TOOLBAR_spidervideoplayer_theme::_DEFAULT();
		break;

	case 'tag'  :	
		TOOLBAR_spidervideoplayer_tag::_DEFAULT();
		break;
		
	default:
		TOOLBAR_spidervideoplayer_playlist::_DEFAULT();
		break;
}
?>