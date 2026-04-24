<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;

$id = '';

if ($tagId = $params->get('tag_id', ''))
{
	$id = ' id="' . $tagId . '"';
}

// The menu class is deprecated. Use nav instead
?>
<ul class="uk-subnav <?php echo $class_sfx; ?>"<?php echo $id; ?>>

<?php foreach ($list as $i => &$item)
{
	$layout = \json_decode($item->getParams()->get('helixultimatemenulayout', '') ?? "");

	if (\json_last_error() !== JSON_ERROR_NONE)
	{
		$layout = '';
	}

	$helixMenuLayout = new Registry($layout);
	$customClass = $helixMenuLayout->get('customclass', '');

	$class = 'item-' . $item->id;

	if (in_array($item->id, $path))
	{
		$class .= ' uk-active';
	}
	elseif ($item->type === 'alias')
	{
		$aliasToId = $item->getParams()->get('aliasoptions');

		if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
		{
			$class .= ' uk-active';
		}
		elseif (in_array($aliasToId, $path))
		{
			$class .= ' alias-parent-active';
		}
	}

	if ($customClass)
	{
		$class .= ' ' . $customClass;
	}

	if ($item->type === 'separator' && ! $item->parent) {
		$class .= ' uk-nav-divider';
	}

	if ($item->type === 'heading' && ! $item->parent) {
		$class .= ' uk-nav-header';
	}
	
	if ($item->parent)
	{
		$class .= ' uk-parent';
	}

	echo '<li class="' . $class . '">';

	switch ($item->type) :
		case 'separator':
		case 'component':
		case 'heading':
		case 'url':
			require ModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
			break;

		default:
			require ModuleHelper::getLayoutPath('mod_menu', 'default_url');
			break;
	endswitch;

	// The next item is deeper.
	if ($item->deeper)
	{
		if ($item->level <= 1) {
			echo '<div class="uk-dropdown" uk-dropdown><ul class="uk-nav uk-nav-default uk-dropdown-nav">';
		} else {
			echo '<ul class="uk-nav-sub">';
		}

		//echo '<div class="uk-dropdown" uk-dropdown><ul class="uk-nav uk-dropdown-nav">';
	}
	// The next item is shallower.
	elseif ($item->shallower)
	{
		echo '</li>';
		//echo str_repeat('</ul></div></li>', $item->level_diff);
		if ($item->level <= 1) {
			echo str_repeat('</ul></div></li>', $item->level_diff);
		} else {
			echo str_repeat('</ul></li>', $item->level_diff);
		}	
	}
	// The next item is on the same level.
	else
	{
		echo '</li>';
	}
}
?></ul>
