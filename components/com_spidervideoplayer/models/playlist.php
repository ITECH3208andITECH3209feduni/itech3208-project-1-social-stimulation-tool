<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );



jimport( 'joomla.application.component.model' );



class spidervideoplayerModelplaylist extends JModelLegacy

	{


		function playlist()

		{
				$db =& JFactory::getDBO();
				$option=JRequest::getVar('option');
				$show_trackid=JRequest::getVar('show_trackid');
				//$playlist_id=JRequest::getVar('playlist_id');
	
				$playlists=array();
				$playlists_id=array();
				//exit;
				
				
				$playlists_id=explode(',',JRequest::getVar('playlist'));
				

				$playlists_id= array_slice($playlists_id,0, count($playlists_id)-1);   
				foreach($playlists_id as $playlist_id)
				{
					$query ="SELECT * FROM #__spidervideoplayer_playlist WHERE published='1' AND id=".(int)$db->escape($playlist_id) ;
					$db->setQuery($query); 
					$playlists[] = $db->loadObject();
					if ($db->getErrorNum())
					{
						echo $db->stderr();
						return false;
					}	
				}
	
				$viedos= array();
				foreach($playlists as $playlist)
				{
					if($playlist)
					{
						$viedos_temp=array();
						$videos_id=explode(',',$playlist->videos);
						$videos_id= array_slice($videos_id,0, count($videos_id)-1);   
						foreach($videos_id as $video_id)
						{
							$query ="SELECT * FROM #__spidervideoplayer_video WHERE id =".(int)$db->escape($video_id) ;
							$db->setQuery($query); 
							$viedos_temp[] = $db->loadObject();
						}
			
						$viedos[$playlist->id] = $viedos_temp;
					}
				}
				return array($playlists,$viedos,$show_trackid);

		}
	}