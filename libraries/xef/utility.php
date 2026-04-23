<?php
/**
 *  @package Expert Extension Framework (XEF)
 *  @copyright Copyright (c)2010-2012 ThemeXpert.com
 *  @license GNU General Public License version 3, or later
 **/

// Protect from unauthorized access
defined('_JEXEC') or die();

// Import Joomla file system class
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Expert Extension Framework (XEF) helper class
 *
 * Expert Extension Framework (XEF) is a set of classes which extends Joomla! 2.5 and later's
 * MVC framework with features making maintaining all ThemeXpert's extensions much easier.
 *
 * Initially designed for module development and more coming...
 */

abstract class XEFUtility
{

    public static function getModuleId($module, $params)
    {
        $id = ($params->get('auto_module_id',0)==1) ? 'txmod_'.$module->id : $params->get('module_unique_id');

        return $id;
    }

    public static function addjQDom( $js )
    {
       //$this->jqDom .= "\t\t\t" . $js ."\n";
    }

    public static function renderCombinedDom()
    {
        $doc = JFactory::getDocument();
        $jqNoConflict = "\n\t\t".'jQuery.noConflict();'."\n";
        $dom = '';
        //add noConflict
        $dom .= $jqNoConflict;
        $dom .= "\n\t\t" . 'jQuery(document).ready(function($){'."\n".self::jqDom."\n\t\t});";

        $doc->addScriptDeclaration($dom);
    }

    public static function addjQuery($module, $params)
    {

        $doc = JFactory::getDocument();
        $app = JFactory::getApplication('site', array(), 'J');

        $version = $params->get('jquery_version') ? $params->get('jquery_version') : '1.8.2';
        $cdn = $params->get('jquery_source') ? $params->get('jquery_source') : 'local';

        $xef_url = JURI::root(true) . '/libraries/xef';
        $mod_url = JURI::root(true) . '/modules/' . $module->module;

        #$file = '//ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js';
        $file = '//asterius.ballarat.edu.au/jquery/'.$version.'/jquery.min.js';

        if( $params->get('jquery_enabled') OR $params->get('load_jquery') )
        {

            if( XEF_JVERSION == '25')
            {
                if( !$app->get('jQuery') )
                {
                    if( $cdn == 'local')
                    {
                        $file = $xef_url . '/assets/js/' . 'jquery-'.$version.'.min.js';

                        //if file not exist in framework we'll check it on module folder
                        if ( !file_exists($file) )
                        {
                            if(file_exists($mod_url . '/assets/js/' . 'jquery-'.$version.'.min.js'))
                            {
                                $file = $mod_url . '/assets/js/' . 'jquery-'.$version.'.min.js';
                            }
                        }
                    }else{
                        $doc->addScript($file);
                    }

                    $app->set('jQuery',$version);
                    $doc->addScript($file);
                }


            }else{
                if( $cdn == 'google-cdn')
                {
                    $doc->addScript($file);

                }else{
                    JHtml::_('jquery.framework');
                }
            }
        }
    }

    public static function loadModernizr($module, $params)
    {
        $doc = JFactory::getDocument();

        //path
        $path = JURI::root(true) . '/libraries/xef/assets/js/modernizr.min.js';

        if( !defined('XEF_MODERNIZR') )
        {
            $doc->addScript($path);

            define('XEF_MODERNIZR', 1);
        }
    }

    /***
     *
     * Get image from given text.
     *
     * @params $text
     * @return $image path
     *
     */
    public static function getImage($text)
    {
        // No image path
        $image_path = 'libraries/xef/assets/images/noimage.jpg';

        if( preg_match( "/\<img.+?src=\"(.+?)\".+?\>/", $text, $matches ) )
        {
            $image_path='';

            if ( isset($matches[1]) )
            {
                $image_path = $matches[1];
            }

            return $image_path;
        }

        return $image_path;
    }

    /***
     *
     * Get only large image from k2 image source, if failed then search for introtext.
     *
     * @params $id
     * @params $title
     * @params $text
     * @return $image_path
     *
     **/
    public static function getK2Images($id, $title, $text)
    {
        if ( file_exists( JPATH_SITE . '/media/k2/items/cache/' . md5("Image".$id) . '_L.jpg' ) )
        {

            $image_path = 'media/k2/items/cache/'.md5("Image".$id).'_L.jpg';

            return $image_path;

        }
        elseif($text != NULL)
        {
            return self::getImage($text);

        }else
        {
            echo "Image not found for article $title \n";
        }

    }

