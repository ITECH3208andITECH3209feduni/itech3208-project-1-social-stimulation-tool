<?php
/**
 * @package Xpert Slider
 * @version 1.1
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die('Restricted access');


if( file_exists(JPATH_SITE . '/components/com_easyblog/easyblog.php' ) )
{
    jimport('joomla.html.html');
    jimport('joomla.form.formfield');

    class JFormFieldEZBCategories extends JFormField
    {

        protected $type = 'EZBCategories';

        protected function getInput()
        {
            $mainframe	= JFactory::getApplication();
            $doc 		= JFactory::getDocument();

            require_once( JPATH_ROOT . '/components/com_easyblog/constants.php' );

            $options 		= array();
            $attr 	 		= '';
            $categoryList	= array();

            // Initialize some field attributes.
            $attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="inputbox chzn-select"';

            // To avoid user's confusion, readonly="true" should imply disabled="true".
            if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
                $attr .= ' disabled="disabled"';
            }

            $attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
            $attr .= $this->multiple ? ' multiple="multiple"' : '';

            // Initialize JavaScript field attributes.
            $attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
            $attr .= 'multiple="multiple"';
            $attr .= 'style="width:280px;"';
            $attr .= 'data-placeholder="Click here to select categories"';

            require_once( JPATH_ROOT . '/administrator/components/com_easyblog/models/categories.php' );
            $model		= new EasyBlogModelCategories();
            $categories	= $model->getAllCategories();

            if(count($categories) > 0)
            {
                $optgroup = JHTML::_('select.optgroup','Select category','id','title');
                array_push($categoryList,$optgroup);

                foreach ($categories as $row) {
                    $opt    = new stdClass();
                    $opt->id    = $row->id;
                    $opt->title = $row->title;

                    array_push($categoryList,$opt);
                }
            }

            $html = JHTML::_('select.genericlist',  $categoryList, $this->name, trim($attr), 'id', 'title', $this->value );
            return $html;
        }
    }
}
