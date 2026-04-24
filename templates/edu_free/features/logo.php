<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Helix Ultimate Site Logo.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureLogo
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
		$this->position = 'logo';
	}

	/**
	 * Render the logo features.
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{
	$doc = Factory::getDocument();
    $header_style = $this->params->get('header_style');

	$nav_item = !in_array($header_style, ['style-5', 'style-6', 'style-7', 'style-8', 'style-9']) ? 'uk-navbar-item ' : '';

	$logo = $this->params->get('logo_image') || $this->params->get('logo_text');

	$html = '';

	if ($logo) {
		$sitename = Factory::getApplication()->get('sitename');
		$altText = $this->params->get('logo_alt', $sitename);

		$html .= '<a class="'.$nav_item.'uk-logo" href="' . Uri::base(true) . '/">';

		if($this->params->get('logo_image')) {
			$html .= '<img class="tm-logo" src="' . $this->params->get('logo_image') . '" alt="' . strip_tags($altText) . '" />';

			if ($logo_height = $this->params->get('logo_height', ''))
			{
				$logo_height = preg_match("@(px|em|rem|%)$@", $logo_height) ? $logo_height : $logo_height . 'px';

				$logoStyle = '.tm-logo {height:' . $logo_height . ';}';
				$logoStyle .= '.tm-mobile-logo {height:' . $logo_height . ';}';

				$doc->addStyleDeclaration($logoStyle);
			}

			/**
			 * If responsive logo height is provided then add the height
			 * to the media query.
			 */
			if ($logo_height_sm = $this->params->get('logo_height_sm', ''))
			{
				$logo_height_sm = preg_match("@(px|em|rem|%)$@", $logo_height_sm) ? $logo_height_sm : $logo_height_sm . 'px';

				$logoStyleSm = '@media(max-width: 992px) {';
				$logoStyleSm .= '.tm-logo {height: ' . $logo_height_sm . ';}';
				$logoStyleSm .= '.tm-mobile-logo {height: ' . $logo_height_sm . ';}';
				$logoStyleSm .= '}';

				$doc->addStyleDeclaration($logoStyleSm);
			}
			
			if ($logo_height_xs = $this->params->get('logo_height_xs', ''))
			{
				$logo_height_xs = preg_match("@(px|em|rem|%)$@", $logo_height_xs) ? $logo_height_xs : $logo_height_xs . 'px';

				$logoStyleXs = '@media(max-width: 576px) {';
				$logoStyleXs .= '.tm-logo {height: ' . $logo_height_xs . ';}';
				$logoStyleXs .= '.tm-mobile-logo {height: ' . $logo_height_xs . ';}';
				$logoStyleXs .= '}';

				$doc->addStyleDeclaration($logoStyleXs);
			}

		} else {
			$html .= $this->params->get('logo_text');
		}

		$html .= '</a>';

		if ($this->params->get('enabled_logo_tooltip') && $this->params->get('logo_tooltip'))
		{
			$html .= '<div class="uk-child-width-1-1" uk-drop="boundary: .uk-logo">';
			$html .= '<div class="uk-card uk-card-body uk-card-default uk-card-small">';
			$html .= '<div>'.$this->params->get('logo_tooltip').'</div>';
			$html .= '</div>';
			$html .= ' </div>';
		}

	}

	return $html;
	}
}
