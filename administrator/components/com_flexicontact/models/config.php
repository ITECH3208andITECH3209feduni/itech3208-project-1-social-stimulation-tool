<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 February 2023
Copyright	: Les Arbres Design 2010-2023
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactModelConfig extends JModelLegacy
{
var $_data;
var $app = null;

function __construct()
{
	parent::__construct();
	$this->app = JFactory::getApplication();
}

//-------------------------------------------------------------------------------
// Get the component parameters
// Returns a stdClass Object containing all our parameters
// This is called from the front and the back so any language strings used must be in both language files
// Note that old menu items that have not been saved still retain their old menu item parameters
// - these OVERWRITE parameters of the same name stored in the component parameters
//
function getData()
{
    if ($this->app->isClient('administrator'))
		$component_params = JComponentHelper::getParams(LAFC_COMPONENT);		// this just gets the component parameters
	else
		$component_params =  $this->app->getParams();       // this gets the component parameters AND the menu parameters
	$this->data = $component_params->toObject();
    
// set defaults for all our parameters so that we have this all in one place

	if (!isset($this->data->logging))          $this->data->logging          = 1;      // changed from 0 at version 8.08
	if (!isset($this->data->log_keep_days))    $this->data->log_keep_days    = 1825;   // default is 5 years
	if (!isset($this->data->email_html))       $this->data->email_html       = 1;
	if (!isset($this->data->autofill))         $this->data->autofill         = 'off';
	if (!isset($this->data->autofocus))        $this->data->autofocus        = 0;
	if (!isset($this->data->top_error))        $this->data->top_error        = 0;
	if (!isset($this->data->agreement_prompt)) $this->data->agreement_prompt = '';
	if (!isset($this->data->agreement_link))   $this->data->agreement_link   = '';
	if (!isset($this->data->agreement_style))  $this->data->agreement_style   = 0;

	if (!isset($this->data->show_copy))        $this->data->show_copy        = LAFC_COPYME_ALWAYS;
	if (!isset($this->data->show_subject))     $this->data->show_subject     = 1;
	if (!isset($this->data->default_subject))  $this->data->default_subject  = '';
	if (!isset($this->data->area_prompt))      $this->data->area_prompt      = JText::_('COM_FLEXICONTACT_MESSAGE');
	if (!isset($this->data->area_opt))         $this->data->area_opt         = 'optional';
	if (!isset($this->data->area_height))      $this->data->area_height      = 4;
	if (!isset($this->data->list_opt))         $this->data->list_opt         = 'disabled';
	if (!isset($this->data->list_prompt))      $this->data->list_prompt      = '';
	if (!isset($this->data->list_list))        $this->data->list_list        = '';
    $this->explode_list();
	
	for ($i = 1; $i <= 5; $i++)
		{
		$fieldname = 'field_opt'.$i;
		$this->data->$fieldname = (isset($this->data->$fieldname))  ? $this->data->$fieldname  : 'disabled';
		$promptname = 'field_prompt'.$i;
		$this->data->$promptname = (isset($this->data->$promptname)) ? $this->data->$promptname : 'Field '.$i;
		}

	if (!isset($this->data->confirm_link))     $this->data->confirm_link     = '';
	if (!isset($this->data->confirm_text))     $this->data->confirm_text     = '<p>'.JText::_('COM_FLEXICONTACT_MESSAGE_SENT').'</p>';

	if (!isset($this->data->user_subject))
		$this->data->user_subject = JText::_('COM_FLEXICONTACT_DEFAULT_USER_SUBJECT');
	if (!isset($this->data->user_template))
		$this->data->user_template = JText::_('COM_FLEXICONTACT_DEFAULT_USER_EMAIL');

	if (!isset($this->data->admin_subject))
		$this->data->admin_subject = '%V_SUBJECT%';
	if (!isset($this->data->admin_template))
		$this->data->admin_template = JText::_('COM_FLEXICONTACT_DEFAULT_ADMIN_EMAIL');

	if (!isset($this->data->page_text))        $this->data->page_text        = '';
	if (!isset($this->data->bottom_text))      $this->data->bottom_text      = '';

	if (!isset($this->data->raw_images))       $this->data->raw_images       = 0;
	if (!isset($this->data->button_class))     $this->data->button_class     = "fc_button";

    if (empty($this->data->magic_word))        $this->data->magic_word        = '';
    if (empty($this->data->magic_word_prompt)) $this->data->magic_word_prompt = JText::_('COM_FLEXICONTACT_MAGIC_WORD');
    if (!isset($this->data->num_images))       $this->data->num_images        = 0;
    if (!isset($this->data->joomla_captcha))   $this->data->joomla_captcha    = 0;

	if (!isset($this->data->copyme_prompt))    $this->data->copyme_prompt    = ''; // 12.14
	if (!isset($this->data->error_class))      $this->data->error_class      = ''; // 12.14
	if (!isset($this->data->show_mandatory))   $this->data->show_mandatory   = 1;  // 12.15

	// new config items must be added to config.xml otherwise the values are lost when editing the config via Joomla component configuration!
    
// if we are on the front end, and 'new_captcha_config' is set, the user has saved the new back end captcha config
// - so make sure we use the back end values that he saved, not any left-over menu item parameters

    if ( ($this->app->isClient('site')) && isset($this->data->new_captcha_config) )
        {
        $component_params = JComponentHelper::getParams(LAFC_COMPONENT);
        $data = $component_params->toObject();
        $this->data->magic_word = $data->magic_word;
        $this->data->num_images = $data->num_images;
        }

    $component = JComponentHelper::getComponent('com_installer');
    $params = $component->params;
    $cache_timeout = $params->get('cachetimeout', 6, 'int');
    if ($cache_timeout == 0)
        {
        $db = JFactory::getDBO();
    	$query = $db->getQuery(true);
        $query->update($db->quoteName('#__update_sites'));
        $query->set($db->quoteName('enabled') . ' = 0');
        $query->where($db->quoteName('name').' like '.$db->quote('%FlexiContact%'));
        $db->setQuery($query);
        $db->execute();
        }
        
	return $this->data;
}

//-------------------------------------------------------------------------------
// explode the list and make the list_array and the list_count
//
function explode_list()
{
    $list_list = $this->data->list_list;
    $list_list = str_replace("\r","",$list_list);			// remove any CR's
    $list_list = str_replace("\n","",$list_list);			// remove any LF's
    $this->data->list_array = explode(",",$list_list);
    $this->data->list_count = count($this->data->list_array);
}

//-------------------------------------------------------------------------------
// Get the post data and return it as an associative array
//
function getPostData($function, $param1)
{
	$jinput = JFactory::getApplication()->input;
	$data = new stdClass();
	JForm::addFieldPath(LAFC_FORM_FIELD_PATH);
   	$form = JForm::getInstance($param1, LAFC_FORMS_PATH.'/'.$param1.'.xml');
	$field_sets = $form->getFieldsets();
	foreach ($field_sets as $fieldset_name => $fieldset)
		{
		$fields = $form->getFieldset($fieldset->name);
		foreach ($fields as $field)
			{
			$field_name = $field->name;
			$default = $form->getFieldAttribute($field_name,'default','');
			$filter = $form->getFieldAttribute($field_name,'filter','string');
			$data->$field_name = $jinput->get($field_name, $default, $filter);
			}
		}
	return $data;
}

// ------------------------------------------------------------------------------------
// Validate all the configuration entries
// Return TRUE on success or FALSE if there is any invalid data
//
function check($function, $param1)
{
	$errors = array();
    $warnings = array();
    
	switch ($param1)
		{
		case 'user_template':
			if (strstr($this->data->$param1, '%V_MESSAGE_DATA%'))
                $warnings[] = JText::_('COM_FLEXICONTACT_USER_EMAIL_NO_MESSAGE');		
			// intentionally drop through to the next case

        case 'admin_template':
			if (empty($this->data->$param1))
                $errors[] = JText::_('COM_FLEXICONTACT_CANNOT_BE_BLANK').': '.JText::_('COM_FLEXICONTACT_EMAIL_TEXT');
            if (preg_match('/from:|to:|cc:|bcc:|subject:|content-type:/i', $this->data->$param1)) 
                $errors[] = JText::_('COM_FLEXICONTACT_INVALID_BODY_TEXT');
            break;

        case 'config_fields':
            if ($this->data->list_opt == 'mandatory')
                {
                $this->explode_list();
                if ($this->data->list_count < 2)
                    $errors[] = JText::_('COM_FLEXICONTACT_LIST_CANNOT_BE_MANDATORY');
                }
			if (strlen($this->data->default_subject) > LAFC_MAX_SUBJECT_LENGTH)
				$this->data->default_subject = mb_substr($this->data->default_subject, 0, LAFC_MAX_SUBJECT_LENGTH);  // limit to size of log column
            break;
                
        case 'config_captcha':
            if ($this->data->joomla_captcha == 1)
                {
                $global_config_captcha = JFactory::getConfig()->get('captcha');
                if ($global_config_captcha == '0')
                    $warnings[] = JText::_('COM_FLEXICONTACT_JOOMLA_CAPTCHA_GLOBAL');
                else
                    {
                    if (!JPluginHelper::isEnabled('captcha', $global_config_captcha))
                        $warnings[] = JText::_('COM_FLEXICONTACT_JOOMLA_CAPTCHA_PLUGIN');
                    }
                }
            if (!self::is_posint($this->data->num_images) || ($this->data->num_images == 1))
                $errors[] = JText::_('COM_FLEXICONTACT_INVALID').' '.JText::_('COM_FLEXICONTACT_V_CAPTCHA_NUMBER');
            if ($this->data->num_images > 1)
                {
                require_once(LAFC_HELPER_PATH.'/flexi_captcha.php');
                $imageFiles = Flexi_captcha::get_image_files(LAFC_SITE_IMAGES_PATH, '.*');
                $num_image_files = count($imageFiles);
                if ($num_image_files == 0)
                    $errors[] = JText::_('COM_FLEXICONTACT_NO_IMAGES');
                elseif ($this->data->num_images > $num_image_files)
                    $errors[] = JText::sprintf('COM_FLEXICONTACT_ONLY_X_IMAGES',$num_image_files);
                }
            break;
        }
        
// if any messages were stored in the $warnings array, show them as a single notice message

	if (!empty($warnings))
		$this->app->enqueueMessage(implode('<br>',$warnings), 'notice');

// if any errors were stored in the $errors array, show them as a single error message

	if (empty($errors))
    	return true;

	$this->app->enqueueMessage(implode('<br>',$errors), 'error');
	return false;
}

//---------------------------------------------------------------
// Save component parameters
// Returns TRUE on success or FALSE if there is an error
//
function store($function, $param1)
{
	$this->getData();											// get the currently saved parameters

	$post_data = $this->getPostData($function, $param1);			// get the post data
	foreach ($post_data as $param_name => $param_value)			// and overwrite old values with any new values
		if (isset($param_value))
            $this->data->$param_name = $param_value;
		
	if (!$this->check($function, $param1))							// Validate the data
		return false;											// check() may have enqueued an error message

// save the component parameters

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    $query->update($db->quoteName('#__extensions'));
    $query->set($db->quoteName('params').' = '.$db->quote(json_encode($this->data)));
    $query->where($db->quoteName('element').' = '.$db->quote(LAFC_COMPONENT));
    $db->setQuery($query);
    $db->execute();

// clean the cache.

	$this->cleanCache('_system', 0);
	$this->cleanCache('_system', 1);
		
	return true;
}

//-------------------------------------------------------------------------------
// Return true if supplied argument is a positive integer, else false
//
static function is_posint($arg, $allow_blank=true, $min=0, $max=0)
{
	if ($arg === '')
		{
		if ($allow_blank)
			return true;
		else
			return false;
		}
	if (!is_numeric($arg))
		return false;
	if (preg_match('/[^\d]/', $arg))
		return false;
    if ($arg < $min)
        return false;
    if (($max != 0) && ($arg > $max))
        return false;
	return true;
}

//-------------------------------------------------------------------------------
// Try to find a Flexicontact front end menu item
//
function get_fc_menu_item()
{
    $query = $this->_db->getQuery(true);
    $query->select(array('title','params'));
    $query->from($this->_db->quoteName('#__menu'));
    $query->where($this->_db->quoteName('link').' LIKE '.$this->_db->quote('%com_flexicontact&view=contact%').' AND '.$this->_db->quoteName('published').' = 1');
	$data = '';
    try
		{
		$this->_db->setQuery($query);
		$data = $this->_db->loadObject();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
        FC_trace::trace($this->ladb_error_text);
		return false;
		}
	return $data;
}	

}