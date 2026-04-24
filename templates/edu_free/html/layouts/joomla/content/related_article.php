<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Initialize variables
$item = $displayData;
$params = $item->params;
$info = $params->get('info_block_position', 0);

// Parse JSON data safely
$attribs = json_decode($item->attribs ?? '{}');
$images = json_decode($displayData->images ?? '{}');

// Determine article format
$article_format = $attribs->helix_ultimate_article_format ?? 'standard';

// Check for full text image
$full_image = !empty($images->image_fulltext ?? '');

// Determine if media exists
$media = (
    !empty($attribs->helix_ultimate_image ?? '') ||
    $full_image ||
    !empty($attribs->helix_ultimate_audio ?? '') ||
    !empty($attribs->helix_ultimate_gallery_images ?? '') ||
    !empty($attribs->helix_ultimate_video ?? '')
);

// Check if we should show meta information
$useDefList = $params->get('show_publish_date') || $params->get('show_author');

// Initialize meta variables
$author = '';
$published = '';

// Build author information
if ($params->get('show_author')) {
    $author = $item->created_by_alias ?: $item->author;
    if (!empty($item->contact_link) && $params->get('link_author') == true) {
        $author = HTMLHelper::_('link', $item->contact_link, $author);
    }
}

// Build publish date
if ($params->get('show_publish_date')) {
    $published = HTMLHelper::_('date', $item->publish_up, Text::_('DATE_FORMAT_LC3'));
    $published = '<time datetime="' . HTMLHelper::_('date', $item->publish_up, 'c') . '">' . $published . '</time>';
}
?>

<div class="el-item uk-panel uk-margin-remove-first-child">
    <div class="uk-child-width-expand uk-grid-column-medium uk-grid-row-small" uk-grid>
        <?php if ($media) : ?>
            <div class="uk-width-1-3@m">
                <?php if ($article_format === 'gallery') : ?>
                    <?php echo LayoutHelper::render('joomla.content.blog.gallery', ['attribs' => $attribs, 'id' => $item->id]); ?>
                <?php elseif ($article_format === 'video') : ?>
                    <?php echo LayoutHelper::render('joomla.content.blog.video', ['attribs' => $attribs]); ?>
                <?php elseif ($article_format === 'audio') : ?>
                    <?php echo LayoutHelper::render('joomla.content.blog.audio', ['attribs' => $attribs]); ?>
                <?php else : ?>
                    <a href="<?php echo Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language)); ?>" aria-label="<?php echo $this->escape($item->title); ?>">
                        <?php echo LayoutHelper::render('joomla.content.full_image', $item); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="uk-margin-remove-first-child">
            <?php echo LayoutHelper::render('joomla.content.blog_style_default_related_item_title', $item); ?>

            <?php if ($useDefList) : ?>
                <p class="uk-margin-small-top uk-article-meta uk-margin-remove-bottom">
                    <?php if ($author && $published) : ?>
                        <?php echo Text::sprintf('TPL_META_AUTHOR_DATE', $author, $published); ?>
                    <?php elseif ($author) : ?>
                        <?php echo Text::sprintf('TPL_META_AUTHOR', $author); ?>
                    <?php elseif ($published) : ?>
                        <?php echo Text::sprintf('TPL_META_DATE', $published); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>