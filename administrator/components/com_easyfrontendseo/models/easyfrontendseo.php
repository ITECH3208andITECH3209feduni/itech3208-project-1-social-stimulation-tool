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
jimport('joomla.application.component.model');

class EasyFrontendSeoModelEasyFrontendSeo extends JModelLegacy
{
    protected $_total;
    protected $_pagination;

    function __construct()
    {
        parent::__construct();
        $mainframe = JFactory::getApplication();

        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest('easyfrontendseo.limitstart', 'limitstart', 0, 'int');
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $search = $mainframe->getUserStateFromRequest('easyfrontendseo.filter.search', 'filter_search', NULL);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
        $this->setState('filter.search', $search);
    }

    function getData()
    {
        if(empty($this->_data))
        {
            $query = $this->_db->getQuery(true);

            $query->select('*');
            $query->from('#__plg_easyfrontendseo AS a');

            $search = $this->getState('filter.search');

            if(!empty($search))
            {
                $search = $this->_db->Quote('%'.$this->_db->escape($search, true).'%');
                $query->where('(a.url LIKE '.$search.') OR (a.title LIKE '.$search.') OR (a.description LIKE '.$search.') OR (a.keywords LIKE '.$search.') OR (a.generator LIKE '.$search.') OR (a.robots LIKE '.$search.')');
            }

            $query->order($this->_db->escape('id ASC'));

            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        if(!empty($this->_data))
        {
            foreach($this->_data as &$entry)
            {
                $entry->complete = true;

                foreach($entry as $value)
                {
                    if(empty($value))
                    {
                        $entry->complete = false;
                        break;
                    }
                }
            }
        }

        return $this->_data;
    }

    function getPagination()
    {
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }

    function getTotal()
    {
        if(empty($this->_total))
        {
            $query = $this->_db->getQuery(true);

            $query->select('*');
            $query->from('#__plg_easyfrontendseo AS a');

            $search = $this->getState('filter.search');

            if(!empty($search))
            {
                $search = $this->_db->Quote('%'.$this->_db->escape($search, true).'%');
                $query->where('(a.url LIKE '.$search.') OR (a.title LIKE '.$search.') OR (a.description LIKE '.$search.') OR (a.keywords LIKE '.$search.') OR (a.generator LIKE '.$search.') OR (a.robots LIKE '.$search.')');
            }

            $query->order($this->_db->escape('id ASC'));

            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    public function getPluginStatus()
    {
        $query = $this->_db->getQuery(true);

        $query->select('*');
        $query->from('#__extensions');
        $query->where('folder = '.$this->_db->quote('system').' AND element = '.$this->_db->quote('easyfrontendseo'));

        $result = $this->_getList($query);

        if($this->_db->getErrorMsg())
        {
            JError::raiseWarning(500, $this->_db->getErrorMsg());
        }
        else
        {
            if(!empty($result))
            {
                $plugin_state = array('enabled' => (boolean)$result[0]->enabled, 'url_settings' => JURI::base().'index.php?option=com_plugins&task=plugin.edit&extension_id='.$result[0]->extension_id);
            }
            else
            {
                $plugin_state = array();
            }
        }

        return $plugin_state;
    }

}
