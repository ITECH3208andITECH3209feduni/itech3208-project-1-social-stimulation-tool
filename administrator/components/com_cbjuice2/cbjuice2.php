<?php
/**
 * Joomla! 1.5 component CBJUICE2
 *
 * @version $Id: cbjuice2.php 2010-12-25 12:20:17 svn $
 * @author Paul Jacobson and Stephen Thompson
 * @copyright Paul Jacobson and Stephen Thompson
 * @package Joomla
 * @subpackage CBJUICE2
 * @license GNU/GPL
 *
 * This component manages users using a CSV file
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
global $_CB_framework,$_CB_database, $ueConfig;
/** @global string $_CB_adminpath
 *  @global array $ueConfig
 */
global $_CB_Admin_Done, $_CB_adminpath, $ueConfig, $mainframe;

if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
	$_CB_adminpath		=	JPATH_ADMINISTRATOR . '/components/com_comprofiler';
	include_once $_CB_adminpath . '/ue_config.php';
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';

} else {
	$_CB_adminpath		=	$mainframe->getCfg( 'absolute_path' ). '/administrator/components/com_comprofiler';
	include_once $_CB_adminpath . '/ue_config.php';
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';
}

global $cbj2_initial_mode, $cbj2_opptype,$cbj2_controller;
// Require the base controller
if (!isset($task))$task="";
$cbj2_initial_mode=$task;
require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'controller.php';

// Require the helpers
require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php';

// Initialize the controller
$cbj2_controller = new Cbjuice2Controller( );

//echo "Prior to request in cbjuice2 ",$task,"<br>";
// joomla specific

$input=new JInput();
$task= trim(JRequest::getVar('task',null));
$cbj2_opptype=trim(JRequest::getVar('opptype',null));
$cbj2_filename=(JRequest::getVar('csvusers',null,'FILES'));
$cbj2_keytype=trim(jRequest::getVar('keytype'));
//echo "Key_type is",$cbj2_keytype,"<br>";
//echo "Uploaded File name is ",$cbj2_filename['tmp_name'],"<br>";
// Perform the Request task
if($task==''){
	$cbj2_controller->display();
	return TRUE;

}
//echo "Task=$task <br>";
if($task=='about'){
	JRequest::setVar('view','default');
	JRequest::setVar('layout','about');
	$cbj2_controller->display();
	return TRUE;
}
if($task=='save'){
	$delimiter=cbGetParam($_REQUEST,'delimiter',null);
	if($delimiter=='TAB'||$delimiter=='tab'){$delimiter='\t';}
	$cbj2_controller->save($delimiter);
	return TRUE;
}
if(!$task=='process') {
	JRequest::setVar('view','default');
	JRequest::setVar('layout','default');
	$cbj2_controller->display();

}

if($task=='process'){
	/* check the delimiter
	 *
	 */
	$params= JComponentHelper::getParams('com_cbjuice2');
	$delimiter=$params->get('csv_delimiter');
//	$delimiter=cbGetParam($_REQUEST,'delimiter',null);
	if($delimiter=="" or $delimiter==NULL){
		echo "<h1><b>".JText::_('CBJ_WARNING2')."</b></h1><br>";
		$cbj2_controller->redirect();
		return;

	}
	switch ($cbj2_opptype){
		case 1;
		case 2:
			Jrequest::setVar('view','add_edit');
			JRequest::setVar('layout','add_edit');
			$cbj2_controller->display();
			$cbj2_controller->add_edit();
			break;
		case 3:
			$params= JComponentHelper::getParams('com_cbjuice2');
			$delimiter=cbGetParam($_REQUEST,'delimiter',null);
			if($delimiter="" or empty($delimiter)){
				$delimiter=$params->get('csv_delimiter');
			}
			if($delimiter=='TAB'||$delimiter=='tab'){$delimiter='\t';}
			$cbj2_controller->save($delimiter);
			break;
		case 4:
			JRequest::setVar('view','default');
			JRequest::setVar('layout','delete');
			$cbj2_controller->display();
			//	echo "Delete being started<br>";
			$cbj2_controller->delete();
			break;
		default:
			echo "Invalid opptype",$cbj2_opptype,"<br>";



	}


}

//echo "<br>return from task";
$cbj2_controller->redirect();
?>