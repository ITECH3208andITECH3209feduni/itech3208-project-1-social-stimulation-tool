<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Initialize variables
$item = $displayData;
$params = $displayData->params;

// Load template data
$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$tmpl_params = $template->params;

// Template parameters
$detail_center_content = $tmpl_params->get('blog_center_content');
$meta_margin = $tmpl_params->get('leading_blog_list_meta_margin', 'default');
$meta_style = $tmpl_params->get('blog_details_meta_style', 'list');

// Build margin classes
$meta_margin_cls = ($meta_margin === 'default') ? 'uk-margin-top' : 'uk-margin-' . $meta_margin . '-top';

if ($meta_style === 'list') {
    $meta_margin_cls .= $detail_center_content ? ' uk-flex-center' : '';
} else {
    $meta_margin_cls .= $detail_center_content ? ' uk-text-center' : '';
}

// Check if we should display meta information
$useDefList = (
    $params->get('show_publish_date') ||
    $params->get('show_category') ||
    $params->get('show_parent_category') ||
    $params->get('show_author')
);

// Initialize meta variables
$author = '';
$published = '';
$category = '';

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

// Build category information
if ($params->get('show_category')) {
    $category = $item->category_title;
    if ($params->get('link_category') && !empty($item->catid)) {
        $categoryRoute = RouteHelper::getCategoryRoute($item->catid);
        $category = '<a href="' . Route::_($categoryRoute) . '" itemprop="genre">' . $category . '</a>';
    }
}
?>

<?php if ($useDefList) : ?>
    <?php if ($meta_style === 'list') : ?>
        <?php
        // List style - build blocks array
        $blocks = array_filter([
            $published ?: '',
            $author ? '<span>' . $author . '</span>' : '',
            $category ?: '',
        ]);
        ?>

        <ul class="<?php echo $meta_margin_cls; ?> uk-margin-remove-bottom uk-subnav uk-subnav-divider">
            <?php foreach ($blocks as $block) : ?>
                <li><?php echo $block; ?></li>
            <?php endforeach; ?>
        </ul>

    <?php else : ?>
        <?php // Sentence style ?>
        <p class="<?php echo $meta_margin_cls; ?> uk-margin-remove-bottom uk-article-meta">
            <?php if ($author && $published) : ?>
                <?php echo Text::sprintf('TPL_META_AUTHOR_DATE', $author, $published); ?>
            <?php elseif ($author) : ?>
                <?php echo Text::sprintf('TPL_META_AUTHOR', $author); ?>
            <?php elseif ($published) : ?>
                <?php echo Text::sprintf('TPL_META_DATE', $published); ?>
            <?php endif; ?>
            
            <?php if ($category) : ?>
                <?php echo Text::sprintf('TPL_META_CATEGORY', $category); ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>
<?php endif; ?>