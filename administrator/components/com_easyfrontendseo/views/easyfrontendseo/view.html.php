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

class EasyFrontendSeoViewEasyFrontendSeo extends JViewLegacy
{
    protected $_state;

    function display($tpl = null)
    {
        JToolBarHelper::title(JText::_('COM_EASYFRONTENDSEO')." - ".JText::_('COM_EASYFRONTENDSEO_SUBMENU_ENTRIES'), 'easyfrontendseo');
        JToolBarHelper::addNew();
        JToolBarHelper::deleteList();
        JToolBarHelper::editList();
        JToolBarHelper::preferences('com_easyfrontendseo', '500');

        $items = $this->get('Data');
        $pagination = $this->get('Pagination');
        $plugin_state = $this->get('PluginStatus');
        $this->_state = $this->get('State');

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_easyfrontendseo/css/easyfrontendseo.css');

        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('plugin_state', $plugin_state);

        parent::display($tpl);
    }

}
