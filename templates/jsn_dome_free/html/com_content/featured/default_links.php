
<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<h2><?php echo JText::_('COM_CONTENT_MORE_ARTICLES'); ?></h2>
<ul>
<?php
	foreach ($this->link_items as &$item) :
?>
	<li>
		<a class="blogsection" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); ?>">
			<?php echo $item->title; ?></a>
	</li>
<?php endforeach; ?>
</ul>