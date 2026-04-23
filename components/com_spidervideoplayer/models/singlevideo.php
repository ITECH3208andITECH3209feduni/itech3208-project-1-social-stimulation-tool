<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );



jimport( 'joomla.application.component.model' );



class spidervideoplayerModelsinglevideo extends JModelLegacy

	{


		function singlevideo()

		{
			$app = JFactory::getApplication('site');
			
			$params = $app->getParams('com_spidervideoplayer');
			$typeselect	=	JRequest::getVar('typeselect',$params->get( 'typeselect' ));
			$video	=	JRequest::getVar('trackID',$params->get( 'video' ));
			
			$row 	=& JTable::getInstance('spidervideoplayer_video', 'Table');
			// load the row from the db table
			$row->load( $video);
	
				return array($row);

		}
	}