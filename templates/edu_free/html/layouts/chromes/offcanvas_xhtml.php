<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 *
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use HelixUltimate\Framework\Platform\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

if ($module->content === null || $module->content === '')
{
	return;
}

$moduleTag     = htmlspecialchars($params->get('module_tag', 'div'), ENT_QUOTES, 'UTF-8');
$bootstrapSize = (int) $params->get('bootstrap_size', 0);
$moduleClass   = $bootstrapSize !== 0 ? ' span' . $bootstrapSize : '';
$headerTag     = htmlspecialchars($params->get('header_tag', 'h3') ?? "", ENT_QUOTES, 'UTF-8');
$headerClass   = htmlspecialchars($params->get('header_class', 'uk-card-title') ?? "", ENT_COMPAT, 'UTF-8');
$moduleClassSfx = Helper::CheckNull($params->get('moduleclass_sfx'));
$encodedModuleClassSfx = is_string($moduleClassSfx) ? htmlspecialchars($moduleClassSfx, ENT_COMPAT, 'UTF-8') : '';

$tmpl_params   = Factory::getApplication()->getTemplate(true)->params;
$header_style = $tmpl_params->get('header_style');

if ($module->content) {
	echo '<' . $moduleTag . ' class="uk-margin-top ' . $encodedModuleClassSfx . $moduleClass . '">';
	if ( ! in_array( $header_style, array( 'style-10', 'style-11', 'style-12', 'style-13', 'style-14', 'style-15', 'style-16','style-17' ), true ) ) {
		if ($module->showtitle) {
			echo '<' . $headerTag . ' class="' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
		}
	}
	echo $module->content;
	echo '</' . $moduleTag . '>';
}