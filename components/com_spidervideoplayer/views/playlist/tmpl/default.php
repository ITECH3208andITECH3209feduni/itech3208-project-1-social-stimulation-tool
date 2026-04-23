<?php
/**
* @package Spider Video Player
* @author Web-Dorado
* @copyright (C) 2012 Web-Dorado. All rights reserved.
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate'); 
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
header ("Content-Type:text/xml"); 

$playlists=$this->playlists;
$videos=$this->videos;
$show_trackid=$this->show_trackid;


echo '<library>';
	foreach($playlists as $playlist) 
	{
		if($playlist)
		{
			if(strpos($playlist->thumb, "http:")===false and strpos($playlist->thumb, "https:")===false )
				echo '<albumFree title="'.htmlspecialchars($playlist->title).'" thumb="'.JURI::root( true ).'/administrator/'.htmlspecialchars($playlist->thumb).'" id="'.$playlist->id.'">';
			else
				echo '<albumFree title="'.htmlspecialchars($playlist->title).'" thumb="'.htmlspecialchars($playlist->thumb).'" id="'.$playlist->id.'">';
			
			$i=0;
			foreach($videos[$playlist->id] as $video)
			{
				$i++;
				echo '<track id="'.$video->id.'" type="'.$video->type.'"';
					if($video->type=="rtmp")
						echo ' fmsUrl="'.htmlspecialchars($video->fmsUrl).'"';
					if($video->type=="http" )
					{
						if(strpos($video->url, "http:")===false and strpos($video->url, "https:")===false )
							echo ' url="'.JURI::root( true ).'/administrator/'.htmlspecialchars($video->url).'"';
						else
							echo ' url="'.htmlspecialchars($video->url).'"';
					}
					else
						echo ' url="'.htmlspecialchars($video->url).'"';
					
					if($video->type=="http"){
						if(strpos($video->urlHD, "http:")===false and strpos($video->urlHD, "https:")===false AND $video->urlHD!='' )
							echo ' urlHD="'.JURI::root( true ).'/administrator/'.htmlspecialchars($video->urlHD).'"';
						else 
							echo ' urlHD="'.htmlspecialchars($video->urlHD).'"';					
					}
					else
						if($video->type=="rtmp")
							echo ' urlHD="'.htmlspecialchars($video->urlHD).'"';
						
					echo ' thumb="';
					if($video->thumb)
						if(is_file('administrator/'.htmlspecialchars($video->thumb)))
							echo JURI::root( true ).'/administrator/'.htmlspecialchars($video->thumb);
					else
						echo htmlspecialchars($video->thumb);
					echo '"';
					
					if($show_trackid)
						echo '  trackId="'.$i.'"';
				echo ' >'.$video->title.'</track>';
			}
			echo '	</albumFree>';
		}
	}
echo '</library>';
?>