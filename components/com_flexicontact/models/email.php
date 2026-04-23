<?php
/********************************************************************
Product		: Flexicontact
Date		: 11 October 2022
Copyright	: Les Arbres Design 2010-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactModelEmail extends JModelLegacy
{

//--------------------------------------------------------------------------------
// Initialise the email model data
//
function init_data($config_data)
{
	$this->data = new stdclass();
    
	switch ($config_data->autofill)
		{
		case 'off':
			$this->data->name = '';
			$this->data->email = '';
			break;
		case 'username':
			$user = JFactory::getUser();
			$this->data->name = $user->username;
			$this->data->email = $user->email;
			break;
		case 'name':
			$user = JFactory::getUser();
			$this->data->name = $user->name;
			$this->data->email = $user->email;
			break;
		}
		
	$this->data->subject = $config_data->default_subject;
	$this->data->show_copy = '0';
	$this->data->agreement_check = '0';
	$this->data->_list1 = '';
	$this->data->field1 = '';
	$this->data->field2 = '';
	$this->data->field3 = '';
	$this->data->field4 = '';
	$this->data->field5 = '';
	$this->data->message = '';
	$this->data->_magic_word = '';
	$this->data->_pic_selected = '';
    return $this->data;
}

//--------------------------------------------------------------------------------
// Get post data
//
function getPostData($config_data)
{
    $this->init_data($config_data);
	$jinput = JFactory::getApplication()->input;
	$this->data->name = $jinput->get('name', $this->data->name, 'STRING');
	$this->data->email = $jinput->get('email', $this->data->email, 'STRING');
	$this->data->subject = $jinput->get('subject', $config_data->default_subject, 'STRING');
	if ($config_data->show_copy == LAFC_COPYME_CHECKBOX)
		$this->data->show_copy = $jinput->get('copy_me', '0', 'INT');					// checkbox
	else
		$this->data->show_copy = '0';
	$this->data->agreement_check = $jinput->get('agreement_check', '0', 'INT');		// checkbox
	$this->data->_list1 = $jinput->get('list1', '', 'STRING');
	$this->data->field1 = $jinput->get('field1', '', 'STRING');
	$this->data->field2 = $jinput->get('field2', '', 'STRING');
	$this->data->field3 = $jinput->get('field3', '', 'STRING');
	$this->data->field4 = $jinput->get('field4', '', 'STRING');
	$this->data->field5 = $jinput->get('field5', '', 'STRING');
	$this->data->message = trim($jinput->get('message', '', 'STRING'));
	$this->data->_magic_word = $jinput->get('magic_word', '', 'STRING');
	$this->data->_pic_selected = $jinput->get('picselected', '-1', 'STRING');
	return $this->data;
}

// -------------------------------------------------------------------------------
// Validate the user input
// We used to use JSession::checkToken() but that function does an immediate and silent redirect if the token doesn't match
// We want to show a message to the user so we now use a different method
//
function validate(&$errors, $config_data)
{
    $jinput = JFactory::getApplication()->input;
    $token = JSession::getFormToken();
    if (!$jinput->get($token, '', 'string'))			// get token from form
		{
        FC_trace::trace("Session token not in form: $token - token check failed");
		$errors['top'] = JText::_('COM_FLEXICONTACT_SESSION');
		return;
		}
    FC_trace::trace("Session token ok: $token");
    
// if using image captcha, validate that the correct image was chosen
// if the user gets it wrong more than 5 times, tell the controller to kill the session

	if ($config_data->num_images > 0)
		{
		require_once(LAFC_HELPER_PATH.'/flexi_captcha.php');
		$pic_selected = substr($this->data->_pic_selected,4);	// strip off the fci_
		$ret = Flexi_captcha::check($pic_selected, $config_data->num_images);
		if ($ret == 1)
			$errors['imageTest'] = JText::_('COM_FLEXICONTACT_WRONG_PICTURE');
		if ($ret == 2)
			{
			$errors['kill'] = 'Yes';		// tell the controller to kill the session
			return;
			}
		}
	
// if using magic word, validate the word

	if (!empty($config_data->magic_word))
        {
		if (strcasecmp($this->data->_magic_word, $config_data->magic_word) != 0)
			$errors['magic_word'] = JText::_('COM_FLEXICONTACT_WRONG_MAGIC_WORD');
        }
            
// if using the Joomla captcha plugin, call its check function

	if (!empty($config_data->joomla_captcha))
		{
		$plugin = Flexicontact_Utility::get_joomla_captcha();
		if ($plugin)													// if we can't get an instance, we can't test so the result is a PASS
			{
			try
				{
				if ($plugin->checkAnswer('') === false)					// can throw a RuntimeException
					{
					FC_trace::trace("Joomla captcha plugin FAIL");
					$errors['jcaptcha'] = JText::_('COM_FLEXICONTACT_CAPTCHA_WRONG');
					}
				else
					FC_trace::trace("Joomla captcha plugin PASS");
				}
			catch (\RuntimeException $e)
				{
				$jcaptcha_error = $e->getMessage();
				FC_trace::trace("Joomla captcha plugin exception so FAIL ($jcaptcha_error)");
				$errors['bottom'] = JText::_('COM_FLEXICONTACT_CAPTCHA_WRONG');
				}
			}
		}
	
// validate the from name
// three types of apostrophe are allowed: APOSTROPHE, LEFT SINGLE QUOTATION MARK, and RIGHT SINGLE QUOTATION MARK

	if (empty($this->data->name))
		$errors['name'] = JText::_('COM_FLEXICONTACT_REQUIRED');
    else
        {
        if (strlen($this->data->name) < 2)
            $errors['name'] = JText::_('COM_FLEXICONTACT_INVALID_NAME');
        if (!preg_match("/^[ ,.'‘’\-\pL]+$/u",$this->data->name))           // \pL is the Unicode character class
            $errors['name'] = JText::_('COM_FLEXICONTACT_INVALID_NAME');
        if (strlen($this->data->name) > 60)
            $this->data->name = mb_substr($this->data->name, 0, LAFC_MAX_NAME_LENGTH);  // limit to length of log column
        }

// validate the from address

	if (!Flexicontact_Utility::is_email($this->data->email, false))
        {
      	FC_trace::trace(" - Email address ".$this->data->email." is invalid");	
		$errors['email'] = JText::_('COM_FLEXICONTACT_BAD_EMAIL');
        }
    else
      	FC_trace::trace(" - Email address ".$this->data->email." is valid");	

// validate the subject

	if (($config_data->show_subject) && (empty($this->data->subject)))
		$errors['subject'] = JText::_('COM_FLEXICONTACT_REQUIRED');
	if (strlen($this->data->subject) > LAFC_MAX_SUBJECT_LENGTH)
		$this->data->subject = mb_substr($this->data->subject, 0, LAFC_MAX_SUBJECT_LENGTH);  // limit to size of log column

// validate the list selection

	if (($config_data->list_opt == "mandatory") && (empty($this->data->_list1)))
		$errors['list'] = JText::_('COM_FLEXICONTACT_SELECT_AN_OPTION');

// validate the user defined fields

	if (($config_data->field_opt1 == "mandatory") && (empty($this->data->field1)))
		$errors['field1'] = JText::_('COM_FLEXICONTACT_REQUIRED');
	if (strlen($this->data->field1) > LAFC_MAX_VARCHAR_LENGTH)
		$this->data->field1 = mb_substr($this->data->field1, 0, LAFC_MAX_VARCHAR_LENGTH);

	if (($config_data->field_opt2 == "mandatory") && (empty($this->data->field2)))
		$errors['field2'] = JText::_('COM_FLEXICONTACT_REQUIRED');
	if (strlen($this->data->field2) > LAFC_MAX_VARCHAR_LENGTH)
		$this->data->field2 = mb_substr($this->data->field2, 0, LAFC_MAX_VARCHAR_LENGTH);

	if (($config_data->field_opt3 == "mandatory") && (empty($this->data->field3)))
		$errors['field3'] = JText::_('COM_FLEXICONTACT_REQUIRED');
	if (strlen($this->data->field3) > LAFC_MAX_VARCHAR_LENGTH)
		$this->data->field3 = mb_substr($this->data->field3, 0, LAFC_MAX_VARCHAR_LENGTH);

	if (($config_data->field_opt4 == "mandatory") && (empty($this->data->field4)))
		$errors['field4'] = JText::_('COM_FLEXICONTACT_REQUIRED');
	if (strlen($this->data->field4) > LAFC_MAX_VARCHAR_LENGTH)
		$this->data->field4 = mb_substr($this->data->field4, 0, LAFC_MAX_VARCHAR_LENGTH);

	if (($config_data->field_opt5 == "mandatory") && (empty($this->data->field5)))
		$errors['field5'] = JText::_('COM_FLEXICONTACT_REQUIRED');
	if (strlen($this->data->field5) > LAFC_MAX_VARCHAR_LENGTH)
		$this->data->field5 = mb_substr($this->data->field5, 0, LAFC_MAX_VARCHAR_LENGTH);

// validate message

	if (($config_data->area_opt == "mandatory") && (empty($this->data->message)))
		$errors['message'] = JText::_('COM_FLEXICONTACT_REQUIRED');
	if (strlen($this->data->message) > LAFC_MAX_MESSAGE_LENGTH)
		$this->data->message = mb_substr($this->data->message, 0, LAFC_MAX_MESSAGE_LENGTH);
}

//-----------------------------------------
// Get client's IP address
//
function getIPaddress()
{
	if (isset($_SERVER["REMOTE_ADDR"]))
		return $_SERVER["REMOTE_ADDR"];
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	if (isset($_SERVER["HTTP_CLIENT_IP"]))
		return $_SERVER["HTTP_CLIENT_IP"];
	return "Unknown";
} 

//-------------------------------------------------------------------------------
// Get client's browser
// Returns 99 for unknown, 0 for msie, 1 for Firefox, etc
//
function getBrowser(&$browser_name)
{ 
	if (isset($_SERVER['HTTP_USER_AGENT']))
	    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
	else
		$u_agent = '';

    if (strstr($u_agent, 'Edg')) 
    	{ 
        $browser_name = 'Edge'; 
        return 7; 
    	} 
    if (strstr($u_agent, 'MSIE') && !strstr($u_agent, 'Opera')) 
    	{ 
        $browser_name = 'MSIE'; 
        return 0; 
    	} 
    if (strstr($u_agent, 'Trident')) 
    	{ 
        $browser_name = 'MSIE'; 
        return 0; 
    	} 
    if (strstr($u_agent, 'Firefox')) 
    	{ 
        $browser_name = 'Firefox'; 
        return 1; 
    	} 
    if (strstr($u_agent, 'Chrome')) 	 // must test for Chrome before Safari!
    	{ 
        $browser_name = 'Chrome'; 
        return 3; 
    	} 
    if (strstr($u_agent, 'Safari')) 
    	{ 
        $browser_name = 'Safari'; 
        return 2; 
    	} 
    if (strstr($u_agent, 'Opera')) 
    	{ 
        $browser_name = 'Opera'; 
        return 4; 
    	} 
    $browser_name = 'Unknown Browser';
    return 99;
} 

//-------------------------------------------------------------------------------
// Resolve an email variable
//
function email_resolve($config_data, $variable)
{
    if (!isset($this->data->name))     // this field is always mandatory so if we don't have it something is wrong
        {
        if ($variable == LAFC_T_FROM_NAME)
            return '** Form data is missing **';   // should never happen
        return '';
        }
    
	switch ($variable)
		{
		case LAFC_T_FROM_NAME:
			return $this->data->name;
		case LAFC_T_FROM_EMAIL:
			return $this->data->email;
		case LAFC_T_SUBJECT:
			return $this->data->subject;
		case LAFC_T_MESSAGE_PROMPT:
			return $config_data->area_prompt;
		case LAFC_T_MESSAGE_DATA:
			if ($config_data->email_html)
				$return_body = nl2br($this->data->message);
			else
				$return_body = $this->data->message;
			return $return_body;
		case LAFC_T_LIST_PROMPT:
			return $config_data->list_prompt;
		case LAFC_T_LIST_DATA:
			return $this->data->list_choice;
		case LAFC_T_FIELD1_PROMPT:
			return $config_data->field_prompt1;
		case LAFC_T_FIELD1_DATA:
			return $this->data->field1;
		case LAFC_T_FIELD2_PROMPT:
			return $config_data->field_prompt2;
		case LAFC_T_FIELD2_DATA:
			return $this->data->field2;
		case LAFC_T_FIELD3_PROMPT:
			return $config_data->field_prompt3;
		case LAFC_T_FIELD3_DATA:
			return $this->data->field3;
		case LAFC_T_FIELD4_PROMPT:
			return $config_data->field_prompt4;
		case LAFC_T_FIELD4_DATA:
			return $this->data->field4;
		case LAFC_T_FIELD5_PROMPT:
			return $config_data->field_prompt5;
		case LAFC_T_FIELD5_DATA:
			return $this->data->field5;
		case LAFC_T_BROWSER:
			return $this->data->browser_string;
		case LAFC_T_IP_ADDRESS:
			return $this->data->ip;
		case LAFC_T_SITE_NAME:
			return JFactory::getApplication()->get('sitename');
		default: return '';
		}
}

//-------------------------------------------------------------------------------
// Merge an email template with post data
//
function email_merge($template_text, $config_data)
{
	$text = $template_text;
	$variable_regex = "#%V_*(.*?)%#s";

	preg_match_all($variable_regex, $text, $variable_matches, PREG_SET_ORDER);

	foreach ($variable_matches as $match)
		{
		$resolved_text = $this->email_resolve($config_data, $match[0]);
		$text = str_replace($match[0], $resolved_text, $text);
		}

	return $text;
}

// -------------------------------------------------------------------------------
// Send the email
// Returns 1 for ok, or an error message on failure
//
function sendEmail($config_data)
{
	$app = JFactory::getApplication();
	$this->data->ip = $this->getIPaddress();
	$this->data->browser_id = $this->getBrowser($this->data->browser_string);
	$this->data->admin_email = $config_data->toPrimary;
	$this->data->admin_from_email = $app->get('mailfrom');
	$this->data->admin_reply_to_email = $this->data->email;        // the email address entered on the contact form
	$this->data->config_show_copy = $config_data->show_copy;
	$this->data->user_from_email = '';
    
	if (($this->data->_list1 != '') && (isset($config_data->list_array[$this->data->_list1])))
		$this->data->list_choice = $config_data->list_array[$this->data->_list1];
	else
		$this->data->list_choice = '';

// build the admin message

	$merged_admin_body = $this->email_merge($config_data->admin_template, $config_data);    
	$this->data->admin_email_body = JMailHelper::cleanBody($merged_admin_body);
    
    $merged_admin_subject = $this->email_merge($config_data->admin_subject, $config_data);
	$this->data->admin_email_subject = JMailHelper::cleanSubject($merged_admin_subject);

    if ($config_data->toPrimary == 'demo@demo.demo')        // demo mode, do not send email
        {
	    FC_trace::trace("sendEmail: Demo mode, no email sent");
        $this->data->status_main = 'Demo mode, no email sent';
        $this->data->status_copy = '0';
        return '1';
        }
    
	if ($app->get('mailonline',0) == 0)
		{
	    FC_trace::trace("sendEmail: emailing is disabled in Joomla");
        $this->data->status_main = JText::_('JDISABLED');
        $this->data->status_copy = '0';
		return JText::_('JDISABLED');
		}

// build the Joomla mail object - this can throw a phpmailerException

    try
		{
		$mail = JFactory::getMailer();
		if ($config_data->email_html)
			$mail->IsHTML(true);
		else
			$this->data->admin_email_body = strip_tags($this->data->admin_email_body);

		if (function_exists('escapeshellarg'))											// 12.14.03 handle sites that don't have escapeshellarg
			$mail->setSender(array($app->get('mailfrom'), $app->get('fromname')));		// with no sender, PHPMailer won't call escapeshellarg
		else
			FC_trace::trace("******* NOT SETTING SENDER BECAUSE THE escapeshellarg FUNCTION DOES NOT EXIST");	
		$mail->addRecipient($config_data->toPrimary);
		if (!empty($config_data->ccAddress))
			$mail->addCC($config_data->ccAddress);
		if (!empty($config_data->bccAddress))
			$mail->addBCC($config_data->bccAddress);
		$mail->addReplyTo($this->data->email, $this->data->name);
		$mail->setSubject($this->data->admin_email_subject);
		$mail->setBody($this->data->admin_email_body);
		if (FC_trace::tracing())
			FC_trace::trace("=====> Sending admin email: ".print_r($mail,true));
		$ret_main = $mail->Send();
		FC_trace::trace("Returned from Joomla Send mail (admin) function");	
		}
	catch (Exception $e)
		{
	    $result_msg = $e->getMessage();
	    $result_code = $e->getCode();
        $this->data->status_main = "$result_msg [$result_code]";
		$this->data->status_copy = '0';		// we are not going to try the user email
        FC_trace::trace("Exception sending admin email: ".$this->data->status_main);
		return $result_msg;
		}
	
	if ($ret_main === true)
        {
		$this->data->status_main = '1';
		FC_trace::trace("=====> Admin email sent ok");
        }
	else
        {
		$this->data->status_main = $mail->ErrorInfo;
		FC_trace::trace("=====> Admin email send failed: ".$mail->ErrorInfo);
        }
	
// if we should send the user a copy, send it separately

	if (($config_data->show_copy == LAFC_COPYME_ALWAYS) || ($this->data->show_copy == '1'))
		{
       	FC_trace::trace("Sending copy email to user");	
    	$this->data->user_from_email = $app->get('mailfrom');       // for the log
		$merged_user_body = $this->email_merge($config_data->user_template, $config_data);
		$this->data->user_email_body = JMailHelper::cleanBody($merged_user_body);
        $merged_user_subject = $this->email_merge($config_data->user_subject, $config_data);
    	$this->data->user_email_subject = JMailHelper::cleanSubject($merged_user_subject);
		$mail = JFactory::getMailer();
		if ($config_data->email_html)
			$mail->IsHTML(true);
		else
			$this->data->user_email_body = strip_tags($this->data->user_email_body);
			
		try
			{
			if (function_exists('escapeshellarg'))											// 12.14.03 handle sites that don't have escapeshellarg
				$mail->setSender(array($app->get('mailfrom'), $app->get('fromname')));		// with no sender, PHPMailer won't call escapeshellarg
			else
				FC_trace::trace("******* NOT SETTING SENDER BECAUSE THE escapeshellarg FUNCTION DOES NOT EXIST");	
			$mail->addRecipient($this->data->email);
			$mail->setSubject($this->data->user_email_subject);
			$mail->setBody($this->data->user_email_body);
			
			if (FC_trace::tracing())
				FC_trace::trace("=====> Sending user email: ".print_r($mail,true));
			$ret_copy = $mail->Send();
			}
		catch (Exception $e)
			{
			$result_msg = $e->getMessage();
			$result_code = $e->getCode();
			$this->data->status_copy = "$result_msg [$result_code]";
			FC_trace::trace("Exception sending user email: ".$this->data->status_copy);
			return $result_msg;
			}

		if ($ret_copy === true)
            {
			$this->data->status_copy = '1';
			FC_trace::trace("=====> User email sent ok");
			}
		else
            {
			$this->data->status_copy = $mail->ErrorInfo;
			FC_trace::trace("=====> User email send failed: ".$mail->ErrorInfo);
			}
		}
	else
        {
       	FC_trace::trace("Not sending copy email to user");	
		$this->data->status_copy = '0';		// copy not requested
        }
		
	return $this->data->status_main;		// both statuses are logged, but the main status decides what happens next
}

//-------------------------------------------------------------------------------
// Validate the email addresses configured in the menu item
// - this is called from the front end controller prior to displaying the contact form
// if invalid, returns an error message
// 
function email_check($config_data)
{
	$msg = '';
	
	$lang = JFactory::getLanguage();
	$lang->load(strtolower(LAFC_COMPONENT), JPATH_ADMINISTRATOR.'/components/com_flexicontact');	// load the back end language file

	if (isset($config_data->toPrimary))
		{
		if (!Flexicontact_Utility::is_email($config_data->toPrimary, false))
			$msg .= '('.JText::_('COM_FLEXICONTACT_V_EMAIL_TO').')';
		}
	else
		$msg .= '('.JText::_('COM_FLEXICONTACT_V_EMAIL_TO').')';
		
	if (isset($config_data->ccAddress))
		{
		if (!Flexicontact_Utility::is_email($config_data->ccAddress))
			$msg .= ' ('.JText::_('COM_FLEXICONTACT_V_EMAIL_CC').')';
		}
		
	if (isset($config_data->bccAddress))
		{
		if (!Flexicontact_Utility::is_email($config_data->bccAddress))
			$msg .= ' ('.JText::_('COM_FLEXICONTACT_V_EMAIL_BCC').')';
		}
		
	if ($msg != '')
		$msg = JText::_('COM_FLEXICONTACT_BAD_CONFIG_EMAIL').' - '.$msg;
		
	return $msg;
}

}