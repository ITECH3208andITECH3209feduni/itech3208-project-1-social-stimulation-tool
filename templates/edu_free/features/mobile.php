<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

/**
 * Helix Ultimate Mobile Header layout.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureMobile
{
	/**
	 * Template parameters
	 *
	 * @var		object	$params		The parameters object
	 * @since	1.0.0
	 */
	private $params;
	public $position;
	public $load_pos;

	/**
	 * Constructor function
	 *
	 * @param	object	$params		The template parameters
	 *
	 * @since	1.0.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
		$this->position = 'mobile';
	}

	/**
	 * Render the mobile features
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{
		$mobile_sticky_init = [];
		$doc = Factory::getDocument();
		$params = $this->params; // Cache params for reuse

		// Get configuration parameters
		$mobile_breakpoint_options = $params->get('mobile_breakpoint_options', 'm');
		$mobile_sticky = $params->get('mobile_navbar_options', 0);
		$mobile_logo_pos = $params->get('mobile_logo_options', 'center');
		$remove_logo_padding = $params->get('mobile_logo_padding_remove', 0);
		$remove_logo_padding_cls = (($mobile_logo_pos === 'left' || $mobile_logo_pos === 'right') && $remove_logo_padding) ? ' uk-padding-remove-' . $mobile_logo_pos : '';

		$mobile_toggle = $params->get('mobile_toggle_options', 'left');
		$mobile_toggle_text = $params->get('mobile_toggle_text', 0);
		$mobile_animations = $params->get('mobile_animations', 'offcanvas');
		$mobile_offcanvas_mode = $params->get('mobile_offcanvas_mode', 'slide');
		$mobile_drobar_layout = $params->get('mobile_drobar_layout', 'top');
		$mobile_dropbar_animation = $params->get('mobile_dropbar_animation', '');

		$mobile_menu_center_vertical = $params->get('mobile_menu_center_vertical', 0);
		$mobile_center_text = $params->get('mobile_center_horizontally', 0);
		$mobile_center_text_cls = $mobile_center_text ? ' uk-text-center' : '';

		$mobile_menu_style = $params->get('mobile_menu_options', '');

		$menu_style_cls = empty($mobile_menu_style) ? 'default' : 'primary';
		$menu_style_cls .= $params->get('mobile_center_horizontally', 0) ? ' uk-nav-center' : '';

		$menuType = $params->get('offcanvas_menu', 'mainmenu', 'STRING');
		$maxLevel = $params->get('offcanvas_max_level', 0, 'INT');

		$menuModule = Helper::createModule('mod_menu', [
			'title' => 'Main Menu',
			'params' => '{"menutype":"' . $menuType . '","base":"","startLevel":"1","endLevel":"' . $maxLevel . '","showAllChildren":"1","tag_id":"","class_sfx":"uk-nav uk-nav-' . $menu_style_cls . ($maxLevel != 1 ? ' uk-nav-accordion' : '') . '","window_open":"","layout":"_:nav","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0", "hu_offcanvas": 1}',
			'name' => 'menu'
		]);

		$menuCanvasModule = Helper::createModule('mod_menu', [
			'title' => 'Main Menu',
			'params' => '{"menutype":"' . $menuType . '","base":"","startLevel":1,"endLevel":' . $maxLevel . ',"showAllChildren":1,"tag_id":"","class_sfx":"uk-nav uk-nav-' . $menu_style_cls . '","window_open":"","layout":"_:canvas","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0", "hu_offcanvas": 1}',
			'name' => 'menu'
		]);

		$flex_cls = $mobile_menu_center_vertical ? ' uk-flex' : '';

		$mobile_menu_offcanvas_right = $params->get('mobile_menu_offcanvas_right', 0);
		$offcanvas_flip = $mobile_menu_offcanvas_right ? ' flip: true;' : '';

		if ($mobile_sticky) {
			$mobile_sticky_init[] = 'cls-active: uk-navbar-sticky';
			$mobile_sticky_init[] = 'sel-target: .uk-navbar-container';
			if ('2' === $mobile_sticky) {
				$mobile_sticky_init[] = 'show-on-up: true';
				$mobile_sticky_init[] = 'animation: uk-animation-slide-top';
			}
		}

		$hasLogoModules = !empty(ModuleHelper::getModules('logo-mobile'));
		$logo_mobile = $params->get('mobile_logo') || $params->get('logo_image') || $params->get('logo_text') || $hasLogoModules;

		$logo = $params->get('logo_text');
		$sitename = Factory::getApplication()->get('sitename');
		$altText = $params->get('logo_alt', $sitename);

		$logo_type = $params->get('logo_type');

		if ($logo_type === 'image') {
			if ($params->get('mobile_logo')) {
				$logo = '<img class="tm-logo" src="' . $params->get('mobile_logo') . '" alt="' . strip_tags($altText) . '" />';
			} elseif ($params->get('logo_image')) {
				$logo = '<img class="tm-logo" src="' . $params->get('logo_image') . '" alt="' . strip_tags($altText) . '" />';
			}
		}

		$logo_init = '';

		if ($logo) {
			$logo_init .= '<a class="uk-navbar-item' . $remove_logo_padding_cls . ' uk-logo" href="' . Uri::base(true) . '/">';
			$logo_init .= $logo;
			$logo_init .= '</a>';
		}

		$mobile_sticky_init = ' uk-sticky="' . implode('; ', array_filter($mobile_sticky_init)) . '"';

		$hasHeaderModules = !empty(ModuleHelper::getModules('header-mobile'));

		$output = '';

		$output .= '<div class="tm-header-mobile uk-hidden@' . $mobile_breakpoint_options . '">';

		if ($mobile_sticky) {
			$output .= '<div' . $mobile_sticky_init . '>';
		}

		$output .= '<div class="uk-navbar-container">';
		$output .= '<div class="container">';
		$output .= '<nav class="uk-navbar" uk-navbar>';

		if ($hasHeaderModules && $mobile_logo_pos === 'center' && $mobile_toggle === 'right') {
			$output .= '<div class="uk-navbar-left">';
			$output .= '<jdoc:include type="modules" name="header-mobile" style="warp_xhtml" />';
			$output .= '</div>';
		}

		if ($mobile_logo_pos === 'left' || $mobile_toggle === 'left') {
			$output .= '<div class="uk-navbar-left">';

			if ($mobile_logo_pos === 'left' && $logo_mobile) {
				$output .= $logo_init;
				$output .= '<jdoc:include type="modules" name="logo-mobile" style="warp_xhtml" />';
			}

			if ($mobile_toggle === 'left') {
				$output .= '<a class="uk-navbar-toggle uk-navbar-toggle-animate" href="#tm-mobile" uk-toggle="animation: uk-animation-fade">';
				$output .= '<span uk-navbar-toggle-icon></span>';

				if ($mobile_toggle_text) {
					$output .= '<span class="uk-margin-small-left">' . Text::_('TPL_HELIX_ULTIMATE_MENU') . '</span>';
				}

				$output .= '</a>';
			}

			if ($hasHeaderModules && $mobile_logo_pos === 'right') {
				$output .= '<jdoc:include type="modules" name="header-mobile" style="warp_xhtml" />';
			}
			$output .= '</div>';
		}

		if ($mobile_logo_pos === 'center' && $logo_mobile) {
			$output .= '<div class="uk-navbar-center">';
			$output .= $logo_init;
			$output .= '<jdoc:include type="modules" name="logo-mobile" style="warp_xhtml" />';
			$output .= '</div>';
		}

		if ($mobile_logo_pos === 'right' || $mobile_toggle === 'right') {
			$output .= '<div class="uk-navbar-right">';

			if ($hasHeaderModules && $mobile_logo_pos === 'left') {
				$output .= '<jdoc:include type="modules" name="header-mobile" style="warp_xhtml" />';
			}

			if ($mobile_toggle === 'right') {
				$output .= '<a class="uk-navbar-toggle uk-navbar-toggle-animate" href="#" uk-toggle="target: #tm-mobile;">';
				$output .= '<div uk-navbar-toggle-icon></div>';
				if ($mobile_toggle_text) {
					$output .= '<span class="uk-margin-small-left">' . Text::_('TPL_HELIX_ULTIMATE_MENU') . '</span>';
				}
				$output .= '</a>';
			}

			if ($mobile_logo_pos === 'right' && $logo_mobile) {
				$output .= $logo_init;
				$output .= '<jdoc:include type="modules" name="logo-mobile" style="warp_xhtml" />';
			}

			$output .= '</div>';
		}

		if ($hasHeaderModules && (($mobile_logo_pos === 'center' && $mobile_toggle === 'left') || ($mobile_logo_pos === 'left' && $mobile_toggle === 'left'))) {
			$output .= '<div class="uk-navbar-right">';
			$output .= '<jdoc:include type="modules" name="header-mobile" style="warp_xhtml" />';
			$output .= '</div>';
		}

		$output .= '</div>';
		$output .= '</nav>';
		$output .= '</div>';

		if ($mobile_animations === 'dropdown') {
			$dropbarPos = $mobile_dropbar_animation === 'slide-right' ? 'bottom-right' : 'bottom-left';
			$dropbarAnimAttr = $mobile_dropbar_animation ? ' animation: ' . $mobile_dropbar_animation . ';' : '';

			$output .= '<div id="tm-mobile" class="uk-dropbar uk-dropbar-large uk-dropbar-top" uk-drop="clsDrop: uk-dropbar; flip: false; container: .tm-header-mobile; target-y: .tm-header-mobile .uk-navbar-container; mode: click; target-x: .tm-header-mobile .uk-navbar-container; stretch: true; pos: ' . $dropbarPos . '; bgScroll: false;' . $dropbarAnimAttr . ' animateOut: true; duration: 300; toggle: false">';
			$output .= '<div class="tm-height-min-1-1 uk-flex uk-flex-column' . $mobile_center_text_cls . '">';

			if ($mobile_drobar_layout === 'center') {
				$output .= '<div class="uk-margin-auto-vertical">';
			}
			$output .= '<div class="container">';
			$output .= '<div class="uk-child-width-1-1 uk-grid" uk-grid>';
			$output .= '<div>';
			$output .= ModuleHelper::renderModule($menuModule);
			$output .= '</div>';
			$output .= '<jdoc:include type="modules" name="mobile" style="sp_xhtml" />';
			$output .= '</div>';

			if ($mobile_drobar_layout === 'center') {
				$output .= '</div>';
			}

			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		}

		if ($mobile_sticky) {
			$output .= '</div>';
		}

		if ($mobile_animations === 'offcanvas') {
			$output .= '<div id="tm-mobile" class="uk-offcanvas" uk-offcanvas="mode:' . $mobile_offcanvas_mode . ';' . $offcanvas_flip . ' overlay: true">';
			$output .= '<div class="uk-offcanvas-bar' . $mobile_center_text_cls . $flex_cls . '">';
			$output .= '<button class="uk-offcanvas-close" type="button" uk-close></button>';

			if ($mobile_menu_center_vertical) {
				$output .= '<div class="uk-margin-auto-vertical uk-width-1-1">';
			}

			$output .= '<div class="uk-child-width-1-1 uk-grid" uk-grid>';
			$output .= '<div>';
			$output .= ModuleHelper::renderModule($menuModule);
			$output .= '</div>';
			$output .= '<jdoc:include type="modules" name="mobile" style="sp_xhtml" />';
			$output .= '</div>';

			if ($mobile_menu_center_vertical) {
				$output .= '</div>';
			}

			$output .= '</div>';
			$output .= '</div>';
		}

		if ($mobile_animations === 'canvas') {
			$output .= '<div id="tm-mobile" class="uk-offcanvas" uk-offcanvas="mode:' . $mobile_offcanvas_mode . ';' . $offcanvas_flip . ' overlay: true">';
			$output .= '<div class="uk-offcanvas-bar' . $mobile_center_text_cls . $flex_cls . '">';
			$output .= '<button class="uk-offcanvas-close" type="button" uk-close></button>';

			if ($mobile_menu_center_vertical) {
				$output .= '<div class="uk-margin-auto-vertical uk-width-1-1">';
			}

			$output .= '<div class="uk-child-width-1-1 offcanvas-menu uk-grid" uk-grid>';
			$output .= '<div class="offcanvas-inner">';
			$output .= ModuleHelper::renderModule($menuCanvasModule);
			$output .= '</div>';
			$output .= '<jdoc:include type="modules" name="mobile" style="sp_xhtml" />';
			$output .= '</div>';

			if ($mobile_menu_center_vertical) {
				$output .= '</div>';
			}

			$output .= '</div>';
			$output .= '</div>';
		}

		if ($mobile_animations === 'modal') {
			$output .= '<div id="tm-mobile" class="uk-modal-full" uk-modal>';
			$output .= '<div class="uk-modal-dialog uk-modal-body' . $mobile_center_text_cls . $flex_cls . ' uk-height-viewport">';
			$output .= '<button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>';

			if ($mobile_menu_center_vertical) {
				$output .= '<div class="uk-margin-auto-vertical uk-width-1-1">';
			}

			$output .= '<div class="uk-child-width-1-1 uk-grid" uk-grid>';
			$output .= '<div>';
			$output .= ModuleHelper::renderModule($menuModule);
			$output .= '</div>';
			$output .= '<jdoc:include type="modules" name="mobile" style="sp_xhtml" />';
			$output .= '</div>';

			if ($mobile_menu_center_vertical) {
				$output .= '</div>';
			}

			$output .= '</div>';
			$output .= '</div>';
		}

		$output .= '</div>';
		return $output;
	}
}