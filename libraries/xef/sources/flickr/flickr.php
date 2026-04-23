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

class XEFSourceFlickr extends XEFHelper
{
	public function getItems()
	{
		jimport('joomla.filesystem.folder');

        $api_key = '2a4dbf07ad5341b2b06d60c91d44e918';
        $cache_path = JPATH_ROOT. '/cache/test/flickr';
        $nsid = '';
        $photos = array();

        // create cache folder if not exist
        JFolder::create($cache_path, 0755);

        if( !class_exists('phpFlickr'))
        {
            require_once 'api/phpFlickr.php';    
        }

        $f = new phpFlickr($api_key);
        $f->enableCache('fs',$cache_path, $this->get('cache_time')); //enable caching

        if($this->get('flickr_search_from') == 'user')
        {
            $username = $this->get('flickr_search_from');
            if($username != NULL)
            {
                $person = $f->people_findByUsername($username);
                $nsid = $person['id'];
            }else{
            	return '';
            }
                
            $photos = $f->people_getPublicPhotos($nsid, NULL, NULL, $this->get('item_count'));
            $source = $photos['photos']['photo'];
        }

        if( $this->get('flickr_search_from') == 'tags' OR $this->get('flickr_search_from') == 'text')
        {
            $tags = $this->get('flickr_attrs');

            if(!empty($tags))
            {
                $attrs = '';
                if($this->get('flickr_search_from') == 'tags') $attrs = 'tags';
                if($this->get('flickr_search_from') == 'text') $attrs = 'text';

                $photos = $f->photos_search(array($attrs=>$tags,'per_page'=>$this->get('item_count')));
                $source = $photos['photo'];
            }else{
            	return '';
            }               
        }

        if($this->get('flickr_search_from') == 'recent'){
            $photos = $f->photos_getRecent( NULL, $this->get('item_count') );
            $source = $photos['photo'];
        }

        //$extras = 'description,date_upload,owner_name,tags';
        $items = array();
        $i = 0;
        if(count($source)>0){
            foreach ($source as $photo)
            {
                $id = $photo['id'];
                $obj = new stdClass();
                $info = $f->photos_getInfo($id);
                $nsid = $info['owner']['username'];

                $obj->title = $info['title'];
                $obj->image = $f->buildPhotoURL($photo,'_b');
                $obj->link = $info['urls']['url'][0]['_content'];
                $obj->introtext = $info['description'];
                $obj->date = date('Y.M.d : H:i:s A', $info['dateuploaded']);

                $items[$i] = $obj;
                $i++;
            }
        }
        //return $items;
        var_dump($f);
	}

	public function getLink($item)
	{
		return $item->link;

	}
	
	public function getImage($item) 
	{
		return $item->image;
	}

	public function getDate($item)
	{
		return $item->date;
	}

	public function getCategory($item) { return ; }
	public function getCategoryLink($item) { return; }
}
