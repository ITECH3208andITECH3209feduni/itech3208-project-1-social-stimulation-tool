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

$db =& JFactory::getDBO();
$params = $this->params;

$playlist = $this->playlist;
$comp_playlist_array = explode(',',$playlist);

$theme 	=& JTable::getInstance('spidervideoplayer_theme', 'Table');
$theme_id = $this->theme;
$theme->load($theme_id);

$display = JRequest::getVar('display');
$typeselect = $this->typeselect;
$track_ID = $this->track;

$track_URL = '';
$track_poster = '';

/*-=-=-=-=-=-=- Single video -=-=-=-=-=-=-*/
$query = "SELECT url,type,title FROM #__spidervideoplayer_video WHERE id IN ($track_ID)";
$db->setQuery($query);
$comp_videos = $db->loadAssocList();

//$single_video_url = $comp_videos[0]['url'];	
if(isset($comp_videos[0]['type']))
	$comp_single_video_type = $comp_videos[0]['type'];
else 
	$comp_single_video_type = '';


if($typeselect==1){
	$comp_playlist_array = '';
	$track 	=& JTable::getInstance('spidervideoplayer_video', 'Table');
	
	$track->load($track_ID);
	
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

$libRows = $theme->libRows;
$libCols = $theme->libCols;
$cellWidth = 100/$libCols.'%';
$cellHeight = 100/$libRows.'%';
$play 	=& JTable::getInstance('spidervideoplayer_playlist', 'Table');

/*-=-=-=-=-=-=- load the row from the db table -=-=-=-=-=-=-*/
$k = $libRows*$libCols;
$p = JRequest::getVar('play',0);
$display = 'style="width:100%;height:100%;border-collapse: collapse;"';
$table_count = 1;
$itemBgHoverColor ='#'.$theme->itemBgHoverColor;
$comp_vds =& JTable::getInstance('spidervideoplayer_video', 'Table');
$ctrlsStack = $theme->ctrlsStack;

if($theme->ctrlsPos==2) {
	$ctrl_top = $theme->appHeight-41 .'px';
	$ctrl_top2 = $theme->appHeight-33 .'px';
}
else
	$ctrl_top = '-0px';

$AlbumId = JRequest::getVar('AlbumId');
$TrackId = JRequest::getVar('TrackId'); 
?>

<script src="<?php echo $cmpnt_js_path; ?>/jquery-1.7.2.js"></script>
<script src="<?php echo $cmpnt_js_path; ?>/jquery-ui.min.js"></script>
<script src="<?php echo $cmpnt_js_path; ?>/jquery.transit.js"></script>

<link href="<?php echo $cmpnt_js_path; ?>/jquery-ui.css" rel="stylesheet" type="text/css"/>

<style>
#main { padding: 0px; min-height: 0px; }
.comp_spvp_control {
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
.comp_spvp_control.youRtmp { 
	width: 47% !important; left: 225px; 
	top:<?php echo $ctrl_top2; ?>;
	padding: 0 10px;
}
.comp_spvp_control.youRtmp.singYouRtmp { width: 20% !important; left: 400px; }

.comp_spvp_control.youRtmp .btnPlay { display:none !important; }

#comp_control_btns { 
	opacity:<?php echo $theme->ctrlsMainAlpha/100; ?>;
	border:none; border-collapse: collapse;
	height:80%; width: 98% !important; 	
	margin: 0 auto; margin-top:4px;	
}

#comp_control_btns td, #comp_control_btns #space { 
	padding:0 !important; 
	text-align: center;
}

