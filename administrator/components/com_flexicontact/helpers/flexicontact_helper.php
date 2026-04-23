<?php
/********************************************************************
Product		: Flexicontact
Date		: 10 October 2022
Copyright	: Les Arbres Design 2010-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

define("LAFC_COMPONENT",         "com_flexicontact");
define("LAFC_COMPONENT_NAME",    "FlexiContact");
define("LAFC_COMPONENT_LINK",    "index.php?option=".LAFC_COMPONENT);
define("LAFC_ADMIN_ASSETS_URL",  JURI::root(true).'/administrator/components/'.LAFC_COMPONENT.'/assets/');
define("LAFC_SITE_CSS_PATH",     JPATH_SITE.'/media/com_flexicontact/css/com_flexicontact.css');
define("LAFC_SITE_CSS_URL",      JURI::root(true).'/media/com_flexicontact/css/com_flexicontact.css');
define("LAFC_SITE_IMAGES_URL",   JURI::root(true).'/media/com_flexicontact/images/');
define("LAFC_SITE_IMAGES_PATH",  JPATH_ROOT.'/media/com_flexicontact/images');
define("LAFC_HELPER_PATH",       JPATH_ROOT.'/administrator/components/com_flexicontact/helpers');
define("LAFC_FORM_FIELD_PATH",JPATH_ADMINISTRATOR.'/components/com_flexicontact/fields'); 
if (substr(JVERSION,0,1) == '3')
	{
    define("LAFC_FORMS_PATH", JPATH_ADMINISTRATOR.'/components/com_flexicontact/forms_j3');
	define("LAFC_BS_DATA_CONTENT", "data-content");
	define("LAFC_TAB_UI", "bootstrap");
	}
else
	{
    define("LAFC_FORMS_PATH", JPATH_ADMINISTRATOR.'/components/com_flexicontact/forms_j4');
	define("LAFC_BS_DATA_CONTENT", "data-bs-content");
	define("LAFC_TAB_UI", "uitab");
	}


// email merge variables

define("LAFC_T_FROM_NAME",     "%V_FROM_NAME%");
define("LAFC_T_FROM_EMAIL",    "%V_FROM_EMAIL%");
define("LAFC_T_SUBJECT",       "%V_SUBJECT%");
define("LAFC_T_MESSAGE_PROMPT","%V_MESSAGE_PROMPT%");
define("LAFC_T_MESSAGE_DATA",  "%V_MESSAGE_DATA%");
define("LAFC_T_LIST_PROMPT",   "%V_LIST_PROMPT%");
define("LAFC_T_LIST_DATA",     "%V_LIST_DATA%");
define("LAFC_T_FIELD1_PROMPT", "%V_FIELD1_PROMPT%");
define("LAFC_T_FIELD1_DATA",   "%V_FIELD1_DATA%");
define("LAFC_T_FIELD2_PROMPT", "%V_FIELD2_PROMPT%");
define("LAFC_T_FIELD2_DATA",   "%V_FIELD2_DATA%");
define("LAFC_T_FIELD3_PROMPT", "%V_FIELD3_PROMPT%");
define("LAFC_T_FIELD3_DATA",   "%V_FIELD3_DATA%");
define("LAFC_T_FIELD4_PROMPT", "%V_FIELD4_PROMPT%");
define("LAFC_T_FIELD4_DATA",   "%V_FIELD4_DATA%");
define("LAFC_T_FIELD5_PROMPT", "%V_FIELD5_PROMPT%");
define("LAFC_T_FIELD5_DATA",   "%V_FIELD5_DATA%");
define("LAFC_T_BROWSER",       "%V_BROWSER%");
define("LAFC_T_IP_ADDRESS",    "%V_IP_ADDRESS%");
define("LAFC_T_SITE_NAME",     "%V_SITE_NAME%");

// log date filters

define("LAFC_LOG_ALL", 0);					// report filters
define("LAFC_LOG_LAST_7_DAYS", 1);
define("LAFC_LOG_LAST_28_DAYS", 2);
define("LAFC_LOG_LAST_12_MONTHS", 3);

// copy me

define("LAFC_COPYME_NEVER", 0);				// never copy the user
define("LAFC_COPYME_CHECKBOX", 1);			// show the checkbox on the contact form
define("LAFC_COPYME_ALWAYS", 2);			// always copy the user

// Themes

define("THEME_ALL", 'all');
define("THEME_STANDARD", 'standard');
define("THEME_TOYS", 'toys');
define("THEME_NEON", 'neon');
define("THEME_WHITE", 'white');
define("THEME_BLACK", 'black');

// Mandatory

define("LAFC_MANDATORY_NEVER", 0);
define("LAFC_MANDATORY_ALWAYS", 1);
define("LAFC_MANDATORY_ON_ERROR", 2);

// Max data lengths to avoid "Data too long" errors in Joomla 4

define("LAFC_MAX_NAME_LENGTH", 60);
define("LAFC_MAX_EMAIL_LENGTH", 60);
define("LAFC_MAX_SUBJECT_LENGTH", 60);
define("LAFC_MAX_VARCHAR_LENGTH", 255);
define("LAFC_MAX_MESSAGE_LENGTH", 5000);

class Flexicontact_Utility
{

// -------------------------------------------------------------------------------
// Draw the top menu and make the current item active
//
static function addSubMenu($submenu = '')
{
	$component_params = JComponentHelper::getParams(LAFC_COMPONENT);
	$params = $component_params->toObject();	
	if (!empty($params->hide_submenu))
		return;
    JHtmlSidebar::addEntry(JText::_('COM_FLEXICONTACT_CONFIGURATION'), 'index.php?option='.LAFC_COMPONENT.'&task=config', $submenu == 'config');
    JHtmlSidebar::addEntry(JText::_('COM_FLEXICONTACT_CAPTCHA_IMAGES'), 'index.php?option='.LAFC_COMPONENT.'&task=images', $submenu == 'images');
    JHtmlSidebar::addEntry(JText::_('COM_FLEXICONTACT_MESSAGE_LOG'), 'index.php?option='.LAFC_COMPONENT.'&task=log_list', $submenu == 'log');
    JHtmlSidebar::addEntry(JText::_('COM_FLEXICONTACT_ABOUT'), 'index.php?option='.LAFC_COMPONENT.'&task=about', $submenu == 'about');
}
  
// -------------------------------------------------------------------------------
// Draw the component menu
// - called at the start of every view
//
static function viewStart()
{
	$entries = JHtmlSidebar::getEntries();
    if (substr(JVERSION,0,1) == '3')
        {
        if (empty($entries))
            echo '<div id="j-main-container">';
        else
            {
            $sidebar = JHtmlSidebar::render();
            echo '<div id="j-sidebar-container" class="span2">'.$sidebar.'</div>';
            echo '<div id="j-main-container" class="span10">';
            }
        }
    else        // Joomla 4
        {
        echo '<div class="row">';
        if (empty($entries))
			echo '<div class="col-md-12">';
        else
            {
            $sidebar = JHtmlSidebar::render();
            echo '<div id="j-sidebar-container" class="col-md-2">'.$sidebar.'</div>';
            echo '<div class="col-md-10">';
            echo '<div id="j-main-container" class="j-main-container">';
            }
        }
}

// -------------------------------------------------------------------------------
// Called at the end of every view that calls viewStart()
//
static function viewEnd()
{
    if (substr(JVERSION,0,1) == '3')
    	echo "</div>";                          // close "j-main-container"
    else        // Joomla 4
        {
       	echo "</div>";                          // close "j-main-container"
    	$entries = JHtmlSidebar::getEntries();
        if (!empty($entries))
        	echo "</div>";                      // close "col-md-10"
       	echo "</div>";                          // close "row"
        }
}

//-------------------------------------------------------------------------------
// Make a select list
//
static function make_list($name, $current_value, $items, $extra_class='')
{
	if (empty($items))
		return '';
	$app = JFactory::getApplication();
	if ($app->isClient('administrator'))
		$class = "form-select form-control lad-input-inline";
	else
		$class = "fc_input";
	if (!empty($extra_class))
		$class .= ' '.$extra_class;
	$html = '<select name="'.$name.'" id="'.$name.'" class="'.$class.'">';
	foreach ($items as $key => $value)
		{
		$selected = '';
		if ($current_value == $key)
			$selected = ' selected="selected"';
		$html .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
	$html .= '</select>';
	return $html;
}

// -------------------------------------------------------------------------------
// Validate an email address
// JMailHelper::isEmailAddress() accepts dotless domain names which cause an exception when sending mail 
//
static function is_email($arg, $allow_blank=true)
{
	if ($arg === '')
		{
		if ($allow_blank)
			return true;
		else
			return false;
		}
	if (strlen($arg) > LAFC_MAX_EMAIL_LENGTH)
		return false;
	if (filter_var($arg, FILTER_VALIDATE_EMAIL) === false)
		return false;
	else
		return true;
}

//---------------------------------------------------------------------------------------------------------
// Get an instance of the configured Joomla captcha plugin
//
static function get_joomla_captcha()
{
	$global_config_captcha = JFactory::getConfig()->get('captcha');
	if (empty($global_config_captcha))
		{
		FC_trace::trace("Captcha plugin enabled but no plugin selected in Joomla Global Configuration");
		return false;
		}
	if (!JPluginHelper::isEnabled('captcha', $global_config_captcha))
		{
		FC_trace::trace("Captcha plugin enabled but $global_config_captcha plugin is disabled");
		return false;
		}
	try
		{
		$captcha_plugin = JCaptcha::getInstance($global_config_captcha);
		}
	catch (\RuntimeException $e)
		{
		FC_trace::trace("Joomla $global_config_captcha captcha plugin error: ".$e->getMessage());
		return false;
		}
	if (!isset($captcha_plugin))
		{
		FC_trace::trace("$global_config_captcha captcha plugin failed to instantiate");
		return false;
		}
	return $captcha_plugin;
}

}