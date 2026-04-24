<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Helper\ModuleHelper;

/**
 * Helix ultimate custom search features
 *
 * @since   1.0.0
 */
class HelixUltimateFeatureSearch
{
    /**
     * Template parameters
     *
     * @var     object  $params     The parameters object
     * @since   1.0.0
     */
    private $params;
	public $position;
	public $load_pos;
    /**
     * Constructor function
     *
     * @param   object  $params     The template parameters
     *
     * @since   1.0.0
     */
    public function __construct($params)
    {
        $this->params = $params;
        $this->position = $this->params->get('search_position', 'header');
    }
    
    /**
     * Render the contact features
     *
     * @return  string
     * @since   1.0.0
     */
    public function renderFeature()
    {
        $app = Factory::getApplication();
        $searchModule = Helper::getSearchModule();
        $mitemid = (int) $app->get('Itemid', '', 'INT');
        $navbar_search = $this->params->get('search_position');

        $search_style = $this->params->get('search_style');
        $header_container = $this->params->get('header_style');
        $output = '';
        if ($navbar_search != 'hide') {
            if ($search_style == 'modal' && !in_array($header_container, ['style-10', 'style-11', 'style-12', 'style-13', 'style-14', 'style-15', 'style-16', 'style-17'])) {
                $output .= '<a class="uk-search-toggle" href="#search-header-modal-' . $mitemid . '" uk-search-icon uk-toggle aria-label="Search"></a>';
                $output .= '<div id="search-header-modal-' . $mitemid . '" class="uk-modal-full" uk-modal>';
                $output .= '<div class="uk-modal-dialog uk-flex uk-flex-center uk-flex-middle" uk-height-viewport>';
                $output .= '<button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>';
                $output .= '<div class="modal-finder uk-search uk-search-large uk-text-center">';
                $output .= ModuleHelper::renderModule($searchModule, ['style' => 'none']);
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
            } else {
                $output .= ModuleHelper::renderModule($searchModule, ['style' => 'none']);
            }
        }
        return $output;
    }
}
