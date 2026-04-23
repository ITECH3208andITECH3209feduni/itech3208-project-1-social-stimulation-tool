<?php
/********************************************************************
Product     : Flexicontact
Date		: 23 February 2023
Copyright	: Les Arbres Design 2010-2023
Contact     : https://www.lesarbresdesign.info
Licence     : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewLog extends JViewLegacy
{

//-------------------------------------------------------------------------------
// Show the list of log records
//
function display($tpl = null)
{
	Flexicontact_Utility::addSubMenu('log');
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_MESSAGE_LOG'), 'lad.png');
	JToolBarHelper::deleteList('','delete_log');
	JToolBarHelper::cancel();
	
// get the order states	and filters			

	$app = JFactory::getApplication();
	$filter_order = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order', 'filter_order', 'DATETIME');
	$filter_order_Dir = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order_Dir', 'filter_order_Dir', 'DESC');
	$filter_date = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_date','filter_date',LAFC_LOG_LAST_28_DAYS,'int');
	$limit = $app->getUserStateFromRequest(LAFC_COMPONENT.'.log_limit', 'log_limit', $app->get('list_limit'), 'int');

// make the filter lists

	$date_filters = array(
		LAFC_LOG_ALL            => JText::_('COM_FLEXICONTACT_LOG_ALL'),
		LAFC_LOG_LAST_7_DAYS    => JText::_('COM_FLEXICONTACT_LOG_LAST_7_DAYS'),
		LAFC_LOG_LAST_28_DAYS   => JText::_('COM_FLEXICONTACT_LOG_LAST_28_DAYS'),
		LAFC_LOG_LAST_12_MONTHS => JText::_('COM_FLEXICONTACT_LOG_LAST_12_MONTHS') );
    
    $limits = array('10' => '10', '20' => '20', '50' => '50', '100' => '100', '200' => '200', '500' => '500');
    if (!in_array($limit, $limits))
        $limits[$limit] = $limit;
	$limit_list_html = Flexicontact_Utility::make_list('log_limit', $limit, $limits, 'ladj-change-submit');

	$lists['date_filters'] = Flexicontact_Utility::make_list('filter_date', $filter_date, $date_filters, 'ladj-change-submit');					

	$numrows = count($this->log_list);

// Show the list

	?>
	<form method="post" name="adminForm" id="adminForm"  class="lad-filterform">
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT ?>">
	<input type="hidden" name="task" value="log_list">
	<input type="hidden" name="boxchecked" value="0">
	<input type="hidden" name="view" value="log_list">
	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>">
	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>">
    <?php
	echo '<div>&nbsp;<div class="lad-filterform-left">';
	echo '</div>'; 
	echo '<div class="lad-filterform-right">'; 
    echo $lists['date_filters'];
    echo ' '.$limit_list_html;
    echo ' <button type="button" class="btn btn-primary ladj-click-submit" data-task="reset_log_search">'.JText::_('JSEARCH_RESET').'</button>';
	echo '</div></div>'; 

    ?>
	<table class="table table-striped">
	<thead><tr>
		<th style="text-align:center;width:20px"><input type="checkbox" name="toggle" value="" class="form-check-input ladj-check-all"></th>
		<th><?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_DATE_TIME', 'DATETIME', $filter_order_Dir, $filter_order);?></th>
		<th><?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_NAME', 'NAME', $filter_order_Dir, $filter_order);?></th>
		<th><?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_EMAIL', 'EMAIL', $filter_order_Dir, $filter_order);?></th>
		<th><?php echo JText::_('COM_FLEXICONTACT_ADMIN_SUBJECT');?></th>
		<th><?php echo JText::_('COM_FLEXICONTACT_MESSAGE');?></th>
		<th><?php echo JText::_('COM_FLEXICONTACT_STATUS');?></th>
	</tr></thead>
	
	<tbody>
	<?php
	for ($i=0; $i < $numrows; $i++) 
		{
		$row = $this->log_list[$i];
		$link = LAFC_COMPONENT_LINK.'&task=log_detail&id='.$row->id;
		$checked = JHTML::_('grid.id', $i, $row->id);
		$date = JHTML::link($link, $row->datetime);
		$status_main = $this->_list_status($row->status_main);
		$status_copy = $this->_list_status($row->status_copy);
		if (empty($row->admin_email_subject))
			$subject = mb_strimwidth($row->subject, 0, 60, "...");
		else
			$subject = mb_strimwidth($row->admin_email_subject, 0, 60, "...");
		echo "\n<tr>";
		echo '<td style="text-align:center">'.$checked.'</td>';
		echo '<td>'.$date.'</td>';
		echo '<td class="lad-break-word">'.$name = $row->name.'</td>';
		echo '<td class="lad-break-word">'.$row->email.'</td>';
		echo '<td class="lad-break-word">'.$subject.'</td>';
		echo '<td class="lad-break-word">'.$row->short_message.'</td>';
		echo "<td>$status_main $status_copy</td>";
		echo "</tr>";
		}
	echo "\n".'</tbody>';
	echo '<tfoot><tr><td colspan="15">'.$this->pagination->getListFooter().'</td></tr></tfoot>';
	echo '</table></form>';
	Flexicontact_Utility::viewEnd();
}

//-------------------------------------------------------------------------------
// Show a single log record
//
function edit($tpl = null)
{
	Flexicontact_Utility::addSubMenu('log');
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_MESSAGE').' '.$this->log_data->id, 'lad.png');
	JToolBarHelper::cancel('log_cancel');

?>
	<form method="post" name="adminForm" id="adminForm" >
	<input type="hidden" name="option" value="com_flexicontact">
	<input type="hidden" name="task" value="cancel">
	</form>
<?php

// The Details fieldset

	echo '<fieldset class="lad-fieldset lad-border lad-half lad-left"><legend>'.JText::_('JDETAILS').'</legend>';
	echo '<table class="fc_log_table table table-condensed table-striped table-bordered">';
	echo '<tbody>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_DATE_TIME').'</td><td>'.$this->log_data->datetime.'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_ADMIN_EMAIL').'</td><td class="lad-break-word">'.$this->log_data->admin_email.'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_ADMIN_EMAIL_FROM').'</td><td class="lad-break-word">'.$this->log_data->admin_from_email.'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_ADMIN_REPLY_TO').'</td><td class="lad-break-word">'.$this->log_data->admin_reply_to_email.'</td></tr>';
	
// for records since version 8.08, show user email choice
// records from before 8.08 have 'config_show_copy' (and 'show_copy') set to 99

	if ($this->log_data->config_show_copy != 99)
		{
		switch ($this->log_data->config_show_copy)
			{
			case LAFC_COPYME_NEVER:	
				$user_email_choice = JText::_('COM_FLEXICONTACT_NEVER'); 
				break;
			case LAFC_COPYME_CHECKBOX: 
				if ($this->log_data->show_copy == 1)
					$user_email_choice = JText::_('COM_FLEXICONTACT_CHECKBOX_CHECKED');
				else
					$user_email_choice = JText::_('COM_FLEXICONTACT_CHECKBOX_NOT_CHECKED'); 
					break;
			case LAFC_COPYME_ALWAYS: $user_email_choice = JText::_('COM_FLEXICONTACT_ALWAYS'); 
				break;
			default: $user_email_choice = '';
			}
		echo '<tr><td>'.JText::_('COM_FLEXICONTACT_USER_EMAIL_OPTION').'</td><td>'.$user_email_choice.'</td></tr>';
		}

	if ($this->log_data->status_copy == '0')
		$user_email_to = '';
	else
		$user_email_to = $this->log_data->email;
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_USER_EMAIL').'</td><td>'.$user_email_to.'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_USER_EMAIL_FROM').'</td><td>'.$this->log_data->user_from_email.'</td></tr>';

	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_IP_ADDRESS').'</td><td>'.$this->log_data->ip.'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_BROWSER').'</td><td>'.$this->log_data->browser_string.'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_STATUS_ADMIN').'</td><td>'.$this->_detail_status($this->log_data->status_main).'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_STATUS_USER').'</td><td>'.$this->_detail_status($this->log_data->status_copy).'</td></tr>';
	if ($this->log_data->agreement_check)
		echo '<tr><td>'.JText::_('COM_FLEXICONTACT_AGREEMENT_ACCEPTED').'</td><td>'.JText::_('JYES').'</td></tr>';
	else
		echo '<tr><td>'.JText::_('COM_FLEXICONTACT_AGREEMENT_ACCEPTED').'</td><td>'.JText::_('JNO').'</td></tr>';
	echo '</tbody></table>';
	echo '</fieldset>';

// The "Fields" fieldset	

	echo '<fieldset class="lad-fieldset lad-border lad-half lad-left"><legend>'.JText::_('COM_FLEXICONTACT_CONFIG_FIELDS_NAME').'</legend>';
	echo '<table class="fc_log_table table table-condensed table-striped table-bordered">';
	echo '<tbody>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_NAME').'</td><td>'.$this->log_data->name.'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_EMAIL').'</td><td>'.$this->log_data->email.'</td></tr>';
	if (empty($this->config_data->show_subject))
		$subject_status = ' <span class="icon-eye-close" style="font-size:larger"></span>';
	else
		$subject_status = '';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_SUBJECT').$subject_status.'</td><td>'.$this->log_data->subject.'</td></tr>';
	echo '<tr><td style="vertical-align:top;">'.JText::_('COM_FLEXICONTACT_MESSAGE').$this->field_name('area_prompt').'</td><td style="white-space:normal;">'.nl2br($this->log_data->message).'</td></tr>';
	if (($this->config_data->list_opt != 'disabled') || ($this->log_data->list_choice))
		echo '<tr><td>'.JText::_('COM_FLEXICONTACT_LIST').$this->field_name('list_prompt').'</td><td>'.$this->log_data->list_choice.'</td></tr>';
	if (($this->config_data->field_opt1 != 'disabled') || ($this->log_data->field1))
		echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_1').$this->field_name('field_prompt1').'</td><td>'.$this->log_data->field1.'</td></tr>';
	if (($this->config_data->field_opt2 != 'disabled') || ($this->log_data->field2))
		echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_2').$this->field_name('field_prompt2').'</td><td>'.$this->log_data->field2.'</td></tr>';
	if (($this->config_data->field_opt3 != 'disabled') || ($this->log_data->field3))
		echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_3').$this->field_name('field_prompt3').'</td><td>'.$this->log_data->field3.'</td></tr>';
	if (($this->config_data->field_opt4 != 'disabled') || ($this->log_data->field4))
		echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_4').$this->field_name('field_prompt4').'</td><td>'.$this->log_data->field4.'</td></tr>';
	if (($this->config_data->field_opt5 != 'disabled') || ($this->log_data->field5))
		echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_5').$this->field_name('field_prompt5').'</td><td>'.$this->log_data->field5.'</td></tr>';
	echo '</tbody></table>';
	echo '</fieldset>';

// The "Email to Admin" fieldset	

	if (!empty($this->log_data->admin_email_body))
		{
		echo '<fieldset class="lad-fieldset lad-border lad-greyback lad-half lad-left"><legend>'.JText::_('COM_FLEXICONTACT_EMAIL_TO_ADMIN').'</legend>';
		echo '<div class="fc-log-subject"><span class="lad-bold">'.JText::_('COM_FLEXICONTACT_SUBJECT').'</span>: '.$this->log_data->admin_email_subject.'</div>';
		echo '<div>'.$this->log_data->admin_email_body.'</div>';
		echo '</fieldset>';
		}

// The "Email to User" fieldset	

	if (!empty($this->log_data->user_email_body))
		{
		echo '<fieldset class="lad-fieldset lad-border lad-greyback lad-half lad-left"><legend>'.JText::_('COM_FLEXICONTACT_EMAIL_TO_USER').'</legend>';
		echo '<div class="fc-log-subject"><span class="lad-bold">'.JText::_('COM_FLEXICONTACT_SUBJECT').'</span>: '.$this->log_data->user_email_subject.'</div>';
		echo '<div>'.$this->log_data->user_email_body.'</div>';
		echo '</fieldset>';
		}

	Flexicontact_Utility::viewEnd();
}

//-------------------------------------------------------------------------------
// Format a field name
//
function field_name($field_label)
{
	$name = $this->config_data->$field_label;
	if (empty($name))
		return '';
	return ' ('.$name.')';
}

//-------------------------------------------------------------------------------
// Format the email sending status - long version with text
//
static function _detail_status($status)
{
FC_trace::trace("_detail_status [$status]");
	if ($status == '0')		// '0' status means no mail was sent
		return JText::_('COM_FLEXICONTACT_NOT_SENT');
	if ($status == '1')		// '1' means email was sent ok
		return '<span class="icon-publish" style="color:green"></span> '.JText::_('COM_FLEXICONTACT_SENT_OK');
	return '<span class="icon-remove" style="color:red"></span> '.$status;			// anything else was an error
}

//-------------------------------------------------------------------------------
// Format the email sending status - short version without text
//
static function _list_status($status)
{
	if ($status == '0')		// '0' status means no mail was sent
		return ' ';
	if ($status == '1')		// '1' means email was sent ok
		return '<span class="icon-publish" style="color:green"></span>';
	return '<span class="icon-remove" style="color:red"></span>';	// anything else was an error
}

}