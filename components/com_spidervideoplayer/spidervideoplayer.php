<?php  
 /**
 * @package Spider Video Player
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_COMPONENT.'/controller.php' ;

$db =& JFactory::getDBO();
$query = "SELECT urlHdHtml5,urlHtml5 FROM #__spidervideoplayer_video";
$db->setQuery($query);
$url = $db->loadRow();
	
if(!$url)
{
	$query = "ALTER TABLE #__spidervideoplayer_video  ADD urlHdHtml5 varchar(255) AFTER thumb, ADD urlHtml5 varchar(255) AFTER urlHD;";
	$db->setQuery($query);
	$db->Query($query);
}

$controller = JRequest::getVar( 'controller' );

$classname    = 'spidervideoplayerController'.$controller;
$controller   = new $classname( );  

$controller->execute( JRequest::getVar( 'task' ) );
  
$controller->redirect();  
?>