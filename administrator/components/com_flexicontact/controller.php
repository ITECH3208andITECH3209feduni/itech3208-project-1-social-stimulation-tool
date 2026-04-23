<?php
/********************************************************************
Product		: Flexicontact
Date		: 27 February 2023
Copyright	: Les Arbres Design 2010-2023
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactController extends JControllerLegacy
{
var $jinput;
var $app;

function __construct()
{
	parent::__construct();
	$this->registerTask('save', 'apply');
	$this->registerTask('save_css', 'apply_css');
    $this->jinput = JFactory::getApplication()->input;
   	$this->app = JFactory::getApplication();
}

function config($cachable = false, $urlparams = false)
{
	$function = $this->jinput->get('function', 'display', 'STRING');
	$param1 = $this->jinput->get('param1', '', 'STRING');
	$vars = $this->jinput->get('vars', '', 'STRING');
	$view = $this->getView('config', 'html');
    $config_model = $this->getModel('config');
    $config_data = $config_model->getData();
	if ($function == 'display')
		if (file_exists(LAFC_SITE_IMAGES_PATH.'/A_001.gif'))
			$this->app->enqueueMessage(JText::_('COM_FLEXICONTACT_INFO_NEW_TOYS'), 'notice');
    $view->function = $function;
    $view->config_data = $config_data;
    $view->param1 = $param1;
    $view->vars = $vars;
	$view->$function();
}

function apply()
{	
	$task = $this->jinput->get('task', '', 'STRING');		// 'save' or 'apply'
	$function = $this->jinput->get('function', '', 'STRING');
	$param1 = $this->jinput->get('param1', '', 'STRING');
	$vars = $this->jinput->get('vars', '', 'STRING');
	$config_model = $this->getModel('config');
	$stored = $config_model->store($function, $param1);
	
	if ($stored)
		{
        if ($task == 'apply')
            $this->setRedirect(LAFC_COMPONENT_LINK."&task=config&function=$function&param1=$param1&vars=$vars",JText::_('COM_FLEXICONTACT_SAVED'));
        else
            $this->setRedirect(LAFC_COMPONENT_LINK."&task=config",JText::_('COM_FLEXICONTACT_SAVED'));
        }
    else
        $this->config();
        
}   

function apply_css()								        // save changes to front end css
{
    $user = JFactory::getUser();
    if (!$user->authorise('core.admin'))
        {
		$msg = JText::_('JGLOBAL_AUTH_ACCESS_DENIED').': '.JText::_('JACTION_ADMIN_GLOBAL').' '.JText::_('JONLY');
    	$this->setRedirect(LAFC_COMPONENT_LINK."&task=config", $msg, 'error');
        return;
        }
	$task = $this->jinput->get('task', '', 'STRING');		// 'save_css' or 'apply_css'
    $css_contents = $this->jinput->get('css_contents', '', 'raw');
	if (strlen($css_contents) == 0)
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config");
	$length_written = file_put_contents (LAFC_SITE_CSS_PATH, $css_contents);
	if ($length_written == 0)
		$msg = JText::_('COM_FLEXICONTACT_NOT_SAVED');
	else
		$msg = JText::_('COM_FLEXICONTACT_SAVED');
	if ($task == 'apply_css')
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config&function=edit_css",$msg);
	else
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config",$msg);
}

function images()
{
	$view = $this->getView('images', 'html');
	$view->display();
}

function delete_image()
{
    $user = JFactory::getUser();
    if (!$user->authorise('core.admin'))
        {
		$msg = JText::_('JGLOBAL_AUTH_ACCESS_DENIED').': '.JText::_('JACTION_ADMIN_GLOBAL').' '.JText::_('JONLY');
    	$this->setRedirect(LAFC_COMPONENT_LINK."&task=images", $msg, 'error');
        return;
        }
	$cids = $this->jinput->get('cid', array(0), 'ARRAY');
	foreach ($cids as $file_name)
		@unlink(LAFC_SITE_IMAGES_PATH.'/'.$file_name);
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=images");
}

function reset_image_search()
{
	$this->jinput->set('filter_theme', 'all');
	$this->jinput->set('filter_colour', 'white');
	$this->images();
}

function log_list()
{
	$view = $this->getView('log', 'html');	
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();
	$logging = (isset($config_data->logging)) ? $config_data->logging : 0;
	if (!$logging)
		$this->app->enqueueMessage(JText::_('COM_FLEXICONTACT_LOGGING_DISABLED'), 'notice');
	$log_model = $this->getModel('log');
	$log_list = $log_model->getList();
	$view->log_list = $log_list;
	$pagination = $log_model->getPagination();
	$view->pagination =	$pagination;
	$view->display();
}

function log_detail()
{
	$view = $this->getView('log', 'html');
	$id = $this->jinput->get('id', '', 'INT');
	$log_model = $this->getModel('log');
	$log_data = $log_model->getOne($id);
	$view->log_data = $log_data;
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();
	$view->config_data = $config_data;
	$view->edit();
}

function delete_log()
{
	$log_model = $this->getModel('log');
	$cids = $this->jinput->get('cid', array(0), 'ARRAY');
	foreach ($cids as $id)
		$log_model->delete($id);
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=log_list");
}

function reset_log_search()
{
	$this->jinput->set('filter_date', LAFC_LOG_LAST_28_DAYS);
	$this->jinput->set('search', '');
	$this->jinput->set('filter_order', 'DATETIME');
	$this->jinput->set('filter_order_Dir', 'DESC');
	$this->jinput->set('limitstart', 0);
	$this->log_list();
}

function log_cancel()
{
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=log_list");
}

function cancel()
{
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=config");
}

function about()
{
	$view = $this->getView('about', 'html');
	$view->display();
}

function trace_on()
{
    if (FC_trace::tracing())
    	FC_trace::delete_trace_file();
	FC_trace::init_trace();
	$this->setRedirect(LAFC_COMPONENT_LINK.'&task=about');
}

function trace_off()
{
	FC_trace::delete_trace_file();
	$this->setRedirect(LAFC_COMPONENT_LINK.'&task=about');
}

function email_test()			// Send a test email to the admin address
{
    $config_model = $this->getModel('config');
    $config_data = $config_model->getData();
	$menu_data = $config_model->get_fc_menu_item();
	if (!$menu_data)
		{
        $this->setRedirect(LAFC_COMPONENT_LINK."&task=config",JText::_('COM_FLEXICONTACT_NO_MENU_ITEM'),'error');
		return;
		}
	$params = json_decode($menu_data->params);
	$email_to = $params->toPrimary;		
	if (!Flexicontact_Utility::is_email($email_to, false))
		{
        $this->setRedirect(LAFC_COMPONENT_LINK."&task=config",JText::_('COM_FLEXICONTACT_BAD_CONFIG_EMAIL').': '.$email_to,'error');
		return;
		}

	$email_text = JText::sprintf('COM_FLEXICONTACT_TEST_EMAIL_TEXT',$this->app->get('sitename'));
	$from_email = $this->app->get('mailfrom');
	$from_name = $this->app->get('fromname');
	$return_info = $this->send_email($from_name, $from_email, $email_to, $email_text, $email_text);

	if ($config_data->logging)
		{
       	FC_trace::trace("Logging the test message");	
		$log_model = $this->getModel('log');
		$log_model->purge($config_data);
		$log_model->init_data($from_name, $from_email, $email_to, $email_text, $email_text, $return_info);
		$log_model->store($log_model->data);
		}

	$mailer = $this->app->get('mailer');
	if ($return_info == '1')
		$this->app->enqueueMessage(JText::sprintf('COM_FLEXICONTACT_TEST_EMAIL_SENT_TO_XX', $email_to, $mailer),'message');
	else
		$this->app->enqueueMessage(JText::_('COM_FLEXICONTACT_EMAIL_SEND_FAILED').'<br>'.$return_info,'error');

    $this->setRedirect(LAFC_COMPONENT_LINK."&task=config");
}

//-------------------------------------------------------------------------------
// Send an email
//
function send_email($from_name, $from_email, $email_to, $subject, $body_text)
{
    FC_trace::trace("send_email: $from_name, $from_email, $email_to, $subject, $body_text");
    if ($email_to == 'demo@demo.demo')        // demo mode, do not send email
        {
	    FC_trace::trace("send_email: Demo mode, no email sent");
        return 'Demo mode, no email sent';
        }
	if ($this->app->get('mailonline',0) == 0)
		{
	    FC_trace::trace("send_email: emailing is disabled in Joomla");
		return JText::_('COM_FLEXICONTACT_EMAIL_DISABLED');
		}
    try
		{
		$mailer = JFactory::getMailer();
        $mailer->IsHTML(true);
		if (function_exists('escapeshellarg'))
            $mailer->setSender(array($from_email, $from_name));
		else                                                                            // with no sender, PHPMailer won't call escapeshellarg
			FC_trace::trace("******* NOT SETTING SENDER BECAUSE THE escapeshellarg FUNCTION DOES NOT EXIST");	
        $mailer->setSubject($subject);
        $mailer->setBody($body_text);
        $mailer->addRecipient($email_to);	
		$ret = $mailer->Send();
		}
	catch (Exception $e)
		{
	    $result_msg = $e->getMessage().' ['.$e->getCode().']';
        FC_trace::trace("phpmailer Exception: ".$result_msg);
		return $result_msg;
		}
	$email_errors = ob_get_contents();				// phpmailer has a nasty habit of echo'ing errors
	ob_clean();	
	if (strlen($email_errors) > 0)
		FC_trace::trace("Phpmailer echoed: [$email_errors]");

	if ($ret === true)
		{
		FC_trace::trace("mailer->Send() returned true");
		return '1';
		}
	else
		{
		FC_trace::trace("mailer->Send() returned error: ".$mailer->ErrorInfo."]");
		return "[ ".$mailer->ErrorInfo." ]";        // Force an error even if ErrorInfo is empty
		}
}

}