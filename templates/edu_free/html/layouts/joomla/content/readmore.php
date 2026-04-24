<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$params    = $displayData['params'];
$item      = $displayData['item'];
$direction = Factory::getLanguage()->isRtl() ? 'left' : 'right';

$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$tmpl_params = $template->params;

$content_center = $tmpl_params->get('blog_center_content');

$button_cls = $tmpl_params->get('leading_blog_list_button', 'text');
$button_margin = $tmpl_params->get('leading_blog_list_button_margin', 'default');
$button_margin_cls = $button_margin == 'default' ? 'uk-margin-top' : 'uk-margin-'.$button_margin.'-top';

$button_margin_cls .= $content_center ? ' uk-text-center' : '';
?>

<div class="<?php echo $button_margin_cls; ?>">
	<?php if (!$params->get('access-view')) : ?>
		<a class="uk-button uk-button-<?php echo $button_cls; ?>" href="<?php echo $displayData['link']; ?>" itemprop="url" aria-label="<?php echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?>
			<?php echo htmlspecialchars($item->title ?? "", ENT_QUOTES, 'UTF-8'); ?>">
			<?php echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?>
		</a>
	<?php elseif ($readmore = $item->alternative_readmore) : ?>
		<a class="uk-button uk-button-<?php echo $button_cls; ?>" href="<?php echo $displayData['link']; ?>" itemprop="url" aria-label="<?php echo htmlspecialchars($item->title ?? "", ENT_QUOTES, 'UTF-8'); ?>">
			<?php echo $readmore; ?>
			<?php if ($params->get('show_readmore_title', 0) != 0) : ?>
				<?php echo HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
			<?php endif; ?>

		</a>
	<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
		<a class="uk-button uk-button-<?php echo $button_cls; ?>" href="<?php echo $displayData['link']; ?>" itemprop="url" aria-label="<?php echo Text::_('COM_CONTENT_READ_MORE'); ?> <?php echo htmlspecialchars($item->title ?? "", ENT_QUOTES, 'UTF-8'); ?>">
			<?php echo Text::_('JGLOBAL_READ_MORE'); ?>
		</a>
	<?php else : ?>
		<a class="uk-button uk-button-<?php echo $button_cls; ?>" href="<?php echo $displayData['link']; ?>" itemprop="url" aria-label="<?php echo Text::_('COM_CONTENT_READ_MORE'); ?> <?php echo htmlspecialchars($item->title ?? "", ENT_QUOTES, 'UTF-8'); ?>">
			<?php echo Text::sprintf('COM_CONTENT_READ_MORE_TITLE', HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit'))); ?>
		</a>
	<?php endif; ?>
</div>
