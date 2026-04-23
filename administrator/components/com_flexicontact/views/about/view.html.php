<?php
/********************************************************************
Product     : Flexicontact
Date		: 23 February 2023
Copyright	: Les Arbres Design 2010-2023
Contact	    : https://www.lesarbresdesign.info
Licence     : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactViewAbout extends JViewLegacy
{
function display($tpl = null)
{
	Flexicontact_Utility::addSubMenu('about');
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_ABOUT'), 'lad.png');
	JToolBarHelper::cancel();

?>
	<form method="get" name="adminForm" id="adminForm" class="form-horizontal">
	<input type="hidden" name="option" value="com_flexicontact">
	<input type="hidden" name="task" value="">
<?php
        	
// get the installed version

	$xml_array = JInstaller::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_flexicontact/flexicontact.xml');
	$component_version = $xml_array['version'];

// build the help screen

	$about['name'] = LAFC_COMPONENT_NAME;
	$about['prefix'] = 'COM_FLEXICONTACT';
	$about['current_version'] = $component_version;
    $about['latest_version'] = $this->get_version('flexicontact');      // get the latest version
	$about['reference'] = 'flexicontact';
	$about['link_version'] = "https://www.lesarbresdesign.info/version-history/flexicontact";
	$about['link_doc'] = "https://www.lesarbresdesign.info/extensions/flexicontact";
	$about['link_rating'] = "https://extensions.joomla.org/extension/contacts-and-feedback/contact-forms/flexi-contact/";
	
	$this->draw_about($about);

	echo '<p></p>';
	JForm::addFieldPath(LAFC_FORM_FIELD_PATH);
	$form = JForm::getInstance('about_form', LAFC_FORMS_PATH.'/about.xml');
	echo $form->renderFieldset('main');
	echo '</form>';
	Flexicontact_Utility::viewEnd();
}

//------------------------------------------------------------------------------
// draw the about screen - this is the same in all our components
//
function draw_about($about)
{
	echo '<h3>'.$about['name'].': '.JText::_($about['prefix'].'_HELP_TITLE').'</h3>';
	if (!empty($this->lad_info_notice))
		echo $this->lad_info_notice;
	else
		{
		echo '<h4>'.JText::_($about['prefix'].'_HELP_RATING').' ';
		echo JHTML::link($about['link_rating'], 'Joomla Extensions Directory', 'target="_blank"').'</h4>';
		}
	echo '<table class="table table-striped table-bordered width-auto">';	
	echo '<tr><td>'.JText::_($about['prefix'].'_VERSION').'</td>';
	echo '<td>'.$about['current_version'].'</td></tr>';	
	if ($about['latest_version'] != '')
		echo '<tr><td>'.JText::_($about['prefix'].'_LATEST_VERSION').'</td><td>'.$about['latest_version'].'</td></tr>';
	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_CHECK').'</td>';
	echo '<td>'.JHTML::link($about['link_version'], 'Les Arbres Design - '.$about['name'], 'target="_blank"').'</td></tr>';
	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_DOC').'</td>';
	echo '<td>'.JHTML::link($about['link_doc'], "www.lesarbresdesign.info", 'target="_blank"').'</td></tr>';
	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_LES_ARBRES').'</td>';
	echo '<td>'.JHTML::link("https://www.lesarbresdesign.info/", 'Les Arbres Design', 'target="_blank"').'</td></tr>';		
	if (!empty($about['extra']))
		foreach($about['extra'] as $row)
			echo '<tr><td>'.$row['left'].'</td><td>'.$row['right'].'</td></tr>';
	echo '</table>';
}
	
//------------------------------------------------------------------------------
// get the latest version info
//
function get_version($product)
{
	$version_file = JPATH_ROOT.'/administrator/components/com_'.$product.'/latest_version.xml';
	$version_info = '';
	$filetime = 0;
	if (file_exists($version_file))
		$filetime = @filemtime($version_file);
	if ((time() - $filetime) < 3600)				// version info is valid for one hour
		$version_info = file_get_contents($version_file);
	else
		{
		$url = 'https://www.lesarbresdesign.info/jupdate?product='.$product.'&src=about';
		try
			{
			$http = JHttpFactory::getHttp();
			$response = $http->get($url, array(), 20);
			$version_info = $response->body;
			}
		catch (RuntimeException $e)
			{
			return '';
			}
		file_put_contents($version_file, $version_info);
		}
    $version = self::str_between($version_info,'<version>','</version>');
    $this->lad_info_notice = self::str_between($version_info, '<lad_info_notice><![CDATA[', ']]></lad_info_notice>');
	return $version;
}
				
function str_between($string, $start, $end)
{
    $string = ' '.$string;
    $pos = strpos($string, $start);
    if ($pos == 0)
        return '';
    $pos += strlen($start);
    $len = strpos($string, $end, $pos) - $pos;
    return substr($string, $pos, $len);
}

}