    // Word limit
    public static function wordLimit($str, $limit = 100, $end_char = '&#8230;')
    {
        if (JString::trim($str) == '')
            return $str;

        // always strip tags for text
        $str = strip_tags($str);

        $find = array("/\r|\n/u", "/\t/u", "/\s\s+/u");
        $replace = array(" ", " ", " ");
        $str = preg_replace($find, $replace, $str);

        preg_match('/\s*(?:\S*\s*){'.(int)$limit.'}/u', $str, $matches);
        if (JString::strlen($matches[0]) == JString::strlen($str))
            $end_char = '';
        return JString::rtrim($matches[0]).$end_char;
    }

    // Character limit
    public static function characterLimit($str, $limit = 150, $end_char = '...')
    {
        if (JString::trim($str) == '')
            return $str;

        // always strip tags for text
        $str = strip_tags(JString::trim($str));

        $find = array("/\r|\n/u", "/\t/u", "/\s\s+/u");
        $replace = array(" ", " ", " ");
        $str = preg_replace($find, $replace, $str);

        if (JString::strlen($str) > $limit)
        {
            $str = JString::substr($str, 0, $limit);
            return JString::rtrim($str).$end_char;
        }
        else
        {
            return $str;
        }

    }

    public static function getResizedImage( $path, $dimensions = array(), $module, $append= '')
    {
        if( !file_exists($path) ) return ;

        if(!class_exists('XpertThumb')){
            include_once 'libs/xpertthumb.php';
        }

        $xt = new XpertThumb($path);

        $image_info = pathinfo($path);
        $image_size = getimagesize($path);

        $cache_path = JPATH_ROOT. '/cache/' . $module->module;

        // create cache folder if not exist
        JFolder::create($cache_path, 0755);

        if(! $append )
        {
            $append = '_resized';
        }

        $name = md5( $image_info['basename'].$dimensions['width'].$dimensions['height']) . $append;

        $newpath = $cache_path . '/' . $name . '.' . $image_info['extension'];

        $image_uri = JURI::base(true). '/cache/' . $module->module . '/'  . $name . '.' . $image_info['extension'];

        if( $image_info['extension'] == 'png' )
        {
            $type = 3;

        }elseif($image_info['extension'] == 'gif')
        {
            $type = 1;

        }else{

            $type = 2;
        }

        if(!file_exists($newpath))
        {
            $xt->resize( $dimensions['width'], $dimensions['height'] , true, 1 )
                ->toFile( $newpath, $type );
        }
        return $image_uri;
    }

    public static function loadStyleSheet($module, $params)
    {
        $doc        = JFactory::getDocument();
        $app        = JApplication::getInstance('site', array(), 'J');
        $template   = $app->getTemplate();
        $mod_url    = 'modules/' . $module->module . '/assets/css';
        $tmpl_url   = 'templates/' . $template . '/css';
        $ext        = '.css';

        // If sub style is available then we'll check for that css file
        // Otherwise we'll convert module name for the css file

        if( $params->get('style') ) // Legacy for J2.5.x will not work on J3
        {
            $name = $params->get('style');

        }elseif( $params->get('mod_style') ){ // This checking for dumb J3 filed name. J3 has style field name so we need to check for alternative!
            $name = $params->get('mod_style');

        }else{
            $name = str_replace('mod_', '', $module->module);
        }

        $css_file = $name . $ext;

        if( file_exists($tmpl_url . '/' . $css_file) )
        {
            $doc->addStyleSheet(JURI::root(true) . '/'. $tmpl_url . '/' . $css_file);
        }elseif( file_exists($mod_url . '/' . $css_file) )
        {
            $doc->addStyleSheet(JURI::root(true) . '/'. $mod_url . '/' . $css_file);
        }else{
            return; // nothing found so return null
        }
    }

    public static function debug(Array $arr)
    {
        echo "<pre>";
            print_r($arr);
        echo "</pre>";
    }
}
