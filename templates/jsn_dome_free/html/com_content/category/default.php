<?php // no direct access
	defined('_JEXEC') or die('Restricted access');
	JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<div class="com-content <?php echo $this->pageclass_sfx;?>">
	<div class="category-list">
		<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h2 class="componentheading">
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h2>
		<?php endif; ?>

		<?php if ($this->params->get('show_category_title', 1) OR $this->params->get('page_subheading')) : ?>
		<h2 class="subheading">
			<?php echo $this->escape($this->params->get('page_subheading')); ?>
			<?php if ($this->params->get('show_category_title')) : ?>
				<span class="subheading-category"><?php echo $this->category->title;?></span>
			<?php endif; ?>
		</h2>
		<?php endif; ?>	
		
		<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
		<div class="contentdescription clearafter">
			<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
				<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
			<?php endif; ?>
			<?php if ($this->params->get('show_description') && $this->category->description) : ?>
				<?php echo JHtml::_('content.prepare', $this->category->description); ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<?php echo $this->loadTemplate('articles'); ?>
		
		<?php if (!empty($this->children[$this->category->id])&& $this->maxLevel != 0) : ?>
			<div class="cat-children">
				<h3><?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?></h3>
				<?php echo $this->loadTemplate('children'); ?>
			</div>
		<?php endif; ?>
</div></div>