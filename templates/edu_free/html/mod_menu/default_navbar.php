<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
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

<ul class="uk-nav uk-nav-default">

<?php foreach ($list as $i => &$item)
{
	$layout = \json_decode($item->getParams()->get('helixultimatemenulayout', ''));

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
		echo '<ul class="uk-nav-sub">';
		
	}
	// The next item is shallower.
	elseif ($item->shallower)
	{
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
		
	}
	// The next item is on the same level.
	else
	{
		echo '</li>';
	}
}
?></ul>
