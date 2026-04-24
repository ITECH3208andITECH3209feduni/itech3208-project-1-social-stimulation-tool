<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;
?>
<ul class="uk-list uk-list-decimal uk-list-divider<?php echo $moduleclass_sfx ?? ''; ?>">
<?php foreach ($list as $item) : ?>
	<li class="el-item">
		<a class="uk-link-heading" href="<?php echo $item->link; ?>">
			<?php echo $item->title; ?>
		</a>
	</li>
<?php endforeach; ?>
</ul>
