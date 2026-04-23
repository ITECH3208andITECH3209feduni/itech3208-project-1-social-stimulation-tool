<?php
/**
* @package Spider Video Player Lite
* @author Web-Dorado
* @copyright (C) 2012 Web-Dorado. All rights reserved.
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
class HTML_contact
{
	///////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////// V I D E O //////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////
	function add_video($lists, $tags){
		JRequest::setVar( 'hidemainmenu', 1 );
		JHTML::_('behavior.modal', 'a.modal');
		$editor	=& JFactory::getEditor('tinymce'); ?>
		<script language="javascript" type="text/javascript">
		Joomla.submitbutton= function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel_video') {
				submitform( pressbutton );
				return;
			}
			if(form.title.value=="") {
				alert('Set Video title');
				return;
			}
			<?php
			foreach($tags as $tag) {
				if($tag->published)
				if($tag->required)
				echo 'if(document.getElementById("params.'.$tag->id.'").value==""){
					alert("Set '.$tag->name.'");
					return;
				}';
			}
			?>
			submitform( pressbutton );
		}
		
		function removeVideo(id) {
			document.getElementById(id+"_link").innerHTML='Select Video';
			document.getElementById(id).value='';
		}
		function change_type(type) {
			switch(type){
				case 'http':
					if(document.getElementById('url_http').value!='')
						document.getElementById('url_http_link').innerHTML=document.getElementById('url_http').value;
					else
						document.getElementById('url_http_link').innerHTML="Select Video";
					
					document.getElementById('div_url').style.display="inline";
					//document.getElementById('url').value='';
					document.getElementById('url_youtube').type='hidden';
					document.getElementById('url_rtmp').type='hidden';
					//document.getElementById('urlHtml5_link').innerHTML="Select Video";
					document.getElementById('div_urlHtml5').style.display="inline";
					document.getElementById('urlHtml5').type='hidden';
					//document.getElementById('urlHtml5').value='';
					document.getElementById('tr_urlHtml5').removeAttribute('style');
					//document.getElementById('urlHdHtml5_link').innerHTML="Select Video";
					document.getElementById('div_urlHdHtml5').style.display="inline";
					document.getElementById('urlHdHtml5').type='hidden';
					//document.getElementById('urlHdHtml5').value='';
					document.getElementById('tr_urlHdHtml5').removeAttribute('style');
					document.getElementById('urlHD_rtmp').type='hidden';
					if(document.getElementById('urlHD').value!='')
					document.getElementById('urlHD_link').innerHTML=document.getElementById('urlHD').value;
					else
					document.getElementById('urlHD_link').innerHTML="Select Video"
					document.getElementById('div_urlHD').style.display="inline";
					document.getElementById('urlHD').type='hidden';
					//document.getElementById('urlHD').value='';
					document.getElementById('tr_fmsUrl').setAttribute('style','display:none');
					document.getElementById('tr_urlHD').removeAttribute('style');
					//new
					document.getElementById('flash_note').setAttribute('style','display:none');
				break;
				
				case 'youtube':
					document.getElementById('div_url').style.display="none";
					document.getElementById('url_youtube').type='text';
					document.getElementById('url_youtube').size='100';
					document.getElementById('url_rtmp').type='hidden';
					document.getElementById('div_urlHtml5').style.display="none";
					document.getElementById('tr_urlHtml5').setAttribute('style','display:none');
					document.getElementById('div_urlHdHtml5').style.display="none";
					document.getElementById('tr_urlHdHtml5').setAttribute('style','display:none');
					document.getElementById('div_urlHD').style.display="none";
					document.getElementById('tr_urlHD').setAttribute('style','display:none');
					document.getElementById('tr_fmsUrl').setAttribute('style','display:none');
					//document.getElementById('urlHD').type='text';
					//document.getElementById('urlHD').value='';
					//document.getElementById('urlHD').size='100';
					//document.getElementById('fmsUrl').value='';
					// new
					document.getElementById('flash_note').setAttribute('style','display:inline-block');
				break;
				
				case 'rtmp':
					document.getElementById('div_url').style.display="none";
					document.getElementById('url_youtube').type='hidden';
					document.getElementById('url_rtmp').size='100';
					document.getElementById('url_rtmp').type='text';
					document.getElementById('div_urlHD').style.display="none";
					document.getElementById('urlHD_rtmp').type='text';
					//document.getElementById('urlHD').value='';
					document.getElementById('urlHD_rtmp').size='100';
					document.getElementById('fmsUrl').value='';
					document.getElementById('div_urlHtml5').style.display="none";
					document.getElementById('tr_urlHtml5').setAttribute('style','display:none');
					document.getElementById('div_urlHdHtml5').style.display="none";
					document.getElementById('tr_urlHdHtml5').setAttribute('style','display:none');
					document.getElementById('tr_fmsUrl').removeAttribute('style');
					document.getElementById('tr_urlHD').removeAttribute('style');
					//new
					document.getElementById('flash_note').setAttribute('style','display:none');
				break;
				
				default:
					alert('def')
			}
		}
		
		i=0;
		function add() {
			var input_tr=document.createElement('tr');
			input_tr.setAttribute("id", "params_tr_"+i);
			var input_name_td=document.createElement('td');
			var input_value_td=document.createElement('td');
			var input_span_td=document.createElement('td');
			var input_name=document.createElement('input');
			input_name.setAttribute("type", "text");
			input_name.setAttribute("name", "pname_"+i);
			input_name.setAttribute("id", "pname_"+i);
			var input_value=document.createElement('input');
			input_value.setAttribute("type", "text");
			input_value.setAttribute("name", "pvalue_"+i);
			input_value.setAttribute("id", "pvalue_"+i);
			var span=document.createElement('span');
			span.setAttribute("style", "cursor:pointer; border:1px solid black; margin-left:10px; font-size:10px");
			span.setAttribute("id", "span_"+i);
			span.setAttribute("onclick", "remove_('"+i+"')");
			span.innerHTML="&nbsp;X&nbsp;";
			input_span_td.appendChild(span);
			input_tr.appendChild(input_name_td);
			input_tr.appendChild(input_value_td);
			input_tr.appendChild(input_span_td);
			input_name_td.appendChild(input_name);
			input_value_td.appendChild(input_value);
			document.getElementById("params_tbody").appendChild(input_tr);
			i++;
		}
		function remove_(x) {
			node=document.getElementById('params_tr_'+x);
			parent_=node.parentNode;
			parent_.removeChild(node);
		}
		</script>
		<style>
			table.admintable.spiderVideoTab14 { border:1px solid #dedede; width:80%; }
			table.admintable.spiderVideoTab14 td { padding:10px; }
			table.admintable.spiderVideoTab14 td.key { width:240px; }
			table.admintable.spiderVideoTab14 td.key label { 
				text-align:center;
				font-size:14px;
				font-weight:700;
			}
			table.admintable.spiderVideoTab14 td input { margin:10px 0; }
			table.admintable.spiderVideoTab14 td #video_list td { padding:0; }
			table.admintable.spiderVideoTab14 td.p_value_type {  padding: 15px 10px; }
			table.admintable.spiderVideoTab14 td.p_value_type label {  display: inline-block; }
			table.admintable.spiderVideoTab14 td.p_value_type input {  margin: 0; }
			table.admintable.spiderVideoTab14 td.p_value_type span#flash_note { 
				color: #ff0000;
				width: 200px;
			}
			table.admintable.spiderVideoTab14 td a.modal { 
				display:table;
				margin:10px 0;
			}
			table.admintable.spiderVideoTab14 td table img {
				cursor:pointer;
				display:block;
				margin:0 auto;
			}
			table.admintable.spiderVideoTab14 td .controls label {
				margin: 10px 16px 10px 0px;
				float: left;
				padding: 0;
			}
			table.admintable.spiderVideoTab14 td .controls label input { margin:2px 6px 2px 0px; }			
		</style>
		<?php $editor->display('text_for_date','','100%','250','40','6');?>
		<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
			<table class="admintable spiderVideoTab14" border="1">
				<tr>
					<td class="key">
						<label for="title"><?php echo JText::_( 'Title' ); ?>:</label>
					</td>
					<td >
						<input type="text" name="title" id="title" size="80" />
					</td>
				</tr>
				
				<tr>
					<td class="key">
						<label for="type"><?php echo JText::_( 'Type' ); ?>:</label>
					</td>
					<td class="p_value_type">
						<span><input type="radio" value="http" id="p_http" name="type" checked="checked" onchange="change_type('http')" />
						<label for="p_http"><?php echo JText::_( 'http' ); ?>:</label></span>
						
						<span><input type="radio" value="youtube"  id="p_youtube" name="type" onchange="change_type('youtube')" />
						<label for="p_youtube"><?php echo JText::_( 'youtube' ); ?>:</label></span>
						
						<span><input type="radio" value="rtmp" id="p_rtmp" name="type" onchange="change_type('rtmp')" />
						<label for="p_rtmp"><?php echo JText::_( 'rtmp' ); ?>:</label></span>
						
						<span id="flash_note" style="display:none">
						<?php echo JText::_( '* Only for HTML5 Player' ); ?>
						</span>
					</td>
				</tr>
				
				<tr id="tr_fmsUrl" style="display:none" >
					<td class="key">
						<label for="fmsUrl"><?php echo JText::_( 'fmsUrl' ); ?>:</label>
					</td>
					<td  id="td_fmsUrl">
						<input type="text" name="fmsUrl" id="fmsUrl" size="100"/>
					</td>
				</tr>
				
				<tr>
					<td class="key">
						<label for="File"><?php echo JText::_( 'URl' ); ?>:</label>
					</td>
					<td id="td_url" >
						<div id="div_url">
							<a class="modal" title="Select Video" href="index.php?option=com_spidervideoplayer&task=media_manager_video&type=url_http&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}" id="url_http_link"><?php echo JText::_( 'Select Video' ); ?></a>
							<a href="javascript:removeVideo('url');"><?php echo JText::_( 'Remove Video' ); ?></a>
						</div>
						<input type="hidden" name="url" id="url"/>
						<input  type="hidden" name="url_http" id="url_http" value=""  />
						<input type="hidden" name="url_youtube" id="url_youtube" value=""  />
						<input type="hidden" name="url_rtmp" id="url_rtmp" value=""  />
					</td>
				</tr>
				
				<tr id="tr_urlHtml5" >
					<td class="key">
						<label for="urlHtml5"><?php echo JText::_( 'Url(HTML5) MP4,WebM,Ogg' ); ?>:</label>
					</td>
					<td  id="td_urlHtml5">
						<div id="div_urlHtml5">
							<a class="modal" title="Select Video" href="index.php?option=com_spidervideoplayer&task=media_manager_video&type=urlHtml5&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}" id="urlHtml5_link"><?php echo JText::_( 'Select Video' ); ?></a>
							<a href="javascript:removeVideo('urlHtml5');"><?php echo JText::_( 'Remove Video' ); ?></a>
						</div>
						<input type="hidden" name="urlHtml5" id="urlHtml5"/>
					</td>
				</tr>
				
				<tr id="tr_urlHD" >
					<td class="key">
						<label for="UrlHD"><?php echo JText::_( 'UrlHD' ); ?>:</label>
					</td>
					<td  id="td_urlHD">
						<div id="div_urlHD">
							<a class="modal" title="Select Video" href="index.php?option=com_spidervideoplayer&task=media_manager_video&type=urlHD&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}" id="urlHD_link"><?php echo JText::_( 'Select Video' ); ?></a>
							<a href="javascript:removeVideo('urlHD');"><?php echo JText::_( 'Remove Video' ); ?></a>
						</div>
						<input type="hidden" name="urlHD" id="urlHD"/>
						<input type="hidden" name="urlHD_rtmp" id="urlHD_rtmp"/>
					</td>
				</tr>
				
				<tr id="tr_urlHdHtml5" >
					<td class="key">
						<label for="urlHdHtml5"><?php echo JText::_( 'UrlHD(HTML5) MP4,WebM,Ogg' ); ?>:</label>
					</td>
					<td id="td_urlHdHtml5">
						<div id="div_urlHdHtml5">
							<a class="modal" title="Select Video" href="index.php?option=com_spidervideoplayer&task=media_manager_video&type=urlHdHtml5&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}" id="urlHdHtml5_link"><?php echo JText::_( 'Select Video' ); ?></a>
							<a href="javascript:removeVideo('urlHdHtml5');"><?php echo JText::_( 'Remove Video' ); ?></a>
						</div>
						<input type="hidden" name="urlHdHtml5" id="urlHdHtml5"/>
					</td>
				</tr>
				
				<tr>
					<td class="key">
						<label for="Thumb"><?php echo JText::_( 'Thumb' ); ?>:</label>
					</td>
					<td>
						<input type="hidden" value="" name="thumb" id="thumb" />
						<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_image&type=thumb&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}"><?php echo JText::_( 'Select Image' ); ?></a>
						<a href="javascript:removeImage();"><?php echo JText::_( 'Remove Image' ); ?></a>
						<div style="height:150px;">
							<img style="display:none;height:150px" height="150" id="imagebox" src="" />
						</div>
						<script type="text/javascript">
						function removeImage(){
							document.getElementById("imagebox").style.display="none";
							document.getElementById("thumb").value='';
						}
						</script>
					</td>
				</tr>
				
				<?php
				if($tags)
				foreach($tags as $tag) {
					if($tag->published)
					echo '<tr>
					<td class="key">
						<label for="title">'.$tag->name.':</label>
					</td>
					<td ><input type="text" name="params['.$tag->id.']" id="params.'.$tag->id.'" size="80" /></td></tr>';
				}
				?>				
				<tr>
					<td class="key"> <label for="published"><?php echo JText::_( 'Published' ); ?>:</label> </td>
					<td> <?php echo $lists['published']; ?> </td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	function show_video(&$rows, &$pageNav, &$lists){
		JHTML::_('behavior.tooltip'); ?>
		<script type="text/javascript">
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'cancel'){
				submitform( pressbutton );
				return;
			}
			submitform( pressbutton );
		}
		Joomla.tableOrdering=function( order, dir, task ) {
			var form = document.adminForm;
			form.filter_order_video.value     = order;
			form.filter_order_Dir_video.value = dir;
			submitform( task );
		}
		</script>
		<form action="index.php?option=com_spidervideoplayer" method="post" name="adminForm" id="adminForm">
			<table width="100%">
				<tr>
					<td align="left" width="100%">
						<input type="text" name="search_video" id="search_video" value="<?php echo $lists['search_video'];?>" class="text_area" placeholder="Search" style="margin:0px" />
						<button class="btn tip hasTooltip" type="submit" data-original-title="Search"><i class="icon-search"></i></button>
						<button class="btn tip hasTooltip" type="button" onclick="document.getElementById('search_video').value='';this.form.submit();" data-original-title="Clear"><i class="icon-remove"></i></button>
						<div class="btn-group pull-right hidden-phone">
							<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
							<?php echo $pageNav->getLimitBox(); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="pull-left"> <?php echo $lists['filter_playlistid'];?></div>
					</td>
				</tr>
			</table>
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="30"><?php echo '#'; ?></th>
						<th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)"></th>
						<th width="40" class="title"><?php echo JHTML::_('grid.sort',   'ID', 'id', @$lists['order_Dir'], @$lists['order'] ); ?></td>
						<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Type', 'type', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Url', 'url', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'UrlHD', 'urlHD', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Thumb', 'thumb', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th nowrap="nowrap" width="70"><?php echo JHTML::_('grid.sort',   'Published', 'published',@$lists['order_Dir'], @$lists['order'] ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="11"> <?php echo $pageNav->getListFooter(); ?> </td>
					</tr>
				</tfoot>
				<?php
				$k = 0;
				for($i=0, $n=count($rows); $i < $n ; $i++) {
					$row = &$rows[$i];
					$checked 	= JHTML::_('grid.id', $i, $row->id);
					$published 	= published($row, $i, 'video');
					$link 		= JRoute::_( 'index.php?option=com_spidervideoplayer&task=edit_video&cid[]='. $row->id );
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $i+1?></td>
						<td><?php echo $checked?></td>
						<td align="center"><?php echo $row->id?></td>
						<td><a href="<?php echo $link;?>"><?php echo $row->title?></a></td>
						<td><?php echo $row->type?></td>
						<td><?php echo $row->url?></td>
						<td><?php echo $row->urlHD?></td>
						<td align="center"><img width="50" src="<?php echo $row->thumb?>" title="<?php echo $row->thumb?>" /></td>
						<td align="center"><?php echo $published?></td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
			</table>
			
			<input type="hidden" name="option" value="com_spidervideoplayer">
			<input type="hidden" name="task" value="video">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="filter_order_video" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir_video" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
	<?php
	}
	
	function edit_video(&$lists, &$row, &$tags){
		JRequest::setVar( 'hidemainmenu', 1 );
		JHTML::_('behavior.modal', 'a.modal');
		$editor	=& JFactory::getEditor(); ?>
		<script language="javascript" type="text/javascript">
		<!--
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'cancel_video'){
				submitform( pressbutton );
				return;
			}
			if(form.title.value=="") {
				alert('Set Video title');
				return;
			}
			<?php
			foreach($tags as $tag) {
				if($tag->published)
				if($tag->required)
				echo 'if(document.getElementById("params.'.$tag->id.'").value==""){
					alert("Set '.$tag->name.'");
					return;
				}';
			}
			?>
			submitform( pressbutton );
		}
		function removeVideo(id) {
			document.getElementById(id+"_link").innerHTML='Select Video';
			document.getElementById(id).value='';
		}
		function change_type(type) {
			switch(type){
				case 'http':
					if(document.getElementById('url_http').value!='')
						document.getElementById('url_http_link').innerHTML=document.getElementById('url_http').value;
					else
						document.getElementById('url_http_link').innerHTML="Select Video";
						document.getElementById('div_url').style.display="inline";
						//document.getElementById('url').value='';
						document.getElementById('url_youtube').type='hidden';
						document.getElementById('url_rtmp').type='hidden';
						//document.getElementById('urlHtml5_link').innerHTML="Select Video";
						document.getElementById('div_urlHtml5').style.display="inline";
						document.getElementById('urlHtml5').type='hidden';
						//document.getElementById('urlHtml5').value='';
						document.getElementById('tr_urlHtml5').removeAttribute('style');
						//document.getElementById('urlHdHtml5_link').innerHTML="Select Video";
						document.getElementById('div_urlHdHtml5').style.display="inline";
						document.getElementById('urlHdHtml5').type='hidden';
						//document.getElementById('urlHdHtml5').value='';
						document.getElementById('tr_urlHdHtml5').removeAttribute('style');
						document.getElementById('urlHD_rtmp').type='hidden';
					
					if(document.getElementById('urlHD').value!='')
						document.getElementById('urlHD_link').innerHTML=document.getElementById('urlHD').value;
					else
						document.getElementById('urlHD_link').innerHTML="Select Video"
						document.getElementById('div_urlHD').style.display="inline";
						document.getElementById('urlHD').type='hidden';
						//document.getElementById('urlHD').value='';
						document.getElementById('tr_fmsUrl').setAttribute('style','display:none');
						document.getElementById('tr_urlHD').removeAttribute('style');
						//new
						document.getElementById('flash_note').setAttribute('style','display:none');
				break;
				case 'youtube':
					document.getElementById('div_url').style.display="none";
					document.getElementById('url_youtube').type='text';
					document.getElementById('url_youtube').size='100';
					document.getElementById('url_rtmp').type='hidden';
					document.getElementById('div_urlHtml5').style.display="none";
					document.getElementById('tr_urlHtml5').setAttribute('style','display:none');
					document.getElementById('div_urlHdHtml5').style.display="none";
					document.getElementById('tr_urlHdHtml5').setAttribute('style','display:none');
					document.getElementById('div_urlHD').style.display="none";
					document.getElementById('tr_urlHD').setAttribute('style','display:none');
					document.getElementById('tr_fmsUrl').setAttribute('style','display:none');
					//document.getElementById('urlHD').type='text';
					//document.getElementById('urlHD').value='';
					//document.getElementById('urlHD').size='100';
					//document.getElementById('fmsUrl').value='';						
					// new
					document.getElementById('flash_note').setAttribute('style','display:inline-block');
				break;
				case 'rtmp':
					document.getElementById('div_url').style.display="none";
					document.getElementById('url_youtube').type='hidden';
					document.getElementById('url_rtmp').size='100';
					document.getElementById('url_rtmp').type='text';
					document.getElementById('div_urlHD').style.display="none";
					document.getElementById('urlHD_rtmp').type='text';
					//document.getElementById('urlHD').value='';
					document.getElementById('urlHD_rtmp').size='100';
					document.getElementById('fmsUrl').value='';
					document.getElementById('div_urlHtml5').style.display="none";
					document.getElementById('tr_urlHtml5').setAttribute('style','display:none');
					document.getElementById('div_urlHdHtml5').style.display="none";
					document.getElementById('tr_urlHdHtml5').setAttribute('style','display:none');
					document.getElementById('tr_fmsUrl').removeAttribute('style');
					document.getElementById('tr_urlHD').removeAttribute('style');
					//new
					document.getElementById('flash_note').setAttribute('style','display:none');
				break;
				default:
					alert('def')
				}
		}
		<?php
		$pname= array();
		$pvalue= array();
		$params=explode('#***#',$row->params);
		foreach($params as $param) {
			if($param){
				$temp=explode('#===#',$param);
				$pname[]=htmlspecialchars($temp[0]);
				$pvalue[]=htmlspecialchars($temp[1]);
			}
		}
		?>
		i=<?php echo count($pname); ?>;
		//-->
		</script>
		<style>
			table.admintable.spiderVideoTab15 { border:1px solid #dedede; width:80%; }
			table.admintable.spiderVideoTab15 td { padding:10px; }
			table.admintable.spiderVideoTab15 td.key { width:240px; }
			table.admintable.spiderVideoTab15 td.key label { 
				text-align:center;
				font-size:14px;
				font-weight:700;
			}
			table.admintable.spiderVideoTab15 td input { margin:10px 0; }
			table.admintable.spiderVideoTab15 td #video_list td { padding:0; }
			table.admintable.spiderVideoTab15 td.p_value_type {  padding: 15px 10px; }
			table.admintable.spiderVideoTab15 td.p_value_type label {  display: inline-block; }
			table.admintable.spiderVideoTab15 td.p_value_type input {  margin: 0; }
			table.admintable.spiderVideoTab15 td.p_value_type span#flash_note { 
				color: #ff0000;
				width: 200px;
			}
			table.admintable.spiderVideoTab15 td a.modal { 
				display:table;
				margin:10px 0;
			}
			table.admintable.spiderVideoTab15 td table img {
				cursor:pointer;
				display:block;
				margin:0 auto;
			}
			table.admintable.spiderVideoTab15 td .controls label {
				margin: 10px 16px 10px 0px;
				float: left;
				padding: 0;
			}
			table.admintable.spiderVideoTab15 td .controls label input { margin:2px 6px 2px 0px; }			
		</style>
		<?php  $editor->display('text_for_date','','100%','250','40','6');?>	
		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<table class="admintable spiderVideoTab15" border="1">
				<tr>
					<td class="key">
						<label for="title"><?php echo JText::_( 'Title' ); ?>:</label>
					</td>
					<td><input type="text" name="title" id="title" size="80" value="<?php echo htmlspecialchars($row->title) ?>" /></td>
				</tr>
				
				<tr>
					<td class="key">
						<label for="Type"><?php echo JText::_( 'Type' ); ?>:</label>
					</td>
					<td class="p_value_type">
						<span><input type="radio" value="http" id="p_http" name="type" <?php if($row->type=="http") echo 'checked="checked"';?> onchange="change_type('http')" /><label for="p_http"><?php echo JText::_( 'http' ); ?>:</label></span>
						<span><input type="radio" value="youtube"  id="p_youtube" name="type" <?php if($row->type=="youtube") echo 'checked="checked"';?> onchange="change_type('youtube')" /><label for="p_youtube"><?php echo JText::_( 'youtube' ); ?>:</label></span>
						<span><input type="radio" value="rtmp" id="p_rtmp" name="type" <?php if($row->type=="rtmp") echo 'checked="checked"';?> onchange="change_type('rtmp')" /><label for="p_rtmp"><?php echo JText::_( 'rtmp' ); ?>:</label></span>
						<span id="flash_note" style="display:<?php if($row->type=="youtube") echo "inline-block"; else echo "none";?>">
						<?php echo JText::_( '* Only for HTML5 Player' ); ?>
						</span>
					</td>
				</tr>
				
				<tr id="tr_fmsUrl" <?php if($row->type!="rtmp") echo 'style="display:none"'; ?>>
					<td class="key">
						<label for="fmsUrl"><?php echo JText::_( 'fmsUrl' ); ?>:</label>
					</td>
					<td  id="td_fmsUrl">
						<input type="text" name="fmsUrl" id="fmsUrl" size="100" <?php if($row->type=="rtmp") echo 'value="'.htmlspecialchars($row->fmsUrl).'"'; ?>/>
					</td>
				</tr>
				
				<tr>
					<td class="key">
						<label for="URL"><?php echo JText::_( 'URL' ); ?>:</label>
					</td>
					<td>
						<div id="div_url" style="display:<?php if($row->type=="http") echo "inline"; else echo "none";?>">
							<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_video&type=url_http&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}" id="url_http_link">
							<?php if($row->url ) echo htmlspecialchars($row->url); else echo "Select Video"; ?></a>
							<a href="javascript:removeVideo('url_http');"><?php echo JText::_( 'Remove Video' ); ?></a>
						</div>
						<input  type="hidden" name="url_http" id="url_http" value="<?php if($row->type=="http") echo htmlspecialchars($row->url) ?>"  />
						<input <?php if($row->type=="youtube") echo 'type="text"  size="100"'; else echo 'type="hidden"';?> name="url_youtube" id="url_youtube" value="<?php if($row->type=="youtube") echo htmlspecialchars($row->url) ?>"  />
						<input <?php if($row->type=="rtmp") echo 'type="text" size="100"'; else echo 'type="hidden" ';?> name="url_rtmp" id="url_rtmp" value="<?php if($row->type=="rtmp") echo htmlspecialchars($row->url) ?>" />
					</td>
				</tr>
				<tr  id="tr_urlHtml5" <?php if($row->type!="http") echo 'style="display:none"'; ?> >
					<td class="key">
						<label for="UrlHtml5"><?php echo JText::_( 'Url(HTML5) MP4,WebM,Ogg' ); ?>:</label>
					</td>
					<td>
						<div id="div_urlHtml5" style="display:<?php if($row->type=="http") echo "inline"; else echo "none";?>">
							<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_video&type=urlHtml5&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}" id="urlHtml5_link">
							<?php if($row->urlHtml5 )echo htmlspecialchars($row->urlHtml5); else echo "Select Video"; ?></a>
							<a href="javascript:removeVideo('urlHtml5');"><?php echo JText::_( 'Remove Video' ); ?></a>
						</div>
						<input type="hidden" name="urlHtml5" id="urlHtml5" value="<?php echo htmlspecialchars($row->urlHtml5) ?>" />
					</td>
				</tr>
				<tr id="tr_urlHD" <?php if($row->type=="youtube") echo 'style="display:none"'; ?> >
					<td class="key">
						<label for="UrlHD"><?php echo JText::_( 'UrlHD' ); ?>:</label>
					</td>
					<td>
						<div id="div_urlHD" style="display:<?php if($row->type=="http") echo "inline"; else echo "none";?>">
							<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_video&type=urlHD&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}" id="urlHD_link">
							<?php if($row->urlHD )echo htmlspecialchars($row->urlHD); else echo "Select Video"; ?></a>
							<a href="javascript:removeVideo('urlHD');"><?php echo JText::_( 'Remove Video' ); ?></a>
						</div>
						<input type="hidden" name="urlHD" id="urlHD" value="<?php if($row->type=="http") echo htmlspecialchars($row->urlHD) ?>" />
						<input <?php if($row->type=="rtmp") echo 'type="text" size="100"'; else echo 'type="hidden" ';?> name="urlHD_rtmp" id="urlHD_rtmp" value="<?php if($row->type=="rtmp") echo htmlspecialchars($row->urlHD) ?>" />
					</td>
				</tr>
				<tr  id="tr_urlHdHtml5" <?php if($row->type!="http") echo 'style="display:none"'; ?> >
					<td class="key">
						<label for="urlHdHtml5"><?php echo JText::_( 'UrlHD(HTML5) MP4,WebM,Ogg' ); ?>:</label>
					</td>
					<td>
						<div id="div_urlHdHtml5" style="display:<?php if($row->type=="http") echo "inline"; else echo "none";?>">
							<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_video&type=urlHdHtml5&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}" id="urlHdHtml5_link">
							<?php if($row->urlHdHtml5 )echo htmlspecialchars($row->urlHdHtml5); else echo "Select Video"; ?></a>
							<a href="javascript:removeVideo('urlHdHtml5');"><?php echo JText::_( 'Remove Video' ); ?></a>
						</div>
						<input type="hidden" name="urlHdHtml5" id="urlHdHtml5" value="<?php echo htmlspecialchars($row->urlHdHtml5) ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="Thumb"><?php echo JText::_( 'Thumb' ); ?>:</label>
					</td>
					<td>
						<input type="hidden" value="<?php echo htmlspecialchars($row->thumb); ?>" name="thumb" id="thumb" />
						<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_image&type=thumb&tmpl=component" rel="{handler: 'iframe', size: {x: 570, y: 400}}"><?php echo JText::_( 'Select Image' ); ?></a>
						<a href="javascript:removeImage();"><?php echo JText::_( 'Remove Image' ); ?></a>
						<div style=" position:absolute; width:1px; height:1px; top:0px; overflow:hidden">
							<textarea id="tempimage" name="tempimage" class="mce_editable"></textarea><br />
						</div>
						<script type="text/javascript">
						function removeImage(){
							document.getElementById("imagebox").style.display="none";
							document.getElementById("thumb").value='';
						}
						</script>
						<div style="height:150px;">
							<img style="display:<?php if($row->thumb=='') echo 'none'; else echo 'block' ?>;height:150px" height="150" id="imagebox" src="<?php echo htmlspecialchars($row->thumb) ; ?>" />
						</div>
					</td>
				</tr>
				
				<?php
				foreach($tags as $tag){
					if($tag->published)
					if( in_array($tag->id,$pname)){
					$key_value = array_search($tag->id,$pname);
					echo '<tr>
						<td class="key">
							<label for="title">'.$tag->name.':</label>
						</td>
						<td>
							<input type="text" name="params['.$tag->id.']" id="params.'.$tag->id.'" value="'.$pvalue[$key_value].'" size="80" />
						</td>
					</tr>';
					}
					else {
						echo '<tr>
							<td class="key">
								<label for="title">'.$tag->name.':</label>
							</td>
							<td>
								<input type="text" name="params['.$tag->id.']" id="params.'.$tag->id.'" value="" size="80" />
							</td>
						</tr>';
					}
				}
				?>
				<tr>
					<td class="key">
						<label for="published"><?php echo JText::_( 'Published' ); ?>:</label>
					</td>
					<td>
						<?php echo $lists['published']; ?>
					</td>
				</tr>
				</table>
				
				<input type="hidden" name="option" value="com_spidervideoplayer" />
				<input type="hidden" name="id" value="<?php echo $row->id?>" />
				<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
				<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	function select_video(&$rows, &$pageNav, &$lists, &$tags){
		JHTML::_('behavior.tooltip'); ?>
		<script type="text/javascript">
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'cancel'){
				submitform( pressbutton );
				return;
			}
			submitform( pressbutton );
		}
		
		Joomla.tableOrdering=function( order, dir, task ) {
			var form = document.adminForm;
			form.filter_order_video.value     = order;
			form.filter_order_Dir_video.value = dir;
			submitform( task );
		}
		function xxx(){
			var VIDS =[];
			var title =[];
			var type =[];
			var url =[];
			var thumb =[];
			for(i=0; i<<?php echo count($rows) ?>; i++)
			if(document.getElementById("v"+i))
			if(document.getElementById("v"+i).checked) {
				VIDS.push(document.getElementById("v"+i).value);
				title.push(document.getElementById("title_"+i).value);
				type.push(document.getElementById("type_"+i).value);
				url.push(document.getElementById("url_"+i).value);
				thumb.push(document.getElementById("thumb_"+i).value);
			}
			window.parent.jSelectVideoS(VIDS, title, type, url, thumb);
		}
		function reset_all(){
			document.getElementById('search_video').value='';
			<?php  if($tags)   foreach($tags as $tag) {?>
				document.getElementById('param_<?php echo $tag->id; ?>').value='';
			<?php } ?>
			this.form.submit();
		}
		</script>
		<form action="index.php?option=com_spidervideoplayer&task=select_video&tmpl=component" method="post" id="adminForm" name="adminForm">
			<table>
				<tr>
					<td align="left"><?php echo JText::_( 'Title' ); ?>:</td>
					
					<td>
						<input type="text" name="search_video" id="search_video" value="<?php echo $lists['search_video'];?>" class="text_area" onchange="document.adminForm.submit();" />
					</td>
					
					<td rowspan="50">
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					</td>
					<td rowspan="50">
						<button onclick="reset_all()"><?php echo JText::_( 'Reset' ); ?></button>
					</td>
					<td align="right" width="100%">
						<button onclick="xxx();" style="width:98px; height:34px; background:url(components/com_spidervideoplayer/images/add_but.png) no-repeat;border:none;cursor:pointer;">&nbsp;</button>
					</td>
				</tr>
				
				<?php
				if($tags)
				foreach($tags as $tag) { ?>
					<tr>
						<td align="left">
							<?php echo JText::_( $tag->name ); ?>:
						</td>
						<td align="left">
							<input type="text" name="param_<?php echo $tag->id;?>" id="param_<?php echo $tag->id; ?>" value="<?php  echo $lists[$tag->id];?>" class="text_area" onchange="document.adminForm.submit();" />
						</td>
					</tr>
				<?php } ?>
			</table>
			
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="30"><?php echo '#'; ?></th>
						<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this,'v')">
						</th>
						<th width="40" class="title"><?php echo JHTML::_('grid.sort',   'ID', 'id', @$lists['order_Dir'], @$lists['order'] ); ?></td>
						<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Type', 'type', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Url', 'url', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'UrlHD', 'urlHD', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', 'Thumb', 'thumb', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th nowrap="nowrap" width="70"><?php echo JHTML::_('grid.sort',   'Published', 'published',@$lists['order_Dir'], @$lists['order'] ); ?></th>
					</tr>
				</thead>
				
				<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<?php
				$k = 0;
				for($i=0, $n=count($rows); $i < $n ; $i++) {
				$row = &$rows[$i];
				$checked 	= JHTML::_('grid.id', $i, $row->id);
				$published 	= published_icon($row, $i, 'video');
				$link 		= JRoute::_( 'index.php?option=com_spidervideoplayer&task=edit_video&cid[]='. $row->id );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center"><?php echo $i+1?></td>
					<td>
						<input type="checkbox" id="v<?php echo $i?>" value="<?php echo $row->id;?>" />
						<input type="hidden" id="title_<?php echo $i?>" value="<?php echo  htmlspecialchars($row->title);?>" />
						<input type="hidden" id="type_<?php echo $i?>" value="<?php echo  $row->type?>" />
						<input type="hidden" id="url_<?php echo $i?>" value="<?php echo  htmlspecialchars($row->url);?>" />
						<input type="hidden" id="thumb_<?php echo $i?>" value="<?php echo  htmlspecialchars($row->thumb);?>" />
					</td>
					<td align="center"><?php echo $row->id?></td>
					<td><a style="cursor: pointer;" onclick="window.parent.jSelectVideoS(['<?php echo $row->id?>'],['<?php echo htmlspecialchars(addslashes($row->title))?>'],['<?php echo $row->type?>'],['<?php echo htmlspecialchars(addslashes($row->url))?>'],['<?php echo htmlspecialchars(addslashes($row->thumb))?>'])"><?php echo $row->title?></a></td>
					<td><?php echo $row->type ?></td>
					<td><?php echo $row->url ?></td>
					<td><?php echo $row->urlHD ?></td>
					<td><?php echo $row->thumb ?></td>
					<td align="center"><?php echo $published?></td>
				</tr>
				<?php
				$k = 1 - $k;
				}
				?>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer">
			<input type="hidden" name="task" value="select_video">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="filter_order_video" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir_video" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
	<?php
	}
	
	function select_video_menu(&$rows, &$pageNav, &$lists, &$tags){
		JHTML::_('behavior.tooltip'); ?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel'){
				submitform( pressbutton );
				return;
			}
			submitform( pressbutton );
		}
		function tableOrdering( order, dir, task ) {
			var form = document.adminForm;
			form.filter_order_video.value     = order;
			form.filter_order_Dir_video.value = dir;
			submitform( task );
		}
		function xxx(){
			var VIDS =[];
			var title =[];
			var url =[];
			for(i=0; i<<?php echo count($rows) ?>; i++)
			if(document.getElementById("v"+i))
			if(document.getElementById("v"+i).checked)
			{
			VIDS.push(document.getElementById("v"+i).value);
			title.push(document.getElementById("title_"+i).value);
			}
			window.parent.jSelectVideoS_menu(VIDS, title);
		}
		function reset_all() {
			document.getElementById('search_video').value='';
			<?php  if($tags)   foreach($tags as $tag) {?>
				document.getElementById('param_<?php echo $tag->id; ?>').value='';
			<?php }?>
			this.form.submit();
		}
		</script>
		<form action="index.php?option=com_spidervideoplayer&task=select_video_menu&tmpl=component" method="post" id="adminForm" name="adminForm">
			<table>
				<tr>
					<td align="left"><?php echo JText::_( 'Title' ); ?>:</td>
					<td>
						<input type="text" name="search_video" id="search_video" value="<?php echo $lists['search_video'];?>" class="text_area" onchange="document.adminForm.submit();" />
					</td>
					<td rowspan="50">
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					</td>
					<td rowspan="50">
						<button onclick="reset_all()"><?php echo JText::_( 'Reset' ); ?></button>
					</td>
				</tr>
				
				<?php
				if($tags)
				foreach($tags as $tag) { ?>
					<tr>
						<td align="left">
							<?php echo JText::_( $tag->name ); ?>:
						</td>
						<td align="left">
							<input type="text" name="param_<?php echo $tag->id;?>" id="param_<?php echo $tag->id; ?>" value="<?php  echo $lists[$tag->id];?>" class="text_area" onchange="document.adminForm.submit();" />
						</td>
					</tr>
				<?php } ?>
			</table>
				
			<table class="table table-striped">
				<thead>
					<tr>
					<th width="30"><?php echo '#'; ?></th>
					<th width="40" class="title"><?php echo JHTML::_('grid.sort',   'ID', 'id', @$lists['order_Dir'], @$lists['order'] ); ?></td>
					<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?></th>
					<th><?php echo JHTML::_('grid.sort', 'Type', 'type', @$lists['order_Dir'], @$lists['order'] ); ?></th>
					<th><?php echo JHTML::_('grid.sort', 'Url', 'url', @$lists['order_Dir'], @$lists['order'] ); ?></th>
					<th><?php echo JHTML::_('grid.sort', 'UrlHD', 'urlHD', @$lists['order_Dir'], @$lists['order'] ); ?></th>
					<th><?php echo JHTML::_('grid.sort', 'Thumb', 'thumb', @$lists['order_Dir'], @$lists['order'] ); ?></th>
					<th nowrap="nowrap" width="70"><?php echo JHTML::_('grid.sort',   'Published', 'published',@$lists['order_Dir'], @$lists['order'] ); ?></th>
					</tr>
				</thead>
				
				<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<?php
				$k = 0;
				for($i=0, $n=count($rows); $i < $n ; $i++) {
				$row = &$rows[$i];
				$checked 	= JHTML::_('grid.id', $i, $row->id);
				$published 	= published_icon($row, $i, 'video');
				$link 		= JRoute::_( 'index.php?option=com_spidervideoplayer&task=edit_video&cid[]='. $row->id );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center"><?php echo $i+1?></td>
					<td>
						<input type="hidden" id="title_<?php echo $i?>" value="<?php echo  htmlspecialchars($row->title);?>" />
						<input type="hidden" id="type_<?php echo $i?>" value="<?php echo  $row->type?>" />
						<input type="hidden" id="url_<?php echo $i?>" value="<?php echo  htmlspecialchars($row->url);?>" />
						<input type="hidden" id="thumb_<?php echo $i?>" value="<?php echo  htmlspecialchars($row->thumb);?>" />
					</td>
					<td align="center"><?php echo $row->id?></td>
					<td><a style="cursor: pointer;" onclick="window.parent.jSelectArticle('<?php echo $row->id?>','<?php echo htmlspecialchars(addslashes($row->title))?>','video')"><?php echo $row->title?></a></td>
					<td><?php echo $row->type ?></td>
					<td><?php echo $row->url ?></td>
					<td><?php echo $row->urlHD ?></td>
					<td><?php echo $row->thumb ?></td>
					<td align="center"><?php echo $published?></td>
				</tr>
				<?php
				$k = 1 - $k;
				}
				?>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer">
			<input type="hidden" name="task" value="select_video_menu">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="filter_order_video" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir_video" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
	<?php 
	} 
	
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////// P L A Y L I S T //////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////
	function add_playlist($lists){
		JRequest::setVar( 'hidemainmenu', 1 );
		JHTML::_('behavior.modal', 'a.modal');
		$editor	=& JFactory::getEditor('tinymce'); ?>
		<script language="javascript" type="text/javascript">
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'cancel_playlist'){
				submitform( pressbutton );
				return;
			}
			if(form.title.value==""){
				alert('Set Playlist title');
				return;
			}
			submitform( pressbutton );
		}
		
		function disableEnterKey(e){
			var key;
			if(window.event)
				key = window.event.keyCode;     //IE
			else
				key = e.which;     				//firefox
			if(key == 13)
				return false;
			else
				return true;
		}
		
		function disableEnterKey(e){
			var key;
			if(window.event)
				key = window.event.keyCode;     //IE
			else
				key = e.which;    				//firefox
			if(key == 13)
				return false;
			else
				return true;
		}
		
		var next=0;
		function jSelectVideoS(VIDS, title, type, url, thumb) {
			video_ids =document.getElementById('videos').value;
			tbody = document.getElementById('video_list');
			for(i=0; i< VIDS.length; i++){
				tr = document.createElement('tr');
				tr.setAttribute('video_id', VIDS[i]);
				tr.setAttribute('id', next);
				tr.style.height='140px';
				
				var td_info = document.createElement('td');
				td_info.setAttribute('id','info_'+next);

				b = document.createElement('b');
				b.innerHTML = title[i];
				
				p_info = document.createElement('p');
				p_info.style.fontStyle="normal";
				p_info.style.color="#666";
				p_info.innerHTML ='Type: '+type[i]+'<br />'+'Url: '+url[i];
				
				img = document.createElement('img');
				img.setAttribute('align','left');
				if(thumb[i]){
					img.style.height='100px';
				}
				else {
					img.style.height='0px';
				}
				img.src = thumb[i];
				img.style.marginRight="10px";
				
				td_info.appendChild(img);
				td_info.appendChild(b);
				td_info.appendChild(p_info);
				//td.appendChild(p_url);
				
				var img_X = document.createElement("img");
				img_X.setAttribute("src", "components/com_spidervideoplayer/images/delete_el.png");
				img_X.setAttribute("onclick", 'remove_row("'+next+'")');
				img_X.style.width='13px';
					var td_X = document.createElement("td");
					td_X.setAttribute("id", "X_"+next);
					td_X.setAttribute("valign", "middle");
					td_X.style.width='20px';
					td_X.appendChild(img_X);
				
				var img_UP = document.createElement("img");
				img_UP.setAttribute("src", "components/com_spidervideoplayer/images/up.png");
				img_UP.setAttribute("onclick", 'up_row("'+next+'")');
				img_UP.style.width='13px';
					var td_UP = document.createElement("td");
					td_UP.setAttribute("id", "up_"+next);
					td_UP.setAttribute("valign", "middle");
					td_UP.style.width='20px';
					td_UP.appendChild(img_UP);
				
				var img_DOWN = document.createElement("img");
				img_DOWN.setAttribute("src", "components/com_spidervideoplayer/images/down.png");
				img_DOWN.setAttribute("onclick", 'down_row("'+next+'")');
				img_DOWN.style.width='13px';
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
			document.getElementById('videos').value=video_ids;
			refresh_();
			SqueezeBox.close();
		}

		function remove_row(id){
			tr=document.getElementById(id);
			tr.parentNode.removeChild(tr);
			refresh_();
		}
		
		function refresh_(){
			video_list=document.getElementById('video_list');
			GLOBAL_tbody=video_list;
			tox='';
			for (x=0; x < GLOBAL_tbody.childNodes.length; x++){
				tr=GLOBAL_tbody.childNodes[x];
				tox=tox+tr.getAttribute('video_id')+',';
			}
			document.getElementById('videos').value=tox;
		}

		function up_row(id){
			form=document.getElementById(id).parentNode;
			k=0;
			while(form.childNodes[k]){
			if(form.childNodes[k].getAttribute("id"))
				if(id==form.childNodes[k].getAttribute("id"))
					break;
				k++;
			}
			if(k!=0){
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
			while(form.childNodes[k]){
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
		<style>
			table.admintable.spiderVideoTab16 { border:1px solid #dedede; }
			table.admintable.spiderVideoTab16 td { padding:0 10px; }
			table.admintable.spiderVideoTab16 td.key label { 
				text-align:center;
				font-size:14px;
				font-weight:700;
			}
			table.admintable.spiderVideoTab16 td input { margin:10px 0; }
			table.admintable.spiderVideoTab16 td a.modal { 
				display:table;
				margin:10px 0;
			}
			table.admintable.spiderVideoTab16 td table img {
				cursor:pointer;
				display:block;
				margin:0 auto;
			}
			table.admintable.spiderVideoTab16 td .controls label {
				margin: 10px 16px 10px 0px;
				float: left;
				padding: 0;    
			}
			table.admintable.spiderVideoTab16 td .controls label input { margin:2px 6px 2px 0px; }
		</style>
		<?php $editor->display('text_for_date','','100%','250','40','6');?>
		<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data" >
			<table class="admintable spiderVideoTab16" border="1">
				<tr>
					<td class="key" style="width:200px">
						<label for="title"><?php echo JText::_( 'Title' ); ?>:</label>
					</td>
					<td>
						<input type="text" name="title" id="title" size="80" onKeyPress="return disableEnterKey(event)" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="videos"><?php echo JText::_( 'Videos' ); ?>:</label>
					</td>
					<td style="width:80%">
						<a class="modal" href="index.php?option=com_spidervideoplayer&amp;task=select_video&amp;tmpl=component&amp;object=id" rel="{handler: 'iframe', size: {x: 850, y: 475}}"><img src="components/com_spidervideoplayer/images/add_but.png" /> </a>
						<table width="100%">
							<tbody id="video_list"></tbody>
						</table>
						<input type="hidden" name="videos" id="videos" size="80" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="Thumb"><?php echo JText::_( 'Thumb' ); ?>:</label>
					</td>
					<td>
						<input type="hidden" value="" name="thumb" id="thumb" />
						<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_image&type=thumb&tmpl=component" rel="{handler: 'iframe', size: {x: 550, y: 550}}"><?php echo JText::_( 'Select Image' ); ?></a>
						<a href="javascript:removeImage();"><?php echo JText::_( 'Remove Image' ); ?></a>
						<div style="height:150px;">
							<img style="display:none;height:150px" height="150" id="imagebox" src="" />
						</div>
						<script type="text/javascript">
						function removeImage(){
							document.getElementById("imagebox").style.display="none";
							document.getElementById("thumb").value='';
						}
						</script>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="published"><?php echo JText::_( 'Published' ); ?>:</label>
					</td>
					<td>
						<?php echo $lists['published']; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	function show_playlist(&$rows, &$pageNav, &$lists){
		JHTML::_('behavior.tooltip'); ?>
		<script type="text/javascript">
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'cancel') 	{
				submitform( pressbutton );
				return;
			}
			submitform( pressbutton );
		}
		Joomla.tableOrdering=function( order, dir, task ) {
			var form = document.adminForm;
			form.filter_order_playlist.value     = order;
			form.filter_order_Dir_playlist.value = dir;
			submitform( task );
		}
		</script>
		<form action="index.php?option=com_spidervideoplayer" method="post" name="adminForm" id="adminForm">
			<table width="100%">
				<tr>
					<td align="left" width="100%">
						<input type="text" name="search_playlist" id="search_playlist" value="<?php echo $lists['search_playlist'];?>" class="text_area" placeholder="Search" style="margin:0px" />
						<button class="btn tip hasTooltip" type="submit" data-original-title="Search"><i class="icon-search"></i></button>
						<button class="btn tip hasTooltip" type="button" onclick="document.getElementById('search_playlist').value='';this.form.submit();" data-original-title="Clear"><i class="icon-remove"></i></button>
						<div class="btn-group pull-right hidden-phone">
							<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
							<?php echo $pageNav->getLimitBox(); ?>
						</div>
					</td>
				</tr>
			</table>
			
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="30"><?php echo '#'; ?></th>
						<th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)"></th>
						<th width="40" class="title"><?php echo JHTML::_('grid.sort',   'ID', 'id', @$lists['order_Dir'], @$lists['order'] ); ?></td>
						<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th width="55"><?php echo JHTML::_('grid.sort', 'Number of Videos', 'number_of_vids', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th nowrap="nowrap" width="70"><?php echo JHTML::_('grid.sort',   'Published', 'published',@$lists['order_Dir'], @$lists['order'] ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<?php
				$k = 0;
				for($i=0, $n=count($rows); $i < $n ; $i++) {
					$row = &$rows[$i];
					$checked 	= JHTML::_('grid.id', $i, $row->id);
					$published 	= published($row, $i, 'playlist');
					$link 		= JRoute::_( 'index.php?option=com_spidervideoplayer&task=edit_playlist&cid[]='. $row->id );
					$number_of_vids=substr_count($row->videos, ',');
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $i+1?></td>
						<td><?php echo $checked?></td>
						<td align="center"><?php echo $row->id?></td>
						<td><a href="<?php echo $link;?>"><?php echo $row->title?></a></td>
						<td align="center"><?php echo $number_of_vids?></td>
						<td align="center"><?php echo $published?></td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer">
			<input type="hidden" name="task" value="playlist">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="filter_order_playlist" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir_playlist" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
	<?php
	}
	
	function edit_playlist(&$lists, &$row, &$videos){
		JRequest::setVar( 'hidemainmenu', 1 );
		JHTML::_('behavior.modal', 'a.modal');
		$editor	=& JFactory::getEditor('tinymce');
		$path = JURI::root(true)."/administrator/components/com_spidervideoplayer/images/"; ?>		
		<script language="javascript" type="text/javascript">
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'cancel_playlist') {
				submitform( pressbutton );
				return;
			}
			if(form.title.value=="") {
				alert('Set Playlist title');
				return;
			}
			submitform( pressbutton );
		}
		
		function disableEnterKey(e) {
			var key;
			if(window.event)
				key = window.event.keyCode; //IE
			else
				key = e.which;     			//firefox
			if(key == 13)
				return false;
			else
				return true;
		}
			
		var next=0;
		function jSelectVideo(VIDS, title, type, url, thumb) {
			video_ids =document.getElementById('videos').value;
			tbody = document.getElementById('video_list');
			for(i=0; i< VIDS.length; i++){
				
				tr = document.createElement('tr');
				tr.setAttribute('video_id', VIDS[i]);
				tr.setAttribute('id', next);
				tr.style.height='140px';
				
				var td_info = document.createElement('td');
				td_info.setAttribute('id','info_'+next);
				
				b = document.createElement('b');
				b.innerHTML = title[i];
				
				p_info = document.createElement('p');
				p_info.style.fontStyle="normal";
				p_info.style.color="#666";
				p_info.innerHTML ='Type: '+type[i]+'<br />'+'Url: '+url[i];
				
				img = document.createElement('img');
				img.setAttribute('align','left');
				if(thumb[i]){
					img.style.height='100px';
				}
				else {
					img.style.height='0px';
				}
				img.src = thumb[i];
				img.style.marginRight="10px";
				td_info.appendChild(img);
				td_info.appendChild(b);
				td_info.appendChild(p_info);
				//td.appendChild(p_url);
				
				var img_X = document.createElement("img");
				img_X.setAttribute("src", "components/com_spidervideoplayer/images/delete_el.png");
				img_X.setAttribute("onclick", 'remove_row("'+next+'")');
				img_X.style.width='13px';
					var td_X = document.createElement("td");
					td_X.setAttribute("id", "X_"+next);
					td_X.setAttribute("valign", "middle");
					td_X.style.width='20px';
					td_X.appendChild(img_X);
				
				var img_UP = document.createElement("img");
				img_UP.setAttribute("src", "components/com_spidervideoplayer/images/up.png");
				img_UP.setAttribute("onclick", 'up_row("'+next+'")');
				img_UP.style.width='13px';
					var td_UP = document.createElement("td");
					td_UP.setAttribute("id", "up_"+next);
					td_UP.setAttribute("valign", "middle");
					td_UP.style.width='20px';
					td_UP.appendChild(img_UP);
				
				var img_DOWN = document.createElement("img");
				img_DOWN.setAttribute("src", "components/com_spidervideoplayer/images/down.png");
				img_DOWN.setAttribute("onclick", 'down_row("'+next+'")');
				img_DOWN.style.width='13px';
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
			refresh_();
		}
			
		function jSelectVideoS(VIDS, title, type, url, thumb) {
			video_ids =document.getElementById('videos').value;
			tbody = document.getElementById('video_list');
			for(i=0; i< VIDS.length; i++){
				
				tr = document.createElement('tr');
				tr.setAttribute('video_id', VIDS[i]);
				tr.setAttribute('id', next);
				
				var td_info = document.createElement('td');
				td_info.setAttribute('id','info_'+next);
				
				b = document.createElement('b');
				b.innerHTML = title[i];
				
				p_info = document.createElement('p');
				p_info.style.fontStyle="normal";
				p_info.style.color="#666";
				p_info.innerHTML ='Type: '+type[i]+'<br />'+'Url: '+url[i];
				
				img = document.createElement('img');
				img.setAttribute('align','left');
				img.style.height='100px';
				img.src = thumb[i];
				img.style.marginRight="10px";
				
				td_info.appendChild(img);
				td_info.appendChild(b);
				td_info.appendChild(p_info);
				//td.appendChild(p_url);
				
				var img_X = document.createElement("img");
				img_X.setAttribute("src", "components/com_spidervideoplayer/images/delete_el.png");
				img_X.setAttribute("onclick", 'remove_row("'+next+'")');
				img_X.style.width='13px';
					var td_X = document.createElement("td");
					td_X.setAttribute("id", "X_"+next);
					td_X.setAttribute("valign", "middle");
					td_X.style.width='20px';
					td_X.appendChild(img_X);
				
				
				var img_UP = document.createElement("img");
				img_UP.setAttribute("src", "components/com_spidervideoplayer/images/up.png");
				img_UP.setAttribute("onclick", 'up_row("'+next+'")');
				img_UP.style.width='13px';
					var td_UP = document.createElement("td");
					td_UP.setAttribute("id", "up_"+next);
					td_UP.setAttribute("valign", "middle");
					td_UP.style.width='20px';
					td_UP.appendChild(img_UP);
				
				
				var img_DOWN = document.createElement("img");
				img_DOWN.setAttribute("src", "components/com_spidervideoplayer/images/down.png");
				img_DOWN.style.cssText = "margin:2px; cursor:pointer";
				img_DOWN.setAttribute("onclick", 'down_row("'+next+'")');
				img_DOWN.style.width='13px';
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
			refresh_();
			SqueezeBox.close();
		}
		
		function remove_row(id){
			tr=document.getElementById(id);
			tr.parentNode.removeChild(tr);
			refresh_();
		}
		function refresh_(){
			video_list=document.getElementById('video_list');
			GLOBAL_tbody=video_list;
			tox='';
			for (x=0; x < GLOBAL_tbody.childNodes.length; x++){
				tr=GLOBAL_tbody.childNodes[x];
				tox=tox+tr.getAttribute('video_id')+',';
			}
			document.getElementById('videos').value=tox;
		}
		
		function up_row(id){
			form=document.getElementById(id).parentNode;
			k=0;
			while(form.childNodes[k]){
				if(form.childNodes[k].getAttribute("id"))
					if(id==form.childNodes[k].getAttribute("id"))
						break;
				k++;
			}
			if(k!=0){
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
			while(form.childNodes[k]){
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
		<style>
			table.admintable.spiderVideoTab1 { border:1px solid #dedede; }
			table.admintable.spiderVideoTab1 td { padding:0 10px; }
			table.admintable.spiderVideoTab1 td.key label { 
				text-align:center;
				font-size:14px;
				font-weight:700;
			}
			table.admintable.spiderVideoTab1 td input { margin:10px 0; }
			table.admintable.spiderVideoTab1 td #video_list td { padding:0; }
			table.admintable.spiderVideoTab1 td a.modal { 
				display:table;
				margin:10px 0;
			}
			table.admintable.spiderVideoTab1 td table img {
				cursor:pointer;
				display:block;
				margin:0 auto;
			}
			table.admintable.spiderVideoTab1 td .controls label {
				margin: 10px 16px 10px 0px;
				float: left;
				padding: 0;
			}
			table.admintable.spiderVideoTab1 td .controls label input { margin:2px 6px 2px 0px; }
		</style>		
		<?php $editor->display('text_for_date','','100%','250','40','6');?>
		<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
			<table class="admintable spiderVideoTab1" border="1">
				<tr>
					<td class="key" style="width:200px">
						<label for="title"><?php echo JText::_( 'Title' ); ?>:</label>
					</td>
					<td>
						<input type="text" name="title" id="title" size="80" value="<?php echo htmlspecialchars($row->title); ?>" onKeyPress="return disableEnterKey(event)" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="videos"><?php echo JText::_( 'Videos' ); ?>:</label>
					</td>
					<td style="width:80%">
						<a class="modal" href="index.php?option=com_spidervideoplayer&amp;task=select_video&amp;tmpl=component&amp;object=id" rel="{handler: 'iframe', size: {x: 850, y: 475}}"><img src="components/com_spidervideoplayer/images/add_but.png" /></a>
						<table width="100%">
							<tbody id="video_list"></tbody>
						</table>
						<input type="hidden" name="videos" id="videos" size="80" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="Thumb"><?php echo JText::_( 'Thumb' ); ?>:</label>
					</td>
					<td>
						<input type="hidden" value="<?php echo htmlspecialchars($row->thumb); ?>" name="thumb" id="thumb" />
						
						<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_image&type=thumb&tmpl=component" rel="{handler: 'iframe', size: {x: 570, y: 400}}"><?php echo JText::_( 'Select Image' ); ?></a>
						<a href="javascript:removeImage();"><?php echo JText::_( 'Remove Image' ); ?></a>
						
						<div style=" position:absolute; width:1px; height:1px; top:0px; overflow:hidden">
							<textarea id="tempimage" name="tempimage" class="mce_editable"></textarea><br />
						</div>
						<script type="text/javascript">
						function removeImage() {
							document.getElementById("imagebox").style.display="none";
							document.getElementById("thumb").value='';
						}
						</script>
						<div style="height:150px;">
							<img style="display:<?php if($row->thumb=='') echo 'none'; else echo 'block' ?>;height:150px" height="150" id="imagebox" src="<?php echo htmlspecialchars($row->thumb) ; ?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="published"><?php echo JText::_( 'Published' ); ?>:</label>
					</td>
					<td> <?php echo $lists['published']; ?> </td>
				</tr>
			</table>
			<?php
			if($videos) {
				foreach($videos as $video) {
					$v_ids[]=$video->id;
					$v_titles[]=addslashes($video->title);
					$v_types[]=$video->type;
					$v_urls[]=addslashes($video->url);
					$v_thumbs[]=addslashes($video->thumb);
				}
				$v_id='["'.implode('","',$v_ids).'"]';
				$v_title='["'.implode('","',$v_titles).'"]';
				$v_type='["'.implode('","',$v_types).'"]';
				$v_url='["'.implode('","',$v_urls).'"]';
				$v_thumb='["'.implode('","',$v_thumbs).'"]';
				?>
				<script type="text/javascript">
				jSelectVideo(<?php echo $v_id?>,<?php echo $v_title?>,<?php echo $v_type?>,<?php echo $v_url?>,<?php echo $v_thumb?>);
			<?php } ?>
				</script>
			<input type="hidden" name="option" value="com_spidervideoplayer" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php
	}

	function select_playlist(&$rows, &$pageNav, &$lists){
		JHTML::_('behavior.tooltip'); ?>
		<script type="text/javascript">
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
					return;
			}
			submitform( pressbutton );
		}
		
		Joomla.tableOrdering=function( order, dir, task ) {
			var form = document.adminForm;
			form.filter_order_playlist.value     = order;
			form.filter_order_Dir_playlist.value = dir;
			submitform( task );
		}
		function xxx(){
			var VIDS =[];
			var title =[];
			var thumb =[];
			var number_of_vids =[];
			for(i=0; i<<?php echo count($rows) ?>; i++)
			if(document.getElementById("v"+i))
			if(document.getElementById("v"+i).checked){
				VIDS.push(document.getElementById("v"+i).value);
				title.push(document.getElementById("title_"+i).value);
				thumb.push(document.getElementById("thumb_"+i).value);
				number_of_vids.push(document.getElementById("number_of_vids_"+i).value);
			}
			window.parent.jSelectVideoS(VIDS, title, thumb, number_of_vids);
		}
		</script>
		<form action="index.php?option=com_spidervideoplayer&task=select_playlist&tmpl=component" method="post" id="adminForm" name="adminForm">
			<table>
				<tr>
					<td align="left">
						<?php echo JText::_( 'Title' ); ?>:
					</td>
					<td>
						<input type="text" name="search_playlist" id="search_playlist" value="<?php echo $lists['search_playlist'];?>" class="text_area" onchange="document.adminForm.submit();" />
					</td>
					<td>
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					</td>
					<td>
						<button onclick="document.getElementById('search_playlist').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
					</td>
					<td align="right" width="100%">
						<button onclick="xxx();" style="width:98px; height:34px; background:url(components/com_spidervideoplayer/images/add_but.png) no-repeat;border:none;cursor:pointer;">&nbsp;</button>
					</td>
				</tr>
			</table>
			
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="30"><?php echo '#'; ?></th>
						<th width="20">
							<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this,'v')">
						</th>
						<th width="40" class="title"><?php echo JHTML::_('grid.sort',   'ID', 'id', @$lists['order_Dir'], @$lists['order'] ); ?></td>
						<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th width="55"><?php echo JHTML::_('grid.sort', 'Number of Videos', 'number_of_vids', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th nowrap="nowrap" width="70"><?php echo JHTML::_('grid.sort',   'Published', 'published',@$lists['order_Dir'], @$lists['order'] ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<?php
				$k = 0;
				for($i=0, $n=count($rows); $i < $n ; $i++) {
					$row = &$rows[$i];
					$checked 	= JHTML::_('grid.id', $i, $row->id);
					$published 	= published_icon($row, $i, 'playlist');
					$number_of_vids=substr_count($row->videos, ',');
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $i+1?></td>
						<td>
							<input type="checkbox" id="v<?php echo $i?>" value="<?php echo $row->id;?>" />
							<input type="hidden" id="title_<?php echo $i?>" value="<?php echo  htmlspecialchars($row->title);?>" />
							<input type="hidden" id="thumb_<?php echo $i?>" value="<?php echo  htmlspecialchars($row->thumb);?>" />
							<input type="hidden" id="number_of_vids_<?php echo $i?>" value="<?php echo  $number_of_vids;?>" />
						</td>
						<td align="center"><?php echo $row->id?></td>
						<td>
							<a style="cursor: pointer;" onclick="window.parent.jSelectVideoS(['<?php echo $row->id?>'],['<?php echo htmlspecialchars(addslashes($row->title));?>'],['<?php echo htmlspecialchars(addslashes($row->thumb));?>'],['<?php echo $number_of_vids;?>'])"><?php echo $row->title?></a>
						</td>
						<td align="center"><?php echo $number_of_vids?></td>
						<td align="center"><?php echo $published?></td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer">
			<input type="hidden" name="task" value="select_playlist">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="filter_order_playlist" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir_playlist" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
	<?php
	}
	
	
	///////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////// T H E M E //////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////
	function add_theme(&$row){
		JRequest::setVar( 'hidemainmenu', 1 );
		$editor	=& JFactory::getEditor();
		$document =& JFactory::getDocument();
		$document->addScript(JURI::root() . 'administrator/components/com_spidervideoplayer/jscolor/jscolor.js');
		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_spidervideoplayer/elements';
		$document->addScript($cmpnt_js_path.'/jquery-1.7.1.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.core.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.widget.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.mouse.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.slider.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.sortable.js');
		$document->addStyleSheet($cmpnt_js_path.'/jquery-ui.css');
		$document->addStyleSheet($cmpnt_js_path.'/parseTheme.css');
		if($row->ctrlsStack)
			$value=$row->ctrlsStack;
		else
			$value='playPrev:1,playPause:1,stop:1,vol:1,time:1,playNext:1,+:1,repeat:1,shuffle:1,hd:1,playlist:1,lib:1,fullScreen:1,play:1,pause:1,share:1';
		
		$ctrls = explode(",", $value);
		$n = count($ctrls);
		$path=JURI::root(true)."/administrator/components/com_spidervideoplayer/images/";
		?>
		<script language="javascript" type="text/javascript">
		SqueezeBox.presets.onClose=function (){document.getElementById('sbox-content').innerHTML=""};
		SqueezeBox.presets.onOpen=function (){refresh_ctrl();};
		function get_radio_value(name) {
			for (var i=0; i < document.getElementsByName(name).length; i++){
				if (document.getElementsByName(name)[i].checked) {
					var rad_val = document.getElementsByName(name)[i].value;
					return rad_val;
				}
			}
		}
		function refresh_() {
			appWidth = parseInt(document.getElementById('appWidth').value);
			appHeight = parseInt(document.getElementById('appHeight').value);
			refresh_ctrl();
			document.getElementById('toolbar-popup-popup').childNodes[1].href='index.php?option=com_spidervideoplayer&task=preview&tmpl=component&appWidth='+appWidth+'&appHeight='+appHeight;
			document.getElementById('toolbar-popup-popup').childNodes[1].setAttribute('rel',"{handler: 'iframe', size: {x:"+(appWidth+30)+", y: "+(appHeight+30)+"}}");
		}
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'cancel_theme') {
				submitform( pressbutton );
				return;
			}
			if(form.title.value=="") {
				alert('Set Theme title');
				return;
			}
			refresh_ctrl();
			submitform( pressbutton );
		}
		function IsNumeric(input){
			var RE = /^-{0,1}\d*\.{0,1}\d+$/;
			return (RE.test(input));
		}
		function refresh_ctrl(){
			ctrlStack="";
			w=document.getElementById('tr_arr').childNodes;
			for(i in w)
			if(IsNumeric(i))
			if(w[i].nodeType!=3) {
				if(ctrlStack=="")
					ctrlStack=w[i].getAttribute('value')+':'+w[i].getAttribute('active'); ///???
				else
					ctrlStack=ctrlStack+","+w[i].getAttribute('value')+':'+w[i].getAttribute('active'); ///???
			}
			document.getElementById('ctrlsStack').value=ctrlStack;
		}
		function check_isnum(e){
			var chCode1 = e.which || e.keyCode;
			if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57))
				return false;
			return true;
		}
		<?php
		$sliders=array("defaultVol", "centerBtnAlpha", "watermarkAlpha", "framesBgAlpha", "ctrlsMainAlpha" );
		foreach( $sliders as $slider) { ?>
			setTimeout(function() {
				$("#slider-<?php echo $slider?>")[0].slide = null;
				$( "#slider-<?php echo $slider?>" ).slider({
					range: "min",
					value: <?php echo $row->$slider?>,
					min: 1,
					max: 100,
					slide: function( event, ui ) {
						$( "#<?php echo $slider?>" ).val( "" + ui.value );
					}
				});
				$( "#<?php echo $slider?>" ).val( "" + $( "#slider-<?php echo $slider?>" ).slider( "value" ) );
			},0);
		<?php } ?>
		jQuery.noConflict();
		$(document).ready(function($) {
			$(document).ready(function(){
				for (var i = 0; i < <?php echo $n ?>; i++) {
					$("#arr_" + i).bind('click',{i:i},function(event){
						i=event.data.i;
						image=document.getElementById("arr_" + i).getAttribute('image');
						if(document.getElementById("td_arr_" + i).getAttribute('active') == 0) {
							document.getElementById("arr_" + i).src="<?php echo $path ;?>"+image+'_1.png';
							document.getElementById("td_arr_" + i).setAttribute('active','1');
						}
						else {
							document.getElementById("arr_" + i).src="<?php echo $path ;?>"+image+'_0.png';
							document.getElementById("td_arr_" + i).setAttribute('active','0');
						}
					});
				}
			});
		});
		setTimeout(function() {
			$( "#tr_arr" ).sortable();
			$( "#tr_arr" ).disableSelection();
		},0);
		//-->
		</script>
		<style>
			table.admintable.spiderVideoTab2 { border:1px solid #dedede; }
			table.admintable.spiderVideoTab2 td { padding:0 10px; }
			table.admintable.spiderVideoTab2 td.key label,
			table.admintable.spiderVideoTab2 td.paramlist_key label {
				font-size:13px;
				font-weight:600;
			}
			table.admintable.spiderVideoTab2 td input { margin:10px 0; }
			table.admintable.spiderVideoTab2 td.paramlist_value label {
				margin: 10px 16px 10px 0px;
				float: left;
				padding: 0;
			}
			table.admintable.spiderVideoTab2 td.paramlist_value.range { padding:10px; }
			table.admintable.spiderVideoTab2 td.paramlist_value.range p { color:#f6931f; float:left; border:0; margin: 0; }
			table.admintable.spiderVideoTab2 td.paramlist_value.range p input { border:0; margin:0; padding:0; font-weight:bold; color:#f6931f; width:20px; }
			table.admintable.spiderVideoTab2 td.paramlist_value.range>div { width:140px; float:left; margin:4px; }
			table.admintable.spiderVideoTab2 td.paramlist_value label input {  margin:2px 6px 2px 0px; }
			table.admintable.spiderVideoTab2 td a.modal { 
				display:table;
				margin:10px 0;
			}
		</style>	
		<?php  $editor->display('text_for_date','','100%','250','40','6');?>
		<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general_parameters" data-toggle="tab">General Parameters</a></li>
				<li><a href="#style_parameters" data-toggle="tab">Style Parameters</a></li>
				<li><a href="#playback_parameters" data-toggle="tab">Playback Parameters</a></li>
				<li><a href="#playlist_parameters" data-toggle="tab">Playlist and Library Parameters</a></li>
				<li><a href="#video_parameters" data-toggle="tab">Video Control Parameters</a></li>
			</ul>
			
			<div class="tab-content">
				<div class="tab-pane active" id="general_parameters">
					<fieldset class="adminform">
					<legend>General Parameters</legend>
						<table class="admintable spiderVideoTab2" border="1">
							<tr>
								<td class="key"><label for="title">Title of theme:</label></td>
								<td><input type="text" name="title" id="title" /></td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label id="appWidth-lbl" for="appWidth">Width of player:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="appWidth" id="appWidth" value="<?php echo $row->appWidth; ?>" class="text_area" onchange="refresh_()" onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label id="appHeight-lbl" for="appHeight">Height of player:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="appHeight" id="appHeight" value="<?php echo $row->appHeight; ?>" class="text_area" onchange="refresh_()" onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Start with:</label></span>
								</td>
								<td class="paramlist_value">
									<label for="startWithLib1" class="radiobtn"><input type="radio" name="startWithLib" id="startWithLib1" value="0" <?php if(!($row->startWithLib)) echo 'checked="checked"'; ?> >Video</label>										
									<label for="startWithLib2" class="radiobtn"><input type="radio" name="startWithLib" id="startWithLib2" value="1"<?php if($row->startWithLib) echo 'checked="checked"'; ?> >Library</label>										
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Show Track Id:</label></span>
								</td>
								<td class="paramlist_value">
									<label for="show_trackid1" class="radiobtn"><input type="radio" name="show_trackid" id="show_trackid1" value="0" <?php if(!($row->show_trackid)) echo 'checked="checked"'; ?> >No</label>
									<label for="show_trackid2" class="radiobtn"><input type="radio" name="show_trackid" id="show_trackid2" value="1"<?php if($row->show_trackid) echo 'checked="checked"'; ?> >Yes</label>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Auto hide time:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="autohideTime" id="autohideTime" value="<?php echo $row->autohideTime; ?>" class="text_area" onkeypress="return check_isnum(event)"> sec
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Keep aspect ratio (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'keepAspectRatio' , 'class="inputbox"', $row->keepAspectRatio); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control bar over video:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'ctrlsOverVid' , 'class="inputbox"', $row->ctrlsOverVid); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark image (only for flash version):</label></span>
								</td>
								<td>
									<input type="hidden" value="<?php echo htmlspecialchars($row->watermarkUrl); ?>" name="watermarkUrl" id="watermarkUrl" />
									<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_image&type=watermarkUrl&tmpl=component" rel="{handler: 'iframe', size: {x: 570, y: 400}}">Select Image</a>
									<a href="javascript:removeImage();">Remove Image</a>
									<div style=" position:absolute; width:1px; height:1px; top:0px; overflow:hidden">
										<textarea id="tempimage" name="tempimage" class="mce_editable"></textarea><br />
									</div>
									<script type="text/javascript">
									function removeImage() {
										document.getElementById("imagebox").style.display="none";
										document.getElementById("watermarkUrl").value='';
									}
									</script>
									<div style="height:150px;">
										<img style="display:<?php if($row->watermarkUrl=='') echo 'none'; else echo 'block' ?>;height:150px" height="150" id="imagebox" src="<?php echo htmlspecialchars($row->watermarkUrl) ; ?>" />
									</div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark Position (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<label for="watermarkPos1" class="radiobtn"><input type="radio" name="watermarkPos" id="watermarkPos1" value="1" <?php if($row->watermarkPos=="1") echo 'checked="checked"'; ?>>Top left</label>
									<label for="watermarkPos2" class="radiobtn"><input type="radio" name="watermarkPos" id="watermarkPos2" value="2" <?php if($row->watermarkPos=="2") echo 'checked="checked"'; ?>>Top right</label>
									<label for="watermarkPos3" class="radiobtn"><input type="radio" name="watermarkPos" id="watermarkPos3" value="3" <?php if($row->watermarkPos=="3") echo 'checked="checked"'; ?>>Bottom left</label>
									<label for="watermarkPos4" class="radiobtn"><input type="radio" name="watermarkPos" id="watermarkPos4" value="4" <?php if($row->watermarkPos=="4") echo 'checked="checked"'; ?>>Bottom right</label>									
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark size (only for flash version)</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="watermarkSize" id="watermarkSize" value="<?php echo $row->watermarkSize; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark Margin (only for flash version)</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="watermarkSpacing" id="watermarkSpacing" value="<?php echo $row->watermarkSpacing; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark opacity (only for flash version):</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" value="0" name="watermarkAlpha" id="watermarkAlpha" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-watermarkAlpha" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
				
				<div class="tab-pane" id="style_parameters">
					<fieldset class="adminform">
					<legend>Style Parameters</legend>
						<table class="admintable spiderVideoTab2" border="1">
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Center button opacity (only for flash version):</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" value="0" name="centerBtnAlpha" id="centerBtnAlpha" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-centerBtnAlpha" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Background color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="appBgColor" id="appBgColor" value="<?php echo $row->appBgColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Video background color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="vidBgColor" id="vidBgColor" value="<?php echo $row->vidBgColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Frames background color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="framesBgColor" id="framesBgColor" value="<?php echo $row->framesBgColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Frames background opacity:</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" value="0" name="framesBgAlpha" id="framesBgAlpha" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-framesBgAlpha" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control buttons main color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="ctrlsMainColor" id="ctrlsMainColor" value="<?php echo $row->ctrlsMainColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control buttons hover color (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="ctrlsMainHoverColor" id="ctrlsMainHoverColor" value="<?php echo $row->ctrlsMainHoverColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control buttons opacity:</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" value="0" name="ctrlsMainAlpha" id="ctrlsMainAlpha" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-ctrlsMainAlpha" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Sliders color</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="slideColor" id="slideColor" value="<?php echo $row->slideColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Hovered item background Color</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="itemBgHoverColor" id="itemBgHoverColor" value="<?php echo $row->itemBgHoverColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Selected item background Color</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="itemBgSelectedColor" id="itemBgSelectedColor" value="<?php echo $row->itemBgSelectedColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Text color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="textColor" id="textColor" value="<?php echo $row->textColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Hovered text color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="textHoverColor" id="textHoverColor" value="<?php echo $row->textHoverColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Selected text color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="textSelectedColor" id="textSelectedColor" value="<?php echo $row->textSelectedColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Loading animation type (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<label for="loadinAnimType1" class="radiobtn"><input type="radio" name="loadinAnimType" id="loadinAnimType1" value="1" <?php if($row->loadinAnimType=="1") echo 'checked="checked"'; ?> >Circles</label>										
									<label for="loadinAnimType2" class="radiobtn"><input type="radio" name="loadinAnimType" id="loadinAnimType2" value="2" <?php if($row->loadinAnimType=="2") echo 'checked="checked"'; ?> >Lines</label>										
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
				
				<div class="tab-pane" id="playback_parameters">
					<fieldset class="adminform">
						<legend>Playback Parameters</legend>
						<table class="admintable spiderVideoTab2" border="1">
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Auto play:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'autoPlay' , 'class="inputbox"', $row->autoPlay); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Auto next song:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'autoNext' , 'class="inputbox"',  $row->autoNext); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Auto next album:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'autoNextAlbum' , 'class="inputbox"',  $row->autoNextAlbum); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Default Volume:</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" value="0" name="defaultVol" id="defaultVol" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-defaultVol" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Repeat</label></span>
								</td>
								<td class="paramlist_value">
									<label for="defaultRepeat1" class="radiobtn"><input type="radio" name="defaultRepeat" id="defaultRepeat1" value="repeatOff" <?php if($row->defaultRepeat=="repeatOff") echo 'checked="checked"'; ?> >Off</label>									
									<label for="defaultRepeat2" class="radiobtn"><input type="radio" name="defaultRepeat" id="defaultRepeat2" value="repeatOne" <?php if($row->defaultRepeat=="repeatOne") echo 'checked="checked"'; ?>>One</label>									
									<label for="defaultRepeat3" class="radiobtn"><input type="radio" name="defaultRepeat" id="defaultRepeat3" value="repeatAll" <?php if($row->defaultRepeat=="repeatAll") echo 'checked="checked"'; ?>>All</label>									
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Shuffle:</label></span>
								</td>
								<td class="paramlist_value">
									<label for="defaultShuffle1" class="radiobtn"><input type="radio" name="defaultShuffle" id="defaultShuffle1" value="shuffleOff" <?php if($row->defaultShuffle=="shuffleOff") echo 'checked="checked"'; ?> >No</label>										
									<label for="defaultShuffle2" class="radiobtn"><input type="radio" name="defaultShuffle" id="defaultShuffle2" value="shuffleOn"  <?php if($row->defaultShuffle=="shuffleOn") echo 'checked="checked"'; ?> >Yes</label>										
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control bar auto hide:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'ctrlsSlideOut' , 'class="inputbox"', $row->ctrlsSlideOut); ?>
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
				
				<div class="tab-pane" id="playlist_parameters">
					<fieldset class="adminform">
					<legend>Playlist and Library Parameters</legend>
						<table class="admintable spiderVideoTab2" border="1">
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Playlist Position:</label></span>
								</td>
								<td class="paramlist_value">
									<label for="playlistPos1" class="radiobtn"><input type="radio" name="playlistPos" id="playlistPos1" value="1" <?php if($row->playlistPos=="1") echo 'checked="checked"'; ?>>Left</label>									
									<label for="playlistPos2" class="radiobtn"><input type="radio" name="playlistPos" id="playlistPos2" value="2" <?php if($row->playlistPos=="2") echo 'checked="checked"'; ?>>Right</label>									
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label id="playlistWidth-lbl" for="playlistWidth">Width of playlist:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="playlistWidth" id="playlistWidth" value="<?php echo $row->playlistWidth; ?>" class="text_area"  onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Playlist over video:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'playlistOverVid' , 'class="inputbox"', $row->playlistOverVid); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Open playlist at start:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'openPlaylistAtStart' , 'class="inputbox"', $row->openPlaylistAtStart); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Playlist auto hide:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'playlistAutoHide' , 'class="inputbox"',  $row->playlistAutoHide); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Playlist text size: </label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="playlistTextSize" id="playlistTextSize" value="<?php echo $row->playlistTextSize; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Library colums:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="libCols" id="libCols" value="<?php echo $row->libCols; ?>" class="text_area" onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Library rows: </label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="libRows" id="libRows" value="<?php echo $row->libRows; ?>" class="text_area" onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Library list text size:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="libListTextSize" id="libListTextSize" value="<?php echo $row->libListTextSize; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Library details text size:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="libDetailsTextSize" id="libDetailsTextSize" value="<?php echo $row->libDetailsTextSize; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
				
				<div class="tab-pane" id="video_parameters">
					<fieldset class="adminform">
						<legend>Video Control Parameters</legend>
						<table class="admintable spiderVideoTab2" border="1">
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Play/pause on click:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'clickOnVid' , 'class="inputbox"', $row->clickOnVid); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Play/pause by space key:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'spaceOnVid' , 'class="inputbox"', $row->spaceOnVid); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Volume control by mouse scroll (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'mouseWheel' , 'class="inputbox"', $row->mouseWheel); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control bar position:</label></span>
								</td>
								<td class="paramlist_value">
									<label for="ctrlsPos1" class="radiobtn"><input type="radio" name="ctrlsPos" id="ctrlsPos1" value="1" <?php if($row->ctrlsPos=="1") echo 'checked="checked"'; ?> >Up</label>
									<label for="ctrlsPos2" class="radiobtn"><input type="radio" name="ctrlsPos" id="ctrlsPos2" value="2" <?php if($row->ctrlsPos=="2") echo 'checked="checked"'; ?> >Down</label>	
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Buttons order on control bar:<br>(Drag and Drop)</label></span>
								</td>
								<td class="paramlist_value">
									<table border="0" bgcolor="#666666" style="background:#666666" cellpadding="0" cellspacing="0" width="70%">
										<?php
										echo '<tr valign="top" id="tr_arr" valign="middle">';
										foreach($ctrls as $key =>  $x) {
											$y = explode(":", $x);
											$ctrl	=$y[0];
											$active	=$y[1];
											if($ctrl.'_'.$active=='+_1' or $ctrl.'_'.$active=='+_0' ){
												echo '<td style="width:150px" id="td_arr_'.$key.'"  active="'.$active.'" value="'.$ctrl.'" width="40" align="center" style="padding:4px">
												<img src="'.$path.$ctrl.'_'.$active.'.png" id="arr_'.$key.'" image="'.$ctrl.'" style="cursor:pointer"/>
												</td>';
											}
											else {
												echo '<td id="td_arr_'.$key.'"  active="'.$active.'" value="'.$ctrl.'" width="40" align="center" style="padding:4px">
												<img src="'.$path.$ctrl.'_'.$active.'.png" id="arr_'.$key.'" image="'.$ctrl.'" style="cursor:pointer"/>
												</td>';
											}
										}
										echo "</tr>"
										?>
									</table>
									<input type="hidden" name="ctrlsStack" id="ctrlsStack" value="<?php echo $value; ?>">
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
			<input type="hidden" name="option" value="com_spidervideoplayer" />
			<input type="hidden" name="task" value="" />
		</form>
		<?php
		$bar=& JToolBar::getInstance( 'toolbar' );
		$bar->appendButton( 'popup', 'preview', 'Preview', 'index.php?option=com_spidervideoplayer&task=preview&tmpl=component&appWidth='.$row->appWidth.'&appHeight='.$row->appHeight, ($row->appWidth+30), ($row->appHeight+30) );
	}
	
	function show_theme(&$rows, &$pageNav, &$lists){
		JHTML::_('behavior.tooltip'); ?>
		<script type="text/javascript">
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'preview') {
				submitform( pressbutton );
				return;
			}
			if (pressbutton == 'cancel'){
				submitform( pressbutton );
				return;
			}
			submitform( pressbutton );
		}
		Joomla.tableOrdering=function( order, dir, task ) {
			var form = document.adminForm;
			form.filter_order_theme.value     = order;
			form.filter_order_Dir_theme.value = dir;
			submitform( task );
		}
		function isChecked(isitchecked){
			if (isitchecked == true){
				document.adminForm.boxchecked.value++;
			}
			else {
				document.adminForm.boxchecked.value--;
			}
		}
		</script>
		<form action="index.php?option=com_spidervideoplayer" method="post" name="adminForm" id="adminForm">
			<table width="100%">
				<tr>
					<td align="left" width="100%">
						<input type="text" name="search_theme" id="search_theme" value="<?php echo $lists['search_theme'];?>" class="text_area" placeholder="Search" style="margin:0px" />
						<button class="btn tip hasTooltip" type="submit" data-original-title="Search"><i class="icon-search"></i></button>
						<button class="btn tip hasTooltip" type="button" onclick="document.getElementById('search_theme').value='';this.form.submit();" data-original-title="Clear"><i class="icon-remove"></i></button>
						<div class="btn-group pull-right hidden-phone">
							<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
							<?php echo $pageNav->getLimitBox(); ?>
						</div>
					</td>
				</tr>
			</table>
				
			<table  class="table table-striped">
				<thead>
					<tr>
						<th width="30" class="title"><?php echo "#" ?></td>
						<th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)"></th>
						<th width="30" class="title"><?php echo JHTML::_('grid.sort',   'ID', 'id', @$lists['order_Dir'], @$lists['order'] ); ?></td>
						<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th><?php echo JText::_('Default'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<?php
				$k = 0;
				for($i=0, $n=count($rows); $i < $n ; $i++){
					$row = &$rows[$i];
					$checked 	= JHTML::_('grid.id', $i, $row->id);
					$link 		= JRoute::_( 'index.php?option=com_spidervideoplayer&task=edit_theme&cid[]='. $row->id ); ?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $i+1?></td>
						<td><?php echo $checked?></td>
						<td align="center"><?php echo $row->id?></td>
						<td><a href="<?php echo $link;?>"><?php echo $row->title?></a></td>
						<td align="center">
							<?php if ( $row->default == 1 ) : ?>
								<img src="templates/hathor/images/menu/icon-16-default.png" alt="<?php echo JText::_( 'Default' ); ?>" />
							<?php else : ?>
								&nbsp;
							<?php endif; ?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer">
			<input type="hidden" name="task" value="theme">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="filter_order_theme" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir_theme" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
	<?php
	}
	
	function edit_theme(&$row){
		JRequest::setVar( 'hidemainmenu', 1 );
		$editor	=& JFactory::getEditor();
		$document	=& JFactory::getDocument();
		$document->addScript(JURI::root() . 'administrator/components/com_spidervideoplayer/jscolor/jscolor.js');
		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_spidervideoplayer/elements';
		$document->addScript($cmpnt_js_path.'/jquery-1.7.1.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.core.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.widget.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.mouse.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.slider.js');
		$document->addScript($cmpnt_js_path.'/jquery.ui.sortable.js');
		$document->addStyleSheet($cmpnt_js_path.'/jquery-ui.css');
		$document->addStyleSheet($cmpnt_js_path.'/parseTheme.css');
		if($row->ctrlsStack)
			$value=$row->ctrlsStack;
		else
			$value='playPrev:1,playPause:1,stop:1,vol:1,time:1,playNext:1,+:0,repeat:1,shuffle:1,hd:1,playlist:1,lib:1,fullScreen:1,play:1,pause:1';
		
		$ctrls = explode(",", $value);
		$n = count($ctrls);
		$path=JURI::root(true)."/administrator/components/com_spidervideoplayer/images/";
		?>
		<script language="javascript" type="text/javascript">
		SqueezeBox.presets.onClose=function (){document.getElementById('sbox-content').innerHTML=""};
		SqueezeBox.presets.onOpen=function (){refresh_ctrl();};
		function get_radio_value(name) {
			for (var i=0; i < document.getElementsByName(name).length; i++) {
				if (document.getElementsByName(name)[i].checked) {
					var rad_val = document.getElementsByName(name)[i].value;
					return rad_val;
				}
			}
		}
		function refresh_() {
			appWidth = parseInt(document.getElementById('appWidth').value);
			appHeight = parseInt(document.getElementById('appHeight').value);
			refresh_ctrl();
			document.getElementById('toolbar-popup-popup').childNodes[1].href='index.php?option=com_spidervideoplayer&task=preview&tmpl=component&appWidth='+appWidth+'&appHeight='+appHeight;
			document.getElementById('toolbar-popup-popup').childNodes[1].setAttribute('rel',"{handler: 'iframe', size: {x:"+(appWidth+30)+", y: "+(appHeight+30)+"}}");
		}
		Joomla.submitbutton= function (pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'cancel_theme') {
				submitform( pressbutton );
				return;
			}
			if(form.title.value=="") {
				alert('Set Theme title');
				return;
			}
			refresh_ctrl();
			submitform( pressbutton );
		}
		function IsNumeric(input){
			var RE = /^-{0,1}\d*\.{0,1}\d+$/;
			return (RE.test(input));
		}
		function refresh_ctrl(){
			ctrlStack="";
			w=document.getElementById('tr_arr').childNodes;
			for(i in w)
			if(IsNumeric(i))
			if(w[i].nodeType!=3) {
				if(ctrlStack=="")
					ctrlStack=w[i].getAttribute('value')+':'+w[i].getAttribute('active'); ///???
				else
					ctrlStack=ctrlStack+","+w[i].getAttribute('value')+':'+w[i].getAttribute('active'); ///???
			}
			document.getElementById('ctrlsStack').value=ctrlStack;
		}
		function check_isnum(e){
			var chCode1 = e.which || e.keyCode;
			if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57))
				return false;
			return true;
		}
		<?php
		$sliders=array("defaultVol", "centerBtnAlpha", "watermarkAlpha", "framesBgAlpha", "ctrlsMainAlpha" );
		foreach( $sliders as $slider) { ?>
			setTimeout(function() {
				$("#slider-<?php echo $slider?>")[0].slide = null;
				$( "#slider-<?php echo $slider?>" ).slider({
					range: "min",
					value: <?php echo $row->$slider?>,
					min: 1,
					max: 100,
					slide: function( event, ui ) {
						$( "#<?php echo $slider?>" ).val( "" + ui.value );
					}
				});
				$( "#<?php echo $slider?>" ).val( "" + $( "#slider-<?php echo $slider?>" ).slider( "value" ) );
			},0);
		<?php } ?>
		$(document).ready(function($) {
			$(document).ready(function(){
				for (var i = 0; i < <?php echo $n ?>; i++) {
					$("#arr_" + i).bind('click',{i:i},function(event){
					i=event.data.i;
					image=document.getElementById("arr_" + i).getAttribute('image');
					if(document.getElementById("td_arr_" + i).getAttribute('active') == 0) {
						document.getElementById("arr_" + i).src="<?php echo $path ;?>"+image+'_1.png';
						document.getElementById("td_arr_" + i).setAttribute('active','1');
					}
					else {
						document.getElementById("arr_" + i).src="<?php echo $path ;?>"+image+'_0.png';
						document.getElementById("td_arr_" + i).setAttribute('active','0');
					}
					});
				}
			});
		});
		setTimeout(function() {
			$( "#tr_arr" ).sortable();
			$( "#tr_arr" ).disableSelection();
		},0);
		//-->
		</script>
		<style>
			table.admintable.spiderVideoTab7 { border:1px solid #dedede; }
			table.admintable.spiderVideoTab7 td { padding:0 10px; }
			table.admintable.spiderVideoTab7 td.key label,
			table.admintable.spiderVideoTab7 td.paramlist_key label {
				font-size:13px;
				font-weight:600;
			}
			table.admintable.spiderVideoTab7 td input { margin:10px 0; }
			table.admintable.spiderVideoTab7 td.paramlist_value label {
				margin: 10px 16px 10px 0px;
				float: left;
				padding: 0;
			}
			table.admintable.spiderVideoTab7 td.paramlist_value.range { padding:10px; }
			table.admintable.spiderVideoTab7 td.paramlist_value.range p { color:#f6931f; float:left; border:0; margin: 0; }
			table.admintable.spiderVideoTab7 td.paramlist_value.range p input { border:0; margin:0; padding:0; font-weight:bold; color:#f6931f; width:20px; }
			table.admintable.spiderVideoTab7 td.paramlist_value.range>div { width:140px; float:left; margin:4px; }
			table.admintable.spiderVideoTab7 td.paramlist_value label input {  margin:2px 6px 2px 0px; }
			table.admintable.spiderVideoTab7 td a.modal { 
				display:table;
				margin:10px 0;
			}
		</style>
		<?php  $editor->display('text_for_date','','100%','250','40','6');?>
		<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general_parameters" data-toggle="tab">General Parameters</a></li>
				<li><a href="#style_parameters" data-toggle="tab">Style Parameters</a></li>
				<li><a href="#playback_parameters" data-toggle="tab">Playback Parameters</a></li>
				<li><a href="#playlist_parameters" data-toggle="tab">Playlist and Library Parameters</a></li>
				<li><a href="#video_parameters" data-toggle="tab">Video Control Parameters</a></li>
			</ul>
			
			<div class="tab-content">
			
				<div class="tab-pane active" id="general_parameters">
					<fieldset class="adminform">
						<legend>General Parameters</legend>
						<table class="admintable spiderVideoTab7" border="1">
							<tr>
								<td class="key"><label for="title">Title of theme:</label></td>
								<td><input type="text" name="title" id="title" value="<?php echo htmlspecialchars($row->title); ?>" /></td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label id="appWidth-lbl" for="appWidth">Width of player:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="appWidth" id="appWidth" value="<?php echo $row->appWidth; ?>" class="text_area" onchange="refresh_()" onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label id="appHeight-lbl" for="appHeight">Height of player:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="appHeight" id="appHeight" value="<?php echo $row->appHeight; ?>" class="text_area" onchange="refresh_()" onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Start with:</label></span>
								</td>
								<td class="paramlist_value">
									<label for="startWithLib1" class="radiobtn"><input type="radio" name="startWithLib" id="startWithLib1" value="0" <?php if(!($row->startWithLib)) echo 'checked="checked"'; ?> >Video</label>
									<label for="startWithLib2" class="radiobtn"><input type="radio" name="startWithLib" id="startWithLib2" value="1"<?php if($row->startWithLib) echo 'checked="checked"'; ?> >Library</label>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Show Track Id:</label></span>
								</td>
								<td class="paramlist_value">
									<label for="show_trackid1" class="radiobtn"><input type="radio" name="show_trackid" id="show_trackid1" value="0" <?php if(!($row->show_trackid)) echo 'checked="checked"'; ?> >No</label>
									<label for="show_trackid2" class="radiobtn"><input type="radio" name="show_trackid" id="show_trackid2" value="1"<?php if($row->show_trackid) echo 'checked="checked"'; ?> >Yes</label>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Auto hide time:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="autohideTime" id="autohideTime" value="<?php echo $row->autohideTime; ?>" class="text_area" onkeypress="return check_isnum(event)"> sec
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Keep aspect ratio (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'keepAspectRatio' , 'class="inputbox"', $row->keepAspectRatio); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control bar over video:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'ctrlsOverVid' , 'class="inputbox"', $row->ctrlsOverVid); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark image (only for flash version):</label></span>
								</td>
								<td>
									<input type="hidden" value="<?php echo htmlspecialchars($row->watermarkUrl); ?>" name="watermarkUrl" id="watermarkUrl" />
									<a class="modal" title="Image" href="index.php?option=com_spidervideoplayer&task=media_manager_image&type=watermarkUrl&tmpl=component" rel="{handler: 'iframe', size: {x: 570, y: 400}}">Select Image</a>
									<a href="javascript:removeImage();">Remove Image</a>
									<div style=" position:absolute; width:1px; height:1px; top:0px; overflow:hidden">
										<textarea id="tempimage" name="tempimage" class="mce_editable"></textarea><br />
									</div>
									<script type="text/javascript">
									function removeImage() {
										document.getElementById("imagebox").style.display="none";
										document.getElementById("watermarkUrl").value='';
									}
									</script>
									<div style="height:150px;">
										<img style="display:<?php if($row->watermarkUrl=='') echo 'none'; else echo 'block' ?>;height:150px" height="150" id="imagebox" src="<?php echo htmlspecialchars($row->watermarkUrl) ; ?>" />
									</div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark Position (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<label for="watermarkPos1" class="radiobtn"><input type="radio" name="watermarkPos" id="watermarkPos1" value="1" <?php if($row->watermarkPos=="1") echo 'checked="checked"'; ?>>Top left</label>
									<label for="watermarkPos2" class="radiobtn"><input type="radio" name="watermarkPos" id="watermarkPos2" value="2" <?php if($row->watermarkPos=="2") echo 'checked="checked"'; ?>>Top right</label>
									<label for="watermarkPos3" class="radiobtn"><input type="radio" name="watermarkPos" id="watermarkPos3" value="3" <?php if($row->watermarkPos=="3") echo 'checked="checked"'; ?>>Bottom left</label>
									<label for="watermarkPos4" class="radiobtn"><input type="radio" name="watermarkPos" id="watermarkPos4" value="4" <?php if($row->watermarkPos=="4") echo 'checked="checked"'; ?>>Bottom right</label>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark size (only for flash version)</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="watermarkSize" id="watermarkSize" value="<?php echo $row->watermarkSize; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark Margin (only for flash version)</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="watermarkSpacing" id="watermarkSpacing" value="<?php echo $row->watermarkSpacing; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Watermark opacity (only for flash version):</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" name="watermarkAlpha" id="watermarkAlpha" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-watermarkAlpha" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
					
				<div class="tab-pane" id="style_parameters">
					<fieldset class="adminform">
					<legend>Style Parameters</legend>
						<table class="admintable spiderVideoTab7" border="1">
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Center button opacity (only for flash version):</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" name="centerBtnAlpha" id="centerBtnAlpha" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-centerBtnAlpha" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Background color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="appBgColor" id="appBgColor" value="<?php echo $row->appBgColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Video background color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="vidBgColor" id="vidBgColor" value="<?php echo $row->vidBgColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Frames background color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="framesBgColor" id="framesBgColor" value="<?php echo $row->framesBgColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
										<span class="editlinktip"><label>Frames background opacity:</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" name="framesBgAlpha" id="framesBgAlpha" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-framesBgAlpha" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control buttons main color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="ctrlsMainColor" id="ctrlsMainColor" value="<?php echo $row->ctrlsMainColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control buttons hover color (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="ctrlsMainHoverColor" id="ctrlsMainHoverColor" value="<?php echo $row->ctrlsMainHoverColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control buttons opacity:</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" name="ctrlsMainAlpha" id="ctrlsMainAlpha" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-ctrlsMainAlpha" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Sliders color</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="slideColor" id="slideColor" value="<?php echo $row->slideColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Hovered item background Color</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="itemBgHoverColor" id="itemBgHoverColor" value="<?php echo $row->itemBgHoverColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Selected item background Color</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="itemBgSelectedColor" id="itemBgSelectedColor" value="<?php echo $row->itemBgSelectedColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Text color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="textColor" id="textColor" value="<?php echo $row->textColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Hovered text color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="textHoverColor" id="textHoverColor" value="<?php echo $row->textHoverColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Selected text color:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="textSelectedColor" id="textSelectedColor" value="<?php echo $row->textSelectedColor; ?>" class="color">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Loading animation type (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<label for="loadinAnimType1" class="radiobtn"><input type="radio" name="loadinAnimType" id="loadinAnimType1" value="1" <?php if($row->loadinAnimType=="1") echo 'checked="checked"'; ?> >Circles</label>
									
									<label for="loadinAnimType2" class="radiobtn"><input type="radio" name="loadinAnimType" id="loadinAnimType2" value="2" <?php if($row->loadinAnimType=="2") echo 'checked="checked"'; ?> >Lines</label>
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
					
				<div class="tab-pane" id="playback_parameters">
					<fieldset class="adminform">
					<legend>Playback Parameters</legend>
						<table class="admintable spiderVideoTab7" border="1">
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Auto play:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'autoPlay' , 'class="inputbox"', $row->autoPlay); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Auto next song:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'autoNext' , 'class="inputbox"',  $row->autoNext); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Auto next album:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'autoNextAlbum' , 'class="inputbox"',  $row->autoNextAlbum); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Default Volume:</label></span>
								</td>
								<td class="paramlist_value range">
									<p><input type="text" name="defaultVol" id="defaultVol" onkeypress="return check_isnum(event)">%</p>
									<div id="slider-defaultVol" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"></div>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Repeat</label></span>
								</td>
								<td class="paramlist_value">									
									<label for="defaultRepeat1" class="radiobtn"><input type="radio" name="defaultRepeat" id="defaultRepeat1" value="repeatOff" <?php if($row->defaultRepeat=="repeatOff") echo 'checked="checked"'; ?> >Off</label>										
									<label for="defaultRepeat2" class="radiobtn"><input type="radio" name="defaultRepeat" id="defaultRepeat2" value="repeatOne" <?php if($row->defaultRepeat=="repeatOne") echo 'checked="checked"'; ?>>One</label>
									<label for="defaultRepeat3" class="radiobtn"><input type="radio" name="defaultRepeat" id="defaultRepeat3" value="repeatAll" <?php if($row->defaultRepeat=="repeatAll") echo 'checked="checked"'; ?>>All</label>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Shuffle:</label></span>
								</td>
								<td class="paramlist_value">
									<label for="defaultShuffle1" class="radiobtn"><input type="radio" name="defaultShuffle" id="defaultShuffle1" value="shuffleOff" <?php if($row->defaultShuffle=="shuffleOff") echo 'checked="checked"';if($row->defaultShuffle=="") echo 'checked="checked"' ; ?> >No</label><label for="defaultShuffle2" class="radiobtn"><input type="radio" name="defaultShuffle" id="defaultShuffle2" value="shuffleOn"  <?php if($row->defaultShuffle=="shuffleOn") echo 'checked="checked"'; ?> >Yes</label>										
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control bar auto hide:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'ctrlsSlideOut' , 'class="inputbox"', $row->ctrlsSlideOut); ?>
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
					
				<div class="tab-pane" id="playlist_parameters">
					<fieldset class="adminform">
					<legend>Playlist and Library Parameters</legend>
						<table class="admintable spiderVideoTab7" border="1">
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Playlist Position:</label></span>
								</td>
								<td class="paramlist_value">
									<label for="playlistPos1" class="radiobtn"><input type="radio" name="playlistPos" id="playlistPos1" value="1" <?php if($row->playlistPos=="1") echo 'checked="checked"'; ?>>Left</label>										
									<label for="playlistPos2" class="radiobtn"><input type="radio" name="playlistPos" id="playlistPos2" value="2" <?php if($row->playlistPos=="2") echo 'checked="checked"'; ?>>Right</label>										
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label id="playlistWidth-lbl" for="playlistWidth">Width of playlist:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="playlistWidth" id="playlistWidth" value="<?php echo $row->playlistWidth; ?>" class="text_area"  onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Playlist over video:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'playlistOverVid' , 'class="inputbox"', $row->playlistOverVid); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Open playlist at start:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'openPlaylistAtStart' , 'class="inputbox"', $row->openPlaylistAtStart); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Playlist auto hide:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'playlistAutoHide' , 'class="inputbox"',  $row->playlistAutoHide); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Playlist text size: </label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="playlistTextSize" id="playlistTextSize" value="<?php echo $row->playlistTextSize; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Library colums:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="libCols" id="libCols" value="<?php echo $row->libCols; ?>" class="text_area" onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Library rows: </label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="libRows" id="libRows" value="<?php echo $row->libRows; ?>" class="text_area" onkeypress="return check_isnum(event)">
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Library list text size:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="libListTextSize" id="libListTextSize" value="<?php echo $row->libListTextSize; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Library details text size:</label></span>
								</td>
								<td class="paramlist_value">
									<input type="text" name="libDetailsTextSize" id="libDetailsTextSize" value="<?php echo $row->libDetailsTextSize; ?>" class="text_area" onkeypress="return check_isnum(event)"> pixels
								</td>
							</tr>
							</table>
					</fieldset>
				</div>
				
				<div class="tab-pane" id="video_parameters">
					<fieldset class="adminform">
						<legend>Video Control Parameters</legend>
						<table class="admintable spiderVideoTab7" border="1">
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Play/pause on click:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'clickOnVid' , 'class="inputbox"', $row->clickOnVid); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Play/pause by space key:</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'spaceOnVid' , 'class="inputbox"', $row->spaceOnVid); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Volume control by mouse scroll (only for flash version):</label></span>
								</td>
								<td class="paramlist_value">
									<?php echo JHTML::_('select.booleanlist', 'mouseWheel' , 'class="inputbox"', $row->mouseWheel); ?>
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Control bar position:</label></span>
								</td>
								<td class="paramlist_value">									
									<label for="ctrlsPos1" class="radiobtn"><input type="radio" name="ctrlsPos" id="ctrlsPos1" value="1" <?php if($row->ctrlsPos=="1") echo 'checked="checked"'; ?> >Up</label>										
									<label for="ctrlsPos2" class="radiobtn"><input type="radio" name="ctrlsPos" id="ctrlsPos2" value="2" <?php if($row->ctrlsPos=="2") echo 'checked="checked"'; ?> >Down</label>										
								</td>
							</tr>
							<tr>
								<td width="20%" class="paramlist_key">
									<span class="editlinktip"><label>Buttons order on control bar:<br>(Drag and Drop)</label></span>
								</td>
								<td class="paramlist_value">
										<table border="0" bgcolor="#666666" style="background:#666666" cellpadding="0" cellspacing="0" width="70%">
										<?php
										echo '<tr valign="top" id="tr_arr" valign="middle">';
										foreach($ctrls as $key =>  $x){
											$y = explode(":", $x);
											$ctrl	=$y[0];
											$active	=$y[1];
											if($ctrl.'_'.$active=='+_1' or $ctrl.'_'.$active=='+_0' ) {
												echo '<td style="width:150px" id="td_arr_'.$key.'"  active="'.$active.'" value="'.$ctrl.'" width="40" align="center" style="padding:4px"><img src="'.$path.$ctrl.'_'.$active.'.png" id="arr_'.$key.'" image="'.$ctrl.'" style="cursor:pointer"/></td>';
											}
											else {
												echo '<td id="td_arr_'.$key.'"  active="'.$active.'" value="'.$ctrl.'" width="40" align="center" style="padding:4px"><img src="'.$path.$ctrl.'_'.$active.'.png" id="arr_'.$key.'" image="'.$ctrl.'" style="cursor:pointer"/></td>';
											}
										}
										echo "</tr>"
										?>
									</table>
									<input type="hidden" name="ctrlsStack" id="ctrlsStack" value="<?php echo $value; ?>">
								</td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
			
			<input type="hidden" name="option" value="com_spidervideoplayer" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
		<?php
		$bar=& JToolBar::getInstance( 'toolbar' );
		$bar->appendButton( 'popup', 'preview', 'Preview', 'index.php?option=com_spidervideoplayer&task=preview&tmpl=component&appWidth='.$row->appWidth.'&appHeight='.$row->appHeight, ($row->appWidth+30), ($row->appHeight+30) );
	}
	
	
	///////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////// T A G //////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////
	function add_tag($lists){	
		JRequest::setVar( 'hidemainmenu', 1 ); ?>
		<script language="javascript" type="text/javascript">
		Joomla.submitbutton= function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel_tag') {
				submitform( pressbutton );
				return;
			}
			submitform( pressbutton );
		}
		function disableEnterKey(e) {
			var key;
			if(window.event)
				key = window.event.keyCode;     //IE
			else
				key = e.which;    				//firefox
			if(key == 13)
				return false;
			else
				return true;
		}
		</script>
		<style>
			table.admintable.spiderVideoTab12 { border:1px solid #dedede; }
			table.admintable.spiderVideoTab12 td { padding:0 10px; }
			table.admintable.spiderVideoTab12 td.key label { 
				text-align:center;
				font-size:14px;
				font-weight:700;
			}
			table.admintable.spiderVideoTab12 td input { margin:10px 0; }
			table.admintable.spiderVideoTab12 td #video_list td { padding:0; }
			table.admintable.spiderVideoTab12 td .controls label {
				margin: 10px 16px 10px 0px;
				float: left;
				padding: 0;
			}
			table.admintable.spiderVideoTab12 td .controls label input { margin:2px 6px 2px 0px; }
		</style>
		<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
			<table class="admintable spiderVideoTab12" border="1">
				<tr>
					<td class="key"><label for="name"><?php echo JText::_( 'Name' ); ?>:</label></td>
					<td><input type="text" name="name" id="name" size="30"  onKeyPress="return disableEnterKey(event)" /></td>
				</tr>
				<tr>
					<td class="key"><label for="required"><?php echo JText::_( 'Required' ); ?>:</label></td>
					<td><?php echo $lists['required']?></td>
				</tr>
				<tr>
				<td class="key">
					<label for="published">
						<?php echo JText::_( 'Published' ); ?>:
					</label>
				</td>
				<td>
					<?php echo $lists['published']?>
				</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	function show_tag(&$rows, &$pageNav, &$lists){
		JHTML::_('behavior.tooltip');
		$ordering = ($lists['order'] == 'ordering'); ?>
		<script type="text/javascript">
			Joomla.submitbutton= function (pressbutton){
				var form = document.adminForm;
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}
				submitform( pressbutton );
			}
			Joomla.tableOrdering=function( order, dir, task ) {
				var form = document.adminForm;
				form.filter_order_tag.value     = order;
				form.filter_order_Dir_tag.value = dir;
				submitform( task );
			}
		</script>
		<form action="index.php?option=com_spidervideoplayer" method="post" name="adminForm" id="adminForm">
			<table width="100%">
				<tr>
					<td align="left" width="100%">
						<input type="text" name="search_tag" id="search_tag" value="<?php echo $lists['search_tag'];?>" class="text_area" placeholder="Search" style="margin:0px" />
						<button class="btn tip hasTooltip" type="submit" data-original-title="Search"><i class="icon-search"></i></button>
						<button class="btn tip hasTooltip" type="button" onclick="document.getElementById('search_tag').value='';this.form.submit();" data-original-title="Clear"><i class="icon-remove"></i></button>
						<div class="btn-group pull-right hidden-phone">
						<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
						<?php echo $pageNav->getLimitBox(); ?>
						</div>
					</td>
				</tr>
			</table>
			
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="30" class="title"><?php echo "#" ?></td>
						<th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)"></th>
						<th width="30" class="title"><?php echo JHTML::_('grid.sort',   'ID', 'id', @$lists['order_Dir'], @$lists['order'] ); ?></td>
						<th><?php echo JHTML::_('grid.sort', 'Name', 'name', @$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th width="100">
							<?php echo JHTML::_('grid.sort',   'Order', 'ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
							<a href="javascript:saveorder(1, 'saveorder')" rel="tooltip" class=" pull-right" data-original-title="Save Order" style="opacity: 0.2; cursor: default;" onclick="return true;"><i class="icon-menu-2"></i></a>
						</th>
						<th nowrap="nowrap" width="100"><?php echo JHTML::_('grid.sort',   'Required', 'required',@$lists['order_Dir'], @$lists['order'] ); ?></th>
						<th nowrap="nowrap" width="100"><?php echo JHTML::_('grid.sort',   'Published', 'published',@$lists['order_Dir'], @$lists['order'] ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<?php
				$k = 0;
				for($i=0, $n=count($rows); $i < $n ; $i++) {
					$row = &$rows[$i];
					$checked 	= JHTML::_('grid.id', $i, $row->id);
					$published 	= published($row, $i, 'tag');
					$access 	= access($row, $i, $row->required );
					$link 		= JRoute::_( 'index.php?option=com_spidervideoplayer&task=edit_tag&cid[]='. $row->id ); ?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $i+1?></td>
						<td><?php echo $checked?></td>
						<td align="center"><?php echo $row->id?></td>
						<td><a href="<?php echo $link;?>"><?php echo $row->name?></a></td>
						<td class="order">
							<span><?php echo $pageNav->orderUpIcon( $i, 1, 'orderup', 'Move Up', $ordering);?></span>
							<span><?php echo $pageNav->orderDownIcon( $i, $n, 1, 'orderdown', 'Move Down', $ordering ); ?></span>
							<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5" style="width:15px" value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
						</td>
						<td align="center"><?php echo $access?></td>
						<td align="center"><?php echo $published?></td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer">
			<input type="hidden" name="task" value="tag">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="filter_order_tag" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir_tag" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
	<?php
	}
	
	function edit_tag(&$lists, &$row){
		JRequest::setVar( 'hidemainmenu', 1 );
		$editor	=& JFactory::getEditor(); ?>
		<script language="javascript" type="text/javascript">
			<!--
			Joomla.submitbutton= function (pressbutton){
				var form = document.adminForm;
				if (pressbutton == 'cancel_tag') {
					submitform( pressbutton );
					return;
				}
				submitform( pressbutton );
			}
			function disableEnterKey(e)	{
				var key;
				if(window.event)
					key = window.event.keyCode;     //IE
				else
					key = e.which;     				//firefox
				if(key == 13)
					return false;
				else
					return true;
			}
			//-->
		</script>
		<style>
			table.admintable.spiderVideoTab13 { border:1px solid #dedede; }
			table.admintable.spiderVideoTab13 td { padding:0 10px; }
			table.admintable.spiderVideoTab13 td.key label { 
				text-align:center;
				font-size:14px;
				font-weight:700;
			}
			table.admintable.spiderVideoTab13 td input { margin:10px 0; }
			table.admintable.spiderVideoTab13 td #video_list td { padding:0; }
			table.admintable.spiderVideoTab13 td .controls label {
				margin: 10px 16px 10px 0px;
				float: left;
				padding: 0;
			}
			table.admintable.spiderVideoTab13 td .controls label input { margin:2px 6px 2px 0px; }
		</style>	
		<?php  $editor->display('text_for_date','','100%','250','40','6');?>
		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<table class="admintable spiderVideoTab13" border="1">
				<tr>
					<td class="key"><label for="name"><?php echo JText::_( 'Name' ); ?>:</label></td>
					<td><input type="text" name="name" id="name" size="30" value="<?php echo htmlspecialchars($row->name) ?>"  onKeyPress="return disableEnterKey(event)" /></td>
				</tr>
				<tr>
					<td class="key"><label for="required"><?php echo JText::_( 'Required' ); ?>: </label></td>
					<td><?php echo $lists['required']; ?></td>
				</tr>
				<tr>
					<td class="key"><label for="published"><?php echo JText::_( 'Published' ); ?>:</label></td>
					<td><?php echo $lists['published']; ?></td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_spidervideoplayer" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	
	///////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////// M E D I A //////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////
	function media_manager_video(){ ?>
		<script type="text/javascript">
		function set_value() {
			var video= document.getElementById('video').value;
			if(video=="") {
				alert('Video is empty');
				return;
			}
			window.parent.document.getElementById('<?php echo JRequest::getVar('type'); ?>'+'_link').innerHTML=video;
			window.parent.document.getElementById('<?php echo JRequest::getVar('type'); ?>').value=video;
			window.parent.SqueezeBox.close();
		}
		function set_selected_video(path) {
			document.getElementById('video').value=path.replace(/\\/g,'/').replace(/\/\//g,'/');
		}
		</script>
		<style>
			button { 
				padding: 3px; 
				border: 1px solid #CCCCCC; 
				color: #0B55C4; 
				background-color: white; 
			}
		</style>
		<?php  
		$folder=JRequest::getVar('folder','');
		$dir = '../media/com_spidervideoplayer/upload/'.$folder;
		function delfiles($del_file) {
			if(is_dir($del_file)) {
				$del_folder = scandir($del_file);
				foreach($del_folder as $file)
					if($file!='.' and $file!='..')
						delfiles($del_file.'/'.$file);
					rmdir($del_file);
			}
			else
			unlink($del_file);
		}
		if(JRequest::getVar('del_file','')!='')
			delfiles(JRequest::getVar('del_file'));
		if(JRequest::getVar('foldername','')!='')
			mkdir($dir.'/'.JRequest::getVar('foldername'));

		$files = JRequest::getVar('file', null, 'files', 'array');
		$allowedExtensions = array("flv","mp4", "f4v", "f4p", "f4a", "f4b","webm","ogg");
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$upload_mb = min($max_upload, $max_post, $memory_limit);
		if (isset($files["type"])) {
			if(isset($_SERVER['CONTENT_LENGTH']) && ($_SERVER['CONTENT_LENGTH']/1048576)>$upload_mb)
				echo '<span style="color:red;">The file size exceeds the maximum allowed size. You can upload videos directly by FTP.</span>' ;
			else {
				if ($files["error"] > 0) {
					echo '<span style="color:red;">Error Code: <b>' . $files["error"] . '</b></span><br />';
				}
				else {
					if (file_exists($dir.'/'. $files["name"])) {
						echo '<span style="color:red;"><b>'.$files["name"] . '</b> already exists.</span><br />';
					}
					else {
						$extension= end(explode(".", strtolower($files['name'])));
						if (!in_array($extension,$allowedExtensions)) {
							echo '<span style="color:red;"><b>'.$files["name"].'</b> invalid file format</span><br />';
						}
						else {
							move_uploaded_file($files["tmp_name"], $dir.'/'. $files["name"]);
							echo "<span style='color:blue;'>Stored in: <b>" . $folder.'/'. $files["name"].'</b></span><br />';
						}
					}
				}
			}
		}
		else {
			echo 'Allowed file extensions - flv, mp4, f4v, f4p, f4a, f4b';
		}
		echo "<br />Directory: <b>".$folder.'/</b><div style="float: right">
		<button type="button" onclick="set_value();">Insert</button>
		<button type="button" onclick="window.parent.SqueezeBox.close();">Cancel</button>
		</div>';
		echo "<br /><br />";
		$files1 = scandir($dir);
		$nofiles=true;
		?>
		<table cellpadding="5" cellspacing="0" border="1" width="500">
			<tr><td>Name</td><td>Size</td><td>Delete</td></tr>
			<?php
			if($folder!='')
				echo '<tr><td colspan="3"><a href="index.php?option=com_spidervideoplayer&task=media_manager_video&type='.JRequest::getVar('type').'&tmpl=component&folder='.substr($folder,0,strrpos($folder,'/')).'" title="Directory Up" style="text-decoration:none; margin:5px;"><button type="button" onclick=""><img src="components/com_spidervideoplayer/images/arrow_up.png" alt="" />Folder Up</button></a></td></tr>';
			foreach($files1 as $file)
			if($file!='.' and $file!='..' and is_dir($dir.'/'.$file)){
				echo '<tr><td><a href="index.php?option=com_spidervideoplayer&task=media_manager_video&type='.JRequest::getVar('type').'&tmpl=component&folder='.$folder.'/'.$file.'" style="color:#333399"><img src="components/com_spidervideoplayer/images/folder_sm.png" alt="" />&nbsp;'. $file .'</a></td><td>&nbsp;</td><td><a  style="color:#333399" href="javascript:if(confirm(\'Are you sure you want to delete the directory and all its contents?\'))document.forms.delfileform.del_file.value=\''.addslashes($dir.'/'.$file).'\';document.forms.delfileform.submit();">Delete</a></td></tr>';
				$nofiles=false;
			}
			foreach($files1 as $file)
				if(!(is_dir($dir.'/'.$file)))
				if (in_array(end(explode(".", strtolower($file))),$allowedExtensions)){
					echo '<tr><td><a href="javascript:set_selected_video(\''.addslashes($dir.'/'.$file).'\')" style="color:#333399">'. $file .'</a></td><td>'.round(filesize($dir.'/'.$file)/1024).' Kb </td><td><a style="color:#333399" href="javascript:if(confirm(\'Are you sure you want to delete?\'))
					document.forms.delfileform.del_file.value=\''.addslashes($dir.'/'.$file).'\';document.forms.delfileform.submit();">Delete</a></td></tr>';
					$nofiles=false;
				}
			if($nofiles)
				echo '<tr><td colspan="3">No Files</td></tr>'; ?>
		</table>
		
		<br />

		<table cellpadding="5" cellspacing="0" border="1" width="500">
			<tr><td>Create a New Folder</td></tr>
			<tr>
				<td>
					<form action="index.php?option=com_spidervideoplayer&task=media_manager_video&type=<?php echo JRequest::getVar('type'); ?>&tmpl=component&folder=<?php echo $folder ?>" method="post" style="margin:5px;">
						<label for="file">Folder Name</label>
						<input type="text" name="foldername" id="foldername" />
						<input type="submit" name="submit" value="Create" />
					</form>
				</td>
			</tr>
		</table>
		
		<br />

		<table cellpadding="5" cellspacing="0" border="1" width="500">
			<tr><td>Upload a File</td></tr>
			<tr>
				<td>
					<form action="index.php?option=com_spidervideoplayer&task=media_manager_video&type=<?php echo JRequest::getVar('type'); ?>&tmpl=component&folder=<?php echo $folder ?>" method="post"	enctype="multipart/form-data" style="margin:5px;">
						<label for="file">Select a file:</label>
						<input type="file" name="file" id="file" />
						<input type="submit" name="submit" value="Upload" />
					</form>
				</td>
			</tr>
		</table>

		<br /><br />

		<label for="file">Video URL:</label>
		<input type="text" name="video" id="video" size="50" />
		
		<br /><br /><br />
		
		<form action="index.php?option=com_spidervideoplayer&task=media_manager_video&type=<?php echo JRequest::getVar('type'); ?>&tmpl=component&folder=<?php echo $folder ?>" method="post" name="delfileform">
			<input type="hidden" name="del_file" />
		</form>
	<?php
	}
	
	function media_manager_image(){ ?>
		<script type="text/javascript">
		function set_value()	{
			var image= document.getElementById('image').value;
			if(image==""){
				alert('Image is empty');
				return;
			}
			window.parent.document.getElementById('imagebox').src=image;
			window.parent.document.getElementById('imagebox').style.display="block";
			window.parent.document.getElementById('<?php echo JRequest::getVar('type'); ?>').value=image;
			window.parent.SqueezeBox.close();
		}
		function set_selected_image(path){
			document.getElementById('image').value=path.replace(/\\/g,'/').replace(/\/\//g,'/');
		}
		</script>
		<style>
			button { 
				padding: 3px; 
				border: 1px solid #CCCCCC; 
				color: #0B55C4; 
				background-color: white; 
			}
		</style>
		<?php  $folder=JRequest::getVar('folder','');
		$dir = '../media/com_spidervideoplayer/upload/'.$folder;
		function delfiles($del_file){
			if(is_dir($del_file)) {
				$del_folder = scandir($del_file);
				foreach($del_folder as $file)
				if($file!='.' and $file!='..')
				delfiles($del_file.'/'.$file);
				rmdir($del_file);
			}
			else
		unlink($del_file);
		}
		if(JRequest::getVar('del_file','')!='')
			delfiles(JRequest::getVar('del_file'));
		if(JRequest::getVar('foldername','')!='')
			mkdir($dir.'/'.JRequest::getVar('foldername'));
		$files = JRequest::getVar('file', null, 'files', 'array');
		$allowedExtensions = array("jpg","png","gif");
		
		if (isset($files["type"])){
			if ($files["error"] > 0){
				echo '<span style="color:red;">Error Code: <b>' . $files["error"] . '</b></span><br />';
			}
			else	{
				if (file_exists($dir.'/'. $files["name"])){
					echo '<span style="color:red;"><b>'.$files["name"] . '</b> already exists.</span><br />';
				}
				else {
					$extension= end(explode(".", strtolower($files['name'])));
					if (!in_array($extension,$allowedExtensions)){
						echo '<span style="color:red;"><b>'.$files["name"].'</b> invalid file format</span><br />';
					}
					else {
						move_uploaded_file($files["tmp_name"],
						$dir.'/'. $files["name"]);
						echo "<span style='color:blue;'>Stored in: <b>" . $folder.'/'. $files["name"].'</b></span><br />';
					}
				}
			}
		}
		else {
			echo 'Allowed file extensions - jpg, png, gif';
		}
		echo "<br />Directory: <b>".$folder.'/</b><div style="float: right">
		<button type="button" onclick="set_value();">Insert</button>
		<button type="button" onclick="window.parent.SqueezeBox.close();">Cancel</button>
		</div>';
		echo "<br /><br />";
		$files1 = scandir($dir);
		$nofiles=true; ?>
	
		<hr />

		<table cellpadding="5" cellspacing="0" border="1" width="500">
			<tr><td>Name</td><td>Size</td><td>Delete</td></tr>
			<?php
			if($folder!='')
				echo '<tr><td colspan="3"><a href="index.php?option=com_spidervideoplayer&task=media_manager_image&type='.JRequest::getVar('type').'&tmpl=component&folder='.substr($folder,0,strrpos($folder,'/')).'" title="Directory Up" style="text-decoration:none; margin:5px;"><button type="button" onclick=""><img src="components/com_spidervideoplayer/images/arrow_up.png" alt="" />Folder Up</button></a></td></tr>';
			
			foreach($files1 as $file)
			if($file!='.' and $file!='..' and is_dir($dir.'/'.$file))	{
				echo '<tr><td><a href="index.php?option=com_spidervideoplayer&task=media_manager_image&type='.JRequest::getVar('type').'&tmpl=component&folder='.$folder.'/'.$file.'" style="color:#333399"><img src="components/com_spidervideoplayer/images/folder_sm.png" alt="" />&nbsp;'. $file .'</a></td><td>&nbsp;</td><td><a  style="color:#333399" href="javascript:if(confirm(\'Are you sure you want to delete the directory and all its contents?\'))document.forms.delfileform.del_file.value=\''.addslashes($dir.'/'.$file).'\';document.forms.delfileform.submit();">Delete</a></td></tr>';
				$nofiles=false;
			}
			
			foreach($files1 as $file)
			if(!(is_dir($dir.'/'.$file)))
				if (in_array(end(explode(".", strtolower($file))),$allowedExtensions)){
					echo '<tr><td><a href="javascript:set_selected_image(\''.addslashes($dir.'/'.$file).'\')" style="color:#333399">'. $file .'</a></td><td>'.round(filesize($dir.'/'.$file)/1024).' Kb </td><td><a style="color:#333399" href="javascript:if(confirm(\'Are you sure you want to delete?\'))
					document.forms.delfileform.del_file.value=\''.addslashes($dir.'/'.$file).'\';document.forms.delfileform.submit();">Delete</a></td></tr>';
					$nofiles=false;
				}
			if($nofiles)
				echo '<tr><td colspan="3">No Files</td></tr>';
			?>
		</table>
		
		<br />
	
		<table cellpadding="5" cellspacing="0" border="1" width="500">
			<tr><td>Create a New Folder</td></tr>
			<tr>
				<td>
					<form action="index.php?option=com_spidervideoplayer&task=media_manager_image&type=<?php echo JRequest::getVar('type'); ?>&tmpl=component&folder=<?php echo $folder ?>" method="post" style="margin:5px;">
						<label for="file">Folder Name</label>
						<input type="text" name="foldername" id="foldername" />
						<input type="submit" name="submit" value="Create" />
					</form>
				</td>
			</tr>
		</table>

		<br />

		<table cellpadding="5" cellspacing="0" border="1" width="500">
			<tr><td>Upload a File</td></tr>
			<tr>
				<td>
					<form action="index.php?option=com_spidervideoplayer&task=media_manager_image&type=<?php echo JRequest::getVar('type'); ?>&tmpl=component&folder=<?php echo $folder ?>" method="post"	enctype="multipart/form-data" style="margin:5px;">
						<label for="file">Select a file:</label>
						<input type="file" name="file" id="file" />
						<input type="submit" name="submit" value="Upload" />
					</form>
				</td>
			</tr>
		</table>
	
		<br /><br />
		
		<label for="file">Image URL:</label>
		<input type="text" name="image" id="image" size="50" />
		
		<br /><br /><br />
		
		<form action="index.php?option=com_spidervideoplayer&task=media_manager_image&type=<?php echo JRequest::getVar('type'); ?>&tmpl=component&folder=<?php echo $folder ?>" method="post" name="delfileform">
			<input type="hidden" name="del_file" />
		</form>
	<?php
	}
	
	function preview_spidervideoplayer(&$row){
		if($row['appWidth']!="" && isset($row['appWidth']))
			$width=$row['appWidth'];
		else
			$width='700';
		if($row['appHeight']!="" && isset($row['appWidth']))
			$height=$row['appHeight'];
		else
			$height='400';
		$row_str=http_build_query($row);
		?>
		<script type="text/javascript" src="../components/com_spidervideoplayer/js/swfobject.js"></script>
		<div id="flashcontent"  style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px"></div>
		<script>
		function get_radio_value(name){
			for (var i=0; i < window.parent.document.getElementsByName(name).length; i++){
				if (window.parent.document.getElementsByName(name)[i].checked) {
					var rad_val = window.parent.document.getElementsByName(name)[i].value;
					return rad_val;
				}
			}
		}
		appWidth			=parseInt(window.parent.document.getElementById('appWidth').value);
		appHeight			=parseInt(window.parent.document.getElementById('appHeight').value);
		playlistWidth		=window.parent.document.getElementById('playlistWidth').value;
		startWithLib		=get_radio_value('startWithLib');
		show_trackid		=get_radio_value('show_trackid');
		autoPlay			=get_radio_value('autoPlay');
		autoNext			=get_radio_value('autoNext');
		autoNextAlbum		=get_radio_value('autoNextAlbum');
		defaultVol			=window.parent.document.getElementById('defaultVol').value;
		defaultRepeat		=get_radio_value('defaultRepeat');
		defaultShuffle		=get_radio_value('defaultShuffle');
		autohideTime		=window.parent.document.getElementById('autohideTime').value;
		centerBtnAlpha		=window.parent.document.getElementById('centerBtnAlpha').value;
		loadinAnimType		=get_radio_value('loadinAnimType');
		keepAspectRatio		=get_radio_value('keepAspectRatio');
		clickOnVid			=get_radio_value('clickOnVid');
		spaceOnVid			=get_radio_value('spaceOnVid');
		mouseWheel			=get_radio_value('mouseWheel');
		ctrlsPos			=get_radio_value('ctrlsPos');
		ctrlsStack			=window.parent.document.getElementById('ctrlsStack').value;
		ctrlsOverVid		=get_radio_value('ctrlsOverVid');
		ctrlsSlideOut		=get_radio_value('ctrlsSlideOut');
		watermarkUrl		=window.parent.document.getElementById('watermarkUrl').value;
		watermarkPos		=get_radio_value('watermarkPos');
		watermarkSize		=window.parent.document.getElementById('watermarkSize').value;
		watermarkSpacing	=window.parent.document.getElementById('watermarkSpacing').value;
		watermarkAlpha		=window.parent.document.getElementById('watermarkAlpha').value;
		playlistPos			=get_radio_value('playlistPos');
		playlistOverVid		=get_radio_value('playlistOverVid');
		playlistAutoHide	=get_radio_value('playlistAutoHide');
		playlistTextSize	=window.parent.document.getElementById('playlistTextSize').value;
		libCols				=window.parent.document.getElementById('libCols').value;
		libRows				=window.parent.document.getElementById('libRows').value;
		libListTextSize		=window.parent.document.getElementById('libListTextSize').value;
		libDetailsTextSize	=window.parent.document.getElementById('libDetailsTextSize').value;
		appBgColor			=window.parent.document.getElementById('appBgColor').value;
		vidBgColor			=window.parent.document.getElementById('vidBgColor').value;
		framesBgColor		=window.parent.document.getElementById('framesBgColor').value;
		ctrlsMainColor		=window.parent.document.getElementById('ctrlsMainColor').value;
		ctrlsMainHoverColor	=window.parent.document.getElementById('ctrlsMainHoverColor').value;
		slideColor			=window.parent.document.getElementById('slideColor').value;
		itemBgHoverColor	=window.parent.document.getElementById('itemBgHoverColor').value;
		itemBgSelectedColor	=window.parent.document.getElementById('itemBgSelectedColor').value;
		textColor			=window.parent.document.getElementById('textColor').value;
		textHoverColor		=window.parent.document.getElementById('textHoverColor').value;
		textSelectedColor	=window.parent.document.getElementById('textSelectedColor').value;
		framesBgAlpha		=window.parent.document.getElementById('framesBgAlpha').value;
		ctrlsMainAlpha		=window.parent.document.getElementById('ctrlsMainAlpha').value;
		str='index.php?option=com_spidervideoplayer@task=preview_settings@tmpl=component@appWidth='+appWidth
		+'@appHeight='+appHeight
		+'@playlistWidth='+playlistWidth
		+'@startWithLib='+startWithLib
		+'@show_trackid='+show_trackid
		+'@autoPlay='+autoPlay
		+'@autoNext='+appHeight
		+'@autoNextAlbum='+autoNextAlbum
		+'@defaultVol='+defaultVol
		+'@defaultRepeat='+defaultRepeat
		+'@defaultShuffle='+defaultShuffle
		+'@autohideTime='+autohideTime
		+'@centerBtnAlpha='+centerBtnAlpha
		+'@loadinAnimType='+loadinAnimType
		+'@keepAspectRatio='+keepAspectRatio
		+'@clickOnVid='+clickOnVid
		+'@spaceOnVid='+spaceOnVid
		+'@mouseWheel='+mouseWheel
		+'@ctrlsPos='+ctrlsPos
		+'@ctrlsStack=['+ctrlsStack
		+']@ctrlsOverVid='+ctrlsOverVid
		+'@ctrlsSlideOut='+ctrlsSlideOut
		+'@watermarkUrl='+watermarkUrl
		+'@watermarkPos='+watermarkPos
		+'@watermarkSize='+watermarkSize
		+'@watermarkSpacing='+watermarkSpacing
		+'@watermarkAlpha='+watermarkAlpha
		+'@playlistPos='+playlistPos
		+'@playlistOverVid='+playlistOverVid
		+'@playlistAutoHide='+playlistAutoHide
		+'@playlistTextSize='+playlistTextSize
		+'@libCols='+libCols
		+'@libRows='+libRows
		+'@libListTextSize='+libListTextSize
		+'@libDetailsTextSize='+libDetailsTextSize
		+'@appBgColor='+appBgColor
		+'@vidBgColor='+vidBgColor
		+'@framesBgColor='+framesBgColor
		+'@ctrlsMainColor='+ctrlsMainColor
		+'@ctrlsMainHoverColor='+ctrlsMainHoverColor
		+'@slideColor='+slideColor
		+'@itemBgHoverColor='+itemBgHoverColor
		+'@itemBgSelectedColor='+itemBgSelectedColor
		+'@textColor='+textColor
		+'@textHoverColor='+textHoverColor
		+'@textSelectedColor='+textSelectedColor
		+'@framesBgAlpha='+framesBgAlpha
		+'@ctrlsMainAlpha='+ctrlsMainAlpha;
		var so = new SWFObject("<?php echo JURI::root();?>components/com_spidervideoplayer/videoPlayer.swf?wdrand=<?php echo mt_rand() ?>", "Player", "100%", "100%", "8", "#000000");
		so.addParam("FlashVars", "settingsUrl=<?php echo JURI::base(true);?>/"+str+"&playlistUrl=<?php echo JURI::base(true);?>/index.php?option=com_spidervideoplayer@task=preview_playlist@show_trackid="+show_trackid+"@tmpl=component");
		so.addParam("quality", "high");
		so.addParam("menu", "false");
		so.addParam("wmode", "transparent");
		so.addParam("loop", "false");
		so.addParam("allowfullscreen", "true");
		so.write("flashcontent");
		</script>
	<?php
	}
	
	function preview_playlist(){
		$show_trackid =	JRequest::getVar('show_trackid',1);
		echo '<library>
			<album title="Nature" thumb="components/com_spidervideoplayer/upload/sunset4.jpg" id="1">
				<track id="1" type="youtube" url="http://www.youtube.com/watch?v=eaE8N6alY0Y" thumb="components/com_spidervideoplayer/upload/red-sunset-casey1.jpg" ';
				if($show_trackid)
					echo' trackId="1" ';
				echo ' >Sunset 1</track>
				<track id="2" type="youtube" url="http://www.youtube.com/watch?v=y3eFdvDdXx0" thumb="components/com_spidervideoplayer/upload/sunset10.jpg"  ';
				if($show_trackid)
					echo' trackId="2" ';
				echo ' >Sunset 2</track>
			</album>
		</library>';
		exit;
	}


	function preview_settings(&$params){
		$ctrlsStack	= str_replace("[", "", $params->get( 'ctrlsStack'));
		$ctrlsStack	= str_replace(", :", ", +:", $ctrlsStack);
		$new = "";
		if($ctrlsStack)	{
			$ctrls = explode(",", $ctrlsStack);
			foreach($ctrls as $key =>  $x)	{
				$y = explode(":", $x);
				$ctrl = $y[0];
				$active	= $y[1];
				if($active==1)
				if($new=="")
				$new = $y[0];
				else
				$new = $new.','.$y[0];
			}
		};
		echo  '<settings>
		<appWidth>'.$params->get( 'appWidth').'</appWidth>
		<appHeight>'.$params->get( 'appHeight').'</appHeight>
		<playlistWidth>'.$params->get( 'playlistWidth').'</playlistWidth>
		<startWithLib>'.change_to_str($params->get( 'startWithLib')).'</startWithLib>
		<autoPlay>'.change_to_str($params->get( 'autoPlay')).'</autoPlay>
		<autoNext>'.change_to_str($params->get( 'autoNext')).'</autoNext>
		<autoNextAlbum>'.change_to_str($params->get( 'autoNextAlbum')).'</autoNextAlbum>
		<defaultVol>'.(($params->get( 'defaultVol')+0)/100).'</defaultVol>
		<defaultRepeat>'.$params->get( 'defaultRepeat').'</defaultRepeat>
		<defaultShuffle>'.$params->get( 'defaultShuffle').'</defaultShuffle>
		<autohideTime>'.$params->get( 'autohideTime').'</autohideTime>
		<centerBtnAlpha>'.(($params->get( 'centerBtnAlpha')+0)/100).'</centerBtnAlpha>
		<loadinAnimType>'.$params->get( 'loadinAnimType').'</loadinAnimType>
		<keepAspectRatio>'.change_to_str($params->get( 'keepAspectRatio')).'</keepAspectRatio>
		<clickOnVid>'.change_to_str($params->get( 'clickOnVid')).'</clickOnVid>
		<spaceOnVid>'.change_to_str($params->get( 'spaceOnVid')).'</spaceOnVid>
		<mouseWheel>'.change_to_str($params->get( 'mouseWheel')).'</mouseWheel>
		<ctrlsPos>'.$params->get( 'ctrlsPos').'</ctrlsPos>
		<ctrlsStack>'.$new.'</ctrlsStack>
		<ctrlsOverVid>'.change_to_str($params->get( 'ctrlsOverVid')).'</ctrlsOverVid>
		<ctrlsSlideOut>'.change_to_str($params->get( 'ctrlsSlideOut')).'</ctrlsSlideOut>
		<watermarkUrl>'.$params->get( 'watermarkUrl').'</watermarkUrl>
		<watermarkPos>'.$params->get( 'watermarkPos').'</watermarkPos>
		<watermarkSize>'.$params->get( 'watermarkSize').'</watermarkSize>
		<watermarkSpacing>'.$params->get( 'watermarkSpacing').'</watermarkSpacing>
		<watermarkAlpha>'.(($params->get( 'watermarkAlpha')+0)/100).'</watermarkAlpha>
		<playlistPos>'.$params->get( 'playlistPos').'</playlistPos>
		<playlistOverVid>'.change_to_str($params->get( 'playlistOverVid')).'</playlistOverVid>
		<playlistAutoHide>'.change_to_str($params->get( 'playlistAutoHide')).'</playlistAutoHide>
		<playlistTextSize>'.$params->get( 'playlistTextSize').'</playlistTextSize>
		<libCols>'.$params->get( 'libCols').'</libCols>
		<libRows>'.$params->get( 'libRows').'</libRows>
		<libListTextSize>'.$params->get( 'libListTextSize').'</libListTextSize>
		<libDetailsTextSize>'.$params->get( 'libDetailsTextSize').'</libDetailsTextSize>
		<playBtnHint>play</playBtnHint>
		<pauseBtnHint>pause</pauseBtnHint>
		<playPauseBtnHint>toggle pause</playPauseBtnHint>
		<stopBtnHint>stop</stopBtnHint>
		<playPrevBtnHint>play previous</playPrevBtnHint>
		<playNextBtnHint>play next</playNextBtnHint>
		<volBtnHint>volume</volBtnHint>
		<repeatBtnHint>repeat</repeatBtnHint>
		<shuffleBtnHint>shuffle</shuffleBtnHint>
		<hdBtnHint>HD</hdBtnHint>
		<playlistBtnHint>open/close playlist</playlistBtnHint>
		<libOnBtnHint>open library</libOnBtnHint>
		<libOffBtnHint>close library</libOffBtnHint>
		<fullScreenBtnHint>switch full screen</fullScreenBtnHint>
		<backBtnHint>back to list</backBtnHint>
		<replayBtnHint>Replay</replayBtnHint>
		<nextBtnHint>Next</nextBtnHint>
		<appBgColor>'."0x".$params->get( 'appBgColor').'</appBgColor>
		<vidBgColor>'."0x".$params->get( 'vidBgColor').'</vidBgColor>
		<framesBgColor>'."0x".$params->get( 'framesBgColor').'</framesBgColor>
		<framesBgAlpha>'.(($params->get( 'framesBgAlpha')+0)/100).'</framesBgAlpha>
		<ctrlsMainColor>'."0x".$params->get( 'ctrlsMainColor').'</ctrlsMainColor>
		<ctrlsMainHoverColor>'."0x".$params->get( 'ctrlsMainHoverColor').'</ctrlsMainHoverColor>
		<ctrlsMainAlpha>'.(($params->get( 'ctrlsMainAlpha')+0)/100).'</ctrlsMainAlpha>
		<slideColor>'."0x".$params->get( 'slideColor').'</slideColor>
		<itemBgHoverColor>'."0x".$params->get( 'itemBgHoverColor').'</itemBgHoverColor>
		<itemBgSelectedColor>'."0x".$params->get( 'itemBgSelectedColor').'</itemBgSelectedColor>
		<itemBgAlpha>'.(($params->get( 'framesBgAlpha')+0)/100).'</itemBgAlpha>
		<textColor>'."0x".$params->get( 'textColor').'</textColor>
		<textHoverColor>'."0x".$params->get( 'textHoverColor').'</textHoverColor>
		<textSelectedColor>'."0x".$params->get( 'textSelectedColor').'</textSelectedColor>
		</settings>';
	exit;
	}
	//global end
}


function change_to_str($x) {
	if($x)
		return 'true';
	return 'false';
}
function published( &$row, $i, $task, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' ){
	$img     = $row->published ? $imgY : $imgX;
	$task     = $row->published ? 'unpublish_'.$task : 'publish_'.$task;
	$alt     = $row->published ? JText::_( 'Published' ) : JText::_( 'Unpublished' );
	$action = $row->published ? JText::_( 'Unpublish Item' ) : JText::_( 'Publish item' );
	$href = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'"><img src="templates/hathor/images/admin/'. $img .'" border="0" alt="'. $alt .'" /></a>';
	return $href;
}
function published_icon( &$row, $i, $task, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' ){
	$img     = $row->published ? $imgY : $imgX;
	$task     = $row->published ? 'unpublish_'.$task : 'publish_'.$task;
	$alt     = $row->published ? JText::_( 'Published' ) : JText::_( 'Unpublished' );
	$action = $row->published ? JText::_( 'Unpublish Item' ) : JText::_( 'Publish item' );
	$href = '<img src="templates/hathor/images/admin/'. $img .'" border="0" alt="'. $alt .'" />';
	return $href;
}
function access( &$row, $i, $archived = NULL ){
	if ( !$row->required ){
		$color_access = 'style="color: red;"';
		$task_access = 'required_tag';
		$name="NOT Required";
	}
	else{
		$color_access = 'style="color: green;"';
		$task_access = 'unrequired_tag';
		$name="Required";
	}
	if ($archived == -1) {
		$href = JText::_( $row->required );
	}
	else {
		$href = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task_access .'\')" '. $color_access .'>'. $name .'</a>';
	}
	return $href;
}?>