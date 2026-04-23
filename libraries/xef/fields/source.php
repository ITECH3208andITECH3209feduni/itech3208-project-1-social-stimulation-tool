<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// no direct access
defined( '_JEXEC' ) or die('Restricted access');

/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldSource extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Source';

    protected $providers = array();

    protected $provider_status = array();

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
        $html = '';


        $providers = $this->element['providers'] ? $this->element['providers'] : 'joomla';
        $this->providers = explode(',', $providers);

        // Set the provider status
        $this->setProviderStatus();

        // Generate the source specific form
        //$provider_options = $this->loadOptions();

        $list = '';

        $html .= '<a class="btn cs-btn" data-toggle="bsmodal" href="#content-source" ><span>'.JText::_('SELECT_CONTENT_SOURCE_BTN').'</span></a>';

        foreach($this->providers as $provider)
        {
            $status = ($this->provider_status[$provider]['status']) ? 'available' : 'notavailable';
            $msg = $this->provider_status[$provider]['msg'];
            $list .= "<li>
                        <a class=\"$provider $status\" href=\"#\" rel=\"popover\" $msg >
                            <span>$provider</span>
                        </a>
                      </li>";
        }

        $html .=
            '<div class="bsmodal hide fade" id="content-source">
                  <div class="bsmodal-header">
                    <button class="close" data-dismiss="bsmodal">×</button>
                    <h3>'.JText::_('SELECT_CONTENT_SOURCE').'</h3>
                  </div>
                  <div class="bsmodal-body">
                    <ul class="cs-list">
                        '. $list .'
                    </ul>
                  </div>
            </div>';

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		$html .= '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . '/>';

        return $html ;
	}

    public function setProviderStatus()
    {
        // Status 1 = Available, 0 = Not available
        $status = '';
        $msg = '';

        foreach ( $this->providers as $provider )
        {
            switch ($provider){
                case 'joomla' :
                case 'module' :

                    // Joomla is available for all :D
                    $this->provider_status[$provider] = array('status'=> 1, 'msg'=>'');
                    break;

                case 'k2':

                    $path = JPATH_SITE . '/components/com_k2/k2.php';

                    if ( ! file_exists( $path ) )
                    {
                        $status = 0;
                        $msg = 'data-original-title="Not Available!" data-content="K2 Not Found. In order to use the K2 Content type, you will need to download and install it from http://www.getk2.org."';
                    }
                    else
                    {
                        $status = 1;
                        $msg = '';
                    }

                    $this->provider_status[$provider] = array('status'=> $status, 'msg'=> $msg);
                    break;

                case 'easyblog':

                    $path = JPATH_SITE . '/components/com_easyblog/easyblog.php';

                    if( ! file_exists( $path ) )
                    {
                        $status = 0;
                        $msg = "data-original-title=\"Not Available!\" data-content=\"EasyBlog Not Found. In order to use the EasyBlog Content type, you will need to download and install it from http://www.stackideas.com.\"";
                    }
                    else
                    {
                        $status = 1;
                        $msg = '';
                    }
                    $this->provider_status[$provider] = array('status'=> $status, 'msg'=> $msg);

                    break;

                case 'flickr':

                    // If module request for flickr we'll serve it without having any question :)
                    $this->provider_status[$provider] = array('status'=> 1, 'msg'=> '');
                    break;

                case 'folder':

                    // Image folder is must in joomla right? so serve it without any question :)
                    $this->provider_status[$provider] = array('status'=> 1, 'msg'=> '');
                    break;

                case 'dribbble':
                    // This service support is coming real soon :)
                    $this->provider_status[$provider] = array('status'=> 0, 'msg'=> "data-original-title='Not Available!' data-content='Dribbble is not available right now. Coming soon.'");
                    break;

                case 'instagram':
                    // This service support is coming real soon :)
                    $this->provider_status[$provider] = array('status'=> 0, 'msg'=> "data-original-title='Not Available!' data-content='Instagram is not available right now. Coming soon.'");
                    break;

                case 'youtube':
                    // This service support is coming real soon :)
                    $this->provider_status[$provider] = array('status'=> 0, 'msg'=> "data-original-title='Not Available!' data-content='Youtube is not available right now. Coming soon.'");
            }
        }
    }

    public function loadOptions()
    {
        $path = JPATH_LIBRARIES . '/xef/sources/';
        $html = '';


        $dispatcher = JDispatcher::getInstance();
        $dispatcher->register('onContentPrepareForm', 'loadForm');

        //echo "<pre>";print_r($dispatcher);echo "</pre>";

        foreach ( $this->provider_status as $provider => $val )
        {
            // If provider is available
            if($val['status'])
            {
               $option = $path . $provider . '/options.xml';
                if( file_exists($option) )
                {
                    /*//JForm::addFormPath( $path . $provider );
                    $form = JForm::getInstance('com_modules.module');

                    //$form->loadFile('options', false);
                    $xml = JFactory::getXML($option);

                    $form->setFields($xml);

                    $fieldset = $form->getFieldset('joomla');

                    foreach($fieldset as $field)
                    {
                        //var_dump($field);
                        $html .= '<li>' . $field->getLabel() . '</li>';
                        $html .= '<li>' . $field->getInput() . '</li>';
                    }*/

                    //JForm::addFormPath( $path . $provider );
                    // get the JForm object
                    jimport('joomla.form.form');
                    $form = JForm::getInstance('test', JPATH_LIBRARIES.'/xef/sources/joomla/option.xml');;
                    //$form->loadFile($option, false);
                    var_dump($form);

                    /*foreach ($form->getFieldsets('params') as $fieldsets => $fieldset)
                    {
                        foreach($form->getFieldset($fieldset->name) as $field)
                        {

                            // If the field is hidden, only use the input.
                            if ($field->hidden)
                            {
                                echo $field->input;
                            }
                            else{
                                echo "<dt>";
                                    echo $field->label;
                                echo "</dt>";
                                echo "<dd>";
                                    echo $field->input;
                                echo "</dd>";
                            }

                        }
                    }*/



                }else{
                    //echo $provider . 'options.xml file is missing, please re-install. Alternatively, extract the installation archive and copy the xef directory inside your site\'s libraries directory.';
                }
            }
        }

        return $html;

    }
}