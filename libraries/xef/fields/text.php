<?php
/**
 *
 * @package     Expose
 * @version     3.0
 * @author      ThemeXpert http://www.themexpert.com
 * @copyright   Copyright (C) 2010 - 2011 ThemeXpert
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 *
 **/

// Ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldText extends JFormField
{
	protected $type = 'Text';

        protected function getInput()
	{
            $output = NULL;
            // Initialize some field attributes.
            $size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
            $maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
            $class      = $this->element['class'];
            $readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
            $disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
            
            $prepend    = ($this->element['prepend'] != NULL) ? '<span class="add-on">'. JText::_($this->element['prepend']). '</span>' : '';

            $append   = ($this->element['append'] != NULL) ? '<span class="add-on">'.JText::_($this->element['append']).'</span>' : '';

            if($prepend) $extra_class = 'input-prepend';
            elseif($append) $extra_class = 'input-append';
            else $extra_class = '';

            $wrapstart  = '<div class="field-wrap clearfix '.$class. $extra_class .'">';
            $wrapend    = '</div>';

            $input = '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' .
                            ' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"' .
                            $size.$disabled.$readonly.$maxLength.'/>';

            $output = $wrapstart . $prepend . $input . $append . $wrapend;
            return $output;
	}

}



