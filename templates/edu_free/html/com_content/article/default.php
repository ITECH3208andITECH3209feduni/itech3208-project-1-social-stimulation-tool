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
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$tmpl_params = $template->params;

// Initialize related articles
$relatedArticles = [];
if ($tmpl_params->get('related_article')) {
    $args = [
        'catId' => $this->item->catid,
        'maximum' => $tmpl_params->get('related_article_limit', 5),
        'itemTags' => $this->item->tags->itemTags ?? [],
        'item_id' => $this->item->id,
    ];
    $relatedArticles = HelixUltimate\Framework\Core\HelixUltimate::getRelatedArticles($args);
}

// Parse JSON data safely
$images = json_decode($this->item->images ?? '{}');
$urls = json_decode($this->item->urls ?? '{}');
$attribs = json_decode($this->item->attribs ?? '{}');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$canEdit = $params->get('access-edit');
$user    = $this->getCurrentUser();
$info    = $params->get('info_block_position', 0);
$htag    = $this->params->get('show_page_heading') ? 'h2' : 'h1';

$article_format = (isset($attribs->helix_ultimate_article_format) && $attribs->helix_ultimate_article_format) ? $attribs->helix_ultimate_article_format : 'standard';

// Check if associations are implemented. If they are, define the parameter.
$assocParam        = (Associations::isEnabled() && $params->get('show_associations'));
$currentDate       = Factory::getDate()->format('Y-m-d H:i:s');
$isNotPublishedYet = $this->item->publish_up > $currentDate;
$isExpired         = !is_null($this->item->publish_down) && $this->item->publish_down < $currentDate;

// Template parameters
$detail_center_content = $tmpl_params->get('detail_center_content');
$article_content_width = $tmpl_params->get('article_content_width', '');
$dropcap = $tmpl_params->get('blog_details_dropcap', 0);

// Build CSS classes
$title_margin = $tmpl_params->get('blog_details_title_margin', 'default');
$title_margin_cls = ($title_margin === 'default' ? 'uk-margin-top' : 'uk-margin-' . $title_margin . '-top');
$title_margin_cls .= $detail_center_content ? ' uk-text-center' : '';

$content_margin = $tmpl_params->get('blog_details_content_margin', 'default');
$content_margin_cls = ($content_margin === 'default' ? 'uk-margin-top' : 'uk-margin-' . $content_margin . '-top');

$dropcap_cls = $dropcap ? ' uk-dropcap' : '';

$article_image_margin = $tmpl_params->get('article_image_margin', 'default');
$article_image_margin_cls = ($article_image_margin === 'default' ? ' uk-margin-top' : ' uk-margin-' . $article_image_margin . '-top');

$tag_cls = $detail_center_content ? ' class="uk-text-center"' : '';

$mediaOutput = '';

switch($article_format) {
	case 'gallery':
		$mediaOutput = LayoutHelper::render('joomla.content.blog.gallery', array('attribs' => $attribs, 'id' => $this->item->id));
		break;
	
	case 'video':
		$mediaOutput = LayoutHelper::render('joomla.content.blog.video', array('attribs' => $attribs));
		break;
	
	case 'audio':
		$mediaOutput = LayoutHelper::render('joomla.content.blog.audio', array('attribs' => $attribs));
		break;
	
	default:
		if ($params->get('access-view')) {
			$mediaOutput = LayoutHelper::render('joomla.content.full_image', $this->item);
		}
		break;
}

