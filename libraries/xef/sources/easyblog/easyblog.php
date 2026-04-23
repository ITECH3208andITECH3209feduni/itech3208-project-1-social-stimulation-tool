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

// Load Easyblog helper and router class
require_once JPATH_SITE . '/components/com_easyblog/helpers/helper.php';
require_once JPATH_SITE . '/components/com_easyblog/helpers/date.php';
require_once JPATH_SITE . '/administrator/components/com_easyblog/tables/profile.php';

class XEFSourceEasyblog extends XEFHelper
{
    public function getItems()
    {
        $catid      = ($this->get('ezb_catfilter')) ? $this->get('ezb_catid',NULL) : '';
        $ordering   = $this->get('ezb_ordering','latest');
        $user 	    = JFactory::getUser();
        $category	= EasyBlogHelper::getTable( 'Category', 'Table' );

        $category->load($catid);

        if($category->private && $user->id == 0){
            echo JText::_('This category is set to private');
            return;
        }

        if( !class_exists( 'EasyBlogModelBlog' ) ){
            jimport( 'joomla.application.component.model' );
            JLoader::import( 'blog' , EBLOG_ROOT . '/' . 'models' );
        }

        $model = EasyBlogHelper::getModel( 'Blog' );

        if( $this->get( 'ezfeatured') )
        {
            $items = $model->getFeaturedBlog( $catid , $this->get('count') );
        }
        else
        {
            $items = $model->getBlogsBy('category', $catid, $ordering , $this->get('count') , EBLOG_FILTER_PUBLISHED, null, false );
        }

        $config = EasyBlogHelper::getConfig();

        if(! empty($items)){
            for($i = 0; $i < count($items); $i++)
            {
                $row    	=& $items[$i];
                $author 	= EasyBlogHelper::getTable( 'Profile', 'Table' );

                $row->author		= $author->load( $row->created_by );
                $row->commentCount 	= EasyBlogHelper::getCommentCount($row->id);


                $requireVerification = false;
                if($config->get('main_password_protect', true) && !empty($row->blogpassword))
                {
                    $row->title	= JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $row->title);
                    $requireVerification = true;
                }

                if($requireVerification && !EasyBlogHelper::verifyBlogPassword($row->blogpassword, $row->id))
                {
                    $theme = new CodeThemes();
                    $theme->set('id', $row->id);
                    $theme->set('return', base64_encode(EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id)));
                    $row->introtext		= $theme->fetch( 'blog.protected.php' );
                    $row->content		= $row->introtext;
                    $row->showRating	= false;
                    $row->protect		= true;
                }
                else
                {
                    $row->introtext		= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->content );
                    $row->showRating	= true;
                    $row->protect		= false;

                }
            }//end foreach
        }
        //XEFUtility::debug($items);
        $items = $this->prepareItems($items);

        return $items;
    }

    public function getLink($item)
    {
        return  EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $item->id . $this->getMenuItemId() );
    }

    public function getCategory($item)
    {
        return $item->category;
    }

    public function getCategoryLink($item)
    {
        return EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$item->category_id . $this->getMenuItemId());
    }

    public function getImage($item)
    {
        return XEFUtility::getImage($item->intro);
    }

    public function getDate($item)
    {
        $config = EasyBlogHelper::getConfig();

        return EasyBlogDateHelper::toFormat( JFactory::getDate( $item->created ), $config->get('layout_dateformat', '%A, %d %B %Y') );
    }
}