#comp_spvp_control .progressBar {
	z-index:5;
	cursor:pointer;
	position: relative;
	width: 100%; height:6px;
	border-top:1px solid rgba(<?php echo HEXDEC(SUBSTR($theme->slideColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
	border-bottom:1px solid rgba(<?php echo HEXDEC(SUBSTR($theme->slideColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
}
#comp_spvp_control .timeBar {
	z-index:5;
	top: 0; left: 0;
	position: absolute;
	width: 0; height: 100%;
	background-color: <?php echo '#'.$theme->slideColor; ?>;
}
#comp_spvp_control .bufferBar {
	opacity:0.3;
	top: 0; left: 0;
	position: absolute;
	width: 0; height: 100%;
	background-color: <?php echo '#'.$theme->slideColor; ?>;	
}
.comp_volumeBar {
	overflow: hidden;
	position: relative;	
	width: 0px; height:4px;
	background-color: rgba(<?php echo HEXDEC(SUBSTR($theme->framesBgColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
	border:1px solid rgba(<?php echo HEXDEC(SUBSTR($theme->slideColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
}
#comp_volumeTD .comp_volume {
	top: 0; left: 0;
	position: absolute;	
	width: 0; height: 100%;
	background-color: <?php echo '#'.$theme->slideColor; ?>;
}
#comp_spvp_playlist {
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

<?php 
if($typeselect==0)
	$startWithLib = $theme->startWithLib;
else
	$startWithLib = 0;
?>

<div id="comp_spvp_global_body" style="width:<?php echo $theme->appWidth; ?>px;height:<?php echo $theme->appHeight; ?>px; position:relative;">

	<!-- 1. Video_div -->
	<div id="comp_spvp_video_div" style="display:<?php if($startWithLib==1) echo 'none'; else echo 'block'?>; width:<?php echo $theme->appWidth; ?>px; height:<?php echo $theme->appHeight; ?>px; background-color:<?php echo "#".$theme->vidBgColor; ?>">
		
		<!-- 1.1. Play_list -->
		<div id="comp_spvp_playlist">
			<input type='hidden' value='0' id="track_list" />
			<div style="height:90%">
				<div onmousedown="scrolltp2=setInterval('scrollTop2()', 30)" onmouseup="clearInterval(scrolltp2)" ontouchstart="scrolltp2=setInterval('scrollTop2()', 30)" ontouchend="clearInterval(scrolltp2)" style="overflow:hidden; text-align:center;width:<?php echo $theme->playlistWidth; ?>px; height:20px">
					<img src="<?php echo $cmpnt_img_path; ?>/top.png" style="cursor:pointer;" id="button20" />
				</div>
				
				<div style="height:<?php echo $theme->appHeight-40; ?>px; overflow:hidden" >
					<?php
					for($i=0;$i<count($comp_playlist_array)-1;$i++) {
						$play->load($comp_playlist_array[$i]);
						
						$comp_v_ids = explode(',',$play->videos);
						$vi_ids = substr($play->videos,0,-1);

						if($i!=0)
							echo '<table border="1" id="comp_track_list_'.$i.'" style="display:none; height:100%; width:100%; border-spacing:0px; border:none; border-collapse:inherit;" >';
						else
							echo '<table border="1" id="comp_track_list_'.$i.'" style="display:block; height:100%; width:100%; border-spacing:0px; border:none; border-collapse:inherit;" >';

						echo '<tr>
							<td style="text-align:left;border:0px solid grey;width:100%;vertical-align:top;">
							<div id="comp_scroll_div2_'.$i.'" class="playlist_values" style="position:relative">';
							$jj = 0;
						
						for($j=0;$j<count($comp_v_ids)-1;$j++) {
							$comp_vds->load($comp_v_ids[$j]);
							
							if($comp_vds->type=="http" || $comp_vds->type=="youtube" || $comp_vds->type=="rtmp") {
								/*-=-=- Video URL -=-=-*/
								if($comp_vds->urlHtml5!='') { 
									if(strpos($comp_vds->urlHtml5, "http:")===false and strpos($comp_vds->urlHtml5, "https:")===false )
										$html5Url = JURI::root().'administrator/'.$comp_vds->urlHtml5;
									else
										$html5Url = $comp_vds->urlHtml5;
								}
								elseif (($comp_vds->urlHtml5 == "" || !strpos($comp_vds->url, 'embed')) && $comp_vds->type != "rtmp") {
									if ($comp_vds->type == "youtube") {
										$html5Url = "https://www.youtube.com/embed/" . substr($comp_vds->url, strpos($comp_vds->url, '?v=') + 3, 11) . "?enablejsapi=1&html5=1&controls=1&modestbranding=1&rel=0";
									}
									else $html5Url = JURI::root().'administrator/'.$comp_vds->url;
								}
								else
									$html5Url = $comp_vds->urlHtml5;
							
								/*-=-=- Thumbnail URL -=-=-*/
								if(strpos($comp_vds->thumb, "http:")===false and strpos($comp_vds->thumb, "https:")===false )
									$vidsTHUMB = JURI::root().'administrator/'.$comp_vds->thumb;
								else
									$vidsTHUMB = $comp_vds->thumb;
								
								/*-=-=- Video HD URL -=-=-*/
								if($comp_vds->urlHdHtml5!='') {
									if(strpos($comp_vds->urlHdHtml5, "http:")===false and strpos($comp_vds->urlHdHtml5, "https:")===false )
										$html5UrlHD = JURI::root().'administrator/'.$comp_vds->urlHdHtml5;
									else
										$html5UrlHD = $comp_vds->urlHdHtml5;
								}
								elseif (($comp_vds->urlHtml5 == "" || !strpos($comp_vds->url, 'embed')) && $comp_vds->type != "rtmp") {
									if ($comp_vds->type == "youtube") {
										$html5UrlHD = "https://www.youtube.com/embed/" . substr($comp_vds->url, strpos($comp_vds->url, '?v=') + 3, 11) . "?enablejsapi=1&html5=1&controls=1&modestbranding=1&rel=0";
									}
									else $html5UrlHD = JURI::root().'administrator/'.$comp_vds->urlHD;									
								}
								else
									$html5UrlHD = $comp_vds->urlHD;
							
								echo '<div id="thumb_'.$jj.'"  onclick="jQuery(\'#comp_HD_on\').val(0);document.getElementById(\'comp_videoID\').src=\''.$html5Url.'\';document.getElementById(\'comp_videoID\').poster=\''.$vidsTHUMB.'\';comp_vid_select(this);comp_vid_num='.$jj.';jQuery(\'#_track\').val('.$jj.');" class="comp_vid_thumb" style="color:#'.$theme->textColor .';cursor:pointer;width:'.$theme->playlistWidth.'px;text-align:center"  >';
								if($comp_vds->thumb)
									echo '<img src="'.$vidsTHUMB.'" width="90px" style="display:none;"  />';
								if($theme->show_trackid==1)
									echo '<p style="font-size:'.$theme->playlistTextSize.'px;line-height:30px;cursor:pointer;" >'.($jj+1).'-'.$comp_vds->title.'</p>';
								else
									echo '<p style="font-size:'.$theme->playlistTextSize.'px;line-height:30px;cursor:pointer;" >'.$comp_vds->title.'</p>';
								echo '</div>';
								echo '<input type="hidden" id="urlHD_'.$jj.'" value="'.$html5UrlHD.'" />';
								echo '<input type="hidden" id="vid_type_'.$jj.'" value="'.$comp_vds->type.'" />';
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
		
		<?php
		/*-=-=- Playlist -=-=-*/
		if($typeselect == 0) { 
			if($comp_vds->type=="youtube" || $comp_vds->type=="rtmp"){ ?>
				<div id="spvp_single_video_main">
					<iframe class="spvp_single_video_main" id="comp_videoID" type="text/html" width="<?php echo $theme->appWidth ?>" height="<?php echo $theme->appHeight; ?>" src="<?php echo $html5Url; ?>?enablejsapi=1&version=3&playerapiid=ytplayer&modestbranding=1&rel=0" frameborder="0" allowfullscreen></iframe> 
				</div>
				<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px; left:10px;" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
			<?php }
			else { ?>
				<video poster="<?php echo $vidsTHUMB; ?>" src="<?php echo $html5Url; ?>" ontimeupdate="comp_timeUpdate()" ondurationchange="comp_durationChange();" id="comp_videoID" style="position:absolute" width="<?php echo $theme->appWidth; ?>" height="<?php echo $theme->appHeight; ?>" >
					<p>Your browser does not support the video tag.</p>  
				</video>
				<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px;" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
			<?php }
		}		
		/*-=-=- single -=-=-*/
		else {
			if($comp_single_video_type=="youtube" || $comp_single_video_type=="rtmp"){ ?>
				<div id="spvp_single_video_main">
					<iframe class="spvp_single_video_main" id="comp_videoID" type="text/html" width="<?php echo $theme->appWidth ?>" height="<?php echo $theme->appHeight; ?>" src="<?php echo substr($track_URL,0,  strpos($track_URL, "?"));?>?enablejsapi=1&version=3&playerapiid=ytplayer&modestbranding=1&rel=0" frameborder="0" allowfullscreen></iframe> 
				</div>
				<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px;left:10px" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
			<?php }
			else { ?>
				<video poster="<?php echo $track_poster ?>" src="<?php echo $track_URL ?>" ontimeupdate="comp_timeUpdate()" ondurationchange="comp_durationChange();" id="comp_videoID" style="position:absolute" width="<?php echo $theme->appWidth; ?>" height="<?php echo $theme->appHeight; ?>" >
					<p>Your browser does not support the video tag.</p>  
				</video>
				<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px;" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
			<?php }
		} ?>		
		
		<!-- 1.3. Control Buttons -->
		<div class="comp_spvp_control" id="comp_spvp_control" style="display:none;overflow:hidden">
			<?php 
			if($theme->ctrlsPos==2){ 
				if($typeselect==0){
					if($comp_vds->type=="http"){ ?>
						<div class="progressBar">
							<div class="timeBar"></div>
							<div class="bufferBar"></div>
						</div>
					<?php
					} else echo "";
				}
				else {
					if($comp_single_video_type=="http"){ ?>
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
			echo '<table width="'.$theme->appWidth.'" id="comp_control_btns"><tr>';
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
						echo '<div id="timeBarNum"><span style="color:#'.$theme->ctrlsMainColor.';opacity:'.$theme->ctrlsMainAlpha/100 .'" id="comp_time">00:00 </span> <span style="color:#'.$theme->ctrlsMainColor.';opacity:'.$theme->ctrlsMainAlpha/100 .'">/</span> <span style="color:#'.$theme->ctrlsMainColor.';opacity:'.$theme->ctrlsMainAlpha/100 .'" id="comp_duration">00:00</span></div>';
					}
					else if($ctrl[0]=='vol') {
						echo '<table id="comp_volumeTD">
						<tr>
							<td id="voulume_img" >
								<img  style="cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"  id="button'.$y.'"    src="'.$cmpnt_img_path.'/vol.png"  />
							</td>
							<td id="volumeTD2" style="width:0px !important">
								<span id="volumebar_player" class="comp_volumeBar">
									<span class="comp_volume"></span>
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
						echo '<img  id="button'.$y.'" class="comp_repeat" style="cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"   src="'.$cmpnt_img_path.'/repeat.png" />';
						echo '<img  id="button'.($y+1).'"  class="comp_repeat" style="display:none;cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"   src="'.$cmpnt_img_path.'/repeatOff.png" />';
						echo '<img  id="button'.($y+2).'"  class="comp_repeat" style="display:none;cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"  src="'.$cmpnt_img_path.'/repeatOne.png" />';
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
	<div id="comp_spvp_album_div" style="display:<?php if($startWithLib==0) echo 'none' ?>;background-color:<?php echo "#".$theme->appBgColor;?>;height:100%; overflow:hidden;position:relative;">
		<table width="100%" height="100%" style="border-collapse: inherit; border:none;">
			<tr id="tracklist_up" style="display:none">
				<td height="12px" colspan="2" style="text-align:right;border:none;">
					<div onmouseover="this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2))?>,0.4)'" onmouseout="this.style.background='none'" id="scroll" style="overflow:hidden;width:50%;height:100%;text-align:center;float:right;cursor:pointer;" onmousedown="scrolltp=setInterval('scrollTop()', 30)" onmouseup="clearInterval(scrolltp)" ontouchstart="scrolltp=setInterval('scrollTop()', 30)" ontouchend="clearInterval(scrolltp)">
					<img  src="<?php echo $cmpnt_img_path; ?>/top.png" style="cursor:pointer;" id="button25" /></div>
				</td>
			</tr>			
			<tr>
				<td style="vertical-align:middle;border:none;">
					<img src="<?php echo $cmpnt_img_path; ?>/prev.png" style="cursor:pointer;" id="button28" onclick="comp_prevPage();" />
				</td>				
				<td style="width: 93%;border:none;">
					<?php
					for($l=0;$l<$table_count;$l++) {
						echo '<table id="comp_lib_table_'.$l.'" '.$display.'> ';
						for($i=0; $i<$libRows; $i++) {
							echo '<tr>';
							for($j=0; $j<$libCols; $j++) {
								if($p<count($comp_playlist_array)-1) {
									$play->load($comp_playlist_array[$p]);
									if(strpos($play->thumb, "http:")===false and strpos($play->thumb, "https:")===false )
										$playTHUMB = JURI::root().'administrator/'.$play->thumb;
									else
									$playTHUMB = $play->thumb;
				
									echo '<td class="playlist_td" id="comp_playlist_'.$p.'" onclick="comp_openPlaylist('.$p.','.$l.')" onmouseover="this.style.color=\'#'.$theme->textHoverColor .'\';this.style.background=\'rgba('.HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)).',0.4)\'" onmouseout="this.style.color=\'#'.$theme->textColor .'\';this.style.background=\' none\'" onclick="" style="color:#'.$theme->textColor .';border:1px solid white;vertical-align:center; text-align:center;width:'.$cellWidth.';height:'.$cellHeight.';cursor:pointer">';
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
						if($p<count($comp_playlist_array)-1) {
							$table_count = $table_count+1;
							$display = 'style="display:none;width:100%;height:100%;border-collapse: collapse;"';
						}
						echo '</table>';
					}

					for($i=0;$i<$p;$i++) {
						$play->load($comp_playlist_array[$i]);
						$comp_v_ids = explode(',',$play->videos);
						$vi_ids = substr($play->videos,0,-1);
						/*$query = "SELECT url,type,title FROM #__spidervideoplayer_video WHERE id IN ($vi_ids)";
						$db->setQuery( $query);
						$comp_videos = $db->loadAssocList();*/
						if(strpos($play->thumb, "http:")===false and strpos($play->thumb, "https:")===false )
							$playTHUMB = JURI::root().'administrator/'.$play->thumb;
						else
							$playTHUMB = $play->thumb;

						echo '<table playlist_id="'.$i.'" id="comp_playlist_table_'.$i.'" style="border:none; border-collapse:inherit; display:none; height:100%; width:100%" >
							<tr>
								<td style="text-align:center;vertical-align:top;border:none;">';
									if($play->thumb!='')
										echo '<img src="'.$playTHUMB.'" width="70%" /><br /><br />';
									echo '<p style="color:#'.$theme->textColor .';font-size:'.$theme->libDetailsTextSize.'px">'.$play->title.'</p>';
								echo '</td>
								<td style="width:50%;border:none;">
									<div style="width:100%;text-align:left;border:1px solid white;height:'.($theme->appHeight-55).'px;vertical-align:top;position:relative;overflow:hidden">
										<div id="comp_scroll_div_'.$i.'" style="position:relative;">';
										$jj=0;
										for($j=0;$j<count($comp_v_ids)-1;$j++) {
											$comp_vds->load($comp_v_ids[$j]);
											if($comp_vds->type=="http" || $comp_vds->type=="youtube" || $comp_vds->type=="rtmp") {
												echo '<p class="vid_title" ondblclick="jQuery(\'#comp_spvp_album_div .show_vid\').click()" onmouseover="this.style.color=\'#'.$theme->textHoverColor .'\';this.style.background=\'rgba('.HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)).',0.4)\'" onmouseout="this.style.color=\'#'.$theme->textColor .'\';this.style.background=\' none\'" style="color:#'.$theme->textColor .';font-size:'.$theme->libDetailsTextSize.'px;line-height:30px;cursor:pointer" onclick="jQuery(\'#comp_HD_on\').val(0);jQuery(\'#comp_track_list_'.$i.'\').find(\'.comp_vid_thumb\')['.$jj.'].click();comp_playBTN();comp_current_playlist_videos();comp_vid_num='.$jj.';jQuery(\'#comp_current_track\').val('.$jj.');copm_vid_select2(this);comp_playlist_select('.$i.') ">'.($jj+1).' - '.$comp_vds->title.'</p>';
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
								<img src="<?php echo $cmpnt_img_path; ?>/next.png" style="cursor:pointer;" id="button27" onclick="comp_nextPage()" />
							</td>
						</tr>
						<tr>
							<td style="border:none;">
								<img src="<?php echo $cmpnt_img_path; ?>/back.png" style="cursor:pointer;display:none" id="button29" onclick="comp_openLibTable()" />
							</td>
						</tr>
						<tr>
							<td style="border:none;"> 
								<img src="<?php echo $cmpnt_img_path; ?>/lib.png" style="cursor:pointer" id="button19" class="show_vid"    />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr id="tracklist_down" style="display:none" >
				<td height="22px" colspan="2" style="text-align:right;border:none;">
					<div  onmouseover="this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2))?>,0.4)'" onmouseout="this.style.background='none'" id="scroll" style="overflow:hidden;width:50%;height:100%;text-align:center;float:right;cursor:pointer;" onmousedown="scrollBot=setInterval('comp_scrollBottom()', 30)" onmouseup="clearInterval(scrollBot)" ontouchstart="scrollBot=setInterval('comp_scrollBottom()', 30)" ontouchend="clearInterval(scrollBot)">
						<img src="<?php echo $cmpnt_img_path; ?>/bot.png" style="cursor:pointer;" id="button26"  />
					</div>
				</td>
			</tr>
		</table>
	</div>
	
	<!-- 3. Share_buttons -->
	<?php if($theme->ctrlsPos==1) $share_top= '-'.$theme->appHeight+22; else $share_top='-133' ?>
	<div id="comp_share_buttons" style="text-align:center; height:108px; width:30px; background-color:rgba(0,0,0,0.5); position:relative; z-index:200; top:<?php echo $share_top ?>px; display:none;" >
		<img onclick = "flashShare('fb',document.getElementById('comp_current_playlist_table').value,document.getElementById('comp_current_track').value)" style="cursor:pointer"  src="<?php echo $cmpnt_img_path; ?>/facebook.png" /><br>
		<img onclick = "flashShare('tw',document.getElementById('comp_current_playlist_table').value,document.getElementById('comp_current_track').value)" style="cursor:pointer" src="<?php echo $cmpnt_img_path; ?>/twitter.png" /><br>
		<img onclick = "flashShare('g',document.getElementById('comp_current_playlist_table').value,document.getElementById('comp_current_track').value)" style="cursor:pointer" src="<?php echo $cmpnt_img_path; ?>/googleplus.png" /><br>
		<img onclick="jQuery('#embed_Url_div').css('display','');embed_url(document.getElementById('comp_current_playlist_table').value,document.getElementById('comp_current_track').value)" style="cursor:pointer" src="<?php echo $cmpnt_img_path; ?>/embed.png" />
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
<input type="hidden" id="comp_current_track" value="0" />
<input type="hidden" id="comp_shuffle" value="<?php echo $shuffle ?>" />
<input type="hidden" id="comp_scroll_height" value="0" />
<input type="hidden" id="comp_scroll_height2" value="0" />
<input type="hidden" value="<?php echo $l ?>" id="comp_lib_table_count"/>
<input type="hidden" value="" id="comp_current_lib_table"/>
<input type="hidden" value="0" id="comp_current_playlist_table"/>
<input type="hidden" value="<?php echo $theme->defaultRepeat ?>" id="comp_repeat"/>
<input type="hidden" value="0" id="comp_HD_on"/>
<input type="hidden" value="" id="comp_volumeBar_width"/>


<script>
var compVideo = document.getElementById('comp_videoID'); //or
var compVideo = jQuery('#comp_videoID').get(0); //or
var compVideo = jQuery('#comp_videoID')[0];

/*-=-=- return a jQuery object -=-=-*/
var compVideo = jQuery('#comp_videoID');
var paly_comp = jQuery('#comp_control_btns .btnPlay');
var pause_comp = jQuery('#comp_control_btns .btnPause');

<?php 
if($typeselect == 0){
	if($comp_vds->type=="youtube" || $comp_vds->type=="rtmp" ){ ?> 
		jQuery("#comp_control_btns .btnPlay, #comp_control_btns .btnPause, #comp_control_btns #comp_volumeTD, #comp_control_btns #timeBarNum, #comp_control_btns .fullScreen, #comp_control_btns .hd").css('display',"none");
		jQuery("#comp_spvp_control").addClass('youRtmp');
	<?php }
	else { ?>
		jQuery("#comp_spvp_control").removeClass('youRtmp');
	<?php }
}
if($typeselect == 1){ 
	if($comp_single_video_type=="youtube" || $comp_single_video_type=="rtmp"){ ?>
		jQuery("#comp_control_btns .playPrev, #comp_control_btns .playNext").css('display',"none");
		jQuery("#comp_spvp_control").addClass('singYouRtmp');
		jQuery("#comp_control_btns .btnPlay, #comp_control_btns .btnPause, #comp_volumeTD, #timeBarNum, #comp_control_btns .fullScreen, #comp_control_btns .hd").css('display',"none");
		jQuery("#comp_spvp_control").addClass('youRtmp');
	<?php 
	}
	else { ?>
		jQuery("#comp_control_btns .playPrev, #comp_control_btns .playNext").css('display',"none");
		jQuery("#comp_spvp_control").removeClass('youRtmp');
	<?php }
}
?> 

function embed_url(a,b) {
	jQuery('#embed_Url').html('<iframe allowFullScreen allowTransparency="true" frameborder="0" width="<?php echo $theme->appWidth ?>" height="<?php echo $theme->appHeight ?>" src="'+location.href+'&AlbumId='+a+'&TrackId='+b+'&tmpl=component" type="text/html" ></iframe>')
	jQuery('#embed_Url').focus(); jQuery('#embed_Url').select();
}

jQuery('#comp_control_btns .share, #comp_share_buttons').on('mouseenter',function(){
	left = jQuery('#comp_control_btns .share').position().left+<?php echo ($theme->appWidth-240); ?>;
	if(parseInt(jQuery('#comp_spvp_playlist').css('width'))==0){
		jQuery('#comp_share_buttons').css('left',left);
	}
	else {
		<?php if ($theme->playlistPos==1){ ?>
			jQuery('#comp_share_buttons').css('left',left+<?php echo $theme->playlistWidth ?>);
		<?php } else { ?>
			jQuery('#comp_share_buttons').css('left',left);
		<?php } ?>
	}
	jQuery('#comp_share_buttons').css('display','');
})

jQuery('#comp_control_btns .share, #comp_share_buttons').on('mouseleave',function(){
	jQuery('#comp_share_buttons').css('display','none')
})

if(<?php echo $theme->autoPlay ?>==1) {
	setTimeout(function(){jQuery('#thumb_0').click()},500);
	<?php if($typeselect==1)  { ?>
		setTimeout(function(){compVideo[0].click()},500);
	<?php } ?>
}

<?php if($theme->defaultShuffle=='shuffleOff') { ?>
	if(jQuery('#comp_control_btns .shuffle')[0]) {
		jQuery('#comp_control_btns .shuffle')[0].style.display = "none";
		jQuery('#comp_control_btns .shuffle')[1].style.display = "";
	}
<?php
} else { ?>
if(jQuery('#comp_control_btns .shuffle')[0]) {
	jQuery('#comp_control_btns .shuffle')[1].style.display = "none";
	jQuery('#comp_control_btns .shuffle')[0].style.display = "";
}
<?php } ?>

jQuery('#comp_control_btns .fullScreen').on('click',function(){
	if(compVideo[0].mozRequestFullScreen)
		compVideo[0].mozRequestFullScreen();
	if(compVideo[0].webkitEnterFullscreen)
		compVideo[0].webkitEnterFullscreen()
	if(compVideo[0].msRequestFullscreen)
		compVideo[0].msRequestFullscreen()
})

jQuery('.stop').on('click',function(){
	compVideo[0].currentTime = 0;
	compVideo[0].pause();
	paly_comp.css('display',"block");
	pause_comp.css('display',"none");
})

<?php if($theme->defaultRepeat=='repeatOff'){ ?>
	if(jQuery('#comp_control_btns .comp_repeat')[0]) {
		jQuery('#comp_control_btns .comp_repeat')[0].style.display = "none";
		jQuery('#comp_control_btns .comp_repeat')[1].style.display = "";
		jQuery('#comp_control_btns .comp_repeat')[2].style.display = "none";
	}
<?php } ?>

<?php if($theme->defaultRepeat=='repeatOne'){ ?>
	if(jQuery('#comp_control_btns .comp_repeat')[0]) {
		jQuery('#comp_control_btns .comp_repeat')[0].style.display = "none";
		jQuery('#comp_control_btns .comp_repeat')[1].style.display = "none";
		jQuery('#comp_control_btns .comp_repeat')[2].style.display = "";
	}
<?php } ?>

<?php if($theme->defaultRepeat=='repeatAll'){ ?>
	if(jQuery('#comp_control_btns .comp_repeat')[0]) {
		jQuery('#comp_control_btns .comp_repeat')[0].style.display = "";
		jQuery('#comp_control_btns .comp_repeat')[1].style.display = "none";
		jQuery('#comp_control_btns .comp_repeat')[2].style.display = "none";
	}
<?php } ?>

jQuery('#comp_control_btns .comp_repeat').on('click',function(){
	comp_repeat = jQuery('#comp_repeat').val();
	switch (comp_repeat) {
		case 'repeatOff':
			jQuery('#comp_repeat').val('repeatOne');
			jQuery('#comp_control_btns .comp_repeat')[0].style.display = "none";
			jQuery('#comp_control_btns .comp_repeat')[1].style.display = "none";
			jQuery('#comp_control_btns .comp_repeat')[2].style.display = "";
		break;
		case 'repeatOne':
			jQuery('#comp_repeat').val('repeatAll');
			jQuery('#comp_control_btns .comp_repeat')[0].style.display="";
			jQuery('#comp_control_btns .comp_repeat')[1].style.display="none";
			jQuery('#comp_control_btns .comp_repeat')[2].style.display="none";
		break;
		case 'repeatAll':
			jQuery('#comp_repeat').val('repeatOff');
			jQuery('#comp_control_btns .comp_repeat')[0].style.display="none";
			jQuery('#comp_control_btns .comp_repeat')[1].style.display="";
			jQuery('#comp_control_btns .comp_repeat')[2].style.display="none";
		break;
	}
})

jQuery('#comp_volumeTD #voulume_img').on('click',function(){
	if(jQuery('#comp_volumeTD .comp_volume')[0].style.width!='0%') {
		compVideo[0].comp_volume = 0;
		jQuery('#comp_volumeBar_width').val(jQuery('#comp_volumeTD .comp_volume')[0].style.width)
		jQuery('#comp_volumeTD .comp_volume').css('width','0%')
	}
	else {
		compVideo[0].comp_volume = parseInt(jQuery('#comp_volumeBar_width').val())/100;
		jQuery('#comp_volumeTD .comp_volume').css('width',jQuery('#comp_volumeBar_width').val())
	}
})

jQuery('#comp_control_btns .hd').on('click',function(){
	comp_current_time = compVideo[0].currentTime;
	comp_HD_on=jQuery('#comp_HD_on').val();
	comp_current_playlist_table=jQuery('#comp_current_playlist_table').val();
	comp_current_track=jQuery('#comp_current_track').val();

	if(jQuery('#comp_track_list_'+comp_current_playlist_table).find('#urlHD_'+comp_current_track).val() && comp_HD_on==0) {
		document.getElementById('comp_videoID').src=jQuery('#comp_track_list_'+comp_current_playlist_table).find('#urlHD_'+comp_current_track).val();
		play();
		setTimeout('compVideo[0].currentTime=comp_current_time',500)
		jQuery('#comp_HD_on').val(1);
	}
	if(jQuery('#comp_track_list_'+comp_current_playlist_table).find('#urlHD_'+comp_current_track).val() && comp_HD_on==1) {
		jQuery('#comp_track_list_'+comp_current_playlist_table).find('#thumb_'+comp_current_track).click();
		setTimeout('compVideo[0].currentTime=comp_current_time',500)
		jQuery('#comp_HD_on').val(0);
	}
})

function support(i,j) {
	if(jQuery('#comp_track_list_'+i).find('#vid_type_'+j).val()!='http') {
		jQuery('#not_supported').css('display','');
		jQuery('#support').val(0);
	}
	else {
		jQuery('#not_supported').css('display','none');
		jQuery('#support').val(1);
	}
}

jQuery('.play').on('click',function(){  compVideo[0].play();  })
jQuery('.pause').on('click',function(){ compVideo[0].pause(); })

/*-=-=- single video thumb -=-=-*/
jQuery(function() {
	var comp_videos  = jQuery("#spvp_single_video_main");
	comp_videos.on('click', function(ev) {
		jQuery(this).addClass("player");
		jQuery(this).find('.spvp_single_video_main')[0].src += "&autoplay=1";
		ev.preventDefault();
	});
});

function comp_vid_select(x){
	jQuery("div.comp_vid_thumb").each(function(){
		if(jQuery(this).find("img")) {
			jQuery(this).find("img").hide(20);
		if(jQuery(this).find("img")[0])
			jQuery(this).find("img")[0].style.display="none";
		}	
		jQuery(this).css('background','none');
	})

	jQuery("div.comp_vid_thumb").each(function(){
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

function copm_vid_select2(x){
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

function comp_playlist_select(x) {
	jQuery("#comp_spvp_album_div td.playlist_td").each(function(){
		jQuery(this).css('background','none');
		jQuery(this).css('color','#<?php echo $theme->textColor  ?>');
		this.onmouseover = function(){this.style.color='#'+'<?php echo $theme->textHoverColor?>' ;this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)) ?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)) ?>,0.4)'}
		this.onmouseout = function(){this.style.color='<?php echo '#'.$theme->textColor ?>';this.style.background=" none"}
	})

	jQuery('#comp_playlist_'+x).css('background','rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>)')
	jQuery('#comp_playlist_'+x).css('color','#<?php echo $theme->textSelectedColor  ?>')
	jQuery('#comp_playlist_'+x)[0].onmouseover = null
	jQuery('#comp_playlist_'+x)[0].onmouseout = null
}

jQuery('#comp_control_btns .shuffle').on('click', function() {
	if(jQuery('#comp_shuffle').val()==0) {
		jQuery('#comp_shuffle').val(1);
		jQuery('#comp_control_btns .shuffle')[1].style.display="none";
		jQuery('#comp_control_btns .shuffle')[0].style.display="";
	}
	else {
		jQuery('#comp_shuffle').val(0);
		jQuery('#comp_control_btns .shuffle')[0].style.display="none";
		jQuery('#comp_control_btns .shuffle')[1].style.display="";
	}
});

jQuery("div.comp_vid_thumb").each(function(){
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

function comp_timeUpdate(){
	if(parseInt(document.getElementById('comp_videoID').currentTime/60)<10 && parseInt(document.getElementById('comp_videoID').currentTime % 60<10))
		document.getElementById('comp_time').innerHTML = '0'+parseInt(document.getElementById('comp_videoID').currentTime/60)+':0'+parseInt(document.getElementById('comp_videoID').currentTime % 60);

	if(parseInt(document.getElementById('comp_videoID').currentTime/60)<10)
		document.getElementById('comp_time').innerHTML = '0'+parseInt(document.getElementById('comp_videoID').currentTime/60)+':'+parseInt(document.getElementById('comp_videoID').currentTime % 60);

	if(parseInt(document.getElementById('comp_videoID').currentTime % 60)<10)
		document.getElementById('comp_time').innerHTML = '0'+parseInt(document.getElementById('comp_videoID').currentTime/60)+':0'+parseInt(document.getElementById('comp_videoID').currentTime % 60);
}

function comp_durationChange() {
	if(parseInt(document.getElementById('comp_videoID').duration/60)<10 && parseInt(document.getElementById('comp_videoID').duration % 60<10))
		document.getElementById('comp_duration').innerHTML = '0'+parseInt(document.getElementById('comp_videoID').duration/60)+':0'+parseInt(document.getElementById('comp_videoID').duration % 60);

	if(parseInt(document.getElementById('comp_videoID').duration/60)<10)
		document.getElementById('comp_duration').innerHTML = '0'+parseInt(document.getElementById('comp_videoID').duration/60)+':'+parseInt(document.getElementById('comp_videoID').duration % 60);

	if(parseInt(document.getElementById('comp_videoID').duration % 60)<10)
		document.getElementById('comp_duration').innerHTML = parseInt(document.getElementById('comp_videoID').duration/60)+':0'+parseInt(document.getElementById('comp_videoID').duration % 60);
}

function comp_scrollBottom(){
	comp_current_playlist_table = document.getElementById('comp_current_playlist_table').value;
	if(document.getElementById('comp_scroll_div_'+comp_current_playlist_table).offsetHeight+parseInt(document.getElementById("comp_scroll_div_"+comp_current_playlist_table).style.top)+55<=document.getElementById('comp_spvp_global_body').offsetHeight)
		return false;
	document.getElementById('comp_scroll_height').value = parseInt(document.getElementById('comp_scroll_height').value)+5
	document.getElementById("comp_scroll_div_"+comp_current_playlist_table).style.top="-"+document.getElementById('comp_scroll_height').value+"px";
};
function scrollTop(){
	comp_current_playlist_table = document.getElementById('comp_current_playlist_table').value;
	if(document.getElementById('comp_scroll_height').value<=0)
		return false;
	document.getElementById('comp_scroll_height').value = parseInt(document.getElementById('comp_scroll_height').value)-5
	document.getElementById("comp_scroll_div_"+comp_current_playlist_table).style.top="-"+document.getElementById('comp_scroll_height').value+"px";
};

function scrollBottom2(){
	comp_current_playlist_table = document.getElementById('comp_current_playlist_table').value;
	if(!comp_current_playlist_table) {
		comp_current_playlist_table = 0;
	}
	if(document.getElementById('comp_scroll_div2_'+comp_current_playlist_table).offsetHeight+parseInt(document.getElementById("comp_scroll_div2_"+comp_current_playlist_table).style.top)+150<=document.getElementById('comp_spvp_global_body').offsetHeight)
		return false;
	document.getElementById('comp_scroll_height2').value = parseInt(document.getElementById('comp_scroll_height2').value)+5
	document.getElementById("comp_scroll_div2_"+comp_current_playlist_table).style.top = "-"+document.getElementById('comp_scroll_height2').value+"px";
};
function scrollTop2(){
	comp_current_playlist_table = document.getElementById('comp_current_playlist_table').value;
	if(document.getElementById('comp_scroll_height2').value<=0)
		return false;
	document.getElementById('comp_scroll_height2').value = parseInt(document.getElementById('comp_scroll_height2').value)-5
	document.getElementById("comp_scroll_div2_"+comp_current_playlist_table).style.top = "-"+document.getElementById('comp_scroll_height2').value+"px";
};

function comp_openPlaylist(i,j) {
	document.getElementById('comp_scroll_height').value = 0;
	comp_lib_table_count = document.getElementById('comp_lib_table_count').value;
	for(lib_table=0;lib_table<comp_lib_table_count;lib_table++) {
		document.getElementById('comp_lib_table_'+lib_table).style.display = "none";
	}
	jQuery("#comp_playlist_table_"+i).fadeIn(700);
	document.getElementById('comp_current_lib_table').value = j;
	document.getElementById('comp_current_playlist_table').value = i;
	document.getElementById('tracklist_down').style.display = "" ;
	document.getElementById('tracklist_up').style.display = "";
	document.getElementById('button29').style.display = "block";
	document.getElementById('button27').onclick = function(){nextPlaylist()};
	document.getElementById('button28').onclick = function(){prevPlaylist()};
}
function nextPlaylist(){
	document.getElementById('comp_scroll_height').value = 0;
	comp_lib_table_count = document.getElementById('comp_lib_table_count').value;
	for(lib_table=0;lib_table<comp_lib_table_count;lib_table++){
		document.getElementById('comp_lib_table_'+lib_table).style.display="none";
	}
	comp_current_lib_table = document.getElementById('comp_current_lib_table').value;
	next_playlist_table = parseInt(document.getElementById('comp_current_playlist_table').value)+1;
	comp_current_playlist_table = parseInt(document.getElementById('comp_current_playlist_table').value);
	if(next_playlist_table><?php echo count($comp_playlist_array)-2 ?>)
		return false;
	jQuery("#comp_playlist_table_"+comp_current_playlist_table).css('display','none');
	jQuery("#comp_playlist_table_"+next_playlist_table).fadeIn(700);

	document.getElementById('comp_current_playlist_table').value = next_playlist_table;
	document.getElementById('tracklist_down').style.display="" ;
	document.getElementById('tracklist_up').style.display="";
	document.getElementById('button29').style.display="block";
}
function prevPlaylist(){
	document.getElementById('comp_scroll_height').value = 0;
	comp_lib_table_count = document.getElementById('comp_lib_table_count').value;
	for(lib_table=0;lib_table<comp_lib_table_count;lib_table++)	{
		document.getElementById('comp_lib_table_'+lib_table).style.display="none";
	}
	comp_current_lib_table = document.getElementById('comp_current_lib_table').value;
	prev_playlist_table = parseInt(document.getElementById('comp_current_playlist_table').value)-1;
	comp_current_playlist_table = parseInt(document.getElementById('comp_current_playlist_table').value);
	if(prev_playlist_table<0)
		return false;
	jQuery("#comp_playlist_table_"+comp_current_playlist_table).css('display','none');
	jQuery("#comp_playlist_table_"+prev_playlist_table).fadeIn(700);

	document.getElementById('comp_current_playlist_table').value = prev_playlist_table;
	document.getElementById('tracklist_down').style.display = "" ;
	document.getElementById('tracklist_up').style.display = "";
	document.getElementById('button29').style.display = "block";
}

function comp_openLibTable() {
	comp_current_lib_table = document.getElementById('comp_current_lib_table').value;
	document.getElementById('comp_scroll_height').value = 0;
	comp_current_playlist_table = document.getElementById('comp_current_playlist_table').value;
	jQuery("#comp_lib_table_"+comp_current_lib_table).fadeIn(700);
	document.getElementById('comp_playlist_table_'+comp_current_playlist_table).style.display = "none";
	document.getElementById('tracklist_down').style.display = "none" ;
	document.getElementById('tracklist_up').style.display = "none";
	document.getElementById('button29').style.display = "none";
	document.getElementById('button27').onclick = function(){comp_nextPage()};
	document.getElementById('button28').onclick = function(){comp_prevPage()};
}

var next_page = 0;
function comp_nextPage() {
	if(next_page==document.getElementById('comp_lib_table_count').value-1)
	return false;
	next_page = next_page+1;
	for(g=0; g<document.getElementById('comp_lib_table_count').value; g++){
		document.getElementById('comp_lib_table_'+g).style.display = "none";
		if(g==next_page) {
			jQuery("#comp_lib_table_"+g).fadeIn(900);
		}
	}
}
function comp_prevPage() {
	if(next_page==0)
		return false;
	next_page = next_page-1;
	for(g=0; g<document.getElementById('comp_lib_table_count').value; g++) {
		document.getElementById('comp_lib_table_'+g).style.display = "none";
		if(g==next_page) {
			jQuery("#comp_lib_table_"+g).fadeIn(900);
		}
	}
}

function comp_playBTN() {
	comp_current_playlist_table = document.getElementById('comp_current_playlist_table').value;
	track_list = document.getElementById('track_list').value;
	document.getElementById('comp_track_list_'+comp_current_playlist_table).style.display = "block";
	if(comp_current_playlist_table!=track_list)
		document.getElementById('comp_track_list_'+track_list).style.display = "none";
	
	document.getElementById('track_list').value = comp_current_playlist_table;
	compVideo[0].play();
	paly_comp.css('display',"none");
	pause_comp.css('display',"block");
}

function play() {
	compVideo[0].play();
	paly_comp.css('display',"none");
	pause_comp.css('display',"block");
}

/*-=-=- Play/Pause control clicked -=-=-*/
jQuery('#comp_control_btns .btnPlay <?php if($theme->clickOnVid==1) echo ',#comp_videoID' ?>, #comp_control_btns .btnPause').on('click', function() {  
	if(compVideo[0].paused) {
		compVideo[0].play();
		paly_comp.css('display',"none");
		pause_comp.css('display',"block");
	}
	else {
      compVideo[0].pause();
	  paly_comp.css('display',"block");
	  pause_comp.css('display',"none");
	}
	return false;
});


function check_volume() {
	comp_percentage = compVideo[0].comp_volume * 100;
	jQuery('#comp_volumeTD .comp_volume').css('width', comp_percentage+'%');
	document.getElementById("comp_spvp_playlist").style.width = '0px';
	document.getElementById("comp_spvp_playlist").style.display = 'none';
}
window.onload = check_volume();

/*-=-=- get HTML5 video time duration -=-=-*/
compVideo.on('loadedmetadata', function() {
   jQuery('.duration').text(compVideo[0].duration);
});

/*-=-=- update HTML5 video current play time -=-=-*/
compVideo.on('timeupdate', function() {
	var comp_progress = jQuery('#comp_spvp_control .progressBar');
	var currentPos = compVideo[0].currentTime; // Get currenttime
	var comp_maxduration = compVideo[0].duration; // Get video duration
	var comp_percentage = 100 * currentPos / comp_maxduration; //in %
	var comp_position = (<?php echo $theme->appWidth; ?> * comp_percentage / 100)-comp_progress.offset().left; 
	jQuery('#comp_spvp_control .timeBar').css('width', comp_percentage+'%');
});

compVideo.on('ended',function(){
	if(jQuery('#comp_repeat').val()=="repeatOne")  {
		compVideo[0].currentTime = 0;
		compVideo[0].play();
		paly_comp.css('display',"none");
		pause_comp.css('display',"block");
	}
	if(jQuery('#comp_repeat').val()=="repeatAll") {
		jQuery('#comp_control_btns .playNext').click();
	}
	if(jQuery('#comp_repeat').val()=="repeatOff"){
		if(comp_vid_num==comp_video_urls.length-1) {
			compVideo[0].currentTime = 0;
			compVideo[0].pause();
			paly_comp.css('display',"block");
			pause_comp.css('display',"none");
		}
	}
	<?php if($theme->autoNext==1) { ?>
		if(jQuery('#comp_repeat').val()=="repeatOff") 
		if(comp_vid_num==comp_video_urls.length-1) {
			compVideo[0].currentTime=0;
			compVideo[0].pause();
			paly_comp.css('display',"block");
			pause_comp.css('display',"none");
		}
		else {	
			jQuery('#comp_control_btns .playNext').click();
		}
	<?php } ?>
})

/* Drag status */
var timeDrag = false;
jQuery('#comp_spvp_control .progressBar').mousedown(function(e) {
   timeDrag = true;
   comp_updatebar(e.pageX);
});

jQuery('#comp_spvp_control .progressBar').select(function(){ })
 
jQuery(document).mouseup(function(e) {
	if(timeDrag) {
		timeDrag = false;
		comp_updatebar(e.pageX);
	}
});

jQuery(document).mousemove(function(e) {
	if(timeDrag) {
		comp_updatebar(e.pageX);
	}
});

/*-=-=- update Progress Bar control -=-=-*/
var comp_updatebar = function(x) {
	var comp_progress = jQuery('#comp_spvp_control .progressBar');
	var comp_maxduration = compVideo[0].duration; //Video duraiton
	var comp_position = x - comp_progress.offset().left; //Click pos
	var comp_percentage = 100 * comp_position / comp_progress.width();
	//Check within range
	if(comp_percentage > 100) {
		comp_percentage = 100;
	}
	if(comp_percentage < 0) {
		comp_percentage = 0;
	}
	//Update progress bar and video currenttime
	jQuery('#comp_spvp_control .timeBar').css('width', comp_percentage+'%');
	jQuery('.spanA').css('left', comp_position+'px');
	compVideo[0].currentTime = comp_maxduration * comp_percentage / 100;
};

/*-=-=- loop to get HTML5 video buffered data -=-=-*/
function startBuffer() {
	setTimeout(function(){
		var comp_maxduration = compVideo[0].duration;
		var comp_currentBuffer = compVideo[0].buffered.end(0);
		var comp_percentage = 100 * comp_currentBuffer / comp_maxduration;
		jQuery('#comp_spvp_control .bufferBar').css('width', comp_percentage+'%');
		if(comp_currentBuffer < comp_maxduration) {
			setTimeout(startBuffer, 500);
		}
	},800)
};

checkVideoLoad=setInterval(function(){
	if(compVideo[0].duration) {
		setTimeout(startBuffer(), 500);
		clearInterval(checkVideoLoad)
	}
}, 1000)

/*-=-=- Mute/Unmute control clicked -=-=-*/
var comp_volume = jQuery('.comp_volumeBar');
jQuery('.muted').click(function() {
	compVideo[0].muted = !compVideo[0].muted;
	return false;
});

/*-=-=- Volume control clicked -=-=-*/
jQuery('.comp_volumeBar').on('mousedown', function(e) {
	var comp_position = e.pageX - comp_volume.offset().left;
	var comp_percentage = 100 * comp_position / comp_volume.width();
	jQuery('#comp_volumeTD .comp_volume').css('width', comp_percentage+'%');
	compVideo[0].comp_volume = comp_percentage / 100;
});

/* Drag status */
var volumeDrag = false;
jQuery('.comp_volumeBar').mousedown(function(e) {
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
	var comp_progress = jQuery('.comp_volumeBar');
	var comp_position = x - comp_progress.offset().left; //Click pos
	var comp_percentage = 100 * comp_position / comp_progress.width();
	//Check within range
	if(comp_percentage > 100) {
		comp_percentage = 100;
	}
	if(comp_percentage < 0) {
		comp_percentage = 0;
	}
	//Update progress bar and compVideo currenttime
	jQuery('#comp_volumeTD .comp_volume').css('width', comp_percentage+'%');
	compVideo[0].comp_volume =  comp_percentage / 100;
};

var yy=1;
controlHideTime='';
jQuery("#comp_spvp_global_body").each(function(){
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
					jQuery("#comp_spvp_playlist").animate({
						width: "0px",
					},300 );
					setTimeout(function(){ jQuery("#comp_spvp_playlist").css('display','none');},300)
					jQuery(".comp_spvp_control").animate({
						width: <?php echo $theme->appWidth; ?>+"px",
						<?php if ($theme->playlistPos==1){ ?>
							marginLeft:'0px'
						<?php } else {?>
							marginRight:'0px'
						<?php } ?>
					}, 300 );
					jQuery("#comp_control_btns").animate({
						width: <?php echo $theme->appWidth?>+"px",
					}, 300 );

					<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
						jQuery("#comp_videoID").animate({
							width: <?php echo $theme->appWidth ?>+"px",
							marginLeft:'0px'
						}, 300 );  
					<?php } ?>

					<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
						jQuery("#comp_videoID").animate({
							width: <?php echo $theme->appWidth ?>+"px",
						}, 300 );  
					<?php } ?>
				<?php } ?>

				<?php if($theme->ctrlsSlideOut==1){ ?>
					jQuery('.comp_spvp_control').hide("slide", { direction: "<?php if($theme->ctrlsPos==1) echo 'up'; else echo 'down'; ?>" }, 1000);
				<?php } ?>

				<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
					jQuery("#comp_videoID").animate({
						width: <?php echo $theme->appWidth ?>+"px",
						marginLeft:'0px'
					}, 300 );  
				<?php } ?>

				<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
					jQuery("#comp_videoID").animate({
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
		if(document.getElementById('comp_spvp_control').style.display=="none") {
			jQuery('.comp_spvp_control').show("slide", { direction: "<?php if($theme->ctrlsPos==1) echo 'up'; else echo 'down'; ?>" }, 450);
		}
	})
})


var xx = 1; 
volumeHideTime = '';
jQuery("#comp_volumeTD").each(function(){
	jQuery('#comp_volumeTD').mouseleave(function() {
		volumeHideTime = setInterval(function(){
			xx=xx+1;
			if(xx<2) {
				return false
			}
			else {
				clearInterval(volumeHideTime);
				xx=1;				
				jQuery("#comp_control_btns #space").animate({
					paddingLeft:<?php echo (($theme->appWidth*20)/100)+'px' ?>,
				},1000);
				
				jQuery("#comp_volumeTD #volumebar_player").animate({ 
					width:'0px',
				},1000);
				
				comp_percentage = compVideo[0].comp_volume * 100;
				jQuery('#comp_volumeTD .comp_volume').css('width', comp_percentage+'%');
			}
		},1000)
	})

	jQuery('#comp_volumeTD').mouseenter(function() {
		if(volumeHideTime) {
			clearInterval(volumeHideTime)
			xx=1;
		}
		jQuery("#comp_control_btns #space").animate({ 
			paddingLeft:<?php echo (($theme->appWidth*20)/100)-100+'px' ?>,
		},500);

		jQuery("#comp_volumeTD #volumebar_player").animate({ 
			width:'100px',
		},500);
	});
})


jQuery('#comp_control_btns .playlist').on('click', function() {
	if(document.getElementById("comp_spvp_playlist").style.width=="0px") { 
		jQuery("#comp_spvp_playlist").css('display','')
		
		jQuery("#comp_spvp_playlist").animate({
			width: <?php echo $theme->playlistWidth; ?>+"px",
		}, 500 );
		
		jQuery(".comp_spvp_control").animate({
			width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
			<?php if ($theme->playlistPos==1){ ?>
				marginLeft:<?php echo $theme->playlistWidth; ?>+'px'
			<?php } else {?>
				marginRight:<?php echo $theme->playlistWidth; ?>+'px'
			<?php } ?>
		}, 500 );
		
		jQuery("#comp_control_btns").animate({
			width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
		}, 500 );

		<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
			jQuery("#comp_videoID").animate({
				width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
				marginLeft:<?php echo $theme->playlistWidth ?>
			}, 500 );  
		<?php } ?>
		
		<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
			jQuery("#comp_videoID").animate({
				width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
			}, 500 );  
		<?php } ?>
	}
	else {
		jQuery("#comp_spvp_playlist").animate({
			width: "0px",
		}, 1500 );
		
		setTimeout(function(){ jQuery("#comp_spvp_playlist").css('display','none');},1500)
		
		jQuery(".comp_spvp_control").animate({
			width: <?php echo $theme->appWidth; ?>+"px",
			<?php if ($theme->playlistPos==1){ ?>
				marginLeft:'0px'
			<?php } else {?>
				marginRight:'0px'
			<?php } ?>
		}, 1500 );

		jQuery("#comp_control_btns").animate({
			width: <?php echo $theme->appWidth?>+"px",
		}, 1500 );

		<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
			jQuery("#comp_videoID").animate({
				width: <?php echo $theme->appWidth ?>+"px",
				marginLeft:'0px'
			}, 1500 );  
		<?php } ?>
		
		<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
			jQuery("#comp_videoID").animate({
				width: <?php echo $theme->appWidth ?>+"px",
			}, 1500 );  
			<?php } ?>
	}
});

comp_current_playlist_table=document.getElementById('comp_current_playlist_table').value;
comp_video_urls = jQuery('#comp_track_list_'+comp_current_playlist_table).find('.comp_vid_thumb');

function comp_current_playlist_videos(){
	comp_current_playlist_table=document.getElementById('comp_current_playlist_table').value;
	comp_video_urls = jQuery('#comp_track_list_'+comp_current_playlist_table).find('.comp_vid_thumb');
}

var comp_vid_num=0;
var comp_used_track = new Array();
jQuery('#comp_control_btns .playPrev').on('click', function() {
	comp_used_track[comp_used_track.length] = comp_vid_num;
	comp_vid_num++;
	if(jQuery('#comp_shuffle').val()==1){
		comp_vid_num = parseInt(Math.random() * (comp_video_urls.length+1 - 0) + 0);
		while(in_array(comp_vid_num,comp_used_track)){
			comp_vid_num = parseInt(Math.random() * (comp_video_urls.length+1 - 0) + 0);
		}
	}
	if(comp_used_track.length>=comp_video_urls.length){
		comp_used_track=[];
	}
	if(comp_vid_num<0){
		comp_vid_num = comp_video_urls.length-1;
	}
	comp_video_urls[comp_vid_num].click();
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


jQuery('#comp_control_btns .playNext').on('click', function() {
	comp_used_track[comp_used_track.length] = comp_vid_num;
	comp_vid_num++;
	if(jQuery('#comp_shuffle').val()==1){
		comp_vid_num = parseInt(Math.random() * (comp_video_urls.length+1 - 0) + 0);
		while(in_array(comp_vid_num,comp_used_track)){
			comp_vid_num = parseInt(Math.random() * (comp_video_urls.length+1 - 0) + 0);
		}
	}
	if(comp_used_track.length>=comp_video_urls.length){
		comp_used_track=[];
	}
	jQuery('#comp_spvp_control .timeBar').css('width', '0%');
	if(comp_vid_num==comp_video_urls.length){
		comp_vid_num=0;
	}
	comp_video_urls[comp_vid_num].click();
});


jQuery("#comp_control_btns .lib").click(function () {
	jQuery('#comp_spvp_album_div').css('transform','');
	jQuery('#comp_spvp_global_body').css('transform','');
	jQuery('#comp_spvp_global_body').transition({
		perspective: '700px',
		rotateY: '180deg',
	},1000);

	setTimeout(function(){
		jQuery('#comp_spvp_album_div').css('-ms-transform','rotateY(180deg)')
		jQuery('#comp_spvp_album_div').css('transform','rotateY(180deg)')

		jQuery('#comp_spvp_album_div').css('-o-transform','rotateY(180deg)')
		document.getElementById('comp_spvp_album_div').style.display='block'
		document.getElementById('comp_spvp_video_div').style.display='none'
	},300);
	
	setTimeout(function(){
		jQuery('#comp_spvp_album_div').css('-ms-transform','');
		jQuery('#comp_spvp_global_body').css('-ms-transform','');

		jQuery('#comp_spvp_album_div').css('transform','');
		jQuery('#comp_spvp_global_body').css('transform','');

		jQuery('#comp_spvp_album_div').css('-o-transform','');
		jQuery('#comp_spvp_global_body').css('-o-transform','');
	},1100);
})


jQuery("#comp_spvp_album_div .show_vid").click(function () {
	jQuery('#comp_spvp_global_body').transition({
		perspective: '700px',
		rotateY: '180deg',
	},1000);

	setTimeout(function(){
		jQuery('#comp_spvp_video_div').css('-ms-transform','rotateY(180deg)')
		jQuery('#comp_spvp_video_div').css('transform','rotateY(180deg)')
		jQuery('#comp_spvp_video_div').css('-o-transform','rotateY(180deg)')
		document.getElementById('comp_spvp_album_div').style.display='none'
		document.getElementById('comp_spvp_video_div').style.display='block'
	},300);
	
	setTimeout(function(){
		jQuery('#comp_spvp_video_div').css('-ms-transform','');
		jQuery('#comp_spvp_global_body').css('-ms-transform','');

		jQuery('#comp_spvp_video_div').css('transform','');
		jQuery('#comp_spvp_global_body').css('transform',''); 

		jQuery('#comp_spvp_video_div').css('-o-transform','');
		jQuery('#comp_spvp_global_body').css('-o-transform','');
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
		

function comp_getPixels() {	
	for(i=1; i<30;i++)
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
	
function comp_changeColor() {
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
	jQuery('#comp_spvp_global_body ,#comp_videoID').each(function(){
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
	if(compVideo[0].paused) {
		compVideo[0].play();
		paly_comp.css('display',"none");
		pause_comp.css('display',"block");
	}
	else {
		compVideo[0].pause();
		paly_comp.css('display',"block");
		pause_comp.css('display',"none");
	}
}

jQuery('#track_list_0').find('#thumb_0').click();

<?php if($comp_vds->type=="http" || $comp_single_video_type=="http"){ ?>
	compVideo[0].pause();
<?php } ?>

if(paly_comp && pause_comp) {
	paly_comp.css('display',"block");
	pause_comp.css('display',"none");
}

<?php if($AlbumId!=''){ ?>
	jQuery('#comp_track_list_<?php echo $AlbumId ?>').find('#thumb_<?php echo $TrackId ?>').click();
<?php } ?>
/*-=-=- END COLOR -=-=-*/


jQuery('#comp_spvp_global_body').find('img').last().load(function(){setTimeout('comp_getPixels();comp_changeColor();',1600)})
jQuery('#comp_volumeTD .comp_volume')[0].style.width='<?php echo $theme->defaultVol?>%';
compVideo[0].comp_volume=<?php echo $theme->defaultVol/100 ;?>;
<?php if($theme->ctrlsSlideOut==0) { ?>
	jQuery('#comp_videoID').mouseenter();
<?php } ?>

<?php if($theme->openPlaylistAtStart==1) { ?>
	jQuery('#comp_control_btns .playlist').click();
<?php } ?>

</script>