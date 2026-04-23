<?php
/**
 * Joomla! 1.5 component CBJUICE2
 *
 * @version $Id: controller.php 2010-12-25 12:20:17 svn $
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

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'  );

/**
 * CBJUICE2 Controller
 *
 * @package Joomla
 * @subpackage CBJUICE2
 */


class Cbjuice2Controller extends JControllerLegacy 
{
	/**
	 * Constructor
	 * @access private
	 * @subpackage CBJUICE2
	 */
	function __construct() {
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'default');
			JRequest::setVar('layout','default');
		}
		$this->item_type = 'Default';


		parent::__construct();
	}


	function add_edit (){

		include_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'add_edit.php';

		$filename=cbGetParam($_FILES,'csvusers',null);
		if(($filename==NULL)or (!file_exists($filename['tmp_name']))){
			echo "File name is not defined<br>";
			return false;
		} else {
			$filename_local=$filename['tmp_name'];
			$addstatus=cbj2_process_users($filename_local,$cbj2_opptype);
		}
		 
	}
	function save ($delimiter){
		include_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'save.php';
		$params= JComponentHelper::getParams('com_cbjuice2');
		if($delimiter="" or empty($delimiter)){
			$delimiter=$params->get('csv_delimiter');
		}
		if($delimiter=='TAB'||$delimiter=='tab'){$delimiter='\t';}
		$result=cbj2_saveusers($delimiter);
	}
	function delete(){
		include_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'delete_users.php';
		$filename=cbGetParam($_FILES,'csvusers',null);
		//    	print_r($filename);
		if(($filename==NULL)or (!file_exists($filename['tmp_name']))){
			echo "File name is not defined<br>";
			return false;
		} else {
			$filename_local=$filename['tmp_name'];
			$addstatus=cbj2_delete_users($filename_local);
		}
	}

}



?>