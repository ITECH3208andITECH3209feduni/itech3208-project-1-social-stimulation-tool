<?php
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Position Mapping field type
 *
 * @package		
 * @subpackage	
 * @since		1.6
 */
class JFormFieldJSNPositionMapping extends JFormField
{
	public $type = 'JSNPositionMapping';
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected function getInput() {
		
		require_once dirname(dirname(__FILE__)).DS.'includes'.DS.'lib'.DS.'jsn_utils.php';
		$jsnUtils 	  		= JSNUtils::getInstance();
		$doc 				= JFactory::getDocument();
		$templateName		= $jsnUtils->getTemplateName();
		$templateAbsPath 	= JPATH_ROOT . DS . 'templates' .  DS . $templateName;
		$attr 				= ($this->element['disabled'] ? 'disabled="'.$this->element['disabled'].'"' : '');
		$default 			= ($this->element['default'] ? $this->element['default'] : '');
		$data 				= $jsnUtils->getPositions($templateName);
	
		$html 				= '<div class="jsn-positionmapping">';

		if($this->value) {
			$html 	.= $jsnUtils->renderPositionComboBox($this->value, $data['desktop'], 'Select position', 'jform[params]['.$this->element['name'].']', $attr);
		} else {
			$html 	.= $jsnUtils->renderPositionComboBox($default, $data['desktop'], 'Select position', 'jform[params]['.$this->element['name'].']', $attr);
		}

		$html 	.= '</div>';
		
		return $html;		
	}
} 