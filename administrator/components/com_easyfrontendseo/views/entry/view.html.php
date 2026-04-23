<?php
/**
 * EFSEO - Easy Frontend SEO for Joomal! 2.5
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Projectsite: http://joomla-extensions.kubik-rubik.de/efseo-easy-frontend-seo
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

class EasyFrontendSeoViewEntry extends JViewLegacy
{
    function display($tpl = null)
    {
        $entry = $this->get('Data');
        $characters_length = $this->get('CharactersLength');
        $robots_array = array('index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow', '');

        if(empty($entry->id))
        {
            JToolBarHelper::title(JText::_('COM_EASYFRONTENDSEO').' - '.JText::_('COM_EASYFRONTENDSEO_NEWENTRY'), 'easyfrontendseo-add');
            JToolBarHelper::save('save');
            JToolBarHelper::cancel('cancel');
        }
        else
        {
            JToolBarHelper::title(JText::_('COM_EASYFRONTENDSEO').' - '.JText::_('COM_EASYFRONTENDSEO_EDITENTRY'), 'easyfrontendseo-edit');
            JToolbarHelper::apply('apply');
            JToolBarHelper::save('save');
            JToolBarHelper::cancel('cancel', 'Close');
        }

        JHTML::_('behavior.framework');

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_easyfrontendseo/css/easyfrontendseo.css');
        $document->addScript('components/com_easyfrontendseo/js/wordcount.js', 'text/javascript');

        $output = "window.addEvent('domready', function(){";
        $output .= "new WordCount('counter_title', {inputName:'title', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_title', {inputName:'title', eventTrigger: 'click', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'});\n";
        $output .= "new WordCount('counter_description', {inputName:'description', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_description', {inputName:'description', eventTrigger: 'click', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'});\n";
        $output .= "new WordCount('counter_keywords', {inputName:'keywords', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_keywords', {inputName:'keywords', eventTrigger: 'click', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'});\n";
        $output .= "new WordCount('counter_generator', {inputName:'generator', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_generator', {inputName:'generator', eventTrigger: 'click', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'});\n";
        $output .= "new WordCount('counter_robots', {inputName:'robots', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'}); new WordCount('counter_robots', {inputName:'robots', eventTrigger: 'click', wordText:'".JText::_('COM_EASYFRONTENDSEO_WORDS')."', charText:'".JText::_('COM_EASYFRONTENDSEO_CHARACTERS')."'});\n";
        $output .= " });";

        $document->addScriptDeclaration($output, 'text/javascript');

        $this->assignRef('entry', $entry);
        $this->assignRef('characters_length', $characters_length);
        $this->assignRef('robots_array', $robots_array);

        parent::display($tpl);
    }

}