?>
	<div class="uk-article" itemscope itemtype="https://schema.org/Article" data-permalink="<?php echo Uri::current(); ?>">
		<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? Factory::getApplication()->get('language') : $this->item->language; ?>">
		<meta property="author" typeof="Person" content="<?php echo $this->escape($this->item->author); ?>">
		<meta property="dateModified" content="<?php echo HTMLHelper::_('date', $this->item->modified, 'c'); ?>">
		<meta property="datePublished" content="<?php echo HTMLHelper::_('date', $this->item->publish_up, 'c'); ?>">
		<meta class="uk-margin-remove-adjacent" property="articleSection" content="<?php echo $this->escape($this->item->category_title); ?>">

	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
    <?php endif;
    if (!empty($this->item->pagination) && !$this->item->paginationposition && $this->item->paginationrelative) {
        echo $this->item->pagination;
    }
    ?>

	<?php if (!empty($mediaOutput)) : ?>
		<div class="uk-text-center<?php echo $article_image_margin_cls; ?>">
			<?php echo $mediaOutput; ?>
		</div>
	<?php endif; ?>

	<?php if ($article_content_width): ?>
		<div class="uk-container uk-container-<?php echo $article_content_width; ?>">
	<?php endif; ?>

	<?php // Todo Not that elegant would be nice to group the params 
	?>
	<?php $useDefList = ($params->get('show_publish_date') || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author')); ?>

	<?php if ($info == 0) : ?>
		<?php echo LayoutHelper::render('joomla.content.article_meta_block', $this->item); ?>
	<?php endif; ?>

    <?php if ($params->get('show_title')) : ?>
    <div class="page-header">
        <<?php echo $htag; ?> class="<?php echo $title_margin_cls; ?> uk-margin-remove-bottom uk-article-title" itemprop="headline">
            <?php echo $this->escape($this->item->title); ?>
        </<?php echo $htag; ?>>
        <?php if ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED) : ?>
            <span class="uk-label uk-label-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
        <?php endif; ?>
        <?php if ($isNotPublishedYet) : ?>
            <span class="uk-label uk-label-warning"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></span>
        <?php endif; ?>
        <?php if ($isExpired) : ?>
            <span class="uk-label uk-label-warning"><?php echo Text::_('JEXPIRED'); ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

	<?php // Content is generated by content plugin event "onContentAfterTitle" 
	?>

	<?php echo $this->item->event->afterDisplayTitle; ?>

	<?php if ($info != 0) : ?>
		<?php echo LayoutHelper::render('joomla.content.article_meta_block', $this->item); ?>
	<?php endif; ?>

	<?php // Language Associations ?>
	<?php if ($assocParam && !empty($this->item->associations)) : ?>
		<div class="uk-article-associations uk-margin-top">
			<div class="uk-flex uk-flex-middle uk-flex-wrap">
				<span class="uk-margin-small-right">
					<span class="icon-globe icon-fw" aria-hidden="true"></span>
					<?php echo Text::_('JASSOCIATIONS'); ?>
				</span>
				
				<ul class="uk-iconnav">
					<?php foreach ($this->item->associations as $association) : ?>
						<li>
							<?php if ($params->get('flags', 1) && !empty($association['language']->image)) : ?>
								<?php 
								$flag = HTMLHelper::_('image', 
									'mod_languages/' . $association['language']->image . '.gif', 
									htmlspecialchars($association['language']->title_native, ENT_QUOTES, 'UTF-8'),
									['title' => htmlspecialchars($association['language']->title_native, ENT_QUOTES, 'UTF-8')], 
									true
								);
								?>
								<a href="<?php echo Route::_($association['item']); ?>" 
									title="<?php echo htmlspecialchars($association['language']->title_native, ENT_QUOTES, 'UTF-8'); ?>">
									<?php echo $flag; ?>
								</a>
							<?php else : ?>
								<a href="<?php echo Route::_($association['item']); ?>" 
									class="uk-button uk-button-small uk-button-default" 
									title="<?php echo htmlspecialchars($association['language']->title_native, ENT_QUOTES, 'UTF-8'); ?>">
									<?php echo strtoupper($association['language']->lang_code); ?>
									<span class="visually-hidden"><?php echo htmlspecialchars($association['language']->title_native, ENT_QUOTES, 'UTF-8'); ?></span>
								</a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php // Content is generated by content plugin event "onContentBeforeDisplay" 
	?>
	<?php echo $this->item->event->beforeDisplayContent; ?>

    <?php if ((int) $params->get('urls_position', 0) === 0) : ?>
        <?php echo $this->loadTemplate('links'); ?>
    <?php endif; ?>

		<?php if ($params->get('access-view')) : ?>
			<?php if (!empty($this->item->pagination) && !$this->item->paginationposition && !$this->item->paginationrelative) : ?>
				<?php echo $this->item->pagination; ?>
			<?php endif; ?>

			<?php if (($tmpl_params->get('social_share') || $params->get('show_vote')) && !$this->print) : ?>
				<div class="uk-flex uk-flex-middle<?php echo $detail_center_content ? ' uk-flex-center' : ''; ?> uk-margin-top">
					<?php if ($detail_center_content) : ?>
						<div>
							<div class="uk-grid-medium uk-child-width-auto uk-flex-middle uk-grid" uk-grid>
					<?php endif; ?>
					
					<?php if ($params->get('show_vote')) : ?>
						<div>
							<?php HTMLHelper::_('jquery.token'); ?>
							<?php echo LayoutHelper::render('joomla.content.rating', ['item' => $this->item, 'params' => $params]); ?>
						</div>
					<?php endif; ?>

					<?php if ($tmpl_params->get('social_share')) : ?>
						<?php if ($detail_center_content && $params->get('show_vote')) : ?>	
							<div>
						<?php elseif (!$detail_center_content && $params->get('show_vote')) : ?>
							<div class="uk-margin-auto-left@s">
						<?php else : ?>
							<div>
						<?php endif; ?>
							<?php echo LayoutHelper::render('joomla.content.social_share', $this->item); ?>
						</div>
					<?php endif; ?>

					<?php if ($detail_center_content) : ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if (isset($this->item->toc)) : ?>
				<?php echo $this->item->toc; ?>
			<?php endif; ?>

			<div class="<?php echo $content_margin_cls . $dropcap_cls; ?>" property="text">
				<?php echo $this->item->text; ?>
			</div>

			<?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
				<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
				<p<?php echo $tag_cls; ?>><?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?></p>
			<?php endif; ?>

			<?php if ((int) $params->get('urls_position', 0) === 1) : ?>
				<?php echo $this->loadTemplate('links'); ?>
			<?php endif; ?>

		<?php // Optional teaser intro text for guests ?>
		<?php elseif ($params->get('show_noauth') && $user->guest) : ?>
			<?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
			<?php echo HTMLHelper::_('content.prepare', $this->item->introtext); ?>
			
			<?php // Optional link to let them register to see the whole article ?>
			<?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
				<?php 
				$menu = Factory::getApplication()->getMenu();
				$active = $menu->getActive();
				$itemId = $active->id ?? 0;
				$link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
				$link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
				?>
				<?php echo LayoutHelper::render('joomla.content.readmore', ['item' => $this->item, 'params' => $params, 'link' => $link]); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if (($params->get('show_parent_category') && !empty($this->item->parent_id)) || $params->get('show_create_date') || $params->get('show_modify_date') || $params->get('show_hits')) : ?>
			<ul class="uk-list uk-article-meta-list">
				<?php if ($params->get('show_parent_category') && !empty($this->item->parent_id)) : ?>
					<li>
						<?php 
						$parentCategory = !empty($this->item->parent_title) ? htmlspecialchars($this->item->parent_title, ENT_QUOTES, 'UTF-8') : 'Parent Category'; 
						?>
						<?php echo LayoutHelper::render('joomla.icon.iconclass', ['icon' => 'icon-folder icon-fw']); ?>
						<?php if ($params->get('link_parent_category')) : ?>
							<?php $parentCategoryUrl = Route::_(RouteHelper::getCategoryRoute($this->item->parent_id, $this->item->language ?? null)); ?>
							<?php echo Text::sprintf('COM_CONTENT_PARENT', HTMLHelper::_('link', $parentCategoryUrl, $parentCategory)); ?>
						<?php else : ?>
							<?php echo Text::sprintf('COM_CONTENT_PARENT', $parentCategory); ?>
						<?php endif; ?>
					</li>
				<?php endif; ?>

				<?php if ($params->get('show_create_date') && !empty($this->item->created)) : ?>
					<li>
						<span class="icon-calendar icon-fw" aria-hidden="true"></span>
						<time datetime="<?php echo HTMLHelper::_('date', $this->item->created, 'c'); ?>" itemprop="dateCreated">
							<?php echo Text::sprintf('TPL_META_DATE_CREATED', HTMLHelper::_('date', $this->item->created, Text::_('DATE_FORMAT_LC3'))); ?>
						</time>
					</li>
				<?php endif; ?>

				<?php if ($params->get('show_modify_date') && !empty($this->item->modified)) : ?>
					<li>
						<span class="icon-calendar icon-fw" aria-hidden="true"></span>
						<time datetime="<?php echo HTMLHelper::_('date', $this->item->modified, 'c'); ?>" itemprop="dateModified">
							<?php echo Text::sprintf('TPL_META_DATE_MODIFIED', HTMLHelper::_('date', $this->item->modified, Text::_('DATE_FORMAT_LC3'))); ?>
						</time>
					</li>
				<?php endif; ?>

				<?php if ($params->get('show_hits') && !empty($this->item->hits)) : ?>
					<li>
						<span class="icon-eye icon-fw" aria-hidden="true"></span>
						<meta content="UserPageVisits:<?php echo (int)$this->item->hits; ?>" itemprop="interactionCount">
						<?php echo Text::sprintf('COM_CONTENT_ARTICLE_HITS', number_format((int)$this->item->hits)); ?>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>

		<?php if ($canEdit) : ?>
			<?php echo LayoutHelper::render('joomla.content.icons', ['params' => $params, 'item' => $this->item]); ?>
		<?php endif; ?>

		<?php if ($article_content_width ): ?>
			</div>
		<?php endif; ?>

		<?php
		if (!empty($this->item->pagination) && $this->item->paginationposition && !$this->item->paginationrelative) :
			echo $this->item->pagination;
			?>
		<?php endif; ?>

		<?php echo LayoutHelper::render('joomla.content.blog.author_info', $this->item); ?>
		<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
		<?php echo $this->item->event->afterDisplayContent; ?>

		<?php if (!$this->print) : ?>
			<?php echo LayoutHelper::render('joomla.content.blog.comments.comments', $this->item); ?>
		<?php endif; ?>

	</div>

	<?php if ($tmpl_params && $tmpl_params->get('related_article') && count($relatedArticles) > 0) : ?>
		<?php echo LayoutHelper::render('joomla.content.related_articles', ['articles' => $relatedArticles, 'item' => $this->item]); ?>
	<?php endif; ?>