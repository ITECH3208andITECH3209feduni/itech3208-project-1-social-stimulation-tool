<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

if (!$list)
{
	return;
}

?>

<div class="uk-slider uk-slider-container" uk-slider="finite:true">

	<div class="uk-position-relative uk-visible-toggle">

		<div class="uk-slider-items uk-grid uk-grid-match">
			<?php for ($i = 0, $n = count($list); $i < $n; $i ++) : ?>
				<?php $item = $list[$i]; ?>
				<div class="el-item uk-width-1-1 uk-width-1-3@m">
					<?php require ModuleHelper::getLayoutPath('mod_articles_news', '_item_tile'); ?>
				</div>
			<?php endfor; ?>
		</div>

		<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
		<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>

	</div>

<ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>

</div>
 