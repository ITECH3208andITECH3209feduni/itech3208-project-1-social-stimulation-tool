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
$priority=$this->priority;
if(!$priority)
	$priority='flash';
?>

<script src="components/com_spidervideoplayer/js/jquery-1.7.2.js"></script>

<div id="spidervideoplayerflash" style="display:none;positio"><?php echo $this->loadTemplate('flash'); ?></div>
<div id="spidervideoplayerhtml5" style="display:none;height:<?php echo $params->appHeight ?>px"><?php echo $this->loadTemplate('html5'); ?></div>

<script src="components/com_spidervideoplayer/js/flash_detect.js"></script>
<script type="text/javascript"> 
var html5 = document.getElementById("spidervideoplayerhtml5");
var flash = document.getElementById("spidervideoplayerflash");

    if(!FlashDetect.installed || <?php echo $priority ?>==html5) {
		flash.parentNode.removeChild(flash);
		spidervideoplayerhtml5.style.display='';
    }
	else {
		html5.parentNode.removeChild(html5);
		spidervideoplayerflash.style.display='';
    }
</script>
			