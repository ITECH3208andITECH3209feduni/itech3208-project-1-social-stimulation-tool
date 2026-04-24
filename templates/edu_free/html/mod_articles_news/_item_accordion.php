<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;

?>

<a class="uk-accordion-title" href>
	<?php echo $item->title; ?>
	<span uk-accordion-icon></span>
</a>

<div class="uk-accordion-content">

	<?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?>
		<a href="<?php echo $item->link; ?>" aria-label="<?php echo $item->title; ?>">
			<div class="uk-inline-clip uk-transition-toggle" tabindex="0">
				<?php echo LayoutHelper::render(
					'joomla.html.image',
					[
						'src' => $item->imageSrc,
						'alt' => $item->imageAlt,
						'class'	=> 'uk-transition-scale-up uk-transition-opaque',
					]
				); ?>
			</div>
		</a>
	<?php endif; ?>

	<?php if (!$params->get('intro_only')) : ?>
		<?php echo $item->afterDisplayTitle; ?>
	<?php endif; ?>

	<?php echo $item->beforeDisplayContent; ?>

	<?php if ($params->get('show_introtext', 1)) : ?>
		<div class="el-content uk-panel<?php if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc)) : ?> uk-margin-top<?php endif; ?>">
			<?php echo substr(strip_tags($item->introtext), 0, 100) . '...'; ?>
		</div>
	<?php endif; ?>

	<?php echo $item->afterDisplayContent; ?>

	<?php if (isset($item->link) && $item->readmore != 0 && $params->get('readmore')) : ?>
		<?php echo '<p class="uk-margin"><a class="uk-button uk-button-text" href="' . $item->link . '">' .Text::_('HELIX_ULTIMATE_READMORE_TEXT'). '</a></p>'; ?>
	<?php endif; ?>

</div>