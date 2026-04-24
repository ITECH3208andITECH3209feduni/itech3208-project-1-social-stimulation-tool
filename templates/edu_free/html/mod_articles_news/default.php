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

<div class="uk-child-width-1-1 uk-grid-small uk-grid-divider" uk-grid>
	<?php foreach ($list as $item) : ?>
		<div>
			<?php require ModuleHelper::getLayoutPath('mod_articles_news', '_item'); ?>
		</div>
	<?php endforeach; ?>
</div>
