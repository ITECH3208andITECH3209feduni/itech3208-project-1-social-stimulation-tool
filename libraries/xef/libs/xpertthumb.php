<?php
/**
 * @package Xpert Thumb
 * @subpackage Xpert Slider
 * @version 1.0
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

jimport('joomla.image.image');

/**
 *
 * Xpert thumb extended Jimage only for adding support JPG file type
 *
 **/

class XpertThumb extends JImage
{
    /**
     * Class constructor.
     *
     * @param   mixed  $source  Either a file path for a source image or a GD resource handler for an image.
     *
     * @since   11.3
     * @throws  RuntimeException
     */
    public function __construct($source = null)
    {

        // Determine which image types are supported by GD, but only once.
        if (!isset(self::$formats[IMAGETYPE_JPEG]))
        {
            $info = gd_info();
            self::$formats[IMAGETYPE_JPEG] = ($info['JPEG Support']) ? true : ($info['JPG Support']) ? true :false;
            self::$formats[IMAGETYPE_PNG] = ($info['PNG Support']) ? true : false;
            self::$formats[IMAGETYPE_GIF] = ($info['GIF Read Support']) ? true : false;
        }
        parent::__construct($source);
    }
}