<?php
  /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );



jimport( 'joomla.application.component.model' );




class spidervideoplayerModelspidervideoplayer extends JModelLegacy

	{
	
	function getParams()
		{
			$option	=JRequest::getVar('option');
			$app = JFactory::getApplication('site');
			
			$params = $app->getParams('com_spidervideoplayer');
			
			$playlist	=	JRequest::getVar('playlist',$params->get( 'playlist' ));
			$theme		=	JRequest::getVar('theme',$params->get( 'theme' ));
			$priority    =   JRequest::getVar('priority',$params->get( 'priority' ));
			$typeselect	=	JRequest::getVar('typeselect',$params->get( 'typeselect' ));
			$track	=	JRequest::getVar('video',$params->get( 'video' ));
		
			$row 	=& JTable::getInstance('spidervideoplayer_theme', 'Table');
			// load the row from the db table
			$row->load( $theme);
			return array($row, $theme, $playlist,$priority,$typeselect,$track);
		}
			

	}