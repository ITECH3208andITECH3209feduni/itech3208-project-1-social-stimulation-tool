<?php
//administrator/components/com_cbjuice2/views/default/tmpl/default.php

defined('_JEXEC') or die('Restricted access');
global $cbj2_initial_mode;
global $_CB_Admin_Done, $_CB_adminpath, $ueConfig, $_CB_framework, $_CB_database, $mainframe;

if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
	$_CB_adminpath		=	JPATH_ADMINISTRATOR . '/components/com_comprofiler';
	include_once $_CB_adminpath . '/plugin.foundation.php';
	include_once $_CB_adminpath . '/ue_config.php';
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';

} else {
	$_CB_adminpath		=	$mainframe->getCfg( 'absolute_path' ). '/administrator/components/com_comprofiler';
	include_once $_CB_adminpath . '/plugin.foundation.php';
	include_once $_CB_adminpath . '/ue_config.php';
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';
}
//echo "Process users called<br>";
cbimport( 'cb.database' );
cbimport( 'cb.html' );
$versionstuff=new JVersion;
$thisversion=$versionstuff->getShortVersion();
JToolBarHelper::title(JText::_('CBJUICE2'), 'generic.png');
//JSubMenuHelper::addEntry(JText::_('process'), 'index.php?option=com_cbjuice2');
//JToolBarHelper::addNew('add','Process the Users');
//JToolBarHelper::custom('process','apply','',JText::_('CBJ_TAB1'),false); 
JToolBarHelper::preferences('com_cbjuice2','500','750');
if((strcasecmp( substr($thisversion,0,3), '1.6' ) >= 0)){
	JSubMenuHelper::addEntry(JText::_('CBJ_TAB1'),'index.php?option=com_cbjuice2&task=&view=default');
	JSubMenuHelper::addEntry(JText::_('CBJ_TAB2'),'index.php?option=com_cbjuice2&task=save&view=default');
	JSubMenuHelper::addEntry(JText::_('CBJ_TAB3'),'index.php?option=com_cbjuice2&task=about&view=about');
}
$params=& JComponentHelper::getParams('com_cbjuice2');
$delimiter=$params->get('csv_delimiter');
$email_subject=$params->get('mail_subject');
$email_sender=$params->get('mail_sender',$ueConfig['reg_email_from']);
$email_body=$params->get('mail_body');
$excluded_fields=$params->get('save_exclude_list');
if($email_body=="CBJ_DEFAULTMESSAGE" or $email_body==""){
$email_body=JText::_('CBJ_DEFAULTMESSAGE');
}
$email_from=	$ueConfig['reg_email_from'];
$name_from =	stripslashes( $ueConfig['reg_email_name'] );
/* get field list for display purposes
 * 
 */
$never_save=array("id","cid");
$full_field_names=$_CB_database->getTableFields(array('#__users','#__comprofiler'),TRUE);
$users_fields=$full_field_names['#__users'];
$comprofiler_fields=$full_field_names['#__comprofiler'];
$users_keys=array_keys($users_fields);
$comprofiler_keys=array_keys($comprofiler_fields);
$users_fields_edited=array_diff($users_keys, $never_save);
$comprofiler_fields_edited=array_diff($comprofiler_keys,$never_save);
$available_fields=array_merge($users_fields_edited,$comprofiler_fields_edited);
$field_list=implode(($delimiter." "),$available_fields);
if(strlen($delimiter)<1){
	echo "<b><H1>".JText::_('CBJ_WARNING1'). "</H1></b><br>",
	"<b><h2>".JTEXT::_('CBJ_WARNING2')."</h2></b><br><br><br>";
}
echo "<h2>".Jtext::_('CBJ_STRING1A')."</h2>";
echo "<h4>".Jtext::_('CBJ_STRING1B')."</h4>";
echo "<h4>".Jtext::_('CBJ_STRING1C')."</h4>";
echo "<p>".Jtext::_('CBJ_STRING2A')." ";
echo "".Jtext::_('CBJ_STRING2B')." ";
echo "".JText::_('CBJ_STRING2C')." ";
echo "".JText::_('CBJ_STRING2D')." ";
echo "".JText::_('CBJ_COMPOUNDHOWTO')."</p>";
echo "<h3>".JText::_('CBJ_STRING3')."</h3>";
echo "<h3>".JText::_('CBJ_AVAILABLE_FIELDS').": </h3>".$field_list."<br><br>";
?>


<form action="index.php?option=com_cbjuice2" method="post"
	enctype="multipart/form-data" name="adminForm" id="cbj2-form"
	class="form-validate">

