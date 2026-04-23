<?php
/**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\String\StringHelper;

// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

$lang =  JFactory::getLanguage();
$lang->load('com_spidervideoplayer',JPATH_BASE);

class plgContentLoadspidervideoplayer extends JPlugin {
	
	/*-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
	public function onContentPrepare($context, &$row, &$params, $page=0 ) {	
		$db = JFactory::getDBO();
		
		//if ( JString::strpos( $row->text, 'loadspidervideoplayer' ) === false ) {
		if ( StringHelper::strpos( $row->text, 'loadspidervideoplayer' ) === false ) {
			return true;
		}
		$regex = '/{loadspidervideoplayer\s*.*?}/i';
		
		if ( !$this->params->get( 'enabled', 1 ) ) {
			$row->text = preg_replace( $regex, '', $row->text );
			return true;
		}
		
		preg_match_all( $regex, $row->text, $matches );
		
		$count = count( $matches[0] );
		
		if ( $count ) {
			$this->_process( $row, $matches, $count, $regex );
		}
	
	}
	
	
	/*-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
	protected function _process( &$row, &$matches, $count, $regex ) {
		$style=-1;
		for ( $i=0; $i < $count; $i++ ) {
			$load = str_replace( 'loadspidervideoplayer', '', $matches[0][$i] );
			$load = str_replace( '{', '', $load );
			$load = str_replace( '}', '', $load );
			$load = trim( $load );
			if(!$load)
				continue;
			
			$params = explode(' ',$load);
			$playlist = explode('=',$params[0]);
			$theme = explode('=',$params[1]);
			$priority = explode('=',$params[2]);
			
			if($playlist[0]!='playlist' && $playlist[0]!='track' || $theme[0]!='theme' || $priority[0]!='priority')
				continue;
			
			if($playlist[0]=='playlist')
				$typeselect = 0;
			else
				$typeselect = 1;
			
			$modules	= $this->_load( $playlist[1], $theme[1],$priority[1], $typeselect );
			$row->text 	= preg_replace( '{'. $matches[0][$i] .'}', $modules, $row->text,1 );
			
		}
		// removes tags without matching module positions
		$row->text = preg_replace( $regex, '', $row->text );
	}
	
	
	/*-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
	protected function _load( $playlist, $theme, $priority, $typeselect ) {
		ob_start();
		static $embedded;
		if(!$embedded) {
			$embedded = true;
		}
		$db =& JFactory::getDBO();	
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		
		$cmpnt_js_path = JURI::root(true).'/components/com_spidervideoplayer/js';
		$cmpnt_img_path = JURI::root(true).'/components/com_spidervideoplayer/views/spidervideoplayer/images';
		
		$document = JFactory::getDocument();
		$document->addStyleSheet($cmpnt_js_path.'/jquery-ui.css'); 
		
		/////////////////////
		///// T H E M E /////
		/////////////////////
		$query = "SELECT * FROM #__spidervideoplayer_theme WHERE id=".(int)$theme; 		 // appWidth, appHeight, show_trackid
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
			$load_playlist_array = explode(',',$playlist);
		}
		
		///////////////////////
		///// V I D E O S /////
		///////////////////////
		//$track = $params->get('video');
		//$track_ID = 3;
		//$query = "SELECT * FROM #__spidervideoplayer_video WHERE id IN ($track_ID)";  // videos,thumb,title
		$query = "SELECT * FROM #__spidervideoplayer_video WHERE id=".(int)$playlist;		
		$db->setQuery($query);
		$load_videos = $db->loadAssocList();		
		$track_URL = ''; $track_poster = ''; 

		if(isset($load_videos[0]['type']))
			$load_single_video_type = $load_videos[0]['type'];
		else 
			$load_single_video_type = '';
		
		if($typeselect==1){
			$load_playlist_array = '';
			$track = $load_videos[0];
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
		$load_vds =& JTable::getInstance('__spidervideoplayer_video', 'Table');
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
		.load_spvp_control {
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
		.load_spvp_control.youRtmp { 
			width: 47% !important; left: 225px; 
			top:<?php echo $ctrl_top2; ?>;
			padding: 0 10px;
		}
		.load_spvp_control.youRtmp.singYouRtmp { width: 20% !important; left: 400px; }

		.load_spvp_control.youRtmp .btnPlay { display:none !important; }

		#load_control_btns { 
			opacity:<?php echo $theme->ctrlsMainAlpha/100; ?>;
			border:none; border-collapse: collapse;
			height:80%; width: 98% !important; 	
			margin: 0 auto; margin-top:4px;	
		}

		#load_control_btns td, #load_control_btns #space { 
			padding:0 !important; 
			text-align: center;
		}

		#load_spvp_control .progressBar {
			z-index:5;
			cursor:pointer;
			position: relative;
			width: 100%; height:6px;
			border-top:1px solid rgba(<?php echo HEXDEC(SUBSTR($theme->slideColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
			border-bottom:1px solid rgba(<?php echo HEXDEC(SUBSTR($theme->slideColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
		}
		#load_spvp_control .timeBar {
			z-index:5;
			top: 0; left: 0;
			position: absolute;
			width: 0; height: 100%;
			background-color: <?php echo '#'.$theme->slideColor; ?>;
		}
		.bufferBar {
			opacity:0.3;
			top: 0; left: 0;
			position: absolute;
			width: 0; height: 100%;
			background-color: <?php echo '#'.$theme->slideColor; ?>;	
		}
		#load_volumeTD .load_volumeBar {
			overflow: hidden;
			position: relative;	
			width: 0px; height:4px;
			background-color: rgba(<?php echo HEXDEC(SUBSTR($theme->framesBgColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->framesBgColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
			border:1px solid rgba(<?php echo HEXDEC(SUBSTR($theme->slideColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->slideColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>);
		}
		#load_volumeTD .load_volume {
			top: 0; left: 0;
			position: absolute;	
			width: 0; height: 100%;
			background-color: <?php echo '#'.$theme->slideColor; ?>;
		}
		#load_spvp_playlist {
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
		
		<div id="spidervideoplayerhtml5_plugin<?php echo $idd; ?>" style="display:none;">
			
			<div id="load_spvp_global_body" style="width:<?php echo $theme->appWidth; ?>px;height:<?php echo $theme->appHeight; ?>px; position:relative;">
				
				<!-- 1. Video_div -->
				<div id="load_spvp_video_div" style="display:<?php if($startWithLib==1) echo 'none'; else echo 'block'?>; width:<?php echo $theme->appWidth; ?>px; height:<?php echo $theme->appHeight; ?>px; background-color:<?php echo "#".$theme->vidBgColor; ?>">
					
					<!-- 1.1. Play_list -->
					<div id="load_spvp_playlist">
						<input type='hidden' value='0' id="track_list" />
						<div style="height:90%">
							<div onmousedown="scrolltp2=setInterval('scrollTop2()', 30)" onmouseup="clearInterval(scrolltp2)" ontouchstart="scrolltp2=setInterval('scrollTop2()', 30)" ontouchend="clearInterval(scrolltp2)" style="overflow:hidden; text-align:center;width:<?php echo $theme->playlistWidth; ?>px; height:20px">
								<img src="<?php echo $cmpnt_img_path; ?>/top.png" style="cursor:pointer;" id="button20" />
							</div>
							
							<div style="height:<?php echo $theme->appHeight-40; ?>px; overflow:hidden" >
								<?php
								for($i=0;$i<count($load_playlist_array)-1;$i++) {									
									$load_v_ids = explode(',',$play->videos);
									$vi_ids = substr($play->videos,0,-1);								
									if($i!=0)
										echo '<table border="1" id="load_track_list_'.$i.'" style="display:none; height:100%; width:100%; border-spacing:0px; border:none; border-collapse:inherit;" >';
									else
										echo '<table border="1" id="load_track_list_'.$i.'" style="display:block; height:100%; width:100%; border-spacing:0px; border:none; border-collapse:inherit;" >';

									echo '<tr>
										<td style="text-align:left;border:0px solid grey;width:100%;vertical-align:top;">
										<div id="load_scroll_div2_'.$i.'" class="playlist_values" style="position:relative">';
										$jj = 0;
										
									for($j=0;$j<count($load_v_ids)-1;$j++) {
										$query = "SELECT * FROM #__spidervideoplayer_video WHERE id IN ($load_v_ids[$j])	";  // videos,thumb,title
										$db->setQuery($query);
										$load_videos = $db->loadAssocList();
										$load_vds = $load_videos["0"];										
										
										if($load_vds["type"]=="http" || $load_vds["type"]=="youtube" || $load_vds["type"]=="rtmp") {
											/*-=-=- Video URL -=-=-*/
											if($load_vds["urlHtml5"]!='') { 
												if(strpos($load_vds["urlHtml5"], "http:")===false and strpos($load_vds["urlHtml5"], "https:")===false )
													$html5Url = JURI::root().'administrator/'.$load_vds["urlHtml5"];
												else
													$html5Url = $load_vds["urlHtml5"];
											}
											elseif (($load_vds["urlHtml5"] == "" || !strpos($load_vds["url"], 'embed')) && $load_vds["type"]!= "rtmp") {
												if ($load_vds["type"] == "youtube") {
													$html5Url = "https://www.youtube.com/embed/" . substr($load_vds["url"], strpos($load_vds["url"], '?v=') + 3, 11) . "?enablejsapi=1&html5=1&controls=1&modestbranding=1&rel=0";
												}
												else $html5Url = JURI::root().'administrator/'.$load_vds["url"];
											}
											else
												$html5Url = $load_vds["urlHtml5"];
										
											/*-=-=- Thumbnail URL -=-=-*/
											if(strpos($load_vds["thumb"], "http:")===false and strpos($load_vds["thumb"], "https:")===false )
												$vidsTHUMB = JURI::root().'administrator/'.$load_vds["thumb"];
											else
												$vidsTHUMB = $load_vds["thumb"];
											
											/*-=-=- Video HD URL -=-=-*/
											if($load_vds["urlHdHtml5"]!='') {
												if(strpos($load_vds["urlHdHtml5"], "http:")===false and strpos($load_vds["urlHdHtml5"], "https:")===false )
													$html5UrlHD = JURI::root().'administrator/'.$load_vds["urlHdHtml5"];
												else
													$html5UrlHD = $load_vds["urlHdHtml5"];
											}
											elseif (($load_vds["urlHtml5"] == "" || !strpos($load_vds["url"], 'embed')) && $load_vds["type"]!= "rtmp") {
												if ($load_vds["type"] == "youtube") {
													$html5UrlHD = "https://www.youtube.com/embed/" . substr($load_vds["url"], strpos($load_vds["url"], '?v=') + 3, 11) . "?enablejsapi=1&html5=1&controls=1&modestbranding=1&rel=0";
												}
												else $html5UrlHD = JURI::root().'administrator/'.$load_vds["urlHD"];									
											}
											else
												$html5UrlHD = $load_vds["urlHD"];

											echo '<div id="thumb_'.$jj.'"  onclick="jQuery(\'#load_HD_on\').val(0);document.getElementById(\'load_videoID\').src=\''.$html5Url.'\';document.getElementById(\'load_videoID\').poster=\''.$vidsTHUMB.'\';load_vid_select(this);load_vid_num='.$jj.';jQuery(\'#_track\').val('.$jj.');" class="load_vid_thumb" style="color:#'.$theme->textColor .';cursor:pointer;width:'.$theme->playlistWidth.'px;text-align:center"  >';
											if($load_vds["thumb"])
												echo '<img src="'.$vidsTHUMB.'" width="90px" style="display:none;"  />';
											if($theme->show_trackid==1)
												echo '<p style="font-size:'.$theme->playlistTextSize.'px;line-height:30px;cursor:pointer;" >'.($jj+1).'-'.$load_vds["title"].'</p>';
											else
												echo '<p style="font-size:'.$theme->playlistTextSize.'px;line-height:30px;cursor:pointer;" >'.$load_vds["title"].'</p>';
											echo '</div>';
											echo '<input type="hidden" id="urlHD_'.$jj.'" value="'.$html5UrlHD.'" />';
											echo '<input type="hidden" id="vid_type_'.$jj.'" value="'.$load_vds["type"].'" />';
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
						if($load_vds["type"]=="youtube" || $load_vds["type"]=="rtmp"){ ?>
							<div id="spvp_single_video_main">
								<iframe class="spvp_single_video_main" id="load_videoID" type="text/html" width="<?php echo $theme->appWidth ?>" height="<?php echo $theme->appHeight; ?>" src="<?php echo $html5Url; ?>?enablejsapi=1&version=3&playerapiid=ytplayer&modestbranding=1&rel=0" frameborder="0" allowfullscreen></iframe> 
							</div>
							<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px; left:10px;" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
						<?php }
						else { ?>
							<video poster="<?php echo $vidsTHUMB; ?>" src="<?php echo $html5Url; ?>" ontimeupdate="load_timeUpdate()" ondurationchange="load_durationChange();" id="load_videoID" style="position:absolute" width="<?php echo $theme->appWidth; ?>" height="<?php echo $theme->appHeight; ?>" ><p>Your browser does not support the video tag.</p></video>
							<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px;" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
						<?php }
					}
					//Single
					else {
						if($load_single_video_type=="youtube" || $load_single_video_type=="rtmp"){ ?>
							<div id="spvp_single_video_main">
								<iframe class="spvp_single_video_main" id="load_videoID" type="text/html" width="<?php echo $theme->appWidth ?>" height="<?php echo $theme->appHeight; ?>" src="<?php echo substr($track_URL,0,  strpos($track_URL, "?"));?>?enablejsapi=1&version=3&playerapiid=ytplayer&modestbranding=1&rel=0" frameborder="0" allowfullscreen></iframe> 
							</div>
							<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px;left:10px" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
						<?php }
						else { ?>
							<video poster="<?php echo $track_poster ?>" src="<?php echo $track_URL ?>" ontimeupdate="load_timeUpdate()" ondurationchange="load_durationChange();" id="load_videoID" style="position:absolute" width="<?php echo $theme->appWidth; ?>" height="<?php echo $theme->appHeight; ?>" ><p>Your browser does not support the video tag.</p></video>
							<a href="http://web-dorado.com/" target="_blank"><img style="position: absolute;top: <?php echo $theme->appHeight-110 ?>px;height:63px;" src="components/com_spidervideoplayer/views/spidervideoplayer/images/wd_logo.png" /></a>
						<?php }
					} 	?>	

					<!-- 1.3. Control Buttons -->
					<div class="load_spvp_control" id="load_spvp_control" style="display:none;overflow:hidden">
						<?php 
						if($theme->ctrlsPos==2){ 
							if($typeselect==0){
								if($load_vds["type"]=="http"){ ?>
									<div class="progressBar">
										<div class="timeBar"></div>
										<div class="bufferBar"></div>
									</div>
								<?php
								} else echo "";
							}
							else {
								if($load_single_video_type=="http"){ ?>
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
						echo '<table width="'.$theme->appWidth.'" id="load_control_btns"><tr>';
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
									echo '<img id="button'.($y+1).'" width="16" class="btnPause" style="display:none; cursor:pointer; margin:0 auto; opacity:'.$theme->ctrlsMainAlpha/100 .'" src="'.$cmpnt_img_path.'/pause.png" />';
									$y=$y+2;
								}
								else if($ctrl[0]=='+') {
									echo '<span id="space" style="padding-left:'.(($theme->appWidth*20)/100).'px"></span>';
								}
								else if($ctrl[0]=='time') {
									echo '<div id="timeBarNum"><span style="color:#'.$theme->ctrlsMainColor.';opacity:'.$theme->ctrlsMainAlpha/100 .'" id="load_time">00:00 </span> <span style="color:#'.$theme->ctrlsMainColor.';opacity:'.$theme->ctrlsMainAlpha/100 .'">/</span> <span style="color:#'.$theme->ctrlsMainColor.';opacity:'.$theme->ctrlsMainAlpha/100 .'" id="load_duration">00:00</span></div>';
								}
								else if($ctrl[0]=='vol') {
									echo '<table id="load_volumeTD">
									<tr>
										<td id="voulume_img" >
											<img  style="cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"  id="button'.$y.'"    src="'.$cmpnt_img_path.'/vol.png"  />
										</td>
										<td id="volumeTD2" style="width:0px !important">
											<span id="volumebar_player" class="load_volumeBar">
												<span class="load_volume"></span>
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
									echo '<img  id="button'.$y.'" class="load_repeat" style="cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"   src="'.$cmpnt_img_path.'/repeat.png" />';
									echo '<img  id="button'.($y+1).'"  class="load_repeat" style="display:none;cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"   src="'.$cmpnt_img_path.'/repeatOff.png" />';
									echo '<img  id="button'.($y+2).'"  class="load_repeat" style="display:none;cursor:pointer;opacity:'.$theme->ctrlsMainAlpha/100 .'"  src="'.$cmpnt_img_path.'/repeatOne.png" />';
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
				<div id="load_spvp_album_div" style="display:<?php if($startWithLib==0) echo 'none' ?>;background-color:<?php echo "#".$theme->appBgColor;?>;height:100%; overflow:hidden;position:relative;">
					<table width="100%" height="100%" style="border:none;border-collapse: inherit;">
						<tr id="tracklist_up" style="display:none">
							<td height="12px" colspan="2" style="text-align:right;border:none;">
								<div onmouseover="this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2))?>,0.4)'" onmouseout="this.style.background='none'" id="scroll" style="overflow:hidden;width:50%;height:100%;text-align:center;float:right;cursor:pointer;" onmousedown="scrolltp=setInterval('scrollTop()', 30)" onmouseup="clearInterval(scrolltp)" ontouchstart="scrolltp=setInterval('scrollTop()', 30)" ontouchend="clearInterval(scrolltp)">
								<img  src="<?php echo $cmpnt_img_path; ?>/top.png" style="cursor:pointer;" id="button25" /></div>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:middle;border:none;">
								<img src="<?php echo $cmpnt_img_path; ?>/prev.png" style="cursor:pointer;" id="button28" onclick="load_prevPage();" />
							</td>				
							<td style="width: 93%;border:none;">
								<?php
								for($l=0;$l<$table_count;$l++) {
									echo '<table id="load_lib_table_'.$l.'" '.$display.'> ';								
									for($i=0;$i<$libRows;$i++) {
										echo '<tr>';									
										for($j=0;$j<$libCols;$j++) {
											if($p<count($load_playlist_array)-1) {
												$load_v_ids = explode(',',$play->videos);
												$vi_ids = substr($play->videos,0,-1);
												if(strpos($play->thumb, "http:")===false and strpos($play->thumb, "https:")===false )
													$playTHUMB = JURI::root().'administrator/'.$play->thumb;
												else
												$playTHUMB = $play->thumb;
							
												echo '<td class="playlist_td" id="load_playlist_'.$p.'"  onclick="load_openPlaylist('.$p.','.$l.')" onmouseover="this.style.color=\'#'.$theme->textHoverColor .'\';this.style.background=\'rgba('.HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)).',0.4)\'" onmouseout="this.style.color=\'#'.$theme->textColor .'\';this.style.background=\' none\'" onclick="" style="color:#'.$theme->textColor .';border:1px solid white;vertical-align:center; text-align:center;width:'.$cellWidth.';height:'.$cellHeight.';cursor:pointer">';
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
									if($p<count($load_playlist_array)-1) {
										$table_count = $table_count+1;
										$display = 'style="display:none;width:100%;height:100%;border-collapse: collapse;"';
									} 
									echo '</table>';
								}
								for($i=0;$i<$p;$i++) {
									$load_v_ids = explode(',',$play->videos);
									$vi_ids = substr($play->videos,0,-1);
									if(strpos($play->thumb, "http:")===false and strpos($play->thumb, "https:")===false )
										$playTHUMB = JURI::root().'administrator/'.$play->thumb;
									else
										$playTHUMB = $play->thumb;

									echo '<table playlist_id="'.$i.'" id="load_playlist_table_'.$i.'"  style="border:none;border-collapse: inherit;display:none;height:100%;width:100%" >
										<tr>
											<td style="text-align:center;vertical-align:top;border:none;">';
												if($play->thumb!='')
													echo '<img src="'.$playTHUMB.'" width="70%" /><br /><br />';
												echo '<p style="color:#'.$theme->textColor .';font-size:'.$theme->libDetailsTextSize.'px">'.$play->title.'</p>';
											echo '</td>
											<td style="width:50%;border:none;">
												<div style="width:100%;text-align:left;border:1px solid white;height:'.($theme->appHeight-55).'px;vertical-align:top;position:relative;overflow:hidden">
													<div id="load_scroll_div_'.$i.'" style="position:relative;">';
													$jj=0;
													for($j=0;$j<count($load_v_ids)-1;$j++) {
														$query = "SELECT * FROM #__spidervideoplayer_video WHERE id IN ($load_v_ids[$j])	";  // videos,thumb,title
														$db->setQuery($query);
														$load_videos = $db->loadAssocList();
														$load_vds = $load_videos["0"];
														
														if($load_vds["type"]=="http" || $load_vds["type"]=="youtube" || $load_vds["type"]=="rtmp") {
															echo '<p class="vid_title" ondblclick="jQuery(\'#load_spvp_album_div .show_vid\').click()" onmouseover="this.style.color=\'#'.$theme->textHoverColor .'\';this.style.background=\'rgba('.HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2)).','.HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)).',0.4)\'" onmouseout="this.style.color=\'#'.$theme->textColor .'\';this.style.background=\' none\'" style="color:#'.$theme->textColor .';font-size:'.$theme->libDetailsTextSize.'px;line-height:30px;cursor:pointer" onclick="jQuery(\'#load_HD_on\').val(0);jQuery(\'#load_track_list_'.$i.'\').find(\'.load_vid_thumb\')['.$jj.'].click();load_playBTN();load_current_playlist_videos();load_vid_num='.$jj.';jQuery(\'#load_current_track\').val('.$jj.');load_vid_select2(this);load_playlist_select('.$i.') ">'.($jj+1).' - '.$load_vds["title"].'</p>';
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
											<img src="<?php echo $cmpnt_img_path; ?>/next.png" style="cursor:pointer;" id="button27" onclick="load_nextPage()" />
										</td>
									</tr>
									<tr>
										<td style="border:none;">
											<img src="<?php echo $cmpnt_img_path; ?>/back.png" style="cursor:pointer;display:none" id="button29" onclick="load_openLibTable()" />
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
								<div  onmouseover="this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2))?>,0.4)'" onmouseout="this.style.background='none'" id="scroll" style="overflow:hidden;width:50%;height:100%;text-align:center;float:right;cursor:pointer;" onmousedown="scrollBot=setInterval('load_scrollBottom()', 30)" onmouseup="clearInterval(scrollBot)" ontouchstart="scrollBot=setInterval('load_scrollBottom()', 30)" ontouchend="clearInterval(scrollBot)">
									<img src="<?php echo $cmpnt_img_path; ?>/bot.png" style="cursor:pointer;" id="button26"  />
								</div>
							</td>
						</tr>
					</table>
				</div>
				
				<!-- 3. Share_buttons -->
				<?php if($theme->ctrlsPos==1) $share_top= '-'.$theme->appHeight+22; else $share_top='-133' ?>
				<div id="load_share_buttons" style="text-align:center; height:108px; width:30px; background-color:rgba(0,0,0,0.5); position:relative; z-index:200; top:<?php echo $share_top ?>px; display:none;" >
					<img onclick = "load_flashShare('fb',document.getElementById('load_current_playlist_table').value,document.getElementById('load_current_track').value)" style="cursor:pointer"  src="<?php echo $cmpnt_img_path; ?>/facebook.png" /><br>
					<img onclick = "load_flashShare('tw',document.getElementById('load_current_playlist_table').value,document.getElementById('load_current_track').value)" style="cursor:pointer" src="<?php echo $cmpnt_img_path; ?>/twitter.png" /><br>
					<img onclick = "load_flashShare('g',document.getElementById('load_current_playlist_table').value,document.getElementById('load_current_track').value)" style="cursor:pointer" src="<?php echo $cmpnt_img_path; ?>/googleplus.png" /><br>
					<img onclick="jQuery('#embed_Url_div').css('display','');embed_url(document.getElementById('load_current_playlist_table').value,document.getElementById('load_current_track').value)" style="cursor:pointer" src="<?php echo $cmpnt_img_path; ?>/embed.png" />
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
			<input type="hidden" id="load_current_track" value="0" />
			<input type="hidden" id="load_shuffle" value="<?php echo $shuffle ?>" />
			<input type="hidden" id="load_scroll_height" value="0" />
			<input type="hidden" id="load_scroll_height2" value="0" />
			<input type="hidden" value="<?php if(isset($l)) echo $l; ?>" id="load_lib_table_count"/>
			<input type="hidden" value="" id="load_current_lib_table"/>
			<input type="hidden" value="0" id="load_current_playlist_table"/>
			<input type="hidden" value="<?php echo $theme->defaultRepeat ?>" id="load_repeat"/>
			<input type="hidden" value="0" id="load_HD_on"/>
			<input type="hidden" value="" id="load_volumeBar_width"/>

			<script>
			var loadVideo = document.getElementById('load_videoID'); //or
			var loadVideo = jQuery('#load_videoID').get(0); //or
			var loadVideo = jQuery('#load_videoID')[0];

			/*-=-=- return a jQuery object -=-=-*/
			var loadVideo = jQuery('#load_videoID');
			var paly_load = jQuery('#load_control_btns .btnPlay');
			var pause_load = jQuery('#load_control_btns .btnPause');

			<?php 
			if($typeselect == 0){
				if($load_vds["type"]=="youtube" || $load_vds["type"]=="rtmp" ){ ?> 
					jQuery("#load_control_btns .btnPlay, #load_control_btns .btnPause, #load_volumeTD, #load_control_btns #timeBarNum, #load_control_btns .fullScreen, #load_control_btns .hd").css('display',"none");
					jQuery("#load_spvp_control").addClass('youRtmp');
				<?php }
				else { ?>
					jQuery("#load_spvp_control").removeClass('youRtmp');
				<?php }
			}
			if($typeselect == 1){ 
				if($load_single_video_type=="youtube" || $load_single_video_type=="rtmp"){ ?>
					jQuery("#load_control_btns .playPrev, #load_control_btns .playNext").css('display',"none");
					jQuery("#load_spvp_control").addClass('singYouRtmp');
					jQuery("#load_control_btns .btnPlay, #load_control_btns .btnPause, #load_volumeTD, #load_control_btns #timeBarNum, #load_control_btns .fullScreen, #load_control_btns .hd").css('display',"none");
					jQuery("#load_spvp_control").addClass('youRtmp');
				<?php 
				}
				else { ?>
					jQuery("#load_control_btns .playPrev, #load_control_btns .playNext").css('display',"none");
					jQuery("#load_spvp_control").removeClass('youRtmp');
				<?php }
			}
			?> 

			function embed_url(a,b) {
				jQuery('#embed_Url').html('<iframe allowFullScreen allowTransparency="true" frameborder="0" width="<?php echo $theme->appWidth ?>" height="<?php echo $theme->appHeight ?>" src="'+location.href+'&AlbumId='+a+'&TrackId='+b+'&tmpl=component" type="text/html" ></iframe>')
				jQuery('#embed_Url').focus(); jQuery('#embed_Url').select();
			}

			jQuery('#load_control_btns .share, #load_share_buttons').on('mouseenter',function(){
				left = jQuery('#load_control_btns .share').position().left+<?php echo ($theme->appWidth-240); ?>;
				if(parseInt(jQuery('#load_spvp_playlist').css('width'))==0){
					jQuery('#load_share_buttons').css('left',left);
				}
				else {
					<?php if ($theme->playlistPos==1){ ?>
						jQuery('#load_share_buttons').css('left',left+<?php echo $theme->playlistWidth ?>);
					<?php } else { ?>
						jQuery('#load_share_buttons').css('left',left);
					<?php } ?>
				}
				jQuery('#load_share_buttons').css('display','');
			})

			jQuery('#load_control_btns .share, #load_share_buttons').on('mouseleave',function(){
				jQuery('#load_share_buttons').css('display','none')
			})

			if(<?php echo $theme->autoPlay ?>==1) {
				setTimeout(function(){jQuery('#thumb_0').click()},500);
				<?php if($typeselect==1)  { ?>
					setTimeout(function(){loadVideo[0].click()},500);
				<?php } ?>
			}

			<?php if($theme->defaultShuffle=='shuffleOff') { ?>
				if(jQuery('#load_control_btns .shuffle')[0]) {
					jQuery('#load_control_btns .shuffle')[0].style.display = "none";
					jQuery('#load_control_btns .shuffle')[1].style.display = "";
				}
			<?php
			} else { ?>
			if(jQuery('#load_control_btns .shuffle')[0]) {
				jQuery('#load_control_btns .shuffle')[1].style.display = "none";
				jQuery('#load_control_btns .shuffle')[0].style.display = "";
			}
			<?php } ?>

			jQuery('#load_control_btns .fullScreen').on('click',function(){
				if(loadVideo[0].mozRequestFullScreen)
					loadVideo[0].mozRequestFullScreen();
				if(loadVideo[0].webkitEnterFullscreen)
					loadVideo[0].webkitEnterFullscreen()
				if(loadVideo[0].msRequestFullscreen)
					loadVideo[0].msRequestFullscreen()
			})

			jQuery('.stop').on('click',function(){
				loadVideo[0].currentTime = 0;
				loadVideo[0].pause();
				paly_load.css('display',"block");
				pause_load.css('display',"none");
			})

			<?php if($theme->defaultRepeat=='repeatOff'){ ?>
				if(jQuery('#load_control_btns .load_repeat')[0]) {
					jQuery('#load_control_btns .load_repeat')[0].style.display = "none";
					jQuery('#load_control_btns .load_repeat')[1].style.display = "";
					jQuery('#load_control_btns .load_repeat')[2].style.display = "none";
				}
			<?php } ?>

			<?php if($theme->defaultRepeat=='repeatOne'){ ?>
				if(jQuery('#load_control_btns .load_repeat')[0]) {
					jQuery('#load_control_btns .load_repeat')[0].style.display = "none";
					jQuery('#load_control_btns .load_repeat')[1].style.display = "none";
					jQuery('#load_control_btns .load_repeat')[2].style.display = "";
				}
			<?php } ?>

			<?php if($theme->defaultRepeat=='repeatAll'){ ?>
				if(jQuery('#load_control_btns .load_repeat')[0]) {
					jQuery('#load_control_btns .load_repeat')[0].style.display = "";
					jQuery('#load_control_btns .load_repeat')[1].style.display = "none";
					jQuery('#load_control_btns .load_repeat')[2].style.display = "none";
				}
			<?php } ?>

			jQuery('#load_control_btns .load_repeat').on('click',function(){
				load_repeat=jQuery('#load_repeat').val();
				switch (load_repeat) {
					case 'repeatOff':
						jQuery('#load_repeat').val('repeatOne');
						jQuery('#load_control_btns .load_repeat')[0].style.display = "none";
						jQuery('#load_control_btns .load_repeat')[1].style.display = "none";
						jQuery('#load_control_btns .load_repeat')[2].style.display = "";
					break;
					case 'repeatOne':
						jQuery('#load_repeat').val('repeatAll');
						jQuery('#load_control_btns .load_repeat')[0].style.display="";
						jQuery('#load_control_btns .load_repeat')[1].style.display="none";
						jQuery('#load_control_btns .load_repeat')[2].style.display="none";
					break;
					case 'repeatAll':
						jQuery('#load_repeat').val('repeatOff');
						jQuery('#load_control_btns .load_repeat')[0].style.display="none";
						jQuery('#load_control_btns .load_repeat')[1].style.display="";
						jQuery('#load_control_btns .load_repeat')[2].style.display="none";
					break;
				}
			})

			jQuery('#load_volumeTD #voulume_img').on('click',function(){
				if(jQuery('#load_volumeTD .load_volume')[0].style.width!='0%') {
					loadVideo[0].load_volume = 0;
					jQuery('#load_volumeBar_width').val(jQuery('#load_volumeTD .load_volume')[0].style.width)
					jQuery('#load_volumeTD .load_volume').css('width','0%')
				}
				else {
					loadVideo[0].load_volume = parseInt(jQuery('#load_volumeBar_width').val())/100;
					jQuery('#load_volumeTD .load_volume').css('width',jQuery('#load_volumeBar_width').val())
				}
			})

			jQuery('#load_control_btns .hd').on('click',function(){
				load_current_time = loadVideo[0].currentTime;
				load_HD_on = jQuery('#load_HD_on').val();
				load_current_playlist_table = jQuery('#load_current_playlist_table').val();
				load_current_track=jQuery('#load_current_track').val();

				if(jQuery('#load_track_list_'+load_current_playlist_table).find('#urlHD_'+load_current_track).val() && load_HD_on==0) {
					document.getElementById('load_videoID').src=jQuery('#load_track_list_'+load_current_playlist_table).find('#urlHD_'+load_current_track).val();
					play();
					setTimeout('loadVideo[0].currentTime=load_current_time',500)
					jQuery('#load_HD_on').val(1);
				}
				if(jQuery('#load_track_list_'+load_current_playlist_table).find('#urlHD_'+load_current_track).val() && load_HD_on==1) {
					jQuery('#load_track_list_'+load_current_playlist_table).find('#thumb_'+load_current_track).click();
					setTimeout('loadVideo[0].currentTime=load_current_time',500)
					jQuery('#load_HD_on').val(0);
				}
			})

			function support(i,j) {
				if(jQuery('#load_track_list_'+i).find('#vid_type_'+j).val()!='http') {
					jQuery('#not_supported').css('display','');
					jQuery('#support').val(0);
				}
				else {
					jQuery('#not_supported').css('display','none');
					jQuery('#support').val(1);
				}
			}

			jQuery('.play').on('click',function(){  loadVideo[0].play();  })
			jQuery('.pause').on('click',function(){ loadVideo[0].pause(); })

			/*-=-=- single video thumb -=-=-*/
			jQuery(function() {
				var load_videos  = jQuery("#spvp_single_video_main");
				load_videos.on('click', function(ev) {
					jQuery(this).addClass("player");
					jQuery(this).find('.spvp_single_video_main')[0].src += "&autoplay=1";
					ev.preventDefault();
				});
			});

			function load_vid_select(x){
				jQuery("div.load_vid_thumb").each(function(){
					if(jQuery(this).find("img")) {
						jQuery(this).find("img").hide(20);
					if(jQuery(this).find("img")[0])
						jQuery(this).find("img")[0].style.display="none";
					}	
					jQuery(this).css('background','none');
				})

				jQuery("div.load_vid_thumb").each(function(){
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

			function load_vid_select2(x){
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

			function load_playlist_select(x) {
				jQuery("#load_spvp_album_div td.playlist_td").each(function(){
					jQuery(this).css('background','none');
					jQuery(this).css('color','#<?php echo $theme->textColor  ?>');
					this.onmouseover = function(){this.style.color='#'+'<?php echo $theme->textHoverColor?>' ;this.style.background='rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 0, 2)) ?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 2, 2))?>,<?php echo HEXDEC(SUBSTR($theme->itemBgHoverColor, 4, 2)) ?>,0.4)'}
					this.onmouseout = function(){this.style.color='<?php echo '#'.$theme->textColor ?>';this.style.background=" none"}
				})

				jQuery('#load_playlist_'+x).css('background','rgba(<?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 0, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 2, 2)) ?>, <?php echo HEXDEC(SUBSTR($theme->itemBgSelectedColor, 4, 2)) ?>, <?php echo $theme->framesBgAlpha/100; ?>)')
				jQuery('#load_playlist_'+x).css('color','#<?php echo $theme->textSelectedColor  ?>')
				jQuery('#load_playlist_'+x)[0].onmouseover = null
				jQuery('#load_playlist_'+x)[0].onmouseout = null
			}

			jQuery('#load_control_btns .shuffle').on('click', function() {
				if(jQuery('#load_shuffle').val()==0) {
					jQuery('#load_shuffle').val(1);
					jQuery('#load_control_btns .shuffle')[1].style.display="none";
					jQuery('#load_control_btns .shuffle')[0].style.display="";
				}
				else {
					jQuery('#load_shuffle').val(0);
					jQuery('#load_control_btns .shuffle')[0].style.display="none";
					jQuery('#load_control_btns .shuffle')[1].style.display="";
				}
			});

			jQuery("div.load_vid_thumb").each(function(){
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

			function load_timeUpdate(){
				if(parseInt(document.getElementById('load_videoID').currentTime/60)<10 && parseInt(document.getElementById('load_videoID').currentTime % 60<10))
					document.getElementById('load_time').innerHTML = '0'+parseInt(document.getElementById('load_videoID').currentTime/60)+':0'+parseInt(document.getElementById('load_videoID').currentTime % 60);

				if(parseInt(document.getElementById('load_videoID').currentTime/60)<10)
					document.getElementById('load_time').innerHTML = '0'+parseInt(document.getElementById('load_videoID').currentTime/60)+':'+parseInt(document.getElementById('load_videoID').currentTime % 60);

				if(parseInt(document.getElementById('load_videoID').currentTime % 60)<10)
					document.getElementById('load_time').innerHTML = '0'+parseInt(document.getElementById('load_videoID').currentTime/60)+':0'+parseInt(document.getElementById('load_videoID').currentTime % 60);
			}

			function load_durationChange() {
				if(parseInt(document.getElementById('load_videoID').duration/60)<10 && parseInt(document.getElementById('load_videoID').duration % 60<10))
					document.getElementById('load_duration').innerHTML = '0'+parseInt(document.getElementById('load_videoID').duration/60)+':0'+parseInt(document.getElementById('load_videoID').duration % 60);

				if(parseInt(document.getElementById('load_videoID').duration/60)<10)
					document.getElementById('load_duration').innerHTML = '0'+parseInt(document.getElementById('load_videoID').duration/60)+':'+parseInt(document.getElementById('load_videoID').duration % 60);

				if(parseInt(document.getElementById('load_videoID').duration % 60)<10)
					document.getElementById('load_duration').innerHTML = parseInt(document.getElementById('load_videoID').duration/60)+':0'+parseInt(document.getElementById('load_videoID').duration % 60);
			}

			function load_scrollBottom(){
				load_current_playlist_table = document.getElementById('load_current_playlist_table').value;
				if(document.getElementById('load_scroll_div_'+load_current_playlist_table).offsetHeight+parseInt(document.getElementById("load_scroll_div_"+load_current_playlist_table).style.top)+55<=document.getElementById('load_spvp_global_body').offsetHeight)
					return false;
				document.getElementById('load_scroll_height').value = parseInt(document.getElementById('load_scroll_height').value)+5
				document.getElementById("load_scroll_div_"+load_current_playlist_table).style.top="-"+document.getElementById('load_scroll_height').value+"px";
			};
			function scrollTop(){
				load_current_playlist_table = document.getElementById('load_current_playlist_table').value;
				if(document.getElementById('load_scroll_height').value<=0)
					return false;
				document.getElementById('load_scroll_height').value = parseInt(document.getElementById('load_scroll_height').value)-5
				document.getElementById("load_scroll_div_"+load_current_playlist_table).style.top="-"+document.getElementById('load_scroll_height').value+"px";
			};

			function scrollBottom2(){
				load_current_playlist_table = document.getElementById('load_current_playlist_table').value;
				if(!load_current_playlist_table) {
					load_current_playlist_table = 0;
				}
				if(document.getElementById('load_scroll_div2_'+load_current_playlist_table).offsetHeight+parseInt(document.getElementById("load_scroll_div2_"+load_current_playlist_table).style.top)+150<=document.getElementById('load_spvp_global_body').offsetHeight)
					return false;
				document.getElementById('load_scroll_height2').value = parseInt(document.getElementById('load_scroll_height2').value)+5
				document.getElementById("load_scroll_div2_"+load_current_playlist_table).style.top = "-"+document.getElementById('load_scroll_height2').value+"px";
			};
			function scrollTop2(){
				load_current_playlist_table = document.getElementById('load_current_playlist_table').value;
				if(document.getElementById('load_scroll_height2').value<=0)
					return false;
				document.getElementById('load_scroll_height2').value = parseInt(document.getElementById('load_scroll_height2').value)-5
				document.getElementById("load_scroll_div2_"+load_current_playlist_table).style.top = "-"+document.getElementById('load_scroll_height2').value+"px";
			};

			function load_openPlaylist(i,j) {
				document.getElementById('load_scroll_height').value = 0;
				load_lib_table_count = document.getElementById('load_lib_table_count').value;
				for(lib_table=0;lib_table<load_lib_table_count;lib_table++) {
					document.getElementById('load_lib_table_'+lib_table).style.display = "none";
				}
				jQuery("#load_playlist_table_"+i).fadeIn(700);
				document.getElementById('load_current_lib_table').value = j;
				document.getElementById('load_current_playlist_table').value = i;
				document.getElementById('tracklist_down').style.display = "" ;
				document.getElementById('tracklist_up').style.display = "";
				document.getElementById('button29').style.display = "block";
				document.getElementById('button27').onclick = function(){nextPlaylist()};
				document.getElementById('button28').onclick = function(){prevPlaylist()};
			}
			function nextPlaylist(){
				document.getElementById('load_scroll_height').value = 0;
				load_lib_table_count = document.getElementById('load_lib_table_count').value;
				for(lib_table=0;lib_table<load_lib_table_count;lib_table++){
					document.getElementById('load_lib_table_'+lib_table).style.display="none";
				}
				load_current_lib_table = document.getElementById('load_current_lib_table').value;
				next_playlist_table = parseInt(document.getElementById('load_current_playlist_table').value)+1;
				load_current_playlist_table = parseInt(document.getElementById('load_current_playlist_table').value);
				if(next_playlist_table><?php echo count($load_playlist_array)-2 ?>)
					return false;
				jQuery("#load_playlist_table_"+load_current_playlist_table).css('display','none');
				jQuery("#load_playlist_table_"+next_playlist_table).fadeIn(700);

				document.getElementById('load_current_playlist_table').value = next_playlist_table;
				document.getElementById('tracklist_down').style.display="" ;
				document.getElementById('tracklist_up').style.display="";
				document.getElementById('button29').style.display="block";
			}
			function prevPlaylist(){
				document.getElementById('load_scroll_height').value = 0;
				load_lib_table_count = document.getElementById('load_lib_table_count').value;
				for(lib_table=0;lib_table<load_lib_table_count;lib_table++)	{
					document.getElementById('load_lib_table_'+lib_table).style.display="none";
				}
				load_current_lib_table = document.getElementById('load_current_lib_table').value;
				prev_playlist_table = parseInt(document.getElementById('load_current_playlist_table').value)-1;
				load_current_playlist_table = parseInt(document.getElementById('load_current_playlist_table').value);
				if(prev_playlist_table<0)
					return false;
				jQuery("#load_playlist_table_"+load_current_playlist_table).css('display','none');
				jQuery("#load_playlist_table_"+prev_playlist_table).fadeIn(700);

				document.getElementById('load_current_playlist_table').value = prev_playlist_table;
				document.getElementById('tracklist_down').style.display = "" ;
				document.getElementById('tracklist_up').style.display = "";
				document.getElementById('button29').style.display = "block";
			}

			function load_openLibTable() {
				load_current_lib_table = document.getElementById('load_current_lib_table').value;
				document.getElementById('load_scroll_height').value = 0;
				load_current_playlist_table = document.getElementById('load_current_playlist_table').value;
				jQuery("#load_lib_table_"+load_current_lib_table).fadeIn(700);
				document.getElementById('load_playlist_table_'+load_current_playlist_table).style.display = "none";
				document.getElementById('tracklist_down').style.display = "none" ;
				document.getElementById('tracklist_up').style.display = "none";
				document.getElementById('button29').style.display = "none";
				document.getElementById('button27').onclick = function(){load_nextPage()};
				document.getElementById('button28').onclick = function(){load_prevPage()};
			}

			var next_page = 0;
			function load_nextPage() {
				if(next_page==document.getElementById('load_lib_table_count').value-1)
				return false;
				next_page = next_page+1;
				for(g=0; g<document.getElementById('load_lib_table_count').value; g++){
					document.getElementById('load_lib_table_'+g).style.display = "none";
					if(g==next_page) {
						jQuery("#load_lib_table_"+g).fadeIn(900);
					}
				}
			}
			function load_prevPage() {
				if(next_page==0)
					return false;
				next_page = next_page-1;
				for(g=0; g<document.getElementById('load_lib_table_count').value; g++) {
					document.getElementById('load_lib_table_'+g).style.display = "none";
					if(g==next_page) {
						jQuery("#load_lib_table_"+g).fadeIn(900);
					}
				}
			}

			function load_playBTN() {
				load_current_playlist_table = document.getElementById('load_current_playlist_table').value;
				track_list = document.getElementById('track_list').value;
				document.getElementById('load_track_list_'+load_current_playlist_table).style.display = "block";
				if(load_current_playlist_table!=track_list)
					document.getElementById('load_track_list_'+track_list).style.display = "none";
				
				document.getElementById('track_list').value = load_current_playlist_table;
				loadVideo[0].play();
				paly_load.css('display',"none");
				pause_load.css('display',"block");
			}

			function play() {
				loadVideo[0].play();
				paly_load.css('display',"none");
				pause_load.css('display',"block");
			}

			/*-=-=- Play/Pause control clicked -=-=-*/
			jQuery('#load_control_btns .btnPlay <?php if($theme->clickOnVid==1) echo ',#load_videoID' ?>, #load_control_btns .btnPause').on('click', function() {
				if(loadVideo[0].paused) {
					loadVideo[0].play();
					paly_load.css('display',"none");
					pause_load.css('display',"block");
				}
				else {
				  loadVideo[0].pause();
				  paly_load.css('display',"block");
				  pause_load.css('display',"none");
				}
				return false;
			});


			function check_volume() {
				load_percentage = loadVideo[0].load_volume * 100;
				jQuery('#load_volumeTD .load_volume').css('width', load_percentage+'%');
				document.getElementById("load_spvp_playlist").style.width = '0px';
				document.getElementById("load_spvp_playlist").style.display = 'none';
			}
			window.onload = check_volume();

			/*-=-=- get HTML5 video time duration -=-=-*/
			loadVideo.on('loadedmetadata', function() {
			   jQuery('.duration').text(loadVideo[0].duration);
			});

			/*-=-=- update HTML5 video current play time -=-=-*/
			loadVideo.on('timeupdate', function() {
				var load_progress = jQuery('#load_spvp_control .progressBar');
				var currentPos = loadVideo[0].currentTime; // Get currenttime
				var load_maxduration = loadVideo[0].duration; // Get video duration
				var load_percentage = 100 * currentPos / load_maxduration; //in %
				var load_position = (<?php echo $theme->appWidth; ?> * load_percentage / 100)-load_progress.offset().left; 
				jQuery('#load_spvp_control .timeBar').css('width', load_percentage+'%');
			});

			loadVideo.on('ended',function(){
				if(jQuery('#load_repeat').val()=="repeatOne")  {
					loadVideo[0].currentTime = 0;
					loadVideo[0].play();
					paly_load.css('display',"none");
					pause_load.css('display',"block");
				}
				if(jQuery('#load_repeat').val()=="repeatAll") {
					jQuery('#load_control_btns .playNext').click();
				}
				if(jQuery('#load_repeat').val()=="repeatOff"){
					if(load_vid_num==load_video_urls.length-1) {
						loadVideo[0].currentTime = 0;
						loadVideo[0].pause();
						paly_load.css('display',"block");
						pause_load.css('display',"none");
					}
				}
				<?php if($theme->autoNext==1) { ?>
					if(jQuery('#load_repeat').val()=="repeatOff") 
					if(load_vid_num==load_video_urls.length-1) {
						loadVideo[0].currentTime=0;
						loadVideo[0].pause();
						paly_load.css('display',"block");
						pause_load.css('display',"none");
					}
					else {	
						jQuery('#load_control_btns .playNext').click();
					}
				<?php } ?>
			})

			/* Drag status */
			var timeDrag = false;
			jQuery('#load_spvp_control .progressBar').mousedown(function(e) {
			   timeDrag = true;
			   load_updatebar(e.pageX);
			});

			jQuery('#load_spvp_control .progressBar').select(function(){ })
			 
			jQuery(document).mouseup(function(e) {
				if(timeDrag) {
					timeDrag = false;
					load_updatebar(e.pageX);
				}
			});

			jQuery(document).mousemove(function(e) {
				if(timeDrag) {
					load_updatebar(e.pageX);
				}
			});

			/*-=-=- update Progress Bar control -=-=-*/
			var load_updatebar = function(x) {
				var load_progress = jQuery('#load_spvp_control .progressBar');
				var load_maxduration = loadVideo[0].duration; //Video duraiton
				var load_position = x - load_progress.offset().left; //Click pos
				var load_percentage = 100 * load_position / load_progress.width();
				//Check within range
				if(load_percentage > 100) {
					load_percentage = 100;
				}
				if(load_percentage < 0) {
					load_percentage = 0;
				}
				//Update progress bar and video currenttime
				jQuery('#load_spvp_control .timeBar').css('width', load_percentage+'%');
				jQuery('.spanA').css('left', load_position+'px');
				loadVideo[0].currentTime = load_maxduration * load_percentage / 100;
			};

			/*-=-=- loop to get HTML5 video buffered data -=-=-*/
			function startBuffer() {
				setTimeout(function(){
					var load_maxduration = loadVideo[0].duration;
					var load_currentBuffer = loadVideo[0].buffered.end(0);
					var load_percentage = 100 * load_currentBuffer / load_maxduration;
					jQuery('#load_spvp_control .bufferBar').css('width', load_percentage+'%');
					if(load_currentBuffer < load_maxduration) {
						setTimeout(startBuffer, 500);
					}
				},800)
			};

			checkVideoLoad=setInterval(function(){
				if(loadVideo[0].duration) {
					setTimeout(startBuffer(), 500);
					clearInterval(checkVideoLoad)
				}
			}, 1000)

			/*-=-=- Mute/Unmute control clicked -=-=-*/
			var load_volume = jQuery('#load_volumeTD .load_volumeBar');
			jQuery('.muted').click(function() {
				loadVideo[0].muted = !loadVideo[0].muted;
				return false;
			});

			/*-=-=- Volume control clicked -=-=-*/
			jQuery('#load_volumeTD .load_volumeBar').on('mousedown', function(e) {
				var load_position = e.pageX - load_volume.offset().left;
				var load_percentage = 100 * load_position / load_volume.width();
				jQuery('#load_volumeTD .load_volume').css('width', load_percentage+'%');
				loadVideo[0].load_volume = load_percentage / 100;
			});

			/* Drag status */
			var volumeDrag = false;
			jQuery('#load_volumeTD .load_volumeBar').mousedown(function(e) {
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
				var load_progress = jQuery('#load_volumeTD .load_volumeBar');
				var load_position = x - load_progress.offset().left; //Click pos
				var load_percentage = 100 * load_position / load_progress.width();
				//Check within range
				if(load_percentage > 100) {
					load_percentage = 100;
				}
				if(load_percentage < 0) {
					load_percentage = 0;
				}
				//Update progress bar and video currenttime
				jQuery('#load_volumeTD .load_volume').css('width', load_percentage+'%');
				loadVideo[0].load_volume =  load_percentage / 100;
			};

			var yy=1;
			controlHideTime='';
			jQuery("#load_spvp_global_body").each(function(){
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
								jQuery("#load_spvp_playlist").animate({
									width: "0px",
								},300 );
								setTimeout(function(){ jQuery("#load_spvp_playlist").css('display','none');},300)
								jQuery(".load_spvp_control").animate({
									width: <?php echo $theme->appWidth; ?>+"px",
									<?php if ($theme->playlistPos==1){ ?>
										marginLeft:'0px'
									<?php } else {?>
										marginRight:'0px'
									<?php } ?>
								}, 300 );
								jQuery("#load_control_btns").animate({
									width: <?php echo $theme->appWidth?>+"px",
								}, 300 );

								<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
									jQuery("#load_videoID").animate({
										width: <?php echo $theme->appWidth ?>+"px",
										marginLeft:'0px'
									}, 300 );  
								<?php } ?>

								<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
									jQuery("#load_videoID").animate({
										width: <?php echo $theme->appWidth ?>+"px",
									}, 300 );  
								<?php } ?>
							<?php } ?>

							<?php if($theme->ctrlsSlideOut==1){ ?>
								jQuery('.load_spvp_control').hide("slide", { direction: "<?php if($theme->ctrlsPos==1) echo 'up'; else echo 'down'; ?>" }, 1000);
							<?php } ?>

							<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
								jQuery("#load_videoID").animate({
									width: <?php echo $theme->appWidth ?>+"px",
									marginLeft:'0px'
								}, 300 );  
							<?php } ?>

							<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
								jQuery("#load_videoID").animate({
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
					if(document.getElementById('load_spvp_control').style.display=="none") {
						jQuery('.load_spvp_control').show("slide", { direction: "<?php if($theme->ctrlsPos==1) echo 'up'; else echo 'down'; ?>" }, 450);
					}
				})
			})


			var xx = 1; 
			volumeHideTime = '';
			jQuery("#load_volumeTD").each(function(){
				jQuery('#load_volumeTD').mouseleave(function() {
					volumeHideTime = setInterval(function(){
						xx=xx+1;
						if(xx<2) {
							return false
						}
						else {
							clearInterval(volumeHideTime);
							xx=1;				
							jQuery("#load_control_btns #space").animate({
								paddingLeft:<?php echo (($theme->appWidth*20)/100)+'px' ?>,
							},1000);
							
							jQuery("#load_volumeTD #volumebar_player").animate({ 
								width:'0px',
							},1000);
							
							load_percentage = loadVideo[0].load_volume * 100;
							jQuery('#load_volumeTD .load_volume').css('width', load_percentage+'%');
						}
					},1000)
				})

				jQuery('#load_volumeTD').mouseenter(function() {
					if(volumeHideTime) {
						clearInterval(volumeHideTime)
						xx=1;
					}
					jQuery("#load_control_btns #space").animate({ 
						paddingLeft:<?php echo (($theme->appWidth*20)/100)-100+'px' ?>,
					},500);

					jQuery("#load_volumeTD #volumebar_player").animate({ 
						width:'100px',
					},500);
				});
			})


			jQuery('#load_control_btns .playlist').on('click', function() { 
				if(document.getElementById("load_spvp_playlist").style.width=="0px") { 
					jQuery("#load_spvp_playlist").css('display','')
					
					jQuery("#load_spvp_playlist").animate({
						width: <?php echo $theme->playlistWidth; ?>+"px",
					}, 500 );
					
					jQuery(".load_spvp_control").animate({
						width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
						<?php if ($theme->playlistPos==1){ ?>
							marginLeft:<?php echo $theme->playlistWidth; ?>+'px'
						<?php } else {?>
							marginRight:<?php echo $theme->playlistWidth; ?>+'px'
						<?php } ?>
					}, 500 );
					
					jQuery("#load_control_btns").animate({
						width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
					}, 500 );

					<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
						jQuery("#load_videoID").animate({
							width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
							marginLeft:<?php echo $theme->playlistWidth ?>
						}, 500 );  
					<?php } ?>
					
					<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
						jQuery("#load_videoID").animate({
							width: <?php echo $theme->appWidth-$theme->playlistWidth; ?>+"px",
						}, 500 );  
					<?php } ?>
				}
				else {
					jQuery("#load_spvp_playlist").animate({
						width: "0px",
					}, 1500 );
					
					setTimeout(function(){ jQuery("#load_spvp_playlist").css('display','none');},1500)
					
					jQuery(".load_spvp_control").animate({
						width: <?php echo $theme->appWidth; ?>+"px",
						<?php if ($theme->playlistPos==1){ ?>
							marginLeft:'0px'
						<?php } else {?>
							marginRight:'0px'
						<?php } ?>
					}, 1500 );

					jQuery("#load_control_btns").animate({
						width: <?php echo $theme->appWidth?>+"px",
					}, 1500 );

					<?php if($theme->playlistOverVid==0 && $theme->playlistPos==1){ ?>
						jQuery("#load_videoID").animate({
							width: <?php echo $theme->appWidth ?>+"px",
							marginLeft:'0px'
						}, 1500 );  
					<?php } ?>
					
					<?php if($theme->playlistOverVid==0 && $theme->playlistPos==2){ ?>
						jQuery("#load_videoID").animate({
							width: <?php echo $theme->appWidth ?>+"px",
						}, 1500 );  
						<?php } ?>
				}
			});

			load_current_playlist_table = document.getElementById('load_current_playlist_table').value;
			load_video_urls = jQuery('#load_track_list_'+load_current_playlist_table).find('.load_vid_thumb');

			function load_current_playlist_videos(){
				load_current_playlist_table = document.getElementById('load_current_playlist_table').value;
				load_video_urls = jQuery('#load_track_list_'+load_current_playlist_table).find('.load_vid_thumb');
			}

			var load_vid_num=0;
			var load_used_track = new Array();
			jQuery('#load_control_btns .playPrev').on('click', function() {
				load_used_track[load_used_track.length] =load_vid_num;
				load_vid_num++;
				if(jQuery('#load_shuffle').val()==1){
					load_vid_num=parseInt(Math.random() * (load_video_urls.length+1 - 0) + 0);
					while(in_array(load_vid_num,load_used_track)){
						load_vid_num=parseInt(Math.random() * (load_video_urls.length+1 - 0) + 0);
					}
				}
				if(load_used_track.length>=load_video_urls.length){
					load_used_track=[];
				}
				if(load_vid_num<0){
					load_vid_num = load_video_urls.length-1;
				}
				load_video_urls[load_vid_num].click();
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


			jQuery('#load_control_btns .playNext').on('click', function() {
				load_used_track[load_used_track.length] = load_vid_num;
				load_vid_num++;
				if(jQuery('#load_shuffle').val()==1){
					load_vid_num=parseInt(Math.random() * (load_video_urls.length+1 - 0) + 0);
					while(in_array(load_vid_num,load_used_track)){
						load_vid_num=parseInt(Math.random() * (load_video_urls.length+1 - 0) + 0);
					}
				}
				if(load_used_track.length>=load_video_urls.length){
					load_used_track=[];
				}
				jQuery('#load_spvp_control .timeBar').css('width', '0%');
				if(load_vid_num==load_video_urls.length){
					load_vid_num=0;
				}
				load_video_urls[load_vid_num].click();
			});


			jQuery("#load_control_btns .lib").click(function () {
				jQuery('#load_spvp_album_div').css('transform','');
				jQuery('#load_spvp_global_body').css('transform','');
				jQuery('#load_spvp_global_body').transition({
					perspective: '700px',
					rotateY: '180deg',
				},1000);

				setTimeout(function(){
					jQuery('#load_spvp_album_div').css('-ms-transform','rotateY(180deg)')
					jQuery('#load_spvp_album_div').css('transform','rotateY(180deg)')

					jQuery('#load_spvp_album_div').css('-o-transform','rotateY(180deg)')
					document.getElementById('load_spvp_album_div').style.display='block'
					document.getElementById('load_spvp_video_div').style.display='none'
				},300);
				
				setTimeout(function(){
					jQuery('#load_spvp_album_div').css('-ms-transform','');
					jQuery('#load_spvp_global_body').css('-ms-transform','');

					jQuery('#load_spvp_album_div').css('transform','');
					jQuery('#load_spvp_global_body').css('transform','');

					jQuery('#load_spvp_album_div').css('-o-transform','');
					jQuery('#load_spvp_global_body').css('-o-transform','');
				},1100);
			})


			jQuery("#load_spvp_album_div .show_vid").click(function () {
				jQuery('#load_spvp_global_body').transition({
					perspective: '700px',
					rotateY: '180deg',
				},1000);

				setTimeout(function(){
					jQuery('#load_spvp_video_div').css('-ms-transform','rotateY(180deg)')
					jQuery('#load_spvp_video_div').css('transform','rotateY(180deg)')
					jQuery('#load_spvp_video_div').css('-o-transform','rotateY(180deg)')
					document.getElementById('load_spvp_album_div').style.display='none'
					document.getElementById('load_spvp_video_div').style.display='block'
				},300);
				
				setTimeout(function(){
					jQuery('#load_spvp_video_div').css('-ms-transform','');
					jQuery('#load_spvp_global_body').css('-ms-transform','');

					jQuery('#load_spvp_video_div').css('transform','');
					jQuery('#load_spvp_global_body').css('transform',''); 

					jQuery('#load_spvp_video_div').css('-o-transform','');
					jQuery('#load_spvp_global_body').css('-o-transform','');
				},1100);
			})

			/*-=-=- COLOR -=-=-*/
			var canvas=[]
			var ctx=[]
			var originalPixels=[]
			var currentPixels=[]
			for(i=1;i<30;i++)
				if(document.getElementById('button'+i)) {
					canvas[i] = document.createElement("canvas");
					ctx[i] = canvas[i].getContext("2d");
					originalPixels[i] = null;
					currentPixels[i] = null;
				}
					

			function load_getPixels() {	
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
				
			function load_changeColor() {
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
				jQuery('#load_spvp_global_body ,#load_videoID').each(function(){
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
				if(loadVideo[0].paused) {
					loadVideo[0].play();
					paly_load.css('display',"none");
					pause_load.css('display',"block");
				}
				else {
					loadVideo[0].pause();
					paly_load.css('display',"block");
					pause_load.css('display',"none");
				}
			}

			jQuery('#track_list_0').find('#thumb_0').click();

			<?php if($load_vds["type"]=="http" || $load_single_video_type=="http"){ ?>
				loadVideo[0].pause();
			<?php } ?>

			if(paly_load && pause_load) {
				paly_load.css('display',"block");
				pause_load.css('display',"none");
			}

			<?php if($AlbumId!=''){ ?>
				jQuery('#load_track_list_<?php echo $AlbumId ?>').find('#thumb_<?php echo $TrackId ?>').click();
			<?php } ?>
			/*-=-=- END COLOR -=-=-*/


			jQuery('#load_spvp_global_body').find('img').last().load(function(){setTimeout('load_getPixels();load_changeColor();',1600)})
			jQuery('#load_volumeTD .load_volume')[0].style.width='<?php echo $theme->defaultVol?>%';
			loadVideo[0].load_volume=<?php echo $theme->defaultVol/100 ;?>;
			<?php if($theme->ctrlsSlideOut==0) { ?>
				jQuery('#load_videoID').mouseenter();
			<?php } ?>

			<?php if($theme->openPlaylistAtStart==1) { ?>
				jQuery('#load_control_btns .playlist').click();
			<?php } ?>
			</script>		
		</div>
		
		<div id="spidervideoplayerflash_plugin<?php echo $idd; ?>" style="display:none">
			
			<script type="text/javascript" src="components/com_spidervideoplayer/js/swfobject.js"></script>
			
			<div id="flashcontent<?php echo $idd ?>"  style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px"></div>

			<script type="text/javascript">
			function load_flashShare(type,b,c){
				load_u=location.href;
				if(load_u=='<?php echo JURI::root(); ?>index.php?' || load_u=='<?php echo JURI::root(); ?>' )
					load_u='<?php echo JURI::root(); ?>index.php?';
				else if(!location.search)
					load_u=load_u+'?';
				else
					load_u=load_u+'&';

				t=document.title;
				switch (type) {
					case 'fb':	
						window.open('http://www.facebook.com/sharer.php?load_u='+encodeURIComponent(load_u+'AlbumId='+b+'&TrackId='+c)+'&t='+encodeURIComponent(t), "Facebook","menubar=1,resizable=1,width=350,height=250");
					break;
					
					case 'g':
						window.open('http://plus.google.com/share?url='+encodeURIComponent(load_u+'AlbumId='+b+'&TrackId='+c)+'&t='+encodeURIComponent(t), "Google","menubar=1,resizable=1,width=350,height=250");
					break;

					case 'tw':
						window.open('http://twitter.com/home/?status='+encodeURIComponent(load_u+'&AlbumId='+b+'&TrackId='+c), "Twitter","menubar=1,resizable=1,width=350,height=250");
					break;
				}
			}	
			var load_so = new SWFObject("<?php echo JURI::root();?>components/com_spidervideoplayer/videoPlayer.swf?wdrand=<?php echo mt_rand() ?>", "Player", "100%", "100%", "8", "#000000");

			load_so.addParam("FlashVars", "settingsUrl=<?php echo $url; ?>");
			load_so.addParam("quality", "high");
			load_so.addParam("menu", "false");
			load_so.addParam("wmode", "transparent");
			load_so.addParam("loop", "false");
			load_so.addParam("allowfullscreen", "true");
			load_so.write("flashcontent<?php echo $idd ?>");
			</script>
		</div>

		<script>
		var html5_plugin = document.getElementById("spidervideoplayerhtml5_plugin<?php echo $idd; ?>");
		var flash_plugin = document.getElementById("spidervideoplayerflash_plugin<?php echo $idd; ?>");

		if(!FlashDetect.installed || '<?php echo $priority ?>'=='HTML5'){
			flash_plugin.parentNode.removeChild(flash_plugin);
			html5_plugin.style.display='';
		}
		else {
			html5_plugin.parentNode.removeChild(html5_plugin);
			flash_plugin.style.display='';
		}
		</script>


		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
