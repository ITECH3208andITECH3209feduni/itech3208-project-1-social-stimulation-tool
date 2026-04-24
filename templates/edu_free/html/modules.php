<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

use Joomla\CMS\Factory;

defined('_JEXEC') or die();
 
function modChrome_sp_xhtml($module, $params, $attribs)
{
	$moduleTag     = htmlspecialchars($params->get('module_tag', 'div') ?? "", ENT_QUOTES, 'UTF-8');
	$bootstrapSize = (int) $params->get('bootstrap_size', 0);
	$moduleClass   = $bootstrapSize !== 0 ? ' span' . $bootstrapSize : '';
	$headerTag     = htmlspecialchars($params->get('header_tag', 'h3') ?? "", ENT_QUOTES, 'UTF-8');
	$headerClass   = htmlspecialchars($params->get('header_class', 'uk-card-title') ?? "", ENT_COMPAT, 'UTF-8');

	if ($module->content) {
		echo '<' . $moduleTag . ' class="uk-panel ' . htmlspecialchars($params->get('moduleclass_sfx') ?? "", ENT_COMPAT, 'UTF-8') . $moduleClass . '">';
		if ($module->showtitle) {
			echo '<' . $headerTag . ' class="' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
		}
		echo '<div class="uk-panel">';
		echo $module->content;
		echo '</div>';
		echo '</' . $moduleTag . '>';
	}
}

function modChrome_navbar_xhtml($module, $params, $attribs)
{
	$moduleTag     = htmlspecialchars($params->get('module_tag', 'div') ?? "", ENT_QUOTES, 'UTF-8');
	$bootstrapSize = (int) $params->get('bootstrap_size', 0);
	$moduleClass   = $bootstrapSize !== 0 ? ' span' . $bootstrapSize : '';
	if ($module->content) {
		echo '<' . $moduleTag . ' class="uk-panel' . htmlspecialchars($params->get('moduleclass_sfx') ?? "", ENT_COMPAT, 'UTF-8') . $moduleClass . '">';
		echo $module->content;
		echo '</' . $moduleTag . '>';
	}
}

/*
 * Module chrome for rendering the module in a mobile toggle
 */
function modChrome_grid_stack($module, $params, $attribs)
{
	$moduleTag     = htmlspecialchars($params->get('module_tag', 'div') ?? "", ENT_QUOTES, 'UTF-8');
	$headerTag     = htmlspecialchars($params->get('header_tag', 'h3') ?? "", ENT_QUOTES, 'UTF-8');
	$headerClass   = htmlspecialchars($params->get('header_class', 'uk-card-title') ?? "", ENT_COMPAT, 'UTF-8');
	
	if ($module->content) {
		echo '<div>';
		echo '<' . $moduleTag . ' class="uk-panel" id="module-' . $module->id . '">';
		if ($module->showtitle) {
			echo '<' . $headerTag . ' class="' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
		}
		echo $module->content;
		echo '</' . $moduleTag . '>';
		echo '</div>';
	}
}

/*
 * Module chrome for rendering the module in a toolbar position
 */
function modChrome_toolbar_stack($module, $params, $attribs)
{
	$moduleTag     = htmlspecialchars($params->get('module_tag', 'div') ?? "", ENT_QUOTES, 'UTF-8');
	if ($module->content) {
		echo '<div>';
		echo '<' . $moduleTag . ' class="uk-panel" id="module-' . $module->id . '">';
		echo $module->content;
		echo '</' . $moduleTag . '>';
		echo '</div>';
	}
}

/*
 * Module chrome for rendering the module in a navbar position
 */
function modChrome_warp_xhtml($module, $params, $attribs)
{
	$moduleTag     = htmlspecialchars($params->get('module_tag', 'div') ?? "", ENT_QUOTES, 'UTF-8');

	if ($module->content) {
		echo '<' . $moduleTag . ' class="uk-navbar-item" id="module-' . $module->id . '">';
		echo $module->content;
		echo '</' . $moduleTag . '>';
	}
}

/*
 * Module chrome for rendering the module in a header position
 */
function modChrome_header_xhtml($module, &$params, &$attribs)
{
	$moduleTag     = htmlspecialchars($params->get('module_tag', 'div') ?? "", ENT_QUOTES, 'UTF-8');
	if ($module->content)
	{
		echo '<' . $moduleTag . ' id="module-' . $module->id . '">';
			echo $module->content;
		echo '</' . $moduleTag . '>';
	}
}

function modChrome_offcanvas_xhtml($module, $params, $attribs)
{
	$moduleTag     = htmlspecialchars($params->get('module_tag', 'div') ?? "", ENT_QUOTES, 'UTF-8');
	$headerTag     = htmlspecialchars($params->get('header_tag', 'h3') ?? "", ENT_QUOTES, 'UTF-8');
	$headerClass   = htmlspecialchars($params->get('header_class', 'uk-card-title') ?? "", ENT_COMPAT, 'UTF-8');
	$tmpl_params   = Factory::getApplication()->getTemplate(true)->params;
	$header_style = $tmpl_params->get('header_style');

	if ($module->content) {
		echo '<' . $moduleTag . ' class="uk-margin-top" id="module-' . $module->id . '">';

		if ( ! in_array( $header_style, array( 'style-10', 'style-11', 'style-12', 'style-13', 'style-14', 'style-15', 'style-16','style-17' ), true ) ) {
			if ($module->showtitle) {
				echo '<' . $headerTag . ' class="' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
			}
		}
		echo $module->content;
		echo '</' . $moduleTag . '>';
	}
}