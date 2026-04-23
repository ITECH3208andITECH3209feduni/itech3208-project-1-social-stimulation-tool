<?php 
	// no direct access
	defined('_JEXEC') or die('Restricted access');
	JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<div class="com-content <?php echo $this->params->get('pageclass_sfx') ?>">
<div class="front-page-blog">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h2 class="componentheading">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h2>
	<?php endif; ?>
	
	<?php $leadingcount=0 ; ?>
	<?php if (!empty($this->lead_items)) : ?>
	<div class="jsn-leading">
	<?php foreach ($this->lead_items as &$item) : ?>
		<div class="jsn-leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
			<?php
				$this->item = &$item;
				echo $this->loadTemplate('item');
			?>
		</div>
		<?php
			$leadingcount++;
		?>
	<?php endforeach; ?>
	</div>
	<?php endif; ?>
	
	<?php
		$introcount=(count($this->intro_items));
		$counter=0;
	?>
	<?php if (!empty($this->intro_items)) : ?>
	<div class="row_separator"></div>
		<?php foreach ($this->intro_items as $key => &$item) : ?>
		<?php
			$key= ($key-$leadingcount)+1;
			$rowcount=( ((int)$key-1) %	(int) $this->columns) +1;
			$row = $counter / $this->columns ;

			if ($rowcount==1) : ?>
				<div class="cols-<?php echo (int) $this->columns;?> <?php echo 'row-'.$row ; ?>">
			<?php endif; ?>
		<div class="jsn-articlecols column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>" style="width:<?php echo intval(100 / $this->columns); ?>%">
			<?php
				$this->item = &$item;
				echo $this->loadTemplate('item');
			?>
		</div>
		<?php $counter++; ?>
		<?php if (($rowcount == $this->columns) or ($counter ==$introcount)): ?>
		<div class="clearbreak"></div>
	</div>
		<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<?php if (!empty($this->link_items)) : ?>
	<div class="row_separator"></div>
	<div class="blog_more clearafter">
		<?php echo $this->loadTemplate('links'); ?>
	</div>
	<?php endif; ?>	
	
	<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
	<div class="row_separator"></div>
	<div class="jsn-pagination-container">
		<?php echo $this->pagination->getPagesLinks(); ?>
		<?php if ($this->params->get('show_pagination_results', 1)) : ?>
			<p class="jsn-pageinfo"><?php echo $this->pagination->getPagesCounter(); ?></p>
		<?php endif; ?>
	</div>
	<?php endif; ?>

</div>
</div>