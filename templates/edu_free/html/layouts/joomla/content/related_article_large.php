<?php 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$item = $displayData;
$params = $item->params;
$info = $params->get('info_block_position', 0);
$attribs = json_decode($item->attribs ?? "");
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$article_format = (isset($attribs->helix_ultimate_article_format) && $attribs->helix_ultimate_article_format)
	? $attribs->helix_ultimate_article_format
	: 'standard';

	$useDefList = $params->get('show_publish_date') || $params->get('show_author');

	$author = $published = $category = '';

	if ($params->get('show_author')) {
		$author = ($item->created_by_alias ?: $item->author);
		if (!empty($item->contact_link ) && $params->get('link_author') == true) {
			$author = HTMLHelper::_('link', $item->contact_link, $author);
		}
	}
	
	if ($params->get('show_publish_date')) {
		$published = HTMLHelper::_('date', $item->publish_up, Text::_('DATE_FORMAT_LC3'));
		$published = '<time datetime="' . HTMLHelper::_('date', $item->publish_up, 'c') . "\">$published</time>";
	}

$images = json_decode($displayData->images);
$full_image = (isset($images->image_fulltext) && !empty($images->image_fulltext));

$media = (isset($attribs->helix_ultimate_image) && $attribs->helix_ultimate_image != '') || $full_image || (isset($attribs->helix_ultimate_audio) && $attribs->helix_ultimate_audio) || (isset($gallery->helix_ultimate_gallery_images) && $gallery->helix_ultimate_gallery_images) || (isset($attribs->helix_ultimate_video) && $attribs->helix_ultimate_video);

?>

<div class="uk-child-width-expand uk-grid-column-small uk-flex-middle" uk-grid>

	<?php if($media) : ?>
		<div class="uk-width-small">
			<?php if($article_format === 'gallery') : ?>
				<?php echo LayoutHelper::render('joomla.content.blog.gallery', array('attribs' => $attribs, 'id' => $item->id)); ?>
			<?php elseif($article_format === 'video') : ?>
				<?php echo LayoutHelper::render('joomla.content.blog.video', array('attribs' => $attribs)); ?>
			<?php elseif($article_format === 'audio') : ?>
				<?php echo LayoutHelper::render('joomla.content.blog.audio', array('attribs' => $attribs)); ?>
			<?php else: ?>
				<a href="<?php echo Route::_(Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
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
					<?php Text::printf('TPL_META_AUTHOR_DATE', $author, $published); ?>
				<?php elseif ($author) : ?>
					<?php Text::printf('TPL_META_AUTHOR', $author); ?>
				<?php elseif ($published) : ?>
					<?php Text::printf('TPL_META_DATE', $published); ?>
				<?php endif; ?>
			</p>
		<?php endif; ?>

		<?php if ($item->introtext): ?>
			<div class="intro-text uk-margin-small-top uk-margin-remove-bottom">
				<?php echo $item->introtext; ?>
			</div>
		<?php endif ?>

	</div>

</div>