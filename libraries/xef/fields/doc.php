<?php
/**
 * @package Expose
 * @subpackage Xpert Contents
 * @version 2.5
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined( '_JEXEC' ) or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldDoc extends JFormField{

    protected $type = 'Doc';


    protected function getInput(){

        $title      = $this->element['title'] ? $this->element['title'] : FALSE;
        $msg        = $this->element['message'] ? $this->element['message'] : FALSE;
        $support    = $this->element['support'] ? $this->element['support'] : FALSE;
        $link       = $this->element['link'] ? $this->element['link'] : FALSE;
        $html       = '';


        if( ($title OR $msg) AND !$support)
        {
            $html .= '<div class="alert">';
                $html .= ($title) ? '<h4>' . JText::_($title) . '</h4>' : '';
                $html .= ($msg) ? '<p>' . JText::_($msg) . '</p>' : '';
            $html .= '</div>';
        }

        if($support)
        {
            $html .= '<div class="alert">';
                $html .= ($title) ? '<h4>' . JText::_($title) . '</h4>' : '';
                $html .= ($msg) ? '<p>' . JText::_($msg) . '</p>' : '';
            $html .= '</div>';
        }

        if($link)
        {
            $html .= '
                <h3>Follow us!</h3>
                <p> <strong>Facebook</strong> : <a href="http://www.facebook.com/ThemeXpert" target="_blank">ThemeXpert</a></p>
                <p> <strong>Twitter</strong> : <a href="http://www.twitter.com/ThemeXpert" target="_blank">@themexpert</a></p>
            ';
        }



        return $html;
    }

    protected function getLabel(){
        return ;
    }
}
