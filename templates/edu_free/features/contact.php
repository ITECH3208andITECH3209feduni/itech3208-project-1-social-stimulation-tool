<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

/**
 * Helix Ultimate contact information.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureContact
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
		$this->position = $this->params->get('contact_pos', 'toolbar-left');
	}

	/**
	 * Render the contact features
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{
		$conditions = $this->params->get('contact_pos') && ($this->params->get('contact_location') || $this->params->get('contact_phone') || $this->params->get('contact_email') || $this->params->get('contact_time') || $this->params->get('contact_custom'));

		if($conditions)
		{
			$output = '<div class="tm-contact-info uk-grid-small uk-flex-middle uk-grid" uk-grid>';

			if($this->params->get('contact_location'))
			{
				$output .= '<div class="sp-contact-location"><span class="uk-margin-small-right fas fa-map-marker-alt" aria-hidden="true"></span>' . $this->params->get('contact_location') . '</div>';
			}

			if($this->params->get('contact_phone'))
			{
				$output .= '<div class="sp-contact-phone"><a class="uk-link-reset" href="tel:' . str_replace(array(')','(',' ','-'),array('','','',''), $this->params->get('contact_phone')) . '"><span class="uk-margin-small-right fas fa-phone" aria-hidden="true"></span>' . $this->params->get('contact_phone') . '</a></div>';
			}

			if($this->params->get('contact_email'))
			{
				$output .= '<div class="sp-contact-email"><a class="uk-link-reset" href="mailto:'. $this->params->get('contact_email') .'"><span class="uk-margin-small-right far fa-envelope" aria-hidden="true"></span>' . $this->params->get('contact_email') . '</a></div>';
			}

			if($this->params->get('contact_time'))
			{
				$output .= '<div class="sp-contact-time"><span class="uk-margin-small-right far fa-clock" aria-hidden="true"></span>' . $this->params->get('contact_time') . '</div>';
			}

			if($this->params->get('contact_custom'))
			{
				$output .= '<div class="sp-contact-custom">' . $this->params->get('contact_custom') . '</div>';
			}

			$output .= '</div>';

			return $output;
		}

	}
}
