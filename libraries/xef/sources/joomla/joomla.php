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

// Require Content router
require_once JPATH_SITE.'/components/com_content/helpers/route.php';

class XEFSourceJoomla extends XEFHelper
{

    public function getItems()
    {
        jimport('joomla.application.component.model');
        $app = JFactory::getApplication('site', array(), 'J');

        // Get the dbo
        $db = JFactory::getDbo();

        // Get an instance of the generic articles model
        if(XEF_JVERSION == '25')
        {
            JModel::addIncludePath(JPATH_SITE.'/components/com_content/models');
            $model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

        }else{

            JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models');
            $model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
        }

        // Set application parameters in model
        $appParams = $app->getParams();
        $model->setState('params', $appParams);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', $this->get( 'count', 4 ));
        $model->setState('filter.published', 1);

        // Access filter
        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Category filter
        switch( $this->get('jomcatfilter') )
        {
            case 0: // All
                $catid = '';
                break;
            case 1: // From specific category
                $catid = $this->get('jom_catid',array());
                break;
        }

        $model->setState( 'filter.category_id', $catid );

        // User filter
        $userId = JFactory::getUser()->get('id');
        switch ($this->get('jom_user_id'))
        {
            case 'by_me':
                $model->setState('filter.author_id', (int) $userId);
                break;
            case 'not_me':
                $model->setState('filter.author_id', $userId);
                $model->setState('filter.author_id.include', false);
                break;

            case '0':
                break;

            default:
                $model->setState('filter.author_id', (int) $this->get('jom_user_id'));
                break;
        }

        // Filter by language
        $model->setState('filter.language',$app->getLanguageFilter());

        //  Featured switch
        switch ($this->get('jom_show_featured'))
        {
            case '1':
               $model->setState('filter.featured', 'only');
               break;
            case '0':
               $model->setState('filter.featured', 'hide');
               break;
            default:
               $model->setState('filter.featured', 'show');
               break;
        }

       // Set ordering
       $order_map = array(
               'm_dsc' => 'a.modified DESC, a.created',
               'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
               'c_dsc' => 'a.created',
               'p_dsc' => 'a.publish_up',
               'hits_dsc' => 'a.hits'
       );

        $ordering = JArrayHelper::getValue($order_map, $this->get('jom_ordering'), 'a.publish_up');
        $dir = 'DESC';

        $model->setState('list.ordering', $ordering);
        $model->setState('list.direction', $dir);
        $items = $model->getItems();

        // Prepare the items with options
        $items = $this->prepareItems($items);

        //XEFUtility::debug($catid);

       return $items;
    }

    public function getLink($item)
    {
        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));

        $link = '';

        $item->slug = $item->id.':'.$item->alias;
        $item->catslug = $item->catid.':'.$item->category_alias;

        if ($access || in_array($item->access, $authorised))
        {
            // We know that user has the privilege to view the article
            $link = JRoute::_( ContentHelperRoute::getArticleRoute($item->slug, $item->catslug) . $this->getMenuItemId() );
        }
        else {
            $link = JRoute::_('index.php?option=com_users&view=login');
        }

        return $link;
    }

    public function getCategory($item)
    {
        return $item->category_title;
    }

    public function getCategoryLink($item)
    {
        return JRoute::_( ContentHelperRoute::getCategoryRoute($item->catid) . $this->getMenuItemId() );
    }

    public function getImage($item)
    {
        $path = '';
        //Take advantage from joomla default Intro image system
        if( isset($item->images) )
        {
            $images = json_decode($item->images);
        }

        if( isset($images->image_intro) and !empty($images->image_intro) )
        {
            $path = $images->image_intro;

        }else{
            //get image from article intro text
            $path = XEFUtility::getImage($item->introtext);
        }

        return $path;
    }

    public function getDate($item)
    {
        return JHTML::_('date',$item->created, JText::_('DATE_FORMAT_LC3'));
    }
}