<?php
/********************************************************************
Product		: Flexicontact
Date		: 28 November 2022
Copyright	: Les Arbres Design 2010-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

// Check for ACL access

if (!JFactory::getUser()->authorise('core.manage', 'com_flexicontact'))
    {
	$app = JFactory::getApplication();
    $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
    }

// Pull in the helper files

require_once JPATH_ADMINISTRATOR.'/components/com_flexicontact/helpers/flexicontact_helper.php';
require_once LAFC_HELPER_PATH.'/trace_helper.php';

// load our css

$document = JFactory::getDocument();
if (substr(JVERSION,0,1) == '3')
    $document->addStyleSheet(JURI::base().'components/com_flexicontact/assets/com_flexicontact_j3.css?'.filemtime(JPATH_ADMINISTRATOR.'/components/com_flexicontact/assets/com_flexicontact_j3.css'));
if (substr(JVERSION,0,1) == '4')
    $document->addStyleSheet(JURI::base().'components/com_flexicontact/assets/com_flexicontact_j4.css?'.filemtime(JPATH_ADMINISTRATOR.'/components/com_flexicontact/assets/com_flexicontact_j4.css'));

JHtml::_('jquery.framework');           // make sure jQuery loads first ... 
$document->addScript(LAFC_ADMIN_ASSETS_URL.'com_flexicontact.js?v=130402');

// create an instance of the controller and tell it to execute $task

require_once( JPATH_ADMINISTRATOR.'/components/com_flexicontact/controller.php' );
$controller	= new FlexicontactController( );

$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task', 'config', 'STRING');

$controller->execute($task);

$controller->redirect();

