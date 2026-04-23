<?php
defined('_JEXEC') or die('Restricted access');
global $cbj2_initial_mode;
JToolBarHelper::title(JText::_('CBJUICE2'), 'generic.png');
JToolBarHelper::preferences('com_cbjuice2','500','750');
$versionstuff=new JVersion;
$thisversion=$versionstuff->getShortVersion();
if(!(strpos($thisversion,"1.6")===FALSE)){
	JSubMenuHelper::addEntry('Process CSVs','index.php?option=com_cbjuice2&task=&view=default');
	JSubMenuHelper::addEntry('Save Users','index.php?option=com_cbjuice2&task=save&view=default');
	JSubMenuHelper::addEntry('About CBJUICE','index.php?option=com_cbjuice2&task=about&view=about');
}
echo Jtext::_('CBJ_ABOUT_STRING1');
echo Jtext::_('CBJ_ABOUT_STRING2');
echo Jtext::_('CBJ_ABOUT_STRING3');
echo Jtext::_('CBJ_ABOUT_STRING4');
echo Jtext::_('CBJ_ABOUT_STRING5');
echo Jtext::_('CBJ_ABOUT_STRING6');
echo Jtext::_('CBJ_ABOUT_STRING7');
echo Jtext::_('CBJ_ABOUT_STRING8');
echo Jtext::_('CBJ_ABOUT_STRING9');
echo Jtext::_('CBJ_ABOUT_STRING10');
echo Jtext::_('CBJ_ABOUT_STRING11');

?>

