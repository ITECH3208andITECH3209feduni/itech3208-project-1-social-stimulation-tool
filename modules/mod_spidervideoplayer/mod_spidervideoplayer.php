<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
	
defined( '_JEXEC' ) or die( 'Restricted access' );

$cmpnt_js_path = JURI::root(true).'/components/com_spidervideoplayer/js';
$cmpnt_img_path = JURI::root(true).'/components/com_spidervideoplayer/views/spidervideoplayer/images';

$document = JFactory::getDocument();
$document->addStyleSheet($cmpnt_js_path.'/jquery-ui.css');

$db =& JFactory::getDBO();
if ($db->getErrorNum()) {
	echo $db->stderr();
	return false;
}

$themeId = $params->get('theme');
$playlist = $params->get('playlist');
$priority = $params->get('priority');
$typeselect = $params->get('typeselect');
$track = $params->get('video');

/////////////////////
///// T H E M E /////
/////////////////////
$query = "SELECT * FROM #__spidervideoplayer_theme WHERE id=".(int)$themeId; 		 // appWidth, appHeight, show_trackid
$db->setQuery($query); 
$theme = $db->loadObject();

///////////////////////////
///// P L A Y L I S T /////
///////////////////////////
$query = "SELECT * FROM #__spidervideoplayer_playlist WHERE id=".(int)$playlist;   // videos,thumb,title
$db->setQuery($query); 
$play = $db->loadObject();
if(isset($play)){
	$playlist = $play->id.',';
	$mod_playlist_array = explode(',',$playlist);
}

///////////////////////
///// V I D E O S /////
///////////////////////
$query = "SELECT * FROM #__spidervideoplayer_video WHERE id IN ($track)";  // videos,thumb,title
$db->setQuery($query);
$mod_videos = $db->loadAssocList();		
$track_URL = ''; $track_poster = ''; 

if(isset($mod_videos[0]['type']))
	$mod_single_video_type = $mod_videos[0]['type'];
else 
	$mod_single_video_type = '';

if($typeselect==1){	
	$mod_playlist_array = '';
	$track = $mod_videos[0];
	$track = (object) $track;
	
	/*----- Video URL -----*/
	if($track->urlHtml5!='') { 
		if(strpos($track->urlHtml5, "http:")===false and strpos($track->urlHtml5, "https:")===false )
			$track_URL = JURI::root().'administrator/'.$track->urlHtml5;
		else
			$track_URL = $track->urlHtml5;
	}
	elseif (($track->urlHtml5 == "" || !strpos($track->url, 'embed')) && $track->type != "rtmp") {
		if ($track->type == "youtube") {
			$track_URL = "https://www.youtube.com/embed/" . substr($track->url, strpos($track->url, '?v=') + 3, 11) . "?enablejsapi=1&html5=1&controls=0&modestbranding=1&rel=0";
		}
		else $track_URL = JURI::root().'administrator/'.$track->url;
	}
	else $track_URL = $track->urlHtml5;
	
	/*----- Video Thumbnail -----*/
	if(strpos($track->thumb, "http:")===false and strpos($track->thumb, "https:")===false )
		$track_poster = JURI::root().'administrator/'.$track->thumb;
	else
		$track_poster = $track->thumb;
}

///////////////////////
///// A L B O M S /////
///////////////////////
$libRows = $theme->libRows;		$libCols = $theme->libCols;
$cellWidth = 100/$libCols.'%';  $cellHeight = 100/$libRows.'%';

$k = $libRows*$libCols;
$p = JRequest::getVar('play',0);
$display = 'style="width:100%;height:100%;border-collapse: collapse;"';
$table_count = 1;
$itemBgHoverColor ='#'.$theme->itemBgHoverColor;
$mod_vds =& JTable::getInstance('__spidervideoplayer_video', 'Table');
$ctrlsStack = $theme->ctrlsStack;

if($theme->ctrlsPos==2) {
	$ctrl_top = $theme->appHeight-41 .'px';
	$ctrl_top2 = $theme->appHeight-33 .'px';
}
else
	$ctrl_top = '-0px';

$AlbumId = JRequest::getVar('AlbumId');
$TrackId = JRequest::getVar('TrackId');  

if($typeselect==0)
	$startWithLib = $theme->startWithLib;
else
	$startWithLib = 0;


///////////////////////////////////
///////////// F L A S H ///////////
///////////////////////////////////
if($theme->appWidth!="")
	$width = $theme->appWidth;
else
	$width = '700';

if($theme->appHeight!="")
	$height = $theme->appHeight;
else
	$height = '400';

$show_trackid=	$theme->show_trackid;
$idd = rand(1, 10000);
$theme_flash = $theme->id;

if($typeselect==0) {
	$url = str_replace("&","@",  str_replace("&amp;","@",JRoute::_("index.php?option=com_spidervideoplayer&view=settings")."&format=row&typeselect=".$typeselect."&playlist=".$playlist."&theme=".$theme_flash)).'&playlistUrl='.str_replace("&","@",str_replace("&amp;","@",JRoute::_("index.php?option=com_spidervideoplayer&view=playlist")."&format=row&playlist=".$playlist."&show_trackid=".$show_trackid)).'&defaultAlbumId='.JRequest::getVar('AlbumId').'&defaultTrackId='.JRequest::getVar('TrackId');
	$src = 'index.php?option=com_spidervideoplayer&view=spidervideoplayer&priority=html5&typeselect='.$typeselect.'&theme='.$theme_flash.'&playlist='.$playlist.'&tmpl=component';
}
else {	
	$url = str_replace("&","@",  str_replace("&amp;","@",JRoute::_("index.php?option=com_spidervideoplayer&view=settings")."&format=row&typeselect=".$typeselect."&video=".$playlist."&theme=".$theme_flash)).'&playlistUrl='.str_replace("&","@",str_replace("&amp;","@",JRoute::_("index.php?option=com_spidervideoplayer&view=singlevideo")."&format=row&trackID=".$playlist."&show_trackid=".$show_trackid)).'&defaultAlbumId='.JRequest::getVar('AlbumId').'&defaultTrackId='.JRequest::getVar('TrackId');
	$src = 'index.php?option=com_spidervideoplayer&view=spidervideoplayer&priority=html5&typeselect='.$typeselect.'&theme='.$theme_flash.'&video='.$playlist.'&tmpl=component';
}	
?>

<style>
#main { padding: 0px; min-height: 0px; }
.mod_spvp_control {
	background-color: rgba(<?php echo HEXDEC(SUBSTR($theme->framesBgColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);	
	width:<?php echo $theme->appWidth; ?>px !important;
	top:<?php echo $ctrl_top ?>;
	<?php if($theme->ctrlsPos==1){ ?>
		padding-bottom: 6px;
	<?php } else { ?>
		padding-bottom: 10px;
	<?php }  ?>
	position:absolute;	
	height:30px;
	z-index:7;
}
.mod_spvp_control.youRtmp { 
	width: 47% !important; left: 225px; 
	top:<?php echo $ctrl_top2; ?>;
	padding: 0 10px;
}
.mod_spvp_control.youRtmp.singYouRtmp { width: 20% !important; left: 400px; }

.mod_spvp_control.youRtmp .btnPlay { display:none !important; }

#mod_control_btns { 
	opacity:<?php echo $theme->ctrlsMainAlpha/100; ?>;
	border:none; border-collapse: collapse;
	height:80%; width: 98% !important; 	
	margin: 0 auto; margin-top:4px;	
}

#mod_control_btns td, #mod_control_btns #space { 
	padding:0 !important; 
	text-align: center;
}

