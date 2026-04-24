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
$num = count($list);
?>
<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-<?php echo ($num <= 4 ? $num : '3' ); ?>@m newsflash-vert-parallax" uk-grid="masonry: true; parallax: 300">
	<?php for ($i = 0, $n = count($list); $i < $n; $i ++) : ?>
		<?php $item = $list[$i]; ?>
		<div>
			<?php require ModuleHelper::getLayoutPath('mod_articles_news', '_item_vertical'); ?>
		</div>
	<?php endfor; ?>
</div>