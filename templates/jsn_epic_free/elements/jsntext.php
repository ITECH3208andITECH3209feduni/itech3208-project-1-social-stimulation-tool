<?php
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Output JSN Text section
 *
 * @package		
 * @subpackage	
 * @since		1.6
 */
class JFormFieldJSNText extends JFormField{

	public $type = 'JSNText';

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected function getInput() {
		$html	= '';
		
		// check System Cache Plugin
		$cacheSensitive = JSN_CACHESENSITIVE && (string) $this->element['cachesensitive']=='yes';
		
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true' || $cacheSensitive) ? ' disabled="disabled"' : '';
		
		//posttext for Parameter
		$posttext	= (isset($this->element['posttext'])) ? '<span class="jsn-posttext">'.$this->element['posttext'].'</span>' : '';
		
		$html		= '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' .
					  ' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"' .
					  $size.$maxLength.$class.$readonly.$disabled.' />'.$posttext;
		return	$html;
	}	
}
