<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Helix Ultimate Site Title.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureTitle
{
	/**
	 * Template parameters
	 *
	 * @var		object	$params		The parameters object
	 * @since	1.0.0
	 */
	private $params;
	public $position;

	/**
	 * Constructor function
	 *
	 * @param	object	$params		The template parameters
	 *
	 * @since	1.0.0
	 */
	public function __construct($params)
	{
		$this->position = 'title';
		$this->params = $params;
	}

	/**
	 * Render the logo features.
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{
		$app = Factory::getApplication();
		$menuitem = $app->getMenu()->getActive();
		$params = $this->params; // Cache params for reuse

		if (!$menuitem) {
			return '';
		}

		$menuParams = $menuitem->getParams();

		if (!$menuParams->get('helixultimate_enable_page_title', 0)) {
			return '';
		}

		// Template-level configurations
		$page_title_container = $params->get('page_title_maxwidth', 'default');
		$page_title_container_cls = $page_title_container !== 'default' ? 'uk-container uk-container-' . $page_title_container : 'container';

		$page_title_style = $params->get('page_title_tyle', '');
		$page_title_style_cls = $page_title_style ? ' uk-' . $page_title_style : '';

		$page_title_padding = $params->get('page_title_padding', '');
		$page_title_padding_cls = $page_title_padding ? ' uk-section uk-section-' . $page_title_padding : ' uk-section';

		$page_title_background_size = $params->get('page_title_background_size', '');
		$page_title_background_size_cls = $page_title_background_size ? ' uk-background-' . $page_title_background_size : '';

		$page_title_bg_position = $params->get('page_title_bg_position', 'center-center');

		$page_title_bg_visibility = $params->get('page_title_bg_visibility', '');
		$page_title_bg_visibility_cls = $page_title_bg_visibility ? ' uk-background-image@' . $page_title_bg_visibility : '';

		$page_title_bg_blendmode = $params->get('page_title_bg_blendmode', '');
		$page_title_bg_blendmode_cls = $page_title_bg_blendmode ? ' uk-background-blend-' . $page_title_bg_blendmode : '';

		$page_title_bg_color = $params->get('page_title_bg_color');
		$page_title_bg_color_cls = $page_title_bg_color ? 'background-color: ' . $page_title_bg_color . ';' : '';

		$theme_page_title_background_image = $params->get('page_title_bg_image');

		$page_title_align = $params->get('page_title_align', '');
		$page_title_align_cls = !empty($page_title_align) ? ' uk-text-' . $page_title_align : '';

		$header_style = $params->get('header_style');
		$header_height = $params->get('header_height', '82px');

		// Menu item-level configurations
		$page_title = $menuitem->title;
		$page_heading = $menuParams->get('helixultimate_page_title_heading', 'h2');
		$page_title_alt = $menuParams->get('helixultimate_page_title_alt');
		$page_subtitle = $menuParams->get('helixultimate_page_subtitle');
		$menu_page_title_bg_color = $menuParams->get('helixultimate_page_title_bg_color');
		$menu_page_title_bg_image = $menuParams->get('helixultimate_page_title_bg_image');

		// Determine sub-heading level
		$page_sub_heading = $page_heading === 'h1' ? 'h2' : 'h3';

		// Build inline styles
		$style = '';

		if ($menu_page_title_bg_color) {
			$style .= 'background-color: ' . $menu_page_title_bg_color . ';';
		} elseif ($page_title_bg_color_cls) {
			$style .= $page_title_bg_color_cls;
		}

		if ($menu_page_title_bg_image) {
			$style .= 'background-image: url(' . Uri::root(true) . '/' . $menu_page_title_bg_image . ');';
		} elseif ($theme_page_title_background_image) {
			$style .= 'background-image: url(' . Uri::root(true) . '/' . $theme_page_title_background_image . ');';
		}

		$style_attr = $style ? ' style="' . $style . '"' : '';

		// Use alternative title if set
		if ($page_title_alt) {
			$page_title = $page_title_alt;
		}

		// Determine if background image is set
		$has_bg_image = $menu_page_title_bg_image || $theme_page_title_background_image;

		// Build output
		$output = '';

		if ($has_bg_image) {
			$output .= '<div class="sp-page-title uk-background-norepeat' . $page_title_padding_cls . $page_title_style_cls . $page_title_background_size_cls . $page_title_bg_visibility_cls . $page_title_bg_blendmode_cls . ' uk-background-' . $page_title_bg_position . '"' . $style_attr . '>';
		} else {
			$output .= '<div class="sp-page-title' . $page_title_padding_cls . $page_title_style_cls . '"' . $style_attr . '>';
		}

		if ($header_style === 'style-18') {
			$output .= '<div class="tm-header-placeholder uk-margin-remove-adjacent uk-visible@m" style="height: ' . $header_height . ';"></div>';
		}

		$output .= '<div class="' . $page_title_container_cls . $page_title_align_cls . '">';
		$output .= '<' . $page_heading . ' class="uk-heading-primary">' . $page_title . '</' . $page_heading . '>';

		if ($page_subtitle) {
			$output .= '<' . $page_sub_heading . ' class="uk-text-large uk-margin-remove-top">' . $page_subtitle . '</' . $page_sub_heading . '>';
		}

		$output .= '<jdoc:include type="modules" name="breadcrumb" style="none" />';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}
}