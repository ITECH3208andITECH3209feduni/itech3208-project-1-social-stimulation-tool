<?php
/**
 *  @package Expert Extension Framework (XEF)
 *  @copyright Copyright (c)2010-2012 ThemeXpert.com
 *  @license GNU General Public License version 3, or later
 **/

// Protect from unauthorized access
defined('_JEXEC') or die();

// Require XEF helper class
require_once JPATH_LIBRARIES . '/xef/xef.php';

require_once(JPATH_SITE . '/components/com_k2/helpers/route.php');
require_once(JPATH_SITE . '/components/com_k2/helpers/utilities.php');

class XEFSourceK2 extends XEFHelper
{

    public function getItems()
    {
        jimport('joomla.filesystem.file');

        $app = JFactory::getApplication('site', array(), 'J');

        $cid = $this->get('k2_catid', NULL);
        $ordering = $this->get('k2_items_ordering','');

        $user = JFactory::getUser();
        $aid = $user->get('aid');
        $db = JFactory::getDBO();

        $jnow = JFactory::getDate();
        $now = $jnow->toSQL();
        $nullDate = $db->getNullDate();

        $query = "SELECT i.*, CASE WHEN i.modified = 0 THEN i.created ELSE i.modified END as lastChanged, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.params AS categoryparams";

        if ($ordering == 'best')
            $query .= ", (r.rating_sum/r.rating_count) AS rating";

        if ($ordering == 'comments')
            $query .= ", COUNT(comments.id) AS numOfComments";

        $query .= " FROM #__k2_items as i LEFT JOIN #__k2_categories c ON c.id = i.catid";

        if ($ordering == 'best')
            $query .= " LEFT JOIN #__k2_rating r ON r.itemID = i.id";

        if ($ordering == 'comments')
            $query .= " LEFT JOIN #__k2_comments comments ON comments.itemID = i.id";

        $query .= " WHERE i.published = 1 AND i.access IN(".implode(',', $user->getAuthorisedViewLevels()).") AND i.trash = 0 AND c.published = 1 AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")  AND c.trash = 0";
        $query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." )";
        $query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";


        if ($this->get('k2_catfilter')) {
            if (!is_null($cid)) {
                if (is_array($cid)) {
                    if ($this->get('k2_get_children')) {
                        $itemListModel = K2Model::getInstance('Itemlist', 'K2Model');
                        $categories = $itemListModel->getCategoryTree($cid);
                        $sql = @implode(',', $categories);
                        $query .= " AND i.catid IN ({$sql})";

                    } else {
                        JArrayHelper::toInteger($cid);
                        $query .= " AND i.catid IN(".implode(',', $cid).")";
                    }

                } else {
                    if ($this->get('k2_get_children')) {
                        $itemListModel = K2Model::getInstance('Itemlist', 'K2Model');
                        $categories = $itemListModel->getCategoryTree($cid);
                        $sql = @implode(',', $categories);
                        $query .= " AND i.catid IN ({$sql})";
                    } else {
                        $query .= " AND i.catid=".(int)$cid;
                    }

                }
            }
        }

        if ($this->get('k2_featured_items') == '0')
            $query .= " AND i.featured != 1";

        if ($this->get('k2_featured_items') == '2')
            $query .= " AND i.featured = 1";

        if ($ordering == 'comments')
            $query .= " AND comments.published = 1";



        if ($app->getLanguageFilter())
        {
            $languageTag = JFactory::getLanguage()->getTag();
            $query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
        }

        switch ($ordering)
        {

            case 'date' :
                $orderby = 'i.created ASC';
                break;

            case 'rdate' :
                $orderby = 'i.created DESC';
                break;

            case 'alpha' :
                $orderby = 'i.title';
                break;

            case 'ralpha' :
                $orderby = 'i.title DESC';
                break;

            case 'order' :
                if ($this->get('FeaturedItems') == '2')
                    $orderby = 'i.featured_ordering';
                else
                    $orderby = 'i.ordering';
                break;

            case 'rorder' :
                if ($this->get('FeaturedItems') == '2')
                    $orderby = 'i.featured_ordering DESC';
                else
                    $orderby = 'i.ordering DESC';
                break;

            case 'hits' :
                if ($this->get('popularityRange'))
                {
                    $datenow = &JFactory::getDate();
                    $date = $datenow->toSQL();
                    $query .= " AND i.created > DATE_SUB('{$date}',INTERVAL ".$this->get('popularityRange')." DAY) ";
                }
                $orderby = 'i.hits DESC';
                break;

            case 'rand' :
                $orderby = 'RAND()';
                break;

            case 'best' :
                $orderby = 'rating DESC';
                break;

            case 'comments' :
                if ($this->get('popularityRange'))
                {
                    $datenow = &JFactory::getDate();
                    $date = $datenow->toSQL();
                    $query .= " AND i.created > DATE_SUB('{$date}',INTERVAL ".$this->get('popularityRange')." DAY) ";
                }
                $query .= " GROUP BY i.id ";
                $orderby = 'numOfComments DESC';
                break;

            case 'modified' :
                $orderby = 'lastChanged DESC';
                break;

            case 'publishUp' :
                $orderby = 'i.publish_up DESC';
                break;

            default :
                $orderby = 'i.id DESC';
                break;
        }

        $query .= " ORDER BY ".$orderby;
        $db->setQuery($query, 0, $this->get('count'));
        $items = $db->loadObjectList();

        //XEFUtility::debug($items);
        $items = $this->prepareItems($items);

        return $items;
        }

    public function getLink($item)
    {
        return urldecode(JRoute::_( K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias), $item->catid.':'.urlencode($item->categoryalias)) . $this->getMenuItemId()));
    }

    public function getCategory($item)
    {
        return $item->categoryname;
    }

    public function getCategoryLink($item)
    {
        return urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->catid.':'.urlencode($item->categoryalias))) . $this->getMenuItemId());
    }

    public function getImage($item)
    {
        return XEFUtility::getK2Images($item->id, $item->title, $item->introtext);
    }

    public function getDate($item)
    {
        return JHTML::_('date',$item->created, JText::_('DATE_FORMAT_LC3'));
    }
}