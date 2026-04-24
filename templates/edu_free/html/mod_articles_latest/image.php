<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

?>
<div class="uk-child-width-1-1 uk-grid-small uk-grid-divider<?php echo $moduleclass_sfx ?? ''; ?>" uk-grid>
    <?php
    foreach ($list as $item) {

        $attrbs = json_decode($item->attribs);
        $images = json_decode($item->images);
        $intro_image = '';

        if (isset($attrbs->spfeatured_image) && $attrbs->spfeatured_image != '') {

            $intro_image = $attrbs->spfeatured_image;
            $basename = basename($intro_image);
            $list_image = JPATH_ROOT . '/' . dirname($intro_image) . '/' . JFile::stripExt($basename) . '_thumbnail.' . JFile::getExt($basename);
            if (file_exists($list_image)) {
                $thumb_image = JURI::root(true) . '/' . dirname($intro_image) . '/' . JFile::stripExt($basename) . '_thumbnail.' . JFile::getExt($basename);
            }
        } elseif (isset($images->image_intro) && !empty($images->image_intro)) {
            $thumb_image = $images->image_intro;
        }
    ?>
        <div>
            <div class="el-item uk-panel uk-margin-remove-first-child">

                <div class="uk-child-width-expand uk-grid-small" uk-grid>
                    <?php if (!empty($thumb_image)) : ?>
                        <div class="uk-width-1-3">
                            <a href="<?php echo $item->link; ?>">
                                <div class="uk-inline-clip uk-transition-toggle">
                                    <img class="el-image uk-transition-scale-up uk-transition-opaque" alt src="<?php echo $thumb_image; ?>">
                                </div>
                            </a>
                        </div>

                    <?php endif; ?>

                    <div class="uk-margin-remove-first-child">
                        <h3 class="el-title uk-h5 uk-margin-top uk-margin-remove-bottom">
                            <a href="<?php echo $item->link; ?>" class="uk-link-reset"><?php echo $item->title; ?></a>
                        </h3>
                        <div class="el-meta uk-text-meta uk-margin-small-top"><?php echo HTMLHelper::_('date', $item->created, 'DATE_FORMAT_LC3'); ?></div>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?>
</div>