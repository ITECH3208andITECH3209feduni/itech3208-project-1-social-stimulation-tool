<?php 
/**
* @package Spider Video Player
* @author Web-Dorado
* @copyright (C) 2012 Web-Dorado. All rights reserved.
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JFormFieldPlaylist extends JFormFieldText {

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
		$editor	=& JFactory::getEditor('tinymce');
		$editor->display('text_for_date','','100%','250','40','6');
		$document =& JFactory::getDocument();
		$db	=& JFactory::getDBO(); ?>

		<script type="text/javascript">
		var next = 0;
		function set_height(){
			document.getElementById('playlists').parentNode.parentNode.parentNode.parentNode.parentNode.style.height="inherit";
		}

		function jSelectVideo(VIDS, title, thumb, number_of_vids) {
			set_height();
			playlist_ids = document.getElementById('playlists').value;
			tbody = document.getElementById('playlist');

			for(i=0; i<VIDS.length; i++) {
				tr = document.createElement('tr');
				tr.setAttribute('playlist_id', VIDS[i]);
				tr.setAttribute('id', next);

				var td_info = document.createElement('td');
				td_info.setAttribute('id','info_'+next);
				td_info.setAttribute('width','90%');

				b = document.createElement('b');
				b.innerHTML = title[i];
				b.style.marginRight = "15px";
				b.style.float = 'left';

				p_info = document.createElement('p');
				p_info.style.fontStyle = "normal";
				p_info.style.color = "#666";
				p_info.style.display = "inline";
				p_info.style.float = 'left';
				p_info.innerHTML = 'Number of videos: '+number_of_vids[i];

				img = document.createElement('img');
				img.setAttribute('align','left');
				img.src = thumb[i];
				if(thumb[i]) {	
					img.style.width = '80px';
					img.style.marginRight = "10px";
				}
				else {	
					img.style.width = '0px';
					img.style.marginRight = "0px";
					b.style.width = '';
					p_info.style.display = "";
					p_info.style.float = '';
					b.style.float = '';
				}
				td_info.appendChild(img);
				td_info.appendChild(b);
				td_info.appendChild(p_info);
				//td.appendChild(p_url);

				var img_X = document.createElement("img");
				img_X.setAttribute("src", "components/com_spidervideoplayer/images/delete_el.png");
				//img_X.setAttribute("height", "17");
				img_X.style.cssText = "cursor:pointer; margin: 0 auto; display: block;width: 15px; max-width: 15px;";
				img_X.setAttribute("onclick", 'remove_row("'+next+'")');

				var td_X = document.createElement("td");
				td_X.setAttribute("id", "X_"+next);
				td_X.setAttribute("valign", "middle");
				//td_X.setAttribute("align", "right");
				td_X.style.width = '50px';
				td_X.appendChild(img_X);

				var img_UP = document.createElement("img");
				img_UP.setAttribute("src", "components/com_spidervideoplayer/images/up.png");
				//img_UP.setAttribute("height", "17");
				img_UP.style.cssText = "cursor:pointer; margin:0 auto; max-width: 15px;width: 15px;";
				img_UP.setAttribute("onclick", 'up_row("'+next+'")');

				var td_UP = document.createElement("td");
				td_UP.setAttribute("id", "up_"+next);
				td_UP.setAttribute("valign", "middle");
				td_UP.style.width = '20px';
				td_UP.appendChild(img_UP);

				var img_DOWN = document.createElement("img");
				img_DOWN.setAttribute("src", "components/com_spidervideoplayer/images/down.png");
				//img_DOWN.setAttribute("height", "17");
				img_DOWN.style.cssText = "max-width:15px; width:15px; margin:0; cursor:pointer";
				img_DOWN.setAttribute("onclick", 'down_row("'+next+'")');

				var td_DOWN = document.createElement("td");
				td_DOWN.setAttribute("id", "down_"+next);
				td_DOWN.setAttribute("valign", "middle");
				td_DOWN.style.width = '20px';
				td_DOWN.appendChild(img_DOWN);

				tr.appendChild(td_info);
				tr.appendChild(td_X);
				tr.appendChild(td_UP);
				tr.appendChild(td_DOWN);
				tbody.appendChild(tr);

				//refresh
				next++;
			}
			document.getElementById('playlists').value=playlist_ids;
			refresh_();
		}

		function jSelectVideoS(VIDS, title, thumb, number_of_vids) {
			set_height();
			playlist_ids =document.getElementById('playlists').value;
			tbody = document.getElementById('playlist');

			for(i=0; i<VIDS.length; i++) {
				tr = document.createElement('tr');
				tr.setAttribute('playlist_id', VIDS[i]);
				tr.setAttribute('id', next);

				var td_info = document.createElement('td');
				td_info.setAttribute('id','info_'+next);
				//td_info.setAttribute('width','60%');

				b = document.createElement('b');
				b.innerHTML = title[i];
				b.style.width='50px';
				b.style.float='left';
				b.style.position="inherit";

				p_info = document.createElement('p');
				p_info.style.fontStyle="normal";
				p_info.style.color="#666";
				p_info.style.display="inline";
				p_info.style.float='left';
				p_info.innerHTML ='Number of videos: '+number_of_vids[i];

				img = document.createElement('img');
				img.setAttribute('align','left');
				img.src = thumb[i];
				if(thumb[i]) {	
					img.style.width='80px';
					img.style.marginRight="10px";
				}
				else {	
					img.style.width='0px';
					img.style.marginRight="0px";
					b.style.width='';
					p_info.style.display="";
					p_info.style.float='';
					b.style.float='';
				}
				td_info.appendChild(img);
				td_info.appendChild(b);
				td_info.appendChild(p_info);
				//td.appendChild(p_url);

				var img_X = document.createElement("img");
				img_X.setAttribute("src", "components/com_spidervideoplayer/images/delete_el.png");
				//img_X.setAttribute("height", "17");
				img_X.style.cssText = "cursor:pointer; margin-left:30px";
				img_X.setAttribute("onclick", 'remove_row("'+next+'")');

				var td_X = document.createElement("td");
				td_X.setAttribute("id", "X_"+next);
				td_X.setAttribute("valign", "middle");
				//td_X.setAttribute("align", "right");
				td_X.style.width='50px';
				td_X.appendChild(img_X);

				var img_UP = document.createElement("img");
				img_UP.setAttribute("src", "components/com_spidervideoplayer/images/up.png");
				//img_UP.setAttribute("height", "17");
				img_UP.style.cssText = "cursor:pointer";
				img_UP.setAttribute("onclick", 'up_row("'+next+'")');

				var td_UP = document.createElement("td");
				td_UP.setAttribute("id", "up_"+next);
				td_UP.setAttribute("valign", "middle");
				td_UP.style.width='20px';
				td_UP.appendChild(img_UP);

				var img_DOWN = document.createElement("img");
				img_DOWN.setAttribute("src", "components/com_spidervideoplayer/images/down.png");
				//img_DOWN.setAttribute("height", "17");
				img_DOWN.style.cssText = "margin:2px;cursor:pointer";
				img_DOWN.setAttribute("onclick", 'down_row("'+next+'")');

				var td_DOWN = document.createElement("td");
				td_DOWN.setAttribute("id", "down_"+next);
				td_DOWN.setAttribute("valign", "middle");
				td_DOWN.style.width='20px';
				td_DOWN.appendChild(img_DOWN);

				tr.appendChild(td_info);
				tr.appendChild(td_X);
				tr.appendChild(td_UP);
				tr.appendChild(td_DOWN);
				tbody.appendChild(tr);

				//refresh
				next++;
			}

			document.getElementById('playlists').value=playlist_ids;
			refresh_();
			SqueezeBox.close();

		}

		function remove_row(id){
			set_height();
			tr=document.getElementById(id);
			tr.parentNode.removeChild(tr);
			refresh_();
		}

		function refresh_(){
			playlist=document.getElementById('playlist');
			GLOBAL_tbody=playlist;
			tox='';
			for (x=0; x < GLOBAL_tbody.childNodes.length; x++) {
				tr=GLOBAL_tbody.childNodes[x];
				tox=tox+tr.getAttribute('playlist_id')+',';
			}

			document.getElementById('playlists').value=tox;
		}

		function up_row(id){
			form=document.getElementById(id).parentNode;
			k=0;
			while(form.childNodes[k]) {
				if(form.childNodes[k].getAttribute("id"))
				if(id==form.childNodes[k].getAttribute("id"))
					break;
				k++;
			}
			if(k!=0) {
				up=form.childNodes[k-1];
				down=form.childNodes[k];
				form.removeChild(down);
				form.insertBefore(down, up);
				refresh_();
			}
		}

		function down_row(id){
			form=document.getElementById(id).parentNode;
			l=form.childNodes.length;
			k=0;
			while(form.childNodes[k]) {
				if(id==form.childNodes[k].id)
					break;
				k++;
			}

			if(k!=l-1) {
				up=form.childNodes[k];
				down=form.childNodes[k+2];
				form.removeChild(up);
				if(!down)
					down=null;
				form.insertBefore(up, down);
				refresh_();
			}
		}
		</script>
		<table class="videoTypeSelPlayList">
			<tr>
				<td>
					<a class="modal" href="index.php?option=com_spidervideoplayer&amp;task=select_playlist&amp;tmpl=component&amp;object=id" rel="{handler: 'iframe', size: {x: 850, y: 575}}">
						<img src="components/com_spidervideoplayer/images/add_but.png" /> 
					</a>
					<table width="100%">
						<tbody id="playlist"></tbody>
					</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="<?php echo $name ?>" id="playlists" size="80" value="<?php echo $value; ?>" />
		<?php
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_spidervideoplayer/tables');
		$playlists = array();
		$playlists_id = explode(',',$value);
		$playlists_id = array_slice($playlists_id,0, count($playlists_id)-1);  
		foreach($playlists_id as $playlist_id) {
			$query = "SELECT * FROM #__spidervideoplayer_playlist WHERE id=".$playlist_id ;
			$db->setQuery($query); 
			$is = $db->loadObject();
			if($is)
				$playlists[] = $db->loadObject();
			if ($db->getErrorNum()) {
				echo $db->stderr();
				return false;
			}	
		}

		if($playlists) {
			foreach($playlists as $playlist) {
				$v_ids[] = $playlist->id;
				$v_titles[] = addslashes($playlist->title);
				$v_thumbs[] = addslashes($playlist->thumb);
				$v_number_of_vids[] = substr_count($playlist->videos, ',');
			}

			$v_id='["'.implode('","',$v_ids).'"]';
			$v_title='["'.implode('","',$v_titles).'"]';
			$v_thumb='["'.implode('","',$v_thumbs).'"]';
			$v_number_of_vid='["'.implode('","',$v_number_of_vids).'"]'; ?>
			<script type="text/javascript">                
				jSelectVideo(<?php echo $v_id?>,<?php echo $v_title?>,<?php echo $v_thumb?>,<?php echo $v_number_of_vid?>);
			</script>
		<?php } ?>
			
		<?php
		$content=ob_get_contents();
		ob_end_clean();
		return $content;

	}
}
?>