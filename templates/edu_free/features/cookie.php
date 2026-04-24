<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

class HelixUltimateFeatureCookie
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
		$this->position = 'cookie_banner';
	}

	/**
	 * Render the contact features
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{
		$app = Factory::getApplication();
		$cookie = $app->input->cookie;
		$params = $this->params; // Cache params for reuse

		// Early return if cookie already accepted or banner not enabled
		if ($cookie->get('cookieAllowed') === 'true' || !$params->get('enabled_cookie_banner')) {
			return '';
		}

		// Check if there's content to display
		if (!$params->get('cookie_content')) {
			return '';
		}

		$cookie_type = $params->get('cookie_type', 'notification');
		$cookie_position = $params->get('cookie_position', 'bottom-left');
		$cookie_style = $params->get('cookie_style', '');
		$cookie_style_cls = $cookie_style ? ' uk-notification-message-' . $cookie_style : '';
		$cookie_button = $params->get('cookie_button', '');
		$cookie_button_text = $params->get('cookie_button_text');

		$cookie_bar_position = $params->get('cookie_bar_position', 'bottom');
		$cookie_bar_position_cls = $cookie_bar_position === 'bottom' ? ' uk-position-bottom uk-position-fixed uk-position-z-index' : ' uk-position-relative';
		$cookie_bar_style = $params->get('cookie_bar_style', 'muted');

		$output = '';

		if ($cookie_type === 'notification') {
			$output .= '<div class="uk-notification uk-notification-' . $cookie_position . '">';
			$output .= '<div class="uk-notification-message' . $cookie_style_cls . ' uk-panel">';
			$output .= '<div>' . $params->get('cookie_content') . '</div>';

			if (empty($cookie_button)) {
				$output .= '<button type="button" class="js-accept uk-notification-close uk-close uk-icon" data-uk-close="" data-uk-toggle="target: !.uk-notification; animation: uk-animation-fade"></button>';
			} else {
				$output .= '<p class="uk-margin"><button type="button" class="js-accept uk-button uk-button-' . $cookie_button . ' uk-width-1-1" data-uk-toggle="target: !.uk-notification; animation: uk-animation-fade">' . $cookie_button_text . '</button></p>';
			}

			$output .= '</div>';
			$output .= '</div>';
		} else {
			$output .= '<div class="tm-cookie-bar uk-section uk-section-xsmall uk-section-' . $cookie_bar_style . $cookie_bar_position_cls . ' uk-position-z-index">';
			$output .= '<div class="uk-container uk-container-expand uk-text-center">';
			$output .= $params->get('cookie_content');

			if (empty($cookie_button)) {
				$output .= '<button type="button" class="js-accept uk-position-center-right uk-position-medium" data-uk-close data-uk-toggle="target: !.uk-section; animation: uk-animation-fade"></button>';
			} else {
				$output .= '<button type="button" class="js-accept uk-button uk-button-' . $cookie_button . ' uk-margin-small-left" data-uk-toggle="target: !.uk-section; animation: uk-animation-fade">' . $cookie_button_text . '</button>';
			}

			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}
}