<table border="0">
	<tr>
		<td valign="bottom" align="right" width="30%"></td>
		<td><input
				title='Process the CSV'
				type="button" name="process" value="Process the CSV"
				onclick="document.adminForm.submit();" />		</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_DELIMITER')?>:</td>
		<td><input class="inputbox" type="text" name="delimiter"
			value=<?php echo '"'.$delimiter.'"';?> size="3" maxlength="3" /><?php  echo " ".JText::_('CBJ_TAB')?>
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_MODE')?></td>
		<td><input class="inputbox" type="radio" name="opptype" value="1" />
		<?php  echo JText::_('CBJ_ADD')?><br />
		<input class="inputbox" type="radio" name="opptype" value="2"
			checked="checked" /><?php  echo JText::_('CBJ_EDIT')?><br />
		<input class="inputbox" type="radio" name="opptype" value="3" /> <b>
		<?php  echo JText::_('CBJ_SAVE')?></b><br />
		<input class="inputbox" type="radio" name="opptype" value="4" /> <b>
		<?php  echo JText::_('CBJ_DELETE')?></b><br />
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_KEYON')?></td>
		<td><input class="inputbox" type="radio" name="keytype"
			checked="checked" value="1" /> <b><?php  echo JText::_('CBJ_KEYUSER')?></b>
			<input class="inputbox" type="radio" name="keytype"
			value="2" /> <b><?php  echo JText::_('CBJ_KEYEMAIL')?></b> <br />
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_DISPLAYDEFAULT')?></td>
		<td><input class="inputbox" type="checkbox" name="defaultname"
			value="1" checked="checked" /> <?php  echo JText::_('CBJ_NAMEDEFAULTMESSAGE')?></td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_MARKCONFIRMED')?></td>
		<td><input class="inputbox" type="radio" name="confirmed" value="1" checked="checked"/>
		<?php  echo JText::_('JYES')?><input class="inputbox" type="radio" name="confirmed" value="0"/>
		<?php  echo JText::_('JNO')?><?php  echo " ".JText::_('CBJ_CONFIRMEDTEXT')?></td>
  </tr>
  <tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_AUDIT1')?></td>
		<td><input class="inputbox" type="checkbox" name="auditlist" value="1"
			checked="checked" /><?php  echo JText::_('CBJ_AUDIT2')?>.</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_HASHED1')?></td>
		<td><input class="inputbox" type="checkbox" name="hashedpwinput"
			value="1" /> <b><?php  echo " ".JText::_('CBJ_HASHED2')?></b>.</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_DEFDOMAIN1')?></td>
		<td><input class="inputbox" type="text" name="defaultemaildomain"
			value="invalid.com" ></input><?php  echo " ".JText::_('CBJ_DEFDOMAIN2')?></td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_FILEIMPORT')?></td>
		<td><input class="inputbox" type="file" name="csvusers" value=""
			size="40" maxlength="250"></input></td>
	</tr>
	<tr>
	<td valign="top" align="right" width="30%"><?php echo JText::_('CBJ_EXCLUDED_FIELDS')?></td>
	<td><textarea cols="50" rows="5" name="cbjuice_excluded_fields" value="user_id"
			class="inputbox"><?php   echo $excluded_fields?>
                    </textarea></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr>
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="left" colspan="2"><br />
		<h3><?php  echo JText::_('CBJ_CONFIRMEMAIL1')?></h3>
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"></td>
		<td><input class="inputbox" type="checkbox" name="ConfirmationEmail"
			value="1"></input> <?php  echo JText::_('CBJ_CONFIRMEMAIL2')?></td>
	</tr>

	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_CONFIRMSUBJEMAIL')?></td>
		<td><input class="inputbox" type="text"
			name="cbjuice_ConfirmationSubject" size="45"
			value=<?php echo '"'.$email_subject.'"';?>></input></td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_CONFIRMSENDER')?></td>
		<td><input class="inputbox" type="text"
			name="cbjuice_ConfirmationSender" size="45"
			value=<?php echo '"'.$email_sender.'"';?>></input></td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_BCC1')?></td>
		<td><input class="inputbox" type="checkbox" name="BccEmail" value="1"></input>
		<?php  echo JText::_('CBJ_BCC2')?></td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('CBJ_DEFTEXT1')?></td>
		<td><textarea cols="80" rows="15" name="cbjuice_message"
			class="inputbox"><?php   echo $email_body;?>
                    </textarea></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr>
		</td>
	</tr>
</table>
<input type="hidden" name="boxchecked" value="0" /> <input type="hidden"
	name="task" value="process" /> <input type="hidden" name="boxchecked"
	value="0" /> <input type="hidden" name="hidemainmenu" value="0" /></form>
