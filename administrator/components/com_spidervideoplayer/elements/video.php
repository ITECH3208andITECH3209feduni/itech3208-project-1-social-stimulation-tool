<?php 
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_spidervideoplayer/tables');
class JFormFieldVideo extends JFormFieldText {

	//function fetchElement($name, $value, &$node, $control_name)
	function getInput() {        
		ob_start();
        static $embedded;
        if(!$embedded) {
            $embedded=true;
        }
		
		JHTML::_('behavior.modal', 'a.modal');
		
		$name = $this->name;
		$value = $this->value;
		$video =& JTable::getInstance('spidervideoplayer_video', 'Table');
		if ($value) {
			$video->load($value);
		} else {
			$video->title = JText::_('Select a Video');
		}
		
		$editor	=& JFactory::getEditor('tinymce');
		$editor->display('text_for_date','','100%','250','40','6');
		$document =& JFactory::getDocument();
		$db	=& JFactory::getDBO();
		?>


		<script type="text/javascript"> 
			function jSelectArticle(id, title, object) {
				document.getElementById(object + '_id').value = id;
				document.getElementById(object + '_name').value = title;
				parent.SqueezeBox.close();
			}

			<?php 
			$db	= JFactory::getDBO();
			$query="SELECT MAX(version_id) FROM #__schemas";
			$db->setQuery($query);
			$version=$db->loadResult();

			if((float)$version>3.1) { ?>
				var i=11; var j=9; var k=7;
				
				var a=3; var b=3;
				var c=2; var d=1;

			<?php
			}
			else { ?>
			var i=11; var j=9; var k=7;

			var a=3; var b=3; 
			var c=3; var d=1;

			<?php } ?>
		</script>

		<?php
		$link = 'index.php?option=com_spidervideoplayer&amp;task=select_video_menu&amp;tmpl=component&amp;object='.$name;

		JHTML::_('behavior.modal', 'a.modal');
		
		$html = "\n".'<div class="videoTypeSelVideo"><div style="float: left;"><input style="background: #ffffff;" type="text" id="video_name" value="'.$video->title.'" disabled="disabled" /></div>';
		//$html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('Select')."\" />";
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('Select a Video').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('Select').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="video_id" name="'.$name.'" value="'.(int)$value.'" />';
		$html .="\n".'<div id="cuc2"></div>';
		$html .="\n"." <script>
			if(document.getElementById('cuc2').parentNode.parentNode.parentNode.childNodes[a].childNodes[b].childNodes[c].childNodes[d].checked) {
				document.getElementById('cuc2').parentNode.parentNode.setAttribute('style','display:none');
			if(document.getElementById('cuc2').parentNode.parentNode.parentNode.childNodes[i])
				document.getElementById('cuc2').parentNode.parentNode.parentNode.childNodes[j].removeAttribute('style');
			else
				document.getElementById('cuc2').parentNode.parentNode.parentNode.childNodes[k].removeAttribute('style');
		}
		else {
			document.getElementById('cuc2').parentNode.parentNode.removeAttribute('style');
			if(document.getElementById('cuc2').parentNode.parentNode.parentNode.childNodes[i])
				document.getElementById('cuc2').parentNode.parentNode.parentNode.childNodes[j].setAttribute('style','display:none');
			else
				document.getElementById('cuc2').parentNode.parentNode.parentNode.childNodes[k].setAttribute('style','display:none');
		}
		</script></div>";

        $content=ob_get_contents();

        ob_end_clean();
        return $content.$html;

	}
}
?>