#mod_spvp_control .progressBar {
	z-index:5;
	cursor:pointer;
	position: relative;
	width: 100%; height:6px;
	border-top:1px solid rgba(<?php echo HEXDEC(SUBSTR($theme->slideColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
	border-bottom:1px solid rgba(<?php echo HEXDEC(SUBSTR($theme->slideColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
}
#mod_spvp_control .timeBar {
	z-index:5;
	top: 0; left: 0;
	position: absolute;
	width: 0; height: 100%;
	background-color: <?php echo '#'.$theme->slideColor; ?>;
}
#mod_spvp_control .bufferBar {
	opacity:0.3;
	top: 0; left: 0;
	position: absolute;
	width: 0; height: 100%;
	background-color: <?php echo '#'.$theme->slideColor; ?>;	
}
.mod_volumeBar {
	overflow: hidden;
	position: relative;	
	width: 0px; height:4px;
	background-color: rgba(<?php echo HEXDEC(SUBSTR($theme->framesBgColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
	border:1px solid rgba(<?php echo HEXDEC(SUBSTR($theme->slideColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
}
#mod_volumeTD .mod_volume {
	top: 0; left: 0;
	position: absolute;	
	width: 0; height: 100%;
	background-color: <?php echo '#'.$theme->slideColor; ?>;
}
#mod_spvp_playlist {
	height:<?php echo $theme->appHeight; ?>px; width:0px;
	<?php
	if ($theme->playlistPos==1)
		echo 'position:absolute;float:left;';
	else
		echo 'position:relative;float:right;';
	?> ;
	background-color: rgba(<?php echo HEXDEC(SUBSTR($theme->framesBgColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
	color:white;
	z-index:100;
}
#spvp_single_video_main {
	position: relative; 
	height: 100%;
}

#spvp_single_video_main #spvp_sing_video_thumb {
	position: absolute; 
	display: block; 
	top: 0; left: 0; 
	width: 100%; height: 100%; 
	z-index: 20; cursor: pointer;
}
#spvp_single_video_main .spvp_single_video_main {
	top: 0; left: 0; 
	position: absolute; 
	width: 100%; height: 100%;	
}

#spvp_single_video_main.player #spvp_sing_video_thumb,
#spvp_single_video_main.player:after { display: none; }
</style>

<script src="<?php echo $cmpnt_js_path ?>/jquery-1.7.2.js" type="text/javascript"></script>
<script src="<?php echo $cmpnt_js_path ?>/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo $cmpnt_js_path ?>/jquery.transit.js" type="text/javascript"></script>
<script src="<?php echo $cmpnt_js_path ?>/flash_detect.js"></script>

<div id="spidervideoplayerhtml5_module<?php echo  $module->id ?>" style="display:none;">

	<div id="mod_spvp_global_body" style="width:<?php echo $theme->appWidth; ?>px;height:<?php echo $theme->appHeight; ?>px; position:relative;">

		<!-- 1. Video_div -->
		<div id="mod_spvp_video_div" style="display:<?php if($startWithLib==1) echo 'none'; else echo 'block'?>; width:<?php echo $theme->appWidth; ?>px; height:<?php echo $theme->appHeight; ?>px; background-color:<?php echo "#".$theme->vidBgColor; ?>">
			
			<!-- 1.1. Play_list -->
			<div id="mod_spvp_playlist">
				<input type='hidden' value='0' id="track_list" />
				<div style="height:90%">
					<div onmousedown="scrolltp2=setInterval('scrollTop2()', 30)" onmouseup="clearInterval(scrolltp2)" ontouchstart="scrolltp2=setInterval('scrollTop2()', 30)" ontouchend="clearInterval(scrolltp2)" style="overflow:hidden; text-align:center;width:<?php echo $theme->playlistWidth; ?>px; height:20px">
						<img src="<?php echo $cmpnt_img_path; ?>/top.png" style="cursor:pointer;" id="button20" />
					</div>
					
					<div style="height:<?php echo $theme->appHeight-40; ?>px; overflow:hidden" >
						<?php
						for($i=0;$i<count($mod_playlist_array)-1;$i++) {
							$mod_v_ids = explode(',',$play->videos);
							$vi_ids = substr($play->videos,0,-1);
						
							if($i!=0)
								echo '<table border="1" id="mod_track_list_'.$i.'" style="display:none; height:100%; width:100%; border-spacing:0px; border:none; border-collapse:inherit;" >';
							else
								echo '<table border="1" id="mod_track_list_'.$i.'" style="display:block; height:100%; width:100%; border-spacing:0px; border:none; border-collapse:inherit;" >';

							echo '<tr>
								<td style="text-align:left;border:0px solid grey;width:100%;vertical-align:top;">
								<div id="mod_scroll_div2_'.$i.'" class="playlist_values" style="position:relative">';
								$jj = 0;
								
							for($j=0;$j<count($mod_v_ids)-1;$j++) {
								$query = "SELECT * FROM #__spidervideoplayer_video WHERE id IN ($mod_v_ids[$j])	";  // videos,thumb,title
								$db->setQuery($query);
								$mod_videos = $db->loadAssocList();
								$mod_vds = $mod_videos["0"];
								
								if($mod_vds["type"]=="http" || $mod_vds["type"]=="youtube" || $mod_vds["type"]=="rtmp") {
									/*-=-=- Video URL -=-=-*/
									if($mod_vds["urlHtml5"]!='') { 
										if(strpos($mod_vds["urlHtml5"], "http:")===false and strpos($mod_vds["urlHtml5"], "https:")===false )
											$html5Url = JURI::root().'administrator/'.$mod_vds["urlHtml5"];
										else
											$html5Url = $mod_vds["urlHtml5"];
									}
									elseif (($mod_vds["urlHtml5"] == "" || !strpos($mod_vds["url"], 'embed')) && $mod_vds["type"]!= "rtmp") {
										if ($mod_vds["type"] == "youtube") {
											$html5Url = "https://www.youtube.com/embed/" . substr($mod_vds["url"], strpos($mod_vds["url"], '?v=') + 3, 11) . "?enablejsapi=1&html5=1&controls=1&modestbranding=1&rel=0";
										}
										else $html5Url = JURI::root().'administrator/'.$mod_vds["url"];
									}
									else
										$html5Url = $mod_vds["urlHtml5"];
								
									/*-=-=- Thumbnail URL -=-=-*/
									if(strpos($mod_vds["thumb"], "http:")===false and strpos($mod_vds["thumb"], "https:")===false )
										$vidsTHUMB = JURI::root().'administrator/'.$mod_vds["thumb"];
									else
										$vidsTHUMB = $mod_vds["thumb"];
									
									/*-=-=- Video HD URL -=-=-*/
									if($mod_vds["urlHdHtml5"]!='') {
										if(strpos($mod_vds["urlHdHtml5"], "http:")===false and strpos($mod_vds["urlHdHtml5"], "https:")===false )
											$html5UrlHD = JURI::root().'administrator/'.$mod_vds["urlHdHtml5"];
										else
											$html5UrlHD = $mod_vds["urlHdHtml5"];
									}
									elseif (($mod_vds["urlHtml5"] == "" || !strpos($mod_vds["url"], 'embed')) && $mod_vds["type"]!= "rtmp") {
										if ($mod_vds["type"] == "youtube") {
											$html5UrlHD = "https://www.youtube.com/embed/" . substr($mod_vds["url"], strpos($mod_vds["url"], '?v=') + 3, 11) . "?enablejsapi=1&html5=1&controls=1&modestbranding=1&rel=0";
										}
										else $html5UrlHD = JURI::root().'administrator/'.$mod_vds["urlHD"];									
									}
									else
										$html5UrlHD = $mod_vds["urlHD"];
								
									echo '<div id="thumb_'.$jj.'"  onclick="jQuery(\'#mod_HD_on\').val(0);document.getElementById(\'mod_videoID\').src=\''.$html5Url.'\';document.getElementById(\'mod_videoID\').poster=\''.$vidsTHUMB.'\';mod_vid_select(this);mod_vid_num='.$jj.';jQuery(\'#_track\').val('.$jj.');" class="mod_vid_thumb" style="color:#'.$theme->textColor .';cursor:pointer;width:'.$theme->playlistWidth.'px;text-align:center"  >';
									if($mod_vds["thumb"])
										echo '<img src="'.$vidsTHUMB.'" width="90px" style="display:none;"  />';
									if($theme->show_trackid==1)
										echo '<p style="font-size:'.$theme->playlistTextSize.'px;line-height:30px;cursor:pointer;" >'.($jj+1).'-'.$mod_vds["title"].'</p>';
									else
										echo '<p style="font-size:'.$theme->playlistTextSize.'px;line-height:30px;cursor:pointer;" >'.$mod_vds["title"].'</p>';
									echo '</div>';
									echo '<input type="hidden" id="urlHD_'.$jj.'" value="'.$html5UrlHD.'" />';
									echo '<input type="hidden" id="vid_type_'.$jj.'" value="'.$mod_vds["type"].'" />';
									$jj=$jj+1;
								}
							}
							echo '</div></td></tr></table>'; 
						} ?> 
						<script></script>
					</div>
					
					<div onmousedown="scrollBot2=setInterval('scrollBottom2()', 30)" onmouseup="clearInterval(scrollBot2)" ontouchstart="scrollBot2=setInterval('scrollBottom2()', 30)" ontouchend="clearInterval(scrollBot2)" style="position:absolute;overflow:hidden; text-align:center;width:<?php echo $theme->playlistWidth; ?>px; height:20px" ><img src="<?php echo $cmpnt_img_path; ?>/bot.png" style="cursor:pointer;" id="button21" /></div>
				</div>
			</div>			
		
			<!-- 1.2. Videos -->
			<?php 
			//Playlist
			if($typeselect == 0) { 
				if($mod_vds["type"]=="youtube" || $mod_vds["type"]=="rtmp"){ ?>
					<div id="spvp_single_video_main">
						<iframe class="spvp_single_video_main" id="mod_videoID" type="text/html" width="<?php echo $theme->appWidth ?>" height="<?php echo $theme->appHeight; ?>" src="<?php echo $html5Url; ?>?enablejsapi=1&version=3&playerapiid=ytplayer&modestbranding=1&rel=0" frameborder="0" allowfullscreen></iframe>
						<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px; left:10px;" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
					</div>
				<?php }
				else { ?>
					<video poster="<?php echo $vidsTHUMB; ?>" src="<?php echo $html5Url; ?>" ontimeupdate="mod_timeUpdate()" ondurationchange="mod_durationChange();" id="mod_videoID" style="position:absolute" width="<?php echo $theme->appWidth; ?>" height="<?php echo $theme->appHeight; ?>" ><p>Your browser does not support the video tag.</p></video>
					<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px;" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
				<?php }
			}
			//Single
			else {
				if($mod_single_video_type=="youtube" || $mod_single_video_type=="rtmp"){ ?>
					<div id="spvp_single_video_main">
						<iframe class="spvp_single_video_main" id="mod_videoID" type="text/html" width="<?php echo $theme->appWidth ?>" height="<?php echo $theme->appHeight; ?>" src="<?php echo substr($track_URL,0,  strpos($track_URL, "?"));?>?enablejsapi=1&version=3&playerapiid=ytplayer&modestbranding=1&rel=0" frameborder="0" allowfullscreen></iframe> 
					</div>
					<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px;left:10px" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
				<?php }
				else {   ?>
					<video poster="<?php echo $track_poster ?>" src="<?php echo $track_URL ?>" ontimeupdate="mod_timeUpdate()"  ondurationchange="mod_durationChange();" id="mod_videoID" style="position:absolute" width="<?php echo $theme->appWidth; ?>" height="<?php echo $theme->appHeight; ?>" ><p>Your browser does not support the video tag.</p></video>
					<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px;" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
				<?php }
			} ?>	

			<!-- 1.3. Control Buttons -->
			<div class="mod_spvp_control" id="mod_spvp_control" style="display:none;overflow:hidden">
				<?php 
				if($theme->ctrlsPos==2){ 
					if($typeselect==0){
						if($mod_vds["type"]=="http"){ ?>
							<div class="progressBar">
								<div class="timeBar"></div>
								<div class="bufferBar"></div>
							</div>
						<?php
						} else echo "";
					}
					else {
						if($mod_single_video_type=="http"){ ?>
							<div class="progressBar">
								<div class="timeBar"></div>
								<div class="bufferBar"></div>
							</div>
						<?php
						} else echo "";
					}
				}
				$ctrls = explode(',',$ctrlsStack);
				$y = 1;
				echo '<table width="'.$theme->appWidth.'" id="mod_control_btns"><tr>';
				for($i=0;$i<count($ctrls);$i++) {
					$ctrl = explode(':',$ctrls[$i]);
					if($typeselect==1) {
						if($ctrl[0]=='playlist')
							$ctrl[1]=0;

						if($ctrl[0]=='lib')
							$ctrl[1]=0;
					}

					if($ctrl[1]==1) {
						echo '<td>';					
						if($ctrl[0]=='playPause') {
							echo '<img id="button'.$y.'" class="btnPlay" style="cursor:pointer; margin:0 auto; opacity:'.$theme->ctrlsMainAlpha/100 .'"   src="'.$cmpnt_img_path.'/play.png" />';							
							echo '<img id="button'.($y+1).'" width="16" class="btnPause" style="display:none; cursor:pointer; margin:0 auto; opacity:'.$theme->ctrlsMainAlpha/100 .'"  src="'.$cmpnt_img_path.'/pause.png" />';
							$y=$y+2;
						}
						else if($ctrl[0]=='+') {
							echo '<span id="space" style="padding-left:'.(($theme->appWidth*20)/100).'px"></span>';
						}
						else if($ctrl[0]=='time') {
							echo '<div id="timeBarNum"><span style="color:#'.$theme->ctrlsMainColor.';opacity:'.$theme->ctrlsMainAlpha/100 .'" id="mod_time">00:00 </span> <span style="color:#'.$theme->ctrlsMainColor.';opacity:'.$theme->ctrlsMainAlpha/100 .'">/</span> <span style="color:#'.$theme->ctrlsMainColor.';opacity:'.$theme->ctrlsMainAlpha/100 .'" id="mod_duration">00:00</span></div>';
						}
						else if($ctrl[0]=='vol') {
							echo '<table id="mod_volumeTD">
							<tr>
								<td id="voulume_img" >
									<img  style="cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"  id="button'.$y.'"  src="'.$cmpnt_img_path.'/vol.png"  />
								</td>
								<td id="volumeTD2" style="width:0px !important">
									<span id="volumebar_player" class="mod_volumeBar">
										<span class="mod_volume"></span>
									</span>
								</td>
							</tr>
							</table> ';
							$y = $y+1;
						}
						else if($ctrl[0]=='shuffle') {
							echo '<img  id="button'.$y.'" class="shuffle" style="cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"   src="'.$cmpnt_img_path.'/shuffle.png" />';
							echo '<img  id="button'.($y+1).'"  class="shuffle" style="display:none;cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"  src="'.$cmpnt_img_path.'/shuffleoff.png" />';
							$y = $y+2;
						}
						else if($ctrl[0]=='repeat') {
							echo '<img  id="button'.$y.'" class="mod_repeat" style="cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"   src="'.$cmpnt_img_path.'/repeat.png" />';
							echo '<img  id="button'.($y+1).'"  class="mod_repeat" style="display:none;cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"   src="'.$cmpnt_img_path.'/repeatOff.png" />';
							echo '<img  id="button'.($y+2).'"  class="mod_repeat" style="display:none;cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"  src="'.$cmpnt_img_path.'/repeatOne.png" />';
							$y = $y+3;
						}
						else {
							echo '<img  style="cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'" id="button'.$y.'" class="'.$ctrl[0].'"  src="'.$cmpnt_img_path.'/'.$ctrl[0].'.png" />';
							$y = $y+1;
						}
						echo '</td>';	
					}
				}
				echo '</tr></table>';
					
				if($theme->ctrlsPos==1){ ?>
					<div class="progressBar">
						<div class="timeBar"></div>
						<div class="bufferBar"></div>
					</div>
				<?php } ?>
			</div>
		</div>
		
		<!-- 2. Album_div -->
		<div id="mod_spvp_album_div" style="display:<?php if($startWithLib==0) echo 'none' ?>;background-color:<?php echo "#".$theme->appBgColor;?>;height:100%; overflow:hidden;position:relative;">
			<table width="100%" height="100%" style="border-collapse: inherit; border:none;">
				<tr id="tracklist_up" style="display:none">
					<td height="12px" colspan="2" style="text-align:right;border:none;">
						<div onmouseover="this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2))?>,0.4)'" onmouseout="this.style.background='none'" id="scroll" style="overflow:hidden;width:50%;height:100%;text-align:center;float:right;cursor:pointer;" onmousedown="scrolltp=setInterval('scrollTop()', 30)" onmouseup="clearInterval(scrolltp)" ontouchstart="scrolltp=setInterval('scrollTop()', 30)" ontouchend="clearInterval(scrolltp)">
						<img src="<?php echo $cmpnt_img_path; ?>/top.png" style="cursor:pointer;" id="button25" /></div>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle;border:none;">
						<img src="<?php echo $cmpnt_img_path; ?>/prev.png" style="cursor:pointer;" id="button28" onclick="mod_prevPage();" />
					</td>				
					<td style="width: 93%;border:none;">
						<?php
						for($l=0;$l<$table_count;$l++) {
							echo '<table id="mod_lib_table_'.$l.'" '.$display.'> ';								
							for($i=0; $i<$libRows; $i++) {
								echo '<tr>';									
								for($j=0;$j<$libCols;$j++) {
									if($p<count($mod_playlist_array)-1) {
										$mod_v_ids = explode(',',$play->videos);
										$vi_ids = substr($play->videos,0,-1);
										if(strpos($play->thumb, "http:")===false and strpos($play->thumb, "https:")===false )
											$playTHUMB = JURI::root().'administrator/'.$play->thumb;
										else
										$playTHUMB = $play->thumb;
					
										echo '<td class="playlist_td" id="mod_playlist_'.$p.'"  onclick="mod_openPlaylist('.$p.','.$l.')" onmouseover="this.style.color=\'#'.$theme->textHoverColor .'\';this.style.background=\'rgba('.HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)).',0.4)\'" onmouseout="this.style.color=\'#'.$theme->textColor .'\';this.style.background=\' none\'" onclick="" style="color:#'.$theme->textColor .';border:1px solid white;vertical-align:center; text-align:center;width:'.$cellWidth.';height:'.$cellHeight.';cursor:pointer">';
											if($play->thumb!='')
												echo '<img src="'.$playTHUMB.'" width="50%"  />';
											echo '<p style="font-size:'.$theme->libListTextSize.'px">'.$play->title.'</p>
										</td>';
										$p = $p+1;
									}
									else {
										echo '<td style="border:1px solid white;vertical-align:top; align:center;width:'.$cellWidth.';height:'.$cellHeight.'"></td>';
									}
								} 
								echo '</tr>';
							} 
							if($p<count($mod_playlist_array)-1) {
								$table_count = $table_count+1;
								$display = 'style="display:none;width:100%;height:100%;border-collapse: collapse;"';
							} 
							echo '</table>';
						}
						for($i=0;$i<$p;$i++) {
							$mod_v_ids = explode(',',$play->videos);
							$vi_ids = substr($play->videos,0,-1);
							if(strpos($play->thumb, "http:")===false and strpos($play->thumb, "https:")===false )
								$playTHUMB = JURI::root().'administrator/'.$play->thumb;
							else
								$playTHUMB = $play->thumb;

							echo '<table playlist_id="'.$i.'" id="mod_playlist_table_'.$i.'"  style="border:none;border-collapse: inherit;display:none;height:100%;width:100%" >
								<tr>
									<td style="text-align:center;vertical-align:top;border:none;">';
										if($play->thumb!='')
											echo '<img src="'.$playTHUMB.'" width="70%" /><br /><br />';
										echo '<p style="color:#'.$theme->textColor .';font-size:'.$theme->libDetailsTextSize.'px">'.$play->title.'</p>';
									echo '</td>
									<td style="width:50%;border:none;">
										<div style="width:100%;text-align:left;border:1px solid white;height:'.($theme->appHeight-55).'px;vertical-align:top;position:relative;overflow:hidden">
											<div id="mod_scroll_div_'.$i.'" style="position:relative;">';
											$jj=0;
											for($j=0;$j<count($mod_v_ids)-1;$j++) {
												$query = "SELECT * FROM #__spidervideoplayer_video WHERE id IN ($mod_v_ids[$j])	";  // videos,thumb,title
												$db->setQuery($query);
												$mod_videos = $db->loadAssocList();
												$mod_vds = $mod_videos["0"];
												
												if($mod_vds["type"]=="http" || $mod_vds["type"]=="youtube" || $mod_vds["type"]=="rtmp") {
													echo '<p class="vid_title" ondblclick="jQuery(\'#mod_spvp_album_div .show_vid\').click()" onmouseover="this.style.color=\'#'.$theme->textHoverColor .'\';this.style.background=\'rgba('.HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)).',0.4)\'" onmouseout="this.style.color=\'#'.$theme->textColor .'\';this.style.background=\' none\'" style="color:#'.$theme->textColor .';font-size:'.$theme->libDetailsTextSize.'px;line-height:30px;cursor:pointer" onclick="jQuery(\'#mod_HD_on\').val(0);jQuery(\'#mod_track_list_'.$i.'\').find(\'.mod_vid_thumb\')['.$jj.'].click();mod_playBTN();mod_current_playlist_videos();mod_vid_num='.$jj.';jQuery(\'#mod_current_track\').val('.$jj.');mod_vid_select2(this);mod_playlist_select('.$i.') ">'.($jj+1).' - '.$mod_vds["title"].'</p>';
													$jj=$jj+1;
												}
											}
											echo '</div>
										</div>
									</td>
								</tr>
							</table>';
						} ?>
					</td>
					
					<td style="vertical-align:bottom;border:none;">
						<table style='height:<?php echo $theme->appHeight-46 ?>px;border:none;border-collapse: inherit;'>
							<tr>
								<td height='100%' style="border:none;">
									<img src="<?php echo $cmpnt_img_path; ?>/next.png" style="cursor:pointer;" id="button27" onclick="mod_nextPage()" />
								</td>
							</tr>
							<tr>
								<td style="border:none;">
									<img src="<?php echo $cmpnt_img_path; ?>/back.png" style="cursor:pointer;display:none" id="button29" onclick="mod_openLibTable()" />
								</td>
							</tr>
							<tr>
								<td style="border:none;"> 
									<img style="cursor:pointer"  id="button19"  class="show_vid"  src="<?php echo $cmpnt_img_path; ?>/lib.png"  />
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr id="tracklist_down" style="display:none" >
					<td height="22px" colspan="2" style="text-align:right;border:none;">
						<div  onmouseover="this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2))?>,0.4)'" onmouseout="this.style.background='none'" id="scroll" style="overflow:hidden;width:50%;height:100%;text-align:center;float:right;cursor:pointer;" onmousedown="scrollBot=setInterval('mod_scrollBottom()', 30)" onmouseup="clearInterval(scrollBot)" ontouchstart="scrollBot=setInterval('mod_scrollBottom()', 30)" ontouchend="clearInterval(scrollBot)">
							<img src="<?php echo $cmpnt_img_path; ?>/bot.png" style="cursor:pointer;" id="button26"  />
						</div>
					</td>
				</tr>
			</table>
		</div>
		
		<!-- 3. Share_buttons -->
		<?php if($theme->ctrlsPos==1) $share_top= '-'.$theme->appHeight+22; else $share_top='-133' ?>
		<div id="mod_share_buttons" style="text-align:center; height:108px; width:30px; background-color:rgba(0,0,0,0.5); position:relative; z-index:200; top:<?php echo $share_top ?>px; display:none;" >
			<img onclick = "mod_flashShare('fb',document.getElementById('mod_current_playlist_table').value,document.getElementById('mod_current_track').value)" style="cursor:pointer"  src="<?php echo $cmpnt_img_path; ?>/facebook.png" /><br>
			<img onclick = "mod_flashShare('tw',document.getElementById('mod_current_playlist_table').value,document.getElementById('mod_current_track').value)" style="cursor:pointer" src="<?php echo $cmpnt_img_path; ?>/twitter.png" /><br>
			<img onclick = "mod_flashShare('g',document.getElementById('mod_current_playlist_table').value,document.getElementById('mod_current_track').value)" style="cursor:pointer" src="<?php echo $cmpnt_img_path; ?>/googleplus.png" /><br>
			<img onclick="jQuery('#embed_Url_div').css('display','');embed_url(document.getElementById('mod_current_playlist_table').value,document.getElementById('mod_current_track').value)" style="cursor:pointer" src="<?php echo $cmpnt_img_path; ?>/embed.png" />
		</div>
		
		<!-- 4. Embed_Url_div -->
		<div id="embed_Url_div" style="display:none;text-align:center;background-color:rgba(0,0,0,0.5); height:160px;width:300px;position:relative;left:<?php echo ($theme->appWidth/2)-150 ?>px;top:-<?php echo ($theme->appHeight/2)+75 ?>px">
			<textarea  onclick="jQuery('#embed_Url').focus(); jQuery('#embed_Url').select();"  id="embed_Url" readonly="readonly" style="font-size:11px;width:285px;overflow-y:scroll;resize:none;height:100px;position:relative;top:5px;"></textarea>
			<span style="position:relative;top:10px;"><button onclick="jQuery('#embed_Url_div').css('display','none')" style="border:0px">Close</button><p style="color:white">Press Ctrl+C to copy the embed code to clipboard</p></span>
		</div>		
	
	</div>
	<?php
	if($theme->defaultShuffle=='shuffleOff')
		$shuffle=0;
	else
		$shuffle=1;
	?>

	<input type="hidden" id="color" value="<?php echo "#".$theme->ctrlsMainColor ?>" />
	<input type="hidden" id="support" value="1" />
	<input type="hidden" id="event_type" value="mouseenter" />
	<input type="hidden" id="mod_current_track" value="0" />
	<input type="hidden" id="mod_shuffle" value="<?php echo $shuffle ?>" />
	<input type="hidden" id="mod_scroll_height" value="0" />
	<input type="hidden" id="mod_scroll_height2" value="0" />
	<input type="hidden" value="<?php if(isset($l)) echo $l; ?>" id="mod_lib_table_count"/>
	<input type="hidden" value="" id="mod_current_lib_table"/>
	<input type="hidden" value="0" id="mod_current_playlist_table"/>
	<input type="hidden" value="<?php echo $theme->defaultRepeat ?>" id="mod_repeat"/>
	<input type="hidden" value="0" id="mod_HD_on"/>
	<input type="hidden" value="" id="mod_volumeBar_width"/>

	<script>
	var modVideo = document.getElementById('mod_videoID'); //or
	var modVideo = jQuery('#mod_videoID').get(0); //or
	var modVideo = jQuery('#mod_videoID')[0];

	/*-=-=- return a jQuery object -=-=-*/
	var modVideo = jQuery('#mod_videoID');
	var paly_mod = jQuery('#mod_control_btns .btnPlay');
	var pause_mod = jQuery('#mod_control_btns .btnPause');
	
	<?php 
	if($typeselect == 0){
		if($mod_vds["type"]=="youtube" || $mod_vds["type"]=="rtmp" ){ ?> 
			jQuery("#mod_control_btns .btnPlay, #mod_control_btns .btnPause, #load_volumeTD, #mod_control_btns #timeBarNum, #mod_control_btns .fullScreen, #mod_control_btns .hd").css('display',"none");
			jQuery("#mod_spvp_control").addClass('youRtmp');
		<?php }
		else { ?>
			jQuery("#mod_spvp_control").removeClass('youRtmp');
		<?php }
	}
	if($typeselect == 1){ 
		if($mod_single_video_type=="youtube" || $mod_single_video_type=="rtmp"){ ?>
			jQuery("#mod_control_btns .playPrev, #mod_control_btns .playNext").css('display',"none");
			jQuery("#mod_spvp_control").addClass('singYouRtmp');
			jQuery("#mod_control_btns .btnPlay, #mod_control_btns .btnPause, #load_volumeTD, #mod_control_btns #timeBarNum, #mod_control_btns .fullScreen, #mod_control_btns .hd").css('display',"none");
			jQuery("#mod_spvp_control").addClass('youRtmp');
		<?php 
		}
		else { ?>
			jQuery("#mod_control_btns .playPrev, #mod_control_btns .playNext").css('display',"none");
			jQuery("#mod_spvp_control").removeClass('youRtmp');
		<?php }
	}
	?> 

	function embed_url(a,b) {
		jQuery('#embed_Url').html('<iframe allowFullScreen allowTransparency="true" frameborder="0" width="<?php echo $theme->appWidth ?>" height="<?php echo $theme->appHeight ?>" src="'+location.href+'&AlbumId='+a+'&TrackId='+b+'&tmpl=component" type="text/html" ></iframe>')
		jQuery('#embed_Url').focus(); jQuery('#embed_Url').select();
	}

	jQuery('#mod_control_btns .share, #mod_share_buttons').on('mouseenter',function(){
		left = jQuery('#mod_control_btns .share').position().left+<?php echo ($theme->appWidth-240); ?>;
		if(parseInt(jQuery('#mod_spvp_playlist').css('width'))==0){
			jQuery('#mod_share_buttons').css('left',left);
		}
		else {
			<?php if ($theme->playlistPos==1){ ?>
				jQuery('#mod_share_buttons').css('left',left+<?php echo $theme->playlistWidth ?>);
			<?php } else { ?>
				jQuery('#mod_share_buttons').css('left',left);
			<?php } ?>
		}
		jQuery('#mod_share_buttons').css('display','');
	})

	jQuery('#mod_control_btns .share, #mod_share_buttons').on('mouseleave',function(){
		jQuery('#mod_share_buttons').css('display','none')
	})

	if(<?php echo $theme->autoPlay ?>==1) {
		setTimeout(function(){jQuery('#thumb_0').click()},500);
		<?php if($typeselect==1)  { ?>
			setTimeout(function(){modVideo[0].click()},500);
		<?php } ?>
	}

	<?php if($theme->defaultShuffle=='shuffleOff') { ?>
		if(jQuery('#mod_control_btns .shuffle')[0]) {
			jQuery('#mod_control_btns .shuffle')[0].style.display = "none";
			jQuery('#mod_control_btns .shuffle')[1].style.display = "";
		}
	<?php
	} else { ?>
	if(jQuery('#mod_control_btns .shuffle')[0]) {
		jQuery('#mod_control_btns .shuffle')[1].style.display = "none";
		jQuery('#mod_control_btns .shuffle')[0].style.display = "";
	}
	<?php } ?>

	jQuery('#mod_control_btns .fullScreen').on('click',function(){
		if(modVideo[0].mozRequestFullScreen)
			modVideo[0].mozRequestFullScreen();
		if(modVideo[0].webkitEnterFullscreen)
			modVideo[0].webkitEnterFullscreen()
		if(modVideo[0].msRequestFullscreen)
			modVideo[0].msRequestFullscreen()
	})

	jQuery('.stop').on('click',function(){
		modVideo[0].currentTime = 0;
		modVideo[0].pause();
		paly_mod.css('display',"block");
		pause_mod.css('display',"none");
	})

	<?php if($theme->defaultRepeat=='repeatOff'){ ?>
		if(jQuery('#mod_control_btns .mod_repeat')[0]) {
			jQuery('#mod_control_btns .mod_repeat')[0].style.display = "none";
			jQuery('#mod_control_btns .mod_repeat')[1].style.display = "";
			jQuery('#mod_control_btns .mod_repeat')[2].style.display = "none";
		}
	<?php } ?>

	<?php if($theme->defaultRepeat=='repeatOne'){ ?>
		if(jQuery('#mod_control_btns .mod_repeat')[0]) {
			jQuery('#mod_control_btns .mod_repeat')[0].style.display = "none";
			jQuery('#mod_control_btns .mod_repeat')[1].style.display = "none";
			jQuery('#mod_control_btns .mod_repeat')[2].style.display = "";
		}
	<?php } ?>

	<?php if($theme->defaultRepeat=='repeatAll'){ ?>
		if(jQuery('#mod_control_btns .mod_repeat')[0]) {
			jQuery('#mod_control_btns .mod_repeat')[0].style.display = "";
			jQuery('#mod_control_btns .mod_repeat')[1].style.display = "none";
			jQuery('#mod_control_btns .mod_repeat')[2].style.display = "none";
		}
	<?php } ?>

	jQuery('#mod_control_btns .mod_repeat').on('click',function(){
		mod_repeat = jQuery('#mod_repeat').val();
		switch (mod_repeat) {
			case 'repeatOff':
				jQuery('#mod_repeat').val('repeatOne');
				jQuery('#mod_control_btns .mod_repeat')[0].style.display = "none";
				jQuery('#mod_control_btns .mod_repeat')[1].style.display = "none";
				jQuery('#mod_control_btns .mod_repeat')[2].style.display = "";
			break;
			case 'repeatOne':
				jQuery('#mod_repeat').val('repeatAll');
				jQuery('#mod_control_btns .mod_repeat')[0].style.display="";
				jQuery('#mod_control_btns .mod_repeat')[1].style.display="none";
				jQuery('#mod_control_btns .mod_repeat')[2].style.display="none";
			break;
			case 'repeatAll':
				jQuery('#mod_repeat').val('repeatOff');
				jQuery('#mod_control_btns .mod_repeat')[0].style.display="none";
				jQuery('#mod_control_btns .mod_repeat')[1].style.display="";
				jQuery('#mod_control_btns .mod_repeat')[2].style.display="none";
			break;
		}
	})

	jQuery('#mod_volumeTD #voulume_img').on('click',function(){
		if(jQuery('#mod_volumeTD .mod_volume')[0].style.width!='0%') {
			modVideo[0].mod_volume = 0;
			jQuery('#mod_volumeBar_width').val(jQuery('#mod_volumeTD .mod_volume')[0].style.width)
			jQuery('#mod_volumeTD .mod_volume').css('width','0%')
		}
		else {
			modVideo[0].mod_volume = parseInt(jQuery('#mod_volumeBar_width').val())/100;
			jQuery('#mod_volumeTD .mod_volume').css('width',jQuery('#mod_volumeBar_width').val())
		}
	})

	jQuery('#mod_control_btns .hd').on('click',function(){ 
		mod_current_time = modVideo[0].currentTime;
		mod_HD_on = jQuery('#mod_HD_on').val();
		mod_current_playlist_table = jQuery('#mod_current_playlist_table').val();
		mod_current_track = jQuery('#mod_current_track').val();

		if(jQuery('#mod_track_list_'+mod_current_playlist_table).find('#urlHD_'+mod_current_track).val() && mod_HD_on==0) {
			document.getElementById('mod_videoID').src=jQuery('#mod_track_list_'+mod_current_playlist_table).find('#urlHD_'+mod_current_track).val();
			play();
			setTimeout('modVideo[0].currentTime=mod_current_time',500)
			jQuery('#mod_HD_on').val(1);
		}
		if(jQuery('#mod_track_list_'+mod_current_playlist_table).find('#urlHD_'+mod_current_track).val() && mod_HD_on==1) {
			jQuery('#mod_track_list_'+mod_current_playlist_table).find('#thumb_'+mod_current_track).click();
			setTimeout('modVideo[0].currentTime=mod_current_time',500)
			jQuery('#mod_HD_on').val(0);
		}
	})

	function support(i,j) {
		if(jQuery('#mod_track_list_'+i).find('#vid_type_'+j).val()!='http') {
			jQuery('#not_supported').css('display','');
			jQuery('#support').val(0);
		}
		else {
			jQuery('#not_supported').css('display','none');
			jQuery('#support').val(1);
		}
	}

	jQuery('.play').on('click',function(){  modVideo[0].play();  })
	jQuery('.pause').on('click',function(){ modVideo[0].pause(); })

	/*-=-=- single video thumb -=-=-*/
	jQuery(function() {
		var mod_videos  = jQuery("#spvp_single_video_main");
		mod_videos.on('click', function(ev) {
			jQuery(this).addClass("player");
			jQuery(this).find('.spvp_single_video_main')[0].src += "&autoplay=1";
			ev.preventDefault();
		});
	});

	function mod_vid_select(x){
		jQuery("div.mod_vid_thumb").each(function(){
			if(jQuery(this).find("img")) {
				jQuery(this).find("img").hide(20);
			if(jQuery(this).find("img")[0])
				jQuery(this).find("img")[0].style.display="none";
			}	
			jQuery(this).css('background','none');
		})

		jQuery("div.mod_vid_thumb").each(function(){
			jQuery(this).mouseenter(function() {
				if(jQuery(this).find("img"))
					jQuery(this).find("img").slideDown(100);
				jQuery(this).css('background','rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>)')
				jQuery(this).css('color','#<?php echo $theme->textHoverColor  ?>')
			})

			jQuery(this).mouseleave(function() {
				if(jQuery(this).find("img"))
					jQuery(this).find("img").slideUp(300);
				jQuery(this).css('background','none');
				jQuery(this).css('color','#<?php echo $theme->textColor  ?>')
			});
			jQuery(this).css('color','#<?php echo $theme->textColor  ?>')
		})

		jQuery(x).unbind('mouseleave mouseenter'); 
		if(jQuery(x).find("img"))
		jQuery(x).find("img").show(10); 
		jQuery(x).css('background','rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>)')
		jQuery(x).css('color','#<?php echo $theme->textSelectedColor  ?>')
	}

	function mod_vid_select2(x){
		jQuery("p.vid_title").each(function(){
			this.onmouseover = function(){this.style.color='#'+'<?php echo $theme->textHoverColor?>' ;this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)) ?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)) ?>,0.4)'}
			this.onmouseout = function(){this.style.color='<?php echo '#'.$theme->textColor ?>';this.style.background=" none"}
			jQuery(this).css('background','none');
			jQuery(this).css('color','#<?php echo $theme->textColor  ?>');
		})
		jQuery(x).css('background','rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>)')
		jQuery(x).css('color','#<?php echo $theme->textSelectedColor  ?>')
		x.onmouseover = null;  
		x.onmouseout = null;
	}

	function mod_playlist_select(x) {
		jQuery("#mod_spvp_album_div td.playlist_td").each(function(){
			jQuery(this).css('background','none');
			jQuery(this).css('color','#<?php echo $theme->textColor  ?>');
			this.onmouseover = function(){this.style.color='#'+'<?php echo $theme->textHoverColor?>' ;this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)) ?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)) ?>,0.4)'}
			this.onmouseout = function(){this.style.color='<?php echo '#'.$theme->textColor ?>';this.style.background=" none"}
		})

		jQuery('#mod_playlist_'+x).css('background','rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>)')
		jQuery('#mod_playlist_'+x).css('color','#<?php echo $theme->textSelectedColor  ?>')
		jQuery('#mod_playlist_'+x)[0].onmouseover = null
		jQuery('#mod_playlist_'+x)[0].onmouseout = null
	}

	jQuery('#mod_control_btns .shuffle').on('click', function() {
		if(jQuery('#mod_shuffle').val()==0) {
			jQuery('#mod_shuffle').val(1);
			jQuery('#mod_control_btns .shuffle')[1].style.display="none";
			jQuery('#mod_control_btns .shuffle')[0].style.display="";
		}
		else {
			jQuery('#mod_shuffle').val(0);
			jQuery('#mod_control_btns .shuffle')[0].style.display="none";
			jQuery('#mod_control_btns .shuffle')[1].style.display="";
		}
	});

	jQuery("div.mod_vid_thumb").each(function(){
		jQuery(this).mouseenter(function() {
			if(jQuery(this).find("img"))
				jQuery(this).find("img").slideToggle(100);
			jQuery(this).css('background','rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>)')
			jQuery(this).css('color','#<?php echo $theme->textHoverColor  ?>')
		})

		jQuery(this).mouseleave(function() {
			if(jQuery(this).find("img"))
				jQuery(this).find("img").slideToggle(300);
			jQuery(this).css('background','none');
			jQuery(this).css('color','#<?php echo $theme->textColor  ?>')
		});
	})

	function mod_timeUpdate(){
		if(parseInt(document.getElementById('mod_videoID').currentTime/60)<10 && parseInt(document.getElementById('mod_videoID').currentTime % 60<10))
			document.getElementById('mod_time').innerHTML = '0'+parseInt(document.getElementById('mod_videoID').currentTime/60)+':0'+parseInt(document.getElementById('mod_videoID').currentTime % 60);

		if(parseInt(document.getElementById('mod_videoID').currentTime/60)<10)
			document.getElementById('mod_time').innerHTML = '0'+parseInt(document.getElementById('mod_videoID').currentTime/60)+':'+parseInt(document.getElementById('mod_videoID').currentTime % 60);

		if(parseInt(document.getElementById('mod_videoID').currentTime % 60)<10)
			document.getElementById('mod_time').innerHTML = '0'+parseInt(document.getElementById('mod_videoID').currentTime/60)+':0'+parseInt(document.getElementById('mod_videoID').currentTime % 60);
	}

	function mod_durationChange() {
		if(parseInt(document.getElementById('mod_videoID').duration/60)<10 && parseInt(document.getElementById('mod_videoID').duration % 60<10))
			document.getElementById('mod_duration').innerHTML = '0'+parseInt(document.getElementById('mod_videoID').duration/60)+':0'+parseInt(document.getElementById('mod_videoID').duration % 60);

		if(parseInt(document.getElementById('mod_videoID').duration/60)<10)
			document.getElementById('mod_duration').innerHTML = '0'+parseInt(document.getElementById('mod_videoID').duration/60)+':'+parseInt(document.getElementById('mod_videoID').duration % 60);

		if(parseInt(document.getElementById('mod_videoID').duration % 60)<10)
			document.getElementById('mod_duration').innerHTML = parseInt(document.getElementById('mod_videoID').duration/60)+':0'+parseInt(document.getElementById('mod_videoID').duration % 60);
	}

	function mod_scrollBottom(){
		mod_current_playlist_table = document.getElementById('mod_current_playlist_table').value;
		if(document.getElementById('mod_scroll_div_'+mod_current_playlist_table).offsetHeight+parseInt(document.getElementById("mod_scroll_div_"+mod_current_playlist_table).style.top)+55<=document.getElementById('mod_spvp_global_body').offsetHeight)
			return false;
		document.getElementById('mod_scroll_height').value = parseInt(document.getElementById('mod_scroll_height').value)+5
		document.getElementById("mod_scroll_div_"+mod_current_playlist_table).style.top="-"+document.getElementById('mod_scroll_height').value+"px";
	};
	function scrollTop(){
		mod_current_playlist_table = document.getElementById('mod_current_playlist_table').value;
		if(document.getElementById('mod_scroll_height').value<=0)
			return false;
		document.getElementById('mod_scroll_height').value = parseInt(document.getElementById('mod_scroll_height').value)-5
		document.getElementById("mod_scroll_div_"+mod_current_playlist_table).style.top="-"+document.getElementById('mod_scroll_height').value+"px";
	};

	function scrollBottom2(){
		mod_current_playlist_table = document.getElementById('mod_current_playlist_table').value;
		if(!mod_current_playlist_table) {
			mod_current_playlist_table = 0;
		}
		if(document.getElementById('mod_scroll_div2_'+mod_current_playlist_table).offsetHeight+parseInt(document.getElementById("mod_scroll_div2_"+mod_current_playlist_table).style.top)+150<=document.getElementById('mod_spvp_global_body').offsetHeight)
			return false;
		document.getElementById('mod_scroll_height2').value = parseInt(document.getElementById('mod_scroll_height2').value)+5
		document.getElementById("mod_scroll_div2_"+mod_current_playlist_table).style.top = "-"+document.getElementById('mod_scroll_height2').value+"px";
	};
	function scrollTop2(){
		mod_current_playlist_table = document.getElementById('mod_current_playlist_table').value;
		if(document.getElementById('mod_scroll_height2').value<=0)
			return false;
		document.getElementById('mod_scroll_height2').value = parseInt(document.getElementById('mod_scroll_height2').value)-5
		document.getElementById("mod_scroll_div2_"+mod_current_playlist_table).style.top = "-"+document.getElementById('mod_scroll_height2').value+"px";
	};

	function mod_openPlaylist(i,j) {
		document.getElementById('mod_scroll_height').value = 0;
		mod_lib_table_count = document.getElementById('mod_lib_table_count').value;
		for(lib_table=0;lib_table<mod_lib_table_count;lib_table++) {
			document.getElementById('mod_lib_table_'+lib_table).style.display = "none";
		}
		jQuery("#mod_playlist_table_"+i).fadeIn(700);
		document.getElementById('mod_current_lib_table').value = j;
		document.getElementById('mod_current_playlist_table').value = i;
		document.getElementById('tracklist_down').style.display = "" ;
		document.getElementById('tracklist_up').style.display = "";
		document.getElementById('button29').style.display = "block";
		document.getElementById('button27').onclick = function(){nextPlaylist()};
		document.getElementById('button28').onclick = function(){prevPlaylist()};
	}
	function nextPlaylist(){
		document.getElementById('mod_scroll_height').value = 0;
		mod_lib_table_count = document.getElementById('mod_lib_table_count').value;
		for(lib_table=0;lib_table<mod_lib_table_count;lib_table++){
			document.getElementById('mod_lib_table_'+lib_table).style.display="none";
		}
		mod_current_lib_table = document.getElementById('mod_current_lib_table').value;
		next_playlist_table = parseInt(document.getElementById('mod_current_playlist_table').value)+1;
		mod_current_playlist_table = parseInt(document.getElementById('mod_current_playlist_table').value);
		if(next_playlist_table><?php echo count($mod_playlist_array)-2 ?>)
			return false;
		jQuery("#mod_playlist_table_"+mod_current_playlist_table).css('display','none');
		jQuery("#mod_playlist_table_"+next_playlist_table).fadeIn(700);

		document.getElementById('mod_current_playlist_table').value = next_playlist_table;
		document.getElementById('tracklist_down').style.display="" ;
		document.getElementById('tracklist_up').style.display="";
		document.getElementById('button29').style.display="block";
	}
	function prevPlaylist(){
		document.getElementById('mod_scroll_height').value = 0;
		mod_lib_table_count = document.getElementById('mod_lib_table_count').value;
		for(lib_table=0;lib_table<mod_lib_table_count;lib_table++)	{
			document.getElementById('mod_lib_table_'+lib_table).style.display="none";
		}
		mod_current_lib_table = document.getElementById('mod_current_lib_table').value;
		prev_playlist_table = parseInt(document.getElementById('mod_current_playlist_table').value)-1;
		mod_current_playlist_table = parseInt(document.getElementById('mod_current_playlist_table').value);
		if(prev_playlist_table<0)
			return false;
		jQuery("#mod_playlist_table_"+mod_current_playlist_table).css('display','none');
		jQuery("#mod_playlist_table_"+prev_playlist_table).fadeIn(700);

		document.getElementById('mod_current_playlist_table').value = prev_playlist_table;
		document.getElementById('tracklist_down').style.display = "" ;
		document.getElementById('tracklist_up').style.display = "";
		document.getElementById('button29').style.display = "block";
	}

	function mod_openLibTable() {
		mod_current_lib_table = document.getElementById('mod_current_lib_table').value;
		document.getElementById('mod_scroll_height').value = 0;
		mod_current_playlist_table = document.getElementById('mod_current_playlist_table').value;
		jQuery("#mod_lib_table_"+mod_current_lib_table).fadeIn(700);
		document.getElementById('mod_playlist_table_'+mod_current_playlist_table).style.display = "none";
		document.getElementById('tracklist_down').style.display = "none" ;
		document.getElementById('tracklist_up').style.display = "none";
		document.getElementById('button29').style.display = "none";
		document.getElementById('button27').onclick = function(){mod_nextPage()};
		document.getElementById('button28').onclick = function(){mod_prevPage()};
	}

	var next_page = 0;
	function mod_nextPage() {
		if(next_page==document.getElementById('mod_lib_table_count').value-1)
		return false;
		next_page = next_page+1;
		for(g=0; g<document.getElementById('mod_lib_table_count').value; g++){
			document.getElementById('mod_lib_table_'+g).style.display = "none";
			if(g==next_page) {
				jQuery("#mod_lib_table_"+g).fadeIn(900);
			}
		}
	}
	function mod_prevPage() {
		if(next_page==0)
			return false;
		next_page = next_page-1;
		for(g=0; g<document.getElementById('mod_lib_table_count').value; g++) {
			document.getElementById('mod_lib_table_'+g).style.display = "none";
			if(g==next_page) {
				jQuery("#mod_lib_table_"+g).fadeIn(900);
			}
		}
	}

	function mod_playBTN() {
		mod_current_playlist_table = document.getElementById('mod_current_playlist_table').value;
		track_list = document.getElementById('track_list').value;
		document.getElementById('mod_track_list_'+mod_current_playlist_table).style.display = "block";
		if(mod_current_playlist_table!=track_list)
			document.getElementById('mod_track_list_'+track_list).style.display = "none";
		
		document.getElementById('track_list').value = mod_current_playlist_table;
		modVideo[0].play();
		paly_mod.css('display',"none");
		pause_mod.css('display',"block");
	}

	function play() {
		modVideo[0].play();
		paly_mod.css('display',"none");
		pause_mod.css('display',"block");
	}

	/*-=-=- Play/Pause control clicked -=-=-*/
	jQuery('#mod_control_btns .btnPlay <?php if($theme->clickOnVid==1) echo ',#mod_videoID' ?>, #mod_control_btns .btnPause').on('click', function() { 
		if(modVideo[0].paused) {
			modVideo[0].play();
			paly_mod.css('display',"none");
			pause_mod.css('display',"block");
		}
		else {
		  modVideo[0].pause();
		  paly_mod.css('display',"block");
		  pause_mod.css('display',"none");
		}
		return false;
	});


	function check_volume() {
		mod_percentage = modVideo[0].mod_volume * 100;
		jQuery('#mod_volumeTD .mod_volume').css('width', mod_percentage+'%');
		document.getElementById("mod_spvp_playlist").style.width = '0px';
		document.getElementById("mod_spvp_playlist").style.display = 'none';
	}
	window.onload = check_volume();

	/*-=-=- get HTML5 modVideo time duration -=-=-*/
	modVideo.on('loadedmetadata', function() {
	   jQuery('.duration').text(modVideo[0].duration);
	});

	/*-=-=- update HTML5 video current play time -=-=-*/
	modVideo.on('timeupdate', function() {
		var mod_progress = jQuery('#mod_spvp_control .progressBar');
		var currentPos = modVideo[0].currentTime; // Get currenttime
		var mod_maxduration = modVideo[0].duration; // Get video duration
		var mod_percentage = 100 * currentPos / mod_maxduration; //in %
		var mod_position = (<?php echo $theme->appWidth; ?> * mod_percentage / 100)-mod_progress.offset().left; 
		jQuery('#mod_spvp_control .timeBar').css('width', mod_percentage+'%');
	});

	modVideo.on('ended',function(){
		if(jQuery('#mod_repeat').val()=="repeatOne")  {
			modVideo[0].currentTime = 0;
			modVideo[0].play();
			paly_mod.css('display',"none");
			pause_mod.css('display',"block");
		}
		if(jQuery('#mod_repeat').val()=="repeatAll") {
			jQuery('#mod_control_btns .playNext').click();
		}
		if(jQuery('#mod_repeat').val()=="repeatOff"){
			if(mod_vid_num==mod_video_urls.length-1) {
				modVideo[0].currentTime = 0;
				modVideo[0].pause();
				paly_mod.css('display',"block");
				pause_mod.css('display',"none");
			}
		}
		<?php if($theme->autoNext==1) { ?>
			if(jQuery('#mod_repeat').val()=="repeatOff") 
			if(mod_vid_num==mod_video_urls.length-1) {
				modVideo[0].currentTime=0;
				modVideo[0].pause();
				paly_mod.css('display',"block");
				pause_mod.css('display',"none");
			}
			else {	
				jQuery('#mod_control_btns .playNext').click();
			}
		<?php } ?>
	})

	/* Drag status */
	var timeDrag = false;
	jQuery('#mod_spvp_control .progressBar').mousedown(function(e) {
	   timeDrag = true;
	   mod_updatebar(e.pageX);
	});

	jQuery('#mod_spvp_control .progressBar').select(function(){ })
	 
	jQuery(document).mouseup(function(e) {
		if(timeDrag) {
			timeDrag = false;
			mod_updatebar(e.pageX);
		}
	});

	jQuery(document).mousemove(function(e) {
		if(timeDrag) {
			mod_updatebar(e.pageX);
		}
	});

	/*-=-=- update Progress Bar control -=-=-*/
	var mod_updatebar = function(x) {
		var mod_progress = jQuery('#mod_spvp_control .progressBar');
		var mod_maxduration = modVideo[0].duration; //Video duraiton
		var mod_position = x - mod_progress.offset().left; //Click pos
		var mod_percentage = 100 * mod_position / mod_progress.width();
		//Check within range
		if(mod_percentage > 100) {
			mod_percentage = 100;
		}
		if(mod_percentage < 0) {
			mod_percentage = 0;
		}
		//Update progress bar and modVideo currenttime
		jQuery('#mod_spvp_control .timeBar').css('width', mod_percentage+'%');
		jQuery('.spanA').css('left', mod_position+'px');
		modVideo[0].currentTime = mod_maxduration * mod_percentage / 100;
	};

	/*-=-=- loop to get HTML5 modVideo buffered data -=-=-*/
	function startBuffer() {
		setTimeout(function(){
			var mod_maxduration = modVideo[0].duration;
			var mod_currentBuffer = modVideo[0].buffered.end(0);
			var mod_percentage = 100 * mod_currentBuffer / mod_maxduration;
			jQuery('#mod_spvp_control .bufferBar').css('width', mod_percentage+'%');
			if(mod_currentBuffer < mod_maxduration) {
				setTimeout(startBuffer, 500);
			}
		},800)
	};

	checkVideoLoad=setInterval(function(){
		if(modVideo[0].duration) {
			setTimeout(startBuffer(), 500);
			clearInterval(checkVideoLoad)
		}
	}, 1000)

	/*-=-=- Mute/Unmute control clicked -=-=-*/
	var mod_volume = jQuery('.mod_volumeBar');
	jQuery('.muted').click(function() {
		modVideo[0].muted = !modVideo[0].muted;
		return false;
	});

	/*-=-=- Volume control clicked -=-=-*/
	jQuery('.mod_volumeBar').on('mousedown', function(e) {
		var mod_position = e.pageX - mod_volume.offset().left;
		var mod_percentage = 100 * mod_position / mod_volume.width();
		jQuery('#mod_volumeTD .mod_volume').css('width', mod_percentage+'%');
		modVideo[0].mod_volume = mod_percentage / 100;
	});

	/* Drag status */
	var volumeDrag = false;
	jQuery('.mod_volumeBar').mousedown(function(e) {
	   volumeDrag = true;
	   updateVolumeBar(e.pageX);
	});
	jQuery(document).mouseup(function(e) {
	   if(volumeDrag) {
		  volumeDrag = false;
		  updateVolumeBar(e.pageX);
	   }
	});
	jQuery(document).mousemove(function(e) {
		if(volumeDrag) {
			updateVolumeBar(e.pageX);
		}
	});


	var updateVolumeBar = function(x) {
		var mod_progress = jQuery('.mod_volumeBar');
		var mod_position = x - mod_progress.offset().left; //Click pos
		var mod_percentage = 100 * mod_position / mod_progress.width();
		//Check within range
		if(mod_percentage > 100) {
			mod_percentage = 100;
		}
		if(mod_percentage < 0) {
			mod_percentage = 0;
		}
		//Update progress bar and modVideo currenttime
		jQuery('#mod_volumeTD .mod_volume').css('width', mod_percentage+'%');
		modVideo[0].mod_volume =  mod_percentage / 100;
	};

	var yy=1;
	controlHideTime='';
	jQuery("#mod_spvp_global_body").each(function(){
		jQuery(this).mouseleave(function() {
			controlHideTime=setInterval(function(){
				yy=yy+1;
				if(yy<<?php echo $theme->autohideTime ?>) {
					return false
				}
				else {
					clearInterval(controlHideTime);
					yy=1;
					jQuery("#event_type").val('mouseleave');
					<?php if($theme->playlistAutoHide==1){ ?>
						jQuery("#mod_spvp_playlist").animate({
							width: "0px",
						},300 );
						setTimeout(function(){ jQuery("#mod_spvp_playlist").css('display','none');},300)
						jQuery(".mod_spvp_control").animate({
							width: <?php echo $theme->appWidth; ?>+"px",
							<?php if ($theme->playlistPos==1){ ?>
								marginLeft:'0px'
							<?php } else {?>
								marginRight:'0px'
							<?php } ?>
						}, 300 );
						jQuery("#mod_control_btns").animate({
							width: <?php echo $theme->appWidth?>+"px",
						}, 300 );

						<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
							jQuery("#mod_videoID").animate({
								width: <?php echo $theme->appWidth ?>+"px",
								marginLeft:'0px'
							}, 300 );  
						<?php } ?>

						<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
							jQuery("#mod_videoID").animate({
								width: <?php echo $theme->appWidth ?>+"px",
							}, 300 );  
						<?php } ?>
					<?php } ?>

					<?php if($theme->ctrlsSlideOut==1){ ?>
						jQuery('.mod_spvp_control').hide("slide", { direction: "<?php if($theme->ctrlsPos==1) echo 'up'; else echo 'down'; ?>" }, 1000);
					<?php } ?>

					<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
						jQuery("#mod_videoID").animate({
							width: <?php echo $theme->appWidth ?>+"px",
							marginLeft:'0px'
						}, 300 );  
					<?php } ?>

					<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
						jQuery("#mod_videoID").animate({
							width: <?php echo $theme->appWidth ?>+"px",
						}, 300 );  
					<?php } ?>
				}
			},1000);
		});

		jQuery(this).mouseenter(function() {
			if(controlHideTime) {
				clearInterval(controlHideTime)
				yy=1;
			}	 
			if(document.getElementById('mod_spvp_control').style.display=="none") {
				jQuery('.mod_spvp_control').show("slide", { direction: "<?php if($theme->ctrlsPos==1) echo 'up'; else echo 'down'; ?>" }, 450);
			}
		})
	})


	var xx = 1; 
	volumeHideTime = '';
	jQuery("#mod_volumeTD").each(function(){
		jQuery('#mod_volumeTD').mouseleave(function() {
			volumeHideTime = setInterval(function(){
				xx=xx+1;
				if(xx<2) {
					return false
				}
				else {
					clearInterval(volumeHideTime);
					xx=1;				
					jQuery("#mod_control_btns #space").animate({
						paddingLeft:<?php echo (($theme->appWidth*20)/100)+'px' ?>,
					},1000);
					
					jQuery("#mod_volumeTD #volumebar_player").animate({ 
						width:'0px',
					},1000);
					
					mod_percentage = modVideo[0].mod_volume * 100;
					jQuery('#mod_volumeTD .mod_volume').css('width', mod_percentage+'%');
				}
			},1000)
		})

		jQuery('#mod_volumeTD').mouseenter(function() {
			if(volumeHideTime) {
				clearInterval(volumeHideTime)
				xx=1;
			}
			jQuery("#mod_control_btns #space").animate({ 
				paddingLeft:<?php echo (($theme->appWidth*20)/100)-100+'px' ?>,
			},500);

			jQuery("#mod_volumeTD #volumebar_player").animate({ 
				width:'100px',
			},500);
		});
	})


	jQuery('#mod_control_btns .playlist').on('click', function() { 
		if(document.getElementById("mod_spvp_playlist").style.width=="0px") { 
			jQuery("#mod_spvp_playlist").css('display','')
			
			jQuery("#mod_spvp_playlist").animate({
				width: <?php echo $theme->playlistWidth; ?>+"px",
			}, 500 );
			
			jQuery(".mod_spvp_control").animate({
				width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
				<?php if ($theme->playlistPos==1){ ?>
					marginLeft:<?php echo $theme->playlistWidth; ?>+'px'
				<?php } else {?>
					marginRight:<?php echo $theme->playlistWidth; ?>+'px'
				<?php } ?>
			}, 500 );
			
			jQuery("#mod_control_btns").animate({
				width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
			}, 500 );

			<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
				jQuery("#mod_videoID").animate({
					width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
					marginLeft:<?php echo $theme->playlistWidth ?>
				}, 500 );  
			<?php } ?>
			
			<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
				jQuery("#mod_videoID").animate({
					width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
				}, 500 );  
			<?php } ?>
		}
		else {
			jQuery("#mod_spvp_playlist").animate({
				width: "0px",
			}, 1500 );
			
			setTimeout(function(){ jQuery("#mod_spvp_playlist").css('display','none');},1500)
			
			jQuery(".mod_spvp_control").animate({
				width: <?php echo $theme->appWidth; ?>+"px",
				<?php if ($theme->playlistPos==1){ ?>
					marginLeft:'0px'
				<?php } else {?>
					marginRight:'0px'
				<?php } ?>
			}, 1500 );

			jQuery("#mod_control_btns").animate({
				width: <?php echo $theme->appWidth?>+"px",
			}, 1500 );

			<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
				jQuery("#mod_videoID").animate({
					width: <?php echo $theme->appWidth ?>+"px",
					marginLeft:'0px'
				}, 1500 );  
			<?php } ?>
			
			<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
				jQuery("#mod_videoID").animate({
					width: <?php echo $theme->appWidth ?>+"px",
				}, 1500 );  
				<?php } ?>
		}
	});

	mod_current_playlist_table=document.getElementById('mod_current_playlist_table').value;
	mod_video_urls = jQuery('#mod_track_list_'+mod_current_playlist_table).find('.mod_vid_thumb');

	function mod_current_playlist_videos(){
		mod_current_playlist_table=document.getElementById('mod_current_playlist_table').value;
		mod_video_urls = jQuery('#mod_track_list_'+mod_current_playlist_table).find('.mod_vid_thumb');
	}

	var mod_vid_num=0;
	var mod_used_track = new Array();
	jQuery('#mod_control_btns .playPrev').on('click', function() {
		mod_used_track[mod_used_track.length] =mod_vid_num;
		mod_vid_num++;
		if(jQuery('#mod_shuffle').val()==1){
			mod_vid_num=parseInt(Math.random() * (mod_video_urls.length+1 - 0) + 0);
			while(in_array(mod_vid_num,mod_used_track)){
				mod_vid_num=parseInt(Math.random() * (mod_video_urls.length+1 - 0) + 0);
			}
		}
		if(mod_used_track.length>=mod_video_urls.length){
			mod_used_track=[];
		}
		if(mod_vid_num<0){
			mod_vid_num = mod_video_urls.length-1;
		}
		mod_video_urls[mod_vid_num].click();
	});


	function in_array(needle, haystack, strict) {	
		// Checks if a value exists in an array
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		var found = false, key, strict = !!strict;
		for (key in haystack) {
			if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
				found = true;
				break;
			}
		}
		return found;
	}


	jQuery('#mod_control_btns .playNext').on('click', function() {
		mod_used_track[mod_used_track.length] = mod_vid_num;
		mod_vid_num++;
		if(jQuery('#mod_shuffle').val()==1){
			mod_vid_num=parseInt(Math.random() * (mod_video_urls.length+1 - 0) + 0);
			while(in_array(mod_vid_num,mod_used_track)){
				mod_vid_num=parseInt(Math.random() * (mod_video_urls.length+1 - 0) + 0);
			}
		}
		if(mod_used_track.length>=mod_video_urls.length){
			mod_used_track=[];
		}
		jQuery('#mod_spvp_control .timeBar').css('width', '0%');
		if(mod_vid_num==mod_video_urls.length){
			mod_vid_num=0;
		}
		mod_video_urls[mod_vid_num].click();
	});


	jQuery("#mod_control_btns .lib").click(function () {
		jQuery('#mod_spvp_album_div').css('transform','');
		jQuery('#mod_spvp_global_body').css('transform','');
		jQuery('#mod_spvp_global_body').transition({
			perspective: '700px',
			rotateY: '180deg',
		},1000);

		setTimeout(function(){
			jQuery('#mod_spvp_album_div').css('-ms-transform','rotateY(180deg)')
			jQuery('#mod_spvp_album_div').css('transform','rotateY(180deg)')

			jQuery('#mod_spvp_album_div').css('-o-transform','rotateY(180deg)')
			document.getElementById('mod_spvp_album_div').style.display='block'
			document.getElementById('mod_spvp_video_div').style.display='none'
		},300);
		
		setTimeout(function(){
			jQuery('#mod_spvp_album_div').css('-ms-transform','');
			jQuery('#mod_spvp_global_body').css('-ms-transform','');

			jQuery('#mod_spvp_album_div').css('transform','');
			jQuery('#mod_spvp_global_body').css('transform','');

			jQuery('#mod_spvp_album_div').css('-o-transform','');
			jQuery('#mod_spvp_global_body').css('-o-transform','');
		},1100);
	})


	jQuery("#mod_spvp_album_div .show_vid").click(function () {
		jQuery('#mod_spvp_global_body').transition({
			perspective: '700px',
			rotateY: '180deg',
		},1000);

		setTimeout(function(){
			jQuery('#mod_spvp_video_div').css('-ms-transform','rotateY(180deg)')
			jQuery('#mod_spvp_video_div').css('transform','rotateY(180deg)')
			jQuery('#mod_spvp_video_div').css('-o-transform','rotateY(180deg)')
			document.getElementById('mod_spvp_album_div').style.display='none'
			document.getElementById('mod_spvp_video_div').style.display='block'
		},300);
		
		setTimeout(function(){
			jQuery('#mod_spvp_video_div').css('-ms-transform','');
			jQuery('#mod_spvp_global_body').css('-ms-transform','');

			jQuery('#mod_spvp_video_div').css('transform','');
			jQuery('#mod_spvp_global_body').css('transform',''); 

			jQuery('#mod_spvp_video_div').css('-o-transform','');
			jQuery('#mod_spvp_global_body').css('-o-transform','');
		},1100);
	})

	/*-=-=- COLOR -=-=-*/
	var canvas=[]
	var ctx=[]
	var originalPixels=[]
	var currentPixels=[]
	for(i=1; i<30; i++)
		if(document.getElementById('button'+i)) {
			canvas[i] = document.createElement("canvas");
			ctx[i] = canvas[i].getContext("2d");
			originalPixels[i] = null;
			currentPixels[i] = null;
		}
			

	function mod_getPixels() {	
		for(i=1;i<30;i++)
			if(document.getElementById('button'+i)) {
				img=document.getElementById('button'+i);	
				canvas[i].width = img.width;
				canvas[i].height = img.height;
				ctx[i].drawImage(img, 0, 0, img.naturalWidth, img.naturalHeight, 0, 0, img.width, img.height);
				originalPixels[i] = ctx[i].getImageData(0, 0, img.width, img.height);
				currentPixels[i] = ctx[i].getImageData(0, 0, img.width, img.height);
				img.onload = null;
			}
	}
		
	function HexToRGB(Hex) {
		var Long = parseInt(Hex.replace(/^#/, ""), 16);
		return {
			R: (Long >>> 16) & 0xff,
			G: (Long >>> 8) & 0xff,
			B: Long & 0xff
		};
	}
		
	function mod_changeColor() {
		for(i=1;i<30;i++)
		if(document.getElementById('button'+i)) {
			if(!originalPixels[i]) return; // Check if image has loaded
			var newColor = HexToRGB(document.getElementById("color").value);

			for(var I = 0, L = originalPixels[i].data.length; I < L; I += 4) {
				if(currentPixels[i].data[I + 3] > 0) {
					currentPixels[i].data[I] = originalPixels[i].data[I] / 255 * newColor.R;
					currentPixels[i].data[I + 1] = originalPixels[i].data[I + 1] / 255 * newColor.G;
					currentPixels[i].data[I + 2] = originalPixels[i].data[I + 2] / 255 * newColor.B;
				}
			}
			ctx[i].putImageData(currentPixels[i], 0, 0);
			img=document.getElementById('button'+i);	
			img.src = canvas[i].toDataURL("image/png");
		}
	}

	<?php if($theme->spaceOnVid==1) { ?>
		var video_focus;
		jQuery('#mod_spvp_global_body ,#mod_videoID').each(function(){
			jQuery(this).on('click',function() { 
				setTimeout("video_focus=1",100) 
			})
		})

		jQuery('body').on('click',function(){video_focus=0})
		jQuery(window).keypress(function(event) {
			if ( event.which == 13 ) {
				event.preventDefault();
			}
			if(event.keyCode==32 && video_focus==1) {
				vidOnSpace()
				return false;
			}
		});
	<?php } ?>


	function vidOnSpace() {
		if(modVideo[0].paused) {
			modVideo[0].play();
			paly_mod.css('display',"none");
			pause_mod.css('display',"block");
		}
		else {
			modVideo[0].pause();
			paly_mod.css('display',"block");
			pause_mod.css('display',"none");
		}
	}

	jQuery('#track_list_0').find('#thumb_0').click();

	<?php if($mod_vds["type"]=="http" || $mod_single_video_type=="http"){ ?>
		modVideo[0].pause();
	<?php } ?>

	if(paly_mod && pause_mod) {
		paly_mod.css('display',"block");
		pause_mod.css('display',"none");
	}

	<?php if($AlbumId!=''){ ?>
		jQuery('#mod_track_list_<?php echo $AlbumId ?>').find('#thumb_<?php echo $TrackId ?>').click();
	<?php } ?>
	/*-=-=- END COLOR -=-=-*/


	jQuery('#mod_spvp_global_body').find('img').last().load(function(){setTimeout('mod_getPixels();mod_changeColor();',1600)})
	jQuery('#mod_volumeTD .mod_volume')[0].style.width='<?php echo $theme->defaultVol?>%';
	modVideo[0].mod_volume=<?php echo $theme->defaultVol/100 ;?>;
	<?php if($theme->ctrlsSlideOut==0) { ?>
		jQuery('#mod_videoID').mouseenter();
	<?php } ?>

	<?php if($theme->openPlaylistAtStart==1) { ?>
		jQuery('#mod_control_btns .playlist').click();
	<?php } ?>
	</script>
</div>                                                                                              

<div id="spidervideoplayerflash_module<?php echo $module->id ?>" style="display:none">
	<script type="text/javascript" src="components/com_spidervideoplayer/js/swfobject.js"></script>
	<div id="flashcontent<?php echo $idd ?>"  style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px"></div>

	<script type="text/javascript">
	function mod_flashShare(type,b,c){
		mod_u = location.href;
		if(mod_u=='<?php echo JURI::root(); ?>index.php?' || mod_u=='<?php echo JURI::root(); ?>' )
			mod_u='<?php echo JURI::root(); ?>index.php?';
		else if(!location.search)
			mod_u=mod_u+'?';
		else
			mod_u=mod_u+'&';

		t = document.title;
		switch (type) {
			case 'fb':	
				window.open('http://www.facebook.com/sharer.php?mod_u='+encodeURIComponent(mod_u+'AlbumId='+b+'&TrackId='+c)+'&t='+encodeURIComponent(t), "Facebook","menubar=1,resizable=1,width=350,height=250");
			break;
			
			case 'g':
				window.open('http://plus.google.com/share?url='+encodeURIComponent(mod_u+'AlbumId='+b+'&TrackId='+c)+'&t='+encodeURIComponent(t), "Google","menubar=1,resizable=1,width=350,height=250");
			break;

			case 'tw':
				window.open('http://twitter.com/home/?status='+encodeURIComponent(mod_u+'&AlbumId='+b+'&TrackId='+c), "Twitter","menubar=1,resizable=1,width=350,height=250");
			break;
		}
	}	
	var mod_so = new SWFObject("<?php echo JURI::root();?>components/com_spidervideoplayer/videoPlayer.swf?wdrand=<?php echo mt_rand() ?>", "Player", "100%", "100%", "8", "#000000");

	mod_so.addParam("FlashVars", "settingsUrl=<?php echo $url; ?>");
	mod_so.addParam("quality", "high");
	mod_so.addParam("menu", "false");
	mod_so.addParam("wmode", "transparent");
	mod_so.addParam("loop", "false");
	mod_so.addParam("allowfullscreen", "true");
	mod_so.write("flashcontent<?php echo $idd ?>");
	</script>

</div>




<script>
var html5_module = document.getElementById("spidervideoplayerhtml5_module<?php echo  $module->id ?>");
var flash_module = document.getElementById("spidervideoplayerflash_module<?php echo  $module->id ?>");

if(!FlashDetect.installed || '<?php echo $priority ?>'=='html5'){
	flash_module.parentNode.removeChild(flash_module);
	html5_module.style.display='';
}
else {
	html5_module.parentNode.removeChild(html5_module);
	flash_module.style.display='';
}
</script>