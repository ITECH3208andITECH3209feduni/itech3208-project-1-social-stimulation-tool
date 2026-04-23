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

$video=$this->row;
if(strpos($video->url, "http:")===false and strpos($video->url, "https:")===false )
	$track_URL=JURI::root( true ).'/administrator/'.htmlspecialchars($video->url);
else
	$track_URL=$video->url;


echo '<library>';
	echo '<albumFree title="Single Video" thumb="" id="0">';
	
	echo '<track id="'.$video->id.'" type="'.$video->type.'" url="'.htmlspecialchars($track_URL).'" thumb="'.JURI::root( true ).'/administrator/'.htmlspecialchars($video->thumb).'">'.$video->title.'</track>';
	
	echo '</albumFree>';
echo '</library>';

?>

