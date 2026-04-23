<?php
/********************************************************************
Product	    : Flexicontact
Date		: 23 February 2023
Copyright	: Les Arbres Design 2010-2023
Contact	    : https://www.lesarbresdesign.info
Licence	    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactViewConfig extends JViewLegacy
{
function display($tpl = null)
{
	Flexicontact_Utility::addSubMenu('config');
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CONFIGURATION'), 'lad.png');
	JToolBarHelper::custom('email_test', 'mail.png', '', 'COM_FLEXICONTACT_TEST_EMAIL', false);
	if (JFactory::getUser()->authorise('core.admin', 'com_flexicontact'))
		JToolBarHelper::preferences('com_flexicontact');

// Set up the configuration links

	$config_table = array(
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&function=edit_form&param1=config_general',
			'icon' => 'config_general.png',
			'name' => 'COM_FLEXICONTACT_CONFIG_GENERAL_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_GENERAL_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&function=edit_form&param1=admin_template&vars=yes',
			'icon' => 'config_email_a.png',
			'name' => 'COM_FLEXICONTACT_EMAIL_TO_ADMIN',
			'desc' => 'COM_FLEXICONTACT_CONFIG_ADMIN_EMAIL_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&function=edit_form&param1=user_template&vars=yes',
			'icon' => 'config_email_u.png',
			'name' => 'COM_FLEXICONTACT_EMAIL_TO_USER',
			'desc' => 'COM_FLEXICONTACT_CONFIG_USER_EMAIL_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&function=edit_form&param1=confirm_text&vars=yes',
			'icon' => 'config_confirm.png',
			'name' => 'COM_FLEXICONTACT_CONFIG_CONFIRM_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_CONFIRM_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&function=config_fields&param1=config_fields',
			'icon' => 'config_fields.png',
			'name' => 'COM_FLEXICONTACT_CONFIG_FIELDS_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_FIELDS_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&function=edit_form&param1=page_text',
			'icon' => 'config_text_top.png',
			'name' => 'COM_FLEXICONTACT_V_TOP_TEXT',
			'desc' => 'COM_FLEXICONTACT_CONFIG_TOP_BOTTOM_TEXT_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&function=edit_form&param1=bottom_text',
			'icon' => 'config_text_bottom.png',
			'name' => 'COM_FLEXICONTACT_V_BOTTOM_TEXT',
			'desc' => 'COM_FLEXICONTACT_CONFIG_TOP_BOTTOM_TEXT_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&function=edit_form&param1=config_captcha',
			'icon' => 'config_captcha.png',
			'name' => 'COM_FLEXICONTACT_CAPTCHA_CONFIG',
			'desc' => 'COM_FLEXICONTACT_CAPTCHA_CONFIG_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&function=edit_css',
			'icon' => 'config_css.png',
			'name' => 'COM_FLEXICONTACT_CONFIG_CSS_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_CSS_DESC')
		);

    $this->form();

	echo '<table class="table table-striped"><thead><tr>';
	echo '<th></th>';
	echo '<th>'.JText::_('COM_FLEXICONTACT_CONFIG_NAME').'</th>';
	echo '<th>'.JText::_('COM_FLEXICONTACT_CONFIG_DESC').'</th>';
	echo '</tr></thead>';

	foreach ($config_table as $config)
		{
		$icon = '<img src="'.LAFC_ADMIN_ASSETS_URL.$config['icon'].'" alt="">';
		echo "<tr>
				<td>$icon</td>
				<td>".JHTML::link($config['link'], JText::_($config['name']))."</td>
				<td>".JText::_($config['desc'])."</td>
			</tr>";
		}
	echo '</table></form>';
	Flexicontact_Utility::viewEnd();
}

//-------------------------------------------------------------------------------
// Handle form-based config pages
//
function edit_form()
{
	Flexicontact_Utility::addSubMenu('config');
	Flexicontact_Utility::viewStart();
   	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_(self::get_title($this->param1)), 'lad.png');
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::cancel();
    $this->form();
    
    if ($this->vars)
        {
        echo JHtml::_(LAFC_TAB_UI.'.startTabSet','config_tabs', array('active' => 'main'));
        echo JHtml::_(LAFC_TAB_UI.'.addTab', 'config_tabs', 'main', JText::_('COM_FLEXICONTACT_TEXT'));
        }

	JForm::addFieldPath(LAFC_FORM_FIELD_PATH);
	$form = JForm::getInstance($this->param1, LAFC_FORMS_PATH.'/'.$this->param1.'.xml');
	$field_sets = $form->getFieldsets();
    
	foreach ($field_sets as $fieldset_name => $fieldset)
        {
        if (isset($fieldset->class))
            echo '<fieldset class="'.$fieldset->class.'">';
        else
            echo '<fieldset>';
        if (!empty($fieldset->label))
            echo '<legend>'.JText::_($fieldset->label).'</legend>';
            
        $form->bind($this->config_data);
		echo $this->renderFieldset($form, $fieldset_name);
        echo '</fieldset>';
        }
        
    if ($this->vars)
        {
        echo JHtml::_(LAFC_TAB_UI.'.endTab');
        echo JHtml::_(LAFC_TAB_UI.'.addTab', 'config_tabs', 'variables', JText::_('COM_FLEXICONTACT_VARIABLES'));
        echo '<h5>'.JText::_('COM_FLEXICONTACT_CLICK_TO_COPY').'</h5>';
    	echo $this->make_key_table();
        echo JHtml::_(LAFC_TAB_UI.'.endTab');
        echo JHtml::_(LAFC_TAB_UI.'.endTabSet');
        }        

	echo '</form>';
	Flexicontact_Utility::viewEnd();
}

//-------------------------------------------------------------------------------
// Render a fieldset with a couple of extra features
//
function renderFieldset($form, $fieldset_name)
{
    $html = '';
    $fields = $form->getFieldset($fieldset_name);
    foreach ($fields as $field)
        {
        if ($field->getAttribute('type','') == 'editor')
            {
            $html .= $form->getLabel($field->name); 
            $html .= $form->getInput($field->name);
            continue;
            }
        if ($field->getAttribute('label','') == '_')
            {
            $html .= $form->getInput($field->name);
            continue;
            }
        $html .= $field->renderField();
        }
    return $html;
}

//-------------------------------------------------------------------------------
// Field config page has a special layout
//
function config_fields()
{
	Flexicontact_Utility::addSubMenu('config');
	Flexicontact_Utility::viewStart();
   	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CONFIG_FIELDS_NAME'), 'lad.png');
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::cancel();
    $this->form();
    
	JForm::addFieldPath(LAFC_FORM_FIELD_PATH);
	$form = JForm::getInstance($this->param1, LAFC_FORMS_PATH.'/'.$this->param1.'.xml');
    $form->bind($this->config_data);

    echo '<fieldset class="lad-fieldset width-auto lad-border">';
	echo $form->renderFieldset('main');
	echo '</fieldset>';

    echo '<fieldset class="lad-fieldset width-auto lad-border">';
	echo '<table class="fc_field_table"><thead>';
	echo '<tr><th colspan="2" style="text-align:left;text-decoration:underline">'.JText::_('COM_FLEXICONTACT_V_TEXT_FIELD_DESC').'</th><th>'.JText::_('COM_FLEXICONTACT_V_PROMPT').'</th></tr>';
	echo '</thead><tbody>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_1').'</td><td>'.$form->getInput('field_opt1').'</td><td>'.$form->getInput('field_prompt1').'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_2').'</td><td>'.$form->getInput('field_opt2').'</td><td>'.$form->getInput('field_prompt2').'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_3').'</td><td>'.$form->getInput('field_opt3').'</td><td>'.$form->getInput('field_prompt3').'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_4').'</td><td>'.$form->getInput('field_opt4').'</td><td>'.$form->getInput('field_prompt4').'</td></tr>';
	echo '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_5').'</td><td>'.$form->getInput('field_opt5').'</td><td>'.$form->getInput('field_prompt5').'</td></tr>';
	echo '</tbody></table>';
	echo '</fieldset>';
        
	echo '</form>';
	Flexicontact_Utility::viewEnd();
}

//-------------------------------------------------------------------------------
// Create the Variables Key Table
//
function make_key_table()
{
	JHtml::_('bootstrap.tooltip', 'span.hasTooltip', ['trigger' => 'hover focus']);
	JText::script('COM_FLEXICONTACT_COPY');	
	JText::script('COM_FLEXICONTACT_COPIED');	    
    $span_start = '<span class="hasTooltip lad-key ladj-copy_to_clipboard" title="'.JText::_('COM_FLEXICONTACT_COPY').'">';
	$html = '<table class="fc_key_table table table-striped table-bordered width-auto">';
	$html .= '<thead>';
    $html .= '<tr><th>'.JText::_('COM_FLEXICONTACT_FIELD').'</th><th>'.JText::_('COM_FLEXICONTACT_STATUS').'</th><th>'.JText::_('COM_FLEXICONTACT_V_PROMPT').'</th><th>'.JText::_('COM_FLEXICONTACT_FIELD_VALUE').'</th></tr>';
	$html .= '</thead><tbody>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_NAME').'</td><td>'.$this->field_status().'</td><td></td><td class="lad-key-cell">'.$span_start.LAFC_T_FROM_NAME.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_EMAIL').'</td><td>'.$this->field_status().'</td><td></td><td class="lad-key-cell">'.$span_start.LAFC_T_FROM_EMAIL.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_MESSAGE').$this->field_name('area_prompt').'</td><td>'.$this->field_status('area_opt').'</td><td class="lad-key-cell">'.$span_start.LAFC_T_MESSAGE_PROMPT.'</span></td><td class="lad-key-cell">'.$span_start.LAFC_T_MESSAGE_DATA.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_SUBJECT').'</td><td>'.$this->field_status('show_subject').'</td><td></td><td class="lad-key-cell">'.$span_start.LAFC_T_SUBJECT.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_LIST').$this->field_name('list_prompt').'</td><td>'.$this->field_status('list_opt').'</td><td class="lad-key-cell">'.$span_start.LAFC_T_LIST_PROMPT.'</span></td><td class="lad-key-cell">'.$span_start.LAFC_T_LIST_DATA.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_1').$this->field_name('field_prompt1').'</td><td>'.$this->field_status('field_opt1').'</td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD1_PROMPT.'</span></td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD1_DATA.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_2').$this->field_name('field_prompt2').'</td><td>'.$this->field_status('field_opt2').'</td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD2_PROMPT.'</span></td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD2_DATA.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_3').$this->field_name('field_prompt3').'</td><td>'.$this->field_status('field_opt3').'</td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD3_PROMPT.'</span></td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD3_DATA.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_4').$this->field_name('field_prompt4').'</td><td>'.$this->field_status('field_opt4').'</td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD4_PROMPT.'</span></td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD4_DATA.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_FIELD_5').$this->field_name('field_prompt5').'</td><td>'.$this->field_status('field_opt5').'</td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD5_PROMPT.'</span></td><td class="lad-key-cell">'.$span_start.LAFC_T_FIELD5_DATA.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_BROWSER').'</td><td></td><td></td><td class="lad-key-cell">'.$span_start.LAFC_T_BROWSER.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_IP_ADDRESS').'</td><td></td><td></td><td class="lad-key-cell">'.$span_start.LAFC_T_IP_ADDRESS.'</span></td></tr>';
    $html .= '<tr><td>'.JText::_('COM_FLEXICONTACT_SITE_NAME').'</td><td></td><td></td><td class="lad-key-cell">'.$span_start.LAFC_T_SITE_NAME.'</span></td></tr>';
 	$html .= '</tbody></table>';
    return $html;
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
// Get the translated configuration option for a field
//
function field_status($option = '')
{
	if (empty($option))
		$value = 'mandatory';
	else
		$value = $this->config_data->$option;
	switch ($value)
		{
		case '0': 		// subject field only
			return '<span class="icon-eye-close"></span> '.JText::_('COM_FLEXICONTACT_HIDDEN');
		case 'disabled': 
			return '<span class="icon-delete" style="color:gray"></span> '.JText::_('COM_FLEXICONTACT_V_DISABLED');
		case '1': 		// subject field only
		case 'mandatory': 
			return '<span class="icon-checkmark" style="color:green"></span> '.JText::_('COM_FLEXICONTACT_V_MANDATORY');
		case 'optional':  
			return '<span class="icon-checkmark" style="color:orange"></span> '.JText::_('COM_FLEXICONTACT_V_OPTIONAL');
		default: return '';
		}
}

//-------------------------------------------------------------------------------
// Edit the front end CSS file
//
function edit_css()
{
	Flexicontact_Utility::addSubMenu('config');
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CONFIG_CSS_NAME'), 'lad.png');
	JToolBarHelper::apply('apply_css');
	JToolBarHelper::save('save_css');
	JToolBarHelper::cancel();
	
    $error = '';
	if (!is_writable(LAFC_SITE_CSS_PATH)) 
		$error = JText::_('COM_FLEXICONTACT_CSS_NOT_WRITEABLE'); 

	if (!is_readable(LAFC_SITE_CSS_PATH)) 
		$error = JText::_('COM_FLEXICONTACT_CSS_NOT_READABLE'); 

	if (!file_exists(LAFC_SITE_CSS_PATH)) 
		$error = JText::_('COM_FLEXICONTACT_CSS_MISSING');
		
    if ($error != '')
        {
		JFactory::getApplication()->redirect(LAFC_COMPONENT_LINK.'&task=config', $error.'<br>('.LAFC_SITE_CSS_PATH.')', 'error'); 
        return;
        }
		
	$editor = JEditor::getInstance('codemirror');
	Flexicontact_Utility::viewStart();
    $this->form();
	$css_contents = @file_get_contents(LAFC_SITE_CSS_PATH);
	$default_css_contents = false;
	if (file_exists(JPATH_SITE.'/media/com_flexicontact/css/default.css')) 
		$default_css_contents = @file_get_contents(JPATH_SITE.'/media/com_flexicontact/css/default.css');

	if ($default_css_contents)
		{
	    echo JHtml::_(LAFC_TAB_UI.'.startTabSet','css_tabs', array('active' => 'tab1'));
	    echo JHtml::_(LAFC_TAB_UI.'.addTab', 'css_tabs', 'tab1', 'com_flexicontact.css');
		}

    echo '<div>';
	echo $editor->display('css_contents', $css_contents,'','','','',false,'','','',array('syntax' => 'css'));
    echo '</div>';

	if ($default_css_contents)
		{
	    echo JHtml::_(LAFC_TAB_UI.'.endTab');
	    echo JHtml::_(LAFC_TAB_UI.'.addTab', 'css_tabs', 'tab2', 'default.css');
		echo '<pre>'.$default_css_contents.'</pre>';
	    echo JHtml::_(LAFC_TAB_UI.'.endTab');
	    echo JHtml::_(LAFC_TAB_UI.'.endTabSet');
		}

	echo '</form>';
	Flexicontact_Utility::viewEnd();
}

//-------------------------------------------------------------------------------
// Output the form common to all config pages
//
function form()
{
	echo '<form method="post" name="adminForm" id="adminForm" class="form-horizontal">';
	echo '<input type="hidden" name="option" value="com_flexicontact">';
	echo '<input type="hidden" name="task" value="">';
	echo '<input type="hidden" name="function" value="'.$this->function.'">';
	echo '<input type="hidden" name="param1" value="'.$this->param1.'">';
	echo '<input type="hidden" name="vars" value="'.$this->vars.'">';
}

//-------------------------------------------------------------------------------
// Map config functions to language strings for the page title
//
static function get_title($function)
{
    switch ($function)
        {
        case 'config_general':   return 'COM_FLEXICONTACT_CONFIG_GENERAL_NAME';
        case 'config_captcha':   return 'COM_FLEXICONTACT_CAPTCHA_CONFIG';
        case 'config_fields':    return 'COM_FLEXICONTACT_CONFIG_FIELDS_NAME';
        case 'user_template':    return 'COM_FLEXICONTACT_EMAIL_TO_USER';
        case 'admin_template':   return 'COM_FLEXICONTACT_EMAIL_TO_ADMIN';
        case 'confirm_text':     return 'COM_FLEXICONTACT_CONFIG_CONFIRM_NAME';
        case 'page_text':        return 'COM_FLEXICONTACT_V_TOP_TEXT';
        case 'bottom_text':      return 'COM_FLEXICONTACT_V_BOTTOM_TEXT';
        return $function;
        }
}

}