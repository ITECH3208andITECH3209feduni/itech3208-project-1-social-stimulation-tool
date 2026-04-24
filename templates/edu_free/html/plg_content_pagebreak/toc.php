<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.pagebreak
 *
 * @copyright   (C) 2025 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

// Only display if we have items in the list
if (empty($list)) {
    return;
}
?>

<div class="uk-card uk-card-default float-end article-index ms-3 mb-3">
    <div class="uk-card-body uk-card-small uk-margin-remove-first-child">
        <?php if (!empty($headingtext)) : ?>
            <h3><?php echo $headingtext; ?></h3>
        <?php endif; ?>

        <ul class="uk-nav uk-nav-default uk-nav-divider">
            <?php foreach ($list as $listItem) : ?>
                <?php $activeClass = $listItem->active ? ' class="uk-active"' : ''; ?>
                <li<?php echo $activeClass; ?>>
                    <a href="<?php echo Route::_($listItem->link); ?>" 
                       class="toclink"
                       title="<?php echo $this->escape($listItem->title); ?>">
                        <?php echo $listItem->title; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>