<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 February 2023
Copyright	: Les Arbres Design 2010-2023
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactController extends JControllerLegacy
{
function display($cachable = false, $urlparams = false)
{
	$this->addModelPath(JPATH_ADMINISTRATOR.'/components/com_flexicontact/models');	// the log and config models are in the back end
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();	    				// get config data merged with menu parameters
	FC_trace::trace("Displaying the contact form. Config structure: ".print_r($config_data,true));	

	$email_model = $this->getModel('email');
	$post_data = $email_model->init_data($config_data);
	$msg = $email_model->email_check($config_data);					// Check the email-to address
	if ($msg != '')
		{
    	FC_trace::trace("Stopping because the email to address is not valid");	
		echo $msg;
		return;
		}
		
	$view = $this->getView('contact','html');
	$view->config_data = $config_data;
	$view->post_data = $post_data;
	$view->display();
}

function send()
{
   	FC_trace::trace("Send request received");	
	$this->addModelPath(JPATH_ADMINISTRATOR.'/components/com_flexicontact/models');	// the log and config models are in the back end
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();    					// get config data merged with menu parameters

	$email_model = $this->getModel('email');
	$email_model->getPostData($config_data);
	
	$errors = array();
   	FC_trace::trace("Calling validate function");	
	$email_model->validate($errors, $config_data);
   	FC_trace::trace("Returned from validate function");	

	if (isset($errors['kill']))
		{														// too many captcha attempts
       	FC_trace::trace("Too many captcha attempts");	
		$session = JFactory::getApplication()->getSession();	// kill the session
		$session->destroy();
        echo JText::_('COM_FLEXICONTACT_WRONG_PICTURE').'<br>'.JText::_('COM_FLEXICONTACT_SESSION');
        return;
		}

	if (isset($errors['top']))
		{														// session token didn't match
       	FC_trace::trace("Validation failed: ".print_r($errors,true));	
		$view = $this->getView('contact','html');
		$view->config_data = $config_data;
		$view->errors = $errors;
		$view->post_data = $email_model->data;
		$view->display();
		return;
		}

	if (!empty($errors))										// if validation failed
		{
       	FC_trace::trace("Validation failed: ".print_r($errors,true));	
		$view = $this->getView('contact','html');
		$view->config_data = $config_data;
		$view->errors = $errors;
		$view->post_data = $email_model->data;
		$view->display();
		return;
		}
   	FC_trace::trace("Validation ok");	

// here if validation ok

   	FC_trace::trace("Calling sendEmail function");	
	$email_status = $email_model->sendEmail($config_data);
   	FC_trace::trace("Returned from sendEmail function with status: $email_status");	

	if ($config_data->logging)
		{
       	FC_trace::trace("Logging the message");	
		$log_model = $this->getModel('log');
		$log_model->purge($config_data);		// purge expired log entries
		$log_model->store($email_model->data);
		}
		
	if ($email_status != '1')					// if send failed, show status using our _confirm view
		{
		$view = $this->getView('_confirm','html');
		$failed_message = JText::_('COM_FLEXICONTACT_MESSAGE_FAILED').': '.$email_status;
		$view->message = $failed_message;
		$view->display();
		return;
		}

// here if the email was sent ok

	if ($config_data->confirm_link)
        {
       	FC_trace::trace("Redirecting to ".$config_data->confirm_link);	
        $link = strtolower($config_data->confirm_link);
        if (substr($link,0,4) != 'http')
            $link = JURI::root().$link;            
		$this->setRedirect($link);
        }
	else
		{
		$view = $this->getView('_confirm','html');
		$view->message = $email_model->email_merge($config_data->confirm_text, $config_data);
		$view->display();
		}
}

//---------------------------------------------------------------------------------------------
// Serve a captcha image
//
function image()
{
	require_once(LAFC_HELPER_PATH.'/flexi_captcha.php');
	Flexi_captcha::show_image();
}

}