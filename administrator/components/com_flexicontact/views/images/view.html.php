<?php
/********************************************************************
Product     : Flexicontact
Date		: 23 February 2023
Copyright	: Les Arbres Design 2010-2023
Contact     : https://www.lesarbresdesign.info
Licence     : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');
require_once JPATH_ADMINISTRATOR.'/components/com_flexicontact/helpers/flexi_captcha.php';

class FlexicontactViewImages extends JViewLegacy
{
function display($tpl = null)
{
	Flexicontact_Utility::addSubMenu('images');
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CAPTCHA_IMAGES'), 'lad.png');
	JToolBarHelper::deleteList('','delete_image');
	JToolBarHelper::cancel();
    
// get the installed themes    
    
    $theme_info_array = Flexi_captcha::get_themes();
    FC_trace::trace("Themes: ".print_r($theme_info_array, true));
                
// make the theme select list

    $theme_list = array();
    foreach ($theme_info_array as $short_name => $theme_info)
        if ($theme_info_array[$short_name]['count'] > 0)
            $theme_list[$short_name] = $theme_info['list_name'];

	$app = JFactory::getApplication();
	$filter_theme = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_theme','filter_theme', 'all','word');
    if (!array_key_exists($filter_theme, $theme_list))
        $filter_theme = 'all';
    $theme_list_html = Flexicontact_Utility::make_list('filter_theme', $filter_theme, $theme_list, 'ladj-change-submit');
    
// make the background colour selector

	$filter_colour = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_colour','filter_colour', 'white','string');
    $colour_list = array('white' => JText::_('COM_FLEXICONTACT_WHITE'), 'black' => JText::_('COM_FLEXICONTACT_BLACK'), 'gray' => JText::_('COM_FLEXICONTACT_GREY'));
	$colour_list_html = Flexicontact_Utility::make_list('filter_colour', $filter_colour, $colour_list, 'ladj-change-submit');

// load the relevant language files

    Flexi_captcha::load_language_files($theme_info_array);
	
// get an array of all the filenames in the image directory

    $imageFiles = Flexi_captcha::get_image_files(LAFC_SITE_IMAGES_PATH, $theme_info_array[$filter_theme]['regex']);
    $image_count = count($imageFiles);
	sort($imageFiles);

// start the form

	echo '<form method="post" name="adminForm" id="adminForm" class="lad-filterform" >';
	echo '<input type="hidden" name="option" value="com_flexicontact">';
	echo '<input type="hidden" name="controller" value="menu">';
	echo '<input type="hidden" name="task" value="images">';
	echo '<input type="hidden" name="boxchecked" value="0">';

    if (empty($imageFiles))
		{
		echo JText::_('COM_FLEXICONTACT_NO_IMAGES');
        echo '</form>';
        Flexicontact_Utility::viewEnd();
		return;
		}

// filter header
    
	echo '<div>&nbsp;<div class="lad-filterform-left">';
    echo '<label for="toggle_all">'.JText::_('JGLOBAL_CHECK_ALL').'</label>';
	echo ' <input type="checkbox" name="toggle_all" id="toggle_all" value="" class="form-check-input ladj-check-all"> ';
    echo '<label for="toggle_all">'.$image_count.' '.JText::_('COM_FLEXICONTACT_IMAGES').'</label>';
	echo '</div>';
	echo '<div class="lad-filterform-right">';
    echo ' '.JText::_('COM_FLEXICONTACT_THEME').' '.$theme_list_html;
	echo ' '.JText::_('COM_FLEXICONTACT_BACKGROUND').' '.$colour_list_html;
	echo ' <button type="button" class="btn btn-primary ladj-click-submit" data-task="reset_image_search">'.JText::_('JSEARCH_RESET').'</button>';
	echo '</div></div>';
    echo '<div style="margin-top:10px;">&nbsp;</div>';

    if (empty($imageFiles) and $filter_theme == 'all')
		{
		echo JText::_('COM_FLEXICONTACT_NO_IMAGES');
        echo '</form>';
        Flexicontact_Utility::viewEnd();
        return;
		}
	
	$i = 0;
	$missing_language = false;
	foreach ($imageFiles as $filename)
		{
		$imageInfo = getimagesize(LAFC_SITE_IMAGES_PATH.'/'.$filename);
		if ($imageInfo !== false)
			{
			$imageX = $imageInfo[0];
			$imageY = $imageInfo[1];
			}
		
		$text_name = 'COM_FLEXICONTACT_IMAGE_'.strtoupper($filename);
		$description = JText::_($text_name);	// resolved by front end language file
		if ($text_name == $description)			// highlight if not resolved
			{
			$missing_language = true;
			$description = '<span class="icon-warning" style="color:orange;font-size:24px;height:32px;line-height:32px"></span>';
			}
		
		echo "\n".'<div class="fcc_image_cell lad-break-word '.$filter_colour.'">';
		echo "\n".JHTML::_('grid.id',   $i++, $filename);
		echo "\n".'<img src="'.LAFC_SITE_IMAGES_URL.$filename.'" width="75" alt="">';
		echo "\n".'<b>'.htmlspecialchars($filename).'</b><br>';
		echo $description.'<br>';
		echo $imageX.'x'.$imageY.'<br>';
		echo '</div>';
		}

	echo '</form>';

	if ($missing_language)
		JFactory::getApplication()->enqueueMessage(JText::_('COM_FLEXICONTACT_MISSING_IMAGE_LANG'), 'error');

	Flexicontact_Utility::viewEnd();
}

}