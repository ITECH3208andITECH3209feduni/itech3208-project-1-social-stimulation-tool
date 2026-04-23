<?php
/********************************************************************
Product		: Flexicontact
Date		: 23 February 2023
Copyright	: Les Arbres Design 2010-2023
Contact		: https://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewContact extends JViewLegacy
{

//---------------------------------------------------------------------------------------------------------
// display the contact form
//
function display($tpl = null)
{
// get the menu item parameters

	$app = JFactory::getApplication('site');
	$document = JFactory::getDocument();
	$menu_params =  $app->getParams();

	if (isset($menu_params->pageclass_sfx))
		echo "\n".'<div class="fc_page'.$menu_params->pageclass_sfx.'">';
	else
		echo "\n".'<div class="fc_page">';

// if there is a page heading in Page Display Options, draw it in H1
	
	if ($menu_params->get('show_page_heading', '0'))						// In menu item Page Display options
		if ($menu_params->get('page_heading', '') != '')
			echo "\n<h1>".$menu_params->get('page_heading', '').'</h1>';

// if there is a page heading in Basic Options, draw it in H2

	if ($menu_params->get('page_hdr', '') != '')							// Basic Options
		echo "\n<h2>".$menu_params->get('page_hdr', '').'</h2>';

// set meta data, if any

	if ($menu_params->get('menu-meta_description'))
		$document->setDescription($menu_params->get('menu-meta_description'));

	if ($menu_params->get('menu-meta_keywords'))
		$document->setMetadata('keywords', $menu_params->get('menu-meta_keywords'));

	if ($menu_params->get('robots'))
		$document->setMetadata('robots', $menu_params->get('robots'));

// add sitename to page title if configured

	if ($app->get('sitename_pagetitles', 0) == 1)
		{
		$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $menu_params->get('page_title', ''));
		$document->setTitle($title);
		}
	if ($app->get('sitename_pagetitles', 0) == 2)
		{
		$title = JText::sprintf('JPAGETITLE', $menu_params->get('page_title', ''), $app->get('sitename'));
		$document->setTitle($title);
		}
		
// resolve any plugins and the draw the top text, if any
// if empty text is returned (e.g. plugin "Easy Content Restriction" is enabled), we show the text unresolved, which can show unresolved plugins

	if (!empty($this->config_data->page_text))		// top text
		{
		$page_text = JHtml::_('content.prepare', $this->config_data->page_text, '', 'com_flexicontact');
		if (empty($page_text) && !empty($this->config_data->page_text))
			{
			FC_trace::trace("Skipping content.prepare for top text because the result was empty");
			$page_text = $this->config_data->page_text;
			}
		echo "\n".'<div class="fc_before">'.$page_text.'</div>';
		}

// if the menu item has a width parameter, add it to the fc_outer div as an inline style

    $form_width = $menu_params->get('form_width', '');
    if ($form_width != '')
        $outer_style = ' style="max-width:'.$form_width.';margin-left:auto;margin-right:auto;"';
    else
        $outer_style = '';

	echo '<div class="fc_outer"'.$outer_style.'>';
    
// if we have a 'top' error show it. If not, and if validation failed and the extra message is configured, show that    
    
    if (isset($this->errors['top']))
        echo '<div class="fc_error fc_top_error">'.$this->errors['top'].'</div>';
    elseif ( ($this->config_data->top_error) and !empty($this->errors) )
		echo '<div class="fc_error fc_top_error">'.JText::_('COM_FLEXICONTACT_MESSAGE_NOT_SENT').'</div>';
	
// start the form

	echo "\n".'<form name="fc_form" method="post" class="fc_form">';
	echo JHTML::_('form.token');
	echo '<input type="hidden" name="task" value="send">';

// from name

    if ($this->config_data->autofocus)
        $autofocus = ' autofocus="autofocus"';
    else
        $autofocus = '';

	$class = self::get_class('name');
	echo "\n".'<div class="fc_line'.$class.'"><label for="name" class="fc_left">'.JText::_('COM_FLEXICONTACT_FROM_NAME').'</label>';
	echo '<input type="text" class="fc_input" name="name" id="name" value="'.$this->escape($this->post_data->name).'"'.$autofocus.'>';
	echo self::get_error('name');
	echo '</div>';

// from email address

	$class = self::get_class('email');
	echo "\n".'<div class="fc_line'.$class.'"><label for="email" class="fc_left">'.JText::_('COM_FLEXICONTACT_FROM_ADDRESS').'</label>';
	echo '<input type="email" class="fc_input" name="email" id="email" value="'.$this->escape($this->post_data->email).'" spellcheck="false">';
	echo self::get_error('email');
	echo '</div>';

// subject

	if ($this->config_data->show_subject)
		{
		$class = self::get_class('subject');
		echo "\n".'<div class="fc_line'.$class.'"><label for="subject" class="fc_left">'.JText::_('COM_FLEXICONTACT_SUBJECT').'</label>';
		echo '<input type="text" class="fc_input" name="subject" id="subject" value="'.$this->escape($this->post_data->subject).'">';
		echo self::get_error('subject');
		echo '</div>';
		}

// the select list

	if ($this->config_data->list_opt != 'disabled')
		{
		$class = self::get_class('list');
		$list_html = Flexicontact_Utility::make_list('list1',$this->post_data->_list1, $this->config_data->list_array);
		echo "\n".'<div class="fc_line'.$class.'"><label for="list1" class="fc_left">'.$this->config_data->list_prompt.'</label>';
		echo $list_html.self::get_error('list');
		echo '</div>';
		}

// the five optional fields

	for ($i=1; $i<=5; $i++)
		{
		$opt_name = 'field_opt'.$i;
		$prompt_name = 'field_prompt'.$i;
		$field_name = 'field'.$i;
		if ($this->config_data->$opt_name == 'disabled')
			continue;
		if ($this->config_data->$prompt_name == '')
			$this->config_data->$prompt_name = '&nbsp;';
		$class = self::get_class($field_name);
		echo "\n".'<div class="fc_line'.$class.'"><label for="'.$field_name.'" class="fc_left">'.$this->config_data->$prompt_name.'</label>';
		echo '<input type="text" class="fc_input" name="'.$field_name.'" id="'.$field_name.'" value="'.$this->escape($this->post_data->$field_name).'">';
		echo self::get_error($field_name);
		echo '</div>';
		}

// the message textarea

	if ($this->config_data->area_opt != 'disabled')
		{
		if ($this->config_data->area_prompt == '')
			$this->config_data->area_prompt = '&nbsp;';
		$class = self::get_class('message');
		echo "\n".'<div class="fc_line fc_msg'.$class.'"><label for="message" class="fc_left fc_textarea">'.$this->config_data->area_prompt.'</label>';
		echo '<textarea class="fc_input" name="message" id="message" rows="'.$this->config_data->area_height.'" >'.$this->escape($this->post_data->message).'</textarea>';
		echo self::get_error('message');
		echo '</div>';
		}

// the "send me a copy" checkbox
// the empty <span> prevents the extra checkbox added by the Beez template

	if ($this->config_data->show_copy == LAFC_COPYME_CHECKBOX)
		{
		if ($this->post_data->show_copy)
			$checked = 'checked = "checked"';
		else
			$checked = '';
		$checkbox = '<input type="checkbox" class="fc_input" name="copy_me" id="copy_me" value="1" '.$checked.'>';
		echo "\n".'<div class="fc_line fc_lcb">';
		$label = $this->config_data->copyme_prompt;
		if (empty($label))
			$label = 'COM_FLEXICONTACT_COPY_ME';
		if (substr($label,0,16) == 'COM_FLEXICONTACT')
			$label = JText::_($label);
		echo $checkbox.'<span></span><label for="copy_me" class="fc_right">'.$label.'</label></div>';
		}
	
// the agreement required checkbox

	if (empty($this->config_data->agreement_prompt))
		$send_button_state = '';
	else
		$send_button_state = $this->make_agreement_text();

// the magic word

	if ($this->config_data->magic_word != '')
		{
		$class = self::get_class('magic_word');
		echo "\n".'<div class="fc_line fc_magic'.$class.'"><label for="magic_word" class="fc_left">'.$this->config_data->magic_word_prompt.'</label>';
		echo '<input type="text" class="fc_input" name="magic_word" id="magic_word" value="'.$this->escape($this->post_data->_magic_word).'" required="required">';
		echo self::get_error('magic_word');
		echo '</div>';
		}

// the image captcha

	if (!empty($this->config_data->num_images))
		{
		require_once(LAFC_HELPER_PATH.'/flexi_captcha.php');
		echo Flexi_captcha::show_image_captcha($this->config_data, self::get_error('imageTest'));
		}
        
// include the Joomla captcha plugin, if configured

	if (!empty($this->config_data->joomla_captcha))
		{
		$plugin = Flexicontact_Utility::get_joomla_captcha();
		if ($plugin)
			{
			echo '<div class="fc_line fc_jcap">';
			echo $plugin->display('fcjcap', 'fcjcap', 'fcjcap');
			if (isset($this->errors['jcaptcha']))
				echo self::get_error('jcaptcha');
			echo '</div>';
			}
		}

// the send button

	echo "\n".'<div class="fc_line fc_send">';
	echo '<input type="submit" class="'.$this->config_data->button_class.'" id="fc_send_button" name="send_button" '.$send_button_state.' value="'.JText::_('COM_FLEXICONTACT_SEND_BUTTON').'">';
	if (isset($this->errors['bottom']))
		echo self::get_error('bottom');
	echo '</div>';
	echo "</form>";
	echo '</div>';					// class="fc_outer"
        
// resolve any plugins and the draw the bottom text, if any
// if empty text is returned (e.g. plugin "Easy Content Restriction" is enabled), we show the text unresolved, which can show unresolved plugins

	if (!empty($this->config_data->bottom_text))
		{
		$bottom_text = JHtml::_('content.prepare', $this->config_data->bottom_text, '', 'com_flexicontact');
		if (empty($bottom_text) && !empty($this->config_data->bottom_text))
			{
			FC_trace::trace("Skipping content.prepare for bottom text because the result was empty");
			$bottom_text = $this->config_data->bottom_text;
			}
		echo "\n".'<div class="fc_after">'.$bottom_text.'</div>';
		}
		
	echo "\n</div>";				// class="fc_page"
}

//---------------------------------------------------------------------------------------------------------
// Get the class name for a field outer div
//
function get_class($field_name)
{
	$class = '';
	if (isset($this->errors[$field_name]))
		$class .= ' fc_err';
	if ($this->config_data->show_mandatory == LAFC_MANDATORY_NEVER)
		return $class;
	if (($this->config_data->show_mandatory == LAFC_MANDATORY_ON_ERROR) && (!isset($this->errors[$field_name])))
		return $class;
	switch ($field_name)
		{
		case 'name':
		case 'email':
		case 'subject':
			$class .= ' fc_req';	// these three are always required
			break;
		case 'list':
			if ($this->config_data->list_opt == 'mandatory')
				$class .= ' fc_req';
			break;
		case 'field1':
		case 'field2':
		case 'field3':
		case 'field4':
		case 'field5':
			$opt_name = 'field_opt'.substr($field_name,5,1);
			if ($this->config_data->$opt_name == 'mandatory')
				$class .= ' fc_req';
			break;
		case 'message':
			if ($this->config_data->area_opt == 'mandatory')
				$class .= ' fc_req';
			break;
		}
	return $class;
}

//---------------------------------------------------------------------------------------------------------
// Get and format an error message
//
function get_error($field_name)
{
	if (!isset($this->errors[$field_name]))
		return '';
	$class = 'fc_error';
	if (!empty($this->config_data->error_class))
		$class .= ' '.$this->config_data->error_class;
	return '<span class="'.$class.'">'.$this->errors[$field_name].'</span>';
}

//-------------------------------------------------------------------------------
// Make the HTML for the agreement required checkbox with its terms and conditions link
// $this->config_data->agreement_prompt contains %link text%, like this: "Please read our %privacy policy% and confirm your agreement"
// $this->config_data->agreement_link is the URL of the actual terms and conditions
// the return value is the send button state, either an empty string, or 'disabled="disabled"'
//
function make_agreement_text()
{
	$matches = array();
	$result = preg_match('/.*?%(.*?)%/is', $this->config_data->agreement_prompt, $matches);
	if ($result == false)			// 0 for no matches, false for an error
		$link_name = '';			// the link is optional
	else
		$link_name = $matches[1];	// the text inside the %...%

	$link = trim($this->config_data->agreement_link);
	$label = $this->config_data->agreement_prompt;
	$iframe = '';

	if (!empty($link))
		switch ($this->config_data->agreement_style)
			{
			case 0:					// popup window
				$link_html = JHTML::link($link, $link_name, 'target="_blank" id="fcj_window_open"');
				break;
			case 1:					// new tab or window
				$link_html = JHTML::link($link, $link_name, 'target="_blank" ');
				break;
			case 2:					// hidden iframe
				$link_html = '<button type="button" id="fc_toggle_terms" class="fc_toggle_terms">'.$link_name.'</button>';
				$iframe = '<div id="fc_terms" class="fc_terms_inactive"><iframe id="fc_terms_iframe" class="terms-frame" src="'.$link.'"></iframe></div>';
				break;
			}

	if ($this->post_data->agreement_check)
		{										// checkbox already checked
		$send_button_state = '';
		$checked = 'checked = "checked"';
		}
	else
		{
		$send_button_state = 'disabled="disabled"';
		$checked = '';
		}
	$checkbox = '<input type="checkbox" class="fc_input" name="agreement_check" id="fcj_agreement_check" value="1" '.$checked.'>';
	echo "\n".'<div class="fc_line fc_lcb">';
	$label = str_replace('%'.$link_name.'%', $link_html, $this->config_data->agreement_prompt);
	echo $checkbox.'<span></span><label for="fcj_agreement_check" class="fc_right">'.' '.$label.'</label></div>';
	echo "\n".$iframe;
	return $send_button_state;
}

}