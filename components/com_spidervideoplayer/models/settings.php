<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );



jimport( 'joomla.application.component.model' );




class spidervideoplayerModelsettings extends JModelLegacy

	{

	
	function getParams()
		{
			$db			=& JFactory::getDBO();
			$option	=JRequest::getVar('option');
			$id		=JRequest::getVar('theme');
		
			$query = 'SELECT id FROM #__spidervideoplayer_theme WHERE `default`=1 LIMIT 1';
			$db->setQuery( $query );
			$def = $db->loadResult();
			if($db->getErrorNum()){
				echo $db->stderr();
				return false;
			}
			
			$query = 'SELECT id FROM #__spidervideoplayer_theme WHERE id='.(int)$db->escape($id).' LIMIT 1';
			$db->setQuery( $query );
			$is = $db->loadResult();
			if($db->getErrorNum()){
				echo $db->stderr();
				return false;
			}
			
			if(!$is)
				$id=$def;
		
			$row 	=& JTable::getInstance('spidervideoplayer_theme', 'Table');
			$row->load( $id);
			return array($row);
		}
		

	

	}