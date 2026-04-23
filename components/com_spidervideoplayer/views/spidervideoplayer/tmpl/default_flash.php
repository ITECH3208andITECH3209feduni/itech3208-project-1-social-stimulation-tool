<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

   
defined('_JEXEC') or die('Restricted access');

$params=$this->params;
$theme=$this->theme;
$playlist=$this->playlist;
$typeselect=$this->typeselect;
$track=$this->track;

if($params->get( 'appWidth' )!="")
	$width=$params->get( 'appWidth' );
else
	$width='640';

if($params->get( 'appHeight' )!="")
	$height=$params->get( 'appHeight' );
else
	$height='480';
	
$show_trackid=	$params->get( 'show_trackid' );
$Itemid = JRequest::getVar('Itemid', 0, 'get', 'int');
$u =& JURI::getInstance();

if($typeselect==0)
	$url=str_replace("&","@",  str_replace("&amp;","@",JRoute::_("index.php?option=com_spidervideoplayer&view=settings")."&format=row&typeselect=".$typeselect."&playlist=".$playlist."&theme=".$theme)).'&playlistUrl='.str_replace("&","@",str_replace("&amp;","@",JRoute::_("index.php?option=com_spidervideoplayer&view=playlist")."&format=row&playlist=".$playlist."&show_trackid=".$show_trackid)).'&defaultAlbumId='.JRequest::getVar('AlbumId').'&defaultTrackId='.JRequest::getVar('TrackId');
else
	$url=str_replace("&","@",  str_replace("&amp;","@",JRoute::_("index.php?option=com_spidervideoplayer&view=settings")."&format=row&typeselect=".$typeselect."&video=".$track."&theme=".$theme)).'&playlistUrl='.str_replace("&","@",str_replace("&amp;","@",JRoute::_("index.php?option=com_spidervideoplayer&view=singlevideo")."&format=row&trackID=".$track."&show_trackid=".$show_trackid)).'&defaultAlbumId='.JRequest::getVar('AlbumId').'&defaultTrackId='.JRequest::getVar('TrackId');?>

<script type="text/javascript" src="components/com_spidervideoplayer/js/swfobject.js"></script>

<div id="flashcontent"  style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px"></div>

<script type="text/javascript">
function flashShare(type,b,c)	
{
	u=location.href;
	if(u=='<?php echo JURI::root(); ?>index.php?' || u=='<?php echo JURI::root(); ?>' )
		u='<?php echo JURI::root(); ?>index.php?';
	else
		if(!location.search)
			u=u+'?';
		else
			u=u+'&';
		
	t=document.title;
	
	switch (type)
	{
		case 'fb':	
			window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u+'AlbumId='+b+'&TrackId='+c)+'&t='+encodeURIComponent(t), "Facebook","menubar=1,resizable=1,width=350,height=250");
			break
		case 'g':
			window.open('http://plus.google.com/share?url='+encodeURIComponent(u+'AlbumId='+b+'&TrackId='+c)+'&t='+encodeURIComponent(t), "Google","menubar=1,resizable=1,width=350,height=250");
			break
			
		case 'tw':
			window.open('http://twitter.com/home/?status='+encodeURIComponent(u+'&AlbumId='+b+'&TrackId='+c), "Twitter","menubar=1,resizable=1,width=350,height=250");
			break
	}
}	
  
	var so = new SWFObject("<?php echo JURI::root();?>components/com_spidervideoplayer/videoPlayer.swf?wdrand=<?php echo mt_rand() ?>", "Player", "100%", "100%", "8", "#000000");
   
	so.addParam("FlashVars", "settingsUrl=<?php echo $url?>");
	so.addParam("quality", "high");
	so.addParam("menu", "false");
	so.addParam("wmode", "transparent");
	so.addParam("loop", "false");
	so.addParam("allowfullscreen", "true");
	so.write("flashcontent");
</script>