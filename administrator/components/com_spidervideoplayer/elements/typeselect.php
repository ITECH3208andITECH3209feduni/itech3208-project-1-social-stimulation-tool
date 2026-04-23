<?php
/**
* @package Spider Random Article
* @author Web-Dorado
* @copyright (C) 2012 Web-Dorado. All rights reserved.
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldtypeselect extends JFormField {
	var	$_name = 'typeselect';
	function getInput() {
		ob_start();
		static $embedded;
		if(!$embedded) {
			$embedded = true;
		} ?>
		<span id="cuc"></span>
		<fieldset name="gag" class="radio videoTypeSelect">
			<label for="show0">
				<input type="radio" name="<?php echo $this->name;?>"  value="0" <?php if($this->value==0) echo 'checked="checked"'?> onChange="show_(0)" id="show0"> Playlist
			</label>
			<label for="show1">
				<input type="radio" name="<?php echo $this->name;?>"  value="1" <?php if($this->value==1) echo 'checked="checked"'?> onChange="show_(1)" id="show1"> Single Video 
			</label>
		</fieldset>
		
		<script type="text/javascript">
		<?php 
		$db	= JFactory::getDBO();
		$query = "SELECT MAX(version_id) FROM #__schemas";
		$db->setQuery($query);
		$version = $db->loadResult();
		if((float)$version>3.1) { ?>
			var i=11;
			var j=9;
			var k=7;
		<?php
		}
		else { ?>
			var i=11;
			var j=9;
			var k=7;
		<?php } ?>
		
		jQuery( "#jform_params_typeselect-lbl" ).text("Typeselect");
		jQuery( "#jform_params_priority" ).append( "<p id='priorityFieldMess' style='color:#ff0000;'>YouTube videos are not supported on flash player.</p>" );
		jQuery("#priorityFieldMess").hide();

		jQuery('#jform_params_priority input[type="radio"]').change(function() {
			if (this.value == 'flash') {
				jQuery("#priorityFieldMess").show();
			}
			else if (this.value == 'html5') {
				jQuery("#priorityFieldMess").hide();
			}
		});

		function show_(x) {
			videoTypeSelPlayList = jQuery("table.videoTypeSelPlayList").parent().parent();
			videoTypeSelVideo = jQuery("div.videoTypeSelVideo").parent().parent();
			
			if(x==0) {
				//alert('playlist');
				videoTypeSelPlayList.show(); videoTypeSelVideo.hide();				
			}
			else {
				//alert('single');
				videoTypeSelPlayList.hide(); videoTypeSelVideo.show(); 
			}
		}
		jQuery( document ).ready(function() {
			videoTypeSelPlayList = jQuery("table.videoTypeSelPlayList").parent().parent();
			videoTypeSelVideo = jQuery("div.videoTypeSelVideo").parent().parent();
			
			priorityFieldMess = jQuery("#priorityFieldMess");
			
			if(jQuery('.videoTypeSelect input#show0').is(':checked')) { 
				videoTypeSelPlayList.show(); videoTypeSelVideo.hide(); 
			}
			else { 
				videoTypeSelPlayList.hide(); videoTypeSelVideo.show();
			}
				
			if(jQuery('#jform_params_priority input#jform_params_priority0').is(':checked')) { priorityFieldMess.show(); }
				else { priorityFieldMess.hide(); }
		});
		</script>
		<?php
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>