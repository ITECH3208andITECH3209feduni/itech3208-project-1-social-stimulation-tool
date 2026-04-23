<?php
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
class JElementctrlsStack extends JElement
{
function fetchElement($name, $value, &$node, $control_name)
{
	
$ctrls = explode(",", $value);
$n = count($ctrls);
$document =& JFactory::getDocument();
$cmpnt_js_path = JURI::root(true).'/administrator/components/com_spidervideoplayer/elements';
$document->addScript($cmpnt_js_path.'/jquery.js');

$path=JURI::root(true)."/administrator/components/com_spidervideoplayer/elements/images/";

        ob_start();
        static $embedded;
                if(!$embedded)
        {

            $embedded=true;

        }
            ?>
<script type="text/javascript">

function nextSibling(start) {
  var nextSib;
  if (!(nextSib=start.nextSibling)) {
    return false;
  }
  while (nextSib.nodeType!=1) {
    if (!(nextSib=nextSib.nextSibling)) {
      return false;
    }
  }
  return nextSib;
}

function previousSibling(start) {
  var previousSib;
  if (!(previousSib=start.previousSibling)) {
    return false;
  }
  while (previousSib.nodeType!=1) {
    if (!(previousSib=previousSib.previousSibling)) {
      return false;
    }
  }
  return previousSib;
}

$.noConflict();
jQuery(document).ready(function($) 
{	
///////////////////////////////////////////////////////////////////////////////////////	/////////////////////////////			  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				  
/////////////////////////////	///////////////////////////////////////////////////////////////////////////////////////			  
$(document).ready(function(){
	
	  
for (var i = 0; i < <?php echo $n ?>; i++)
{
		
	$("#arr_" + i).bind('mouseenter',{i:i},function(event){
		i=event.data.i;
	    $("#panel_arr_" + i).fadeIn();
	  });
	$("#td_arr_" + i).bind('mouseleave',{i:i},function(event){
		i=event.data.i;
	    $("#panel_arr_" + i).fadeOut(0);
	  });

	$("#go_x_" + i).bind('click',{i:i},function(event){
		i=event.data.i;
		image=document.getElementById("arr_" + i).getAttribute('image');
		if(this.checked)
		{
			document.getElementById("arr_" + i).src="<?php echo $path ;?>"+image+'_1.png';
			document.getElementById("td_arr_" + i).setAttribute('active','1');
		}
		else
		{
			document.getElementById("arr_" + i).src="<?php echo $path ;?>"+image+'_0.png';
			document.getElementById("td_arr_" + i).setAttribute('active','0');
		}
		refresh_ctrl();
	});
	
	$("#go_l_" + i).bind('click',{i:i},function(event){
		i=event.data.i;
		current=document.getElementById("td_arr_" + i)
		parent=current.parentNode;
		previous=previousSibling(current);
	//	parent.removeChild(current);
	if(previous)
		parent.insertBefore(current, previous);
		refresh_ctrl();

	});

	$("#go_r_" + i).bind('click',{i:i},function(event){
		i=event.data.i;
		current=document.getElementById("td_arr_" + i)
		parent=current.parentNode;
		next=nextSibling(current);
		//parent.removeChild(next);
	if(next)
		parent.insertBefore(next,current);
		refresh_ctrl();

	});

}	  

refresh_ctrl();
function refresh_ctrl()
{
	ctrlStack="";
	w=document.getElementById('tr_arr').childNodes;
	for(i=0; i<<?php echo $n ?>; i++)
		if(w[i].nodeType!=3)
			{
				if(ctrlStack=="")
				ctrlStack=w[i].getAttribute('value')+':'+w[i].getAttribute('active'); ///???
				else
				ctrlStack=ctrlStack+","+w[i].getAttribute('value')+':'+w[i].getAttribute('active'); ///???
			}
	document.getElementById('<?php echo  $control_name.$name ?>').value=ctrlStack;
}

});
///////////////////////////////////////////////////////////////////////////////////////	/////////////////////////////			  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				  
/////////////////////////////	///////////////////////////////////////////////////////////////////////////////////////			

  
});
</script>
<style>
<?php
for($i=0; $i < $n-1; $i++)
		echo "#panel_arr_".$i.", ";
echo "#panel_arr_".($n-1);
?>

{
display:none;
cursor:pointer;
}
</style>
<table border="0" bgcolor="#666666" cellpadding="0" cellspacing="0" width="100%">
                <?php 
				echo '<tr height="45" valign="top" id="tr_arr" valign="middle">';
				foreach($ctrls as $key =>  $x) 
				 {
						$y = explode(":", $x);
						$ctrl	=$y[0];
						$active	=$y[1];
						if($ctrl=="+")
						{
							echo '<td id="td_arr_'.$key.'"  active="'.$active.'" value="'.$ctrl.'" align="center" bgcolor="#FFFFFF">
										<img src="'.$path.$ctrl.'_'.$active.'.png" id="arr_'.$key.'" height="20" width="200" image="'.$ctrl.'"/>
											<div id="panel_arr_'.$key.'">
													<span id="go_l_'.$key.'"><img src="'.$path.'l_arrow.png" width="10" /></span> 
													<span><input type="checkbox"';
													if($active==1)
														echo ' checked="checked"';
														
													echo ' id="go_x_'.$key.'" /></span> ';
													echo '<span id="go_r_'.$key.'"><img src="'.$path.'r_arrow.png" width="10" /></span>
											</div>
									</td>';

						}
						else
						{
							echo '<td id="td_arr_'.$key.'"  active="'.$active.'" value="'.$ctrl.'" width="40" align="center" style="padding:3px 0px 0px 0px">
										<img src="'.$path.$ctrl.'_'.$active.'.png" id="arr_'.$key.'" width="20" height="20" image="'.$ctrl.'"/>
											<div id="panel_arr_'.$key.'">
													<span id="go_l_'.$key.'"><img src="'.$path.'l_arrow.png" width="10" /></span> 
													<span><input type="checkbox"';
													if($active==1)
														echo ' checked="checked"';
														
													echo ' id="go_x_'.$key.'" style="margin:0; padding:0" /></span> ';
													echo '<span id="go_r_'.$key.'"><img src="'.$path.'r_arrow.png" width="10" /></span>
											</div>
									</td>';
					 	}
				}
				 echo '</tr>';
				 ?>

               
      
</table>           
<input type="hidden" name="<?php echo $control_name."[".$name."]";?>" id="<?php echo  $control_name.$name ?>" value="<?php echo $value; ?>">

        <?php

        $content=ob_get_contents();

        ob_end_clean();
        return $content;

    }
	}
	
	?>