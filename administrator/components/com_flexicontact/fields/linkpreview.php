<?php
/********************************************************************
Product		: Multiple Products
Date		: 28 November 2022
Copyright	: Les Arbres Design 2010-2022
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

JFormHelper::loadFieldClass('text');

class JFormFieldLinkpreview extends JFormFieldText
{
protected $type = 'linkpreview';

protected function getInput()
{    
    $button = '<button type="button" class="btn btn-info ladj-preview" data-jroot="'.JURI::root(true).'">'.JText::_('JGLOBAL_PREVIEW').'</button>';    
	$html = parent::getInput();
    return $html.$button;
}

}